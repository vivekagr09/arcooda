<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('asset'); //assetSearch, assetFromDatabase
moduleLoad('invoice'); //invoiceChargesFromDatabase, invoiceJobFromDatabase, invoiceJobsFromDatabase, invoiceProductsFromDatabase
moduleLoad('template');

include_once __DIR__ . '/header.inc.php';

$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value)) {
            unset($get_vars[$key]);
        }
    }
}

$action     = 'job';
$job_id     = intval($posted['job_id']);
$invoice_id = intval($posted['invoice_id']);

$job_info = invoiceJobFromDatabase($job_id);
if ($invoice_id != $job_info['invoice_id']) {
    die('Mismatching job/purchase order ids');
}

$job_list = invoiceJobsFromDatabase($invoice_id);

$my_url           = $get_vars;
$my_url['action'] = 'edit';
unset($my_url['job_id']);
unset($my_url['skip_job']);

$process_url = $my_url;
$process_url = 'index.php?' . http_build_query($process_url);
$process_url .= '&process_job=';

$skip_url = $my_url;
$skip_url = 'index.php?' . http_build_query($skip_url);
$skip_url .= '&skip_job=';

$delete_url = $my_url;
$delete_url = 'index.php?' . http_build_query($delete_url);
$delete_url .= '&delete_job=';

?>
<script type="text/javascript" src="common.js"></script>

<h2>Edit Invoice <?=invoiceFormatNumber($invoice_id, $invoice['revision_id'])?> - <?=date('d/m/Y', time())?></h2>

<div>
There are currently <?=count($job_list)?> email(s) to be sent for this invoice<br />
<br />
<table align="center">
<form accept-charset="utf-8" action="<?=$process_url . $job_id?>" method="post">
<?php
switch ($job_info['job_type']) {
    case '1':
        $headers = array();
        $content = explode("\n", $job_info['job_content']);
        while (($row = array_shift($content)) != '') {
            $row                              = explode(':', $row);
            $headers[trim(array_shift($row))] = trim(implode(':', $row));
        }
        $invoice          = invoiceFromDatabase($invoice_id);
        $invoice_products = invoiceProductsFromDatabase($invoice_id);
        $invoice_charges  = invoiceChargesFromDatabase($invoice_id);
        $invoice_assets   = assetSearch('invoice', array('invoice_id' => $invoice_id));
        $email_template   = new Template();
        $email_template->set_vars(invoiceTemplateVars($invoice, $invoice_products, $invoice_charges));
        //    foreach($invoice_template as $field=>$content)
        //    {
        //        $email_template->set_contents($content);
        //        $invoice_template[$field]=$email_template->apply();
        //    }
        $content_rows = count($content);
        $email_template->set_contents(implode("\n", $content));
        $content = $email_template->apply();
        $email_template->set_contents($headers['Subject']);
        $headers['Subject'] = $email_template->apply();
        $attach_list        = explode(',', $headers['Attachments']);
        foreach ($attach_list as $key2 => $attach_id) {
            $attach_list[$key2] = intval(trim($attach_id));
        }

        $attach_list = array_intersect($attach_list, $invoice_assets);
?>
	<tr>
		<td class="left_title" colspan="2" style="text-align:center">Preview Email to be sent to the Customer.</td>
	</tr>
<?php
        foreach ($headers as $key => $value) {
            switch ($key) {
                case 'Attachments':
?>
	<tr>
		<td class="left_title" style="width:25%;"><?php hecho($key)?></td>
		<td style="width:75%;">
<?php
                    foreach ($attach_list as $attach_id) {
                        $asset = assetFromDatabase('invoice', $attach_id);
                        $url   = '/webadmin/assets/?asset_type=invoice&asset_id=' . $attach_id . '&action=';
?>
			<a href="<?=$url?>view" target="_blank"><img src="<?=$url?>thumb" title="<?php hecho($asset['asset_caption'] . ' (' . $asset['fileorig'] . ')')?>" />
<?php
                    }
?>
		</td>
	</tr>
<?php
                    break;
                case 'Subject':
                    break;
                case 'Invoice':
?>
	<tr>
		<td class="left_title" style="width:25%;"><?php hecho($key)?></td>
		<td style="width:75%;"><a href="invoice_view.php?invoice_id=<?=$invoice_id?>" target="_blank"><?php hecho($value)?></a></td>
	</tr>
<?php
                    break;
                case 'CreditForm':
?>
	<tr>
		<td class="left_title" style="width:25%;"><?php hecho($key)?></td>
		<td style="width:75%;"><a href="creditform.pdf" target="_blank"><?php hecho($value)?></a></td>
	</tr>
<?php
                    break;
                default:
?>
	<tr>
		<td class="left_title" style="width:25%;"><?php hecho($key)?></td>
		<td style="width:75%;"><?php hecho($value)?></td>
	</tr>
<?php
                    break;
            }
        }
?>
	<tr>
		<td class="left_title" style="width:25%;">Subject</td>
		<td style="width:75%;"><input type="text" name="job_subject" class="intext" value="<?php hecho($headers['Subject'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">Request Read Receipt <a href="#" style="color:blue;" onclick="openWindow('read_receipt.html',400,400,true); return false;">(?)</a></td>
		<td><input type="checkbox" name="job_receipt" value="y" /></td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">Content</td>
		<td style="width:75%;"><textarea name="job_content" rows="<?=$content_rows + 3?>" onkeyup="textareaAutoSize(this,1,<?=$content_rows + 3?>);"><?php hecho($content)?></textarea></td>
	</tr>
<?php
        break;
    default:
?>
	<tr>
		<td class="left_title" style="width:25%;">Job Description</td>
		<td style="width:75%;"><?=nl2br(htmlspecialchars($job_info['job_content']))?></td>
	</tr>
<?php
        break;
}
?>
	<tr class="alt">
		<td colspan="2" style="text-align:center;">
			<input type="submit" style="width:100px;" value="Send Email" />
			<!--<input type="button" style="width:100px;" value="Skip Job" onclick="document.location='<?=$skip_url . $job_id?>'" />-->
			<input type="button" style="width:100px;" value="Cancel Email" onclick="document.location='<?=$delete_url . $job_id?>'" />
		</td>
	</tr>
</form>
</table>
<br /><br />
</div>
<?php

include_once __DIR__ . '/footer.inc.php';

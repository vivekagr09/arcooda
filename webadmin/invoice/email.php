<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('asset'); //assetFromDatabase
moduleLoad('template');

include_once __DIR__ . '/classes/MimeMailParser.php';

$action  = 'email';
$id      = intval($posted['id']);
$part_id = -1;

$email = invoiceEmailFromDatabase($id);

$Parser = new MimeMailParser();
$Parser->setText($email["content"]);
$attachments = $Parser->getAttachments();

if (array_key_exists("cid", $posted)) {
    foreach ($attachments as $k => $v) {
        if (array_key_exists("content-id", $v["headers"]) && $v["headers"]["content-id"] == $posted["cid"]) {
            $part_id = $k;
        }
    }
}

if (array_key_exists("attachment", $posted)) {
    $part_id = intval($posted["attachment"]);
}

if (array_key_exists($part_id, $attachments)) {
    $attachment = $attachments[$part_id];
    header("Content-Type:" . $attachment->content_type);
    echo $attachment->getContent();
    die();
}

include_once __DIR__ . '/header.inc.php';
?>
<table class="wide table_fields">
	<tr>
		<th style="width:25%;"></th>
		<td style="width:75%;"></td>
	</tr>
<?php
foreach ($Parser->parts[1]["headers"] as $k => $v) {
?>
	<tr>
		<th><input type="checkbox" name="<?=htmlspecialchars("headers[" . $k . "][checked]")?>" checked="checked" /><?=htmlspecialchars($k)?></th>
		<td><input style="width:98%;" type="text" name="<?=htmlspecialchars("headers[" . $k . "][value]")?>" value="<?=htmlspecialchars($v)?>" /></td>
	</tr>
<?php
}
?>
	<tr>
		<th>Content (text)</th>
		<td><?=formatHTMLTextarea("body[text]", $Parser->getMessageBody("text"))?></td>
	</tr>
	<tr>
		<th>Content (html)</th>
		<td><?=formatHTMLTextarea("body[html]", $Parser->getMessageBody("html"))?></td>
	</tr>
<?php
if (count($attachments)) {
?>
	<tr>
		<th>Attachments</th>
		<td>
<?php
foreach ($attachments as $k => $v) {
        $url               = $posted;
        $url["attachment"] = $k;
        $url               = "?" . http_build_query($url);
?>
			<input type="checkbox" name="<?=htmlspecialchars("attachments[" . $k . "][checked]")?>" checked="checked" />
			<a href="<?=htmlspecialchars($url)?>" target="_blank"><img src="<?=htmlspecialchars($url)?>" height="96" /></a>
<?php
}
?>
		</td>
	</tr>
<?php
}
?>
</table>
<?php
die();

$my_url           = $get_vars;
$my_url['action'] = 'edit';
unset($my_url['id']);
unset($my_url['skip_email']);

$process_url = $my_url;
$process_url = '.?' . http_build_query($process_url);
$process_url .= '&process_email=';

$skip_url = $my_url;
$skip_url = '.?' . http_build_query($skip_url);
$skip_url .= '&skip_email=';

$delete_url = $my_url;
$delete_url = '.?' . http_build_query($delete_url);
$delete_url .= '&delete_email=';
?>
<h2><?=htmlspecialchars(sprintf(_("Edit Invoice %s - %s"), $invoice["invoice_number"], date(_("d/m/Y"))))?></h2>

<div>
	There are currently <?=count($email_list)?> email(s) to be sent for this invoice<br />
	<br />
	<form action="<?=$process_url . $id?>" method="post">
		<table style="margin:auto">
<?php
$headers = array();
$content = explode("\n", $email['content']);
while (($row = array_shift($content)) != '') {
    $row                              = explode(':', $row);
    $headers[trim(array_shift($row))] = trim(implode(':', $row));
}
$email_template = new Template();
$email_template->set_vars(invoiceTemplateVars($invoice, $invoice_products, $invoice_charges));
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
			<tr><td class="left_title" colspan="2" style="text-align:center">Preview Email to be sent to the Customer.</td></tr>
<?php
foreach ($headers as $key => $value) {
    switch ($key) {
        case 'Attachments':
?>
			<tr>
				<th><?=htmlspecialchars($key)?></th>
				<td style="width:75%;">
<?php
            foreach ($attach_list as $attach_id) {
                $asset = assetFromDatabase('invoice', $attach_id);
                $url   = '/webadmin/assets/?asset_type=invoice&asset_id=' . $attach_id . '&action=';
?>
			<a href="<?=htmlspecialchars($url . "view")?>" target="_blank"><img src="<?=htmlspecialchars($url . "thumb")?>" title="<?=htmlspecialchars($asset['asset_caption'] . ' (' . $asset['fileorig'] . ')')?>" />
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
				<th><?=htmlspecialchars($key)?></th>
				<td style="width:75%;"><a href="invoice_view.php?invoice_id=<?=$invoice_id?>" target="_blank"><?=htmlspecialchars($value)?></a></td>
			</tr>
<?php
            break;

        case 'DummyInvoice':
?>
			<tr>
				<th><?=htmlspecialchars($key)?></th>
				<td style="width:75%;"><a href="/webadmin/shipinvoice/shipinvoice_view.php?sinvoice_id=<?=$invoice_id?>" target="_blank"><?=htmlspecialchars($value)?></a></td>
			</tr>
<?php
            break;

        case 'CreditForm':
?>
			<tr>
				<th><?=htmlspecialchars($key)?></th>
				<td style="width:75%;"><a href="creditform.pdf" target="_blank"><?=htmlspecialchars($value)?></a></td>
			</tr>
<?php
            break;

        default:
?>
			<tr>
				<th><?=htmlspecialchars($key)?></th>
				<td style="width:75%;"><?=htmlspecialchars($value)?></td>
			</tr>
<?php
            break;
    }
}
?>
			<tr>
				<th>Subject</th>
				<td style="width:75%;"><input type="text" name="email" class="intext" value="<?=htmlspecialchars($headers['Subject'])?>" /></td>
			</tr>
			<tr>
				<th>Request Read Receipt <a href="#" style="color:blue;" onclick="openWindow('read_receipt.html', 400, 400, true);return false">(?)</a></th>
				<td><input type="checkbox" name="email_receipt" value="y" /></td>
			</tr>
			<tr>
				<th style="width:25%;">Content</th>
				<td style="width:75%;"><textarea name="email_content" rows="<?=$content_rows + 3?>" onkeyup="textareaAutoSize(this, 1, <?=$content_rows + 3?>)"><?=htmlspecialchars($content)?></textarea></td>
			</tr>
			<tr class="alt"><td colspan="2" style="text-align:center;">
				<input type="submit" style="width:100px;" value="Send Email" />
				<input type="button" style="width:100px;" value="Cancel Email" onclick="document.location='<?=$delete_url . $id?>'" />
			</td></tr>
		</table>
	</form>
	<br /><br />
</div>
<?php

include_once __DIR__ . '/footer.inc.php';

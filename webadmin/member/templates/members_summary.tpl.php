<!-- members_summary.tpl.php -->
<?php
moduleLoad('invoice'); //invoiceFormatNumber

$match_array = array('asp' => 20, 'car' => 16, 'cnv' => 9, 'he' => 2, 'hg' => 1, 'xbox' => 18);
$cs          = getCountryInfo($member['signup_IP']);
if ('' == $cs['a2']) {
    $cs['a2'] = 'zz';
}

$percent = (count($all_invoices)) ? sprintf("%.1f", (count($processed_invoices) / count($all_invoices)) * 100) : 0;

$temp = array();
if (count($member_requests)) {
	$temp[] = 'Member Requests';
	foreach ($member_requests as $member_request) {
		$temp[] = '- ' . $member_request['comments'];
	}
}
if (count($member_warnings)) {
	$temp[] = '<b>Member Warnings</b>';
	foreach ($member_warnings as $member_warning) {
		$temp[] = '- ' .htmlspecialchars($member_warning['comments']);
	}
}
$member_tooltip = html_entity_decode(implode("<br />", $temp));
?>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionSummary\');return false;">Customer Summary</a>')?>
<div id="sectionSummary"><table class="wide">
	<tr ><td class="left_title" rowspan="10">
		<?php if ($member['avatar_image']){ ?>
			<img src="/avatar/svgavatars/ready-avatars/<?= $member['avatar_image'] ?>" style="padding: 20px;" width="70" height="70" title="Make your own Avatar to use for comments, posts and product reviews">
		<?php }else{ ?>
			 <img src="/images/avatar.jpeg" style="padding: 20px;" width="70" height="70" title="Make your own Avatar to use for comments, posts and product reviews">
		<?php } ?>
	</td></tr>
	<tr style="text-align:center;">
		<td class="left_title quarter">Customer ID :</td>
		<td class="quarter"<?=(!empty($member_tooltip)) ? ' style="background:#ffc0cb"' : ''?>>
<?php
if (!empty($member_tooltip)) {
?>
			<svg class="svg-inline--fa fa-question-circle fa-w-16" style="margin-left:10;font-size:15px;float:right" onmouseover="<?=htmlspecialchars('tt_in(' . json_encode($member_tooltip) . ')')?>" onmouseout="tt_out()" aria-hidden="true" focusable="false" data-prefix="far" data-icon="question-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 448c-110.532 0-200-89.431-200-200 0-110.495 89.472-200 200-200 110.491 0 200 89.471 200 200 0 110.53-89.431 200-200 200zm107.244-255.2c0 67.052-72.421 68.084-72.421 92.863V300c0 6.627-5.373 12-12 12h-45.647c-6.627 0-12-5.373-12-12v-8.659c0-35.745 27.1-50.034 47.579-61.516 17.561-9.845 28.324-16.541 28.324-29.579 0-17.246-21.999-28.693-39.784-28.693-23.189 0-33.894 10.977-48.942 29.969-4.057 5.12-11.46 6.071-16.666 2.124l-27.824-21.098c-5.107-3.872-6.251-11.066-2.644-16.363C184.846 131.491 214.94 112 261.794 112c49.071 0 101.45 38.304 101.45 88.8zM298 368c0 23.159-18.841 42-42 42s-42-18.841-42-42 18.841-42 42-42 42 18.841 42 42z"></path></svg>
<?php
}
?>
			<?=htmlspecialchars(memberFormatNumber($member_id))?>
		</td>
		<td class="left_title quarter">Last Order :</td>
		<td class="quarter"><?=(is_array($last_order) && count($last_order)) ? date(_('d/m/Y'), $last_order['open_time']) : 'Never'?></td>
	</tr>
	<tr style="text-align:center;">
		<td class="left_title quarter">Company Name :</td>
		<td class="quarter"><?=htmlspecialchars($member['company_name'])?></td>
		<td class="left_title quarter">Total Orders :</td>
		<td class="quarter"><a target="_blank" href="/webadmin/invoice/?action=list&amp;status=sales|proforma|processed&amp;member_id=<?=$member_id?>"><?=count($processed_invoices)?></a> (<?=$percent?> %)</td>
	</tr>
	<tr style="text-align:center;">
		<td class="left_title quarter">Contact Person :</td>
		<td class="quarter"><?=htmlspecialchars($member['first_name'] . ' ' . $member['last_name'])?></td>
		<td class="left_title quarter">Last Inquiry :</td>
		<td class="quarter"><?=(is_array($last_inquiry) && count($last_inquiry)) ? date(_('d/m/Y'), $last_inquiry['open_time']) : 'Never'?></td>
	</tr>
	<tr style="text-align:center;">
		<td class="left_title quarter">Account Type :</td>
		<td class="quarter">
			<?=('company' == $member['account_type']) ? 'Commercial Customer' : 'Residential Customer'?>
		</td>
<!--
		<td class="left_title quarter">Member Type :</td>
		<td class="quarter">
<?php
$site_list = memberGetOptionList('site_id');
foreach (explode(",", $member['site_id']) as $site_id) {
    if ('' != $site_id) {
?>
			<i class="favicon-<?=$site_id?>" title="<?=htmlspecialchars($site_list[$site_id])?>"></i><?=($member_discounts[$site_id] > 0) ? sprintf('&nbsp;%d%%&nbsp;', $member_discounts[$site_id]) : ''?>
<?php
    }
}
?>
		</td>
-->
		<td class="left_title quarter">Total Inquiries/Invoices :</td>
		<td class="quarter"><a target="_blank" href="/webadmin/invoice/?action=list&amp;member_id=<?=$member_id?>"><?=count($all_invoices)?></a></td>
	</tr>
	<tr style="text-align:center;">
		<td class="left_title quarter">Date Joined :</td><td class="quarter"><?=date(_('d/m/Y'), strtotime($member['created_at']))?></td>
		<td class="left_title quarter">Last Login :</td><td class="quarter"></td>
	</tr>
	<tr style="text-align:center;">
		<td class="left_title quarter">Signup IP Address :</td>
		<td class="quarter">
			<a href="https://www.ip2location.com/<?=$member['signup_IP']?>" target="_blank"><?=$member['signup_IP']?></a>
			<i class="flagicon-<?=strtolower(stripslashes($cs['a2']))?>" title="<?=htmlspecialchars($cs['country'])?>"></i>
		</td>
		<td class="left_title quarter">Total Logins :</td>
		<td class="quarter"></td>
	</tr>
	<tr style="text-align:center;">
		<td valign="top" class="left_title quarter" rowspan="2">BPAY :</td>
		<td rowspan="2" valign="top" class="quarter"><?=('AU' == $member['country']) ? 'Biller Code: 424606<br />' . bpayGenerateNumber($member['member_id']) : 'N/A'?></td>
		<td class="left_title quarter">Purchase Total :</td><td class="quarter"><?=$sale . ' ' . (('AU' == $member['country']) ? 'AUD' : 'USD')?></a></td>
	</tr>
	<tr style="text-align:center;"><td class="left_title quarter">Outstanding Payment :</td><td class="quarter"><?=$balance . ' ' . (('AU' == $member['country']) ? 'AUD' : 'USD')?></a></td>
	<tr style="text-align:center;">
<?php
$display = array();
$chunkdata = invoiceChunk($all_invoices, 'invoice_action');
$invoice_open_new=array();
foreach ($chunkdata as $key => $data) {
		if($key != 'cancelled' && $key !='processed' && $key !='return_completed' && $key != 'nointerest'){
			$invoice_open_new = array_merge($invoice_open_new, $data);
		}
}
$invoice_open_new  = array_reverse(invoiceSort($invoice_open_new, 'open_date'));
$merged  = array_merge($open_invoices, $open_inquiries);
foreach ($merged as $inv_id) {
    $inv              = $invoice_cache[$inv_id];
    $inv_products     = $invoiceP_cache[$inv_id];
    $products_tooltip = array();
    $inv_currency     = $inv['invoice_currency'];
    foreach ($inv_products as $product) {
        $products_tooltip[] =
        '<tr>' .
        '<td style="text-align:right;">' . $product['product_qty'] . '</td>' .
        '<td>x</td>' .
        '<td nowrap>#' . $product['product_id'] . ': ' . $product['product_name'] . '</td>' .
        '<td>@</td>' .
        '<td style="text-align:right;">' . convertCurrency($product['product_offer'], $inv_currency) . '</td>' .
        '<td>=' . $symbol . '</td>' .
        '<td style="text-align:right;">' . convertCurrency($product['product_offer'] * $product['product_qty'], $inv_currency) . '</td>' .
            '<td>' . $inv_currency . '</td>' .
            '</tr>';
    }
    $ship_offer = convertCurrency($inv['ship_offer'], $inv_currency);
    if ('' != $inv['ship_product_name']) {
        $products_tooltip[] = '<tr><td style="text-align:right;">1</td><td>x</td><td nowrap>Freight: ' . $invoice['ship_product_name'] . '</td><td>@</td> <td>' . $ship_offer . '</td><td>=' . $symbol . '</td><td style="text-align:right;">' . $ship_offer . '</td><td>' . $inv_currency . '</td></tr>';
    }

    $products_tooltip = '<table>' . implode('', $products_tooltip) . '</table>';
    $display[]        = '<a onmouseover="' . htmlspecialchars('tt_in(' . json_encode($products_tooltip) . ')') . '" onmouseout="tt_out()" target="_blank" href="/webadmin/invoice/?action=edit&amp;invoice_id=' . $inv_id . '">' . invoiceFormatNumber($inv_id, 0, $inv['site_id']) . '</a>';
}
?>
		<td class="left_title quarter">Open Invoices/Inquiries :</td><td class="quarter"><?=implode(', ', $display)?></td>
		<td class="left_title quarter">Status :</td>
		<td class="quarter" style="background:<?=('active' == $member['status']) ? '#0f0' : '#ffc0cb'?>"><?=('active' == $member['status']) ? 'Active' : 'Deactive'?></td>
	</tr>
</table></div><br />
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
moduleLoad('member'); //memberFromDatabase

$preview_time  = mktime(0, 0, 0);
$handler_list          = invoiceGetOptionList('handler_id');
$payment_received_list = invoiceGetOptionList('payment_received');
$products_located_list = invoiceGetOptionList('products_located');
$ship_located_list     = invoiceGetOptionList('ship_located');
$site_list             = invoiceGetOptionList('site_id');

$colour_list = array(
    "n"          => '<b style="color:red;">%s</b>',
    "y"          => '<b style="color:green;">%s</b>',
    "yp"         => '<b style="color:#0c0;">%s</b>',

    "approval"   => '<b style="color:blue;">%s</b>',
    "cancelled"  => '<b style="color:red;">%s</b>',
    "feedback"   => '<b style="color:green;">%s</b>',
    "nohelp"     => '<b style="color:red;">%s</b>',
    "nointerest" => '<b style="color:red;">%s</b>',
    "payment"    => '<b style="color:blue;">%s</b>',
    "processed"  => '<b style="color:blue;">%s</b>',
    "sales"      => '<b style="color:blue;">%s</b>',
    "shipped"    => '<b style="color:blue;">%s</b>',
    "shipping"   => '<b style="color:blue;">%s</b>',
    "working"    => '<b style="color:green;">%s</b>',

    'Open'       => '<b style="color:green;">%s</b>',
    'Proforma'   => '<b style="color:blue;">%s</b>',
    'Processed'  => '<b style="color:blue;">%s</b>',
    'Closed'     => '<b style="color:red;">%s</b>',
);

$invoice_action_list = invoiceGetOptionList("invoice_action");

$payment_due      = $invoice['invoice_offer'];
$payment_received = $invoice["payments_located"];
if (is_array($invoice_payments)) {
    foreach ($invoice_payments as $payment) {
        $payment_due = bcsub($payment_due, $payment['payment_amount'], 2);
    }

    $payment_received = (bccomp($payment_due, $invoice['invoice_offer'], 2) == 0) ? 'n' : ((bccomp($payment_due, "0.01", 2) < 0) ? 'y' : 'yp');
}

$offer = $invoice['invoice_offerau'] . ' AUD';
$prof  = $invoice['invoice_profitau'] . ' AUD';
if (in_array($posted['site_id'], array(3, 20, 26,39))) {
    $offer = convertCurrency($invoice['invoice_offer'], $invoice['invoice_currency'], 'USD') . ' USD';
    $prof  = convertCurrency($invoice['invoice_profit'], $invoice['invoice_currency'], 'USD') . ' USD';
}

if (!array_key_exists($invoice["handler_id"], $handler_list)) {
    $invoice['handler_id'] = $_GLOBALS['staff']['id'];
}

$item_type = ($display_inquiry) ? 'Inquiry' : 'Invoice';
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionInvoiceSummary\');return false">' . $item_type . ' Summary</a>')?>
<script type="text/javascript">
//<![CDATA[
$(function() {
	$("[name='handler_note']").change(function() {
		$("#summary_handler_date").text(<?= jecho(date('d/m/Y', $_SERVER['REQUEST_TIME'])) ?>);
	});
	$("[name='display_to_customer']").on('click', function() {
		$("#summary_display_to_customere").text(<?= jecho(date('d/m/Y', $_SERVER['REQUEST_TIME'])) ?>);
	});
	$("[name='processed_order']").change(function() {
		if ($(this).is(':checked')) {
            $("[name='display_to_customer']").val('Your order has been shipped, thanks very much for your purchase');
            $("#summary_display_to_customere").text(<?= jecho(date('d/m/Y', $_SERVER['REQUEST_TIME'])) ?>);
        } else {
            $("[name='display_to_customer']").val('');
            $("#summary_display_to_customere").text('');
        }
		
	});
});
//]]>
</script>
<div id="sectionInvoiceSummary">
	<table class="wide">
		<tr>
			<td class="left_title" style="width:25%;"><?=$item_type?> #</td>
			<td style="text-align:center;width:25%;"><a href="<?=htmlspecialchars(".?action=view&invoice_id=" . $invoice_id . "&invoice_time=" . $preview_time)?>" target="_blank"><?=htmlspecialchars(invoiceFormatNumber($invoice_id, $invoice['revision_id'], $invoice['site_id']))?></a></td>
			<td class="left_title" style="width:25%;">Date of <?=$item_type?></td>
			<td style="text-align:center;width:25%;"><?=date('d/m/Y', strtotime($invoice['open_date']))?></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;">Customer Name</td>
			<td style="text-align:center;width:25%;"><a href="<?=htmlspecialchars("/webadmin/member/?action=edit&member_id=" . $invoice['member_id'])?>" target="_blank"><?=htmlspecialchars($invoice['bill_name'])?></a></td>
			<td class="left_title" style="width:25%;"><?='<input type="button" onclick="toggleSection(\'site_link\'); toggleSection(\'site_id\');" value="Change" />&nbsp;'?>Company</td>
			<td style="text-align:center;width:25%;"><a id="site_link" href="<?=htmlspecialchars("/webadmin/invoice/?action=list&site_id=" . $invoice['site_id'])?>" target="_blank"><?=htmlspecialchars($site_list[$invoice['site_id']])?></a> <select style="width:100%;display:none;" name="site_id"><?=formatHTMLOptions($site_list, $invoice['site_id'])?></select></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;"><?=$item_type?> Value</td>
			<td style="text-align:center;width:25%;"><?=$offer?></td>
			<td class="left_title" style="width:25%;">Last Staff</td>
			<?php 
				$invoice_messages=invoiceHistoryCustomerMessage($invoice["write_date"],$invoice_id);
				if (is_array($invoice_messages) && count($invoice_messages)) { ?>
						<td style="text-align:center;width:25%;background-color:#def;"><?=nl2br(htmlspecialchars('CUSTOMER'))?> <i><?=date('d/m/Y', $invoice['write_time'])?></i></td>
						
			<?php   } else { ?>
				<td style="text-align:center;width:25%;"><?=htmlspecialchars($all_staff_list[$invoice['staff_id']])?> <i><?=date('d/m/Y', $invoice['write_time'])?></i></td>

			<?php }
			?>

		</tr>
		<tr>
			<td class="left_title" style="width:25%;">Profit</td>
			<td style="text-align:center;width:25%;"><?=$prof?></td>
			<td class="left_title" style="width:25%;">Products Located</td>
			<td style="text-align:center;width:25%;"><?=(array_key_exists($invoice['products_located'], $colour_list)) ? sprintf($colour_list[$invoice['products_located']], htmlspecialchars($products_located_list[$invoice['products_located']])) : htmlspecialchars($products_located_list[$invoice['products_located']])?></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;">Quick Summary</td>
			<td style="text-align:center;width:25%;"><a href="<?=htmlspecialchars(".?action=summary&invoice_id=" . $invoice_id)?>" target="_blank">View</a></td>
			<td class="left_title" style="width:25%;">Freight Located</td>
			<td style="text-align:center;width:25%;"><?=(array_key_exists($invoice['ship_located'], $colour_list)) ? sprintf($colour_list[$invoice['ship_located']], htmlspecialchars($ship_located_list[$invoice['ship_located']])) : htmlspecialchars($ship_located_list[$invoice['ship_located']])?></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;">Days Open</td>
			<td style="text-align:center;width:25%;"><?=$invoice['days_open']?></td>
			<td class="left_title" style="width:25%;">Payment Received</td>
			<td style="text-align:center;width:25%;"><?=(array_key_exists($payment_received, $colour_list)) ? sprintf($colour_list[$payment_received], htmlspecialchars($payment_received_list[$payment_received])) : htmlspecialchars($payment_received_list[$payment_received])?></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;"><?=$item_type?> Handler</td>
			<td style="text-align:center;"><select name="handler_id"><?=formatHTMLOptions($handler_list, $invoice['handler_id'])?></select></td>
			<td class="left_title" style="width:25%;">Status</td>
			<td style="text-align:center;width:25%;"><?=$invoice_action_list[$invoice['invoice_action']]?></td>
		</tr>
		<tr style="margin-top: 20px;">
			<td class="left_title" style="width:25%;padding-top:20px">Handler Note</td>
			<td style="width:50%;padding-top:20px;" colspan="2"><div  style="display: flex; width: 100%;"><span style="padding: 2px;" id="summary_handler_date"><?= ($invoice['handler_date']!='0000-00-00 00:00:00') ? date('d/m/Y', strtotime($invoice['handler_date'])) : '' ?></span> <input name="handler_note"  type="text" value="<?=htmlspecialchars($invoice['handler_note'])?>" maxlength="88" style="width: inherit;"/></div></td>
			<td style="padding-top:20px;padding-right:10px;float:right;">
				<a href="/webadmin/invoice/invoice_products_list.php?invoice_id=<?=$invoice_id?>" target="_blank">
					<input type="button" value="Order Summary">
				</a>
			</td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%">Online Display to Customer
			<i class="far fa-question-circle" onmouseover="tt_in('Will Display Live In Customer’s My Dashboard');" onmouseout="tt_out();"></i>

				<!-- <i style="font-size: 9px;">Will Display Live In Customer’s My Dashboard</i> -->
			</td>
			<td style="width:50%;padding-top:4px;" colspan="2"><div style="display: flex; width: 100%;"><span style="padding: 2px;" id="summary_display_to_customere"><?= ($invoice['display_to_date']!='0000-00-00 00:00:00') ? date('d/m/Y', strtotime($invoice['display_to_date'])) : '' ?></span> <input name="display_to_customer" type="text" value="<?=htmlspecialchars($invoice['display_to_customer'])?>" maxlength="88" style="width: inherit;"/>
			</div>
			</td>
			<td style="display: flex; justify-content: space-between; align-items: center;padding-right:10px;">
				<div style="display: flex; align-items: center;">
					<input type="checkbox" name="processed_order" value="y" <?= $invoice['processed_order'] == 'y' ? 'checked' : '' ?> 
						style="width: 18px; height: 18px; margin-right: 10px;" />
					<span style="font-size: 12px; font-style: italic;">Processed Order</span>
				</div>
				<a href="/webadmin/member/?action=bill_ship&member_id=<?=$invoice['member_id']?>" target="_blank">
					<input type="button" value="Shipping Address">
				</a>
			</td>

		</tr>

<?php
if (0 != $invoice["tax_id"]) {
    $view2_url           = $get_vars;
    $view2_url['action'] = 'tax_view';
    unset($view2_url['invoice_id']);
    $view2_url = '.?' . http_build_query($view2_url) . '&invoice_id=';
?>
	<tr>
		<td class="left_title">&nbsp;</td>
		<td>&nbsp;</td>
		<td class="left_title">&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">Tax Invoice #</td>
		<td style="text-align:center;width:25%;"><a href="<?=htmlspecialchars($view2_url . $invoice_id)?>" target="_blank"><?php printf("T#%05d", $invoice["tax_id"])?></a></td>
		<td class="left_title" style="width:25%;">Tax Invoice Date</td>
		<td style="text-align:center;width:25%;"><?=date("d/m/Y", $invoice["tax_time"])?></td>
	</tr>
<?php
}
if (count($invoice_links) > 0) {
?>
	<tr>
		<td colspan="4" style="background-color:#EEEEAA;text-align:center;">This <?=$item_type?> is linked with
<?php
foreach ($invoice_links as $link) {
?>
			<a href="<?=htmlspecialchars(".?action=view&invoice_id=" . $link['invoice_id'])?>" target="_blank"><?=invoiceFormatNumber($link['invoice_id'])?></a>
<?php
}
?>
		</td>
	</tr>
<?php
}
?>
	<!-- <tr><td colspan="4" class="unfinished">Add Product/Order helpers</td></tr> -->
</table>
<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionInvoiceSummary_hideNote">
	<?=$item_type?> Summary can be viewed by Clicking "<?=$item_type?> Summary" above<br />
</div>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionMemberHistory\');">Member History</a>')?>
<div id="sectionMemberHistory">
<?php
$member_info = memberFromDatabase($invoice['member_id'], true);

$member_invoices = count(invoiceSearch(array('member_id' => $invoice['member_id'], 'invoice_status' => array('proforma', 'sales', 'processed', 'cancelled'))));
$member_orders   = count(invoiceSearch(array('member_id' => $invoice['member_id'], 'invoice_status' => 'processed')));

$member_invoices_url = '.?action=list&status=proforma|sales|processed|cancelled&member_id=' . $invoice['member_id'];
$member_orders_url   = '.?action=list&status=processed&member_id=' . $invoice['member_id'];
?>
<table class="wide">
	<tr style="background-color:#D8D8D8;">
		<td class="left_title" style="background-color:#FFEEEE;width:25%;">Company</td>
		<td style="width:25%;text-align:center;"><?=$member_info['company_name']?></td>
		<td class="left_title" style="background-color:#FFEEEE;width:25%;">Date Joined</td>
		<td style="width:25%;text-align:center;"><?=date("d/m/Y", $member_info['time_joined'])?></td>
	</tr>
	<tr style="background-color:#D8D8D8;">
		<td class="left_title" style="background-color:#FFEEEE;width:25%;">Total Invoices</td>
		<td style="width:25%;text-align:center;"><a href="<?=$member_invoices_url?>" target="_blank"><?=$member_invoices?></a></td>
		<td class="left_title" style="background-color:#FFEEEE;width:25%;">Value of Invoices</td>
		<td style="width:25%;text-align:center;"><?= $member_info['invoice_totalus']?> </td>
	</tr>
	<tr style="background-color:#D8D8D8;">
		<td class="left_title" style="background-color:#FFEEEE;width:25%;">Total Orders</td>
		<td style="width:25%;text-align:center;"><a href="<?=$member_orders_url?>" target="_blank"><?=$member_orders?></a></td>
		<td class="left_title" style="background-color:#FFEEEE;width:25%;">Value of Orders</td>
		<td style="width:25%;text-align:center;"><?= $member_info['order_total'] ?></td>
	</tr>
	<tr style="background-color:#D8D8D8;">
		<td class="left_title" style="background-color:#FFEEEE;width:25%;">Last Contact</td>
		<td style="width:25%;text-align:center;"><?=($member_info['time_last_contacted'] > 0) ? date('d/m/Y', $member_info['time_last_contacted']) : 'Not Contacted'?></td>
		<td class="left_title" style="background-color:#FFEEEE;width:25%;">Last Order</td>
		<td style="width:25%;text-align:center;"><?=$member_info['last_order_time']?></td>
	</tr>
</table>
<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionMemberHistory_hideNote">
	Member History can be viewed by Clicking "Member History" above<br />
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

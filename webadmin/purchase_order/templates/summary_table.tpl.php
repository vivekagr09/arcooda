<!-- summary_table.tpl -->

<?php
global $_GLOBALS;
$site_list             = invoiceGetOptionList("site_id");
$payment_received_list = invoiceGetOptionList("payment_received");
$products_located_list = invoiceGetOptionList("products_located");
$ship_located_list     = invoiceGetOptionList("ship_located");
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

$invoice_action_list = array(
    // 'working'    => 'Working On Enquiry',
    'feedback'   => 'Waiting Customer Feedback',
    'nohelp'     => 'Closed - Unable to help Customer',
    'nointerest' => 'Customer Not Interested',
    'sales'      => 'Invoice',
    'shipped'    => 'Goods Shipped',
    'cancelled'  => 'Cancelled',
    // 'payment'    => 'Awaiting Payment',
    'approval'   => 'Awaiting Payment Approval',
    'shipping'   => 'Awaiting Shipping',
    // 'processed'  => 'Processed',

    'Closed'     => 'Closed',
    'Open'       => 'Open',
    // 'Processed'  => 'Processed',
    'Proforma'   => 'P/Invoice',

	'working'    => 'Working on Purchase Inquiry',
	'supplier'   => 'Awaiting Supplier',
	'order'   => 'Make Purchase Order',
	'order-working'    => 'Working on Purchase Order',
	'supplier-waiting'   => 'Waiting Supplier',
	'payment'   => 'Arranging Payment',
	'goods'  => 'Waiting Goods',
	'checking-goods'  => 'Checking Goods',
	'processed'  => 'Processed',
	'awaiting-order'   => 'Awaiting Order Approval',
);

$producttype_urls = [
    1 => 'Arcade-Parts',
    2 => 'Arcade-Machines',
    3 => 'Arcade-Game-Boards-and-Software',
    4 => 'Merchandises',
];

$url = $producttype_urls[$invoice['producttype_id']] ?? '';

// $product_types = invoiceGetOptionList('producttype_id');
$product_types = getBaseProductTypes();
// echo "<pre>"; print_r($invoice); die;

$payment_due      = $invoice['invoice_offer'];
$payment_received = $invoice["payments_located"];

if (is_array($invoice_payments) && count($invoice_payments) > 0) {
    foreach ($invoice_payments as $payment) {
        $payment_due = bcsub($payment_due, $payment['payment_amount'], 2);
    }

    $payment_received = (bccomp($payment_due, $invoice['invoice_offer'], 2) == 0) ? 'n' : ((bccomp($payment_due, "0.01", 2) < 0) ? 'y' : 'yp');

}

$permissions = []; //getInquiryPermissions($_GLOBALS['staff']['user']);

$offer = convertCurrency($invoice['total_invoice_cost'], $invoice['invoice_currency'], 'USD') . ' USD';
$actualOffer = $invoice['total_invoice_cost']. ' ' . $invoice['invoice_currency'];

$prof  = convertCurrency($invoice['invoice_profit'], $invoice['invoice_currency'], 'USD') . ' USD';

if (!array_key_exists($invoice["handler_id"], $active_staff_list)) {
    $invoice['handler_id'] = $staff_id;
}

$item_type = ($display_enquiry) ? 'Enquiry' : 'Invoice';

$last_invoice = $DB->query_first("SELECT * from `invoices` WHERE `member_id` = " . intval($invoice['member_id']) . " ORDER BY `invoice_id` DESC");


if (count($last_contact = purchaseOrderHistorySearch(array('order_id' => $order_id, 'message_type_id' => array(INVOICE_MESSAGE_TO_CUSTOMER, INVOICE_MESSAGE_INVOICE_SENT))))) {
	$last_contact = purchaseOrderHistoryFromDatabase(reset($last_contact));
	$last_contact = date('d/m/Y', $last_contact['message_time']);
} else {
	$last_contact = 'Not Contacted';
}
?>

<script type="text/javascript">
//<![CDATA[
$(function() {
	$("[name='handler_note'],[name='handler_status']").change(function() {
		$("#summary_handler_date").text(<?= jecho(date('d/m/Y', $_SERVER['REQUEST_TIME'])) ?>);
	});
	$("[name='handler_status']").change(function() {
		$("[name='invoice_action']").val($(this).val());
	});
});
//]]>
</script>

<?= formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionInvoiceSummary\');return false">Purchase ' . $item_type . ' Summary</a>') ?>

<div id="sectionInvoiceSummary">
	<table class="wide">
		<tr>
			<td class="left_title" style="width:25%;">Purchase Order #</td>
			<td style="text-align:center;width:25%;"><a href="<?php hecho("index.php?action=view&order_id=" . $order_id)?>" target="_blank"><?php hecho(purchaseOrderFormatNumber($order_id, $invoice['revision_id'], $invoice['site_id']))?></a></td>
			<td class="left_title" style="width:25%;">Date of Purchase Order</td>
			<td style="text-align:center;width:25%;"><?= date('d/m/Y', max($invoice['proforma_time'], $invoice['open_time'])) ?></td>
		</tr>

		<tr>
			<td class="left_title" style="width:25%;"></td>
			<td style="text-align:center;width:25%;"></td>
			<td class="left_title" style="width:25%;">Last Contact</td>
			<td style="text-align:center;width:25%;"><span><?= $last_contact ?></span></td>
		</tr>

		<tr>
			<td class="left_title" style="width:25%;">Supplier Name</td>
			<td style="text-align:center;width:25%;"><a href="<?php hecho("/webadmin/supplier_nes/?action=edit&supplier_id=" . $invoice['supplier_id'])?>" target="_blank"><?php hecho($invoice['bill_name'])?></a></td>
			<td class="left_title" style="width:25%;">Last Staff</td>
			<td style="text-align:center;width:25%;"><?php hecho($all_staff_list[$invoice['staff_id']])?></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;">Purchase Order Value</td>
			<td style="text-align:center;width:25%;"><?= $actualOffer ?></td>
			<td class="left_title" style="width:25%;">Category</td>
			<td style="text-align:center;width:25%;">
				<!-- <a href="<?php hecho("index.php?action=list&producttype_id=" . $invoice['producttype_id'])?>" target="_blank"><?= $product_types[$invoice['producttype_id']] ?></a> -->
				<a href="/buy/<?= $url ?>" target="_blank"><?= $product_types[$invoice['producttype_id']] ?></a>
			</td>
		</tr>
		<?php if($invoice['invoice_currency'] != 'USD') { ?>
		<tr>
			<td class="left_title" style="width:25%;">Purchase Order Value</td>
			<td style="text-align:center;width:25%;"><?= $offer ?></td>
			<td class="left_title" style="width:25%;"></td>
			<td style="text-align:center;width:25%;"></td>
		</tr>
		<?php } ?>
		<tr>
			<td class="left_title" style="width:25%;"><?= (in_array(2, $permissions)) ? 'Profit' : '' ?></td>
			<td style="text-align:center;width:25%;"><?= (in_array(2, $permissions)) ? $prof : '' ?></td>
			<td class="left_title" style="width:25%;">Order Approved</td>
			<td style="text-align:center;width:25%;">
				<?= ($invoice['order_action']=='processed') ? sprintf($colour_list['y'], hecho($products_located_list['y'], 1)) :sprintf($colour_list['n'], hecho($products_located_list['n'], 1)) ?></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;">Quick Summary</td>
			<td style="text-align:center;width:25%;"><a href="<?php hecho("index.php?action=summary&order_id=" . $order_id)?>" target="_blank">View</a></td>
			<td class="left_title" style="width:25%;">Payment Made</td>
			<td style="text-align:center;width:25%;">
				<?php if(count($invoice_payments) > 0 && $invoice['payments_located'] == 'n') 
				{
					echo 'Deposit';
				}else{
					 echo ('y' == $invoice['payments_located']) ? sprintf($colour_list['y'], hecho($products_located_list['y'], 1)) :sprintf($colour_list['n'], hecho($products_located_list['n'], 1));
				}
				?>
					
					
			</td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;">Days Open</td>
			<td style="text-align:center;width:25%;"><?= $invoice['days_open'] ?></td>
			<td class="left_title" style="width:25%;">Goods Received</td>
			<td style="text-align:center;width:25%;"><?= ('y' == $invoice['goods_checked'] ) ? sprintf($colour_list['y'], hecho($products_located_list['y'], 1)) :sprintf($colour_list['n'], hecho($products_located_list['n'], 1)) ?></td></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;"><?= $item_type ?> Handler</td>
			<td><select name="handler_id"><?= formatHTMLOptions($active_staff_list, $invoice['handler_id']) ?></select></td>
			<td class="left_title" style="width:25%;">Status</td>
			<td style="text-align:center;width:25%;"><?= $invoice_action_list[$invoice['order_action']] ?></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;">Handler Note</td>
			<td style="text-align:center;width:25%;" colspan="3"><select name="handler_status" style="width:100%;">
				<option value=""></option>
				<?= formatHTMLOptions(purchaseOrderGetOptionList('handler_status'), $invoice['handler_status']) ?>
			</select></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;">Handler Comments</td>
			<td colspan="3">
				<div id="summary_handler_date" style="display:inline-block;"><?= ($invoice['handler_time']) ? date('d/m/Y', $invoice['handler_time']) : '' ?></div>
				<input name="handler_note" style="width:80%;" type="text" value="<?php hecho($invoice['handler_note'])?>" maxlength="88" />
			</td>
		</tr>

<?php
if (0 != $invoice["tax_id"]) {
    $view2_url           = $get_vars;
    $view2_url['action'] = 'tax_view';
    unset($view2_url['invoice_id']);
    $view2_url = 'index.php?' . http_build_query($view2_url) . '&invoice_id=';
?>
	<tr>
		<td class="left_title">&nbsp;</td>
		<td>&nbsp;</td>
		<td class="left_title">&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">Tax Invoice #</td>
		<td style="text-align:center;width:25%;"><a href="<?php hecho($view2_url . $invoice_id)?>" target="_blank"><?php printf("T#%05d", $invoice["tax_id"])?></a></td>
		<td class="left_title" style="width:25%;">Tax Invoice Date</td>
		<td style="text-align:center;width:25%;"><?= date("d/m/Y", $invoice["tax_time"]) ?></td>
	</tr>
<?php
}
if (is_array($invoice_links) && count($invoice_links) > 0) {
?>
	<tr>
		<td colspan="4" style="background-color:#EEEEAA;text-align:center;">This <?= $item_type ?> is linked with
<?php
foreach ($invoice_links as $link) {
?>
			<a href="<?php hecho("index.php?action=view&invoice_id=" . $link['invoice_id'])?>" target="_blank"><?= invoiceFormatNumber($link['invoice_id']) ?></a>
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
	<?= $item_type ?> Summary can be viewed by Clicking "<?= $item_type ?> Summary" above<br />
</div>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionMemberHistory\');">Supplier History</a>') ?>
<div id="sectionMemberHistory">
<?php
//$member_info = memberFromDatabase($invoice['member_id'], true);

$criteria          = array('supplier_id' => $invoice['supplier_id'], 'order_status' => array('processed', 'proforma', 'sales'));
$member_enquiries  = purchaseOrderSearch($criteria);
$sum_value_enquiry = purchaseOrderSearch($member_enquiries);
$criteria['order_status'] = 'processed';
$member_orders              = purchaseOrderSearch($criteria);
$member_orders              = array_reverse(invoiceSort($member_orders, 'date_added'));
$sum_value_orders           = purchaseOrderSearch($member_enquiries);
$last_order_time            = invoiceGetValue($member_orders[0], 'last_order_time');

$member_enquiry_url = 'index.php?action=list&amp;status=enquiry|proforma|sales&amp;member_id=' . $invoice['supplier_id'];
$member_orders_url  = 'index.php?action=list&amp;status=processed&amp;member_id=' . $invoice['supplier_id'];

if (count($last_contact = purchaseOrderHistorySearch(array('order_id' => purchaseOrderSearch(array('supplier_id' => $invoice['supplier_id'])), 'message_type_id' => array(INVOICE_MESSAGE_TO_CUSTOMER, INVOICE_MESSAGE_INVOICE_SENT))))) {
	$last_contact = purchaseOrderHistoryFromDatabase(reset($last_contact));
	$last_contact = date('d/m/Y', $last_contact['message_time']);
} else {
	$last_contact = 'Not Contacted';
}

?>
<table class="wide">
	<tr style="background-color:#D8D8D8;">
		<td class="left_title" style="background-color:#FFEEEE;width:25%;">Company</td>
		<td style="width:25%;text-align:center;"><?= $member['company'] ?></td>
		<td class="left_title" style="background-color:#FFEEEE;width:25%;">Date Joined</td>
		<td style="width:25%;text-align:center;"><?= date("d/m/Y", strtotime($member['created'])) ?></td>
	</tr>
	<tr style="background-color:#D8D8D8;">
		<td class="left_title" style="background-color:#FFEEEE;width:25%;">Total Enquiries</td>
		<td style="width:25%;text-align:center;"><a href="<?= $member_enquiry_url ?>" target="_blank"><?= count($member_enquiries) ?></a></td>
		<td class="left_title" style="background-color:#FFEEEE;width:25%;">Value of Enquiries</td>
		<td style="width:25%;text-align:center;">$<?= sprintf('%.2f', $sum_value_enquiry) ?> USD</td>
	</tr>
	<tr style="background-color:#D8D8D8;">
		<td class="left_title" style="background-color:#FFEEEE;width:25%;">Total Orders</td>
		<td style="width:25%;text-align:center;"><a href="<?= $member_orders_url ?>" target="_blank"><?= count($member_orders) ?></a></td>
		<td class="left_title" style="background-color:#FFEEEE;width:25%;">Value of Orders</td>
		<td style="width:25%;text-align:center;">$<?= sprintf('%.2f', $member_info['order_totalus']) ?> USD</td>
	</tr>
	<tr style="background-color:#D8D8D8;">
		<td class="left_title" style="background-color:#FFEEEE;width:25%;">Last Contact</td>
		<td style="width:25%;text-align:center;"><?= $last_contact ?></td>
		<td class="left_title" style="background-color:#FFEEEE;width:25%;">Last Order</td>
		<td style="width:25%;text-align:center;"><?= $last_order_time ?></td>
	</tr>
</table>
<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionMemberHistory_hideNote">
	Member History can be viewed by Clicking "Member History" above<br />
</div>

<!-- /summary_table.tpl -->

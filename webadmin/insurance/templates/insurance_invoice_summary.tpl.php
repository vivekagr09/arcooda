<!-- insurance_invoice_summary.tpl.php -->

<?php
$invoice=array();
$invoice_payments=array();
$invoice_links=array();

if($insurance_claim['invoice_id']>0)
{
	$invoice=invoiceFromDatabase($insurance_claim['invoice_id']);
	$invoice_payments=invoicePaymentsFromDatabase($invoice['invoice_id']);
	if($invoice['group_id']>0) $invoice_links=invoiceSearch(array('group_id'=>$invoice['group_id']));
}

$invoice_companies=invoiceGetOptionList('site_id');

$status_array=invoiceGetOptionList("invoice_action");

$yesno=array(
	'y'=>'<span style="color:green"><b>Yes</b></span>',
	'n'=>'<span style="color:red"><b>No</b></span>',
	'yp'=>'<span style="color:#00CC00"><b>Deposit</b></span>'
);

$payment_due=$invoice['invoice_offer'];
foreach($invoice_payments as $payment)
	$payment_due-=$payment['payment_amount'];
if($invoice['invoice_id']>0)
	$payment_received=(($payment_due==$invoice['invoice_offer'])?'n':(($payment_due==0)?'y':'yp'));

?>

<?php echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionInvoiceSummary\');">Invoice Summary</a>') ?>
<div id="sectionInvoiceSummary">

<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;">Invoice #</td>
		<td style="text-align:center;width:25%;" id="invoice[invoice_id]">
<?php
if($invoice['invoice_id']>0)
{
?>
			<a href="/webadmin/invoice/?action=view&invoice_id=<?php echo $invoice['invoice_id'] ?>" target="_blank"><?php echo invoiceFormatNumber($invoice['invoice_id'],$invoice['revision_id'],$invoice['site_id']) ?></a>
<?php
}
?>
		</td>
		<td class="left_title" style="width:25%;">Date of Invoice</td>
		<td style="text-align:center;width:25%;" id="invoice[write_date]"><?php echo ($invoice['invoice_id']>0)?date('d/m/Y',$invoice['write_time']):'' ?></td>
	</tr>

	<tr>
		<td class="left_title">Customer Name</td>
		<td style="text-align:center;" id="invoice[bill_name]"><a href="/webadmin/members/?action=edit&member_id=<?php echo $invoice['member_id'] ?>" target="_blank"><?php hecho($invoice['bill_name']) ?></a></td>
		<td class="left_title">Company</td>
		<td style="text-align:center;" id="invoice[company_name]"><a href="/webadmin/invoice/?action=list&site_id=<?php echo $invoice['site_id'] ?>" target="_blank"><?php echo $invoice_companies[$invoice['site_id']] ?></a></td>
	</tr>

	<tr>
		<td class="left_title">Invoice Value</td>
		<td style="text-align:center;" id="invoice[invoice_offerau]"><?php echo ($invoice['invoice_id']>0)?($invoice['invoice_offerau'].' AUD'):'' ?></td>
		<td class="left_title">Last Staff</td>
		<td style="text-align:center;" id="invoice[last_staff]"><?php hecho($all_staff_list[$invoice['staff_id']]) ?></td>
	</tr>

	<tr>
		<td class="left_title">Profit</td>
		<td style="text-align:center;"<?php echo ' id="invoice[invoice_profitau]"' ?>><?php echo ($invoice['invoice_id']>0)?($invoice['invoice_profitau'].' AUD'):'' ?></td>
		<td class="left_title">Products Located</td>
		<td style="text-align:center;" id="invoice[products_located]"><?php echo $yesno[$invoice['products_located']] ?></td>
	</tr>

	<tr>
		<td class="left_title">Quick Summary</td>
		<td style="text-align:center;" id="invoice[view_url]">
<?php
if($invoice['invoice_id']>0)
{
?>
			<a href="/webadmin/invoice/?action=summary&invoice_id=<?php echo $invoice['invoice_id'] ?>" target="_blank">View</a>
<?php
}
?>
		</td>
		<td class="left_title">Freight Located</td>
		<td style="text-align:center;" id="invoice[ship_located]"><?php echo $yesno[$invoice['ship_located']] ?></td>
	</tr>

	<tr>
		<td class="left_title">Days Open</td>
		<td style="text-align:center;" id="invoice[days_open]"><?php echo $invoice['days_open'] ?></td>
		<td class="left_title">Payment Received</td>
		<td style="text-align:center;" id="invoice[payment_received]"><?php echo $yesno[$payment_received] ?></td>
	</tr>

	<tr>
		<td class="left_title">Invoice Handler</td>
		<td style="text-align:center;" id="invoice[handler_name]"><?php hecho($all_staff_list[$invoice['handler_id']]) ?></td>
		<td class="left_title">Status</td>
		<td style="text-align:center;" id="invoice[invoice_action]"><?php echo $status_array[$invoice['invoice_action']] ?></td>
	</tr>

	<tr>
		<td class="left_title">Handler Note</td>
		<td style="text-align:center;" colspan="3" id="invoice[handler_note]"><?php hecho($invoice['handler_note']) ?></td>
	</tr>

<?php
if(count($invoice_links)>0)
{
?>
	<tr>
		<td colspan="4" style="background-color:#EEEEAA;text-align:center;" id="invoice[invoice_links]">This Invoice is linked with
<?php
	foreach($invoice_links as $link)
	{
?>
			<a href="/webadmin/invoice/?action=view&invoice_id=<?php echo $link['invoice_id'] ?>" target="_blank"><?php echo invoiceFormatNumber($link['invoice_id']) ?></a>
<?php
	}
?>
		</td>
	</tr>
<?php
}
?>
</table>
<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionInvoiceSummary_hideNote">
	Invoice Summary can be viewed by Clicking "Invoice Summary" above<br />
</div>

<!-- /insurance_invoice_summary.tpl.php -->

<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceSearch, invoiceGetOptionList, invoiceFromDatabase, invoiceFormatNumber

$contractor_id = $posted['contractor_id'];
$contractor    = contractorFromDatabase($contractor_id);
$orders    = invoiceSearch(array('contractor_id' => $contractor_id));
$shipping  = contractorShippingAddressesFromDatabase($contractor_id);
$status    = invoiceGetOptionList('invoice_action');
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title></title>
		<script type="text/javascript">
//<![CDATA[
var windowObj = this;
function printpage() {
	if (window.print) window.print();
	else alert('To print this page, select Print Frame from the file menu.');
}
function doPrint() {
	if (confirm("Click OK to print this document")) printpage();
}
//]]>
		</script>
		<style type="text/css">
/*<![CDATA[*/
body {width:7in;margin:0.5in auto;}
table {width:100%;border-collapse:collapse;}
td,th {border:1px solid #ccc;}
/*]]>*/
		</style>
	</head>
	<body style="background-color:#fff;" onLoad="doPrint()">

<div class="contractor_content"><div class="wide">
	<center><h2>contractor Summary</h2></center>

	<h3>Customer Details:</h3>
	<table>
		<tr>
			<th style="width:25%;">Customer ID :</th>
			<td><?php hecho(contractorFormatNumber($contractor['contractor_id']))?></td>
		</tr>
<?php
if ('' != $contractor['company']) {
?>
		<tr>
			<th>Company :</th>
			<td><?php hecho($contractor['company'])?></td>
		</tr>
<?php
}
?>
		<tr>
			<th>Contact Details :</th>
			<td><?php hecho($contractor['full_name'])?><br /><?=nl2br(hecho($contractor['address_full'], 1))?><br />Mobile: <?php hecho($contractor['mobile_number'])?></td>
		</tr>
	</table>
	<br />

	<h3>Customer Shipping Details:</h3>
	<table>
<?php
foreach ($shipping as $ship) {
?>
		<tr>
			<th style="width:25%;">Shipping Address :</th>
			<td><?=nl2br(hecho($ship['address_full'], 1))?><br />Mobile: <?php hecho($ship['mobile_no'])?><br />Contact Person: <?php hecho($ship['contact'])?><br />Email: <?php hecho($ship['email'])?></td>
		</tr>
<?php
}
?>
	</table>
	<br />

	<h3>Customer Orders:</h3>
	<table>
		<tr>
			<th>Date</th>
			<th>Invoice #</th>
			<th>Value</th>
			<th>Status</th>
		</tr>
<?php
foreach ($orders as $order_id) {
    $order = invoiceFromDatabase($order_id);
?>
		<tr>
			<td><?=date("d/m/y", $order['open_time'])?></td>
			<td><?php hecho(invoiceFormatNumber($order['invoice_id']))?></td>
			<td>$<?=convertCurrency($order['invoice_offerus'], "USD")?></td>
			<td><?php hecho($status[$order['invoice_action']])?></td>
		</tr>
<?php
}
?>
</table>

	</body>
</html>

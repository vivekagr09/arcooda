<?php
include_once('freight_common.php');

$qry=$DB->query("SELECT id FROM freight_services WHERE type='pp'");
$prepaid_list=array();
while($row=$DB->fetch_assoc($qry)) $prepaid_list[]=$row['id'];

?>
<!--<link rel="stylesheet" type="text/css" href="style.css" />
<center><?= formatHTMLTitle('Pre-paid Inventory') ?></center>-->
<html>
<head>
	<title>Prepaid Inventory - Print List</title>
	<script>
	function printpage()
	{
		if (window.print) window.print();
		else alert('To print this page, select Print Frame from the file menu.');
	}

	function doPrint()
	{
		if (confirm("Click OK to print this document")) printpage();
	}
	</script>
</head>
<body onLoad="doPrint()">
	<center style="font-size:16px;"><b>Prepaid Inventory Stock</b></center><br />
	<table style="width: 98%;border-collapse:collapse;font-size:13px;" border="1" align="center" cellspacing=0 cellpadding=2 border=0>
		<tr><th>ID</th><th>Product Name</th><th>Qty</th><th>Cost Price (AUD)</th><th>Sell Price (AUD)</th><th>Total Stock Value</th></tr>
<?php
	$alt=false;
	foreach($prepaid_list as $product_id)
	{
		$product = freightServiceFromDatabase($product_id);
?>
		<tr<?= ($alt=!$alt)?'':' class="alt"' ?> style="text-align:center">
			<td>F0<?= $product_id ?></td><td><?= $product['name'] ?></td><td><?= $product['qty'] ?></td><td>$<?= sprintf("%.2f",$product['cost_price']) ?></td>
			<td>$<?= sprintf("%.2f",$product['sell_price']) ?></td><td><b>$<?= sprintf("%.2f",($product['qty'] * $product['cost_price'])) ?></b></td>
		</tr>
<?php
} ?>
	</table>
</body>
</html>
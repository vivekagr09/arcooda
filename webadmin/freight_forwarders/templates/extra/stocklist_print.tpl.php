<?php
if((trim($supplier['supplier_name']) == "") || (trim($supplier['supplier_name_ch']) == ""))
{	// One of the name fields (or both) is (are) empty, cbf figuring which it is
	$sn = trim($supplier['supplier_name'] . $supplier['supplier_name_ch']);
}
else
{	// Both names are set, display them both
	$sn = trim($supplier['supplier_name']) . " / " . trim($supplier['supplier_name_ch']);
}
?>
<html>
<head>
<title>Stocklist - <?php echo $sn ?></title>
<style type="text/css">
<!--
table
{
	white-space: nowrap;
	text-align: center;
	width: 100%;
	border-collapse: collapse;
}

th
{
	border-bottom: thin solid;
}

td
{
	overflow: hidden;
}
-->
</style>
</head>

<body>
<h3 style="text-align: left">Stocklist - <?php echo $sn ?></h3>
<table>
	<tr>
		<th>Our ID</th>
		<th>Qty<br />Available</th>
		<th>Name of Product </th>
		<th>Buy Price </th>
		<th>Sell Price </th>
		<th>% Markup </th>
		<th>Location</th>
<!--
		<th>Terms</th>
		<th>Notes</th>
		<th>Movement</th>
-->
	</tr>
<?php
foreach($stock as $s): ?>
	<tr>
		<td><?php echo $s['product_id'];?></td>
		<td><?php echo $s['qty'];?></td>
		<td style="text-align: left"><?php echo /*trimString(*/$s['product_name']/*, 30)*/;?></td>
		<td><?php echo sprintf("%0.2f", $s['buy_price']);?></td>
		<td><?php echo $s['sell_price'] ? sprintf("%0.2f", $s['sell_price']) : "-";?></td>
		<td><?php echo $s['percent_markup'] ? sprintf("%0.2f", $s['percent_markup']) : "-";?></td>
		<td><?php echo trim($s['location_name']);?></td>
<?php
/*<!--
		<td><?php echo $s['term'];?></td>
		<td style="overflow: hidden"><?php echo trimString($s['notes']);?></td>
		<td><?php echo $s['movement'];?></td>
-->*/
?>
	</tr>
<?php
endforeach; ?>
</table>
</body>

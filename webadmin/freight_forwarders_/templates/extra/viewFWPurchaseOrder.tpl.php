<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Fortunewave Purchase Order F<?= sprintf("%06d", $porder_id); ?></title>
<style type="text/css">
<!--
html,body {
	height: 100%;
}

h1 {
	font-size: medium;
	font-style: normal;
	font-weight: bold;
	text-transform: uppercase;
	text-decoration: underline;
	text-align: center;
}
td.items_header {
	font-size: medium;
	font-style: italic;
	font-weight: bold;
	text-decoration: underline;
	text-align: center;
}
td.number {
	text-align: right;
}
td {
	vertical-align: top;
	white-space: nowrap;
}
td.centre {
	text-align: center;
}
table {
	border-collapse: collapse;
	margin-left: auto;
	margin-right: auto;
	width: 95%;
}
td.left_title {
	font-size: medium;
	font-style: normal;
	font-weight: bold;
}
td.right_data {
	font-size: medium;
	font-style: normal;
	font-weight: bold;
	text-decoration: underline;
}
-->
</style>
</head>

<body>
<table style="height: 100%">
	<tr>
		<td valign="top">
			<table>
			  <tr>
				<td><img src="http://www.arcooda.com/fw1-logo.gif" alt="FW Banner Image" title="FW Banner Image"</td>
				<td style="text-align:right; font-size: 10pt; font-family: Arial Narrow, Arial, Helvetica, sans-serif; vertical-align:bottom">
						<strong>ARCOODA</strong><br />
					44 Maitland Rd (Downstairs), Mayfield East, NSW 2304, Australia<br />
					Tel: (+61) 02 4968 9313  Fax: (+61) 02 4968 9314  Email: sales@arcooda.com
				</td>
			  </tr>
			</table>
			<br />
			<table>
			  <tr>
				<td colspan="4"><h1>Purchase Order </h1></td>
			  </tr>
			  <tr><td>&nbsp;</td></tr>
			  <tr>
				<td width="1%" class="left_title">Messers:</td>
				<td rowspan="6"> <?= $supplier['supplier_name'] ?><br />
				<?= nl2br($supplier['address']) ?><br />
				<?= $supplier['c_name'] ?><br />
				<br />
				TEL: <?= $supplier['phone'] ?><br />
				FAX: <?= $supplier['fax'] ?>
				<td width="18%" class="left_title">Date:</td>
				<td width="12%" class="right_data"><?= date("j-M-y", $date); ?></td>
			  </tr>
			  <tr>
				<td>&nbsp;</td>
				<td class="left_title">P/O No.: </td>
				<td class="right_data">F<?= sprintf("%06d", $porder_id); ?></td>
			  </tr>
			  <tr>
				<td>&nbsp;</td>
				<td class="left_title">Sale Term: </td>
				<td class="right_data"><?= $term; ?></td>
			  </tr>
			  <tr>
				<td>&nbsp;</td>
				<td class="left_title">Via:</td>
				<td class="right_data"><?= $courier['courier_name'] //Get Courier Info ?></td>
			  </tr>
			  <tr>
				<td>&nbsp;</td>
				<td class="left_title">Payment Term: </td>
				<td class="right_data"><?= $payment_type //Get Payment Type ?></td>
			  </tr>
			  <tr>
				<td>&nbsp;</td>
				<td class="left_title">Vendor Inv. No.: </td>
				<td class="right_data">XXXX</td>
			  </tr>
			</table>
			<br />
			<table>
			  <tr>
				<td class="items_header" width="1%">Quantity</td>
				<td class="items_header" width="1%">Item / I.D. No.</td>
				<td class="items_header">Description</td>
				<td class="items_header" width="10%">Unit Price</td>
				<td class="items_header" width="10%">Amount</td>
			  </tr>
<?php
foreach($order_products as $p): // FOREACH over products ?>
			  <tr>
				<td class="centre"><?= $p['qty'] ? $p['qty'] : "N/A" ?></td>
				<td class="centre"><?= $p['id'] ?></td>
				<td><?= htmlspecialchars($p['name']) ?></td>
				<td class="number"><?= sprintf("%01.2f", $p['price']); ?></td>
				<td class="number"><?php
$amount = sprintf("%01.2f",$p['qty'] * $p['price']); echo $amount; ?></td>
			  </tr>
<?php
$subtotal += $amount; // Track Total Costs
			endforeach; ?>
			</table>
		</td>
	</tr>
	<tr>
		<td style="vertical-align: bottom">
			<table>
			  <tr>
				<td width="10%" rowspan="3" class="left_title">Remarks: </td>
				<td colspan="2" rowspan="3"><?= nl2br(stripslashes($order_notes)); ?></td>
				<td colspan="2" class="left_title">Sub Total: </td>
				<td width="14%" class="number"><?= sprintf("%01.2f", $subtotal); ?></td>
			  </tr>
			  <tr>
				<td colspan="2" class="left_title">Freight Charges: </td>
				<td width="14%" class="number"><?= $shipping_charge ? sprintf("%0.2f", $shipping_charge) : "-" ?></td>
			  </tr>
			  <tr>
				<td width="7%" class="left_title">Total:</td>
				<td width="9%" class="left_title"><div align="right"><?=$order_currency?></div></td>
				<td width="14%" class="number"><?php
$total = $subtotal + $shipping_charge; echo sprintf("%01.2f", $total); ?></td>
			  </tr>
			</table>
		</td>
	</tr>
</table>
</body>
</html>

<?php
include_once __DIR__ . '/common.inc.php';
$invoice          = invoiceFromPost($posted);
$invoice_products = invoiceProductsFromPost($posted);
$symbol           = getCurrencySymbol($invoice["invoice_currency"], 'HTML');
$total 			  = 0;
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title></title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
	</head>
	<body><div class="container" style="padding:30px;margin-top:50px;"><table class="table">
<?php
if (!array_key_exists("products", $posted) || 0 != $posted["products"]) {
    foreach ($invoice_products as $product) {
		$total += convertCurrency($product['product_offer'] * $product['product_qty'], $invoice["invoice_currency"]);
?>
	<tr>
		<td style="text-align:right;"><?=htmlspecialchars($product['product_qty'])?></td>
		<td>x</td>
		<td nowrap="nowrap">#<?=htmlspecialchars(invoiceProductFormatNumber($product['product_id'], $product['sub_id']))?>: <?=htmlspecialchars($product['product_name'])?></td>
		<td>@</td>
		<td style="text-align:right;"><?=htmlspecialchars(convertCurrency($product['product_offer'], $invoice["invoice_currency"]))?></td>
		<td>=<?=$symbol?></td>
		<td style="text-align:right;"><?=htmlspecialchars(convertCurrency($product['product_offer'] * $product['product_qty'], $invoice["invoice_currency"]))?></td>
		<td><?=htmlspecialchars($invoice["invoice_currency"])?></td>
	</tr>
<?php
    }
}
?>
	<tr>
		<td colspan="5" style="text-align:right;">Total</td>
		<td>=<?=$symbol?></td>
		<td style="text-align:right;"><?=$total?></td>
		<td><?=htmlspecialchars($invoice["invoice_currency"])?></td>
	</tr>

	</table></div></body>
</html>

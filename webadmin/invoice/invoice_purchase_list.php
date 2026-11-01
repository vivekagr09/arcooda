<?php
include_once __DIR__ . '/common.inc.php';

$invoice          = invoiceFromPost($posted);
$invoice_products = invoiceProductsFromPost($posted);
$invoice_charges  = invoiceChargesFromPost($posted);
$symbol           = getCurrencySymbol($invoice["invoice_currency"], 'HTML');

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
	<body><div class="container"><table class="table">
<?php
if (!array_key_exists("products", $posted) || 0 != $posted["products"]) {
    foreach ($invoice_products as $product) {
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

if (!array_key_exists("charges", $posted) || 0 != $posted["charges"]) {
    foreach ($invoice_charges as $charge) {
?>
	<tr>
		<td style="text-align:right;"><?=$charge['charge_qty']?></td>
		<td>x</td>
		<td nowrap="nowrap">C<?=sprintf("%02d", $charge['charge_id'])?>: <?=htmlspecialchars($charge['charge_name'])?></td>
		<td>@</td>
		<td style="text-align:right;"><?=htmlspecialchars(convertCurrency($charge['charge_offer'], $invoice["invoice_currency"]))?></td>
		<td>=<?=$symbol?></td>
		<td style="text-align:right;"><?=htmlspecialchars(convertCurrency($charge['charge_offer'] * $charge['charge_qty'], $invoice["invoice_currency"]))?></td>
		<td><?=htmlspecialchars($invoice["invoice_currency"])?></td>
	</tr>
<?php
    }
}

if (!array_key_exists("shipping", $posted) || 0 != $posted["shipping"]) {
    if ('' != $invoice['ship_product_name']) {
        $ship_offer = convertCurrency($invoice['ship_offer'], $invoice["invoice_currency"]);
?>
	<tr>
		<td style="text-align:right;">1</td>
		<td>x</td>
		<td nowrap="nowrap">F<?=str_pad($invoice['ship_id'], 2, '0', STR_PAD_LEFT) . (('y' == $invoice['ship_insurance']) ? 'I' : 'N')?>
		<?=htmlspecialchars($invoice['ship_product_name'])?></td>
		<td>@</td>
		<td style="text-align:right;"><?=htmlspecialchars($ship_offer)?></td>
		<td>=<?=$symbol?></td>
		<td style="text-align:right;"><?=htmlspecialchars($ship_offer)?></td>
		<td><?=htmlspecialchars($invoice["invoice_currency"])?></td>
	</tr>
<?php
    }
}

?>

	</table></div></body>
</html>

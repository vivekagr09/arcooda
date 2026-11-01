<!-- list_orders.tpl -->

<?php
/*
Inputs :
$invoices_all - All the invoices to be displayed
$invoice_cache - The Cache with all the invoice info
$invoiceP_cache - The cache with the all the invoice products.
$status - Array with the status info
$paymentMethods - Array with all the payment methods..
 */

moduleLoad('invoice'); //invoiceFormatNumber
?>

<table class="wide">
	<tr>
		<th>Date</td>
		<th>Invoice #</th>
		<th>Value</th>
		<th>Status</th>
		<th>Payment Type</th>
		<th></th>
		<th></th>
		<th>Make New</th>
	</tr>
<?php
$count = 0;
foreach ($invoices_all as $invoice_id) {
    $invoice          = $invoice_cache[$invoice_id];
    $invoice_currency = $invoice['invoice_currency'];
    $symbol           = getCurrencySymbol($invoice_currency, 'HTML');
    $invoice_products = $invoiceP_cache[$invoice_id];

    $products_tooltip = array();
    foreach ($invoice_products as $product) {
        ob_start();
?>
	<tr>
		<td style="text-align:right;"><?=$product['product_qty']?></td>
		<td>x</td>
		<td nowrap="nowrap">#<?=$product['product_id']?>: <?=htmlspecialchars($product['product_name'])?></td>
		<td>@</td>
		<td style="text-align:right;"><?=convertCurrency($product['product_offer'], $invoice_currency)?></td>
		<td>=<?=$symbol?></td>
		<td style="text-align:right;"><?=convertCurrency($product['product_offer'] * $product['product_qty'], $invoice_currency)?></td>
		<td><?=$invoice_currency?></td>
	</tr>
<?php
$products_tooltip[] = ob_get_clean();
    }

    $ship_offer = convertCurrency($invoice['ship_offer'], $invoice['invoice_currency']);
    if ('' != $invoice['ship_product_name']) {
        $products_tooltip[] = '<tr><td style="text-align:right;">1</td><td>x</td><td nowrap>Freight: ' . $invoice['ship_product_name'] . '</td><td>@</td> <td>' . $ship_offer . '</td><td>=' . $symbol . '</td><td style="text-align:right;">' . $ship_offer . '</td><td>' . $invoice_currency . '</td></tr>';
    }

    $products_tooltip = '<table>' . implode('', $products_tooltip) . '</table>';
    $url              = '/webadmin/invoice/?invoice_id=' . $invoice_id . '&action=';
?>
	<tr style="text-align:center"<?=($count++ % 2 == 1) ? ' class="alt"' : ''?>>
		<td><?=date(_('d/m/Y'), $invoice['open_time'])?></td>
		<td onmouseover="<?=htmlspecialchars("tt_in(" . json_encode($products_tooltip) . ")")?>" onmouseout="tt_out()"><?=invoiceFormatNumber($invoice['invoice_id'], $invoice['revision_id'], $invoice['site_id'])?></td>
		<td>$<?=$invoice['invoice_offer']?> <?=$invoice['invoice_currency']?></td>
		<td><?=$status[$invoice['invoice_action']]?></td>
		<td><?=$paymentMethods[$invoice['paymenttype_id']]?></td>
		<td><a target="_blank" href="<?=htmlspecialchars($url . 'edit')?>">Edit</a></td>
		<td style="text-align:center"><a target="_blank" href="<?=htmlspecialchars($url . 'view')?>">View</a></td>
		<td><a target="_blank" href="<?=htmlspecialchars($url . 'copy&status=sales')?>">Invoice</a> / <a target="_blank" href="<?=htmlspecialchars($url . 'copy&status=inquiry')?>">Inquiry</a></td>
	</tr>
<?php
}
?>
</table>

<!-- /list_orders.tpl -->

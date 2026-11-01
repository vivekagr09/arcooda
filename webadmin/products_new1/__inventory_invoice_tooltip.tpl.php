<!-- __inventory_invoice_tooltip.tpl -->

<?php
/**
 * Product Invoices Tooltip
 *
 * Inputs:
 * - $item
 * - - product_number
 * - $invoice
 * - - bill_address_full
 * - - id
 * - - invoice_currency
 * - - ship_offer
 * - - ship_product_name
 * - $invoice_products[]
 * - - decimal
 * - - product_name
 * - - product_offer
 * - - product_qty
 *
 * Overwrites:
 * - $temp
 */
?>

<table>
    <tr><td colspan="8">Invoice To :<br /><?=nl2br(hecho($invoice['bill_address_full'], 1))?></td></tr>
<?php

foreach ($invoice_products as $temp) {
?>
    <tr>
        <td style="text-align:right;"><?=$temp['product_qty']?></td>
        <td>x</td>
        <td style="white-space:nowrap;"><?php hecho($item['product_number'])?>: <?php hecho($temp['product_name'])?></td>
        <td>@</td>
        <td style="text-align:right;"><?=convertCurrency($temp['product_offer'], $invoice['invoice_currency'], $invoice['invoice_currency'], false, $temp['decimal'])?></td>
        <td>=<?=getCurrencySymbol($invoice['invoice_currency'])?></td>
        <td style="text-align:right;"><?=convertCurrency(bcmul($temp['product_offer'], $temp['product_qty'], $temp['decimal']), $invoice['invoice_currency'], $invoice['invoice_currency'], false, $temp['decimal'])?></td>
        <td><?=$invoice['invoice_currency']?></td>
    </tr>
<?php
}
unset($temp);

if ($invoice['ship_product_name']) {
?>
    <tr>
        <td style="text-align:right;">1</td>
        <td>x</td>
        <td style="white-space:nowrap;">Freight: <?php hecho($invoice['ship_product_name'])?></td>
        <td>@</td>
        <td><?=convertCurrency($invoice['ship_offer'], $invoice['invoice_currency'], $invoice['invoice_currency'], false)?></td>
        <td>=<?=getCurrencySymbol($invoice['invoice_currency'])?></td>
        <td style="text-align:right;"><?=convertCurrency($invoice['ship_offer'], $invoice['invoice_currency'], $invoice['invoice_currency'], false)?></td>
        <td><?=$invoice['invoice_currency']?></td>
    </tr>
<?php
}

?>
</table>

<!-- /__inventory_invoice_tooltip.tpl -->

<?php
/*
Inputs to this page :
$alltogether - the list that makes up the invoices + payments
$company_name - the name of the company
$use_cur - the currency to use
$only_invoices - list of invoices
$payments - list of payments
$invoice_cache - the cache with all the invoice info
$invoice_products - the cache will the invoice products
$balance - the balance to highway for this customer..
 */

moduleLoad('invoice'); //invoiceFormatNumber

?>
<table class="wide" border="1" bordercolor="#CCC">
	<tr style="text-align:center;" class="alt"><td style="background-color:#F0F4F9"></td><td style="background-color:#F0F4F9" ></td><td><b><?=$payby?></b></td><td><b><?=$sale?></b></td><td style="background-color:#FF9999;"><b><?=$balance?></b></td><td style="background-color:#F0F4F9"></td></tr>
	<tr><th>Date</th><th>Reference</th><th>Payment by <?=$company_name . ' (' . $use_cur . ')'?></th><th>Sale By Highway (<?=$use_cur?>)</th><th>Balance To Highway</th><th>Notes</th></tr>
<?php
$count = 0;
foreach ($alltogether as $key => $time) {
    $is_invoice = false;
    $payment    = $invoice    = array();
    if ($key < $tinvs) {
        $invoice_id = $only_invoices[$key];
        $invoice    = $invoice_cache[$invoice_id];
        $pay        = $invoice['invoice_offerau'];
        if ('AUD' != $use_cur) {
            $pay = convertCurrency($invoice['invoice_offer'], $invoice['invoice_currency'], $use_cur);
        }

        $new_balance = $balance - $pay;
        $is_invoice  = true;
    } else {
        $nkey       = $key - $tinvs;
        $payment    = $payments[$nkey];
        $invoice_id = $payment['invoice_id'];
        $pay        = $payment['payment_amountau'];
        if ('' == $pay || 'AUD' != $use_cur) {
            $pay = convertCurrency($payment['payment_amount'], $payment['payment_currency'], $use_cur);
        }

        $new_balance = $balance + $pay;
    }
    $invoice          = $invoice_cache[$invoice_id];
    $invoice_currency = $invoice['invoice_currency'];
    $symbol           = getCurrencySymbol($invoice_currency, 'HTML');
    $invoice_products = $invoiceP_cache[$invoice_id];
    $products_tooltip = array();
    foreach ($invoice_products as $product) {
        $products_tooltip[] =
        '<tr>' .
        '<td style="text-align:right;">' . $product['product_qty'] . '</td>' .
        '<td>x</td>' .
        '<td nowrap>#' . $product['product_id'] . ': ' . $product['product_name'] . '</td>' .
        '<td>@</td>' .
        '<td style="text-align:right;">' . convertCurrency($product['product_offer'], $invoice_currency) . '</td>' .
        '<td>=' . $symbol . '</td>' .
        '<td style="text-align:right;">' . convertCurrency($product['product_offer'] * $product['product_qty'], $invoice_currency) . '</td>' .
            '<td>' . $invoice_currency . '</td>' .
            '</tr>';
    }
    $ship_offer = convertCurrency($invoice['ship_offer'], $invoice['invoice_currency']);
    if ('' != $invoice['ship_product_name']) {
        $products_tooltip[] = '<tr><td style="text-align:right;">1</td><td>x</td><td nowrap>Freight: ' . $invoice['ship_product_name'] . '</td><td>@</td> <td>' . $ship_offer . '</td><td>=' . $symbol . '</td><td style="text-align:right;">' . $ship_offer . '</td><td>' . $invoice_currency . '</td></tr>';
    }

    $products_tooltip = '<table>' . implode('', $products_tooltip) . '</table>';

    $invoice_number = invoiceFormatNumber($invoice_id, 0, $invoice['site_id']);
    $balance        = convertCurrency($balance, $use_cur);
?>
	<tr style="text-align:center;"<?=$count++ % 2 == 1 ? ' class="alt"' : ''?>>
		<td style="width:14%;padding:4px 6px;"><?=date(_('d/m/Y'), $time)?></td>
		<td style="width:14%;padding:4px 6px;" onmouseover="<?=htmlspecialchars('tt_in(' . json_encode($products_tooltip) . ')')?>" onmouseout="tt_out();"><a href="/webadmin/invoice/?action=view&invoice_id=<?=$invoice_id?>" target="_blank"><?=$invoice_number?></a></td>
		<td style="width:14%;padding:4px 6px;"><?=(!$is_invoice) ? $pay : ''?></td>
		<td style="width:14%;padding:4px 6px;"><?=($is_invoice) ? $pay : ''?></td>
		<td style="width:14%;padding:4px 6px;"><?=$balance?></td>
		<td style="width:30%;padding:4px 6px;"><?=wordwrap($payment['payment_text'], 50, '<br />', true)?></td>
	</tr>
<?php
$balance = $new_balance;
}
?>
</table>
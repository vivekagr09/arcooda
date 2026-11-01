<!-- _stock_values.tpl -->

<?php
/**
 * Product Stock Values Template
 *
 * Inputs:
 */

$type_list = productGetOptionList('type');
$list_currency = 'USD';
$alt           = false;
$sell_total    = "0.0000";
$buy_total     = "0.0000";
?>

<table class="table_grid" style="width:auto">
    <tr>
        <th>Category</th>
        <th>Sell Price Totals</th>
        <th>Cost Price Totals</th>
        <th>GP %</th>
    </tr>
<?php
foreach ($type_list as $k => $v) {
    $stock_query = productQuery(array('stock_type' => 'stock_for_sale', 'type' => $k));
    $sell_value  = "0.0000";
    foreach (productCount($stock_query, array('secondary_currency', 'qty', 'secondary_price')) as $currency => $qty_list) {
        foreach ($qty_list as $qty => $price_list) {
            if ($qty) {
                foreach ($price_list as $price => $frequency) {
                    if ($list_currency !== $currency) {
                        $price = convertCurrency($price, $currency, $list_currency, false, 4);
                    }
                    $sell_value = bcadd($sell_value, bcmul(bcmul($qty, $frequency, 0), $price, 4), 4);
                }
            }
        }
    }
    $buy_value = "0.0000";
    foreach (productCount($stock_query, array('cost_currency', 'qty', 'cost_price')) as $currency => $qty_list) {
        foreach ($qty_list as $qty => $price_list) {
            if ($qty) {
                foreach ($price_list as $price => $frequency) {
                    if ($list_currency !== $currency) {
                        $price = convertCurrency($price, $currency, $list_currency, false, 4);
                    }
                    $buy_value = bcadd($buy_value, bcmul(bcmul($qty, $frequency, 0), $price, 4), 4);
                }
            }
        }
    }
    $sell_total = bcadd($sell_total, $sell_value, 4);
    $buy_total  = bcadd($buy_total, $buy_value, 4);

    if (bccomp($sell_value, '0.0000', 4) <= 0 && bccomp($buy_value, '0.0000', 4) <= 0) {
        continue;
    }
?>
    <tr style="text-align:center;"<?=($alt = !$alt) ? ' class="alt"' : ''?>>
        <td style="text-align:left"><a target="_blank" href="print.php?type=<?=$k?>&amp;stock=stock_for_sale"><b><?php hecho($v)?></b></a></td>
        <td style="text-align:right">$<?=convertCurrency($sell_value, $list_currency, '', false)?></td>
        <td style="text-align:right">$<?=convertCurrency($buy_value, $list_currency, '', false)?></td>
        <td style="text-align:right"><?=(bccomp($sell_value, "0.0000", 4) > 0) ? (bcmul(100, bcsub("1.0000", bcdiv($buy_value, $sell_value, 4), 4), 2) . '%') : "N/A"?></td>
    </tr>
<?php
}
?>
    <tr style="text-align:center;"<?=($alt = !$alt) ? ' class="alt"' : ''?>>
        <td style="text-align:left"><b>Total:</b></td>
        <td style="text-align:right"><b>$<?=convertCurrency($sell_total, $list_currency, '', false)?></b></td>
        <td style="text-align:right"><b>$<?=convertCurrency($buy_total, $list_currency, '', false)?></b></td>
        <td style="text-align:right"><b><?=(bccomp($sell_total, "0.0000", 4) > 0) ? (bcmul(100, bcsub("1.0000", bcdiv($buy_total, $sell_total, 4), 4), 2) . '%') : "N/A"?></b></td>
    </tr>
</table>
<br /><br />

<!-- /_stock_values.tpl -->

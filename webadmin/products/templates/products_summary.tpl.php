<!--
Created and modified by Mingze Xi@ Feb 2, 2015

Last Update: Feb 23, 2015

Update Log: Removed hyperlink to product page.

Finished: 100%

Unfinished job:
-->

<div align="center">
	<?php echo formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionSummary\');return false">Quick Summary</a>') ?>
</div>

<!--product summary section starts here, based on products_summary.tpl.php-->
<div id="sectionSummary">

<table class="wide" align="center" style="width:95%;padding-left: 3;border: 1px solid #CCCCCC;border-collapse: collapse;margin-left: auto;margin-right: auto;">
	<tr>
		<td style="width:18%;text-align: right;background-color: #EEEEE0;color: #000000;font-weight: bold;">Factory ID :</td>
		<td style="width:32%;background-color: #FFFFFF;"><?php echo htmlspecialchars($product['factory_id']) ?></td>
		<td style="width:18%;text-align: right;background-color: #EEEEE0;color: #000000;font-weight: bold;"><?php hecho("Product ID :")?> </td>
		<td style="width:32%;background-color: #FFFFFF;"><?php hecho($id)?></td>
	</tr>
	<tr>
		<td style="width:18%;text-align: right;background-color: #EEEEE0;color: #000000;font-weight: bold;">Product Name :</td>
		<td style="width:32%;background-color: #FFFFFF;"><?php hecho(("" != $product["product_name"]) ? $product["product_name"] : $product["product_name"])?></td>
		<td style="width:18%;text-align: right;background-color: #EEEEE0;color: #000000;font-weight: bold;"><?php hecho("Supplier:")?> </td>
		<td style="width:32%;background-color: #FFFFFF;"><?php hecho(getSupplierName($product[(("m" == $product["product_type"]) ? "manufacturer" : "supplier")]))?></td>
	</tr>
	<!--calculate prices-->
<?php
if (!function_exists('getUStoRate')) {
    function getUStoRate($code = 'USD')
    {
        global $DB;
        if ('USD' == $code) {
            return '1.000000';
        }
        if ($row = $DB->query_first(sprintf(
            "SELECT `usto` FROM `highwaygroup`.`currency` WHERE `code` = %s LIMIT 1",
            $DB->escape($code)
        ), MYSQLI_ASSOC)) {
            return $row['usto'];
        }
        return '0.000000';
    }
}

if ('' == $product['approx_cost_currency']) {
    $product['approx_cost_currency'] = 'USD';
}

//get the old exchange rate from the database (from cost currency to USD)
$old_exch_rate = doubleval($product['approx_cost_exch_rate']);
$UStoRate      = doubleval(getUStoRate($product['approx_cost_currency']));
//calculate the old approximate cost (also knows the approx cost currency)
$old_approx_price = $product['cost_price'] * $old_exch_rate * $UStoRate;
//calculate the new approximate cost, convert from the cost currency to the approx cost currency
$new_approx_price = showPriceFrom($product['cost_price'], $product['cost_currency'], $product['approx_cost_currency']);
if (0 == $product['markup'] && 0 != $new_approx_price) {
    $recal_markup      = ($product['sales_price'] - $new_approx_price) / $new_approx_price * 100;
    $product['markup'] = sprintf('%01.2f', $recal_markup);
}
//the old sales price is calculated from the old approx cost and markup
$old_sales_price = (1 + doubleval($product['markup']) / 100) * $old_approx_price;
//the new sales price is calculated from the new approx cost and markup
$new_sales_price = (1 + doubleval($product['markup']) / 100) * $new_approx_price;
//calculate the profits
$old_profit = $old_sales_price - $old_approx_price;
$new_profit = $new_sales_price - $new_approx_price;
?>
	<!--cost prices-->
	<tr>
		<td style="width:18%;text-align: right;background-color: #EEEEE0;color: #000000;font-weight: bold;">Cost Price (<?php echo ($product['cost_currency']) ?>) :</td>
		<td style="width:32%;background-color: #FFFFFF;"><?php echo $product['cost_price'] . ' ' . $product['cost_currency'] ?></td>
		<td style="width:18%;text-align: right;background-color: #EEEEE0;color: #000000;font-weight: bold;">Last Sold :</td>
		<td style="width:32%;background-color: #FFFFFF;"><?php echo ($product["last_sold_time"]) ? date("d/m/Y", $product["last_sold_time"]) : "Never" ?></td>
	</tr>
	<!--sales price here-->
	<tr>
		<td style="width:18%;text-align: right;background-color: #EEEEE0;color: #000000;font-weight: bold;">Sale Price (<?php echo ($product['cost_currency']) ?>) :</td>
		<td style="width:32%;background-color: #FFFFFF;">	<?php echo showPriceFrom($new_sales_price, $product['approx_cost_currency'], $product['sales_currency']) . ' ' . $product['sales_currency'] ?> <small><i>inc. GST</i></small>
		</td>
		<td style="width:18%;text-align: right;background-color: #EEEEE0;color: #000000;font-weight: bold;">Product Qty :</td>
		<td style="width:32%;background-color: #FFFFFF;"><?php echo $product["qty"] ?></td>
	</tr>

</table>
<br />

		<!--Product Picture A9-->

<?php
global $_GLOBALS;

?>
<!--
Created and modified by Mingze Xi@ Feb 9, 2015
Last Update: Feb 16, 2015

Update log: 1. Moved "Display Price Online" and "Active" to products_status.tpl.php@poducts/templates

Problem:


Finished: 100%

Note: Bulk pricing section is removed.

The product pricing (PR) section starts here, based on products_pricing2.tpl.php @AGS/products/template
-->
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
//get the last modified date, either the dated first added or last edited date
$last_modified = $product['date_added'];
if ($product['last_modified'] > 0) {
    $last_modified = $product['last_modified'];
}

?>

<div align="center">
<?php echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionPricing2\');">Pricing</a>'); ?>
</div>

<div id="sectionPricing2">
	<table style="width:95%;padding-left: 3;border: 1px solid #CCCCCC;border-collapse: collapse;margin-left: auto;margin-right: auto;" align="center">
	<tr>
		<td style="width:25%;background-color:#F0F4F9;">&nbsp;</td>
			<td style="width:32%;text-align:right;background-color:#FFCFAF;">
				<strong><?php echo date('d M Y', $last_modified) ?></strong>
			</td>
		<td style="width:33%;text-align:right;background-color:#FFCFAF;">
			<strong><?php echo date('d M Y', time()) ?></strong>
		</td>
	</tr>
	<tr style="text-align:right;">
		<td style="background-color:#EEEEE0"><strong>Cost Price:</strong></td>
		<td style="background-color:#F0F0FF" nowrap="nowrap" align="right" class="bottom">
			<?php echo $product['cost_price'] . ' ' . $product['cost_currency'] ?>
		</td>
		<td style="background-color:#FFFFFF" nowrap="nowrap" align="right" class="bottomRight">
			<?php echo $product['cost_price'] . ' ' . $product['cost_currency'] ?>
		</td>
	</tr>
	<tr style="text-align:right;">
		<td style="background-color:#EEEEE0"><strong>Approx. Cost Price:</strong></td>
		<td style="background-color:#F0F0FF" nowrap="nowrap" align="right" class="bottom">
			<?php echo sprintf('%01.2f', $old_approx_price) . ' ' . $product['approx_cost_currency'] ?>
		</td>
		<td style="background-color:#FFFFFF" nowrap="nowrap" align="right" class="bottomRight">
			<?php echo sprintf('%01.2f', $new_approx_price) . ' ' . $product['approx_cost_currency'] ?>
		</td>
	</tr>
	<tr style="text-align:right;">
		<td style="background-color:#EEEEE0"><strong>Sales Price:</strong></td>
		<td style="background-color:#F0F0FF" nowrap="nowrap" align="right" class="bottom">
			<?php echo showPriceFrom($old_sales_price, $product['approx_cost_currency'], $product['sales_currency']) . ' ' . $product['sales_currency'] ?>
		</td>
		<td style="background-color:#FFFFFF" nowrap="nowrap" align="right" class="bottomRight">
			<?php echo showPriceFrom($new_sales_price, $product['approx_cost_currency'], $product['sales_currency']) . ' ' . $product['sales_currency'] ?>
		</td>
	</tr>
	<tr style="text-align:right;">
		<td style="background-color:#EEEEE0"><strong>Profit:</strong></td>
		<td style="background-color:#F0F0FF" nowrap="nowrap" align="right" class="bottom">
			<?php echo showPriceFrom($old_profit, $product['approx_cost_currency'], $product['sales_currency']) . ' ' . $product['sales_currency'] ?>
		</td>
		<td style="background-color:#FFFFFF" nowrap="nowrap" align="right" class="bottomRight">
			<?php echo showPriceFrom($new_profit, $product['approx_cost_currency'], $product['sales_currency']) . ' ' . $product['sales_currency'] ?>
		</td>
	</tr>
	<tr style="text-align:right;">
		<td style="background-color:#EEEEE0"><strong>Markup %:</strong></td>
		<td style="background-color:#F0F0FF" nowrap="nowrap" align="right" class="bottom">
			<?php echo sprintf('%01.2f', $product['markup']) ?>%
		</td>
		<td style="background-color:#FFFFFF" nowrap="nowrap" align="right" class="bottomRight" >
			<?php echo sprintf('%01.2f', $product['markup']) ?>%
		</td>
	</tr>
	</table>
	<br/>
	<!--2nd Price section PR8-PR13-->
	<!--PR8-->
	<table table style="width:95%;padding-left: 3;border: 1px solid #CCCCCC;border-collapse: collapse;margin-left: auto;margin-right: auto;" align="center">
		<tr>
			<td style="text-align:right;background-color:#EEEEE0"><b>Cost Price :</b></td>
			<td>
				<input type="text" name="prices[cost_price]" id="cost_price" size="7" onchange="convert(this.form.cost_price, this.form.cost_currency, this.form.approx_cost_price, this.form.approx_cost_currency);doMarkup();note_changes();" value="<?php echo $product['cost_price'] ?>" />
				<select name="prices[cost_currency]" style="width:60%" id="cost_currency" onchange="convert(this.form.cost_price, this.form.cost_currency, this.form.approx_cost_price, this.form.approx_cost_currency);doMarkup();">
<?php
$result = $DB->query("SELECT * FROM `highwaygroup`.`currency` ORDER BY `name` ASC");
while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['code'] . '"';
    echo $row['code'] == $product['cost_currency'] ? ' selected="selected"' : "";
    echo '>';
    echo strtoupper($row['code']) . ' - ' . $row['name'] . '</option>';
}
?>
				</select>
			</td>
		</tr>

		<!--PR9-->
		<tr>
			<td style="text-align:right;background-color:#EEEEE0"><b>Approx Cost Price :</b></td>
			<td>
				<input type="hidden" name="prices[approx_cost_exch_rate]" id="approx_cost_exch_rate" value="" />
				<input type="text" name="approx_cost_price" size="7" id="approx_cost_price" disabled="disabled" onchange="doMarkup();note_changes();" />
				<select name="prices[approx_cost_currency]" id="approx_cost_currency" style="width:60%" onchange="convert(this.form.cost_price, this.form.cost_currency, this.form.approx_cost_price, this.form.approx_cost_currency);doMarkup();">
<?php
$result = $DB->query("SELECT * FROM `highwaygroup`.`currency` ORDER BY `name` ASC");
while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['code'] . '"';
    echo $row['code'] == $product['approx_cost_currency'] ? ' selected="selected"' : "";
    echo '>';
    echo strtoupper($row['code']) . ' - ' . $row['name'] . '</option>';
}
?>
				</select>
			</td>
		</tr>
		<!--PR10-->
		<tr>
			<td style="text-align:right;background-color:#EEEEE0"><b>Sales Price :</b></td>
			<td>
			<input type="text" name="prices[sales_price]" id="sales_price" size="7" onchange="doMarkup();note_changes();" value="<?php echo $product['sales_price'] ?>" />
			<select name="prices[sales_currency]" id="sales_currency" style="width:60%;" onchange="doMarkup();setAllValues('show_sales_currency',this.value);">
<?php
$result = $DB->query("SELECT * FROM `highwaygroup`.`currency` ORDER BY `name` ASC");
while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['code'] . '"';
    echo $row['code'] == $product['sales_currency'] ? ' selected="selected"' : "";
    echo '>';
    echo strtoupper($row['code']) . ' - ' . $row['name'] . '</option>';
}
?>
			</select>
			</td>
		</tr>
		<!--PR11-->
		<tr>
			<td style="text-align:right;background-color:#EEEEE0"><b>Markup % :</b></td>
			<td>
			<input type="text" name="prices[markup]" id="markup" size="7" onchange="reverseMarkup();note_changes();" value="<?php echo $product['markup'] ?>" /> %</td>
		</tr>

		<!--PR12-->
<?php
if ($product['sales_currency'] == $product['cost_currency']) {
    $product['price_flux'] = 'n';
}

?>
		<tr>
			<td nowrap="nowrap" style="text-align:right;background-color:#EEEEE0"><b>Price Flux :</td>
			<td>
				<select name="prices[price_flux]" id="price_flux" style="width:70px;" onchange="note_changes();">
					<option value="y"<?php echo ('y' == $product['price_flux']) ? ' selected="selected"' : '' ?>>Yes</option>
					<option value="n"<?php echo ('n' == $product['price_flux']) ? ' selected="selected"' : '' ?>>No</option>
				</select>
				<br />
				<span style="color:red;font-size:11px;"><b>If Sales &amp; Cost Currency are the same, then Price Flux should be set to 'No'</b></span>
			</td>
		</tr>
		</table>
		<br />
		<!--PR13 & PR14 & PR15-->
		<table table style="width:95%;padding-left: 3;border: 1px solid #CCCCCC;border-collapse: collapse;margin-left: auto;margin-right: auto;" align="center">
		<tr>
			<td nowrap="nowrap" style="text-align:right;background-color:#EEEEE0"><b>Sample Price:</b></td>
			<td>
				<input type="text" name="data1[sample_price]" size="7" value="<?php echo $product['sample_price'] ?>" />
				<select name="data1[sample_currency]" id="sample_currency" style="width:60%;">
<?php
$result = $DB->query("SELECT * FROM `highwaygroup`.`currency` ORDER BY `name` ASC");
while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['code'] . '"';
    echo $row['code'] == $_GLOBALS['site_options']['sales_currency'] ? ' selected="selected"' : "";
    echo '>';
    echo strtoupper($row['code']) . ' - ' . $row['name'] . '</option>';
}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap" style="text-align:right;background-color:#EEEEE0"><b>Sample Qty:</b></td>
			<td><input type="text" name="data1[sample_qty]" size="7" value="<?php echo $product['sample_qty'] ?>" /></td>
		</tr>
		<!--tr>
			<td nowrap="nowrap" class="left_title" style="text-align:right;"><b>Minimum Order:</b></td>
			<td><input type="text" name="data1[min_order]" size="7" value="<?php echo $product['min_order'] ?>" /></td>
		</tr-->
		<tr>
			<td nowrap="nowrap" style="text-align:right;background-color:#EEEEE0"><b>Qty :</b></td>
			<td><input type="text" name="data1[qty]" size="7" value="<?php echo $product['qty'] ?>" /></td>
		</tr>
			<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>

	</table>
</div>


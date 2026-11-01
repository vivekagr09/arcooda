<!-- staff_summary.tpl -->

<?php
/**
 * Expects:
 *  $currency - the currency to display prices in
 *  $destination - the shipping destination country
 *  $price_list - a list of freight prices to display
 *  $service_from - the shipping source country
 */

$option_list = array();
$option_list['destination'] = freightGetOptionList('destination');
$option_list['service_from'] = freightGetOptionList('service_from');
$option_list['supplier_id'] = freightGetOptionList('supplier_id');
$symbol = getCurrencySymbol($currency);

$alt = false;
?>

<?php echo formatHTMLTitle('<a href="#" onclick="toggleSection(\'section_staff_summary\');return false">Staff Summary</a>', '<a href="#" onclick="toggleSection(\'staff_product_notes\');return false">Toggle Notes</a>') ?>

<div id="section_staff_summary">

<h3>Results for sending <?php echo htmlspecialchars($weight) ?> kgs from <?php echo htmlspecialchars($option_list['service_from'][$service_from]) ?> to <?php echo htmlspecialchars($option_list['destination'][$destination]) ?></h3>

<table>
	<tr>
		<th>Provider</th>
		<th>Service</th>
		<th>Buy Rate (<?php echo htmlspecialchars($currency) ?>)</th>
		<th>Retail Price (<?php echo htmlspecialchars($currency) ?>)</th>
		<th>Our Sell Price (<?php echo htmlspecialchars($currency) ?>)</th>
		<th>Profit (<?php echo htmlspecialchars($currency) ?>)</th>
<?php
if($currency != 'USD')
{
?>
		<th>Profit (USD)</th>
<?php
}
?>
	</tr>
<?php
foreach($price_list as $v)
{
	$edit_url = "index.php?action=edit&product_id=" . $v['product_id'];
	$forwarder_url = "/webadmin/freight_forwarders/edit_forwarder.php?action=edit&id=" . $v['supplier_id'];
?>
	<tr<?php echo ($alt = !$alt) ? '' : ' class="alt"' ?>>
<?php
	if($v['supplier_id'] != 0)
	{
?>
		<td><a href="<?php echo htmlspecialchars($forwarder_url) ?>" target="_blank"><?php echo htmlspecialchars(($option_list['supplier_id'][$v['supplier_id']] != '') ? $option_list['supplier_id'][$v['supplier_id']] : '[Untitled Supplier]') ?></a></td>
<?php
	}
	else
	{
?>
		<td>[Undefined Supplier]</td>
<?php
	}
?>
		<td><a href="<?php echo htmlspecialchars($edit_url) ?>" target="_blank"><?php echo $v['name'] ?></a></td>
		<td><?php echo $symbol . convertCurrency($v['cost_price'], $v['currency'], $currency) ?></td>
		<td><?php echo $symbol . convertCurrency($v['retail_price'], $v['currency'], $currency) ?></td>
		<td><b><?php echo $symbol . convertCurrency($v['sell_price'], $v['currency'], $currency) ?></b></td>
		<td><?php echo $symbol . convertCurrency($v['profit'], $v['currency'], $currency) ?></td>
<?php
	if($currency != 'USD')
	{
?>
		<td><?php echo $symbol . convertCurrency($v['profit'], $v['currency'], 'USD') ?></td>
<?php
	}
?>
	</tr>
<?php
	if ($v['break_up'] != '')
	{
?>
	<tr><td name="staff_product_notes" style="display:none;" colspan="<?php echo ($currency != 'USD') ? 7 : 6 ?>"><?php echo nl2br(htmlspecialchars($v['break_up'])) ?></td></tr>
<?php
	}
}
?>
</table>
</div>

<div id="section_staff_summary_hideNote" style="display:none;">
	Click on "Staff Summary" above to display this section
</div>

<br />
<br />





<!-- /staff_summary.tpl -->

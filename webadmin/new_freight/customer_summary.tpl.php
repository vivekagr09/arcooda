<!-- customer_summary.tpl -->

<?php
/**
 * Expects:
 *  $currency - the currency to display prices in
 *  $destination - the shipping destination country
 *  $price_list - a list of freight prices to display
 *  $service_from - the shipping source country
 *  $weight - the weight of the shipment in kgs
 * Optional:
 *  $goods_value - the value of the goods for shipping purposes
 */

$option_list = array();
$option_list['destination'] = freightGetOptionList('destination');
$option_list['service_from'] = freightGetOptionList('service_from');
$option_list['del_time_id'] = freightGetOptionList('del_time_id');
$option_list['tracking'] = freightGetOptionList('tracking');
$option_list['currency'] = freightGetOptionList('currency');
$symbol = getCurrencySymbol($currency);
?>

<?php echo formatHTMLTitle('<a href="#" onclick="toggleSection(\'section_customer_summary\');return false">Customer Summary</a>') ?>

<div id="section_customer_summary">
<table>
	<tr><td colspan="5">
		&nbsp;<br />
		Prices for shipping <?php echo htmlspecialchars($weight) ?> kgs from <?php echo htmlspecialchars($option_list['service_from'][$service_from]) ?> to <?php echo htmlspecialchars($option_list['destination'][$destination]) ?><br />
		&nbsp;
	</td></tr>
	<tr>
		<th>Price (<?php echo htmlspecialchars($currency) ?>)</th>
		<th>Service</th>
		<th>Transit Time</th>
		<th>Tracking</th>
		<th>Insurance<?php echo ($goods_value == 0) ? ' Available' : '' ?></th>
	</tr>
<?php
foreach($price_list as $v)
{
	$name = explode('-', $v['name']);
	$ins = (strpos($v['insurance_cost'], '(')) ? $v['insurance_cost'] : (($v['insurance'] == 'y') ? 'Included' : 'No');
?>
	<tr class="alt">
		<td><b><?php echo $symbol . convertCurrency($v['sell_price'], $v['currency'], $currency) ?></b></td>
		<td><?php echo htmlspecialchars(trim(reset(explode('-', $v['name'])))) ?></td>
		<td><?php echo htmlspecialchars($option_list['del_time_id'][$v['del_time_id']]) ?></td>
		<td><?php echo htmlspecialchars($option_list['tracking'][$v['tracking']]) ?></td>
		<td><?php echo htmlspecialchars($ins) ?></td>
	</tr>
<?php
}
?>
	<tr class="alt">
		<td colspan="5"><dl>
			<dt>PRICING :</dt>
			<dd>Above prices are in <?php echo htmlspecialchars($option_list['currency'][$currency]) ?></dd>
			<dt>DELIVERY :</dt>
			<dd>Please note the transit times are estimates only.</dd>
			<dt>TRACKING :</dt>
			<dd>Some couriers give you to the facility to Track the status of your parcel over the Internet. (A tracking number & website will be emailed to you once your goods have been shipped, if available).</dd>
			<dt>INSURANCE :</dt>
			<dd>Some couriers also provide Insurance. The cost of insurance is usually based on some percent of the total value of the goods.</dd>
		</ul></td>
	</tr>
</table>
</div>

<div id="section_customer_summary_hideNote" style="display:none;">
	Click on "Customer Summary" above to display this section
</div>

<br />
<br />

<!-- /customer_summary.tpl -->

<!-- international_calculator.tpl -->

<?php
/**
 * Optional:
 *  $currency - the currency to display prices in
 *  $destination - the shipping destination country
 *  $goods_value - the value of the goods for shipping purposes
 *  $service_from - the shipping source country
 *  $weight - the weight of the shipment in kgs
 */

$option_lists = array();
$option_lists['service_from'] = freightGetOptionList('service_from');
$option_lists['destination'] = freightGetOptionList('destination');
$option_lists['currency'] = freightGetOptionList('currency');

/**
 * Limit the destinations to those entered in freight database
 */
$rebuild = array();
$query = $DB->query("SELECT DISTINCT country FROM freight_services_int_destinations");
while($row = $DB->fetch_assoc($query)) $rebuild[$row['country']] = $row['country'];
$DB->free_result($query);
$option_lists['destination'] = array_intersect_key($option_lists['destination'], $rebuild);

/**
 * Limit the sources to those entered in freight database
 */
$rebuild = array();
$query = $DB->query("SELECT DISTINCT service_from FROM freight_services WHERE active='y' AND location_type='int'");
while($row = $DB->fetch_assoc($query)) $rebuild[$row['service_from']] = $row['service_from'];
$DB->free_result($query);
$option_lists['service_from'] = array_intersect_key($option_lists['service_from'], $rebuild);
?>

<script type="text/javascript">
//<![CDATA[
$(function() {
	var $form = $("form.international_calculator");

	$form.submit(function() {
		validateForm();
	});
	$form.find("select[name='goods_currency']").change(function() {
		$form.find("#cur_goods").text($(this).val());
	}).change();
});
//]]>
</script>

<?php echo formatHTMLTitle('International Freight Calculator') ?>

<form accept-charset="utf-8" class="international_calculator" action="" method="get"><table>
	<tr>
		<th>Source :</th>
		<td><select name="service_from">
			<option value=""></option>
			<?php echo formatHTMLOptions($option_lists['service_from'], $service_from) ?>
		</select></td>
	</tr>
	<tr>
		<th>Destination :</th>
		<td><select name="destination">
			<option value=""></option>
			<?php echo formatHTMLOptions($option_lists['destination'], $destination) ?>
		</select></td>
	</tr>
	<tr>
		<th>Weight :</th>
		<td><input type="text" name="weight" value="<?php echo htmlspecialchars($weight) ?>" /> Kgs</td>
	</tr>
	<tr>
		<th>Value of Goods :</th>
		<td>
			<input type="text" name="goods_value" value="<?php echo htmlspecialchars($goods_value) ?>" />
			<span id="cur_goods" name="cur_goods"></span>
		</td>
	</tr>
	<tr>
		<th>Currency :</th>
		<td><select name="goods_currency">
			<option value=""></option>
			<?php echo formatHTMLOptions($option_lists['currency'], $currency) ?>
		</select></td>
	</tr>
	<tr><th colspan="2" style="text-align:center;">
		<input type="submit" value="Calculate" /><br />
		<br />
		<small><a style="color:#00f;" href="index.php?action=domestic">Switch to Domestic Calculator</a></small>
	</th></tr>
</table></form>

<br />
<br />

<!-- /international_calculator.tpl -->

<!-- destinations.tpl -->

<?php
$country_list = $section_handler->getOptionList("country");
$location_list = $section_handler->countryLocationsFromDatabase($item["service_from"]);
$delivery_time_list = $section_handler->getOptionList("delivery_time_id");

$country_remainder = $country_list;
$location_remainder = $location_list;
foreach($section_handler->zonesFromDatabase($id) as $v1)
{
	foreach($section_handler->zoneCountriesFromDatabase($v1["id"]) as $v2) unset($country_remainder[$v2["country"]]);
	foreach($section_handler->zoneLocationsFromDatabase($v1["id"]) as $v2) unset($location_remainder[$v2["location_id"]]);
}

if ($item["forwarder_id"] != 0 && $item["forwarder_code"] != "") foreach($section_handler->search(array("forwarder_id" => $item["forwarder_id"], "forwarder_code" => $item["forwarder_code"])) as $k) foreach($section_handler->zonesFromDatabase($k) as $v1)
{
	foreach($section_handler->zoneCountriesFromDatabase($v1["id"]) as $v2) unset($country_remainder[$v2["country"]]);
	foreach($section_handler->zoneLocationsFromDatabase($v1["id"]) as $v2) unset($location_remainder[$v2["location_id"]]);
}

function country_row($key, $row = array())
{
	global $zone_key, $zone_id, $country_list, $country_remainder, $delivery_time_list;
	static $alt = true;

	$main_key = $zone_key . "[countries][" . $key . "]";

	$hidden = array(
		"id" => $row["id"],
	);

	ob_start();
?>
<tr<?php echo ($alt = !$alt) ? ' class="alt"' : '' ?> id="country_row_<?php hecho($key) ?>">
	<td>
		<?php echo formatHTMLInputs($hidden, $main_key) ?>
<?php
	if ($row["country"] != "")
	{
?>
		<input type="hidden" name="<?php hecho($main_key . "[country]") ?>" value="<?php hecho($row["country"]) ?>" />
		<?php hecho($country_list[$row["country"]]) ?>
<?php
	}
	else
	{
?>
		<select name="<?php hecho($main_key . "[country]") ?>">
			<option value=""></option>
			<?php echo formatHTMLOptions($country_remainder) ?>
		</select>
<?php
	}
?>
	</td>
	<td><select name="<?php hecho($main_key . "[delivery_time_id]") ?>">
		<option value=""></option>
		<?php echo formatHTMLOptions($delivery_time_list, $row["delivery_time_id"]) ?>
	</select></td>
	<td>
		<input type="button" value="Delete" onclick="<?php hecho("country_delete(" . jecho($key, 1) . ")") ?>" />
	</td>
</tr>
<?php
	return ob_get_clean();
}

function location_row($key, $row = array())
{
	global $zone_key, $zone_id, $location_list, $delivery_time_list;
	static $alt = true;

	$main_key = $zone_key . "[locations][" . $key . "]";

	$hidden = array(
		"id" => $row["id"],
		"location_id" => $row["location_id"],
	);

	ob_start();
?>
<tr<?php echo ($alt = !$alt) ? ' class="alt"' : '' ?> id="location_row_<?php hecho($key) ?>">
	<td>
		<?php echo formatHTMLInputs($hidden, $main_key) ?>
		<input type="text" name="<?php hecho($main_key . "[location_str]") ?>" value="<?php hecho($location_list[$row["location_id"]]) ?>" style="width:98%;" />
	</td>
	<td><select name="<?php hecho($main_key . "[delivery_time_id]") ?>">
		<option value=""></option>
		<?php echo formatHTMLOptions($delivery_time_list, $row["delivery_time_id"]) ?>
	</select></td>
	<td>
		<input type="button" value="Delete" onclick="<?php hecho("location_delete(" . jecho($key, 1) . ")") ?>" />
	</td>
</tr>
<?php
	return ob_get_clean();
}
?>

<script type="text/javascript">
//<![CDATA[
var country_count = <?php jecho(count($zone_countries)) ?>;
var location_count = <?php jecho(count($zone_locations)) ?>;
<?php
$temp = array();
foreach($location_remainder as $k => $v) $temp[] = array($k, $v);
?>
var location_remainder = <?php jecho($temp) ?>;
<?php
unset($temp);
?>
$(function() {
	var apply_autocomplete = function(target) {
		$("[name$='[location_str]']", target).autocomplete(location_remainder, {
			"formatItem":function(row, i, max) {
				return row[1];
			},
			"matchContains":true
		}).change(function() {
			var location_str = $(this).val().trim();
			var location_id = 0;
			$(location_remainder).each(function() {
				if (location_str == this[1]) location_id = this[0];
			});
			$(this).siblings("[name$='[location_id]']").val(location_id);
			if (location_id == 0) $(this).val("");
			return true;
		}).result(function(event, data, formatted) {
			$(this).siblings("[name$='[location_id]']").val(data[0]);
		});
	};
	if ($.autocomplete) apply_autocomplete($("body"));
	else $.getScript("/jquery/skip_deps&autocomplete.js", function() {
		apply_autocomplete($("body"));
	});
	$("[name='country_add_button']").click(function() {
		country_count++;
		var $row = $(<?php echo str_replace("[COUNT]", '" + country_count + "', jecho(country_row("[COUNT]"), 1)) ?>);
		$("#country_table").append($row);
		$("#country_table tr").removeClass("alt").filter(":odd").addClass("alt");
	}).show();
	$("[name='location_add_button']").click(function() {
		location_count++;
		var $row = $(<?php echo str_replace("[COUNT]", '" + location_count + "', jecho(location_row("[COUNT]"), 1)) ?>);
		$("#location_table").append($row);
		$("#location_table tr").removeClass("alt").filter(":odd").addClass("alt");
		apply_autocomplete($row);
	}).show();
});
function country_delete(key)
{
	$("#country_row_" + key).remove();
	$("#country_table tr").removeClass("alt").filter(":odd").addClass("alt");
}
function location_delete(key)
{
	$("#location_row_" + key).remove();
	$("#location_table tr").removeClass("alt").filter(":odd").addClass("alt");
}
//]]>
</script>

<?php echo formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionDestinations\');return false">Destinations</a>') ?>

<div id="sectionDestinations">

<h2>Countries</h2>

<table class="table_grid" id="country_table">
	<tr>
		<th style="width:100%;">Country</th>
		<th style="white-space:nowrap;">Delivery Time</th>
		<th>Action</th>
	</tr>
<?php
$count = 0;
$zone_countries_affected = array();
foreach($zone_countries as $country)
{
	$count++;
	$zone_countries_affected[] = $country["id"];
	echo country_row($count, $country);
}
?>
</table>
<div style="text-align:center;">
	<input type="hidden" name="<?php hecho($zone_key . "[countries][affected]") ?>" value="<?php hecho(implode("_", $zone_countries_affected)) ?>" />
	<input name="country_add_button" style="display:none;" type="button" value="Add Country" />
</div>

<h2>Locations</h2>

<table class="table_grid" id="location_table">
	<tr>
		<th style="width:100%;">Location</th>
		<th style="white-space:nowrap;">Delivery Time</th>
		<th>Action</th>
	</tr>
<?php
$count = 0;
$zone_locations_affected = array();
foreach($zone_locations as $location)
{
	$count++;
	$zone_locations_affected[] = $location["id"];
	echo location_row($count, $location);
}
?>
</table>
<div style="text-align:center;">
	<input type="hidden" name="<?php hecho($zone_key . "[locations][affected]") ?>" value="<?php hecho(implode("_", $zone_locations_affected)) ?>" />
	<input name="location_add_button" type="button" value="Add Location" />
</div>

<br />
<br />

</div>

<!-- /destinations.tpl -->

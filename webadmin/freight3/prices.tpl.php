<!-- prices.tpl -->

<?php
function price_row($key, $row = array())
{
	global $zone_key, $zone_id;
	static $alt = true;

	$main_key = $zone_key . "[rates][" . $key . "]";

	$hidden = array(
		"id" => $row["id"],
	);

	ob_start();
?>
<tr<?php echo ($alt = !$alt) ? ' class="alt"' : '' ?> id="price_row_<?php hecho($key) ?>">
	<td>
		<?php echo formatHTMLInputs($hidden, $main_key) ?>
		<input class="number" size="6" type="text" onchange="<?php hecho("weight_change(" . jecho($key, 1) . ")") ?>" value="<?php hecho($row["weight"]) ?>" name="<?php hecho($main_key . "[weight]") ?>" />
	</td>
	<td><input class="number" size="6" type="text" onchange="<?php hecho("weight_change_lbs(" . jecho($key, 1) . ")") ?>" value="<?php hecho($row["weight_lbs"]) ?>" name="<?php hecho($main_key . "[weight_lbs]") ?>" /></td>
	<td><input class="number" size="6" type="text" onchange="<?php hecho("price_change(" . jecho($key, 1) . ")") ?>" value="<?php hecho($row["cost_price"]) ?>" name="<?php hecho($main_key . "[cost_price]") ?>" /></td>
	<td><input type="button" value="Delete" onclick="<?php hecho("price_delete(" . jecho($key, 1) . ")") ?>" /></td>
</tr>
<?php
	return ob_get_clean();
}
?>

<script type="text/javascript">
//<![CDATA[
var last_weight = "";
var last_cost = "";
var price_count = <?php jecho(count($zone_rates)) ?>;
var skip_highlight = false;
$(function() {
	$("[name='price_add_button']").click(function() {
		price_count++;
		var $row = $(<?php echo str_replace("[COUNT]", '" + price_count + "', jecho(price_row("[COUNT]"), 1)) ?>);
		$("#price_table").append($row);
		$("#price_table tr").removeClass("alt").filter(":odd").addClass("alt");
		price_highlight();
		last_weight = "";
		last_cost = "";
	}).show();
	$("[name='price_delete_all_button']").click(function() {
		if (!confirm("Are you sure you want to delete all prices?")) return;
		$("[id^='price_row_']").detach();
		$("#price_table tr").removeClass("alt").filter(":odd").addClass("alt");
		price_highlight();
		last_weight = "";
		last_cost = "";
	}).show();
	price_highlight();
});
function price_delete(key)
{
	$("#price_row_" + key).detach();
	$("#price_table tr").removeClass("alt").filter(":odd").addClass("alt");
	price_highlight();
	last_weight = "";
	last_cost = "";
}
function weight_change(key)
{
	var main_key = <?php jecho($zone_key . "[rates][") ?> + key + "]";
	$("[name='" + main_key + "[weight]']").val(bcadd($("[name='" + main_key + "[weight]']").val(), "0.000", 3));
	$("[name='" + main_key + "[weight_lbs]']").val(bcmul($("[name='" + main_key + "[weight]']").val(), "2.20462262", 3));
	price_highlight();
	last_weight = "";
	last_cost = "";
}
function weight_change_lbs(key)
{
	var main_key = <?php jecho($zone_key . "[rates][") ?> + key + "]";
	$("[name='" + main_key + "[weight]']").val(bcdiv($("[name='" + main_key + "[weight_lbs]']").val(), "2.20462262", 3));
	weight_change(key);
}
function price_change(key)
{
	var main_key = <?php jecho($zone_key . "[rates][") ?> + key + "]";
	$("[name='" + main_key + "[cost_price]']").val(bcadd($("[name='" + main_key + "[cost_price]']").val(), "0.00", 2));
	price_highlight();
	last_weight = "";
	last_cost = "";
}
function price_highlight()
{
	if (skip_highlight) return;
	var valid = true;

	var found = [];
	var dupe = [];
	$("[name$='[weight_lbs]']").css("background-color", "");
	$("[name$='[weight]']").css("background-color", "").each(function() {
		if (in_array($(this).val(), found) || bccomp($(this).val(), "0.000", 3) <= 0) dupe.push($(this).val());
		else found.push($(this).val());
	}).each(function() {
		if (in_array($(this).val(), dupe))
		{
			$("[name='" + $(this).attr("name").split("weight").join("weight_lbs") + "']").css("background-color", "#fdd");
			$(this).css("background-color", "#fdd");
		}
	});
	if (dupe.length > 0) valid = false;

	$("[name$='[cost_price]']").css("background-color", "").each(function() {
		if (bccomp($(this).val(), "0.00", 2) <= 0)
		{
			$(this).css("background-color", "#fdd");
			valid = false;
		}
	});

	if (dupe.length > 0) $("input[value='Submit Changes']").attr("disabled", "disabled");
	else $("input[value='Submit Changes']").attr("disabled", "");
}
function price_sort(field)
{
	var elems = [];
	$("[name$='[" + field + "]']").each(function() {
		elems.push({"row" : $(this).closest("tr"), "val" : $(this).val()});
	});
	elems.sort(function(a, b) {
		return bccomp(a["val"], b["val"], 3);
	});
	var table = $("#price_table");
	for (var i = 0;i < elems.length;i++) table.append(elems[i]["row"]);
	$("#price_table tr").removeClass("alt").filter(":odd").addClass("alt");
	last_weight = "";
	last_cost = "";
}
//]]>
</script>

<?php echo formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionPrices\');return false">Prices</a>') ?>

<div id="sectionPrices">

<table class="table_grid" id="price_table" style="width:auto;">
	<tr>
		<th><a href="#" onclick="price_sort('weight');return false">Weight (kg)</a></th>
		<th><a href="#" onclick="price_sort('weight');return false">Weight (lbs)</a></th>
		<th><a href="#" onclick="price_sort('cost_price');return false">Price (<?php hecho($item["currency"]) ?>)</a></th>
		<th>Action</th>
	</tr>
<?php
$count = 0;
$zone_rates_affected = array();
foreach($zone_rates as $rate)
{
	$count++;
	$zone_rates_affected[] = $rate["id"];
	echo price_row($count, $rate);
}
?>
</table>
<div style="text-align:center;">
	<input type="hidden" name="<?php hecho($zone_key . "[rates][affected]") ?>" value="<?php hecho(implode("_", $zone_rates_affected)) ?>" />
	<input name="price_add_button" style="display:none;" type="button" value="Add Price" />
	<input name="price_delete_all_button" style="display:none;" type="button" value="Delete All Prices" />
</div>
<br />
<br />

<script type="text/javascript">
//<![CDATA[
function addRange()
{
	var consignment_price = $("[name='consignment_price']").val();
	var consignment_weight = $("[name='consignment_weight']").val();
	var per_unit_price = $("[name='per_unit_price']").val();
	var per_unit_weight = $("[name='per_unit_weight']").val();
	var max_weight = $("[name='max_weight']").val();

	if (bccomp("0.000", per_unit_weight, 3) > 0)
	{
		alert("The per weight cannot be 0");
		return;
	}
	if (bccomp(max_weight, consignment_weight, 3) < 0)
	{
		alert("The consignment weight cannot be more than the maximum weight");
		return;
	}

	skip_highlight = true;
	var cost_price = consignment_price;
	for (var weight = consignment_weight;bccomp(weight, max_weight, 3) <= 0;weight = bcadd(weight, per_unit_weight, 3), cost_price = bcadd(cost_price, per_unit_price, 6)) if (last_weight != weight || last_cost != cost_price)
	{
		price_count++;
		var $row = $(<?php echo str_replace("[COUNT]", '" + price_count + "', jecho(price_row("[COUNT]"), 1)) ?>);
		$("#price_table").append($row);
		$("#price_table tr").removeClass("alt").filter(":odd").addClass("alt");

		$("[name='freight_service[zones][0][rates][" + price_count + "][weight]']").val(weight);
		$("[name='freight_service[zones][0][rates][" + price_count + "][cost_price]']").val(bcadd(cost_price, "0.00", 2));
		weight_change(price_count);

		last_weight = weight;
		last_cost = cost_price;
	}
	skip_highlight = false;
	price_highlight();

	$("[name='consignment_price']").val(bcsub(cost_price, per_unit_price, 6));
	$("[name='consignment_weight']").val(bcsub(weight, per_unit_weight, 3));
	$("[name='per_unit_price']").val("");
	$("[name='max_weight']").val("");
}
$(function() {
	$("[name='consignment_price'], [name='per_unit_price']").change(function() {
		$(this).val(bcadd($(this).val(), "0.00", 6));
	});
	$("[name='consignment_weight'], [name='per_unit_weight'], [name='max_weight']").change(function() {
		$(this).val(bcadd($(this).val(), "0.000", 3));
	});
});
//]]>
</script>
</div>

<?php echo formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionPriceRanges\');return false">Quick Price Range</a>') ?>

<div id="sectionPriceRanges">

<table class="table_fields" style="width:50%;margin:auto;">
	<tr>
		<th style="width:50%;">Start Price (<?php hecho($item["currency"]) ?>):</th>
		<td><input class="number wide" size="6" name="consignment_price" /></td>
	</tr>
	<tr>
		<th>Start Weight (kg):</th>
		<td><input size="6" class="number wide" name="consignment_weight" /></td>
	</tr>
	<tr>
		<th>Finish Weight (kg):</th>
		<td><input class="number wide" size="6" name="max_weight" /></td>
	</tr>
	<tr>
		<th>Divisions (kg):</th>
		<td><input size="6" class="number wide" name="per_unit_weight" /></td>
	</tr>
	<tr>
		<th>Price Per Division (<?php hecho($item["currency"]) ?>):</th>
		<td><input class="number wide" size="6" name="per_unit_price" /></td>
	</tr>
</table>
<div style="text-align:center;"><input type="button" value="Add Price Range" onclick="addRange()" /></div>
<br />
<br />
</div>


<!-- /prices.tpl -->

<!-- zones.tpl -->

<?php
$section_name = $section_handler->section_name;

function zone_row($key, $row = array())
{
    global $section_name;
    static $alt = false;
    $main_key   = $section_name . '[zones][' . $key . ']';
    $hidden     = array(
        "id" => $row["id"],
    );

    if (strlen($row["countries_tooltip"]) > 255) {
        $row["countries_tooltip"] = substr($row["countries_tooltip"], 0, 252) . "...";
    }

    if (strlen($row["locations_tooltip"]) > 255) {
        $row["locations_tooltip"] = substr($row["locations_tooltip"], 0, 252) . "...";
    }

    ob_start();
?>
<tr id="<?php hecho($main_key)?>"<?php echo ($alt = !$alt) ? ' class="alt"' : '' ?>>
	<td style="width:100%;">
		<?php echo formatHTMLInputs($hidden, $main_key) ?>
		<input type="text" name="<?php hecho($main_key . "[name]")?>" value="<?php hecho($row["name"])?>" class="wide" />
	</td>
	<td><abbr id="<?php hecho($main_key . "[countries_abbr]")?>" title="<?php hecho($row["countries_tooltip"])?>" style="border-bottom:1px dotted #000;"> <?php echo intval($row["countries_linked"]) ?> </abbr></td>
	<td><abbr id="<?php hecho($main_key . "[locations_abbr]")?>" title="<?php hecho($row["locations_tooltip"])?>" style="border-bottom:1px dotted #000;"> <?php echo intval($row["locations_linked"]) ?> </abbr></td>
	<td style="white-space:nowrap;" id="<?php hecho($main_key . "[weight_range]")?>"><?php hecho($row["weight_range"])?></td>
	<td style="white-space:nowrap;" id="<?php hecho($main_key . "[price_range]")?>"><?php hecho($row["cost_price_range"])?></td>
	<td style="white-space:nowrap;">
<?php
if (0 != $row["id"]) {
?>
		<input type="button" value="Destinations" onclick="<?php hecho("window.open(" . jecho("index.php?action=destinations&return_message=edit_destinations&zone_id=" . $row["id"], 1) . ")")?>" />
		<input type="button" value="Pricing" onclick="<?php hecho("window.open(" . jecho("index.php?action=prices&return_message=edit_prices&zone_id=" . $row["id"], 1) . ")")?>" />
<?php
} else {
?>
		<input type="button" value="Destinations" disabled="disabled" title="Please submit changes to enable this button" />
		<input type="button" value="Pricing" disabled="disabled" title="Please submit changes to enable this button" />
<?php
}
?>
		<input type="button" value="Delete" onclick="<?php hecho("if (confirm(" . jecho("Are you sure?", 1) . ")) $(this).closest(" . jecho("tr", 1) . ").detach();$(\"#zones_table tr[id^='" . $section_name . "[zones][']\").removeClass(\"alt\").filter(\":odd\").addClass(\"alt\")")?>" />
	</td>
</tr>
<?php
return ob_get_clean();
}

if (is_array($item_zones)) {

?>

<script type="text/javascript">
//<![CDATA[
var item_zones_count = <?php jecho(max(100, count($item_zones)))?>;
function setFreightServiceZoneData(data)
{
	switch(data["return_message"])
	{
	case "edit_destinations":
	case "edit_prices":
		$("tr[id^='<?php echo $section_name ?>[zones][']").each(function() {
			if ($("[name$='[id]']", this).val() == data["id"])
			{
				var countries = [];
				for (var i = 0;i < data["countries"].length;i++) countries.push(data["countries"][i]["country_name"]);
				countries.sort();
				countries = countries.join(", \n");
				//if (countries.length > 255) countries = countries.substr(0, 252) + "...";
				$("[id$='[countries_abbr]']", this).attr("title", countries).text(data["countries"].length);

				var locations = [];
				for (var i = 0;i < data["locations"].length;i++) locations.push(data["locations"][i]["location_name"]);
				locations.sort();
				locations = locations.join(", \n");
				//if (locations.length > 255) locations = locations.substr(0, 252) + "...";
				$("[id$='[locations_abbr]']", this).attr("title", locations).text(data["locations"].length);

				var weight_range = null;
				var price_range = null;
				for (var i = 0;i < data["rates"].length;i++)
				{
					var rate = data["rates"][i];
					if (bccomp(rate["weight"], "0.000", 3) > 0)
					{
						var cost_per_kg = bcdiv(rate["cost_price"], rate["weight"], 2);
						if (weight_range == null)
						{
							weight_range = {"min":rate["weight"], "max":rate["weight"]};
							price_range = {"min":cost_per_kg, "max":cost_per_kg};
						}
						else
						{
							if (bccomp(rate["weight"], weight_range["min"], 3) < 0) weight_range["min"] = rate["weight"];
							else if (bccomp(rate["weight"], weight_range["max"], 3) > 0) weight_range["max"] = rate["weight"];
							if (bccomp(cost_per_kg, price_range["min"], 3) < 0) price_range["min"] = cost_per_kg;
							else if (bccomp(cost_per_kg, price_range["max"], 3) > 0) price_range["max"] = cost_per_kg;
						}
					}
				}
				if (weight_range != null)
				{
					$("[id$='[weight_range]']", this).text(weight_range["min"] + " - " + weight_range["max"]);
					$("[id$='[price_range]']", this).text("$" + price_range["min"] + " - $" + price_range["max"]);
				}
			}
		});
	}
}
$(function() {
	$("#zones_add_zone_button").click(function() {
		$("#zones_table").append(<?php echo str_replace("[COUNT]", '" + item_zones_count + "', jecho(zone_row("[COUNT]"), 1)) ?>);
		$(<?php jecho("#zones_table tr[id^='" . $section_name . "[zones][']")?>).removeClass("alt").filter(":odd").addClass("alt");
		item_zones_count++;
	});
	$("#zones_print_summary_button").click(function() {
	});
});
//]]>
</script>

<?php echo formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionZones\');return false">Zones</a>') ?>

<div id="sectionZones">

<table id="zones_table" class="table_grid">
	<tr>
		<th>Zone Name</th>
		<th>Countries</th>
		<th>Locations</th>
		<th>Weight Range (kg)</th>
		<th>Price Range (<?php hecho($item["currency"])?>)</th>
		<th>Action</th>
	</tr>
<?php
$country_list  = $section_handler->getOptionList("country");
    $location_list = $section_handler->countryLocationsFromDatabase($item["service_from"]);

    $count               = 0;
    $item_zones_affected = array();
    foreach ($item_zones as $zone) {
        $countries_linked          = $section_handler->zoneCountriesFromDatabase($zone["id"]);
        $zone["countries_tooltip"] = array();
        foreach ($countries_linked as $row) {
            $zone["countries_tooltip"][] = $country_list[$row["country"]];
        }

        sort($zone["countries_tooltip"]);
        $zone["countries_tooltip"] = implode(", \n", $zone["countries_tooltip"]);
        $zone["countries_linked"]  = count($countries_linked);

        $locations_linked          = $section_handler->zoneLocationsFromDatabase($zone["id"]);
        $zone["locations_tooltip"] = array();
        foreach ($locations_linked as $row) {
            $zone["locations_tooltip"][] = $location_list[$row["location_id"]];
        }

        sort($zone["locations_tooltip"]);
        $zone["locations_tooltip"] = implode(", \n", $zone["locations_tooltip"]);
        $zone["locations_linked"]  = count($locations_linked);

        $zone["weight_range"] = "-";
        $zone["price_range"]  = "-";

        $rate_min = array();
        $rate_max = array();
        foreach ($section_handler->zoneRatesFromDatabase($zone["id"]) as $rate) {
            if (count($rate_min) == 0) {
                $rate_min = $rate_max = $rate;
            } else {
                foreach ($rate as $k => $v) {
					/*
					if ("weight" != $k && bccomp($rate["weight"], "0.000", 3) != 0) {
                        $v = bcdiv($v, $rate["weight"], 2);
					}
					*/

                    if (bccomp($v, $rate_min[$k], 2) < 0) {
                        $rate_min[$k] = $v;
                    } elseif (bccomp($rate_max[$k], $v, 2) < 0) {
                        $rate_max[$k] = $v;
                    }

                }
            }

        }

        if (count($rate_min)) {
            $zone["weight_range"]     = $rate_min["weight"] . " - " . $rate_max["weight"];
            $zone["cost_price_range"] = '$' . $rate_min["cost_price"] . " - " . '$' . $rate_max["cost_price"];
        }

        $item_zones_affected[] = $zone["id"];
        echo zone_row($count++, $zone);
    }
?>
</table>
<br />
<div style="text-align:center;">
	<input id="zones_add_zone_button" type="button" value="Add Zone" />
	<input id="zones_print_summary_button" type="button" value="Print Summary" class="unfinished" />
</div>

<input type="hidden" name="<?php hecho($section_handler->section_name . "[zones][affected]")?>" value="<?php hecho(implode(",", $item_zones_affected))?>" />

<br />
<br />

</div>

<?php
}
?>

<!-- /zones.tpl -->

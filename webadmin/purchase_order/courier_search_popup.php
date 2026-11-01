<?php
include_once __DIR__ . "/common.inc.php";

$_REQUEST["show_all"] = $_GET["show_all"] = "true";

$cubic_divisor = "5000";

$invoice               = invoiceFromPost($posted);
$invoice["ship_mass"]  = "0.000";
$invoice["ship_cubic"] = "0.000";

$invoice_products = invoiceProductsFromPost($posted);
$issues           = array("weight" => false);
$problem_products = array();

foreach ($invoice_products as $k => $v) {
    if ("y" != $v["generic"] && "b" != $v["generic"]) {
        $product_str = productStrJoin(intval($v["product_id"]), intval($v["sub_id"]), intval($invoice["site_id"]));
        $product     = productFromDatabase($product_str);

        $dimensions = productDimensionFromDatabase($product_str);
        if (!is_array(reset($dimensions))) {
            $dimensions = array($dimensions);
        }

        $packing = array();
        if (function_exists("productPackingFromDatabase")) {
            $packing = productPackingFromDatabase($product_str, $invoice["site_id"], true);
        }

        $bulk_sizes = array();
        foreach ($dimensions as $row) {
            $bulk_sizes[1] = array(array(
                "qty"    => 1,
                "weight" => $row["weight"],
                "cubic"  => bcmul($row["cubic"], (0 != $row["cubic_divisor"]) ? $row["cubic_divisor"] : $cubic_divisor, 3),
                "width"  => $row["pack_width"],
                "depth"  => $row["pack_depth"],
                "height" => $row["pack_height"],
            ));
        }

        foreach ($dimensions as $row) {
            $bulk_sizes[intval($row["carton_units"])] = array(array(
                "qty"    => $row["carton_units"],
                "weight" => $row["carton_weight"],
                "cubic"  => bcmul($row["carton_cubic"], (0 != $row["carton_cubic_divisor"]) ? $row["carton_cubic_divisor"] : $cubic_divisor, 3),
                "width"  => $row["carton_width"],
                "depth"  => $row["carton_depth"],
                "height" => $row["carton_height"],
            ));
        }

        if (function_exists("productBulkWeightsFromDatabase")) {
            foreach (productBulkWeightsFromDatabase($product_str) as $row) {
                $bulk_sizes[$row["qty"]] = array(array(
                    "qty"    => $row["qty"],
                    "weight" => $row["weight"],
                    "cubic"  => bcmul($row["cubic"], (0 != $row["cubic_divisor"]) ? $row["cubic_divisor"] : $cubic_divisor, 3),
                    "width"  => $row["width"],
                    "depth"  => $row["depth"],
                    "height" => $row["height"],
                ));
            }
        }

        if (count($packing)) {
            foreach ($packing as $k1 => $v1) {
                if ("y" == $v1["pallet_enable"]) {
                    $try1 = bcmul((bccomp($v1["pallet_width"], $v1["width"], 1) > 0) ? $v1["pallet_width"] : $v1["width"], (bccomp($v1["pallet_depth"], $v1["depth"], 1) > 0) ? $v1["pallet_depth"] : $v1["depth"], 2);
                    $try2 = bcmul((bccomp($v1["pallet_width"], $v1["depth"], 1) > 0) ? $v1["pallet_width"] : $v1["depth"], (bccomp($v1["pallet_depth"], $v1["width"], 1) > 0) ? $v1["pallet_depth"] : $v1["width"], 2);
                    if (bccomp($try1, $try2, 2) < 0) {
                        $packing[$k1]["volume"] = bcmul($try1, bcadd($v1["pallet_height"], $v2["height"], 1), 3);
                    } else {
                        $packing[$k1]["volume"] = bcmul($try2, bcadd($v1["pallet_height"], $v1["height"], 1), 3);
                    }

                    $packing[$k1]["weight"]         = bcadd($v1["weight"], $v1["pallet_weight"], 3);
                    $packing[$k1]["volume_divisor"] = "1";
                }
            }

            $bulk_sizes[1] = array();
            foreach ($packing as $row) {
                $bulk_sizes[1][] = array(
                    "qty"    => 1,
                    "weight" => $row["weight"],
                    "cubic"  => bcmul($row["volume"], (0 != $row["volume_divisor"]) ? $row["volume_divisor"] : $cubic_divisor, 3),
                    "width"  => $row["width"],
                    "depth"  => $row["depth"],
                    "height" => $row["height"],
                );
            }

        }
        unset($bulk_sizes[0]);
        krsort($bulk_sizes);

        $changes = true;
        while ($changes) {
            $changes = false;
            foreach ($bulk_sizes as $k1 => $v1) {
                if (count($v1) == 1 && (bccomp($v1[0]["width"], "0.00", 2) == 0 || bccomp($v1[0]["depth"], "0.00", 2) == 0 || bccomp($v1[0]["height"], "0.00", 2) == 0)) {
                    foreach ($bulk_sizes as $k2 => $v2) {
                        if ($k2 < $k1 && count($v2) == 1) {
                            if (bccomp($v2[0]["width"], "0.00", 2) != 0 && bccomp($v2[0]["depth"], "0.00", 2) != 0 && bccomp($v2[0]["height"], "0.00", 2) != 0) {
                                $multiplier = ($k1 + 0.0) / ($k2 + 0.0);
                                $root_x     = ceil(pow($multiplier, 1.0 / 3.0));
                                $root_y     = ceil(sqrt($multiplier / $root_x));
                                $root_z     = ceil($multiplier / ($root_x * $root_y));
                                $dim        = array($v2[0]["width"], $v2[0]["depth"], $v2[0]["height"]);
                                bcscale(2);
                                usort($dim, "bccomp");
                                $bulk_sizes[$k1][0]["width"]  = bcmul($dim[0], $root_x, 2);
                                $bulk_sizes[$k1][0]["depth"]  = bcmul($dim[1], $root_y, 2);
                                $bulk_sizes[$k1][0]["height"] = bcmul($dim[2], $root_z, 2);
                                $changes                      = true;
                            }
                            break;
                        }
                    }
                }
            }

        }

        $packages  = array();
        $qty_count = $v["product_qty"];
        foreach ($bulk_sizes as $qty => $row) {
            while ($qty <= $qty_count) {
                $packages = array_merge($packages, $row);
                $qty_count -= $qty;
            }
        }

        foreach ($packages as $k1 => $v1) {
            if (bccomp($v1["weight"], "0.000", 3) == 0) {
                if (bccomp($product["weight_inc"], "0.000", 3) == 0) {
                    $packages[$k1]["weight"] = bcmul($product["weight"], $v1["qty"], 3);
                } else {
                    $packages[$k1]["weight"] = bcadd($product["weight"], bcmul($product["weight_inc"], $v1["qty"] - 1, 3), 3);
                }

            }
            if (bccomp($v1["cubic"], "0.000", 3) == 0) {
                $packages[$k1]["cubic"] = bcmul($v1["width"], bcmul($v1["depth"], $v1["height"], 3), 3);
            }

        }

        $ship_weight = "0.000";
        $ship_mass   = "0.000";
        $ship_cubic  = "0.000";
        foreach ($packages as $row) {
            $ship_mass  = bcadd($ship_mass, $row["weight"], 3);
            $ship_cubic = bcadd($ship_cubic, bcdiv($row["cubic"], $cubic_divisor, 3), 3);
        }
        if (bccomp($ship_mass, $ship_cubic, 3) >= 0) {
            $ship_weight = $ship_mass;
        } else {
            $ship_weight = $ship_cubic;
        }

        if (bccomp($ship_weight, "0.0000", 4) <= 0) {
            $issues["weight"]   = true;
            $problem_products[] = $v;
        }

        $invoice["ship_mass"]  = bcadd($invoice["ship_mass"], $ship_mass, 3);
        $invoice["ship_cubic"] = bcadd($invoice["ship_cubic"], $ship_cubic, 3);
        if (bccomp($invoice["ship_weight"], $invoice["ship_mass"], 3) <= 0 || bccomp($invoice["ship_weight"], $invoice["ship_cubic"], 3) <= 0) {
            if (bccomp($invoice["ship_mass"], $invoice["ship_cubic"], 3) > 0) {
                $invoice["ship_weight"] = $invoice["ship_mass"];
            } else {
                $invoice["ship_weight"] = $invoice["ship_cubic"];
            }

        }
    } else {
        $problem_products[] = $v;
    }
}

$count                  = 0;
$invoice_courier_quotes = array();
if (moduleLoad("freight") && bccomp($invoice["ship_weight"], "0.0000", 4) != 0/* && !$issues["weight"]*/) {
    $flag  = false;
    $found = array();
    foreach (freightSortArray(freightSearchCouriers($invoice["ship_country"], $invoice["ship_postcode"], $invoice["ship_state"], $invoice["ship_city"], $invoice["ship_weight"], $invoice["products_offer"], $invoice["invoice_currency"])) as $v) {
        $key                 = $v["ship_number"];
        $v["packing_string"] = "";

        $container_list = array(true);
        if (moduleLoad("packing")) {
            list($width, $depth, $height) = array($v['width'], $v['depth'], $v['height']);
            if ($width > 0 && $depth > 0) {
                $packing                     = array();
                $packing['packing_products'] = $invoice_products;
                $packing['satchel']          = array('width' => $v['width'], 'depth' => $v['depth'], 'height' => $v['height'], 'weight' => $v['weight'], 'name' => $v['name']);
                $packing['packing_action']   = 'checkout_satchel';
                $packing['spareparts']       = true;
                $error_list                  = array();
                $container_list              = packingFromCache($packing);
                if (count($error_list)) {
                    $container_list = array();
                }

                if (count($container_list) == 1) {
                    $v["packing_string"] = "b64_gz_ser:" . base64_encode(gzcompress(serialize($container_list)));
                }

            }
        }

        if (!array_key_exists($key, $found) && count($container_list) == 1) {
            /**
             * Stupid fucking price rounding, argh
             */
            $dollars = convertCurrency($v["sell_price"], $v["currency"], $invoice["invoice_currency"]);
            $cents   = bcmod(bcmul($dollars, "100", 0), "100");
            if (bccomp($cents, "0", 0) > 0) {
                $dollars = bcadd($dollars, bcmul(bcsub("100", $cents, 0), "0.01", 2), 2);
            }

            $invoice_courier_quotes[] = array(
                "ship_number"    => $v["ship_number"],
                "service_id"     => $v["id"],
                "express"        => array_key_exists("express", $v) ? $v["express"] : "n",
                "receipted"      => array_key_exists("receipted", $v) ? $v["receipted"] : "n",
                "registered"     => array_key_exists("registered", $v) ? $v["registered"] : "n",

                "width"          => $v["width"],
                "depth"          => $v["depth"],
                "height"         => $v["height"],
                "weight"         => $v["weight"],

                "quote_count"    => $count,
                "flag"           => ("y" == $v["active"] && !$flag) ? "y" : "n",
                "name"           => $v["name"],
                "del_time"       => $v["del_time"],
                "track"          => $v["track"],
                "insurance"      => $v["insurance"],
                "tail_gate"      => $v["tail_gate"],
                "cost_price"     => convertCurrency($v["cost_price"], $v["currency"], $invoice["invoice_currency"]),
                //"sell_price" => convertCurrency($v["sell_price"], $v["currency"], $invoice["invoice_currency"]),
                "retail_price"   => convertCurrency($v["retail_price"], $v["currency"], $invoice["invoice_currency"]),
                "cost_priceau"   => convertCurrency($v["cost_price"], $v["currency"], "AUD"),
                //"sell_priceau" => convertCurrency($v["sell_price"], $v["currency"], "AUD"),
                "cost_priceus"   => convertCurrency($v["cost_price"], $v["currency"], "USD"),
                //"sell_priceus" => convertCurrency($v["sell_price"], $v["currency"], "USD"),
                "supplier"       => $v["supplier"],
                "supplier_name"  => $v["supplier_name"],

                // Continued: Stupid fucking rounding
                "sell_price"     => $dollars,
                "sell_priceau"   => convertCurrency($dollars, $invoice["invoice_currency"], "AUD"),
                "sell_priceus"   => convertCurrency($dollars, $invoice["invoice_currency"], "USD"),

                "active"         => $v["active"],
                "packing_string" => $v["packing_string"],
                "Locality"       => array_key_exists("Locality", $v) ? $v["Locality"] : "",
                "State"          => array_key_exists("State", $v) ? $v["State"] : "",
                "Pcode"          => array_key_exists("Pcode", $v) ? $v["Pcode"] : "",
                "Country"        => $v["Country"],
                "qty"            => ("pp" == $v["type"]) ? $v["qty"] : "-",
            );
            if ("y" == $v["active"]) {
                $flag = true;
            }

            $found[$key] = $key;
            $count++;
        }
    }
}

if (!array_key_exists("return_message", $_GET)) {
    $_GET["return_message"] = " ";
}

$active   = array();
$inactive = array();
foreach ($invoice_courier_quotes as $k => $v) {
    if ("y" == $v["active"]) {
        $active[$k] = $v;
    } else {
        $inactive[$k] = $v;
    }
}

include_once __DIR__ . "/header.inc.php";
?>
<style type="text/css">
/*<![CDATA[*/
.ac_results{padding:0;border:1px solid #000;background-color:#fff;overflow:hidden;z-index:99999}
.ac_results ul{width:100%;list-style-position:outside;list-style:none;padding:0;margin:0}
.ac_results li{margin:0;padding:2px 5px;cursor:default;display:block;font:menu;font-size:12px;line-height:16px;overflow:hidden}
.ac_loading{background:#fff url(/images/loadingAnimation.gif) right center no-repeat}
.ac_odd{background-color:#eee}
.ac_over{background-color:#126;color:#fff}
.active_n td{background:#fcc}
.alt.active_n td{background:#fbb}
.highlight.active_n td{background:#fb7}
/*]]>*/
</style>

<script type="text/javascript" src="/jquery/autocomplete&skip_deps.js"></script>

<script type="text/javascript">
//<![CDATA[
var all_data = <?php jecho($invoice_courier_quotes)?>;
var active_data = <?php jecho($active)?>;
<?php
if (count($invoice_courier_quotes)) {
?>
if (window.opener != null && window.opener.setCourierData != null) window.opener.setCourierData(active_data);
else if(parent.setCourierData != null) parent.setCourierData(active_data);
<?php
}
?>

$(function() {
	$("[name='checkout[ship_number]']").change(function() {
		if ($(this).attr("checked"))
		{
			$(this).closest("table").find(".highlight").removeClass("highlight");
			$(this).closest("tr").addClass("highlight");
		}
	});
	$("[name='courier_search_accept']").click(function() {
		var selected_number = $("[name='checkout[ship_number]']:checked").val();
		for (var k in all_data) if (all_data[k].ship_number == selected_number)
		{
			if (window.opener != null && window.opener.setShipToCourierQuote != null) window.opener.setShipToCourierQuote(all_data[k]);
			else if(parent.setShipToCourierQuote != null) parent.setShipToCourierQuote(all_data[k]);
			else alert('Unable to send freight quote back to form');
			window.close();
			break;
		}
	}).fadeIn();
	$("[name='shipto_country[0]']").change(function() {
		$(".ship_country_AU").stop().toggle($(this).val() == "AU");
	}).change();
	$("[name='shipto_city[0]'], [name='shipto_state[0]'], [name='shipto_postcode[0]']").autocomplete("postcode_search.json.php", {
		"formatItem":function(row, i, max) {
			return row.Locality + ", " + row.State + " " + row.Pcode;
		},
		"matchContains":true,
		"scroll":true,
		"parse":function parse(data) {
			var parsed = [];
			for (var i = 0;i < data.length;i++) parsed.push({
				data: data[i],
				value: data[i].Locality + ", " + data[i].State + " " + data[i].Pcode,
				result: null
			});
			return parsed;
		}
	}).result(function(event, data, formatted) {
		$("[name='shipto_city[0]']").val(data.Locality);
		$("[name='shipto_state[0]']").val(data.State);
		$("[name='shipto_postcode[0]']").val(data.Pcode);
	});
});

//]]>
</script>

<form accept-charset="utf-8" action="" method="post">
<?php
$inputs = $posted;
foreach (array("shipto_country", "shipto_postcode", "shipto_state", "shipto_city", "ship_weight", "products_offer" /*"invoice_currency", */) as $v) {
    if (array_key_exists($v, $inputs)) {
        unset($inputs[$v]);
    }
}

echo formatHTMLInputs($inputs);

$varies = array();
foreach ($invoice_courier_quotes as $k1 => $v1) {
    foreach ($v1 as $k2 => $v2) {
        $varies[$k2][$v2] = $v2;
    }
}

?>
	<table style="width:75%;">
<?php
if (20 != $_GLOBALS["site_settings"]["site_id"]) {
?>
		<tr class="ship_country_AU">
			<td class="left_title" style="width:33%;">City</td>
			<td style="width:33%;"><input type="text" name="shipto_city[0]" value="<?php hecho($invoice['ship_city'])?>" style="width:98%;" /></td>
			<td style="color:#f00;width:33%;">
<?php
if (!array_key_exists("Locality", $varies)) {
?>
				<span style="color:#f00;">WARNING: No match found</span>
<?php
} elseif (!array_key_exists("Locality", $varies) || strtoupper(reset($varies["Locality"])) != strtoupper($invoice['ship_city'])) {
?>
				<span style="color:#f00;">WARNING: Using closest match '<?php hecho(ucwords(strtolower(reset($varies["Locality"]))))?>'</span>
<?php
} else {
?>
				<span style="color:#0f0;">Match found</span>
<?php
}
?>
			</td>
		</tr>
		<tr class="ship_country_AU">
			<td class="left_title">State</td>
			<td><input type="text" name="shipto_state[0]" value="<?php hecho($invoice['ship_state'])?>" style="width:98%;" /></td>
			<td style="color:#f00;width:33%;">
<?php
if (!array_key_exists("State", $varies)) {
?>
				<span style="color:#f00;">WARNING: No match found</span>
<?php
} elseif (strtoupper(reset($varies["State"])) != strtoupper($invoice['ship_state'])) {
?>
				<span style="color:#f00;">WARNING: Using closest match '<?php hecho(strtoupper(reset($varies["State"])))?>'</span>
<?php
} else {
?>
				<span style="color:#0f0;">Match found</span>
<?php
}
?>
			</td>
		</tr>
		<tr class="ship_country_AU">
			<td class="left_title">Postcode</td>
			<td><input type="text" name="shipto_postcode[0]" value="<?php hecho($invoice['ship_postcode'])?>" style="width:98%;" /></td>
			<td style="color:#f00;width:33%;">
<?php
if (!array_key_exists("Pcode", $varies)) {
?>
				<span style="color:#f00;">WARNING: No match found</span>
<?php
} elseif (intval(reset($varies["Pcode"])) != intval($invoice['ship_postcode'])) {
?>
				<span style="color:#f00;">WARNING: Using closest match '<?php printf("%04d", reset($varies["Pcode"]))?>'</span>
<?php
} else {
?>
				<span style="color:#0f0;">Match found</span>
<?php
}
?>
			</td>
		</tr>
<?php
}
?>
		<tr>
			<td class="left_title" style="width:33%;">Country</td>
			<td style="width:33%;"><select name="shipto_country[0]">
				<option value=""></option>
				<?=formatHTMLOptions(getCountryList(), $invoice['ship_country'])?>
			</select></td>
			<td style="width:33%;"></td>
		</tr>
		<tr>
			<td class="left_title">Shipment Weight (kg)</td>
			<td><input type="text" name="ship_weight" value="<?php hecho($invoice['ship_weight'])?>" style="width:98%;" /></td>
			<td></td>
		</tr>
		<tr>
			<td class="left_title">Insurance Value (<?php hecho($invoice["invoice_currency"])?>)</td>
			<td><input type="text" name="products_offer" value="<?php hecho($invoice['products_offer'])?>" style="width:98%;" /></td>
			<td></td>
		</tr>
	</table>
	<input type="submit" />
</form>

<?php
if (count($problem_products)) {
?>
<div style="border:3px solid #f00;background:#fbb;width:95%;margin:auto;text-align:left;"><div style="padding:16px;">
	Correct shipping weights are unavailable for:
	<ul>
<?php
foreach ($problem_products as $v) {
?>
		<li><?php hecho(((0 != $v["sub_id"]) ? sprintf("%05d-%04d", $v["product_id"], $v["sub_id"]) : sprintf("%05d", $v["product_id"])) . " - " . $v["product_name"])?></li>
<?php
}
?>
	</ul>
</div></div>
<?php
}

if (moduleLoad("freight")) {
    foreach (freightWarningsSearch(array("country" => $invoice["ship_country"])) as $id) {
        $warning = freightWarningFromDatabase($id);
?>
<div style="background:pink;border:3px solid red;text-align:center;padding:16px;margin:16px;"><b style="color:#f00;">Notice:</b> <?=nl2br(hecho($warning["notes"], 1))?></div>
<?php
}
}

if (!moduleLoad("packing")) {
?>
<div style="background:pink;border:3px solid red;text-align:center;padding:16px;margin:16px;"><b style="color:#f00;">Notice:</b> Packing dimension calculations were unsuccessful, please ensure products will fit your selected box or satchel before continuing</div>
<?php
}

$courier_list = array();
if (function_exists("getFreightServices") || moduleLoad("freight")) {
    $courier_list = getFreightServices('personal', '', true);
}

/*
$varies = array();
foreach($invoice_courier_quotes as $k1 => $v1) foreach($v1 as $k2 => $v2) $varies[$k2][$v2] = $v2;
unset($varies["packing_string"]);
foreach($varies as $k => $v) if (count($v) == 1) unset($varies[$k]);
print_table($varies);
 */
?>

<?=formatHTMLTitle("Available Freight Quotes", '<a href="#" onclick="' . hecho("$(\".active_n\").toggle();return false", 1) . '">Show/Hide Inactive Services</a>')?>

<table class="wide spacetable table_grid">
	<tr>
		<th>ID</th>
		<th>Service</th>
		<th>Transit Time</th>
		<th>Tracking</th>
		<th>Insurance</th>
		<th>Tail Gate</th>
		<th>Qty</th>
		<th>Cost<br />Price<br />(<?php hecho($invoice["invoice_currency"])?>)</th>
		<th>Sell<br />Price<br />(<?php hecho($invoice["invoice_currency"])?>)</th>
<?php
if (in_array($invoice["invoice_currency"], array("AUD", "USD")) || 20 != $invoice["site_id"]) {
    if ("AUD" != $invoice["invoice_currency"]) {
?>
		<th>Cost<br />Price<br />(AUD)</th>
		<th>Sell<br />Price<br />(AUD)</th>
<?php
}
} elseif ("USD" != $invoice["invoice_currency"]) {
?>
		<th>Cost<br />Price<br />(USD)</th>
		<th>Sell<br />Price<br />(USD)</th>
<?php
}
?>
	</tr>
<?php
$alt = true;
if ( /*!$issues["weight"] &&*/count($invoice_courier_quotes)) {
    foreach ($invoice_courier_quotes as $k => $v) {
        $alt     = !$alt;
        $key     = "checkout_invoice_courier_quote_" . $k;
        $class   = array();
        $class[] = "active_" . $v["active"];
        if ("y" == $v["flag"]) {
            $class[] = "highlight";
        }

        if ($alt) {
            $class[] = "alt";
        }

?>
	<tr<?=(count($class)) ? (' class="' . implode(" ", $class) . '"') : ''?> <?=("n" == $v["active"]) ? ' style="display:none;"' : ''?>>
		<td><input type="radio" name="checkout[ship_number]"<?=("y" == $v["flag"]) ? ' checked="checked"' : ''?> value="<?php hecho($v["ship_number"])?>" id="<?=$key?>" /> <label for="<?=$key?>"><?php hecho($v["ship_number"])?></label></td>
		<td style="white-space:nowrap;"><label for="<?=$key?>"><?php hecho($v["name"])?></label></td>
		<td style="text-align:center;"><label for="<?=$key?>"><?php hecho($v["del_time"])?></label></td>
		<td style="text-align:center;"><label for="<?=$key?>"><?=("y" == $v["track"]) ? "Yes" : "No"?></label></td>
		<td style="text-align:center;"><label for="<?=$key?>"><?=("y" == $v["insurance"]) ? "Yes" : "No"?></label></td>
		<td style="text-align:center;"><label for="<?=$key?>"><?=("y" == $v["tail_gate"]) ? "Yes" : "No"?></label></td>
		<td style="text-align:center;"><label for="<?=$key?>"><?=$v["qty"]?></label></td>
		<td style="text-align:right;"><label for="<?=$key?>"><b style="white-space:nowrap;"><?php hecho(getCurrencySymbol($invoice["invoice_currency"]) . $v["cost_price"])?></b></label></td>
		<td style="text-align:right;"><label for="<?=$key?>"><b style="white-space:nowrap;"><?php hecho(getCurrencySymbol($invoice["invoice_currency"]) . $v["sell_price"])?></b></label></td>
<?php
if (in_array($invoice["invoice_currency"], array("AUD", "USD")) || 20 != $invoice["site_id"]) {
            if ("AUD" != $invoice["invoice_currency"]) {
?>
		<td style="text-align:right;"><label for="<?=$key?>"><b style="white-space:nowrap;"><?php hecho("$" . $v["cost_priceau"])?></b></label></td>
		<td style="text-align:right;"><label for="<?=$key?>"><b style="white-space:nowrap;"><?php hecho("$" . $v["sell_priceau"])?></b></label></td>
<?php
}
        } elseif ("USD" != $invoice["invoice_currency"]) {
?>
		<td style="text-align:right;"><label for="<?=$key?>"><b style="white-space:nowrap;"><?php hecho("$" . $v["cost_priceus"])?></b></label></td>
		<td style="text-align:right;"><label for="<?=$key?>"><b style="white-space:nowrap;"><?php hecho("$" . $v["sell_priceus"])?></b></label></td>
<?php
}
?>
	</tr>
<?php
}
}

?>
</table>
<br />
<br />

<div style="text-align:center;">
	<input type="button" name="courier_search_accept" value="Add Selection" style="display:none;" />
</div>
<?php

include_once __DIR__ . '/footer.inc.php';

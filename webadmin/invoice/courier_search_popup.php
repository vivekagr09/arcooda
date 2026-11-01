<?php
$_REQUEST["show_all"] = $_GET["show_all"] = "true";

include_once __DIR__ . '/common.inc.php';
moduleLoad('freight'); //freightSortArray, freightWarningsSearch, freightWarningFromDatabase
moduleLoad('product'); //productFromDatabase

$cubic_divisor = "5000";
$invoice               = invoiceFromPost($posted);
$invoice["ship_mass"]  = "0.000";
$invoice["ship_cubic"] = "0.000";
if($invoice['ship_weight'] == 0 ){
    unset($posted['product_affected']);
    unset($posted['prod_count']);
}

$invoice_products = invoiceProductsFromPost($posted);
$issues           = array("weight" => false);
$problem_products = array();
foreach ($invoice_products as $k => $v) {
    if ("y" != $v["generic"] && "b" != $v["generic"]) {
        $product_str = productStrJoin(intval($v["product_id"]), intval($v["sub_id"]), intval($invoice["site_id"]));
        $product     = productFromDatabase($product_str);

        $dimensions = [];
        if (function_exists('productDimensionFromDatabase')) {
            $dimensions = productDimensionFromDatabase($product_str);
            if (count($dimensions) && !is_array(reset($dimensions))) {
                $dimensions = [$dimensions];
            }
        }
        $packing = [];
        if (function_exists('productPackingFromDatabase')) {
            $packing = productPackingFromDatabase($product_str);
            if (count($packing) && !is_array(reset($packing))) {
                $packing = [$packing];
            }
        }
       $bulk_sizes = [];
        foreach ($dimensions as $row) {
            $cubic = bcmul($row['width'] ?? '0.0', bcmul($row['depth'] ?? '0.0', $row['height'] ?? '0.0', 3), 3);
            $bulk_sizes[1] = [
                [
                    'qty'    => 1,
                    'weight' => $row['weight'] ?? '0.000',
                    'cubic'  => (bccomp($cubic, '0.000', 3) > 0) ? $cubic : bcmul($row['cubic'] ?? '0.000', (0 != ($row['cubic_divisor'] ?? 0)) ? $row['cubic_divisor'] : $cubic_divisor, 3),
                    'width'  => $row['width'] ?? '0.0',
                    'depth'  => $row['depth'] ?? '0.0',
                    'height' => $row['height'] ?? '0.0',
                ],
            ];
        }

       /* foreach ($dimensions as $row) {
            $bulk_sizes[intval($row["carton_units"])] = array(array(
                "qty"    => $row["carton_units"],
                "weight" => $row["carton_weight"],
                "cubic"  => bcmul($row["carton_cubic"], (0 != $row["carton_cubic_divisor"]) ? $row["carton_cubic_divisor"] : $cubic_divisor, 3),
                "width"  => $row["carton_width"],
                "depth"  => $row["carton_depth"],
                "height" => $row["carton_height"],
            ));
        }*/

        if (function_exists('productBulkWeightsFromDatabase')) {
            foreach (productBulkWeightsFromDatabase($product_str) as $row) {
                $cubic                   = bcmul($row['width'], bcmul($row['depth'], $row['height'], 3), 3);
                $bulk_sizes[$row['qty']] = [
                    [
                        'qty'    => $row['qty'],
                        'weight' => $row['weight'],
                        'cubic'  => (bccomp($cubic, '0.000', 3) > 0) ? $cubic : bcmul($row['cubic'], (0 != $row['cubic_divisor']) ? $row['cubic_divisor'] : $cubic_divisor, 3),
                        'width'  => $row['width'],
                        'depth'  => $row['depth'],
                        'height' => $row['height'],
                    ],
                ];
            }
        }

        if (count($packing)) {
            $bulk_sizes[1] = [];
            foreach ($packing as $row) {
                $bulk_sizes[1][] = [
                    'qty'    => 1,
                    'weight' => $row['weight'],
                    'cubic'  => bcmul($row['width'], bcmul($row['depth'], $row['height'], 3), 3),
                    'width'  => $row['width'],
                    'depth'  => $row['depth'],
                    'height' => $row['height'],
                ];
            }
        }
        unset($bulk_sizes[0]);
        krsort($bulk_sizes);

         /**
         * Estimate packages with no dimensions
         */
        $changes = true;
        while ($changes) {
            $changes = false;
            foreach ($bulk_sizes as $k1 => $v1) {
                if (count($v1) == 1 && (bccomp($v1[0]['width'], '0.00', 2) == 0 || bccomp($v1[0]['depth'], '0.00', 2) == 0 || bccomp($v1[0]['height'], '0.00', 2) == 0)) {
                    foreach ($bulk_sizes as $k2 => $v2) {
                        if ($k2 < $k1 && count($v2) == 1) {
                            if (bccomp($v2[0]['width'], '0.00', 2) != 0 && bccomp($v2[0]['depth'], '0.00', 2) != 0 && bccomp($v2[0]['height'], '0.00', 2) != 0) {
                                $multiplier = (float)$k1 / (float)$k2;
                                $root_x     = ceil(pow($multiplier, 1.0 / 3.0));
                                $root_y     = ceil(sqrt($multiplier / $root_x));
                                $root_z     = ceil($multiplier / ($root_x * $root_y));
                                $dim        = [
                                    $v2[0]['width'],
                                    $v2[0]['depth'],
                                    $v2[0]['height'],
                                ];
                                bcscale(2);
                                usort($dim, 'bccomp');
                                $bulk_sizes[$k1][0]['width']  = bcmul($dim[0], (string)$root_x, 2);
                                $bulk_sizes[$k1][0]['depth']  = bcmul($dim[1], (string)$root_y, 2);
                                $bulk_sizes[$k1][0]['height'] = bcmul($dim[2], (string)$root_z, 2);
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
            if (bccomp($v1['weight'], '0.000', 3) == 0) {
                if (bccomp($product['weight_inc'], '0.000', 3) == 0) {
                    $packages[$k1]['weight'] = bcmul($product['weight'], $v1['qty'], 3);
                } else {
                    $packages[$k1]['weight'] = bcadd($product['weight'], bcmul($product['weight_inc'], strval($v1['qty'] - 1), 3), 3);
                }
            }
            if (bccomp($v1['cubic'], '0.000', 3) == 0) {
                $packages[$k1]['cubic'] = bcmul($v1['width'], bcmul($v1['depth'], $v1['height'], 3), 3);
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
if (bccomp($invoice['ship_weight'], '0.0000', 4) != 0/* && !array_key_exists('weight', $issues)*/) {
    if (class_exists('Freight_Service_Handler')) {
        $invoice['supply_country'] = !empty($invoice['supply_country']) ?$invoice['supply_country']: 'CN';
        $FSH = Freight_Service_Handler::getInstance();
        $courier_quotes = $FSH->hintInternational($invoice['supply_country'], $invoice['ship_country'], '0', '0', '0', $invoice['ship_weight'], true);
        $courier_quotes = $FSH->calculateServices($courier_quotes, $invoice['products_offer'], $invoice['invoice_currency']);
        foreach ($courier_quotes as $k => $v) {
            $container_list = [true];
            if (function_exists('packingFromCache')) {
                $max_dim = [$v['max_width'], $v['max_depth'], $v['max_height']];
                usort($max_dim, function($a, $b) { return bccomp($a, $b, 1); });
                if (bccomp($max_dim[1], '0.000', 1) > 0) {
                    $packing                     = [];
                    $packing['packing_products'] = $invoice_products;
                    $packing['satchel']          = [
                        'width'  => $v['max_width'],
                        'depth'  => $v['max_depth'],
                        'height' => $v['max_height'],
                        'weight' => $v['max_weight'],
                        'name'   => $v['service'],
                    ];
                    $packing['packing_action'] = 'checkout_satchel';
                    $packing['spareparts']     = true;
                    if (isset($error_list)) {
                        $safety         = $error_list;
                        $container_list = packingFromCache($packing);
                        $error_list     = $safety;
                    } else {
                        $container_list = packingFromCache($packing);
                    }
                    if (count($container_list) == 1) {
                        $courier_quotes[$k]['packing_string'] = 'b64_gz_ser:' . base64_encode(gzcompress(serialize($container_list)));
                    }
                }
            }
        }
        // usort($courier_quotes, function($a, $b) {
        //     return strcmp($b['insurance'], $a['insurance']) ?: bccomp($a['offer_price'], $b['offer_price']) ?: bccomp($a['cost_price'], $b['cost_price']);
        // });

        usort($courier_quotes, function($a, $b) {
            return bccomp($a['offer_price'], $b['offer_price']) ?: bccomp($a['cost_price'], $b['cost_price']);
        });
        foreach ($courier_quotes as $v) {
            $invoice_courier_quotes[] = [
                'quote_count'    => count($invoice_courier_quotes),
                'flag'           => count($invoice_courier_quotes) ? 'n' : 'y',
                'name'           => $v['service'],
                'del_time'       => $v['delivery_time'],
                'track'          => $v['tracking'],
                'insurance'      => $v['insurance'],
                'tail_gate'      => $v['tailgate'],
                'cost_price'     => $v['cost_price'],
                'sell_price'     => $v['offer_price'],
                'retail_price'   => $v['online_price'],
                'cost_priceau'   => convertCurrency($v['cost_price'], $v['currency'], 'AUD', 4),
                'sell_priceau'   => convertCurrency($v['offer_price'], $v['currency'], 'AUD', 4),
                'cost_priceus'   => convertCurrency($v['cost_price'], $v['currency'], 'USD', 4),
                'sell_priceus'   => convertCurrency($v['offer_price'], $v['currency'], 'USD', 4),
                'supplier'       => $v['forwarder_id'],
                'supplier_name'  => $v['forwarder'],

                'ship_number'    => $v['ship_number'],
                'packing_string' => '',
                "Locality"       => array_key_exists("Locality", $v) ? $v["Locality"] : "",
                'registered'     => $v['registered'],
                'receipted'      => $v['receipted'],
                'express'        => $v['express'],
                'service_id'     => $v['service_id'],
                'freight_notes'  => $v['freight_notes'],
            ];
        }
    } elseif (function_exists('freightSortArray') && function_exists('freightSearchCouriers')) {
        $found = [];
        foreach (freightSortArray(freightSearchCouriers($invoice['ship_country'], $invoice['ship_postcode'], $invoice['ship_state'], $invoice['ship_city'], $invoice['ship_weight'], $invoice['products_offer'], $invoice['invoice_currency'])) as $v) {
            $key = $v['ship_number'];

            $container_list = [true];
            if (function_exists('packingFromCache')) {
                list($width, $depth, $height) = [
                    $v['width'],
                    $v['depth'],
                    $v['height'],
                ];
                if ($width > 0 && $depth > 0) {
                    $packing                     = [];
                    $packing['packing_products'] = $invoice_products;
                    $packing['satchel']          = [
                        'width'  => $v['width'],
                        'depth'  => $v['depth'],
                        'height' => $v['height'],
                        'weight' => $v['weight'],
                        'name'   => $v['name'],
                    ];
                    $packing['packing_action'] = 'checkout_satchel';
                    $packing['spareparts']     = true;
                    if (isset($error_list)) {
                        $safety         = $error_list;
                        $container_list = packingFromCache($packing);
                        $error_list     = $safety;
                    } else {
                        $container_list = packingFromCache($packing);
                    }
                    if (count($container_list) == 1) {
                        if (false !== ($gzcompress = gzcompress(serialize($container_list)))) {
                            $v['packing_string'] = 'b64_gz_ser:' . base64_encode($gzcompress);
                        }
                    }
                }
            }

            if (!array_key_exists($key, $found) && count($container_list) == 1) {
                $invoice_courier_quotes[] = [
                    'ship_number'    => $v['ship_number'],
                    'service_id'     => $v['id'],
                    'express'        => $v['express'] ?? 'n',
                    'receipted'      => $v['receipted'] ?? 'n',
                    'registered'     => $v['registered'] ?? 'n',

                    'width'          => $v['width'],
                    'depth'          => $v['depth'],
                    'height'         => $v['height'],
                    'weight'         => $v['weight'],

                    'quote_count'    => $count,
                    'flag'           => (0 == $count) ? 'y' : 'n',
                    'name'           => $v['name'],
                    'del_time'       => $v['del_time'],
                    'track'          => $v['track'],
                    'insurance'      => $v['insurance'],
                    'tail_gate'      => $v['tail_gate'],
                    'cost_price'     => convertCurrency($v['cost_price'], $v['currency'], $invoice['invoice_currency']),
                    'sell_price'     => convertCurrency($v['sell_price'], $v['currency'], $invoice['invoice_currency']),
                    'retail_price'   => convertCurrency($v['retail_price'], $v['currency'], $invoice['invoice_currency']),
                    'cost_priceau'   => convertCurrency($v['cost_price'], $v['currency'], 'AUD'),
                    'sell_priceau'   => convertCurrency($v['sell_price'], $v['currency'], 'AUD'),
                    'cost_priceus'   => convertCurrency($v['cost_price'], $v['currency'], 'USD'),
                    'sell_priceus'   => convertCurrency($v['sell_price'], $v['currency'], 'USD'),
                    'supplier'       => $v['supplier'],
                    'supplier_name'  => $v['supplier_name'],
                    "Locality"       => array_key_exists("Locality", $v) ? $v["Locality"] : "",
                    'packing_string' => (array_key_exists('packing_string', $v)) ? $v['packing_string'] : '',

                    'notes'          => $v['notes'],
                ];
                $found[$key] = $key;
                $count++;
            }
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

include_once __DIR__ . '/header.inc.php';
?>
<style type="text/css">
/*<![CDATA[*/
.ac_results{padding:0;border:1px solid #000;background-color:#fff;overflow:hidden;z-index:99999}
.ac_results ul{width:100%;list-style-position:outside;list-style:none;padding:0;margin:0}
.ac_results li{margin:0;padding:2px 5px;cursor:default;display:block;font:menu;font-size:12px;line-height:16px;overflow:hidden}
.ac_loading{background:#fff url(/images/loadingAnimation.gif) right center no-repeat}
.ac_odd{background-color:#eee}
.ac_over{background-color:#0A246A;color:#fff}
.active_n td{background:#fcc}
.alt.active_n td{background:#fbb}
.highlight.active_n td{background:#fb7}
/*]]>*/
</style>

<script type="text/javascript" src="/jquery/autocomplete&skip_deps.js"></script>

<script type="text/javascript">
//<![CDATA[
var all_data    = <?=json_encode($invoice_courier_quotes)?>;
var active_data = <?=json_encode($active)?>;
<?php
if (count($invoice_courier_quotes)) {
?>
if (window.opener != null && window.opener.setCourierData != null) {
    window.opener.setCourierData(active_data);
} else if (parent.setCourierData != null) {
    parent.setCourierData(active_data);
}
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
			else if(parent.setShipToCourierQuote != null) 
                parent.setShipToCourierQuote(all_data[k]);
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

<form action="/webadmin/invoice/courier_search_popup.php" method="post">
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
			<td style="width:33%;"><input type="text" name="shipto_city[0]" value="<?=htmlspecialchars($invoice['ship_city'])?>" style="width:98%;" /></td>
			<td style="color:#f00;width:33%;">
<!-- <?php
    if (!array_key_exists("Locality", $varies) || $invoice['ship_country'] != 'AU') {
?>
				<span style="color:#f00;">WARNING: No match found</span>
<?php
    } elseif (!array_key_exists("Locality", $varies) || strtoupper(reset($varies["Locality"])) != strtoupper($invoice['ship_city'])) {
?>
				<span style="color:#f00;">WARNING: Using closest match ''</span>
<?php
    } else {
?>
				<span style="color:#0f0;">Match found</span>
<?php
    }
?> -->
			</td>
		</tr>
		<tr class="ship_country_AU">
			<td class="left_title">State</td>
			<td><input type="text" name="shipto_state[0]" value="<?=htmlspecialchars($invoice['ship_state'])?>" style="width:98%;" /></td>
			<td style="color:#f00;width:33%;">
<!-- <?php
    if (!array_key_exists("State", $varies)) {
?>
				<span style="color:#f00;">WARNING: No match found</span>
<?php
    } elseif (strtoupper(reset($varies["State"])) != strtoupper($invoice['ship_state'])) {
?>
				<span style="color:#f00;">WARNING: Using closest match ''</span>
<?php
    } else {
?>
				<span style="color:#0f0;">Match found</span>
<?php
    }
?> -->
			</td>
		</tr>
		<tr class="ship_country_AU">
			<td class="left_title">Postcode</td>
			<td><input type="text" name="shipto_postcode[0]" value="<?=htmlspecialchars($invoice['ship_postcode'])?>" style="width:98%;" /></td>
			<td style="color:#f00;width:33%;">
<!-- <?php
    if (!array_key_exists("Pcode", $varies)) {
?>
				<span style="color:#f00;">WARNING: No match found</span>
<?php
    } elseif (intval(reset($varies["Pcode"])) != intval($invoice['ship_postcode'])) {
?>
				<span style="color:#f00;">WARNING: Using closest match '<?=sprintf("%04d", reset($varies["Pcode"]))?>'</span>
<?php
    } else {
?>
				<span style="color:#0f0;">Match found</span>
<?php
    }
?> -->
			</td>
		</tr>
<?php
}
?>
		<tr>
			<td class="left_title" style="width:33%;">Country</td>
			<td style="width:33%;"><select name="shipto_country[0]">
				<option value=""></option>
				<?=formatHTMLOptions(getCountryListNested(), $invoice['ship_country'])?>
			</select></td>
			<td style="width:33%;"></td>
		</tr>
		<tr>
			<td class="left_title">Shipment Weight (kg)</td>
			<td><input type="text" name="ship_weight" value="<?=htmlspecialchars($invoice['ship_weight'])?>" style="width:98%;" /></td>
			<td></td>
		</tr>
		<tr>
			<td class="left_title">Insurance Value (<?=htmlspecialchars($invoice["invoice_currency"])?>)</td>
			<td><input type="text" name="products_offer" value="<?=htmlspecialchars($invoice['products_offer'])?>" style="width:98%;" /></td>
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
		<li><?=htmlspecialchars(((0 != $v["sub_id"]) ? sprintf("%05d-%04d", $v["product_id"], $v["sub_id"]) : sprintf("%05d", $v["product_id"])) . " - " . $v["product_name"])?></li>
<?php
    }
?>
	</ul>
</div></div>
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

<?=formatHTMLTitle("Available Freight Quotes", '<a href="#" onclick="' . htmlspecialchars("$(\".active_n\").toggle();return false") . '">Show/Hide Inactive Services</a>')?>

<table class="wide spacetable table_grid">
	<tr>
		<th>ID</th>
		<th>Service</th>
		<th>Transit Time</th>
		<th>Tracking</th>
		<th>Insurance</th>
		<th>Tail Gate</th>
		<th>Qty</th>
		<th>Cost<br />Price<br />(<?=htmlspecialchars($invoice["invoice_currency"])?>)</th>
		<th>Sell<br />Price<br />(<?=htmlspecialchars($invoice["invoice_currency"])?>)</th>
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
		<td><input type="radio" name="checkout[ship_number]"<?=("y" == $v["flag"]) ? ' checked="checked"' : ''?> value="<?=htmlspecialchars($v["ship_number"])?>" id="<?=$key?>" /> <label for="<?=$key?>"><?=htmlspecialchars($v["ship_number"])?></label></td>
		<td style="white-space:nowrap;"><label for="<?=$key?>"><?=htmlspecialchars($v["name"])?></label></td>
		<td style="text-align:center;"><label for="<?=$key?>"><?=htmlspecialchars($v["del_time"])?></label></td>
		<td style="text-align:center;"><label for="<?=$key?>"><?=("y" == $v["track"]) ? "Yes" : "No"?></label></td>
		<td style="text-align:center;"><label for="<?=$key?>"><?=("y" == $v["insurance"]) ? "Yes" : "No"?></label></td>
		<td style="text-align:center;"><label for="<?=$key?>"><?=("y" == $v["tail_gate"]) ? "Yes" : "No"?></label></td>
		<td style="text-align:center;"><label for="<?=$key?>"><?=$v['qty']?></label></td>
		<td style="text-align:right;"><label for="<?=$key?>"><b style="white-space:nowrap;"><?=htmlspecialchars(getCurrencySymbol($invoice["invoice_currency"], "UTF-8") . $v["cost_price"])?></b></label></td>
		<td style="text-align:right;"><label for="<?=$key?>"><b style="white-space:nowrap;"><?=htmlspecialchars(getCurrencySymbol($invoice["invoice_currency"], "UTF-8") . $v["sell_price"])?></b></label></td>
<?php
        if (in_array($invoice["invoice_currency"], array("AUD", "USD")) || 20 != $invoice["site_id"]) {
            if ("AUD" != $invoice["invoice_currency"]) {
?>
		<td style="text-align:right;"><label for="<?=$key?>"><b style="white-space:nowrap;"><?=htmlspecialchars("$" . $v["cost_priceau"])?></b></label></td>
		<td style="text-align:right;"><label for="<?=$key?>"><b style="white-space:nowrap;"><?=htmlspecialchars("$" . $v["sell_priceau"])?></b></label></td>
<?php
            }
        } elseif ("USD" != $invoice["invoice_currency"]) {
?>
		<td style="text-align:right;"><label for="<?=$key?>"><b style="white-space:nowrap;"><?=htmlspecialchars("$" . $v["cost_priceus"])?></b></label></td>
		<td style="text-align:right;"><label for="<?=$key?>"><b style="white-space:nowrap;"><?=htmlspecialchars("$" . $v["sell_priceus"])?></b></label></td>
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

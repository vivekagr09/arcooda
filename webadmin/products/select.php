<?php
include_once __DIR__ . "/common.inc.php";

if (function_exists('productGetOptionList')) {
    $warranty_list     = productGetOptionList('warranty');
}

$js_data = array();
if (is_array($posted['product_list']) && $posted['multiple'] == 'y')
{
	$product_list = array_unique($posted['product_list']);
	$js_data['product_list'] = array();
	foreach($product_list as $product_str){
        $product = productFromDatabase($product_str);
        if('y' == $product['special_flag'] AND ($product['special_expiry'] >= date('Y-m-d 00:00:00') OR $product['special_expiry'] == '0000-00-00 00:00:00')){
              $product['product_offer'] = number_format((float)convertCurrency($product["special_priceus"], $product['secondary_currency'], 'USD', false, $product['precision']), 2, '.', ''); 
        }else if ('consignment' == $product['stock_type']) {
            $product['product_costus'] =convertCurrency($product['consignment_cost_price'], $product['consignment_cost_currency'], 'USD', false, $product['precision']);
            $product['product_offer'] =$product['consignment_sales_price'];
        }else{
            $product['product_costus'] = convertCurrency($product["cost_price"], $product["cost_currency"], "USD");
            $product['product_offer'] = $product["effective_price"];
        }
        $js_data["product_list"][] = $product;

    } 
}
else{ 
    $js_data = productFromDatabase(intval($posted["id"]));
        if('y' == $js_data['special_flag'] AND ($js_data['special_expiry'] >= date('Y-m-d 00:00:00') OR $js_data['special_expiry'] == '0000-00-00 00:00:00')){
              $pSalesPrice  = number_format((float)convertCurrency( $js_data["special_priceus"], $js_data['secondary_currency'], 'USD', false,  $js_data['precision']), 2, '.', ''); 
               $js_data['product_offer'] =$pSalesPrice ;
                $js_data['product_offerus'] =$pSalesPrice;
                $js_data['product_online'] =$pSalesPrice;
                $js_data['product_onlineau'] =$pSalesPrice;
                $js_data['product_onlineus'] =$pSalesPrice;
                $js_data['product_offerau'] =convertCurrency($pSalesPrice, $js_data['consignment_cost_currency'], 'AUD', false, $js_data['precision']);
        }else if('consignment' == $js_data['stock_type']) {
            $js_data['product_costus'] =convertCurrency($js_data['consignment_cost_price'], $js_data['consignment_cost_currency'], 'USD', false, $js_data['precision']);
            $js_data['product_onlineau'] =convertCurrency($js_data['consignment_cost_price'], $js_data['consignment_cost_currency'], 'AUD', false, $js_data['precision']);

            $js_data['product_offer'] =$js_data['consignment_sales_price'];
            $js_data['product_offerus'] =$js_data['consignment_sales_price'];
            $js_data['product_online'] =$js_data['consignment_sales_price'];
            $js_data['product_onlineau'] =$js_data['consignment_sales_price'];
            $js_data['product_onlineus'] =$js_data['consignment_sales_price'];
            $js_data['product_offerau'] =convertCurrency($js_data['consignment_sales_price'], $js_data['consignment_cost_currency'], 'AUD', false, $js_data['precision']);
        }else{
            $js_data['product_costus'] = convertCurrency($js_data["cost_price"], $js_data["cost_currency"], "USD");
            $js_data['product_onlineau'] =convertCurrency($js_data['consignment_cost_price'], $js_data['consignment_cost_currency'], 'AUD', false, $js_data['precision']);
            $js_data['product_offer'] = $js_data["effective_price"];
            $js_data['product_offerus'] =$js_data['consignment_sales_price'];
        }
}
$js_data['return_message'] = $posted['return_message'];
 {
    $dimensions = productDimensionFromDatabase(intval($posted["id"]));
    if (!is_array(reset($dimensions))) {
        $dimensions = array($dimensions);
    }

    $packing = array();
    if (function_exists("productPackingsFromDatabase")) {
        $packing = productPackingsFromDatabase(intval($posted["id"]),null);
    }
    /**
     * Collect the packaging sizes
     */
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
        foreach (productBulkWeightsFromDatabase(intval($posted["id"])) as $row) {
            $bulk_sizes[$row["bulk_qty"]] = array(array(
                "qty"    => $row["bulk_qty"],
                "weight" => $row["weight"],
                "cubic"  => bcmul($row["cubic"], (0 != $row["cubic_divisor"]) ? $row["cubic_divisor"] : $cubic_divisor, 3),
                "width"  => $row["width"],
                "depth"  => $row["depth"],
                "height" => $row["height"],
            ));
        }
    }

    if (count($packing)) {
        /**
         * Hack for invalid product updates
         */
        foreach ($packing as $k1 => $v1) {
            if ("y" /*$v1["pallet_id"] != 0*/ == $v1["pallet_enable"]) {
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

    /**
     * Estimate packages with no dimensions
     */
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
    $js_data["bulk_sizes"] = json_encode($bulk_sizes);
 }
if (array_key_exists($js_data['warranty'], $warranty_list) && '' != $warranty_list[$js_data['warranty']]) {
	$js_data['warranty_note'] = 'Warranty: ' . $warranty_list[$js_data['warranty']];
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title></title>
	<script type="text/javascript">
//<![CDATA[
if (window.opener != null && window.opener.setProductData != null)
{
	window.opener.setProductData(<?php jecho($js_data) ?>);
	window.close();
}
else alert('Unable to send product back to form');
//]]>
	</script>
</head>
<body></body>
</html>
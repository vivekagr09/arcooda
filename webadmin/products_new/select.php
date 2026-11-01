<?php
include_once __DIR__ . '/common.inc.php';
$id        = intval($_REQUEST['id'] ?? 0);
$color_id  = intval($_REQUEST['color_id'] ?? 0);
$item_list = $_REQUEST['item_list'] ?? [];
if (!$id && !count($item_list)) {
    die('Invalid Product ID passed...');
}

$big_list = $all_products = array();
if (count($item_list)) {
    $big_list = $item_list;
} elseif ($id) {
    $big_list[] = $id.'-'.$color_id;
}

foreach ($big_list as $prod_id) {
    list($prod_id, $color_id) = explode('-', $prod_id);
    $item = productFromDatabase($prod_id);
    if (0 == $item["id"]) {
        $item = productFromDatabase($prod_id);
    }

    foreach (array('manufacturer') as $k) {
        $option_list       = productGetOptionList($k);
        $item[$k . '_str'] = $option_list[$item[$k]];
    }
    $dimension = reset(productDimensionFromDatabase($prod_id));
    // for invoice purposes need to modify a few things...
    if($color_id > 0) {
        $color_data=productGenericFromDatabase($color_id,'product_colour') ;
       $item['product_name'] = $item['product_name'].'-'.$color_data['colour'];
    }
    $item['product_offer']      = $item['item_online']      = convertCurrency($item['secondary_price'], $item['secondary_currency'], 'USD', false);
    $item['product_offerau']    = convertCurrency($item['secondary_price'], $item['secondary_currency'], 'AUD', false, 4);
    $item['product_offerus']    = convertCurrency($item['secondary_price'], $item['secondary_currency'], 'USD', false, 4);
    $item['product_cost']       = convertCurrency($item['cost_price'], $item['cost_currency'], 'USD', false);
    $item['product_costau']     = convertCurrency($item['cost_price'], $item['cost_currency'], 'AUD', false, 4);
    $item['product_costus']     = convertCurrency($item['cost_price'], $item['cost_currency'], 'USD', false, 4);
    $item['product_weight']     = $dimension['online_weight'];
    $item['product_weight_inc'] = $dimension['inc_weight'];
    $item['bulk_weights']       = productBulkWeightsFromDatabase($prod_id);
    $item['return_message']     = $_REQUEST['return_message'];
    $item['cost_currency']      = $item['cost_currency'];
    $item['cost_price']         = $item['cost_price'];
    $item['color_id']         = ($color_id)?$color_id:0;
    $item['colorhtml']        =getcolorhtmlforbuilderoption($item['colour']);
    $results = productSuppliersFromDatabase($prod_id);

    if (count($results) == 1 && 0 != $results[0]['supplier_id']) {
        $supplier              = reset($results);
        $item['preferred_supplier']     = $supplier['supplier_id'];
        $item['supplier_name'] = $supplier['supplier_name'];
        if ('' != $supplier['currency'] && 0 != $results[0]['offer_price']) {
            $item['currency']   = $item['cost_currency']   = $supplier['currency'];
            $item['cost_price'] = bcdiv($supplier['offer_price'], $supplier['qty'], 4);
        }
        if ('' != $supplier['product_name']) {
            $item['supplier_product_name'] = $supplier['product_name'];
        }
    }

    $item['reserved'] = 0;
    foreach (productReservedFromDatabase($prod_id) as $row) {
        $item['reserved'] += $row['qty'];
    }

    {
        $dimensions = productDimensionFromDatabase($prod_id);
        if (!is_array(reset($dimensions))) {
            $dimensions = array($dimensions);
        }

        $packing = array();
        if (function_exists("productPackingFromDatabase")) {
            $packing = productPackingFromDatabase($prod_id);
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
            foreach (productBulkWeightsFromDatabase($prod_id) as $row) {
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

        $item["bulk_sizes"] = json_encode($bulk_sizes);
    }

    $all_products[] = $item;
}
function getcolorhtmlforbuilderoption($color){
    if(isset($_GET['searchid'])){
        $html_option='';
        if(count(explode(',', $color)) && $color){
        $html .='<table class="table_fields" style="width: 100%; margin-top: 10px;">
        <tr>
            <td>
                <ul style="list-style:none;">';
                
                foreach (explode(',', $color) as $key => $colr) {
                    $color_detail=getProductColourById($colr);
                     $html_option .='<option value="'.$color_detail['id'].'" '.$selected_val.' >'.$color_detail["colour"].'</option>';
                    $html =  $html. '<li style="text-align: center; display: inline-block; vertical-align: top; margin: 10px 5px;">
                        <div class="cat action" >
                        <input type="checkbox" value="'.$color_detail['id'].'" name="pb_options[options][0]['.$_GET['searchid'].'][color][]" style="width: 24px; height: 24px;">
                        <p style="padding:12px;background:'.$color_detail["color_val"].'">
                            </p>
                            </div>
                    </li>';
            }
            $html .='</ul> </td> </tr> </table>';
            $html .='Default Colour :  <select name="pb_options[options][0]['.$_GET['searchid'].'][default_color]" style="width: 80%; resize: vertical;height: 26px; margin-top: 10px;">';
            $html .= $html_option;
            $html .='</select>';
        return $html;
        }
        return '';
    }
    return '';
}
/*if (count($all_products) == 1) {
    $item = reset($all_products);
} else {
    $item = array('return_message' => $_REQUEST['return_message'], 'item_list' => $all_products);
}*/
$item = array('return_message' => $_REQUEST['return_message'],'row_id'=>$_REQUEST['row_id'], 'item_list' => $all_products);

if (!headers_sent()) {
    header('Content-Type: text/html; charset=utf-8');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <script type="text/javascript">
        var data = <?=json_encode($item)?>;
        console.log(data.return_message);
        if(data.return_message=='add'){
            if (window.opener != null && window.opener.setProductData != null)
            window.opener.setProductData(data['item_list'][0]);
            else alert("Unable to send tag back to form");
            window.close();
        }else if(data.return_message=='partof'){
            if (window.opener != null && window.opener.productsPartofData != null)
            window.opener.productsPartofData(data['item_list'][0]);
            else alert("Unable to send tag back to form");
            window.close();
        }else if(data.return_message=='softwareof'){
            if (window.opener != null && window.opener.productsSoftwareData != null)
            window.opener.productsSoftwareData(data['item_list'][0]);
            else alert("Unable to send tag back to form");
            window.close();
        }else if(data.return_message=='upgradelinking'){
            if (window.opener != null && window.opener.dispalyupgradelinkingproductddata != null) 
            window.opener.dispalyupgradelinkingproductddata(data);
            else alert("Unable to send tag back to form");
            window.close();
        }else if(data.return_message=='monitor_part'){
            if (window.opener != null && window.opener.dispalymonitor_partproductddata != null) 
            window.opener.dispalymonitor_partproductddata(data);
            else alert("Unable to send tag back to form");
            window.close();
        }else if(data.return_message=='link_product'){
            if (window.opener != null && window.opener.dispallinkproductddata != null) 
            window.opener.dispallinkproductddata(data);
            else alert("Unable to send tag back to form");
            window.close();
        }else if(data.return_message=='recommended_products_data'){
            if (window.opener != null && window.opener.recommended_products_data != null) 
            window.opener.recommended_products_data(data);
            else alert("Unable to send tag back to form");
            window.close();
        }else if(data.return_message=='rule_product'){
            
            if (window.opener != null && window.opener.dispalyonlyavailableforowner != null) 
            window.opener.dispalyonlyavailableforowner(data);
            else alert("Unable to send tag back to form");
            window.close();
        }else if(data.return_message=='addstock'){
            if (window.opener != null && window.opener.setProductData != null) 
            window.opener.setProductData(data);
            else alert("Unable to send tag back to form");
            window.close();
        }else if(data.return_message=='blah'){
            if (window.opener != null && window.opener.set_products_data != null) 
            window.opener.set_products_data(data);
            else alert("Unable to send tag back to form");
            window.close();
        }else if(data.return_message=='add_free_gift'){
            if (window.opener != null && window.opener.setfreeGiftProductsAddData != null) {
            window.opener.setfreeGiftProductsAddData(data['item_list']);
            window.close(); 
            }else{ alert("Unable to send tag back to form");
            window.close();}
        }else if(data.return_message == 'add_gift'){
            if (window.opener != null && window.opener.setProductsAddData != null) {
            window.opener.setProductsAddData(data['item_list']);
            window.close(); 
            }else{
             alert("Unable to send tag back to form");
            window.close();
            }
        }else if(data.return_message == 'recommended_product'){
            if (window.opener != null && window.opener.setProductsRecommended != null) {
            window.opener.setProductsRecommended(data['item_list']);
            window.close(); 
            }else{
             alert("Unable to send tag back to form");
            window.close();
            }
        }else if(data.return_message=='add_on_products'){
            if (window.opener != null && window.opener.add_on_products_data != null) {
            window.opener.add_on_products_data(data['item_list']);
            window.close(); 
            }else{ alert("Unable to send tag back to form");
            window.close();}
        }else if(data.return_message=='add_on_products_monitor'){
            if (window.opener != null && window.opener.add_on_products_data_monitor != null) {
            window.opener.add_on_products_data_monitor(data['item_list']);
            window.close(); 
            }else{ alert("Unable to send tag back to form");
            window.close();}
        }else if(data.return_message=='distributorcategory'){
            data['item_list'][0]['requestionsection']="<?= $_REQUEST['section'][0] ?>"
            if (window.opener != null && window.opener.add_distributordata_data != null) {
            window.opener.add_distributordata_data(data['item_list'][0]);
            window.close(); 
            }else{ alert("Unable to send tag back to form");
            window.close();}
        }else{
            if (window.opener != null && window.opener.linked_products_data != null)
            window.opener.linked_products_data(data);
            else alert("Unable to send tag back to form");
            window.close();
        }
        
    </script>
</head>
<body></body>
</html>

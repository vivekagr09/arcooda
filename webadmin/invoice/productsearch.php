<?php
include_once __DIR__ . '/common.inc.php';

$myurl = "productsearch.php";

switch ($_POST['action']) {
    case 'select':
        showSelectProduct();
        break;
    default:
        showSearchProduct();
        break;
}
return;

function show_links($links)
{
    $result = '';
    if (!count($links)) {
        return;
    }

    $result .= '<b>Showing : ' . $links['start_from'] . ' - ' . $links['ends_at'] . ' of ' . $links['max'] . '</b>';
    if ($links['url_prev_page']) {
        $result .= ' | <a href="' . $links['url_prev_page'] . '">&lt;&lt;</a>';
    }

    foreach ($links['links'] as $link) {
        if ($link['url']) {
            $result .= ' | <a href="' . $link['url'] . '">' . $link['start'] . ' - ' . $link['end'] . '</a>';
        } else {
            $result .= ' | ' . $link['start'] . ' - ' . $link['end'];
        }
    }

    if ($links['url_next_page']) {
        $result .= ' | <a href="' . $links['url_next_page'] . '">&gt;&gt;</a> ';
    }

    return $result;
}

function searchProductSuppliers($product_id, $detailed = false)
{
    global $DB;

    $result = array();
    $sql    = " SELECT";
    if ($detailed) {
        $sql .= "	sp.*,	s.*,";
    }

    $sql .= "	sp.supplier_id," .
        "	p.cost_price as buy_price," .
        "	p.cost_currency as currency," .
        "	s.supplier as supplier_name," .
        "	'' as supplier_name_ch," .
        "	s.country as location," .
        "	s.notes" .
        " FROM supplier_product sp" .
        " INNER JOIN suppliers s ON s.id=sp.supplier_id" .
        " INNER JOIN products p ON p.id=sp.product_id" .
        " WHERE sp.product_id=" . $product_id;
    $q = $DB->query($sql);
    while ($row = $DB->fetch_array($q, MYSQLI_ASSOC)) {
        $result[] = $row;
    }

    return $result;
}

function showSearchProduct()
{
    global $DB, $myurl;
    $itemsperpage   = 50;
    $posted         = array_merge($_GET, $_POST);
    $page           = $posted['page'];
    $return_message = $posted['return_message'];
    $prod_type      = $posted['type'];
    $currency       = $posted['currency'];
    $search_id      = $posted['search_id'];
    $search_for     = $posted['for'];
    $sort_by        = $posted['sort_by'];
    $sort_ord       = $posted['sort_ord'];
    if ('' == $sort_by) {
        $sort_by = 'p.product_name';
    }

    $base_types = array();
    $sql        = "SELECT pt.id, pt.name FROM product_types pt WHERE pt.parent=0";
    $query      = $DB->query($sql);
    while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
        $base_types[$row['id']] = $row['name'];
    }

    $sql   = array();
    $sql[] = "SELECT SQL_CALC_FOUND_ROWS";
    $sql[] = "	p.product_name," .
        "	p.*," .
        "	p.id as product_id," .
        "	IF(p.price_flux='y',(p.markup+100.0)*p.cost_price/100.0,p.sales_price) AS offer_price," .
        "	IF(p.price_flux='y',p.cost_currency,p.sales_currency) AS offer_currency," .
        "	p.cost_price," .
        "	p.cost_currency," .
        "	pt.name as product_subtype," .
        "	pt.parent as product_roottype";
    $sql[] = "FROM products p";
    $sql[] = "INNER JOIN product_types pt ON p.type=pt.id";

    $where = array();
    if ('' != $search_for) {
        $where[] = "p.product_name LIKE " . formatSQLString('%' . $search_for . '%');
    }

//    if($search_for!='')    $where[]=    "(p.id LIKE '%".implode("%' OR p.id LIKE '%",explode(' ',$search_for))."%' OR p.product_name LIKE '%".implode("%' OR p.product_name LIKE '%",explode(' ',$search_for))."%')";
    if ('' != $search_id) {
        $where[] = "p.id=" . intval($search_id);
    }

    if ($prod_type > 0) {
        $where[] = "pt.parent='{$prod_type}'";
    }

    if (count($where) > 0) {
        $sql[] = "WHERE " . implode(' AND ', $where);
    }

    if ('' != $sort_by) {
        $sql[] = "ORDER BY {$sort_by}";
    }
    //.($sort_ord!='')?' DESC':'';
    $sql[] = "LIMIT " . $page * $itemsperpage . ',' . $itemsperpage;

    $product_list = array();
    $query        = $DB->query(implode(' ', $sql));
    $shown_rows   = $DB->num_rows($query);
    $total_rows   = $DB->query_first("SELECT FOUND_ROWS()");
    $total_rows   = $total_rows[0];
    while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
        $row['product_online'] = convertCurrency($row['offer_price'], $row['offer_currency'], $currency);
        $row['product_cost']   = convertCurrency($row['cost_price'], $row['cost_currency'], $currency);
        $product_list[]        = $row;
    }

    $total_pages = floor($total_rows / $itemsperpage) + 1;

    $the_links               = array();
    $the_links['start_from'] = $page * $itemsperpage + 1;
    $the_links['ends_at']    = min($total_rows, ($page + 1) * $itemsperpage);
    $the_links['max']        = $total_rows;
    $the_links['links']      = array();
    $max_page                = ceil($total_rows / $itemsperpage) - 1;
    $min_page                = 0;
    for ($i = $min_page; $i <= $max_page; $i++) {
        $a_link          = array();
        $a_link['start'] = $i * $itemsperpage + 1;
        $a_link['end']   = min($a_link['start'] + $itemsperpage - 1, $total_rows);
        if ($page != $i) {
            $a_link['url'] = $myurl . '?page=' . $i . '&return_message=' . $return_message . '&type=' . $prod_type . '&currency=' . $currency . '&for=' . $search_for . '&sort_by=' . $sort_by . '&sort_ord=' . $sort_ord;
        }

        if (0 == $i || $i == $max_page || $i == $page || $i == $page - 1 || $i == $page + 1 || ($i > $page && $i < 4)) {
            $the_links['links'][$i] = $a_link;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="style.css" type="text/css" />
		<script type="text/javascript">
//<![CDATA[
function getElement(name)
{
    var elem = document.getElementById(name);
    if (elem) {
        return elem;
    }
    elem = document.getElementsByName(name);
	if (elem.length == 0) {
        return null;
    }
    return elem[0];
}
function setValue(name, value)
{
    var elem = getElement(name);
    if (elem == null) {
        return;
    }
    elem.value = value;
}
//]]>
		</script>
	</head>
	<body style="text-align:center">
		<form method="post" action="<?=$myurl?>?page=<?=$page?>&amp;return_message=<?=$return_message?>&amp;type=<?=$prod_type?>&amp;currency=<?=$currency?>&amp;for=<?=urlencode($search_for)?>&amp;search_id=<?=$search_id?>&amp;sort_by=<?=$sort_by?>&amp;sort_ord=<?=$sort_ord?>">
			<input type="hidden" name="product_id" value="0" />
			<input type="hidden" name="action" value="select" />

			<div class="invoice_content">
				<table class="wide spacetable">
					<tr>
						<th>ID</th>
						<th>Product Name</th>
						<th>Cost Price</th>
						<th>Sales Price</th>
						<th>&nbsp;</th>
					</tr>
<?php
    if (count($product_list) > 0) {
        foreach ($product_list as $item) {
?>
					<tr<?=($alt = !$alt) ? '' : ' class="alt"'?> style="font-size:12px;">
						<td style="text-align:right"><?=$item['product_id']?></td>
						<td style="width:100%;"><a target="_blank" href="<?=htmlspecialchars(sprintf(
							'/products/%s/%s/%s_%d.html?preview=true',
							urlencode(preg_replace('/\\s+/', '_', $base_types[$item['product_roottype']])),
							urlencode(preg_replace('/\\s+/', '_', $item['product_subtype'])),
							urlencode(preg_replace('/\\s+/', '_', $item['product_name'])),
							$item['product_id']
						))?>"><?=htmlspecialchars($item['product_name'])?></a></td>
						<td style="text-align:right"><?=$item['product_cost']?></td>
						<td style="text-align:right"><?=$item['product_online']?></td>
						<td style="text-align:center"><input type="button" value="Select" onclick="javascript:setValue('product_id','<?=$item['product_id']?>');form.submit();" /></td>
					</tr>
<?php
        }
    } else {
?>
	<tr><td colspan="5" style="text-align:center;font-size:12px;"><br />No products found on this page<br /><br /></td></tr>
<?php
    }
?>
	<tr><th colspan="5" style="text-align:center;"><?=show_links($the_links)?></th></tr>
</table>
<br />
<table style="width:0px;background-color:transparent;border:0px none;margin:auto">
<?php
    if ($prod_type > 0) {
?>
	<tr style="background-color:transparent;font-size:12px;"><td style="white-space:nowrap;"><b><a style="color:blue;" target="_blank" href="/webadmin/products/admin.php?table=products&parent=<?=$prod_type?>&action=add">Add New <?=str_replace('<', '&lt;', ucfirst($base_types[$prod_type]))?></a></b></td></tr>
<?php
    } else {
        foreach ($base_types as $key => $value) {
?>
	<tr style="background-color:transparent;font-size:12px;"><td style="white-space:nowrap;"><b><a style="color:blue;" target="_blank" href="/webadmin/products/admin.php?table=products&parent=<?=$key?>&action=add">Add New <?=str_replace('<', '&lt;', ucfirst($value))?></a></b></td></tr>
<?php
        }
    }
?>
</table>
</div>
</form>
</body>
</html>
<?php
}

function showSelectProduct()
{
    moduleLoad('product');

    global $DB;

    $posted     = array_merge($_GET, $_POST);
    $product_id = $posted['product_id'];
    $currency   = $posted['currency'];

    $product = $DB->query_first(
        " SELECT" .
        "	p.*," .
        "	pt.parent AS producttype_id" .
        " FROM products p" .
        " INNER JOIN product_types pt ON p.type=pt.id" .
        " WHERE p.id='{$product_id}'"
    );

    if ('y' == $product['price_flux']) {
        $product['sales_price']    = ($product['markup'] + 100.0) * $product['cost_price'] / 100.0;
        $product['sales_currency'] = $product['cost_currency'];
    }

    $return_data['producttype_id'] = $product['producttype_id'];

    $return_data['product_id']         = $product['id'];
    $return_data['product_name']       = $product['product_name'];
    $return_data['product_qty']        = 1;
    $return_data['product_cost']       = convertCurrency($product['cost_price'], $product['cost_currency'], $currency);
    $return_data['product_costus']     = convertCurrency($product['cost_price'], $product['cost_currency'], 'USD');
    $return_data['product_online']     = convertCurrency($product['sales_price'], $product['sales_currency'], $currency);
    $return_data['product_offerus']    = convertCurrency($product['sales_price'], $product['sales_currency'], 'USD');
    $return_data['product_offer']      = $return_data['product_online'];
    $return_data['product_weight']     = convertWeight($product['weight']);
    $return_data['product_weight_inc'] = $product['weight_inc'];
    $return_data['product_notes']      = trim($product['notes']);
    $return_data['min_order']          = $product['min_order'];
    if ($product['min_order'] > 1) {
        $return_data['product_notes'] = trim($return_data['product_notes'] . "\n- unit price in $" . convertCurrency($product['sales_price'], $product['sales_currency'], $currency) . strtolower($currency) . ' for MOQ in ' . $product['min_order'] . ' pieces');
    } else {
        $return_data['min_order'] = 1;
    }

    $return_data['supplier_productid']   = $product['id'];
    $return_data['supplier_qty']         = 1;
    $return_data['supplier_cost']        = $return_data['product_cost'];
    $return_data['supplier_productname'] = $return_data['product_name'];

    {
        $product_str = productStrJoin($product["product_id"], $product["sub_id"]);

        $dimensions = productDimensionFromDatabase($product_str);
        if (!is_array(reset($dimensions))) {
            $dimensions = array($dimensions);
        }

        $packing = array();
        if (function_exists("productPackingFromDatabase")) {
            $packing = productPackingFromDatabase($product_str);
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
            foreach (productBulkWeightsFromDatabase($product_str) as $row) {
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

        $return_data["bulk_sizes"] = json_encode($bulk_sizes);
    }

    $supplier_count = 0;

    $supplier_list  = searchProductSuppliers($product_id, true);
    $supplier_count = count($supplier_list);
    $supplier       = $supplier_list[0];

    if (1 == $supplier_count) {
        $return_data['supplier_id']             = $supplier['supplier_id'];
        $return_data['supplier_cost']           = convertCurrency($supplier['buy_price'], $supplier['currency'], $currency);
        $return_data['supplier_costus']         = convertCurrency($supplier['buy_price'], $supplier['currency'], 'USD');
        $return_data['supplier_name']           = $supplier['supplier_name'] . ' ' . $supplier['supplier_name_ch'];
        $return_data['supplier_country']        = $supplier['location'];
        $return_data['supplier_notes']          = $supplier['notes'];
        $return_data['supplier_cost_orig']      = $supplier['buy_price'];
        $return_data['supplier_cost_orig_curr'] = $supplier['currency'];
    }

    $return_data['supplier_count'] = $supplier_count;
    $return_data['return_message'] = $posted['return_message'];
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
	</head>
	<body>
		<script type="text/javascript">
//<![CDATA[
var data = <?=json_encode($return_data)?>;
if (window.opener.setProductData) {
	window.opener.setProductData(data);
} else {
	alert('Unable to send product back to form');
}
window.close();
//]]>
		</script>
	</body>
</html>
<?php
}

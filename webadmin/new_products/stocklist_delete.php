<?php
$section_code = "STOCK";
include_once __DIR__ . '/common.inc.php';

if (!function_exists('http_build_query')) {
    function http_build_query($formdata)
    {
        $arr = array();
        if (is_array($formdata) && count($formdata) > 0) {
            foreach ($formdata as $key => $val) {
                if ('' != $val) {
                    $arr[] = urlencode($key) . "=" . urlencode($val);
                }
            }

            return implode("&", $arr);
        }
        return '';
    }
}

if (!function_exists('stripslashes_deep')) {
    function stripslashes_deep($value)
    {
        $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
        return $value;
    }
}

//Strip the goddamn slashes before it touches anything in the system (Politically correct time to do it)
$posted        = array_merge($_GET, $_POST);
$public_posted = array_merge($_GET, $_POST);
foreach ($public_posted as $key => $value) {
    if (is_array($value)) {
        unset($public_posted[$key]);
    }
}

$stock_id = intval($posted['id']);

if (!$stock_item = $DB->query_first(sprintf(
    "SELECT `s`.*, `scs`.`name` AS `company_stock`, `spc`.`name` AS `product_catagory`, `pt`.`name` AS `product_type`, `psl`.`location` AS `storage`, `pcl`.`name` AS `country_location`, `pc`.`condition` AS `condition`, `sup`.`supplier` AS `supplier` FROM `stock` AS `s` LEFT JOIN `stock_company_stock` AS `scs` ON `scs`.`id` = `s`.`company_stock` LEFT JOIN `stock_product_catagories` AS `spc` ON `spc`.`id` = `s`.`product_catagory` LEFT JOIN `product_types` AS `pt` ON `pt`.`id` = `s`.`product_type` LEFT JOIN `product_storage_location` AS `psl` ON `psl`.`id` = `s`.`storage` LEFT JOIN `product_country_location` AS `pcl` ON `pcl`.`id` = `s`.`country_location` LEFT JOIN `product_conditions` AS `pc` ON `pc`.`id` = `s`.`condition` LEFT JOIN `suppliers` AS `sup` ON `sup`.`id` = `s`.`supplier` WHERE `s`.`id` = %d",
    $stock_id
), MYSQLI_ASSOC)) {
    header('location: stocklist_admin.php');
    die();
}

include_once __DIR__ . '/header.inc.php';

$row = $DB->query_first("SELECT COUNT(id) AS total FROM stock");

?>
<br />

<table width="100%" cellspacing="2" cellpadding="2"><tr bgcolor="#EEEEEE">
	<td width="20%" align="center"><b>Stocklist</b></td>
	<td width="80%" align="right" style="white-space:nowrap;">
		<a href="javascript:MM_openBrWindow('/webadmin/products/admin_help.html','help','scrollbars=yes,width=500,height=400')">HELP</a> |
		<a href="stocklist_admin.php?action=add">Add Stocklist</a> |
		<a href="stocklist_admin.php?action=list">List Stocklist (<?=intval($row['total'])?>)</a>
	</td>
</tr></table>

<h2>Do you want to delete this Item?</h2>
<?php
$list_data = array(
    'Purchase Order No'           => $stock_item['purchase_no'],
    'Stock COD'                   => $stock_item['stock_cod'],
    'COE ID'                      => $stock_item['coe_id'],
    'Company Stock'               => $stock_item['company_stock'],
    'Product Name'                => $stock_item['product_name'],
    'Product Category'            => $stock_item['product_catagory'],
    'Product Type'                => $stock_item['product_type'],
    'Storage Location'            => $stock_item['storage'],
    'Floor Location'              => $stock_item['floor_location'],
    'Country/Location'            => $stock_item['country_location'],
    'Date Received'               => $stock_item['date_received'],
    'Product Details'             => $stock_item['product_details'],
    'Condition'                   => $stock_item['condition'],
    'Actual Product Weight (kgs)' => $stock_item['weight'],
    'Total Weight (kgs)'          => $stock_item['total_weight'],
    'Supplier'                    => $stock_item['supplier'],
    'Unit Price'                  => $stock_item['unit_price'] . ' ' . $stock_item['cost_currency'],
    'Sample Qty'                  => $stock_item['sample_qty'],
    'Stock Qty'                   => $stock_item['stock_qty'],
    'Order Qty'                   => $stock_item['order_qty'],
    'Customer Name'               => $stock_item['customer_name'],
    'Invoice Reference'           => $stock_item['invoice_ref'],
    'Active'                      => ('y' == $stock_item['active']) ? 'Yes' : 'No',
);

$list_items = array();
foreach ($list_data as $key => $value) {
    $list_items[] = array('name' => $key, 'value' => $value);
}

viewby_summary('', $list_items);
?>
<br />

<table align="center" width="0">
	<tr>
	<form accept-charset="utf-8" action="stocklist_admin.php?action=delete&id=<?=$stock_id?>" method="post">
		<td><input type="submit" value="Yes" /></td>
	</form>
	<form accept-charset="utf-8" action="stocklist_admin.php" method="post">
		<td><input type="submit" value="No" /></td>
	</form>
	</tr>
</table>

<?php
function viewby_summary($view_url, $list_items)
{
?>
<table width="0" align="center">
<?php
$total = 0;
    $alt   = true;
    foreach ($list_items as $col) {
?>
	<tr<?=($alt = !$alt) ? '' : ' style="background-color:#EEEEEE;"'?>>
		<td style="font-weight:bold;white-space:nowrap;">
<?php
if ('' != $col['id']) {
            echo '<a href="' . $view_url . $col['id'] . '">';
        }

        if ('' != $col['name']) {
            echo $col['name'];
        } else {
            echo 'Other';
        }

        if ('' != $col['id']) {
            echo '</a>';
        }

?>
		</td>
		<td style="text-align:right;"><?=$col['value']?></td>
	</tr>
<?php
}
?>
</table>
<?php
}
?>
<?php
include_once __DIR__ . '/common.inc.php';

include_once '../currency.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/products/query_attachment.inc.php';

$posted = $_GET;

$title      = array();
$types_list = array();
if ('' != $posted['parent']) {
    $row     = $DB->query_first("SELECT name FROM product_types WHERE id=" . intval($posted['parent']), MYSQLI_ASSOC);
    $title[] = $row['name'];
    $query   = $DB->query("SELECT id,parent FROM product_types WHERE parent=" . intval($posted['parent']));
    while ($row = $DB->fetch_assoc($query)) {
        $types_list[] = $row['id'];
    }

}
if ('' != $posted['type']) {
    $row        = $DB->query_first("SELECT name FROM product_types WHERE id=" . intval($posted['type']), MYSQLI_ASSOC);
    $title[]    = $row['name'];
    $types_list = array(intval($posted['type']));
}

$products_list = array();
$query         = $DB->query(
    " SELECT p.id" .
    " FROM products p" .
    ((count($types_list)) ? (" WHERE p.type IN(" . implode(',', $types_list) . ")") : "") .
    " ORDER BY p.product_name"
);
while ($row = $DB->fetch_assoc($query)) {
    $products_list[] = $row['id'];
}

if (count($products_list)) {
    $gm_date   = 'D, d M Y H:i:s \G\M\T';
    $want_time = 0;

    $want_time = max($want_time, filemtime($_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF']));

    $row       = $DB->query_first("SELECT MAX(UNIX_TIMESTAMP(last_modified)) FROM products WHERE id IN(" . implode(',', $products_list) . ")", MYSQLI_NUM);
    $want_time = max($want_time, $row[0]);

    $if_modified_since = preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
    if (gmdate($gm_date, $want_time) == $if_modified_since) {
        header("HTTP/1.0 304 Not Modified");
        header('Expires:');
        header('Cache-Control:');
        die();
    }

    header('Date: ' . gmdate($gm_date, time()));
    header('Last-Modified: ' . gmdate($gm_date, $want_time));
    header('X-Powered-By:');
    header('Expires:');
    header('Pragma:');
    header('Cache-Control:');
}

?>
<html>
<head>
<style type="text/css">
<!--
.gridtable
{
	width:100%;
	border-collapse:collapse;
}
.gridtable th, .gridtable td
{
	border:1px solid black;
	text-align:center;
	font-size:10pt;
	padding:2px 2px 2px 2px;
}
.gridtable th
{
	font-weight:bold;
	color:#FFFFFF;
	background-color:#555555;
}
.gridtable td.admin_note
{
	padding-left:32px;
	padding-right:32px;
	text-align:left;
}
-->
</style>
</head>
<body>
<div align="center">
	<font size="+1"><b><u><?php echo ucwords(implode(' : ', $title)) ?></u></b></font>
</div>

<br />

<table class="gridtable" align="center">
	<thead style="display:table-header-group;"><tr>
		<th colspan="7" class="wide">General</th>
		<th colspan="5" class="wide">Unpacked</th>
		<th colspan="5" class="wide">Crated</th>
		<th></th>
	</tr>
	<tr>
		<th>COE ID</th>
		<th>Product</th>
		<th>Factory ID</th>
		<th>Suppliers</th>
		<th>Sale Price</th>
		<th>Cost Price</th>
		<th>Cost Price (USD)</th>

		<th>Dimensions (cms)</th>
		<th>Weight (Kgs)</th>
		<th>Volume (m<sup>3</sup>)</th>
		<th>20ft Qty</th>
		<th>40ft Qty</th>

		<th>Dimensions (cms)</th>
		<th>Weight (Kgs)</th>
		<th>Volume (m<sup>3</sup>)</th>
		<th>20ft Qty</th>
		<th>40ft Qty</th>

		<th>Latest Update</th>
	</tr></thead>
	<tfoot style="display:table-footer-group;"></tfoot>
	<tbody>
<?php
$common_lists = array();
foreach (array('manufacturer' => 'manufacturers', 'year_released' => 'year_released', 'supplier' => 'suppliers', 'name' => 'product_types', 'condition' => 'product_conditions', 'availability' => 'product_availability', 'name' => 'product_country_location') as $key => $table) {
    $query = $DB->query("SELECT id,`" . $key . "` FROM " . $table);
    while ($row = $DB->fetch_assoc($query)) {
        $common_lists[$table][$row['id']] = htmlspecialchars($row[$key]);
    }

}

$supplier_products = array();
$query             = $DB->query("SELECT * FROM supplier_product WHERE product_id IN(" . implode(',', $products_list) . ")");
while ($row = $DB->fetch_assoc($query)) {
    $supplier_products[$row['product_id']][] = $row['supplier_id'];
}

$sshots_count = array();
$query        = $DB->query("SELECT id,COUNT(ss_id) AS count FROM product_screenshots WHERE id IN(" . implode(',', $products_list) . ") GROUP BY id");
while ($row = $DB->fetch_assoc($query)) {
    $sshots_count[$row['id']] = $row['count'];
}

foreach ($products_list as $product_id) {
    $product = $DB->query_first("SELECT p.*, UNIX_TIMESTAMP(last_modified) AS time_modified FROM products p WHERE p.id=" . intval($product_id), MYSQLI_ASSOC);
    if ('y' == $product['price_flux']) {
        $product['sales_price']    = $product['cost_price'] * ($product['markup'] + 100.0) / 100.0;
        $product['sales_currency'] = $product['cost_currency'];
    }
    $product['dimension'] = '';
    $manuals_total        = count(productAttachmentsFromDatabase($product_id)) + count(productAttachmentsFromFTP($product_id));
    if ($product_dimen = $DB->query_first("SELECT pd.* FROM products_dimen pd WHERE pd.id=" . intval($product_id), MYSQLI_ASSOC)) {
        if (($product_dimen['width'] + $product_dimen['depth'] + $product_dimen['height']) != 0) {
            $product['dimension'] = 'W' . $product_dimen['width'] . 'xD' . $product_dimen['depth'] . 'xH' . $product_dimen['height'];
        }

        if (0 != $product_dimen['volume']) {
            $product['volume'] = $product_dimen['volume'];
        }

        if (0 != $product_dimen['weight']) {
            $product['weight'] = $product_dimen['weight'];
        }

    }
    $product_packing = array();
    $query           = $DB->query("SELECT * FROM product_packing WHERE product_id=" . intval($product_id));
    while ($row = $DB->fetch_assoc($query)) {
        $product_packing[] = $row;
    }

    $product_load = $DB->query_first("SELECT * FROM container_load WHERE product_id=" . intval($product_id), MYSQLI_ASSOC);
    $suppliers    = array();
    if (count($supplier_products[$product_id])) {
        foreach ($supplier_products[$product_id] as $supplier_id) {
            $suppliers[] = $common_lists['suppliers'][$supplier_id];
        }
    }

?>
	<tr>
		<td><?php echo $product_id ?></td>
		<td><?php echo htmlspecialchars($product['product_name']) ?></td>
		<td><?php echo htmlspecialchars($product['factory_id']) ?></td>
		<td><?php echo implode("<br />\n", $suppliers) ?></td>
		<td><?php echo getCurrencySymbol($product['sales_currency']) . convertCurrency($product['sales_price'], $product['sales_currency']) ?> <?php echo $product['sales_currency'] ?></td>
		<td><?php echo getCurrencySymbol($product['cost_currency']) . convertCurrency($product['cost_price'], $product['cost_currency']) ?> <?php echo $product['cost_currency'] ?></td>
		<td>$<?php echo convertCurrency($product['cost_price'], $product['cost_currency'], 'USD') ?></td>

		<td><?php echo htmlspecialchars($product['dimension']) ?></td>
		<td><?php echo htmlspecialchars($product['weight']) ?></td>
		<td><?php echo ($product['metres3_unpacked'] > 0.0) ? sprintf('%.3f', $product['metres3_unpacked']) : '' ?></td>
		<td><?php echo ($product_load['freeload_20ft_qty'] > 0) ? ((('approx' == $product_load['freeload_20ft_type']) ? '~' : '') . $product_load['freeload_20ft_qty']) : '' ?></td>
		<td><?php echo ($product_load['freeload_40ft_qty'] > 0) ? ((('approx' == $product_load['freeload_40ft_type']) ? '~' : '') . $product_load['freeload_40ft_qty']) : '' ?></td>

		<td><?php foreach ($product_packing as $packing) {
        echo 'W' . $packing['width'] . 'xD' . $packing['depth'] . 'xH' . $packing['height'] . "<br />";
    }
?>
        </td>
		<td>
<?php
    foreach ($product_packing as $packing) {
        echo $packing['weight'] . "<br />";
    }
?>
        </td>
		<td><?php echo ($product['metres3_packed'] > 0.0) ? sprintf('%.3f', $product['metres3_packed']) : '' ?></td>
		<td><?php echo ($product_load['contload_20ft_qty'] > 0) ? ((('approx' == $product_load['contload_20ft_type']) ? '~' : '') . $product_load['contload_20ft_qty']) : '' ?></td>
		<td><?php echo ($product_load['contload_40ft_qty'] > 0) ? ((('approx' == $product_load['contload_40ft_type']) ? '~' : '') . $product_load['contload_40ft_qty']) : '' ?></td>

		<td><?php echo date('d/m/Y', $product['time_modified']) ?></td>
	</tr>
<?php
$query = $DB->query("SELECT *, UNIX_TIMESTAMP(date_added) AS time_added FROM product_admin_notes WHERE product_id=" . intval($product_id) . " ORDER BY date_added DESC");
    $temp  = array();
    while ($row = $DB->fetch_assoc($query)) {
        $temp[] = nl2br(htmlspecialchars($row['user'] . ' - ' . date('d/m/Y', $row['time_added']) . "\n" . $row['note']));
    }

    if (count($temp) > 1) {
        $temp   = array($temp[0]);
        $temp[] = 'More notes available in webadmin...';
    }
    if (count($temp)) {
?>
	<tr><td colspan="18" class="admin_note"><?php echo implode("<hr />", $temp) ?></td></tr>
<?php
}
}
?>
	</tbody>
</table>

<br />
</body>
</html>
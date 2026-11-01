<?php
include_once __DIR__ . '/common.inc.php';

?><!DOCTYPE html>
<html>
<head>
<style type="text/css">
<!--
.titles
{
	font-weight: bold;
	font-size: 10pt;
	color: #FFFFFF;
	background-color: #555555;
	border:1px solid black;
	text-align: center;
	writing-mode:tb-rl;
}
.data
{
	border:1px solid black;
	font-size: 10pt;
	text-align: center;
	padding:2px 2px 2px 2px;
}
-->
</style>
</head>
<?php
include '../currency.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/products/query_attachment.inc.php';

$whereClause = "";
$category    = "";
$subCategory = "";
if ("" != $_GET['parent']) {
    $temp        = $DB->query_first("SELECT name FROM product_types WHERE id='" . $_GET['parent'] . "'");
    $category    = ucwords($temp[0]);
    $whereClause = " WHERE pt.parent='" . $parent . "'";
    if ("" != $_GET['type']) {
        $temp2       = $DB->query_first("SELECT name FROM product_types WHERE id='" . $_GET['type'] . "'");
        $subCategory = ucwords($temp2[0]);
        $whereClause .= " AND p.type='" . $type . "'";
    }
}

$sql = " SELECT" .
    "	p.id" .
    " FROM products p" .
    " LEFT JOIN product_types pt ON p.type=pt.id" .
    $whereClause .
    " ORDER BY p.product_name";

$query = $DB->query($sql);
while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
    $items[] = $row;
}

$title = $category;
if ("" != $subCategory) {
    $title .= " : " . $subCategory;
}

?>
<body>
<div align="center">
	<font size="+1"><b><u><?php echo $title ?></u></b></font>
</div>

<br />

<table width="100%" style="border-collapse:collapse;" align="center">
	<tr>
		<td class="titles">COE ID</td>
		<td class="titles">Product</td>
		<td class="titles">Cost (USD)</td>
		<td class="titles">Selling (USD)</td>
		<td class="titles">Supplier</td>
		<td class="titles">Man.</td>
		<td class="titles">Year</td>
		<td class="titles">Location</td>
		<td class="titles">Avail.</td>
		<td class="titles">Short</td>
		<td class="titles">Long</td>
		<td class="titles">Details</td>
		<td class="titles">Manual</td>
		<td class="titles">Condition</td>
		<td class="titles">Dimensions</td>
		<td class="titles">Weight (kg)</td>
		<td class="titles">SS</td>
		<td class="titles">Active</td>
	</tr>
<?php
for ($i = 0; $i < count($items); $i++) {
    $current = $items[$i];

    $sql = " SELECT" .
        "	p.*," .
        "	p.id AS prodID," .
        "	p.active AS prodActive," .
        "	m.manufacturer," .
        "	yr.year_released," .
        "	pcl.name as location," .
        "	pa.availability," .
        "	pc.`condition`," .
        "	pt.*," .
        "	s.supplier" .
        " FROM products p" .
        " LEFT JOIN manufacturers m ON p.manufacturer=m.id" .
        " LEFT JOIN year_released yr ON p.year_released=yr.id" .
        " LEFT JOIN product_country_location pcl ON p.country_location=pcl.id" .
        " LEFT JOIN product_availability pa ON p.available=pa.id" .
        " LEFT JOIN product_conditions pc ON p.`condition`=pc.id" .
        " LEFT JOIN product_types pt ON p.type=pt.id" .
        " LEFT JOIN supplier_product sp ON sp.product_id=p.id" .
        " LEFT JOIN suppliers s ON sp.supplier_id=s.id" .
        " WHERE p.id=" . $current['id'] .
        " ORDER BY p.product_name";
    $current = $DB->query_first($sql, MYSQLI_ASSOC);

    // now need to modify the selling price, if the sales and cost currencies are different..
    if ($current['sales_currency'] != $current['cost_currency'] && 'y' == $current['price_flux']) {
        $markup                 = floatval($current['markup']) / 100;
        $newCost                = showPriceFrom($current['cost_price'], $current['cost_currency'], $current['sales_currency']);
        $newSales               = $newCost * (1 + $markup) * 100;
        $newSales               = intval($newSales) / 100;
        $current['sales_price'] = showPriceFrom($newSales, $current['sales_currency'], $current['sales_currency']);
    }

    $manuals            = productAttachmentsFromDatabase($current['prodID']);
    $manuals_ftp        = productAttachmentsFromFTP($current['prodID']);
    $current['manuals'] = count($manuals) + count($manuals_ftp);

    $cost       = showPriceFrom($current['cost_price'], $current['cost_currency']);
    $selling    = showPriceFrom($current['sales_price'], $current['sales_currency']);
    $result     = $DB->query("SELECT ss_id FROM product_screenshots WHERE id='" . $current['prodID'] . "'");
    $numScreens = intval($DB->num_rows($result));
//    if($current['weight']>0.0) $current['weight']=sprintf('%.2f',$current['weight']);
?>
	<tr>
		<td class="data"><?php echo $current['prodID'] ?></td>
		<td class="data"><?php echo str_replace('<', '&lt;', stripslashes($current['product_name'])) ?></td>
		<td class="data">$<?php echo sprintf("%01.2f", $cost) ?></td>
		<td class="data">$<?php echo sprintf("%01.2f", $selling) ?></td>
		<td class="data"><?php echo str_replace('<', '&lt;', stripslashes($current['supplier'])) ?></td>
		<td class="data"><?php echo str_replace('<', '&lt;', $current['manufacturer']) ?></td>
		<td class="data"><?php echo str_replace('<', '&lt;', $current['year_released']) ?></td>
		<td class="data"><?php echo str_replace('<', '&lt;', $current['location']) ?></td>
		<td class="data"><?php echo str_replace('<', '&lt;', $current['availability']) ?></td>
		<td class="data"><?php echo trim($current['short_desc']) != "" ? "Y" : "N" ?></td>
		<td class="data"><?php echo trim($current['long_desc']) != "" ? "Y" : "N" ?></td>
		<td class="data"><?php echo trim($current['product_details']) != "" ? "Y" : "N" ?></td>
		<td class="data"><?php echo trim($current['manuals'] > 0) != "" ? "Y" : "N" ?></td>
		<td class="data"><?php echo str_replace('<', '&lt;', $current['condition']) ?></td>
		<td class="data"><?php echo str_replace('<', '&lt;', stripslashes($current['dimension'])) ?></td>
		<td class="data"><?php echo str_replace('<', '&lt;', $current['weight']) ?></td>
		<td class="data"><?php echo $numScreens ?></td>
		<td class="data"><?php echo ucfirst($current['prodActive']) ?></td>
	</tr>
<?php
}
?>
</table>

<br />

</body>
</html>


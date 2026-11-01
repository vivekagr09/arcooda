<?php
include_once __DIR__ . '/common.inc.php';

?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Style-Type" content="text/css">
<style type="text/css">
.titles
{
	font-weight: bold;
	font-size: 10pt;
	color: #FFFFFF;
	background-color: #555555;
	text-align: center;
}
.data
{
	font-size: 10pt;
	text-align: center;
}
</style>
</head>
<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/currency.php';

$fullAddress = "list_stock_details.php?";
$whereClause = "";
$category    = "";
$subCategory = "";

if ("" != $_GET['parent']) {
    $fullAddress .= "parent=" . $parent;
    $temp     = $DB->query_first("SELECT name FROM product_types WHERE id='" . $_GET['parent'] . "'");
    $category = ucwords($temp[0]);

    if ("" != $_GET['type']) {
        $temp2       = $DB->query_first("SELECT name FROM product_types WHERE id='" . $_GET['type'] . "'");
        $subCategory = ucwords($temp2[0]);
        $whereClause .= " AND p.product_type='" . $type . "'";
        $fullAddress .= "&type=" . $type;
    }
    if ("" != $_GET['company']) {
        $whereClause .= " AND p.company_stock='" . $company . "'";
        $fullAddress .= "&company=" . $company;
    }
    if ("" != $_GET['catagory']) {
        $whereClause .= " AND p.product_catagory='" . $catagory . "'";
        $fullAddress .= "&catagory=" . $catagory;
    }
    if ("" != $_GET['supplier']) {
        $whereClause .= " AND p.supplier='" . $supplier . "'";
        $fullAddress .= "&supplier=" . $supplier;
    }
    if ("" != $_GET['location']) {
        $whereClause .= " AND p.storage='" . $location . "'";
        $fullAddress .= "&location=" . $location;
    }
    if ("" != $_GET['samplestock']) {
        $whereClause .= " AND p.samplestock='" . $samplestock . "'";
        $fullAddress .= "&location=" . $location;
    }
}

if ($order) {
    $sort = " ORDER BY `" . $order . "`";
} else {
    $sort = " ORDER BY coe_id";
}

$sql = " SELECT" .
    "	p.*," .
    "	t.name AS type_name," .
    "	cl.name AS location," .
    "	tp.name as section," .
    "	pd.`condition` as product_condition," .
    "	tp.description as section_description," .
    "	ca.name AS product_catagory_name," .
    "	ps.location AS storage_location_name," .
    "	s.supplier AS supplier_name," .
    "	sa.name AS samplestock_name" .
    " FROM stock p" .
    " LEFT JOIN product_types t ON t.id=p.product_type" .
    " LEFT JOIN product_types tp ON tp.id=t.parent" .
    " LEFT JOIN product_country_location cl ON (p.country_location=cl.id)" .
    " LEFT JOIN stock_product_catagories ca ON (p.product_catagory=ca.id)" .
    " LEFT JOIN product_storage_location ps ON (p.storage=ps.id)" .
    " LEFT JOIN product_conditions pd ON (p.`condition`=pd.id)" .
    " LEFT JOIN suppliers s ON (p.supplier=s.id)" .
    " LEFT JOIN stock_samplestock sa ON (p.samplestock=sa.id)" .
    " WHERE 1" .
    $whereClause .
    $sort;

$result = $DB->query($sql);
while ($row = $DB->fetch_array($result)) {
    $items[] = $row;
}

$title = $category;
if ("" != $subCategory) {
    $title .= " : " . $subCategory;
}

?>
<body>
<div align="center"><font size="+1"><b><u><?php echo $title ?></u></b></font></div>
<br />
<table width="80%" border="1" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td class="titles"><a href="<?php echo $fullAddress ?>&order=stock_cod" style="color:white">Stock COD</a></td>
        <td class="titles"><a href="<?php echo $fullAddress ?>&order=purchase_no" style="color:white">Purchase No</a></td>
		<td class="titles"><a href="<?php echo $fullAddress ?>&order=product_name" style="color:white">Name</a></td>
		<td class="titles"><a href="<?php echo $fullAddress ?>&order=coe_id" style="color:white">COE ID</a></td>
        <td class="titles"><a href="<?php echo $fullAddress ?>&order=id" style="color:white">Stock ID</a></td>
		<td class="titles"><a href="<?php echo $fullAddress ?>&order=product_catagory" style="color:white">Category</a></td>
		<td class="titles"><a href="<?php echo $fullAddress ?>&order=product_type" style="color:white">Type</a></td>
		<td class="titles"><a href="<?php echo $fullAddress ?>&order=supplier" style="color:white">Supplier</a></td>
		<td class="titles"><a href="<?php echo $fullAddress ?>&order=sample_qty" style="color:white">Sample</a></td>
		<td class="titles"><a href="<?php echo $fullAddress ?>&order=stock_qty" style="color:white">Stock</a></td>
		<td class="titles"><a href="<?php echo $fullAddress ?>&order=order_qty" style="color:white">Order</a></td>
		<td class="titles"><a href="<?php echo $fullAddress ?>&order=storage" style="color:white">Storage Location</a></td>
		<td class="titles"><a href="<?php echo $fullAddress ?>&order=date_received" style="color:white">Received</a></td>
		<td class="titles"><a href="<?php echo $fullAddress ?>&order=condition" style="color:white">Condition</a></td>
		<td class="titles">Cost Price</td>
		<td class="titles"><a href="<?php echo $fullAddress ?>&order=weight" style="color:white">kg</a></td>
		<td class="titles"><a href="<?php echo $fullAddress ?>&order=active" style="color:white">Active</a></td>
		<td class="titles"><a href="<?php echo $fullAddress ?>&order=qty" style="color:white">Qty</a></td>
		<td class="titles"><a href="<?php echo $fullAddress ?>&order=last_user" style="color:white">User</a></td>
		<td class="titles"><a href="<?php echo $fullAddress ?>&order=company_stock" style="color:white">Company Stock</a></td>
	</tr>
<?php
foreach ($items as $current) {
    $cost = showPriceFrom($current['unit_price'], $current['cost_currency']);
?>
	<tr>
		<td class="data"><?php echo stripslashes($current['stock_cod']) ?>&nbsp;</td>
		<td class="data"><?php echo stripslashes($current['purchase_no']) ?>&nbsp;</td>
		<td class="data"><?php echo stripslashes($current['product_name']) ?>&nbsp;</td>
		<td class="data"><?php echo stripslashes($current['coe_id']) ?>&nbsp;</td>
		<td class="data"><?php echo stripslashes($current['id']) ?>&nbsp;</td>
		<td class="data"><?php echo stripslashes($current['product_catagory_name']) ?>&nbsp;</td>
		<td class="data"><?php echo stripslashes($current['type_name']) ?>&nbsp;</td>
		<td class="data"><?php echo stripslashes($current['supplier_name']) ?>&nbsp;</td>
		<td class="data"><?php echo stripslashes($current['sample_qty']) ?>&nbsp;</td>
		<td class="data"><?php echo stripslashes($current['stock_qty']) ?>&nbsp;</td>
		<td class="data"><?php echo stripslashes($current['order_qty']) ?>&nbsp;</td>
		<td class="data"><?php echo stripslashes($current['storage_location_name']) ?>&nbsp;</td>
		<td class="data"><?php echo $current['date_received'] ?>&nbsp;</td>
		<td class="data"><?php echo stripslashes($current['product_condition']) ?>&nbsp;</td>
		<td class="data">$<?php echo sprintf("%01.2f", $cost) ?></td>
		<td class="data"><?php echo stripslashes($current['weight']) ?>&nbsp;</td>
		<td class="data"><?php echo stripslashes($current['active']) ?>&nbsp;</td>
		<td class="data"><?php echo stripslashes($current['sample_qty'] + $current['stock_qty'] + $current['order_qty']) ?>&nbsp;</td>
		<td class="data"><?php echo stripslashes($current['last_user']) ?>&nbsp;</td>

<?php
// Company Stock Name
    $company_stock_name = array(1 => "Coinop Express", 2 => "Fortunewave", 3 => "Highwaygames");
?>
		<td class="data"><?php echo stripslashes($company_stock_name[$current['company_stock']]) ?>&nbsp;</td>
	</tr>
<?php
}
?>
</table>
<br />
</body>
</html>

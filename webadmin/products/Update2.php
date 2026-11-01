<?php
include_once __DIR__ . "/common.inc.php";
$id = intval($_REQUEST["id"]);

if ($product = $DB->query_first("SELECT `product_name`, `type` AS `product_catagory`, `type` AS `product_type`, `storage`, `country_location`, `condition`, `weight`, `cost_price` AS `unit_price`, `cost_currency`, `active` FROM `products` WHERE `id` = " . $id . " LIMIT 1", MYSQLI_ASSOC))
{
	$supplier = $DB->query_first("SELECT `supplier_id` FROM `supplier_product` WHERE `product_id` = " . $id . " LIMIT 1", MYSQLI_ASSOC);
	if ($supplier['supplier_id'] > 0) $product["supplier"] = $supplier['supplier_id'];

	$type_tree = array();
	$query = $DB->query("SELECT `id`, `parent`, `name` FROM `product_types`");
	while($row = $DB->fetch_assoc($query)) $type_tree[$row["id"]] = $row;
	$DB->free_result($query);

	while(array_key_exists($product["product_catagory"], $type_tree) && $type_tree[$product["product_catagory"]]["parent"] > 0) $product["product_catagory"] = $type_tree[$product["product_catagory"]]["parent"];

	$product_admin_notes = array();
	$query = $DB->query("SELECT `note`, `user`, UNIX_TIMESTAMP(`date_added`) AS `time_added` FROM `product_admin_notes` WHERE `product_id` = " . $id);
	while ($row = $DB->fetch_assoc($query))
	{
		$row["date_added"] = date('d M, Y', $row['time_added']);
		$product_admin_notes[] = $row;
	}
	$DB->free_result($query);

?>
<!DOCTYPE html>
<html>
<head>
<title></title>
<script type="text/javascript">
//<![CDATA[
var product = <?php jecho($product) ?>;
var product_admin_notes = <?php jecho($product_admin_notes) ?>;
for (var k in product) window.opener.$("[name='data3[" . k . "]']").val(product[k]);
for (var k = 0;k < product_admin_notes.length;k++)
{
	var v = product_admin_notes[i];
	window.opener.addOldAdminNote(v.user, v.date_added, v.note);
}
window.opener.refreshCosts();
window.close();
//]]>
</script>
</head>
<body>
</body>
</html>
<?php
}
else
{
?>
<!DOCTYPE html>
<html>
<head>
<title></title>
<script type="text/javascript">
//<![CDATA[
//window.close();
//]]>
</script>
</head>
<body>
Could not find any products in the database with coeid: <?php echo $_GET["id"] ?>
</body>
</html>
<?php
}
?>
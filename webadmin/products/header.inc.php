<?php
include_once dirname(__FILE__) . "/common.inc.php";

$popup_window = (array_key_exists("return_message", $get_vars)) || isset($_GET['popup']);

if (!$popup_window)
{
	include_once $_SERVER["DOCUMENT_ROOT"] . "/webadmin/header.inc.php";
}
else
{
	header("Content-Type:text/html; charset=utf-8");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Admin</title>
	<style type="text/css">
body, p, td	{ font-family:sans-serif; font-size:12px; }
a:hover	{ color:#f00; }
	</style>
	<link rel="stylesheet" type="text/css" href="style.css" />
	<script type="text/javascript" src="/webadmin/jscript/tooltip.1.0.2.js"></script>
	<script type="text/javascript" src="/webadmin/jscript/common.php.js"></script>
</head>
<body style="background:#F0F4F9;">
<?php
}

if (!isset($_GET['popup'])) {
$row = $DB->query_first("SELECT SUM(IF(`active` = 'y', 1, 0)) AS `active`, COUNT(*) AS `total` FROM `products`");
?>
<div class="product_content">
	<table cellpadding="2" cellspacing="2" style="width:100%;"><tr>
		<td style="text-align:center;width:20%;background:#eee;"><b>Products</b></td>
		<td style="text-align:right;width:80%;background:#eee;">
			<a href="/webadmin/products_new?action=add">Add New Product</a> |
			<!--<a href="index.php?action=search">Search Products</a> | -->
			<a href="/webadmin/products_new?action=list&amp;active=y">List Active Products (<?php echo $row['active'] ?>)</a> |
			<a href="/webadmin/products_new?action=list">List All Products (<?php echo $row['total'] ?>)</a>
		</td>
	</tr></table>
	<script type="text/javascript">
//<![CDATA[
//]]>
	</script>
	<br />
<?php } ?>

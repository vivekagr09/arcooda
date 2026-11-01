<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?= $title ?></title>
	<link rel="stylesheet" href="style.css" type="text/css" />
</head>

<body>
<h1><?= $header ?></h1>
<!-- menu -->
<div>
	<ul>
		<li><a href="suppliers_admin.php?action=add">Add/Edit Supplier</a></li>
		<li><a href="suppliers_admin.php?action=list">List Suppliers</a></li>
		<li><a href="purchaseorders_admin.php?action=add">Add Puchase Order</a></li>
		<li><a href="purchaseorders_admin.php?action=list&show=open">Open Purchase Orders (<?= $open_po_count ?>)</a></li>
		<li><a href="purchaseorders_admin.php?action=list&show=all">All Purchase Orders (<?= $po_count ?>)</a></li>
		<li><a href="stuff_admin.php">Edit Purchase Order Options</a></li>
		<li>Recent Changes</li>
	</ul>
</div>
<!-- end menu -->
<!-- messages -->
<div>
<?= $messages ?>
</div>
<!-- end messages -->
<!-- page content -->
<div>
<?= $content ?>
</div>
<!-- end page content -->
</body>
</html>

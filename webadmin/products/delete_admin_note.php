<?php

include_once 'products_common.inc.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
	$id = intval($_GET['id']);

	$result = $DB->query(sprintf("SELECT * FROM `product_admin_notes` WHERE `id` = %d", $id ));

	$row = $DB->fetch_array($result);

	if (count($row) > 0) {
		$DB->query("DELETE FROM `product_admin_notes` WHERE `id` = " . $id);

		if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
			header('Location: ' . $_SERVER['HTTP_REFERER']);
			exit();
		}

		header('Location: /webadmin/products');
		exit();
	}
}

die("404");

?>
<?php

//
//	DO NOT RUN.
//

die();

include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

include_once 'products_common.inc.php';

include '../currency.php';
include 'config.inc.php';
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webadmin/functions/formatting.inc.php');
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webadmin/functions/lists.inc.php');
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webadmin/options/options_functions.inc.php');
/*
$products = $DB->query('SELECT * FROM `products`');

while($p = $DB->fetch_array($products)) {
	$name = $p['product_name'];

	$ssq = $DB->query('SELECT * FROM `product_screenshots` WHERE `id`=' . $p['id']);

	while($ss = $DB->fetch_array($ssq)) {
		$caption = $ss['caption'];
		$ssid = $ss['ss_id'];

		//$c = ($caption == "") ? $ssid : $caption;

		$cc = trim($name . ' ' . $ssid);

		$DB->query('UPDATE `product_screenshots` SET `caption` = ' . formatSQLString($cc) . ' WHERE `ss_id` = ' . $ss['ss_id']);
	}
}
*/
echo "DONE";

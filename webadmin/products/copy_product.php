<?php

// Product ID is passed to action="add"
// Using that product ID to retrieve product data then fill up all inputs

die();

// Using an existing product ID, insert a new product and populate fields with existing product's data.
// Once complete, script redirects to the edit page for the new product.

$section_code = "PRODUCT";

include_once __DIR__ . '/common.inc.php';

include_once 'products_common.inc.php';

include_once __DIR__ . '/header.inc.php';

include '../currency.php';
include 'config.inc.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/functions/formatting.inc.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/functions/lists.inc.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/options/options_functions.inc.php';

if ('' == $table) {
    $table = 'products';
}

$existing_product_id = $_GET['id'];
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/functions/formatting.inc.php';
$existing_product = $DB->query_first(" SELECT" . "	*," . "	UNIX_TIMESTAMP(date_added) AS date_added," . "	UNIX_TIMESTAMP(last_modified) AS last_modified" . " FROM " . $table . " WHERE id='" . $id . "'");

//  $new_product =

var_dump($existing_product);

die(' UNDER DEVELOPMENT');
header("location:admin.php?action=edit&id=10488");

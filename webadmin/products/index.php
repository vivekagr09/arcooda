<?php
include_once "common.inc.php";

$action = $posted['action'];
$product_id = $posted['id'];
if ($posted['product_id'] != 0) $product_id = intval($posted['product_id']);

//Update Actions
switch ($action)
{
default:
	break;
}

//Redirect Actions
switch ($action)
{
case 'export':
header('Location: products_exportXLS.php?' . http_build_query($get_vars));
break;

case 'select':
	header('Location: select.php?' . http_build_query($get_vars));
	break;

case 'list':
case 'search':
default:
	$list_url = $get_vars;
	//?row=1&currency=USD&search_id=&type=
	header("Location: list.php" . rtrim("?" . http_build_query($list_url), "?"));
	die();
}

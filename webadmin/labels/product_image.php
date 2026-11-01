<?php
include_once "../connection.inc.php";

$product_id = intval($_REQUEST['product_id']);
$product    = $DB->query_first("SELECT * FROM products WHERE id=" . $product_id);

$image = $_SERVER['DOCUMENT_ROOT'] . '/images/coedefault.jpg';

if ('' != $product['image'] && file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $product['image'])) {
    $image = $_SERVER['DOCUMENT_ROOT'] . '/' . $product['image'];
}

header("Content-Type: image/auto");
header("Content-Disposition: inline; filename=" . basename($image));
header("Date: " . date('r', filemtime($image)));
header("Cache-Control: public");
header("Content-Length: " . filesize($image));
readfile($image);

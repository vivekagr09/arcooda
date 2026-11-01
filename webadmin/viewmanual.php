<?php

die("DEPRECATED");

include_once __DIR__ . '/common.inc.php';

$product_id = intval($_REQUEST['product_id']);

$manual_type = $_REQUEST['type'];
$types       = array('coe' => 'coe', 'fac' => 'fac');
$manual_type = (isset($types[$manual_type])) ? $types[$manual_type] : 'coe';

$file = $DB->query_first("SELECT " . $manual_type . "_manual AS original, " . $manual_type . "_mimetype AS mimetype FROM product_manuals WHERE product_id=" . $product_id);

$filename = __DIR__ . '/../manuals/' . $manual_type . '_' . $product_id;
if (file_exists($filename) && filesize($filename) > 0) {
    if ('' != $file['mimetype']) {
        header("Content-Type: " . $file['mimetype']);
    } else {
        header("Content-Type: " . mime_content_type($file['original']));
    }

    header("Content-Disposition: inline; filename=" . $file['original']);
    readfile($filename);
} else {
    header("HTTP/1.1 404 Not Found", true, 404);
}

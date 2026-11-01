<?php
// Common
include_once "../common.inc.php";

$pb = new ProductBuilder($DB);

$categories = $pb->getAllCategoryNames();

//unset($categories[0]);

echo json_encode($categories);
?>
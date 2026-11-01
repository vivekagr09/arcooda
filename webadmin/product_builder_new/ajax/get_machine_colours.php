<?php
// Common
include_once "../common.inc.php";

$pb = new ProductBuilder($DB);

$colours = $pb->getAllcolours();

//unset($categories[0]);

echo json_encode($colours);
?>
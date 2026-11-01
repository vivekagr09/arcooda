<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/webadmin/common.inc.php";
moduleOverride("news", dirname(__FILE__) . "/query.inc.php");
moduleLoad("news");
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
$NH = new News_Handler();
?>
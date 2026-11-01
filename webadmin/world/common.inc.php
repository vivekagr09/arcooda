<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/webadmin/common.inc.php";

moduleOverride("world", __DIR__ . "/query.inc.php");
moduleLoad("world");

$section_handler = new World_Handler();
$section_name = $section_handler->section_name;
?>
<?php
include_once("packing_common.inc.php");
$container_list=packingFromPost($posted);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Packinglist.xml"');
include 'templates/packing_excel_export.tpl.php';
?>
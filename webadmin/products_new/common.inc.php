<?php
include_once __DIR__ . '/query.inc.php';

$section_code = 'PRODUCT';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';
global $_GLOBALS;
moduleLoad('staff');
$allowed_sections = get_allowed_sections($_GLOBALS['staff']['user']);
if (!has_permission('PRODUCT', $allowed_sections)) {
    redirect('/webadmin');
}
moduleOverride('product', __DIR__ . '/query.inc.php');
moduleLoad('product');
error_reporting(0);
$section_handler = new Product_Handler();
$section_name    = $section_handler->section_name;
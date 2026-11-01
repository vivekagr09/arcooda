<?php
include_once __DIR__ . '/query.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

global $_GLOBALS;

moduleOverride('serial', __DIR__ . '/query.inc.php');
moduleLoad('serial');
unset($_GLOBALS['criteria_options']['serial']['active']);

$section_handler = new Serial_Handler();
$section_name    = $section_handler->section_name;


<?php
include_once __DIR__ . '/query.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

moduleOverride('newsletter', __DIR__ . '/query.inc.php');

moduleLoad('member');

$section_handler = new Newsletter_Handler();
$section_name    = $section_handler->section_name;

<?php
include_once __DIR__ . '/query.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

moduleOverride('freight', __DIR__ . '/query.inc.php');
moduleLoad('freight');

$section_handler = Freight_Service_Handler::getInstance();

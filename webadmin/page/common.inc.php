<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

$section_code = 'OPTIONS';

moduleOverride('page', __DIR__ . '/query.inc.php');
moduleLoad('page');

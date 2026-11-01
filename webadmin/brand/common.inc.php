<?php
include_once __DIR__ . '/query.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

moduleOverride('brand', __DIR__ . '/query.inc.php');
moduleLoad('brand');

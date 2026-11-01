<?php
include_once __DIR__ . '/query.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

moduleOverride('category', __DIR__ . '/query.inc.php');
moduleLoad('category');

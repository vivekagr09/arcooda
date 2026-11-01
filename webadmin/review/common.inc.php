<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

// $section_code = 'OPTIONS';

moduleOverride('review', __DIR__ . '/function/query.inc.php');
moduleLoad('review');
moduleLoad('member');
moduleLoad('product');

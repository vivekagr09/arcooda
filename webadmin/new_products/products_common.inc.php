<?php
include_once __DIR__ . '/functions/query_products.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

moduleOverride('product', __DIR__ . '/functions/query_products.inc.php');
moduleLoad('product');

<?php
include_once __DIR__ . '/query.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

moduleOverride('documentinvoice', __DIR__ . '/query.inc.php');
moduleLoad('documentinvoice');

moduleLoad('asset');
moduleLoad('freight');
moduleLoad('invoice');
moduleLoad('member');
moduleLoad('product');

include_once __DIR__ . '/documentinvoice_values.inc.php';

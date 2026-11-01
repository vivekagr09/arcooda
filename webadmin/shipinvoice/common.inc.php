<?php
include_once __DIR__ . '/query.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

moduleOverride('shipinvoice', __DIR__ . '/query.inc.php');
moduleLoad('shipinvoice');

moduleLoad('asset');
moduleLoad('freight');
moduleLoad('invoice');
moduleLoad('member');
moduleLoad('product');

include_once __DIR__ . '/shipinvoice_values.inc.php';

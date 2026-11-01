<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = 'add';
$id     = 0;

$item            = array();
$item_accounts   = array();
$item_comments   = array();
$item_contacts   = array();
$item_lock       = false;
$item_quotes     = array();
$item_warehouses = array();

?>
<h2>Add New Forwarder</h2>
<?php

include __DIR__ . '/addedit.tpl.php';

include_once __DIR__ . '/footer.inc.php';

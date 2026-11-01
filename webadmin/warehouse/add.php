<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = 'add';
$id     = $_GET['id'] ?? 0;

$item = wareHouseFromDatabase($id) ?? warehouseContactDefault();
error_reporting(E_ALL);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
$item_contacts = warehouseContactsFromDatabase($id);
$media_type_links = [];
include __DIR__ . '/_addedit.tpl.php';

include_once __DIR__ . '/footer.inc.php';

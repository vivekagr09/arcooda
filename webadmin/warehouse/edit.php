<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = 'edit';
$id     = $_GET['id'] ?? 0;
$item = wareHouseFromDatabase($id) ?? warehouseContactDefault();
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
$partner_assets  = assetSearch('warehouse',['warehouse_id' => $id ,'active' => '']);

$item_contacts = warehouseContactsFromDatabase($id);
$itemOnlineText = warehouseOnlineFromDatabase($id);
include __DIR__ . '/_addedit.tpl.php';

include_once __DIR__ . '/footer.inc.php';

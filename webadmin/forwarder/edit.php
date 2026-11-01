<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = 'edit';
$id     = intval($_REQUEST['id']);

$item            = forwarderFromDatabase($id);
$item_accounts   = forwarderAccountsFromDatabase($id);
$item_comments   = forwarderCommentsFromDatabase($id);
$item_contacts   = forwarderContactsFromDatabase($id);
$item_lock       = forwarderGetLock($id);
$item_quotes     = forwarderQuotesFromDatabase($id);
$item_warehouses = forwarderWarehousesFromDatabase($id);

?>
<h2>Edit Forwarder</h2>
<?php

include __DIR__ . '/addedit.tpl.php';

include_once __DIR__ . '/footer.inc.php';
?>
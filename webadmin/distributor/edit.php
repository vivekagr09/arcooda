<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = 'edit';
$id     = $_GET['id'] ?? 0;

$item = distributorFromDatabase($id) ?? distributorDefault();

$item_contacts = distributorContactsFromDatabase($id);

include __DIR__ . '/_addedit.tpl.php';

include_once __DIR__ . '/footer.inc.php';

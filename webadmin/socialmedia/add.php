<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = 'add';
$id     = $_GET['id'] ?? 0;

$item = socialMediaPartnerFromDatabase($id) ?? socialMediaContactDefault();

$item_contacts = socialMediaContactsFromDatabase($id);
$media_type_links = [];
include __DIR__ . '/_addedit.tpl.php';

include_once __DIR__ . '/footer.inc.php';

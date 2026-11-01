<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = 'edit';
$id     = $_GET['id'] ?? 0;
$partner_assets  = assetSearch('media_partner',['owner_id' => $id ,'active' => '']);
$item = socialMediaPartnerFromDatabase($id) ?? socialMediaContactDefault();

$item_contacts = socialMediaContactsFromDatabase($id);
$media_type_links = partnerlinksFromDatabase($id);
include __DIR__ . '/_addedit.tpl.php';

include_once __DIR__ . '/footer.inc.php';

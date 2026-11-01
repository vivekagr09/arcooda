<?php
/**
 * @var mixed[] $get_vars
 */
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$member_id                = 0;
$member                   = memberDefault();
$member_assets            = array();
$member_authentication    = array(array("active" => "n"));
$member_comments          = array();
$member_contacts          = array();
$member_couriers          = array();
$member_discounts         = array();
$member_forwarders        = array();
$member_logins            = array();
$member_mailinglists      = array();
$member_notifyparties     = array();
$member_pricing           = array();
$member_requests          = array();
$member_serials           = array();
$member_shippingaddresses = array();
$member_specials          = array();
$member_wanted            = array();
$member_warnings          = array();

$account_id = '';

$doadd_url           = $get_vars;
$doadd_url['action'] = 'do_add';
$doadd_url           = '.?' . http_build_query($doadd_url);
$action              = 'add';
?>
<h2>Add New Member</h2>
<form action="<?=htmlspecialchars($doadd_url)?>" method="post">
<?php

include __DIR__ . '/templates/addedit_members.tpl.php';

?>
</form>
<?php

include_once __DIR__ . '/footer.inc.php';

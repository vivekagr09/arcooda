<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action    = 'add';
$contractor_id = $id = 0;
$staff_id  = $_GLOBALS['staff']['id'];

$contractor               = array();
$contractor_lock          = false;
$contractor_comments      = array();
$contractor_contacts      = array();
$contractor_couriers      = array();
$contractor_discounts     = array();
$contractor_forwarders    = array();
$contractor_logins        = array();
$contractor_login_history = array();
$contractor_notify        = array();
$contractor_preferences   = array();
$contractor_shipping      = array();
$contractor_specials      = array();
$contractor_requests      = array();

?>
<h2>Add New Contractor</h2>
<?php

include __DIR__ . '/addedit.tpl.php';

include_once __DIR__ . '/footer.inc.php';

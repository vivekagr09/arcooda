<?php
include_once __DIR__ . '/common.inc.php';
include_once __DIR__ . '/header_distributer.php';
$action    = 'add';
$distributor_id = $id = 0;
$staff_id  = $_GLOBALS['staff']['id'];
$comment=[];
$distributor               = array();
$distributor_lock          = false;
$distributor_comments      = array();
$distributor_contacts      = array();
$distributor_couriers      = array();
$distributor_discounts     = array();
$distributor_forwarders    = array();
$distributor_logins        = array();
$distributor_login_history = array();
$distributor_notify        = array();
$distributor_preferences   = array();
$distributor_shipping      = array();
$distributor_specials      = array();
$distributor_requests      = array();

?>
<h2>Add New Distributor</h2>
<?php

include __DIR__ . '/addedit_distributor.tpl.php';

include_once __DIR__ . '/footer.inc.php';
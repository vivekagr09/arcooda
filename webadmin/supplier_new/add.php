<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action    = 'add';
$supplier_id = $id = 0;
$staff_id  = $_GLOBALS['staff']['id'];

$supplier               = array();
$supplier_lock          = false;
$supplier_comments      = array();
$supplier_contacts      = array();
$supplier_couriers      = array();
$supplier_discounts     = array();
$supplier_forwarders    = array();
$supplier_logins        = array();
$supplier_login_history = array();
$supplier_preferences   = array();
$supplier_shipping      = array();
$supplier_specials      = array();
$supplier_requests      = array();
$supplier_stock         = array();
?>
<h2>Add New Supplier</h2>
<?php

include __DIR__ . '/addedit.tpl.php';

include_once __DIR__ . '/footer.inc.php';

<?php
$_GLOBALS['load_ext_options'] = false;
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';
include_once 'functions/query_voucher.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/members/functions/query_member.inc.php';

$old_site_id = $posted['site_id'];

if (array_key_exists("site_settings", $_GLOBALS) && array_key_exists("site_id", $_GLOBALS["site_settings"]) && !in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) {
    $posted["site_id"] = $_GLOBALS["site_settings"]["site_id"];
}

$amounts       = array('25' => '25.00', '50' => '50.00', '100' => '100.00', '200' => '200.00', '0' => 'Other');
$terms         = array('0' => 'None') + voucherTermsList();
$currency_list = getCurrencyList();
$status        = array('open' => 'Open', 'processed' => 'Processed', 'cancelled' => 'Cancelled', 'expired' => 'Expired');
$expiry        = array('1' => '1 Month', '3' => '3 Months', '6' => '6 Months', '12' => '12 Months', '0' => 'Other');
$site_list     = array(1 => 'Highway Games', 2 => 'Highway Entertainment', 9 => 'Converters.tv', 16 => 'Car Converters');

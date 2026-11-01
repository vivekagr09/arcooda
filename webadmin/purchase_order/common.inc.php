<?php
global $_GLOBALS;
$_GLOBALS['development']      = array(); //TEMP: Disable development stuff
$_GLOBALS['load_ext_options'] = false;

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

include_once __DIR__ . "/functions/query_invoice.inc.php";
//moduleLoad('invoice');

moduleLoad('asset');
moduleLoad('inquiry');
moduleLoad('supplier_new');
moduleLoad('phpmailer');
moduleLoad('shipinvoice');
moduleLoad('staff');
moduleLoad('supplier');
moduleLoad('template');

include_once __DIR__ . '/functions/query_forwardersquotes.inc.php';

if (3 != $_GLOBALS['site_settings']['site_id']) {
    $posted['site_id'] = $_GLOBALS['site_settings']['site_id'];
}

$all_staff_list      = getStaffList(false);
$active_staff_list   = getStaffList();
$inactive_staff_list = array_diff_assoc($all_staff_list, $active_staff_list);

$display_enquiry = !array_key_exists('status', $posted) || '' == $posted['status'] || preg_match('/enquiry|inquiry|closed/', $posted['status']);
$display_invoice = !array_key_exists('status', $posted) || '' == $posted['status'] || preg_match('/proforma|sales|cancelled/', $posted['status']);
$display_both    = $display_enquiry && $display_invoice;

$_GLOBALS['site_options']['WARNING_IDLE_DAYS'] = 5;

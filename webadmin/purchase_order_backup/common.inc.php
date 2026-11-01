<?php
global $_GLOBALS;

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

include_once __DIR__ . "/functions/query_invoice.inc.php";
//moduleLoad('invoice');

moduleLoad('asset');
moduleLoad('supplier_new');
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

$display_inquiry = !array_key_exists('status', $posted) || '' == $posted['status'] || preg_match('/enquiry|inquiry|closed/', $posted['status']);
$display_invoice = !array_key_exists('status', $posted) || '' == $posted['status'] || preg_match('/proforma|sales|cancelled/', $posted['status']);
$display_both    = $display_inquiry && $display_invoice;

$_GLOBALS['site_options']['WARNING_IDLE_DAYS'] = 5;

if (!function_exists("getInvoicePermissions")) {
    function getInvoicePermissions($user_id)
    {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `section_id` FROM `highwaygroup`.`invoice_permissions` WHERE `user` = %d",
            $user_id
        ))) {
            while ($row = $DB->fetch_array($query)) {
                $result[$row['section_id']] = $row['section_id'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}

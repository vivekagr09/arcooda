<?php

$_GLOBALS['load_ext_options'] = false;

include_once __DIR__ . '/query.inc.php';

/**
 * @var mixed[] $posted
 */
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

global $_GLOBALS;
global $DB;
moduleLoad('staff');
$allowed_sections = get_allowed_sections($_GLOBALS['staff']['user']);
if (!has_permission('INVOICES', $allowed_sections)) {
    redirect('/webadmin');
}
moduleOverride('invoice', __DIR__ . '/query.inc.php');
moduleLoad('invoice');

moduleLoad('asset');
moduleLoad('export');
moduleLoad('express');
moduleLoad('forwarder');
moduleLoad('freight');
moduleLoad('member');
moduleLoad('order');
moduleLoad('product');
moduleLoad('shipinvoice');
moduleLoad('supplier');
moduleLoad('survey');

//@todo What kind of logic is this?

$old_site_id = (array_key_exists('site_id', $posted)) ? $posted['site_id'] : '';
if (!array_key_exists("site_id", $posted) && !in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) {
    $posted["site_id"] = $_GLOBALS["site_settings"]["site_id"];
}
if (array_key_exists('site_id', $posted)) {
    if ('9' == $posted['site_id']) {
        $posted['site_id'] = '9|16|25';
    }
    if ('9' == $old_site_id && '9|16|25' == $posted['site_id']) {
        $posted['site_id'] = '9';
    }
    if ('16' == $old_site_id && '9|16|25' == $posted['site_id']) {
        $posted['site_id'] = '16';
    }
    if ('25' == $old_site_id && '9|16|25' == $posted['site_id']) {
        $posted['site_id'] = '25';
    }
}

$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value) || trim($value) == '') {
            unset($get_vars[$key]);
        }
    }
}

$all_staff_list = array();
if ($query = $DB->query(
    "SELECT `id`, `first_name`, `last_name` FROM `highwaygroup`.`users`"
)) {
    while ($row = $DB->fetch_assoc($query)) {
        $all_staff_list[$row['id']] = trim($row['first_name'] . ' ' . $row['last_name']);
    }
    $DB->free_result($query);
}

$display_inquiry = (array_key_exists('status', $posted) && in_array($posted['status'], array(
    'inquiry',
    'enquiry',
    'closed',
    'inquiry|closed',
    'closed|inquiry',
    'enquiry|closed',
    'closed|enquiry',
    'inquiry|enquiry',
    'enquiry|inquiry',
    'inquiry|enquiry|closed',
    'inquiry|closed|enquiry',
    'enquiry|inquiry|closed',
    'enquiry|closed|inquiry',
    'closed|inquiry|enquiry',
    'closed|enquiry|inquiry',
)));

$display_both = (array_key_exists('status', $posted) && 'inquiry|proforma|sales' == $posted['status']);

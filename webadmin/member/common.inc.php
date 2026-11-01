<?php
include_once __DIR__ . '/query.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

moduleOverride('member', __DIR__ . '/query.inc.php');

moduleLoad('asset');
moduleLoad('bpay');
moduleLoad('invoice');
moduleLoad('member');
moduleLoad('product');
moduleLoad('survey');

global $_GLOBALS;
global $DB;
moduleLoad('staff');
$allowed_sections = get_allowed_sections($_GLOBALS['staff']['user']);
if (!has_permission('MEMBERS', $allowed_sections)) {
    redirect('/webadmin');
}
$site_list = array(1 => 'hg', 2 => 'he', 9 => 'cnv', 16 => 'car', 20 => 'asp');
if ($_GLOBALS['site_settings']['site_id'] > 0 && isset($site_list[$_GLOBALS['site_settings']['site_id']])) {
    $_GET['site_id'] = $_GLOBALS['site_settings']['site_id'];
}

$all_staff_list = array();
if ($query = $DB->query(sprintf(
    "SELECT `id`, CONCAT_WS(' ', `first_name`, `last_name`) AS `name`, `active` FROM `highwaygroup`.`users` ORDER BY `first_name` ASC"
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $all_staff_list[$row['id']] = $row['name'];
    }
    $DB->free_result($query);
}

$staff_id = $_GLOBALS['staff']['staff_id'];
$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value)) {
            unset($get_vars[$key]);
        }
    }
}

$country_list = getCountryList(false);

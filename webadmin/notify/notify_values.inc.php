<?php
global $_GLOBALS;

//Restrict to current website if not coinopexpress.com
$_GET["site_id"] = $_GLOBALS['site_settings']['site_id'];

$posted = array_merge($_GET, $_POST);

$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value)) {
            unset($get_vars[$key]);
        }
    }
}

$all_staff_list = array();
$active_staff   = array();
$inactive_staff = array();

$query = $DB->query("SELECT `id`, CONCAT_WS(' ', `first_name`, `last_name`) AS `name` FROM `highwaygroup`.`users` WHERE `active` = 'y' ORDER BY `first_name`");
while ($row = $DB->fetch_assoc($query)) {
    $all_staff_list[$row['id']] = $row['name'];
}

$active_staff = $all_staff_list;

$action_list = array();
$query       = $DB->query("SELECT action_id, action_name FROM notify_actions ORDER BY action_id");
while ($row = $DB->fetch_assoc($query)) {
    $action_list[$row['action_id']] = $row['action_name'];
}

$websites_list = array();
if ($query = $DB->query(sprintf(
    "SELECT * FROM `highwaygroup`.`company_info` ORDER BY `company_name` ASC"
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $websites_list[$row['company_id']] = $row['company_name'];
    }
    $DB->free_result($query);
}

<?php
global $_GLOBALS;
global $DB;

$_GET['website'] = 39;

$staff_id = $_GLOBALS['staff']['id'];

$staff_mapping = softjobGetOptionList('staff_mapping');
if (0 != $staff_mapping[$staff_id]) {
    $staff_id = $staff_mapping[$staff_id];
}

$all_staff_list = array();
$active_staff   = array();
$query          = $DB->query("SELECT `id`, CONCAT_WS(' ', `first_name`, `last_name`) AS `name`, `active` FROM `highwaygroup`.`users` ORDER BY `first_name` ASC");
while ($row = $DB->fetch_assoc($query)) {
    $all_staff_list[$row['id']] = $row['name'];
    if ('y' == $row['active']) {
        $active_staff[$row['id']] = $row['name'];
    }
}

$inactive_staff    = array();
$assigned_to       = array(0 => 'Software Developer');
$assigned_to_email = array(0 => 'errors@arcooda.com');

$website_list = array(
    3  => 'Coinop Express',
    39 => 'Arcooda',
);

$status_list = array();
$query       = $DB->query("SELECT id,status FROM status ORDER BY status ASC");
while ($row = $DB->fetch_assoc($query)) {
    $status_list[$row['id']] = $row['status'];
}

$posted = array_merge($_GET, $_POST);

$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value)) {
            unset($get_vars[$key]);
        }
    }
}

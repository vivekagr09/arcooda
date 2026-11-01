<?php
global $_GLOBALS;

moduleLoad('staff');

$allowed_sections = get_allowed_sections($_GLOBALS['staff']['user']);

$all_staff_list = array();
$active_staff   = array();
$inactive_staff = array();

$all_staff_list = staffGetOptionList('all_staff');
$active_staff   = staffGetOptionLisT('id');

$website_list = array();
$query        = $DB->query("SELECT `id`, `website` FROM `highwaygroup`.`websites` ORDER BY `website` ASC");
while ($row = $DB->fetch_assoc($query)) {
    $website_list[$row['id']] = $row['website'];
}

$staff_id = $_GLOBALS['staff']['id'];

$posted = array_merge($_GET, $_POST);

$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value)) {
            unset($get_vars[$key]);
        }
    }
}

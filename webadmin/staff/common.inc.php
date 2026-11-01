<?php
include_once __DIR__ . '/query.inc.php';

/**
 * @var mixed[] $posted
 */
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

global $_GLOBALS;
global $DB;

moduleOverride('staff', __DIR__ . '/query.inc.php');
moduleLoad('staff');

moduleLoad('timesheet');
moduleLoad('permission');

$allowed_sections = get_allowed_sections($_GLOBALS['staff']['user']);

$all_staff_list = array();
$active_staff   = array();
$inactive_staff = array();
$query          = $DB->query("SELECT `id`, CONCAT_WS(' ', `first_name`, `last_name`) AS `name`, `active` FROM `highwaygroup`.`users` ORDER BY `last_name` ASC");
while ($row = $DB->fetch_assoc($query)) {
    $all_staff_list[$row['id']] = $row['name'];
    if ('y' == $row['active']) {
        $active_staff[$row['id']] = $row['name'];
    } else {
        $inactive_staff[$row['id']] = $row['name'];
    }
}
$DB->free_result($query);

$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value)) {
            unset($get_vars[$key]);
        }
    }
}

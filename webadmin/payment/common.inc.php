<?php
include_once __DIR__ . '/query.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

moduleOverride('payment', __DIR__ . '/query.inc.php');
moduleLoad('payment');

global $_GLOBALS;
global $DB;

$all_staff_list = array();
if ($query = $DB->query(sprintf(
	"SELECT `id`, CONCAT_WS(' ', `first_name`, `last_name`) AS `name` FROM `highwaygroup`.`users` ORDER BY `first_name` ASC"
))) {
	while ($row = $DB->fetch_assoc($query)) {
		$all_staff_list[$row['id']] = $row['name'];
	}
	$DB->free_result($query);
}

if ($row = $DB->query_first(sprintf(
	"SELECT 1 FROM `highwaygroup`.`users` WHERE `user` = %s LIMIT 1",
	$DB->escape($_GLOBALS['staff']['user'])
), MYSQLI_ASSOC)) {
    die('Staff validation problem - No match found in highwaygroup webadmin');
}

$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value)) {
            unset($get_vars[$key]);
        }
    }
}

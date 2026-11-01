<?php
//Restrict to current website if not highwaygroup.net
if (array_key_exists("site_settings", $_GLOBALS) && array_key_exists("site_id", $_GLOBALS["site_settings"]) && !in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) $_GET["site_id"] = $_GLOBALS["site_settings"]["site_id"];

$get_vars = $posted;
if (count($get_vars) > 0) foreach($get_vars as $key => $value) if (is_array($value)) unset($get_vars[$key]);

$all_staff_list = array();
$active_staff = array();
$inactive_staff = array();
$query = $DB->query("SELECT `user`, `id`, CONCAT_WS(' ', `first_name`, `last_name`) AS `name`, `active` FROM `highwaygroup`.`users` ORDER BY `user` ASC");
while ($row = $DB->fetch_assoc($query))
{
	$all_staff_list[$row['user']] = $row['name'];
	if ($row['active'] == 'y') $active_staff[$row['user']] = $row['name'];
	else $inactive_staff[$row['user']] = $row['name'];
}
$DB->free_result($query);
?>
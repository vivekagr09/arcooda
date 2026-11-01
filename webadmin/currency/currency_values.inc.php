<?php
global $_GLOBALS;

$all_staff_list=array();
$active_staff=array();
$inactive_staff=array();

$query=$DB->query("SELECT `id`, CONCAT_WS(' ', `first_name`, `last_name`) AS `name` FROM `highwaygroup`.`users` ORDER BY `first_name`");
while($row=$DB->fetch_assoc($query))
{
	$all_staff_list[$row['id']]=$row['name'];
//	if($row['active']=='y')
		$active_staff[$row['id']]=$row['name'];
//	else
//		$inactive_staff[$row['id']]=$row['name'];
}

$staff_id = $_GLOBALS['staff']['id'];

$posted = array_merge($_GET, $_POST);

$get_vars=$posted;
if(count($get_vars)>0)
	foreach($get_vars as $key=>$value)
		if(is_array($value))
			unset($get_vars[$key]);

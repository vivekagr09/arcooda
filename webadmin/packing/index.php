<?php
include_once('packing_common.inc.php');

$action=$posted['action'];
$packing_id=intval($posted['packing_id']);

//Update Actions
switch($action)
{
case 'do_add':
case 'do_edit':
}

$get_vars=$posted;
$keep_only=array('packing_id', 'order_by', 'search_for', 'page_id', 'order', 'return_message', 'container_list', 'packing_action');
if(count($get_vars)>0)
	foreach($get_vars as $key=>$value)
		if(!in_array($key,$keep_only))
			unset($get_vars[$key]);

//Redirect Actions
switch($action)
{
default:
//	header('Location: packing_search.php?'.http_build_query($get_vars));
	break;
}

die();
?>
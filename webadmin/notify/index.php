<?php
include_once('notify_common.inc.php');

$action=$posted['action'];

//Update Actions
switch($action)
{
case 'do_add':
	unset($posted['notify_id']);
	$get_vars['notify_id']=notifyToDatabase($posted);
	$action='list';
	break;
case 'do_options':
	$notify_options=notifyOptionsFromPost($posted);
	notifyOptionsToDatabase($notify_options);
	$action='options';
	break;
}

$keep_only=array('notify_id','site_id','order_by','search_for','page','return_message');
if(count($get_vars)>0)
	foreach($get_vars as $key=>$value)
		if(!in_array($key,$keep_only))
			unset($get_vars[$key]);

//Redirect Actions
switch($action)
{
case 'add':
	unset($get_vars['notify_id']);
	header('Location: notify_add.php?'.http_build_query($get_vars));
	break;
case 'search':
	header('Location: notify_search.php?'.http_build_query($get_vars));
	break;
case 'delete':
	header('Location: notify_delete.php?'.http_build_query($get_vars));
	break;
case 'select':
	header('Location: notify_select.php?'.http_build_query($get_vars));
	break;
case 'list':
	unset($get_vars['notify_id']);
	header('Location: notify_list.php?'.http_build_query($get_vars));
	break;
case 'options':
default:
	header('Location: notify_options.php?'.http_build_query($get_vars));
	break;
}

die();
?>
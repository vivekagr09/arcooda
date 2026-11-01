<?php
include_once('currency_common.inc.php');

$action=$posted['action'];

//Update Actions
switch($action)
{
case 'do_add':
case 'do_edit':
	$action='edit';
	break;
case 'do_activate':
case 'do_deactivate':
case 'do_toggle':
	$action='list';
	break;
}

$get_vars=$posted;
$keep_only=array('order_by', 'search_for', 'page_id', 'order', 'code', 'amount_from', 'currency_from', 'currency_to', 'code_first_letter', 'name_first_letter');
if(count($get_vars)>0)
	foreach($get_vars as $key=>$value)
		if(is_array($value) || !in_array($key,$keep_only))
			unset($get_vars[$key]);

//Redirect Actions
switch($action)
{
//case 'add':
//case 'delete':
case 'edit':
case 'view':
	header('Location: currency_view.php?'.http_build_query($get_vars));
	break;
case 'graph':
	header('Location: currency_graph.php?'.http_build_query($get_vars));
	break;
case 'history':
	header('Location: currency_history.php?'.http_build_query($get_vars));
	break;
default:
case 'search':
case 'list':
	header('Location: currency_list.php?'.http_build_query($get_vars));
	break;
}

die();
?>
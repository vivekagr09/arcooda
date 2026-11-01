<?php
include_once('products_common.inc.php');

moduleLoad('product');

$posted = array_merge($_GET, $_POST);

$action=$posted['action'];

//Update Actions
switch($action)
{
case 'do_add':
	unset($posted['product_id']);
case 'do_edit':
	$summary=productFromPost($posted);
	$posted['product_id']=productToDatabase($summary);
	$action='edit';
	break;
case 'do_delete':
case 'do_deactivate':
case 'do_activate':
	break;
}

$get_vars=$posted;
$keep_only=array('product_id','order_by','search_for','page','order','currency','return_message');
if(count($get_vars)>0)
	foreach($get_vars as $key=>$value)
		if(is_array($value) || !in_array($key,$keep_only))
			unset($get_vars[$key]);

//Redirect Actions
switch($action)
{
case 'add':
	unset($get_vars['product_id']);
//	header('Location: products_add.php?'.http_build_query($get_vars));
	break;
case 'edit':
//	header('Location: products_edit.php?'.http_build_query($get_vars));
	break;
case 'select':
	header('Location: products_return.php?'.http_build_query($get_vars));
	break;
case 'list':
default:
	unset($get_vars['products_id']);
	header('Location: products_list.php?'.http_build_query($get_vars));
}

die();
?>
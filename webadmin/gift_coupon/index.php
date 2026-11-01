<?php
include_once dirname(__FILE__) . "/common.inc.php";

$action=$posted['action'];
$staff_id=$_GLOBALS['staff']['staff_id'];
if($staff_id=='') $_GLOBALS['staff']['staff_id']=$staff_id=staffFromUser($_SERVER['REMOTE_USER']);
$id=intval($posted['id']);

//Update Actions
switch($action)
{
case 'do_preset':


	$preset=giftcouponPresetFromPost($posted);

	/*echo "<pre>";
	print_r($preset);
	echo "</pre>";
	die();*/

	$posted['preset_id']=$preset_id=giftcouponPresetToDatabase($preset);
	$posted['update']	= true;
	$action='preset_edit';
	break;
case 'do_add':
	unset($posted['id']);
case 'do_edit':
	$giftcoupon=giftcouponFromPost($posted);

	$posted['id']=$id=giftcouponToDatabase($giftcoupon);
	$action='edit';
	break;
case 'lock':
	giftcouponSetLock($id,$staff_id);
	$action='list';
	break;
case 'unlock':
	giftcouponSetLock($id);
	$action='list';
	break;
}

$get_vars=$posted;
//print_r($get_vars);
$keep_only=array('id', 'preset_id', 'search_for', 'order_by', 'order', 'page', 'return_message', 'item_list','update');
if(count($get_vars)>0)
	foreach($get_vars as $key=>$value)
		if(is_array($value) || !in_array($key,$keep_only))
			unset($get_vars[$key]);

switch($action)
{
case 'add':
	header('Location: add.php?'.http_build_query($get_vars));
	break;
case 'edit':
	$giftcoupon_lock=giftcouponGetLock($id);
	if($giftcoupon_lock===false || $giftcoupon_lock['staff_id']==$staff_id)
		giftcouponSetLock($id,$staff_id);
	header('Location: edit.php?'.http_build_query($get_vars));
	break;
case 'preset':
	header('Location: preset_list.php?'.http_build_query($get_vars));
	break;
case 'preset_add':
	header('Location: preset.php?'.http_build_query($get_vars));
	break;
case 'preset_edit':
	header('Location: preset.php?'.http_build_query($get_vars));
	break;
case 'search':
	header('Location: search.php?'.http_build_query($get_vars));
	break;
case 'select':
	header('Location: select.php?'.http_build_query($get_vars));
	break;
case 'list':
default:
	unset($get_vars['id']);
	header('Location: list.php?'.http_build_query($get_vars));
}

die();
?>
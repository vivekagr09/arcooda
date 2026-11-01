<?php
include_once __DIR__ . "/common.inc.php";

$action=$posted['action'];

//Processing Switch-case
switch($action)
{
case 'do_edit':
	$content = customTemplateToDatabase($posted);
	$action = 'edit';
	break;
case 'do_add':
	unset($posted['id']);
    // print_r($posted); die;
	$firmwareid=customTemplateToDatabase($posted);
	$action = 'list';
	break;
case 'delete':
	customTemplateDeleteFromDatabase($posted['id']);
	$action = 'list';
	break;
case 'do_toggle':
	$id = $_REQUEST['id'];
	customTemplateSetValue($id, 'status', (customTemplateHasValue($id, 'status', 'active')) ? 'deactive' : 'active');

	$action = 'list';
	break;
}
$keep_only=array('id');
$get_vars=$posted;

if(count($get_vars)>0) {
	foreach($get_vars as $key=>$value) {
		if(is_array($value) || !in_array($key,$keep_only))
			unset($get_vars[$key]);
	}
}
//Redirect Actions
switch($action)
{
case 'add':
	redirect('add.php?'.http_build_query($get_vars));
	break;
case 'edit':
	redirect('edit.php?'.http_build_query($get_vars));
	break;
case 'list':
default:
	redirect('list.php?'.http_build_query($get_vars));
}
?>
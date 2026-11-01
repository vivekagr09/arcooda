<?php
include_once('common.inc.php');

$action=$posted['action'];
$staff_id=$_GLOBALS['staff']['id'];
$id=intval($posted['id']);

//Update Actions
switch($action)
{
case 'do_add':
	unset($posted['weeklyspecial']['id']);
case 'do_edit':
	if (is_array($posted['weeklyspecial']))
	{
		$id = $posted['weeklyspecial']['id'];
		$is_new = !$section_handler->exists($id);
		$old_weeklyspecial = $section_handler->fromDatabase($id);
		$new_weeklyspecial = $section_handler->fromPost($posted['weeklyspecial']);
		$id = $section_handler->toDatabase($new_weeklyspecial);

		$new_weeklyspecial['id'] = $posted['id'] = $id;

		$delta = array();

		if(isset($posted['weeklyspecial_products_affected']))
		{
			$weeklyspecial_products=$section_handler->productsFromPost($posted);
			$section_handler->productsToDatabase($id,$weeklyspecial_products);
		}

		foreach($new_weeklyspecial as $key => $value) if ($old_weeklyspecial[$key] != $new_weeklyspecial[$key] && !is_array($new_weeklyspecial[$key]) && !is_array($old_weeklyspecial[$key])) $delta[] = $key . ' changed from: ' . $DB->escape($old_weeklyspecial[$key]) . ' to: ' . $DB->escape($new_weeklyspecial[$key]);

		if ($posted['comments'] != '')
		{
			$section_handler->commentToDatabase($id, $posted['comments']);
			$delta[] = 'Comment added: ' . $DB->escape($posted['comments']);
		}

		if (count($delta)) $section_handler->commentToDatabase($id, "Changes Made: \n" . implode("\n", $delta), 'sql');

		$action = 'edit';
		if($posted['return_message']!='') $action='select';
	}
	else
	{
		$action = 'error';
	}
	break;
case 'unlock':
	$section_handler->setLock($id);
	$action='list';
	break;
case 'toggle':
	$section_handler->setValue($id,'active',(($section_handler->getValue($id,'active')=='y')?'n':'y'));
	$action='list';
	break;
}

$get_vars = $posted;
$keep_only = array_keys($section_handler->getOptionList('all_fields'));
$keep_only[] = 'order_by';
$keep_only[] = 'search_for';
$keep_only[] = 'page_id';
$keep_only[] = 'order';
$keep_only[] = 'multiple';
$keep_only[] = 'return_message';
if (count($get_vars) > 0) foreach($get_vars as $key => $value) if (is_array($value) || !in_array($key, $keep_only)) unset($get_vars[$key]);

switch($action)
{
case 'edit':
	$section_handler->setLock($posted['id'],$_GLOBALS['staff']['staff_id']);
case 'search':
case 'comments':
case 'select':
case 'view':
	header('Location: '.$action.'.php?'.http_build_query($get_vars));
	break;
default:
	$action='list';
case 'add':
case 'list':
	unset($get_vars['id']);
	header('Location: '.$action.'.php?'.http_build_query($get_vars));
	break;
}
?>
<?php
include_once('workmanual_common.inc.php');

$action=$posted['action'];

//Update Actions
switch($action)
{
case 'do_add':
	unset($posted['manual']['manual_id']);
case 'do_edit':
	$manual_id=$posted['manual']['manual_id'];
	$new_workmanual=workmanualFromPost($posted['manual']);
	$manual_id=workmanualToDatabase($new_workmanual);
	$posted['manual_id']=$manual_id;

	$action='list';
	break;

case 'do_chapter_add':
	unset($posted['chapter']['chapter_id']);
	$posted['chapter']['chapter_rank']=count(workmanualChapterSearch($posted['chapter']['manual_id']))+1;
case 'do_chapter_edit':
	$chapter_id=$posted['chapter']['chapter_id'];
	$new_chapter=workmanualChapterFromPost($posted['chapter']);
	$chapter_id=workmanualChapterToDatabase($new_chapter);
	$posted['chapter_id']=$chapter_id;
	$posted['manual_id']=$new_chapter['manual_id'];

	if(isset($posted['page']))
	{
		$page_id=$posted['page']['page_id'];
		$posted['page']['chapter_id']=$chapter_id;
		$new_page=workmanualPageFromPost($posted['page']);
		$page_id=workmanualPageToDatabase($new_page);
	}

	if(is_array($posted['page_id']))
		foreach($posted['page_id'] as $key=>$page_id)
		{
			$rank=max(1,$posted['page_rank'][$key]);
			if(workmanualPageExists($page_id))
				$DB->query("UPDATE `highwaygroup`.`workmanual_pages` SET page_rank=".intval($rank)." WHERE page_id=".intval($page_id)." LIMIT 1");
		}

	$action='chapter_edit';
	break;

case 'do_chapter_delete':
	$chapter_id=$posted['chapter_id'];
	workmanualChapterDelete($chapter_id);
	$action='view';
	break;

case 'do_chapter_moveup':
case 'do_chapter_movedown':
	$chapter_id=$posted['chapter_id'];
	$chapter=workmanualChapterFromDatabase($chapter_id);
	$manual_id=$chapter['manual_id'];

	$chapter_list=workmanualChapterSearch($manual_id);
	$old_index=array_search($chapter_id,$chapter_list);
	$new_index=max(0,$old_index-1);
	if($action=='do_chapter_movedown')
		$new_index=min(count($chapter_list)-1,$old_index+1);

	$chapter_list[$old_index]=$chapter_list[$new_index];
	$chapter_list[$new_index]=$chapter_id;

	foreach($chapter_list as $rank=>$chapter_id)
		$DB->query("UPDATE `highwaygroup`.`workmanual_chapters` SET chapter_rank=".($rank+1)." WHERE chapter_id=".intval($chapter_id)." LIMIT 1");

	$action='view';
	break;
}

$get_vars=$posted;
$keep_only=array('order_by', 'search_for', 'page_id', 'order', 'manual_id', 'chapter_id');
if(count($get_vars)>0)
	foreach($get_vars as $key=>$value)
		if(is_array($value) || !in_array($key,$keep_only))
			unset($get_vars[$key]);

//Redirect Actions
switch($action)
{
case 'add':
	unset($get_vars['manual_id']);
	header('Location: workmanual_add.php?'.http_build_query($get_vars));
	break;
case 'edit':
	header('Location: workmanual_edit.php?'.http_build_query($get_vars));
	break;
case 'delete':
	header('Location: workmanual_delete.php?'.http_build_query($get_vars));
	break;
case 'view':
	header('Location: workmanual_view.php?'.http_build_query($get_vars));
	break;

case 'chapter_add':
	unset($get_vars['chapter_id']);
	header('Location: workmanual_chapter_add.php?'.http_build_query($get_vars));
	break;
case 'chapter_edit':
	header('Location: workmanual_chapter_edit.php?'.http_build_query($get_vars));
	break;
case 'chapter_delete':
	header('Location: workmanual_chapter_delete.php?'.http_build_query($get_vars));
	break;
case 'chapter_view':
	if(is_array($posted['chapter_list']))
		$get_vars['chapter_id']=implode('|',$posted['chapter_list']);
	header('Location: workmanual_chapter_view.php?'.http_build_query($get_vars));
	break;

case 'page_add':
	unset($get_vars['page_id']);
	header('Location: workmanual_page_add.php?'.http_build_query($get_vars));
	break;
case 'page_edit':
	header('Location: workmanual_page_edit.php?'.http_build_query($get_vars));
	break;
case 'page_delete':
	header('Location: workmanual_page_delete.php?'.http_build_query($get_vars));
	break;
case 'page_view':
	header('Location: workmanual_page_view.php?'.http_build_query($get_vars));
	break;

case 'summary':
	if(is_array($posted['chapter_list']))
		$get_vars['chapter_id']=implode('|',$posted['chapter_list']);
	header('Location: workmanual_summary.php?'.http_build_query($get_vars));
	break;
case 'search':
	header('Location: workmanual_search.php?'.http_build_query($get_vars));
	break;
default:
case 'list':
	header('Location: workmanual_list.php?'.http_build_query($get_vars));
	break;
}

die();
?>
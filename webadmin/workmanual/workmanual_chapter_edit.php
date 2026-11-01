<?php
include_once('workmanual_common.inc.php');
include_once('templates/workmanual_header.tpl.php');

$chapter_id=intval($posted['chapter_id']);
$chapter=workmanualChapterFromDatabase($chapter_id);

$page_id='';
$page=array();
$page_list=workmanualPageSearch(array('chapter_id'=>$chapter_id, 'page_rank'=>0));
if(count($page_list)>0)
{
	$page_id=reset($page_list);
	$page=workmanualPageFromDatabase($page_id);
}
$page_list=workmanualPageSearch(array('chapter_id'=>$chapter_id));

$manual_list=array();
foreach(workmanualSearch() as $manual_id)
{
	$manual=workmanualFromDatabase($manual_id);
	$manual_list[$manual_id]=$manual['manual_title'];
}

$doedit_url=$get_vars;
$doedit_url['action']='do_chapter_edit';
$doedit_url='index.php?'.http_build_query($doedit_url);

$action='edit';
?>
<form action="<?= $doedit_url ?>" method="post">
<?php
include_once('templates/workmanual_chapter_addedit.tpl.php');
?>
</form>
<?php
include_once('templates/workmanual_footer.tpl.php');
?>
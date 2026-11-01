<?php
include_once('project_common.inc.php');
include_once('templates/project_header.tpl.php');

$chapter_id=intval($posted['chapter_id']);
$chapter=projectChapterFromDatabase($chapter_id);

$page_id='';
$page=array();
$page_list=projectPageSearch(array('chapter_id'=>$chapter_id, 'page_rank'=>0));
if(count($page_list)>0)
{
	$page_id=reset($page_list);
	$page=projectPageFromDatabase($page_id);
}
$page_list=projectPageSearch(array('chapter_id'=>$chapter_id));

$project_list=array();
foreach(projectSearch() as $project_id)
{
	$project=projectFromDatabase($project_id);
	$project_list[$project_id]=$project['project_title'];
}

$doedit_url=$get_vars;
$doedit_url['action']='do_chapter_edit';
$doedit_url='index.php?'.http_build_query($doedit_url);

$action='edit';
?>
<form action="<?php echo $doedit_url ?>" method="post">
<?php
include_once('templates/project_chapter_addedit.tpl.php');
?>
</form>
<?php
include_once('templates/project_footer.tpl.php');
?>
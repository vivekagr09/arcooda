<?php
include_once('project_common.inc.php');
include_once('templates/project_header.tpl.php');

$chapter_id=intval($posted['chapter_id']);
$chapter=projectChapterFromDatabase($chapter_id);

$page_list=projectPageSearch(array('chapter_id'=>$chapter_id));

$project_list=array();
foreach(projectSearch() as $project_id)
{
	$project=projectFromDatabase($project_id);
	$project_list[$project_id]=$project['project_title'];
}

$dodelete_url=$get_vars;
$dodelete_url['action']='do_chapter_delete';
$dodelete_url='index.php?'.http_build_query($dodelete_url);

$action='delete';
if(count($page_list)>1)
{
?>
<h1>You cannot delete this chapter:</h1>
<h2><?php echo htmlspecialchars($chapter['chapter_title']) ?></h2>

Because it contains attachments<br /><br />

<?php echo print_table($chapter,1) ?>

<input type="button" onclick="javascript:history.back();" value="Cancel" />

<?php
}
else
{
?>
<form action="<?php echo $dodelete_url ?>" method="post">
<h1>Are you sure you wish to delete chapter:</h1>
<h2><?php echo htmlspecialchars($chapter['chapter_title']) ?></h2>

<?php echo print_table($chapter,1) ?>

<input type="button" onclick="javascript:this.form.submit();" value="Delete" />
<input type="button" onclick="javascript:window.close();" value="Cancel" />

</form>
<?php
}
include_once('templates/project_footer.tpl.php');

?>
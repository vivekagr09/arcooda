<?php
include_once('workmanual_common.inc.php');
include_once('templates/workmanual_header.tpl.php');

$chapter_id=intval($posted['chapter_id']);
$chapter=workmanualChapterFromDatabase($chapter_id);

$page_list=workmanualPageSearch(array('chapter_id'=>$chapter_id));

$manual_list=array();
foreach(workmanualSearch() as $manual_id)
{
	$manual=workmanualFromDatabase($manual_id);
	$manual_list[$manual_id]=$manual['manual_title'];
}

$dodelete_url=$get_vars;
$dodelete_url['action']='do_chapter_delete';
$dodelete_url='index.php?'.http_build_query($dodelete_url);

$action='delete';
if(count($page_list)>1)
{
?>
<h1>You cannot delete this chapter:</h1>
<h2><?= htmlspecialchars($chapter['chapter_title']) ?></h2>

Because it contains attachments<br /><br />

<?= print_table($chapter,1) ?>

<input type="button" onclick="javascript:history.back();" value="Cancel" />

<?php
}
else
{
?>
<form action="<?= $dodelete_url ?>" method="post">
<h1>Are you sure you wish to delete chapter:</h1>
<h2><?= htmlspecialchars($chapter['chapter_title']) ?></h2>

<?= print_table($chapter,1) ?>

<input type="button" onclick="javascript:this.form.submit();" value="Delete" />
<input type="button" onclick="javascript:window.close();" value="Cancel" />

</form>
<?php
}
include_once('templates/workmanual_footer.tpl.php');

?>
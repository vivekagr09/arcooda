<?php
include_once('project_common.inc.php');
include_once('templates/project_header.tpl.php');

$chapter_id='';
$chapter=array();
$chapter['project_id']=$posted['project_id'];

$page_id='';
$page=array();

$project_list=array();
foreach(projectSearch() as $project_id)
{
	$project=projectFromDatabase($project_id);
	$project_list[$project_id]=$project['project_title'];
}

$doadd_url=$get_vars;
$doadd_url['action']='do_chapter_add';
$doadd_url='index.php?'.http_build_query($doadd_url);

$action='add';
?>
<form action="<?php echo $doadd_url ?>" method="post">
<?php
include_once('templates/project_chapter_addedit.tpl.php');
?>
</form>
<?php
include_once('templates/project_footer.tpl.php');
?>
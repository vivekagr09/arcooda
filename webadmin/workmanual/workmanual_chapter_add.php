<?php
include_once('workmanual_common.inc.php');
include_once('templates/workmanual_header.tpl.php');

$chapter_id='';
$chapter=array();
$chapter['manual_id']=$posted['manual_id'];

$page_id='';
$page=array();

$manual_list=array();
foreach(workmanualSearch() as $manual_id)
{
	$manual=workmanualFromDatabase($manual_id);
	$manual_list[$manual_id]=$manual['manual_title'];
}

$doadd_url=$get_vars;
$doadd_url['action']='do_chapter_add';
$doadd_url='index.php?'.http_build_query($doadd_url);

$action='add';
?>
<form action="<?= $doadd_url ?>" method="post">
<?php
include_once('templates/workmanual_chapter_addedit.tpl.php');
?>
</form>
<?php
include_once('templates/workmanual_footer.tpl.php');
?>
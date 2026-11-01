<?php
include_once('project_common.inc.php');
include_once('templates/printout_header.tpl.php');

$project_id=intval($posted['project_id']);
$project=projectFromDatabase($project_id);

$chapter_list=explode('|',$posted['chapter_id']);
if(count($chapter_list)==0 || reset($chapter_list)==0)
	$chapter_list=projectChapterSearch(array('project_id'=>$project_id));

$layout=array();
foreach($chapter_list as $chapter_id)
	$layout[$chapter_id]=projectPageSearch(array('chapter_id'=>$chapter_id));

$action='print';
?>
<div style="width:100%;text-align:left;">
	<h1><?php echo formatHTMLTitle($project['project_title']) ?></h1>
<?php
foreach($layout as $chapter_id=>$page_list)
{
	$chapter=projectChapterFromDatabase($chapter_id);
?>
	<h2><?php echo intval($chapter['chapter_rank']) ?>. <?php echo htmlspecialchars($chapter['chapter_title']) ?></h2>
<?php
}
?>
</div>
<?php

include_once('templates/printout_footer.tpl.php');
?>
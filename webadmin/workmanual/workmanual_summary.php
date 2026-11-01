<?php
include_once('workmanual_common.inc.php');
include_once('templates/printout_header.tpl.php');

$manual_id=intval($posted['manual_id']);
$manual=workmanualFromDatabase($manual_id);

$chapter_list=explode('|',$posted['chapter_id']);
if(count($chapter_list)==0 || reset($chapter_list)==0)
	$chapter_list=workmanualChapterSearch(array('manual_id'=>$manual_id));

$layout=array();
foreach($chapter_list as $chapter_id)
	$layout[$chapter_id]=workmanualPageSearch(array('chapter_id'=>$chapter_id));

$action='print';
?>
<div style="width:100%;text-align:left;">
	<h1><?= formatHTMLTitle($manual['manual_title']) ?></h1>
<?php
foreach($layout as $chapter_id=>$page_list)
{
	$chapter=workmanualChapterFromDatabase($chapter_id);
?>
	<h2><?= intval($chapter['chapter_rank']) ?>. <?= htmlspecialchars($chapter['chapter_title']) ?></h2>
<?php
}
?>
</div>
<?php

include_once('templates/printout_footer.tpl.php');
?>
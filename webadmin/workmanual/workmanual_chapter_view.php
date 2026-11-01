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
{
	$row=$DB->query_first("SELECT view_id FROM `highwaygroup`.`workmanual_views` WHERE staff_id=".intval($staff_id)." AND chapter_id=".intval($chapter_id));
	if($row)
		$DB->query("UPDATE `highwaygroup`.`workmanual_views` SET `view_date` = NOW() WHERE `view_id` = " . intval($row['view_id']));
	else
		$DB->query("INSERT INTO `highwaygroup`.`workmanual_views` (`staff_id`, `chapter_id`, `view_date`) VALUES (" . intval($staff_id) . ", " . intval($chapter_id) . ", NOW())");
	$layout[$chapter_id]=workmanualPageSearch(array('chapter_id'=>$chapter_id));
}

$action='print';

?>
<div style="width:100%;text-align:left;">
<?php
include_once('templates/workmanual_print.tpl.php');
?>
</div>
<?php

include_once('templates/printout_footer.tpl.php');
?>
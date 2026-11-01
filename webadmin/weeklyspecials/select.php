<?php
include_once('common.inc.php');
include_once('templates/header.tpl.php');

$action='select';
$id=intval($posted['id']);
$staff_id=$_GLOBALS['staff']['id'];

$weeklyspecial=$section_handler->fromDatabase($id);
$weeklyspecial_lock=$section_handler->getLock($id);
$weeklyspecial_comments=$section_handler->commentSearch(array('weeklyspecial_id'=>$id,'comment_type'=>'comment'));

$weeklyspecial['lock']=$weeklyspecial_lock;
$weeklyspecial['comments']=$weeklyspecial_comments;
?>
<h2>Select Weekly Special <?= $section_handler->formatNumber($id) ?> - <?= date('d/m/Y',time()) ?></h2>

<script type="text/javascript">
//<![CDATA[
var data = <?=json_encode($weeklyspecial)?>;
if(window.opener!=null && window.opener.setWeeklySpecialData!=null)
	window.opener.setWeeklySpecialData(data);
else
	alert('Unable to send WeeklySpecial back to form');
window.close();
//]]>
</script>

<input type="button" onclick="window.close()" value="Close Window" />
<?php

include_once('templates/footer.tpl.php');
?>
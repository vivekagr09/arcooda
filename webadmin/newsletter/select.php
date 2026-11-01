<?php
include_once('common.inc.php');
include_once('templates/header.tpl.php');

$action='select';
$id=intval($posted['id']);
$staff_id=$_GLOBALS['staff']['id'];

$item=$section_handler->fromDatabase($id);
$item_lock=$section_handler->getLock($id);
$item_comments=$section_handler->commentSearch(array($section_handler->section_name.'_id'=>$id,'comment_type'=>'comment'));

$item['lock']=$item_lock;
$item['comments']=$item_comments;
?>
<h2>Select Newsletter <?php echo $section_handler->formatNumber($id) ?> - <?php echo date('d/m/Y',$_SERVER['REQUEST_TIME']) ?></h2>

<script type="text/javascript">
//<![CDATA[
var data = <?php jecho($item) ?>;
if (window.opener != null && window.opener.setNewsletterData != null) window.opener.setNewsletterData(data);
else alert('Unable to send Newsletter back to form');
window.close();
//]]>
</script>

<input type="button" onclick="window.close()" value="Close Window" />
<?php

include_once('templates/footer.tpl.php');
?>
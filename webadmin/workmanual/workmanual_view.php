<?php
include_once('workmanual_common.inc.php');
include_once('templates/workmanual_header.tpl.php');

$manual_id=intval($posted['manual_id']);
$manual=workmanualFromDatabase($manual_id);
$manual_chapters=workmanualChapterSearch(array('manual_id'=>$manual_id));

$doprint_url=$get_vars;
$doprint_url['action']='chapter_view';
$doprint_url='index.php?'.http_build_query($doprint_url);

$action='view';
?>
<form action="<?= $doprint_url ?>" method="post" target="_blank">
<?php
include_once('templates/workmanual_view.tpl.php');
?>
</form>
<?php
include_once('templates/workmanual_footer.tpl.php');
?>
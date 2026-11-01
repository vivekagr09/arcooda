<?php
include_once('workmanual_common.inc.php');
include_once('templates/workmanual_header.tpl.php');

$manual_id='';
$manual=array();

$doadd_url=$get_vars;
$doadd_url['action']='do_add';
$doadd_url='index.php?'.http_build_query($doadd_url);

$action='add';
?>
<form action="<?= $doadd_url ?>" method="post">
<?php
include_once('templates/workmanual_addedit.tpl.php');
?>
</form>
<?php
include_once('templates/workmanual_footer.tpl.php');
?>
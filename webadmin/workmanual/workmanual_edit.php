<?php
include_once('workmanual_common.inc.php');
include_once('templates/workmanual_header.tpl.php');

$manual_id=intval($posted['manual_id']);
$manual=workmanualFromDatabase($manual_id);

$doedit_url=$get_vars;
$doedit_url['action']='do_edit';
$doedit_url='index.php?'.http_build_query($doedit_url);

$action='edit';
?>
<form action="<?= $doedit_url ?>" method="post">
<?php
include_once('templates/workmanual_addedit.tpl.php');
?>
</form>
<?php
include_once('templates/workmanual_footer.tpl.php');
?>
<?php
include_once('project_common.inc.php');
include_once('templates/project_header.tpl.php');

$project_id=intval($posted['project_id']);
$project=projectFromDatabase($project_id);

$doedit_url=$get_vars;
$doedit_url['action']='do_edit';
$doedit_url='index.php?'.http_build_query($doedit_url);

$action='edit';
?>
<form action="<?php echo $doedit_url ?>" method="post">
<?php
include_once('templates/project_addedit.tpl.php');
?>
</form>
<?php
include_once('templates/project_footer.tpl.php');
?>
<?php
include_once('project_common.inc.php');
include_once('templates/project_header.tpl.php');

$project_id='';
$project=array("site_id" => $_GLOBALS["site_settings"]["site_id"]);

$doadd_url=$get_vars;
$doadd_url['action']='do_add';
$doadd_url='index.php?'.http_build_query($doadd_url);

$action='add';
?>
<form action="<?php echo $doadd_url ?>" method="post">
<?php
include_once('templates/project_addedit.tpl.php');
?>
</form>
<?php
include_once('templates/project_footer.tpl.php');
?>
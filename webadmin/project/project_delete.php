<?php
include_once('project_common.inc.php');
include_once('templates/project_header.tpl.php');

$project_id=intval($posted['project_id']);
$project=projectFromDatabase($project_id);

$dodelete_url=$get_vars;
$dodelete_url['action']='do_delete';
$dodelete_url='index.php?'.http_build_query($dodelete_url);

$action='delete';
?>
<form action="<?php echo $dodelete_url ?>" method="post">
	<h1 style="text-align:center;">Delete this Project?</h1>
	<?php echo print_table($project,1) ?>
	<br />
	<br />
	<input type="button" value="Delete" onclick="javascript:this.form.submit();" />
	<input type="button" value="Cancel" onclick="javascript:document.location='index.php';" />
</form>
<?php
include_once('templates/project_footer.tpl.php');
?>
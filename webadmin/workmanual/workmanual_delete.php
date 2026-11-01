<?php
include_once('workmanual_common.inc.php');
include_once('templates/workmanual_header.tpl.php');

$manual_id=intval($posted['manual_id']);
$manual=workmanualFromDatabase($manual_id);

$dodelete_url=$get_vars;
$dodelete_url['action']='do_delete';
$dodelete_url='index.php?'.http_build_query($dodelete_url);

$action='delete';
?>
<form action="<?= $dodelete_url ?>" method="post">
	<h1 style="text-align:center;">Delete this Work Manual?</h1>
	<?= print_table($manual,1) ?>
	<br />
	<br />
	<input type="button" value="Delete" onclick="javascript:this.form.submit();" />
	<input type="button" value="Cancel" onclick="javascript:document.location='index.php';" />
</form>
<?php
include_once('templates/workmanual_footer.tpl.php');
?>
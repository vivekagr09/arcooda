<?php
include_once('softjob_common.inc.php');
include_once('templates/softjob_header.tpl.php');

$softjob_id=intval($posted['softjob_id']);
$softjob=softjobFromDatabase($softjob_id);

$dodelete_url=$get_vars;
$dodelete_url['action']='do_delete';
$dodelete_url='index.php?'.http_build_query($dodelete_url);

$action='delete';
?>
<form accept-charset="utf-8" action="<?= $dodelete_url ?>" method="post">
	<h1 style="text-align:center;">Delete this Job?</h1>
	<?= print_table($softjob,1) ?>
	<br />
	<br />
	<input type="button" value="Delete" onclick="javascript:this.form.submit();" />
	<input type="button" value="Cancel" onclick="javascript:document.location='index.php';" />
</form>
<?php
include_once('templates/view_softjob.tpl.php');
?>
<?php
include_once('templates/softjob_footer.tpl.php');
?>
<?php
include_once('forwarders_common.inc.php');
include_once('templates/forwarders_header.tpl.php');

$forwarder_id=intval($posted['id']);
$forwarder=forwardersFromDatabase($forwarders_id);

$dodelete_url=$get_vars;
$dodelete_url['action']='do_delete';
$dodelete_url='index.php?'.http_build_query($dodelete_url);

$action='delete';
?>
<form accept-charset="utf-8" autocomplete="off" method="post" action="<?php echo $dodelete_url ?>">
	<h1 style="text-align:center;">Delete this Forwarder?</h1>
	<?php echo print_table($forwarder,1) ?>
	<br />
	<br />
	<input type="button" value="Delete" onclick="javascript:this.form.submit();" />
	<input type="button" value="Cancel" onclick="javascript:document.location='index.php';" />
</form>
<?php
include_once('templates/forwarders_view.tpl.php');
?>
<?php
include_once('templates/forwarders_footer.tpl.php');
?>
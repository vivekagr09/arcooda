<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

include '../header.inc.php';
include '../currency.php';
include 'forward_menu.php';

if ("true" == $_POST["do_delete"]) {
	$DB->query(sprintf(
		"DELETE FROM `fowarder` WHERE `fow_id` = %d LIMIT 1",
		$_REQUEST['id']
	));

    print "Deleted Forwarder: {$_POST['id']}";
    include "../footer.inc.php";
}

$info = $DB->query_first(sprintf(
	"SELECT `fow_id`, `fow_company`, `fow_website`, `fow_country`, `fow_contact` FROM `fowarder` WHERE `fow_id` = %d LIMiT 1",
	$_REQUEST['id']
));
?>
<table align="center" cellpadding="10" cellspacing="0">
	<tr>
		<td bgcolor="#FFFFCC">ID</td>
		<td bgcolor="#CCCCCC"><?=$info['fow_id']?></td>
	</tr>
	<tr>
		<td bgcolor="#FFFFCC">Name</td>
		<td bgcolor="#CCCCCC"><?=$info['fow_company']?></td>
	</tr>
	<tr>
		<td bgcolor="#FFFFCC">Website</td>
		<td bgcolor="#CCCCCC"><?=$info['fow_website']?></td>
	</tr>
	<tr>
		<td bgcolor="#FFFFCC">Country</td>
		<td bgcolor="#CCCCCC"><?=$info['fow_country']?></td>
	</tr>
	<tr>
		<td bgcolor="#FFFFCC">Contact</td>
		<td bgcolor="#CCCCCC"><?=$info['fow_contact']?></td>
	</tr>
</table>
<form accept-charset="utf-8" autocomplete="off" method="post" action="del_forwarder.php">
	<p align="center">
		<input type="hidden" name="id" value="<?=$info['fow_id']?>" />
		<input type="hidden" name="do_delete" value="true" />
		<input type="submit" value=" Delete " />
	</p>
</form>
<?php
include '../footer.inc.php';

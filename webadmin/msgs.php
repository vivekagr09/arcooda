<?php

include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

include_once __DIR__ . "/msgs.inc.php";
?>
<table width="100%" border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td class="pageMenu" width="20%">
			<div align="center">
				Messages to Customers
			</div>
		</td>
		<td class="pageMenu" width="80%">
			<div align="right">
				<a href="<?=$_SERVER['PHP_SELF'];?>?action=list">List Messages</a>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
</table>

<?php
if (isset($_REQUEST['id'])) {
    switch ($_REQUEST['action']) {
        case "edit":
            drawEditForm($_REQUEST['id']);
            break;
        case "do_edit":
            $sql = "UPDATE user_msg_strings SET string='" . $_REQUEST['string'] . "' WHERE id='" . $_REQUEST['id'] . "'";
            $DB->query($sql);
        default:
            drawSummary();
    }
} else {
    drawSummary();
}

function drawSummary()
{
    global $DB;
    $shaded = 0;
    $sql    = "SELECT * FROM user_msg_strings";
    $result = $DB->query($sql);
    if ($DB->num_rows($result)) {
?>
<table border="0" width="100%">
	<th>ID</th>
	<th>Title</th>
	<th>Text</th>
	<th>&nbsp;</th>
<?php
while ($row = $DB->fetch_array($result)) {
?>
	<tr class="tableRow<?=$shaded ? "Alt" : "";?>">
		<td><?=$row['id'];?></td>
		<td nowrap><?=$row['name'];?></td>
		<td><?=$row['string'];?></td>
		<td>&nbsp;<a href="<?=$_SERVER['PHP_SELF'];?>?action=edit&id=<?=$row['id'];?>">Edit</a>&nbsp;</td>
	</tr>
<?php
$shaded = $shaded ? 0 : 1;
        }
        echo "</table>";
    } else {
        echo "No Message Strings defined!<br />\r\n";
    }
}

function drawEditForm($msg_id)
{
    global $DB;
    $sql = "SELECT * FROM user_msg_strings WHERE id='$msg_id'";
    $row = $DB->query_first($sql);
?>
<form accept-charset="utf-8" method="post" action="<?=$_SERVER['PHP_SELF'];?>">
<table align="center">
	<tr>
		<td class="left" colspan="2"><div  align="center">Edit Message</div></td>
	</tr>
	<tr>
		<td class="left">Name:&nbsp;</td>
		<td class="right"><?=$row['name'];?></td>
	</tr>
	<tr>
		<td class="left">Text:&nbsp;</td>
		<td class="right"><textarea rows=8 cols=50 name="string"><?=$row['string'];?></textarea></td>
	</tr>
	<tr>
		<td class="left">Note:&nbsp;</td>
		<td class="right">In Signup messages, {email} will be replaced with<br />the user's email address.</td>
	</tr>
	<tr>
		<td class="left" colspan="2"><div  align="center"><input type="submit" name="Submit" value="Update"></div></td>
	</tr>
</table>
<input type="hidden" name="action" value="do_edit">
<input type="hidden" name="id" value="<?=$msg_id;?>">
</form>
<?php
}

?>

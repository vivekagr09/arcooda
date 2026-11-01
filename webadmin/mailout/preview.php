<?php
include_once __DIR__ . '/common.inc.php';

include_once 'email_functions.php';

// VIEW AN ARCHIVED MAILOUT IF ID GIVEN OTHERWISE SHOW THE CURRENT MAILOUT
if ($_GET['view'] == "archive" && $_GET['id'] != "") $email = get_archived_mailout($_GET['id']);
else $email = get_mailout_vars();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" >
<html xmlns="http://www.w3.org/1999/xhtml">
<head><title>Preview</title></head>
<body>

<table width="90%" border="0" align="center" cellpadding="2" cellspacing="0">
	<tr>
		<td width="15%" nowrap="nowrap"><div align="right"><b>Subject : </b></div></td>
		<td width="85%"><?php echo $email['subject'] == "" ? "None" : $email['subject'] ?></td>
	</tr>
	<tr>
		<td nowrap="nowrap"><div align="right"><b>Attachments : </b></div></td>
		<td><?php echo $email['files'] == "" ? "None" : $email['files'] ?></td>
	</tr>
</table>
<br />
<table width="90%" border="0" align="center" cellpadding="2" cellspacing="0">
	<tr><td><hr size="1" width="100%" noshade="noshade" /></td></tr>
	<tr><td><b>Message : HTML Format</b></td></tr>
	<tr><td><table width="90%" border="0" cellspacing="0" cellpadding="10" align="center"><tr><td class="box"><?php echo stripslashes($email['html_message']) ?></td></tr></table></td></tr>
	<tr><td><b>Message : Plain Text</b></td></tr>
	<tr><td><table width="90%" border="0" cellspacing="0" cellpadding="10" align="center"><tr><td class="box"><pre><?php echo stripslashes($email['text_message']) ?></pre></td></tr></table></td></tr>
	<tr><td><hr size="1" width="100%" noshade="noshade" /></td></tr>
</table>

</body>
</html>

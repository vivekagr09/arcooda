<?php
/***********************************************************************
** Title.........:
** Version.......:
** Author........:	Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
** Filename......:
** Last changed..:
** Notes.........:
***********************************************************************/
$num_emails = $DB->query_first("SELECT COUNT(*) AS total FROM mailout_tmp_list");

?>
<table width="100%%" border="0" cellspacing="1" cellpadding="4">
    <tr bgcolor="#EEEEEE">
      <td><strong>Mailout</strong></td>
      <td><div align="right">
			<a href="index.php">Create List</a> |
			<a href="composer.php?action2=new_email">New Email</a> |
			<a href="sender.php?action2=send&resume=yes">Re-send</a> |
			<a href="../generic_admin.php?table=mailout_tmp_list&query=" target="_blank">List Emails (<?= $num_emails['total'] ?>)</a>
		</div></td>
    </tr>
</table>

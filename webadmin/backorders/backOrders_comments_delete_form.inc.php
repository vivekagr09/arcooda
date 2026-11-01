<?php
$comment = $DB->query_first(sprintf(
	"SELECT * FROM `highwaygroup`.`backorder_comment` WHERE `id` = %d LIMIT 1",
	$id
), MYSQLI_ASSOC);

?>
<div style="text-align:center;"><form accept-charset="utf-8" autocomplete="off" method="post" action="backOrders_comments.php?backorder=<?=$backorder?>&id=<?=$comment['id']?>&action=do_delete" name="admin_form"><table style="margin:auto;border-style:none;">
	<tr><td colspan="2">Are you sure you want to delete this comment?</td></tr>
	<TR>
		<TD bgColor=#eeeeee><B>Comment Details</B></TD>
		<td bgColor=#f3f7fc><?=nl2br(htmlspecialchars($comment['comment']))?></td>
	</tr>
	<tr><td colspan=2 style="text-align:center;">
		<input type="submit" name="Delete" value="Delete" />
		<input type="reset" name="clear" value="Clear" />
	</td></tr>
</table></form></div>

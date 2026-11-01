<div style="text-align:center;"><form accept-charset="utf-8" autocomplete="off" method="post" action="backOrders_comments.php?&backorder=<?= $backorder?>&action=do_add" name="admin_form"><table width="95%" align=center border=0>
	<tr><td colspan="2"><?= $error_msg ?>&nbsp;</td></tr>
	<TR><TD noWrap bgColor=#eeeeee><B>Comment Details</B></TD></TR>
	<tr><td><textarea name='comment' style="width:95%;height:100"></textarea></td></tr>
	<tr><td>
		<input type="submit" name="add" value="Add">
		<input type="reset" name="clear" value="Clear">
	</td></tr>
</table></form></div>

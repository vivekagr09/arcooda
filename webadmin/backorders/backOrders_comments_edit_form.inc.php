<?php
$comment = $DB->query_first(sprintf(
    "SELECT * FROM `highwaygroup`.`backorder_comment` WHERE `id` = %d LIMIT 1",
    $id
), MYSQLI_ASSOC);

?>
		<center>
	<form accept-charset="utf-8" autocomplete="off" method="post" action="backOrders_comments.php?backorder=<?= $backorder?>&id=<?= $comment['id']?>&action=do_edit" name="admin_form">
          <table width="95%" align=center border=0>
            <tr>
              <td colspan="2"><?= $error_msg ?>&nbsp;</td>
            </tr>
            <TR>
                <TD noWrap bgColor=#eeeeee><B>Comment Details</B></TD>
			</TR>
			<tr>
				<td><textarea name='comment' style="width:95%;height:100"><?= $comment['comment']?></textarea></td>
			</tr>
			<tr>
			<td>

		<input type="submit" name="edit" value="Update">
        <input type="reset" name="clear" value="Clear">
              </td>
            </tr>
          </table>
	</form>
        </center>

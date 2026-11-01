<?php
/**
 * Expects:
 *  $table
 *  $id
 */

$item = $DB->query_first("SELECT * FROM `" . $table . "_types` WHERE `id` = " . intval($id) . " LIMIT 1", MYSQLI_ASSOC);
?>
<center><table border="0">
<?php
$alt = true;
foreach($item as $k => $v) if (!is_int($k))
{
?>
	<tr bgcolor="<?php echo ($alt = !$alt) ? "#EEEEEE" : "#FFFFFF" ?>">
		<td align="right"><b><?php hecho($k) ?>:</b></td>
		<td><?php hecho($v) ?></td>
	</tr>
<?php
}
?>
	<tr><td colspan="2"><center>
		<input type="hidden" name="id" value="<?php hecho($item["id"]) ?>" />
		<input type="hidden" name="action" value="do_delete" />
		<input type="submit" name="add" value="Yes" />
		<input type="reset" name="clear" value="No" onClick="javascript:history.back()" />
	</center></td></tr>
</table></center>

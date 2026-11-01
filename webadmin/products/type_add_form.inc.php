<?php
/**
 * Expects:
 *  $table
 *  $id
 *  $error_msg
 */

$item = $DB->query_first("SELECT * FROM `" . $table . "_types` WHERE `id` = " . intval($id) . " LIMIT 1", MYSQLI_ASSOC);
?>
<center><table border="0" cellpadding="2">
	<tr><td colspan="2"><?php echo $error_msg ?>&nbsp;</td></tr>
	<tr>
		<td nowrap="nowrap" align="right"><b>Item Type:</b></td>
		<td><input type="text" name="type_name" size="20" value="<?php hecho($item["type_name"]) ?>" style="width:400px;" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap" align="right"><b>Item Blurb:</b></td>
		<td><textarea name="type_blurb" cols="20" rows="3" style="width:400px;"><?php hecho($item["blurb"]) ?></textarea></td>
	</tr>
	<tr>
		<td nowrap="nowrap" align="right"><b>Item Type Image:</b></td>
		<td>
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input type="file" name="type_image" style="width:400px;" /> (50x50 Pixels)
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" align="right"><b>Type Header Image:</b></td>
		<td>
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input type="file" name="header_image" style="width:400px;" /> (200x50 Pixels)
		</td>
	</tr>
	<tr><td colspan="2"><center>
		<input type="hidden" name="id" value="<?php hecho($item["id"]) ?>" />
		<input type="hidden" name="action" value="do_add" />
		<input type="submit" name="add" value="Add" />
		<input type="reset" name="clear" value="Clear" />
	</center></td></tr>
</table></center>

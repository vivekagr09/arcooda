<form action="plugs.php" method="post" enctype="multipart/form-data"><table border="0" align="center" cellpadding="4" cellspacing="0">
	<tr>
		<td nowrap="nowrap" class="bottom">&nbsp;</td>
		<td class="bottom">&nbsp;</td>
	</tr>
	<tr>
		<td class="left" nowrap="nowrap"><b>Plug Type :</b></td>
<?php
include_once '../world_functions.php';
$str = "SELECT * FROM `world`.`plug_types` WHERE id = '$id'";
$res = $DB->query_first($str);
?>
		<td class="right"><input type="text" name="data[value]" value="<?php hecho($res['value']) ?>" size="40" style="width:400px;" /></td>
	</tr>
	<tr>
		<td class="left" nowrap="nowrap"><b>Plug Image :</b></td>
		<td class="right"><img src="../images/plug_<?php hecho($res['id']) ?>_0xfa.jpg" /></td>
	</tr>
	<tr>
		<td class="left" nowrap="nowrap"><b>File Name :</b></td>
		<td class="right"><input type="text" value="<?php hecho(basename($res['file_path'])) ?>" style="width:400px" readonly="readonly" /></td>
	</tr>
	<tr>
		<td class="left"><b><span style="color:#f00;font-size:7px;vertical-align:top;">*</span>New Image :</td>
		<td class="right">
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input type="file" name="new_plug" value="<?php hecho($res['file_path']) ?>" style="width:400px;" />
		</td>
	</tr>
	<tr>
		<td colspan="2" class="top"><span style="color: #f00; font-size: 7px; vertical-align: top;">*</span><b>Caution: Uploading new image will overwrite the old image</b></td>
	</tr>
	<tr>
		<td nowrap="nowrap">&nbsp;</td>
		<td><p>
			<input type="hidden" name="id" value="<?php hecho($id) ?>" />
			<input type="hidden" name="action" value="do_edit" />
			<input type="submit" name="do_action" value="Update" />
			<input type="reset" name="clear" value="Clear" />
		</p></td>
	</tr>
</table></form>

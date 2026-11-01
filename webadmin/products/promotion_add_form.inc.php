<!-- promotion_add_form.inc -->

<form action="/webadmin/products/promotion_admin.php?popup" method="post" enctype="multipart/form-data">
	<table border="0" align="center">

		<tr bgcolor="#FFFFFF"><td colspan="2">&nbsp;</td></tr>

		<tr bgcolor="#EEEEEE">
			<td nowrap="nowrap" align="right"><b>Promotion Name :</b></td>
			<td><input type="text" name="promotion_name" size="40" style="width:400px;" /></td>
		</tr>

		<tr bgcolor="#EEEEEE">
			<td nowrap="nowrap" align="right"><b>Image :</b></td>
			<td>
				<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
				<input type="file" name="promotion_image" style="width:400px;" />
			</td>
		</tr>


		<tr>
			<td colspan="2">
				<br>
				<input type="hidden" name="action" value="do_add">
				<input type="submit" name="add" value="Add">
			</td>
		</tr>

		<tr>
			<td colspan="2">
				<i style="color: red;"><small>Please make sure to save the product details first before adding new promotion. This will reload the page.</small></i>
			</td>
		</tr>
	</table>

</form>

<!-- /promotion_add_form.inc -->

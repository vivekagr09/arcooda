<form action="?action=do_delete_item" method="post" name="delstockform">
	<table>
		<tr>
			<td class="left_title">Stocklist ID: </td>
			<td><?php echo $sid?>-<?php echo $pid?></td>
		</tr>
		<tr>
			<td class="left_title">Product Name</td>
			<td><?php echo $product_name?></td>
		</tr>
		<tr>
			<td class="left_title">Supplier Name</td>
			<td><?php echo trim($supplier_name) . ((trim($supplier_name_ch)) ? " (" . trim($supplier_name_ch) . ")" : "")?></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: center">
				<input name="supplier_id" type="hidden" value="<?php echo $sid?>" />
				<input name="product_id" type="hidden" value="<?php echo $pid?>" />
				<input name="from" type="hidden" value="<?php echo $from?>" />
				<input name="go" type="hidden" value="0" />
				<input name="yes" type="button" value="Yes" onclick="javascript:document.forms['delstockform'].go.value = <?php echo $sid?><?php echo $pid?>; document.forms[0].submit()" />
				<input name="no" type="submit" value="No" />
			</td>
		</tr>
	</table>
</form>

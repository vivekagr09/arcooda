<form accept-charset="utf-8" action="?action=do_delete_item" method="post" name="delstockform">
	<table>
		<tr>
			<td class="left_title">Stocklist ID: </td>
			<td><?=$sid?>-<?=$pid?></td>
		</tr>
		<tr>
			<td class="left_title">Product Name</td>
			<td><?=$product_name?></td>
		</tr>
		<tr>
			<td class="left_title">Supplier Name</td>
			<td><?=trim($supplier_name) . ((trim($supplier_name_ch)) ? " (" . trim($supplier_name_ch) . ")" : "")?></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: center">
				<input name="supplier_id" type="hidden" value="<?=$sid?>" />
				<input name="product_id" type="hidden" value="<?=$pid?>" />
				<input name="from" type="hidden" value="<?=$from?>" />
				<input name="go" type="hidden" value="0" />
				<input name="yes" type="button" value="Yes" onclick="javascript:document.forms['delstockform'].go.value = <?=$sid?><?=$pid?>; document.forms[0].submit()" />
				<input name="no" type="submit" value="No" />
			</td>
		</tr>
	</table>
</form>

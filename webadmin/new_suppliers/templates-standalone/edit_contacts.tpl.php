<form accept-charset="utf-8" method="post" action="?action=<?=$edit ? "doEdit&amp;id={$_REQUEST['id']}" : "doAdd"?>">
<table>
	<tr>
		<td class="left_title">
			Contact Name:
		</td>
		<td>
			<input name="contact_name" type="text" value="<?=$contact_name?>" />
		</td>
	</tr>
	<tr>
		<td class="left_title">
			Contact email:
		</td>
		<td>
			<input name="contact_email" type="text" value="<?=$contact_email?>" />
		</td>
	</tr>
<?php
if(!$edit) : ?>
	<tr>
		<td class="left_title">
			Supplier:
		</td>
		<td>
			<select name="supplier">
<?php
foreach($suppliers as $s): ?>
				<option value="<?=$s['supplier_id']?>"<?= ($s['supplier_id'] == $supplier) ? " selected=\"selected\"" : ""?>><?=$s['supplier_name']?></option>
<?php
endforeach; ?>
			</select>
		</td>
	</tr>
<?php
endif; ?>
</table>
<br />
<div style="text-align: center">
	<input type="reset" />
	<input type="submit" />
</div>
</form>

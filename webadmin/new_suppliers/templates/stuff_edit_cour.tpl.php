<script>
<!--
function setModified(target)
{
	var modTag = target + "[modified]";
	document.forms['edit_stuff_form'].elements[modTag].value = 1;
}
-->
</script>

<form name="edit_stuff_form" method="post" action="<?= $_SERVER['PHP_SELF']; ?>?action=update&amp;section=<?= $_REQUEST['section']; ?>">
	<h2>Couriers</h2>
	<table class="wide">
		<th>Name</th>
		<th>Description</th>
		<th>Account Number</th>
<?php
$shaded = true;
foreach($couriers as $c): ?>
		<tr<?= ($shaded = !$shaded) ? " class=\"alt\"" : ""; ?>>
			<td><input name="couriers[<?= $c['courier_id']; ?>][courier_name]" type="text" class="intext" value="<?= $c['courier_name']; ?>" onchange="setModified('couriers[<?= $c['courier_id']; ?>]');" /></td>
			<td><input name="couriers[<?= $c['courier_id']; ?>][description]" type="text" class="intext" value="<?= $c['description']; ?>" onchange="setModified('couriers[<?= $c['courier_id']; ?>]');" /></td>
			<td><input name="couriers[<?= $c['courier_id']; ?>][account_no]" type="text" class="intext" value="<?= $c['account_no']; ?>" onchange="setModified('couriers[<?= $c['courier_id']; ?>]');" /></td>
			<input name="couriers[<?= $c['courier_id']; ?>][modified]" type="hidden" value="0" />
			<input name="couriers[<?= $c['courier_id']; ?>][courier_id]" type="hidden" value="<?= $c['courier_id']; ?>" />
		</tr>
<?php
endforeach; ?>
		<tr<?= ($shaded = !$shaded) ? " class=\"alt\"" : ""; ?>>
			<td><input name="newcourier[courier_name]" type="text" class="intext" value="[new]" /></td>
			<td><input name="newcourier[description]" type="text" class="intext" value="[new]" /></td>
			<td><input name="newcourier[account_no]" type="text" class="intext" value="[new]" /></td>
		</tr>
	</table>
	<br />

	<table class="buttons"><tr><td>
	<input type="reset" value="Reset" /><input type="submit" value="Update" />
	</td></tr></table>
</form>

<script>
<!--
function setModified(target)
{
	var modTag = target + "[modified]";
	document.forms['edit_stuff_form'].elements[modTag].value = 1;
}
-->
</script>

<form accept-charset="utf-8" name="edit_stuff_form" method="post" action="<?= $_SERVER['PHP_SELF'] ?>?action=update">
	<h2>Couriers</h2>
	<table class="wide">
		<th>Name</th>
		<th>Description</th>
		<th>Account Number</th>
<?php
$shaded = true;
foreach($couriers as $c): ?>
		<tr<?= ($shaded = !$shaded) ? " class=\"alt\"" : "" ?>>
			<td><input name="couriers[<?= $c['courier_id'] ?>][courier_name]" type="text" class="intext" value="<?= $c['courier_name'] ?>" onchange="setModified('couriers[<?= $c['courier_id'] ?>]');" /></td>
			<td><input name="couriers[<?= $c['courier_id'] ?>][description]" type="text" class="intext" value="<?= $c['description'] ?>" onchange="setModified('couriers[<?= $c['courier_id'] ?>]');" /></td>
			<td><input name="couriers[<?= $c['courier_id'] ?>][account_no]" type="text" class="intext" value="<?= $c['account_no'] ?>" onchange="setModified('couriers[<?= $c['courier_id'] ?>]');" /></td>
			<input name="couriers[<?= $c['courier_id'] ?>][modified]" type="hidden" value="0" />
			<input name="couriers[<?= $c['courier_id'] ?>][courier_id]" type="hidden" value="<?= $c['courier_id'] ?>" />
		</tr>
<?php
endforeach; ?>
		<tr<?= ($shaded = !$shaded) ? " class=\"alt\"" : "" ?>>
			<td><input name="newcourier[courier_name]" type="text" class="intext" value="[new]" /></td>
			<td><input name="newcourier[description]" type="text" class="intext" value="[new]" /></td>
			<td><input name="newcourier[account_no]" type="text" class="intext" value="[new]" /></td>
		</tr>
	</table>
	<br />

	<h2>Shipping Destinations</h2>
	<table class="wide">
		<th>Short Name</th>
		<th>Address</th>
<?php
$shaded = true;
foreach($dests as $d): ?>
		<tr<?= ($shaded = !$shaded) ? " class=\"alt\"" : "" ?>>
			<td><input name="shipdest[<?= $d['dest_id'] ?>][name]" type="text" class="intext" value="<?= $d['name'] ?>" onchange="setModified('shipdest[<?= $d['dest_id'] ?>]');" /></td>
			<td><textarea rows="5" name="shipdest[<?= $d['dest_id'] ?>][address]" onchange="setModified('shipdest[<?= $d['dest_id'] ?>]');"><?= $d['address'] ?></textarea></td>
			<input name="shipdest[<?= $d['dest_id'] ?>][modified]" type="hidden" value="0" />
			<input name="shipdest[<?= $d['dest_id'] ?>][dest_id]" type="hidden" value="<?= $d['dest_id'] ?>" />
		</tr>
<?php
endforeach; ?>
		<tr<?= ($shaded = !$shaded) ? " class=\"alt\"" : "" ?>>
			<td><input name="newdest[name]" type="text" class="intext" value="[new]" /></td>
			<td><textarea rows="5" name="newdest[address]">[new]</textarea></td>
		</tr>
	</table>
	<br />

	<h2>Terms</h2>
	<table class="wide">
		<th>Term</th>
<?php
$shaded = true;
foreach($terms as $t): ?>
		<tr<?= ($shaded = !$shaded) ? " class=\"alt\"" : "" ?>>
			<td><input name="terms[<?= $t['term_id'] ?>][term]" type="text" class="intext" value="<?= $t['term'] ?>" onchange="setModified('terms[<?= $t['term_id'] ?>]');" /></td>
			<input name="terms[<?= $t['term_id'] ?>][modified]" type="hidden" value="0" />
			<input name="terms[<?= $t['term_id'] ?>][term_id]" type="hidden" value="<?= $t['term_id'] ?>" />
		</tr>
<?php
endforeach; ?>
		<tr<?= ($shaded = !$shaded) ? " class=\"alt\"" : "" ?>>
			<td><input name="newterm[term]" type="text" class="intext" value="[new]" /></td>
		</tr>
	</table>
	<br />

	<h2>Payment Methods</h2>
	<table class="wide">
		<th>Payment Method</th>
<?php
$shaded = true;
foreach($paymethods as $p): ?>
		<tr<?= ($shaded = !$shaded) ? " class=\"alt\"" : "" ?>>
			<td><input name="paymethods[<?= $p['paymethod_id'] ?>][text]" type="text" class="intext" value="<?= $p['text'] ?>" onchange="setModified('paymethods[<?= $p['paymethod_id'] ?>]');" /></td>
			<input name="paymethods[<?= $p['paymethod_id'] ?>][modified]" type="hidden" value="0" />
			<input name="paymethods[<?= $p['paymethod_id'] ?>][paymethod_id]" type="hidden" value="<?= $p['paymethod_id'] ?>" />
		</tr>
<?php
endforeach; ?>
		<tr<?= ($shaded = !$shaded) ? " class=\"alt\"" : "" ?>>
			<td><input name="newpaymethod[text]" type="text" class="intext" value="[new]" /></td>
		</tr>
	</table>
	<br />

	<table class="buttons"><tr><td>
	<input type="reset" value="Reset" /><input type="submit" value="Update" />
	</td></tr></table>
</form>

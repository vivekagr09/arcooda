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
	<h2>Payment Methods</h2>
	<table class="wide">
		<tr>
			<td class="left_title">Payment Method</td>
			<td><input name="paymethods[<?= $p['paymethod_id']; ?>][text]" type="text" class="intext" value="<?= $p['text']; ?>" onchange="setModified('paymethods[<?= $p['paymethod_id']; ?>]');" /></td>
		</tr>
	</table>
	<input name="paymethods[<?= $p['paymethod_id']; ?>][modified]" type="hidden" value="0" />
	<input name="paymethods[<?= $p['paymethod_id']; ?>][paymethod_id]" type="hidden" value="<?= $p['paymethod_id']; ?>" />
	<br />

	<table class="buttons"><tr><td>
	<input type="reset" value="Reset" /><input type="submit" value="Update" />
	</td></tr></table>
	</table>
</form>

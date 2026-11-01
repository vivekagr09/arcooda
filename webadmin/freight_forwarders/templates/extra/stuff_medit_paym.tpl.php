<script>
<!--
function setModified(target)
{
	var modTag = target + "[modified]";
	document.forms['edit_stuff_form'].elements[modTag].value = 1;
}
-->
</script>

<form name="edit_stuff_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?action=update&amp;section=<?php echo $_REQUEST['section']; ?>">
	<h2>Payment Methods</h2>
	<table class="wide">
		<tr>
			<td class="left_title">Payment Method</td>
			<td><input name="paymethods[<?php echo $p['paymethod_id']; ?>][text]" type="text" class="intext" value="<?php echo $p['text']; ?>" onchange="setModified('paymethods[<?php echo $p['paymethod_id']; ?>]');" /></td>
		</tr>
	</table>
	<input name="paymethods[<?php echo $p['paymethod_id']; ?>][modified]" type="hidden" value="0" />
	<input name="paymethods[<?php echo $p['paymethod_id']; ?>][paymethod_id]" type="hidden" value="<?php echo $p['paymethod_id']; ?>" />
	<br />

	<table class="buttons"><tr><td>
	<input type="reset" value="Reset" /><input type="submit" value="Update" />
	</td></tr></table>
	</table>
</form>

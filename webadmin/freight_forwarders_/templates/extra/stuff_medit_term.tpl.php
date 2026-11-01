<script>
<!--
function setModified(target)
{
	var modTag = target + "[modified]";
	document.forms['edit_stuff_form'].elements[modTag].value = 1;
}
-->
</script>

<form accept-charset="utf-8" name="edit_stuff_form" method="post" action="<?= $_SERVER['PHP_SELF']; ?>?action=update&amp;section=<?= $_REQUEST['section']; ?>">
	<h2>Terms</h2>
	<table class="wide">
		<tr>
			<td class="left_title">Term</td>
			<td><input name="terms[<?= $t['term_id']; ?>][term]" type="text" class="intext" value="<?= $t['term']; ?>" onchange="setModified('terms[<?= $t['term_id']; ?>]');" /></td>
		</tr>
	</table>
			<input name="terms[<?= $t['term_id']; ?>][modified]" type="hidden" value="0" />
			<input name="terms[<?= $t['term_id']; ?>][term_id]" type="hidden" value="<?= $t['term_id']; ?>" />
	<br />

	<table class="buttons"><tr><td>
	<input type="reset" value="Reset" /><input type="submit" value="Update" />
	</td></tr></table>
</form>

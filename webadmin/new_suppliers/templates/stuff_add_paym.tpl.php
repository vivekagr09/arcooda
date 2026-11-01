<form name="edit_stuff_form" method="post" action="<?= $_SERVER['PHP_SELF']; ?>?action=update&amp;section=<?= $_REQUEST['section']; ?>">
	<h2>Payment Methods</h2>
	<table class="wide">
		<tr>
			<td class="left_title">Payment Method</td>
			<td><input name="newpaymethod[text]" type="text" class="intext" /></td>
		</tr>
	</table>
	<br />

	<table class="buttons"><tr><td>
	<input type="reset" value="Reset" /><input type="submit" value="Update" />
	</td></tr></table>
</form>

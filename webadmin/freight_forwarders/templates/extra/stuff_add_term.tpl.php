<form name="edit_stuff_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?action=update&amp;section=<?php echo $_REQUEST['section']; ?>">
	<h2>Terms</h2>
	<table class="wide">
		<tr>
			<td class="left_title">Term</td>
			<td><input name="newterm[term]" type="text" class="intext" /></td>
		</tr>
	</table>
	<br />

	<table class="buttons"><tr><td>
	<input type="reset" value="Reset" /><input type="submit" value="Update" />
	</td></tr></table>
</form>

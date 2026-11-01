<form name="edit_stuff_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?action=update&amp;section=<?php echo $_REQUEST['section']; ?>">
	<h2>Shipping Destinations</h2>
	<table class="wide">
		<tr>
			<td class="left_title">Short Name</td>
			<td><input name="newdest[name]" type="text" class="intext" /></td>
		</tr>
		<tr>
			<td class="left_title">Address</td>
			<td><textarea rows="5" name="newdest[address]"></textarea></td>
		</tr>
	</table>
	<br />

	<table class="buttons"><tr><td>
	<input type="reset" value="Reset" /><input type="submit" value="Update" />
	</td></tr></table>
</form>

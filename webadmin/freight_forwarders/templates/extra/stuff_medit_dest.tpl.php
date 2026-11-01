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
	<h2>Shipping Destinations</h2>
	<table class="wide">
		<tr>
			<td class="left_title">Short Name</td>
			<td><input name="shipdest[<?php echo $d['dest_id']; ?>][name]" type="text" class="intext" value="<?php echo $d['name']; ?>" onchange="setModified('shipdest[<?php echo $d['dest_id']; ?>]');" /></td>
		</tr>
		<tr>
			<td class="left_title">Address</td>
			<td><textarea rows="5" name="shipdest[<?php echo $d['dest_id']; ?>][address]" onchange="setModified('shipdest[<?php echo $d['dest_id']; ?>]');"><?php echo $d['address']; ?></textarea></td>
		</tr>
	</table>
	<input name="shipdest[<?php echo $d['dest_id']; ?>][modified]" type="hidden" value="0" />
	<input name="shipdest[<?php echo $d['dest_id']; ?>][dest_id]" type="hidden" value="<?php echo $d['dest_id']; ?>" />
	<br />

	<table class="buttons"><tr><td>
	<input type="reset" value="Reset" /><input type="submit" value="Update" />
	</td></tr></table>
</form>

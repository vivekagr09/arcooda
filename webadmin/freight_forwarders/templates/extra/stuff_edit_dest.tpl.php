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
	<table><tr><th>Shipping Destinations</th></tr></table>
	<table>
		<tr>
			<th>Short Description</th>
			<th>Address</th>
			<th class="collapse">&nbsp;</th>
		</tr>
<?php
$shaded = true;
foreach($dests as $d): ?>
		<tr<?php echo ($shaded = !$shaded) ? " class=\"alt\"" : ""; ?>>
			<td><?php echo $d['name']; ?></td>
			<td><?php echo nl2br($d['address']); ?></td>
			<td class="collapse"><a href="stuff_admin.php?section=dest&action=edit&id=<?php echo $d['dest_id']; ?>">Modify</a>&nbsp;|&nbsp;<a href="stuff_admin.php?section=dest&action=del&id=<?php echo $d['dest_id']; ?>" onclick="javascript:return confirm('Are you sure you want to delete this item?');">Delete</a></td>
		</tr>
<?php
endforeach; ?>
	</table>
</form>

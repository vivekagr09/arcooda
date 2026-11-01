<table>
	<tr>
		<th>Payment Methods</th>
		<th class="collapse">&nbsp;</th>
	</tr>
<?php
$shaded = true;
foreach($paymethods as $p): ?>
	<tr<?php echo ($shaded = !$shaded) ? " class=\"alt\"" : ""; ?>>
		<td><?php echo $p['text']; ?></td>
		<td class="collapse"><a href="stuff_admin.php?section=paym&action=edit&id=<?php echo $p['paymethod_id']; ?>">Modify</a>&nbsp;|&nbsp;<a href="stuff_admin.php?section=paym&action=del&id=<?php echo $p['paymethod_id']; ?>" onclick="javascript:return confirm('Are you sure you want to delete this item?');">Delete</a></td>
	</tr>
<?php
endforeach; ?>
</table>

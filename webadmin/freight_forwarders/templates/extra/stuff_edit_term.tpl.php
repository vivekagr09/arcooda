<table>
	<tr>
		<th>Shipping Terms</th>
		<th class="collapse">&nbsp;</th>
	</tr>
<?php
$shaded = true;
foreach($terms as $t): ?>
	<tr<?php echo ($shaded = !$shaded) ? " class=\"alt\"" : ""; ?>>
		<td><?php echo $t['term']; ?></td>
		<td class="collapse"><a href="stuff_admin.php?section=term&action=edit&id=<?php echo $t['term_id']; ?>">Modify</a>&nbsp;|&nbsp;<a href="stuff_admin.php?section=term&action=del&id=<?php echo $t['term_id']; ?>">Delete</a></td>
	</tr>
<?php
endforeach; ?>
</table>

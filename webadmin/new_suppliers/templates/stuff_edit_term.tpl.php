<table>
	<tr>
		<th>Shipping Terms</th>
		<th class="collapse">&nbsp;</th>
	</tr>
<?php
$shaded = true;
foreach($terms as $t): ?>
	<tr<?= ($shaded = !$shaded) ? " class=\"alt\"" : ""; ?>>
		<td><?= $t['term']; ?></td>
		<td class="collapse"><a href="stuff_admin.php?section=term&action=edit&id=<?= $t['term_id']; ?>">Modify</a>&nbsp;|&nbsp;<a href="stuff_admin.php?section=term&action=del&id=<?= $t['term_id']; ?>">Delete</a></td>
	</tr>
<?php
endforeach; ?>
</table>

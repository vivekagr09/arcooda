<!-- summary -->
<table class="wide" style="text-align: center">
	<tr>
		<th>Supplier ID</th>
		<th>Supplier</th>
		<th>Country</th>
		<th>Open P/O</th>
		<th>All P/O</th>
		<th>Last Ordered</th>
		<th class="collapse">&nbsp;</th>
	</tr>
<?php
$alt = true; ?>
<?php
foreach($suppliers as $supp): ?>
	<tr <?= ($alt = !$alt) ? "class=\"alt\"" : "" ?>>
		<td>ID<?= sprintf("%06d", $supp['supplier_id']); ?></td>
		<td><?= $supp['supplier_name']; ?></td>
		<td><?= $supp['country_name']; ?></td>
<?php
if($supp['open_po_count']): ?>
		<td><a href="purchaseorders_admin.php?action=list&show=open&supplier_id=<?= $supp['supplier_id']; ?>"><?= $supp['open_po_count']; ?></a></td>
<?php
else: ?>
		<td><?= $supp['open_po_count']; ?></td>
<?php
endif; ?>
<?php
if($supp['po_count']): ?>
		<td><a href="purchaseorders_admin.php?action=list&show=all&supplier_id=<?= $supp['supplier_id']; ?>"><?= $supp['po_count']; ?></a></td>
<?php
else: ?>
		<td><?= $supp['po_count']; ?></td>
<?php
endif; ?>
		<td><?= isset($supp['last_po']) ? date("d/m/Y", $supp['last_po']) : ""; ?></td>
		<td class="collapse"><a href="?action=edit&id=<?= $supp['supplier_id']; ?>">View</a>&nbsp;</td>
	</tr>
<?php
endforeach; ?>
</table>
<!-- end summary -->

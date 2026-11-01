<!-- summary -->
<b>Showing: <?=$page_first?> to <?=$page_last?> of <?=$total_items?></b> | <?=$page_links?><br /><br />
<table class="wide" style="text-align: center">
	<tr>
<!--	<th>Supplier ID</th>-->
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
	<tr <?php echo ($alt = !$alt) ? "class=\"alt\"" : "" ?>>
<!--	<td>ID<?php echo sprintf("%06d", $supp['supplier_id']); ?></td> -->
		<td><span class="titled" title="[Suppiler ID: <?php echo sprintf("%06d", $supp['supplier_id']); ?>]"><?php echo trim($supp['supplier_name']); ?></span></td>
		<td><?php echo trim($supp['country_name']); ?></td>
<?php
if($supp['open_po_count']): ?>
		<td><a href="purchaseorders_admin.php?action=list&show=open&supplier_id=<?php echo $supp['supplier_id']; ?>"><?php echo $supp['open_po_count']; ?></a></td>
<?php
else: ?>
		<td><?php echo $supp['open_po_count']; ?></td>
<?php
endif; ?>
<?php
if($supp['po_count']): ?>
		<td><a href="purchaseorders_admin.php?action=list&show=all&supplier_id=<?php echo $supp['supplier_id']; ?>"><?php echo $supp['po_count']; ?></a></td>
<?php
else: ?>
		<td><?php echo $supp['po_count']; ?></td>
<?php
endif; ?>
		<td><?php echo isset($supp['last_po']) ? date("d/m/Y", $supp['last_po']) : ""; ?></td>
		<td class="collapse"><a href="?action=edit&id=<?php echo $supp['supplier_id']; ?>">View</a>&nbsp;|&nbsp;<a href="stocklist_admin.php?action=edit&supplier_id=<?php echo $supp['supplier_id']; ?>">Stock</a></td>
	</tr>
<?php
endforeach; ?>
</table>
<br /><b>Showing: <?=$page_first?> to <?=$page_last?> of <?=$total_items?></b> | <?=$page_links?><br />
<!-- end summary -->

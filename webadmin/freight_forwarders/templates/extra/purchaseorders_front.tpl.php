<!-- summary -->
<b>Showing: <?=$page_first?> to <?=$page_last?> of <?=$total_items?></b> | <?=$page_links?><br /><br />
<table class="wide">
	<tr>
		<th>Order ID</th>
		<th>Date</th>
		<th></th>
		<th>Supplier</th>
		<th>&nbsp;Total&nbsp;Price&nbsp;</th>
		<th>Status</th>
		<th>Ordered By</th>
		<th>Days</th>
		<th>&nbsp;</th>
	</tr>
<?php
$alt = true; ?>
<?php
foreach($orders as $o):
	$from_company_list = getWebsitesList();
?>
	<tr <?php echo ($alt = !$alt) ? "class=\"alt\"" : "" ?> style="text-align: center">
		<td><?php echo $o['from_company'] ? "F" : "C"; ?>#<?php echo sprintf("%06d", $o['porder_id']); ?></td>
		<td><?php echo date("d/m/Y", $o['creation_date']); ?></td>
	<td><img src="images/<?php echo $o['from_company'] ? "logo_FW.gif" : "logo_coe.gif"; ?>" alt="<?=htmlspecialchars($from_company_list[$o['from_company']])?>" title="<?=htmlspecialchars($from_company_list[$o['from_company']])?>"></td>
		<td><?php echo $o['supplier_name']; ?></td>
		<td style="text-align: right">
<?php
if($o['paid']):
?>
			<?php echo sprintf("%01.2f", $o['static_price']); ?> USD
<?php
else:
?>
			<?php echo sprintf("%01.2f", showPriceFrom($o['total_price'], $o['order_currency'], 'USD')); ?> USD
<?php
endif
?>
		</td>
		<td class="collapse"><img src="images/<?php echo $o['status_code']; ?>.gif" alt="<?php echo $o['status']; ?>" title="<?php echo $o['status']; ?>"</td>
		<td><?php echo $o['user']['first_name'] . " " . $o['user']['last_name']; ?></td>
		<td><?php echo $o['days_open']; ?></td>
		<td class="collapse"><a href="view_purchaseorder.php?id=<?php echo $o['porder_id']?>" target="_blank">View</a>&nbsp;|&nbsp;<a href="?action=edit&id=<?php echo $o['porder_id']; ?>">Edit</a></td>
	</tr>
<?php
endforeach; ?>
</table>
<br /><b>Showing: <?=$page_first?> to <?=$page_last?> of <?=$total_items?></b> | <?=$page_links?><br />
<!-- end summary -->

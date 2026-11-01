<!-- summary -->
<b>Showing: <?= $page_first; ?> to <?= $page_last; ?> of <?= $total_items; ?></b> | <?=$page_links;?><br /><br />
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
foreach($orders as $o): ?>
	<tr <?= ($alt = !$alt) ? "class=\"alt\"" : "" ?> style="text-align: center">
		<td><?= $o['from_company'] ? "F" : "C"; ?>#<?= sprintf("%06d", $o['porder_id']); ?></td>
		<td><?= date("d/m/Y", $o['creation_date']); ?></td>
	<td><img src="images/<?= $o['from_company'] ? "logo_FW.gif" : "logo_coe.gif"; ?>" alt="<?= $o['from_company'] ? "Fortunewave" : "Coinopexpress"; ?>" title="<?= $o['from_company'] ? "Fortunewave" : "Coinopexpress"; ?>"></td>
		<td><?= $o['supplier_name']; ?></td>
		<td style="text-align: right">
		<?php if($o['paid']):?>
			<?= sprintf("%01.2f", $o['static_price']); ?> USD
		<?php else:?>
			<?= sprintf("%01.2f", showPriceFrom($o['total_price'], $o['order_currency'], 'USD')); ?> USD
		<?php endif?>
		</td>
		<td class="collapse"><img src="images/<?= $o['status_code']; ?>.gif" alt="<?= $o['status']; ?>" title="<?= $o['status']; ?>"</td>
		<td><?= $o['user']['first_name'] . " " . $o['user']['last_name']; ?></td>
		<td><?= $o['days_open']; ?></td>
		<td class="collapse"><a href="view_purchaseorder.php?id=<?=$o['porder_id']?>" target="_blank">View</a>&nbsp;|&nbsp;<a href="?action=edit&id=<?= $o['porder_id']; ?>">Edit</a></td>
	</tr>
<?php
endforeach; ?>
</table>
<br /><b>Showing: <?= $page_first; ?> to <?= $page_last; ?> of <?= $total_items; ?></b> | <?=$page_links;?><br />
<!-- end summary -->

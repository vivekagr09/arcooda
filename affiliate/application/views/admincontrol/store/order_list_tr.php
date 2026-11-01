<?php foreach($orders as $order){ ?>
	<tr>
		<td><?php echo $order['id'];?></td>
		<td class="text-center"><?php echo c_format($order['total_sum']); ?></td>
		<td class="text-center order-status"><?php echo $status[$order['status']]; ?></td>
		<td class="text-center"><?php echo __('admin.'.array_search(str_replace("_", " ", $order['payment_method']),$payment_methods)); ?></td>
		<td class="text-center">
			<img style="width: 20px;margin: 0 10px;" src="<?= base_url('assets/vertical/assets/images/flags/'. strtolower($order['country_code'])).'.png' ?>" /> IP: <?= $order['ip'] ?>
			</td>
		<td class="text-center"><?php echo $order['txn_id'];?></td>
		<td>
			<span class="badge rounded-pill <?= ($order['status'] == 1) ? 'bg-success text-white' : 'bg-warning text-dark' ?>">
			    <?= $status[$order['status']] ?>
			</span>
		</td>
		<td>
			<a href="<?= base_url('admincontrol/vieworder/'. $order['id']) ?>" class="btn btn-primary btn-sm"><?= __('admin.details') ?></a>
		</td>
	</tr>
<?php } ?>
<tr>
    <td colspan="8" class="text-end">
        <ul class="pagination"><?= $pagination ?></ul>
    </td>
</tr>

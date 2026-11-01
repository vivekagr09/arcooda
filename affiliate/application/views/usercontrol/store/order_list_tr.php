<?php foreach($orders as $order){ ?>
	<tr>
		<td><?php echo $order['id'];?></td>
		<td class="txt-cntr"><?php echo c_format($order['total_sum']); ?></td>
		<td class="txt-cntr order-status"><?php echo $status[$order['status']]; ?></td>
		<td class="txt-cntr"><?php echo str_replace("_", " ", $order['payment_method']) ?></td>
		<td class="txt-cntr">
			<img style="width: 20px;margin: 0 10px;" src="<?= base_url('assets/vertical/assets/images/flags/'. strtolower($order['country_code'])).'.png' ?>" /> IP: <?= $order['ip'] ?>
			</td>
		<td class="txt-cntr"><?php echo $order['txn_id'];?></td>
		<td>
			<div class="badge <?= ($order['status'] == 1) ? 'bg-success' : 'bg-warning' ?>">
				<?= $status[$order['status']] ?>
			</div>
		</td>
		<td>
			<a href="<?= base_url('usercontrol/vieworder/'. $order['id']) ?>" class="btn btn-primary btn-sm"><?= __('admin.details') ?></a>
		</td>
	</tr>
<?php } ?>
<tr>
    <td colspan="8" class="text-right">
        <ul class="pagination pagination-td"><?= $pagination ?></ul>
    </td>
</tr>
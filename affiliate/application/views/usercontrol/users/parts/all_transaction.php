<div class="card-body p-0">
	<div class="table-responsive">
		<table class="table transaction-table table-striped ">
			<thead>
				<tr>
					<th><?= __('user.module') ?></th>
					<th><?= __('user.id') ?></th>
					<th><?= __('user.price') ?></th>
					<th><?= __('user.payment_gateway') ?></th>
					<th><?= __('user.transaction_id') ?></th>
					<th><?= __('user.status') ?></th>
					<th><?= __('user.date') ?></th>
					<th>#</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($all_transaction as $key => $value){
					switch($value['module']){
					    case 'deposit':
					   		$payment_gateway = __('user.'.$value['payment_gateway']);
					        $transaction_id = $value['payment_detail'];
					        $status_text = withdrwal_status($value['status_id']);
					        $url = base_url('usercontrol/deposit_details/'.$value['id']);
					        break;
					    case 'membership':
					    	$payment_gateway = $value['payment_gateway'];
					        $transaction_id = json_decode($value['payment_detail'])->transaction_id;
					        $status_text = membership_withdrwal_status($value['status_id']);
					        $url = base_url('usercontrol/membership_purchase_details/'.$value['id']);
					        break;
					    case 'store':
					    	$payment_gateway = $value['payment_gateway'];
					        $transaction_id = $value['payment_detail'];
					        $status_text = store_withdrwal_status($value['status_id']);
					        $url = base_url('usercontrol/vieworder/'.$value['id']);
					        break;
					} ?>
					<tr>
						<td>
							<?php
								if ($value['module'] == 'store') {
									echo __('user.store');
								}elseif ($value['module'] == 'membership') {
									echo __('user.membership');
								}elseif ($value['module'] == 'deposit') {
									echo __('user.deposit');
								}
							?>
						</td>
						<td><?= $value['id'] ?></td>
						<td><?= c_format($value['price']) ?></td>
						<td><?= $payment_gateway ?></td>
						<td><?= $transaction_id ?></td>
						<td><?= $status_text ?></td>
						<td><?= dateFormat($value['datetime'],'d F Y H:i'); ?></td>
						<td>
							<a href="<?= $url ?>" class="btn btn-primary btn-sm" target="_blank">
								<?= __('user.details') ?>
							</a>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<div class="card-footer">
	<ul class="pagination justify-content-end">
		<?= $pagination ?>
	</ul>
</div>
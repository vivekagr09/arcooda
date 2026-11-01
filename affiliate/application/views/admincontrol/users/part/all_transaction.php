<div class="card-body p-0">
	<div class="table-responsive">
		<table class="table transaction-table table-striped ">
			<thead>
				<tr>
					<th><?= __('admin.module') ?></th>
					<th><?= __('admin.id') ?></th>
					<th><?= __('admin.user') ?></th>
					<th><?= __('admin.price') ?></th>
					<th><?= __('admin.payment_gateway') ?></th>
					<th><?= __('admin.transaction_id') ?></th>
					<th><?= __('admin.status') ?></th>
					<th><?= __('admin.date') ?></th>
					<th>#</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($all_transaction as $key => $value){
					$payment_method = strtolower(trim(str_replace("_", " ",$value['payment_gateway'])));
					switch($value['module']){
						case 'deposit':
						$payment_gateway =  __('admin.'.array_search($payment_method,array_map('strtolower',$payment_methods)));
						$transaction_id = $value['payment_detail'];
						$status_text = withdrwal_status($value['status_id']);
						$url = base_url('admincontrol/vendor_deposit_details/'.$value['id']);
						break;
						case 'membership':
						$payment_gateway =  __('admin.'.array_search($payment_method ,array_map('strtolower',$payment_methods)));
						$transaction_id = json_decode($value['payment_detail'])->transaction_id;
						$status_text = membership_withdrwal_status($value['status_id']);
						$url = base_url('membership/membership_purchase_edit/'.$value['id']);
						break;
						case 'store':
						$payment_gateway = __('admin.'.array_search($payment_method,array_map('strtolower',$payment_methods)));
						$transaction_id = $value['payment_detail'];
						$status_text = store_withdrwal_status($value['status_id']);
						$url = base_url('admincontrol/vieworder/'.$value['id']);
						break;
					} ?>
					<tr>
						<td><?= __('admin.'.$value['module']) ?></td>
						<td><?= $value['id'] ?></td>
						<td><?= $value['username'] ?></td>
						<td><?= c_format($value['price']) ?></td>
						<td><?= $payment_gateway ?></td>
						<td><?= $transaction_id ?></td>
						<td><?= $status_text ?></td>
						<td><?= dateFormat($value['datetime'],'d F Y H:i'); ?></td>
						<td>
							<a href="<?= $url ?>" class="btn btn-primary btn-sm" target="_blank">
								<?= __('admin.details') ?>
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
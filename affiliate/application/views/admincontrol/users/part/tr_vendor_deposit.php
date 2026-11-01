<?php foreach ($lists as $key => $value) { ?>
	<tr>
		<td><?= $value['vd_id'] ?></td>
		<td><?= $value['username'] ?></td>
		<td><?= dateFormat($value['vd_created_on'],'d F Y') ?></td>
		<td>
			<?php  
				if ($value['vd_payment_method'] == 'bank_transfer') {
					echo __('admin.bank_transfer');
				}elseif ($value['vd_payment_method'] == 'paytm') {
					echo __('admin.paytm');
				}elseif ($value['vd_payment_method'] == 'opay') {
					echo __('admin.opay');
				}elseif ($value['vd_payment_method'] == 'paypal') {
					echo __('admin.paypal');
				}elseif ($value['vd_payment_method'] == 'razorpay') {
					echo __('admin.razorpay');
				}elseif ($value['vd_payment_method'] == 'flutterwave') {
					echo __('admin.flutterwave');
				}elseif ($value['vd_payment_method'] == 'stripes') {
					echo __('admin.stripe');
				}elseif ($value['vd_payment_method'] == 'yandex') {
					echo __('admin.yandex');
				}elseif ($value['vd_payment_method'] == 'skrill') {
					echo __('admin.skrill');
				}elseif ($value['vd_payment_method'] == 'paystack') {
					echo __('admin.paystack');
				}elseif ($value['vd_payment_method'] == 'xendit') {
					echo __('admin.xendit');
				}else{
					echo str_replace("_", " ", $value['vd_payment_method']);
				}
			?>
		</td>
		<td><?= $value['vd_txn_id'] ?></td>
		<td><?= c_format($value['vd_amount']) ?></td>
		<td><?= withdrwal_status($value['vd_status']) ?></td>
		<td class="text-right">
			<a href="<?= base_url('admincontrol/vendor_deposit_details/'. $value['vd_id']) ?>" class="btn btn-primary btn-sm"><?= __('admin.details') ?></a>
			<a href="javascript:void(0);" class="btn btn-danger btn-sm btn-delete-deposit" data-id="<?= $value['vd_id'] ?>">
				<?= __('admin.delete') ?>
			</a>
		</td>
	</tr>
<?php  } ?>
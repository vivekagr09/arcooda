<?php foreach ($lists as $key => $value) { ?>
	<tr>
		<td><?= $value['id'] ?></td>
		<td><?= $value['username'] ?></td>
		<td><?= dateFormat($value['created_at'],'d F Y') ?></td>
		<td><?= $value['prefer_method'] ?></td>
		<td><a class="trans_ids" href="javascript:void(0);" data-trans_ids="<?= $value['tran_ids'] ?>"><i class="fas fa-eye"></i></a></td>
		<td><?= c_format($value['total']) ?></td>
		<td><?= withdrwal_status($value['status']) ?></td>
		<td class="text-right">
			<a href="<?= base_url('admincontrol/wallet_requests_details/'. $value['id']) ?>" class="btn btn-primary btn-sm"><?= __('admin.details') ?></a>
						
			<?php
			if($value['tran_ids'] != ""){
				echo '<button data-id="'.$value['tran_ids'].'" class="btn btn-danger btn-sm btn-deletes">'. __('admin.revert_to_wallet').'</button>';
			} else {
				echo __('admin.transaction_not_available');
			}
			?>		
		</td>
	</tr>
<?php  } ?>
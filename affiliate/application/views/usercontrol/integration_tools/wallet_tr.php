<?php foreach ($transaction as $key => $value) { ?>
	<?php list($text,$ip) = parseMessage($value['comment'],$value,'usercontrol',true,true); ?>
	<tr class="main-tr <?= $recurring ? 'recurring recurringof-'.$recurring : '' ?>">
		<td><?= $key + 1 ?></td>
		<td><?= $text ?></td>
		<td>
			<?php if($value['integration_orders_total']){ ?>
				<?= c_format($value['integration_orders_total']) ?>
			<?php } ?>
			<?php if($value['local_orders_total']){ ?>
				<?= c_format($value['local_orders_total']) ?>
			<?php } ?>
		</td>
		<td><?= c_format($value['amount']) ?></td>
		<td><?= $value['created_at'] ?></td>
		<td><?= wallet_type($value) ?></td>
		<td class="text-center">
			<?= $status_icon[$value['status']] ?>
			<?php if(false && $value['status'] == "1"){ ?>
				<?php if($allow_with){ ?>
					<button class="btn btn-primary send-request" data-id="<?= $value['id'] ?>"><?= __('user.send_request') ?></button>
				<?php } else { ?>
					<button class="btn btn-primary " data-bs-toggle="modal" href='#withdrawal-limit'><?= __('user.send_request') ?></button>
				<?php } ?>
			<?php } else {
				echo $status[$value['status']];
			} ?>
		</td>
	</tr>
	<tr class="action-tr <?= $recurring ? 'recurring recurringof-'.$recurring : '' ?>">
		<td></td>
		<td colspan="4" class="text-left"><?= $ip ?></td>
		<td colspan="4" class="text-right">
			<?php if(!$value['parent_id']){  ?>
	    		<?php if($value['wallet_recursion_id']){ ?>
	    			<span class="badge bg-secondary p-2"><?= __('user.runs') ?> <?= (int)$value['total_recurring'] ?> <?= __('user.cycle_and_next_is_at') ?> <?= $value['wallet_recursion_next_transaction'] ?></span>
	    		<?php } ?>
	    	<?php } ?>
		</td>
	</tr>
<?php } ?>
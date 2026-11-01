<?php foreach ($transaction as $key => $value) { ?>
	<?php list($text,$ip) = parseMessage($value['comment'],$value,'usercontrol',true); ?>
	<tr class="main-tr <?= $recurring ? 'recurring recurringof-'.$recurring : '' ?>">
		<td>
			<?= $key + 1 ?>
			<div><?= $value['is_vendor'] ? 'Vendor'	 : '' ?>	</div>
		</td>
		<td><?= wallet_ex_type($value) ?></td>
		<td>
			<?php if($value['integration_orders_total']){ ?>
				<?= c_format($value['integration_orders_total']) ?>
			<?php } ?>
			<?php if($value['local_orders_total']){ ?>
				<?= c_format($value['local_orders_total']) ?>
			<?php } ?>
		</td>
		<td>
			<div class="dpopver-content d-none">
				<?php
					list($message,$ip_details) = parseMessage($value['comment'],$value,'usercontrol',true);
					echo "<div>". $message ."</div>";
				?>
			</div>
			<div class="wallet-popover badge bg-secondary py-1 pl-2 font-14" toggle="popover"> 
				<?= c_format($value['amount']) ?> 
			</div>
		</td>
		<td><?= payment_method($value['payment_method']) ?></td>
		<td><?= $value['created_at'] ?></td>
		<td><?= $value['comm_from'] == 'ex' ? __('user.external') : __('user.store') ?></td>
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
	    			<span class="badge bg-default p-2"><?= __('user.runs') ?> <?= (int)$value['total_recurring'] ?> <?= __('user.cycle_and_next_is_at') ?> <?= $value['wallet_recursion_next_transaction'] ?></span>
	    		<?php } ?>
	    	<?php } ?>
	    	<?php if((int)$value['total_recurring']){ ?>
	    		<button data-toggle="tooltip" title="Show Recurring Transition" class="btn btn-sm btn-primary show-recurring-transition" data-id="<?= $value['id'] ?>"><i class="mdi mdi-plus"></i></button>
	    	<?php } ?>
		</td>
	</tr>
<?php } ?>
<?php foreach($orders as $index => $order){ ?>
	<?php if($order['type'] == 'store'){ ?>
		<tr>
			<td><?= orderId($order['id']);?></td>
			<td class="txt-cntr"><?php echo c_format($order['total']); ?></td>
			<td class="txt-cntr"><?php echo $order['order_country_flag'];?></td>
			<td><?= __('admin.local_store') ?></td>
			<?php 
				$icon = strtolower(str_replace(" ", "_", $status[$order['status']])) .'.png';
			?>
			<td class="txt-cntr">
				<div class="badge <?= ($order['status'] == 1) ? 'bg-success' : 'bg-warning' ?>">
					<?= $status[$order['status']] ?>
				</div>
			</td>
			<td class="txt-cntr">
				<?php
				if($order['wallet_commission_status'] == 0) {
					?>
					<span class="badge <?php if((int)$order['wallet_status'] > 0){ ?>bg-success<?php }else{ ?>bg-warning<?php } ?>"><?= $wallet_status[(int)$order['wallet_status']] ?></span>
					<?php
			 	} else {
					echo commission_status($order['wallet_commission_status']);
			 	}

				?>
				<br>
				<?php echo c_format($order['commission_amount']); ?>
			</td>
			<td class="txt-cntr"><?= date("d-m-Y h:i A",strtotime($order['created_at'])); ?></td>
		</tr>
		 <?php
	} else { ?>
		<tr>
			<td><?= orderId($order['order_id']);?></td>
			<td class="txt-cntr"><?php echo c_format($order['total']); ?></td>
			<td class="txt-cntr"><?php echo $order['order_country_flag'];?></td>
			<td><?= __('admin.external') ?></td>
			<td class="txt-cntr">
				<div class="badge <?= ($order['status'] == 1) ? 'bg-success' : 'bg-warning' ?>">
					<?= $status[$order['status']] ?>
				</div>
			</td>
			<td class="txt-cntr">
				<?php

				if($order['wallet_commission_status'] == 0) {
					?>
					<span class="badge <?php if((int)$order['wallet_status'] > 0){ ?>bg-success<?php }else{ ?>bg-warning<?php } ?>"><?= $wallet_status[(int)$order['wallet_status']] ?></span>
					<?php
			 	} else {
					echo commission_status($order['wallet_commission_status']);
			 	}

			 	
				?>
				<br>
				<?= c_format($order['commission']) ?>
			</td>
			<td class="txt-cntr"><?php echo date("d-m-Y h:i A",strtotime($order['created_at'])); ?></td>
			 
		</tr>
		 <?php
	}
} ?>


<?php if(empty($orders)){ ?>
<tr>
    <td colspan="100%" class="text-center mt-5">
        <div class="d-flex justify-content-center align-items-center flex-column mt-5">
            <i class="fas fa-exchange-alt fa-5x text-muted"></i>
            <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
        </div>
    </td>
</tr>
<?php } ?>
 
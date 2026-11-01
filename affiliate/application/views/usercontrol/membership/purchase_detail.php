<?php
$payment_details=json_decode($plan->payment_details);
?>
<div class="row">
	<div class="col-sm-6">
		<div class="card">
			<div class="card-header">
				<h5 class="card-title m-0"><?= __('user.purchase_details') ?></h5>
			</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item"><b><?= __('user.id') ?>:</b> <?= $plan->id ?></li>
				<li class="list-group-item"><b><?= __('user.plan_name') ?>:</b> <?= ($plan->plan ? $plan->plan->name : '') ?></li>
				<li class="list-group-item"><b><?= __('user.price') ?>:</b> <?= c_format(($plan->plan ? $plan->plan->price : 0)) ?></li>

				<li class="list-group-item"><b><?= __('user.special_price') ?>:</b> <?= c_format(($plan->plan ? $plan->plan->special : 0)) ?></li>
				<?php 
					$bonus = $plan->bonusData();
					if($bonus){
				?>
				<li class="list-group-item"><b><?= __('user.bonus') ?>:</b> <?= c_format($bonus->amount) ?></li>
				<?php } else { ?>
				<li class="list-group-item"><b><?= __('user.bonus') ?>:</b> <?= __('user.no_bonus') ?></li>
				<?php } ?>
				<li class="list-group-item"><b><?= __('user.type') ?>:</b> 
					<?php   
						if ($plan->plan) {
							if ($plan->plan->type == 'paid') {
								echo __('user.paid');
							}elseif ($plan->plan->type == 'free') {
								echo __('user.free');
							}else{
								echo $plan->plan->type;
							}
						}else{
							echo '';
						}
					?>
				</li>

				<li class="list-group-item"><b><?= __('user.free_trail') ?>:</b> 
					<?php
					 
					if($plan->plan->have_trail > 0){
						echo  $plan->plan->free_trail . " " . __('user.days');
					}
					else{

						echo '0' . " " . __('user.days');
					}
					?>
				</li>
				<?php if(isset($payment_details)) { ?>
				<li class="list-group-item"><b><?= __('user.payment_status') ?>:</b>
					<?= $payment_details->payment_status ?>
				</li>
				<?php } ?>

				<li class="list-group-item"><b><?= __('user.payment_method') ?>:</b>
					<?= $plan->payment_method ?>
				</li>

			
				   
				
				<?php if($plan->status_id == 1) { ?>

					<?php if(!$plan->is_lifetime) { ?>

					<li class="list-group-item"><b><?= __('user.remaining_time') ?>:</b> <span data-time-remains="<?= $plan->strToTimeRemains(); ?>"><?= $plan->remainDay() ?></span></li>

					<?php } ?>

					<li class="list-group-item"><b><?= __('user.started_on') ?>:</b> <?= dateFormat($plan->started_at,'d F Y, h:i A'); ?></li>

					<?php if(!$plan->is_lifetime) { ?>

					<li class="list-group-item"><b><?= __('user.ending_on') ?>:</b> <?= dateFormat($plan->expire_at,'d F Y, h:i A'); ?></li>

					<?php } ?>

				<?php } else if($plan->payment_method=="Bank Transfer") { ?>

					<li class="list-group-item"><b><?= __('user.plan_date') ?>:</b>
						<?= dateFormat($plan->started_at,'d F Y') . " to ". $plan->expire_text ?>
					</li>

				<?php } ?>



				<?php if(!empty($plan->payment_details) && $plan->payment_details != "[]") {
					$payment_details = json_decode($plan->payment_details);
						foreach($payment_details as $key => $value) {
							if($key == 'payment_proof') {
								?>
								<li class="list-group-item"><b><?= __('user.payment_proof') ?>:</b> 
									<a target="_blank" href="<?php echo base_url('assets/user_upload/'.$value) ?>"><?php echo $value; ?></a>
								</li>
								<?php
							}
						}
					
				}?>

				<li class="list-group-item"><b><?= __('user.created_at') ?>:</b> <?= dateFormat($plan->created_at, 'd F Y, h:i A') ?></li>

			</ul>
		</div>
	</div>

	<div class="col-sm-6">
		<div class="card">
			<div class="card-header">
				<h5 class="card-title m-0"><?= __('user.plan_details') ?></h5>
			</div>
    		<ul class="list-group list-group-flush">
                <li class="list-group-item"><b><?= __('user.name') ?>:</b> <?= $plan->plan->name ?></li>
                <li class="list-group-item"><b><?= __('user.type') ?>:</b> 
                	<?php   
						if ($plan->plan) {
							if ($plan->plan->type == 'paid') {
								echo __('user.paid');
							}elseif ($plan->plan->type == 'free') {
								echo __('user.free');
							}else{
								echo $plan->plan->type;
							}
						}else{
							echo '';
						}
					?>
                </li>
                <li class="list-group-item"><b><?= __('user.price') ?>:</b> <?= c_format(($plan->plan  ? $plan->plan->price : 0)) ?></li>

                <li class="list-group-item"><b><?= __('user.special_price') ?>:</b> <?= c_format(($plan->plan ? $plan->plan->special : 0)) ?></li>
                <?php if($plan->commission_sale_status): ?>
                	<li class="list-group-item"><b><?= __('user.level') ?>:</b> <?= ($plan->level_number) ? $plan->level_number : __('user.default') ?></li>
                <?php endif ?>
                <li class="list-group-item"><b><?= __('user.user_type') ?>:</b> <?= ($plan->plan->user_type == 2) ? __('user.vendor') : __('user.affiliate') ?></li>
                <?php if($plan->plan->user_type == 2): ?>
                	<li class="list-group-item"><b><?= __('user.campaign') ?>:</b> <?= isset($plan->plan->campaign) ? $plan->plan->campaign : __('user.unlimited') ?></li>
                	<li class="list-group-item"><b><?= __('user.product') ?>:</b> <?= isset($plan->plan->product) ? $plan->plan->product : __('user.unlimited') ?></li>
                <?php endif ?>
                <li class="list-group-item"><b><?= __('user.description') ?>:</b></li>
            </ul>
            <div class="px-3 mt-2">
            	<?= $plan->plan->description ?>
            </div>
		</div>

		<div class="card mt-3">
			<div class="card-header">
				<h5 class="card-title m-0"><?= __('user.status_history') ?></h5>
			</div>
    		<div class="card-body m-0 p-0">
    			<div class="table-responsive">
    				<table class="table table-striped">
	    				<thead>
	    					<tr>
	    						<td width="100px"><?= __('user.status') ?></td>
	    						<td><?= __('user.note') ?></td>
	    					</tr>
	    				</thead>
	    				<tbody>
	    					<?php foreach ($history as $key => $value) { ?>
	    						<tr>
	    							<td><?= $value->status_text ?></td>
	    							<td><?= $value->comment ?></td>
	    						</tr>
	    					<?php } ?>
	    				</tbody>
	    			</table>
    			</div>
    		</div>
		</div>
	</div>
</div>

<script type="text/javascript">
    $(function() {
        start_plan_expiration_timer();
    });
</script>
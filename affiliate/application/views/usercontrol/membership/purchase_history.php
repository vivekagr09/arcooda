<div class="row">

	<div class="col-sm-12">

		<div class="card">

			<div class="card-header">

				<h5 class="card-title m-0"><?= __('user.purchase_history') ?></h5>

			</div>

			<div class="card-body p-0">

            	<div class="table-responsive m-0">

                	<table class="table table-striped">

                		<thead>

                			<tr>

                				<th width="1"><?= __('user.id') ?></th>

                				<th><?= __('user.plan_name') ?></th>

                				<th><?= __('user.price') ?></th>

                				<th><?= __('user.type') ?></th>

                				<th><?= __('user.plan_status') ?></th>
 
                                <th><?= __('user.payment_method') ?></th>

                				<th><?= __('user.remaining_time') ?></th>

                				<th><?= __('user.start_date') ?></th>

                				<th><?= __('user.end_date') ?></th>

                				<th width="180px"><?= __('user.created_at') ?></th>

                				<th width="180px"><?= __('user.action') ?></th>

                			</tr>

                		</thead>

                		<tbody>

                			<?php if(count($plans) == 0){ ?>

                        		<tr>

                        			<td colspan="100%" class="text-center"><?= __('user.no_records_found') ?></td>

                        		</tr>

                        	<?php } ?>

                			<?php foreach ($plans as $key => $plan) { ?>

                				<tr>

                					<td><?= $plan->id ?></td>

                					<td><?= ($plan->plan ? $plan->plan->name : '') ?></td>

                					<td><?= c_format($plan->total) ?></td>

                					<td>
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
                                    </td>

                					<td><?= $plan->active_text ?></td>

                                    <td>
                                        <?= $plan->payment_method ?>
                                        <?php if($plan->payment_details) { ?>
                                        <?php $payment_details = json_decode($plan->payment_details, true); ?>
                                            <?php if(isset($payment_details['transaction_id'])) { ?>
                                            <br><b><?= __('user.transaction_id') ?>:</b>  <?= $payment_details['transaction_id'] ?>
                                            <?php } ?>
                                            <?php if(isset($payment_details['payment_status'])) { ?>
                                            <br><b><?= __('user.payment_status') ?>:</b> <span class="badge <?php if(in_array(strtolower($payment_details['payment_status']), array('completed','succeeded','success','complete','paid','active'))) { ?>bg-success<?php }else{ ?>bg-danger<?php } ?>"><?= ucfirst($payment_details['payment_status']) ?></span> 
                                            <?php } ?>
                                        <?php } ?>
                                    </td>

                					<td>
									
										<?php 

                                            if($plan->status_id == 1 && !($plan->is_lifetime))
                                            {
                                                $strToTimeRemains = $plan->strToTimeRemains();
                                                ?>
                                                <span data-time-remains="<=$strToTimeRemains;?>"><?= $plan->remainDay(); ?></span>
                                                <?php
                                            }
                                            else
                                            {
                                                echo $plan->remainDay();
                                            } 
                                            ?>
										
									</td>

                					<td><?= dateFormat($plan->started_at,'d/m/Y') ?></td>

                					<td><?= $plan->expire_text ?></td>

                					<td><?= dateFormat($plan->created_at) ?></td>

                					<td>

                						<a href="<?= base_url('usercontrol/membership_purchase_details/'. $plan->id) ?>" class="btn btn-sm btn-primary">Details</a>

                					</td>

                				</tr>

                			<?php } ?>

                		</tbody>

                	</table>

            	</div>

            </div>

            <?php if($links){ ?>

                <div class="card-footer text-right">

                	<div class="pull-left">

                		<?= $links[1] ?>

                	</div>

                	<div class="pull-right">

                    	<ul class="pagination m-0"><?= $links[0] ?></ul>

                    </div>

                </div>

           <?php } ?>

		</div>

	</div>

</div>

<script type="text/javascript">
    $(function() {
        start_plan_expiration_timer();
    });
</script>
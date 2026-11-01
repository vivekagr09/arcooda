<div class="row">
	<div class="col-sm-6">
		<div class="card mb-2">
			<div class="card-header bg-secondary text-white">
				<h5><?= __('admin.purchase_details') ?></h5>
			</div>

			<ul class="list-group list-group-flush">
				<li class="list-group-item"><b><?= __('admin.id') ?>:</b> <?= $plan->id ?></li>
				<li class="list-group-item"><b><?= __('admin.plan_name') ?>:</b> <?= ($plan->plan ? $plan->plan->name : '') ?></li>

				<li class="list-group-item"><b><?= __('admin.price') ?>:</b> <?= c_format(($plan->plan ? $plan->plan->price : 0)) ?></li>

				<li class="list-group-item"><b><?= __('admin.special_price') ?>:</b> <?= c_format(($plan->plan ? $plan->plan->special : 0)) ?></li>

				<?php 

					$bonus = $plan->bonusData();

					if($bonus){

				?>

				<li class="list-group-item"><b><?= __('admin.bonus') ?>:</b> <?= c_format($bonus->amount) ?></li>

				<?php } else { ?>

				<li class="list-group-item"><b><?= __('admin.bonus') ?>:</b> <?= __('admin.no_bonus') ?></li>

				<?php } ?>

				<li class="list-group-item"><b><?= __('admin.type') ?>:</b> 
					<?php   
						if ($plan->plan) {
                            if ($plan->plan->type == 'paid') {
                                echo __('admin.paid');
                            }elseif ($plan->plan->type == 'free') {
                                echo __('admin.free');
                            }else{
                                echo $plan->plan->type;
                            }
                        }else{
                            echo '';
                        }
					?>
				</li>

				<li class="list-group-item"><b><?= __('admin.plan_status') ?>:</b> <?= $plan->active_text ?></li>

				<li class="list-group-item"><b><?= __('admin.payment_status') ?>:</b> <?= $plan->status_text ?></li>



				<?php if($plan->status_id == 1) { ?>

					<?php if(!$plan->is_lifetime) { ?>

					<li class="list-group-item"><b><?= __('admin.remaining_time') ?>:</b> <?= $plan->remainDay(); ?></li>

					<?php } ?>

					<li class="list-group-item"><b><?= __('admin.started_on') ?>:</b> <?= dateFormat($plan->started_at,'d F Y, h:i A'); ?></li>

					<?php if(!$plan->is_lifetime) { ?>

					<li class="list-group-item"><b><?= __('admin.ending_on') ?>:</b> <?= dateFormat($plan->expire_at,'d F Y, h:i A'); ?></li>

					<?php } ?>

				<?php } ?>

				
				<?php if(!empty($plan->payment_details) && $plan->payment_details != "[]") {
					$payment_details = json_decode($plan->payment_details);
					foreach($payment_details as $key => $value) {
						if($key == 'payment_proof') {
							?>
							<li class="list-group-item"><b><?= __('admin.payment_proof') ?>:</b> 
								<a target="_blank" href="<?php echo base_url('assets/user_upload/'.$value) ?>"><?php echo $value; ?></a>
							</li>
							<?php
						}
					}
				}?>

				<li class="list-group-item"><b><?= __('admin.created_at') ?>:</b> <?= dateFormat($plan->created_at, 'd F Y, h:i A') ?></li>

			</ul>

		</div>



		

		<?php if(!$plan->is_lifetime && $plan->status_id == 1) { ?>

		<div class="card mb-2">

			<div class="card-header bg-secondary text-white">

				<h5><?= __('admin.edit_plan') ?></h5>

			</div>

			<div class="card-body">

				<form id="plan-form">

					<div class="form-group">

						<label class="form-control-label"><?= __('admin.expire_on') ?></label>

						<input type="text" value="<?= dateFormat($plan->expire_at,'d-m-Y H:i') ?>" name="expire_at" class='form-control datepicker'>

					</div>

				</form>

			</div>

			<div class="card-footer text-right">

				<button class="btn btn-save-plan btn-primary btn-sm"><?= __('admin.save_plan') ?></button>

			</div>

		</div>

		<?php } ?>

	</div>



	<div class="col-sm-6">

		<div class="card">

			<div class="card-header bg-secondary text-white">

				<h5><?= __('admin.plan_details') ?></h5>

			</div>

    		<ul class="list-group list-group-flush">

                <li class="list-group-item"><b><?= __('admin.name') ?>:</b> <?= $plan->plan->name ?></li>

                <li class="list-group-item"><b><?= __('admin.type') ?>:</b> 
                	<?php  
                		if ($plan->plan->type == 'paid') {
                            echo __('admin.paid');
                        }elseif ($plan->plan->type == 'free') {
                            echo __('admin.free');
                        }else{
                            echo $plan->plan->type;
                        }
                	?>
                </li>

                <li class="list-group-item"><b><?= __('admin.price') ?>:</b> <?= c_format($plan->plan->price) ?></li>

                <li class="list-group-item"><b><?= __('admin.special_price') ?>:</b> <?= c_format(($plan->plan ? $plan->plan->special : 0)) ?></li>

                <li class="list-group-item"><b><?= __('admin.description') ?>:</b></li>

            </ul>

            <div class="px-3 mt-2">

            	<?= $plan->plan->description ?>

            </div>

		</div>



		<div class="card mt-3">

			<div class="card-header bg-secondary text-white">

				<h5><?= __('admin.status_history') ?></h5>

			</div>

    		<div class="card-body m-0 p-0">

    			<div class="table-responsive">

    				<table class="table table-striped">

	    				<thead>

	    					<tr>

	    						<td width="100px"><?= __('admin.status') ?></td>

	    						<td><?= __('admin.note') ?></td>

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

	    			<div class="card-body border-top">

	    				<h6 class="mt-0 text-primary"><?= __('admin.status_history') ?></h6>

	    				<div class="add-history">

	    					<div class="form-group">

		    					<label class="form-control-label"><?= __('admin.status') ?></label>

		    					<select class="form-control" name="status_id">

		    						<option value=""><?= __('admin.select_status') ?></option>

		    						<?php foreach (App\MembershipPlan::$status_list as $key => $value) { ?>

		    							<option value="<?= $key ?>">
		    								<?php   
												if ($value == 'Received') {
													echo __('admin.received');
												}elseif ($value == 'Complete') {
													echo __('admin.complete');
												}elseif ($value == 'Total not match') {
													echo __('admin.total_not_match');
												}elseif ($value == 'Denied') {
													echo __('admin.denied');
												}elseif ($value == 'Expired') {
													echo __('admin.expired');
												}elseif ($value == 'Failed') {
													echo __('admin.failed');
												}elseif ($value == 'Processed') {
													echo __('admin.processed');
												}elseif ($value == 'Refunded') {
													echo __('admin.refunded');
												}elseif ($value == 'Reversed') {
													echo __('admin.reversed');
												}elseif ($value == 'Voided') {
													echo __('admin.voided');
												}elseif ($value == 'Canceled Reversal') {
													echo __('admin.cancel_reversal');
												}elseif ($value == 'Waiting For Payment') {
													echo __('admin.waiting_for_payment');
												}elseif ($value == 'Pending') {
													echo __('admin.pending');
												}elseif ($value == 'Active') {
													echo __('admin.active');
												}else{
													echo $value;
												}
											?>
		    							</option>

		    						<?php } ?>

		    					</select>

		    				</div>

		    				<div class="form-group">

		    					<label class="form-control-label"><?= __('admin.comment') ?></label>

		    					<textarea class="form-control" name="comment"></textarea>

		    				</div>

		    				<div class="form-footer text-right mt-3">
		    					<button type="button" class="btn-add-commnet btn btn-primary"><?= __('admin.add_history') ?></button>

		    				</div>

	    				</div>

	    			</div>

    			</div>

    		</div>

		</div>

	</div>
</div>



<link href="<?php echo base_url('assets/css/datepicker.css'); ?>" rel="stylesheet" type="text/css" />

<script src="<?php echo base_url('assets/js/bootstrap-datepicker.js'); ?>"></script>



<script type="text/javascript">

	$(".datepicker").datetimepicker({ 

        autoclose: true, 

        todayHighlight: true,

		showSecond: true,

        format:"d-m-Y H:m"

    })



	$(".btn-save-plan").click(function(){

		$this = $(this);

		$.ajax({

			url:'<?= base_url("membership/submit_plan_update/". $plan->id) ?>',

			type:'POST',

			dataType:'json',

			data:$("#plan-form").serialize(),

			beforeSend:function(){$this.btn("loading");},

			complete:function(){$this.btn("reset");},

			success:function(json){

				$container = $("#plan-form");

				$container.find(".is-invalid").removeClass("is-invalid");

				$container.find("span.invalid-feedback").remove();

		

				if (json['location']) {

					window.location.href= json['location'];

				}

				

				if(json['errors']){

				    $.each(json['errors'], function(i,j){

				        $ele = $container.find('[name="'+ i +'"]');

				        if($ele){

				            $ele.addClass("is-invalid");

				            if($ele.parent(".input-group").length){

				                $ele.parent(".input-group").after("<span class='invalid-feedback'>"+ j +"</span>");

				            } else{

				                $ele.after("<span class='invalid-feedback'>"+ j +"</span>");

				            }

				        }

				    })

				}

			},

		})

	})



	$(".btn-add-commnet").click(function(){

		$this = $(this);

		$.ajax({

			url:'?addhistory=true',

			type:'POST',

			dataType:'json',

			data:$(".add-history :input"),

			beforeSend:function(){$this.btn("loading");},

			complete:function(){$this.btn("reset");},

			success:function(json){

				$container = $(".add-history");

				$container.find(".is-invalid").removeClass("is-invalid");

				$container.find("span.invalid-feedback").remove();

		

				if (json['reload']) {

					window.location.reload();

				}

				

				if(json['errors']){

				    $.each(json['errors'], function(i,j){

				        $ele = $container.find('[name="'+ i +'"]');

				        if($ele){

				            $ele.addClass("is-invalid");

				            if($ele.parent(".input-group").length){

				                $ele.parent(".input-group").after("<span class='invalid-feedback'>"+ j +"</span>");

				            } else{

				                $ele.after("<span class='invalid-feedback'>"+ j +"</span>");

				            }

				        }

				    })

				}

			},

		})

	})

</script>
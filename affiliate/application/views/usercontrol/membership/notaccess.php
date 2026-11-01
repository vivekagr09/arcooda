<?php 
$is_vendor = 0;
if (isset($_SESSION) && isset($_SESSION['user']['is_vendor']) && $_SESSION['user']['is_vendor']) {
	$is_vendor = $_SESSION['user']['is_vendor'];
}
?>
<div class="row">
	<div class="col-sm-12">
		<?php if($MembershipSetting['status'] && isset($plan) && $plan){ ?>
	        <div class="new-card pb-3 shadow-sm m-0">
	            <div class="card-header">
	                <h5 class="card-title"><?= __('user.active_plan') ?></small></h5>
	            </div>
	            
	            <?php if (isset($is_lifetime_plan) && $is_lifetime_plan) { ?>
	                <div class="card-body">
	                    <h4 class="text-center text-success"><?= __('user.lifetime_free_membership') ?></h4>
	                    <p class="text-center text-muted"><?= __('user.you_have_a_lifetime') ?></p>
	                </div>
	            <?php } ?>
	            <?php 

	            if (isset($plan) && $plan) {
	            	$current_plan_name = $plan->plan->name;
	            	$current_plan_expire = $plan->expire_at;?>
	                <div class="card-body">
	                    <h4 class="text-success"><span class="text-muted"><?= __('user.plan') ?>: </span><?= $plan->plan ? $plan->plan->name : '' ?></h4>
	                </div>
	                <ul class="list-group list-group-flush">
	                    <li class="list-group-item">
	                        <span><?= __('user.plan_date') ?></span>
	                        <span class="text-right pull-right text-primary">
							<?php 
								$remain = $plan->remainDay();
								$planto = ($plan->is_lifetime) ? __('user.lifetime') : dateFormat($plan->expire_at,'d F Y h:i A');
							?>
							<?= dateFormat($plan->started_at,'d F Y h:i A')." ".__('user.to')." ". $planto ?>
	                        </span>  
	                    </li>
	                    <li class="list-group-item">
	                        <span class="d-inline-block"><?= __('user.remaining_time') ?></span>
	                        <span class=" pull-right text-primary text-right">
	                            <?php
	                                if($plan->is_lifetime){
										echo '<span class="font-32" style="line-height: 22px;">&infin;</span>';
									} else {
										echo "<span data-time-remains='".$plan->strToTimeRemains()."'>". $remain ."</span>";
									}
	                            ?>
	                        </span>  
	                    </li> 
	                    <li class="list-group-item">
	                        <span><?= __('user.plan_status') ?></span>
	                        <span class="text-right pull-right text-primary">
	                            <?= $plan->active_text ?>
	                        </span>  
	                    </li>
	                </ul>
	                <!-- <div class="card-body">
	                    <?= $plan->plan ? $plan->plan->description : '' ?>
	                </div> -->
	            <?php }else{
	            	$current_plan_name = 'NULL';
	            	$current_plan_expire = 'NULL';
	            } ?>
	        </div>
		    <div class="new-card border card-toggle shadow-sm">
	            <div class="card-header">
	                <h6 class='card-title'><?= __('user.description') ?></h6>
	                <div class="card-options">
	                    <button class="open-close-button"></button>
	                </div>
	            </div>
	            <div class="card-container">
	                <div class="card-body pb-0">
	                    <?= $plan->plan ? $plan->plan->description : '' ?>
	                </div>
	            </div>
	        </div>
	    <?php }else{ ?>
	    	<h4 class="notification_on_pages">
				<span class="badge bg-secondary"><?= __('user.currently_not_have_paid_plan') ?></span>
			</h4>
	    <?php } ?>
	</div>
</div>
<div class="row text-center">
	<div class="col-sm-12">
		<h5><?= __('user.our_membership_plans') ?></small></h5>
	</div>
	<?php
	 foreach ($plans as $key => $plan) { 
		if ($is_vendor) {
			if ($plan->user_type == '2') { ?>
				<div class="col-lg-3">
			        <div class="card plan-card mb-4">
			            <div class="card-body" style="position: relative;overflow: hidden;">
			            	<?php if($plan->label_text) { ?>
			                <span class="plan-label" style="background: <?= $plan->label_background ?>;color: <?= $plan->label_color ?>;"><?= $plan->label_text ?></span>
			                <?php } ?>
			                <div class="pt-3 pb-3">
			                    <h6 class="text-uppercase text-primary"><?= $plan->name ?></h6>
			                </div>
			                <div>
			                    <h1 class="plan-price padding-b-15">
			                    	<?php  if($plan->price == 0){ ?>
			                    		<?= __('user.free') ?>
			                    	<?php } else { ?>
			                    		<?php if($plan->special) { ?>
			                    		<?= c_format($plan->special) ?>
			                    		<?php }else{ ?>
			                    		<?= c_format($plan->price) ?>
			                    		<?php } ?>
			                    	<?php } ?>
			                    </h1>
			                    <?php if($plan->special) {
			                    	$percentage = round((($plan->price - $plan->special) * 100) / $plan->price);
			                    ?>
			                    <h4><span class="price" style="text-decoration: line-through;color: gray"><?= c_format($plan->price) ?></span> <span class="badge" style="background: <?= $plan->label_background ?>;color: <?= $plan->label_color ?>;"><?= __('user.save_now') ?> <?= $percentage ?>%!</span></h4>
			                    <?php } ?>
			                 
			                    <div class="text-muted m-l-10">
			                    	<sup><?php
					                    if($plan->billing_period == "lifetime_free") {
			                                echo __('user.lifetime');
			                            } else if($plan->billing_period == "custom") {
			                                echo $plan->custom_period." ".__('user.days');
			                            } else {
			                            	if (strtolower($plan->billing_period) == 'monthly') {
			                            		echo __('user.monthly');
			                            	}elseif (strtolower($plan->billing_period) == 'yearly') {
			                            		echo __('user.yearly');
			                            	}elseif (strtolower($plan->billing_period) == 'weekly') {
			                            		echo __('user.weekly');
			                            	}elseif (strtolower($plan->billing_period) == 'daily') {
			                            		echo __('user.daily');
			                            	}else{
			                            		echo ucwords(strtolower($plan->billing_period));
			                            	}
			                            }
			                        ?></sup>
		                        </div>
			                    <div class="plan-div-border"></div>
			                </div>
			                <?php if($plan->commission_sale_status): ?>
			                	<?php $sale_comission_rate = ($plan->sale_comission_rate) ? $plan->sale_comission_rate.'%' : __('user.default') ?>
			                	<p style="background: <?= $plan->label_background ?>;color: <?= $plan->label_color ?>;"><?= __('user.affiliate_commission').' '.$sale_comission_rate ?></p>
			                <?php endif ?>
			                <div class="plan-features pb-3 mt-3 text-muted padding-t-b-30">
			                	<?php if($plan->user_type == 2): ?>
			                		<p>
				                		<b><?= __('user.campaign') ?> : </b>
				                		<?= isset($plan->campaign) ? $plan->campaign : __('user.unlimited') ?>
				                	</p>
				                	<p>
				                		<b><?= __('user.product') ?> : </b>
				                		<?= isset($plan->product) ? $plan->product : __('user.unlimited') ?>
				                	</p>
			                	<?php endif ?>
			                	
			                    <?= $plan->description ?>

			                    <?php if($plan->name == $current_plan_name) {
		                    		$plan_status = '1';
		                    		$current_plan_expire_var = $current_plan_expire;
		                    	}else{
		                    		$plan_status = '0';
		                    		$current_plan_expire_var = '0';
		                    	} ?>

			                    <a href="javascript:void(0)" onclick="choosePlan(<?= $plan->id ?>,<?php if($plan->special) { ?><?= $plan->special ?><?php }else{ ?><?= $plan->price ?><?php } ?>, <?= $plan_status ?>,'<?= $current_plan_expire_var ?>')" class="btn btn-primary"><?= __('user.puchase_now') ?>
			                   </a>
			                </div>
			                <?php if($plan->bonus) { ?>
			                <div class="bonus">
			                	<label><?= __('user.bonus_rate') ?></label>
			                	<p class="m-0"><?= c_format($plan->bonus) ?></p>
			                </div>
			            	<?php } ?>
			            </div>
			        </div>
			    </div>
			<?php }
		} else { ?>
			<div class="col-lg-3">
		        <div class="card plan-card mb-4">
		            <div class="card-body" style="position: relative;overflow: hidden;">
		            	<?php if($plan->label_text) { ?>
		                <span class="plan-label" style="background: <?= $plan->label_background ?>;color: <?= $plan->label_color ?>;"><?= $plan->label_text ?></span>
		                <?php } ?>
		                <div class="pt-3 pb-3">
		                    <h6 class="text-uppercase text-primary"><?= $plan->name ?></h6>
		                </div>
		                <div>
		                    <h1 class="plan-price padding-b-15">
		                    	<?php  if($plan->price == 0){ ?>
		                    		<?= __('user.free') ?>
		                    	<?php } else { ?>
		                    		<?php if($plan->special) { ?>
		                    		<?= c_format($plan->special) ?>
		                    		<?php }else{ ?>
		                    		<?= c_format($plan->price) ?>
		                    		<?php } ?>
		                    	<?php } ?>
		                    </h1>
		                    <?php if($plan->special) {
		                    	$percentage = round((($plan->price - $plan->special) * 100) / $plan->price);
		                    ?>
		                    <h4><span class="price" style="text-decoration: line-through;color: gray"><?= c_format($plan->price) ?></span> <span class="badge" style="background: <?= $plan->label_background ?>;color: <?= $plan->label_color ?>;"><?= __('user.save_now') ?> <?= $percentage ?>%!</span></h4>
		                    <?php } ?>
		                 
		                    <div class="text-muted m-l-10">
		                    	<sup><?php
			                    	if($plan->billing_period == "lifetime_free") {
		                                echo __('user.lifetime');
		                            } else if($plan->billing_period == "custom") {
		                                echo $plan->custom_period." ".__('user.days');
		                            } else {
		                                if (strtolower($plan->billing_period) == 'monthly') {
		                            		echo __('user.monthly');
		                            	}elseif (strtolower($plan->billing_period) == 'yearly') {
		                            		echo __('user.yearly');
		                            	}elseif (strtolower($plan->billing_period) == 'weekly') {
		                            		echo __('user.weekly');
		                            	}elseif (strtolower($plan->billing_period) == 'daily') {
		                            		echo __('user.daily');
		                            	}else{
		                            		echo ucwords(strtolower($plan->billing_period));
		                            	}
		                            }
		                        ?></sup>
	                        </div>
		                    <div class="plan-div-border"></div>
		                </div>
		                <?php if($plan->commission_sale_status): ?>
		                	<?php $sale_comission_rate = ($plan->sale_comission_rate) ? $plan->sale_comission_rate.'%' : __('user.default') ?>
		                	<p style="background: <?= $plan->label_background ?>;color: <?= $plan->label_color ?>;"><?= __('user.affiliate_commission').' '.$sale_comission_rate ?></p>
		                <?php endif ?>
		                <div class="plan-features pb-3 mt-3 text-muted padding-t-b-30">
		                	<?php if($plan->user_type == 2): ?>
		                		<p>
			                		<b><?= __('user.campaign') ?> : </b>
			                		<?= isset($plan->campaign) ? $plan->campaign : __('user.unlimited') ?>
			                	</p>
			                	<p>
			                		<b><?= __('user.product') ?> : </b>
			                		<?= isset($plan->product) ? $plan->product : __('user.unlimited') ?>
			                	</p>
		                	<?php endif ?>
		                	
		                    <?= $plan->description ?>
		                    <?php ?>
		                    <?php if($plan->name == $current_plan_name) {
		                    	$plan_status = '1';
	                    		$current_plan_expire_var = $current_plan_expire;
	                    	}else if($current_plan_expire!="" or $current_plan_expire!=null) 
		                    	{
		                    		$plan_status = '1';
		                    		$current_plan_expire_var = $current_plan_expire;
		                    	}
	                    	else{
	                    		$plan_status = '0';
	                    		$current_plan_expire_var = '0';
	                    	} ?>

		                    <a href="javascript:void(0)" onclick="choosePlan(<?= $plan->id ?>, 
		                    	<?php if($plan->special) { ?><?= $plan->special ?><?php }else{ ?><?= $plan->price ?><?php } ?>, <?= $plan_status ?>, '<?= strtotime($current_plan_expire_var) ?>')" class="btn btn-primary"><?= __('user.puchase_now') ?></a>
		                </div>
		                <?php if($plan->bonus) { ?>
		                <div class="bonus">
		                	<label><?= __('user.bonus_rate') ?></label>
		                	<p class="m-0"><?= c_format($plan->bonus) ?></p>
		                </div>
		            	<?php } ?>
		            </div>
		        </div>
		    </div>
		<?php }
	} ?>

	<div class="modal" id="model-payments">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h6 class="modal-title"><?= __('user.choose_payment_method') ?></h6>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	        <ul class="list-group">
	        	<?php foreach($payment_gateways as $key => $value){ ?>
		        	<li class="list-group-item text-left">
		        		<a href="javascript:void(0)" onclick="buy('<?= $value['name'] ?>')"><?= $value['title'] ?></a>
		        	</li>
		        <?php } ?>
	        </ul>
	        <?php if(empty($payment_gateways)){ ?>
	      		<div class="alert alert-info"><?= __('user.no_payment_method_available') ?>  <a href="<?= base_url('usercontrol/contact-us') ?>"><?= __('user.contact') ?> </a> <?= __('user.administrator') ?> </div>
	      	<?php } ?>
	      	<div class="alert-error-message mt-2"></div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('user.close') ?></button>
	      </div>
	    </div>
	  </div>
	</div>

	<div class="modal" id="model-free-plan">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h6 class="modal-title"><?= __('user.membership_notification_alert') ?></h6>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	      	<div><?= __('user.register_for_free_plan') ?></div><br>
	      	<div><?= __('user.plan_end_at') ?> :</div><br>
	      	<div><?php echo $planto; ?></div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('user.close') ?></button>
	      </div>
	    </div>
	  </div>
	</div>


	<div class="modal" id="model-pending-plan">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h6 class="modal-title"><?= __('user.membership_notification_alert') ?></h6>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	      	<h6 class="text-warning"><?= __('user.pending_plan_warning') ?></h6>
	      	<div class="text-center"><?= __('user.id') ?>: <span id="pendingplan_id"></span></div>
	      	<div class="text-center"><?= __('user.plan_name') ?>: <span id="pendingplan_name"></span></div>
	      	<div class="text-center"><?= __('user.payment_status') ?>: <span id="pendingplan_status"></span></div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('user.close') ?></button>
	      </div>
	    </div>
	  </div>
	</div>

	<script type="text/javascript">
		var plan_id = 0;
		var userCurrency = '<?= $this->session->userdata('userCurrency'); ?>';
		function buy(paymentGateway){
			let paystackAccptCurrencies = ['GHS' , 'NGN', 'USD', 'ZAR'];
			if(paymentGateway == 'paystack' && ! paystackAccptCurrencies.includes(userCurrency)){
				$('.alert-error-message').html('<div class="alert alert-danger">' + '<?= __('user.paystack_accept_only_currency') ?>' + '</div>');
				return false;
			}
			if (paymentGateway == 'xendit' && (userCurrency != 'IDR' && userCurrency != 'PHP')) {
			    $('.alert-error-message').html('<div class="alert alert-danger">' + '<?= __('user.xendit_accept_only_currency') ?>' + '</div>');
			    return false;
			}
			if(paymentGateway == 'yookassa' && userCurrency != 'RUB'){
				$('.alert-error-message').html('<div class="alert alert-danger">' + '<?= __('user.yookassa_accept_only_currency') ?>' + '</div>');
				return false;
			}
			$('.alert-error-message').empty();
			window.location.href = "<?= base_url('membership/buy_membership/') ?>" + plan_id + '/' + paymentGateway;
		}

		function choosePlan(pID, price, plan_status, plan_expire) {
		    var today = new Date();
		    var datetime = Date.parse(today) / 1000;

		    if (price > 0) {
		        plan_id = pID;
		        $.ajax({
		            url: '<?= base_url("membership/checkPendingPlan") ?>',
		            type: 'POST',
		            dataType: 'json',
		            data: { 'action': 'pendingplan' },
		            success: function (json) {
		                if (json.status == 'true') {
		                    console.log(json);
		                    $("#model-payments").modal("show");
		                } else if (json.status == 'no_plan') {
		                    $("#model-payments").modal("show");
		                } else if (json.plan_id != '') {
		                    $("#pendingplan_id").html(json.plan_id);
		                    $("#pendingplan_name").html(json.name);
		                    $("#pendingplan_status").html(json.payment_status);
		                    $("#model-pending-plan").modal("show");
		                } else {
		                    var message = '<?= __("user.something_went_wrong_please_try_again") ?>';
		                    Swal.fire({
		                        icon: 'error',
		                        text: message,
		                    });
		                }
		            },
		        });
		    } else {
		        if (plan_status == '1') {
		            if (datetime > plan_expire) {
		                window.location.href = "<?= base_url('membership/buy_membership') ?>/" + pID;
		            } else {
		                $("#model-free-plan").modal("show");
		            }
		        } else {
		            window.location.href = "<?= base_url('membership/buy_membership') ?>/" + pID;
		        }
		    }
		}


	</script>
	<script type="text/javascript">
	    $(".card-toggle .open-close-button").click(function(){
	        $(this).parents(".card-toggle").toggleClass("open")
	    })
	</script>
</div>
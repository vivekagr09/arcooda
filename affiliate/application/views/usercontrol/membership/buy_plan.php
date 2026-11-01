<div class="row text-center">
	<div class="col-lg-4"></div>
	<div class="col-lg-4">
        <div class="card plan-card mb-4">
            <div class="card-body" style="position: relative;overflow: hidden;">
                <?php if($plan->label_text) { ?>
                    <span class="plan-label" style="background: <?= $plan->label_background ?>;color: <?= $plan->label_color ?>;">
                        <?= $plan->label_text ?>
                    </span>
                <?php } ?>
                <div class="pt-3 pb-3">
                    <h6 class="text-uppercase text-primary"><?= $plan->name ?></h6>
                </div>
                <div>
                    <h1 class="plan-price padding-b-15">
                    	<?php  if($plan->price == 0){
                    		echo 'FREE';
                    	} else {
                    		if($plan->special)
                                echo c_format($plan->special);
                            else
                                echo c_format($plan->price);
                    	} ?>
                    </h1>
                    <?php if($plan->special){
                        $percentage = round((($plan->price - $plan->special) * 100) / $plan->price); ?>
                            <h4>
                                <span class="price" style="text-decoration: line-through;color: gray">
                                    <?= c_format($plan->price) ?>
                                </span>
                                <span class="badge" style="background: <?= $plan->label_background ?>;color: <?= $plan->label_color ?>;">
                                    <?= __('user.save_now') ?> <?= $percentage ?>%!
                                </span>
                            </h4>
                    <?php } ?>
                    <div class="text-muted m-l-10">
                        <sup>
                            <?php
                                if($plan->billing_period == "lifetime_free") {
                                    echo "Lifetime";
                                } else if($plan->billing_period == "custom") {
                                    echo $plan->custom_period." Days";
                                } else {
                                    echo ucwords(strtolower($plan->billing_period));
                                }
                            ?>
                        </sup>
                    </div>
                    <div class="plan-div-border"></div>
                </div>
                <div class="plan-features mt-3 text-muted padding-t-b-30">
                    <?= $plan->description ?>
                </div>
                <div class="payment-module-membership">
                	<?php 
                        if(isset($confirm))
                            echo $confirm;
                    ?>
                </div>
                <?php if($plan->bonus){ ?>
                    <div class="bonus mt-3">
                        <label>Bonus Rate</label>
                        <p class="m-0"><?= c_format($plan->bonus) ?></p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function backCheckout() {
        window.location.href = '<?= base_url('usercontrol/purchase_plan') ?>';
    }
</script>
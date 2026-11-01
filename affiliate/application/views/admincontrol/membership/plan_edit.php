<div class="row" ng-app="affiliatePro" ng-controller="createPlan">
    <div class="col-12">
        <form id="form_plan">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card m-b-30">
                        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                            <h5><?= __('admin.edit_plan') ?></h5>
                            <a id="toggle-uploader" href="<?= base_url('membership/plans') ?>" class="btn btn-sm btn-light pull-right"><?= __('admin.back') ?></a>
                        </div>
                        <div class="card-body">
                        	<input type="text" class="d-none" name="id" ng-model='plan.id'>
                            <div class="form-group">
                            	<label class="form-control-label"><?= __('admin.name') ?></label>
                            	<input type="text" name="name" class="form-control" ng-model='plan.name'>
                            </div>

                            <div class="form-group">
                            	<label class="form-control-label"><?= __('admin.type') ?></label>
                            	<select name="type" class="form-control" ng-model='plan.type'>
                            		<option value=""><?= __('admin.choose_plan_type') ?></option>
                            		<option value="free"><?= __('admin.free') ?></option>
                            		<option value="paid"><?= __('admin.paid') ?></option>
                            	</select>
                                <div ng-show='plan.type == "free"' class="text-danger">
                                    <?= __('admin.default_trial_package_display_warning') ?>
                                </div>
                            </div>

                            <div class="form-group" ng-show='plan.type == "paid"'>
                            	<label class="form-control-label"><?= __('admin.price') ?></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><?= $CurrencySymbol ?></span></div>
                    	            <input type="text" name="price" class="form-control only-number-allow" ng-model='plan.price'>
                                </div>
                            </div>

                            <div class="form-group" ng-show='plan.type == "paid"'>
                                <label class="form-control-label"><?= __('admin.special_price') ?></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><?= $CurrencySymbol ?></span></div>
                                    <input type="text" name="special" class="form-control only-number-allow" ng-model='plan.special'>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label"><?= __('admin.welcome_bonus') ?></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><?= $CurrencySymbol ?></span></div>
                                    <input type="text" name="bonus" class="form-control only-number-allow" ng-model='plan.bonus'>
                                </div>
                            </div>

                            <?php if($award_level['status']){ ?>
                                <div class="form-group">
                                    <label class="form-control-label"><?= __('admin.commission_sale_status') ?></label>
                                    <select class="form-control" name="commission_sale_status" required>
                                        <option value=""><?= __('admin.choose_commission_sale_status') ?></option>
                                        <option <?= $plan->commission_sale_status == 0 ? 'selected' : '' ?> value="0"><?= __('admin.disable') ?></option>
                                        <option <?= $plan->commission_sale_status == 1 ? 'selected' : '' ?> value="1"><?= __('admin.enable') ?></option>
                                    </select>
                                </div>
                            <?php } else { ?>
                                <div class="form-group">
                                    <label class="form-control-label"><?= __('admin.commission_sale_status') ?></label>
                                    <select class="form-control">
                                        <option value=""><?= __('admin.disable') ?></option>
                                    </select>
                                </div>
                            <?php } ?>

                            <?php if($award_level['status']){ ?>
                                <div class="form-group <?= $plan->commission_sale_status == 0 ? 'd-none' : '' ?>">
                                    <label class="control-label"><?= __('admin.plan_level') ?></label>
                                    <select class="form-control" name="plan_level">
                                        <option value=""><?= __('admin.choose_plan_level') ?></option>
                                        <option <?= ($plan->level_id == 0) ? 'selected' : ''; ?> value="0"><?= __('admin.default') ?></option>
                                        <?php foreach($levels as $key => $value): ?>
                                            <?php $class = ($value['id'] == $plan->level_id) ? 'selected' : ''; ?> 
                                            <option <?= $class ?> value="<?= $value['id'] ?>"><?= $value['level_number'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            <?php } ?>

                            <div class="form-group">
                            	<div class="row">
                            		<div class="col">
                            	       <label class="form-control-label"><?= __('admin.billing_period') ?></label>
		                            	<select class="form-control" ng-model='plan.billing_period' name="billing_period">
		                            		<option value=""><?= __('admin.choose_billing_period') ?></option>
		                            		<option value="daily"><?= __('admin.daily') ?></option>
		                            		<option value="weekly"><?= __('admin.weekly') ?></option>
		                            		<option value="monthly"><?= __('admin.monthly') ?></option>
		                            		<option value="yearly"><?= __('admin.yearly') ?></option>
		                            		<option value="lifetime_free"><?= __('admin.lifetime') ?></option>
                                            <option value="custom"><?= __('admin.custom') ?></option>
		                            	</select>
                            		</div>
                            		<div class="col" ng-show='plan.billing_period=="custom"'>
                                        <label class="form-control-label"><?= __('admin.days') ?></label>
		                            	<div>
			                            	<input type="text" name="custom_period" ng-model='plan.custom_period' class="form-control only-number-allow" placeholder="<?= __('admin.enter_custom_in_days') ?>">
		                            	</div>
                            		</div>
                            	</div>
                            </div>

                            <div class="form-group" ng-show='plan.type == "paid"'>
                            	<label class="form-control-label">
	                            	<?= __('admin.add_free_trial') ?> &nbsp;
	                            </label>
	                            <div>
	                            	<label><input type="radio" value="0" ng-checked='plan.have_trail == "0"' name="have_trail" ng-model='plan.have_trail'> <?= __('admin.no') ?></label>
		                            <label><input type="radio" value="1" ng-checked='plan.have_trail == "1"' name="have_trail" ng-model='plan.have_trail'> <?= __('admin.yes') ?></label>
	                            </div>
                            </div>

                            <div class="form-group" ng-show='plan.have_trail == "1"'>
                            	<label class="form-control-label"><?= __('admin.free_trial') ?></label>
                            	<input type="text" placeholder="<?= __('admin.enter_free_trial_in_days') ?>" name="free_trail" class="form-control" ng-model='plan.free_trail'>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label"><?= __('admin.user_type') ?></label>
                                <select class="form-control" name="user_type" required>
                                    <option value=""><?= __('admin.choose_user_type') ?></option>
                                    <option <?= $plan->user_type == "1" ? 'selected' : '' ?> value="1"><?= __('admin.affiliate') ?></option>
                                    <option <?= $plan->user_type == "2" ? 'selected' : '' ?> value="2"><?= __('admin.vendor') ?></option>
                                </select>
                            </div>
                            <div class="form-group <?= ($plan->user_type == "2") ? '' : 'd-none' ?>">
                                <label class="form-control-label"><?= __('admin.campaign') ?></label>
                                <input type="number" name="campaign" min="0" class="form-control" value="<?= $plan->campaign ?>" placeholder="<?= __('admin.leave_empty_unlimited') ?>">
                            </div>
                            <div class="form-group <?= ($plan->user_type == "2") ? '' : 'd-none' ?>">
                                <label class="form-control-label"><?= __('admin.product') ?></label>
                                <input type="number" name="product" min="0" class="form-control" value="<?= $plan->product ?>" placeholder="<?= __('admin.leave_empty_unlimited') ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label"><?= __('admin.label_text') ?></label>
                                <input type="text" name="label_text" class="form-control" ng-model='plan.label_text'>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label"><?= __('admin.label_background') ?></label>
                                <input type="text" name="label_background" class="form-control" data-jscolor="{position:'right'}" ng-model='plan.label_background'>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label"><?= __('admin.label_color') ?></label>
                                <input type="text" name="label_color" class="form-control" data-jscolor="{position:'right'}" ng-model='plan.label_color'>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label"><?= __('admin.description') ?></label>
                                <textarea placeholder="<?= __('admin.enter_description') ?>" name="description" class="form-control summernote-img" ng-model='plan.description'></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label"><?= __('admin.sort_order') ?></label>
                                <input type="text" name="sort_order" class="form-control" ng-model='plan.sort_order'>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label"><?= __('admin.display') ?> </label>
                                <select class="form-control" name="status">
                                    <option value=""><?= __('admin.is_display') ?></option>
                                    <option <?= $plan->status == "1" ? 'selected' : '' ?> value="1"><?= __('admin.yes') ?></option>
                                    <option <?= $plan->status == "0" ? 'selected' : '' ?> value="0"><?= __('admin.no') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <img width="160px" src="<?= base_url('assets/login/multiple_pages') ?>/img/<?= $plan->plan_icon ? $plan->plan_icon : 'saturn.png' ?>" alt="saturn">
                        </div>
                         <div class="form-group">
                            <label class="form-control-label"><?= __('admin.plan_icon') ?></label>
                            <input type="file" id="plan_icon" name="plan_icon" class="form-control">
                        </div>

                        <div class="card-footer text-right">
                        	<button ng-click='submitForm($event,1)' class="btn-submit btn-primary btn" type="submit"> <?= __('admin.save_and_close') ?></button>
                            <button ng-click='submitForm($event,0)' class="btn-submit btn-primary btn" type="submit"> <?= __('admin.save') ?> </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div> 
</div>
<script type="text/javascript" src="<?= base_url('assets/js/angular.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/angular.tool.js?v='.time()) ?>"></script>

<script type="text/javascript">
	app.controller('createPlan', function($scope, $http) {
	    $scope.plan = <?= json_encode($plan) ?>;

	    $scope.submitForm = function($event, close){

            let formData = new FormData()
            let img = $('#plan_icon')[0].files[0]
            formData.append('plan_icon', img)
            
            $.ajax({
                url: "<?php echo base_url('membership/upload_plan_icon');?>",
                method: 'post',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    var data = $("#form_plan").serializeArray();
                    data.push({'name':'plan_icon','value' : response})
                      
                    ngCall($http, 'membership/submit_plan_form' + '?close='+close, {postData: data}, function(json){  
                        handle_res(json)
                    },function(){
                        $($event.currentTarget).addClass("btn-loading")
                    },function(){
                        $($event.currentTarget).removeClass("btn-loading")
                    })
                }
            })
        }
	});

    $("select[name='commission_sale_status']").on('change',function(){
        let value = $(this).val();
        if(value == 1){
            $("select[name='plan_level']").parents('.form-group').removeClass('d-none');
        } else {
            $("select[name='plan_level']").val(0);
            $("select[name='plan_level']").parents('.form-group').addClass('d-none');
        }
    })

    $("select[name='user_type']").on('change',function(){
        let value = $(this).val();
        if(value == 2){
            $("input[name='campaign'],input[name='product']").parents('.form-group').removeClass('d-none');
        } else {
            $("input[name='campaign'],input[name='product']").val('');
            $("input[name='campaign'],input[name='product']").parents('.form-group').addClass('d-none');
        }
    })
</script>
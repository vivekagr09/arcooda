<?php if(!empty($license_alret)) { ?>
  <h5 class="notification_on_pages mb-3">
    <div class="bg-danger text-white p-3 rounded">
      <?= $license_alret; ?>
    </div>
  </h5>
<?php } ?>

<div class="card">
    <div class="card-header bg-secondary text-white">
            <h5><?= __('admin.membership_settings') ?></h5>
        </div>
    <div class="card-body">
        <form method="post" action="" enctype="multipart/form-data" id="setting-form">
            <div class="row">
                <div class="col-12">
                    <ul class="nav nav-pills flex-column flex-sm-row" id="TabsNav">
                        <li class="nav-item flex-sm-fill text-sm-center">
                            <a class="nav-link active bg-secondary" data-bs-toggle="pill" href="#tab-setting"><?= __('admin.settings') ?></a>
                        </li>
                        <li class="nav-item flex-sm-fill text-sm-center">
                            <a class="nav-link" data-bs-toggle="pill" href="#tab-cron_jobs"><?= __('admin.cron_jobs') ?></a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-setting">
                            <div class="mb-3">
                                <label class="form-label"><?= __('admin.status') ?></label>
                                <select class="form-select" name="membership[status]">
                                    <option value="0" <?= ($membership['status'] == 0) ? 'selected' : '' ?>><?= __('admin.disable') ?></option>
                                    <option value="1" <?= ($membership['status'] == 1) ? 'selected' : '' ?>><?= __('admin.enable_for_all_users') ?></option>
                                    <option value="2" <?= ($membership['status'] == 2) ? 'selected' : '' ?>><?= __('admin.enable_for_all_vendors') ?></option>
                                    <option value="3" <?= ($membership['status'] == 3) ? 'selected' : '' ?>><?= __('admin.enable_for_all_affiliates') ?></option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><?= __('admin.show_epire_notification_interval_in_days') ?></label>
                                <input type="number" value="<?= $membership['notificationbefore'] ?>" class="form-control" name="membership[notificationbefore]">
                            </div>

                            <?php
                                $default_affiliate_plan_id = $membership['default_affiliate_plan_id'] ?? $membership['default_plan_id'];
                                $default_vendor_plan_id = $membership['default_vendor_plan_id'] ?? $membership['default_plan_id'];
                            ?>
                            <div class="mb-3">
                                <label class="form-label"><?= __('admin.default_plan_for_new_affiliates') ?></label>
                                <select class="form-select" name="membership[default_affiliate_plan_id]">
                                    <option value=""><?= __('admin.none') ?></option>
                                    <?php foreach ($plans as $key => $plan) {
                                        if($plan->user_type == 1) { ?>
                                        <option value="<?= $plan->id ?>" <?= $default_affiliate_plan_id == $plan->id ? 'selected' : '' ?>><?= $plan->name ?></option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><?= __('admin.default_plan_for_new_vendors') ?></label>
                                <select class="form-select" name="membership[default_vendor_plan_id]">
                                    <option value=""><?= __('admin.none') ?></option>
                                    <?php foreach ($plans as $key => $plan) {
                                        if($plan->user_type == 2) { ?>
                                        <option value="<?= $plan->id ?>" <?= $default_vendor_plan_id == $plan->id ? 'selected' : '' ?>><?= $plan->name ?></option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab-cron_jobs">
                            <div class="row">
                                <div class="col-sm-6">
                                    <h5><?= __('admin.what_is_cron_job') ?></h5>
                                    <p><?= __('admin.what_is_cron_job_answer') ?></p>

                                    <h6><?= __('admin.to_add_cron_job_steps') ?>:</h6>

                                    <ol>
                                        <li><?= __('admin.to_add_cron_job_step1') ?></li>
                                        <li><?= __('admin.to_add_cron_job_step2') ?></li>
                                        <li><?= __('admin.to_add_cron_job_step3') ?></li>
                                        <li><?= __('admin.to_add_cron_job_step4') ?>  <b><?= __('admin.once_per_minute') ?>(* * * * *)</b>.</li>
                                        <li><?= __('admin.to_add_cron_job_step5') ?> <div> <code>curl <?= base_url('/cronJob/expire_package_notification') ?></code></div> </li>
                                        <li><?= __('admin.to_add_cron_job_step6') ?></li>
                                    </ol>
                                </div>
                                <div class="col-sm-6">
                                    <img src="<?= base_url('assets/images/cronjob2.jpg') ?>" class="img-fluid">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary btn-submit"><?= __('admin.save') ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


<script type="text/javascript">
	$(".btn-submit").on('click',function(evt){
	    evt.preventDefault();
	    
    	var formData = new FormData($("#setting-form")[0]);  

	    $(".btn-submit").btn("loading");
	    formData = formDataFilter(formData);
	    $this = $("#setting-form");

	    $.ajax({
	        type:'POST',
	        dataType:'json',
	        cache:false,
	        contentType: false,
	        processData: false,
	        data:formData,
	        success:function(result){
	            $(".btn-submit").btn("reset");
	            $(".alert-dismissable").remove();

	            $this.find(".has-error").removeClass("has-error");
	            $this.find(".is-invalid").removeClass("is-invalid");
	            $this.find("span.text-danger").remove();
	            
	            if(result['location']){
	                window.location = result['location'];
	            }

	            if(result['success']){
	                showPrintMessage(result['success'],'success');
	                var body = $("html, body");
					body.stop().animate({scrollTop:0}, 500, 'swing', function() { });

					$('.formsetting_error').text("");
					$('.productsetting_error').text("");
	            }

	            if(result['errors']){
	                $.each(result['errors'], function(i,j){
	                    $ele = $this.find('[name="'+ i +'"]');
	                    if(!$ele.length){ 
	                    	$ele = $this.find('.'+ i);
	                    }
	                    if($ele){
	                        $ele.addClass("is-invalid");
	                        $ele.parents(".form-group").addClass("has-error");
	                        $ele.after("<span class='d-block text-danger'>"+ j +"</span>");
	                    }
	                });

					errors = result['errors'];
					$('.formsetting_error').text(errors['formsetting_recursion_custom_time']);
					$('.productsetting_error').text(errors['productsetting_recursion_custom_time']);
	            }
	        },
	    });
	
	    return false;
	});
</script>

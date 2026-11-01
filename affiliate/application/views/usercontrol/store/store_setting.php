<div class="row">
	<div class="col-12">
		<ul class="nav nav-tabs nav-tabs-custom" id="TabsNav">
		    <li><a class="nav-link" data-bs-toggle="tab" href="#vendor_store_setting"><?= __('user.store_setting') ?></a></li>
		    <li><a class="nav-link active" data-bs-toggle="tab" href="#vendor_vendor_settings"><?= __('user.vendor_setting') ?></a></li>
		</ul>

		<div class="tab-content">
			<div id="vendor_vendor_settings" class="tab-pane fade in active show">
			    <div class="card">
			        <form class="setting-form">
			            <div class="card-body">
			                <div class="row">
			                    <div class="col-lg-12">
			                        <div class="mb-3">
			                            <div class="form-group">
			                                <label class="col-form-label"><?= __('user.other_affilite_sell_my_items') ?></label>
			                                <select class="form-select" name="vendor_status">
			                                    <option value="0"><?= __('user.not_sell_anyone') ?></option>
			                                    <option value="1" <?= (int)$setting['vendor_status'] == 1 ? 'selected' : '' ?>><?= __('user.sell_all_affiliates') ?></option>
			                                    <option value="2" <?= (int)$setting['vendor_status'] == 2 ? 'selected' : '' ?>><?= __('user.sell_my_affiliates') ?></option>
			                                </select>
			                            </div>

			                            <fieldset class="mb-2">
			                                <legend><?= __('user.product_commission') ?></legend>
			                                <div class="form-group">
			                                    <label class="col-form-label"><?= __('user.affiliate_click_commission'); ?></label>
			                                    <div class="form-group">
			                                        <div class="input-group mt-2">
			                                            <span class="input-group-text"><?= __('user.click') ?></span>
			                                            <input name="affiliate_click_count"  class="form-control" value="<?php echo $setting['affiliate_click_count']; ?>" type="text" placeholder='Clicks'>
			                                        </div>
			                                        <div class="input-group mt-2">
			                                            <span class="input-group-text"><?= $CurrencySymbol ?></span>
			                                            <input name="affiliate_click_amount" class="form-control" value="<?php echo $setting['affiliate_click_amount']; ?>" type="text" placeholder='Amount'>
			                                        </div>
			                                    </div>
			                                </div>

			                                <div class="form-group">
			                                    <label class="col-form-label"><?= __('user.affiliate_sale_commission'); ?></label>
			                                    <div class="form-group">
			                                        <div class="input-group mt-2">
			                                            <?php
			                                                $commission_type= array(
			                                                    'percentage' => 'Percentage (%)',
			                                                    'fixed'      => 'Fixed',
			                                                );
			                                            ?>
			                                            <select name="affiliate_sale_commission_type" class="form-select affiliate_sale_commission_type">
			                                                <?php foreach ($commission_type as $key => $value) { ?>
			                                                    <option <?= $setting['affiliate_sale_commission_type'] == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?></option>
			                                                <?php } ?>
			                                            </select>
			                                        </div>
			                                        <div class="input-group mt-2">
			                                            <?php if ($setting['affiliate_sale_commission_type'] == 'percentage'){ ?>
			                                                <span class="input-group-text">%</span>
			                                            <?php } else { ?>
			                                                <span class="input-group-text"><?= $CurrencySymbol ?></span>
			                                            <?php } ?>
			                                            <input name="affiliate_commission_value" id="affiliate_commission_value" class="form-control" value="<?php echo $setting['affiliate_commission_value']; ?>" type="text" placeholder='Sale Commission'>
			                                        </div>
			                                    </div>
			                                </div>
			                            </fieldset>
			                        </div>
			                    </div>
			                </div>
			            </div>
			            <div class="card-footer">
			                <button class="btn btn-primary btn-submit"><?= __('user.save_settings') ?></button>
			            </div>
			        </form>
			    </div>
			</div>
			<div id="vendor_store_setting" class="tab-pane fade">
			    <div class="card">
			        <form class="setting-form" enctype="multipart/form-data">
			            <input type="hidden" name="store_page_settings" value="1">
			            <div class="card-body">
			                <?php
			                    $store_meta = (!empty($store_details['store_meta'])) ? json_decode($store_details['store_meta'], true) : [];
			                    $store_logo = isset($store_meta['store_logo']) && !empty($store_meta['store_logo']) ? 'assets/user_upload/vendor_store/' . $store_meta['store_logo'] : 'assets/store/default/img/ct-banner-img.png';
			                    $cover_background = isset($store_meta['cover_background']) && !empty($store_meta['cover_background']) ? 'assets/user_upload/vendor_store/' . $store_meta['cover_background'] : 'assets/store/default/img/ctbg.png';
			                ?>
			                <div class="row">
			                    <div class="col-md-9">
			                        <div class="mb-3">
			                            <label class="form-label"><?= __('user.store_name') ?></label>
			                            <input type="text" name="store_name" class="form-control" value="<?= ($store_details['store_name']) ?>">
			                        </div>

			                        <div class="mb-3">
			                            <label class="form-label"><?= __('user.cover_text_color') ?></label>
			                            <input name="cover_text_color" value="<?= isset($store_meta['cover_text_color']) ? $store_meta['cover_text_color'] : ""; ?>" class="form-control jscolor" data-jscolor type="text">
			                        </div>

			                        <div class="mb-3">
			                            <label class="form-label"><?= __('user.show_your_name_on_cover') ?></label>
			                            <select name="cover_show_vendor_name" class="form-select">
			                                <option value="0" selected><?= __('user.no') ?></option>
			                                <option value="1" <?= isset($store_meta['cover_show_vendor_name']) && $store_meta['cover_show_vendor_name'] == 1 ? "selected" : ""; ?>><?= __('user.yes') ?></option>
			                            </select>
			                        </div>

			                        <div class="mb-3">
			                            <label class="form-label"><?= __('user.cover_background_image') ?></label>
			                            <div>
			                                <div class="d-inline-block">
			                                    <button class="btn btn-primary">
			                                        <span><?= __('admin.choose_file') ?></span>
			                                        <input id="cover_background" name="cover_background" class="form-control form-control-sm" type="file"/>
			                                    </button>
			                                </div>
			                                <img id="cover_background_preview" src="<?= base_url($cover_background); ?>" class="img-thumbnail" border="0" width="100%">
			                            </div>
			                        </div>
			                    </div>

			                    <div class="col-md-3">
			                        <div class="mb-3">
			                            <label class="form-label"><?= __('user.store_logo') ?></label>
			                            <div>
			                                <div class="d-inline-block">
			                                    <button class="btn btn-primary">
			                                        <span><?= __('admin.choose_file') ?></span>
			                                        <input id="store_logo" name="store_logo" class="form-control form-control-sm" type="file"/>
			                                    </button>
			                                </div>
			                                <img id="store_logo_preview" src="<?= base_url($store_logo); ?>" class="img-thumbnail" border="0" width="100%">
			                            </div>
			                        </div>
			                    </div>

			                    <div class="col-md-12">
			                        <div class="mb-3">
			                            <label class="form-label"><?= __('user.vendor_store_contact_us_map') ?></label>
			                            <textarea name="store_contact_us_map" class="form-control"><?= $store_details['store_contact_us_map']; ?></textarea> 
			                        </div>

			                        <div class="mb-3">
			                            <label class="form-label"><?= __('user.vendor_store_address') ?></label>
			                            <textarea name="store_address" class="form-control"><?= $store_details['store_address']; ?></textarea> 
			                        </div>

			                        <div class="row">
			                            <div class="col-md-6">
			                                <div class="mb-3">
			                                    <label class="form-label"><?= __('user.vendor_store_email') ?></label>
			                                    <input name="store_email" value="<?= $store_details['store_email']; ?>" class="form-control" type="email"> 
			                                </div>
			                            </div>
			                            <div class="col-md-6">
			                                <div class="mb-3">
			                                    <label class="form-label"><?= __('user.vendor_store_mobile_phone_number') ?></label>
			                                    <input name="store_contact_number" value="<?= $store_details['store_contact_number']; ?>" class="form-control" type="text"> 
			                                </div>
			                            </div>
			                        </div>

			                        <div class="mb-3">
			                            <label class="form-label"><?= __('user.vendor_store_terms_condition') ?></label>
			                            <textarea name="store_terms_condition" class="form-control"><?= $store_details['store_terms_condition']; ?></textarea> 
			                        </div>
			                    </div>
			                </div>
			            </div>
			            <div class="card-footer">
			                <button class="btn btn-primary"><?= __('user.save_settings') ?></button>
			                <?php if(isset($store_details['store_slug']) && !empty($store_details['store_slug'])) { ?>
			                <a href="<?= base_url('store/').$store_details['store_slug']; ?>" target="_blank" class="btn btn-info"><?= __('user.preview_store_page') ?></a>
			                <?php } ?>
			            </div>
			        </form>
			    </div>
			</div>
		</div>
	</div>
</div>

<script src="<?= base_url('assets/js/jscolor.js'); ?>"></script>

<script type="text/javascript">

	function readURLStorePage(input, previewer) {
		if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#'+previewer).attr('src', e.target.result);
	        };
	        reader.readAsDataURL(input.files[0]);
	    }
	}


	$("select[name='affiliate_sale_commission_type']").on('change',function(){
		let type = $(this).val();

		if(type == 'percentage')
			$('input[name="affiliate_commission_value"]').siblings('.input-group-prepend').find('.input-group-text').text('%');
		else
			$('input[name="affiliate_commission_value"]').siblings('.input-group-prepend').find('.input-group-text').text('$');
		

	})

	$("#cover_background").change(function () {
	    readURLStorePage(this, 'cover_background_preview');
	});

	$("#store_logo").change(function () {
	    readURLStorePage(this, 'store_logo_preview');
	});

	$(document).on('keyup', 'input[name="store_name"]', function(){
		let store_name = $(this).val();
		$.ajax({
			url : "<?= base_url('usercontrol/check_duplicate_store'); ?>",
	        type:'POST',
	        dataType:'json',
	        data:{store_name: store_name},
	        success:function(result){
	        	$ele = $('[name="store_name"]');
	            if(!$ele.length){ 
	            	$ele = $('.store_name');
	            }
	        	if(result.error) {
	            	if(!$ele.hasClass("is-invalid")) {
		                $ele.addClass("is-invalid");
		                $ele.parents(".form-group").addClass("has-error");
		                $ele.after("<span class='d-block text-danger'>"+ result.error +"</span>");
		            }
				} else {
	                $ele.removeClass("is-invalid");
	                $ele.parents(".form-group").removeClass("has-error");
	                $ele.parent().find('span.text-danger').remove();
				}
	        },
	    });
	});

	$(".setting-form").on('submit',function(evt){
	    evt.preventDefault();	    
    	var formData = new FormData(this);  

	    $(".btn-submit").btn("loading");
	    formData = formDataFilter(formData);
	    $this = $(this);

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

	            if(result.store_page_url) {
	            	$this.find('.btn-preview').attr('href', result.store_page_url);
	            }
	        },
	    });
		
	    return false;
	});

</script>
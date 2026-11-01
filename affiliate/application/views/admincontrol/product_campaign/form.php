<link rel="stylesheet" type="text/css" href="<?= base_url("assets/plugins/ui/jquery-ui.min.css") ?>">
<script type="text/javascript" src="<?= base_url('assets/plugins/ui/jquery-ui.min.js') ?>"></script>
<link rel="stylesheet" type="text/css" href="<?= base_url("assets/plugins/select2/select2.min.css") ?>">
<script type="text/javascript" src="<?= base_url('assets/plugins/select2/select2.full.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/datatable/moment.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/datatable/daterangepicker.min.js') ?>"></script>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/datatable/daterangepicker.css') ?>" />

<form class="form-horizontal" method="post" action=""  enctype="multipart/form-data" id="form_form">
	<input type="hidden" name="product_id" value="<?php echo $product['product_id'] ?>" />
	<div class="card">
		<div class="card-header bg-secondary text-white">
            <h5 class="pull-left">
            	<?= __('admin.product_details') ?>
            </h5>
		</div>
	<div class="card-body">
				<fieldset class="border p-3 rounded-3 mb-3 shadow-sm custom-design">
				    <legend class="px-2 fs-6 fw-bold"><?= __('admin.product_type'); ?></legend>
				    <div class="row g-3">
					    <div class="col-md-4">
					        <div class="form-check">
					            <input class="form-check-input invisible" type="radio" name="product_type" id="virtual" value="virtual" <?= ($product['product_type'] == 'virtual' || $product['product_type'] == '') ? 'checked="checked"' : '' ?> >
					            <label class="form-check-label btn btn-secondary w-100 proType <?= ($product['product_type'] == 'virtual' || $product['product_type'] == '') ? 'bg-primary' : 'bg-secondary' ?>" for="virtual" data-value="virtual">
					                <?= __('admin.virtual_product'); ?>
					            </label>
					        </div>
					    </div>
					    <div class="col-md-4">
					        <div class="form-check">
					            <input class="form-check-input invisible" type="radio" name="product_type" id="downloadable" value="downloadable" <?= ($product['product_type'] == 'downloadable') ? 'checked="checked"' : '' ?> >
					            <label class="form-check-label btn btn-secondary w-100 proType <?= ($product['product_type'] == 'downloadable') ? 'bg-primary' : 'bg-secondary' ?>" for="downloadable" data-value="downloadable">
					                <?= __('admin.downloadable_product'); ?>
					            </label>
					        </div>
					    </div>
				    </div>
				</fieldset>
<!--Active buttons style-->
<script>
	$(document).ready(function() {
	    $(".proType").click(function() {
	        // Remove bg-primary and add bg-secondary to all proType elements
	        $(".proType").removeClass("bg-primary").addClass("bg-secondary");

	        // Add bg-primary and remove bg-secondary from the clicked element
	        $(this).removeClass("bg-secondary").addClass("bg-primary");
	    });
	});
</script>
<!--Active buttons style-->
				<div class="form-group">
					<label class="col-form-label"><?= __('admin.product_promotion_url') ?></label>
					<div>
						<input placeholder="<?= __('admin.enter_product_promotion_url') ?>" name="product_url" id="product_url" value="<?php echo $product['product_url']; ?>" class="form-control" type="text" />
						<span class="text-danger product_url_error"></span>
					</div>
				</div>

<div class="row">
	<div class="col-sm-8">
		<div class="form-group">
			<label class="col-form-label"><?= __('admin.product_name') ?></label>
			<div>
				<input placeholder="<?= __('admin.enter_your_product_name') ?>" name="product_name" value="<?php echo $product['product_name']; ?>" class="form-control" type="text">
			</div>
		</div>
		<div class="form-group">
			<label class="col-form-label"><?= __('admin.product_sku') ?> </label>
			<div>
				<input placeholder="<?= __('admin.enter_your_product_sku') ?>" name="product_sku" id="product_sku" class="form-control" value="<?php echo $product['product_sku']; ?>" type="text">
			</div>
		</div>
		<div class="form-group">
			<label class="col-form-label"><?= __('admin.product_msrp') ?></label>
			<div>
				<input placeholder="<?= __('admin.product_msrp_placeholder') ?>" name="product_msrp" class="form-control" value="<?php echo $product['product_msrp']; ?>" type="number">
			</div>
		</div>
		<div class="form-group">
			<label class="col-form-label"><?= __('admin.product_price') ?></label>
			<div>
				<input placeholder="<?= __('admin.enter_your_product_price') ?>" name="product_price" class="form-control" value="<?php echo $product['product_price']; ?>" type="number">
			</div>
		</div>
		<div class="form-group">
			<label class="col-form-label"><?= __('admin.product_quantity') ?></label>
			<div>
				<input placeholder="<?= __('admin.product_quantity_placeholder') ?>" name="product_quantity" class="form-control" value="<?php echo $product['_meta_product_quantity']; ?>" type="number">
			</div>
		</div>
	</div>
	<!--product image section-->
	<div class="col-sm-4">
	    <div class="form-group form-image-group">
	        <label class="col-form-label"><?= __('admin.product_featured_image') ?></label>
	        <div class="mb-3">
	            <label for="product_featured_image" class="form-label"><?= __('admin.choose_file') ?></label>
	            <input class="form-control form-control-sm" type="file" id="product_featured_image" name="product_featured_image" onchange="readURL(this,'#featureImage')">
	        </div>
	        <?php 
	        $product_featured_image = isset($product['product_featured_image']) && $product['product_featured_image'] != '' 
	            ? 'assets/images/product/upload/thumb/' . $product['product_featured_image'] 
	            : 'assets/images/no_product_image.png'; 
	        ?>
	        <img src="<?php echo base_url($product_featured_image); ?>" id="featureImage" class="img-thumbnail" width="220px">
	    </div>
	</div>
	<!--product image section-->

		<div class="col-sm-12">

	<!--categories section-->
	<div class="form-group">
	    <label class="col-form-label"><?= __('admin.categories') ?></label>
	    <div class="category-container">
	        <input name="category_auto" placeholder="<?= __('admin.categories') ?>" id="category_auto" class="form-control" autocomplete="off">
	        <div style="max-height: 200px; overflow-y: auto;">
	            <ul class="list-group category-selected">
	                <?php if(isset($categories)){ ?>
	                    <?php foreach ($categories as $key => $category) { ?>
	                        <li class="list-group-item d-flex justify-content-between align-items-center">
	                            <span><?= $category['name'] ?></span>
	                            <input type="hidden" name="category[]" value="<?= $category['id'] ?>">
	                            <button type="button" class="btn btn-danger btn-sm remove-category">
	                                <i class="fa fa-trash"></i>
	                            </button>
	                        </li>
	                    <?php } ?>
	                <?php } ?>
	            </ul>
	        </div>
	    </div>
	</div>
	<!--categories section-->


	<div class="form-group">
		<label class="col-form-label"><?= __('admin.description') ?></label>
		<div>
			<textarea rows="4" placeholder="<?= __('admin.enter_your_product_description') ?>" class="form-control" name="product_description"  type="text"><?php echo $product['product_description']; ?></textarea>
		</div>
	</div>

	<div class="form-group">
		<label class="col-form-label"><?= __('admin.product_launching_datetime') ?></label>
		<div>
			<input placeholder="<?= __('admin.product_launching_datetime_placeholder') ?>" name="product_launching_datetime" class="form-control" value="<?= (!empty($product['_meta_product_launching_datetime'])) ? date('d-m-Y H:i', strtotime($product['_meta_product_launching_datetime'])) : ""; ?>" type="text">
		</div>
	</div>

	<div class="row">
		<?php
		if(isset($product['_meta_product_sale_start']) && ! empty($product['_meta_product_sale_start'])) {
			$product_sale_period = date('d-m-Y H:i:s', strtotime($product['_meta_product_sale_start']))." - ".date('d-m-Y H:i:s', strtotime($product['_meta_product_sale_end']));
		} else {
			$product_sale_period = '';
		}

		?>
		<div class="col-md-7">
			<div class="form-group">
				<label class="col-form-label"><?= __('admin.product_sale_period') ?></label>
				<div>
					<input placeholder="<?= __('admin.product_sale_period_placeholder') ?>" name="product_sale_period" class="form-control daterange-picker" type="text" value="<?= $product_sale_period; ?>" autocomplete="off">
				</div>
			</div>
		</div>
		<div class="col-md-5">
			<div class="form-group">
				<label class="col-form-label"><?= __('admin.product_sale_period_price') ?></label>
				<div>
					<input placeholder="<?= __('admin.product_sale_period_price') ?>" name="product_sale_price" class="form-control" type="number" value="<?= $product['_meta_product_sale_price']; ?>">
				</div>
			</div>
		</div>
	</div>

	<div class="row mt-4">
		<div class="col-sm-4">
			<div class="form-group mb-0 ">
				<label class="control-label"><?= __('admin.show_on_store'); ?></label>
				<div class="form-check form-switch">
				    <input class="form-check-input update_product_settings" type="checkbox" id="on_store" name="on_store" <?= (isset($product['on_store']) && (int)$product['on_store'] == 1) ? 'checked' : '' ?> data-setting_key="on_store" data-product_id="<?= $product['product_id'] ?>">
				    <label class="form-check-label" for="on_store"><?= __('admin.status_on'); ?> / <?= __('admin.status_off'); ?></label>
				</div>

			</div>
		</div>
		<div class="col-sm-4">
			<div class="form-group mb-0 ">
				<label class="control-label"><?= __('admin.show_to_affiliates'); ?></label>
				<div class="form-check form-switch">
				    <input class="form-check-input update_product_settings" type="checkbox" id="show_to_affiliates" name="show_to_affiliates" <?= (isset($product['_meta_show_to_affiliates']) && (int)$product['_meta_show_to_affiliates'] == 1) ? 'checked' : '' ?> data-setting_key="_meta_show_to_affiliates" data-product_id="<?= $product['product_id'] ?>">
				    <label class="form-check-label" for="show_to_affiliates"><?= __('admin.status_on'); ?> / <?= __('admin.status_off'); ?></label>
				</div>
			</div>
		</div>

		<div class="col-sm-4">
			<div class="form-group mb-0 ">
				<label class="control-label"><?= __('admin.show_to_featured'); ?></label>
				<div class="form-check form-switch">
				    <input class="form-check-input update_product_settings" type="checkbox" id="show_to_featured" name="show_to_featured" <?= (isset($product['_meta_show_to_featured']) && (int)$product['_meta_show_to_featured'] == 1) ? 'checked' : '' ?> data-setting_key="_meta_show_to_featured" data-product_id="<?= $product['product_id'] ?>">
				    <label class="form-check-label" for="show_to_featured"><?= __('admin.status_on'); ?> / <?= __('admin.status_off'); ?></label>
				</div>
			</div>
		</div>
				</div>
			</div>
		</div>
	</div>
</div>

	<div class="card mt-4 <?= ($product['product_type'] == 'downloadable') ? '' : 'd-none' ?>" id="downloadable_card">
		<div class="card-header">
			<h4 class="header-title">
				<?= __('admin.downloadable_product'); ?>
			</h4>
		</div>
		<div class="card-body">
			<div class="file-preview-button btn btn-primary">
				<?= __('admin.downloadable_file'); ?>
				<input type="file" class="downloadable_file_input" name="downloadable_file" multiple="multiple">
			</div>

			<div id="priview-table" class="table-responsive">
				<table class="table table-hover">
					<thead>
						<?php if($product['product_type'] == 'downloadable') {   $product['downloadable_files'] = json_decode($product['downloadable_files'],true);
						foreach ($product['downloadable_files'] as $key => $value) { ?>
							<tr>
								<td width="70px"> <div class="upload-priview up-zip" ></div></td>
								<td>
									<?= $value['mask'] ?>
									<input type="hidden" name="keep_files[]" value="<?= $value['name'] ?>">
								</td>
								<td width="70px"><button type="button" class="btn btn-danger btn-sm remove-priview-server" data-id="'+ i +'" ><?= __('admin.remove'); ?></button></td>
							</tr>
						<?php } } ?>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>


		</div>
	</div>

	<div class="card mt-4" id="shipping_detail_card">
		<div class="card-header">
			<h5 class="header-title">
				<?= __('admin.shipping_detail'); ?>
			</h5>
		</div>

	<div class="card-body">
		<div class="row">
			<div class="col-sm-4">
				<div class="form-group mb-0 ">
					<label class="control-label"><?= __('admin.enable_shipping'); ?></label>
						<div class="form-check form-switch">
						    <input class="form-check-input update_product_settings" type="checkbox" id="allow_shipping" name="allow_shipping" <?= (isset($product['allow_shipping']) && (int)$product['allow_shipping'] == 1) ? 'checked' : '' ?> data-setting_key="allow_shipping" data-product_id="<?= $product['product_id'] ?>">
						    <label class="form-check-label" for="allow_shipping"><?= __('admin.status_on'); ?> / <?= __('admin.status_off'); ?></label>
						</div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label class="control-label"><?= __('admin.country_location'); ?></label>
					<div class="form-check form-switch">
					    <input class="form-check-input allow_country_settings" type="checkbox" id="allow_country" name="allow_country" <?= (int)$product['state_id'] >= 1 ? 'checked' : '' ?> data-setting_key="allow_country" data-product_id="<?= $product['product_id'] ?>">
					    <label class="form-check-label" for="allow_country"><?= __('admin.status_on'); ?> / <?= __('admin.status_off'); ?></label>
					</div>
				</div>	
			</div>
			<div class="col-sm-12">
				<div class="country-chooser">
					<div class="row">
						<div class="col">
							<select class="form-control" name="country_id" id="country_id">
								<option value="0"><?= __('admin.select_country'); ?></option>
								<?php foreach ($country_list as $key => $value) { ?>
									<option <?= $product_state->country_id == $value->id ? 'selected' : '' ?> value="<?= $value->id ?>"><?= $value->name ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="col">
							<select class="form-control" name="state_id" id="state_id">
								<option value=""><?= __('admin.select_state'); ?></option>
								<?php foreach ($states as $key => $value) { ?>
									<option <?= $product_state->id == $value->id ? 'selected' : '' ?> value="<?= $value->id ?>"><?= $value->name ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
			</div>
			<script type="text/javascript">
				$(document).on("change", ".allow_country_settings", function(){
					var checked = $(this).prop('checked');
					if (checked == true) {
						$(".country-chooser").show();
					}else{
						$(".country-chooser").hide();
					}
				})

				$( document ).ready(function() {
				    $(".allow_country_settings").trigger('change');
				});

				$("#country_id").on('change',function(){
					var country = $(this).val();
					$('#state_id').prop("disabled",true)
					$.ajax({
						url: '<?php echo base_url('get_state') ?>',
						type: 'post',
						dataType: 'json',
						data: {country_id : country},
						success: function (json) {
							$('#state_id').prop("disabled",false)
							if(json){
								var html = '<option value=""><?= __('admin.select_state'); ?></option>';
								$.each(json, function(k,v){
									html += '<option value="'+v.id+'">'+v.name+'</option>';
								});
								$('#state_id').html(html);
							}
						}
					});
				});
			</script>
		</div>
	</div>
</div>

	<div class="card mt-4">
		<div class="card-header">
			<h4 class="header-title">
				<?= __('admin.recurssion_details'); ?>
			</h4>
		</div>

		<div class="card-body">
			<div class="row">
				<div class="col-sm-12">
					<fieldset class="custom-design mb-2">
						<legend><?= __('admin.product_recursion'); ?></legend>
						<div class="form-group">
							<div>
								<?php
								$product_recursion_type = $product['product_recursion_type'];
								$product_recursion = $product['product_recursion'];
								?>
								<select name="product_recursion_type" class="form-control">
									<option <?= '' == $product_recursion_type ? 'selected' : '' ?> value=""><?=  __('admin.none') ?></option>
									<option <?= 'default' == $product_recursion_type ? 'selected' : '' ?> value="default"><?= __('admin.default') ?></option>
									<option <?= 'custom' == $product_recursion_type ? 'selected' : '' ?> value="custom"><?= __('admin.custom') ?></option>								
								</select>							
							</div>
							<div class="toggle-container mt-2">
								<div class="d-none default-value">
									<p class="text-muted">
										<?php
										if($setting['product_recursion'] == 'custom_time'){
											if ($setting['recursion_endtime'] == NULL || $setting['recursion_endtime'] == '') {
												echo __('admin.default_recursion'). " : " . timetosting($setting['recursion_custom_time']). " | ".__('admin.endtime').": ".__('admin.life_time');
											}else{
												echo __('admin.default_recursion'). " : " . timetosting($setting['recursion_custom_time']). " | ".__('admin.endtime')." : " . dateFormat($setting['recursion_endtime']);
											}
										}else{
											if ($setting['recursion_endtime'] == NULL || $setting['recursion_endtime'] == '') {
												echo __('admin.default_recursion'). " : " . __('admin.'.$setting['product_recursion']) . " | ".__('admin.endtime')." : ".__('admin.life_time');
											}else{
												echo __('admin.default_recursion'). " : " . __('admin.'.$setting['product_recursion']) . " | ".__('admin.endtime')." : " . dateFormat($setting['recursion_endtime']);
											}
										}
										?>
									</p>
								</div>

								<div class="d-none custom-value">
									<div class="custom_recursion">
										<div class="form-group">
											<select name="product_recursion" class="form-control" id="recursion_type">
												<option value=""><?= __('admin.select_recursion'); ?></option>
												<option <?php if($product_recursion == 'every_day') { ?> selected <?php } ?> value="every_day"><?=  __('admin.every_day') ?></option>
												<option <?php if($product_recursion == 'every_week') { ?> selected <?php } ?>  value="every_week"><?=  __('admin.every_week') ?></option>
												<option <?php if($product_recursion == 'every_month') { ?> selected <?php } ?>  value="every_month"><?=  __('admin.every_month') ?></option>
												<option <?php if($product_recursion == 'every_year') { ?> selected <?php } ?>  value="every_year"><?=  __('admin.every_year') ?></option>
												<option <?php if($product_recursion == 'custom_time') { ?> selected <?php } ?>  value="custom_time"><?=  __('admin.custom_time') ?></option>
											</select>
										</div>

										<div class="form-group custom_time">
											<?php
											$minutes = $product['recursion_custom_time'];
											$day = floor ($minutes / 1440);
											$hour = floor (($minutes - $day * 1440) / 60);
											$minute = $minutes - ($day * 1440) - ($hour * 60);
											?>

											<input type="hidden" name="recursion_custom_time" value="<?php echo $minutes; ?>">

											<div class="row">
												<div class="col-sm-4">
													<label class="control-label"><?= __('admin.days'); ?> : </label>
													<input placeholder="Days" type="number" class="form-control" value="<?= $day ? $day : '' ?>" id="recur_day" onkeydown="if(event.key==='.'){event.preventDefault();}"  oninput="event.target.value = event.target.value.replace(/[^0-9]*/g,'');">
												</div>						
												<div class="col-sm-4">
													<label class="control-label"><?= __('admin.hours'); ?> : </label>
													<select class="form-control" id="recur_hour">
														<?php for ($x = 0; $x <= 23; $x++) {
															$selected = ($x == $hour ) ? 'selected="selected"' : '';
															echo '<option value="'.$x.'" '.$selected.'>'.$x.'</option>';
														} ?>
													</select>
												</div>						
												<div class="col-sm-4">
													<label class="control-label"><?= __('admin.minutes'); ?> : </label>
													<select class="form-control" id="recur_minute">
														<?php for ($x = 0; $x <= 59; $x++) {
															$selected = ($x == $minute ) ? 'selected="selected"' : '';
															echo '<option value="'.$x.'" '.$selected.'>'.$x.'</option>';
														} ?>
													</select>
												</div>						
											</div>									
										</div>
										<br>
										<div class="endtime-chooser row">
											<div class="col-sm-12">
												<div class="form-group">
													<label class="control-label d-block"><?= __('admin.choose_custom_endtime') ?> <input <?= $product['recursion_endtime'] ? 'checked' : '' ?>  id='setCustomTime' name='recursion_endtime_status' type="checkbox"> </label>
													<div style="<?= !$product['recursion_endtime'] ? 'display:none' : '' ?>" class='custom_time_container'>
														<input type="text" class="form-control" value="<?= $product['recursion_endtime'] ? date("d-m-Y H:i",strtotime($product['recursion_endtime'])) : '' ?>" name="recursion_endtime" id="endtime" placeholder="<?= __('admin.choose_endtime'); ?>" >
													</div>
												</div>
											</div>
										</div>
									</div>								
								</div>
							</div>

							<script type="text/javascript">
								$("select[name=product_recursion_type]").on("change",function(){
									$con = $(this).parents(".form-group");
									$con.find(".toggle-container .custom-value, .toggle-container .default-value").addClass('d-none');

									if($(this).val() == 'default'){
										$con.find(".toggle-container .default-value").removeClass("d-none");
									}else if($(this).val() == 'custom'){
										$con.find(".toggle-container .custom-value").removeClass("d-none");
									}
								})
								$("select[name=product_recursion_type]").trigger("change");


								$("select[name=product_recursion]").on("change",function(){
									$con = $(this).parents(".custom_recursion");
									$con.find(".custom_time").addClass('d-none');

									if($(this).val() == 'custom_time'){
										$con.find(".custom_time").removeClass("d-none");
									}
								})
								$("select[name=product_recursion]").trigger("change");
							</script>
						</div>
					</fieldset>
				</div>
			</div>
		</div>
	</div>

	
<div class="card mt-4">
	<div class="card-header">
		<h5 class="header-title">
			<?= __('admin.checkout_details'); ?>
		</h5>
	</div>

	<div class="card-body">

<!--Checkout theme section-->
<div class="row mt-4">
  <div class="col-12">
    <div class="form-group">
      <label class="form-label"><?= __('admin.choose_checkout_template'); ?></label>
      <div class="input-group">
        <select class="form-select" name="checkout_template">
          <?php 
          foreach ($checkout_template as $key => $value) {
            $selected = $product['_meta_checkout_template'] == $key ? "selected" : ""; 
            echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
          } 
          ?>
        </select>
        <button type="button" class="btn btn-primary preview-selected-template" data-id="<?=$product['product_id']?>"><?= __('admin.preview_checkout_template'); ?></button>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
  <div class="col-12">
    <div class="form-group">
      <label class="form-label"><?= __('admin.product_checkout_terms') ?></label>
      <textarea data-height='300px' placeholder="<?= __('admin.enter_your_product_checkout_terms') ?>" class="form-control summernote-img" name="product_checkout_terms"><?php echo $product['_meta_product_checkout_terms']; ?></textarea>
    </div>
  </div>
</div>
<!--Checkout theme section-->

<!--Product reviews section-->
<div class="row">
    <div class="col-sm-12">
        <fieldset class="border p-3 rounded-3 product_reviews_area">
            <legend class="fs-4"><?= __('admin.product_reviews'); ?></legend>

            <div class="form-group mb-3">
                <label class="form-label"><?= __('admin.product_reviewer_name') ?></label>
                <input type="text" class="form-control" placeholder="<?= __('admin.enter_product_reviewer_name') ?>" id="product_reviewer_name">
            </div>

            <div class="form-group mb-3">
                <label class="form-label"><?= __('admin.product_reviewer_comment') ?></label>
                <textarea class="form-control" rows="3" placeholder="<?= __('admin.enter_product_reviewer_comment') ?>" id="product_reviewer_comment"></textarea>
            </div>

            <button type="button" class="btn btn-primary add-product-review"><?= __('admin.add_product_review'); ?></button>

            <div class="row mt-4">
                <div class="col">
                    <table class="table table-bordered product_reviews_table <?= (isset($product['_meta_product_reviews']) && !empty($product['_meta_product_reviews'])) ? "" : "d-none"; ?>">
                        <thead>
                            <tr>
                                <th><?= __('admin.product_reviewer_name') ?></th>
                                <th><?= __('admin.product_reviewer_comment') ?></th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($product['_meta_product_reviews']) && !empty($product['_meta_product_reviews'])) {
                                $product_reviews = json_decode($product['_meta_product_reviews']);
                                foreach ($product_reviews as $value) {
                                    echo '<tr><td>'.$value->name.'</td><td>'.$value->comment.'</td><td><input type="hidden" name="product_reviewer_name[]" value="'.$value->name.'"><input type="hidden" name="product_reviewer_comment[]" value="'.$value->comment.'"><button type="button" class="btn btn-danger remove-product-review">'. __('admin.remove').'</button></td></tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </fieldset>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.add-product-review').click(function() {
            // Clear previous error states
            $('.product_reviews_area').find('.has-error').removeClass('has-error');
            $('.product_reviews_area').find('span.text-danger').remove();

            let reviewer_name = $('#product_reviewer_name').val();
            let reviewer_comment = $('#product_reviewer_comment').val();

            let isValid = true;

            // Validate reviewer_name
            if (reviewer_name.length < 2) {
                $('#product_reviewer_name').closest('.form-group').addClass('has-error');
                $('#product_reviewer_name').closest('.form-group').append('<span class="text-danger"><?= __('admin.product_reviewer_name_is_required'); ?></span>');
                isValid = false;
            }

            // Validate reviewer_comment
            if (reviewer_comment.length < 10) {
                $('#product_reviewer_comment').closest('.form-group').addClass('has-error');
                $('#product_reviewer_comment').closest('.form-group').append('<span class="text-danger"><?= __('admin.product_reviewer_comment_is_required'); ?></span>');
                isValid = false;
            }

            if (isValid) {
                $('.product_reviews_table tbody').append('<tr><td>' + reviewer_name + '</td><td>' + reviewer_comment + '</td><td><input type="hidden" name="product_reviewer_name[]" value="' + reviewer_name + '"><input type="hidden" name="product_reviewer_comment[]" value="' + reviewer_comment + '"><button type="button" class="btn btn-danger remove-product-review"><?= __('admin.remove'); ?></button></td></tr>');
                $('.product_reviews_table').removeClass('d-none');
                $('#product_reviewer_name').val('');
                $('#product_reviewer_comment').val('');
            }
        });

        $(document).on('click', '.remove-product-review', function() {
            $(this).closest('tr').remove();
            if ($('.product_reviews_table tbody tr').length === 0) {
                $('.product_reviews_table').addClass('d-none');
            }
        });
    });
</script>
<!--Product reviews section-->

<!-- Product Footer section -->
<div class="row mt-4">
    <div class="col-sm-12">
        <fieldset class="border p-3 rounded-3 product_footer_area">
            <legend class="fs-4"><?= __('admin.product_footer'); ?></legend>

            <div class="form-group">
                <label class="col-form-label"><?= __('admin.product_footer_page_name') ?></label>
                <input class="form-control" type="text" placeholder="<?= __('admin.product_footer_page_name') ?>" id="product_footer_page_name" />
            </div>

            <div class="form-group mb-3">
                <label class="col-form-label"><?= __('admin.product_footer_page_description') ?></label>
                <textarea class="form-control" placeholder="<?= __('admin.product_footer_page_description') ?>" id="product_footer_page_description"></textarea>
            </div>

            <span class="btn btn-primary add-product-footer"><?= __('admin.add_product_footer_page'); ?></span>

            <div class="row mt-4">
                <div class="col">
                    <table class="table-bordered mt-4 product_footer_table <?= (isset($product['_meta_product_footer']) && !empty($product['_meta_product_footer'])) ? "" : "d-none"; ?> w-100">
                        <thead>
                            <tr>
                                <td class="p-2" style="min-width:250px;"><?= __('admin.product_footer_page_name') ?></td>
                                <td class="p-2"><?= __('admin.product_footer_page_description') ?></td>
                                <td class="p-2"></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($product['_meta_product_footer']) && !empty($product['_meta_product_footer'])) {
                                $product_footer = json_decode($product['_meta_product_footer']);
                                foreach ($product_footer as $value) {
                                    echo '<tr><td class="p-2">'.$value->name.'</td><td class="p-2">'.$value->description.'</td><td class="p-2"><input type="hidden" name="product_footer_name[]" value="'.$value->name.'"><input type="hidden" name="product_footer_description[]" value="'.$value->description.'"><span class="btn btn-danger remove-product-footer">'. __('admin.remove').'</span></td></tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </fieldset>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.add-product-footer').click(function() {
            // Clear previous error states
            $('.product_footer_area').find('.has-error').removeClass('has-error');
            $('.product_footer_area').find('span.text-danger').remove();

            let footer_page_name = $('#product_footer_page_name').val();
            let footer_page_description = $('#product_footer_page_description').val();

            let isValid = true;

            // Validate footer_page_name
            if (footer_page_name.length < 2) {
                $('#product_footer_page_name').closest('.form-group').addClass('has-error');
                $('#product_footer_page_name').closest('.form-group').append('<span class="text-danger"><?= __('admin.product_footer_name_is_required'); ?></span>');
                isValid = false;
            }

            // Validate footer_page_description
            if (footer_page_description.length < 10) {
                $('#product_footer_page_description').closest('.form-group').addClass('has-error');
                $('#product_footer_page_description').closest('.form-group').append('<span class="text-danger"><?= __('admin.product_footer_description_is_required'); ?></span>');
                isValid = false;
            }

            if (isValid) {
                $('.product_footer_table tbody').append('<tr><td class="p-2">' + footer_page_name + '</td><td class="p-2">' + footer_page_description + '</td><td class="p-2"><input type="hidden" name="product_footer_name[]" value="' + footer_page_name + '"><input type="hidden" name="product_footer_description[]" value="' + footer_page_description + '"><span class="btn btn-danger remove-product-footer"><?= __('admin.remove'); ?></span></td></tr>');
                $('.product_footer_table').removeClass('d-none');
                $('#product_footer_page_name').val('');
                $('#product_footer_page_description').val('');
            }
        });

        $(document).on('click', '.remove-product-footer', function() {
            $(this).closest('tr').remove();
            if ($('.product_footer_table tbody tr').length === 0) {
                $('.product_footer_table').addClass('d-none');
            }
        });
    });
</script>
<!-- Product Footer section -->

	</div>
</div>
			
	<div class="card ">
		<div class="card-header change-category-color"><h4 class="header-title"><?= __('admin.commission_settings'); ?></h4></div>
		<div class="card-body">
			<?php if($seller){ ?>
				<div class="form-group mb-2">
				  <label class="form-label fs-5"><?= __('admin.product_status'); ?></label>
				  <div class="row g-3">
				    <div class="col-sm-3">
				      <div class="form-check">
				        <input class="form-check-input" type="radio" name="product_status" value="0" id="status_review" checked="">
				        <label class="form-check-label fs-5" for="status_review">
				          <span class="badge bg-warning"><?= __('admin.in_review'); ?></span>
				        </label>
				      </div>
				    </div>
				    <div class="col-sm-3">
				      <div class="form-check">
				        <input class="form-check-input" type="radio" name="product_status" value="1" id="status_approved" <?= (int)$product['product_status'] == 1 ? 'checked' : '' ?>>
				        <label class="form-check-label fs-5" for="status_approved">
				          <span class="badge bg-success"><?= __('admin.approved'); ?></span>
				        </label>
				      </div>
				    </div>
				    <div class="col-sm-3">
				      <div class="form-check">
				        <input class="form-check-input" type="radio" name="product_status" value="2" id="status_denied" <?= (int)$product['product_status'] == 2 ? 'checked' : '' ?>>
				        <label class="form-check-label fs-5" for="status_denied">
				          <span class="bg-danger text-white rounded"><?= __('admin.denied'); ?></span>
				        </label>
				      </div>
				    </div>
				    <div class="col-sm-3">
				      <div class="form-check">
				        <input class="form-check-input" type="radio" name="product_status" value="3" id="status_edit" <?= (int)$product['product_status'] == 3 ? 'checked' : '' ?>>
				        <label class="form-check-label fs-5" for="status_edit">
				          <span class="badge bg-warning"><?= __('admin.ask_to_edit'); ?></span>
				        </label>
				      </div>
				    </div>
				  </div>
				</div>

				<div class="commission-setting">
					<fieldset class="custom-design mb-2">
						<legend><?= __('admin.commission_for_admin'); ?></legend>
						<div class="form-group">
							<label class="control-label"><?= __('admin.click_commission'); ?></label>
							<div>
								<?php
								$commission_type= array(
									'default'    => 'Default',
									'fixed'      => 'Fixed',
								);
								?>
								<select name="admin_click_commission_type" class="form-control">
									<?php foreach ($commission_type as $key => $value) { ?>
										<option <?= $seller->admin_click_commission_type == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?></option>
									<?php } ?>
								</select>
							</div>

							<div class="toggle-container">
								<div class="default-value d-none">
									<small class="text-muted d-block">
										<?php
										$commnent_line = "<b>".__('admin.default_commission').": </b>";
										if($vendor_setting['admin_click_amount'] && $vendor_setting['admin_click_count']){
											$commnent_line .= c_format($vendor_setting['admin_click_amount']) ." ".__('admin.per')." ". (int)$vendor_setting['admin_click_count'] ." ".__('admin.clicks');
										}
										else if($setting['product_commission_type'] == 'Fixed'){
											$commnent_line .= __('admin.default_commission_warning');
										}
										echo $commnent_line;
										?>
									</small>
								</div>
								<div class="custom-value d-none">										
									<div class="form-group">
										<div class="comm-group">
											<div>
												<div class="input-group mt-2">
													<div class="input-group-prepend"><span class="input-group-text"><?= __('admin.click'); ?></span></div>
													<input name="admin_click_count"  class="form-control" value="<?php echo $seller->admin_click_count; ?>" type="text" placeholder='<?= __('admin.clicks') ?>'>
												</div>
											</div>
											<div>
												<div class="input-group mt-2">
													<div class="input-group-prepend"><span class="input-group-text">$</span></div>
													<input name="admin_click_amount" class="form-control" value="<?php echo $seller->admin_click_amount; ?>" type="text" placeholder='<?= __('admin.amount') ?>'>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<script type="text/javascript">
								$("select[name=admin_click_commission_type]").on("change",function(){
									$con = $(this).parents(".form-group");
									$con.find(".toggle-container .percentage-value, .toggle-container .custom-value").addClass('d-none');

									if($(this).val() == 'default'){
										$con.find(".toggle-container .default-value").removeClass("d-none");
									}else{
										$con.find(".toggle-container .custom-value").removeClass("d-none");
									}
								})
								$("select[name=admin_click_commission_type]").trigger("change");
							</script>
						</div>

						<div class="form-group">
							<div class="row">
								<div class="col-sm-6">
									<label class="control-label"><?= __('admin.sale_commission'); ?></label>
									<div>
										<?php
										$commission_type= array(
											'default'    => 'Default',
											'percentage' => 'Percentage (%)',
											'fixed'      => 'Fixed',
										);
										?>
										<select name="admin_sale_commission_type" class="form-control">
											<?php foreach ($commission_type as $key => $value) { ?>
												<option <?= $seller->admin_sale_commission_type == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="toggle-container">
										<div class="default-value d-none">
											<label class="control-label d-block"><?= __('admin.default_commission') ?></label>
											<small class="text-muted d-block">
												<?php
												$commnent_line = "";
												if($vendor_setting['admin_sale_commission_type'] == ''){
													$commnent_line .= __('admin.default_commission_warning');
												}
												else if($vendor_setting['admin_sale_commission_type'] == 'percentage'){
													$commnent_line .= __('admin.percentage').' : '. (float)$vendor_setting['admin_commission_value'] .'%';
												}
												else if($vendor_setting['admin_sale_commission_type'] == 'Fixed'){
													$commnent_line .= __('admin.fixed').' : '. c_format($vendor_setting['admin_commission_value']);
												}
												echo $commnent_line;
												?>
											</small>
										</div>
										<div class="percentage-value d-none">										
											<div class="form-group">
												<label class="control-label m-0"><?= __('admin.sale_commission'); ?></label>
												<input name="admin_commission_value" id="admin_commission_value" class="form-control mt-2" value="<?php echo $seller->admin_commission_value; ?>" type="text" placeholder='<?= __('admin.sale') ?>'>
											</div>
										</div>
									</div>
								</div>
							</div>

							<script type="text/javascript">
								$("select[name=admin_sale_commission_type]").on("change",function(){
									$con = $(this).parents(".form-group");
									$con.find(".toggle-container .percentage-value, .toggle-container .default-value").addClass('d-none');

									if($(this).val() == 'default'){
										$con.find(".toggle-container .default-value").removeClass("d-none");
									}else{
										$con.find(".toggle-container .percentage-value").removeClass("d-none");
									}
								})
								$("select[name=admin_sale_commission_type]").trigger("change");
							</script>
						</div>
					</fieldset>

					<fieldset class="custom-design mb-2">
						<legend><?= __('admin.commission_for_affiliate'); ?></legend>

						<div class="form-group mb-1">
							<label class="control-label"><?= __('admin.click_commission'); ?> : </label> 
							<span>
								<?php 
								if($seller->affiliate_click_commission_type == 'default'){
									echo c_format($seller_setting->affiliate_click_amount) ." ".__('admin.per')." ". (int)$seller_setting->affiliate_click_count ."".__('admin.clicks');
								} else{ 
									echo c_format($seller->affiliate_click_amount) ." ".__('admin.per')." ". (int)$seller->affiliate_click_count ."".__('admin.clicks');
								} 
								?>
							</span>
						</div>

						<div class="form-group mb-1">
							<label class="control-label"><?= __('admin.sale_commission'); ?> : </label> 
							<span>
								<?php 
								$commnent_line = "";
								if($seller->affiliate_sale_commission_type == 'default'){ 
									if($seller_setting->affiliate_sale_commission_type == ''){
										$commnent_line .= __('admin.warning_default_commission_not_set');
									}
									else if($seller_setting->affiliate_sale_commission_type == 'percentage'){
										$commnent_line .= __('admin.percentage').' : '. (float)$seller_setting->affiliate_commission_value .'%';
									}
									else if($seller_setting->affiliate_sale_commission_type == 'fixed'){
										$commnent_line .= __('admin.fixed').' : '. c_format($seller_setting->affiliate_commission_value);
									}
								} else if($seller->affiliate_sale_commission_type == 'percentage'){
									$commnent_line .= __('admin.percentage').' : '. (float)$seller->affiliate_commission_value .'%';
								} else if($seller->affiliate_sale_commission_type == 'fixed'){
									$commnent_line .= __('admin.fixed').' : '. c_format($seller->affiliate_commission_value);
								} 

								echo $commnent_line;

								?>

							</span>
						</div>
						<!-- -->

						<div class="percentage-value d-none">		

							<div class="form-group">
								<label class="control-label"><?= __('user.sale_commission'); ?></label>
								<div>
									<?php
										$commission_type= array(
											'default'    => 'Default',
											'percentage' => 'Percentage (%)',
											'fixed'      => 'Fixed',
										);
									?>
									<select name="affiliate_sale_commission_type" class="form-control">
										<?php foreach ($commission_type as $key => $value) { ?>
											<option <?= $seller->affiliate_sale_commission_type == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label"><?= __('user.click_commission'); ?></label>
								<div>
									<?php
										$commission_type= array(
											'default'    => 'Default',
											'fixed'      => 'Fixed',
										);
									?>
									<select name="affiliate_click_commission_type" class="form-control">
										<?php foreach ($commission_type as $key => $value) { ?>
											<option <?= $seller->affiliate_click_commission_type == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?></option>
										<?php } ?>
									</select>
								</div> 	
							</div>

							<div class="form-group">
								<label class="control-label m-0"><?= __('user.sale_commission') ?></label>
								<input name="affiliate_commission_value" id="affiliate_commission_value" class="form-control mt-2" value="<?php echo $seller->affiliate_commission_value; ?>" type="text" placeholder='Sale'>
							</div>

							<div class="form-group">
								<div class="comm-group">
									<div>
										<div class="input-group mt-2">
										  	<div class="input-group-prepend"><span class="input-group-text"><?= __('user.click') ?></span></div>
											<input name="affiliate_click_count"  class="form-control" value="<?php echo $seller->affiliate_click_count; ?>" type="text" placeholder='Clicks'>
										</div>
									</div>
									<div>
										<div class="input-group mt-2">
										  	<div class="input-group-prepend"><span class="input-group-text">$</span></div>
											<input name="affiliate_click_amount" class="form-control" value="<?php echo $seller->affiliate_click_amount; ?>" type="text" placeholder='Amount'>
										</div>
									</div>
								</div>
							</div>	
						</div>

						<!-- -->		
					</fieldset>

					<fieldset class="custom-design mb-2">
						<legend><?= __('admin.finalize_commission'); ?></legend>
						<div class="row">
							<div class="col-sm-4">
								<label class="control-label"><?= __('admin.vendor') ?>  <span data-toggle='tooltip' title="<?= __('admin.info_lbl_product_owner') ?>"></span></label>
								<input type="text" readonly="" value="0" class="form-control" id="ipt-vendor_commission">
							</div>
							<div class="col-sm-4">
								<label class="control-label"><?= __('admin.admin') ?> <span data-toggle='tooltip' title="<?= __('admin.info_lbl_admin') ?>"></span></label>
								<input type="text" readonly="" value="0" class="form-control" id="ipt-admin_sale_com">
							</div>
							<div class="col-sm-4">
								<label class="control-label"><?= __('admin.affiliate') ?> <span data-toggle='tooltip' title="<?= __('admin.info_lbl_product_other_affiliate') ?>"></span></label>
								<input type="text" readonly="" value="0" class="form-control" id="ipt-affiliate_sale_com">
							</div>
						</div>
					</fieldset>
				</div>

			<?php } else { ?>	
				<p class="text-center mt-4 mb-4"><?= __('admin.no_any_vendor_on_this_product'); ?></p>

				<fieldset class="custom-design mb-2">
					<legend><?= __('admin.commission_for_affiliate'); ?></legend>
					<div class="form-group">
						<div class="row">
							<div class="col-sm-6">
								<?php
								$selected_commition_type = $product['product_commision_type'];
								$selected_commision_value = $product['product_commision_value'];
								$commission_type= array(
									'default'    => __('admin.default'),
									'percentage' => __('admin.percentage').' (%)',
									'fixed'      => __('admin.fixed'),
								);
								?>
								<div class="form-group">
									<label class="control-label"><?= __('admin.sale_commission') ?></label>
									<select name="product_commision_type" class="form-control">
										<?php foreach ($commission_type as $key => $value) { ?>
											<option <?= $key == $selected_commition_type ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="toggle-container">
									<div class="default-value d-none">
										<div class="form-group">
											<label class="control-label"><?= __('admin.default_commission') ?></label>
											<div>
												<?php
												$commnent_line = __('admin.default_commission_warning');
												if($setting['product_commission_type'] == 'percentage'){
													$commnent_line =  __('admin.default_commission').' : '. $setting['product_commission'] .'%';
												}
												else if($setting['product_commission_type'] == 'Fixed'){
													$commnent_line =  __('admin.default_commission').' : '. $setting['product_commission'];
												}
												echo "<small>{$commnent_line}</small>";;
												?>
											</div>
										</div>
									</div>
									<div class="percentage-value d-none">
										<div class="form-group">
											<label class="control-label"><?= __('admin.sale_commission') ?></label>
											<input placeholder="<?= __('admin.enter_product_sale_commission_value') ?>" name="product_commision_value" id="product_commision_value" class="form-control" value="<?php echo $selected_commision_value; ?>" type="text">
										</div>
									</div>
								</div>
							</div>
						</div>

						<script type="text/javascript">
							$("select[name=product_commision_type]").on("change",function(){
								$con = $(this).parents(".form-group");
								$con.find(".toggle-container .percentage-value, .toggle-container .default-value").addClass('d-none');

								if($(this).val() == 'default'){
									$con.find(".toggle-container .default-value").removeClass("d-none");
								}else{
									$con.find(".toggle-container .percentage-value").removeClass("d-none");
								}
							})
							$("select[name=product_commision_type]").trigger("change");
						</script>
					</div>


					<div class="form-group">
						<label class="control-label"><?= __('admin.product_click_commission') ?></label>
						<div>
							<?php
							$selected_commition_type = $product['product_click_commision_type'];
							$product_click_commision_ppc = $product['product_click_commision_ppc'];
							$product_click_commision_per = $product['product_click_commision_per'];
							?>
							<select name="product_click_commision_type" class="form-control">
								<option <?= 'default' == $selected_commition_type ? 'selected' : '' ?> value="default"><?= __('admin.default') ?></option>
								<option <?= 'custom' == $selected_commition_type ? 'selected' : '' ?> value="custom"><?= __('admin.custom') ?></option>
							</select>
						</div>
						<div class="toggle-container">
							<div class="d-none default-value">
								<small class="text-muted">
									<?php
									if($setting['product_noofpercommission'] != '' && $setting['product_ppc'] != ''){
										echo __('admin.default_commission')." : ".c_format($setting['product_ppc']). " ".__('admin.per')." " . $setting['product_noofpercommission'] . " ".__('admin.clicks');
									} else {
										echo __('admin.default_commission_warning');
									}
									?>
								</small>
							</div>
							<div class="d-none custom-value">
								<div class="comm-group">
									<div>
										<div class="input-group mt-2">
											<div class="input-group-prepend"><span class="input-group-text"><?= __('admin.click') ?></span></div>
											<input placeholder="<?= __('admin.number_of_clicks_per_commission') ?>" name="product_click_commision_per" id="product_click_commision_value" class="form-control" value="<?php echo $product_click_commision_per; ?>" type="text">
										</div>
									</div>
									<div>
										<div class="input-group mt-2">
											<div class="input-group-prepend"><span class="input-group-text">$</span></div>
											<input placeholder="<?= __('admin.commission_amount') ?>" name="product_click_commision_ppc" id="product_click_commision_ppc" class="form-control" value="<?php echo $product_click_commision_ppc; ?>" type="text">
										</div>
									</div>
								</div>
							</div>
						</div>

						<script type="text/javascript">
							$("select[name=product_click_commision_type]").on("change",function(){
								$con = $(this).parents(".form-group");
								$con.find(".toggle-container .custom-value, .toggle-container .default-value").addClass('d-none');

								if($(this).val() == 'default'){
									$con.find(".toggle-container .default-value").removeClass("d-none");
								}else{
									$con.find(".toggle-container .custom-value").removeClass("d-none");
								}
							})
							$("select[name=product_click_commision_type]").trigger("change");
						</script>
					</div>
				</fieldset>

			<?php } ?>
		</div>
	</div>
	<?php if($seller){ ?>
		<div class="card mt-3">
			<div class="card-header "><h4 class="header-title"><?= __('admin.admin_comments') ?></h4></div>
			<div class="card-body chat-card">
				<?php $comment = json_decode($seller->comment,1); ?>
				<?php if($comment){ ?>
					<ul class="comment-products">
						<?php foreach ($comment as $key => $value) { ?>
							<li class="<?= $value['from'] == 'admin' ? 'me' : 'other' ?>"> <div><?= $value['comment'] ?></div> </li>
						<?php } ?>
					</ul>
				<?php } ?>
				<div class="bg-white form-group m-0 p-2">
					<textarea class="form-control" placeholder="<?= __('admin.enter_message_and_save_product_to_send') ?>" name="admin_comment"></textarea>
				</div>
			</div>
		</div>
	<?php } ?>

	<!-- Save Button -->
	<div class="mt-4 mb-4 text-end">
	    <!-- Save and Close Button -->
	    <button type="submit" class="btn btn-lg btn-success btn-submit" name="save_close">
	        <i class="bi bi-save me-2"></i> <?= __('admin.save_and_close') ?>
	        <!-- Spinner icon that shows during loading -->
	        <span class="loading-submit d-none">
	            <span class="spinner-border spinner-border-sm text-light" role="status"></span>
	        </span>
	    </button>
	  
	    <button type="submit" class="btn btn-lg btn-success btn-submit" name="save">
	        <i class="bi bi-save me-2"></i> <?= __('admin.save') ?>
	        <!-- Spinner icon that shows during loading -->
	        <span class="loading-submit d-none">
	            <span class="spinner-border spinner-border-sm text-light" role="status"></span>
	        </span>
	    </button>
	</div>
	<!-- Save Button -->

</form>

		<div class="modal fade" id="updatepageModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel"><?php echo __('admin.top_update');?></h5>
						<button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
					</div>
					<div class="modal-body">
						<div class="row" id="rtcl-user-login-wrapper">
							<div class="col-md-12 rtcl-login-form-wrap login-1">

								<form id="frm_update_footer_page" class="form-horizontal" method="post" novalidate>
									<div class="form-group">
										<label class="control-label">
											<?php echo __('admin.product_footer_page_name')?>
										</label>
										<input type="text" name="name" id="footer_page_name" class="form-control" required>
									</div>
									<div class="form-group">
										<label class="control-label">
											<?php __('admin.product_footer_page_description') ?>
										</label>
										<textarea name="footer_page_description" class="form-control" id="footer_page_description"></textarea>
										<input type="hidden" id="updatetr" value="">
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-primary"  id="btnUpdate" ><?php echo __('admin.top_update')?></button>
									</div>
								</form>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>

<script type="text/javascript">
	var cache = {};
	$( document ).ready(function() {
		$('input[name="product_type"]:checked').trigger('change');
	});
	$(".comment-products").animate({ scrollTop: $('.comment-products').prop("scrollHeight")}, 1000);
	$(".commission-setting :input, input[name=product_price]").on("change",calcCommission);
	$('#endtime').datetimepicker({
		format:'d-m-Y H:i',
		inline:true,
	});

	$('input[name="product_launching_datetime"]').datetimepicker({
		minDate:new Date(),
		format:'d-m-Y H:i'
	});

	$(function() {
		$('input[name="product_sale_period"]').daterangepicker({
			timePicker: true,
			locale: {
				format: 'DD-MM-Y HH:mm:ss',
				cancelLabel: 'Clear'
			},
			autoUpdateInput: false,
			timePicker24Hour:true,
			timePickerSeconds:true,
			autoApply:true
		});

		$('input[name="product_sale_period"]').on('apply.daterangepicker', function(ev, picker) {
			$(this).val(picker.startDate.format('DD-MM-Y HH:mm:ss') + ' - ' + picker.endDate.format('DD-MM-Y HH:mm:ss'));
		});

		$('input[name="product_sale_period"]').on('cancel.daterangepicker', function(ev, picker) {
			$(this).val('');
		});
	});

	$('#setCustomTime').on('change', function(){
		$(".custom_time_container").hide();
		if($(this).prop("checked")){
			$(".custom_time_container").show();
		}
	});

	var xhrCommission;

	function calcCommission(){
		$this = $(this);
		if(xhrCommission && xhrCommission.readyState != 4){
			xhrCommission.abort()
		}

		xhrCommission = $.ajax({
			url:'<?= base_url('admincontrol/calc_commission') ?>',
			type:'POST',
			dataType:'json',
			data:$(".commission-setting :input, input[name=product_price], input[name=product_id]"),
			success:function(json){
				if(json['success']){
					$("#ipt-vendor_commission").val(json['commission']['vendor_commission']);
					$("#ipt-admin_sale_com").val(json['commission']['admin_sale_com']);
					$("#ipt-affiliate_sale_com").val(json['commission']['affiliate_sale_com']);
				}
			},
		})
	}

calcCommission();

$("#category_auto").autocomplete({
    source: function(request, response) {
        var term = request.term;
        if (term in cache) {
            response(cache[term]);
            return;
        }

        $.getJSON('<?= base_url("admincontrol/category_auto") ?>', request, function(data, status, xhr) {
            cache[term] = data;
            response(data);
        });
    },
minLength: 0,
select: function(event, ui) {
    $("#category_auto").blur();
    event.preventDefault();
    if ($(".category-selected input[value='" + ui.item.value + "']").length == 0) {
        $(".category-selected").append(`
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>${ui.item.label}</span>
                <input type="hidden" name="category[]" value="${ui.item.value}">
                <button type="button" class="btn btn-danger btn-sm remove-category">
                    <i class="fa fa-trash"></i>
                </button>
            </li>
        `);
    }
},
}).on('focus', function() {
    $(this).data("uiAutocomplete").search($(this).val());
});

$(".category-selected").delegate(".remove-category", 'click', function() {
    $(this).parents("li").remove();
});

function readURLBanner(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function(e) {
			$('#bannerImage').attr('src', e.target.result);
		}
		reader.readAsDataURL(input.files[0]);
	}
}

$(document).on('change', '#recur_day, #recur_hour, #recur_minute', function(){
	var days = $('#recur_day').val();
	var hours = $('#recur_hour').val();
	var minutes = $('#recur_minute').val();
	var total_minutes;		

	total_hours = parseInt(days*24) + parseInt(hours);
	total_minutes = parseInt(total_hours*60) + parseInt(minutes);
	$('.custom_time').find('input[name="recursion_custom_time"]').val(total_minutes);
});

$('.preview-selected-template').on('click', function() {
	var product_id = $(this).data('id');
	let selected_template = $('select[name="checkout_template"]').val();
	window.open('<?= base_url('store/checkout_preview/') ?>'+ encodeURI(selected_template)+'?id='+product_id, "_blank");
});

$(".btn-submit").on('click',function(evt){
	evt.preventDefault();
	$btn = $(this);
	var formData = new FormData($("#form_form")[0]);

	var on_store_checked = $('input[name=on_store]').prop('checked');
	if (on_store_checked == true) {
		formData.append("on_store", "1");
	}else{
		formData.append("on_store", "0");
	}

	var show_to_affiliates_checked = $('input[name=show_to_affiliates]').prop('checked');
	if (show_to_affiliates_checked == true) {
		formData.append("show_to_affiliates", "1");
	}else{
		formData.append("show_to_affiliates", "0");
	}
	$(".tlink_url_error").empty();
	$(".text-danger").empty();
	var res = $('input[name="product_url"]').val();

	if(res != "") {
		var result = res.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
		if(result == null && !res.includes("http://localhost") && !res.includes("https://localhost")) {
			
			$(".product_url_error").text('Please enter valid link');
			$("")
			$('input[name="product_url"]').after("<span class='text-danger'>Please enter valid link.</span>");
			return false;
		}
	}

	var show_to_featured_checked = $('input[name=show_to_featured]').prop('checked');
	if (show_to_featured_checked == true) {
		formData.append("show_to_featured", "1");
	}else{
		formData.append("show_to_featured", "0");
	}

	var allow_shipping_checked = $('input[name=allow_shipping]').prop('checked');
	if (allow_shipping_checked == true) {
		formData.append("allow_shipping", "1");
	}else{
		formData.append("allow_shipping", "0");
	}
	$.each(fileArray, function(i,j){ formData.append("downloadable_file[]", j.rawData); });
	formData.append("action", $(this).attr("name"));

	formData = formDataFilter(formData);
	$this = $("#form_form");	       

	$btn.btn("loading");
	$.ajax({
		url:'<?= base_url('Productsales/store') ?>',
		type:'POST',
		dataType:'json',
		cache:false,
		contentType: false,
		processData: false,
		data:formData,
		xhr: function (){
			var jqXHR = null;

			if ( window.ActiveXObject ){
				jqXHR = new window.ActiveXObject( "Microsoft.XMLHTTP" );
			}else {
				jqXHR = new window.XMLHttpRequest();
			}

			jqXHR.upload.addEventListener( "progress", function ( evt ){
				if ( evt.lengthComputable ){
					var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
					$('.loading-submit').text(percentComplete + "% "+'<?= __('admin.loading') ?>');
				}
			}, false );

			jqXHR.addEventListener( "progress", function ( evt ){
				if ( evt.lengthComputable ){
					var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
					$('.loading-submit').text("Save");
				}
			}, false );
			return jqXHR;
		},
		error:function(){ $btn.btn("reset"); },
		success:function(result){            	
			$btn.btn("reset");
			$('.loading-submit').hide();
			$this.find(".has-error").removeClass("has-error");
			$this.find("span.text-danger").remove();

			if(result['location']){
				window.location = result['location'];
			}
			if(result['errors']){
				$.each(result['errors'], function(i,j){
					$ele = $this.find('[name="'+ i +'"]');
					if($ele){
						$ele.parents(".form-group").addClass("has-error");
						$ele.after("<span class='text-danger'>"+ j +"</span>");
					}
				});
			}
		},
	});

	return false;
});

$('.update_product_settings').on('change', function(){
	var checked = $(this).prop('checked');
	var setting_key = $(this).data('setting_key');
	var product_id = $(this).data('product_id');

	if (checked == true) {
		var status = 1;
	}else{
		var status = 0;
	}

	$.ajax({
		url:'<?= base_url("productsales/update_product_settings") ?>',
		type:'POST',
		dataType:'json',
		data:{'action':'update_all_settings', status:status, setting_key:setting_key, product_id:product_id},
		success:function(json){
		},
	})
});

$(document).on('click',".proType",function(e){
	e.preventDefault();
	var value =$(this).data('value');
	$(".proType").removeClass('btn_active');
	$(this).addClass('btn_active');
	$(`input[name="product_type"][value="`+value+`"]`).prop('checked',true).change();
	
	if(value=='downloadable'){
		$("#downloadable_card").removeClass('d-none');
		$("#shipping_detail_card").addClass('d-none');
	} else {
		$("#downloadable_card").addClass('d-none')		
		$("#shipping_detail_card").removeClass('d-none');
	}	
});
var fileArray = [];

$('.downloadable_file_input').change(function(e){
	$.each(e.target.files, function(index, value){
		var fileReader = new FileReader(); 
		fileReader.readAsDataURL(value);
		fileReader.name = value.name;
		fileReader.rawData = value;
		fileArray.push(fileReader);
	});

	render_priview();
});

var getFileTypeCssClass = function(filetype) {
	var fileTypeCssClass;
	fileTypeCssClass = (function() {
		switch (true) {
			case /image/.test(filetype): return 'image';
			case /video/.test(filetype): return 'video';
			case /audio/.test(filetype): return 'audio';
			case /pdf/.test(filetype): return 'pdf';
			case /csv|excel/.test(filetype): return 'spreadsheet';
			case /powerpoint/.test(filetype): return 'powerpoint';
			case /msword|text/.test(filetype): return 'document';
			case /zip/.test(filetype): return 'zip';
			case /rar/.test(filetype): return 'rar';
			default: return 'default-filetype';
		}
	})();
	return fileTypeCssClass;
};

function render_priview() {
	var html = '';

	$.each(fileArray, function(i,j){
		html += '<tr>';
		html += '    <td width="70px"> <div class="upload-priview up-'+ getFileTypeCssClass(j.rawData.type) +'" ></div></td>';
		html += '    <td>'+ j.name +'</td>';
		html += '    <td width="70px"><button type="button" class="btn btn-danger btn-sm remove-priview" data-id="'+ i +'" ><?= __('admin.remove') ?></button></td>';
		html += '</tr>';
	})

	$("#priview-table tbody").html(html);
}

$("#priview-table").delegate('.remove-priview','click', function(){
	if(!confirm('<?= __('admin.are_you_sure') ?>')) return false;

	var index = $(this).attr("data-id");
	fileArray.splice(index,1);
	render_priview()
})
$(document).on('click','.remove-priview-server',function(){
	if(!confirm("<?= __('admin.are_you_sure') ?>")) return false;
	$(this).parents("tr").remove();
})
</script>

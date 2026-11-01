<div class="card">
	<div class="card-body">
		<form class="form-horizontal" method="post" action=""  enctype="multipart/form-data" id="setting-form">
			<div class="row">
				<div class="col-sm-12">
					<ul class="nav nav-pills nav-stacked" role="tablist" id="TabsNav">
						<li class="nav-item">
							<a class="nav-link active show" data-bs-toggle="tab" href="#marketpostback-setting" role="tab"><?= __('admin.marketpostback') ?></a>
						</li>
					</ul>
				</div>
				<div class="col-sm-12">
					<div class="tab-content">
						<div class="tab-pane p-3 active show" id="marketpostback-setting" role="tabpanel">
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label class="control-label"><?= __('admin.postback_status') ?></label>
										<select class="form-control" name="marketpostback[status]">
											<option value="0"><?= __('admin.disable') ?></option>
											<option value="1" <?= $marketpostback['status'] ? 'selected' : '' ?>><?= __('admin.enable') ?></option>
										</select>
									</div>
									
									<div class="form-group">
										<label class="control-label"><?= __('admin.postback_url') ?></label>
										<input type="text" name="marketpostback[url]" value="<?= $marketpostback['url'] ?>" class="form-control">
									</div>
									<div class="form-group">
										<label class="control-label"><?= __('admin.dynemic_params') ?></label>
										<div>
											<?php
												$dynamicparam = [
													'city' => __('admin.city'),
													'regionCode' => __('admin.region_code'),
													'regionName' => __('admin.region_name'),
													'countryCode' => __('admin.country_code'),
													'countryName' => __('admin.country_name'),
													'continentName' => __('admin.continent_name'),
													'timezone' => __('admin.time_zone'),
													'currencyCode' => __('admin.currency_code'),
													'currencySymbol' => __('admin.currency_symbol'),
													'ip' => __('admin.ip'),
													'type' => __('admin.type').' action,general_click,product_click,sale ',
													'id' => __('admin.id_sale_id_or_click_id'),
												];
												$marketpostback_dynamicparam = json_decode($marketpostback['dynamicparam'],1);
												$marketpostback_static = json_decode($marketpostback['static'],1);
											?>
											<div class="row">
												<?php foreach ($dynamicparam as $key => $value) { ?>
													<div class="col-sm-3">
														<label class="checkbox font-weight-light">
															<input type="checkbox" <?= isset($marketpostback_dynamicparam[$key]) ? 'checked' : '' ?> name="marketpostback[dynamicparam][<?= $key ?>]" value="<?= $key ?>">
															<span> <b><?= $key ?></b> - <?= $value ?> </span>
														</label>
													</div>
												<?php } ?>
											</div>
										</div>
									</div>

									<div class="card">
										<div class="card-header">
											<h6 class="card-title m-0"><?= __('admin.static_params') ?></h6>
										</div>
										<div class="card-body p-0">
											<div class="static-params table-responsive">
												<table class="table table-striped table-bordered ">
													<thead>
														<tr>
															<td><?= __('admin.param_key') ?></td>
															<td><?= __('admin.param_value') ?></td>
															<td width="50px">#</td>
														</tr>
													</thead>
													<tbody></tbody>
													<tfoot>
														<tr>
															<td colspan="3"><button class="pull-right btn btn-sm btn-primary add-static-params" type="button"><?= __('admin.add') ?></button></td>
														</tr>
													</tfoot>
												</table>
											</div>
										</div>
									</div>

									<script type="text/javascript">
										$(".add-static-params").click(function(){
											addStaticParam('','');
										})

										<?php foreach ($marketpostback_static as $key => $value) {
											echo "addStaticParam('". $value['key'] ."','". $value['value'] ."');";
										} ?>

										var addStaticParamIndex = 0;
										function addStaticParam(key,val) {
											var html = `<tr>
													<td>
														<input type="text" value='${key}' name="marketpostback[static][${addStaticParamIndex}][key]" placeholder="<?= __('admin.param_key') ?>" class="form-control">
													</td>
													<td>
														<input type="text" name="marketpostback[static][${addStaticParamIndex}][value]" value='${val}' placeholder="<?= __('admin.param_value') ?>" class="form-control">
													</td>
													<td>
														<button class="pull-right btn btn-sm btn-danger remove-static-params" type="button"><i class="fa fa-trash"></i></button>
													</td>
												</tr>`;

											addStaticParamIndex++;
											$(".static-params tbody").append(html);
										}

										$(".static-params").delegate(".remove-static-params","click",function(){
											$(this).parents("tr").remove();
										})
									</script>
								

								</div>
							</div>
						</div>

						
					</div>
				</div>
				<div class="col-sm-12 text-right">
					<button type="submit" class="btn btn-sm btn-primary btn-submit"><?= __('admin.save_settings') ?></button>
				</div>
			</div>
		</form>
	</div>
</div>


<script type="text/javascript">
$("#setting-form").on('submit',function(){
	$("#setting-form .alert-error").remove();
	var affiliate_cookie = parseInt($(".input-affiliate_cookie").val());
	if(affiliate_cookie <= 0 || affiliate_cookie > 365){
		$(".input-affiliate_cookie").after("<div class='alert alert-danger alert-error'>"+'<?= __('admin.days_between_1_to_365') ?>'+"</div>");
	}
	if($("#setting-form .alert-error").length == 0) return true;
	return false;
})

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
                    if($ele){
                        $ele.parents(".form-group").addClass("has-error");
                        $ele.after("<span class='d-block text-danger'>"+ j +"</span>");
                    }
                });
            }
        },
    })
    return false;
});

</script>

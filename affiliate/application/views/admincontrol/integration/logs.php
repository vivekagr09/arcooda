<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<div>
					<h4 class="mt-0 header-title pull-left"><?php echo __('admin.integration_logs') ?></h4>
					<div class="pull-right">
						<button class="btn btn-danger btn-sm delete-selected"><?php echo __('admin.delete_selected') ?></button>
					</div>
				</div>
			</div>

			<div class="card-body">

				<div class="well">
					<form>
						<div class="row">
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label"><?php echo __('admin.type') ?></label>
									<?php $selected = isset($_GET['type']) ? $_GET['type'] : ''; ?>
									<select class="form-control" name="type">
										<option value=""><?= __('admin.all') ?></option>
										<option <?= $selected == 'action' ? 'selected' : '' ?> value="action"><?= __('admin.action') ?></option>
										<option <?= $selected == 'integration_sale' ? 'selected' : '' ?> value="integration_sale"><?= __('admin.integration_sale') ?></option>
										<option <?= $selected == 'product_click' ? 'selected' : '' ?> value="product_click"><?= __('admin.product_click') ?></option>
										<option <?= $selected == 'store_sale' ? 'selected' : '' ?> value="store_sale"><?= __('admin.store_sale') ?></option>
									</select>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label class="control-label d-block">&nbsp;</label>
									<div>
										<button class="btn btn-primary" type="submit"><?php echo __('admin.filter') ?></button>
									</div>
								</div>
							</div>
							<div class="col-sm-3"></div>
						</div>
					</form>
				</div>
				<div class="table-rep-plugin">
				    
				    <div class="text-center">
                        <?php if ($logs ==null) {?>
							<div class="text-center mt-5">
							 <div class="d-flex justify-content-center align-items-center flex-column mt-5">
								 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
								 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
							 </div>
							</div>
                        <?php } else { ?>
	                        <div class="table-responsive b-0" data-pattern="priority-columns">
	                            <table id="tech-companies-1" class="table-tiny toggle-tr">
									<thead>
										<tr>
											<th class="text-left"  width="20px"><input type="checkbox" class="select-all"></th>
											<th class="text-left"  width="50px"><?= __('admin.id') ?></th>
											<th class="text-left"  width="200px"><?= __('admin.user_name') ?></th>
											<th class="text-left" ><?= __('admin.website') ?></th>
								            <th class="text-left"  width="190px"><?= __('admin.ip') ?></th>
								            <th class="text-left"  width="180px"><?= __('admin.created_at') ?></th>
								            <th class="text-left"  width="180px"><?= __('admin.click_type') ?></th>
								            <th class="text-left"  width="20px"></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($logs as $key => $log) { ?>
											<tr class="toggler">
												<td><input type="checkbox" name="ids[]" value="<?= $log['id'] ?>" class="select-single"></td>
												<td class="text-left"><?= $log['id'] ?></td>
												<td class="text-left"><?= $log['username'] ?></td>
												<td class="text-left"><?= $log['base_url'] ?></td>
									            <td class="text-left"><?= $log['flag'] ?> <?= $log['ip'] ?> - <small><?= $log['country_code'] ?></small></td>
									            <td class="text-left"><?= $log['created_at'] ?></td>
									            <td class="text-left"><?= $log['click_type'] ?></td>
									            <td class="text-left"><button class="btn btn-primary btn-sm"><i class="fa fa-info"></i></button></td>
											</tr>
											<tr style="display: none">
												<td></td>
												<td colspan="7">
													<div class="row">
														<div class="col-sm-3">
															<label><?= __('admin.page') ?> : </label> <span><?= $log['link'] ?></span>
														</div>
														<div class="col-sm-3">
															<label><?= __('admin.browser') ?> : </label> <span><?= $log['browserName'] ?> - <small><?= $log['browserVersion'] ?></small></span>
														</div>
														<div class="col-sm-3">
															<label><?= __('admin.os_platform') ?> : </label> <span><?= $log['osPlatform'] ?> -  <small><?= __('admin.version') ?> : <?= $log['osVersion'] ?></small></span>
														</div>
														<div class="col-sm-3">
															<label><?= __('admin.mobile_name') ?> : </label> <span><?= $log['mobileName'] ?></span>
														</div>
													</div>
												</td>
											</tr>
										<?php } ?>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="100%"><ul class="pagination"><?= $pagination ?></ul></td>
										</tr>
									</tfoot>
								</table>
							</div>
						<?PHP } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="integration-code"><div class="modal-dialog"><div class="modal-content"></div></div></div>

<script type="text/javascript">
	$(".select-all").on('change',function(){
		$(".select-single").prop("checked", $(this).prop("checked")).trigger("change");
	});

	$(".toggle-tr tbody tr.toggler").on('click',function(){
		$(this).next('tr').slideToggle('fast');
	})

	$(".select-single").on('change',function(){
		if($(".select-single:checked").length == 0){
			$(".delete-selected").hide();
		} else {
			$(".delete-selected").show();
		}
	})

	$(".delete-selected").on('click',function(){
		if(!confirm("<?= __('admin.are_you_sure') ?>")) return false;

		$this = $(this);
		$.ajax({
			url:'<?= base_url("integration/delete_log") ?>',
			type:'POST',
			dataType:'json',
			data:$(".select-single:checked"),
			beforeSend:function(){ $this.btn("loading"); },
			complete:function(){ $this.btn("reset"); },
			success:function(json){
				window.location.reload();
			},
		})
	})

	$(".wallet-toggle .tog").on('click',function(){
		$(this).parents(".wallet-toggle").find("> div").toggleClass("hide");
	})
	$(".tool-remove-link").on('click',function(){
		if(!confirm("<?= __('admin.are_you_sure') ?>")) return false;
		return true;
	})

	$(".get-code").on('click',function(){
		$this = $(this);
		$.ajax({
			url:'<?= base_url("integration/tool_get_code") ?>',
			type:'POST',
			dataType:'json',
			data:{id:$this.attr("data-id")},
			beforeSend:function(){ $this.btn("loading"); },
			complete:function(){ $this.btn("reset"); },
			success:function(json){
				if(json['html']){
					$("#integration-code .modal-content").html(json['html']);
					$("#integration-code").modal("show");
				}
			},
		})
	})
</script>
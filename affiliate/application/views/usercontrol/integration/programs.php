<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<div>
					<h4 class="mt-0 header-title pull-left"><?= __('admin.integration_programs') ?></h4>
					<div class="pull-right">
						<a class="btn btn-primary btn-sm" href="<?= base_url('usercontrol/programs_form') ?>"><?= __('admin.add_new') ?></a>
					</div>
				</div>
			</div>
			<div class="body">
				<div class="table-rep-plugin">
					<div class="table-responsive b-0" data-pattern="priority-columns">
							<?php if ($programs ==null) {?>
								<div class="text-center mt-5">
									 <div class="d-flex justify-content-center align-items-center flex-column mt-5">
										 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
										 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
									 </div>
								</div>
							<?php } else { ?>
								<table id="tech-companies-1" class="table  table-striped">
									<thead>
										<tr>
											<th><?= __('admin.id') ?></th>
											<th><?= __('admin.name') ?></th>
											<th><?= __('admin.sale_commission') ?></th>
											<th><?= __('admin.click_commission') ?></th>
											<th><?= __('admin.sale_status') ?></th>
											<th><?= __('admin.click_status') ?></th>
											<th><?= __('admin.status') ?></th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($programs as $key => $program) { ?>
											<tr>
												<td><?= $program['id'] ?></td>
												<td><?= $program['name'] ?></td>
												<td>
													<?php 
														if($program['vendor_id']){
															echo __('user.admin')." : ";
															if($program['admin_sale_status']){
																if($program['admin_commission_type'] == 'percentage'){ echo $program['admin_commission_sale'].'%'; }
																else if($program['admin_commission_type'] == 'fixed'){ echo c_format($program['admin_commission_sale']); }
																else { echo __('user.not_set'); }
															} else{
																echo __('user.not_set');
															}

															echo "<br>".__('user.affiliate')." : ";
															if($program['sale_status']){
																if($program['commission_type'] == 'percentage'){ echo $program['commission_sale'].'%'; }
																else if($program['commission_type'] == 'fixed'){ echo c_format($program['commission_sale']); }
																else { echo __('user.not_set'); }
															} else{
																echo __('user.not_set');
															}
														} else{
															if($program['sale_status']){
																if($program['commission_type'] == 'percentage'){ echo $program['commission_sale'].'%'; }
																else if($program['commission_type'] == 'fixed'){ echo c_format($program['commission_sale']); }
																else { echo __('user.not_set'); }
															} else{
																echo __('user.not_set');
															}
														}
													?>
												</td>
												<td>
													<?php
														if($program['vendor_id']){
															echo __('user.admin')." : ";
															if($program['admin_click_status']){
																if($program["admin_commission_click_commission"] && $program['admin_commission_number_of_click']){
																	echo c_format($program["admin_commission_click_commission"]). " ".__('user.per')." ". $program['admin_commission_number_of_click'] ." ".__('user.clicks');
																} else { echo __('user.not_set'); }
															} else{
																echo __('user.not_set');
															}

															echo "<br>".__('user.affiliate')." : ";
															if($program['click_status']){
																echo c_format($program["commission_click_commission"]). " ".__('user.per')." ". $program['commission_number_of_click'] ." ".__('user.clicks');
															} else{
																echo __('user.not_set');
															}
														} else{
															if($program['click_status']){
																echo c_format($program["commission_click_commission"]). " ".__('user.per')." ". $program['commission_number_of_click'] ." ".__('user.clicks');
															} else{
																echo __('user.not_set');
															}
														}
													?>
												</td>
												<td>
													<?php
														if($program['vendor_id']){
															echo __('user.admin')." : ". ($program['admin_sale_status'] ? __('user.enable') : __('user.disable'));
															echo "<br>".__('user.affiliate')." : ". ($program['sale_status'] ? __('user.enable') : __('user.disable'));
														} else {
															echo (int)$program['sale_status'] ? __('user.enable') : __('user.disable');
														}
													?>
												<td>
													<?php
														if($program['vendor_id']){
															echo __('user.admin')." : ". ($program['admin_click_status'] ? __('user.enable') : __('user.disable'));
															echo "<br>".__('user.affiliate')." : ". ($program['click_status'] ? __('user.enable') : __('user.disable'));
														} else {
															echo (int)$program['click_status'] ? __('user.enable') : __('user.disable');
														}
													?>	
												</td>
											</td>
												<td><?= program_status($program['status']) ?></td>
												<td>
													<a class="btn btn-primary btn-sm" href="<?= base_url('usercontrol/programs_form/'. $program['id']) ?>"><?= __('admin.edit') ?></a>
													<button <?= $program['associate_programns'] ? 'disabled' : '' ?> class="btn btn-danger btn-sm delete-program" data-id="<?= $program['id'] ?>"><?= __('admin.delete') ?></button>
												</td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="message-model">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body text-center"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('user.close') ?></button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(".delete-program").on('click',function(){
		$this = $(this);
		if(!confirm('<?= __('user.are_you_sure') ?>')) return false;
		$.ajax({
			url:'<?= base_url('usercontrol/delete_programs_form/') ?>',
			type:'POST',
			dataType:'json',
			data:{id: $this.attr("data-id")},
			beforeSend:function(){$this.btn("loading");},
			complete:function(){$this.btn("reset");},
			success:function(json){
				if(json['success']){
					$this.parents("tr").remove();
				}
				if(json['message']){
					$("#message-model .modal-body").html(json['message']);
					$("#message-model").modal("show");
				}
			},
		})
	})
</script>
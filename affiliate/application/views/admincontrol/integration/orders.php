<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<div class="pull-left">
					<h5 class="pull-left"><?= __('admin.integration_orders') ?></h5>
				</div>
				<div class="pull-right">
					<div class="btn-group d-none btn-group-md delete-multiple-container" role="group">
						<button class="btn btn-danger delete-multiple" type="button"><?= __('admin.delete_selected') ?><span class="selected-count"></span></button>
						<button class="btn btn-dark clear-selection" onclick="unselect()" type="button"><?= __('admin.unselect') ?></button>
				    </div>
				</div>
			</div>

			<div class="card-body">
				<div class="table-rep-plugin">
                    <?php if ($orders == null) {?>
						<div class="text-center mt-5">
						 <div class="d-flex justify-content-center align-items-center flex-column mt-5">
							 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
							 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
						 </div>
						</div>
                	<?php } else { ?>

                	<div class="table-responsive b-0" data-pattern="priority-columns">
                        <table id="tech-companies-1" class="table orders-table  table-striped">
							<thead>
								<tr>
									<th width="50px">

										<input type="checkbox" class="selectall-wallet-checkbox">
										<?= __('admin.id') ?>		
									</th>
									<th width="90px"><?= __('admin.order_id') ?></th>
									<th width="180px"><?= __('admin.user_name') ?></th>
									<th><?= __('admin.product_ids') ?></th>
									<th><?= __('admin.total') ?></th>
									<th><?= __('admin.currency') ?></th>
									<th width="90px"><?= __('admin.commission_type') ?></th>
									<th><?= __('admin.commission') ?></th>
									<th><?= __('admin.ip') ?></th>
									<th><?= __('admin.country_code') ?></th>
									
									<th><?= __('admin.script_name') ?></th>
									<th width="180px"><?= __('admin.created_at') ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($orders as $key => $order) { ?>
									<tr>
										<td>
											<button type="button" class="d-inline-block toggle-child-tr"><i class="fa fa-plus"></i></button>
											<label class="checkbox-label">
												<input type="checkbox" class="wallet-checkbox" value="<?= $order['id'] ?>">
												<?= $order['id'] ?>		
											</label>
										</td>
										<td><?= $order['order_id'] ?></td>
										<td><?= $order['user_name'] ?></td>
										<td><?= $order['product_ids'] ?></td>
										<td><?= $order['total'] ?></td>
										<td><?= $order['currency'] ?></td>
										<td><?= $order['commission_type'] ?></td>
										<td><?= c_format($order['commission']) ?></td>
										<td><?= $order['ip'] ?></td>
										<td><?= $order['country_code'] ?>&nbsp;<img title="<?= $order['country_code'] ?>" src="<?= base_url('assets/vertical/assets/images/flags/'. strtolower($order['country_code'])) ?>.png" width='25' height='15'></td>
										
										<td><img class="img-integration" src="<?= base_url('assets/integration/small/' .$order['script_name'].'.png') ?>"></td>
										<td><?= $order['created_at'] ?></td>
									</tr>
									<tr class="detail-tr">
										<td colspan="100%">
								            <div>
								                <ul>
								                	<li><b><?= __('admin.website') ?></b> <span><a href="//<?= $order['base_url'] ?>" target='_blank'><?= $order['base_url'] ?></a></span></li>
								                	<?php 
													$custom_data = json_decode($order['custom_data'],1);
													foreach ($custom_data as $value) { ?>
														<li class="">
															<b><?= $value['key'] ?>:</b> <span><?= $value['value'] ?></span>
														</li>
												<?php } ?>
								                </ul>
							               	</div>
							            </td>
							        </tr>

								<?php } ?>
								<?php } ?>
							</tbody>
						</table>
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
				<button type="button" class="btn btn-default" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-delete">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div id="message"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-bs-dismiss="modal"><?= __('admin.cancel') ?></button>
				<button type="button" class="btn btn-primary confirm-delete" data-id="0"><?= __('admin.delete') ?></button>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var selecttion = {};

	$(".orders-table").delegate(".toggle-child-tr","click",function(){
        $tr = $(this).parents("tr");
        $ntr = $tr.next("tr.detail-tr");
        if($ntr.css("display") == 'table-row'){
            $ntr.hide();
            $(this).find("i").attr("class","fa fa-plus");
        }else{
            $(this).find("i").attr("class","fa fa-minus");
            $ntr.show();
        }
    })

	$('.selectall-wallet-checkbox').on('change',function(){
		$(".wallet-checkbox").prop("checked", $(this).prop("checked")).trigger("change");
		var status = $(this).prop("checked");
		$(".wallet-checkbox").each(function(i,j){
			if(!status) delete selecttion[$(j).val()]
			else selecttion[$(j).val()] = $(j).val();
		})
	});

	$(".orders-table").delegate(".wallet-checkbox","change",function(){
		var status = $(this).prop("checked");

		if(!status) delete selecttion[$(this).val()]
		else selecttion[$(this).val()] = $(this).val();

		if(Object.keys(selecttion).length == 0){
			$(".delete-multiple-container").addClass('d-none');
		} else {
			$(".delete-multiple-container").removeClass('d-none');
			$(".delete-multiple").show();
			$(".selected-count").text(Object.keys(selecttion).length);
		}
	})

	function unselect(){
		selecttion = {};
		$(".wallet-checkbox").prop("checked",0).trigger("change");
	}

	$(".delete-multiple").on('click',function(e){
		$this = $(this);

		var ids = Object.keys(selecttion).join(",");
		e.preventDefault();
		e.stopPropagation();

		if(!confirm('<?= __('admin.are_you_sure') ?>')) return false;
		$.ajax({
			type:'POST',
			dataType:'json',
			data:{ids:ids},
			beforeSend:function(){ $this.btn("loading"); },
			complete:function(){ $this.btn("reset"); },
			success:function(json){
				$("#modal-delete #message").html(json['html']);
				$("#modal-delete .confirm-delete").attr("data-id",ids);
				$("#modal-delete").modal("show");
			},
		})
	})

	$(document).delegate(".confirm-delete",'click',function(e){
		e.preventDefault();
		e.stopPropagation();

		$this = $(this);
		$.ajax({
			url: '<?php echo base_url("integration/deleteOrdersConfirm") ?>',
			type:'POST',
			dataType:'json',
			data:{
				ids:$this.attr("data-id")
			},
			beforeSend:function(){ $this.btn("loading"); },
			complete:function(){ $this.btn("reset"); },
			success:function(json){
				window.location.reload();
			},
		})
	})
</script>
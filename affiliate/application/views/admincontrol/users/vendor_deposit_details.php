<?php if($saas_status){ ?>		
	<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/wallet.css?v='. time()) ?>">
	<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/flag/css/main.min.css?v='. time()) ?>">

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h6 class="m-0"><?= __('admin.deposit_requests_details') ?> #<?= $request['vd_id'] ?></h6>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-sm-4">
							<h6 class="font-14 text-info with-heading"><?= __('admin.request_details') ?></h6>
							<table class="details-dtable">
								<tr>
									<th><?= __('admin.id') ?></th>
									<td><?= $request['vd_id'] ?></td>
								</tr>
								<tr>
									<th><?= __('admin.user') ?></th>
									<td> <?= $request['username'] ?></td>
								</tr>
								<tr>
									<th><?= __('admin.amount_deposited') ?></th>
									<td> <?= c_format($request['vd_amount']) ?></td>
								</tr>
								<tr>
									<th><?= __('admin.payment_method') ?></th>
									<td> <?= __('admin.'.$request['vd_payment_method']) ?></td>
								</tr>
								<tr>
									<th><?= __('admin.payment_status') ?></th>
									<td> <?= withdrwal_status($request['vd_status']) ?></td>
								</tr>
							</table>
						</div>
						<div class="col-sm-4">
							<h6 class="font-14 text-info with-heading"><?= __('admin.submitted_details') ?></h6>
							<table class="details-dtable">
								<?php
										$data = json_decode($request['vd_meta'],1);
										foreach ($data as $key => $value) {
											if(!empty($value)) {
												$hasDetails = true;
											if($key == 'payment_proof') {
												$payment_proof = '<tr>
													<th class="text-capitalize">'.str_replace("_", " ", $key) .'</th>
													<td> <a target="_blank" href="'.base_url('assets/user_upload/'.$value).'">'.$value.'</a></td>
												</tr>';
											continue;
											}
										 ?>
										<tr>
											<th class="text-capitalize"><?= str_replace("_", " ", $key) ?></th>
											<td><?= $value ?></td>
										</tr>
									<?php }} ?>

									<?php if(!isset($hasDetails)) { ?>
										<tr>
											<td colspan="2"><?= __('admin.no_additional_details') ?></td>
										</tr>
									<?php } ?>

									<?php if(isset($payment_proof)) {
												echo $payment_proof;
											} ?>
							</table>
						</div>

						<div class="col-sm-4">
							<h6 class="font-14 text-info with-heading"><?= __('admin.add_custom_status_history') ?></h6>
							<div class="well add-history-form">
								<div class="form-group">
									<label class="form-control-label"><?= __('admin.status') ?></label>
									<select class="form-control" name="status">
										<option value=""><?= __('admin.select_status') ?></option>
										<?php foreach ($status_list as $key => $value) { ?>
											<option value="<?= $key ?>">
												<?php   
													if ($value == 'Received') {
														echo __('admin.received');
													}elseif ($value == 'Complete') {
														echo __('admin.complete');
													}elseif ($value == 'Total not match') {
														echo __('admin.total_not_match');
													}elseif ($value == 'Denied') {
														echo __('admin.denied');
													}elseif ($value == 'Expired') {
														echo __('admin.expired');
													}elseif ($value == 'Failed') {
														echo __('admin.failed');
													}elseif ($value == 'Processed') {
														echo __('admin.processed');
													}elseif ($value == 'Refunded') {
														echo __('admin.refunded');
													}elseif ($value == 'Reversed') {
														echo __('admin.reversed');
													}elseif ($value == 'Voided') {
														echo __('admin.voided');
													}elseif ($value == 'Canceled Reversal') {
														echo __('admin.cancel_reversal');
													}elseif ($value == 'Waiting For Payment') {
														echo __('admin.waiting_for_payment');
													}elseif ($value == 'Pending') {
														echo __('admin.pending');
													}else{
														echo $value;
													}
												?>
											</option>
										<?php } ?>
									</select>
								</div>
								<div class="form-group">
									<label><?= __('admin.comment') ?></label>
									<textarea name="comment" class="form-control" rows="3"></textarea>
								</div>
								<div class="form-group mb-0 text-right">
									<button class="btn btn-sm btn-add-status btn-info"><?= __('admin.add_status') ?></button>
								</div>
							</div>
						</div>
					</div>
					

					<div class="row">
						<div class="col-sm-12">
							<h6 class="font-14 text-info with-heading"><?= __('admin.status_history') ?></h6>
							<table class="table table-bordered table-sm table-status-history">
								<thead>
									<tr>
										<th><?= __('admin.status') ?></th>
										<th><?= __('admin.comment') ?></th>
									</tr>
								</thead>
								<tbody id="history_container"></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$(".btn-add-status").on("click",function(){
			$this = $('.add-history-form');
			$.ajax({
				type:'POST',
				dataType:'json',
				data:$(".add-history-form :input"),
				beforeSend:function(){
					$('.btn-add-status').btn("loading");
				},
				complete:function(){
					$('.btn-add-status').btn("reset");
				},
				success:function(json){
					$container = $this;
					$container.find(".is-invalid").removeClass("is-invalid");
					$container.find("span.invalid-feedback").remove();
			
					if (json['success']) {
						if($(".add-history-form select[name=status]").val() == "1"){
							window.location.reload();
						} else{
							getHistory()
						}
						$('[name="status"], [name="comment"]').val('')
					}
					
					if(json['errors']){
					    $.each(json['errors'], function(i,j){
					        $ele = $container.find('[name="'+ i +'"]');
					        if($ele){
					            $ele.addClass("is-invalid");
					            if($ele.parent(".input-group").length){
					                $ele.parent(".input-group").after("<span class='invalid-feedback'>"+ j +"</span>");
					            } else{
					                $ele.after("<span class='invalid-feedback'>"+ j +"</span>");
					            }
					        }
					    })
					}
				},
			})
		})

		function getHistory() {
			$this = $(this);
			$.ajax({
				url:'<?= base_url('admincontrol/get_vendor_deposit_history/'. $request['vd_id']) ?>',
				type:'POST',
				dataType:'json',
				beforeSend:function(){
					$("#history_container").html("<tr><td colspan='2' class='text-center'>"+'<?= __('admin.loading') ?>'+"...</td></tr>");
				},
				success:function(json){
					$("#history_container").html(json['html']);
				},
			})
		}

		getHistory()

		$(document).delegate('.wallet-popover','click', function(){
			var html = $(this).parents("tr").find(".dpopver-content").html();
	        $(this).attr('data-content',html);
		    if($('.popover').hasClass('show')){
		        $('.popover').remove()
		    } else {
		        $(this).popover('show');
		    }
		});

		$('html').on('click', function(e) {
		  if (typeof $(e.target).data('original-title') == 'undefined' &&
		     !$(e.target).parents().is('.popover.in')) {
		    $('[data-original-title]').popover('hide');
		  }
		});

		$(document).ready(function(){
			$(".wallet-popover").popover({
		        placement : 'right',
			    html : true,
		    });
		})
	</script>
<?php } else { ?>
	<div class="row">
		<div class="col-12">
			<div class="alert alert-info">
				<span><?= __('admin.saas_module_is_off') ?></span>
				<a href="<?= base_url('admincontrol/addons') ?>"><?= __('admin.admin_click_here_to_activate') ?></a>
			</div>
		</div>
	</div>
<?php } ?>

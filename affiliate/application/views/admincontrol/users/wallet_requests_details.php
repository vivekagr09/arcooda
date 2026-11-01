<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/wallet.css?v='. time()) ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/flag/css/main.min.css?v='. time()) ?>">

		<div class="card">
			<div class="card-header bg-blue-payment">
				<div class="card-title-white pull-left m-0">
					<?= __('admin.withdraw_requests_details') ?> #<?= $request['id'] ?>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-sm-4">
						<h6 class="text-info with-heading"><?= __('admin.request_details') ?></h6>
						<table class="details-dtable">
							<tr>
								<th><?= __('admin.id') ?></th>
								<td> <?= $request['id'] ?></td>
							</tr>
							<tr>
								<th><?= __('admin.total') ?></th>
								<td> <?= c_format($request['total']) ?></td>
							</tr>
							<tr>
								<th><?= __('admin.payment_method') ?></th>
								<td> <?= $request['prefer_method'] ?></td>
							</tr>
							<tr>
								<th><?= __('admin.payment_status') ?></th>
								<td> <?= withdrwal_status($request['status']) ?></td>
							</tr>
						</table>
					</div>
					<div class="col-sm-4">
						<h6 class="text-info with-heading"><?= __('admin.submitted_details') ?></h6>
						<table class="details-dtable">
							<?php
									$data = json_decode($request['settings'],1);
									foreach ($data as $key => $value) {
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
								<?php } ?>

								<?php if(isset($payment_proof)) echo $payment_proof; ?>
						</table>
					</div>
					<div class="col-sm-4">
						<h6 class="text-info"><?= __('admin.status_history') ?></h6>
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
				

				<br><br>

				<h6 class="text-info with-heading"><?= __('admin.transactions') ?></h6>

				<div class="table-responsive">
					<table class="table transaction-table table-striped">
						<thead>
							<tr>
								<th><?= __('admin.date') ?></th>
								<th><?= __('admin.user') ?></th>
								<th><?= __('admin.order') ?></th>
								<th width="150px"><?= __('admin.commission') ?></th>
								<th><?= __('admin.type') ?></th>
								<th><?= __('admin.payment_status') ?></th>
								<th><?= __('admin.commission_status') ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($transaction as $key => $value) {
									$data = [];
									$data['value'] = $value;
									$data['class'] = $class;
									$data['stop_checkbox'] = 1; 
									$data['stop_child'] = 1; 
									$data['wallet_status'] = $status; 
									$data['hide_recursion_btn'] = true; 
									echo $this->Product_model->getHtml('usercontrol/users/parts/new_wallet_tr', $data);
								} 
							?>
						</tbody>
					</table>
				</div>


				<div class="row">
					<div class="col-sm-8">
						<?= $confirm ?>
					</div>
					<div class="col-sm-4">
						<h6 class="text-info with-heading"><?= __('admin.add_custom_status_history') ?></h6>
						<div class="well add-history-form">
							<div class="form-group">
								<label class="form-control-label"><?= __('admin.status') ?></label>
								<select class="form-control" name="status">
									<option value=""><?= __('admin.select_status') ?></option>
									<?php foreach ($status_list as $key => $value) { ?>
										<option value="<?= $key ?>"><?= $value ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="form-group">
								<label><?= __('admin.comment') ?></label>
								<textarea name="comment" class="form-control" rows="3"></textarea>
							</div>
							<div class="form-group mb-0 text-right">
								<button class="btn btn-sm btn-add-status btn-primary" data-close="true">
									<?= __('admin.add_status_and_close') ?>
								</button>
								<button class="btn btn-sm btn-add-status btn-info"><?= __('admin.add_status') ?></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

<script type="text/javascript">
	$(".btn-add-status").on("click",function(){
		$el = $(this);
		$this = $('.add-history-form');
		$.ajax({
			type:'POST',
			dataType:'json',
			data:$(".add-history-form :input"),
			beforeSend:function(){
				$el.btn("loading");
			},
			complete:function(){
				$el.btn("reset");
			},
			success:function(json){
				$container = $this;
				$container.find(".is-invalid").removeClass("is-invalid");
				$container.find("span.invalid-feedback").remove();
		
				if (json['success']){
					if($el.data('close')){
						window.location.href = '<?= base_url('admincontrol/wallet_requests_list') ?>';
					} else {
						if($(".add-history-form select[name=status]").val() == "1"){
							window.location.reload();
						} else{
							getHistory()
						}
						$('[name="status"], [name="comment"]').val('')
					}
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
			url:'<?= base_url('admincontrol/get_withdrwal_history/'. $request['id']) ?>',
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
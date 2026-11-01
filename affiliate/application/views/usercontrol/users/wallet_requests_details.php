<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/wallet.css?v='. time()) ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/flag/css/main.min.css?v='. time()) ?>">
<div class="row">
	<div class="col-12">
		<div class="table-responsive">
			<div class="card">
				<div class="card-header">
					<h6 class="m-0"><?= __('user.withdraw_requests_details') ?> #<?= $request['id'] ?></h6>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-sm-4">
							<h6 class="font-14 text-info with-heading"><?= __('user.request_details') ?></h6>
							<table class="details-dtable">
								<tr>
									<th><?= __('user.id') ?></th>
									<td> <?= $request['id'] ?></td>
								</tr>
								<tr>
									<th><?= __('user.balance') ?></th>
									<td> <?= c_format($request['total']) ?></td>
								</tr>
								<tr>
									<th><?= __('user.payment_method') ?></th>
									<td> <?= $request['prefer_method'] ?></td>
								</tr>
								<tr>
									<th><?= __('user.payment_status') ?></th>
									<td><?= withdrwal_status($request['status']) ?></td>
								</tr>
							</table>
						</div>
						<div class="col-sm-4">
							<h6 class="font-14 text-info with-heading"><?= __('user.submited_details') ?></h6>
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
							<h6 class="font-14 text-info with-heading"><?= __('user.status_history') ?></h6>
							<table class="table table-bordered table-sm table-status-history">
								<thead>
									<tr>
										<th><?= __('user.status') ?></th>
										<th><?= __('user.comment') ?></th>
									</tr>
								</thead>
								<tbody id="history_container"></tbody>
							</table>
						</div>
					</div>

					<br><br>

					<h6 class="font-14 text-info with-heading"><?= __('user.transactions') ?></h6>

					<div class="table-responsive">
						<table class="table transaction-table">
							<thead>
								<tr>
									<th><?= __('user.date') ?></th>
									<th><?= __('user.user') ?></th>
									<th><?= __('user.order') ?></th>
									<th width="150px"><?= __('user.commission') ?></th>
									<th><?= __('user.type') ?></th>
									<th><?= __('user.status') ?></th>
									<th></th>
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
										echo $this->Product_model->getHtml('usercontrol/users/parts/new_wallet_tr', $data);
									} 
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
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

	function getHistory() {
		$this = $(this);
		$.ajax({
			url:'<?= base_url('usercontrol/get_withdrwal_history/'. $request['id']) ?>',
			type:'POST',
			dataType:'json',
			beforeSend:function(){
				$("#history_container").html("<tr><td colspan='2' class='text-center'>"+'<?= __('user.loading') ?>'+"...</td></tr>");
			},
			success:function(json){
				$("#history_container").html(json['html']);
			},
		})
	}

	getHistory()
</script>
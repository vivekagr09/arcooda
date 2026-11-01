<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/wallet.css?v='. time()) ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/flag/css/main.min.css?v='. time()) ?>">

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h6 class="m-0"><?= __('user.deposit_requests_details') ?> #<?= $request['vd_id'] ?></h6>
				<div class="pull-right">
					<a class="btn btn-primary btn-sm" href="<?= base_url('usercontrol/my_deposits') ?>"><?= __('admin.back') ?></a>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-sm-4">
						<h6 class="font-14 text-info with-heading"><?= __('user.request_details') ?></h6>
						<table class="details-dtable">
							<tr>
								<th><?= __('user.id') ?></th>
								<td><?= $request['vd_id'] ?></td>
							</tr>
							<tr>
								<th><?= __('user.user') ?></th>
								<td> <?= $request['username'] ?></td>
							</tr>
							<tr>
								<th><?= __('user.amount_deposited') ?></th>
								<td> <?= c_format($request['vd_amount']) ?></td>
							</tr>
							<tr>
								<th><?= __('user.payment_method') ?></th>
								<td> <?= __('user.'.$request['vd_payment_method']) ?></td>
							</tr>
							<tr>
								<th><?= __('user.payment_status') ?></th>
								<td> <?= withdrwal_status($request['vd_status']) ?></td>
							</tr>
						</table>
					</div>
					<div class="col-sm-4">
						<h6 class="font-14 text-info with-heading"><?= __('user.submited_details') ?></h6>
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
										<td colspan="2"><?= __('user.no_additional_details') ?></td>
									</tr>
								<?php } ?>

								<?php if(isset($payment_proof)) {
										echo $payment_proof;
									} ?>
						</table>
					</div>
				</div>
				

				<div class="row mt-4">
					<div class="col-sm-12">
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
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function getHistory() {
		$this = $(this);
		$.ajax({
			url:'<?= base_url('admincontrol/get_vendor_deposit_history/'. $request['vd_id']) ?>',
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
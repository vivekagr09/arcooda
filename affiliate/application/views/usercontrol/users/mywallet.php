<div class="row">
	<div class="col-12">
		<ul class="nav nav-tabs">
			<li class="nav-item">
				<a class="nav-link active" data-toggle="tab" href="#tab-wallet"><?= __('user.my_wallet') ?></a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#tab-payout"><?= __('user.my_payout') ?></a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#tab-paymentdetails"><?= __('user.payment_details') ?></a>
			</li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane active" id="tab-wallet">
				<div class="card m-b-20">
					<?php 
						$allow_with = false;
						if( (float)$totals['wallet_unpaid_amount'] >= (float)$site_setting['wallet_min_amount']){
							$allow_with = true;
						}
					?>

                    <div class="card">
						<div class="card-header">
						    
						    <div class="row">
					<div class="col-sm-3">
						<div class="card m-b-30 text-white bg-info">
                            <div class="card-body">
                                <blockquote class="card-bodyquote mb-0">
                                 <h6 class="mt-0 round-inner">
									<?php echo $totals['all_clicks'] ?> / 
									<?php echo c_format($totals['all_clicks_comm']) ?></h6>
                                 	<p class="mb-0 text-muted"><?= __('user.total_click_commition') ?></p>
                                </blockquote>
                            </div>
                        </div>
					</div>
					<div class="col-sm-3">
						<div class="card m-b-30 text-white bg-info">
                            <div class="card-body">
                                <blockquote class="card-bodyquote mb-0">
                                    	<h6 class="mt-0 round-inner">
								<?php echo $totals['total_sale_count']; ?>/
								<?php echo c_format($totals['all_sale_comm']); ?>
							</h6>
                                 	<p class="mb-0 text-muted"><?= __('user.sale_ommition') ?></p>
                                </blockquote>
                            </div>
                        </div>
					</div>
					<div class="col-sm-3">
						<div class="card m-b-30 text-white bg-info">
                            <div class="card-body">
                                <blockquote class="card-bodyquote mb-0">
                                    	<h6 class="mt-0 round-inner">
								<?php echo c_format($totals['wallet_accept_amount']); ?>/
								<?php echo c_format($totals['wallet_request_sent_amount']); ?>/
								<?php echo c_format($totals['unpaid_commition']); ?>
							</h6>
                                 	<p class="mb-0 text-muted"><?= __('user.paid_request_unpaid') ?> </p>
                                </blockquote>
                            </div>
                        </div>
					</div>
					<div class="col-sm-3">
						<div class="card m-b-30 text-white bg-info">
                            <div class="card-body">
                                <blockquote class="card-bodyquote mb-0">
                                   <h6 class="mt-0 round-inner"> <?= (int)$totals['integration']['action_count'] ?> / <?= c_format($totals['integration']['action_amount']) ?></h6>
                                 	<p class="mb-0 text-muted"><?= __('user.total_action_amount') ?></p>
                                </blockquote>
                            </div>
                        </div>
					</div>
					
				</div>
				
				
							<form method="GET">
								<div class="row">
									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label"><?= __('user.type') ?></label>
											<select name="type" class="form-control">
												<option value=""><?= __('user.all') ?></option>
												<option value="actions" <?= isset($_GET['type']) && $_GET['type'] == 'actions' ? 'selected' : '' ?>><?= __('user.actions') ?></option>
												<option value="clicks" <?= isset($_GET['type']) && $_GET['type'] == 'clicks' ? 'selected' : '' ?>><?= __('user.clicks') ?></option>
												<option value="sale" <?= isset($_GET['type']) && $_GET['type'] == 'sale' ? 'selected' : '' ?>><?= __('user.sale') ?></option>
												<option value="external_integration" <?= isset($_GET['type']) && $_GET['type'] == 'external_integration' ? 'selected' : '' ?>><?= __('user.external_integration') ?></option>
											</select>
										</div>
									</div>
									<div class="col-sm-5">
										<div class="form-group">
											<label class="control-label d-block">&nbsp;</label>
											<button class="btn btn-primary"><?= __('user.filter') ?></button>
											<?php if($allow_with){ ?>
												<button type="button" class="btn btn-primary withdrawal-all" ><?= __('user.withdrawal_all') ?> <small><?= c_format($totals['wallet_unpaid_amount']) ?></small></button>
											<?php } else { ?>
												<button type="button" class="btn btn-primary" data-bs-toggle="modal" href='#withdrawal-limit'><?= __('user.withdrawal_all') ?> <small><?= c_format($totals['wallet_unpaid_amount']) ?></small></button>
											<?php } ?>
										</div>
									</div>
									
								</div>
							</form>
						</div>
						<div class="card-body">
							<?php if ($transaction ==null) {?>
								<div class="text-center mt-5">
								 <div class="d-flex justify-content-center align-items-center flex-column mt-5">
									 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
									 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
								 </div>
								</div>
				            <?php } else { ?>         
								<div class="table-responsive">
									<table class="table table-sortable wallet-table">
										<thead>
											<tr>
												<th scope="col">#</th>
												<th></th>
												<th scope="col"><?= __('user.order_total') ?></th>
												<th class="sortTr <?= sort_order('wallet.amount') ?>" scope="col"><a href="<?= sortable_link('usercontrol/mywallet/','wallet.amount') ?>"><?= __('user.commission') ?></a></th>
												<th scope="col"><?= __('user.payment_method') ?></th>

												<th class="sortTr <?= sort_order('wallet.created_at') ?>" scope="col"><a href="<?= sortable_link('usercontrol/mywallet/','wallet.created_at') ?>"><?= __('user.date') ?></a></th>

												<th class="sortTr <?= sort_order('wallet.comm_from') ?>" scope="col"><a href="<?= sortable_link('admincontrol/mywallet','wallet.comm_from') ?>"><?= __('user.comm_from') ?></a></th>

												<th class="sortTr <?= sort_order('wallet.type') ?>" scope="col"><a href="<?= sortable_link('usercontrol/mywallet/','wallet.type') ?>"><?= __('user.type') ?></a></th>

												<th class="sortTr <?= sort_order('wallet.status') ?>" scope="col"><a href="<?= sortable_link('usercontrol/mywallet/','wallet.status') ?>"><?= __('user.status') ?></a></th>
											</tr>
										</thead>
										<tbody>
											<?= $table ?>
										</tbody>
									</table>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="tab-payout">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title"><?= __('user.my_payout') ?></h4>
					</div>
					<div class="card-body">
						
						<?php if ($payout_transaction ==null) {?>
							<div class="text-center mt-5">
							 <div class="d-flex justify-content-center align-items-center flex-column mt-5">
								 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
								 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
							 </div>
							</div>
                        <?php } else { ?>
							<div class="card-body p-0">
								<table class="table table-striped">
									<thead>
										<tr>
											<th width="50px">#</th>
											<th width="80px"><?= __('user.amount') ?></th>
											<th style="min-width: 250px"><?= __('user.comment') ?></th>
											<th width="200px"><?= __('user.date') ?></th>
											<th width="150px"><?= __('user.type') ?></th>
											<th width="150px"><?= __('user.status') ?></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($payout_transaction as $key => $value) { ?>
										<tr>
											<td><?= $key + 1 ?></td>
											<td><?= c_format($value['amount']) ?></td>
											<td><?= parseMessage($value['comment'],$value,'usercontrol') ?></td>
											<td><?= $value['created_at'] ?></td>
											<td><?= wallet_type($value) ?></td>
											<td><?= $status[$value['status']] ?></td>
										</tr>
										<?php } ?>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="100%" class="text-right">
												<div class="pagination">
													<?= $pagination_link; ?>
												</div>
											</td>
										</tr>
									</tfoot>
								</table>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="tab-paymentdetails">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title"><?= __('user.my_payment_details') ?></h4>
					</div>
					<div class="card-body">
						
						<form class="form-horizontal" method="post" action="<?= base_url('usercontrol/addpayment') ?>"  enctype="multipart/form-data">
							<input type="hidden" name="payment_id" value="<?= $paymentlist['payment_id'] ?>">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label"><?= __('user.bank_name') ?> </label>
								<div class="col-sm-9">
									<input placeholder="<?= __('user.enter_your_bank_name') ?>" name="payment_bank_name" value="<?php echo $paymentlist['payment_bank_name']; ?>" class="form-control" required="required" type="text">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label"><?= __('user.account_number') ?></label>
								<div class="col-sm-9">
									<input placeholder="<?= __('user.enter_your_account_number') ?>" name="payment_account_number" value="<?php echo $paymentlist['payment_account_number']; ?>" class="form-control" required="required" type="text">
								</div>
							</div>
							
							<div class="form-group row">
								<label class="col-sm-3 col-form-label"><?= __('user.account_name') ?></label>
								<div class="col-sm-9">
									<input placeholder="<?= __('user.enter_your_account_name') ?>" name="payment_account_name" class="form-control" value="<?php echo $paymentlist['payment_account_name']; ?>" required="required" type="text">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label"><?= __('user.ifsc_code') ?> </label>
								<div class="col-sm-9">
									<input placeholder="<?= __('user.enter_your_ifsc_code') ?>" name="payment_ifsc_code" id="payment_ifsc_code" class="form-control" value="<?php echo $paymentlist['payment_ifsc_code']; ?>" required="required" type="text">
								</div>
							</div>
							<div class="form-group text-right">
								<button class="btn btn-default btn-success" id="update-payment"  type="submit"><i class="fa fa-save"></i> <?= __('user.submit') ?></button>
							</div>
						</form>

					</div>
				</div>
				<div class="card m-t-30">
					<div class="card-header">
						<h5 class="card-title"><?= __('user.add_paypal_account') ?></h5>
					</div>
					<div class="card-body">
						<form class="form-horizontal" method="post" action="<?= base_url('usercontrol/addpayment') ?>" enctype="multipart/form-data">
							<div class="form-group row">
								<label class="col-sm-2 col-form-label"><?= __('user.paypal_email') ?> </label>
								<div class="col-sm-10">
									<input type="hidden" name="id" value="<?= $paypalaccounts['id'] ?>">
									<input name="paypal_email" class="form-control" value="<?= $paypalaccounts['paypal_email'] ?>" required="required" type="email">
								</div>
							</div>
							
							<div class="form-group text-right">
								<input name="add_paypal" value="<?= __('user.submit') ?>" type="submit" class="btn btn-default btn-success">
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
</div>
</div>


<div class="modal fade" id="withdrawal-limit">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body"><?= $site_setting['wallet_min_message'] ?></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('user.close') ?></button>
			</div>
		</div>
	</div>
</div>

<div class="clearfix"></div><br>
<script type="text/javascript">
	$(".show-recurring-transition").on("click",function(){
		$this = $(this);
		var id = $this.attr("data-id");

		$this.find("i").toggleClass("mdi-plus mdi-minus")

		$nextAll = $this.parents("tr").nextAll("tr.recurringof-"+id);
		if($nextAll.length){
			if($nextAll.eq(0).css("display") == 'table-row'){
				$nextAll.hide();
			} else {
				$nextAll.show();
			}
			return false;
		}

		$this.parents("tr").nextAll("tr.recurringof-"+id).remove();

		$.ajax({
			url:'<?= base_url('usercontrol/getRecurringTransaction') ?>',
			type:'POST',
			dataType:'json',
			data:{
				id:id,
			},
			beforeSend:function(){
				$this.btn("loading");
			},
			complete:function(){
				$this.btn("reset");
			},
			success:function(json){
				if(json['table']){
					$this.parents("tr").after(json['table'])
				}
			},
		})
	})

	$('.withdrawal-all').on('click',function(){

		if(!confirm('<?= __('user.are_you_sure') ?>')) return false;

		$this = $(this);
		$.ajax({
			type:'POST',
			dataType:'json',
			data:{request_payment_all: true},
			beforeSend:function(){ $this.btn("loading"); },
			complete:function(){ $this.btn("reset"); },
			success:function(json){
				window.location.reload();
			},
		})
	});

	$('.send-request').on('click',function(){
		$this = $(this);
		$.ajax({
			type:'POST',
			dataType:'json',
			data:{request_payment: $this.attr("data-id")},
			beforeSend:function(){ $this.btn("loading"); },
			complete:function(){ $this.btn("reset"); },
			success:function(json){
				$this.parents("tr").remove();
			},
		})
	})

	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		var hash = $(e.target).attr('href');
		if (history.pushState) {
		    history.pushState(null, null, hash);
		} else {
		    location.hash = hash;
		}
	});

	$(document).ready(function(){
		var hash = window.location.hash;
		if (hash) { $('.nav-link[href="' + hash + '"]').tab('show'); }
	})


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


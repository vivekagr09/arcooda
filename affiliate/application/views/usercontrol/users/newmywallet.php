<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/datatable') ?>/daterangepicker.css" />
<script src="<?= base_url('assets/plugins/datatable') ?>/moment.js"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/datatable') ?>/daterangepicker.min.js"></script>

<div class="row mb-4">
    <div class="<?= ($userdetails['is_vendor'] == 1) ? "col-xl-3" : "col-xl-4"; ?>">
        <div class="card border mt-2">
            <div class="card-header">
                <h6 class='card-title text-center text-uppercase text-primary m-0'><?= __('user.balance') ?></h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <ul class="list-inline row mb-0 clearfix">
                        <li class="col-6">
                            <p class="m-b-5 font-18 font-500 counter text-primary set-color"><?= c_format($user_totals['user_balance']) ?></p>
                            <p class="mb-0 text-muted"><?= __('user.balance') ?></p>
                        </li>
                        <li class="col-6 border-left">
                            <p class="m-b-5 font-18 font-500 counter text-primary set-color"><?= c_format($user_totals['wallet_accept_amount']); ?></p>
                            <p class="mb-0 text-muted"><?= __('user.paid_balance') ?></p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php if ($userdetails['is_vendor'] == 1) { ?>
    <div class="col-xl-3">
        <div class="card border mt-2">
            <div class="card-header">
                <h6 class='card-title text-center text-uppercase text-primary m-0'><?= __('user.total_sales') ?></h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <ul class="list-inline row mb-0 clearfix">
                        <li class="col-12">
                            <p class="m-b-5 font-18 font-500 counter text-primary set-color"><?= c_format($user_totals['vendor_sale_localstore_total'] + $user_totals['vendor_order_external_total']) ?></p>
                            <p class="mb-0 text-muted"><?= __('user.vendor_store') ?></p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="<?= ($userdetails['is_vendor'] == 1) ? "col-xl-3" : "col-xl-4"; ?>">
        <div class="card border mt-2">
            <div class="card-header">
                <h6 class='card-title text-center text-uppercase text-primary m-0'><?= __('user.actions') ?></h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <ul class="list-inline row mb-0 clearfix">
                        <li class="col-12">
                            <p class="m-b-5 font-18 font-500 counter text-primary">
                            	<span class="set-color"><?= (int)$user_totals['click_action_total'] + (int)$user_totals['vendor_action_external_total'] ?></span> / 
                            	<?= c_format($user_totals['click_action_commission'] + $user_totals['vendor_action_external_commission']) ?>
                            		
                        	</p>
                            <p class="mb-0 text-muted">
                            	<?= ($userdetails['is_vendor']) ? __('user.vendor_pay') : '' ?>
                            	<span class="set-color">
                            		<?= ($userdetails['is_vendor']) ? c_format($user_totals['vendor_action_external_commission_pay']) : '</br>' ?>
                            	</span>
                            </p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="<?= ($userdetails['is_vendor'] == 1) ? "col-xl-3" : "col-xl-4"; ?>">
        <div class="card border mt-2">
            <div class="card-header">
                <h6 class='card-title text-center text-uppercase text-primary m-0'><?= __('user.clicks') ?></h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <ul class="list-inline row mb-0 clearfix">
                        <li class="col-12">
                            <p class="m-b-5 font-18 font-500 counter text-primary">
                                <span class="set-color">
                                    <?= (int)($user_totals['total_clicks_count']) ?>
                                </span> /
                                <span class="set-color">
                                    <?= c_format($user_totals['total_clicks_commission']) ?>
                                </span>
                            </p>
                            <p class="mb-0 text-muted">
                            	<?= ($userdetails['is_vendor']) ? __('user.vendor_pay') : '' ?>
                                <span class="set-color">
                                    <?= ($userdetails['is_vendor']) ? c_format(
                                        	$user_totals['vendor_click_localstore_commission_pay'] +
                                        	$user_totals['vendor_click_external_commission_pay']) : '</br>' ?>
                                </span>
                            </p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(isset($walletauto_withdrawal) && $walletauto_withdrawal==1) { ?>
<h4 class="notification_on_pages">
	<span class="badge bg-secondary"><?= $wallet_auto_withdrawal_message; ?></span>
</h4>
<?php } ?>

<div class="row">
	<div class="col-12">
		<div class="card m-b-20">
			<div class="card">
				<div class="card-header">
					<form action="<?= base_url('usercontrol/mywallet') ?>" method="GET">
						<div class="row">
							<div class="col-sm-2">
								<div class="form-group mb-0">
									<select name="type" class="form-control">
										<option value=""><?= __('user.filter_by_type') ?></option>
										<option value="actions" <?= isset($_GET['type']) && $_GET['type'] == 'actions' ? 'selected' : '' ?>><?= __('user.actions') ?></option>
										<option value="clicks" <?= isset($_GET['type']) && $_GET['type'] == 'clicks' ? 'selected' : '' ?>><?= __('user.clicks') ?></option>
										<option value="sale" <?= isset($_GET['type']) && $_GET['type'] == 'sale' ? 'selected' : '' ?>><?= __('user.sale') ?></option>
										<option value="external_integration" <?= isset($_GET['type']) && $_GET['type'] == 'external_integration' ? 'selected' : '' ?>><?= __('user.external_integration') ?></option>
									</select>
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group mb-0">
									<select name="paid_status" class="form-control">
										<option value=""><?= __('user.filter_by_paid_type') ?></option>
										<option value="paid" <?= isset($_GET['paid_status']) && $_GET['paid_status'] == 'paid' ? 'selected' : '' ?>><?= __('user.paid') ?></option>
										<option value="unpaid" <?= isset($_GET['paid_status']) && $_GET['paid_status'] == 'unpaid' ? 'selected' : '' ?>><?= __('user.unpaid') ?></option>
									</select>
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group mb-0">
									<select name="withdraw_type" class="form-control">
										<option value=""><?= __('user.filter_by_withdraw_type') ?></option>
										<option value="1" <?= isset($_GET['withdraw_type']) && $_GET['withdraw_type'] == '1' ? 'selected' : '' ?>><?= __('user.canceled') ?></option>
										<option value="2" <?= isset($_GET['withdraw_type']) && $_GET['withdraw_type'] == '2' ? 'selected' : '' ?>><?= __('user.trashed') ?></option>
									</select>
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-grou mb-0">
									<input autocomplete="off" type="text" name="date" value="<?= isset($_GET['date']) ? $_GET['date'] : '' ?>" class="form-control daterange-picker" placeholder='<?= __('user.filter_by_date') ?>'>
								</div>
							</div>
							
							<div class="col-sm-4">
								<div class="form-group mb-0 withdrawal-button-group">
									<button class="btn btn-primary"><?= __('user.filter') ?></button>
									<?php if(isset($walletauto_withdrawal) && $walletauto_withdrawal==1) { } else { ?>
									<button type="button" class="btn btn-info withdrawal-all"><?= __('user.withdrawal_all_selected') ?></button>
									<button type="button" class="btn btn-primary withdrawal-unpaid" value="<?= $wallet_unpaid_amount ?>"><?= __('user.withdrawal_all') ?> (<?= c_format($wallet_unpaid_amount) ?>)</button>
									<?php }?>
								</div>
							</div>
							
						</div>
					</form>
				</div>
				<div class="card-body p-0">

					<div class="text-center1">
						<?php if ($transaction ==null) {?>
							<div class="text-center mt-5">
							 <div class="d-flex justify-content-center align-items-center flex-column mt-5">
								 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
								 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
							 </div>
							</div>
						<?php } else { ?>
							<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/wallet.css?v='. time()) ?>">
							<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/flag/css/main.min.css?v='. time()) ?>">

							<div class="table-responsive">
								<table class="table transaction-table">
									<thead>
										<tr>
											<th width="50px" class="checkbox-td">
												<?php if(isset($walletauto_withdrawal) && $walletauto_withdrawal==1) { } else { ?>
												<label>
													<input type="checkbox" class="selectall">
												</label><?php }?>
											</th>
											<th width="30px"></th>
											<th><?= __('user.date') ?></th>
											<th><?= __('user.user') ?></th>
											<th><?= __('user.type') ?></th>
											<th><?= __('user.commission') ?></th>
											<th><?= __('user.integration_id') ?></th>
											<th><?= __('user.payment') ?></th>
											<th><?= __('user.status') ?></th>
											<?php if($userdetails['is_vendor']): ?>
												<th><?= __('user.actions') ?></th>
											<?php endif ?>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php  
										$group_changed = 1;
										$html = '';
										$lastRow = count($transaction)-1;

										foreach ($transaction as $key => $value) {
											
											$product_user=0;
											if($value['from_user_id'] == ""){
											
											$product = $this->db->query('SELECT * FROM product WHERE product_id='.$value['reference_id'])->row_array();
											
											$product_user=$product['product_created_by'];
											}else{
												$integration = $this->db->query('SELECT * FROM integration_tools WHERE id='.$value['reference_id'])->row_array();
												if($integration['vendor_id'] == 0){
													$product_user=1;
												}else{
													$product_user=$integration['vendor_id'];
												}
												
											}

											$class = '';
											if($current_group_id && $current_group_id == $value['group_id']){
												$class = 'child';
											} else{
												$current_group_id = $value['group_id'];
												$group_changed =1;
											}


											$data = [];
											$data['value'] = $value;
											$data['product_user'] = $product_user;

											$data['class'] = $class;
											$data['wallet_status'] = $status;
											$data['userdetails'] = $userdetails;
											
											
											$data['child_id'] =$child_id= (isset($transaction[$key+1]) && $transaction[$key+1]['group_id'] && $transaction[$key+1]['has_recursion_records'] !="" &&  $transaction[$key+1]['group_id'] == $value['group_id']) ? $transaction[$key+1]['id']  : null;
											$data['has_child'] = (isset($transaction[$key+1]) && $transaction[$key+1]['group_id'] && $transaction[$key+1]['parent_id']==0  && $transaction[$key+1]['group_id'] == $value['group_id']) ? 1  : 0;
											
											$data['walletauto_withdrawal'] = $walletauto_withdrawal;
											$html .= $this->Product_model->getHtml('usercontrol/users/parts/new_wallet_tr', $data);
										
											if($group_changed || $lastRow == $key){
												echo $html;
												$html = '';
												$group_changed = 0;
											}
										}
										?>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="100%" class="text-right">
												<div class="pagination justify-content-end">
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
		</div>
	</div>
</div>

<div class="modal fade" id="withdrawal-payments">
	<div class="modal-dialog">
		<div class="modal-content">
		</div>
	</div>
</div>

<div class="modal fade" id="0-withdrawal-payments">
	<div class="modal-dialog">
		<!-- Modal content-->
	    <div class="modal-content">
	      	<div class="modal-header">
	      		<h4 class="modal-title"><?= __('user.withdrwal_alert') ?></h4>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      	</div>
	      	<div class="modal-body">
	        	<div><?= __('user.withdrwal_greater_than_zero') ?></div>
	      	</div>
	      	<div class="modal-footer">
	        	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('user.close') ?></button>
	      	</div>
	    </div>
	</div>
</div>

<div class="modal modal-style" id="modal-completed">
	<div class="modal-dialog modal-dialog-centered" role="document">
	    <div class="modal-content">
	        <div class="modal-header">
	            <h5 class="modal-title"><?= __('user.payment_completed') ?></h5>
	            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	        </div>
	        <div class="modal-body">
	   			<p><?= __('user.transaction_status_can_change_revert') ?></p>
	        </div>
	        <div class="modal-footer">
	            <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?= __('user.close') ?></button>
	        </div>
	    </div>
	</div>
</div>

<div class="modal fade" id="modal-confirm">
	<div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-body"></div></div></div>
</div>
<div class="modal fade" id="modal-confirmstatus" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-body"></div></div></div>
</div>
<div class="modal fade" id="modal-recursion">
	<div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-body"></div></div></div>
</div>

<script src="<?= base_url('assets/plugins/datatable') ?>/moment.js"></script>

<div class="clearfix"></div><br>
<script type="text/javascript">
	
	$(document).delegate(".show-child-transaction","click",function(){

		$tr = $(this).parents("tr");
		var status = $(this).find("i").hasClass('fa-angle-down') ? 1 : 0;
		var group_id = $tr.attr("group_id");
		
		if(status){
			//$('.transaction-table .child-row[group_id='+ group_id +']').show();
			$('.transaction-table .child-row[group_id='+ group_id +']:not(.recurring)').show();
			$(this).find("i").removeClass('fa-angle-down');
			$(this).find("i").addClass('fa-angle-up');
			$tr.addClass('opened')
			$('.transaction-table [group_id='+ group_id +']').addClass('highlight');
		} else{
			//$('.transaction-table .child-row[group_id='+ group_id +']').hide();
			$('.transaction-table .child-row[group_id='+ group_id +']:not(.recurring)').hide();
			$(this).find("i").removeClass('fa-angle-up');
			$(this).find("i").addClass('fa-angle-down');
			$tr.removeClass('opened')
			$('.transaction-table [group_id='+ group_id +']').removeClass('highlight');
		}

		$('.transaction-table .child-row[group_id='+ group_id +']:not(.recurring):last').addClass("last-group-row");
	})

	$(".filter-toggle").on("click", function(){
		$(".wallet-filter").slideToggle('fast');
	})

	$(".show-recurring-transition").on("click",function(){
		$this = $(this);
		var id = $this.attr("data-id");
		var group_id=$this.parents("tr").attr("group_id");

		var classname=$this.parents("tr").attr('class');
		

		$this.find("i").toggleClass("mdi-plus mdi-minus");
		
		$nextAll = $this.parents("tr").nextAll("tr.recurringof-"+id);

		$this.parents("tr").nextAll("tr.recurringof-"+id+":last").addClass('last-recurring');

		if($nextAll.length){
			if($nextAll.eq(0).css("display") == 'table-row'){
				$this.parents("tr").removeClass('opened-recurring');
				$nextAll.hide();
				$('.transaction-table .child-row[group_id='+ group_id +']:not(.recurring):last').addClass("last-group-row");
			} else {
				$this.parents("tr").addClass('opened-recurring');
				$nextAll.show();
				
				if(classname.includes('last-group-row')){
					$('.transaction-table .child-row[group_id='+ group_id +']:not(.recurring):last').removeClass("last-group-row");
				}
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
				newtr:1,
				ischild:$this.parents("tr").hasClass("child-row")
			},
			beforeSend:function(){$this.btn("loading");},
			complete:function(){$this.btn("reset");},
			success:function(json){
				if(json['table']){
					$this.parents("tr").addClass('opened-recurring');
					$this.parents("tr").after(json['table']);
					$this.parents("tr").nextAll("tr.recurringof-"+id+":last").addClass('last-recurring');
					$this.parents("tr").nextAll("tr.recurringof-"+id+"").removeAttr('style');
					
					if(classname.includes('last-group-row')){
					$('.transaction-table .child-row[group_id='+ group_id +']:not(.recurring):last').removeClass("last-group-row");
				}
					$(".wallet-popover").popover({
						placement : 'right',
						html : true,
					});
				}
			},
		})
	})

	$('.selectall').on('change',function(){
		$('.wallet-checkbox').prop("checked",$(this).prop("checked"));
	});

	$('.withdrawal-unpaid').on('click',function(){
		var amount = $(this).val();
		withdrawal_payments('all',$(this),amount);
	});

	$('.withdrawal-all').on('click',function(){
		var ids = $(".wallet-checkbox:checked").map(function(){ return $(this).val() }).toArray().join(",");
		withdrawal_payments(ids,$(this),null);
	});

	function withdrawal_payments(ids,_this,amount) {
		if (amount == '0') {
			$("#0-withdrawal-payments").modal("show");
		}else{
			$.ajax({
				url:'<?= base_url("usercontrol/get_withdrawal_modal") ?>',
				type:'POST',
				dataType:'json',
				data:{ids:ids},
				beforeSend:function(){_this.btn("loading");},
				complete:function(){_this.btn("reset");},
				success:function(json){
					$("#withdrawal-payments .modal-content").html(json['html']);
					$("#withdrawal-payments").modal("show");
				},
			})
		}
	}

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
	
	/*open and close popover tooltip*/
	$(function () {
	  // Initialize the popovers
	  var popovers = $('[data-bs-toggle="popover"]').popover({
	    html: true,
	    trigger: 'click'
	  });

	  // Add a click event listener to the document object
	  $(document).on('click', function (e) {
	    // Check if the clicked element is inside a popover or not
	    if (!popovers.has(e.target).length) {
	      // If it is not, hide all popovers
	      popovers.popover('hide');
	    }
	  });

	  // Add a click event listener to the popovers
	  popovers.on('click', function () {
	    // Hide all other popovers except the current one
	    var currentPopoverId = $(this).attr('aria-describedby');
	    popovers.not(this).each(function () {
	      var popoverId = $(this).attr('aria-describedby');
	      if (popoverId !== currentPopoverId) {
	        $(this).popover('hide');
	      }
	    });
	  });
	});
	/*open and close popover tooltip*/


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


	$("#modal-confirm .modal-body").delegate("[delete-tran-confirm]","click",function(){
		$this = $(this);
		$.ajax({
			url: '<?php echo base_url("usercontrol/confirm_remove_tran") ?>',
			type:'POST',
			dataType:'json',
			data:{id:$this.attr("delete-tran-confirm")},
			beforeSend:function(){ $this.button("loading"); },
			complete:function(){ $this.button("reset"); },
			success:function(json){
				window.location.reload();
			},
		})
	});

	$("#modal-confirm .modal-body").delegate("[change-tran-by-commi-confirm]","click",function(){
		$this = $(this);
		var status_type  = $this.attr("status_type");
		var id = $this.attr("id");

		$.ajax({
	        type: "POST",
	        url: '<?php echo base_url("usercontrol/change_commission_status") ?>',
	        data: {status_type:status_type,id:id},
	        cache: false,
	        success: function(data) 
	        {
	        	window.location.reload();
	        }
	    });
	});
	
	
	$('.daterange-picker').daterangepicker({
		opens: 'left',
		autoUpdateInput: false,
		ranges: {
			'<?= __('user.today'); ?>': [moment(), moment()],
			'<?= __('user.yesterday'); ?>': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'<?= __('user.last_7_days'); ?>': [moment().subtract(6, 'days'), moment()],
			'<?= __('user.last_30_days'); ?>': [moment().subtract(29, 'days'), moment()],
			'<?= __('user.this_month'); ?>': [moment().startOf('month'), moment().endOf('month')],
			'<?= __('user.last_month'); ?>': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		},
		locale: {
			cancelLabel: 'Clear',
			format: 'DD-M-YYYY'
		}
	});

	$('.daterange-picker').on('apply.daterangepicker', function(ev, picker) {
		$(this).val(picker.startDate.format('DD-M-YYYY') + ' - ' + picker.endDate.format('DD-M-YYYY'));
	});

	$('.daterange-picker').on('cancel.daterangepicker', function(ev, picker) {
		$(this).val('');
	});

	$(document).on('click', '.show-trans-aff-details', function() {
		$('.transaction-datails-div-hidden').toggle();
	});
	
	$(document).on('click', '.wallet-checkbox', function() {
		let curTR = $(this).closest('tr');
		
		if($(this).prop('checked')) {
			if(!$(curTR).hasClass('child-row')) {
				$("tr[group_id='"+$(curTR).attr('group_id')+"'].child-row").each(function( index ) {
					$( this ).find('.wallet-checkbox').prop('checked', true);
					$( this ).find('.wallet-checkbox').prop('disabled', true);
				});
			}
		} else {
			if($(curTR).hasClass('child-row')) {
				$("tr[group_id='"+$(curTR).attr('group_id')+"']:not(.child-row)").each(function( index ) {
					$( this ).find('.wallet-checkbox').prop('checked', false);
				});
			} else {
				$("tr[group_id='"+$(curTR).attr('group_id')+"'].child-row").each(function( index ) {
					$( this ).find('.wallet-checkbox').prop('checked', false);
					$( this ).find('.wallet-checkbox').prop('disabled', false);
				});
			}
		}
	});

	function changeStatus(el,id,status){
		let type = el.options[el.selectedIndex].dataset.type;

		if(status == 3 && type != 'recursion'){
			$("#modal-completed").modal("show");
			return false;
		}

		switch(type){
		  	case 'comission':
		    	infoRemoveTranByComission(el.value,id);
		    	break;
		  	case 'wallet':
		    	walletChangeStatus(el.value,id);
		    	break;
		    case 'remove':
		    	infoRemoveTransaction(id);
		    	break;
	    	case 'recursion':
	    		infoRecursionTransaction(id);
	    		break;
		  	default:
		    	return;
		}	
	}

	function infoRemoveTranByComission(value,id){
		$.ajax({
			url: '<?= base_url("usercontrol/info_remove_tran_by_commission") ?>',
			type:'POST',
			dataType:'json',
			data:{id:id,status_type:value},
			success:function(json){
				$("#modal-confirm .modal-body").html(json['html']);
				$("#modal-confirm").modal("show");
			},
		})
	}

	function walletChangeStatus(value,id){
		$.ajax({
			url: '<?= base_url("usercontrol/wallet_change_status") ?>',
			type:'POST',
			dataType:'json',
			data:{id:id,val:value},
			success:function(json){
				if(json['ask_confirm']){
					$("#modal-confirmstatus .modal-body").html(json['html']);
					$("#modal-confirmstatus").modal('show');
				}
				if(json['success']){
					window.location.reload();
				}
			},
		})
	}

	$("#modal-confirmstatus").delegate(".close-modal","click",function(){
		$("#modal-confirmstatus").modal("hide");
	})

	function infoRemoveTransaction(id){
		$.ajax({
			url: '<?= base_url("usercontrol/info_remove_tran") ?>',
			type:'POST',
			dataType:'json',
			data:{id:id},
			success:function(json){
				$("#modal-confirm .modal-body").html(json['html']);
				$("#modal-confirm").modal("show");
			},
		})
	}

	function infoRecursionTransaction(id){
		$.ajax({
			url: '<?= base_url("usercontrol/info_recursion_tran") ?>',
			type:'POST',
			dataType:'json',
			data:{id:id},
			success:function(json){
				$("#modal-recursion .modal-body").html(json['html']);
				$("#modal-recursion").modal("show");
				if( json['recursion_type'] == 'custom_time' ){
					$('.custom_time').show();
				}else{
					$('.custom_time').hide();
				}
			},
		})
	}

</script>


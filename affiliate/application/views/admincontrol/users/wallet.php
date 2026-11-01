<?php
	$db =& get_instance();
	$userdetails=$db->userdetails();
?>
<div class="row gx-3 gy-3 wallet-page">
  <div class="col-xl-3 d-flex">
    <div class="card flex-fill">
      <div class="card-header bg-secondary text-white text-center text-uppercase">
        <h5><?= __('admin.admin_balance') ?></h5>
      </div>
      <div class="card-body">
        <div class="text-center">
          <ul class="list-inline row mb-0 clearfix">
            <li class="col-12">
              <p class="m-b-5 counter"><?= c_format($admin_totals['admin_balance']) ?></p>
              <p class="mb-0 text-muted"><?= __('admin.total_admin_balance') ?></p>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 d-flex">
    <div class="card flex-fill">
      <div class="card-header bg-secondary text-white text-center text-uppercase">
        <h5><?= __('admin.total_sales') ?></h5>
      </div>
      <div class="card-body">
        <div class="text-center">
          <ul class="list-inline row mb-0 clearfix">
            <li class="col-6">
              <p class="m-b-5 counter"><?= c_format($admin_totals['sale_localstore_total'] + $admin_totals['order_external_total']) ?></p>
              <p class="mb-0 text-muted"><?= __('admin.admin_store') ?></p>
            </li>
            <li class="col-6">
              <p class="m-b-5 counter"><?= c_format($admin_totals['sale_localstore_vendor_total']) ?></p>
              <p class="mb-0 text-muted"><?= __('admin.vendor_store') ?></p>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 d-flex">
    <div class="card flex-fill">
      <div class="card-header bg-secondary text-white text-center text-uppercase">
        <h5><?= __('admin.actions') ?></h5>
      </div>
      <div class="card-body">
        <div class="text-center">
          <ul class="list-inline row mb-0 clearfix">
            <li class="col-12">
              <p class="m-b-5 counter"><?= (int)$admin_totals['click_action_total'] ?> / <?= c_format($admin_totals['click_action_commission']) ?></p>
              <p class="mb-0 text-muted"><?= __('admin.all_actions') ?></p>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 d-flex">
    <div class="card flex-fill">
      <div class="card-header bg-secondary text-white text-center text-uppercase">
        <h5><?= __('admin.clicks') ?></h5>
      </div>
      <div class="card-body">
        <div class="text-center">
          <ul class="list-inline row mb-0 clearfix">
            <li class="col-12">
              <p class="m-b-5 counter">
                <?= (int)(
                  $admin_totals['click_localstore_total'] +
                  $admin_totals['click_integration_total'] +
                  $admin_totals['click_form_total'] 
                ) ?> /
                <?= c_format(
                  $admin_totals['click_localstore_commission'] +
                  $admin_totals['click_integration_commission'] +
                  $admin_totals['click_form_commission']
                ) ?>
              </p>
              <p class="mb-0 text-muted"><?= __('admin.all_clicks') ?></p>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
	<div class="col-sm-12">
		<div class="card border-0">
			<div class="card-header border-top border-left border-right">
				<div class="clearfix">
					<div class="pull-left">
						<h5 class="card-title m-0"><?= __('admin.filter_transactions') ?></h5>
					</div>
					<div class="pull-right">
						<button class="filter-toggle btn btn-md">
						    <i class="bi bi-filter text-dark"></i>
						</button>

						<button class="btn btn-danger btn-sm delete-multiple" type="button"><?= __('admin.delete_selected') ?> <span class="selected-count"></span></button>	
					</div>
				</div>
				<form method="GET" class="wallet-filter mt-2" style="display: none;">
<div class="row">
    <div class="col-12 col-md-6 col-lg-3">
        <div class="form-group">
            <select class="form-select" name="user_id">
                <option value=""><?= __('admin.filter_by_user') ?></option>
                <?php foreach ($users as $key => $value) { ?>
                    <option <?= isset($user_id) && $user_id == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>">
                    	<?= $value['name'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        
        <div class="form-group">
            <input autocomplete="off" type="text" name="date" value="<?= isset($_GET['date']) ? $_GET['date'] : '' ?>" class="form-control daterange-picker" placeholder='<?= __('admin.filter_by_date') ?>'>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <div class="form-group">
            <select class="form-select" name="status">
                <option value=""><?= __('admin.filter_by_status') ?></option>
                <option value="0" <?= isset($_GET['status']) && $_GET['status'] == '0' ? 'selected' : '' ?>><?= __('admin.on_hold') ?></option>
                <option value="1" <?= isset($_GET['status']) && $_GET['status'] == '1' ? 'selected' : '' ?>><?= __('admin.in_wallet') ?></option>
                <option value="2" <?= isset($_GET['status']) && $_GET['status'] == '2' ? 'selected' : '' ?>><?= __('admin.request_send') ?></option>
                <option value="3" <?= isset($_GET['status']) && $_GET['status'] == '3' ? 'selected' : '' ?>><?= __('admin.accept') ?></option>
                <option value="4" <?= isset($_GET['status']) && $_GET['status'] == '4' ? 'selected' : '' ?>><?= __('admin.reject') ?></option>
            </select>
        </div>

        <div class="form-group">
            <select class="form-select" name="recurring">
                <option value=""><?= __('admin.filter_by_recurring_transaction') ?></option>
                <option value="0" <?= isset($_GET['recurring']) && $_GET['recurring'] == '0' ? 'selected' : '' ?>><?= __('admin.not_recurring') ?></option>
                <option value="1" <?= isset($_GET['recurring']) && $_GET['recurring'] == '1' ? 'selected' : '' ?>><?= __('admin.recurring') ?></option>
            </select>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <div class="form-group">
            <select class="form-select" name="type">
                <option value=""><?= __('admin.filter_by_type') ?></option>
                <option value="actions" <?= isset($_GET['type']) && $_GET['type'] == 'actions' ? 'selected' : '' ?>><?= __('admin.actions') ?></option>
                <option value="clicks" <?= isset($_GET['type']) && $_GET['type'] == 'clicks' ? 'selected' : '' ?>><?= __('admin.clicks') ?></option>
                <option value="sale" <?= isset($_GET['type']) && $_GET['type'] == 'sale' ? 'selected' : '' ?>><?= __('admin.sale') ?></option>
                <option value="external_integration" <?= isset($_GET['type']) && $_GET['type'] == 'external_integration' ? 'selected' : '' ?>><?= __('admin.external_integration') ?></option>
            </select>
        </div>

        <div class="form-group">
            <select class="form-select" name="paid_status">
                <option value=""><?= __('admin.filter_by_paid_type') ?></option>
                <option value="paid" <?= isset($_GET['paid_status']) && $_GET['paid_status'] == 'paid' ? 'selected' : '' ?>><?= __('admin.paid') ?></option>
                <option value="unpaid" <?= isset($_GET['paid_status']) && $_GET['paid_status'] == 'unpaid' ? 'selected' : '' ?>><?= __('admin.unpaid') ?></option>
            </select>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <div class="form-group d-flex align-items-end">
            <button class="btn btn-primary"><?= __('admin.filter') ?></button>
        </div>
    </div>
</div>


				</form>
			</div>
			<div class="card-body p-0">
				<div class="text-center1">
					<div class="showmessage"></div>
					<?php if ($transaction ==null) {?>
						<div class="d-flex justify-content-center align-items-center flex-column mt-5">
						    <i class="fas fa-exchange-alt fa-5x text-muted"></i>
						    <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
						</div>
					<?php } else { ?>
						<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/wallet.css?v='. time()) ?>">
						<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/flag/css/main.min.css?v='. time()) ?>">

						<div class="table-responsive wallet-table--design">
							<table class="table transaction-table">
								<thead class="bg-secondary text-white">
									<tr>
										<th></th>
										<th></th>
										<th><?= __('admin.id') ?></th>
										<th><?= __('admin.date') ?></th>
										<th><?= __('admin.owner') ?></th>
										<th><?= __('admin.user') ?></th>
										<th><?= __('admin.integration_type') ?></th>
										<th><?= __('admin.commission') ?></th>
										<th><?= __('admin.commission_owner') ?></th>
										<th><?= __('admin.payment') ?></th>
										<th><?= __('admin.status') ?></th>
										<th><?= __('admin.actions') ?></th>
										<th><?= __('admin.automation') ?></th>
									</tr>
								</thead>
								<tbody>
									<?php 
										$group_changed = 1;
										$html = '';
										$lastRow = count($transaction)-1;

										foreach ($transaction as $key => $value) { 
											$class = '';
							     			if($current_group_id && $current_group_id == $value['group_id']){
							     				$class = 'child';
							     			} else{
							     				$current_group_id = $value['group_id'];
							     				$group_changed =1;
							     			}
							     			
							     			$value['wallet_recursion_endtime'] = ($value['wallet_recursion_endtime'] == "0000-00-00 00:00:00") ? null : $value['wallet_recursion_endtime'];
							     			
							     			$data = [];

							     			$data['value'] = $value;
							     			$data['userdetails'] = $userdetails;
							     			$data['class'] = $class;
							     			$data['wallet_status'] = $status;
							     			$data['has_child'] = (isset($transaction[$key+1]) && $transaction[$key+1]['group_id'] &&  $transaction[$key+1]['group_id'] == $value['group_id']) ? 1  : 0;
							     			$data['child_id'] = (isset($transaction[$key+1]) && $transaction[$key+1]['group_id'] &&  $transaction[$key+1]['group_id'] == $value['group_id']) ? $transaction[$key+1]['id']  : null;
							     			
							     			$html .= $this->Product_model->getHtml('admincontrol/users/part/new_wallet_tr', $data);

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
	</div>
</div>
<div class="modal modal-style" id="modal-completed">
	<div class="modal-dialog modal-dialog-centered" role="document">
	    <div class="modal-content">
	        <div class="modal-header">
	            <h5 class="modal-title"><?= __('admin.payment_completed') ?></h5>
	            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
	        </div>
	        <div class="modal-body">
	   			<p><?= __('admin.transaction_status_can_change_revert') ?></p>
	        </div>
	        <div class="modal-footer">
	            <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
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

<div id="wallet-details-model" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?= __('admin.order_details') ?></h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="delete-wallet-record-modal" aria-modal="true">
	<div class="modal-dialog">
		<div class="modal-content">    <div class="modal-header">
        <h5 class="modal-title"><?= __('admin.wallet_notification_info') ?></h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
    </div>
    <div class="modal-body">
        <h4 class="notification_on_pages">
            <div class="bg-danger text-white"><?= __('admin.please_select_at_least_one_wallet_record') ?></div>
        </h4>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
    </div>
</div>
	</div>
</div>

<script src="<?= base_url('assets/plugins/datatable') ?>/moment.js"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/datatable') ?>/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/datatable') ?>/daterangepicker.css" />

<script type="text/javascript">

	$(document).on('click', '.view-tran-details', function () {
		
		let data = {
			type : $(this).data('comm_from'),
			ref1 : $(this).data('ref_id_1'),
			ref2 : $(this).data('ref_id_2')
		};

		$.ajax({
			url:'<?= base_url('admincontrol/getOrderDetails') ?>',
			type:'POST',
			dataType:'html',
			data:data,
			success:function(response){
				$('#wallet-details-model .modal-body').html(response);
				$('#wallet-details-model').modal('show');
			},
		});
	});


	$(document).delegate(".show-child-transaction","click",function(){

		$tr = $(this).parents("tr");
		var status = $(this).find("i").hasClass('fa-angle-down') ? 1 : 0;
		var group_id = $tr.attr("group_id");
		
		if(status){
			$('.transaction-table .child-row[group_id='+ group_id +']:not(.recurring)').show();
			$(this).find("i").removeClass('fa-angle-down');
			$(this).find("i").addClass('fa-angle-up');
			$tr.addClass('opened')
			$('.transaction-table [group_id='+ group_id +']').addClass('highlight');
		} else{
			$('.transaction-table .child-row[group_id='+ group_id +']:not(.recurring)').hide();
			$(this).find("i").removeClass('fa-angle-up');
			$(this).find("i").addClass('fa-angle-down');
			$tr.removeClass('opened')
			$('.transaction-table [group_id='+ group_id +']').removeClass('highlight');
		}

		$('.transaction-table .child-row[group_id='+ group_id +']:last').addClass("last-group-row");
	});

	$(document).delegate(".show-recurring-transition","click",function(){
		$this = $(this);
		var id = $this.attr("data-id");
		$this.find("i").toggleClass("mdi-plus mdi-minus")
		$nextAll = $this.parents("tr").nextAll("tr.recurringof-"+id);

		$this.parents("tr").nextAll("tr.recurringof-"+id+":last").addClass('last-recurring');

		if($nextAll.length){
			if($nextAll.eq(0).css("display") == 'table-row'){
				$this.parents("tr").removeClass('opened-recurring');
				$nextAll.hide();
			} else {
				$this.parents("tr").addClass('opened-recurring');
				$nextAll.show();
			}
			return false;
		}

		$this.parents("tr").nextAll("tr.recurringof-"+id).remove();
		
		$.ajax({
			url:'<?= base_url('admincontrol/getRecurringTransaction') ?>',
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
					$(".wallet-popover").popover({
						placement : 'right',
						html : true,
					});
				}
			},
		})
	});

	$(".filter-toggle").on("click", function(){
		$(".wallet-filter").slideToggle('fast');
	})

	$(document).delegate('.selectall-wallet-checkbox','change',function(){
		$(".wallet-checkbox").prop("checked", $(this).prop("checked")).trigger("change");
	});

	$(document).delegate(".wallet-checkbox",'change',function(){
		if($(".wallet-checkbox:checked").length == 0){
			$(".delete-multiple").hide();
		} else {
			$(".delete-multiple").show();
			$(".selected-count").text($(".wallet-checkbox:checked").length);
		}
	})

	$(".delete-multiple").on('click',function(){
		var ids = $(".wallet-checkbox:checked").map(function(){ return $(this).val() }).toArray().join(",");
		if(ids){
		$this = $(this);
		$.ajax({
			url: '<?php echo base_url("admincontrol/info_remove_tran_multiple") ?>',
			type:'POST',
			dataType:'json',
			data:{ids:ids},
			beforeSend:function(){ $this.button("loading"); },
			complete:function(){ $this.button("reset"); },
			success:function(json){
				$("#modal-confirm .modal-body").html(json['html']);
				$("#modal-confirm").modal("show");
			},
		})
		}else{
			
			$("#delete-wallet-record-modal").modal("show");
		}
	})

	$("#modal-confirm .modal-body").delegate("[delete-mmultiple-confirm]","click",function(){
		$this = $(this);
		$.ajax({
			url: '<?php echo base_url("admincontrol/confirm_remove_tran_multi") ?>',
			type:'POST',
			dataType:'json',
			data:{id:$this.attr("delete-mmultiple-confirm")},
			beforeSend:function(){ $this.button("loading"); },
			complete:function(){ $this.button("reset"); },
			success:function(json){
				window.location.reload();
			},
		})
	})

	$('.daterange-picker').daterangepicker({
		opens: 'left',
		autoUpdateInput: false,
		ranges: {
			'Today': [moment(), moment()],
			'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			'This Month': [moment().startOf('month'), moment().endOf('month')],
			'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
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
	

	$("#modal-confirm .modal-body").delegate("[delete-tran-confirm]","click",function(){
		$this = $(this);
		$.ajax({
			url: '<?php echo base_url("admincontrol/confirm_remove_tran") ?>',
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
	        url: '<?php echo base_url("admincontrol/change_commission_status") ?>',
	        data: {status_type:status_type,id:id},
	        cache: false,
	        success: function(data) 
	        {
	        	window.location.reload();
	        }
	    });
	});
	
	$('[name="user_id"]').select2();


	/*open and close popover tooltip*/
	$(function () {
	  // Initialize the popover
	  var popover = $('[data-bs-toggle="popover"]').popover({
	    html: true,
	    trigger: 'click'
	  });

	  // Add a click event listener to the document object
	  $(document).on('click', function (e) {
	    // Check if the clicked element is inside the popover or not
	    if (!popover.has(e.target).length) {
	      // If it is not, hide the popover
	      popover.popover('hide');
	    }
	  });

	  // Add a click event listener to the popover
	  popover.on('click', function () {
	    // Hide all other popovers except the current one
	    $('[data-bs-toggle="popover"]').not(this).popover('hide');
	  });
	});

	/*open and close popover tooltip*/

	$(document).ready(function(){
		$(".wallet-popover").popover({
			placement : 'right',
			html : true,
		});
	})
		$('html').on('click', function(e) {
		
		if (typeof $(e.target).data('original-title') == 'undefined' &&
			!$(e.target).parents().is('.popover.in')) {
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
			url: '<?= base_url("admincontrol/info_remove_tran_by_commission") ?>',
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
			url: '<?= base_url("admincontrol/wallet_change_status") ?>',
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
			url: '<?= base_url("admincontrol/info_remove_tran") ?>',
			type:'POST',
			dataType:'json',
			data:{id:id},
			success:function(json){
				$("#modal-confirm .modal-body").html(json['html']);
				$("#modal-confirm").modal("show");
			},
		});
	}

	function infoRecursionTransaction(id){
		$.ajax({
			url: '<?= base_url("admincontrol/info_recursion_tran") ?>',
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
<div class="modal fade" id="importUsersModel" tabindex="-1" aria-labelledby="importUsersModelLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="importUsersModelLabel"><?= __('admin.import_users') ?></h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form id="import_form">
					<label class="file">
						<input name="import_control" type="file" id="import_control" aria-label="File browser example">
						<span class="file-custom"></span>
					</label>
				</form>
				<div id="import-status"></div>
				<div id="import-log"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
				<a href="" class="btn btn-secondary d-none"><?= __('admin.close') ?></a>
				<button type="button" class="btn btn-primary btn_import_data" data-bs-dismiss="modal"><?= __('admin.upload') ?></button>
			</div>
		</div>
	</div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card">
                <div class="card-body">
									<form id="search-form">
									    <div class="row">
									        <!-- Search by Name -->
									        <div class="col-12 col-sm-2">
									            <div class="mb-3">
									                <input type="search" name="name" class="form-control" placeholder="<?= __('admin.name') ?>">
									            </div>
									        </div>
									        <!-- Search by Email -->
									        <div class="col-12 col-sm-2">
									            <div class="mb-3">
									                <input type="search" name="email" class="form-control" placeholder="<?= __('admin.email') ?>">
									            </div>
									        </div>
									        <!-- Select Group -->
									        <div class="col-12 col-sm-2">
									            <div class="mb-3">
									                <select class="form-select select2" name="groups[]" multiple="multiple" data-placeholder="<?= __('admin.groups') ?>">
									                    <?php foreach ($user_groups as $key => $group) { ?>
									                        <option value="<?= $group->id ?>">
									                            <?= $group->group_name ?>
									                        </option>
									                    <?php } ?>
									                </select>
									            </div>
									        </div>
									        <!-- Buttons -->
									        <div class="col-12 col-sm-6">
									            <div class="mb-3">
									                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
									                    <a class="btn btn-primary mb-2" href="<?= base_url("admincontrol/addusers") ?>"><?= __('admin.add_affiliate') ?></a>
									                    <button type="button" class="btn btn-dark mb-2 export-excel"> <i class="fa fa-file-excel"></i> <?= __('admin.export') ?></button>
									                    <button type="button" class="btn btn-info mb-2 import-excel" data-bs-toggle="modal" data-bs-target="#importUsersModel"> <i class="fa fa-file-excel"></i> <?= __('admin.import') ?></button>
									                    <button class="btn btn-danger mb-2 delete-multiple" type="button"><?= __('admin.delete_selected') ?><span class="selected-count"></span></button>
									                </div>
									            </div>
									        </div>
									    </div>
									</form>
                    <div class="selection-message">
                        <?= __('admin.all') ?> <span class="selected-count"></span> <?= __('admin.users_on_this_page_are_selected') ?>
                        <a href="javascript:void(0)" class="select-all-users"><?= __('admin.select_all') ?> <span class="total-user"></span> <?= __('admin.users') ?></a>
                        <a href="javascript:void(0)" class="clear-selection"><?= __('admin.clear_selection') ?></a>
                    </div>
                    <div class="dimmer">
                        <div class="loader"></div>
                        <div class="dimmer-content">
                            <div class="table-responsive">
                                <div class="table-header-menus">
                                    <p class="p-2 mb-0 lead user-approvals-filer">
																		<?php if ($approvals_count['total'] > 0) { ?>
																				<a class="px-2 py-1 rounded <?= (!isset($_GET['apr']) || $_GET['apr'] == 'all') ? 'bg-primary text-white' : 'bg-secondary text-white' ?>" data-apr="all" href="javascript:void(0);">show all users (<?= $approvals_count['total']; ?>)</a>
																			<?php } ?>
																			<?php if ($approvals_count['pending'] > 0 || $approvals_count['declined'] > 0) { ?>
																				<?php if ($approvals_count['approved'] > 0) { ?>
																					<a class="px-2 py-1 rounded <?= (isset($_GET['apr']) && $_GET['apr'] == 'approved') ? 'bg-primary text-white' : 'bg-secondary text-white' ?>" data-apr="approved" href="javascript:void(0);">show approved users (<?= $approvals_count['approved']; ?>)</a>
																				<?php } ?>
																				<?php if ($approvals_count['pending'] > 0) { ?>
																					<a class="px-2 py-1 rounded <?= (isset($_GET['apr']) && $_GET['apr'] == 'pending') ? 'bg-primary text-white' : 'bg-secondary text-white' ?>" data-apr="pending" href="javascript:void(0);">show pending approvals (<?= $approvals_count['pending'] ?>)</a>
																				<?php } ?>
																				<?php if ($approvals_count['declined'] > 0) { ?>
																					<a class="px-2 py-1 rounded <?= (isset($_GET['apr']) && $_GET['apr'] == 'declined') ? 'bg-primary text-white' : 'bg-secondary text-white' ?>" data-apr="declined" href="javascript:void(0);">show declined approvals (<?= $approvals_count['declined'] ?>)</a>
																				<?php } ?>
																			<?php } ?>
																			<div class="multi-approve-decline">
																				<a href="javascript:void(0)" class="text-success approved-decline-action" data-action-value="1">Approved</a> / <a href="javascript:void(0)" class="text-danger approved-decline-action" data-action-value="2">Decline</a>
																			</div>
                                    </p>
                                </div>
                                <table id="tech-companies-1" class="table table-hover user-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th><input type="checkbox" class="selectall-wallet-checkbox"></th>
                                            <th><?= __('admin.user_details') ?></th>
                                            <th><?= __('admin.user_level') ?></th>
                                            <th><?= __('admin.membership_details') ?></th>
                                            <th><?= __('admin.plan_status') ?></th>
                                            <th><?= __('admin.country') ?></th>
                                            <th><?= __('admin.groups') ?></th>
                                            <th><?= __('admin.vendor') ?></th>
                                            <th><?= __('admin.referred_by') ?></th>
                                            <th><?= __('admin.action') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="100%" class="text-end">
                                                <div class="pagination"></div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-delete" tabindex="-1" aria-labelledby="modal-deleteLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div id="message"></div>
				<hr>
				<div>
					<div class="form-check">
						<input class="form-check-input" type="checkbox" value="delete_transaction" id="delete_transaction">
						<label class="form-check-label" for="delete_transaction">
							<?= __('admin.delete_all_transaction_or_commission') ?>
						</label>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('admin.cancel') ?></button>
				<button type="button" class="btn btn-primary confirm-delete" data-id="0"><?= __('admin.delete') ?></button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-tree" tabindex="-1" aria-labelledby="modal-treeLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-body"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
			</div>
		</div>
	</div>
</div>

<script src="<?= base_url('assets/plugins/tree') ?>/jquery-ui-1.10.4.custom.min.js"></script>

<script src="<?= base_url('assets/plugins/tree') ?>/jquery.tabelizer.js"></script>

<link href="<?= base_url('assets/plugins/tree') ?>/tabelizer.min.css?v=<?= av() ?>" media="all" rel="stylesheet" type="text/css" />


<!--footer_user_payment_details_modal-->
<div class="modal" id="payment-detail_modal" tabindex="-1" aria-labelledby="payment-detail_modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title mt-0"><?= __('admin.footer_user_payment_details') ?></h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <h4 class="modal-title mt-0"><?= __('admin.footer_bank_details') ?></h4>
        <div class="table-rep-plugin">
          <div class="table-responsive b-0" data-pattern="priority-columns">
            <table id="tech-companies-1" class="table table-striped">
              <thead>
                <tr>
                  <th><?= __('admin.footer_bank_name') ?></th>
                  <th><?= __('admin.footer_account_number') ?></th>
                  <th><?= __('admin.footer_account_name') ?></th>
                  <th><?= __('admin.footer_ifsc_code') ?></th>
                </tr>
              </thead>
              <tbody class="bank-details"></tbody>
            </table>
          </div>
        </div>

        <h4 class="modal-title mt-0"><?= __('admin.footer_paypal_emails') ?></h4>
        <div class="table-rep-plugin">
          <div class="table-responsive b-0" data-pattern="priority-columns">
            <table id="tech-companies-1" class="table table-striped">
              <thead>
                <tr>
                  <th></th>
                  <th><?= __('admin.footer_email') ?></th>
                </tr>
              </thead>
              <tbody class="paypal-details"></tbody>
            </table>
          </div>
        </div>

        <h4 class="modal-title mt-0"><?= __('admin.footer_user_details') ?></h4>
        <div class="table-rep-plugin">
          <div class="table-responsive b-0" data-pattern="priority-columns">
            <table id="tech-companies-1" class="table table-striped">
              <tbody class="user-details"></tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('admin.footer_close') ?></button>
        </div>
      </div>
    </div>
  </div>
</div>
<!--footer_user_payment_details_modal-->


<!--footer_user_payment_details_script-->
<script type="text/javascript">
  $(document).delegate("[payment_detail]",'click',function(e){
    e.preventDefault();
    e.stopPropagation();
    $this = $(this);
    var user_id = $this.attr("payment_detail");
    $.ajax({
      url:'<?= base_url("admincontrol/getpaymentdetail") ?>/' + user_id,
      type:'POST',
      dataType:'json',
      beforeSend:function(){ $this.btn("loading"); },
      complete:function(){ $this.btn("reset"); },
      success:function(json){

        $('#payment-detail_modal').modal("show");
        var html = '';
        $.each(json['paymentlist'], function(i,j){
          html += '<tr>';
          html += '<th>'+ j['payment_bank_name'] +'</th>';
          html += '<th>'+ j['payment_account_number'] +'</th>';
          html += '<th>'+ j['payment_account_name'] +'</th>';
          html += '<th>'+ j['payment_ifsc_code'] +'</th>';
          html += '</tr>';
        })  

        $('#payment-detail_modal .bank-details').html(html);

        var html = '';

        $.each(json['paypalaccounts'], function(i,j){

          html += '<tr>';

          html += '<th>'+ (i+1) +'</th>';

          html += '<th>'+ j['paypal_email'] +'</th>';

          html += '</tr>';

        })  

        $('#payment-detail_modal .paypal-details').html(html);

        var html = '';

        html += '<tr>';

        html += '<th><?= __('admin.footer_firstname') ?></th>';

        html += '<td>'+ json.user.firstname +'</td>';

        html += '</tr>';

        html += '<tr>';

        html += '<th><?= __('admin.footer_lastname') ?></th>';

        html += '<td>'+ json.user.lastname +'</td>';

        html += '</tr>';

        html += '<tr>';

        html += '<th><?= __('admin.footer_username') ?></th>';

        html += '<td>'+ json.user.username +'</td>';

        html += '</tr>';

        html += '<tr>';

        html += '<th><?= __('admin.footer_email') ?></th>';

        html += '<td>'+ json.user.email +'</td>';

        html += '</tr>';

        html += '<tr>';

        html += '<th><?= __('admin.footer_phone') ?></th>';

        html += '<td>'+ json.user.phone +'</td>';

        html += '</tr>';

        html += '<tr>';

        html += '<th><?= __('admin.footer_address') ?></th>';

        html += '<td>'+ json.user.address +'</td>';

        html += '</tr>';

        html += '<tr>';

        html += '<th><?= __('admin.footer_state') ?></th>';

        html += '<td>'+ json.user.state +'</td>';

        html += '</tr>';

        html += '<tr>';

        html += '<th><?= __('admin.footer_country') ?></th>';

        html += '<td>'+ json.user.country +'</td>';

        html += '</tr>';

        $('#payment-detail_modal .user-details').html(html);
      },  
    })
  })
</script>
<!--footer_user_payment_details_script-->


<script type="text/javascript" async="">
	$(document).on('click', '.btn-login-aff', function(){
		$.post('<?= base_url('admincontrol/doLoginAff') ?>', {id:$(this).data('id')}, function(result) {
			$res=$.trim(result);
			if($res == 'success') {
				window.open('<?= base_url('usercontrol/dashboard') ?>', '_blank');
			}
		})
	})

	$(document).on('click', '.user-approvals-filer a', function(){
	    $('.user-approvals-filer a.bg-primary.text-white').removeClass('bg-primary text-white').addClass('bg-secondary text-white');
	    $(this).removeClass('bg-secondary text-white').addClass('bg-primary text-white');
	    getPage(1);
	});

	$(document).on('click', 'a[data-approval-change]', function(){
		if(xhr && xhr.readyState != 4) xhr.abort();
		data = {};
		let status = ($(this).data('approval-change') == 1) ? 'approve_users' : 'decline_users'
		data['action'] = "process_approval";
		data[status] = [$(this).data('user-id')];
		xhr = $.ajax({
			type:'POST',
			dataType:'json',
			data: data,
			beforeSend:function(){
				$(".dimmer").addClass("active");
			},
			complete:function(){
				$(".dimmer").removeClass("active");
			},
			success:function(response){
				if(response.approvals_status.status) {
					$('.approvals-status-alert').removeClass('alert-danger');
					$('.approvals-status-alert').addClass('alert-success');
					$('.approvals-status-alert').text(response.approvals_status.message);
				} else {
					$('.approvals-status-alert').addClass('alert-danger');
					$('.approvals-status-alert').removeClass('alert-success');
					$('.approvals-status-alert').text(response.approvals_status.message);
				}
				$('.approvals-status-alert').show();
				setTimeout(function(){ $('.approvals-status-alert').hide(); }, 3000);
				reloadApprovalFilter(response.approvals_count)
				getPage(1, response.approvals_count);
			}
		});
	});

	var selected = {};

	var all_ids = [];

	$('.clear-selection').on('click',function(){

		selected = {};

		$(".selection-message").addClass('d-none');

		$('.selectall-wallet-checkbox').prop("checked",0);

		changeViews();

	});

	function changeViews() {

		$(".wallet-checkbox").prop("checked",  false);

		if(Object.keys(selected).length == 0){

			$(".selection-message").addClass('d-none');

		} else {

			$(".selection-message").removeClass('d-none');

			$(".selected-count").text(Object.keys(selected).length);

		}

		$(".select-all-users").show();

		if(Object.keys(selected).length == all_ids.length){

			$(".select-all-users").hide();

		}

		$.each(selected, function(i,j){

			$('.wallet-checkbox[value="'+ j +'"]').prop("checked",true);

		})

		if(Object.keys(selected).length == 0){

			$(".delete-multiple").hide();
			$(".multi-approve-decline").hide();

		} else {

			$(".delete-multiple").show();
			$(".multi-approve-decline").show();

		}

	}

	$('.selectall-wallet-checkbox').on('change',function(){

		$(".wallet-checkbox").prop("checked",  $(this).prop("checked"));

		$('.wallet-checkbox').each(function(){

			var val = $(this).val();

			if($(this).prop("checked")){ selected[val]=val; } 

			else { delete selected[val]; }

		})

		changeViews();
	})

	jQuery('.select2').select2({
		placeholder : "<?= __('admin.filter_by_groups') ?>"
	});

	$(".user-table").delegate(".wallet-checkbox","change",function(){

		var status = $(this).prop("checked");

		if(!status) delete selected[$(this).val()]

			else selected[$(this).val()] = $(this).val();

		changeViews();

	})

	$(".select-all-users").on('click',function(){

		$this = $(this);

		$.ajax({
			type:'POST',
			dataType:'json',
			data:{action:'get_all_ids'},
			beforeSend:function(){ $this.btn("loading");},
			complete:function(){ $this.btn("reset"); },
			success:function(json){
				$.each(json['ids'],function(i,id){
					selected[id]= id;
				})
				$(".selected-count").text(Object.keys(selected).length);
				all_ids = json['ids'];
				changeViews();
			},
		});

	})

	$(".user-table").delegate(".checkbox-label","click",function(e){

		e.stopPropagation();

	});

	$(document).delegate("[edit-plan-user]","click",function(e){

		e.stopPropagation();

		var user_id = $(this).attr('edit-plan-user');
		
		var is_vendor = $(this).attr('edit-plan-user-type');

		$("#membershipuser-image").remove();


		$this = $(this);

		$.ajax({

			url:'<?= base_url("membership/user_plan_modal") ?>',

			dataType:'html',

			data:{user_id:user_id, is_vendor:is_vendor},

			beforeSend:function(){$this.btn("loading");},

			complete:function(){$this.btn("reset");},

			success:function(html){

				$('body').append('<div id="membershipuser-image" class="modal">' + html + '</div>');

				$('#membershipuser-image').modal('show');
			},
		})
	});



	$(".delete-multiple").on('click',function(e){

		$this = $(this);

		var ids = Object.keys(selected).join(",");

		e.preventDefault();

		e.stopPropagation();

		if(!confirm('<?= __('admin.are_you_sure') ?>')) return false;

		$.ajax({

			url: '<?php echo base_url("admincontrol/deleteAllusersMultiple") ?>',

			type:'POST',

			dataType:'json',

			data:{ids:ids},

			beforeSend:function(){ $this.btn("loading"); },

			complete:function(){ $this.btn("reset"); },

			success:function(json){

				$("#modal-delete #message").html(json['message']);

				$("#modal-delete .confirm-delete").attr("data-id",ids);

				$("#modal-delete").modal("show");

			},

		})

	})

	var xhr;

	function getPage(page, existingCounts = null) {

		$this = $(this);

		if(xhr && xhr.readyState != 4) xhr.abort();

		let reg_approval_filter = $('.user-approvals-filer a.bg-primary.text-white').data('apr');

		if((reg_approval_filter == 'pending' || reg_approval_filter == 'approved' || reg_approval_filter == 'declined') && existingCounts != null && existingCounts[reg_approval_filter] == 0) {
			reg_approval_filter = 'all';
		}

 
		let data = $("#search-form").serialize();

		xhr = $.ajax({

			type:'POST',

			dataType:'json',

			data: data + "&apr="+reg_approval_filter+"&page="+page,

			beforeSend:function(){

				$(".dimmer").addClass("active");

			},

			complete:function(){

				$(".dimmer").removeClass("active");

			},

			success:function(json){

				if(json['table']){

					$('.selectall-wallet-checkbox').prop("checked",false)

					$(".user-table tbody").html(json['table']);

					reloadApprovalFilter(json['approvals_count']);
					changeViews();

				}

				if(json['pagination']){

					$(".user-table tfoot .pagination").html(json['pagination']);

				}

			},

		})

	}

	function reloadApprovalFilter(data) {
		if($('.user-approvals-filer').length == 0) {
			$('.dimmer .table-responsive').append('<p class="p-2 mb-0 lead user-approvals-filer"><p>')
		}
		
		if(data['total'] > 0) {
		    if($('.user-approvals-filer a[data-apr="all"]').length <= 0) {
		        $('.user-approvals-filer').append('<a class="px-2 py-1 rounded bg-secondary text-white" data-apr="all" href="javascript:void(0);">'+'<?= __('admin.show_all_users') ?>'+' ('+data['total']+')</a>');
		    } else {
		        $('.user-approvals-filer a[data-apr="all"]').text('<?= __('admin.show_all_users') ?>'+' ('+data['total']+')')
		    }
		} else {
			$('.user-approvals-filer a[data-apr="all"]').remove();
		}
		if(data['approved'] > 0) { 
		    if($('.user-approvals-filer a[data-apr="approved"]').length <= 0) {
		        $('.user-approvals-filer').append('<a class="px-2 py-1 rounded bg-secondary text-white" data-apr="approved" href="javascript:void(0);">'+'<?= __('admin.show_approved_users') ?>'+' ('+data['total']+')</a>');
		    } else {
		        $('.user-approvals-filer a[data-apr="approved"]').text('<?= __('admin.show_approved_users') ?>'+' ('+data['approved']+')')
		    }
		} else {
			$('.user-approvals-filer a[data-apr="approved"]').remove();
		}
		if(data['pending'] > 0) {
		    if($('.user-approvals-filer a[data-apr="pending"]').length <= 0) {
		        $('.user-approvals-filer').append('<a class="px-2 py-1 rounded bg-secondary text-white" data-apr="pending" href="javascript:void(0);">'+'<?= __('admin.show_pending_users') ?>'+' ('+data['total']+')</a>');
		    } else {
		        $('.user-approvals-filer a[data-apr="pending"]').text('<?= __('admin.show_pending_users') ?>'+' ('+data['pending']+')')
		    } 
		} else {
			$('.user-approvals-filer a[data-apr="pending"]').remove();
		}
		if(data['declined'] > 0) {
		    if($('.user-approvals-filer a[data-apr="declined"]').length <= 0) {
		        $('.user-approvals-filer').append('<a class="px-2 py-1 rounded bg-secondary text-white" data-apr="declined" href="javascript:void(0);">'+'<?= __('admin.show_declined_users') ?>'+' ('+data['total']+')</a>');
		    } else {
		        $('.user-approvals-filer a[data-apr="declined"]').text('<?= __('admin.show_declined_users') ?>'+' ('+data['declined']+')')
		    }
		} else {
		    $('.user-approvals-filer a[data-apr="declined"]').remove();
		}
		if(data['pending'] == 0 && data['declined'] == 0) { 
			$('.user-approvals-filer a[data-apr="approved"]').remove();
			$('.user-approvals-filer a[data-apr="pending"]').remove();
			$('.user-approvals-filer a[data-apr="declined"]').remove();
		} 

		if($('.user-approvals-filer a.bg-primary.text-white').length == 0) {
		    $('.user-approvals-filer a[data-apr="all"]').removeClass('bg-secondary text-white').addClass('bg-primary text-white');
		}
	}

	$("#search-form input").on('keyup',function(){
		getPage(1);
	});

	$("#search-form select").on('change',function(){
		getPage(1);
	})

	$(".user-table tfoot .pagination").delegate("a","click", function(e){
		e.preventDefault();
		getPage($(this).attr("data-ci-pagination-page"));
	})

	getPage(1);

	$(".user-table").delegate(".btn-remove",'click',function(e){
		if(!confirm('<?= __('admin.are_you_sure') ?>')) e.preventDefault();
		return true;
	});

	$(".user-table").delegate(".show-tree",'click',function(e){
		e.preventDefault();
		e.stopPropagation();

		$this = $(this);
		$.ajax({
			url: '<?php echo base_url("admincontrol/showTree") ?>',
			type:'POST',
			dataType:'json',
			data:{id:$this.attr("data-id")},
			beforeSend:function(){ $this.btn("loading"); },
			complete:function(){ $this.btn("reset"); },
			success:function(json){
				$("#modal-tree .modal-body").html(json['html']);
				$("#modal-tree").modal("show");
			},
		});
	});

	$(".user-table").delegate(".btn-delete2",'click',function(e){
		e.preventDefault();
		e.stopPropagation();
		$this = $(this);
		/*delete user popup*/
		Swal.fire({
		   icon: 'warning',
		   title: '<?= __('admin.delete_user') ?>',
	       text: '<?= __('admin.are_you_sure') ?>',
	       showCancelButton: true,
	       cancelButtonText: 'cancel'
		}).then(function(dismiss){
	        if(dismiss.value==true)
	        {
				$.ajax({
					url: '<?php echo base_url("admincontrol/deleteAllusers") ?>',
					type:'POST',
					dataType:'json',
					data:{id:$this.attr("data-id")},
					beforeSend:function(){ $this.btn("loading"); },
					complete:function(){ $this.btn("reset"); },
					success:function(json){
						$("#modal-delete #message").html(json['message']);
						$("#modal-delete .confirm-delete").attr("data-id",$this.attr("data-id"));
						$("#modal-delete").modal("show");
					},
				});
	        }
	        else
	        {
	          return false;
	        }
	    });
	    /*delete user popup*/
	});

	$(document).delegate(".confirm-delete",'click',function(e){
		e.preventDefault();
		e.stopPropagation();
		$this = $(this);
		$.ajax({
			url: '<?php echo base_url("admincontrol/deleteUsersConfirm") ?>',
			type:'POST',
			dataType:'json',
			data:{
				id:$this.attr("data-id"),
				delete_transaction:$("#delete_transaction").prop("checked")
			},
			beforeSend:function(){ $this.btn("loading"); },
			complete:function(){ $this.btn("reset"); },
			success:function(json){

				if(json['status'] === 'error') {
					$("#modal-delete").modal("hide");
				  showPrintMessage(json['message'], 'error');
				  return;
				}

				window.location.reload();
			},
		})
	})

	$(".export-excel").on('click',function(){
		$this = $(this);
		$.ajax({
			url:'<?= base_url('admincontrol/get_user_data') ?>',
			type:'POST',
			dataType:'json',
			data: {
				action:'export',
			},

			beforeSend:function(){
				$this.btn("loading");
			},

			complete:function(){
				$this.btn("reset");
			},

			success:function(json){

      if(json['status'] === 'error') {
          showPrintMessage(json['message'], 'error');
          return;
      }

				console.log(json);
				if (json['download']) {

					window.location.href = json['download'];
				}
			},
		})
	})

	$(".btn_import_data").on('click',function(){

		$this = $("#import_form");

		var formData = new FormData($this[0]);

		formData.append("action",'import');

		formData = formDataFilter(formData);

		$(".btn_import_data").prop("disabled",true);

		$.ajax({
			url:'<?= base_url('admincontrol/get_user_data') ?>',
			type:'POST',
			dataType:'json',
			cache:false,
			contentType: false,
			processData: false,
			data:formData,
			xhr: function (){
			var jqXHR = null;

			if ( window.ActiveXObject ){
				jqXHR = new window.ActiveXObject( "Microsoft.XMLHTTP" );
			}else {
				jqXHR = new window.XMLHttpRequest();

			}
				jqXHR.upload.addEventListener( "progress", function ( evt ){
					if ( evt.lengthComputable ){
						var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
						$('#import-status').text('<?= __('admin.uploading') ?>'+" - " + percentComplete + "%").show();
					}

				}, false );

				jqXHR.addEventListener( "progress", function ( evt ){

					if ( evt.lengthComputable ){

						var percentComplete = Math.round( (evt.loaded * 100) / evt.total );

						$('#import-status').html('<?= __('admin.import_users') ?>'+'...');

					}

				}, false );

				return jqXHR;

			},

			error:function(){

				$(".btn_import_data").prop("disabled",false);

			},

			success:function(result){

				$(".btn_import_data").prop("disabled",false);

		    if(result['status'] === 'error') {
		        showPrintMessage(result['message'], 'error');
		        return;
		    }

				if(result['location']){ window.location = result['location']; }

				$(".hidden-close").removeClass("d-none");

				$(".btn-close").remove();

				$("#import-log").html('');

				$("#import-status").html('');

				if(result['errors']){

					showPrintMessage(result['errors'], 'error');

					$("#import-log").html(result['errors']);

				}
			},
		})

	});	

	$('.accordian-body').on('show.bs.collapse', function () {

		$(this).closest("table")

		.find(".collapse.in")

		.not(this)

		.collapse('toggle')

	})

	$(".approved-decline-action").on('click',function(e){
		var ids = Object.keys(selected).join(",");
		var data = {};
		let status = ($(this).data('action-value') == 1) ? 'approve_users' : 'decline_users'

		data['ids'] = ids;
		data[status] = ids;

		$.ajax({
			url: '<?php echo base_url("admincontrol/multiApproveDecline") ?>',
			type:'POST',
			dataType:'json',
			data:data,
			beforeSend:function(){
				$(".dimmer").addClass("active");
			},
			complete:function(){
				$(".dimmer").removeClass("active");
			},
			success:function(response){
				if (response.approvals_status.status != 'NULL') {
					if(response.approvals_status.status) {
						$('.approvals-status-alert').removeClass('alert-danger');
						$('.approvals-status-alert').addClass('alert-success');
						$('.approvals-status-alert').text(response.approvals_status.message);
					} else {
						$('.approvals-status-alert').addClass('alert-danger');
						$('.approvals-status-alert').removeClass('alert-success');
						$('.approvals-status-alert').text(response.approvals_status.message);
					}

					$('.approvals-status-alert').show();
					setTimeout(function(){ $('.approvals-status-alert').hide(); }, 3000);
				}
				
				reloadApprovalFilter(response.approvals_count)
				getPage(1, response.approvals_count);
				location.reload();
			}
		})
	});
</script>


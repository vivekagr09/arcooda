<div class="row">
	<div class="col-sm-12">
		<div class="card">
			<div class="card-header">
				<form method="GET">
					<div class="row">
						<div class="col-sm-3">
							<div class="form-group">
								<label class="control-label"><?= __('admin.user') ?></label>
								<select class="form-control" name="user_id">
									<option value=""><?= __('admin.filter_by_user') ?></option>
									<?php foreach ($users as $key => $value) { ?>
										<option <?= isset($user_id) && $user_id == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['username'] ?></option>	
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label class="control-label"><?= __('admin.date') ?></label>
								<input autocomplete="off" type="text" name="date" value="<?= isset($_GET['date']) ? $_GET['date'] : '' ?>" class="form-control daterange-picker">
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label class="control-label d-block">&nbsp;</label>
								<button class="btn btn-primary"><?= __('admin.filter') ?></button>
							</div>
						</div>

						<div class="col-sm-12">
							<div class="form-group text-right">
								<a href="<?= base_url('admincontrol/ask_again_withdrawal') ?>" type="button" class=" btn-sm btn btn-success "> <?= __('admin.ask_again_for_withdrawal') ?> </a>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="table-rep-plugin">
			    
			    <div class="text-center">
                    <?php if ($transaction ==null) {?>
						 <div class="d-flex justify-content-center align-items-center flex-column mt-5">
							 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
							 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
						 </div>
                    <?php }
                    else {?>
                         
                   <div class="table-responsive b-0" data-pattern="priority-columns">
                          <table id="tech-companies-1" class="table  table-striped">
						<thead>
							<tr>
								<th><input type="checkbox" class="select-all" ></th>
								<th>#</th>
								<th><?= __('admin.username') ?></th>
								<th><?= __('admin.order_total') ?></th>
								<th><?= __('admin.commission') ?></th>
								<th></th>
								<th><?= __('admin.date') ?></th>
								<th><?= __('admin.status') ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($transaction as $key => $value) { ?>
							<tr>
								<td><input type="checkbox" class="select-single" amount='<?= $value['amount'] ?>' value="<?= $value['id'] ?>" ></td>
								<td><?php echo $key + 1 ?></td>
								<td><?php echo $value['username'] ?></td>
								<td>
									<?php if($value['integration_orders_total']){ ?>
										<?= c_format($value['integration_orders_total']) ?>
									<?php } ?>
								</td>
								<td>
									<div class="dpopver-content d-none">
										<?php
											list($message,$ip_details) = parseMessage($value['comment'],$value,'admincontrol',true);
											echo "<div>". $message ."</div>";
										?>
									</div>
									<div 
										class="wallet-popover badge bg-<?= $value['amount'] >= 0 ? 'secondary' : 'danger' ?> py-1 pl-2 font-14" 
										toggle="popover"
									> 
										<?= c_format($value['amount']) ?> 
									</div>
								</td>
								<td><?= wallet_ex_type($value) ?></td>
								<td><?php echo $value['created_at'] ?></td>
								<td><?php echo $request_status[$value['status']] ?></td>
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


<script src="<?= base_url('assets/plugins/datatable') ?>/moment.js"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/datatable') ?>/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/datatable') ?>/daterangepicker.css?v=<?= av() ?>" />

<script type="text/javascript">

	$('.wallet-popover').on('click', function(){
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

	$('.daterange-picker').daterangepicker({
        opens: 'left',
        autoUpdateInput: false,
        ranges: {
            '<?= __('admin.today') ?>': [moment(), moment()],
            '<?= __('admin.yesterday') ?>': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            '<?= __('admin.last_7_days') ?>': [moment().subtract(6, 'days'), moment()],
            '<?= __('admin.last_30_days') ?>': [moment().subtract(29, 'days'), moment()],
            '<?= __('admin.this_month') ?>': [moment().startOf('month'), moment().endOf('month')],
            '<?= __('admin.last_month') ?>': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
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

	$('.change-status').on('click',function(){
		$this = $(this);
		var status = $this.attr("status");
		$.ajax({
			type:'POST',
			dataType:'json',
			data:{status:status,request_payment: $this.attr("data-id")},
			beforeSend:function(){$this.btn("loading")},
			complete:function(){$this.btn("reset")},
			success:function(json){
				$this.parents("tr").remove();
			},
		})
	})
	$('.accept-all').on('click',function(){
		$this = $(this);
		var status = $this.attr("status");

		if(status == '3') {
			var confirmText = '<?= __('admin.are_your_sure_to_accept_all_transaction') ?>';
		} else {
			var confirmText = '<?= __('admin.are_your_sure_to_reject_all_transaction') ?>';
		}

		if(!confirm(confirmText)) return false;
		$.ajax({
			type:'POST',
			dataType:'json',
			data:{status:status,request_payment_all: true},
			beforeSend:function(){$this.btn("loading")},
			complete:function(){$this.btn("reset")},
			success:function(json){
				window.location.reload();
			},
		})
	})

	$(".select-all").on('change',function(){
		$('.select-single').prop("checked", $(this).prop("checked"));
	})

	$(".select-single").on('change',function(){
		if($(".select-single:checked").length == 0){
			$(".show-on-select").hide();
		} else {
			$(".show-on-select").show();
		}
	})

	$('.selected-option').on('click',function(){
		$this = $(this);
		var status = $this.attr("status");
		var selected = $('.select-single:checked').on('map',function() {return this.value;}).get().join(',')

		if($(".select-single:checked").length > 0){
			if(status == '3') {
				var confirmText = '<?= __('admin.are_your_sure_to_accept_selected_transaction') ?>';
			} else {
				var confirmText = '<?= __('admin.are_your_sure_to_reject_selected_transaction') ?>';
			}
			if(!confirm(confirmText)) return false;

			$.ajax({
				type:'POST',
				dataType:'json',
				data:{status:status, selected_option: selected},
				beforeSend:function(){$this.btn("loading")},
				complete:function(){$this.btn("reset")},
				success:function(json){
					window.location.reload();
				},
			})
			
		} else {
			alert("<?= __('admin.select_any_checkbox') ?>");
		}
	})
</script>
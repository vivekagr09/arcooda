<?php if($saas_status){ ?>
	<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/datatable') ?>/daterangepicker.css?v=<?= av() ?>" />
	
	<div class="row">
		<div class="col-12">
		    <div class="card">
				<div class="card-header">
					<form method="GET" onsubmit="return deposit_filter()" id="deposit_filter">
						<input type="hidden" name="get_deposit" value="1">
						<div class="row">
							<div class="col-sm-3">
								<div class="form-group">
									<select class="form-control" name="user_id">
										<option value=""><?= __('admin.filter_by_vendor') ?></option>
										<?php foreach ($users as $key => $value) { ?>
											<option <?= isset($user_id) && $user_id == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['username'] ?></option>	
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<input autocomplete="off" type="text" name="date" value="<?= isset($_GET['date']) ? $_GET['date'] : '' ?>" class="form-control daterange-picker" placeholder='<?= __('admin.filter_by_date') ?>'>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<button class="btn btn-primary btn-new-filter"><?= __('admin.filter') ?></button>
								</div>
							</div>
						</div>
					</form>
					<strong class="text-primary pull-right"><?= __('admin.total_deposited_amount') ?>: <?php echo c_format($total_deposited); ?> </strong>
				</div>
				<div class="card-body p-0">
					<div class="new-empty text-center d-none">
						<div class="d-flex justify-content-center align-items-center flex-column mt-5">
						    <i class="fas fa-exchange-alt fa-5x text-muted"></i>
						    <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
						</div>
			    	</div>
					<div class="table-responsive deposit-datatable">
					    <table class="table transaction-table table-striped">
					        <thead class="bg-secondary text-white">
					            <tr>
					                <th width="100px"><?= __('admin.id') ?></th>
					                <th><?= __('admin.user') ?></th>
					                <th><?= __('admin.date') ?></th>
					                <th><?= __('admin.payment_method') ?></th>
					                <th><?= __('admin.transactions_ids') ?></th>
					                <th><?= __('admin.total') ?></th>
					                <th><?= __('admin.status') ?></th>
					                <th></th>
					            </tr>
					        </thead>
					        <tbody>
					        </tbody>
					    </table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="<?= base_url('assets/plugins/datatable') ?>/moment.js"></script>
	<script type="text/javascript" src="<?= base_url('assets/plugins/datatable') ?>/daterangepicker.min.js"></script>
	<script type="text/javascript">

		$( document ).ready(function() {
			deposit_filter();
			let last_pill = localStorage.getItem("last_pill");
			if(last_pill){ $('[href="'+ last_pill +'"]').click() }
		});

		function deposit_filter() {
			$.ajax({
				type:'POST',
				dataType:'json',
				data:$("#deposit_filter").serialize(),
				beforeSend:function(){
					$('.btn-new-filter').btn("loading");
				},
				complete:function(){
					$('.btn-new-filter').btn("reset");
				},
				success:function(json){
					if(json['html']){
						$(".deposit-datatable tbody").html(json['html']);
						$(".new-empty").addClass('d-none');
						$(".deposit-datatable").show();
					} else{
						$(".deposit-datatable").hide();
						$(".new-empty").removeClass('d-none');
					}
				},
			})

			return false;
		} 

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

		$(document).delegate(".btn-delete-deposit",'click',function(){
			$this = $(this);

			Swal.fire({
				title: '<?= __('admin.are_you_sure') ?>',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33'
			}).then((result) => {
				if(result.value){
					$.ajax({
						type:'POST',
						dataType:'json',
						data:{delete_request: true,id:$this.data("id")},
						success:function(json){
							Swal.fire({
								title: json.title,
								text: json.message,
								icon: json.type,
							})

							if(json.type == 'success')
								$this.parents("tr").remove();
						},
					})
				}
			})
		});

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

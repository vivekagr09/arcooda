<div class="card">
    <div class="card-header bg-secondary text-white">
        <form method="GET" onsubmit="return new_filter()" id="new_filter">
            <input type="hidden" name="get_new" value="1">
            <div class="row">
                <div class="col-12 mb-2">
                    <div class="card-title-white m-0"><?= __('admin.withdraw_requests_list') ?></div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
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
    </div>
</div>
<div class="card-body p-0">
<div class="new-empty text-center d-none">
    <i class="fas fa-exchange-alt fa-5x text-muted" style="margin: 20px auto;"></i>
    <h3 class="m-b-30 text-muted"><?= __('admin.no_data_found') ?></h3>
</div>

<div class="table-responsive new-datatable">
    <table class="table transaction-table table-striped">
        <thead class="bg-secondary text-white">
            <tr>
                <th><?= __('admin.id') ?></th>
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
<div class="modal fade" id="modal-confirm">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-confirmstatus">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-recursion">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="transIds" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= __('admin.transactions_ids') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-wrap"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
            </div>
        </div>
    </div>
</div>


<script src="<?= base_url('assets/plugins/datatable') ?>/moment.js"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/datatable') ?>/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/datatable') ?>/daterangepicker.css?v=<?= av() ?>" />
<script type="text/javascript">

	function new_filter() {
		$.ajax({
			type:'POST',
			dataType:'json',
			data:$("#new_filter").serialize(),
			beforeSend:function(){
				$('.btn-new-filter').btn("loading");
			},
			complete:function(){
				$('.btn-new-filter').btn("reset");
			},
			success:function(json){
				if(json['html']){
					$(".new-datatable tbody").html(json['html']);
					$(".new-empty").addClass('d-none');
					$(".new-datatable").show();
				} else{
					$(".new-datatable").hide();
					$(".new-empty").removeClass('d-none');
				}
			},
		})

		return false;
	} new_filter();


	function old_filter() {
		$.ajax({
			type:'POST',
			dataType:'json',
			data:$("#old_filter").serialize(),
			beforeSend:function(){
				$('.btn-old-filter').btn("loading");
			},
			complete:function(){
				$('.btn-old-filter').btn("reset");
			},
			success:function(json){
				if(json['html']){
					$(".old-datatable tbody").html(json['html']);
					$(".old-empty").addClass('d-none');
					$(".old-datatable").show();
				} else{
					$(".old-datatable").hide();
					$(".old-empty").removeClass('d-none');
				}
			},
		})

		return false;
	} old_filter();

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

    $(".transaction-table").delegate(".trans_ids","click",function(){
    	var trans_ids = $(this).data('trans_ids')
    	$("#transIds .modal-body p").text(trans_ids);
    	$("#transIds").modal('toggle');
    })

	$(document).delegate(".btn-deletes",'click',function(){
		$this = $(this);

		Swal.fire({
			title: '<?= __('admin.are_you_sure') ?>',
			text: '<?= __('admin.comission_will_revert_back_to_user_wallet') ?>',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: '<?= __('admin.yes_revert') ?>'
		}).then((result) => {
			if (result.value) {
				var ids = $(".wallet-checkbox:checked").map(function(){ return $(this).val() }).toArray();

				$this = $(this);
				$.ajax({
					type:'POST',
					dataType:'json',
					data:{delete_request: true,id:$this.data("id")},
					beforeSend:function(){ $this.btn("loading"); },
					complete:function(){ $this.btn("reset"); },
					success:function(json){
						if (json['error']) {
							Swal.fire("Error", json['error'], "error");
						}
						if (json['success']) {
							$this.parents("tr").remove();
							Swal.fire({
								title: '<?= __('admin.success') ?>',
								text: '<?= __('admin.comission_is_reverted_back_to_user_wallet') ?>',
								icon: 'success',
							}).then((result) => {
							})
						}
					},
				})
			}
		})
	});

</script>
<div class="row">
	<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h6 class="m-0"><?= __('user.withdraw_requests_list') ?></h6>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
  						<table class="table">
								<thead>
									<tr>
										<th><?= __('user.id') ?></th>
										<th><?= __('user.date') ?></th>
										<th><?= __('user.transactions_ids') ?></th>
										<th><?= __('user.total') ?></th>
										<th><?= __('user.status') ?></th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($lists as $key => $value) { ?>
										<tr>
											<td><?= $value['id'] ?></td>
											<td><?= dateFormat($value['created_at'],'d F Y') ?></td>
											<td><a class="trans_ids" href="javascript:void(0);" data-trans_ids="<?= $value['tran_ids'] ?>"><i class="fas fa-eye"></i></a></td>
											<td><?= c_format($value['total']) ?></td>
											<td><?= withdrwal_status($value['status']) ?></td>
											<td class="text-right">
												<a href="<?= base_url('usercontrol/wallet_requests_details/'. $value['id']) ?>" class="btn btn-primary btn-sm"><?= __('user.details') ?></a>
												
												<?php
												if($value['status'] != 1)
												{
													?>
													<button id='<?= $value['id'] ?>' class="btn btn-danger btn-sm btn-deletes"><?= __('user.delete') ?></button>
													<?php	
												}
												?>
												
											</td>
										</tr>
									<?php  } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
 </div>
</div>

<div class="modal fade" id="transIds"  tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?= __('user.transactions_ids') ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-wrap"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('user.close') ?></button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
	$(".transaction-table").delegate(".trans_ids","click",function(){
  	var trans_ids = $(this).data('trans_ids')
  	$("#transIds .modal-body p").text(trans_ids);
  	$("#transIds").modal('toggle');
  })

	$(document).delegate(".btn-deletes",'click',function(){
		$this = $(this);

		Swal.fire({
			title: '<?= __('user.are_you_sure') ?>',
			text: '<?= __('user.you_not_be_able_to_revert_this') ?>',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: '<?= __('user.yes_delete') ?>'
		}).then((result) => {
			if (result.value) {
				var ids = $(".wallet-checkbox:checked").map(function(){ return $(this).val() }).toArray();

				$this = $(this);
				$.ajax({
					type:'POST',
					dataType:'json',
					data:{delete_request: true,id:$this.attr("id")},
					beforeSend:function(){ $this.btn("loading"); },
					complete:function(){ $this.btn("reset"); },
					success:function(json){
						if (json['error']) {
							Swal.fire("Error", json['error'], "error");
						}
						if (json['success']) {
							$this.parents("tr").remove();
							Swal.fire({
								title: '<?= __('user.success') ?>',
								text: '<?= ('user.request_deleted_successfully_transactions_in_wallet') ?>',
								icon: 'success',
							}).then((result) => {
							})
						}
					},
				})
			}
		})
	})
</script>
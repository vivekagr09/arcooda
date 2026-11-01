<div class="card mt-3 mb-3">
	<div class="card-header">
		<h4 class="card-title pull-left"><?= __('admin.all_notification') ?></h4>
		<div class="pull-right">
			<a href="<?= base_url('admincontrol/notification?clearall=1') ?>" class="btn btn-sm clear_notification btn-danger"><?= __('admin.clear_notification') ?></a>
			<button class="btn btn-danger btn-sm delete-selected"><?= __('admin.delete_selected') ?></button>
		</div>
	</div>

	<div class="card-body">
        	<?php if($notifications == null) {?>
				<div class="text-center mt-5">
				 <div class="d-flex justify-content-center align-items-center flex-column mt-5">
					 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
					 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
				 </div>
				</div>
                <?php } else { ?>
			<div class="table-responsive">
				<table class="table table-hover">
					<thead>
						<tr>
							<td colspan="4">
								<div class="checkbox">
									<label>
										<input type="checkbox" value="" class="select_all">
										<?= __('admin.select_all') ?>
									</label>
								</div>
							</td>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($notifications as $key => $notification) { ?>
							<tr>
								<td width="50px">
									<div class="checkbox">
										<label><input type="checkbox" value="<?= $notification['notification_id'] ?>" name="notification[]" class="notification_id"></label>
									</div>
								</td>
								<td width="50px">
									<div class="round">
										<i class="mdi mdi-cart-outline"></i>
									</div>
								</td>
								<td>
		                        	<b><?php echo $notification['notification_title']; ?></b><br>
		                        		<small class="text-muted"><?php echo $notification['notification_description']; ?>
		                        	</small>
								</td>
								<td width="80px">
									<a class="btn btn-primary" href="javascript:void(0)" onclick="shownofication(<?php echo $notification['notification_id'] . ',\'' . base_url('admincontrol') . $notification['notification_url'] . '\''; ?>)" class="dropdown-item notify-item"><?= __('admin.details') ?></a>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		<?php } ?>
	</div>

	<div class="card-footer">
		<ul class="pagination justify-content-end">
			<?php echo $pagination ?>
		</ul>
	</div>
</div>

<script type="text/javascript">
	$('.clear_notification').on('click',function(){
		if(!confirm("<?= __('admin.delete_notifications_confirmation') ?>")) return false;

		return true;
	});


	$('.delete-selected').on('click',function(){
		var ids = [];
		if($('.notification_id:checked').length > 0){
			$('.notification_id:checked').each(function(){
				ids.push($(this).val());
			})

			$this = $(this);
			$.ajax({
				type:'POST',
				dataType:'json',
				data:{delete_ids:ids},
				beforeSend:function(){
					$this.prop("disabled", true);
				},
				complete:function(){
					$this.prop("disabled", false);

				},
				success:function(json){
					window.location.reload();
				},
			})
		} else {
			alert("<?= __('admin.select_notification') ?>");
		}

	})

	$('.select_all').on('click',function(){
		$('.notification_id').prop("checked", $(this).prop("checked") );
	})
</script>
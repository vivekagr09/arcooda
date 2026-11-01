</style>
<div class="row">
	<div class="col-12">
		<div class="card m-b-30">
			<div class="card-header">
				<div class="pull-right">
					<a href="<?= base_url('admincontrol/group_form/')  ?>" class="btn btn-primary add-new" id="<?= $lang['id'] ?>"><?= __("admin.add_new") ?></a>
				</div>
			</div>
			<div class="card-body">
				<div class="table-rep-plugin">
					<div class="table-responsive b-0" data-pattern="priority-columns">
						<table class="table">
							<thead>
								<tr>
									<th><?= __("admin.sn") ?></th>
									<th><?= __("admin.image") ?></th>
									<th><?= __("admin.group_name") ?></th>
									<th><?= __("admin.group_users_count") ?></th>
									<th><?= __("admin.group_ads_count") ?></th>
									<th><?= __("admin.description") ?></th>
									<th width="180px"><?= __("admin.is_default") ?></th>
									<th width="180px"><?= __("admin.action") ?></th>
								</tr>
							</thead>
							<tbody id="user-groups">
								<?php foreach($groups as $key=> $group){ ?>
									<tr>
										<td><?= (++$key) ?></td>
										<td>
											<?php $avatar = $group->avatar != '' ? 'site/'.$group->avatar : 'no_image_available.png' ; ?>
											<img src="<?php echo base_url();?>assets/images/<?php echo $avatar; ?>" id="blah" class="thumbnail" border="0" width="50px">
										</td>
										<td><?= $group->group_name ?></td>
										<td><?= $group->users_count ?></td>										
										<td><?= $group->tools_count ?></td>										
										<td><?=  wordwrap(substr($group->group_description, 0, 100),80,"<br>\n") ?><?= strlen($group->group_description) > 100 ? '....' : ''; ?></td>
										<td>
											<div class="form-check form-switch">
											<input class="form-check-input btn_lang_toggle" type="checkbox" <?= ($group->is_default == 1) ? "checked" : ""?> data-toggle="toggle" data-size="normal" data-on="<?= __('admin.status_on'); ?>" data-off="<?= __('admin.status_off'); ?>" data-lang_id="<?= $group->id ?>" data-column="is_default">
										</div>
										</td>
										<td>
										<a href="<?= base_url('admincontrol/group_form/'.$group->id)  ?>" class="btn btn-warning bg-warning text-dark" data-toggle="tooltip" data-original-title="<?= __('admin.update') ?>"><?= __('admin.update') ?></a>
										<button data-toggle="tooltip" data-original-title="<?= __("admin.delete") ?>" class="btn btn-danger detele-button" data-id="<?=$group->id?>"><?= __("admin.delete") ?></button>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div> 
	</div> 
</div>

<script type="text/javascript">

	$(document).on('change', ".btn_lang_toggle", function(){
		let skip_change = false;
		let id = $(this).data('lang_id');
		let column = $(this).data('column');
		let checked = $(this).prop('checked');

		if (checked == true) {
			var status = 1;
		}else{
			var status = 0;
		}

		$.ajax({
			url: "<?= base_url('admincontrol/group_status_toggle')?>",
			type: "POST",
			dataType: "json",
			data: {
				id:id,
				status:status,
				column:column
			},
			success: function (response) {	
				window.location.reload();
			}
		});
	});	

	$(".detele-button").on('click',function(){
		$('.tooltip').remove();
		$this = $(this);


		if(!confirm('<?= __('admin.are_you_sure') ?>')) {
			return false
		}
		
		
		$.ajax({
			url:'<?= base_url("admincontrol/delete_user_group") ?>',
			type:'POST',
			dataType:'json',
			data:{id:$this.attr("data-id")},
			beforeSend:function(){
				$this.prop("disabled",true);
			},
			complete:function(){
				$this.prop("disabled",false);
			},
			success:function(json){
				
				if(json.status==1)
				{
					window.location.reload();	
				}else{
					Swal.fire('Warning', json.message, 'warning');
				}
				
			},
		})
	});

	setTimeout(function(){ $('.alert-dismissable').remove(); }, 5000);

</script>
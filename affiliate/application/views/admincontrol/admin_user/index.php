<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
				<h5><?= __('admin.manage_admin')?></h5>
				<a id="toggle-uploader" class="btn btn-light" href="<?= base_url('admincontrol/admin_user_form/')  ?>"><?= __('admin.add_new_admin')?></a>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-hover table-striped">
						<thead>
							<tr>
								<th><?= __('admin.first_last')?></th>
								<th><?= __('admin.country')?></th>
								<th><?= __('admin.image')?></th>
								<th><?= __('admin.email')?></th>
								<th><?= __('admin.username')?></th>                                
								<th><?= __('admin.phone')?></th>
								<th><?= __('admin.action')?></th>
							</tr>
						</thead>
						<tbody>
							<?php if(empty($users)){ ?>
								<tr>
									<td colspan="7" class="text-center"><?= __('admin.empty_admin_list') ?></td>
								</tr>
							<?php } ?>
							<?php foreach ($users as $key => $user) { ?>
								<tr>
									<td><?= $user->firstname ?> <?= $user->lastname ?>
								</td>
									<td>
									    <?php
									        if ($user->Country != '') {
									            $flag = 'flags/' . strtolower($user->sortname) . '.png';
									        } else {
									            $flag = 'users/avatar-1.png';
									        }
									    ?>
									    <img class="rounded-circle img-popover" src="<?php echo base_url('assets/vertical/assets/images/'.$flag); ?>" style="width:30px;height: 30px" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-content="<img src='<?php echo base_url('assets/vertical/assets/images/'.$flag); ?>' width='200'>">
									</td>
									<td>
									    <?php $avatar = $user->avatar != '' ? 'users/' . $user->avatar : 'no-user_image.jpg' ; ?>
									    <img class="rounded-circle img-popover" src="<?php echo base_url();?>assets/images/<?php echo $avatar; ?>" style="width:30px;height: 30px" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-content="<img src='<?php echo base_url();?>assets/images/<?php echo $avatar; ?>' width='200'>">
									</td>
									<td><?= $user->email ?></td>
									<td><?= $user->username ?></td>
									<td><?= $user->PhoneNumber ?></td>
									<td>
										<a class="btn btn-primary btn-sm" href="<?= base_url('admincontrol/admin_user_form/'. $user->id) ?>">
										  <i class="bi bi-pencil"></i>
										</a>
										<a class="btn btn-danger btn-sm confirm" href="<?= base_url('admincontrol/admin_user_delete/'. $user->id) ?>">
										  <i class="bi bi-trash"></i>
										</a>
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

<script type="text/javascript">
	$(".confirm").on('click',function(){
	  if(!confirm('<?= __('admin.sure_delete') ?>')) return false;
	  return true;
	})
</script>


<script>
    var popoverTriggerList = [].slice.call(document.querySelectorAll('.img-popover'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    })
</script>
<?php $k=1; foreach($userslist as $users){  ?>

	<?php 
		$hasFiles = false;
		$valueStored = json_decode($users['value']); 
		$fieldsShown = [];
	?>

	<?php foreach ($data as $key => $value) { if($value['type'] == 'header') continue; ?>
	<?php 

	

	if( $value['type'] == 'file') {
		if(is_array($valueStored->{'custom_'.$value['name']})) {
			foreach ($valueStored->{'custom_'.$value['name']} as $fileName) {
				if(!empty($fileName)) {
					$hasFiles = true;
				}
			}
		} else {
			if(!empty($valueStored->{'custom_'.$value['name']})) {
				$hasFiles = true;
			}
		}

		if(isset($valueStored->{'custom_'.$value['name']})) {
			array_push($fieldsShown, 'custom_'.$value['name']);
		}
	}
	?>

	<?php } ?>

	<?php

	foreach ($valueStored as $key => $value) {
		if(!in_array($key, $fieldsShown)) {

			if(str_contains($key,'existing') || str_contains($key,'hidden') || !str_contains($key, 'custom')) continue;

			$filecheck = ['png', 'gif', 'jpeg', 'jpg', 'PNG', 'GIF', 'JPEG', 'JPG', 'ICO', 'ico', 'pdf', 'docx', 'doc', 'ppt', 'xls', 'txt'];

			if(is_array($value)) {
				foreach ($value as $v) {
					$v_explode = explode('.', $v);
					if(sizeof($v_explode) == 2 && in_array($v_explode[1], $filecheck)) {
						$hasFiles = true;
					}
				}
			} else {
				$v_explode = explode('.', $value);
				if(sizeof($v_explode) == 2 && in_array($v_explode[1], $filecheck)) {
					$hasFiles = true;
				}
			}
		}
	}


	?>

    <?php
        if(empty($users['amount'])){
            $users['amount'] = 0;
        }

        if(empty($users['click'])){
            $users['click'] = 0;
        }

        if(empty($users['af_click'])){
            $users['af_click'] = 0;
        }
    ?>

	<tr>

	    <td>
	    	<label class="checkbox-label">
				<input type="checkbox" class="wallet-checkbox" value="<?= $users['id'] ?>">

				<?= $users['id'] ?>		

			</label>

	    	<i class="fa fa-plus" aria-hidden="true" data-bs-toggle="collapse" data-bs-target="#demo1<?= $k; ?>" class="accordion-toggle"></i>

	    </td>

		<td>
			<?php echo $users['firstname'];?> <?php echo $users['lastname'];?>
			<br><small><?= $users['username'];?></small>
			<br><small><?= $users['email'];?></small>
		</td>

		<td>
			<?php
				if($award_level['status']){
					if($membership['status'] && $users['user_plan_comission_sale_status']){
						if($users['user_plan_level'])
							echo  $users['user_plan_level'];
						else 
							echo __('admin.default');
					} elseif($users['user_level']){
						echo $users['user_level'];
					} else {
						echo __('admin.default');
					}
				} else {
					echo __('admin.award_off');
				}
			?>
		</td>

		<td>
			<?php

				if(((int)$users['is_vendor'] == 1 && (int)$membership['status'] == 3) || ((int)$users['is_vendor'] != 1 && (int)$membership['status'] == 2)) {
					echo '<span class="px-2 py-1 rounded bg-light text-dark">'.__('admin.not_available').'</span>';
				} else if($membership['status']){
					$plan = false;

					if($users['reg_approved'] == 0) {
						echo '<i>'.__('admin.approval_pending').'</i>';
					} else if($users['reg_approved'] == 2) {
						echo '<i>'.__('admin.approval_declined').'</i>';
					} else {
						if($users['membership_plan']) {
							echo $users['membership_plan']."<br/>";	
							if((int)$users['membership_plan_id'] > 0){ 
								$plan = App\MembershipUser::find($users['membership_plan_id']);
								if($plan){
									echo "<small>".$plan->remainDay()."</small>"; 
								}
							} 
						} else {
							echo '<i>'.__('admin.plan_not_purchased').'</i>';						
						}
					}

					if($users['reg_approved'] != 0 && $users['reg_approved'] != 2){ ?>
						<div><a href="javascript:void(0)" edit-plan-user='<?= $users['id'] ?>' edit-plan-user-type='<?= $users['is_vendor'] ?>' ><?= __('admin.edit_plan') ?></a></div>
					<?php } else { ?>
						<div>
							<a href="javascript:void(0)" class="text-success" data-approval-change="1" data-user-id='<?= $users['id'] ?>'><?= __('admin.approved') ?></a> 
							<?php if($users['reg_approved'] != 2) { ?>
								/ <a href="javascript:void(0)" class="text-danger" data-approval-change="2" data-user-id='<?= $users['id'] ?>'><?= __('admin.decline') ?></a>
							<?php } ?>
						</div>
					<?php }
				} else {
					echo '<div>'.__('admin.membership_off').'</div>';
					$plan = false;

					if($users['reg_approved'] == 0) {
						echo '<i>'.__('admin.approval_pending').'</i>';
					} else if($users['reg_approved'] == 2) {
						echo '<i>'.__('admin.approval_declined').'</i>';
					}

					if($users['reg_approved'] != 0 && $users['reg_approved'] != 2){ ?>
						
					<?php } else { ?>
						<div>
							<a href="javascript:void(0)" class="text-success" data-approval-change="1" data-user-id='<?= $users['id'] ?>'><?= __('admin.approved') ?></a> 
							<?php if($users['reg_approved'] != 2) { ?>
								/ <a href="javascript:void(0)" class="text-danger" data-approval-change="2" data-user-id='<?= $users['id'] ?>'><?= __('admin.decline') ?></a>
							<?php } ?>
						</div>
					<?php }
				}
			?>
		</td>

		<td>
			<?php
				if ((!isset($plan) || empty($plan)) || ((int)$users['is_vendor'] == 1 && (int)$membership['status'] == 3) || ((int)$users['is_vendor'] != 1 && (int)$membership['status'] == 2)) {
					echo '<span class="bg-light text-dark px-2 py-1 rounded">'.__('admin.not_available').'</span>';
				} else {
					echo $plan->status_text;
				}  
			?>
		</td>

		<td class="text-center">

			<?php

				if ($users['Country'] != '') {

					$flag = 'flags/' . strtolower($users['sortname']) . '.png';

				} else {

					$flag = 'users/avatar-1.png';

				}

			?>

			<img class="rounded-circle" src="<?php echo base_url(); ?>assets/vertical/assets/images/<?php echo $flag; ?>" style="width:30px;height: 30px">

        </td>

        <td>
    		<?php 
    		$groups = []; 
    		foreach($users['groups_name'] as $grp) {
    			$groups[] = $grp['group_name'];
    		}
    		echo !empty($groups) ? implode(', ',$groups) : "<small class=\"text-muted\">".__('admin.no_group_assigned')."</small>";
    		?>
        </td>

		<td class="text-center">
			<?php if($users['is_vendor']){ ?>
				<i class="fa fa-check-square-o" style="font-size: 20px;color: green;"></i>
			<?php }else{ ?>
				<i class="fa fa-remove" style="font-size: 20px;color: red;"></i>
			<?php } ?>
		</td>

		<td>
			<?php echo (!empty($users['under_affiliate'])) ? $users['under_affiliate'] : __('admin.admin') ;?>
		</td>

		<td>

			<?php if($hasFiles) { ?>
				<i class="fa fa-paperclip mr-2" aria-hidden="true" data-bs-toggle="collapse" data-bs-target="#demo1<?= $k; ?>" class="accordion-toggle"></i>
			<?php } ?>

			<a data-toggle="tooltip" data-original-title="<?= __('admin.view_downline') ?>" href="<?= base_url('admincontrol/downline/'. $users['id']) ?>" class="btn btn-primary btn-sm"> <i class="fa fa-eye"></i> </a>

			<button data-toggle="tooltip" data-original-title="<?= __('admin.quick_view_downline') ?>" data-id="<?php echo $users['id'] ?>" class="btn show-tree btn-info btn-sm"> <i class="fa fa-sitemap"></i> </button>

			<a data-toggle="tooltip" data-original-title="<?= __('admin.edit') ?>" class="btn btn-sm btn-primary" onclick="return confirmpopup('<?php echo base_url();?>admincontrol/addusers/<?php echo $users['id'];?>');" href="javascript:void(0)">
				<i class="fa fa-edit cursors" aria-hidden="true"></i>
			</a>

			<button data-toggle="tooltip" data-original-title="<?= __('admin.payment_details') ?>" class="btn btn-sm btn-primary " payment_detail="<?php echo $users['id'] ?>"><i class="fa fa-info-circle" aria-hidden="true"></i></button>

			<button data-toggle="tooltip" data-original-title="<?= __('admin.delete') ?>" class="btn btn-sm btn-danger btn-delete2" data-id="<?php echo $users['id'] ?>">
				<i class="fa fa-trash-o cursors" aria-hidden="true"></i>
			</button>

			<?php if($users['status']){ ?>

				<a data-toggle="tooltip" data-original-title="<?= __('admin.disable_status') ?>" href="<?= base_url('admincontrol/u_status_toggle/' . $users["id"]) ?>" class="btn btn-remove btn-primary btn-sm"><i class="fa fa-lock"></i></a>

			<?php } else { ?>

				<a data-toggle="tooltip" data-original-title="<?= __('admin.enable_status') ?>" href="<?= base_url('admincontrol/u_status_toggle/' . $users["id"]) ?>" class="btn btn-remove btn-danger btn-sm"><i class="fa fa-unlock"></i></a>

			<?php } ?>

			<button data-toggle="tooltip" data-original-title="<?= __('admin.login') ?>" class="btn btn-sm btn-success btn-login-aff" data-id="<?php echo $users['id'] ?>"><i class="fa fa-sign-in" aria-hidden="true"></i></button>


		</td>

	</tr>

	<tr class="accordian-body collapse as" id="demo1<?= $k; ?>">

		<td colspan="100%" class="hiddenRow">

			<div class="card m-2 p-2">

			    <div class="row">

					<div class='col-sm-4 col-md-3'><b><?= __('admin.clicks') ?>:</b> <?php echo (int)$users['click'] + (int)$users['external_click'] + (int)$users['form_click']+ (int)$users['aff_click']; ?> / <?php echo c_format($users['click_commission']) ?></div>

					<div class='col-sm-4 col-md-3'><b><?= __('admin.action_click') ?>:</b> <?= (int)$users['external_action_click'] ?> / <?= c_format($users['action_click_commission']) ?></div>

					<div class='col-sm-4 col-md-3'><b><?= __('admin.sales_commissions') ?>:</b> <?php echo c_format($users['amount'] + $users['external_sale_amount']); ?> / <?php echo c_format($users['sale_commission']); ?></div>

					<div class='col-sm-4 col-md-3'><b><?= __('admin.paid_comm') ?>:</b> <?php echo c_format($users['paid_commition']); ?></div>

					<div class='col-sm-4 col-md-3'><b><?= __('admin.in_request') ?>:</b>	<?php echo c_format($users['in_request_commiton']); ?></div>

					<div class='col-sm-4 col-md-3'><b><?= __('admin.total') ?> <?= __('admin.commissions') ?>:</b> <?php echo c_format($users['all_commition']); ?></div>

					<?php $fieldsShown = [];  ?>

					<?php 
						$mobile_validation_done = false;

						foreach ($data as $key => $value) { 
						
						if($value['type'] == 'header') continue; 


							$mobile_validation = (isset($value['mobile_validation']) && $value['mobile_validation'] ) ? $value['mobile_validation'] : '';

							if($mobile_validation == 'true' && $mobile_validation_done == false) {
								$printableValue = $users['phone'];
								$mobile_validation_done = true;
							} else {
								$printableValue = isset($valueStored->{'custom_'.$value['name']}) ? $valueStored->{'custom_'.$value['name']} : null;
							}



						?>
						<div class='col-sm-4 col-md-3'>
							<b><?= $value['label'] ?>:</b> <?php 
							
							if( $value['type'] == 'file') {
								if(is_array($valueStored->{'custom_'.$value['name']})) {
									foreach ($valueStored->{'custom_'.$value['name']} as $fileName) {
										echo '<a target="_blank" href="'.base_url().'assets/user_upload/'.$fileName.'">'.$fileName.'</a>';
									}
								} else {
									echo '<a target="_blank" href="'.base_url().'assets/user_upload/'.$valueStored->{'custom_'.$value['name']}.'">'.$valueStored->{'custom_'.$value['name']}.'</a>';
								}

								if(!isset($valueStored->{'custom_'.$value['name']}) || (empty($valueStored->{'custom_'.$value['name']}) && $valueStored->{'custom_'.$value['name']} != 0)) {
									echo __('admin.no_files_uploaded');
								}
							} else {
								if(empty($printableValue) && $printableValue !== 0) {
									echo __('admin.not_available');
								} else {
									echo $printableValue; 
								}
							}
							?>
						</div>


					<?php 
					if(isset($valueStored->{'custom_'.$value['name']})) {
						array_push($fieldsShown, 'custom_'.$value['name']);
					}

				} 

				?>


				<?php

				foreach ($valueStored as $key => $value) {
					if(!in_array($key, $fieldsShown)) {

						if(str_contains($key,'existing') || str_contains($key,'hidden') || !str_contains($key, 'custom')) continue;

						echo "<div class='col-sm-4 col-md-3'><b>".explode('-', $key)[0].":</b>";

						$filecheck = ['png', 'gif', 'jpeg', 'jpg', 'PNG', 'GIF', 'JPEG', 'JPG', 'ICO', 'ico', 'pdf', 'docx', 'doc', 'ppt', 'xls', 'txt'];

						if(is_array($value)) {
							foreach ($value as $v) {

								$v_explode = explode('.', $v);

								if(sizeof($v_explode) == 2 && in_array($v_explode[1], $filecheck)) {
									echo '<a target="_blank" href="'.base_url().'assets/user_upload/'.$v.'">'.$v.'</a>';
								} else {
									echo $v;
								}

								
							}
						} else {
							$v_explode = explode('.', $value);

							if(sizeof($v_explode) == 2 && in_array($v_explode[1], $filecheck)) {
								echo '<a target="_blank" href="'.base_url().'assets/user_upload/'.$value.'">'.$value.'</a>';
							} else {
								echo $value;
							}
						}

						if(empty($value) && $value != 0) {
							echo __('admin.no_files_uploaded');
						}

						echo "</div>";
					}
				}


				?>

				</div>

			</div>

		</td>

	</tr>

<?php $k++; } ?>
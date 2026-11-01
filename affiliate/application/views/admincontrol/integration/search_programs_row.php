<?php foreach ($programs as $key => $program) { ?>
<tr>
<td><?= $program['id'] ?></td>
<td><?= $program['name'] ?></td>
<td><?= $program['username'] ? $program['username'] : 'Admin' ?></td>
<td>
	<?php 
		if($program['vendor_id']){
			echo __('admin.admin')." : ";
			if($program['admin_sale_status']){
				if($program['admin_commission_type'] == 'percentage'){ echo $program['admin_commission_sale'].'%'; }
				else if($program['admin_commission_type'] == 'fixed'){ echo c_format($program['admin_commission_sale']); }
				else { echo __('admin.not_set'); }
			} else{
				echo __('admin.not_set');
			}

			echo "<br>".__('admin.affiliate')." : ";
			if($program['sale_status']){
				if($program['commission_type'] == 'percentage'){ echo $program['commission_sale'].'%'; }
				else if($program['commission_type'] == 'fixed'){ echo c_format($program['commission_sale']); }
				else { echo __('admin.not_set'); }
			} else{
				echo __('admin.not_set');
			}
		} else{
			if($program['sale_status']){
				if($program['commission_type'] == 'percentage'){ echo $program['commission_sale'].'%'; }
				else if($program['commission_type'] == 'fixed'){ echo c_format($program['commission_sale']); }
				else { echo __('admin.not_set'); }
			} else{
				echo __('admin.not_set');
			}
		}
	?>
</td>
<td>
	<?php
		if($program['vendor_id']){
			echo __('admin.admin')." : ";
			if($program['admin_click_status']){
				if($program["admin_commission_click_commission"] && $program['admin_commission_number_of_click']){
					echo c_format($program["admin_commission_click_commission"]). " ".__('admin.per')." ". $program['admin_commission_number_of_click'] ." ".__('admin.clicks');
				} else { echo __('admin.not_set'); }
			} else{
				echo __('admin.not_set');
			}

			echo "<br>".__('admin.affiliate')." : ";
			if($program['click_status']){
				echo c_format($program["commission_click_commission"]). " ".__('admin.per')." ". $program['commission_number_of_click'] ." ".__('admin.clicks');
			} else{
				echo __('admin.not_set');
			}
		} else{
			if($program['click_status']){
				echo c_format($program["commission_click_commission"]). " ".__('admin.per')." ". $program['commission_number_of_click'] ." ".__('admin.clicks');
			} else{
				echo __('admin.not_set');
			}
		}
	?>
</td>
<td>
	<?php
		if($program['vendor_id']){
			echo __('admin.admin')." : ". ($program['admin_sale_status'] ? __('admin.enable') : __('admin.disable'));
			echo "<br> ".__('admin.affiliate')." : ". ($program['sale_status'] ? __('admin.enable') : __('admin.disable'));
		} else {
			echo (int)$program['sale_status'] ? __('admin.enable') : __('admin.disable');
		}
	?>
<td>
	<?php
		if($program['vendor_id']){
			echo __('admin.admin')." : ". ($program['admin_click_status'] ? __('admin.enable') : __('admin.disable'));
			echo "<br> ".__('admin.affiliate')." : ". ($program['click_status'] ? __('admin.enable') : __('admin.disable'));
		} else {
			echo (int)$program['click_status'] ? __('admin.enable') : __('admin.disable');
		}
	?>	
</td>
<td><?= program_status($program['status']) ?></td>
<td>
	<a class="btn btn-primary btn-sm" href="<?= base_url('integration/programs_form/'. $program['id']) ?>"><?= __('admin.edit') ?></a>
	<button <?= $program['associate_programns'] ? __('admin.disabled') : '' ?> class="btn btn-danger btn-sm delete-program" data-id="<?= $program['id'] ?>"><?= __('admin.delete') ?></button>
</td>
</tr>
<?php } ?>
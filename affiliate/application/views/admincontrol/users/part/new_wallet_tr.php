<tr class="transaction-table-tr <?= ($class == 'child' || $class == 'child-recurring') ? 'child-row' : '' ?> wallet-id-<?= $value['id'] ?> <?= $recurring ? 'recurring recurringof-'.$recurring : '' ?>" group_id='<?= $value['group_id'] ?>' data-id="<?= $value['id'] ?>">
	

	<?php if($recurring){ ?>
		<td class="escape-middle">
			<div class="checkbox-td">
				<label>
					<input type="checkbox" class="wallet-checkbox" value="<?= $value['id'] ?>">
				</label>
			</div>
		</td>
		<td class="text-center p-relative child-arrow-rec escape-middle">
		</td>
	<?php } else { ?>
		<td class="escape-middle">
			<div class="checkbox-td">
				<label>
					<input type="checkbox" class="wallet-checkbox" value="<?= $value['id'] ?>">
				</label>
			</div>
		</td>

		<td class="escape-middle text-center p-relative <?= $force_class ?> <?= $class == 'child' ? 'child-arrow' : '' ?>">
			<?php if($has_child && $class != 'child'){ ?>
				<button class="show-child-transaction"><i class="fa fa-angle-down"></i></button>
				<div class="button-line"></div>
			<?php } ?>
		</td>

	<?php }  ?>

<!--Transaction Date td-->
<td>
    <div class="d-flex justify-content-between align-items-center">
        <div class="no-wrap">
            <span class="badge bg-secondary fs-6"><?= __('admin.id') ?>: <?= $value['id'] ?></span>
        </div>
    </div>
</td>
<!--Transaction Date td-->

<!--Transaction Date td-->
<td>
    <div class="d-flex justify-content-between align-items-center">
        <div class="no-wrap">
            <span class="font-weight-bold"><?= dateFormat($value['created_at'],'d F Y') ?></span>
        </div>
    </div>
</td>
<!--Transaction Date td-->


<!--CampaignOwner td-->
<td>
    <div class="no-wrap">
        <?php if($value['is_vendor']) { ?>
            <small class="badge bg-secondary text-white fs-6 rounded-pill">
                <i class="bi bi-cart4 me-2"></i>
                <?= __('admin.user_vendor') ?>
            </small>
        <?php } else { ?>
            <small class="badge bg-white border border-secondary text-secondary fs-6 rounded-pill">
                <i class="bi bi-person-circle me-2"></i>
                <?= __('admin.user_admin') ?>
            </small>
        <?php } ?>
    </div>
</td>
<!--CampaignOwner td-->


<!--User td-->
<td>
	<div>
		<small class="badge bg-secondary fs-6"><?php echo $value['username']; ?></small>
	</div>
</td>
<!--User td-->


<!--Campaign td-->
<td>
    <div class="badge bg-secondary payment-method fs-6" style="display: inline-flex; align-items: center;">
        <?= $order_type = wallet_ex_type($value, $class) ?>

        <?php if (!$order_type) { ?>
            <?= wallet_type($value) ?>
        <?php } ?>
        
        <?php if (!$value['parent_id'] && $class != "child") {
            if (in_array($value['type'], ['sale_commission', 'admin_sale_commission', 'vendor_sale_commission']) && in_array($value['comm_from'], ['store', 'ex']) && !empty($value['reference_id_2'])) { ?>
                <div class="me-1">:</div>
                
                <?php if ($value['integration_orders_total']) { ?>
                    <span class="me-1"><?= c_format($value['integration_orders_total']) ?></span>
                <?php } ?>
                <?php if ($value['local_orders_total']) { ?>
                    <span class="me-1"><?= c_format($value['local_orders_total']) ?></span>
                <?php } ?>
                <?php if ($value['payment_method']) { ?>
                    <span class="me-1"><?= payment_method($value['payment_method']) ?></span>
                <?php } ?>
                
				<button class="hover-info wallet-btn view-tran-details ms-1" style="padding-left: 5px; padding-right: 5px; background: none; border: none;" data-ref_id_1="<?= $value['reference_id'] ?>" data-ref_id_2="<?= $value['reference_id_2'] ?>" data-comm_from="<?= $value['comm_from'] ?>">
				    <span class="bg-primary p-1 rounded-circle">
				        <i class="bi bi-info-circle fs-6 text-white"></i>
				    </span>
				</button>

            <?php }
        } ?>
    </div>
</td>
<!--Campaign td-->


<!-- Commission td -->
<td>
    <div class="badge d-flex justify-content-between bg-<?= is_need_to_pay($value) ? 'danger' : 'secondary' ?> text-white py-1 px-3 align-items-center">
        <div class="left-side d-flex align-items-center">
            <span class="me-1 fs-6"><?= c_format($value['amount']) ?></span>
        </div>

        <div class="right-side d-flex align-items-center">
            <?php
                list($message, $ip_details) = parseMessage($value['comment'], $value, 'admincontrol', true);
            ?>
            <button data-bs-toggle="popover" data-bs-html="true" title="" data-bs-content="<?= htmlspecialchars($message, ENT_QUOTES); ?>" class="btn px-1 py-0 me-1" style="background: none; border: none; color: white;">
                <i class="bi bi-info-circle fs-6"></i>
            </button>

            <div class="dropdown d-inline ms-1">
                <a href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color: white;">
                    <i class="bi bi-three-dots-vertical fs-6"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <?php $ip_details2 = json_decode($value['ip_details'], true); ?>
                    <?php foreach ($ip_details2 as $ip2) { ?>
                        <a class="dropdown-item" href="#">
                            <i class="me-2 flag-sm flag-sm-<?= strtoupper($ip2['country_code']) ?>"></i>
                            <?= $ip2['country_code'] ?> <?= (!empty($ip2['ip'])) ? $ip2['ip'] : '<i>' . __('admin.ip_not_available') . '</i>' ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</td>
<!-- Commission td -->



<?php $wallet_type_action = wallet_type($value, 'code'); ?>

<td>
	<!-- <div class="transaction-type"><?= $wallet_type_action; ?>
	</div> -->
	<div>
		<small><?= wallet_whos_commission($value) ?></small>
	</div>
</td>




<!-- Paid td -->
<td>
	<?php
		if($value['user_id'] == 1) {
			echo '<span class="badge bg-success text-light px-3 py-2 fs-6">'.__('admin.paid').'</span>';
		
		} else {
			if(isset($req_query) && sizeof($req_query) > 0)
			{
				echo withdrwal_status($req_query['status']);
			}
			else
			{
				echo wallet_paid_status($value['status']);
			}
		}
	?>
</td>
<!-- Paid td -->

<!-- Status td -->
<td>
	<?php
	if($value['user_id'] == 1 && $value['status'] == 1)
		$value['status'] = 3;

	if(!isset($hideStaticStatus)) { 
		$id = (!empty($child_id) && $value['amount'] < 0) ? $child_id : $value['id'];

		$req_query = $this->db->query("SELECT * from wallet_requests WHERE FIND_IN_SET($id,tran_ids)");
		$req_query = $req_query->row_array();

		if($value['amount'] < 0 && ! sizeof($req_query) > 0) {
			$goups_res = $this->db->query("SELECT id from wallet WHERE group_id=".$value['group_id']."")->result();
			foreach ($goups_res as $res) {
				$req_query = $this->db->query("SELECT * from wallet_requests WHERE FIND_IN_SET(".$res->id.",tran_ids)");
				$req_query = $req_query->row_array();
				if(sizeof($req_query) > 0) {break;}
			}
		}

		if($req_query['status'] != ''){
			$fixed_status = array(2,3,4,5,7,8,9,10,11,12,13);

			if(in_array(intval($req_query['status']), $fixed_status, TRUE)){
				echo withdrwal_status($req_query['status']);
			} else {
				if($value['commission_status'] == 0)
			 		echo $status_icon[$value['status']];
			}	
		} else {
			if($value['commission_status'] == 0)
		 		echo $status_icon[$value['status']];
		}
	 
		echo commission_status($value['commission_status']);
	 } ?>
</td>
<!-- Status td -->


<!-- Actions td -->
<td>
	<select id="tran-<?=$value['id']?>" class="form-control change-status" 
		onchange="changeStatus(this,'<?=$value['id']?>','<?= $value['status'] ?>');" >

		<option value="" disabled selected><?= __('admin.select_status') ?></option>
		
		<option <?= ($req_query['status'] == 1) ? "disabled" : "" ?>
				value="1" data-type="comission"><?= __('admin.cancel') ?></option>

		<option <?= ($req_query['status'] == 1) ? "disabled" : "" ?>  
				value="2" data-type="comission"><?= __('admin.trash') ?></option>

		<option value="0" data-type="wallet"><?= __('admin.on_hold') ?></option>

		<option value="1" data-type="wallet"><?= __('admin.in_wallet') ?></option>

		<option value="" data-type="remove"><?= __('admin.remove') ?></option>

		<?php if(!$value['parent_id'] && $class != 'child' && $value['is_vendor'] == 0): ?>
			<option value="" data-type="recursion"><?= __('admin.recursion') ?></option>
		<?php endif ?>		
	</select>
</td>
<!-- Actions td -->

<!-- Automation td -->
<td>
	<div class="text-center actions no-wrap">
		<?php if($value['wallet_recursion_id']){ ?>
            <?php if($class != "child"){  ?>
				<button type="button" class="wallet-btn fs-5" title="<?= cycle_details($value['total_recurring'], $value['wallet_recursion_next_transaction'], $value['wallet_recursion_endtime'], $value['total_recurring_amount']) ?>" data-bs-toggle="tooltip" data-id="<?= $value['id'] ?>">
				  <i class="bi bi-gear-fill"></i>
				</button>
			<?php } ?>

		<?php } ?>
		
		 <?php if($value['has_recursion_records'] > 0) { ?>
			<button data-bs-toggle="tooltip" title="<?= __('admin.show_recurring_transition') ?>" class="wallet-btn show-recurring-transition" data-id="<?= $value['id'] ?>" style="color:#EB940D !important">
				<span class="plus fs-5">
					<i class="bi bi-plus-circle-fill"></i>
				</span>
				<span class="minus fs-5">
					<i class="bi bi-dash-circle-fill"></i>
				</span>
			</button>
	    <?php } ?>
	</div>
</td>
<!-- Automation td -->
</tr>
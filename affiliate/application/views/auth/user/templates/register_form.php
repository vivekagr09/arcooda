<?php
$is_vendor = isset($user) ? (int)$user['is_vendor'] : 0;
$is_vendor_registration = (isset($is_vendor_registration) && $is_vendor_registration) ? 1 : $is_vendor;
?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/plugins/datetimepicker/jquery.datetimepicker.min.css') ?>" />

<script src="<?php echo base_url('assets/plugins/datetimepicker/jquery.datetimepicker.full.min.js') ?>" type="text/javascript"></script>


<?php if(!isset($edit_view) && $registration_status != 0){ ?>
<div class="card">
    <div class="list-group list-group-horizontal" id="list-tab" role="tablist">
        <?php if($registration_status == 1 || $registration_status == 3): ?>
            <a class="list-group-item list-group-item-action <?= (isset($is_vendor_registration) && $is_vendor_registration) ? "" : "active"; ?>" href="<?php echo base_url('register') ?>" role="tab" data-registartion_type="aff"><?= __('front.affiliate') ?></a>
        <?php endif ?>
        
        <?php if((int)$vendor_storestatus!=0 || (int)$vendor_marketstatus!=0 ) { 
            if($registration_status == 1 || $registration_status == 2): ?>
                <a class="list-group-item list-group-item-action <?= ((isset($is_vendor_registration) && $is_vendor_registration) || $registration_status == 2) ? "active" : ""; ?>" href="<?php echo base_url('register/vendor') ?>" role="tab" data-registartion_type="ven"><?= __('front.vendor') ?></a>
            <?php endif ?>
        <?php } ?>
    </div>
</div>
<?php } ?>


<form action="" method="POST" role="form" class="reg_form p-3" novalidate="" enctype='multipart/form-data'>
	<div id="preventAutoLoad" style="position: fixed; top: -100%;">
 	 <input type="password" autocomplete="password" />
	 <input type="text" autocomplete="username" />
    </div>

	<input type="hidden" name="is_vendor" value="<?= (isset($is_vendor_registration) && $is_vendor_registration) ? 1 : 0; ?>">
	<input type="hidden" name="affiliate_cookie" id="affiliate_cookie" value="-1">
	
	<script type="text/javascript">
		var tel_input = false;
		var grecaptcha = undefined;
	</script>
	
	<?php
		$db =& get_instance(); 
		$products = $db->Product_model; 
	    $googlerecaptcha =$db->Product_model->getSettings('googlerecaptcha');	


		$fields = array();
		$email = isset($user) ? $user['email'] : '';
		$fields['email'] = '<div class="form-group mb-2">
			<input type="email" id="email" name="email" placeholder="'. __('user.email') .'" class="form-control custom_input" value="'. $email .'">  
		</div>';

if(isset($read_only_user_membership_plan)){
    if($membership['status']){
        if($userPlan['name']) {
            $user_membership_plan_value = $userPlan['name'];
        } else {
            $user_membership_plan_value = 'Not available';
        }
    } else {
        $user_membership_plan_value = 'Off'; 
    }

    $fields['user_membership_plan'] = '
    <div class="form-group mb-2">
        <label for="user_membership_plan">'. __('admin.user_membership_plan') .'</label>
        <input readonly type="text" class="form-control custom_input" id="user_membership_plan" value="'.$user_membership_plan_value.'">
    </div>';
}

if (isset($allow_vendor_option)) {
    $is_vendor = isset($user) ? (int)$user['is_vendor'] : 0;
    $fields['is_vendor'] = '
    <div class="form-group mb-2">
        <div class="row gx-5">
            <div class="col-auto">
                <label class="form-label">'. __('admin.vendorstatus') .'</label>
            </div>
            <div class="col-auto">
                <div class="form-check form-switch">
                    <input name="is_vendor" class="form-check-input update_all_settings" type="checkbox" '. ($is_vendor == 1 ? 'checked' : '') .'>
                </div>
            </div>
        </div>
    </div>';
}


$firstname = isset($user) ? $user['firstname'] : '';
$fields['firstname'] = '
<div class="form-group mb-2">
    <input type="text" name="firstname" id="firstname" class="form-control custom_input" placeholder="'. __('user.first_name') .'" value="'. $firstname .'" >
</div>';


$lastname = isset($user) ? $user['lastname'] : '';
$fields['lastname'] = '
<div class="form-group mb-2">
    <input type="text" name="lastname" id="lastname" class="form-control custom_input" placeholder="'. __('user.last_name') .'" value="'. $lastname .'">
</div>';


$username = isset($user) ? $user['username'] : '';
$disabled_username = isset($disable_username) ? 'disabled' : '';

$fields['username'] = '
<div class="form-group mb-2">
    <input type="text" name="username" id="username" class="form-control custom_input" placeholder="'. __('user.username') .'" value="'. $username .'" '.$disabled_username.'>
</div>';

$fields['password'] = '
<div class="form-group mb-2">
    <input type="password" name="password" id="password" placeholder="'. __('user.password') .'" class="form-control custom_input">
</div>';

$fields['confirm_password'] = '
<div class="form-group mb-2">
    <input type="password" name="cpassword" id="cpassword" placeholder="'. __('user.repeat_password') .'" class="form-control custom_input">
</div>';


	$customValue = json_decode(isset($user['value']) ? $user['value'] : '[]', 1);

	$systemPhoneInput = false;
?>
	
	<?php foreach ($data as $key => $value) { 

		if((!isset($edit_view) || !$edit_view) && isset($value['hide_on_registration']) && $value['hide_on_registration']) continue;
		  
		$required    = (isset($value['required']) && $value['required'] == 'true') ? 'required="required"' : '';
		$label       = (isset($value['label']) && $value['label'] ) ? $value['label'] : '';
		$placeholder = (isset($value['placeholder']) && $value['placeholder'] ) ? $value['placeholder'] : $value['label'];
		$className   = (isset($value['className']) && $value['className'] ) ? $value['className'] : '';
		$name        = 'custom_'.((isset($value['name']) && $value['name'] ) ? $value['name'] : '');
		$ivalue      = (isset($value['value']) && $value['value'] ) ? $value['value'] : (isset($customValue[$name]) ? $customValue[$name] : '');
		$maxlength   = (isset($value['maxlength']) && $value['maxlength'] ) ? $value['maxlength'] : '';
		$min         = (isset($value['min']) && $value['min'] ) ? $value['min'] : '';
		$max         = (isset($value['max']) && $value['max'] ) ? $value['max'] : '';
		$mobile_validation         = (isset($value['mobile_validation']) && $value['mobile_validation'] ) ? $value['mobile_validation'] : '';
		$multiple_files         = (isset($value['multiple']) && $value['multiple'] ) ? 'multiple' : '';
		$_customValue = $ivalue;

		switch ($value['type']) {
			case 'header': 
				echo  $fields[strtolower($label)]; 
				if($label == 'Email' && isset($read_only_user_membership_plan)){
					echo  $fields['user_membership_plan']; 
				}
				if($label == 'Email' && isset($allow_vendor_option)){
					echo  $fields['is_vendor']; 
				}
			break;
			case 'text':
				if($mobile_validation == 'true'){ ?>
					<link rel="stylesheet" href="<?= base_url('assets/plugins/tel/css/intlTelInput.css') ?>?v=<?= av() ?>">
					<script src="<?= base_url('assets/plugins/tel/js/intlTelInput.js') ?>"></script>

					<?php if($systemPhoneInput === false) { ?>
						<div class="form-group mb-2">
							<div>
								 <input type="hidden" name='PhoneNumberInput' id="phonenumber-input" value="" class="form-control" placeholder="<?= __('store.phone_number') ?>">

								<input onkeypress="return isNumberKey(event);" id="phone_<?= $key ?>" class="form-control custom_input tel_input" name="phone" type="text" value="<?= $user['phone'] ?>" <?= $required ?>>
							</div>
						</div>

					<?php $systemPhoneInput = true;
						} else {
				    ?>
					<div class="form-group">
					    <div>
					        <input onkeypress="return isNumberKey(event);" id="phone_<?= $key ?>" class="form-control custom_input tel_input <?= $className ?>" placeholder="<?= $placeholder ?>"  name="<?= $name ?>" type="text" value="<?= $ivalue ?>" <?= $required ?>>
					    </div>
					</div>
					<?php } ?>
					

		 			<script type="text/javascript">
					$( document ).ready(function() {

						window.tel_inputphone_<?= $key ?> = intlTelInput(document.querySelector("#phone_<?= $key ?>"), {
						  initialCountry: "auto",
						  utilsScript: "<?= base_url('/assets/plugins/tel/js/utils.js?1562189064761') ?>",
						  separateDialCode:true,
						  geoIpLookup: function(success, failure) {
						    $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
						      var countryCode = (resp && resp.country) ? resp.country : "US";
						         success(countryCode);
						    });
						  },
						});
					});

					</script>

				<?php } else { ?>
					<div class="form-group">
						<input type="text" name='<?= $name ?>' id="<?= $name ?>" value="<?= $ivalue ?>" class="<?= $className ?>" placeholder="<?= $placeholder ?>" <?= $required ?> maxlength = '<?= $maxlength ?>' >
					</div>
				<?php }
				break;
			case 'autocomplete': ?>
				<div class="form-group">
					<input type="text" name='<?= $name ?>' id="<?= $name ?>" value="<?= $ivalue ?>" class="<?= $className ?> autocomplete" placeholder="<?= $placeholder ?>" <?= $required ?> maxlength = '<?= $maxlength ?>' >
				</div>
			<?php
			break;			
			case 'number': ?>
				<div class="form-group">
					<input type="number" name="<?= $name ?>" id="<?= $name ?>" class="<?= $className ?>" value="<?= $ivalue ?>" min="<?= $min ?>" max="<?= $max ?>"  <?= $required ?> placeholder="<?= $label ?>">
				</div>
			<?php
			break;
			case 'hidden': ?>
					<input type="hidden" name="<?= $name ?>" id="<?= $name ?>" class="<?= $className ?>" value="<?= $ivalue ?>" placeholder="<?= $label ?>">
			<?php
			break;
			case 'paragraph': ?>
			<div class="form-group">
				<textarea name="<?= $name ?>" id="<?= $name ?>" class="form-control <?= $className ?>" rows="3" <?= $required ?> maxlength = '<?= $maxlength ?>' placeholder="<?= $label ?>"><?= $ivalue ?></textarea>
			</div>
			<?php
			break;
			case 'textarea': ?>
			<div class="form-group">
				<textarea name="<?= $name ?>" id="<?= $name ?>" class="<?= $className ?>" rows="3" <?= $required ?> maxlength = '<?= $maxlength ?>' placeholder="<?= $label ?>"><?= $ivalue ?></textarea>
			</div>
			<?php
			break;
			case 'date': ?>
			 <div class="form-group">
			        <div class="input-group date" data-provide="datepicker">
					    <input type="text" class="form-control custom_input <?= $className ?> datetimepicker" name="<?= $name ?>" value="<?= $ivalue ?>" placeholder="<?= $placeholder ?>" <?= $required ?>>
					    <div class="input-group-addon">
					        <span class="glyphicon glyphicon-th"></span>
					    </div>
					</div>
	          </div>
			<?php
			break;
			case 'checkbox-group':
			if(isset($value['values'])){

				echo '<div id="'.$name.'>" class="form-group text-left"><label>'.$label.'</label><br/>';
				foreach ($value['values'] as $k => $v) {
					$label = (isset($v['label']) && $v['label'] ) ? $v['label'] : '';
					$ivalue = (isset($v['value']) && $v['value'] ) ? $v['value'] : '';
					$checked = '';
					if(isset($edit_view) && in_array($ivalue, $_customValue)) {
						$checked = "checked='checked'";
					} else if( !isset($edit_view) && isset($v['checked']) && $v['checked']){
						$checked = "checked='checked'";
					}
                ?>
    			<div class="checkbox mr-2" style="display:inline-block;">
    		        <label>
    		          <input type="checkbox" name="<?= $name ?>[]" value="<?=$ivalue;?>" class="<?= $className ?>" <?= $checked ?>>
    		          <span class="box"></span>
    		          <?= $label; ?>
    		        </label>
    	      	</div>
			<?php } ?>
			</div>
			<?php } 
			break;
			case 'radio-group':
			if(isset($value['values'])){
				echo '<div class="form-group text-left" id="'.$name.'"><label>'.$label.'</label><br/>';
				foreach ($value['values'] as $k => $v) {
					$label = (isset($v['label']) && $v['label'] ) ? $v['label'] : '';
					$ivalue = (isset($v['value']) && $v['value'] ) ? $v['value'] : '';
					$checked = '';
					if(isset($edit_view) && $_customValue == $ivalue) {
						$checked = "checked='checked'";
					} else if( !isset($edit_view) && isset($v['checked']) && $v['checked']){
						$checked = "checked='checked'";
					}
			 ?>
				<!-- <div class="radio pl-2" style="display: inline-block;">
					<label>
						<input type="radio" name="<?= $name ?>" value="<?= $ivalue ?>" <?= $checked ?> class="<?= $className ?>">
						<?= $label ?>
					</label>
				</div> -->

				  <label class="radio-inline mr-2">
					  <input type="radio" name="<?= $name ?>" value="<?= $ivalue ?>" <?= $checked ?>><span class="indicator"></span> <?= $label ?>
					</label>
			<?php } ?>
			</div>
			<?php } 
			break;
			case 'select':
			if(isset($value['values'])){ ?>
				<div class="form-group">
				 	<select name="<?= $name ?>" id="<?= $name ?>" class="form-control custom_input <?= $class ?>">
				 		<option><?= $label ?></option>
				 		<?php 
				 
				 			foreach ($value['values'] as $k => $v) {
							$label = (isset($v['label']) && $v['label'] ) ? $v['label'] : '';
							$ivalue = (isset($v['value']) && $v['value'] ) ? $v['value'] : '';
							$selected = '';
							if(isset($edit_view) && $_customValue == $ivalue) {
								$selected = "selected='selected'";
							} else if( !isset($edit_view) && isset($v['selected']) && $v['selected']){
								$selected = "selected='selected'";
							}
				 		?>
				 		<option value="<?= $ivalue ?>" <?= $selected ?>><?= $label ?></option>
						<?php } ?>
				 	</select>
				</div>
			<?php } 
			break;
			case 'file':
				?>
				<div class="form-group">
					<label class="text-left d-block"><?= $label ?></label>
					<input type="file" name='<?= $name ?><?= ($multiple_files != '') ? '[]' : ''; ?>' id="<?= $name ?>" class="<?= $className ?>" <?= $multiple_files; ?>/>
				</div>
				<?php if(isset($edit_view) && !empty($ivalue)){

					if(is_array($ivalue)) {
						?>
						<ul class="list-group list-group-flush mb-4">
							<?php foreach ($ivalue as $v) { 
								if(!empty($v)) {
								?>
							<li class="list-group-item d-flex justify-content-between align-items-center">
							    <a target="_blank" href="<?= base_url(); ?>assets/user_upload/<?= $v; ?>"><?= $v; ?></a>
							    <span class="badge bg-danger badge-pill" style="cursor: pointer;" onclick="return this.parentNode.remove();">Delete</span>
							    <input type="hidden" name="existing_<?= $name ?>[]" value="<?= $v; ?>"/>
						  	</li>
							<?php }
							} ?>
						</ul>
						<?php
					} else {
						?>
						<ul class="list-group list-group-flush mb-4">
							<li class="list-group-item d-flex justify-content-between align-items-center">
							    <a target="_blank" href="<?= base_url(); ?>assets/user_upload/<?= $ivalue; ?>"><?= $ivalue; ?></a>
							    <span class="badge bg-danger badge-pill" style="cursor: pointer;" onclick="return this.parentNode.remove();">Delete</span>
    						  	<input type="hidden" name="existing_<?= $name ?>" value="<?= $ivalue; ?>"/>
						  	</li>
						</ul>
						<?php
					}

				} ?>


				<?php
			break;
			default:
				echo $value['type'];
				break;
		} ?>
	<?php } ?>

	<?php if($vendor_storestatus) { ?>
    <div class="form-group store_fields" <?= (isset($is_vendor_registration) && $is_vendor_registration) ? "" : "style=\"display: none;\""; ?>>
        <input type="text" id="store_name" name="store_name" placeholder="<?= __('user.your_store_name') ?>" class="form-control custom_input" value="<?= (isset($user) && !empty($user['store_name'])) ? $user['store_name'] : '' ?>">  
    </div>
	<?php } ?>

	<?php if(isset($edit_view_refer)){ ?>
		<div class="form-group">
			<label class="control-label"><?= __('admin.Under_Affiliate') ?></label>
			<select class="form-control custom_input" name="refid">
				<option value="0"> <?= __('admin.none') ?> </option>
				<?php foreach ($refer_users as $key => $value) { ?>
					<option <?= (isset($user) && $user['refid'] == $value['id']) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['username'] ?></option>
				<?php } ?>
			</select>
		</div>
	<?php } ?>
	
	<?php if(isset($edit_view_level)){ ?>
		<div class="form-group mb-2">
			<label class="control-label"><?= __('admin.user_level') ?></label>
			<?php if($award_level['status']){ ?>
				<?php if($membership['status'] && $userPlan['commission_sale_status']){ ?>
					<?php if($userPlan['level_number']){ ?>
						<input disabled type="text"class="form-control custom_input" value="<?= $userPlan['level_number'] ?>">
					<?php } else { ?>
						<input disabled type="text"class="form-control custom_input" value="Default">
					<?php } ?>
				<?php } else { ?>
					<select class="form-control custom_input" name="level_id">
						<option value=""> <?= __('admin.none') ?> </option>
						<option <?= (isset($user) && $user['level_id'] == 0) ? 'selected' : '' ?> value="0">Default</option>
						<?php foreach ($levels as $key => $value) { ?>
							<option <?= (isset($user) && $user['level_id'] == $value['id']) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['level_number'] ?></option>
						<?php } ?>
					</select>
				<?php } ?>
			<?php } else { ?>
				<input disabled type="text"class="form-control custom_input" value="Off">
			<?php } ?>	
		</div>
	<?php } ?>
	
	<?php if(isset($edit_view)){ ?>
		<div class="form-group mb-2">
			<label class="control-label"><?= __('admin.country') ?></label>
			<select class="form-control custom_input" name="country_id">
				<option value="0"> <?= __('admin.none') ?> </option>
				<?php foreach ($countries as $key => $value) { ?>
					<option <?= (isset($user) && $user['ucountry'] == $value['id']) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
				<?php } ?>
			</select>
		</div>

		<?php if(!isset($user_groups_readonly) || !$user_groups_readonly){?>
			<div class="form-group mb-2">
				<label class="control-label"><?= __('admin.groups') ?></label>
				<select class="form-control select2" name="groups[]" multiple="multiple">
					<?php foreach ($user_groups as $key => $group) { ?>
						<option <?= (isset($user) && in_array($group->id, explode(',', $user['groups'])))? 'selected' : '' ?> value="<?= $group->id ?>"><?= $group->group_name ?></option>
					<?php } ?>
				</select>
			</div>
		<?php } else { 

			$unsbscribed = $this->db->query('select id from  unsubscribed_emails where email="'.$email.'"')->row();

			?>
			<div class="form-group">
				<label class="control-label">Email Subscription:</label>
				<select class="form-control" name="email_subscription">
					<option value="0">Enable</option>
					<option value="1" <?= !empty($unsbscribed) ? 'selected' : ''; ?>>Disable</option>
				</select>
			</div>
			<div class="form-group">
				<label class="control-label">Groups belong:</label>
				<ul class="list-group">
				<?php foreach ($user_groups as $key => $group) { 
					if(!isset($user) || !in_array($group->id, explode(',', $user['groups']))) continue;

					$hasGroupAssigned = true;
					
					$avatar = base_url('assets/images/');

					$avatar .= $group->avatar != '' ? 'site/'.$group->avatar : 'no_image_available.png' ; 
					?>

				  <li class="list-group-item">
				  	<img class="mr-2" src="<?= $avatar; ?>" height="35" width="35"/>
				  	<?= $group->group_name ?>
				  </li>
				<?php } ?>
			  <?php if(!isset($hasGroupAssigned)) { ?>
			  	<li class="list-group-item">No group assigned to you!</li>
			  <?php } ?>			
				</ul>
			</div>
		<?php } ?>
		
	<?php } ?>
	
	<?php if(!isset($edit_view)){ ?>

		<?php if (isset($googlerecaptcha['affiliate_register']) && $googlerecaptcha['affiliate_register']) { ?>
			<div class="captch mb-3 form-group">
				<script src='https://www.google.com/recaptcha/api.js'></script>
				<div class="g-recaptcha" data-sitekey="<?= $googlerecaptcha['sitekey'] ?>"></div>
				<input type="hidden" name="captch_response" id="captch_response">
			</div>
		<?php } ?>

		<?php if(isset($template_index)) { ?>
			<div class="check_box text-left">
				<p>
					<input type="checkbox" id="checkbox" name="terms" value="1" class="" checked>
					<span for="checkbox">
						<a href="#" data-bs-toggle="modal" data-bs-target="#termOfUse" style="margin-top:0px; color: black;">
							<?= __('front.terms_and_conditions') ?>
						</a>
					</span>
				</p>
			</div>
		<?php } else { ?>
			<div class="checkbox">
				<label>
				  <input type="checkbox" id="checkbox" name="terms" value="1" class="" checked>
				  <span class="box"></span>
				  <a href="#" data-bs-toggle="modal" data-bs-target="#terms_content" style="margin-top:0px; color: black;">
				  	<?= __('front.terms_and_conditions') ?>
				  </a>
				</label>
			</div>
		<?php } ?>

		 <button class="<?= $allow_back_to_login ? "btn btn-primary" : ""; ?> btn-registration btn-submit btn btn-block font-weight-bold mar-20 front_button_color front_button_hover_color front_button_text_color" type="submit" value="<?= __('user.submit') ?>">
		 	<?= __('front.create_account') ?>
		 </button>

	<?php } ?>
</form>

 
<script type="text/javascript">

	function isNumberKey(evt)
	{
	  var charCode = (evt.which) ? evt.which : event.keyCode;
	    if (charCode != 46 && charCode != 45 && charCode > 31
	    && (charCode < 48 || charCode > 57))
	     return false;

	  return true;
	}

	jQuery('.datetimepicker').datetimepicker({
		timepicker:false,
		format:'d.m.Y'
	});

	<?php if(isset($edit_view)){ ?>
		jQuery('.select2').select2({
			placeholder : '<?= (isset($user_groups_readonly) && $user_groups_readonly) ? __('admin.no_groups_assigned') : __('admin.assign_user_groups'); ?>'
		});
	<?php } ?>

$( document ).ready(function() {
    $(".reg_form").submit(function(e){
        e.preventDefault();
        $this = $(this);
        
        var is_valid = 0;
        var need_valid = 0;

		$(".tel_input").each(function() {
			let this_is_valid = true;
		    $(this).parents(".form-group").removeClass("has-error");
		    $(this).parents(".form-group").find(".text-danger").remove();
		    if(window["tel_input"+$(this).attr('id')]){
		        var errorMap = ['<?= __('user.invalid_number') ?>','<?= __('user.invalid_country_code') ?>','<?= __('user.too_short') ?>','<?= __('user.too_long') ?>','<?= __('user.invalid_number') ?>'];
		        var errorInnerHTML = '';
		        if ($(this).val().trim()) {
		        	need_valid++;
		            if (window["tel_input"+$(this).attr('id')].isValidNumber()) {

						window["tel_input"+$(this).attr('id')].setNumber($(this).val().trim());

		                is_valid++;
		                this_is_valid = true;
		            } else {
		                var errorCode = window["tel_input"+$(this).attr('id')].getValidationError();
		                errorInnerHTML = errorMap[errorCode];
		                this_is_valid = false;
		            }
		        } else {
		        	if($(this).attr('required') !== undefined) {
		        		need_valid++;
		                this_is_valid = false;
			        	errorInnerHTML = 'The Mobile Number field is required.'; 
			        }
		        }

		        if(!this_is_valid){
		            $(this).parents(".form-group").addClass("has-error");
		            $(this).parents(".form-group").find('> div').after("<span class='text-danger'>"+ errorInnerHTML +"</span>");
		        }
		    }
		});
        

        if(need_valid === is_valid){

            var check_captch = true;
            if (grecaptcha === undefined) { check_captch = false; }
            $("#captch_response").val('')
            if(check_captch){
                captch_response = grecaptcha.getResponse();
                $("#captch_response").val(captch_response)
            }

            var data = new FormData(this);

            $(".tel_input").each(function() {
		        if ($(this).val().trim() && window["tel_input"+$(this).attr('id')].isValidNumber()) {
		        	country_id = window["tel_input"+$(this).attr('id')].getSelectedCountryData().dialCode;
	                data.append($(this).attr('name')+'_afftel_input_pre', country_id);
		        }
		    });

            $.ajax({
                url:'<?= base_url("pagebuilder/register") ?>',
                type:'POST',
                dataType:'json',
                cache: false,
                contentType: false,
                processData: false,
                data:data,
                beforeSend:function(){ 
                	$this.find(".btn-submit").attr("disabled", true); 
                },
                complete:function(){ 
                	$this.find(".btn-submit").removeAttr("disabled"); 
                },
                success:function(json){
                    if(json['redirect']){ window.location.replace(json['redirect']); }
                    if(json['warning']){ alert(json['warning']) }

                    $this.find(".is-invalid").removeClass("is-invalid");
                    $this.find("span.invalid-feedback").remove();

                    if(json['errors']){
                        $.each(json['errors'], function(i,j){
                            if(i == 'captch_response' && grecaptcha){ grecaptcha.reset(); }
                            if(i == 'terms'){ $(".reg-agree-label").after("<span class='invalid-feedback'>"+ j +"</span>"); return true; }

                            $ele = $this.find('#'+i);
                            if($ele){
                                $formGroup = $ele.parents(".form-group");
                                $ele.addClass("is-invalid");
                                if($formGroup.find(".input-group").length){
                                    $formGroup.find(".input-group").after("<span class='invalid-feedback'>"+ j +"</span>");
                                } else if($ele.hasClass('form-group')){
                                    $ele.addClass("has-error");
                                    $ele.append("<br><small class='text-danger'>"+ j +"</small>");
                                }  else {
                                    $ele.parents(".form-group").addClass("has-error");
                                    $ele.after("<span class='invalid-feedback'>"+ j +"</span>");
                                }
                            }
                        })
                    }
                },
            })
        }
        return false;
    });

});
</script>
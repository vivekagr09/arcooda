<?php
	$db =& get_instance();
	$userdetails=$db->userdetails();

	$allows = array(
		'user'   => [1 => 1, 2 => 2, 3=>3 , 4=>4, 5=>5 ,7=>7, 8=>8, 9=>9, 10=>10, 11=>11,12=>12,13=>13],
		'admin'  => [1 => 1, 2 => 2, 3=>3 , 4=>4, 5=>5 , 6=>6, 7=>7, 8=>8, 9=>9, 10=>10, 11=>11, 13=>13],
		'client' => [2 => 2, 3=>3 , 6=>6, 7=>7, 8=>8, 9=>9],
	);

	$user_level_changed = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'user_level_changed'")->row()->id;
	$allows['user'][$user_level_changed] = $user_level_changed;
	
	$vendor_create_product = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_create_product'")->row()->id;
	$new_order_for_vendor = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'new_order_for_vendor'")->row()->id;
	
	$new_vendor_deposit_request = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'new_vendor_deposit_request'")->row()->id;
	$vendor_deposit_request_updated = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_deposit_request_updated'")->row()->id;
	
	$allows['admin'][$vendor_create_product] = $vendor_create_product;
	
	$allows['admin'][$new_order_for_vendor] = $new_order_for_vendor;
	
	$allows['user'][$new_vendor_deposit_request] = $new_vendor_deposit_request;
	$allows['user'][$vendor_deposit_request_updated] = $vendor_deposit_request_updated;
	$allows['admin'][$new_vendor_deposit_request] = $new_vendor_deposit_request;
	$allows['admin'][$vendor_deposit_request_updated] = $vendor_deposit_request_updated;

	
	//$admin_edit_vendor_product = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'admin_edit_vendor_product'")->row()->id;
	$vendor_product_status_0 = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_product_status_0'")->row()->id;
	$vendor_product_status_1 = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_product_status_1'")->row()->id;
	$vendor_product_status_2 = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_product_status_2'")->row()->id;
	$vendor_product_status_3 = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_product_status_3'")->row()->id;
	$vendor_order_status_complete = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_order_status_complete'")->row()->id;


	$vendor_create_form = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_create_form'")->row()->id;
	$vendor_form_status_0 = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_form_status_0'")->row()->id;
	$vendor_form_status_1 = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_form_status_1'")->row()->id;
	$vendor_form_status_2 = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_form_status_2'")->row()->id;
	$vendor_form_status_3 = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_form_status_3'")->row()->id;

	$order_on_vendor_program = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'order_on_vendor_program'")->row()->id;
	$vendor_create_ads = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_create_ads'")->row()->id;
	$vendor_ads_status_0 = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_ads_status_0'")->row()->id;
	$vendor_ads_status_1 = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_ads_status_1'")->row()->id;
	$vendor_ads_status_2 = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_ads_status_2'")->row()->id;
	$vendor_ads_status_3 = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_ads_status_3'")->row()->id;

	$vendor_create_program = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_create_program'")->row()->id;
	$vendor_program_status_0 = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_program_status_0'")->row()->id;
	$vendor_program_status_1 = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_program_status_1'")->row()->id;
	$vendor_program_status_2 = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_program_status_2'")->row()->id;
	$vendor_program_status_3 = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'vendor_program_status_3'")->row()->id;

	$withdrwal_status_change = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'withdrwal_status_change'")->row()->id;
	$send_register_mail_api = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'send_register_mail_api'")->row()->id;
	$wallet_noti_on_hold_wallet = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'wallet_noti_on_hold_wallet'")->row()->id;


	$subscription_status_change = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'subscription_status_change'")->row()->id;
	$allows['user'][$subscription_status_change] = $subscription_status_change;
	

	$subscription_expire_notification = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'subscription_expire_notification'")->row()->id;
	$allows['user'][$subscription_expire_notification] = $subscription_expire_notification;

	$subscription_buy = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'subscription_buy'")->row()->id;
	$allows['admin'][$subscription_buy] = $subscription_buy;
	$allows['user'][$subscription_buy] = $subscription_buy;


	$new_user_request = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'new_user_request'")->row()->id;
	$allows['admin'][$new_user_request] = $new_user_request;
	$allows['user'][$new_user_request] = $new_user_request;
	$new_user_approved = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'new_user_approved'")->row()->id;
	$allows['admin'][$new_user_approved] = $new_user_approved;
	$allows['user'][$new_user_approved] = $new_user_approved;
	$new_user_declined = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = 'new_user_declined'")->row()->id;
	$allows['admin'][$new_user_declined] = $new_user_declined;
	$allows['user'][$new_user_declined] = $new_user_declined;

	
	$allows['admin'][$send_register_mail_api] = $send_register_mail_api;
	$allows['user'][$send_register_mail_api] = $send_register_mail_api;
	
	$allows['user'][$withdrwal_status_change] = $withdrwal_status_change;

	$allows['user'][$vendor_product_status_0] = $vendor_product_status_0;
	$allows['user'][$vendor_product_status_1] = $vendor_product_status_1;
	$allows['user'][$vendor_product_status_2] = $vendor_product_status_2;
	$allows['user'][$vendor_product_status_3] = $vendor_product_status_3;
	$allows['user'][$vendor_order_status_complete] = $vendor_order_status_complete;
	
	$allows['user'][$vendor_form_status_0] = $vendor_form_status_0;
	$allows['user'][$vendor_form_status_1] = $vendor_form_status_1;
	$allows['user'][$vendor_form_status_2] = $vendor_form_status_2;
	$allows['user'][$vendor_form_status_3] = $vendor_form_status_3;

	$allows['user'][$order_on_vendor_program] = $order_on_vendor_program;
	$allows['user'][$vendor_ads_status_0] = $vendor_ads_status_0;
	$allows['user'][$vendor_ads_status_1] = $vendor_ads_status_1;
	$allows['user'][$vendor_ads_status_2] = $vendor_ads_status_2;
	$allows['user'][$vendor_ads_status_3] = $vendor_ads_status_3;

	$allows['user'][$vendor_program_status_0] = $vendor_program_status_0;
	$allows['user'][$vendor_program_status_1] = $vendor_program_status_1;
	$allows['user'][$vendor_program_status_2] = $vendor_program_status_2;
	$allows['user'][$vendor_program_status_3] = $vendor_program_status_3;
	
	$allows['admin'][$vendor_product_status_0] = $vendor_product_status_0;
	$allows['admin'][$vendor_create_form] = $vendor_create_form;
	$allows['admin'][$vendor_create_ads] = $vendor_create_ads;
	$allows['admin'][$vendor_form_status_0] = $vendor_form_status_0;
	$allows['admin'][$vendor_create_program] = $vendor_create_program;
	$allows['admin'][$vendor_program_status_0] = $vendor_program_status_0;
	$allows['admin'][$vendor_ads_status_0] = $vendor_ads_status_0;
	
	$allows['user'][$wallet_noti_on_hold_wallet] = $wallet_noti_on_hold_wallet;


	
	$ticketMailTemplates = ['ticket_created_email', 'ticket_reply_email', 'ticket_status_email'];

	foreach ($ticketMailTemplates as $unique_id) {
		$templateID = (int)$this->db->query("SELECT id FROM mail_templates WHERE unique_id = '{$unique_id}'")->row()->id;
		if(!empty($templateID)) {
			$allows['admin'][$templateID] = $allows['user'][$templateID] = $templateID;
		}
	}





?>

	
		<div class="row">
			<div class="col-12">
			    	<div>
						<h4 class="page-title pull-left"><?= __('admin.mail_editor') ?> (<small><?php echo $templates['name'] ?></small>) </h4>
						<a href="<?= base_url('admincontrol/mails') ?>" class="btn pull-right btn-sm btn-outline-primary"><?= __('admin.back') ?></a>
						<div class="clearfix"></div>
					</div>
				<div class="card m-b-30">
					<div class="card-body">
						<form action="" method="POST" role="form" id="mail_template_form">
							<input type="hidden" id="template_id" name="id" value="<?php echo $templates['id'] ?>">
							
							<div role="tabpanel">
								<ul class="nav nav-pills" role="tablist" id="myTab">
									<?php if($allows['user'][$templates['id']]){ ?><li role="presentation" class=" nav-item">
										<a href="#for-user" class="active nav-link" aria-controls="for-user" role="tab" data-bs-toggle="tab"><?= __('admin.user') ?></a>
									</li><?php } ?>
									<?php if($allows['admin'][$templates['id']]){ ?><li role="presentation">
										<a href="#for-admin" class="nav-link" aria-controls="for-admin" role="tab" data-bs-toggle="tab"><?= __('admin.admin') ?></a>
									</li><?php } ?>
									<?php if($allows['client'][$templates['id']]){ ?><li role="presentation">
										<a href="#for-client" class="nav-link" aria-controls="for-admin" role="tab" data-bs-toggle="tab"><?= __('admin.client') ?></a>
									</li><?php } ?>
								</ul>
							
								<hr>
								<div class="tab-content">
									<?php if($allows['user'][$templates['id']]){ ?>
										<div role="tabpanel" class="tab-pane active template" id="for-user">
											<div class="form-group">
												<label for=""><?= __('admin.subject') ?></label>
												<input type="text" class="form-control" name="subject" value="<?php echo $templates['subject'] ?>">
											</div>
											<div class="form-group">
												<label for=""><?= __('admin.subject') ?></label>
												<textarea type="text" class="form-control summernote-img" name="text" ><?php echo $templates['text'] ?></textarea>
											</div>
										</div>
									<?php } ?>
									<?php if($allows['admin'][$templates['id']]){ ?>
										<div role="tabpanel" class="tab-pane template" id="for-admin">
											<div class="form-group">
												<label for=""><?= __('admin.subject') ?></label>
												<input type="text" class="form-control" name="admin_subject" value="<?php echo $templates['admin_subject'] ?>">
											</div>
											<div class="form-group">
												<label for=""><?= __('admin.subject') ?></label>
												<textarea type="text" class="form-control summernote-img" name="admin_text" ><?php echo $templates['admin_text'] ?></textarea>
											</div>
										</div>
									<?php } ?>
									<?php if($allows['client'][$templates['id']]){ ?>
										<div role="tabpanel" class="tab-pane template" id="for-client">
											<div class="form-group">
												<label for=""><?= __('admin.subject') ?></label>
												<input type="text" class="form-control" name="client_subject" value="<?php echo $templates['client_subject'] ?>">
											</div>
											<div class="form-group">
												<label for=""><?= __('admin.subject') ?></label>
												<textarea type="text" class="form-control summernote-img" name="client_text" ><?php echo $templates['client_text'] ?></textarea>
											</div>
										</div>
									<?php } ?>
								</div>
							</div>
							<div>
								<?php 
									$shortcode= explode(",",($templates['shortcode']));
									foreach ($shortcode as $key => $value) {
										echo '<span class="m-1">[['. $value .']]</span>';
									}
								?>
							</div>
							<br>
							<div class="row">
								<div class="col-sm-2">
									<button type="submit" class="btn btn-primary"><?= __('admin.submit') ?></button>
								</div>
								<div class="col-sm-10">
									<div class="input-group">
							            <input type="text" id="test_email" class="form-control" name="test_email" class="test_email">
							            <span class="btn btn-primary send-test"> <?= __('admin.send_test') ?></span>
							        </div>
								</div>
							</div>
							
						</form>
					</div>
				</div> 
			</div> 
		</div>

<script type="text/javascript">
	$( document ).ready(function() {
		$('#myTab li:first a').tab('show');

		$(".send-test").on('click',function(){
			$this = $(this);
			$.ajax({
				type:'POST',
				dataType:'json',
				data: $('.tab-pane.active').find('input, select, textarea').serialize() + "&id="+ $("#template_id").val()+"&test_email="+$("#test_email").val()+"&send_test=true&test_for=" + $('.tab-pane.active.template').attr("id"),
				beforeSend:function(){ $this.button("loading"); },
				complete:function(){ $this.button("reset"); },
				success:function(json){
					$(".alert-mail").remove();
					if(json['error']){
						$this.parents(".input-group").after('<div class="alert alert-mail alert-danger">'+ json['error'] +'</div>');
					}
					if(json['success']){
						$this.parents(".input-group").after('<div class="alert alert-mail alert-success">'+ json['success'] +'</div>');
					}
				},
			})
		})
	});
</script>
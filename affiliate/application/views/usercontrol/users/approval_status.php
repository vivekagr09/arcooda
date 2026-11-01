<?php if($userdetails['reg_approved'] == 0 && $userdetails['verification_id'] != "") { ?>

<div class="container-fluid pt-4">
	<h2 class="notification_on_pages">
	<span class="badge bg-info"><?= __('user.registration_approval_pending') ?></span>
	</h2>
	<div class="jumbotron">
	  <h1 class="display-4 mb-4"><?= __('user.hello') ?>, <?= $this->session->userdata['user']['firstname']; ?> <?= $this->session->userdata['user']['lastname']; ?>!</h1>
	  <p class="lead"><?= __('user.registration_approval_verify_pending') ?></p>
	  <p class="lead"><?= __('user.if_have_any_queries_please_contact_us') ?> <a href="<?php echo base_url('usercontrol/contact-us');?>"><?= __('user.here') ?></a></p>
	</div>
</div>

<?php } else if($userdetails['reg_approved'] == 0) { ?>

<div class="container-fluid pt-4">
	<h2 class="notification_on_pages">
	<span class="badge bg-info"><?= __('user.registration_approval_pending') ?></span>
	</h2>
	<div class="jumbotron">
	  <h1 class="display-4 mb-4"><?= __('user.hello') ?>, <?= $this->session->userdata['user']['firstname']; ?> <?= $this->session->userdata['user']['lastname']; ?>!</h1>
	  <p class="lead"><?= __('user.thank_you_registering_request_received') ?></p>
	  <p class="lead"><?= __('user.if_have_any_queries_please_contact_us') ?> <a href="<?php echo base_url('usercontrol/contact-us');?>"><?= __('user.here') ?></a></p>
	</div>
</div>

<?php } else if($userdetails['reg_approved'] == 1) { ?>

<div class="container-fluid pt-4">
	<h2 class="notification_on_pages">
	<span class="badge bg-success"><?= __('user.registration_approval_verify_success') ?></span>
	</h2>
	<div class="jumbotron">
	  <h1 class="display-4 mb-4"><?= __('user.hello') ?>, <?= $this->session->userdata['user']['firstname']; ?> <?= $this->session->userdata['user']['lastname']; ?>!</h1>
	  <p class="lead"><?= __('user.registration_account_access') ?> <a href="<?php echo base_url('usercontrol/dashboard');?>"><?= __('user.here') ?></a></p>
	  <p class="lead"><?= __('user.if_have_any_queries_please_contact_us') ?> <a href="<?php echo base_url('usercontrol/contact-us');?>"><?= __('user.here') ?></a></p>
	</div>
</div>

<?php } else if($userdetails['reg_approved'] == 2) { ?>

<div class="container-fluid pt-4">
	<h2 class="notification_on_pages">
		<span class="badge bg-danger">
			<?= __('user.new_user_registration_request_declined_by_admin') ?>
		</span>
	</h2>

	<div class="jumbotron">
	  <h1 class="display-4 mb-4"><?= __('user.hello') ?>, <?= $this->session->userdata['user']['firstname']; ?> <?= $this->session->userdata['user']['lastname']; ?>!</h1>
	  <p class="lead"><?= __('user.request_declined_by_admin_contact_us') ?> <a href="<?php echo base_url('usercontrol/contact-us');?>"><?= __('user.here') ?></a></p>
	</div>
</div>

<?php 
} else {
	header("Location: ".base_url('usercontrol/dashboard')); 
	die;
}
?>
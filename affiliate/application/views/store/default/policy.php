<section class="about-page">
  <div class="container">
     <div class="row">
	   <div class="col-12 col-md-12 col-lg-4 col-xl-6">
	   	<?php 
	   		$policyimage = $store_setting['policyimage'] ? base_url('assets/images/site/'. $store_setting['policyimage']) : base_url('assets/store/default/img/about-img.png');
	   		?>
	     <img src="<?=$policyimage;?>" class="img-fluid img-about-main mt-4" alt="<?= __('store.image') ?>">
	   </div>
	   <div class="col-12 col-md-12 col-lg-8 col-xl-6">
	      <div class="about-top-text">
		    <h2><?= __('store.privacy_policy') ?></h2>
			<img src="<?= base_url('assets/store/default/'); ?>img/popline.png" class="cn-titlebar mx-0"  alt="<?= __('store.image') ?>">
			<?= !empty($content['policy_content']) ? $content['policy_content'] : __('store.privacy_if_not_exist'); ?>
			<a href="<?= $base_url ?>contact"><?= __('store.contact_us') ?></a>
		  </div>
	   </div>
	 </div> 
  </div>
</section>
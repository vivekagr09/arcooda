<section class="about-page">
  <div class="container">
     <div class="row">
	   <div class="col-12 col-md-12 col-lg-4 col-xl-6">
        <?php $img = (!empty($data->image)) ? base_url('assets/images/site/'. $data->image) : base_url('assets/store/default/img/about-img.png'); ?>
        <img src="<?= $img ?>" class="img-fluid img-about-main mt-4" alt="<?= __('store.image') ?>">
	    </div>
	   <div class="col-12 col-md-12 col-lg-8 col-xl-6">
	      <div class="about-top-text">
		    <h2><?= $data->title ?></h2>
			<img src="<?= base_url('assets/store/default/'); ?>img/popline.png" class="cn-titlebar mx-0"  alt="<?= __('store.image') ?>">
			<?= $data->content ?>
		  </div>
	   </div>
	 </div> 
  </div>
</section>
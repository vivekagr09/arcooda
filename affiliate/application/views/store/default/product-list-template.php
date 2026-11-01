<script id="product-list-template" type="text/html">
	{{^products}}
	{{^show_dummy}}
	<?php for ($i=0; $i < 2; $i++) { ?>
	<div class="product-wrapper">
	  <div class="product-img position-relative">
	    <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/product1.png" class="img-fluid primg" />
	    <div class="cn-flag position-absolute">
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/flag.png" /> Belgium, Provincie
	      Brabant
	    </div>
	  </div>

	  <div class="pr-content">
	    <div class="price">$659.00</div>
	    <div class="rating-row d-flex justify-space-center">
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st1.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st1.png" />
	    </div>
	    <h3>Lorem Ipsum</h3>
	    <p>What is Lorem Ipsum?</p>
	    <div class="product-buttons d-flex">
	      <a href="#" class="btn btn-product bg-main2 text-white"
	        ><?= __('store.details') ?></a
	      >
	    </div>
	  </div>
	</div>
	<div class="product-wrapper">
	  <div class="product-img position-relative">
	    <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/car1.png" class="img-fluid primg" />
	    <div class="cn-flag position-absolute">
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/flag.png" /> Belgium, Provincie
	      Brabant
	    </div>
	  </div>

	  <div class="pr-content">
	    <div class="price">$659.00</div>
	    <div class="rating-row d-flex justify-space-center">
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st1.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st1.png" />
	    </div>
	    <h3>Lorem Ipsum</h3>
	    <p>What is Lorem Ipsum?</p>
	    <div class="product-buttons d-flex">
	      <a href="#" class="btn btn-product bg-main2 text-white"
	        ><?= __('store.details') ?></a
	      >
	    </div>
	  </div>
	</div>
	<div class="product-wrapper">
	  <div class="product-img position-relative">
	    <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/car2.png" class="img-fluid primg" />
	    <div class="cn-flag position-absolute">
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/flag.png" /> Belgium, Provincie
	      Brabant
	    </div>
	  </div>

	  <div class="pr-content">
	    <div class="price">$659.00</div>
	    <div class="rating-row d-flex justify-space-center">
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st1.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st1.png" />
	    </div>
	    <h3>Lorem Ipsum</h3>
	    <p>What is Lorem Ipsum?</p>
	    <div class="product-buttons d-flex">
	      <a href="#" class="btn btn-product bg-main2 text-white"
	        ><?= __('store.details') ?></a
	      >
	    </div>
	  </div>
	</div>
	<div class="product-wrapper">
	  <div class="product-img position-relative">
	    <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/car3.png" class="img-fluid primg" />
	    <div class="cn-flag position-absolute">
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/flag.png" /> Belgium, Provincie
	      Brabant
	    </div>
	  </div>

	  <div class="pr-content">
	    <div class="price">$659.00</div>
	    <div class="rating-row d-flex justify-space-center">
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st1.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st1.png" />
	    </div>
	    <h3>Lorem Ipsum</h3>
	    <p>What is Lorem Ipsum?</p>
	    <div class="product-buttons d-flex">
	      <a href="#" class="btn btn-product bg-main2 text-white"
	        ><?= __('store.details') ?></a
	      >
	    </div>
	  </div>
	</div>
	<div class="product-wrapper">
	  <div class="product-img position-relative">
	    <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/car4.png" class="img-fluid primg" />
	    <div class="cn-flag position-absolute">
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/flag.png" /> Belgium, Provincie
	      Brabant
	    </div>
	  </div>

	  <div class="pr-content">
	    <div class="price">$659.00</div>
	    <div class="rating-row d-flex justify-space-center">
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st1.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st1.png" />
	    </div>
	    <h3>Lorem Ipsum</h3>
	    <p>What is Lorem Ipsum?</p>
	    <div class="product-buttons d-flex">
	      <a href="#" class="btn btn-product bg-main2 text-white"
	        ><?= __('store.details') ?></a
	      >
	    </div>
	  </div>
	</div>
	<div class="product-wrapper">
	  <div class="product-img position-relative">
	    <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/car5.png" class="img-fluid primg" />
	    <div class="cn-flag position-absolute">
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/flag.png" /> Belgium, Provincie
	      Brabant
	    </div>
	  </div>

	  <div class="pr-content">
	    <div class="price">$659.00</div>
	    <div class="rating-row d-flex justify-space-center">
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st1.png" />
	      <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/') ?>img/st1.png" />
	    </div>
	    <h3>Lorem Ipsum</h3>
	    <p>What is Lorem Ipsum?</p>
	    <div class="product-buttons d-flex">
	      <a href="#" class="btn btn-product bg-main2 text-white"
	        ><?= __('store.details') ?></a
	      >
	    </div>
	  </div>
	</div>
	<?php } ?>
	{{/show_dummy}}
	{{#show_dummy}}
	<div class="card w-100">
		<div class="card-body py-4">
			<div class="row w-100">
				<div class="col-12 text-danger">
					<h3 class="text-center"><?= __('store.no_products_available') ?></h3>
				</div>
			</div>
		</div>
	</div>
	{{/show_dummy}}
	{{/products}}

	{{#products}}
	<div class="product-wrapper">
		<div class="product-img position-relative mb-2">
			<a href="{{product_details_href}}"><img alt="<?= __('store.image') ?>" src="{{product_image_src}}" class="img-fluid primg" onerror="this.src='<?= base_url('assets/store/default/img/no-image.png')?>';"/></a>
			{{#country_code}}
			<div class="cn-flag position-absolute">
				<img alt="<?= __('store.image') ?>" src="{{country_flag_src}}" /> {{country_name}}, {{state_name}}</span>
			</div>
			{{/country_code}}
		</div>

		<div class="pr-content">
			<div class="price">{{product_price}}</div>
			<div class="rating-row d-flex justify-space-center">{{{product_avg_rating_stars}}}</div>
			<h3>{{product_name}}</h3>
			<p>{{product_short_description}}</p>
		</div>
		<div class="product-buttons d-flex">
			<a href="{{product_details_href}}" class="btn btn-product bg-main2 text-white"><?= __('store.details') ?></a>
		</div>
	</div>
	{{/products}}
</script>
<?php
if(empty($products)){ 
?>
	<div class="card w-100">
		<div class="card-body py-4">
			<div class="row w-100">
				<div class="col-12 text-danger">
					<h3 class="text-center"><?= __('store.no_products_available') ?></h3>
				</div>
			</div>
		</div>
	</div>
<?php 
} 
?>

<?php foreach ($products as $key => $product) { ?>
	<div class="product-wrapper">
		<div class="product-img position-relative">
			<?php 
			
			$href = base_url("store/". base64_encode($user_id) . "/product/". $product['product_slug']);
			$image = (!empty($product['product_featured_image'])) ? base_url('assets/images/product/upload/thumb/'. $product['product_featured_image']) : base_url('assets/store/default/').'img/product1.png';
			?>
			<a href="<?= $href ?>"><img alt="<?= __('store.image') ?>" src="<?= $image ?>" class="img-fluid primg" /></a>
			<div class="cn-flag position-absolute">
				<?php if($product['country_code']){ ?>
					<img alt="<?= __('store.image') ?>" src="<?= getFlag($product['country_code']) ?>" /> <?= $product['country_name'] ?>, <?= $product['state_name'] ?></span>
				<?php } ?>
			</div>
		</div>

		<div class="pr-content">
			<div class="rating-row d-flex justify-space-center">
				<?php
				for ($i=0; $i < $product['product_avg_rating']; $i++) { 
				?>
				<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st.png">
				<?php
				}
				while($product['product_avg_rating'] < 5) {
				?>
				<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st1.png">
				<?php        
				$product['product_avg_rating']++;            
				}
				?>
			</div>
			<h3><?= (!empty($product['product_name'])) ? $product['product_name'] : 'Lorem Ipsum' ?></h3>
			<?php $desc_suffix = (strlen($product['product_short_description']) > 50) ? "..." : ""; ?>
			<p><?= (!empty($product['product_short_description'])) ? substr($product['product_short_description'], 0, 50).$desc_suffix : 'What is Lorem Ipsum?' ?></p>
			<div class="price"><?= (!empty($product['product_price'])) ? c_format($product['product_price']) : '$659.00' ?></div>
		</div>
		<div class="product-buttons d-flex">
			<a href="<?= $href ?>" class="btn btn-product bg-main2 text-white"><?= __('store.details') ?></a>
		</div>
	</div>
<?php } ?>
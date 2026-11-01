<!--sub_menu-->
<div>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mt-1 mr-1">
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#profilesubnav" aria-controls="profilesubnav" aria-expanded="false" aria-label="Toggle navigation">
		    <span class="navbar-toggler-icon"></span>
		  </button>
            <div class="collapse navbar-collapse" id="profilesubnav">
                <ul class="nav navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>profile"><?= __('store.profile') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>order"><?= __('store.orders') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>shipping"><?= __('store.shipping') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>wishlist"><?= __('store.wishlist') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>logout"><?= __('store.logout') ?></a>
                    </li>
                </ul>
            </div>
    </nav>
</div>
<!--sub_menu-->

<section class="profile-page">
	<div class="container main-container">
			<div class="">
				<h2><?= __('store.wishlist') ?></h2>
				<div class="my-orders">
					<div class="cart-wrapper w-listed-products">
						<?php  
							if(isset($products) && sizeof($products)) {
								foreach($products as $product) {
									$href = base_url("store/". base64_encode($user_id) . "/product/". $product['product_slug']);
									$image = (!empty($product['product_featured_image'])) ? base_url('assets/images/product/upload/thumb/'. $product['product_featured_image']) : base_url('assets/store/default/').'img/product1.png';
									?>
									<div class="row bg-white py-2 mb-2">
										<div class="col-9 p-2">
											<img src="<?= $image; ?>" class="mr-2" width="50" height="50"/>
											<span class=""><?= $product['product_name'] ?></span>
										</div>
										<div class="col-3">
											<span class="my-orders-text">
												<a href="<?= $href ?>" class="btn btn-wishlist"><?= __('store.details') ?></a>&nbsp;
												<a id="btn-add-to-wishlist" data-product_id="<?= $product['product_id'] ?>" href="javascript:void(0);" class="btn btn-wishlist-remove"><?= __('store.remove') ?></a>
											</span>
										</div>
									</div>									
									<?php
								}
							} else {
								?>
									<div class="row bg-white py-2 mb-2">
										<div class="col-12 p-2 text-center">
											<span class="wishlist-product-title ml-4"><?= __('store.no_wishlisted_products_available') ?></span>
										</div>
									</div>
								<?php
							}
						?>
					</div>
				</div>
			</div>
	</div>
</section>
<script>
$(document).on('click', '#btn-add-to-wishlist',function(){
	$.ajax({
		url:'<?= base_url('Store/toggle_wishlist') ?>',
		type:'POST',
		dataType:'json',
		data: { product_id : $(this).data('product_id')},
		success:function(json){
			location.reload();
		},
	});
});
</script>
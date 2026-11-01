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
			<h2><?= __('store.orders') ?></h2>
			<div class="my-orders">
				<div class="cart-wrapper">
					<ul class="cart-header">
						<li><?= __('store.order_id') ?></li>
						<li class="cart-item-price"><?= __('store.price') ?></li>
						<li><?= __('store.order_status') ?></li>
						<li><?= __('store.payment_method') ?></li>
						<li><?= __('store.transaction') ?></li>
						<li></li>
					</ul>

					<?php if($buyproductlist) {

							$subtotal = 0;
						
							foreach($buyproductlist as $product){ 

								$subtotal = $subtotal + (float)$product['total_sum'];
								
							?>
							<ul class="cart-items-row">
								<li><span class="my-orders-text"><?php echo $product['id'];?></span></li>
								<li><span class="my-orders-text"><?php echo c_format($product['total_sum']); ?></span></li>
								<li><span class="my-orders-text"><?php echo $status[$product['status']]; ?></span></li>
								<li><span class="my-orders-text text-center"><?php echo str_replace("_", " ", $product['payment_method']);?></span></li>
								<li><span class="my-orders-text"><?php echo $product['txn_id'];?></span></li>
								<li><span class="my-orders-text">
									<a href="<?= base_url('store/vieworder/'. $product['id']) ?>" class="btn btn-save-profile"><?= __('store.details') ?></a>
								</span></li>
							</ul>
						<?php } ?>
						<ul class="cart-footer-row">
							<li>
								<span><?= __('store.subtotal') ?></span>		 
								<span><?php echo c_format($subtotal); ?></span>		 
							</li>
							<li>
								<span><?= __('store.total') ?></span>		 
								<span><?php echo c_format($subtotal); ?></span>		 
							</li>
						</ul>
					<?php } else { ?>
						<ul class="cart-items-row">
							<li class="w-100"><span class="my-orders-text"><?= __('store.no_order_found') ?></span></li>		
						</ul>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</section>

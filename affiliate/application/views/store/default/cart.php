<section class="cart-page cart-page-width">
	<?php if($products) { ?>
		<form method="POST" id="cart-form">
			<div class="container">
				<h2><?= __('store.shopping_cart') ?></h2>
				
				<div class="cart-table">
					<?php include_once "cart_products_table.php"; ?>
				</div>
				
				<div class="cart-buttons-row">
					<a href="<?= $base_url ?>checkout" class="btn btn-checoutcart bg-main2"><?= __('store.checkout') ?></a>
					<a href="<?= $base_url ?>" class="btn btn-checoutcart bg-main"><img src="<?= base_url('assets/store/default/'); ?>img/shopar.png" alt="><?= __('store.icon') ?>"> <?= __('store.continue_shopping') ?></a>
				</div>
			</div>
			<form>
			<?php } else { ?>
				<div class="text-center">
					<img class="mt-4" src="<?= base_url('assets/store/default/img/empty_cart_teaser.jpg') ?>"><br/>
					<h2 class="m-0 mb-4"><?= __('store.shopping_cart_is_empty') ?></h2>
					<a href="<?= $base_url ?>" class="btn btn-checoutcart bg-main text-light mb-4"><img src="<?= base_url('assets/store/default/'); ?>img/shopar.png" alt="icon"> <?= __('store.continue_shopping') ?></a>
				</div>
			<?php } ?>
		</section>


		<script type="text/javascript">
			var xhr ;

			$("#cart-form").delegate(".qty-input","change",function(){
				if(xhr && xhr.readyState != 4) xhr.abort();
				$this = $(this);
				xhr = $.ajax({
					url:'',
					type:'POST',
					dataType:'html',
					data:$("#cart-form").serialize(),
					beforeSend:function(){},
					complete:function(){},
					success:function(response){
						$('.cart-table').html(response);
						updateCart();
					}
				})
				return false;
			})

			$(document).delegate(".cart-counter button","click",function(){
				var val = $(this).parent().find("input").val();
				if($(this).hasClass("add")) { val ++ }
					else { val -- }
						if(val <= 0) val = 1;
					$(this).parent().find("input").val(val).trigger("change");
				});
			

		</script>
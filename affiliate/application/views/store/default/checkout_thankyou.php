<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="description" content=""/>
	<meta name="author" content=""/>
	
	<?php if(isset($meta_title)){ ?> <meta property="og:title" content="<?php echo $meta_title ?>"/><?php } ?>
	<?php if(isset($meta_description)){ ?> <meta property="og:description" content="<?php echo $meta_description ?>"/><?php } ?>
	<?php if(isset($meta_image)){ ?> <meta property="og:image" content="<?php echo $meta_image ?>"/><?php } ?>
	<?php 
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	?>
	<meta property="og:url" content="<?= $actual_link ?>"/>
	<meta name="twitter:card" content="summary_large_image"/>

	<?php if($store_setting['favicon']){ ?>
		<link rel="icon" href="<?= base_url('assets/images/site/'.$store_setting['favicon']) ?>" type="image/*" sizes="16x16">
	<?php } ?>

	<title><?= $store_setting['name'] ?>  <?= isset($meta_title) ? '- ' . $meta_title : '' ?></title>

	<!--  CSS -->
	<link rel="stylesheet" href="<?= base_url('assets/store/default/'); ?>css/bootstrap.min.css" />
	<link rel="stylesheet" href="<?= base_url('assets/store/default/'); ?>css/thankyou.css" />

	<script src="<?= base_url('assets/store/default/'); ?>js/jquery-3.5.1.slim.min.js"></script>
	<script src="<?= base_url('assets/store/default/'); ?>js/jquery.min.js"></script>
	<script src="<?= base_url('assets/store/default/'); ?>js/bootstrap.min.js"></script>
	<script src="<?= base_url('assets/plugins/store/') ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

	<script type="text/javascript">
		try {
			<?php 
			if($store_setting['google_analytics'] != ''){
				$ana = preg_replace('/<script>/', '', $store_setting['google_analytics']);
				$ana = preg_replace('/<\/script>/', '', $ana);
				echo $ana;
			} 
			?>
		} catch (error) {
			console.log(error);
		}
	</script>

	<?php 
	$global_script_status = (array)json_decode($SiteSetting['global_script_status'],1);
	if(in_array('store', $global_script_status)){
		echo $SiteSetting['global_script'];
	}
	?>

	<script type="text/javascript">
		(function ($) {
			$.fn.btn = function (action) {
				var self = $(this);
				if (action == 'loading') {
					if ($(self).attr("disabled") == "disabled") {
                  }
                  $(self).attr("disabled", "disabled");
                  $(self).attr('data-btn-text', $(self).html());
                  $(self).html('<div class="spinner-border spinner-border-sm"></div>&nbsp;' + $(self).text());
              }
              if (action == 'reset') {
              	$(self).html($(self).attr('data-btn-text'));
              	$(self).removeAttr("disabled");
              }
          }
      })(jQuery);
      var formDataFilter = function(formData) {
      	if (!(window.FormData && formData instanceof window.FormData)) return formData
      		if (!formData.keys) return formData
      			var newFormData = new window.FormData()
      		Array.from(formData.entries()).forEach(function(entry) {
      			var value = entry[1]
      			if (value instanceof window.File && value.name === '' && value.size === 0) {
      				newFormData.append(entry[0], new window.Blob([]), '')
      			} else {
      				newFormData.append(entry[0], value)
      			}
      		});
      		return newFormData;
      	}
      </script>

      <?php if (is_rtl()) { ?>
      	<!-- place here your RTL css code -->
      <?php } ?>
  </head>

<body class="body-bgcolor">

<div class="container-fluid">
    <div class="row top-head">
        <div class="col-sm-2 text-center back-button">
            <a href="<?= base_url('store/order') ?>">
            	<img src="<?= base_url('assets/store/default/img/back-button.png') ?>" class="img-fluid" alt="<?= __('store.back') ?>"></a>
        </div>
         <?php  $logo = ($store_setting['logo']) ? base_url('assets/images/site/'.$store_setting['logo']) : base_url('assets/store/default/').'img/logo.png'; ?>
        <div class="col-sm-8 text-center logo-head">
            <a href="<?=base_url('store');?>"><img src="<?= $logo; ?>"></a>
        </div>
        <div class="col-sm-2 text-center print-button">
            <a class="no-print print" href="javascript:void(0);"><img src="<?= base_url('assets/store/default/'); ?>img/printer.png" class="img-fluid" alt="<?= __('store.print') ?>"></a>

        </div>
    </div>
</div>


<!--sub_menu-->
<div>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mt-0 mr-1">
		<a class="navbar-brand" href="#">
			<?= $store_setting['name'] ?> <?= isset($meta_title) ? '- ' . $meta_title : '' ?>
		</a>
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

<!--order form-->
<div class="container">
	<div class="row">
        <div class="container main-container">
	    	<!--order number info-->
	        <div class="row">
	            <h4>
	            	<div>
	            		<?= __('store.order_number') ?> (#<?php echo orderId($order['id']); ?>)
	            	</div>
	            	<br>
			            <?php if($order['order_country']){ ?>
							<small><b><?= __('store.order_done_from') ?></b>
								<?php echo $order['order_country'];?><?php echo $order['order_country_flag'];?>
						<?php  } ?>
							</small>
						<?php if(!$order['order_country']){ ?>
							<small><b><?= __('store.order_done_from') ?></b>
								<?php echo 'localhost';?>
						<?php  } ?>
							</small>
						<br>
	            	<span><?= __('store.thank_you_for_purchasing_an_order') ?></span></h4>
	        </div>
	        <!--order number info-->

	        <!--product info-->
	        <div class="row non-flex">
	            <h3 class="title"><?= __('store.product_info') ?></h3>
	            <div class="table-responsive-xl">
	                <table class="table table-striped text-nowrap">
	                    <thead class="thead-dark">
	                        <tr>
	                            <th scope="col"><?= __('store.name') ?></th>
	                            <th scope="col"><?= __('store.image') ?></th>
	                            <th scope="col"><?= __('store.unit_price') ?></th>
	                            <th scope="col"><?= __('store.quantity') ?></th>
	                            <th scope="col"><?= __('store.discount') ?></th>
	                            <th scope="col"><?= __('store.total') ?></th>
	                        </tr>
	                    </thead>

	                    <tbody>
	                    	<?php foreach ($products as $key => $product) { ?>
	                        <tr>
	                            <td>
	                            	<br>
									<!--product variation-->
									<?php
										$combinationString = "";
										if(isset($product['variation']) && !empty($product['variation'])) {
											$variation = json_decode($product['variation']);
											foreach ($variation as $key => $value) {
												if($key == 'colors') {
													$combinationString .= ($combinationString == "") ? explode("-",$value)[1] : ",".explode("-",$value)[1];
												} else {
													$combinationString .= ($combinationString == "") ? $value : ",".$value;
												}
											}
										}
										?>

	                            	<!--product name-->
	                            	<?= $product['product_name'] ?> <?= ($combinationString != "") ? "(".$combinationString.")" : "" ?>


									<!--coupon discount-->
									<?php if($product['coupon_discount'] > 0){ ?>
										<p><?= __('store.code') ?>:</p> 
											<p class="coupon-code">
												<span> <?= $product['coupon_code'] ?></span>
											<?= __('store.applied') ?>
										</p>
									<?php } ?>

									<!--course link-->
									<?php if($order['status'] == 1 && ($product['product_type'] == 'downloadable' || $product['product_type'] =='video' || $product['product_type'] =='videolink') && $product['downloadable_files']) { 
										 if ($product['product_type'] =='video' || $product['product_type'] =='videolink') { ?>

								 	<!--course_link-->
								 	<p><?= __('store.course_link') ?>:</p>
									 	<span>
									 		<a href="<?=base_url('store/vieworderdetails/').$order['id'].'?referance='.$product['product_id'] ?>" title="<?= __('store.start_course') ?>" target="_blank"> <?= __('store.start_course') ?>
									 		</a>
									 	</span>

								<?php  } else {?>

									<!--files_to_download-->
									<p><?= __('store.files_to_download') ?>:</p>
									<?php foreach ($product['downloadable_files'] as $downloadable_filess) {
										$downloadable_link =  base_url('store/downloadable_file/'. $downloadable_filess['name'] . '/' .$downloadable_filess['mask'].'/'.$product['order_id']);
										$downloadable_link .=empty($is_guest)? '?link='.encryptString($order['user_id']):'';
									 ?>
										<span>
											<a href="<?php echo $downloadable_link; ?>" target="_blank"><?php echo $downloadable_filess['mask'] ?>
											</a>
										</span>
									<?php } ?>
								<?php } } ?>		
							<!--files and courses section-->	
	                        	</td>
	                            <td>
	                            	<img class="img-thumbnail img-fluid" width="60" src="<?= (!empty($product['image'])) ? $product['image'] : base_url('assets/store/default/img/1.png'); ?>" alt="<?= __('store.image') ?>">
									
	                        	</td>
	                            <td><?php echo c_format($product['price'] + $product['variation_price']); ?></td>
	                            <td><?php echo $product['quantity']; ?></td>
	                            <td>
	                            	<?php if($product['coupon_discount'] > 0){  
	                            	 echo isset($totals['discount_total']) ? c_format($totals['discount_total']['value']) : '';
	                            	 } ?>
	                            </td>
	                            <td><?php echo c_format($product['total']); ?></td>
	                        </tr>
	                    <?php }  ?>
	                    </tbody>
	                </table>
	            </div>
	        </div>

            <div class="container">
                <div class="row total-container">
                    <div class="total">
                    	<?php foreach ($totals as $key => $total) { ?>
                        <h6><?= $total['text'] ?>: <span><?php echo c_format($total['value']); ?></span></h6>
                        <?php } ?>
                    </div>
                </div>
            </div>
	  
	        <!--product info-->
	                
	        <!--order payment info-->
	        <div class="row non-flex">
	            <h3 class="title"><?= __('store.order_payment_info') ?></h3>
	            <div class="table-responsive-lg">
	                <table class="table table-striped">
	                    <thead class="thead-dark">
	                        <tr>
	                            <th scope="col"><?= __('store.mode') ?></th>
	                            <th scope="col"><?= __('store.transaction_id') ?></th>
	                            <th scope="col"><?= __('store.payment_status') ?></th>
	                        </tr>
	                    </thead>
	                    <tbody>
	                        <tr>
	                        	<?php if($order['status'] == 0){ ?>
	                            <td><?= __('store.waiting_for_payment_status') ?></td>
	                            <?php } ?>

	                            <?php foreach ($payment_history as $key => $value) { ?>
	                            	<td><?php echo str_replace("_", " ", $value['payment_mode']) ?></td>
	                            	<td><?php echo $order['txn_id'];?></td>
	                            	<td><?php echo $value['paypal_status'] ?></td>
	                            <?php } ?>

	                            <?php if($order['payment_method'] == 'bank_transfer'){ ?>
										<td><?= __('store.bank_transfer_instruction') ?></td>
										<pre class="well"><?php echo $paymentsetting['bank_transfer_instruction'] ?></pre>
								<?php } ?>
	                        </tr>
	                    </tbody>
	                </table>
	                <?php if($orderProof){ ?>
						<label class="control-label"><b><?= __('store.payment_proof') ?></b>
								<a href="<?= $orderProof->downloadLink ?>" target='_blank'>: <?= __('store.download') ?></a>
						</label>
					<?php } ?>
	            </div>
	        </div>
	        <!--order payment info-->
	        
	        <!--shipping info-->
	        <?php if($order['allow_shipping']){ ?>
	        <div class="row non-flex dark-text">
	            <h3 class="title"><?= __('store.shipping_details') ?></h3>
	            <div class="table-responsive-lg">
	                <table class="table table-striped">
	                    <thead class="thead-dark">
	                        <tr>
	                            <th scope="col">.</th>
	                            <th scope="col">.</th>
	                        </tr>
	                    </thead>
	                    <tbody>
	                        <tr>
	                            <td><?= __('store.phone') ?></td>
	                            <td><?php echo $order['phone'] ?></td>
	                        </tr>
	                        <tr>
	                            <td><?= __('store.address') ?></td>
	                            <td><?php echo $order['address'] ?></td>
	                        </tr>
	                        <tr>
	                            <td><?= __('store.country') ?></td>
	                            <td><?php echo $order['country_name'] ?></td>
	                        </tr>
	                        <tr>
	                            <td><?= __('store.state') ?></td>
	                            <td><?php echo $order['state_name'] ?></td>
	                        </tr>
	                        <tr>
	                            <td><?= __('store.city') ?></td>
	                            <td><?php echo $order['city'] ?></td>
	                        </tr>
	                        <tr>
	                            <td><?= __('store.postal_code') ?></td>
	                            <td><?php echo $order['zip_code'] ?></td>
	                        </tr>
	                    </tbody>
	                </table>
	            </div>
	        </div>
	        <?php } ?>
	        <!--shipping info-->
	        
	        
	        <!--order attechments-->
	        <?php if($order['files']){ ?>
	        <div class="row non-flex">
	            <h3 class="title"><?= __('store.order_attechments_download') ?></h3>
	            <div class="table-responsive-lg">
	                <table class="table table-striped">
	                    <thead class="thead-dark">
	                        <tr>
	                            <th scope="col">#</th>
	                            <th></th>
	                            <th></th>
	                        </tr>
	                    </thead>
	                    <tbody>
	                        <tr>
	                            <td># <?php echo $order['files'] ?></td>
	                            <td></td>
	                            <td></td>
	                        </tr>
	                    </tbody>
	                </table>
	            </div>
	        </div>
	        <?php } ?>
	        <!--order attechments-->

	        <!--order status-->
	        <div class="row non-flex">
	            <h3 class="title"><?= __('store.update_order_status') ?></h3>
	            <div class="table-responsive-lg">
	                <table class="table table-striped">
	                    <thead class="thead-dark">
	                        <tr>
	                            <th scope="col">#</th>
	                            <th scope="col"><?= __('store.status') ?></th>
	                            <th scope="col"><?= __('store.comment') ?></th>
	                        </tr>
	                    </thead>
	                    <tbody>
	                    	<?php if(!$order_history){ ?>
								<tr>
									<td><?= __('store.no_any_order_status') ?></td>
								</tr>
							<?php } ?>
	                    	<?php foreach ($order_history as $key =>$value) { ?>
	                        <tr>
	                            <td>#<?= $key ?></td>
	                            <td><?= $status[$value['order_status_id']] ?></td>
	                            <td><?= $value['comment'] ?></td>
	                        </tr>
	                        <?php } ?>
	                    </tbody>
	                </table>
	            </div>
	        </div>
	        <!--order status-->
    	</div>
	</div>
</div>
<!--order form-->

<!--footer-->
<div class="container-fluid">
    <div class="row top-head footer">
        <div class="col-sm-12 text-center back-button">
            <p><?= ($store_setting['footer'] != '') ? $store_setting['footer'] : __('store.all_rights_reserved')." ".date('Y')."."?></p>
        </div>
    </div>
</div>
<!--footer-->

<script>
	$(".print").on('click',function(){
		window.print();
	})
	function getUrlParameter(name) {
		name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
		var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
		results = regex.exec(location.search);
		return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
	}
	if(getUrlParameter('print')==1)  window.print();
</script>

</body>
</html>
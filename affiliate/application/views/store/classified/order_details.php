<link href="<?= base_url('assets/store/classified/'); ?>assets/css/thankyou.css" rel="stylesheet" />
<section class="about-wrap-layout1">
<div class="container">
<div class="profile-main">
<h2><?= __('store.order_details') ?> (#<?php echo orderId($order['id']); ?>)</h2>
   <div class="my-orders my-order-details">
      <div class="cart-wrapper">
         <ul class="cart-header">
            <li class="cart-item-price"><?= __('store.name') ?></li>
            <li><?= __('store.unit_price') ?></li>
            <li><?= __('store.quantity') ?></li>
            <li><?= __('store.total_discount') ?></li>
            <li><?= __('store.total') ?></li>
         </ul>

         <?php foreach ($products as $key => $product) { ?>
            <ul class="cart-items-row">
               <li>
                  <span class="my-orders-text img-order-details-img"><img src="<?= (!empty($product['image'])) ? $product['image'] : base_url('assets/store/default/img/1.png'); ?>" alt="<?= __('store.image') ?>">
                     <p class="my-0"><?php
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
                     <?= $product['product_name'] ?> <?= ($combinationString != "") ? "(".$combinationString.")" : "" ?>
                     <?php if($product['coupon_discount'] > 0){ ?>
                        <br><small class="couopn-code-text">
                           Code : <span class="c-name"> <?= $product['coupon_code'] ?></span> <?= __('store.applied') ?>
                        </small>
                     <?php } ?>
                     
                  </p>
               </span>
            </li>
            <li><span class="my-orders-text"><?php echo c_format($product['price'] + $product['variation_price']); ?></span>
               <li><span class="my-orders-text"><?php echo $product['quantity']; ?></span>
                  <li><span class="my-orders-text">
                  </span></span>
                  <li><span class="my-orders-text"><?php echo c_format($product['total']); ?></span>
                  </ul>

                  <?php if($order['status'] == 1 && $product['product_type'] == 'downloadable' && $product['downloadable_files']) { ?>
                     <ul class="cart-items-row">
                        <li class="download">
                    <?php foreach ($product['downloadable_files'] as $downloadable_filess) { ?>
                           <a class="btn btn-link btn-sm" href="<?php echo base_url('store/downloadable_file/'. $downloadable_filess['name'] . '/' .$downloadable_filess['mask'])."/".$order_id ?>" target="_blank"><?php echo $downloadable_filess['mask'] ?></a>
                          
                        <?php } ?>
                     </li>
                  </ul>
                     <?php } ?>
               <?php } ?>
               
               <ul class="cart-footer-row">
                  <?php foreach ($totals as $key => $total) { ?>
                     <li>
                        <span><?= $total['text'] ?></span>      
                        <span><?php echo c_format($total['value']); ?></span>     
                     </li>
                  <?php } ?>
               </ul>
            </div>
         </div>
         
         <div class="my-order-details-bottom">
            <div class="cart-wrapper order-details-bottom-left my-orders">
               <h2><?= __('store.order_payment_info') ?></h2>
               <ul class="cart-header">
                  <li><?= __('store.mode') ?></li>
                  <li class="cart-item-price"><?= __('store.transaction_id') ?></li>
                  <li><?= __('store.payment_status') ?></li>
               </ul>

               <?php if($order['status'] == 0){ ?>
                  <ul class="cart-items-row">
                     <li><span class="my-orders-text"> <?= __('store.waiting_for_payment_status') ?></span></li>
                  </ul>
               <?php } ?>
               <?php foreach ($payment_history as $key => $value) { ?>
                  <ul>
                     <li><span class="my-orders-text"><?php echo str_replace("_", " ", $value['payment_mode']) ?></span></li>
                     <li><span class="my-orders-text"><?php echo $order['txn_id'];?></span></li>
                     <li><span class="my-orders-text"><?php echo $value['paypal_status'] ?></span></li>
                  </ul>
               <?php } ?>

               <?php if($order['payment_method'] == 'bank_transfer'){ ?>
                  <div class="form-group">
                     <label class="control-label"><b><?= __('store.bank_transfer_instruction') ?></b></label>
                     <pre class="well"><?php echo $paymentsetting['bank_transfer_instruction'] ?></pre>
                  </div>
               <?php } ?>

               <?php if($order['comment']){ ?>
                  <div class="cart-wrapper order-details-bottom-left w-100 mt-4 my-orders">
                     <h2><?= __('store.order_view_comment') ?></h2>
                     <ul class="cart-header">
                        <li><?= __('store.title') ?></li>
                        <li><?= __('store.comment') ?></li>
                     </ul>
                     <?php foreach ($order['comment'] as $key => $value) { ?>
                        <ul class="cart-items-row">
                           <li><span class="my-orders-text"><?= $value['title'] ?></span> </li>    
                           <li><span class="my-orders-text"><?= $value['comment'] ?></span> </li>     
                        </ul>
                     <?php } ?>
                  </div>
               <?php } ?>

               <?php if($order['files']){ ?>
                  <div class="card mt-2 no-border">
                     <div class="card-body">
                        <label class="control-label"><b><?= __('store.order_attechments_download') ?></b></label>
                        <div><?php echo $order['files'] ?></div>
                     </div>
                  </div>
               <?php } ?>
               <?php if($order['order_country']){ ?>
                  <div class="card mt-2 no-border">
                     <div class="card-body">
                        <label class="control-label"><b><?= __('store.order_done_from') ?></b></label>
                        <div>
                           <?php echo $order['order_country'];?><?php echo $order['order_country_flag'];?>
                        </div>
                     </div>
                  </div>
               <?php  } ?>
               
               <?php if($orderProof){ ?>
                  <div class="card mt-2 no-border">
                     <div class="card-body">
                        <label class="control-label"><b><?= __('store.payment_proof') ?></b>
                           <a href="<?= $orderProof->downloadLink ?>" target='_blank'>: <?= __('store.download') ?></a>
                        </label>
                     </div>
                  </div>
               <?php } ?>
            </div>


            <?php if($order['allow_shipping']){ ?>
               <div class="cart-wrapper order-details-bottom-right my-orders">
                  <h2><?= __('store.shipping_details') ?></h2>
                  <ul class="cart-header">
                     <li>&nbsp;</li>
                     <li class="cart-item-price">&nbsp;</li>
                  </ul>
                  <ul class="list-group">
                     <li><span class="my-orders-text"><?= __('store.address') ?></span></li>
                     <li><span class="my-orders-text"><?php echo $order['address'] ?></span></li>
                  </ul>
                  <ul class="cart-items-row">
                     <li><span class="my-orders-text"><?= __('store.country') ?></span></li>
                     <li><span class="my-orders-text"><?php echo $order['country_name'] ?></span></li>
                  </ul>
                  <ul class="cart-items-row">
                     <li><span class="my-orders-text"><?= __('store.state') ?></span></li>
                     <li><span class="my-orders-text"><?php echo $order['state_name'] ?></span></li>
                  </ul>
                  <ul class="cart-items-row">
                     <li><span class="my-orders-text"><?= __('store.city') ?></span></li>
                     <li><span class="my-orders-text"><?php echo $order['city'] ?></span></li>
                  </ul>
                  <ul class="cart-items-row">
                     <li><span class="my-orders-text"><?= __('store.postal_code') ?></span></li>
                     <li><span class="my-orders-text"><?php echo $order['zip_code'] ?></span></li>
                  </ul>
               </div>
            <?php } ?>  
         </div>

         <h2 class="mt-5"><?= __('store.update_order_status') ?></h2>
         <div class="cart-wrapper order-details-bottom-left w-100 mt-4 order-details-last-bottom">
            <ul class="cart-header">
               <li>#</li>
               <li class="cart-item-price"><?= __('store.status') ?></li>
               <li><?= __('store.comment') ?></li>
            </ul>

            <?php if(!$order_history){ ?>
               <ul class="cart-items-row">
                  <li><span class="my-orders-text"><?= __('store.no_any_order_status') ?></span> </li>
               </ul>
            <?php } ?>
            <?php foreach ($order_history as $key => $value) { ?>
               <ul class="cart-items-row">
                  <li><span class="my-orders-text">#<?= $key ?></span> </li>     
                  <li><span class="my-orders-img-check"><?= $status[$value['order_status_id']] ?></span> </li>    
                  <li><span class="my-orders-text"><?= $value['comment'] ?></span> </li>        
               </ul>
            <?php } ?>
         </div>
         
         
      </div>

   </div>
</section>
               
   <section class="about-wrap-layout1">
      <div class="container">
         <div class="col-lg-10 col-sm-12 mx-auto">
            <div class="row">
               <div class="col-lg-12">
                  <div class="about-box-layout1">
                     <h2 class="item-title"><?= __('store.wishlist') ?></h2>
                  </div>
               </div>
               <div class="col-lg-12">
                  <div class="about-box-layout2">
                     <?php if (count($products)!=0): ?>
                        
                      
                        <table class="table">
                         <thead>
                          <tr>
                           <th scope="col"><?= __('store.image') ?></th>
                           <th scope="col"><?= __('store.product') ?></th>
                           <th scope="col"><?= __('store.actions') ?></th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php foreach ($products as $key => $value) {
                           $image = (!empty($value['product_featured_image'])) ? base_url('assets/images/product/upload/thumb/'. $value['product_featured_image']) : base_url('assets/store/default/').'img/product1.png';

                           ?>
                           <tr> 

                              <th scope="row"><img src="<?= $image; ?>" class="mr-2" width="50" height="50"/></th>
                              <td><?=$value['product_name']?></td>
                              
                              <td>
                                 <a type="button" href="<?=base_url('store/product/'.$value['product_id'])?>" class="btn btn-info">Details</a>
                                 <a type="button" class="btn btn-danger btn-remove"  data-id="<?=$value['product_id']?>" ><?= __('store.remove') ?></a>
                              </td>
                           </tr>
                        <?php }?>

                     </tbody>
                  </table>
               <?php else: ?>
                  <div class="row bg-white py-2 mb-2">
                     <div class="col-12 p-2 text-center">
                        <span class="wishlist-product-title ml-4"><?= __('store.no_wishlisted_products_available') ?></span>
                     </div>
                  </div>
               <?php endif ?>
            </div>
         </div>
      </div>
   </div>
</div>
</section>
<script type="text/javascript">
  $(document).on('click', '.btn-remove',function(){

     let product_id = $(this).attr('data-id');
     
     $.ajax({
       url:'<?= base_url('store/toggle_wishlist') ?>',
       type:'POST',
       dataType:'json',
       data: { product_id },
       success:function(json){
         window.location.reload()
      },
   });

  });
</script>
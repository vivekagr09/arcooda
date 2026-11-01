   <section class="about-wrap-layout1">
      <div class="container">
         <div class="col-lg-10 col-sm-12 mx-auto">
            <div class="row">
               <div class="col-lg-12">
                  <div class="about-box-layout1">
                     <h2 class="item-title"><?= __('store.orders') ?></h2>
                  </div>
               </div>
               <div class="col-lg-12">
                  <div class="about-box-layout2">
                     <table class="table">
                       <thead>
                         <tr>
                           <th scope="col"><?= __('store.order_id') ?></th>
                           <th scope="col"><?= __('store.price') ?></th>
                           <th scope="col"><?= __('store.order_status') ?></th>
                           <th scope="col"><?= __('store.payment_method') ?></th>
                           <th scope="col"><?= __('store.actions') ?></th>
                         </tr>
                       </thead>
                       <tbody>
                        <?php foreach ($orders as $key => $value) {  ?>
                               <tr>
                           <th scope="row"><?=$value['id']?></th>
                           <td><?=$value['total_sum']?></td>
                           <td><?=$value['last_status']?></td>
                           <td><?=$value['payment_method']?></td>
                           <td>
                              <a type="button" href="<?=base_url('store/order-details/'.encryptString($value['id']))?>" class="btn btn-info"><?= __('store.details')?></a>
                           </td>
                         </tr>
                       <?php }?>

                       </tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
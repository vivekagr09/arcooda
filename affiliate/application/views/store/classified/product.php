<section aff-section="classified_product_page"></section>
<script aff-template="classified_product_page" type="text/html">

   <section class="single-product-wrap-layout2 section-padding-equal-70 bg-accent">
      <div class="container">
         <div class="col-lg-10 col-sm-12 mx-auto">
            <div class="single-product-box-layout1">
               {{#product}}
               <div class="product-info light-shadow-bg">
                  <div class="product-content light-box-content">
                     <div class="item-img-gallery">
                        <div class="tab-content">
                           <div class="tab-pane fade show active" id="gallery1" role="tabpanel">
                              <a href="#">
                                 <img class="zoom_01" src="{{product_featured_image}}" alt="thumbnail">
                              </a>
                           </div>
                        </div>
                        <input type="hidden" id="product_id" value="{{product_id}}">
                        </div>
                        <div class="single-entry-meta">
                           <ul>
                              <li><i class="far fa-clock"></i>{{product_updated_date}}</li>
                              <li><i class="fas fa-user"></i> <a href="<?=base_url('store/productionstore/')?>{{product_created_by_base64}}" >{{product_created_by_name}}</a></li>
                              <li><i class="far fa-eye"></i>{{view}} views</li>
                           </ul>
                        </div>
                        <div class="row">
                           <div class="col-lg-6">
                              <div class="item-price">
                                 {{product_price}}
                              </div></div>
                              <div class="col-lg-6">
                                 <a href="{{product_url}}" class="float-end">   
                                    <button type="submit" class="buy-btn pull-end"><?= __('store.buy_now')?></button>
                                 </a>
                              </div>
                           </div>
                           <div class="item-details-2">
                              <div class="row">
                                 <div class="col-lg-8">
                                    <div class="left-side-content">
                                       <h3>{{product_name}}</h3>
                                       <p>{{product_description}}</p>
                                    </div>
                                 </div>
                                 <div class="col-lg-4">
                                    <div class="right-side-content">
                                       <ul class="item-meta">
                                          <li><span><?= __('store.category')?> :</span> {{product_category}}</li>
                                          <li><span><?= __('store.sku')?> :</span> {{product_sku}}</li>
                                       </ul>
                                       <ul class="item-action">
                                          <li><i class="far fa-eye"></i>{{view}} views</li>
                                          <li><a href="javascript:void(0);" id="btn-add-to-wishlist" class="<?=$is_wishlisted_class?>"><i class="<?=$is_wishlisted_class !='' ?'fa' : 'far'?> fa-heart"></i><?=$is_wishlisted_class !='' ?'Remove' : 'Add'?> to Favourites</a></li>

                                          <li class="item-social">
                                             <?php $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>
                                             <span style="cursor: pointer;" data-social-share data-share-url="<?= $actual_link;?>" class="share-title"><i class="fas fa-share-alt"></i><?= __('store.share')?></span>
                                          </li>
                                       </ul>
                                    </div>
                                    <a href="{{product_url}}" class="float-end">
                                       <button type="submit" class="buy-btn pull-end mt-4">
                                          <?= __('store.buy_now')?></button>
                                    </a>

                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     {{/product}}
                     <div class="item-related-product light-shadow-bg section-padding-top-heading ">
                        <div class="section-title-wrap margin-bottom-50">
                           <!-- section title -->
                           <div class="heading-layout1">
                              <h2 class="heading-title align-items-center">
                                 <?= __('store.related_ads')?></h2>
                           </div>
                           <div class="title-divider">
                              <div class="line"></div>
                              <i class="star-on fa fa-star"></i>
                              <div class="line"></div>
                           </div>
                        </div>
                        <div class="pupular-cat">
                           <div class="owl-carousel owl-theme" id="pupularCat">
                              {{#related_products}}
                              <div class="listing-wrapper row item">
                                 {{#.}}
                                 <div class="col-lg-3 col-md-4 col-sm-6" >
                                    <div class="product-box-layout1">
                                       <div class="item-img">
                                          <a class="{{#product_sale_is_on}}item-trending{{/product_sale_is_on}}" href="{{product_details_url}}">
                                             <img alt="Product" src="{{product_featured_image}}" />
                                          </a>
                                       </div>

                                       <div class="item-content">
                                          <ul class="entry-meta">
                                             <li><i class="fa fa-tags"></i>{{total_sale}} sold</li>
                                             <li><i class="fas fa-user"></i> {{product_created_by_name}}</li>
                                          </ul>

                                          <h3 class="item-title">
                                             <a class="item-img" href="{{product_details_url}}">{{product_name}}</a>
                                          </h3>

                                          <div class="item-price float-start">{{product_price}}</div>

                                       <div class="read-more-home-btn">
                                          <ul>
                                             <li class="float-start"><a href="{{product_details_url}}">Read more</a></li>
                                             <li class="float-end"><a href="{{product_url}}">
                                                <?= __('store.buy_now')?></a></li>
                                          </ul>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              {{/.}}
                           </div>
                           {{/related_products}}
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <?=$social_share_modal?>


   </script>

   <script type="text/javascript">

      function aff_prepare_classified_product_page(data) {
         data['related_products'] = createChunks(data['related_products'], 8);
         return data;
      }



      function createChunks(array, chunk_size) {
         let chunks = [];

         while (array.length > 0)
          chunks.push(array.splice(0, chunk_size));

       return chunks;
    }
    
      $(window).on('shown.bs.modal', function(){
       $('#social-share-modal').find('.close').addClass('btn')
    });
   

    $(document).on('click', '#btn-add-to-wishlist',function(){

       let status = $(this).hasClass('w-listed');
       console.log(status);
       $(this).toggleClass('w-listed');
       if(!status) {
         $(this).html('<i class="fa fa-heart"></i>Remove to Favourites')
      } else {
         $(this).html('<i class="far fa-heart"></i>Add to Favourites')  
      }
      $.ajax({
        url:'<?= base_url('store/toggle_wishlist') ?>',
        type:'POST',
        dataType:'json',
        data: { product_id : $("#product_id").val()},
        success:function(json){
            // do nothing
         },
      });

   });
</script>
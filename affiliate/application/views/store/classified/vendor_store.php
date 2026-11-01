<?php
$this->load->library('session');
$this->session->userdata['store_information'] = $store_details;
$store_meta = (!empty($store_details['store_meta'])) ? json_decode($store_details['store_meta'], true) : [];

   $image = isset($store_meta['store_logo']) && !empty($store_meta['store_logo']) ? 'assets/user_upload/vendor_store/' . $store_meta['store_logo'] : 'assets/store/default/img/ct-banner-img.png';
   
   $background_image = isset($store_meta['cover_background']) && !empty($store_meta['cover_background']) ? 'assets/user_upload/vendor_store/' . $store_meta['cover_background'] : 'assets/store/default/img/ctbg.png';
   $vendor_setting = $this->Product_model->getSettings('vendor');

  //echo "<pre>"; print_r($vendor_setting['storestatus']); exit;
?>

 
    <link rel="stylesheet" href="<?=base_url()?>assets/store/default/fonts/fonts.css?v=6.2" />
   
    <link rel="stylesheet" href="<?=base_url()?>assets/store/default/css/style.css?v=6.2" />

    <link rel="stylesheet" href="<?=base_url()?>assets/store/default/css/responsive.css?v=6.2 "/>

<section class="single-ctg-banner" style="background-image: url(<?= base_url($background_image) ?>);">
   <div class="container">
      <div class="banner-caption-ctg store">
        <div class="ctg-banner-img-wrapper"><img src="<?= base_url($image) ?>" alt="<?= $category['name'] ?>" width="306" height="100%"></div>
      <div class="text-caption" style="color:<?= isset($store_meta['cover_text_color']) ? $store_meta['cover_text_color'] : "#FFFFFF"; ?>">
         <h2 style="color: #fff"><?= $store_details['store_name'] ?></h2>       
         <?php if(isset($store_meta['cover_show_vendor_name']) && $store_meta['cover_show_vendor_name'] == 1) { ?>
         <h1><?= $store_details['store_owner'] ?></h1>
       <?php  } ?>
      </div>
     </div>
   </div>
</section>
<section class="container-fluid vendor-store-contact-section mt-2">
   
</section>
<section aff-section="classified_vendor_products"></section>
<script aff-template="classified_vendor_products" type="text/html">
   <section class="product-inner-wrap-layout1 bg-accent  section-padding-top-heading">
      <div class="container">
         <?php if ($vendor_setting['storestatus'] == 1) { ?>
         <div class="row">

            {{#filter}}
            <div class="col-xl-3 col-lg-4 sidebar-break-md sidebar-widget-area" id="accordion">
               
               <div class="card">
               <div class="card-body">
                  <h3 class="widget-bg-title"><?= __('store.contact_vendor') ?></h3>
                  <div class="sidebar-vendor-store position-relative">
                     <div class="vendor-profile-image">
                        <?php 
                              $vendor_store_image = ($store_details['avatar']) ? base_url('assets/images/users/'.$store_details['avatar']) : base_url('assets/vertical/assets/images/no-image.jpg');
                           ?>
                        <img src="<?= $vendor_store_image ?>" alt="<?= $store_details['store_owner'] ?>" width="100%" />
                     </div>
                     <div class="vendor-contact">
                        <p><?= $store_details['firstname'] ?></p>
                        <p><?= $store_details['lastname'] ?></p>
                        <div class="vendor-country">
                           <img alt="<?= __('store.image') ?>" src="<?= getFlag($store_details['country_code']) ?>"><?= $store_details['country_name'] ?> <?= ($store_details['state_name']) ? ','.$store_details['state_name'] : '' ?>
                        </div>
                        <a href="#" data-toggle="modal" data-target="#vendorModal"><?= __('store.contact_me') ?></a>
                     </div>
                  </div>
               </div>
            </div>
            
            <hr>
               <div class="widget-bottom-margin-md widget-accordian widget-filter">
                  <h3 class="widget-bg-title"><?= __('store.filter')?></h3>
                  <form id="filter-form" action="">
                     <div class="accordion-box">
                        {{#categories_filter.status}}
                        <div class="card filter-category multi-accordion filter-item-list">
                           <div class="card-header">
                              <a class="parent-list" role="button" data-toggle="collapse" href="#collapseTwo" aria-expanded="true">
                              <?= __('store.category')?>
                              </a>
                           </div>
                           <div id="collapseTwo" class="collapse show" data-parent="#accordion">
                              <div class="card-body">
                                 <div class="multi-accordion-content" id="accordion2">
                                    {{#categories_filter.data}}
                                    <div class="card">
                                       <div class="card-header">
                                          <a class="parent-list collapsed" role="button" data-toggle="collapse" href="#category-{{id}}" aria-expanded="false">
                                          <img src="{{image}}">
                                          {{name}} ({{product_count}})
                                          </a>
                                       </div>
                                       <div id="category-{{id}}" class="collapse" data-parent="#accordion2">
                                          <div class="card-body">
                                             <ul class="sub-list">
                                                <li><a href="javascript:void(0);" class="filter-and-sort-products" data-sort-key="aff_filter_category" data-sort-value="{{id}}">{{name}} ({{product_count}})</a></li>
                                                {{#childs}}
                                                <li><a href="javascript:void(0);" class="filter-and-sort-products" data-sort-key="aff_filter_category" data-sort-value="{{id}}">{{name}} ({{product_count}})</a></li>
                                                {{/childs}}
                                             </ul>
                                          </div>
                                       </div>
                                    </div>
                                    {{/categories_filter.data}}
                                 </div>
                              </div>
                           </div>
                        </div>
                        {{/categories_filter.status}}
                        {{#location_filter.status}}
                        <div class="card filter-location multi-accordion filter-item-list">
                           <div class="card-header">
                              <a class="parent-list" role="button" data-toggle="collapse" href="#collapseThree" aria-expanded="true">
                              <?= __('store.location')?>
                              </a>
                           </div>
                           <div id="collapseThree" class="collapse show" data-parent="#accordion">
                              <div class="card-body">
                                 <div class="multi-accordion-content" id="accordion3">
                                    {{#location_filter.data}}
                                    <div class="card">
                                       <div class="card-header">
                                          <a class="parent-list collapsed" role="button" data-toggle="collapse" href="#location{{id}}" aria-expanded="false">
                                          {{name}} ({{product_count}})
                                          </a>
                                       </div>
                                       <div id="location{{id}}" class="collapse" data-parent="#accordion3">
                                          <div class="card-body">
                                             <ul class="sub-list">
                                                {{#states}}
                                                <li><a href="javascript:void(0);" class="filter-and-sort-products" data-sort-key="aff_filter_location" data-sort-value="{{id}}">{{name}} ({{product_count}})</a></li>
                                                {{/states}}
                                             </ul>
                                          </div>
                                       </div>
                                    </div>
                                    {{/location_filter.data}}
                                 </div>
                              </div>
                           </div>
                        </div>
                        {{/location_filter.status}}
                        <div class="card filter-price-range filter-item-list">
                           <div class="card-header">
                              <a class="parent-list" role="button" data-toggle="collapse" href="#collapseFour" aria-expanded="true">
                              <?= __('store.category')?>
                              </a>
                           </div>
                           <div id="collapseFour" class="collapse show" data-parent="#accordion">
                              <div class="card-body">
                                 <div class="price-range-content">
                                    <div class="row">
                                       <div class="col-lg-6 form-group">
                                          <input type="number" name="aff_filter_price_min" class="form-control" placeholder="min" value="<?= $_GET['aff_filter_price_min']; ?>"/>
                                       </div>
                                       <div class="col-lg-6 form-group">
                                          <input type="number" name="aff_filter_price_max" class="form-control" placeholder="max" value="<?= $_GET['aff_filter_price_max']; ?>"/>
                                       </div>
                                       <div class="col-12 form-group">
                                          <input type="hidden" name="aff_filter_category" value="<?= $_GET['aff_filter_category']; ?>" />
                                          <input type="hidden" name="aff_filter_location" value="<?= $_GET['aff_filter_location']; ?>" />
                                          <input type="hidden" name="aff_sort_by" value="<?= $_GET['aff_sort_by']; ?>" />
                                          <button type="submit" class="filter-btn mt-4"><?= __('store.apply_filters')?></button>
                                          <span class="btn btn-block btn-outline-dark w-100 mt-2 btn-clear-filter"><?= __('store.clear_filters')?></span>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
            {{/filter}}
            <div class="col-xl-9 col-lg-8">
               {{#results_status}}
               <div class="product-filter-heading">
                  <div class="row align-items-center">
                     <div class="col-md-6">
                        
                        <h2 class="item-title">{{results_status}}</h2>
                        
                     </div>
                     <div class="col-md-6 d-flex justify-content-md-end justify-content-center">
                        <div class="product-sorting">
                           <div class="ordering-controller">
                              <button class="ordering-btn dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                              Sort By
                              </button>
                              <div class="dropdown-menu">
                                 <a class="dropdown-item filter-and-sort-products" data-sort-key="aff_sort_by" data-sort-value="1" href="javascript:void(0);">
                                    <?= __('store.a_to_z_title')?></a>
                                 <a class="dropdown-item filter-and-sort-products" data-sort-key="aff_sort_by" data-sort-value="2" href="javascript:void(0);">
                                 <?= __('store.z_to_a_title')?></a>
                                 <a class="dropdown-item filter-and-sort-products" data-sort-key="aff_sort_by" data-sort-value="3" href="javascript:void(0);">
                                 <?= __('store.data_added_newest')?></a>
                                 <a class="dropdown-item filter-and-sort-products" data-sort-key="aff_sort_by" data-sort-value="4" href="javascript:void(0);">
                                 <?= __('store.data_added_oldest')?></a>
                                 <a class="dropdown-item filter-and-sort-products" data-sort-key="aff_sort_by" data-sort-value="5" href="javascript:void(0);">
                                 <?= __('store.most_viewed')?></a>
                                 <a class="dropdown-item filter-and-sort-products" data-sort-key="aff_sort_by" data-sort-value="6" href="javascript:void(0);">
                                 <?= __('store.less_viewed')?></a>
                                 <a class="dropdown-item filter-and-sort-products" data-sort-key="aff_sort_by" data-sort-value="7" href="javascript:void(0);">
                                 <?= __('store.price_low_to_high')?></a>
                                 <a class="dropdown-item filter-and-sort-products" data-sort-key="aff_sort_by" data-sort-value="8" href="javascript:void(0);">
                                 <?= __('store.price_high_to_low')?></a>
                              </div>
                           </div>
                           <div class="layout-switcher">
                              <ul>
                                 <li class="active">
                                    <a href="#" data-type="product-box-list" class="product-view-trigger">
                                       <i class="fas fa-th-list"></i>
                                    </a>
                                 </li>
                                 <li>
                                    <a class="product-view-trigger" href="#" data-type="product-box-grid">
                                       <i class="fas fa-th-large"></i>
                                    </a>
                                 </li>
                              </ul>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               {{/results_status}}
               <div id="product-view" class="product-box-grid">
                  <div class="row">
                     {{#products}}
                     <div class="col-xl-4 col-md-6 d-none">
                        <div class="product-grid-view">
                           <div class="grid-view-layout1">
                              <div class="product-box-layout1 top-rated-grid">
                                 <div class="item-img">
                                    <a href="{{product_details_url}}" class="{{#product_sale_is_on}}item-trending{{/product_sale_is_on}}"><img src="{{product_featured_image}}" alt="Product"></a>
                                 </div>
                                 <div class="item-content">
                                    <h3 class="item-title"><a href="{{product_details_url}}">{{product_name}}</a></h3>
                                    <ul class="entry-meta">
                                       <li><i class="fa fa-tags"></i>{{total_sale}} <?= __('store.sold')?></li>
                                       <li><i class="fas fa-user"></i> <a href="<?=base_url('store/productionstore/')?>{{product_created_by_base64}}" >{{product_created_by_name}}</a></li>
                                    </ul>
                                    <div class="item-price">
                                       {{product_price}}
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="product-list-view">
                           <div class="list-view-layout1">
                              <div class="product-box-layout3 top-rated-list">
                                 <div class="item-img">
                                    <a href="{{product_details_url}}" class="{{#product_sale_is_on}}item-trending{{/product_sale_is_on}}"><img src="{{product_featured_image}}" alt="Product"></a>
                                 </div>
                                 <div class="product-info">
                                    <div class="item-content">
                                       <h3 class="item-title"><a href="{{product_details_url}}">{{product_name}}</a></h3>
                                       <ul class="item-condition">
                                          <li><span><?= __('store.category')?>:</span> {{product_category}}</li>
                                          <li><span><?= __('store.sku')?>:</span> {{product_sku}}</li>
                                       </ul>
                                       <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do dolore magna aliqua. In eu mi bibendum neque egestas…</p>
                                       <ul class="entry-meta">
                                          <li><i class="fa fa-tags"></i>{{total_sale}} sold</li>
                                          <li><i class="fas fa-user"></i> {{product_created_by_name}}</li>
                                          <li><i class="far fa-eye"></i>{{total_views}} Views</li>
                                       </ul>
                                    </div>
                                    <div class="item-right">
                                       <div class="item-price">
                                         {{product_price}}
                                       </div>
                                       <div class="item-btn">
                                          <a href="{{product_details_url}}"><?= __('store.details')?></a>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     {{/products}}
                     {{^products}}
                        <div class="alert alert-warning"><?= __('store.no_produts_found')?></div>
                     {{/products}}
                  </div>
               </div>
               {{#pagination}}
               <div class="pagination-layout1">
                  {{#previous}}
                     <div class="btn-prev">
                        <a href="{{previous}}"><i class="fas fa-angle-double-left"></i><?= __('store.previous')?></a>
                     </div>
                  {{/previous}}
                  {{^previous}}
                     <div class="btn-prev disabled">
                        <a href="javascript:void(0);"><i class="fas fa-angle-double-left"></i><?= __('store.previous')?></a>
                     </div>
                  {{/previous}}
                  
                  <div class="page-number">
                     {{#left_links}}
                     <a href="{{link}}">{{index}}</a>
                     {{/left_links}}
                     {{#current}}
                     <a href="{{link}}" class="active">{{index}}</a>
                     {{/current}}
                     {{#right_links}}
                     <a href="{{link}}">{{index}}</a>
                     {{/right_links}}
                  </div>

                  {{#next}}
                     <div class="btn-prev">
                        <a href="{{next}}"><?= __('store.next')?> <i class="fas fa-angle-double-right"></i></a>
                     </div>
                  {{/next}}
                  {{^next}}
                     <div class="btn-prev disabled">
                        <a href="javascript:void(0);"><?= __('store.next')?> <i class="fas fa-angle-double-right"></i></a>
                     </div>
                  {{/next}}
               </div>
               {{/pagination}}
            </div>
         </div>
         <?php }else{ ?>
            <div class="row">
               <div class="col-md-12">
                  <div class="text-center py-4"><p class="text-muted display-2"><?= __('store.sorry')  ?></p>
                  <p class="text-muted display-4"><?= __('store.no_product_avilable_to_store')  ?>...</p></div>
               </div>
            </div>
         <?php } ?>
      </div>
   </section>
</script>

<div class="modal fade" id="vendorModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center"><?= __('store.contact_me') ?></h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <div class="contact-inner-wrapper">
            <div class="row">
               <div class="col-12 col-md-6">
                  <div class="contact-form-wrapper">
                      <div class="cn-main">
                         <h2><?= __('store.contact_info') ?></h2>
                        <div class="cn-info-row">
                          <p><span class="cn-ifno-title"><?= __('store.phone') ?>:</span> <span><?= !empty($store_details['store_contact_number']) ? $store_details['store_contact_number'] : '';?></span></p>
                          <p><span class="cn-ifno-title"><?= __('store.email') ?>:</span> <span><?= !empty($store_details['store_email']) ? $store_details['store_email'] : '';?></span></p>
                          <p><span class="cn-ifno-title"><?= __('store.address') ?>:</span> <span><?= !empty($store_details['store_address']) ? $store_details['store_address'] : '';?></span></p>
                        </div>
                        <h2><?= __('store.contact_info') ?></h2>
                        
                        <form class="form-horizontal cn-main-form p-2" action="<?= base_url('classified/vendor_contact') ?>" method="post">
                           <input type="hidden" name="vendoremail" value="<?= !empty($store_details['store_email']) ? $store_details['store_email'] : '';?>"/>
                           <input type="hidden" name="vendor" value="<?= !empty($store_details['id']) ? $store_details['id'] : '';?>">
                           <div class="form-row">
                              <div class="form-group">
                                 <input name="name" type="text" placeholder="<?= __('store.your_name') ?>" class="form-control">
                                 <p class="error-message"></p>
                              </div>
                              <div class="form-group">
                                 <input name="email" type="text" placeholder="<?= __('store.your_email') ?>" class="form-control">
                                 <p class="error-message"></p>
                              </div>
                           </div>
                           <div class="form-group">
                              <input name="phone" type="text" placeholder="<?= __('store.your_phone') ?>" class="form-control">
                              <p class="error-message"></p>
                           </div>
                           <div class="form-group">
                              <textarea class="form-control" name="message" placeholder="<?= __('store.please_enter_your_message_here') ?>" rows="5"></textarea>
                              <p class="error-message"></p>
                           </div>
                           <div class="checkbox">
                               <label>
                                 <input type="checkbox" name="terms" value="1" class="mr-2 float-left" style="height: 25px; width: 25px;" checked />
                                    <a href="javascript:void(0);" class="vendor-store-terms-condition" target="_blank">
                                    <?= __('store.terms_n_conditions') ?>
                                 </a>
                                 <p class="error-message"></p>
                               </label>
                           </div>
                           <div class="form-group">
                              <input type="submit" class="btn cn-sbt-btn" value="<?= __('store.submit') ?>">
                           </div>
                        </form>
                      </div>
                     </div>
               </div>
               <div class="col-12 col-md-6">
                  <div class="contact-map">
                        <?php 
                           $iframe_link = !empty($store_details['store_contact_us_map']) ? $store_details['store_contact_us_map'] :
                           '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d55565170.29301636!2d-132.08532758867793!3d31.786060306224!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x54eab584e432360b%3A0x1c3bb99243deb742!2sUnited%20States!5e0!3m2!1sen!2sph!4v1592929054111!5m2!1sen!2sph" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>';
                           echo htmlspecialchars_decode($iframe_link);
                        ?>
                  </div>
               </div>
            </div>
            </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="vendorTermsConditionModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center"><?= __('store.terms_n_conditions') ?></h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <p>
            <?= !empty($store_details['store_terms_condition']) ? $store_details['store_terms_condition'] : __('store.vendor_store_terms_if_not_exist'); ?>
         </p>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
   $(".vendor-store-terms-condition").on('click',function(e){
      e.preventDefault();

      $("#vendorTermsConditionModal").modal('show');
   })
   $(".cn-sbt-btn").on('click',function(e){
          e.preventDefault();

          $this = $(this);
          $this.prop('disabled',true);
          let form = $(this).parents('form');
          let url = form.attr('action');

          $.ajax({
              type:'POST',
              dataType:'json',
              url:url,
              data:form.serialize(),
              success:function(result){
                        $("input").removeClass('error');
                        $(".error-message").text('');

                        if(result.validation){
                           $.each(result.validation,function(key,value){
                               $("[name='"+key+"']").addClass('error');
                               $("[name='"+key+"']").siblings('.error-message').text(value);
                           }) 
                        } else {
                           if(result.status){
                               form[0].reset();
                               Swal.fire({
                                   icon: 'success',
                                   html: result.message,
                               });
                           } else {
                               Swal.fire({
                                   icon: 'error',
                                   html: result.message,
                               });
                           }
                        }

                $this.prop('disabled',false);
              },
          }); 
      })
</script>


<script type="text/javascript">
   $(document).on('submit', '#filter-form', function () {
      $(this)
        .find('input[name]')
        .filter(function () {
            return !this.value;
        })
        .prop('name', '');
   });
</script>
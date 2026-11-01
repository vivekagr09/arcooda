<section aff-section="classified_catalog_page"></section>
<script aff-template="classified_catalog_page" type="text/html">
   <section class="product-inner-wrap-layout1 bg-accent  section-padding-top-heading">
      <div class="container">
         <div class="row">
            {{#filter}}
            <div class="col-xl-3 col-lg-4 sidebar-break-md sidebar-widget-area" id="accordion">
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
                                          {{name}} ({{product_count}})</a>
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
                                       <li><i class="fas fa-user"></i> {{product_created_by_name}}</li>
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
      </div>
   </section>
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
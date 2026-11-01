
<!--classified home -->
<section aff-section="classified_home_page"></section>
<script aff-template="classified_home_page" type="text/html">

	<section class="slide-home">
		<div class="owl-carousel owl-theme" id="slider-home">
			<div class="item">
				<section class="main-banner-wrap-layout1 bg-dark-overlay bg-common minus-mgt-90"
					data-bg-image="{{theme_sections.classifiedbannerimg}}">
					<div class="container">
						<div class="main-banner-box-layout1 animated-headline">
							<h2 class="ah-headline item-title">	{{theme_sections.classified_banner_title}}</h2>
							<div class="item-subtitle">{{theme_sections.classified_banner_subtitle}}</div>
							<div class="search-box-layout1">
					            {{#filter}}
								<form id="filter-form" action="<?php echo base_url('store/catalog') ?>">
									<div class="row no-gutters  align-items-center justify-content-center">
										<div class="col-lg-3 form-group dropdown">
											<div class="dropdown input-search-btn search-location">
											  <button id="dLabel" class="dropdown-select" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											   <i class="fas fa-map-marker-alt"></i>
											   <?= __('store.select_location')?>
											    <span class="caret"></span>
											  </button>
											  <ul class="dropdown-menu" aria-labelledby="dLabel">
											  	{{#countries}}
											    	<li data-sort-key="aff_filter_country" data-sort-value="{{id}}">{{name}}</li>
											    {{/countries}}
											  </ul>
											</div>
										</div>
										<div class="col-lg-3 form-group">
											<div class="dropdown input-search-btn search-category">
											  <button id="dLabel" class="dropdown-select" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											   <i class="fas fa-map-marker-alt"></i>
											   <?= __('store.select_category')?>
											    <span class="caret"></span>
											  </button>
											  <ul class="dropdown-menu" aria-labelledby="dLabel">
											    {{#categories}}
											   		<li data-sort-key="aff_filter_category" data-sort-value="{{id}}">{{name}}</li>
											    {{/categories}}
											  </ul>
											</div>
										</div>

										<div class="col-lg-4 form-group">
											<div class="input-search-btn search-keyword">
												<i class="fas fa-text-width"></i>
												<input type="text" class="form-control" placeholder="<?= __('store.enter_keyword_here...')?>" name="aff_filter_keyword">
											 </div>
										</div>

										<input type="hidden" name="aff_filter_country" value="">
										<input type="hidden" name="aff_filter_category" value="">
										<div class="col-lg-2 form-group"><button class="submit-btn" type="submit"><?= __('store.search')?></button></div>
									</div>
								</form>
								{{/filter}}
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>
	</section>

	<section class="listing-section section-padding-top-heading bg-accent">
		<div class="container">
			<!-- section container -->
			<div class="section-title-wrap margin-bottom-50">
				<div class="heading-layout1">
					<h2 class="heading-title align-items-center"><?= __('store.advertisements')?></h2>
				</div>

				<div class="title-divider">
					<div class="line"></div>

					<div class="line"></div>
				</div>
			</div>
			

			<div class="add-listing-wrapper">
				<div class="add-listing-nav shadow-1">
					<div class="row clearfix">
						<div class="col-md-12 col-sm-12 col-xs-12 float-start">
							<div class="listing-tabs">
								<ul class="nav nav-tabs" role="tablist">
									<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#latest-ads"
											role="tab"><?= __('store.latest_ads')?></a></li>
									<li class="nav-item"><a class="nav-link " data-toggle="tab" href="#discount-ads"
											role="tab"><?= __('store.discount_ads')?></a></li>
									<li class="nav-item"><a class="nav-link " data-toggle="tab" href="#popular-ads"
											role="tab"><?= __('store.popular_ads')?></a></li>
								</ul>
							</div>
						</div>
					</div>
				</div>

				<div class="listing-main tab-content padding-top-50 gridview pupular-cat">
					<div class="tab-pane fade active show" id="latest-ads" role="tabpanel">
						<div class="owl-carousel owl-theme product_slider">
							{{#latest_products}}
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
												<li><i class="fa fa-tags"></i>{{total_sale}} <?= __('store.sold')?></li>
												<li><i class="fas fa-user"></i> <a href="<?=base_url('store/productionstore/')?>{{product_created_by_base64}}" >{{product_created_by_name}}</a></li>
											</ul>

											<h3 class="item-title">
												<a class="item-img" href="{{product_details_url}}">{{product_name}}</a>
											</h3>

											<div class="item-price float-start">{{product_price}}</div>
											   
                                 			<div class="read-more-home-btn">
												<ul>
												 	<li class="float-start"><a href="{{product_details_url}}"><?= __('store.read_more')?>
												 </a></li>
													<li class="float-end"><a href="{{product_url}}"><?= __('store.buy_now')?></a></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
								{{/.}}
							</div>
							{{/latest_products}}

						</div>
					</div>
					<div class="tab-pane fade " id="discount-ads" role="tabpanel">
						<div class="owl-carousel owl-theme product_slider">
							{{#discount_products}}
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
												 	<li class="float-start"><a href="{{product_details_url}}"><?= __('store.read_more')?></a></li>
													<li class="float-end"><a href="{{product_url}}"><?= __('store.buy_now')?></a></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
								{{/.}}
							</div>
							{{/discount_products}}

						</div>
					</div>
					<div class="tab-pane fade " id="popular-ads" role="tabpanel">
						<div class="owl-carousel owl-theme product_slider">
							{{#popular_products}}
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
												<li><i class="fa fa-tags"></i>{{total_sale}} <?= __('store.sold')?></li>
												<li><i class="fas fa-user"></i> {{product_created_by_name}}</li>
											</ul>

											<h3 class="item-title">
												<a class="item-img" href="{{product_details_url}}">{{product_name}}</a>
											</h3>

											<div class="item-price float-start">{{product_price}}</div>
											
					             			<div class="read-more-home-btn">
												<ul>
												 	<li class="float-start"><a href="{{product_details_url}}"><?= __('store.read_more')?></a></li>
													<li class="float-end"><a href="{{product_url}}"><?= __('store.buy_now')?></a></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
								{{/.}}
							</div>
							{{/popular_products}}
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- section container end -->
	</section>

	<section class="section-padding-top-heading">
		<div class="container">
			<div class="section-title-wrap margin-bottom-50">
				<!-- section title -->
				<div class="heading-layout1">
					<h2 class="heading-title align-items-center"><?= __('store.popular_categories')?></h2>
				</div>

				<div class="title-divider">
					<div class="line"></div>

					<div class="line"></div>
				</div>
			</div>

			<div class="category_slider">
				<div class="row justify-content-center">
					{{#popular_categories}}
					<div class="col-lg-2">
						<div class="category-box-layout1 shadow-1">
							<div class="item-icon align-items-center mb-2">
								<a href="javascript:void(0);" data-sort-key="aff_filter_category" data-sort-value="{{id}}" data-sort="true">
									<img src="{{image}}" alt="category" style="width:50px;height:50px" />
								</a>
							</div>
							<div class="item-content">
								<h3 class="item-title"><a href="javascript:void(0);" data-sort-key="aff_filter_category" data-sort-value="{{id}}" data-sort="true">{{name}}</a></h3>
								<div class="item-count"><a href="javascript:void(0);" data-sort-key="aff_filter_category" data-sort-value="{{id}}" data-sort="true">{{products_count}} <?= __('store.products')?></a></div>
							</div>
						</div>
					</div>
					{{/popular_categories}}
					
				</div>
			</div>
		</div>
	</section>

	<section class="section-padding-top-heading ">
		<div class="container">
			<div class="section-title-wrap margin-bottom-50">
				<!-- section title -->
				<div class="heading-layout1">
					<h2 class="heading-title align-items-center"><?= __('store.featured_ads')?></h2>
				</div>

				<div class="title-divider">
					<div class="line"></div>

					<div class="line"></div>
				</div>
			</div>

			<div class="pupular-cat">
				<div class="owl-carousel owl-theme" id="pupularCat">
					{{#featured_products}}
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
										<li><i class="fa fa-tags"></i>{{total_sale}} <?= __('store.sold')?></li>
										<li><i class="fas fa-user"></i> {{product_created_by_name}}</li>
									</ul>

									<h3 class="item-title">
										<a class="item-img" href="{{product_details_url}}">{{product_name}}</a>
									</h3>

									<div class="item-price float-start">{{product_price}}</div>
									   
                         			<div class="read-more-home-btn">
										<ul>
										 	<li class="float-start"><a href="{{product_details_url}}"><?= __('store.read_more')?></a></li>
											<li class="float-end"><a href="{{product_url}}"><?= __('store.buy_now')?></a></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						{{/.}}
					</div>
					{{/featured_products}}
				</div>
			</div>
		</div>
	</section>
	{{#launching_products_available}}
	<section class="premiumAds pupular-cat bg-accent">
		<div class="container">
			<div class="owl-carousel owl-theme " id="premusAds">
				{{#launching_products}}
				<div class="item">
					<div class="listing-item clearfix">
						<div class="figure">
							<img alt="listing item" src="{{product_featured_image}}" width="156" height="127">
						</div>

						<div class="listing-content clearfix">
							<div class="listing-title">
								<h6><a href="#">{{product_name}}</a></h6>
							</div>

							<div class="listing-location float-start">
		                        <a href="#"><i class="fas fa-calendar-alt"></i><?= __('store.launching_on')?>: {{product_launch_date}}</a>
							</div>
						</div>
					</div>

					<div class="listing-border-bottom bgorange-1"></div>
				</div>
				{{/launching_products}}
			</div>
		</div>
	</section>
	{{/launching_products_available}}
</script>


<script type="text/javascript">
	function aff_prepare_classified_home_page(data) 
	{
		data['latest_products'] = createChunks(data['latest_products'], 8);
		data['discount_products'] = createChunks(data['discount_products'], 8);
		data['popular_products'] = createChunks(data['popular_products'], 8);
		data['featured_products'] = createChunks(data['featured_products'], 8);
		return data;
	}

	function createChunks(array, chunk_size) {
		let chunks = [];
		while (array.length > 0)
		  chunks.push(array.splice(0, chunk_size));
		return chunks;
	}

	$(document).on('submit', '#filter-form', function () {
		$(this)
		.find('input[name]')
		.filter(function () {
		    return !this.value;
		})
		.prop('name', '');
	});

	$(document).on('click', '[data-sort-key]', function () {
		$('input[name="'+$(this).data('sort-key')+'"]').val($(this).data('sort-value'));

		console.log($(this).data('sort') );

		if($(this).data('sort') == true) {
			$('input[name="'+$(this).data('sort-key')+'"]').closest('form').submit();
		}
	});


	
</script>
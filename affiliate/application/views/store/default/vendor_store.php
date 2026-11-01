<?php 
	$store_meta = (!empty($store_details['store_meta'])) ? json_decode($store_details['store_meta'], true) : [];

	$image = isset($store_meta['store_logo']) && !empty($store_meta['store_logo']) ? 'assets/user_upload/vendor_store/' . $store_meta['store_logo'] : 'assets/store/default/img/ct-banner-img.png';
	
	$background_image = isset($store_meta['cover_background']) && !empty($store_meta['cover_background']) ? 'assets/user_upload/vendor_store/' . $store_meta['cover_background'] : 'assets/store/default/img/ctbg.png';

?>
<section class="single-ctg-banner" style="background-image: url(<?= base_url($background_image) ?>);">
   <div class="container">
      <div class="banner-caption-ctg store">
        <div class="ctg-banner-img-wrapper"><img src="<?= base_url($image) ?>" alt="<?= $category['name'] ?>" width="306" height="100%"></div>
		<div class="text-caption" style="color:<?= isset($store_meta['cover_text_color']) ? $store_meta['cover_text_color'] : "#FFFFFF"; ?>">
		   <h2><?= $store_details['store_name'] ?></h2>		   
			<?php if(isset($store_meta['cover_show_vendor_name']) && $store_meta['cover_show_vendor_name'] == 1) { ?>
		   <h1><?= $store_details['store_owner'] ?></h1>
		 <?php  } ?>
		</div>
	  </div>
   </div>
</section>

<section class="container-fluid vendor-store-contact-section mt-2">
	<div class="card">
		<div class="card-body">
			<h4><?= __('store.contact_vendor') ?></h4>
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
</section>

<section class="product-category-page" style="display: none;">
   <div class="container" >
   	<div class="category-row">
			<div class="sidebar">
				
				<div class="sidebar-block mt-4">
					<h2><?= __('store.contact_vendor') ?></h2>
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

				<div class="sidebar-block mt-4">
					<h2><?= __('store.refine_by') ?></h2>
					<div class="sidebar-search position-relative">
						<input id="searchProduct" type="text" placeholder="Enter Keywords">
						<img src="<?= base_url('assets/store/default/'); ?>img/cancel.png" class="cancel-img" alt="<?= __('store.cancel') ?>">
						<a href="javascript:void(0);" id="clear-all-search"><?= __('store.clear_all') ?></a>
					</div>
				</div>
				
				<div class="sidebar-block mt-4">
					<h2><?= __('store.price') ?></h2>
					<div class="price-sidebar-slider">
						<div id="slider-range"></div>
						<div class="price-caption">
							<span><?= __('store.price') ?>:</span>
							<span id="slider-range-value1"></span>
							<span class="dashed"> &nbsp;  - &nbsp; </span>
							<span id="slider-range-value2"></span>
						</div>
						<a href="javascript:void(0);" id="filter-price-range"><?= __('store.filter') ?></a>
						<form>
							<input type="hidden" name="min-value" value="0">
							<input type="hidden" name="max-value" value="10000">
						</form>
					</div>
				</div>
				
				<div class="sidebar-block mt-4">
					<h2><?= __('store.product_rating') ?></h2>
					<div class="sidebar-rating-filter">
						<div class="filter-rating-row">
							<div class="d-flex">
							<input type="radio" name="rating-filter" value="5">
							<div class="rating-images">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st.png">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st.png">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st.png">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st.png">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st.png">
							</div>
							</div>
							<span>(5)</span>
						</div>
						
						<div class="filter-rating-row">
							<div class="d-flex">
							<input type="radio" name="rating-filter" value="4">
							<div class="rating-images">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st.png">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st.png">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st.png">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st.png">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st1.png">
							</div>
							</div>
							<span>(4)</span>
						</div>
						
						<div class="filter-rating-row">
							<div class="d-flex">
							<input type="radio" name="rating-filter" value="3">
							<div class="rating-images">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st.png">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st.png">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st.png">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st1.png">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st1.png">
							</div>
							</div>
							<span>(3)</span>
						</div>
						
						<div class="filter-rating-row">
							<div class="d-flex">
							<input type="radio" name="rating-filter" value="2">
							<div class="rating-images">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st.png">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st.png">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st1.png">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st1.png">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st1.png">
							</div>
							</div>
							<span>(2)</span>
						</div>
						
						<div class="filter-rating-row">
							<div class="d-flex">
							<input type="radio" name="rating-filter" value="1">
							<div class="rating-images">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st.png">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st1.png">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st1.png">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st1.png">
								<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/st1.png">
							</div>
							</div>
							<span>(1)</span>
						</div>
						<a href="javascript:void(0);" id="clear-rating-filter"><?= __('store.clear_all') ?></a>
					</div>
				</div>
				

				<?php 
				$store_setting = $this->Product_model->getSettings('store');
				if($store_setting['is_variation_filter']){ 
				  ?>
				<div class="sidebar-block mt-4">
					<h2><?= __('store.color') ?></h2>
					<div class="sidebar-colors">
					<?php 
						if(isset($colors) && is_array($colors) && sizeOf($colors) > 0) {
							for ($i=0; $i < sizeOf($colors); $i++) { 
								echo '<span data-color="'.$colors[$i].'" style="background: '.$colors[$i].'"></span>';
							} 
						} else {
							?>
							<span style="background: #BE0027"></span>
							<span style="background: #CF8D2E"></span>
							<span style="background: #E4E932"></span>
							<span style="background: #371777"></span>
							<span style="background: #037EF3"></span>
							<span style="background: #BE0027"></span>
							<span style="background: #CF8D2E"></span>
							<span style="background: #E4E932"></span>
							<span style="background: #371777"></span>
							<span style="background: #037EF3"></span>
							<?php
						}
					?>
					</div>
				</div>
				  <?php
				}
				?>
				
				<div class="sidebar-block mt-4">
					<h2><?= __('store.product_tag') ?></h2>
					<div class="sidebar-tags">
						<?php 
								if(isset($tags) && is_array($tags) && sizeOf($tags) > 0) {
								for ($i=0; $i < sizeOf($tags); $i++) { 
									echo '<a href="javaScript:void(0);" data-tag="'.$tags[$i].'">'.$tags[$i].'</a>';
								} 
							} else {
								?>
								<a href="javaScript:void(0);">Lorem Ipsum</a>
								<a href="javaScript:void(0);" class="active">Lorem Ipsum</a>
								<a href="javaScript:void(0);">Lorem Ipsum</a>
								<a href="javaScript:void(0);">Lorem Ipsum</a>
								<a href="javaScript:void(0);">Lorem Ipsum</a>
								<?php
							}
						?>
					</div>
				</div>
				
			</div>
			
			
			
			<div class="catg-property-wrapper">
				
			<div class="inner-pages-breadcrumb">
				<h2><?= $store_details['store_name'] ?></h2>
				<p><a href="<?= $home_link ?>"><?= __('store.home') ?></a> / <a href="<?= $base_url.$store_details['store_slug'] ?>"><?= $store_details['store_name'] ?></a></p>
			</div>
				
			<div class="product-sort-row">
				<p><span></span> <?= __('store.showing') ?> <small id="show-count">0</small> / <small id="total-count">15</small> <?= __('store.results') ?></p>
				<div class="sort-filter">
					<label><?= __('store.sort_by') ?>: </label>
					<select id="sort-by">
						<option value="popular" selected><?= __('store.popular_products') ?></option>
						<option value="low-to-high"><?= __('store.price_low_to_high') ?></option>
						<option value="high-to-low"><?= __('store.price_high_to_low') ?></option>
						<option value="latest"><?= __('store.newest_first') ?></option>
					</select>
				</div>
			</div>
				
			<div class="product-row d-flex flex-wrap product-list">
			</div>
			<a href="javascript:void(0);" class="see-more" data-next_page="1"><img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/loading.png"><?= __('store.show_more') ?></a></div>
		</div>
	</div>
</section>


<?php include 'product-list-template.php';  ?>

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
								
								<form class="form-horizontal cn-main-form p-2" action="<?= base_url('store/vendor_contact') ?>" method="post">
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
		$(document).ready(function() {
			load_Product($('#searchProduct').val(), {
				category_slug : '<?= (isset($category['slug'])) ? $category['slug'] : ""; ?>',
			});

			$(document).on('click', '.sidebar-tags a', function() {
				$(this).toggleClass('active');
				load_Product($('#searchProduct').val(), {
					next_page: $(this).data('next_page'),
				});
			});


			$(document).on('click', '.sidebar-colors span', function() {
				$(this).toggleClass('active');
				load_Product($('#searchProduct').val(), {
					next_page: $(this).data('next_page'),
				});
			});
			
			
			$(document).on('click', '.see-more', function() {
				load_Product($('#searchProduct').val(), {
					next_page: $(this).data('next_page'),
				});
			});

			$('#searchProduct').keyup(function(e) {
				e.preventDefault();
				var search = $(this).val();
				load_Product(search);
			});

			$(document).on('click', ".category_block a", function(e){ 
				e.stopPropagation(); 
			});

			$(document).on('click', ".category_block .has-children", function(e){
				e.stopPropagation();
				$(this).find("> ul").slideToggle();
			});

			$(document).on('change', '#sort-by', function(){
				load_Product($('#searchProduct').val());
			});

			$(document).on('click', '#filter-price-range', function(){
				load_Product($('#searchProduct').val());
			});

			$(document).on('click', 'input[name="rating-filter"]', function(){
				load_Product($('#searchProduct').val());
			});

			$(document).on('click', '#clear-rating-filter', function(){
				$('input[name="rating-filter"]:checked').prop('checked', false);
				load_Product($('#searchProduct').val());
			});

			$(document).on('click', '#clear-all-search', function(){
				$('input[name="rating-filter"]:checked').prop('checked', false);
				$('#searchProduct').val('');
				load_Product($('#searchProduct').val());
			});

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


		});

		function load_Product(search, postData = {}) {
			var data = postData;
			data.created_by = <?= $store_details['id']; ?>;
			data.search = search;
			data.order_by = $('#sort-by').val();
			data.min_price = $('input[name="min-value"]').val();
			data.max_price = $('input[name="max-value"]').val();
			if($('input[name="rating-filter"]:checked').length){
				data.product_avg_rating = $('input[name="rating-filter"]:checked').val();
			}

			data.colors = [];
			data.tags = [];

			$('.sidebar-tags a').each(function( index ) {
				if($(this).hasClass('active')){
					data.tags.push($(this).data('tag'));
				}
			});

			$('.sidebar-colors span').each(function( index ) {
				if($(this).hasClass('active')){
					data.colors.push($(this).data('color'));
				}
			});

			data.request_page = 'category';
			data.limit = 15;
			var ajaxReq = 'ToCancelPrevReq';
			var ajaxReq = $.ajax({
				url: "<?= base_url() ?>" + 'Store/load_Product',
				type: 'POST',
				dataType: 'JSON',
				data: data,
				beforeSend : function() {
					if(ajaxReq != 'ToCancelPrevReq' && ajaxReq.readyState < 4) {
						ajaxReq.abort();
					}
					$('.btn-search').addClass('btn-loading');
				},
				complete : function() {
					$('.btn-search').removeClass('btn-loading');
					$('.product-category-page').show();
				},
				success: function(res) {
					if(res.category) {

						if(postData.next_page && postData.next_page > 1) {
							$('.product-list').append(Mustache.render($('#product-list-template').html(), res.category));
						} else {
							$('.product-list').html(Mustache.render($('#product-list-template').html(), res.category));
						}

						$('.see-more').data('next_page', res.category.next_page);
						
						if(res.category.is_last_page) {
							$('.see-more').hide();
						}

						if(res.category.total_count) {
							$('#total-count').text(res.category.total_count);
						}

						if(postData.next_page && postData.next_page > 1){
							$('#show-count').text((parseInt($('#show-count').text())+res.category.count));
						} else {
							$('#show-count').text(res.category.count);
						};
					}

					if(res.category.products.length <= 0) {
	   				let NoProductHtml = `<div class="text-center py-4"><p class="text-muted display-2">`+'<?= __('store.sorry')  ?>'+`</p>
	   				<p class="text-muted display-4">`+'<?= __('store.no_product_avilable_to_store')  ?>'+`...</p></div>`;
	   				$('.product-category-page .container').html(NoProductHtml);
					}
				}
			});
		}

		<?php if($category) { ?>
			var c = $('[data-id="<?= $category['id'] ?>"]').parents("li");
			var ele = c[c.length-1];
		<?php } ?>

		$(document).ready(function() {
		$('.noUi-handle').on('click', function() {
			$(this).width(50);
		});
		var rangeSlider = document.getElementById('slider-range');
		var moneyFormat = wNumb({
			decimals: 0,
			thousand: ',',
			prefix: $('a[data-currency-symbol]').data('currency-symbol'),
			edit: function(value){
				if(value == "$10,000") {
					return "$10,000 +";
				} else {
					return value;
				}
			}
		});
		
		noUiSlider.create(rangeSlider, {
			start: [0, 10000],
			step: 50,
			range: {
			'min': [0],
			'max': [10000]
			},
			format: moneyFormat,
			connect: true
		});
	
		// Set visual min and max values and also update value hidden form inputs
		rangeSlider.noUiSlider.on('update', function(values, handle) {
			document.getElementById('slider-range-value1').innerHTML = values[0];
			document.getElementById('slider-range-value2').innerHTML = values[1];
			$('input[name="min-value"]').val(moneyFormat.from(values[0]));
			$('input[name="max-value"]').val(moneyFormat.from(values[1]));
			console.log(moneyFormat.from(values[0]));
			console.log(moneyFormat.from(values[1]));
		});
	});


</script>
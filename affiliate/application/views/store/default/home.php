<!-- Banner  -->
<section class="banner-slider">
	<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
		<div class="carousel-inner text-center text-white">
		<?php
		$homepage_slider = json_decode($store_setting['homepage_slider']);
		for ($i=0; $i < sizeof($homepage_slider); $i++) { 
			$homepage_slider_available = true;
		?>
			<div class="carousel-item <?= ($i==0) ? 'active' : ''; ?>">
				<div class="banner-caption" style="background-image: url(<?= (!empty($homepage_slider[$i]->slider_background_image)) ? base_url('assets/images/site/'. $homepage_slider[$i]->slider_background_image) : base_url('assets/store/default/img/banner.png') ?>);">
					<div class="banner-caption-inner">

					<?php $slider_text_color = (!empty($homepage_slider[$i]->slider_text_color)) ? $homepage_slider[$i]->slider_text_color : '#FFF'; ?>

					<h1 class="display-4" style="color: <?= $slider_text_color ?> !important;">
						<?= htmlentities($homepage_slider[$i]->title, ENT_QUOTES); ?><br/>
						<?= htmlentities($homepage_slider[$i]->sub_title, ENT_QUOTES); ?>
					</h1>
					
					<?= (!empty($homepage_slider[$i]->content)) ? '<p style="color: '.$slider_text_color.' !important;">'.htmlentities($homepage_slider[$i]->content, ENT_QUOTES).'</p>' : '' ?>

					<?php if(!empty($homepage_slider[$i]->button_text)) { ?>
						<a href="<?= $homepage_slider[$i]->button_link; ?>" class="btn btn-main bg-white color mt-4" style="color: <?= (!empty($homepage_slider[$i]->button_text_color)) ? $homepage_slider[$i]->button_text_color : '#FFF' ?> !important; background-color: <?= (!empty($homepage_slider[$i]->button_bg_color)) ? $homepage_slider[$i]->button_bg_color : '#FFF' ?> !important;"><?= $homepage_slider[$i]->button_text; ?>&nbsp;<i class="fa fa-angle-right" aria-hidden="true"></i></a>
					<?php } ?>
					</div>
				</div>
			</div>
		<?php	
		}

		// dummy homepage slide if not available
		if(!isset($homepage_slider_available)) {
			?>
			<div class="carousel-item active">
				<div class="banner-caption" style="background-image: url(<?= base_url('assets/store/default/img/banner.png') ?>);">
					<div class="banner-caption-inner">
						<h1>Are you ready to <span> lead the way </span></h1>
						<p>
						Lorem Ipsum has been the industry's standard dummy text ever
						since the 1500s, when an unknown printer took a galley of type
						and scrambled it to make a type specimen book.
						</p>						
						<a href="#" class="btn btn-main bg-white color" data-toggle="modal" data-target="#buyModel"><?= __('store.buy_now') ?> &nbsp;<i class="fa fa-angle-right" aria-hidden="true"></i></a>
					</div>
				</div>
			</div>
			<?php
		}

		?>
		</div>
		<a class="carousel-control-prev bg-main2" href="#carouselExampleControls" role="button" data-slide="prev">
			<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/slider-arrow.png" />
		</a>
		<a class="carousel-control-next bg-main2" href="#carouselExampleControls" role="button" data-slide="next">
			<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/slider-arrow.png" />
		</a>
	</div>
</section>

<!-- Top tags -->
<section class="category-tag">
	<div class="container">
		<h3><?= __('store.top_tags') ?>:</h3>
		<ul class="category-listing">
			<?php if ($category_tags){ ?>
			<?php foreach ($category_tags as $key => $category_tag) { ?>
			<?php $has_top_tags = true; ?>
			<li><a href="<?= base_url('store/category/'. $category_tag['slug']) ?>"><?= $category_tag['name'] ?></a></li>
			<?php } ?>
			<?php } ?>
		
			<?php if (!isset($has_top_tags)) { ?>
				<li class="demo-cat-badge"><a href="#">Lorem Ipsume 1</a></li>
				<li class="demo-cat-badge"><a href="#">Lorem Ipsume 2</a></li>
				<li class="demo-cat-badge"><a href="#">Lorem Ipsume 3</a></li>
			<?php } ?>

			<li><a href="<?= $base_url ?>category" class="bg-main">+ <?= __('store.see_all_categories') ?></a></li>
		</ul>
	</div>
</section>
<!-- home page product grid -->
<section class="home-product-grid">
	<div class="container">
		<div class="home-trend-top d-flex justify-content-between">
			<h2 class="section-title">
			<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/package-b.png" /> <?= __('store.trending_products') ?>
			</h2>
			<div class="searchbox">
			<input id="searchProduct" type="text" placeholder="<?= __('store.search') ?>" />
			<img src="<?= base_url('assets/store/default/'); ?>img/search.png" class="search-icon-home" alt="<?= __('store.search') ?>">
			</div>
		</div>

		<div class="product-row d-flex flex-wrap product-list-trending">
			
		</div>
		<a href="javascript:void(0);" class="see-more see-more-trendings" data-next_page="1" data-request_page_section="trending">
			<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/loading.png" /> <?= __('store.show_more') ?>
		</a>
	</div>
</section>


<?php
$homepage_features = (isset($store_setting['homepage_features']) && !empty($store_setting['homepage_features'])) ? json_decode($store_setting['homepage_features']) : [];
?>
<!-- Home page feature box -->
<section class="stats-home">
	<div class="container">
		<div class="stats-row d-flex justify-content-center">
		<?php
		foreach($homepage_features as $hf) {
			$homepage_features_are_available = true;
		?>
			<?php $img = (!empty($hf->feature_image)) ? base_url('assets/images/site/'. $hf->feature_image) : base_url('assets/store/default/img/stats1.png'); ?>
			<div class="stats-box d-flex align-items-center mx-4">
			<div class="stats-icon">
				<img alt="<?= __('store.image') ?>" src="<?= $img; ?>" style="width: 65px; height: 65px;"/>
			</div>
			<div class="stats-text">
				<h4><?= $hf->title; ?></h4>
				<p><?= $hf->sub_title; ?></p>
			</div>
			</div>
		<?php	
		}

		if(!isset($homepage_features_are_available)) {
		?>
		<div class="stats-box d-flex align-items-center mx-4">
		<div class="stats-icon">
		<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/')?>img/stats1.png" style="width: 65px; height: 65px;"/>
		</div>
		<div class="stats-text">
		<h4><?= __('store.free_shipping') ?></h4>
		<p><?= __('store.free_shipping_all_order') ?></p>
		</div>
		</div>

		<div class="stats-box d-flex align-items-center mx-4">
		<div class="stats-icon">
		<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/')?>img/stats2.png" style="width: 65px; height: 65px;"/>
		</div>
		<div class="stats-text">
		<h4><?= __('store.100_money_guarantee') ?></h4>
		<p><?= __('store.30_days_money_back') ?></p>
		</div>
		</div>

		<div class="stats-box d-flex align-items-center mx-4">
		<div class="stats-icon">
		<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/')?>img/stats3.png" style="width: 65px; height: 65px;"/>
		</div>
		<div class="stats-text">
		<h4><?= __('store.help_center') ?></h4>
		<p><?= __('store.24_7_support_system') ?></p>
		</div>
		</div>

		<div class="stats-box d-flex align-items-center mx-4">
		<div class="stats-icon">
		<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/')?>img/stats4.png" style="width: 65px; height: 65px;"/>
		</div>
		<div class="stats-text">
		<h4><?= __('store.payment_method') ?></h4>
		<p><?= __('store.secure_payment') ?></p>
		</div>
		</div>
		<?php
		}
		?>
		</div>
	</div>
</section>


<!-- New product box -->
<section class="home-new-products">
	<div class="container">
	<div class="home-trend-top d-flex justify-content-between">
		<h2 class="section-title color2">
		<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/package.png" /> <?= __('store.new_products') ?>
		</h2>
		<ul class="category-listing">
		<li class="demo-cat-badge"><a href="#">Lorem Ipsume 1</a></li>
		<li class="demo-cat-badge"><a href="#">Lorem Ipsume 2</a></li>
		<li class="demo-cat-badge"><a href="#">Lorem Ipsume 3</a></li>
		</ul>
	</div>

	<div class="product-row d-flex flex-wrap product-list-new">
	</div>

	<a href="javascript:void(0);" class="see-more see-more-new" data-next_page="1" data-request_page_section="new">
	<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/loading.png" /> <?= __('store.show_more') ?>
	</a>
	</div>
</section>

<section class="banner-ads">
	<?php if(isset($settings['hbanimage']) && $settings['hbanimage'] != ""){?>
		<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/images/site/'); ?><?= $settings['hbanimage'];?>" class="img-fluid img-banner-ads" />	
	<?php }else{ ?>
		<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/ad-bg.jpg" class="img-fluid img-banner-ads" />
	<?php }?>
	

	<?php $homepage_banner = (isset($store_setting['homepage_banner'])) ? json_decode($store_setting['homepage_banner']) : []; ?>

	<div class="ad-caption">
	<h3><?= (isset($homepage_banner->title) && !empty($homepage_banner->title)) ? $homepage_banner->title : 'LOREM IPSUM'; ?></h3>
	<p><?= (isset($homepage_banner->content) && !empty($homepage_banner->content)) ? $homepage_banner->content : 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.'; ?></p>
	<a href="<?= (isset($homepage_banner->button_link) && !empty($homepage_banner->button_link)) ? $homepage_banner->button_link : '#'; ?>"><?= (isset($homepage_banner->button_text) && !empty($homepage_banner->button_text)) ? $homepage_banner->button_text : 'Lorem Ipsum'; ?></a>
	</div>
</section>

<section class="main-categories">
	<div class="container">
		<h2 class="section-title">
			<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/package-b.png" /> <?= __('store.categories') ?>
		</h2>

		<div class="categories-listing-row d-flex">
			<?php


			if(!empty($category))
			{
				foreach ($category as $cat_value) {
					?>
						<a href="<?php echo base_url('store/category/'. $cat_value['slug'])?>" class="category-home">
							<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/images/product/upload/thumb/'); ?><?=$cat_value['image'];?>" />
							<h3><?=$cat_value['name'];?></h3>
						</a>
					<?php
				}
			}
			else
			{
				?>
					<div class="category-home">
						<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/ctg1.png" />
						<h3><?= __('store.dog') ?></h3>
					</div>
				<?php
				
			}
			?>
			
		</div>
	</div>
</section>

<section class="home-blog">
	<div class="container">

	<?php $bs_cards = (isset($store_setting['bs_cards']) && !empty($store_setting['bs_cards'])) ? json_decode($store_setting['bs_cards']) : []; ?>

	<div class="row">
		<?php
		
		foreach($bs_cards as $hf){
			$bs_cards_are_available = true;
			if($hf->button_link!="")
				$bs_button_link=$hf->button_link;
			else
				$bs_button_link="#";
			  

		?>
			<?php $img = (!empty($hf->feature_image)) ? base_url('assets/images/site/'. $hf->feature_image) : base_url('assets/store/default/img/blog1.png'); ?>
			<div class="col-md-6 col-12">
				<a class="bs_button_link"  href="<?php echo $bs_button_link; ?>" target="<?php if($hf->link_target=="true") {  echo '_blank'; } else  { echo '_self';} ?>" >
					<div class="blog-wrapper bg-main2" <?= (!empty($hf->bg_color)) ? 'style="background-color:'.$hf->bg_color.'"' : ''?>>
						<img alt="<?= __('store.image') ?>" src="<?= $img; ?>" class="blog-img" />
						<div class="blog-content">
						<h4><?= $hf->title; ?></h4>
						<p><?= $hf->sub_title; ?></p>
						</div>
					</div>
				</a>
			</div>
		<?php	
		}

		if(!isset($bs_cards_are_available)) {
			?>
			<div class="col-md-6 col-12">
				<div class="blog-wrapper bg-main2">
				<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/img') ?>/blog1.png" class="blog-img" />
				<div class="blog-content">
					<h4>What is Lorem Ipsum?</h4>
					<p>
					Lorem Ipsum is simply dummy text of the printing and
					typesetting industry.
					</p>
				</div>
				</div>
			</div>
          	<div class="col-md-6 col-12">
				<div class="blog-wrapper bg-main">
				<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/img') ?>/fb2.png" class="blog-img" />
				<div class="blog-content">
					<h4>What is Lorem Ipsum?</h4>
					<p>
					Lorem Ipsum is simply dummy text of the printing and
					typesetting industry.
					</p>
				</div>
				</div>
          	</div>
			<div class="col-md-6 col-12">
				<div class="blog-wrapper bg-main">
				<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/img') ?>/fb3.png" class="blog-img" />
				<div class="blog-content">
					<h4>What is Lorem Ipsum?</h4>
					<p>
					Lorem Ipsum is simply dummy text of the printing and
					typesetting industry.
					</p>
				</div>
				</div>
          	</div>
			<div class="col-md-6 col-12">
				<div class="blog-wrapper bg-main2">
				<img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/img') ?>/fb4.png" class="blog-img" />
				<div class="blog-content">
					<h4>What is Lorem Ipsum?</h4>
					<p>
					Lorem Ipsum is simply dummy text of the printing and
					typesetting industry.
					</p>
				</div>
				</div>
			</div>
			<?php
		}
		?>
	</div>

	<div class="blog-para">
		<?php
			$para = isset($store_setting['homepage_bottom_section']) ? json_decode($store_setting['homepage_bottom_section']) : "";
			$para = isset($para->content) ? $para->content : "";
		?>
		<?= (!empty(strip_tags($para))) ? $para : 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
'; ?>
	</div>
	<a href="javascript:void(0);" class="blog-more"><?= __('store.show_more') ?> <br/> <i class="fas fa-angle-down"></i></a>
	</div>
</section>

<?php include 'product-list-template.php';  ?>


<script type="text/javascript">
	$(document).on('click', '.blog-more', function(){
		var el = $(".blog-para"),
		curHeight = el.height(),
		autoHeight = el.css('height', 'auto').height();
		el.height(curHeight).animate({height: autoHeight}, 500);
		$(this).after('<a href="javascript:void(0);" class="blog-less">'+'<?= __('store.hide') ?>'+' <br/> <i class="fas fa-angle-up"></i></a>');
		$(this).remove();
	});

	$(document).on('click', '.blog-less', function(){
		var el = $(".blog-para");
		el.animate({height: '50px'}, 500);
		$(this).after('<a href="javascript:void(0);" class="blog-more">'+'<?= __('store.show_more') ?>'+' <br/> <i class="fas fa-angle-down"></i></a>');
		$(this).remove();
	});

	$(document).ready(function() {
		load_Product($('#searchProduct').val());

		$('#searchProduct').keyup(function(e) {
			e.preventDefault();
			var search = $(this).val();
			load_Product(search);
		});
	});


	$(document).on('click', '.see-more', function() {
		load_Product(null, {
			next_page: $(this).data('next_page'),
			request_page_section: $(this).data('request_page_section')
		});
	});

	function load_Product(search, postData = {}) {
		var data = postData;
		data.search = search;
		data.request_page = 'home';
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
			},
			success: function(res) {
				if(res.trendings) {
					if(postData.next_page && postData.next_page > 1) {
						$('.product-list-trending').append(Mustache.render($('#product-list-template').html(), res.trendings));
					} else {
						$('.product-list-trending').html(Mustache.render($('#product-list-template').html(), res.trendings));
					}
					$('.see-more-trendings').data('next_page', res.trendings.next_page);
					if(res.trendings.is_last_page) {
						$('.see-more-trendings').hide();
					}
				}

				if(res.new) 
				{
					if(postData.next_page && postData.next_page > 1) {
						$('.product-list-new').append(Mustache.render($('#product-list-template').html(), res.new));
					} else {
						$('.product-list-new').html(Mustache.render($('#product-list-template').html(), res.new));
					}
					$('.see-more-new').data('next_page', res.new.next_page);
					if(res.new.is_last_page) {
						$('.see-more-new').hide();
					}
				}

				if(res.category.new && res.category.new.length) {
					$('.home-new-products .category-listing').html(res.category.new);
				}

				if(res.category.all && res.category.all.length) {
					$(".demo-cat-badge").hide();
				}
			}
		});
	}
</script>
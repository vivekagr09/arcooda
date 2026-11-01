<?php
  $db =& get_instance();
  $products = $db->Product_model;
  $sales_store_side_font = $products->getSettings('site','sales_store_side_font');
  
  if ($sales_store_side_font['sales_store_side_font'] == "Roboto") {
  	$sales_store_side_font['sales_store_side_font'] = '"Roboto", sans-serif';
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta property='og:url' content='<?= $_SERVER['REQUEST_URI']; ?>'/>
	<?php if(isset($meta_title)){ ?> <meta property="og:title" content="<?php echo $meta_title ?>"/><?php } ?>
	<?php if(isset($meta_description)){ ?> 
	<meta name="description" content="<?php echo $meta_description ?>"/>
	<meta property="og:description" content="<?php echo $meta_description ?>"/>
	<?php } ?>
	<?php if(isset($meta_image)){ ?> <meta property="og:image" content="<?php echo $meta_image ?>"/><?php } ?>

	<meta
		property="og:url"
		content="<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?>"
	/>

	<meta name="twitter:card" content="summary_large_image"/>

	<?php if($store_setting['favicon']){ ?>
		<link rel="icon" href="<?= base_url('assets/images/site/'.$store_setting['favicon']) ?>" type="image/*" sizes="16x16">
	<?php } ?>

   <title><?= $store_setting['name'] ?>  <?= isset($meta_title) ? '- ' . $meta_title : '' ?></title>

	<link href="<?= base_url('assets/store/classified/'); ?>dependencies/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link href="<?= base_url('assets/store/classified/'); ?>dependencies/fontawesome/css/all.min.css" rel="stylesheet" />
	<link href="<?= base_url('assets/store/classified/'); ?>dependencies/fontawesome/css/all.css" rel="stylesheet" />
	<link href="<?= base_url('assets/store/classified/'); ?>dependencies/flaticon/flaticon.css" rel="stylesheet" />
	<link href="<?= base_url('assets/store/classified/'); ?>dependencies/owl.carousel/css/owl.carousel.min.css" rel="stylesheet" />
	<link href="<?= base_url('assets/store/classified/'); ?>dependencies/owl.carousel/css/owl.theme.default.min.css" rel="stylesheet" />
	<link href="<?= base_url('assets/store/classified/'); ?>dependencies/jquery-animated-headlines/css/jquery.animatedheadline.css" rel="stylesheet" />
	<link rel="stylesheet" href="<?= base_url('assets/store/classified/'); ?>assets/css/sweetalert2.min.css?v=9.0.0.3" />
	<link href="<?= base_url('assets/store/classified/'); ?>dependencies/magnific-popup/css/magnific-popup.css" rel="stylesheet" />
	<link href="<?= base_url('assets/store/classified/'); ?>dependencies/animate.css/css/animate.min.css" rel="stylesheet" />
	<link href="<?= base_url('assets/store/classified/'); ?>dependencies/meanmenu/css/meanmenu.min.css" rel="stylesheet" />
	<link href="<?= base_url('assets/store/classified/'); ?>assets/css/app.css" rel="stylesheet" />
	
	<!-- Jquery Js -->
	<script src="<?= base_url('assets/store/classified/'); ?>dependencies/jquery/js/jquery.min.js"></script>
	<!-- Popper Js -->
	<script src="<?= base_url('assets/store/classified/'); ?>dependencies/popper.js/js/popper.min.js"></script>
	<!-- Bootstrap Js -->
	<script src="<?= base_url('assets/store/classified/'); ?>dependencies/bootstrap/js/bootstrap.min.js"></script>


	<!-- Owl Carousel Js -->
	<script src="<?= base_url('assets/store/classified/'); ?>dependencies/owl.carousel/js/owl.carousel.min.js"></script>
	<!-- ImagesLoaded Js -->
	<script src="<?= base_url('assets/store/classified/'); ?>dependencies/imagesloaded/js/imagesloaded.pkgd.min.js"></script>
	<!-- Animated Headline Js -->
	<script src="<?= base_url('assets/store/classified/'); ?>dependencies/jquery-animated-headlines/js/jquery.animatedheadline.min.js"></script>
	<!-- Magnific Popup Js -->
	<script src="<?= base_url('assets/store/classified/'); ?>dependencies/magnific-popup/js/jquery.magnific-popup.min.js"></script>
	<!-- ElevateZoom Js -->
	<script src="<?= base_url('assets/store/classified/'); ?>dependencies/elevatezoom/js/jquery.elevateZoom-2.2.3.min.js"></script>
	  <script src="<?= base_url('assets/store/classified/'); ?>assets/js/sweetalert2.all.min.js"></script>
	<!-- Meanmenu Js -->
	<script src="<?= base_url('assets/store/classified/'); ?>dependencies/meanmenu/js/jquery.meanmenu.min.js"></script>
	<style type="text/css">
      h1, h2, h3, h4, h5, h6, span {
        font-family: <?= $sales_store_side_font['sales_store_side_font'] ?> !important;
      }
  </style>

    <?php if(is_rtl()) { ?>
      <!-- place here your RTL css code -->
      <link rel="stylesheet" href="<?= base_url('assets/store/classified/'); ?>assets/css/rtl.css?v=
      <?= av() ?>"/>

   	<?php } ?>
   	<script type="text/javascript">
			var grecaptcha = undefined;
		</script>
   	
</head>

<?php
$storelogoheight=36;
$storelogowidthstr='';
  if($store_setting['store_custom_logo_size']!=0)
  {
    $storelogowidth=$store_setting['store_logo_custom_width'];
    $storelogoheight=$store_setting['store_logo_custom_height'];
    $storelogowidthstr= 'width="'.$storelogowidth.'"'; 
  }
?>

<body class="sticky-header" style="font-family: <?= $sales_store_side_font['sales_store_side_font'] ?>;">
	<div class="wrapper" id="wrapper">
		<header class="header" aff-section="classified_header"></header>
		<script aff-template="classified_header" type="text/html">
			<div id="rt-sticky-placeholder"></div>
				<div class="header-menu menu-layout2 " id="header-menu">
					<div class="container">
						<div class="row d-flex align-items-center">
							<div class="col-lg-2">
								<div class="logo-area">
									<?php  
									$logo = ($store_setting['logo']) ? base_url('assets/images/site/'.$store_setting['logo']) : base_url('assets/store/default/').'img/logo.png'; 
									?>
									<a class="navbar-brand temp-logo" href="{{home_page_url}}">
										<img alt="<?= __('store.image') ?>" src="<?= $logo ?>" onerror="this.src='<?=base_url('assets/store/default/').'img/logo.png'?>';" height="<?php echo $storelogoheight; ?>" <?php echo $storelogowidthstr; ?> />
									</a>
								</div>
							</div>

							<div class="col-lg-7 d-flex justify-content-end ml-auto">
								<nav class="template-main-menu" id="dropdownmain">
									<ul>
										<li><a href="{{home_page_url}}"><?= __('store.home')?></a></li>
										<li><a href="{{aboutus_page_url}}"><?= __('store.about_us')?></a></li>
										<li><a href="{{catalog_page_url}}"><?= __('store.catalog')?></a></li>
										<li><a href="{{contact_page_url}}"><?= __('store.contact')?></a></li>

									</ul>
								</nav>
							</div>

							<div class="col-lg-3 d-flex justify-content-end dropdown-login">
								{{#SelectedLanguage}}
								<div class="dropdown me-4">
								  <button class="btn btn-light dropdown-toggle p-2 px-4" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius: 3.125rem;">
								    {{SelectedLanguage}}
								  </button>
								  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
								  	{{#LanguageHtml}}
		                      <a class="dropdown-item" href="{{href}}">{{name}}</a>
		                     {{/LanguageHtml}}
								  </div>
								</div>
                			{{/SelectedLanguage}}

                			{{#SelectedCurrency}}
								<div class="dropdown me-4">
								  <button class="btn btn-light dropdown-toggle p-2 px-4" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius: 3.125rem;">
								    {{SelectedCurrency}}
								  </button>
								  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
								  	{{#CurrencyHtml}}
		                      <a class="dropdown-item" href="{{href}}">{{code}}</a>
		                     {{/CurrencyHtml}}
								  </div>
								</div>
                			{{/SelectedCurrency}}

								<div class="header-action-layout1">
                  <nav id="dropdown" class="template-main-menu" style="display: block;">
                     <ul id="dropdown">
                     	{{^loginUser}}
                        <li class="header-btn">
                           <a href="javascript:void(0);" class="item-btn has-dropdown"><?= __('store.login')?></a>
                           <ul class="sub-menu">
                              <li>
                                 <a href="{{customer_login_url}}"><?= __('store.client_login')?></a>
                              </li>
                              <li>
                                 <a href="{{affiliate_login_url}}" target="_blank">
                                 	<?= __('store.affiliate_login')?></a>
                              </li>
                           </ul>
                        </li>
                     	{{/loginUser}}
                     	{{#loginUser}}
                   		<li class="header-login-icon header-login-icon1">
                           <a  href="javascript:void(0);" class="color-primary item-btn has-dropdown">
                           	<i class="far fa-user"></i>
                           </a>
                           <ul class="sub-menu">
                              <li>
                                 <a href="{{customer_profile}}"><?= __('store.client_profile')?></a>
                              </li>
                              <li>
                                 <a href="{{customer_orders}}"><?= __('store.client_orders')?></a>
                              </li>
                              <li>
                                 <a href="{{customer_wishlist}}"><?= __('store.wishlist')?></a>
                              </li>
                              <li>
                                 <a href="{{customer_logout_url}}"><?= __('store.logout')?></a>
                              </li>
                           </ul>
                        </li>
                     	{{/loginUser}}
                     </ul>
                  </nav>
                </div>
							</div>
						</div>
					</div>
				</div>

				<section id="meanmenu-content" class="d-none">
					<div class="row">
						<div class="col-2 py-2 px-4">
							<div class="logo-area">
								<a class="temp-logo" href="{{home_page_url}}">
									<img src="{{logo}}" height="36" onerror="this.src='<?=base_url('assets/store/default/').'img/logo.png'?>';" alt="<?= __('store.image') ?>" class="img-fluid">
								</a>
							</div>
						</div>

						<div class="col-7 col-sm-8 py-2 d-flex justify-content-end dropdown-login">
							{{#SelectedLanguage}}
							<div class="dropdown me-4">
							  <button class="btn btn-light dropdown-toggle p-2 px-4" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius: 3.125rem;">
							    {{SelectedLanguage}}
							  </button>
							  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
							  	{{#LanguageHtml}}
	                      <a class="dropdown-item" href="{{href}}">{{name}}</a>
	                     {{/LanguageHtml}}
							  </div>
							</div>
	            			{{/SelectedLanguage}}

	            			{{#SelectedCurrency}}
							<div class="dropdown me-4">
							  <button class="btn btn-light dropdown-toggle p-2 px-4" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius: 3.125rem;">
							    {{SelectedCurrency}}
							  </button>
							  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
							  	{{#CurrencyHtml}}
	                      <a class="dropdown-item" href="{{href}}">{{code}}</a>
	                     {{/CurrencyHtml}}
							  </div>
							</div>
        			{{/SelectedCurrency}}

							<div class="dropdown me-0">
               	{{^loginUser}}
                     <button  type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" class="btn btn-light dropdown-toggle p-2 px-4"><?= __('store.login')?></button>
										  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                       <a class="dropdown-item" href="{{customer_login_url}}"><?= __('store.client_login')?></a>
                       <a class="dropdown-item" href="{{affiliate_login_url}}" target="_blank">
                       	<?= __('store.affiliate_login')?></a>
                     </div>
               	{{/loginUser}}
               	{{#loginUser}}

                     <button type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" class="color-primary item-btn has-dropdown btn btn-light dropdown-toggle p-2 px-4">
                     	<i class="far fa-user"></i>
                     </button>
										  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                       <a class="dropdown-item" href="{{customer_profile}}"><?= __('store.client_profile')?></a>
                       <a class="dropdown-item" href="{{customer_orders}}"><?= __('store.client_orders')?></a>
                       <a class="dropdown-item" href="{{customer_wishlist}}"><?= __('store.wishlist')?></a>
                       <a class="dropdown-item" href="{{customer_logout_url}}"><?= __('store.logout')?></a>
                     </div>

               	{{/loginUser}}
               </div>

						</div>
					</div>
				</section>
		</script>

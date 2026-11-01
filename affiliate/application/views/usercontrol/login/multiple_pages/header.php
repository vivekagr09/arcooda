<!doctype html>
<html lang="en">
<head>
<?php
  if($site_setting['google_analytics']){ echo $site_setting['google_analytics']; }
  if($site_setting['faceboook_pixel']){ echo $site_setting['faceboook_pixel']; }

  $logo = base_url('assets/login/multiple_pages/img/logo.png');
    if ($theme_settings[0]->logo) {
        $logo = base_url('assets/images/theme_images/' . $theme_settings[0]->logo);
    }
    echo ($site_setting['favicon']) ? '<link rel="icon" href="' . base_url('assets/images/site/' . $site_setting['favicon']) . '" type="image/*" sizes="16x16">' : '';

  $global_script_status = (array)json_decode($site_setting['global_script_status'],1);
  if(in_array('front', $global_script_status)){ echo $site_setting['global_script']; }
  $db =& get_instance();
  $products = $db->Product_model;
  $googlerecaptcha =$db->Product_model->getSettings('googlerecaptcha');
  $front_side_font =$products->getSettings('site','front_side_font');
  $cookies_menu_setting = $db->Product_model->getSettings('site');
  $front_header_color_before_scroll =$products->getFrontThemeSettings('theme','front_header_color_before_scroll');
  $front_header_color_after_scroll =$products->getFrontThemeSettings('theme','front_header_color_after_scroll');
  $front_footer_color =$products->getFrontThemeSettings('theme','front_footer_color');
  $bottom_banner_before_footer = $products->getFrontThemeSettings('theme','bottom_banner_before_footer');
  $front_button_color =$products->getFrontThemeSettings('theme','front_button_color');
  $front_button_hover_color =$products->getFrontThemeSettings('theme','front_button_hover_color');
  $front_button_text_color =$products->getFrontThemeSettings('theme','front_button_text_color');
  $front_header_button_color_before_scroll =$products->getFrontThemeSettings('theme','front_header_button_color_before_scroll');
  $front_header_button_color_after_scroll =$products->getFrontThemeSettings('theme','front_header_button_color_after_scroll');
  $front_header_button_text_color_before_scroll =$products->getFrontThemeSettings('theme','front_header_button_text_color_before_scroll');
  $front_header_button_text_color_after_scroll =$products->getFrontThemeSettings('theme','front_header_button_text_color_after_scroll');
  $front_header_button_hover_color_before_scroll =$products->getFrontThemeSettings('theme','front_header_button_hover_color_before_scroll');
  $front_header_button_hover_color_after_scroll =$products->getFrontThemeSettings('theme','front_header_button_hover_color_after_scroll');
  $front_runner_bar_color =$products->getFrontThemeSettings('theme','front_runner_bar_color');
  $front_runner_bar_text_color =$products->getFrontThemeSettings('theme','front_runner_bar_text_color');
  $front_theme_text_color =$products->getFrontThemeSettings('theme','front_theme_text_color');
  $front_faq_before_hover_color =$products->getFrontThemeSettings('theme','front_faq_before_hover_color');
  $front_faq_after_hover_color =$products->getFrontThemeSettings('theme','front_faq_after_hover_color');
  $header_menu_bg_color_responsive =$products->getFrontThemeSettings('theme','header_menu_bg_color_responsive');
  ?>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $site_setting['name'] ?></title>
    <meta name="author" content="<?= $meta_author ?>">
    <meta name="keywords" content="<?= $meta_keywords ?>">
    <meta name="description" content="<?= $meta_description ?>">
    <title><?= $setting['heading'] ?></title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/login/multiple_pages/css/font-awesome.min.css') ?>?v=<?= av() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/login/multiple_pages/css/bootstrap.min.css') ?>?v=<?= av() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/login/multiple_pages/css/owl.carousel.min.css') ?>?v=<?= av() ?>">

    <link rel="stylesheet" href="<?= base_url('assets/login/multiple_pages') ?>/css/style.css?v=<?= av() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/login/multiple_pages') ?>/css/login-style.css?v=<?= av() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/login/multiple_pages') ?>/css/responsive.css?v=<?= av() ?>">

<!--bootstrap 5 js files-->
<script src="<?= base_url('assets/template/js/jquery-3.6.0.min.js'); ?>?v=<?= av() ?>"></script>
<script src="<?= base_url('assets/template/js/popper.min.js'); ?>?v=<?= av() ?>"></script>
<script src="<?= base_url('assets/template/js/bootstrap.min.js'); ?>?v=<?= av() ?>"></script>
<!--bootstrap 5 js files-->

    <?php if($theme_settings[0]->custom_logo_size): ?>
        <style type="text/css">
            .customLogoClass{
                width: <?= (int) $theme_settings[0]->log_custom_width ?>px !important;
                height: <?= (int) $theme_settings[0]->log_custom_height ?>px !important;
            }
        </style>
    <?php endif ?>

    <style type="text/css">
        h1, h3, h4, button, input, optgroup, select, textarea {
            font-family: <?= $front_side_font['front_side_font'] ?> !important;
        }
        .header-navbar {
            background:  <?= $front_header_color_before_scroll['front_header_color_before_scroll'] ?>;
        }
        .header-navbar.stick {
            background:  <?= $front_header_color_after_scroll['front_header_color_after_scroll'] ?>;
        }
        .footer-area {
            background: <?= $front_footer_color['front_footer_color'] ?>;
        }
        .front_button_color {
            background: <?= $front_button_color['front_button_color'] ?> !important;
        }
        .front_button_hover_color:hover {
            background: <?= $front_button_hover_color['front_button_hover_color'] ?> !important;
        }
        .front_button_text_color {
            color: <?= $front_button_text_color['front_button_text_color'] ?> !important;
        }
        .header-navbar .btn-login {
            background: <?= $front_header_button_color_before_scroll['front_header_button_color_before_scroll'] ?> !important;
            color: <?= $front_header_button_text_color_before_scroll['front_header_button_text_color_before_scroll'] ?> !important;
        }
        .header-navbar.stick .btn-login {
            background: <?= $front_header_button_color_after_scroll['front_header_button_color_after_scroll'] ?> !important;
            color: <?= $front_header_button_text_color_after_scroll['front_header_button_text_color_after_scroll'] ?> !important;
        }
        .header-navbar .btn-login:hover{
            background: <?= $front_header_button_hover_color_before_scroll['front_header_button_hover_color_before_scroll'] ?> !important;
        }
        .header-navbar.stick .btn-login:hover{
            background: <?= $front_header_button_hover_color_after_scroll['front_header_button_hover_color_after_scroll'] ?> !important;
        }
        .news-ticker-area {
            background: <?= $front_runner_bar_color['front_runner_bar_color'] ?> !important;
            border-bottom: 10px solid <?= $front_runner_bar_color['front_runner_bar_color'] ?> !important;
        }
        .news-ticker-inner p {
            color: <?= $front_runner_bar_text_color['front_runner_bar_text_color'] ?> !important;
        }
        .front_theme_text_color {
            color: <?= $front_theme_text_color['front_theme_text_color'] ?> !important;
        }
        .featured-slider .owl-dots button.owl-dot.active {
            background: <?= $front_theme_text_color['front_theme_text_color'] ?> !important;
        }
        .single-testimonial {
            border-top: 6px solid <?= $front_theme_text_color['front_theme_text_color'] ?> !important;
        }
        .accordion.faq-accodion .card .card-header h2 button {
            background: <?= $front_faq_before_hover_color['front_faq_before_hover_color'] ?> !important;
        }
        .accordion.faq-accodion .card .card-header h2 button:hover {
            background: <?= $front_faq_after_hover_color['front_faq_after_hover_color'] ?> !important;
        }
        .login-form.register-form .checkbox input[type="checkbox"]:checked + .box:after {
            background-color: <?= $front_button_color['front_button_color'] ?> !important;
            border-color: <?= $front_button_color['front_button_color'] ?> !important;
        }
        @media (max-width: 991px) {
        .header-navbar .collapse.show {
            background: <?= $header_menu_bg_color_responsive['header_menu_bg_color_responsive'] ?> !important;
            }
        }
    </style>
  </head>

  <body style="font-family: <?= $front_side_font['front_side_font'] ?> !important;">

<?php 

$fbmessager_status = (array)json_decode($site_setting['fbmessager_status'],1);

if(in_array('front', $fbmessager_status)){

  echo $site_setting['fbmessager_script'];

  $LanguageHtml = $products->getLanguageHtml('usercontrol');

}

?>

  <?php if( current_url() != site_url('/login') && current_url() != site_url('/register') && current_url() != site_url('/register/vendor') && current_url() != site_url('/forget-password') && current_url() != site_url('/terms-of-use')){ ?>

    <!--Top Navbar-->

    <header class="header-navbar">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
              <a class="navbar-brand" href="<?= base_url('/') ?>">
                <img src="<?= $logo ?>" <?= ($theme_settings[0]->custom_logo_size) ? 'class="customLogoClass"' : '' ?> alt="<?= $setting['heading'] ?>">
              </a>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
              </button>
 
              <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
                <ul class="navbar-nav ml-auto">

                  <?php 
                    if (isset($header_menus) && !empty($header_menus)) {
                      foreach ($header_menus as $key => $menu) { 
                  ?>
 
                  
                  <?php if (empty($menu['parent_id'])==true && $menu['is_header_dropdown']==1) { ?>
                    <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle" href="#" id="dropdown-menu-<?= $menu['page_id'] ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?=$menu['page_type']=='editable' ? $menu['page_name'] : __('front.'.$menu['page_name']) ?>
                      </a>

                      <?php 
                        $parentSlug = $menu['page_id'];

                        $dropdowns = array_filter($header_menus, function ($menu) use ($parentSlug) {
                            return ($menu['parent_id'] == $parentSlug);
                        });

                        if (isset($dropdowns) && !empty($dropdowns)) {
                      ?>
                      <ul class="dropdown-menu" aria-labelledby="dropdown-menu-<?= $menu['page_id'] ?>">
                        <?php foreach ($dropdowns as $key => $dropdown) {  ?>
                        <li><a class="dropdown-item" href="<?= site_url($dropdown['page_type']=='editable' ? 'p/'.$dropdown['slug'] : $dropdown['slug']) ?>"><?= $dropdown['page_type']=='editable' ? $dropdown['page_name'] : __('front.'.$dropdown['page_name']) ?></a></li>
                        <?php } ?>
                      </ul>

                    <?php } ?>

                    </li>
                  <?php }else if (empty($menu['parent_id'])==true){ ?>
                    <li class="nav-item <?php if(site_url(uri_string()) == site_url($menu['slug'])){ echo 'active'; } ?>">

                      <a class="nav-link" href="<?= site_url($menu['page_type']=='editable' ? 'p/'.$menu['slug'] : $menu['slug']) ?>"><?=$menu['page_type']=='editable' ? $menu['page_name'] : __('front.'.$menu['page_name']) ?></a>

                    </li>
                  <?php }}} ?>

                  <?php 

                  $store_setting = $this->Product_model->getSettings('store');

                  if($store_setting['menu_on_front']){ 

                  ?>

                    <li class="nav-item <?php if(base_url(uri_string()) == base_url('/store')){ echo 'active'; } ?>">

                      <a class="nav-link" href="<?= base_url('/store') ?>" <?= ($store_setting['menu_on_front_blank']) ? 'target="_blank"' : ''; ?>><?= __('front.my_store') ?></a>

                    </li>

                  <?php } ?>

                  <?php if($store['language_status']){ ?>
                    <li class="nav-item dropdown">
                      <?= $LanguageHtml ?>
                    </li>  
                  <?php } ?>

                </ul>
                <a href="<?= base_url('/login') ?>" class="btn-login my-2 my-lg-0"><?= __('front.log_in') ?></a>
              </div>
            </nav>
        </div>
    </header>

  <?php } ?>
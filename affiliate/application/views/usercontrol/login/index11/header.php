<!doctype html>
<html lang="en">
<head>
<?php
    if($SiteSetting['google_analytics']){ echo $SiteSetting['google_analytics']; }
    if($SiteSetting['faceboook_pixel']){ echo $SiteSetting['faceboook_pixel']; }
    $logo = $SiteSetting['front-side-themes-logo'] ? 'assets/images/site/'.$SiteSetting['front-side-themes-logo'] : 'assets/login/index1/img/logo.png';
    echo '<link rel="icon" href="'. base_url('assets/images/' . ($SiteSetting['favicon'] ? 'site/'.$SiteSetting['favicon'] : 'fav.png')) .'" type="image/*" sizes="16x16">';
    $global_script_status = (array)json_decode($SiteSetting['global_script_status'],1);
    if(in_array('front', $global_script_status)){ echo $SiteSetting['global_script']; }
    $db =& get_instance();
    $products = $db->Product_model;
    $googlerecaptcha =$db->Product_model->getSettings('googlerecaptcha');
    $front_side_font =$db->Product_model->getSettings('site','front_side_font');
    $front_side_font_value = $front_side_font['front_side_font'] ?? '';
    $cookies_menu_setting = $db->Product_model->getSettings('site');
    ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $title ?></title>
    <meta name="author" content="<?= $meta_author ?>">
    <meta name="keywords" content="<?= $meta_keywords ?>">
    <meta name="description" content="<?= $meta_description ?>">

<!-- Bootstrap 5 Css -->
<link rel="stylesheet" href="<?= base_url('assets/template/css/bootstrap.min.css') ?>?v=<?= av() ?>">
<link rel="stylesheet" href="<?= base_url('assets/template/css/bootstrap-icons.css') ?>?v=<?= av() ?>">
<link rel="stylesheet" href="<?= base_url('assets/template/css/bootstrap-toggle.min.css') ?>?v=<?= av() ?>">
<!-- Bootstrap 5 Css -->


    <link href="<?= base_url('assets/login/index11/css/') ?>/main.css?v=<?= av() ?>" rel="stylesheet">

    <script src="<?= base_url('assets') ?>/js/jquery.min.js"></script>


    <?php if($SiteSetting['front_custom_logo_size']): ?>
        <style type="text/css">
            .customLogoClass{
                width: <?= (int) $SiteSetting['front_log_custom_width'] ?>px !important;
                height: <?= (int) $SiteSetting['front_log_custom_height'] ?>px !important;
            }
        </style>
    <?php endif ?>
    
    <?php if (is_rtl()) { ?>
      <!-- place here your RTL css code -->
      <!-- <link href="<?= base_url('assets/login/index11') ?>/css/rtl.css?v=<?= av() ?>" rel="stylesheet"> -->
    <?php } ?>

    <style type="text/css">
        .forny-container {
            font-family: <?= $front_side_font_value ?> !important;
        }
    </style>
    </head>

    <body background="<?= base_url('assets/login/index11') ?>/images/bg.jpg">
        <div class="container-fluid">
            <div class="row justify-content-between align-items-center">
                <!-- Logo Section -->
                <div class="col-6 col-xl-6">
                    <a href="<?=base_url();?>">
                        <img src="<?= base_url($logo);?>" alt="<?= __('front.logo') ?>" class="img-fluid logo_img <?= ($SiteSetting['front_custom_logo_size']) ? 'customLogoClass' : '' ?>">
                    </a>
                </div>
                
                <!-- Language Changer Section -->
                <div class="col-6 col-xl-6 d-flex justify-content-end align-items-center">
                    <?php if ($store['language_status']) { ?>
                        <div class="language-changer me-3 text-white"><?= $LanguageHtml ?></div>
                    <?php } ?>
                </div>
            </div>
        </div>

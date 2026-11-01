<!doctype html>
<html lang="en">
<head>
<?php
    if($SiteSetting['google_analytics']){ echo $SiteSetting['google_analytics']; }
    if($SiteSetting['faceboook_pixel']){ echo $SiteSetting['faceboook_pixel']; }
    $logo = $SiteSetting['front-side-themes-logo'] ? 'assets/images/site/'.$SiteSetting['front-side-themes-logo'] : 'assets/login/index3/image/logo.png';
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
    
<!-- Custom Css -->
<link rel="stylesheet" href="<?= base_url('assets/login/index3/css/style.css') ?>?v=<?= av() ?>">
<!-- Custom Css -->

<script src="<?= base_url('assets/js/jquery.min.js') ?>?v=<?= av() ?>"></script>


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
    <?php } ?>

    <style type="text/css">
        .button {
            font-family: <?= $front_side_font['front_side_font'] ?> !important;
        }
    </style>
</head>

<body style="font-family: <?= $front_side_font['front_side_font'] ?> !important;">
    <div class="container-fluid h-100">
        <?php if($store['language_status']){ ?>
            <div class="language-changer position-absolute top-0 end-0 mt-3 me-3"> <?= $LanguageHtml ?>
            </div>
        <?php } ?>
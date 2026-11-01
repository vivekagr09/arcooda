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

    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>?v=<?= av() ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/common.css') ?>?v=<?= av() ?>" rel="stylesheet">
    <link href="<?= base_url('assets/login/index9/css/style.css') ?>?v=<?= av() ?>" rel="stylesheet">

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
        h1, h3, button, input, optgroup, select, textarea {
            font-family: <?= $front_side_font['front_side_font'] ?> !important;
        }
    </style>
</head>

<body style="font-family: <?= $front_side_font['front_side_font'] ?> !important;">
    <div class="container-fluid ">
        <div class="row h-100 full_height">
            <div class="col-md-6">
                <div class="row justify-content-between">
                    <a href="<?=base_url();?>" class="logo_area ml-1 mt-1">
                        <img src="<?= base_url($logo);?>" class="logo_img <?= ($SiteSetting['front_custom_logo_size']) ? 'customLogoClass' : '' ?>" alt="<?= __('front.logo') ?>"></a>

                        <?php if($store['language_status']){ ?>
                            <div class="language-changer">
                                <?= $LanguageHtml ?>
                            </div>
                        <?php } ?>
                </div>
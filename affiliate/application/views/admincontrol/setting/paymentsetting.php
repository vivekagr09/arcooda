<div class="card">
    <div class="card-body">
        <form class="form-horizontal" autocomplete="off" method="post" action=""  enctype="multipart/form-data" id="setting-form">

            <div class="row">
                <div class="col-sm-12">
                    <ul class="nav nav-pills flex-column flex-sm-row tab-container" role="tablist" id="TabsNav">
                        <li class="nav-item flex-sm-fill text-sm-center mb-1 mb-sm-0">
                            <a class="nav-link active bg-secondary show" data-bs-toggle="tab" href="#site-setting" role="tab">
                            <?= __('admin.site_setting') ?></a>
                        </li>
                        <li class="nav-item flex-sm-fill text-sm-center mb-1 mb-sm-0">
                            <a class="nav-link" data-bs-toggle="tab" href="#site-meta" role="tab">
                            <?= __('admin.site_meta') ?></a>
                        </li>
                        <li class="nav-item flex-sm-fill text-sm-center mb-1 mb-sm-0">
                            <a class="nav-link" data-bs-toggle="tab" href="#site-logo" role="tab">
                            <?= __('admin.site_logo') ?></a>
                        </li>
                        <li class="nav-item flex-sm-fill text-sm-center mb-1 mb-sm-0">
                            <a class="nav-link" data-bs-toggle="tab" href="#email-setting" role="tab">
                            <?= __('admin.email_setting') ?></a>
                        </li>
                        <li class="nav-item flex-sm-fill text-sm-center mb-1 mb-sm-0">
                            <a class="nav-link" data-bs-toggle="tab" href="#tnc-page" role="tab">
                            <?= __('admin.terms_and_condition') ?></a>
                        </li>
                        <li class="nav-item flex-sm-fill text-sm-center mb-1 mb-sm-0">
                            <a class="nav-link" data-bs-toggle="tab" href="#tracking" role="tab">
                            <?= __('admin.tracking') ?></a>
                        </li>
                        <li class="nav-item flex-sm-fill text-sm-center mb-1 mb-sm-0">
                            <a class="nav-link" data-bs-toggle="tab" href="#googleads-setting" role="tab">
                            <?= __('admin.googleads') ?></a>
                        </li>
                        <li class="nav-item flex-sm-fill text-sm-center mb-1 mb-sm-0">
                            <a class="nav-link" data-bs-toggle="tab" href="#googlerecaptcha-setting" role="tab">
                            <?= __('admin.googlerecaptcha') ?></a>
                        </li>
                        <li class="nav-item flex-sm-fill text-sm-center mb-1 mb-sm-0">
                            <a class="nav-link" data-bs-toggle="tab" href="#user-dashboard-setting" role="tab">
                            <?= __('admin.user_dashboard') ?></a>
                        </li>
                        <li class="nav-item flex-sm-fill text-sm-center mb-1 mb-sm-0">
                            <a class="nav-link" data-bs-toggle="tab" href="#security" role="tab"><?= __('admin.security') ?></a>
                        </li>
                        <li class="nav-item flex-sm-fill text-sm-center mb-1 mb-sm-0">
                            <a class="nav-link" data-bs-toggle="tab" href="#theme" role="tab"><?= __('admin.theme_design') ?></a>
                        </li>
                    </ul>
                </div>

<div class="col-sm-12">
    <div class="tab-content">

<div class="tab-pane p-3" id="theme" role="tabpanel">
    <div class="row">
        <div class="col-sm-6">
            <fieldset class="border p-2">
                <legend class="w-auto px-2"><?= __('admin.colors') ?></legend>

                <div class="container">
                    <div class="row justify-content-center">
                        <h5 class="text-primary border-bottom pb-2 mt-3"><?= __('admin.admin_side') ?></h5>
                    </div>
                </div>

                <div class="row align-items-center theme-setting-row">
                    <div class="col-md-6">
                        <label class="form-label"><?= __('admin.side_bar_color') ?></label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control form-control-color" type="color" name="theme[admin_side_bar_color]" value="<?= $theme['admin_side_bar_color'] != '' ? $theme['admin_side_bar_color'] : '#ffffff' ?>">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary default-theme-setting" value="admin_side_bar_color" title="<?= __('admin.default') ?>">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="row align-items-center theme-setting-row">
                    <div class="col-md-6">
                        <label class="form-label"><?= __('admin.side_bar_scroll_color') ?></label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control form-control-color" type="color" name="theme[admin_side_bar_scroll_color]" value="<?= $theme['admin_side_bar_scroll_color'] != '' ? $theme['admin_side_bar_scroll_color'] : '#007BFF' ?>">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary default-theme-setting" value="admin_side_bar_scroll_color" title="<?= __('admin.default') ?>">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="row align-items-center theme-setting-row">
                    <div class="col-md-6">
                        <label class="form-label"><?= __('admin.side_bar_text_color') ?></label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control form-control-color" type="color" name="theme[admin_side_bar_text_color]" value="<?= $theme['admin_side_bar_text_color'] != '' ? $theme['admin_side_bar_text_color'] : '#686868' ?>">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary default-theme-setting" value="admin_side_bar_text_color" title="<?= __('admin.default') ?>">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="row align-items-center theme-setting-row">
                    <div class="col-md-6">
                        <label class="form-label"><?= __('admin.side_bar_text_hover_color') ?></label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control form-control-color" type="color" name="theme[admin_side_bar_text_hover_color]" value="<?= $theme['admin_side_bar_text_hover_color'] != '' ? $theme['admin_side_bar_text_hover_color'] : '#007BFF' ?>">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary default-theme-setting" value="admin_side_bar_text_hover_color" title="<?= __('admin.default') ?>">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="row align-items-center theme-setting-row">
                    <div class="col-md-6">
                        <label class="form-label"><?= __('admin.top_bar_color') ?></label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control form-control-color" type="color" name="theme[admin_top_bar_color]" value="<?= $theme['admin_top_bar_color'] != '' ? $theme['admin_top_bar_color'] : '#ffffff' ?>">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary default-theme-setting" value="admin_top_bar_color" title="<?= __('admin.default') ?>">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="row align-items-center theme-setting-row">
                    <div class="col-md-6">
                        <label class="form-label"><?= __('admin.footer_color') ?></label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control form-control-color" type="color" name="theme[admin_footer_color]" value="<?= $theme['admin_footer_color'] != '' ? $theme['admin_footer_color'] : '#f2f3f5' ?>">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary default-theme-setting" value="admin_footer_color" title="<?= __('admin.default') ?>">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="row align-items-center theme-setting-row">
                    <div class="col-md-6">
                        <label class="form-label"><?= __('admin.logo_color') ?></label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control form-control-color" type="color" name="theme[admin_logo_color]" value="<?= $theme['admin_logo_color'] != '' ? $theme['admin_logo_color'] : '#007BFF' ?>">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary default-theme-setting" value="admin_logo_color" title="<?= __('admin.default') ?>">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="row align-items-center theme-setting-row">
                    <div class="col-md-6">
                        <label class="form-label"><?= __('admin.button_color') ?></label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control form-control-color" type="color" name="theme[admin_button_color]" value="<?= $theme['admin_button_color'] != '' ? $theme['admin_button_color'] : '#3d5674' ?>">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary default-theme-setting" value="admin_button_color" title="<?= __('admin.default') ?>">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="row align-items-center theme-setting-row">
                    <div class="col-md-6">
                        <label class="form-label"><?= __('admin.button_hover_color') ?></label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control form-control-color" type="color" name="theme[admin_button_hover_color]" value="<?= $theme['admin_button_hover_color'] != '' ? $theme['admin_button_hover_color'] : '#007BFF' ?>">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary default-theme-setting" value="admin_button_hover_color" title="<?= __('admin.default') ?>">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>

                <div class="container">
                    <div class="row justify-content-center">
                        <h5 class="text-primary border-bottom pb-2 mt-3"><?= __('admin.user_side') ?></h5>
                    </div>
                </div>

                <div class="row align-items-center theme-setting-row">
                    <div class="col-md-6">
                        <label class="form-label"><?= __('admin.side_bar_color') ?></label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control form-control-color" type="color" name="theme[user_side_bar_color]" value="<?= $theme['user_side_bar_color'] != '' ? $theme['user_side_bar_color'] : '#ffffff' ?>">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary default-theme-setting" value="user_side_bar_color" title="<?= __('admin.default') ?>">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="row align-items-center theme-setting-row">
                    <div class="col-md-6">
                        <label class="form-label"><?= __('admin.side_bar_heading_and_menu_text_color') ?></label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control form-control-color" type="color" name="theme[user_side_bar_text_color]" value="<?= $theme['user_side_bar_text_color'] != '' ? $theme['user_side_bar_text_color'] : '#3f567a' ?>">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary default-theme-setting" value="user_side_bar_text_color" title="<?= __('admin.default') ?>">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="row align-items-center theme-setting-row">
                    <div class="col-md-6">
                        <label class="form-label"><?= __('admin.side_bar_clock_text_color') ?></label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control form-control-color" type="color" name="theme[user_side_bar_clock_text_color]" value="<?= $theme['user_side_bar_clock_text_color'] != '' ? $theme['user_side_bar_clock_text_color'] : '#5ec394' ?>">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary default-theme-setting" value="user_side_bar_clock_text_color" title="<?= __('admin.default') ?>">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="row align-items-center theme-setting-row">
                    <div class="col-md-6">
                        <label class="form-label"><?= __('admin.side_bar_text_hover_color') ?></label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control form-control-color" type="color" name="theme[user_side_bar_text_hover_color]" value="<?= $theme['user_side_bar_text_hover_color'] != '' ? $theme['user_side_bar_text_hover_color'] : '#085445' ?>">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary default-theme-setting" value="user_side_bar_text_hover_color" title="<?= __('admin.default') ?>">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="row align-items-center theme-setting-row">
                    <div class="col-md-6">
                        <label class="form-label"><?= __('admin.top_bar_color') ?></label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control form-control-color" type="color" name="theme[user_top_bar_color]" value="<?= $theme['user_top_bar_color'] != '' ? $theme['user_top_bar_color'] : '#ffffff' ?>">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary default-theme-setting" value="user_top_bar_color" title="<?= __('admin.default') ?>">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="row align-items-center theme-setting-row">
                    <div class="col-md-6">
                        <label class="form-label"><?= __('admin.footer_color') ?></label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control form-control-color" type="color" name="theme[user_footer_color]" value="<?= $theme['user_footer_color'] != '' ? $theme['user_footer_color'] : '#5ec394' ?>">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary default-theme-setting" value="user_footer_color" title="<?= __('admin.default') ?>">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="row align-items-center theme-setting-row">
                    <div class="col-md-6">
                        <label class="form-label"><?= __('admin.button_color') ?></label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control form-control-color" type="color" name="theme[user_button_color]" value="<?= $theme['user_button_color'] != '' ? $theme['user_button_color'] : '#3d5674' ?>">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary default-theme-setting" value="user_button_color" title="<?= __('admin.default') ?>">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="row align-items-center theme-setting-row">
                    <div class="col-md-6">
                        <label class="form-label"><?= __('admin.button_hover_color') ?></label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control form-control-color" type="color" name="theme[user_button_hover_color]" value="<?= $theme['user_button_hover_color'] != '' ? $theme['user_button_hover_color'] : '#5ec394' ?>">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary default-theme-setting" value="user_button_hover_color" title="<?= __('admin.default') ?>">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
            </fieldset>
        </div>

        <div class="col-sm-6">
            <fieldset class="border p-2">
                <legend class="w-auto px-2"><?= __('admin.fonts') ?></legend>
                <div class="form-group">
                    <div class="container">
                        <div class="row justify-content-center">
                            <h5 class="text-primary border-bottom pb-2 mt-3"><?= __('admin.font_family') ?></h5>
                        </div>
                    </div>

                        <div class="row align-items-center font-style-main">
                            <div class="col-md-4">
                                <label for="admin_side_font" class="form-label"><?= __('admin.admin_side') ?></label>
                            </div>
                            <div class="col-md-6">
                                <select id="admin_side_font" class="form-select class_admin_side_font" name="site[admin_side_font]">
                                    <?php foreach ($font_families as $key => $value) { 
                                        if ($site['admin_side_font'] != '') {
                                            ?>
                                                <option value="<?= $value ?>" <?= $site['admin_side_font'] == $value ? 'selected' : '' ?> > <?= $key ?></option>
                                            <?php
                                        } else {
                                            ?>
                                                <option value="<?= $value ?>" <?= $value == 'PT Sans' ? 'selected' : '' ?> > <?= $key ?></option>
                                            <?php
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary default-font-setting" value="admin_side_font" title="<?= __('admin.default') ?>">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                            </div>
                        </div>
                        <div class="row align-items-center font-style-main">
                            <div class="col-md-4">
                                <label for="user_side_font" class="form-label"><?= __('admin.user_side') ?></label>
                            </div>
                            <div class="col-md-6">
                                <select id="user_side_font" class="form-select class_user_side_font" name="site[user_side_font]">
                                    <option value="Poppins" <?= $site['user_side_font'] == 'Poppins' ? 'selected' : '' ?>>Poppins</option>
                                    <?php foreach ($font_families as $key => $value) { 
                                        if ($site['user_side_font'] != '') {
                                            ?>
                                                <option value="<?= $value ?>" <?= $site['user_side_font'] == $value ? 'selected' : '' ?> > <?= $key ?></option>
                                            <?php
                                        } else {
                                            ?>
                                                <option value="<?= $value ?>" <?= $value == 'Poppins' ? 'selected' : '' ?> > <?= $key ?></option>
                                            <?php
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary default-font-setting" value="user_side_font" title="<?= __('admin.default') ?>">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row align-items-center font-style-main">
                            <div class="col-md-4">
                                <label for="front_side_font" class="form-label"><?= __('admin.front_side') ?></label>
                            </div>
                            <div class="col-md-6">
                                <select id="front_side_font" class="form-select class_front_side_font" name="site[front_side_font]">
                                    <?php foreach ($font_families as $key => $value) { 
                                        if ($site['front_side_font'] != '') {
                                            ?>
                                                <option value="<?= $value ?>" <?= $site['front_side_font'] == $value ? 'selected' : '' ?> > <?= $key ?></option>
                                            <?php
                                        } else {
                                            ?>
                                                <option value="<?= $value ?>" <?= $value == 'sans-serif' ? 'selected' : '' ?> > <?= $key ?></option>
                                            <?php
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary default-font-setting" value="front_side_font" title="<?= __('admin.default') ?>">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row align-items-center font-style-main">
                            <div class="col-md-4">
                                <label for="cart_store_side_font" class="form-label"><?= __('admin.cart_store_side') ?></label>
                            </div>
                            <div class="col-md-6">
                                <select id="cart_store_side_font" class="form-select class_cart_store_side_font" name="site[cart_store_side_font]">
                                    <option value="Jost" <?= $site['cart_store_side_font'] == 'Jost' ? 'selected' : '' ?>>Jost</option>
                                    <?php foreach ($font_families as $key => $value) { 
                                        if ($site['cart_store_side_font'] != '') {
                                            ?>
                                                <option value="<?= $value ?>" <?= $site['cart_store_side_font'] == $value ? 'selected' : '' ?> > <?= $key ?></option>
                                            <?php
                                        } else {
                                            ?>
                                                <option value="<?= $value ?>" <?= $value == 'Jost' ? 'selected' : '' ?> > <?= $key ?></option>
                                            <?php
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary default-font-setting" value="cart_store_side_font" title="<?= __('admin.default') ?>">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row align-items-center font-style-main">
                            <div class="col-md-4">
                                <label for="sales_store_side_font" class="form-label"><?= __('admin.sales_store_side') ?></label>
                            </div>
                            <div class="col-md-6">
                                <select id="sales_store_side_font" class="form-select class_sales_store_side_font" name="site[sales_store_side_font]">
                                    <?php foreach ($font_families as $key => $value) { 
                                        if ($site['sales_store_side_font'] != '') {
                                            ?>
                                                <option value="<?= $value ?>" <?= $site['sales_store_side_font'] == $value ? 'selected' : '' ?> > <?= $key ?></option>
                                            <?php
                                        } else {
                                            ?>
                                                <option value="<?= $value ?>" <?= $value == 'Roboto' ? 'selected' : '' ?> > <?= $key ?></option>
                                            <?php
                                        }
                                    } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary default-font-setting" value="sales_store_side_font" title="<?= __('admin.default') ?>">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </div>
                        </div>
                </div>
            </fieldset>
            <br>

            <fieldset class="border p-2">
                <legend class="w-auto px-2"><?= __('admin.admin_login_page') ?></legend>

                <div class="row align-items-center theme-setting-row">
                    <div class="col-md-6">
                        <label class="form-label"><?= __('admin.admin_login_box_background_color') ?></label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control form-control-color" type="color" name="theme[admin_login_box_background_color]" value="<?= $theme['admin_login_box_background_color'] != '' ? $theme['admin_login_box_background_color'] : '#7a90a8' ?>">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary default-theme-setting" value="admin_login_box_background_color" title="<?= __('admin.default') ?>">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-lg-12">
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><?= __('admin.choose_background_option') ?></h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-start">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="radio" id="set_image_as_background" <?= (int)$theme['admin_login_background_option'] == 0 ? 'checked' : '' ?> name="theme[admin_login_background_option]" value="0">
                                        <label class="form-check-label" for="set_image_as_background"><?= __('admin.set_image_as_background') ?></label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="set_color_as_background" <?= (int)$theme['admin_login_background_option'] == 1 ? 'checked' : '' ?> name="theme[admin_login_background_option]" value="1">
                                        <label class="form-check-label" for="set_color_as_background"><?= __('admin.set_color_as_background') ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-lg-12">
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">
                                <div class="row align-items-center theme-setting-row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label"><?= __('admin.admin_login_background_color') ?></label>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control form-control-color me-2" type="color" name="theme[admin_login_background_color]" value="<?= $theme['admin_login_background_color'] != '' ? $theme['admin_login_background_color'] : '#5e7590' ?>">
                                    </div>
                                    <div class="col-md-2 text-end mt-2">
                                        <button class="btn btn-primary default-theme-setting" value="admin_login_background_color" title="<?= __('admin.default') ?>">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <h5><?= __('admin.admin_login_background_image') ?></h5>
                                        <hr>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="theme_admin-login-background-image" class="btn btn-primary"><?= __('admin.choose_file') ?></label>
                                        <input id="theme_admin-login-background-image" class="form-control form-control-file d-none" type="file" name="theme_admin-login-background-image" onchange="readURLAndSetValue(this,'theme[admin-login-background-image]','#admin-login-background-image')">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="hidden" name="theme[admin-login-background-image]" value="<?= $theme['admin-login-background-image'] ?>">
                                        <?php $admin_login_background_image = $theme['admin-login-background-image'] ? base_url('assets/images/site/'. $theme['admin-login-background-image']) : base_url('assets/admin/img/bg-main.png'); ?>
                                        <img id="admin-login-background-image" class='img-fluid mt-3' src="<?= $admin_login_background_image ?>" style="max-width: 200px;">
                                    </div>
                                    <div class="col-md-3">
                                        <?php if($theme['admin-login-background-image']) { ?>
                                        <button class="btn btn-danger btn-delete-image mt-3" data-img_input="theme[admin-login-background-image]" data-img_ele="admin-login-background-image" data-img_placeholder="<?= base_url('assets/admin/img/bg-main.png');?>"><i class="bi bi-trash-fill"></i></button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </fieldset>
        </div>
    </div>
</div>

<div class="tab-pane p-3" id="user-dashboard-setting" role="tabpanel">
    <fieldset>
        <legend><?= __('admin.user_dashboard_notice') ?></legend>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input update_all_settings" type="checkbox" <?= $userdashboard['invitation_link_id'] == 1 ? 'checked' : '' ?> data-bs-toggle="toggle" data-bs-size="normal" data-bs-on="<?= __('admin.status_on') ?>" data-bs-off="<?= __('admin.status_off') ?>" data-setting_key="invitation_link_id" data-setting_type="userdashboard">
                    <label class="form-check-label"><?= __('admin.invitation_link_id') ?></label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input update_all_settings" type="checkbox" <?= $userdashboard['top_affiliate'] == 1 ? 'checked' : '' ?> data-bs-toggle="toggle" data-bs-size="normal" data-bs-on="<?= __('admin.status_on') ?>" data-bs-off="<?= __('admin.status_off') ?>" data-setting_key="top_affiliate" data-setting_type="userdashboard">
                    <label class="form-check-label"><?= __('admin.top_affiliate') ?></label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input update_all_settings" type="checkbox" <?= $userdashboard['contact_us_page'] == 1 ? 'checked' : '' ?> data-bs-toggle="toggle" data-bs-size="normal" data-bs-on="<?= __('admin.status_on') ?>" data-bs-off="<?= __('admin.status_off') ?>" data-setting_key="contact_us_page" data-setting_type="userdashboard">
                    <label class="form-check-label"><?= __('admin.contact_us_page') ?></label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input update_all_settings" type="checkbox" <?= $userdashboard['tickets_page'] == 1 ? 'checked' : '' ?> data-bs-toggle="toggle" data-bs-size="normal" data-bs-on="<?= __('admin.status_on') ?>" data-bs-off="<?= __('admin.status_off') ?>" data-setting_key="tickets_page" data-setting_type="userdashboard">
                    <label class="form-check-label"><?= __('admin.tickets_page') ?></label>
                </div>
            </div>
        </div>
    </fieldset>

    <fieldset class="mt-4">
        <legend><?= __('admin.display_welcome_popup') ?></legend>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form-label"><?= __('admin.popup_display') ?></label>
                    <select class="form-control" name="welcome[show_popup]">
                        <option value="enable" <?= $welcome['show_popup'] == 'enable' ? 'selected' : '' ?>><?= __('admin.enable') ?></option>
                        <option value="disable" <?= $welcome['show_popup'] == 'disable' ? 'selected' : '' ?>><?= __('admin.disable') ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label"><?= __('admin.popup_title') ?></label>
                    <input placeholder="<?= __('admin.enter_page_title') ?>" id="welcome[heading]" name="welcome[heading]" value="<?php echo $welcome['heading']; ?>" class="form-control" type="text">
                </div>
                <div class="form-group mb-3">
                    <label class="form-label"><?= __('admin.popup_video_link') ?> <span data-html="true" data-bs-placement="right" data-bs-toggle="tooltip" data-bs-container="body" title="<h6>we support all links like:</h6> <ul><li>https://www.youtube.com/watch?v=R1StjWM_LOE&feature=youtu.be</li><li>https://www.youtu.com/R1StjWM_LOE</li><li>https://www.youtube.com/embed/R1StjWM_LOE</li></ul>"></span></label>
                    <input placeholder="<?= __('admin.enter_url_link_video') ?>" name="welcome[video_link]" id="videolink" class="form-control" value="<?php echo $welcome['video_link']; ?>" type="text">
                    <span class="text-danger" id="linkError"></span>
                </div>
                <div class="form-group">
                    <label class="form-label"><?= __('admin.popup_content') ?></label>
                    <textarea name="welcome[content]" id="welcome[content]" class="form-control" rows="4"><?php echo $welcome['content']; ?></textarea>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group mt-3">
                    <?php
                    $video_link = $welcome['video_link'];

                    if (strpos(strtolower($video_link), 'youtube') !== false && strpos($video_link, 'embed') == false) {
                        $id = explode("v=", $video_link);
                        $video_link = 'https://www.youtube.com/embed/' . $id[1];
                    }
                    if (strpos(strtolower($video_link), 'youtu.be') !== false && strpos($video_link, 'embed') == false) {
                        $id = explode("/", $video_link);
                        $video_link = 'https://www.youtube.com/embed/' . $id[3];
                    }
                    ?>
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="<?php echo $video_link ?>" allowfullscreen id="ifrm_videoid"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
</div>


<?php   
    $site_url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].''.$_SERVER['REDIRECT_URL'];
    $root = rtrim($site_url, 'admincontrol/paymentsetting/');
?>

<div class="tab-pane p-3" id="security" role="tabpanel">
    <div class="change_admin_url mt-3">
        <label><?= __('admin.change_admin_url') ?></label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon3"><?php echo $root.'/'?></span>
            </div>
            <input type="text" class="form-control" name="security[admin_url]" aria-describedby="basic-addon3" value="<?php if($security['admin_url'] == null) {echo "admin";} else {echo $security['admin_url'];} ?>">
            <span class="input-group-text">
                <a href="javascript:void(0)" class="set-default-admin-url">
                    <?= __('admin.set_default') ?>
                </a>
            </span>
        </div>
    </div>

    <div class="change_front_url mt-3">
        <label><?= __('admin.change_front_url') ?></label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon3"><?php echo $root.'/'?></span>
            </div>
            <input type="text" class="form-control" name="security[front_url]" aria-describedby="basic-addon3" value="<?php echo $security['front_url']; ?>">
            <span class="input-group-text">
                <a href="javascript:void(0)" class="set-default-front-url">
                    <?= __('admin.set_default') ?>
                </a>
            </span>
        </div>
    </div>

    <div class="force_ssl mt-3">
        <div class="input-group mt-3">
            <label class="control-label" style="margin-right: 10px;"><?= __('admin.force_ssl') ?></label>
            <div class="form-switch">
                <input class="form-check-input update_all_settings" type="checkbox" <?= $security['force_ssl'] == 1 ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('admin.status_on') ?>" data-off="<?= __('admin.status_off') ?>" data-setting_key="force_ssl" data-setting_type="security">
            </div>
        </div>
    </div>
</div>


<div class="tab-pane p-3 active show" id="site-setting" role="tabpanel">
    <div class="accordion" id="settingsAccordion">
    <!-- Site Settings -->
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingSite">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSite" aria-expanded="true" aria-controls="collapseSite">
                <?= __('admin.site_settings') ?>
            </button>
        </h2>
        <div id="collapseSite" class="accordion-collapse collapse show" aria-labelledby="headingSite" data-bs-parent="#settingsAccordion">
            <div class="accordion-body">
                <!-- Maintenance Mode -->
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input update_all_settings" type="checkbox" id="maintenance_mode" <?= $site['maintenance_mode']==1 ? 'checked' : '' ?> data-bs-toggle="toggle" data-bs-size="sm" data-bs-on="<?= __('admin.status_on') ?>" data-bs-off="<?= __('admin.status_off') ?>" data-setting_key="maintenance_mode" data-setting_type="site" data-bs-toggle="tooltip" title="<?= __('admin.front_site_maintainance_mode') ?>">
                    <label class="form-check-label" for="maintenance_mode"data-bs-toggle="tooltip" title="<?= __('admin.front_site_maintainance_mode') ?>"><?= __('admin.front_site_maintainance_mode') ?></label>
                </div>

                <!-- Store Maintenance Mode -->
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input update_all_settings" type="checkbox" id="store_maintenance_mode" <?= $site['store_maintenance_mode']==1 ? 'checked' : '' ?> data-bs-toggle="toggle" data-bs-size="sm" data-bs-on="<?= __('admin.status_on') ?>" data-bs-off="<?= __('admin.status_off') ?>" data-setting_key="store_maintenance_mode" data-setting_type="site" data-bs-toggle="tooltip" title="<?= __('admin.store_maintenance_mode') ?>">
                    <label class="form-check-label" for="store_maintenance_mode" data-bs-toggle="tooltip" title="<?= __('admin.store_maintenance_mode') ?>"><?= __('admin.store_maintenance_mode') ?></label>
                </div>

                <!-- Enable Shorten Numbers -->
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input update_all_settings" 
                           type="checkbox" 
                           id="enable_shorten_numbers" 
                           <?= isset($site['enable_shorten_numbers']) && $site['enable_shorten_numbers'] == 1 ? 'checked' : '' ?>
                           data-bs-toggle="toggle" 
                           data-bs-size="sm" 
                           data-bs-on="<?= __('admin.status_on') ?>"
                           data-bs-off="<?= __('admin.status_off') ?>"
                           data-setting_key="enable_shorten_numbers" 
                           data-setting_type="site"
                           data-bs-toggle="tooltip" 
                           title="<?= __('admin.before_after_example') ?>">
                    <label class="form-check-label" 
                           for="enable_shorten_numbers"
                           data-bs-toggle="tooltip" 
                           title="<?= __('admin.before_after_example') ?>">
                        <?= __('admin.enable_shorten_numbers') ?>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Store Settings -->
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingStore">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStore" aria-expanded="false" aria-controls="collapseStore">
                <?= __('admin.mail_settings') ?>
            </button>
        </h2>
        <div id="collapseStore" class="accordion-collapse collapse" aria-labelledby="headingStore" data-bs-parent="#settingsAccordion">
            <div class="accordion-body">
                <!-- User Account Mail Verification -->
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input update_all_settings" type="checkbox" id="mail_verifiy" <?= $store['mail_verifiy']==1 ? 'checked' : '' ?> data-bs-toggle="toggle" data-bs-size="sm" data-bs-on="<?= __('admin.status_on') ?>" data-bs-off="<?= __('admin.status_off') ?>" data-setting_key="mail_verifiy" data-setting_type="store">
                    <label class="form-check-label" for="mail_verifiy"><?= __('admin.user_account_mail_verification') ?></label>
                </div>

                <!-- Approval For Registration -->
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input update_all_settings regisapproval" type="checkbox" id="registration_approval" <?= $store['registration_approval']==1 ? 'checked' : '' ?> data-bs-toggle="toggle" data-bs-size="sm" data-bs-on="<?= __('admin.status_on') ?>" data-bs-off="<?= __('admin.status_off') ?>" data-setting_key="registration_approval" data-setting_type="store">
                    <label class="form-check-label" for="registration_approval"><?= __('admin.approval_for_registration') ?></label>
                </div>
            </div>
        </div>
    </div>

    <!-- Language Settings -->
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingLanguage">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLanguage" aria-expanded="false" aria-controls="collapseLanguage">
                <?= __('admin.language_settings') ?>
            </button>
        </h2>
        <div id="collapseLanguage" class="accordion-collapse collapse" aria-labelledby="headingLanguage" data-bs-parent="#settingsAccordion">
            <div class="accordion-body">
                <!-- Show Language Dropdown -->
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input update_all_settings" type="checkbox" id="language_status" <?= $store['language_status']==1 ? 'checked' : '' ?> data-bs-toggle="toggle" data-bs-size="sm" data-bs-on="<?= __('admin.status_on') ?>" data-bs-off="<?= __('admin.status_off') ?>" data-setting_key="language_status" data-setting_type="store">
                    <label class="form-check-label" for="language_status"><?= __('admin.show_language_dropdown') ?></label>
                </div>
            </div>
        </div>
    </div>

    <!-- Currency Settings -->
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingCurrency">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCurrency" aria-expanded="false" aria-controls="collapseCurrency">
                <?= __('admin.currency_settings') ?>
            </button>
        </h2>
        <div id="collapseCurrency" class="accordion-collapse collapse" aria-labelledby="headingCurrency" data-bs-parent="#settingsAccordion">
            <div class="accordion-body">
                <!-- Hide Currency From -->
                <label class="form-check-label mb-2"><?= __('admin.hide_currency_from') ?></label>
                <?php
                    $hcf = isset($site['hide_currency_from']) && !empty($site['hide_currency_from']) ? explode(',', $site['hide_currency_from']) : [];
                ?>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="site[hide_currency_from][]" value="admin" <?= in_array('admin', $hcf) ? 'checked' : '' ?> id="hide_currency_from_admin">
                    <label class="form-check-label" for="hide_currency_from_admin"><?= __('admin.admin_dashboard') ?></label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="site[hide_currency_from][]" value="user" <?= in_array('user', $hcf) ? 'checked' : '' ?> id="hide_currency_from_user">
                    <label class="form-check-label" for="hide_currency_from_user"><?= __('admin.user_dashboard') ?></label>
                </div>
            </div>
        </div>
    </div>

<?php
    $zones_array = array();
    $timestamp = time();
    foreach(timezone_identifiers_list() as $key => $zone) {
        date_default_timezone_set($zone);
        $zones_array[$zone] = date('P', $timestamp) . " {$zone} ";
    }
 ?>

    <!-- Time Zone Settings -->
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingTimeZone">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTimeZone" aria-expanded="false" aria-controls="collapseTimeZone">
                <?= __('admin.time_zone') ?>
            </button>
        </h2>
        <div id="collapseTimeZone" class="accordion-collapse collapse" aria-labelledby="headingTimeZone" data-bs-parent="#settingsAccordion">
            <div class="accordion-body">
                <label for="time_zone" class="form-label"><?= __('admin.select_time_zone') ?></label>
                <select class="form-select" name="site[time_zone]" id="time_zone">
                    <?php foreach ($zones_array as $key => $value): ?>
                        <option value="<?= $key ?>" <?= $site['time_zone'] == $key ? 'selected' : '' ?>><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Registration Form Settings -->
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingRegistrationForm">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRegistrationForm" aria-expanded="false" aria-controls="collapseRegistrationForm">
                <?= __('admin.registration_form') ?>
            </button>
        </h2>
        <div id="collapseRegistrationForm" class="accordion-collapse collapse" aria-labelledby="headingRegistrationForm" data-bs-parent="#settingsAccordion">
            <div class="accordion-body">
                <label for="registration_status" class="form-label"><?= __('admin.select_registration_status') ?></label>
                <select class="form-select" name="store[registration_status]" id="registration_status">
                    <option value="1" <?= ($store['registration_status'] == 1) ? 'selected' : '' ?>><?= __('admin.enable_affiliate_vendor_registration') ?></option>
                    <option value="0" <?= ($store['registration_status'] == 0) ? 'selected' : '' ?>><?= __('admin.disable_affiliate_vendor_registration') ?></option>
                    <option value="2" <?= ($store['registration_status'] == 2) ? 'selected' : '' ?>><?= __('admin.disable_affiliate_registration') ?></option>
                    <option value="3" <?= ($store['registration_status'] == 3) ? 'selected' : '' ?>><?= __('admin.disable_vendor_registration') ?></option>
                </select>
            </div>
        </div>
    </div>

    <!-- Notification Email Settings -->
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingNotificationEmail">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNotificationEmail" aria-expanded="false" aria-controls="collapseNotificationEmail">
                <?= __('admin.notification_email') ?>
            </button>
        </h2>
        <div id="collapseNotificationEmail" class="accordion-collapse collapse" aria-labelledby="headingNotificationEmail" data-bs-parent="#settingsAccordion">
            <div class="accordion-body">
                <label for="notify_email" class="form-label"><?= __('admin.enter_notification_email') ?></label>
                <input name="site[notify_email]" value="<?php echo $site['notify_email']; ?>" class="form-control" id="notify_email" type="email">
            </div>
        </div>
    </div>

    <!-- Session Timeout Settings -->
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingSessionTimeout">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSessionTimeout" aria-expanded="false" aria-controls="collapseSessionTimeout">
                <?= __('admin.admin_session_timeout') ?>
            </button>
        </h2>
        <div id="collapseSessionTimeout" class="accordion-collapse collapse" aria-labelledby="headingSessionTimeout" data-bs-parent="#settingsAccordion">
            <div class="accordion-body">
                <label for="session_timeout" class="form-label"><?= __('admin.session_timeout_timing_in_seconds') ?></label>
                <input name="site[session_timeout]" value="<?php echo $site['session_timeout']; ?>" class="form-control" id="session_timeout" placeholder="<?= __('admin.default_timeout_is_1800_seconds') ?>" onkeypress="return isNumeric(event)" oninput="maxLengthCheck(this)" type="number" maxlength="6" min = "1" max = "999999">
            </div>
        </div>
    </div>

    <!-- User Session Timeout Settings -->
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingUserSessionTimeout">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUserSessionTimeout" aria-expanded="false" aria-controls="collapseUserSessionTimeout">
                <?= __('admin.user_session_timeout') ?>
            </button>
        </h2>
        <div id="collapseUserSessionTimeout" class="accordion-collapse collapse" aria-labelledby="headingUserSessionTimeout" data-bs-parent="#settingsAccordion">
            <div class="accordion-body">
                <label for="user_session_timeout" class="form-label"><?= __('admin.user_session_timeout_timing_in_seconds') ?></label>
                <input name="site[user_session_timeout]" value="<?php echo $site['user_session_timeout']; ?>" class="form-control" id="user_session_timeout" placeholder="<?= __('admin.default_timeout_is_1800_seconds') ?>" onkeypress="return isNumeric(event)" oninput="maxLengthCheck(this)" type="number" maxlength="6" min = "1" max = "999999">
            </div>
        </div>
    </div>

    <!-- Notification Sound Settings -->
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingNotificationSound">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNotificationSound" aria-expanded="false" aria-controls="collapseNotificationSound">
                <?= __('admin.notification_sound') ?>
            </button>
        </h2>
        <div id="collapseNotificationSound" class="accordion-collapse collapse" aria-labelledby="headingNotificationSound" data-bs-parent="#settingsAccordion">
            <div class="accordion-body">
                <?php 
                    $arrFiles = array();
                    $handle = opendir('assets/notify');
                    if ($handle) {
                        while (($entry = readdir($handle)) !== FALSE) {
                            $arrFiles[] = $entry;
                        }
                    }
                    
                    foreach ($arrFiles as $file) {
                        $allowed = array('mp3', 'mp4');
                        $filename = $file;
                        $ext = pathinfo($filename, PATHINFO_EXTENSION);
                        $checked = '';
                    
                        if ($audio_sound != '') {
                            if ($filename == $audio_sound) {
                                $checked = 'checked';
                            }
                        }
                    
                        if (in_array($ext, $allowed)) {
                            ?>
                            <div class="sound-main mb-2">
                                <input type="radio" name="site[notification_sound]" value="<?= $file ?>" <?= $checked ?>>
                                <div class="audio-file">
                                    <?php  echo $file; ?>
                                </div>
                                <audio class="audio-control" controls>
                                    <source src="<?= base_url('/assets/notify/'.$file) ?>" type="audio/mpeg">
                                </audio>
                            </div>
                            <?php   
                        }
                    }
                ?>
            </div>
        </div>
    </div>

    </div>
</div>

<div class="tab-pane p-3" id="site-meta" role="tabpanel">
    <!-- Website Name and Footer -->
    <div class="card mb-4">
        <div class="card-header"><?= __('admin.site_information') ?></div>
        <div class="card-body">
            <div class="form-group">
                <label for="websiteName" class="form-label"><?= __('admin.website_name') ?></label>
                <input name="site[name]" id="websiteName" value="<?php echo $site['name']; ?>" class="form-control" type="text">
            </div>
            <div class="form-group">
                <label for="footerText" class="form-label"><?= __('admin.footer_text') ?></label>
                <input name="site[footer]" id="footerText" value="<?php echo $site['footer']; ?>" class="form-control" type="text">
            </div>
        </div>
    </div>

    <!-- Meta Tag Info -->
    <div class="card">
        <div class="card-header"><?= __('admin.meta_tag') ?></div>
        <div class="card-body">
            <div class="form-group">
                <label for="description" class="form-label"><?= __('admin.description') ?></label>
                <input name="site[meta_description]" id="description" value="<?php echo $site['meta_description']; ?>" class="form-control" type="text">
            </div>
            <div class="form-group">
                <label for="keywords" class="form-label"><?= __('admin.keywords') ?></label>
                <input name="site[meta_keywords]" id="keywords" value="<?php echo $site['meta_keywords']; ?>" class="form-control" type="text">
            </div>
            <div class="form-group">
                <label for="author" class="form-label"><?= __('admin.author') ?></label>
                <input name="site[meta_author]" id="author" value="<?php echo $site['meta_author']; ?>" class="form-control" type="text">
            </div>
        </div>
    </div>
</div>

                           
<div class="tab-pane p-3" id="site-logo" role="tabpanel">
    <div class="form-group"> 
        <fieldset>
            <legend><?= __('admin.admin_side_logo') ?></legend>
            <div class="row">
                <div class="col-sm-2">
                    <div class="fileUpload btn btn-sm btn-primary">
                        <span><?= __('admin.choose_file') ?></span>
                        <input name="site_admin-side-logo" class="upload" type="file" onchange="readURLAndSetValue(this,'site[admin-side-logo]','#admin-side-logo')">
                    </div>
                    <p class="logo-info-text"><?= __('admin.admin_side_logo_recommended_size') ?></p>
                </div>
                <div class="col-sm-10">
                    <input type="hidden" name="site[admin-side-logo]" value="<?= $site['admin-side-logo'] ?>">
                    <?php $admin_side_logo = $site['admin-side-logo'] ? base_url('assets/images/site/'. $site['admin-side-logo']) : base_url('assets/vertical/assets/images/no_image_yet.png'); ?>
                    <img id="admin-side-logo" class='img-responsive_setting' src="<?= $admin_side_logo ?>" style="width: 150px;">
                    <?php if($site['admin-side-logo']) { ?>
                    <span class="btn btn-sm btn-danger btn-delete-image" data-img_input="site[admin-side-logo]" data-img_ele="admin-side-logo" data-img_placeholder="<?= base_url('assets/vertical/assets/images/no_image_yet.png');?>"><i class="fa fa-trash"></i></span>
                    <?php } ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label  class="control-label"><?= __('admin.site_setting_logo_custom_size') ?></label>
                        <select name="site[custom_logo_size]" class="form-control">
                            <option value="0"><?= __('admin.disable') ?></option>
                            <option <?php echo ($site['custom_logo_size'] == 1) ? "selected" :""; ?> value="1"><?= __('admin.user_dashboard') ?></option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4 logo_cust_size_inp" <?php echo ($site['custom_logo_size'] != 1) ? 'style="display:none;"' :""; ?>>
                    <div class="form-group">
                        <label  class="control-label"><?= __('admin.site_setting_logo_width') ?></label>
                        <input name="site[log_custom_width]" value="<?php echo $site['log_custom_width']; ?>" class="form-control" type="number">
                    </div>
                </div>
                <div class="col-sm-4 logo_cust_size_inp" <?php echo ($site['custom_logo_size'] != 1) ? 'style="display:none;"':""; ?>>
                    <div class="form-group">
                        <label  class="control-label"><?= __('admin.site_setting_logo_height') ?></label>
                        <input name="site[log_custom_height]" value="<?php echo $site['log_custom_height']; ?>" class="form-control" type="number">
                    </div>
                </div>
                <script type="text/javascript">
                    $(document).on('change', 'select[name="site[custom_logo_size]"]', function() {
                        if($(this).val() == 1) {
                            $('.logo_cust_size_inp').show();
                        } else {
                            $('.logo_cust_size_inp').hide();
                        }
                    });
                </script>
            </div>
        </fieldset>
    </div>
   
   <div class="form-group"> 
        <fieldset>
            <legend><?= __('admin.front_side_themes_logo') ?></legend>
            <div class="row">
                <div class="col-sm-2">
                    <div class="fileUpload btn btn-sm btn-primary">
                        <span><?= __('admin.choose_file') ?></span>
                        <input name="site_front-side-themes-logo" class="upload" type="file" onchange="readURLAndSetValue(this,'site[front-side-themes-logo]','#front-side-themes-logo')">
                    </div>
                    <p class="logo-info-text"><?= __('admin.front_side_themes_logo_recommended_size') ?></p>
                </div>
                <div class="col-sm-10">
                    <input type="hidden" name="site[front-side-themes-logo]" value="<?= $site['front-side-themes-logo'] ?>">
                    <?php $front_side_themes_logo = $site['front-side-themes-logo'] ? base_url('assets/images/site/'. $site['front-side-themes-logo']) : base_url('assets/vertical/assets/images/no_image_yet.png'); ?>
                    <img id="front-side-themes-logo" class='img-responsive_setting' src="<?= $front_side_themes_logo ?>" style="width: 150px;">
                    <?php if($site['front-side-themes-logo']) { ?>
                    <span class="btn btn-sm btn-danger btn-delete-image" data-img_input="site[front-side-themes-logo]" data-img_ele="front-side-themes-logo" data-img_placeholder="<?= base_url('assets/vertical/assets/images/no_image_yet.png');?>"><i class="fa fa-trash"></i></span>
                    <?php } ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label  class="control-label"><?= __('admin.site_setting_logo_custom_size') ?></label>
                        <select name="site[front_custom_logo_size]" class="form-control">
                            <option value="0"><?= __('admin.disable') ?></option>
                            <option <?php echo ($site['front_custom_logo_size'] == 1) ? "selected" :""; ?> value="1"><?= __('admin.front_side_themes') ?></option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4 front_logo_cust_size_inp" <?php echo ($site['front_custom_logo_size'] != 1) ? 'style="display:none;"' :""; ?>>
                    <div class="form-group">
                        <label  class="control-label"><?= __('admin.site_setting_logo_width') ?></label>
                        <input name="site[front_log_custom_width]" value="<?php echo $site['front_log_custom_width']; ?>" class="form-control" type="number">
                    </div>
                </div>
                <div class="col-sm-4 front_logo_cust_size_inp" <?php echo ($site['front_custom_logo_size'] != 1) ? 'style="display:none;"':""; ?>>
                    <div class="form-group">
                        <label  class="control-label"><?= __('admin.site_setting_logo_height') ?></label>
                        <input name="site[front_log_custom_height]" value="<?php echo $site['front_log_custom_height']; ?>" class="form-control" type="number">
                    </div>
                </div>
                <script type="text/javascript">
                    $(document).on('change', 'select[name="site[front_custom_logo_size]"]', function() {
                        if($(this).val() == 1) {
                            $('.front_logo_cust_size_inp').show();
                        } else {
                            $('.front_logo_cust_size_inp').hide();
                        }
                    });
                </script>
            </div>
        </fieldset>
    </div>

    <div class="form-group"> 
        <fieldset>
            <legend><?= __('admin.website_favicon') ?></legend>
            <div class="row">
                <div class="col-sm-2">
                    <div class="fileUpload btn btn-sm btn-primary">
                        <span><?= __('admin.choose_file') ?></span>
                        <input name="site_favicon" class="upload" type="file" onchange="readURLAndSetValue(this,'site[favicon]','#site-favicon')">
                    </div>
                </div>
                <div class="col-sm-10">
                    <input type="hidden" name="site[favicon]" value="<?= $site['favicon'] ?>">
                    <?php $img = $site['favicon'] ? base_url('assets/images/site/'. $site['favicon']) : base_url('assets/vertical/assets/images/no_image_yet.png'); ?>
                    <img id='site-favicon' class='img-responsive_setting' src="<?= $img ?>" style="width: 150px;">
                    <?php if($site['favicon']) { ?>
                    <span class="btn btn-sm btn-danger btn-delete-image" data-img_input="site[favicon]" data-img_ele="site-favicon" data-img_placeholder="<?= base_url('assets/vertical/assets/images/no_image_yet.png');?>"><i class="fa fa-trash"></i></span>
                    <?php } ?>
                </div>
            </div>
        </fieldset>
    </div>

    <div class="form-group">
        <fieldset>
            <legend><?= __('admin.global_script') ?></legend>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <textarea rows="8" name="site[global_script]" class="form-control site-global_script" placeholder="<?= __('admin.insert_script_here') ?>"><?php echo $site['global_script']; ?></textarea>
                    </div>
                </div>
                <div class="col-sm-6">
                    <?php  $global_script_status = (array)json_decode($site['global_script_status'],1); ?>
                    <div class="form-group">
                        <label class="control-label"><?= __('admin.show_global_script') ?></label>
                        <div>
                            <div>
                                <label>
                                <input type="checkbox" <?= in_array('admin', $global_script_status) ? 'checked' : '' ?> name="site[global_script_status][]" value="admin"> <?= __('admin.option_admin_side') ?>
                                </label>
                            </div>
                            <div>
                                <label>
                                <input type="checkbox" <?= in_array('affiliate', $global_script_status) ? 'checked' : '' ?> name="site[global_script_status][]" value="affiliate"> <?= __('admin.option_affiliate_side') ?>
                                </label>
                            </div>
                            <div>
                                <label>
                                <input type="checkbox" <?= in_array('front', $global_script_status) ? 'checked' : '' ?> name="site[global_script_status][]" value="front"> <?= __('admin.option_front_side') ?>
                                </label>
                            </div>
                            <div>
                                <label>
                                <input type="checkbox" <?= in_array('store', $global_script_status) ? 'checked' : '' ?> name="site[global_script_status][]" value="store"> <?= __('admin.option_store_side') ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>

    <div class="form-group">
        <fieldset>
            <legend><?= __('admin.google_analytics_for_site_page') ?></legend>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <textarea rows="8" name="site[google_analytics]" class="form-control site-google_analytics" placeholder="<?= __('admin.insert_script_here') ?>"><?php echo $site['google_analytics']; ?></textarea>
                        <a href="https://support.google.com/analytics/answer/1008080?hl=en" target="_blank"><?= __('admin.get_analytics_code') ?></a>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label"><?= __('admin.example') ?></label>
                        <img class="img-responsive_setting w-100" src="<?= base_url('assets/images/google_analytics.png') ?>">
                    </div>
                </div>
            </div>
        </fieldset>
    </div>

    <div class="form-group">
        <fieldset>
            <legend><?= __('admin.faceboook_pixel_for_site_page') ?></legend>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="control-label"><?= __('admin.faceboook_pixel_for_site_page') ?></label>
                    <textarea rows="8" name="site[faceboook_pixel]" class="form-control site-faceboook_pixel" placeholder="<?= __('admin.insert_script_here') ?>"><?php echo $site['faceboook_pixel']; ?></textarea>
                    <a href="https://developers.facebook.com/docs/facebook-pixel/implementation" target="_blank"><?= __('admin.get_facebook_pixel_code') ?></a>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="control-label"><?= __('admin.example') ?></label>
                    <img class="img-responsive_setting w-100" src="<?= base_url('assets/images/faceboook_pixel.png') ?>">
                </div>
            </div>
        </div>
    </fieldset>
    </div>
  
    <div class="form-group"> 
        <fieldset>
            <legend><?= __('admin.facebook_chat_plugin_script') ?></legend>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <textarea rows="8" name="site[fbmessager_script]" class="form-control site-fbmessager_script" placeholder="<?= __('admin.insert_script_here') ?>"><?php echo $site['fbmessager_script']; ?></textarea>
                    </div>
                    <?php  $fbmessager_status = (array)json_decode($site['fbmessager_status'],1); ?>
                    <div class="form-group">
                        <label class="control-label"><?= __('admin.show_facebook_chat_code') ?> :</label>
                        <div>
                            <div style="display:inline-block;">
                                <label>
                                <input type="checkbox" <?= in_array('admin', $fbmessager_status) ? 'checked' : '' ?> name="site[fbmessager_status][]" value="admin"> <?= __('admin.option_admin_side') ?>
                                </label>
                            </div>
                            &nbsp;&nbsp;
                            <div style="display:inline-block;">
                                <label>
                                <input type="checkbox" <?= in_array('affiliate', $fbmessager_status) ? 'checked' : '' ?> name="site[fbmessager_status][]" value="affiliate"> <?= __('admin.option_affiliate_side') ?>
                                </label>
                            </div>
                            &nbsp;&nbsp;
                            <div style="display:inline-block;">
                                <label>
                                <input type="checkbox" <?= in_array('front', $fbmessager_status) ? 'checked' : '' ?> name="site[fbmessager_status][]" value="front"> <?= __('admin.option_front_side') ?>
                                </label>
                            </div>
                            &nbsp;&nbsp;
                            <div style="display:inline-block;">
                                <label>
                                <input type="checkbox" <?= in_array('store', $fbmessager_status) ? 'checked' : '' ?> name="site[fbmessager_status][]" value="store"> <?= __('admin.option_store_side') ?>
                                </label>
                            </div>
                            &nbsp;&nbsp;
                        </div>
                    </div>
                    <a class="mt-2" href="https://developers.facebook.com/docs/messenger-platform/discovery/facebook-chat-plugin/#setup_tool" target="_blank"><?= __('admin.get_facebook_chat_code') ?></a>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label"><?= __('admin.example') ?></label>
                        <img class="img-responsive_setting w-100" src="<?= base_url('assets/images/fb_chat_script.png') ?>">
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
</div>

        <div class="tab-pane p-3" id="login-2" role="tabpanel"> 
        </div>

        <div class="tab-pane p-3" id="email-setting" role="tabpanel">
            <div class="form-group">
                <label class="control-label"><?= __('admin.send_email') ?></label>
                <select class="form-control" name="email[mail_send_option]">
                    <option value="enable" <?= $email['mail_send_option'] == 'enable' ? 'selected' : '' ?>><?= __('admin.enable') ?></option>
                    <option value="disable" <?= $email['mail_send_option'] == 'disable' ? 'selected' : '' ?>><?= __('admin.disable') ?></option>
                </select>
            </div>
            <div class="form-group">
                <label class="control-label"><?= __('admin.mail_type') ?></label>
                <select class="form-control" name="email[mail_type]">
                    <option value="smtp" <?= $email['mail_type'] == 'smtp' ? 'selected' : '' ?>><?= __('admin.smtp') ?></option>
                    <option value="php_mailer" <?= $email['mail_type'] == 'php_mailer' ? 'selected' : '' ?>><?= __('admin.php_mailer') ?></option>
                </select>
            </div>
            <div class="form-group">
                <label  class="control-label"><?= __('admin.from_email') ?></label>
                <input name="email[from_email]" value="<?php echo $email['from_email']; ?>" class="form-control" type="text">
            </div>
            <div class="form-group">
                <label  class="control-label"><?= __('admin.from_name') ?></label>
                <input name="email[from_name]" value="<?php echo $email['from_name']; ?>" class="form-control" type="text">
            </div>
            <div class="form-group for-smtp-mail">
                <label  class="control-label"><?= __('admin.smtp_hostname') ?></label>
                <input name="email[smtp_hostname]" value="<?php echo $email['smtp_hostname']; ?>" class="form-control" type="text">
            </div>
            <div class="form-group for-smtp-mail">
                <label  class="control-label"><?= __('admin.smtp_username') ?></label>
                <input name="email[smtp_username]" value="<?php echo $email['smtp_username']; ?>" class="form-control" type="text">
            </div>
            <div class="form-group for-smtp-mail">
                <label  class="control-label"><?= __('admin.smtp_password') ?></label>
                <div class="input-group password-group">
                    <input readonly="" onfocus="this.removeAttribute('readonly');" onblur="this.setAttribute('readonly','readonly');" autocomplete="off" type="password" class="form-control" name="email[smtp_password]" value="<?php echo $email['smtp_password']; ?>">
                    <div class="input-group-prepend">
                        <button class="btn btn-outline-secondary" type="button"><i class="fa fa-eye"></i></button>
                    </div>
                </div>
            </div>
            <div class="form-group for-smtp-mail">
                <label  class="control-label"><?= __('admin.smtp_port') ?></label>
                <input name="email[smtp_port]" value="<?php echo $email['smtp_port']; ?>" class="form-control" type="text">
            </div>
            <div class="form-group for-smtp-mail">
                <label class="control-label"><?= __('admin.smtp_crypto') ?></label>
                <select class="form-control" name="email[smtp_crypto]">
                    <option value=""><?= __('admin.none') ?></option>
                    <option value="tls" <?= $email['smtp_crypto'] == 'tls' ? 'selected' : '' ?>><?= __('admin.tls') ?></option>
                    <option value="ssl" <?= $email['smtp_crypto'] == 'ssl' ? 'selected' : '' ?>><?= __('admin.ssl') ?></option>
                </select>
            </div>
            <div class="form-group">
                <label  class="control-label"><?= __('admin.unsubscribed_page_title') ?></label>
                <input name="email[unsubscribed_page_title]" value="<?php echo $email['unsubscribed_page_title']; ?>" class="form-control" type="text">
            </div>
            <div class="form-group">
                <label  class="control-label"><?= __('admin.unsubscribed_page_message') ?></label>
                <textarea name="email[unsubscribed_page_message]" class="form-control"><?php echo $email['unsubscribed_page_message']; ?></textarea>
            </div>
            <fieldset>
                <legend><?= __('admin.testing') ?></legend>
                <div class="input-group mb-3">
                    <input type="text" class="form-control testingemail" placeholder="<?= __('admin.test_email_send_on') ?>" aria-label="Recipient's username" aria-describedby="basic-addon2">
                    <div class="input-group-append cp">
                        <span class="btn btn-primary input-group-text send-test-mail" id="basic-addon2"><?= __('admin.send_test_mail') ?></span>
                    </div>
                </div>
            </fieldset>
        </div>

<div class="tab-pane p-3" id="tnc-page" role="tabpanel">
    <div class="mb-3">
        <label class="form-label"><?= __('admin.select_language') ?></label>
        <select class="form-select" name="tnc[language_id]" id="drpLanguage" onchange="return changeLanguage();">
            <?php 
            if(isset($languages))
            {
                $language_id=1;
                foreach($languages as $language)
                {?>
                <option <?php 
                if($language['is_default']==1) {echo 'selected';} ?> value="<?=$language['id']?>"><?=$language['name'] ?></option>
              
            <?php  }     
            }?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label"><?= __('admin.page_title') ?></label>
        <input placeholder="<?= __('admin.enter_page_title') ?>" name="tnc[heading]" value="<?php echo $tnc['heading']; ?>" class="form-control"  type="text">
    </div>
    <div class="mb-3">
        <label class="form-label"><?= __('admin.page_content') ?></label>
        <textarea name="tnc[content]" id="tnc[content]" class="form-control summernote-img"><?php echo $tnc['content']; ?></textarea>
    </div>
</div>


<div class="tab-pane p-3" id="tracking" role="tabpanel">
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title"><?= __('admin.affiliate_tracking') ?></h5>
            <div class="form-group">
                <label class="form-label"><?= __('admin.select_tracking_method') ?></label>
                <select class="form-select" name="site[affiliate_tracking_place]">
                    <option value="0" selected><?= __('admin.use_cookies') ?></option>
                    <option <?= $site['affiliate_tracking_place'] == 1 ? 'selected' : ''; ?> value="1"><?= __('admin.use_local_storage') ?></option>
                    <option <?= $site['affiliate_tracking_place'] == 2 ? 'selected' : ''; ?> value="2"><?= __('admin.use_cookies_and_local_storage_both') ?></option>
                </select>
            </div>
            <div class="form-group">
                <label  class="form-label"><?= __('admin.affiliate_cookie') ?></label>
                <input class="form-control" type="number" value="<?= $store['affiliate_cookie'] ?>" name="store[affiliate_cookie]">
            </div>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title"><?= __('admin.cookie_preferences') ?></h5>
            <div class="form-group">
                <label class="form-label"><?= __('admin.block_click_across_browser') ?></label>
            <div class="form-check form-switch">
                <input class="form-check-input update_all_settings" type="checkbox" <?= $site['block_click_across_browser']==1 ? 'checked' : '' ?> data-bs-toggle="toggle" data-bs-size="normal" data-bs-on="<?= __('admin.status_on') ?>" data-bs-off="<?= __('admin.status_off') ?>" data-setting_key="block_click_across_browser" data-setting_type="site">
            </div>
            </div>
            <div class="form-group">
                <label class="form-label"><?= __('admin.show_on_menu') ?></label>
                <div class="form-check form-switch">
                    <input class="form-check-input update_all_settings" type="checkbox" <?= $site['cookies_menu']==1 ? 'checked' : '' ?> data-bs-toggle="toggle" data-bs-size="normal" data-bs-on="<?= __('admin.status_on') ?>" data-bs-off="<?= __('admin.status_off') ?>" data-setting_key="cookies_menu" data-setting_type="site">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label"><?= __('admin.cookies_consent') ?></label>
                <div class="form-check form-switch">
                    <input class="form-check-input update_all_settings" type="checkbox" <?= $site['cookies_consent']==1 ? 'checked' : '' ?> data-bs-toggle="toggle" data-bs-size="normal" data-bs-on="<?= __('admin.status_on') ?>" data-bs-off="<?= __('admin.status_off') ?>" data-setting_key="cookies_consent" data-setting_type="site">
                </div>
            </div>
            <div class="form-group">
                <label  class="form-label"><?= __('admin.cookies_consent_messg_setting') ?></label>
                <input class="form-control" type="text" value="<?= (isset($site['cookies_consent_mesag']))? $site['cookies_consent_mesag']:__('admin.cookies_consent_default_message'); ?>" name="site[cookies_consent_mesag]">
            </div>
        </div>
    </div>
</div>


<div class="tab-pane py-3" id="googleads-setting" role="tabpanel">
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?= __('admin.google_ads_setting_key_section') ?></h5>
                    <div class="row">
                        <?php 
                            $adsSections = [1 => 'side_bar_ads', 3 => 'footer_ads', 4 => 'right_side_ads', 5 => 'center_page_ads'];
                            foreach($adsSections as $id => $section): 
                                $googleAds = $this->Setting_model->getGoogleAds($id);
                        ?>
                        <div class="col">
                            <div class="form-check form-switch">
                                <input class="form-check-input" name="googleadsStatus[<?= $id ?>]" type="checkbox" <?= $googleAds[0]['status'] == 1 ? 'checked' : '' ?> data-bs-toggle="toggle" data-bs-size="normal" data-bs-on="<?= __('admin.status_on') ?>" data-bs-off="<?= __('admin.status_off') ?>" data-setting_key="invitation_link_id" data-setting_type="userdashboard">
                                <label class="form-label"><?= __("admin.$section") ?></label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?= __('admin.client_ads_key') ?></h5>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-flex flex-wrap">
                                <div class="form-group me-3">
                                    <label class="form-label"><?= __('admin.client_ads_key') ?></label>
                                    <input placeholder="" id="googleads[client_key]" name="googleads[client_key]" value="" class="form-control" type="text">
                                </div>
                                <div class="form-group me-3">
                                    <label class="form-label"><?= __('admin.ads_unit_key') ?></label>
                                    <input placeholder="" id="googleads[unit_key]" name="googleads[unit_key]" value="" class="form-control" type="text">
                                </div>
                                <div class="form-group me-3">
                                    <label class="form-label"><?= __('admin.ads_section') ?></label>
                                    <select class="form-select" id="googleads[ad_section]" name="googleads[ad_section]">
                                        <option value="1"><?= __('admin.side_bar_top') ?></option>
                                        <option value="2"><?= __('admin.side_bar_bottom') ?></option>
                                        <option value="3"><?= __('admin.footer') ?></option>
                                        <option value="4"><?= __('admin.right_side') ?></option>
                                        <option value="5"><?= __('admin.center_page') ?></option>
                                    </select>
                                </div>
                                <input type="hidden" name="googleads[id]" id="googleads[id]" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?= __('admin.ads_list') ?></h5>
                    <div class="table-responsive mt-3">
                        <table class="table table-striped ads-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?= __('admin.client_key') ?></th>
                                    <th><?= __('admin.unit_key') ?></th>
                                    <th><?= __('admin.ad_section') ?></th>
                                    <th><?= __('admin.action') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1;
                                foreach ($googleads as $rows) { ?>
                                    <tr id="row_<?= $rows['id']; ?>">
                                        <td><?= $i; ?></td>
                                        <td><?= $rows['client_key']; ?></td>
                                        <td><?= $rows['unit_key']; ?></td>
                                        <td><?= ads_google_status($rows['ad_section']); ?></td>
                                        <td>
                                            <a class="btn btn-sm btn-primary" href="javascript:void(0)" onclick="editAds(<?= $rows['id']; ?>,'<?= $rows['client_key']; ?>','<?= $rows['unit_key']; ?>',<?= $rows['ad_section']; ?>)">
                                                <i class="fa fa-edit cursors" aria-hidden="true"></i>
                                            </a>
                                            <button data-id="<?= $rows['id']; ?>" data-bs-toggle="tooltip" data-bs-original-title="<?= __('admin.delete') ?>" class="btn btn-sm btn-danger btn-delete2">
                                                <i class="fa fa-trash-o cursors" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php $i++;
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="tab-pane p-3" id="googlerecaptcha-setting" role="tabpanel">
    <div class="row">
        <div class="col-lg-6">
            <!-- Card for Site Key and Secret Key -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?= __('admin.text_site_key') ?></h5>
                    <input class="form-control mb-3" type="text" value="<?= $googlerecaptcha['sitekey'] ?>" name="googlerecaptcha[sitekey]" id="site_key">
                    <h5 class="card-title"><?= __('admin.text_secret_key') ?></h5>
                    <input class="form-control mb-3" type="text" value="<?= $googlerecaptcha['secretkey'] ?>" name="googlerecaptcha[secretkey]" id="secret_key">
                </div>
            </div>

            <!-- Card for Login and Register Settings -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= __('admin.login_register_settings') ?></h5>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input update_all_settings" type="checkbox" <?= $googlerecaptcha['admin_login'] == 1 ? 'checked' : '' ?> data-bs-toggle="toggle" data-bs-size="normal" data-bs-on="<?= __('admin.status_on') ?>" data-bs-off="<?= __('admin.status_off') ?>" data-setting_key="admin_login" data-setting_type="googlerecaptcha">
                        <label class="form-check-label"><?= __('admin.admin_login') ?></label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input update_all_settings" type="checkbox" <?= $googlerecaptcha['affiliate_login'] == 1 ? 'checked' : '' ?> data-bs-toggle="toggle" data-bs-size="normal" data-bs-on="<?= __('admin.status_on') ?>" data-bs-off="<?= __('admin.status_off') ?>" data-setting_key="affiliate_login" data-setting_type="googlerecaptcha">
                        <label class="form-check-label"><?= __('admin.affiliate_login') ?></label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input update_all_settings" type="checkbox" <?= $googlerecaptcha['affiliate_register'] == 1 ? 'checked' : '' ?> data-bs-toggle="toggle" data-bs-size="normal" data-bs-on="<?= __('admin.status_on') ?>" data-bs-off="<?= __('admin.status_off') ?>" data-setting_key="affiliate_register" data-setting_type="googlerecaptcha">
                        <label class="form-check-label"><?= __('admin.affiliate_register') ?></label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input update_all_settings" type="checkbox" <?= $googlerecaptcha['client_login'] == 1 ? 'checked' : '' ?> data-bs-toggle="toggle" data-bs-size="normal" data-bs-on="<?= __('admin.status_on') ?>" data-bs-off="<?= __('admin.status_off') ?>" data-setting_key="client_login" data-setting_type="googlerecaptcha">
                        <label class="form-check-label"><?= __('admin.client_login') ?></label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input update_all_settings" type="checkbox" <?= $googlerecaptcha['client_register'] == 1 ? 'checked' : '' ?> data-bs-toggle="toggle" data-bs-size="normal" data-bs-on="<?= __('admin.status_on') ?>" data-bs-off="<?= __('admin.status_off') ?>" data-setting_key="client_register" data-setting_type="googlerecaptcha">
                        <label class="form-check-label"><?= __('admin.client_register') ?></label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input update_all_settings" type="checkbox" <?= $googlerecaptcha['store_contact'] == 1 ? 'checked' : '' ?> data-bs-toggle="toggle" data-bs-size="normal" data-bs-on="<?= __('admin.status_on') ?>" data-bs-off="<?= __('admin.status_off') ?>" data-setting_key="store_contact" data-setting_type="googlerecaptcha">
                        <label class="form-check-label"><?= __('admin.store_contact') ?></label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <!-- Card for Instructions -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"><?= __('admin.how_to_get_site_key_secret_key') ?></h4>
                    <p><?= __('admin.how_to_get_site_key_secret_key_s1') ?> <a href="https://accounts.google.com" class="link" target="_blank"><?= __('admin.how_to_get_site_key_secret_key_s2') ?></a>. <?= __('admin.how_to_get_site_key_secret_key_s3') ?> <a href="https://www.google.com/recaptcha/" class="link" target="_blank"><?= __('admin.how_to_get_site_key_secret_key_s4') ?></a>, <?= __('admin.how_to_get_site_key_secret_key_s5') ?> <strong><?= __('admin.how_to_get_site_key_secret_key_s6') ?></strong> <?= __('admin.how_to_get_site_key_secret_key_s7') ?></p>
                    <p><?= __('admin.how_to_get_site_key_secret_key_s8') ?> <strong><?= __('admin.how_to_get_site_key_secret_key_s9') ?></strong> <?= __('admin.how_to_get_site_key_secret_key_s10') ?></p>
                    <img src="<?= base_url("assets/images/grecaptcha/grecaptcha-2.png") ?>" class='img-thumbnail'>
                    <p><?= __('admin.how_to_get_site_key_secret_key_s11') ?></p>
                    <img src="<?= base_url("assets/images/grecaptcha/grecaptcha-3.png") ?>" class='img-thumbnail'>
                </div>
            </div>
        </div>
    </div>
</div>

                    </div>
                </div>

                <div class="col-sm-12 text-end mt-3">
                    <button type="submit" id="securitform" class="btn btn-lg btn-primary btn-submit">
                        <i class="bi bi-save"></i>
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
    function change_force_ssl() {
        var security_force_ssl = $("#security_force_ssl").val();
        if(security_force_ssl == 0) {
            $("#toggle_change_force_ssl").removeClass('fa-toggle-off')
            $("#toggle_change_force_ssl").addClass('fa-toggle-on')
            $("#security_force_ssl").val(1)
        } else {
            $("#toggle_change_force_ssl").removeClass('fa-toggle-on')
            $("#toggle_change_force_ssl").addClass('fa-toggle-off')
            $("#security_force_ssl").val(0)
        }
        $("#securitform").trigger('click');
    }
    function maxLengthCheck(object) {
        if (object.value.length > object.maxLength)
            object.value = object.value.slice(0, object.maxLength)
    }
    
    function isNumeric (evt) {
        var theEvent = evt || window.event;
        var key = theEvent.keyCode || theEvent.which;
        key = String.fromCharCode (key);
        var regex = /[0-9]|\./;
        if ( !regex.test(key) ) {
            theEvent.returnValue = false;
            if(theEvent.preventDefault) theEvent.preventDefault();
        }
    }
</script>

<script type="text/javascript">
    $('select[name="email[mail_type]"]').on('change', function(){
        if($(this).val() == 'smtp') {
            $('.for-smtp-mail').show();
        } else {
            $('.for-smtp-mail').hide();
        }
    });
    
    $('select[name="email[mail_type]"]').trigger('change');
    
    $("#setting-form").on('submit',function(e){
        e.preventDefault();
        $("#setting-form .alert-error").remove();
        var affiliate_cookie = parseInt($(".input-affiliate_cookie").val());
        if(affiliate_cookie <= 0 || affiliate_cookie > 365){
            $(".input-affiliate_cookie").after("<div class='alert alert-danger alert-error'><?= __('admin.days_between_1_and_365') ?></div>");
        }
        if($("#setting-form .alert-error").length == 0) return true;
        return false;
    })
    $(".items-holder").delegate(".remove-items",'click',function(){
        $(this).parent(".input-group").remove();
    })
    $(".add-items").on('click',function(){
        $(".items-holder").append('\
            <div class="input-group mb-3">\
            <input type="text" name="login[text_list][]" class="form-control" placeholder="<?= __('admin.list_items') ?>" >\
            <div class="input-group-append remove-items">\
            <span class="input-group-text"><i class="fa fa-trash"></i></span>\
            </div>\
            </div>\
            ');
    })
    $(document).on('ready',function() 
    {
        if($("#mail_verifiy").parent().hasClass('off'))
        {
            $("#registration_approval_group").show();
        } 
        else
             $("#registration_approval_group").hide();
    });
    
    $('.send-test-mail').on('click',function(){ 
        $(".alert-dismissable").remove();
        $this = $(this);
        $.ajax({
            type:'POST',
            dataType:'json',
            data:{send_test_mail:$(".testingemail").val()},
            beforeSend:function(){ $this.btn("loading"); },
            complete:function(){$this.btn("reset"); },
            success:function(json){
            $(".tab-content").prepend('<div class="alert mt-4 alert-info alert-dismissable">'+ json['message'] +'</div>');
            var body = $("html, body");
            body.stop().animate({scrollTop:0}, 500, 'swing', function() { }); 

             },
        });
    })
    $(".ads-table").delegate(".btn-delete2",'click',function(e){
        
        e.preventDefault();
        e.stopPropagation();
        $this = $(this);
        
        Swal.fire({
           icon: 'warning',
           title: 'Delete Ads!',
           text: 'Are you sure?',
           showCancelButton: true,
           cancelButtonText: 'cancel'
        }).then(function(dismiss){
            if(dismiss.value==true)
            {
                var Adsid=$this.attr("data-id");
                $.ajax({
                    url: '<?php echo base_url("admincontrol/deleteGoogleAds") ?>',
                    type:'POST',
                    dataType:'json',
                    data:{id:$this.attr("data-id")},
                    beforeSend:function(){ $this.btn("loading"); },
                    complete:function(){ $this.btn("reset"); },
                    success:function(result){

                        if(result['success']){
                            showPrintMessage(result['success'],'success');
                            var body = $("html, body");
                            body.stop().animate({scrollTop:0}, 500, 'swing', function() { });
                            $("#row_"+Adsid).remove();
                            refreshAdsGoogle();
                            
                        }
                        if(result['errors']){
                            showPrintMessage(result['errors'],'errors');
                            var body = $("html, body");
                            body.stop().animate({scrollTop:0}, 500, 'swing', function() { });
                            refreshAdsGoogle();
                            
                        }
                    },
                });
            }
            else
            {
              return false;
            }
        });
        /*delete user popup*/
    });

    function refreshAdsGoogle(){
        $("#googleads\\[id\\]").val('');
        $("#googleads\\[client_key\\]").val('');
        $("#googleads\\[unit_key\\]").val('');
        $("#googleads\\[ad_section\\]").val('');
        $.ajax({
                url: '<?php echo base_url("admincontrol/refreshGoogleAds") ?>',
                type:'POST',
                dataType:'json',
                data:{},
                beforeSend:function(){ $this.btn("loading"); },
                complete:function(){ $this.btn("reset"); },
                success:function(result){
                    $(".ads-table tbody").html(result['adsList']);
                },
            });
       
    }
    $(".btn-submit").on('click',function(evt){
        evt.preventDefault();
        
        $(".site-global_script").val( window.btoa(unescape(encodeURIComponent($(".site-global_script").val() ))) );
        $(".site-fbmessager_script").val( window.btoa(unescape(encodeURIComponent($(".site-fbmessager_script").val() ))) );
        $(".site-faceboook_pixel").val( window.btoa(unescape(encodeURIComponent($(".site-faceboook_pixel").val() ))) );
        $(".site-google_analytics").val( window.btoa(unescape(encodeURIComponent($(".site-google_analytics").val() ))) );
    
        var formData = new FormData($("#setting-form")[0]);
    
        $(".site-global_script").val( decodeURIComponent(escape(window.atob( $(".site-global_script").val() ))) );
        $(".site-fbmessager_script").val( decodeURIComponent(escape(window.atob( $(".site-fbmessager_script").val() ))) );
        $(".site-faceboook_pixel").val( decodeURIComponent(escape(window.atob( $(".site-faceboook_pixel").val() ))) );
        $(".site-google_analytics").val( decodeURIComponent(escape(window.atob( $(".site-google_analytics").val() ))) );
    
        $(".btn-submit").btn("loading");
        formData = formDataFilter(formData);
        $this = $("#setting-form");
    
        $.ajax({
            type:'POST',
            dataType:'json',
            cache:false,
            contentType: false,
            processData: false,
            data:formData,
            success:function(result){
                $(".btn-submit").btn("reset");
                $(".alert-dismissable").remove();
    
                $this.find(".has-error").removeClass("has-error");
                $this.find("span.text-danger").remove();
    
                if(result['location']){
                    window.location = result['location'];
                }
    
                if(result['success']){
                    showPrintMessage(result['success'],'success');
                    var body = $("html, body");
                    body.stop().animate({scrollTop:0}, 500, 'swing', function() { });
                    refreshAdsGoogle();
                    
                }
                if(result['message']){
                    showPrintMessage(result['message'],'error');
                    var body = $("html, body");
                    body.stop().animate({scrollTop:0}, 500, 'swing', function() { });
                    
                }
    
                if(result['errors']){
                    $.each(result['errors'], function(i,j){
                        $ele = $this.find('[name="'+ i +'"]');
                        if($ele){
                            $ele.parents(".form-group").addClass("has-error");
                            $ele.after("<span class='d-block text-danger'>"+ j +"</span>");
                        }
                    });
                }
            },
        })
        return false;
    });
    var levels = {};
    
    <?php 
        for ($i=1; $i <= 10; $i++) { 
            $v = 'referlevel_'.$i;
            if (isset($$v)) { ?>
            levels['<?= $i ?>'] = <?= json_encode($$v) ?>;
        <?php }
        }
        ?>
    $('#referlevel_select').on('change',function(){
        var level =  $(this).val();
    
        var html = '';
        for(var i = 1; i <= level; i++){
            html += '<tr>';
            html += '<td>'+i+'</td>';
            html += '<td><input type="number" step="any" name="referlevel_'+i+'[commition]" value="'+(levels[i] ? levels[i]['commition'] : '' )+'" class="form-control" /></td>';
            html += '<td><div class="input-group"><input type="number" step="any" name="referlevel_'+i+'[sale_commition]" value="'+(levels[i] ? levels[i]['sale_commition'] : '' )+'" class="form-control" /><div class="input-group-append"><span class="input-group-text refer-symball"></span></div>                                                         </div></td>';
            html += '<td><div class="input-group"><input type="number" step="any" name="referlevel_'+i+'[ex_commition]" value="'+(levels[i] ? levels[i]['ex_commition'] : '' )+'" class="form-control" /><div class="input-group-append"><span class="input-group-text"><?= $CurrencySymbol ?></span></div></div></td>';
            html += '<td><div class="input-group"><input type="number" step="any" name="referlevel_'+i+'[ex_action_commition]" value="'+(levels[i] ? levels[i]['ex_action_commition'] : '' )+'" class="form-control" /><div class="input-group-append"><span class="input-group-text"><?= $CurrencySymbol ?></span></div></div></td>';
            html += '</tr>';
        }
        $('#tbl_refer_level tbody').html(html);
    
        chnage_teigger();
    });
    
    $(document).on('click','.btn-delete-image', function(){
        let input_name = $(this).data('img_input');
        $('input[name="'+input_name+'"]').val('');
    
        let image_ele_id = $(this).data('img_ele');
        let placeholder_image = $(this).data('img_placeholder');
        $('#'+image_ele_id).attr('src', placeholder_image);
    
        $(this).remove()
    });
    
    $(document).on('click','.set-default-admin-url', function(){
        $.ajax({
            url:'<?= base_url("admincontrol/set_default_admin_url") ?>',
            type:'POST',
            dataType:'json',
            data:{'action':'set_default_admin_url'},
            success:function(json){
                window.location.reload();
            },
        })
    });
    
    $(document).on('click','.set-default-front-url', function(){
        $.ajax({
            url:'<?= base_url("admincontrol/set_default_front_url") ?>',
            type:'POST',
            dataType:'json',
            data:{'action':'set_default_front_url'},
            success:function(json){
                window.location.reload();
            },
        })
    });
    
     $('.update_all_settings').on('change', function()
    {
        var checked = $(this).prop('checked') ? 1 : 0; // Convert boolean to 1 or 0
        var setting_key = $(this).data('setting_key');
        var setting_type = $(this).data('setting_type');
     
        $.ajax({
            url: '<?= base_url("admincontrol/update_all_settings") ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                'action': 'update_all_settings',
                'status': checked,  // Fixed this line
                'setting_key': setting_key,
                'setting_type': setting_type
            },
            success: function(json)
            {
                // Handle success here
            },
        });
    });


    function editAds(id,client_key,unit_key,ad_section){
        $("#googleads\\[id\\]").val(id);
        $("#googleads\\[client_key\\]").val(client_key);
        $("#googleads\\[unit_key\\]").val(unit_key);
        $('#googleads\\[ad_section\\] option[value="'+ad_section+'"]').prop('selected', true);

    }

     function updateRegistrationAproval()
    {
        var status = 0; 
        var setting_key = "registration_approval";
        var setting_type = "store";

        $.ajax({
            url:'<?= base_url("admincontrol/update_all_settings") ?>',
            type:'POST',
            dataType:'json',
            data:{'action':'update_all_settings', status:status, setting_key:setting_key, setting_type:setting_type},
            success:function(json)
            {
            },
        });

    }

    //function to show button on large screens
    $(document).ready(function() {
        var $securitForm = $('#securitform');
        var isFixed = false;

        var handleScroll = function() {
            var scrollTop = $(window).scrollTop();
            var bottom_gap = ($(window).scrollTop() + $(window).height()) - $(document).height();
            var pageHeightExceedsWindow = $(document).height() > $(window).height();

            if (pageHeightExceedsWindow && scrollTop > 200 && bottom_gap < -50) {
                if (!isFixed) {
                    isFixed = true;
                    $securitForm.addClass('position-fixed bottom-0 end-0 m-3').hide().fadeIn(300);
                }
            } else {
                isFixed = false;
                $securitForm.removeClass('position-fixed bottom-0 end-0 m-3');
            }
        };

        // Initial check and event bindings
        handleScroll();
        $(window).on('scroll resize', handleScroll);
    });
    //function to show button on large screens

    $(document).ready(function(){
        $.ajax({
            url:'<?= base_url("admincontrol/set_default_theme_color_settings") ?>',
            type:'POST',
            dataType:'json',
            data:{'action':'set_default_theme_color_settings', 'setting_type':'theme'},
            success:function(json){

            },
        });

        $.ajax({
            url:'<?= base_url("admincontrol/set_default_theme_font_settings") ?>',
            type:'POST',
            dataType:'json',
            data:{'action':'set_default_theme_font_settings', 'setting_type':'site'},
            success:function(json){

            },
        });
    });

    function changeLanguage()
    {
       $(".alert-dismissable").remove();
        $this = $(this);
        $.ajax({
            url:'<?= base_url("admincontrol/getTermAndCondition") ?>',
            type:'POST',
            dataType:'json',
            data:{language_id:$("#drpLanguage").val()},
            beforeSend:function(){ $this.btn("loading"); },
            complete:function(){$this.btn("reset"); },
            success:function(json){
                 if(json.error){
                 }
                 else
                 {
                    $("input[name='tnc[heading]']").val(json.heading);
                    $('.summernote-img').summernote('code', '')
                    $('.summernote-img').html(escape($('.summernote-img').summernote('code', json.content)))
                 }
             
             },
        });
        
       return false;
        
    }

    $("#videolink").change(function(){
        var url = $('#videolink').val();
        if(url.toLowerCase().includes("youtube") && !url.toLowerCase().includes("embed")){
            $id = url.split("v=");
            url = 'https://www.youtube.com/embed/'+$id[1];
        } else if(url.toLowerCase().includes("youtu") && !url.toLowerCase().includes("embed")){
            $id = url.split("/");
            url = 'https://www.youtube.com/embed/'+$id[3];
        }
        loadIframe('ifrm_videoid',url);
    });

      function loadIframe(iframeName, url) {
        var $iframe = $('#' + iframeName);
        if ( $iframe.length ) {
            $iframe.attr('src',url);   
            return false;
        }
        return true;
    }
</script>
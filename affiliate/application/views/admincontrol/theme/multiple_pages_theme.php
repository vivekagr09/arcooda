<style>
	#tab_settings table.table > tbody > tr > td, #tab_settings table.table > tfoot > tr > td, #tab_settings table.table > thead > tr > td {
		padding: 5px 12px !important;
		vertical-align: middle !important;
	}
	#tab_settings .home_sections_positions_loading,
	#tab_settings .homepages_top_menu_positions_loading {
		margin:0px !important;
		padding:0px !important;
	}

	.homepage_top_menu_pages .homepages_top_menu_positions_loading{
		position: absolute;
	    top: 50%;
	    left: 50%;
	    transform: translate(-50%, -50%);
	}


	.thead-tr-loader {

		display: block;

		position: relative;

		height: 0.5rem;

		width: 1.5rem;

		color: #467fcf;

		top: 15px;

	}

	.thead-tr-loader:before {
		border-radius: 50%;
		border: 3px solid currentColor;
		opacity: .15;
	}

	.thead-tr-loader:before, .thead-tr-loader:after {
		width: 1.5rem;
		height: 1.5rem;
		margin: -1.25rem 0 0 -1.25rem;
		position: absolute;
		content: '';
		top: 50%;
		left: 50%;
	}

	.thead-tr-loader:after {
		-webkit-animation: loader .6s linear;
		animation: loader .6s linear;
		-webkit-animation-iteration-count: infinite;
		animation-iteration-count: infinite;
		border-radius: 50%;
		border: 3px solid;
		border-color: transparent;
		border-top-color: currentColor;
		box-shadow: 0 0 0 1px transparent;
	}
</style>


<style>
legend {
background-color: gray;
color: white;
padding: 5px 10px;
}
</style>

<span id="alertdiv_2"></span>

<div class="card">
	<div class="card-body">
		<form class="form-horizontal" autocomplete="off" method="post" enctype="multipart/form-data" action="" id="admin-form">
			<div class="row">
				<div class="col-sm-12">
					<ul class="nav nav-pills flex-column flex-sm-row tab-container" role="tablist" id="TabsNav">
					    <li class="nav-item">
							<a class="nav-link active show" href="#tab_home" data-bs-toggle="tab" role="tab">
							<?= __('admin.theme_home') ?></a>

						</li>

						<li class="nav-item">

							<a class="nav-link" href="#tab_sliders" data-bs-toggle="tab" role="tab">

							<?= __('admin.theme_sliders') ?></a>

						</li>

						<li class="nav-item">

							<a class="nav-link"  href="#tab_home_content" data-bs-toggle="tab" role="tab">

							<?= __('admin.theme_home_content') ?></a>

						</li>

						<li class="nav-item">

							<a class="nav-link" href="#tab_sections" data-bs-toggle="tab" role="tab">

							<?= __('admin.theme_sections') ?></a>

						</li>

							<li class="nav-item">

							<a class="nav-link"  href="#tab_home_videos" data-bs-toggle="tab" role="tab">

							<?= __('admin.theme_home_videos') ?></a>

						</li>

						<li class="nav-item">

							<a class="nav-link"  href="#tab_recommendation" data-bs-toggle="tab" role="tab">

							<?= __('admin.theme_recommendation') ?></a>

						</li>

						<li class="nav-item">

							<a class="nav-link"  href="#tab_faq" data-bs-toggle="tab" role="tab">

							<?= __('admin.theme_faq') ?></a>

						</li>

						<li class="nav-item">

							<a class="nav-link"  href="#tab_page_pages" data-bs-toggle="tab" role="tab">Pages & Links</a>

						</li>

						<li class="nav-item">

							<a class="nav-link" href="#theme_content" data-bs-toggle="tab" role="tab"  >

							<?= __('admin.theme_content') ?></a>

						</li>

						<li class="nav-item">

							<a class="nav-link" href="#tab_settings" data-bs-toggle="tab" role="tab"  >

							<?= __('admin.theme_settings') ?></a>

						</li>

						<li class="nav-item">

							<a class="nav-link" href="#theme_settings" data-bs-toggle="tab" role="tab"  >

							<?= __('admin.theme') ?></a>

						</li>

						<li class="nav-item">

							<a class="nav-link" href="<?php echo base_url(); ?>"target=_blank>

							<?= __('admin.view_site') ?></a>

						</li>

					</ul>
				</div>
			</div>

<div class="col-sm-12">

<div class="tab-content">
<div role="tabpanel" class="tab-pane p-3" id="theme_settings">

	<div class="row">

		<div class="col-12">

			<div class="card m-b-30">
				<div class="card-header">

					<h4 class="card-title pull-left"><?= __('admin.multiple_pages_theme_setting') ?></h4>

				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-sm-6 multiple-pages-theme">
                            <fieldset>
                                <legend><?= __('admin.theme_setting') ?></legend>
                                <div class="row d-flex theme-setting-row">
                                    <div class="col-sm-6">
                                        <label class="control-label"><?= __('admin.header_color_before_scroll') ?></label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control form-control-color front_header_color_before_scroll" type="color" name="theme[front_header_color_before_scroll]" value="<?= $theme['front_header_color_before_scroll'] != '' ? $theme['front_header_color_before_scroll'] : 'transparent' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-primary default-front-theme-setting" value="front_header_color_before_scroll" title="<?= __('admin.default') ?>">
                            			<i class="bi bi-arrow-counterclockwise"></i>
                                    </div>
                                </div>
                                <div class="row d-flex theme-setting-row">
                                    <div class="col-sm-6">
                                        <label class="control-label"><?= __('admin.header_button_color_before_scroll') ?></label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control form-control-color front_header_button_color_before_scroll" type="color" name="theme[front_header_button_color_before_scroll]" value="<?= $theme['front_header_button_color_before_scroll'] != '' ? $theme['front_header_button_color_before_scroll'] : '#E98024' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-primary default-front-theme-setting" value="front_header_button_color_before_scroll" title="<?= __('admin.default') ?>">
                            			<i class="bi bi-arrow-counterclockwise"></i>
                                    </div>
                                </div>
                                <div class="row d-flex theme-setting-row">
                                    <div class="col-sm-6">
                                        <label class="control-label"><?= __('admin.header_button_text_color_before_scroll') ?></label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control form-control-color front_header_button_text_color_before_scroll" type="color" name="theme[front_header_button_text_color_before_scroll]" value="<?= $theme['front_header_button_text_color_before_scroll'] != '' ? $theme['front_header_button_text_color_before_scroll'] : '#ffffff' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-primary default-front-theme-setting" value="front_header_button_text_color_before_scroll" title="<?= __('admin.default') ?>">
                            			<i class="bi bi-arrow-counterclockwise"></i>
                                    </div>
                                </div>
                                <div class="row d-flex theme-setting-row">
                                    <div class="col-sm-6">
                                        <label class="control-label"><?= __('admin.header_button_hover_color_before_scroll') ?></label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control form-control-color front_header_button_hover_color_before_scroll" type="color" name="theme[front_header_button_hover_color_before_scroll]" value="<?= $theme['front_header_button_hover_color_before_scroll'] != '' ? $theme['front_header_button_hover_color_before_scroll'] : '#F66B14' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-primary default-front-theme-setting" value="front_header_button_hover_color_before_scroll" title="<?= __('admin.default') ?>">
                            			<i class="bi bi-arrow-counterclockwise"></i>
                                    </div>
                                </div>
                                <div class="row d-flex theme-setting-row">
                                    <div class="col-sm-6">
                                        <label class="control-label"><?= __('admin.header_color_after_scroll') ?></label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control form-control-color front_header_color_after_scroll" type="color" name="theme[front_header_color_after_scroll]" value="<?= $theme['front_header_color_after_scroll'] != '' ? $theme['front_header_color_after_scroll'] : '#E98024' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-primary default-front-theme-setting" value="front_header_color_after_scroll" title="<?= __('admin.default') ?>">
                            			<i class="bi bi-arrow-counterclockwise"></i>
                                    </div>
                                </div>
                                <div class="row d-flex theme-setting-row">
                                    <div class="col-sm-6">
                                        <label class="control-label"><?= __('admin.header_button_color_after_scroll') ?></label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control form-control-color front_header_button_color_after_scroll" type="color" name="theme[front_header_button_color_after_scroll]" value="<?= $theme['front_header_button_color_after_scroll'] != '' ? $theme['front_header_button_color_after_scroll'] : '#ffffff' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-primary default-front-theme-setting" value="front_header_button_color_after_scroll" title="<?= __('admin.default') ?>">
                            			<i class="bi bi-arrow-counterclockwise"></i>
                                    </div>
                                </div>
                                <div class="row d-flex theme-setting-row">
                                    <div class="col-sm-6">
                                        <label class="control-label"><?= __('admin.header_button_text_color_after_scroll') ?></label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control form-control-color front_header_button_text_color_after_scroll" type="color" name="theme[front_header_button_text_color_after_scroll]" value="<?= $theme['front_header_button_text_color_after_scroll'] != '' ? $theme['front_header_button_text_color_after_scroll'] : '#E98024' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-primary default-front-theme-setting" value="front_header_button_text_color_after_scroll" title="<?= __('admin.default') ?>">
                            			<i class="bi bi-arrow-counterclockwise"></i>
                                    </div>
                                </div>
                                <div class="row d-flex theme-setting-row">
                                    <div class="col-sm-6">
                                        <label class="control-label"><?= __('admin.header_button_hover_color_after_scroll') ?></label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control form-control-color front_header_button_hover_color_after_scroll" type="color" name="theme[front_header_button_hover_color_after_scroll]" value="<?= $theme['front_header_button_hover_color_after_scroll'] != '' ? $theme['front_header_button_hover_color_after_scroll'] : '#FC535E' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-primary default-front-theme-setting" value="front_header_button_hover_color_after_scroll" title="<?= __('admin.default') ?>">
                            			<i class="bi bi-arrow-counterclockwise"></i>
                                    </div>
                                </div>
                                <div class="row d-flex theme-setting-row">
                                    <div class="col-sm-6">
                                        <label class="control-label"><?= __('admin.button_color') ?></label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control form-control-color front_button_color" type="color" name="theme[front_button_color]" value="<?= $theme['front_button_color'] != '' ? $theme['front_button_color'] : '#f1a05a' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-primary default-front-theme-setting" value="front_button_color" title="<?= __('admin.default') ?>">
                            			<i class="bi bi-arrow-counterclockwise"></i>
                                    </div>
                                </div>
                                <div class="row d-flex theme-setting-row">
                                    <div class="col-sm-6">
                                        <label class="control-label"><?= __('admin.button_hover_color') ?></label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control form-control-color front_button_hover_color" type="color" name="theme[front_button_hover_color]" value="<?= $theme['front_button_hover_color'] != '' ? $theme['front_button_hover_color'] : '#F66B14' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-primary default-front-theme-setting" value="front_button_hover_color" title="<?= __('admin.default') ?>">
                            			<i class="bi bi-arrow-counterclockwise"></i>
                                    </div>
                                </div>
                                <div class="row d-flex theme-setting-row">
                                    <div class="col-sm-6">
                                        <label class="control-label"><?= __('admin.button_text_color') ?></label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control form-control-color front_button_text_color" type="color" name="theme[front_button_text_color]" value="<?= $theme['front_button_text_color'] != '' ? $theme['front_button_text_color'] : '#ffffff' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-primary default-front-theme-setting" value="front_button_text_color" title="<?= __('admin.default') ?>">
                            			<i class="bi bi-arrow-counterclockwise"></i>
                                    </div>
                                </div>
                                <div class="row d-flex theme-setting-row">
                                    <div class="col-sm-6">
                                        <label class="control-label"><?= __('admin.runner_bar_color') ?></label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control form-control-color front_runner_bar_color" type="color" name="theme[front_runner_bar_color]" value="<?= $theme['front_runner_bar_color'] != '' ? $theme['front_runner_bar_color'] : '#E98024' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-primary default-front-theme-setting" value="front_runner_bar_color" title="<?= __('admin.default') ?>">
                            			<i class="bi bi-arrow-counterclockwise"></i>
                                    </div>
                                </div>
                                <div class="row d-flex theme-setting-row">
                                    <div class="col-sm-6">
                                        <label class="control-label"><?= __('admin.runner_bar_text_color') ?></label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control form-control-color front_runner_bar_text_color" type="color" name="theme[front_runner_bar_text_color]" value="<?= $theme['front_runner_bar_text_color'] != '' ? $theme['front_runner_bar_text_color'] : '#ffffff' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-primary default-front-theme-setting" value="front_runner_bar_text_color" title="<?= __('admin.default') ?>">
                            			<i class="bi bi-arrow-counterclockwise"></i>
                                    </div>
                                </div>
                                <div class="row d-flex theme-setting-row">
                                    <div class="col-sm-6">
                                        <label class="control-label"><?= __('admin.theme_titles_color') ?></label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control form-control-color front_theme_text_color" type="color" name="theme[front_theme_text_color]" value="<?= $theme['front_theme_text_color'] != '' ? $theme['front_theme_text_color'] : '#E98024' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-primary default-front-theme-setting" value="front_theme_text_color" title="<?= __('admin.default') ?>">
                            			<i class="bi bi-arrow-counterclockwise"></i>
                                    </div>
                                </div>
                                <div class="row d-flex theme-setting-row">
                                    <div class="col-sm-6">
                                        <label class="control-label"><?= __('admin.faq_before_hover_color') ?></label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control form-control-color front_faq_before_hover_color" type="color" name="theme[front_faq_before_hover_color]" value="<?= $theme['front_faq_before_hover_color'] != '' ? $theme['front_faq_before_hover_color'] : '#6A6A6A' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-primary default-front-theme-setting" value="front_faq_before_hover_color" title="<?= __('admin.default') ?>">
                            			<i class="bi bi-arrow-counterclockwise"></i>
                                    </div>
                                </div>
                                <div class="row d-flex theme-setting-row">
                                    <div class="col-sm-6">
                                        <label class="control-label"><?= __('admin.faq_after_hover_color') ?></label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control form-control-color front_faq_after_hover_color" type="color" name="theme[front_faq_after_hover_color]" value="<?= $theme['front_faq_after_hover_color'] != '' ? $theme['front_faq_after_hover_color'] : '#E98024' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-primary default-front-theme-setting" value="front_faq_after_hover_color" title="<?= __('admin.default') ?>">
                            			<i class="bi bi-arrow-counterclockwise"></i>
                                    </div>
                                </div>
                                <div class="row d-flex theme-setting-row">
                                    <div class="col-sm-6">
                                        <label class="control-label"><?= __('admin.bottom_banner_before_footer') ?></label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control form-control-color bottom_banner_before_footer" type="color" name="theme[bottom_banner_before_footer]" value="<?= $theme['bottom_banner_before_footer'] != '' ? $theme['bottom_banner_before_footer'] : '#D4731E' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-primary default-front-theme-setting" value="bottom_banner_before_footer" title="<?= __('admin.default') ?>">
                            			<i class="bi bi-arrow-counterclockwise"></i>
                                    </div>
                                </div>
                                <div class="row d-flex theme-setting-row">
                                    <div class="col-sm-6">
                                        <label class="control-label"><?= __('admin.footer_color') ?></label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control form-control-color front_footer_color" type="color" name="theme[front_footer_color]" value="<?= $theme['front_footer_color'] != '' ? $theme['front_footer_color'] : '#363839' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-primary default-front-theme-setting" value="front_footer_color" title="<?= __('admin.default') ?>">
                            			<i class="bi bi-arrow-counterclockwise"></i>
                                    </div>
                                </div>
                                <div class="row d-flex theme-setting-row">
                                    <div class="col-sm-6">
                                        <label class="control-label"><?= __('admin.header_menu_bg_color_responsive') ?></label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control form-control-color header_menu_bg_color_responsive" type="color" name="theme[header_menu_bg_color_responsive]" value="<?= $theme['header_menu_bg_color_responsive'] != '' ? $theme['header_menu_bg_color_responsive'] : '#dd7a23' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-primary default-front-theme-setting" value="header_menu_bg_color_responsive" title="<?= __('admin.default') ?>">
                            			<i class="bi bi-arrow-counterclockwise"></i>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="row">
                    	<div class="col-sm-12 text-right">
		                    <button type="submit" id="securitform" class="btn btn-sm btn-primary save-theme-settings"><?= __('admin.save_settings') ?></button>
		                </div>
                    </div>
				</div>
			</div>
		</div>
	</div>
</div>

<div role="tabpanel" class="tab-pane p-3 active show" id="tab_home">

	<div class="row">

		<div class="col-12">

			<div class="card m-b-30">

				<div class="card-header">

					<h4 class="card-title pull-left"><?= __('admin.theme_home') ?></h4>

				</div>

<div class="card-body">
    <div class="row">
        <div class="col-xl-9">
            <img class="img-thumbnail" src="<?php echo base_url("assets/images/themes/multiple_pages.png") ?>" height="550" width="1000" >
        </div>

        <div class="col-xl-3">
            <div class="card bg-white">
                <div class="card-body">
                    <h5 class="card-title"><?php echo __( 'admin.theme_support_features') ?></h5>
                    <ul class="list-unstyled">
                        <li><h6 class="card-text"><?php echo __( 'admin.support_dynamic_slider') ?></h6></li>

                        <li><h6 class="card-text"><?php echo __( 'admin.support_dynamic_sections') ?></h6></li>

                        <li><h6 class="card-text"><?php echo __( 'admin.support_dynamic_recommendation') ?></h6></li>

                        <li><h6 class="card-text"><?php echo __( 'admin.support_dynamic_content') ?></h6></li>

                        <li><h6 class="card-text"><?php echo __( 'admin.support_dynamic_videos') ?></h6></li>

                        <li><h6 class="card-text"><?php echo __( 'admin.support_dynamic_pages') ?></h6></li>

                        <li><h6 class="card-text"><?php echo __( 'admin.support_drag_and_drop') ?></h6></li>

                        <li><h6 class="card-text"><?php echo __( 'admin.support_terms_page') ?></h6></li>

                        <li><h6 class="card-text"><?php echo __( 'admin.support_contact_us_page') ?></h6></li>

                        <li><h6 class="card-text"><?php echo __( 'admin.support_faq_dynamic_page') ?></h6></li>

                        <li><h6 class="card-text"><?php echo __( 'admin.support_dynamic_bottom_menus') ?></h6></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


			</div> 

		</div> 

	</div>

</div>

										

<div role="tabpanel" class="tab-pane p-3" id="tab_sliders">

<div class="col-12">

	<div class="card m-b-30">

		<div class="card-header">

			<h4 class="card-title pull-left"><?= __('admin.top_slider_settings') ?></h4>

			

		</div>

		<div class="card-body">

			<table class="table">

				<tbody >

					<tr>
						<td width="200"><?= __('admin.auto_play_slider') ?></td>

						<td>

							<?php if(isset($theme_multiple_page_settings['top_slider_auto_play']) && $theme_multiple_page_settings['top_slider_auto_play'] == 1) { ?>
								<i class="fa fa-toggle-on" style="cursor: pointer;color: green;font-size: 35px;width:50px" onclick="change_theme_multiple_page(this, 'top_slider_auto_play');" id="top_slider_auto_play-1"></i>
							<?php } else { ?>
								<i class="fa fa-toggle-off" style="cursor: pointer;color: red;font-size: 35px;width:50px" onclick="change_theme_multiple_page(this, 'top_slider_auto_play');" id="top_slider_auto_play-0"></i>
							<?php } ?>

							<input class="theme_multiple_page_settings" type="hidden" name="theme_multiple_page[top_slider_auto_play]" value="<?= $theme_multiple_page_settings['top_slider_auto_play'] ?? 0 ?>">

						</td>
					</tr>

					<tr class="top_slider_auto_play_timing" <?= (isset($theme_multiple_page_settings['top_slider_auto_play']) && $theme_multiple_page_settings['top_slider_auto_play'] == 1) ? "" : 'style="display:none;"' ?> >
						<td><?= __('admin.auto_play_slider_timing') ?></td>

						<td>

							<input type="number" class="form-control theme_multiple_page_settings" name="theme_multiple_page[top_slider_auto_timing]" value="<?= $theme_multiple_page_settings['top_slider_auto_timing'] ?? 10 ?>">
							<small><?= __('admin.the_default_timing_10_sec');?></small>
						</td>
					</tr>

				</tbody>

			</table>

			<div class="row">

				<button type="button" class="btn btn-primary btn-submit-theme"> <?= __('admin.submit') ?> </button>

				<span class="loading-submit"></span>

			</div>

		</div>
		<div class="card-header">

			<h4 class="card-title pull-left"><?= __('admin.top_sliders_listing') ?></h4>

			<div class="pull-right">

				<a class="btn btn-primary" href="<?= base_url('themes/add_new_slider/')  ?>"><?= __('admin.add_slider') ?></a>

			</div>

		</div>
		<div class="card-body">
			<div class="table-responsive">

				<!-- <small class="text-muted"><?= __('admin.change_position_by_simply_drag_drop_rows') ?></small> -->

				<table class="table-hover table-striped table">

					<thead>

						<tr>

							<th><?= __('admin.title') ?></th>

							<th width="450"><?= __('admin.description') ?></th>

							<th><?= __('admin.image') ?></th>

							<th><?= __('admin.link') ?></th>

							<th><?= __('admin.button_text') ?></th>

							<th><?= __('admin.status') ?></th>

							<th><?= __('admin.language') ?></th> 

							<th><?= __('admin.action')?></th>

						</tr>

					</thead>

					<tbody data-whe_column="section_id" data-pos_column="position" data-table="theme_sections" class="sortable">

						<?php if(empty($theme_sliders)){ ?>

						<tr style="background-color:#FFF!important;">

							<td colspan="100%" class="text-center"><?= __('admin.no_sections_available') ?></td>

						</tr>

						<?php } ?>

						<?php foreach ($theme_sliders as $key => $slider) { ?>

						<tr data-id="<?= $section->section_id ?>" style="background-color:#FFF!important; cursor: move;">

							<td><?= $slider->title ?></td>

							<td width="450"><?= substr($slider->description, 0, 100); ?><?= (strlen($slider->description) > 100) ? "..." : "";?></td>

							<td><img src="<?php echo base_url("assets/images/theme_images/".$slider->image) ?>" height="50" width="auto"></td>

							<td><?= $slider->link ?></td>

							<td><?= $slider->button_text ?></td>

							<td><?= ($slider->status == 1) ?

								'<lable class="badge bg-success">'.__('admin.active').'</lable>' :

								'<lable class="badge bg-secondary">'.__('admin.inactive').'</lable>' ?>

							</td>

							<td><?= $slider->name ?></td>

							<td>

								<a class="btn btn-primary btn-sm" href="<?= base_url('themes/edit_slider/'. $slider->slider_id) ?>"><i class="fa fa-edit"></i></a>

								<a class="btn confirm btn-danger btn-sm" href="<?= base_url('themes/theme_delete/'. $slider->slider_id) ?>"><i class="fa fa-trash"></i></a>

							</td>

						</tr>

						<?php } ?>

					</tbody>

				</table>

			</div>
			
		</div>

	</div>

</div>

</div>

<div role="tabpanel" class="tab-pane p-1" id="tab_sections">

	<div class="col-12">

		<div class="card m-b-30">

			<div class="card-header">

				<h4 class="card-title pull-left"><?= __('admin.section') ?></h4>

				<div class="pull-right">

					<a class="btn btn-primary" href="<?= base_url('themes/add_new_section/')  ?>"><?= __('admin.add_page_section') ?></a>

				</div>

			</div>

			<div class="card-body">

				<div class="table-responsive">

					<small class="text-muted"><?= __('admin.change_position_by_simply_drag_drop_rows') ?></small>

					<table class="table-hover table-striped table">

						<thead>

							<tr>

								<th><?= __('admin.title') ?></th>

								<th width="450"><?= __('admin.description') ?></th>

								<th><?= __('admin.image') ?></th>

								<th><?= __('admin.link') ?></th>

								<th><?= __('admin.button_text') ?></th>

								<th><?= __('admin.status') ?></th>

								<th><?= __('admin.language') ?></th>

								<th><?= __('admin.action')?></th>

							</tr>

						</thead>

						<tbody data-whe_column="section_id" data-pos_column="position" data-table="theme_sections" class="sortable">

							<?php if(empty($theme_sections)){ ?>

							<tr style="background-color:#FFF!important;">

								<td colspan="100%" class="text-center"><?= __('admin.no_sections_available') ?></td>

							</tr>

							<?php } ?>

							<?php foreach ($theme_sections as $key => $section) { ?>

							<tr data-id="<?= $section->section_id ?>" style="background-color:#FFF!important; cursor: move;">

								<td><?= $section->title ?></td>

								<td width="450"><?= substr($section->description, 0, 100); ?><?= (strlen($section->description) > 100) ? "..." : "";?></td>

								<td><img src="<?php echo base_url("assets/images/theme_images/".$section->image) ?>" height="50" width="auto"></td>

								<td><?= $section->link ?></td>

								<td><?= $section->button_text ?></td>


								<td><?= ($section->status == 1) ?

									'<lable class="badge bg-success">'.__('admin.active').'</lable>' :

									'<lable class="badge bg-secondary">'.__('admin.inactive').'</lable>' ?>

								</td>
								<td><?= $section->name ?></td>

								<td>

									<a class="btn btn-primary btn-sm" href="<?= base_url('themes/edit_section/'. $section->section_id) ?>"><i class="fa fa-edit"></i></a>

									<a class="btn confirm btn-danger btn-sm" href="<?= base_url('themes/delete_section/'. $section->section_id) ?>"><i class="fa fa-trash"></i></a>

								</td>

							</tr>

							<?php } ?>

						</tbody>

					</table>

				</div>

			</div>

		</div>

	</div>

</div>

<div role="tabpanel" class="tab-pane p-3" id="tab_recommendation">

	<div class="col-12">

		<div class="card m-b-30">

			<div class="card-header">

				<h4 class="card-title pull-left"><?= __('admin.recommendations') ?></h4>

				

				<div class="pull-right">

					<a class="btn btn-primary" href="<?= base_url('themes/add_new_recommendation/')  ?>"><?= __('admin.add_new_recommendation') ?></a>

				</div>

			</div>

			<div class="card-body">

				<div class="table-responsive">

					<small class="text-muted"><?= __('admin.change_position_by_simply_drag_drop_rows') ?></small>

					<table class="table-hover table-striped table">

						<thead>

							<tr>

								<th><?= __('admin.title')?></th>

								<th><?= __('admin.occupation')?></th>

								<th><?= __('admin.description')?></th>

								<th><?= __('admin.image')?></th>

								<th><?= __('admin.status')?></th>

								<th><?= __('admin.language')?></th>

								<th><?= __('admin.action')?></th>

							</tr>

						</thead>

						<tbody class="sortable" data-whe_column="recommendation_id" data-pos_column="position" data-table="theme_recommendation">

							<?php if(empty($theme_recommendation)){ ?>

							<tr>

								<td colspan="100%" class="text-center"><?= __('admin.no_recommendation_available') ?></td>

							</tr>

							<?php } ?>

							<?php foreach ($theme_recommendation as $key => $recommendation) { ?>

							<tr data-id="<?= $recommendation->recommendation_id ?>" style="background-color:#FFF!important; cursor: move;">

								<td><?= $recommendation->title ?></td>

								<td><?= $recommendation->occupation ?></td>

								<td width="450"><?= substr($recommendation->description, 0, 100); ?><?= (strlen($recommendation->description) > 100) ? "..." : "";?></td>

								<td><img src="<?php echo base_url("assets/images/theme_images/".$recommendation->image) ?>" height="50" width="auto"></td>

								<td><?= ($recommendation->status == 1) ?

									'<lable class="badge bg-success">'.__('admin.active').'</lable>' :

									'<lable class="badge bg-secondary">'.__('admin.inactive').'</lable>' ?>

								</td>

								<td><?= $recommendation->name ?></td>

								<td>

									<a class="btn btn-primary btn-sm" href="<?= base_url('themes/edit_recommendation/'. $recommendation->recommendation_id ) ?>"><i class="fa fa-edit"></i></a>

									<a class="btn confirm btn-danger btn-sm" href="<?= base_url('themes/delete_recommendation/'. $recommendation->recommendation_id ) ?>"><i class="fa fa-trash"></i></a>

								</td>

							</tr>

							<?php } ?>

						</tbody>

					</table>

				</div>

			</div>

		</div>

	</div>

</div>



<div role="tabpanel" class="tab-pane p-3" id="tab_faq">

	<div class="col-12">

		<div class="card m-b-30">

			<div class="card-header">

				<h4 class="card-title pull-left"><?= __('admin.faq') ?></h4>

				

				<div class="pull-right">

					<a class="btn btn-primary" href="<?= base_url('themes/add_new_faq/')  ?>"><?= __('admin.add_new_faq') ?></a>

				</div>

			</div>

			<div class="card-body">

				<div class="table-responsive">

					<small class="text-muted"><?= __('admin.change_position_by_simply_drag_drop_rows') ?></small>

					<table class="table-hover table-striped table">

						<thead>
							<tr>

								<th><?= __('admin.question') ?></th>

								<th><?= __('admin.answer') ?></th>

								<th><?= __('admin.status') ?></th>

								<th><?= __('admin.language') ?></th>

								<th><?= __('admin.action') ?></th>

							</tr>

						</thead>

						<tbody class="sortable" data-whe_column="faq_id" data-pos_column="position" data-table="theme_faq">

							<?php if(empty($theme_faqs)){ ?>

							<tr>
								<td colspan="100%" class="text-center"><?= __('admin.no_faq_available') ?></td>
							</tr>

							<?php } ?>

							<?php foreach ($theme_faqs as $key => $faq) { ?>

							<tr data-pos="<?= $faq->position ?>" data-id="<?= $faq->faq_id ?>" style="background-color:#FFF!important; cursor: move;">

								<td><?= $faq->faq_question ?></td>

								<td width="450"><?= substr($faq->faq_answer, 0, 100); ?><?= (strlen($faq->faq_answer) > 100) ? "..." : "";?></td>

								<td><?= ($faq->status == 1) ?

									'<lable class="badge bg-success">'.__('admin.active').'</lable>' :

									'<lable class="badge bg-secondary">'.__('admin.inactive').'</lable>' ?>

								</td>
								<td><?= $faq->name ?></td>
								<td>
									<a class="btn btn-primary btn-sm" href="<?= base_url('themes/edit_faq/'. $faq->faq_id ) ?>"><i class="fa fa-edit"></i></a>

									<a class="btn confirm btn-danger btn-sm" href="<?= base_url('themes/delete_faq/'. $faq->faq_id ) ?>"><i class="fa fa-trash"></i></a>
								</td>

							</tr>

							<?php } ?>

						</tbody>

					</table>

				</div>

			</div>

		</div>

	</div>

</div>



<div role="tabpanel" class="tab-pane p-3" id="tab_home_content">

	<div class="col-12">

		<div class="card m-b-30">

			<div class="card-header">

				<h4 class="card-title pull-left"><?= __('admin.home_content_settings') ?></h4>

			</div>

			<div class="card-body">

				<table class="table">

					<tbody >

						<tr>
							<td width="200"><?= __('admin.auto_play_slider') ?></td>

							<td>

								<?php if(isset($theme_multiple_page_settings['home_content_auto_play']) && $theme_multiple_page_settings['home_content_auto_play'] == 1) { ?>
									<i class="fa fa-toggle-on" style="cursor: pointer;color: green;font-size: 35px;width:50px" onclick="change_theme_multiple_page(this, 'home_content_auto_play');" id="home_content_auto_play-1"></i>
								<?php } else { ?>
									<i class="fa fa-toggle-off" style="cursor: pointer;color: red;font-size: 35px;width:50px" onclick="change_theme_multiple_page(this, 'home_content_auto_play');" id="home_content_auto_play-0"></i>
								<?php } ?>

								<input class="theme_multiple_page_settings" type="hidden" name="theme_multiple_page[home_content_auto_play]" value="<?= $theme_multiple_page_settings['home_content_auto_play'] ?? 0 ?>">

							</td>
						</tr>

						<tr class="home_content_auto_play_timing" <?= (isset($theme_multiple_page_settings['home_content_auto_play']) && $theme_multiple_page_settings['home_content_auto_play'] == 1) ? "" : 'style="display:none;"' ?> >
							<td><?= __('admin.auto_play_slider_timing') ?></td>

							<td>

								<input type="number" class="form-control theme_multiple_page_settings" name="theme_multiple_page[home_content_auto_timing]" value="<?= $theme_multiple_page_settings['home_content_auto_timing'] ?? 10 ?>">
								<small><?= __('admin.the_default_timing_10_sec');?></small>
							</td>
						</tr>

					</tbody>

				</table>

				<div class="row">

					<button type="button" class="btn btn-primary btn-submit-theme"> <?= __('admin.submit') ?> </button>

					<span class="loading-submit"></span>

				</div>

			</div>
			<div class="card-header">

				<h4 class="card-title pull-left"><?= __('admin.home_content') ?></h4>

				

				<div class="pull-right">

					<a class="btn btn-primary" href="<?= base_url('themes/add_new_homecontent/')  ?>"><?= __('admin.add_home_content') ?></a>

				</div>

			</div>
			<div class="card-body">

				<div class="table-responsive">

					<small class="text-muted"><?= __('admin.change_position_by_simply_drag_drop_rows') ?></small>

					<table class="table-hover table-striped table">

						<thead>

							<tr>

								<th><?= __('admin.title') ?></th>

								<th><?= __('admin.description') ?></th>

								<th><?= __('admin.image') ?></th>

								<th><?= __('admin.status') ?></th>

								<th><?= __('admin.language') ?></th>

								<th><?= __('admin.action')?></th>

							</tr>

						</thead>

						<tbody class="sortable" data-whe_column="homecontent_id" data-pos_column="position" data-table="theme_homecontent">

							<?php if(empty($theme_homecontent)){ ?>

							<tr>

								<td colspan="100%" class="text-center"><?= __('admin.no_content_available') ?></td>

							</tr>

							<?php } ?>

							<?php foreach ($theme_homecontent as $key => $homecontent) { ?>

							<tr data-id="<?= $homecontent->homecontent_id ?>" style="background-color:#FFF!important; cursor: move;">

								<td width="150"><?= $homecontent->title ?></td>

								<td width="450">
									<?= substr(strip_tags($homecontent->description), 0, 100); ?>
									<?= (strlen(strip_tags($homecontent->description))> 100) ? '...' : '';?></td>

								<td><img src="<?php echo base_url("assets/images/theme_images/".$homecontent->image) ?>" height="50" width="100"></td>

								<td><?= ($homecontent->status == 1) ?

									'<lable class="badge bg-success">'.__('admin.active').'</lable>' :

									'<lable class="badge bg-secondary">'.__('admin.inactive').'</lable>' ?>

								</td>
								<td><?= $homecontent->name ?></td>
								<td>

									<a class="btn btn-primary btn-sm" href="<?= base_url('themes/edit_homecontent/'. $homecontent->homecontent_id) ?>"><i class="fa fa-edit"></i></a>

									<a class="btn confirm btn-danger btn-sm" href="<?= base_url('themes/delete_homecontent/'. $homecontent->homecontent_id) ?>"><i class="fa fa-trash"></i></a>

								</td>

							</tr>

							<?php } ?>

						</tbody>

					</table>

				</div>

			</div>

		</div>

	</div>

</div>

<div role="tabpanel" class="tab-pane p-3" id="tab_home_videos">

	<div class="col-12">

		<div class="card m-b-30">

			<div class="card-header">

				<h4 class="card-title pull-left"><?= __('admin.home_video') ?></h4>

				

				<div class="pull-right">

					<a class="btn btn-primary" href="<?= base_url('themes/add_new_video/')  ?>"><?= __('admin.add_new_video') ?></a>

				</div>

			</div>

			<div class="card-body">

				<div class="table-responsive">

					<small class="text-muted"><?= __('admin.change_position_by_simply_drag_drop_rows') ?></small>

					<table class="table-hover table-striped table">

						<thead>

							<tr>

								<th><?= __('admin.video_title') ?></th>

								<th><?= __('admin.video_sub_title') ?></th>

								<th><?= __('admin.video_link') ?></th>

								<th><?= __('admin.watch_video') ?></th>

								<th><?= __('admin.status') ?></th>

								<th><?= __('admin.language')?></th>

								<th><?= __('admin.action')?></th>

							</tr>

						</thead>

						<tbody class="sortable" data-whe_column="video_id" data-pos_column="position" data-table="theme_videos">

							<?php if(empty($theme_videos)){ ?>

							<tr>

								<td colspan="100%" class="text-center"><?= __('admin.no_data_available')?></td>

							</tr>

							<?php } ?>

							<?php foreach ($theme_videos as $key => $video) { ?>

							<tr data-id="<?= $video->video_id ?>" style="background-color:#FFF!important; cursor: move;">

								<td><?= $video->video_title ?></td>

								<td><?= $video->video_sub_title ?></td>

								<td><?= $video->video_link ?>

								</td>

								<td>

									<a class="btn btn-info btn-sm" href="<?= $video->video_link ?>" target="_blank" role="button"><?= __('admin.watch_video') ?></a>

								</td>

								<td>

									<?= ($video->status == 1) ?

									'<lable class="badge bg-success">'.__('admin.active').'</lable>' :

									'<lable class="badge bg-secondary">'.__('admin.inactive').'</lable>' ?>

								</td>
								<td><?= $video->name ?></td>
								<td>

									<a class="btn btn-primary btn-sm" href="<?= base_url('themes/edit_video/'. $video->video_id) ?>"><i class="fa fa-edit"></i></a>

									<a class="btn confirm btn-danger btn-sm" href="<?= base_url('themes/delete_video/'. $video->video_id) ?>"><i class="fa fa-trash"></i></a>

								</td>

							</tr>

							<?php } ?>

						</tbody>

					</table>

				</div>

			</div>

		</div>

	</div>

</div>

<div role="tabpanel" class="tab-pane p-3" id="tab_page_pages">

	<div class="col-12">

		<div class="card m-b-30">

			<div class="card-header">

				<h4 class="card-title pull-left"><?= __('admin.theme_pages') ?></h4>

				<div class="pull-right">

					<a class="btn btn-primary" href="<?= base_url('themes/add_new_page/')  ?>"><?= __('admin.add_new_page') ?></a>

				</div>

				<div class="pull-right mr-2 ml-2">
					<select class="form-control" name="search_theme_pages" id="search_theme_pages">
						<option value=""><?= __('admin.select') ?>..</option>

						<option value="header" <?php echo ($this->input->get('menu_pages') == 'header') ? 'selected' : '' ?>><?= __('admin.header_menu_pages') ?></option>

						<option value="header_dropdown" <?php echo ($this->input->get('menu_pages') == 'header_dropdown') ? 'selected' : '' ?>><?= __('admn.header_dropdown_pages') ?></option>

						<option value="footer" <?php echo ($this->input->get('menu_pages') == 'footer') ? 'selected' : '' ?>><?= __('admin.footer_menu_pages') ?></option>

						<option value="both" <?php echo ($this->input->get('menu_pages') == 'both') ? 'selected' : '' ?>><?= __('admin.header_footer_both') ?></option>
					</select>
				</div>

			</div>

			<div class="card-body">
				<div class="table-responsive homepage_top_menu_pages">

					<small class="text-muted"><?= __('admin.change_position_by_simply_drag_drop_rows') ?></small>

					<table class="table-hover table-striped table">

						<thead>

							<tr>

								<th><?= __('admin.id') ?></th>

								<th><?= __('admin.page_name') ?></th>

								<th><?= __('admin.slug_others') ?></th>

								<th><?= __('admin.top_banner_title') ?></th>

								<th><?= __('admin.top_banner_sub_title') ?></th>

								<th><?= __('admin.page_content_title') ?></th>

								<th><?= __('admin.status') ?></th>

								<th><?= __('admin.language') ?></th>

								<th><?= __('admin.action')?></th>

							</tr>

						</thead>

						<tbody class="sortable_pages_for_top_menus">

							<?php if(empty($theme_pages)){ ?>

							<tr>

								<td colspan="100%" class="text-center"><?= __('admin.no_page_available') ?></td>

							</tr>

							<?php } ?>

							<?php foreach ($theme_pages as $key => $page) { ?>

							<tr class="deleterow-<?php echo $page->page_id ?>">

								<td>
									<?= $page->page_id ?>

									<input type="hidden" name="page_id[]" value="<?= $page->page_id ?>"/>
								</td>

								<td><?= $page->page_name ?></td>

								<td>
									<div>Slug:: <span class="badge bg-secondary"><?= $page->slug ?></span></div>
									<div>isHeaderMenu:: <span class="badge bg-secondary"><?php echo $page->is_header_menu==1 ? 'True' : 'False' ?></span></div>
									<div>isDropdown:: <span class="badge bg-secondary"><?php echo $page->is_header_dropdown==1 ? 'True' : 'False' ?></span></div>
									<div>isFooterMenu:: <span class="badge bg-secondary"><?php echo $page->link_footer_section != '' ? 'True' : 'False' ?></span></div>
								</td>

								<td><?= $page->top_banner_title ?></td>

								<td><?= $page->top_banner_sub_title ?></td>

								<td><?= $page->page_content_title ?></td>

								<td>

									<?php if ($page->status ==1) { ?>

									<i class="fa fa-toggle-on" style="cursor: pointer;color: green;font-size: 35px;width:50px" onclick="change_page_status('<?= $page->page_id ?>');" id="page_status_active_<?= $page->page_id ?>"> 

									<?php } else{ ?>

									<i class="fa fa-toggle-off" style="cursor: pointer;color: red;font-size: 35px;width:50px" onclick="change_page_status('<?= $page->page_id ?>');" id="page_status_active_<?= $page->page_id ?>"> 

									<?php } ?>	

									<input type="hidden" name="page_status" id="page_status_<?= $page->page_id ?>" value="<?php echo $page->status;?>">

									</i>
								</td>
								<td><?php if($page->page_type=='editable'){ ?><?= $page->name ?><?php }?></td>

								<td>
									<?php if($page->page_type=='editable'){ ?>

									<a class="btn btn-primary btn-sm" href="<?= base_url('themes/edit_page/'. $page->page_id) ?>"><i class="fa fa-edit"></i></a>

									<a class="btn btn-danger btn-sm delete_page" data-id="<?= $page->page_id; ?>" data-href="<?= base_url('themes/delete_page/'. $page->page_id) ?>"><i class="fa fa-trash"></i></a>

									<?php } else { ?>
									<a class="btn btn-primary btn-sm" href="<?= base_url('themes/edit_page/'. $page->page_id) ?>"><i class="fa fa-edit"></i></a>

									<?php } ?>

								</td>

							</tr>

							<?php } ?>

						</tbody>

					</table>

					<span class="homepages_top_menu_positions_loading" style="display:none;">

						<div class="thead-tr-loader"></div>

					</span>

				</div>

			</div>

		</div>

	</div>

	<div class="col-12">
		<div class="card m-b-30">
			<div class="card-header">
				<h4 class="card-title pull-left"><?= __('admin.theme_links') ?></h4>
				<div class="pull-right">
					<span id="add_new_link" class="btn btn-primary text-white"><?= __('admin.add_new_link') ?></span>
				</div>
			</div>

			<div class="card-body">
				<div class="table-responsive">
					<table class="table-hover table-striped table">
						<thead>
							<tr>
								<th><?= __('admin.link_title') ?></th>
								<th><?= __('admin.link_url') ?></th>
								<th class="text-center"><?= __('admin.link_position') ?></th>
								<th><?= __('admin.status') ?></th>
								<th><?= __('admin.language') ?></th>
								<th><?= __('admin.action')?></th>
							</tr>
						</thead>

						<tbody id="links-tbody">

							<?php if(empty($theme_links)){ ?>
								<tr>
									<td colspan="100%" class="text-center"><?= __('admin.no_links_available') ?></td>
								</tr>
							<?php } ?>

							<?php foreach ($theme_links as $link) { ?>
							<tr data-tlink_id="<?= $link->tlink_id ?>" data-tlink_title="<?= $link->tlink_title ?>" data-tlink_url="<?= $link->tlink_url ?>" data-tlink_position="<?= $link->tlink_position ?>" data-tlink_status="<?= $link->tlink_status ?>" data-tlink_target_blank="<?= $link->tlink_target_blank ?>" data-language_id="<?= $link->language_id ?>">
								<td><?= $link->tlink_title ?></td>
								<td><?= $link->tlink_url ?></td>
								<td class="text-center"><?php 

									switch ($link->tlink_position) {
										case 1:
											echo __('admin.menu_a');
											break;
										case 2:
											echo __('admin.menu_b');
											break;
										case 3:
											echo __('admin.menu_c');
											break;
										case 4:
											echo __('admin.menu_d');
											break;
										default:
											echo __('admin.none');
											break;
									}
									 
								?></td>
								<td>
									<i class="btn_tlink_status_toggle fa <?= ($link->tlink_status == 1) ? 'fa-toggle-on' : 'fa-toggle-off' ?>" style="cursor: pointer; color: <?= ($link->tlink_status == 1) ? 'green' : 'red' ?>; font-size: 35px; width:50px"></i>
								</td>
								<th><?= $link->name ?></th>
								<td>
									<a class="btn btn-primary text-white btn-sm btn_edit_tlink"><i class="fa fa-edit"></i></a>
									<a class="btn btn-danger text-white btn-sm btn_delete_tlink"><i class="fa fa-trash"></i></a>
								</td>
							</tr>
							<?php } ?>

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

</div>

<div role="tabpanel" class="tab-pane p-3" id="theme_content">
	<div class="col-md-12">

		<div class="card m-b-30">

			<div class="card-header">
				<h4 class="card-title pull-left"><?= __('admin.theme_content') ?></h4>
			</div>
			<div class="card-body">
				<div class="row">
					<div role="tabpanel" class="tab-pane p-3" id="tab_setting_inner_content"> 
						<div class="col-md-4">
							<div class="form-group">
					            <label class="control-label"><?= __('admin.select_language') ?></label>
					            <select class="form-control" name="language_id" id="drpLanguage" onchange="return changeLanguage();">
					                <?php 
					                if(isset($languages))
					                {
					                    foreach($languages as $language)
					                    {?>
					                    <option <?php 

					                     if($language['is_default']==1) {echo 'selected';} ?> value="<?=$language['id']?>"><?=$language['name'] ?></option>
					                  
					                   <?php  }     
					                }?>
					                
					            </select>
					    	</div>
					    </div>
						     <div id="setting_content_html">
						    </div>
					    <br/>
					    <br/>
					    <div class="row">
							<button type="button" class="btn btn-primary btn-submit-theme"> <?= __('admin.submit') ?> 
							</button>
							<span class="loading-submit"></span>

						</div>
						<div class="invalid-form-error" style="color: red;display: none;"><?= __('admin.invalid_form_details_please_check_and_validate') ?> 
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>			

</div>
								
<div role="tabpanel" class="tab-pane p-3" id="tab_settings">

	<div class="col-md-12">

		<div class="card m-b-30">

			<div class="card-header">
				<h4 class="card-title pull-left"><?= __('admin.theme_settings') ?></h4>
			</div>
			<div class="card-body">
				 
					<fieldset class="mt-1">

						<legend><?= __('admin.homepage_section_management') ?></legend>

						<div class="row">

							<div class="col-md-12">

								<small class="text-muted">&nbsp;<?= __('admin.change_position_by_simply_drag_drop_rows') ?></small>

									<table class="table-hover table">

									<thead>

										<tr>

										<th style="verticle-align:middle;"><?= __('admin.enable').'/'.__('admin.disable') ?></th>

										<th style="verticle-align:middle;"><?= __('admin.section_name') ?>
											<span class="home_sections_positions_loading float-right" style="display:none;">
												<div class="thead-tr-loader"></div>
											</span>
										</th>

										</tr>

									</thead>

									<tbody class="sortable2">

										<?php foreach($home_sections_settings as $hs_setting) { ?>

										<tr style="background-color:#FFF!important; cursor: move;">

											<td style="width:100px; text-align:center;">

											<?php if ($hs_setting->sec_is_enable == 1) { ?>

												<i class="fa fa-toggle-on" style="cursor: pointer;color: green;font-size: 35px;width:50px" onclick="change_section_status(<?= $hs_setting->sec_id ?>);" id="section_status_active_<?= $hs_setting->sec_id ?>"></i> 

											<?php } else{ ?>

												<i class="fa fa-toggle-off" style="cursor: pointer;color: red;font-size: 35px;width:50px" onclick="change_section_status(<?= $hs_setting->sec_id ?>);" id="section_status_active_<?= $hs_setting->sec_id ?>"></i>

											<?php } ?>	

											<input type="hidden" name="sec_status[]" id="section_status_<?= $hs_setting->sec_id ?>" value="<?= $hs_setting->sec_is_enable ?>"/>

											<input type="hidden" name="sec_id[]" value="<?= $hs_setting->sec_id ?>"/>

											</td>

											<td><?= $hs_setting->sec_title ?></td>

										</tr>

										<?php } ?>

									</tbody>

								</table>

							</div>

						</div>

						<hr/>
						<h5 class="mt-4">
							<?= __('admin.top_banner_runner_settings') ?>
						</h5>
						<hr class="m-0" />
						<table class="table table-borderless">

						<tbody >

							<tr>
								<td width="250"><?= __('admin.auto_play_runner') ?></td>

								<td>

									<?php if(isset($theme_multiple_page_settings['home_runner_auto_play']) && $theme_multiple_page_settings['home_runner_auto_play'] == 1) { ?>
										<i class="fa fa-toggle-on" style="cursor: pointer;color: green;font-size: 35px;width:50px" onclick="change_theme_multiple_page(this, 'home_runner_auto_play');" id="home_runner_auto_play-1"></i>
									<?php } else { ?>
										<i class="fa fa-toggle-off" style="cursor: pointer;color: red;font-size: 35px;width:50px" onclick="change_theme_multiple_page(this, 'home_runner_auto_play');" id="home_runner_auto_play-0"></i>
									<?php } ?>

									<input class="theme_multiple_page_settings" type="hidden" name="theme_multiple_page[home_runner_auto_play]" value="<?= $theme_multiple_page_settings['home_runner_auto_play'] ?? 0 ?>">

								</td>
							</tr>

							<tr class="home_runner_auto_play_timing" <?= (isset($theme_multiple_page_settings['home_runner_auto_play']) && $theme_multiple_page_settings['home_runner_auto_play'] == 1) ? "" : 'style="display:none;"' ?> >
								<td><?= __('admin.auto_play_runner_timing') ?></td>

								<td>

									<input type="number" class="form-control theme_multiple_page_settings" name="theme_multiple_page[home_runner_auto_timing]" value="<?= $theme_multiple_page_settings['home_runner_auto_timing'] ?? 10 ?>">
									<small><?= __('admin.the_default_timing_10_sec');?></small>
								</td>
							</tr>

						</tbody>

					</table>

					</fieldset>
				  
				<br>
 
				<br>

				<div class="row">

					<button type="button" class="btn btn-primary btn-submit-theme"> <?= __('admin.submit') ?> </button>

					<span class="loading-submit"></span>

				</div>
				<div class="invalid-form-error" style="color: red;display: none;"><?= __('admin.invalid_form_details_please_check_and_validate') ?></div>
				</div> 
				
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

				<div id="link_form_modal" class="modal" tabindex="-1" role="dialog">
					<div class="modal-dialog modal-lg" role="document">
						<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title"><?= __('admin.add_new_link') ?></h5>
							<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<form id="link_form">
								<input name="tlink_id" type="hidden" value="0"/>
								<div class="row">
									<div class="col-12">
										<div class="form-group">
								            <label class="control-label"><?= __('admin.select_language') ?></label>
								            <select class="form-control" name="language_id" id="drpLanguage" onchange="return changeLanguage();">
								                <?php 
								                if(isset($languages))
								                {
								                    foreach($languages as $language)
								                    {?>
								                    <option <?php 

								                    if($tutorial['language_id']==$language['id'])
								                    {
								                    	echo 'selected';
								                    }
								                    else if(!isset($tutorial) && $language['is_default']==1) {echo 'selected';} ?> value="<?=$language['id']?>"><?=$language['name'] ?></option>
								                  
								                   <?php  }     
								                }?>
								                
								            </select>
								    	</div>
								    </div>
									<div class="col-12">
										<div class="form-group">
											<label><?= __('admin.link_title') ?></label>
											<input name="tlink_title" type="text" class="form-control" placeholder="<?= __('admin.link_title_to_display') ?>">
										</div>
									</div>
									<div class="col-12">
										<div class="form-group">
											<label><?= __('admin.link_url') ?></label>
											<input name="tlink_url" type="text" class="form-control" placeholder="<?= __('admin.link_url_to_open') ?>">
											<span class="text-danger tlink_url_error"></span>
										</div>
									</div>
									<div class="col-4">
										<div class="form-group">
											<label><?= __('admin.link_position') ?></label>
											<select name="tlink_position" class="form-control">
												<option value="0"><?= __('admin.none') ?></option>
												<option value="1"><?= __('admin.footer_menu_a') ?></option>
												<option value="2"><?= __('admin.footer_menu_b') ?></option>
												<option value="3"><?= __('admin.footer_menu_c') ?></option>
												<option value="4"><?= __('admin.footer_menu_d') ?></option>
											</select>
										</div>
									</div>
									<div class="col-4">
										<div class="form-group">
											<label><?= __('admin.link_status') ?></label>
											<select name="tlink_status" class="form-control">
												<option value="1"><?= __('admin.enable') ?></option>
												<option value="0"><?= __('admin.disabled') ?></option>
											</select>
										</div>
									</div>
									<div class="col-4">
										<div class="form-group">
											<label><?= __('admin.is_open_in_new_tab') ?></label>
											<select name="tlink_target_blank" class="form-control">
												<option value="1"><?= __('admin.yes') ?></option>
												<option value="0"><?= __('admin.no') ?></option>
											</select>
										</div>
									</div>
								</div>
								
								
							</form>
						</div>
							<div class="modal-footer">
								<button id="link_form_submit" type="button" class="btn btn-primary"><?= __('admin.save_changes') ?></button>
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
							</div>
						</div>
				</div>
					<script type="text/javascript">

						$("#link_form_submit").on('click',function(evt){

							$(".tlink_url_error").empty();

							$("#link_form_submit").btn("loading");

							var res = $('input[name="tlink_url"]').val();
							if(res != "") {
								var result = res.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
								if(result == null && !res.includes("http://localhost") && !res.includes("https://localhost")) {
									$(".tlink_url_error").text('<?= __('admin.please_enter_valid_link') ?>');
									$("#link_form_submit").btn("reset");
									return false;
								}
							}
							
							evt.preventDefault();

							$.ajax({
								url:'<?= base_url('themes/store_link') ?>',
								type:'POST',
								dataType:'json',
								data:$("#link_form").serializeArray(),
								complete:function(result){
									$("#link_form_submit").btn("reset");
									$('#link_form_modal').modal('hide');
								},
								success:function(response){
									let swalIcon = response.status ? 'success' : 'error';
									if(response.status) {
										let linksBody = "";

										if(response.data.length == 0) {
											linksBody = `<tr><td colspan="100%" class="text-center">`+'<?= __('admin.no_links_available') ?>'+`</td></tr>`;
										}

										for (let index = 0; index < response.data.length; index++) {
											const element = response.data[index];

											let link_pos = '<?= __('admin.none') ?>';
											switch (element['tlink_position']) {
												case "1":
													link_pos = '<?= __('admin.menu_a') ?>';
													break;
												case "2":
													link_pos = '<?= __('admin.menu_b') ?>';
													break;
												case "3":
													link_pos = '<?= __('admin.menu_c') ?>';
													break;
												case "4":
													link_pos = '<?= __('admin.menu_d') ?>';
													break;
												default:
													link_pos = '<?= __('admin.none') ?>';
													break;
											}

											console.log(link_pos, element['tlink_position'])

											let link_class = (element['tlink_status'] == 1) ? 'fa-toggle-on' : 'fa-toggle-off';
											let link_color = (element['tlink_status'] == 1) ? 'green' : 'red';

											linksBody += `<tr data-tlink_id="`+ element['tlink_id'] +`" data-tlink_title="`+ element['tlink_title'] +`" data-tlink_url="`+ element['tlink_url'] +`" data-tlink_position="`+ element['tlink_position'] +`" data-tlink_status="`+ element['tlink_status'] +`" data-tlink_target_blank="`+ element['tlink_target_blank'] +`"  
												data-language_id="`+ element['language_id'] +`"
												>
												<td>`+ element['tlink_title'] +`</td>
												<td>`+ element['tlink_url'] +`</td>
												<td class="text-center">`+link_pos+`</td>
												<td><i class="btn_tlink_status_toggle fa `+ link_class +`" style="cursor: pointer; color: `+ link_color +`; font-size: 35px; width:50px"></i></td>
												<td>`+ element['name'] +`</td>
												<td>
													<a class="btn btn-primary text-white btn-sm btn_edit_tlink"><i class="fa fa-edit"></i></a>
													<a class="btn btn-danger text-white btn-sm btn_delete_tlink"><i class="fa fa-trash"></i></a>
												</td>
											</tr>`
										}
										$("#links-tbody").html(linksBody);
									}
									Swal.fire({
										icon: swalIcon,
										text: response.message,
									});
								}
							});
							return false;
						});

						$(document).on('click', '.btn_delete_tlink', function(){
							Swal.fire({
								title: '<?= __('admin.are_you_sure') ?>',
								text: '<?= __('admin.you_not_be_able_to_revert_this') ?>',
								icon: 'warning',
								showCancelButton: true,
								confirmButtonColor: '#3085d6',
								cancelButtonColor: '#d33',
								confirmButtonText: '<?= __('admin.yes_delete_it') ?>'
							}).then((result) => {
								if (result.value) {
									let thatBtn = $(this);
									thatBtn.btn("loading");
									$.ajax({
										url:'<?= base_url('themes/delete_link') ?>',
										type:'POST',
										dataType:'json',
										data:{tlink_id:$(this).closest('tr').data('tlink_id')},
										complete:function(res){
											thatBtn.closest("tr").remove();
											Swal.fire('Deleted!', '<?= __('admin.your_link_has_been_deleted') ?>', 'success');
										}
									});
								}
							});
						});

						$(document).on('click', '.btn_edit_tlink', function(){
							let dataRow = $(this).closest('tr');
							$('#link_form_modal input[name="tlink_id"]').val(dataRow.data('tlink_id'));
							$('#link_form_modal input[name="tlink_title"]').val(dataRow.data('tlink_title'));
							$('#link_form_modal input[name="tlink_url"]').val(dataRow.data('tlink_url'));
							$('#link_form_modal select[name="tlink_position"]').val(dataRow.data('tlink_position'));
							$('#link_form_modal select[name="tlink_status"]').val(dataRow.data('tlink_status'));
							$('#link_form_modal select[name="tlink_target_blank"]').val(dataRow.data('tlink_target_blank'));
							$('#link_form_modal select[name="language_id"]').val(dataRow.data('language_id'));
							$('#link_form_modal').modal('show');
						});

						$(document).on('click', '#add_new_link', function(){
							$('#link_form_modal input[name="tlink_id"]').val('');
							$('#link_form_modal input[name="tlink_title"]').val('');
							$('#link_form_modal input[name="tlink_url"]').val('');
							$('#link_form_modal select[name="language_id"]').val(1);
							$('#link_form_modal').modal('show');
						});

						$(document).on('change', '#slider_link_type', function(){

							$('#slider-link').val($(this).val());

						});

						$(document).on('click', ".btn_tlink_status_toggle", function(){
							let tlink_id = $(this).closest('tr').data('tlink_id');
							let tlink_status = $(this).hasClass('fa-toggle-off') ? 1 : 0;
							if(tlink_status) {
								$(this).addClass('fa-toggle-on').removeClass('fa-toggle-off');
								$(this).css("color", "green");
							} else {
								$(this).addClass('fa-toggle-off').removeClass('fa-toggle-on');
								$(this).css("color", "red");
							}

							$.ajax({
								url: "<?= base_url('themes/tlink_status_toggle') ?>",
								type: "POST",
								dataType: "json",
								data: {
									tlink_id:tlink_id,
									tlink_status:tlink_status,
								},
								success: function (response) {	
								}
							});
						});	

						$(function() {

							$( ".sortable2" ).sortable({

								update: function( event, ui ) {

									update_homepage_sections_table();

								}

							});

							$( ".sortable2" ).disableSelection();

						});

						$(function() {

							$( ".sortable_pages_for_top_menus" ).sortable({

								update: function( event, ui ) {

									update_homepage_top_menu_position();

								}

							});

							$( ".sortable_pages_for_top_menus" ).disableSelection();

						});

						$(function() {

							$( ".sortable" ).sortable({

								update: function( event, ui ) {

									let positions = [];

									$(this).children('tr').each(function () {

										if($(this).data('id') != null) {

											positions.push($(this).data('id'));

										}

									});

									$.ajax({

										url: "<?= base_url('themes/change_positions')  ?>",

										type: "POST",

										dataType: "json",

										data: {table:$(this).data('table'), whe_column:$(this).data('whe_column'), pos_column:$(this).data('pos_column'),positions:JSON.stringify(positions)},

										success: function (response) {	
										}

									});

								}

							});

							$( ".sortable" ).disableSelection();

						});

					</script>



					<script type="text/javascript">

						

						var loadFile = function(event) {

							var image = document.getElementById('output');

							image.src = URL.createObjectURL(event.target.files[0]);

						};



						$(document).on('click', '.remove-runner-btn', function(){

							$(this).parent().remove();

							$('#runners-section .col-md-12').each(function( index ) {

								$(this).find('.control-label').text('<?= __('admin.runner') ?>'+(index+1));

							});

							let count = $('#runners-section .col-md-12').length;

							if (count == 1) {

								$('#runners-section').prepend(`

								<div class="col-md-12">

									<div class="form-group">

										<label class="control-label">`+'<?= __('admin.runner') ?>'+` `+count+`</label>

										<input name="top_banner_slider[]" class="form-control" type="text">

									</div>

									<button type="button" class="btn btn-danger btn-md remove-runner-btn" style="position: absolute; top: 30px; right: 11px;"><i class="fa fa-trash"></i></button>

								</div>`);

							}

						});





						$(document).on('click', '#add-more-runner-btn', function(){

							let count = $('#runners-section .col-md-12').length;

							$(this).parent().before(`

							<div class="col-md-12">

								<div class="form-group">

									<label class="control-label">`+'<?= __('admin.runner') ?>'+` `+count+`</label>

									<input name="top_banner_slider[]" class="form-control" type="text">

								</div>

								<button type="button" class="btn btn-danger btn-md remove-runner-btn" style="position: absolute; top: 30px; right: 11px;"><i class="fa fa-trash"></i></button>

							</div>`);

						});



						// $(".btn-slider-submit").on('click',function(evt){

						// 	$("#linkError").empty();

						// 	$this = $("#admin-form");

						// 	$(".btn-submit").btn("loading");

						// 	$('.loading-submit').show();

						// 	var res = $('#slider-link').val();

						// 	if(res != "") {

						// 		var result = res.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);

						// 		if(result == null && !res.includes("http://localhost") && !res.includes("https://localhost"))

						// 		{

						// 			$("#linkError").append('<?= __('admin.please_enter_valid_link') ?>');

						// 			$(".btn-submit").btn("reset");

						// 			return false;

						// 		}
						// 	}

							

						// 	evt.preventDefault();

						// 	var formData = new FormData($("#admin-form")[0]);



						// 	formData = formDataFilter(formData);

							

						// 	$.ajax({

						// 		url:'<?= base_url('themes/update_slider') ?>',

						// 		type:'POST',

						// 		dataType:'json',

						// 		cache:false,

						// 		contentType: false,

						// 		processData: false,

						// 		data:formData,

						// 		xhr: function (){

						// 			var jqXHR = null;



						// 			if ( window.ActiveXObject ){

						// 				jqXHR = new window.ActiveXObject( "Microsoft.XMLHTTP" );

						// 			}else {

						// 				jqXHR = new window.XMLHttpRequest();

						// 			}

									

						// 			jqXHR.upload.addEventListener( "progress", function ( evt ){

						// 				if ( evt.lengthComputable ){

						// 					var percentComplete = Math.round( (evt.loaded * 100) / evt.total );

						// 					$('.loading-submit').text(percentComplete + "% "+'<?= __('admin.loading') ?>');

						// 				}

						// 			}, false );



						// 			jqXHR.addEventListener( "progress", function ( evt ){

						// 				if ( evt.lengthComputable ){

						// 					var percentComplete = Math.round( (evt.loaded * 100) / evt.total );

						// 					$('.loading-submit').text('<?= __('admin.save') ?>');

						// 				}

						// 			}, false );

						// 			return jqXHR;

						// 		},

						// 		complete:function(result){

						// 			$(".btn-submit").btn("reset");

						// 		},

						// 		success:function(result){

						// 			$('.loading-submit').hide();

						// 			$this.find(".has-error").removeClass("has-error");

						// 			$this.find("span.text-danger").remove();

						// 			if(result['location'])
						// 				window.location = result['location'];

						// 			if(result['errors']){
						// 				$.each(result['errors'], function(i,j){
						// 					$ele = $this.find('[name="'+ i +'"]');
						// 					$ele.parents(".form-group").addClass("has-error");
						// 					if(i == 'avatar')
						// 						$ele.parent().parent().append("<span class='text-danger'>"+ j +"</span>");
						// 					else
						// 						$ele.after("<span class='text-danger'>"+ j +"</span>");
						// 				});
						// 			}

						// 		},

						// 	})

						// 	return false;

						// });

					</script>



<script>
function read_url(input,name,display_id) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
    	$("input[name='"+name+"']").val('image.jpg');
      	$('#'+display_id).attr('src', e.target.result);
    }
    reader.readAsDataURL(input.files[0]);
  }
}


$(document).ready(function() {



$(".delete_page").click(function(e){
	if(!confirm("Are your sure ?")) return false;
	var href = $(this).attr("data-href");
	var id = $(this).attr("data-id");
	$.ajax({
		url: href,
		type: "GET",
		success: function (data) {
			$(".deleterow-" + id).remove();
			var alert_div = '<div class="alert alert-success alert-dismissable" ><button type="button" class="close" data-bs-dismiss="alert" aria-hidden="true">&times;</button>'+
				'<span id="alert_msg_2">'+'<?= __('admin.item_has_been_successfully_deleted') ?>'+'</span></div>';
			$("#alertdiv_2").append(alert_div);
			$("#alertdiv_2").show();
			setTimeout( function(){
			$("#alertdiv_2").fadeOut();
			}  , 2000 );
		}
	});
});

	$('#summernote').summernote({
	    minHeight: 300,
		toolbar: [
			['style', ['bold', 'italic', 'underline', 'clear']],
			['font', ['strikethrough', 'superscript', 'subscript']],
			['fontsize', ['fontsize']],
			['color', ['color']],
			['para', ['ul', 'ol', 'paragraph']],
			['height', ['height']]
		]
	});
});

</script>

<script type="text/javascript">

$(".confirm").on('click',function(){

if(!confirm('<?= __('admin.are_you_sure') ?>')) return false;

		return 1;

	})

</script>

<script type="text/javascript">

function validURL(str) {
	let pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
		'((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
		'((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
		'(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
		'(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
		'(\\#[-a-z\\d_]*)?$','i'); // fragment locator

	return !!pattern.test(str) || str.match(/^https?:\/\/\w+(\.\w+)*(:[0-9]+)?(\/.*)?$/g) !== null;
}

var imageArrays = [
					'homepage_video_section_bg',
					'logo',
					'faq_banner_image',
					'contact_banner_image',
					'avatar_login',
					'avatar_registration',
					'avatar_terms'
				];
 
$(".btn-submit-theme").on('click',function(evt){

	evt.preventDefault();

	$this = $("#admin-form");

	$(".btn-submit").btn("loading");

	$('.loading-submit').show();

	

	let is_invalid_form = false;

	let links_array = ["youtube_link", "facebook_link", "twitter_link", "instegram_link", "banner_button_link"]

	$.each(links_array, function( index, value ) {
		$("#"+value+"_error").empty();
		let link = $('#'+value).val();
		if(link != "") {
			if(!validURL(link)) {
				is_invalid_form = true;
				$("#"+value+"_error").append('<?= __('admin.please_enter_valid_link') ?>');
			}
		}
	});

	

	$("#whatsapp_number_error").empty();

	let whatsapp_number = $("input[name='whatsapp_number']").val();	

	if(whatsapp_number != "") {
		let whatsapp_number_is_valid = whatsapp_number.match(/^\+[1-9]{1}[0-9]{3,14}$/g);

		if(whatsapp_number_is_valid == null) {

			is_invalid_form = true;

			$("#whatsapp_number_error").append('<?= __('admin.please_enter_valid_mobile_number') ?>');

		}
	
	}


	$("#contact_us_phone_error").empty();

	let contact_us_phone_number = $("input[name='contact_us_phone']").val();	

	if(contact_us_phone_number != "") {
		let contact_us_phone_is_valid = contact_us_phone_number.match(/^\+[1-9]{1}[0-9]{3,14}$/g);

		if(contact_us_phone_is_valid == null) {

			is_invalid_form = true;

			$("#contact_us_phone_error").append('<?= __('admin.please_enter_valid_mobile_number') ?>');

		}
	}

	let contact_us_email = $("input[name='contact_us_email']").val();
	if(contact_us_email != "") {	
		if (!/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test($("input[name='contact_us_email']").val())) {
			is_invalid_form = true;
			$("#contact_us_email_error").append('<?= __('admin.please_enter_valid_email_address') ?>');
		}
	}


	if(is_invalid_form) {

		$(".btn-submit").btn("reset");

		$(".invalid-form-error").show();

		return false;

	}else{
		$(".invalid-form-error").hide();
	}



var formData = new FormData($("#admin-form")[0]);

formData = formDataFilter(formData);


$.ajax({

url:'<?= base_url('themes/update_settings') ?>',

type:'POST',

dataType:'json',

cache:false,

contentType: false,

processData: false,

data:formData,

xhr: function (){

var jqXHR = null;

if ( window.ActiveXObject ){

jqXHR = new window.ActiveXObject( "Microsoft.XMLHTTP" );

}else {

jqXHR = new window.XMLHttpRequest();

}

jqXHR.upload.addEventListener( "progress", function ( evt ){

if ( evt.lengthComputable ){

var percentComplete = Math.round( (evt.loaded * 100) / evt.total );

$('.loading-submit').text(percentComplete + "% "+'<?= __('admin.laoding') ?>');

}

}, false );

jqXHR.addEventListener( "progress", function ( evt ){

if ( evt.lengthComputable ){

var percentComplete = Math.round( (evt.loaded * 100) / evt.total );

$('.loading-submit').text('<?= __('admin.save') ?>');

}

}, false );

return jqXHR;

},

complete:function(result){

$(".btn-submit-theme").btn("reset");

},

success:function(result){

$('.loading-submit').hide();

$this.find(".has-error").removeClass("has-error");

$this.find("span.text-danger").remove();

if(result['location']){

	window.location = result['location'];

}

if(result['errors']){

$.each(result['errors'], function(i,j){

	$ele = $this.find('[name="'+ i +'"]');
	$ele.parents(".form-group").addClass("has-error");
	if(imageArrays.includes(i))
		$ele.parent().parent().append("<span class='text-danger'>"+ j +"</span>");
	else
		$ele.after("<span class='text-danger'>"+ j +"</span>");

});

}

},

})

return false;

});

$(".alert").fadeTo(2000, 500).slideUp(500, function(){

$(".alert").alert('close');

});

function update_homepage_top_menu_position() {
	$('.homepages_top_menu_positions_loading').show();

	let page_id = $('input[name="page_id[]"]').map(function(){ 

		return this.value; 

	}).get();

	$.ajax({

		url: "<?= base_url('themes/change_homepage_top_menu_positions')  ?>",

		type: "POST",

		data: { 'page_id[]': page_id},

		xhr: function (){

			var jqXHR = null;

			if ( window.ActiveXObject ){

				jqXHR = new window.ActiveXObject( "Microsoft.XMLHTTP" );

			}else {

				jqXHR = new window.XMLHttpRequest();

			}

			jqXHR.upload.addEventListener( "progress", function ( evt ){

				if ( evt.lengthComputable ){

					var percentComplete = Math.round( (evt.loaded * 100) / evt.total );

				}

			}, false );

			jqXHR.addEventListener( "progress", function ( evt ){

				if ( evt.lengthComputable ){

					var percentComplete = Math.round( (evt.loaded * 100) / evt.total );

				}

			}, false );

			return jqXHR;

		},

		complete: function(){

			setTimeout(function(){ $('.homepages_top_menu_positions_loading').hide(); }, 500);

		}

	});
}


function update_homepage_sections_table(){

	$('.home_sections_positions_loading').show();



	let sec_id = $('input[name="sec_id[]"]').map(function(){ 

		return this.value; 

	}).get();



	let sec_status = $('input[name="sec_status[]"]').map(function(){ 

		return this.value; 

	}).get();



	$.ajax({

		url: "<?= base_url('themes/change_home_sections_positions')  ?>",

		type: "POST",

		data: { 'sec_id[]': sec_id, 'sec_status[]': sec_status},

		xhr: function (){

			var jqXHR = null;

			if ( window.ActiveXObject ){

				jqXHR = new window.ActiveXObject( "Microsoft.XMLHTTP" );

			}else {

				jqXHR = new window.XMLHttpRequest();

			}

			jqXHR.upload.addEventListener( "progress", function ( evt ){

				if ( evt.lengthComputable ){

					var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
					// $('.home_sections_positions_loading').text(percentComplete + "% "+'<?= __('admin.completed') ?>');

				}

			}, false );

			jqXHR.addEventListener( "progress", function ( evt ){

				if ( evt.lengthComputable ){

					var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
					// $('.home_sections_positions_loading').text(percentComplete + "%"+'<?= __('admin.completed') ?>');

				}

			}, false );

			return jqXHR;

		},

		complete: function(){

			// $('.home_sections_positions_loading').text('<?= __('admin.records_updated_successfully') ?>');

			setTimeout(function(){ $('.home_sections_positions_loading').hide(); }, 500);

		}

	});

}


$(document).on('click', '.theme_multiple_page_settings_save', function(){
	let postData = {};

	$('.theme_multiple_page_settings').each(function( index ) {
	  postData[$(this).attr('name')] = $(this).val();
	});

	$.ajax({

		url: "<?= base_url('themes/store_theme_multiple_page_settings')  ?>",

		type: "POST",

		data: postData,

		success: function (response) {	

			console.log(response);	

		}
	});
});


function change_theme_multiple_page(that, type){

	let value = $(that).hasClass('fa-toggle-off') ? 1 : 0;

	$('input[name="theme_multiple_page['+type+']"]').val(value);

	if ( value == 0 ) {

		$(that).addClass('fa-toggle-off');

		$(that).removeClass('fa-toggle-on');

		$(that).css("color", "red");

		$('.'+type+'_timing').css('display', 'none');

	} else {

		$(that).addClass('fa-toggle-on');

		$(that).removeClass('fa-toggle-off');

		$(that).css("color", "green");

		$('.'+type+'_timing').css('display', '');
	}
}

function change_section_status(id){

	let status = $('#section_status_'+id).val();

	if ( status == 1 ) {

		$('#section_status_'+id).val(0);

		$('#section_status_active_'+id).addClass('fa-toggle-off');

		$('#section_status_active_'+id).removeClass('fa-toggle-on');

		$('#section_status_active_'+id).css("color", "red");

	} else {

		$('#section_status_'+id).val(1);

		$('#section_status_active_'+id).addClass('fa-toggle-on');

		$('#section_status_active_'+id).removeClass('fa-toggle-off');

		$('#section_status_active_'+id).css("color", "green");

	}

	update_homepage_sections_table();

}

function change_page_status(id){

	var page_status = $('#page_status_'+id).val();

	if (page_status== 1) {

		var status = 0;

		var msg = '<?= __('admin.page_inactive_successfully') ?>';

	}else{

		var status = 1;

		var msg = '<?= __('admin.page_active_successfully') ?>';

	}

	$.ajax({

	url: "<?= base_url('themes/update_page_status/')  ?>",

	type: "POST",

	dataType: "json",

	data: {id:id,status:status},

	success: function (data)

	{	

		if (page_status == 1) {

			$('#page_status_active_'+id).addClass('fa-toggle-off');

			$('#page_status_active_'+id).removeClass('fa-toggle-on');

			$('#page_status_active_'+id).css("color", "red");

			$('#page_status_'+id).val(0);

		}

		if (page_status == 0) {

			$('#page_status_active_'+id).addClass('fa-toggle-on');

			$('#page_status_active_'+id).removeClass('fa-toggle-off');

			$('#page_status_active_'+id).css("color", "green");

			$('#page_status_'+id).val(1);

		}

	}
	});
}


$(document).on('click', '.btn-delete-image', function(){
	let input_name = $(this).data('img_input');
	let image_ele_id = $(this).data('img_ele');
	let placeholder_image = $(this).data('img_placeholder');
	$('input[name="'+input_name+'"]').val('');
	$('#'+image_ele_id).attr('src', placeholder_image);
	$(this).remove()
});

$(document).on('change', '#search_theme_pages', function(){
	let menu_name 				= $(this).val();
	let current_url 			= $(location).attr('href');
	
	var url = new URL(current_url);
	url.searchParams.set("menu_pages", menu_name);

	window.location.href = url.href; 
});

$(".save-theme-settings").on('click',function(evt){
    evt.preventDefault();

    var front_header_color_before_scroll = $('.front_header_color_before_scroll').val();
    if (front_header_color_before_scroll == '#000000') {
    	front_header_color_before_scroll = 'transparent';
    }
    var front_header_button_color_before_scroll = $('.front_header_button_color_before_scroll').val();
    var front_header_button_text_color_before_scroll = $('.front_header_button_text_color_before_scroll').val();
    var front_header_button_hover_color_before_scroll = $('.front_header_button_hover_color_before_scroll').val();
    var front_header_color_after_scroll = $('.front_header_color_after_scroll').val();
    var front_header_button_color_after_scroll = $('.front_header_button_color_after_scroll').val();
    var front_header_button_text_color_after_scroll = $('.front_header_button_text_color_after_scroll').val();
    var front_header_button_hover_color_after_scroll = $('.front_header_button_hover_color_after_scroll').val();
    var front_button_color = $('.front_button_color').val();
    var front_button_hover_color = $('.front_button_hover_color').val();
    var front_button_text_color = $('.front_button_text_color').val();
    var front_runner_bar_color = $('.front_runner_bar_color').val();
    var front_runner_bar_text_color = $('.front_runner_bar_text_color').val();
    var front_theme_text_color = $('.front_theme_text_color').val();
    var front_faq_before_hover_color = $('.front_faq_before_hover_color').val();
    var front_faq_after_hover_color = $('.front_faq_after_hover_color').val();
    var bottom_banner_before_footer = $('.bottom_banner_before_footer').val();
    var front_footer_color = $('.front_footer_color').val();
    var header_menu_bg_color_responsive = $('.header_menu_bg_color_responsive').val();
    

    var data = {
    	'theme[front_header_color_before_scroll]':front_header_color_before_scroll,
    	'theme[front_header_button_color_before_scroll]':front_header_button_color_before_scroll,
    	'theme[front_header_button_text_color_before_scroll]':front_header_button_text_color_before_scroll,
    	'theme[front_header_button_hover_color_before_scroll]':front_header_button_hover_color_before_scroll,
    	'theme[front_header_color_after_scroll]':front_header_color_after_scroll,
    	'theme[front_header_button_color_after_scroll]':front_header_button_color_after_scroll,
    	'theme[front_header_button_text_color_after_scroll]':front_header_button_text_color_after_scroll,
    	'theme[front_header_button_hover_color_after_scroll]':front_header_button_hover_color_after_scroll,
    	'theme[front_button_color]':front_button_color,
    	'theme[front_button_hover_color]':front_button_hover_color,
    	'theme[front_button_text_color]':front_button_text_color,
    	'theme[front_runner_bar_color]':front_runner_bar_color,
    	'theme[front_runner_bar_text_color]':front_runner_bar_text_color,
    	'theme[front_theme_text_color]':front_theme_text_color,
    	'theme[front_faq_before_hover_color]':front_faq_before_hover_color,
    	'theme[front_faq_after_hover_color]':front_faq_after_hover_color,
    	'theme[bottom_banner_before_footer]':bottom_banner_before_footer,
    	'theme[front_footer_color]':front_footer_color,
    	'theme[header_menu_bg_color_responsive]':header_menu_bg_color_responsive
    }

    $.ajax({
    	url:'<?= base_url('themes/themesetting/')  ?>',
        type:'POST',
        dataType:'json',
        cache:false,
        data:data,
        success:function(result){
        	
            $(".save-theme-settings").btn("reset");
            $(".alert-dismissable").remove(); 
            if(result['location']){
                //window.location = result['location'];
            }

            if(result['success']){
                showPrintMessage(result['success'],'success');
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

$(".default-front-theme-setting").on("click", function(){
    var setting = $(this).val();
    var color = '';

    if (setting == "front_header_color_before_scroll") {
        color = "transparent";
        $("input[name='theme[front_header_color_before_scroll]']").val(color);
    }else if (setting == 'front_header_button_color_before_scroll') {
        color = "#E98024";
        $("input[name='theme[front_header_button_color_before_scroll]']").val(color);
    }else if (setting == 'front_header_button_text_color_before_scroll') {
        color = "#ffffff";
        $("input[name='theme[front_header_button_text_color_before_scroll]']").val(color);
    }else if (setting == 'front_header_button_hover_color_before_scroll') {
        color = "#F66B14";
        $("input[name='theme[front_header_button_hover_color_before_scroll]']").val(color);
    }else if (setting == 'front_header_color_after_scroll') {
        color = "#E98024";
        $("input[name='theme[front_header_color_after_scroll]']").val(color);
    }else if (setting == 'front_header_button_color_after_scroll') {
        color = "#ffffff";
        $("input[name='theme[front_header_button_color_after_scroll]']").val(color);
    }else if (setting == 'front_header_button_text_color_after_scroll') {
        color = "#E98024";
        $("input[name='theme[front_header_button_text_color_after_scroll]']").val(color);
    }else if (setting == 'front_header_button_hover_color_after_scroll') {
        color = "#FC535E";
        $("input[name='theme[front_header_button_hover_color_after_scroll]']").val(color);
    }else if (setting == 'front_button_color') {
        color = "#f1a05a";
        $("input[name='theme[front_button_color]']").val(color);
    }else if (setting == 'front_button_hover_color') {
        color = "#F66B14";
        $("input[name='theme[front_button_hover_color]']").val(color);
    }else if (setting == 'front_button_text_color') {
        color = "#ffffff";
        $("input[name='theme[front_button_text_color]']").val(color);
    }else if (setting == 'front_runner_bar_color') {
        color = "#E98024";
        $("input[name='theme[front_runner_bar_color]']").val(color);
    }else if (setting == 'front_runner_bar_text_color') {
        color = "#ffffff";
        $("input[name='theme[front_runner_bar_text_color]']").val(color);
    }else if (setting == 'front_theme_text_color') {
        color = "#E98024";
        $("input[name='theme[front_theme_text_color]']").val(color);
    }else if (setting == 'front_faq_before_hover_color') {
        color = "#6A6A6A";
        $("input[name='theme[front_faq_before_hover_color]']").val(color);
    }else if (setting == 'front_faq_after_hover_color') {
        color = "#E98024";
        $("input[name='theme[front_faq_after_hover_color]']").val(color);
    }else if (setting == 'front_footer_color') {
        color = "#363839";
        $("input[name='theme[front_footer_color]']").val(color);
    }else if (setting == 'bottom_banner_before_footer') {
        color = "#D4731E";
        $("input[name='theme[bottom_banner_before_footer]']").val(color);
    }
 	else if (setting == 'header_menu_bg_color_responsive') {
        color = "#dd7a23";
        $("input[name='theme[header_menu_bg_color_responsive]']").val(color);
    }

 

    if(color != '') {
        $.ajax({
            url:base_url+'themes/default_front_theme_settings',
            type:'POST',
            dataType:'json',
            data:{'action':'default_front_theme_settings', setting:setting, color:color},
            success:function(json){
            },
        });
    }
});

$(document).ready(function(){
    $.ajax({
        url:'<?= base_url("themes/set_default_front_theme_settings") ?>',
        type:'POST',
        dataType:'json',
        data:{'action':'set_default_front_theme_settings', 'setting_type':'theme'},
        success:function(json){

        },
    });
	
	changeLanguage();
});

function getSettingTabContent($language_id)
{
    $.ajax({
            url:'<?= base_url("themes/getSettingTabContent") ?>',
            type:'POST',
            dataType:'json',
            data:{'language_id':$language_id, 'setting_type':'theme'},
            beforeSend:function(){},
            complete:function(){},
            success:function(json){  
               if(json['html']){
                  $("#setting_content_html").html(json['html']);  
               } else {
                 
               } 
            },
       });
}
function changeLanguage()
{
	$language_id=$("#drpLanguage").val();
	getSettingTabContent($language_id);
}
</script>
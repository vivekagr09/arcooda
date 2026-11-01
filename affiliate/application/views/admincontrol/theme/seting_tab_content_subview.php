<fieldset class="mt-3">

	<legend><?= __('admin.top_banner_runner') ?></legend>

	<div id="runners-section" class="row">
		<?php 
		$setting_id=null;
		foreach ($theme_settings as $settings) { 

			$setting_id = $settings->settings_id;

			$top_banner_slider = json_decode($settings->top_banner_slider);
		} 

		?>

		<input type ="hidden" name="settings_id" value ="<?php echo $setting_id;?>">

		<?php

		if(isset($top_banner_slider) && sizeof($top_banner_slider) > 0) {

			foreach($top_banner_slider as $runner){

		?>

		<div class="col-md-12">

			<div class="form-group">

				<label class="control-label"><?= __('admin.runner') ?> 1</label>

				<input name="top_banner_slider[]" class="form-control" type="text" value="<?= $runner; ?>">

			</div>

			<button type="button" class="btn btn-danger btn-md remove-runner-btn" style="position: absolute; top: 30px; right: 11px;"><i class="fa fa-trash"></i></button>

		</div>

		<?php

			}

		} else {

		?>

		<div class="col-md-12">

			<div class="form-group">

				<label class="control-label"><?= __('admin.runner') ?> 1</label>

				<input name="top_banner_slider[]" class="form-control" type="text">

			</div>

			<button type="button" class="btn btn-danger btn-md remove-runner-btn" style="position: absolute; top: 30px; right: 11px;"><i class="fa fa-trash"></i></button>

		</div>

		<?php

		}

		?>

		<div class="col-md-12">

			<button id="add-more-runner-btn" type="button" class="btn btn-primary btn-md"><i class="fa fa-plus"></i> <?= __('admin.add_more_runners') ?></button>

		</div>

	</div>

	

</fieldset>

<fieldset class="mt-3">

	<legend><?= __('admin.logo') ?></legend>

	<div class="row">
		<div class="col-md-3">
			<label class="control-label"><?= __('admin.logo') ?>:</label>
			<div class="form-group">
				<div class="fileUpload btn btn-sm btn-primary m-0">

					<span><?= __('admin.choose_file') ?></span>

					<input id="logo" name="logo" class="upload" type="file" >

				</div>
				<p class="logo-info-text m-0"><?= __('admin.multiple_pages_theme_logo_recommended_size') ?></p>
			</div>
		</div>
		<div class="col-md-9">
			<div class="form-group mt-4">
				<input type="hidden" name="hidden_logo" value="<?= $settings->logo ?>" />

				<?php
					$logo = false;
					$avatar= 'assets/vertical/assets/images/no_image_yet.png';
					if($settings->logo !=''){
						$logo = true;
						$avatar= '/assets/images/theme_images/'.$settings->logo;
					}
				?>

				<img id="output_logo"  src="<?= base_url($avatar); ?>" class="thumbnail" border="0" width="220px" />

				<?php if($logo) { ?>
				<span class="btn btn-sm btn-danger btn-delete-image" data-img_input="hidden_logo" data-img_ele="output_logo" data-img_placeholder="<?= base_url('assets/vertical/assets/images/no_image_yet.png');?>"><i class="fa fa-trash"></i></span>
				<?php } ?>	

			</div>
		</div>

		<div class="col-sm-4">
			<div class="form-group">
				<label  class="control-label"><?= __('admin.site_setting_logo_custom_size') ?></label>
				<select name="custom_logo_size" class="form-control">
					<option value="0"><?= __('admin.disable') ?></option>
					<option <?= ($settings->custom_logo_size == 1) ? "selected" :""; ?> value="1"><?= __('admin.multiple_pages_theme') ?></option>
				</select>
			</div>
		</div>

		<div class="col-sm-4 logo_cust_size_inp" <?= ($settings->custom_logo_size != 1) ? 'style="display:none;"' :""; ?>>
			<div class="form-group">
			<label  class="control-label"><?= __('admin.site_setting_logo_width') ?></label>
			<input name="log_custom_width" value="<?= $settings->log_custom_width; ?>" class="form-control" type="number">
			</div>
		</div>

		<div class="col-sm-4 logo_cust_size_inp" <?= ($settings->custom_logo_size != 1) ? 'style="display:none;"':""; ?>>
			<div class="form-group">
			<label  class="control-label"><?= __('admin.site_setting_logo_height') ?></label>
			<input name="log_custom_height" value="<?= $settings->log_custom_height; ?>" class="form-control" type="number">
			</div>
		</div>

		<script type="text/javascript">
			$(document).on('change', 'select[name="custom_logo_size"]', function() {
					if($(this).val() == 1) {
						$('.logo_cust_size_inp').show();
					} else {
						$('.logo_cust_size_inp').hide();
					}
			});
		</script>
	</div>
</fieldset>

<fieldset class="mt-3">

	<legend><?= __('admin.home_section_title_sub_title') ?></legend>

	<div class="row">

		<?php foreach ($theme_settings as $settings) { $setting_id = $settings->settings_id; } ?>

		<div class="col-md-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.top_title') ?></label>

				<input type ="hidden" name= "settings_id" value ="<?php echo @$setting_id;?>" >

				<input name="home_section_title" value="<?php echo $settings->home_section_title; ?>" class="form-control" type="text">

			</div>

		</div>

		

		<div class="col-md-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.sub_title') ?></label>

				<input name="home_section_subtitle" class="form-control" value="<?php echo $settings->home_section_subtitle; ?>" type="text">

			</div>

		</div>

	</div>

</fieldset>

<fieldset class="mt-3">

	<legend><?= __('admin.recommendation_section_title_sub_title') ?></legend>

	<div class="row">

		<?php foreach ($theme_settings as $settings) { $setting_id = $settings->settings_id; } ?>

		<div class="col-md-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.top_title') ?></label>

				<input type ="hidden" name= "settings_id" value ="<?php echo @$setting_id;?>" >

				<input name="recommendation_section_title" value="<?php echo $settings->recommendation_section_title; ?>" class="form-control" type="text">

			</div>

		</div>

		

		<div class="col-md-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.sub_title') ?></label>

				<input name="recommendation_section_subtitle" class="form-control" value="<?php echo $settings->recommendation_section_subtitle; ?>" type="text">

			</div>

		</div>

	</div>

</fieldset>		

<fieldset class="mt-3">

	<legend><?= __('admin.membership_section_title_sub_title') ?></legend>

	<div class="row">

		<?php foreach ($theme_settings as $settings) { $setting_id = $settings->settings_id; } ?>

		<div class="col-md-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.top_title') ?></label>

				<input type ="hidden" name= "settings_id" value ="<?php echo @$setting_id;?>" >

				<input name="membership_top_title" value="<?php echo $settings->membership_top_title; ?>" class="form-control" type="text">

			</div>

		</div>

		

		<div class="col-md-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.sub_title') ?></label>

				<input name="membership_sub_title" class="form-control" value="<?php echo $settings->membership_sub_title; ?>" type="text">

			</div>

		</div>

	</div>

</fieldset>

<fieldset class="mt-3">

	<legend><?= __('admin.videos_section_background') ?></legend>

	<div class="row">

		<?php foreach ($theme_settings as $settings) { $setting_id = $settings->settings_id; } ?>

		<div class="col-md-12">

			<div class="form-group">

				<label class="control-label"><?= __('admin.background_image') ?></label>

				<div>

					<div class="fileUpload btn btn-sm btn-primary">

						<span><?= __('admin.choose_file') ?></span>

						<input id="homepage_video_section_bg" name="homepage_video_section_bg" class="upload" type="file" >

					</div>

					<input type="hidden" name="hidden_homepage_video_section_bg" value="<?= $settings->homepage_video_section_bg ?>" />

					<?php
					$homepage_video_section_bg_dlt = false;
					$avatar= 'assets/login/multiple_pages/img/video-section-bg.png';
					if($settings->homepage_video_section_bg !=''){
						$homepage_video_section_bg_dlt = true;
						$avatar= '/assets/images/theme_images/'.$settings->homepage_video_section_bg;
					}
					?>

					<img id="output_homepage_video_section_bg"  src="<?php echo base_url().$avatar;?>" class="thumbnail" border="0" width="220px" />

					<?php if($homepage_video_section_bg_dlt) { ?>
					<span class="btn btn-sm btn-danger btn-delete-image" data-img_input="hidden_homepage_video_section_bg" data-img_ele="output_homepage_video_section_bg" data-img_placeholder="<?php echo base_url();?>assets/login/multiple_pages/img/video-section-bg.png"><i class="fa fa-trash"></i></span>
					<?php } ?>																			

				</div>

			</div>

		</div>

	</div>

	<!-- <div class="row">

	<div class="col-sm-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.video_title') ?></label>

				<input name="video_title" class="form-control" value="<?php echo $settings->video_title; ?>" type="text">

			</div>

		</div>

	<div class="col-sm-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.video_sub_title') ?></label>

				<input name="video_sub_title" class="form-control" value="<?php echo $settings->video_sub_title; ?>" type="text">

			</div>

		</div>

	</div> -->

</fieldset>

<fieldset class="mt-3">

	<legend><?= __('admin.faq_page') ?></legend>

	<div class="row">

		<?php foreach ($theme_settings as $settings) { $setting_id = $settings->settings_id; } ?>

		<div class="col-md-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.banner_title') ?></label>

				<input type ="hidden" name= "settings_id" value ="<?php echo @$setting_id;?>" >

				<input name="faq_banner_title" value="<?php echo $settings->faq_banner_title; ?>" class="form-control" type="text">

			</div>

		</div>

		

		<div class="col-md-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.section_title') ?></label>

				<input name="faq_section_title" class="form-control" value="<?php echo $settings->faq_section_title; ?>" type="text">

			</div>

		</div>



		<div class="col-md-12">

			<div class="form-group">

				<label class="control-label"><?= __('admin.section_sub_title') ?></label>

				<input name="faq_section_subtitle" class="form-control" value="<?php echo $settings->faq_section_subtitle; ?>" type="text">

			</div>

		</div>



		<div class="col-md-12">

			<div class="form-group">

				<label class="control-label"><?= __('admin.banner_image') ?></label>

				<div>

					<div class="fileUpload btn btn-sm btn-primary">

						<span><?= __('admin.choose_file') ?></span>

						<input id="faq_banner_image" name="faq_banner_image" class="upload" type="file" >

					</div>

					<input type="hidden" name="hidden_faq_banner_image" value="<?= $settings->faq_banner_image ?>" />

					<?php
						$faq_banner_image = false;
						$avatar= 'assets/login/multiple_pages/img/bg-photo.jpg';
						if($settings->faq_banner_image !=''){
							$faq_banner_image = true;
							$avatar= '/assets/images/theme_images/'.$settings->faq_banner_image;
						}
					?>

					<img id="output_faq_banner_image"  src="<?php echo base_url().$avatar;?>" class="thumbnail" border="0" width="220px" />

					<?php if($faq_banner_image) { ?>
					<span class="btn btn-sm btn-danger btn-delete-image" data-img_input="hidden_faq_banner_image" data-img_ele="output_faq_banner_image" data-img_placeholder="<?php echo base_url();?>assets/login/multiple_pages/img/bg-photo.jpg"><i class="fa fa-trash"></i></span>
					<?php } ?>	
				</div>

			</div>

		</div>

	</div>

</fieldset>

<fieldset class="mt-5">

	<legend><?= __('admin.contact_us_page') ?></legend>

	<div class="row">

		<div class="col-md-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.banner_title') ?></label>

				<input name="contact_us_t_title" value="<?php echo $settings->contact_us_t_title; ?>" class="form-control" type="text">

			</div>

		</div>

		

		<div class="col-md-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.banner_sub_title') ?></label>

				<input name="contact_us_slug_title" class="form-control" value="<?php echo $settings->contact_us_slug_title; ?>" type="text">

			</div>

		</div>

		<div class="col-md-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.section_title') ?></label>

				<input name="contact_sec_title" value="<?php echo $settings->contact_sec_title; ?>" class="form-control" type="text">

			</div>

		</div>

		

		<div class="col-md-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.section_sub_title') ?></label>

				<input name="contact_sec_subtitle" class="form-control" value="<?php echo $settings->contact_sec_subtitle; ?>" type="text">

			</div>

		</div>

		<div class="col-md-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.full_address') ?></label>

				<input name="contact_us_full_address" class="form-control" value="<?php echo $settings->contact_us_full_address; ?>" type="text">

			</div>

		</div>

		<div class="col-md-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.phone_number') ?></label>

				<input name="contact_us_phone" class="form-control" value="<?php echo $settings->contact_us_phone; ?>" type="text" maxlength="20">

				<small><?= __('admin.type_the_symbol_before_the_number') ?></small>

				<span class="text-danger" id="contact_us_phone_error"></span>

			</div>

		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label"><?= __('admin.email_address') ?></label>
				<input name="contact_us_email" class="form-control" value="<?php echo $settings->contact_us_email; ?>" type="email" />
				<span class="text-danger" id="contact_us_email_error"></span>
			</div>
		</div>

		<div class="col-md-12">

			<div class="form-group">

				<label class="control-label"><?= __('admin.google_map_iframe') ?></label>

				<textarea name="contact_us_iframe" class="form-control" type="text" rows=4><?php echo $settings->contact_us_iframe; ?></textarea>

			</div>

		</div>

		<div class="col-sm-3">

			<div class="form-group">

				<label class="control-label"><?= __('admin.youtube_link') ?></label>

				<input placeholder="<?= __('admin.youtube_link') ?>" name="youtube_link" id="youtube_link" class="form-control" value="<?php echo $settings->youtube_link; ?>" type="text">

				<span class="text-danger" id="youtube_link_error"></span>

			</div>

		</div>

		<div class="col-sm-3">

			<div class="form-group">

				<label class="control-label"><?= __('admin.facebook_link') ?></label>

				<input placeholder="<?= __('admin.facebook_link') ?>" name="facebook_link" id="facebook_link" class="form-control" value="<?php echo $settings->facebook_link; ?>" type="text">

				<span class="text-danger" id="facebook_link_error"></span>

			</div>

		</div>

		<div class="col-sm-3">

			<div class="form-group">

				<label class="control-label"><?= __('admin.twitter_link') ?></label>

				<input placeholder="<?= __('admin.twitter_link') ?>" name="twitter_link" id="twitter_link" class="form-control" value="<?php echo $settings->twitter_link; ?>" type="text">

				<span class="text-danger" id="twitter_link_error"></span>

			</div>

		</div>

		<div class="col-sm-3">

			<div class="form-group">

				<label class="control-label"><?= __('admin.instagram_link') ?></label>

				<input placeholder="<?= __('admin.instagram_link') ?>" name="instegram_link" id="instegram_link" class="form-control" value="<?php echo $settings->instegram_link; ?>" type="text">

				<span class="text-danger" id="instegram_link_error"></span>

			</div>

		</div>

		<div class="col-sm-4">

			<div class="form-group">

				<label class="control-label"><?= __('admin.whatsapp_number') ?></label>

				<input placeholder="<?= __('admin.whatsapp_number') ?>" name="whatsapp_number" id="whatsapp_number" class="form-control" value="<?php echo $settings->whatsapp_number; ?>" type="text">

				<span class="text-danger" id="whatsapp_number_error"></span>

			</div>

		</div>

		<div class="col-sm-8">

			<div class="form-group">

				<label class="control-label"><?= __('admin.default_message') ?></label>

				<input placeholder="<?= __('admin.default_message') ?>" name="whatsapp_default_msg" id="whatsapp_default_msg" class="form-control" value="<?php echo $settings->whatsapp_default_msg; ?>" type="text">

				<span class="text-danger" id="whatsapp_default_msg_error"></span>

			</div>

		</div>
		

		<div class="col-md-12">

			<div class="form-group">

				<label class="control-label"><?= __('admin.banner_image') ?></label>

				<div>

					<div class="fileUpload btn btn-sm btn-primary">

						<span><?= __('admin.choose_file') ?></span>

						<input id="contact_banner_image" name="contact_banner_image" class="upload" type="file" >

					</div>

					<input type="hidden" name="hidden_contact_banner_image" value="<?= $settings->contact_banner_image ?>" />

					<?php
						$contact_banner_image = false;
						$avatar= 'assets/login/multiple_pages/img/bg-photo.jpg';
						if($settings->contact_banner_image !=''){
							$contact_banner_image = true;
							$avatar= '/assets/images/theme_images/'.$settings->contact_banner_image;
						}
					?>

					<img id="output_contact_banner_image"  src="<?php echo base_url().$avatar;?>" class="thumbnail" border="0" width="220px" />

					<?php if($contact_banner_image) { ?>
					<span class="btn btn-sm btn-danger btn-delete-image" data-img_input="hidden_contact_banner_image" data-img_ele="output_contact_banner_image" data-img_placeholder="<?php echo base_url();?>assets/login/multiple_pages/img/bg-photo.jpg"><i class="fa fa-trash"></i></span>
					<?php } ?>	
				</div>

			</div>

		</div>

	</div>

</fieldset>



<fieldset class="mt-5">

	<legend><?= __('admin.footer_edit_section') ?></legend>

	<div class="row">

		<!-- <div class="col-sm-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.footer_about_title') ?></label>

				<input name="footer_about_title" class="form-control" value="<?php echo $settings->footer_about_title; ?>" type="text">

			</div>

		</div>

		<div class="col-sm-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.footer_about_text') ?></label>

				

				<textarea name="footer_about_text" class="form-control" type="text"><?php echo $settings->footer_about_text; ?></textarea>

			</div>

		</div>
-->
		<div class="col-sm-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.menu_a_title') ?></label>

				<input name="footer_menu_title_a" class="form-control" value="<?php echo $settings->footer_menu_title_a; ?>" type="text">

			</div>

		</div>

		<div class="col-sm-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.menu_b_title') ?></label>

				<input name="footer_menu_title_b" class="form-control" value="<?php echo $settings->footer_menu_title_b; ?>" type="text">

			</div>

		</div>

		<div class="col-sm-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.menu_c_title') ?></label>

				<input name="footer_menu_title_c" class="form-control" value="<?php echo $settings->footer_menu_title_c; ?>" type="text">

			</div>

		</div>

		<div class="col-sm-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.menu_d_title') ?></label>

				<input name="footer_menu_title_d" class="form-control" value="<?php echo $settings->footer_menu_title_d; ?>" type="text">

			</div>

		</div>

		<div class="col-sm-12">

			<div class="form-group">

				<label class="control-label"><?= __('admin.copyright') ?></label>

				<input placeholder="<?= __('admin.insert_your_copyright') ?>" name="copyright" class="form-control" value="<?php echo $settings->copyright; ?>" type="text">

			</div>

		</div>

	</div>

</fieldset>

<fieldset class="mt-5">

	<legend><?= __('admin.bottom_banner_settings') ?></legend>

	<div class="row">

		<div class="col-sm-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.banner_bottom_title') ?></label>

				<input placeholder="<?= __('admin.banner_bottom_title') ?>" name="banner_bottom_title" class="form-control" value="<?php echo $settings->banner_bottom_title; ?>" type="text">

			</div>

		</div>

		<div class="col-sm-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.banner_bottom_slug') ?></label>

				<input placeholder="<?= __('admin.banner_bottom_slug') ?>" name="banner_bottom_slug" class="form-control" value="<?php echo $settings->banner_bottom_slug; ?>" type="text">

			</div>

		</div>

		<div class="col-sm-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.button_text') ?></label>

				<input placeholder="<?= __('admin.button_text') ?>" name="banner_button_text" class="form-control" value="<?php echo $settings->banner_button_text; ?>" type="text">

			</div>

		</div>

		<div class="col-sm-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.link') ?></label>

				<input placeholder="<?= __('admin.link') ?>" name="banner_button_link" id="banner_button_link" class="form-control" value="<?php echo $settings->banner_button_link; ?>" type="text">

				<span class="text-danger" id="banner_button_link_error"></span>

			</div>

		</div>

	</div>

</fieldset>



<fieldset class="mt-5">

	<legend><?= __('admin.login_registration_terms') ?></legend>

	<div class="row">

		<div class="col-md-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.login_page_text_area') ?></label>

				<textarea name="login_content" rows="10" class="form-control" type="text"><?php echo $settings->login_content; ?></textarea>

				<small><?= __('admin.recommend_max_100_characters') ?></small>

			</div>

		</div>

		

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label"><?= __('admin.login_page_background_image') ?></label>
				<div>
					<div class="fileUpload btn btn-sm btn-primary">
						<span><?= __('admin.choose_file') ?></span>
						<input id="avatar_login" name="avatar_login" class="upload" type="file" >
					</div>

					<?php
						$is_login_img = false;
						$avatar= 'assets/login/multiple_pages/img/bg-photo.jpg';
						if($settings->login_img !=''){
							$is_login_img = true;
							$avatar= '/assets/images/theme_images/'.$settings->login_img;
						}
					?>
					<input type="hidden" name="hidden_login_img" value="<?= $settings->login_img ?>" />

					<img id="output_login"  src="<?php echo base_url().$avatar;?>" class="thumbnail" border="0" width="220px" />

					<?php if($is_login_img) { ?>
					<span class="btn btn-sm btn-danger btn-delete-image" data-img_input="hidden_login_img" data-img_ele="output_login" data-img_placeholder="<?php echo base_url();?>assets/login/multiple_pages/img/bg-photo.jpg"><i class="fa fa-trash"></i></span>
					<?php } ?>
				</div>
			</div>
		</div>



		<div class="col-md-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.registration_page_text_area') ?></label>

				<textarea name="reg_content" rows="10" value="" class="form-control" type="text"><?php echo $settings->reg_content; ?></textarea>

				<small><?= __('admin.recommend_max_100_characters') ?></small>

			</div>

		</div>

		

		<div class="col-md-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.registration_page_background_image') ?></label>

				<div>
					<div class="fileUpload btn btn-sm btn-primary">
						<span><?= __('admin.choose_file') ?></span>
						<input id="avatar_registration" name="avatar_registration" class="upload" type="file" >
					</div>

					<?php
					$is_reg_img = false;
					$avatar= 'assets/login/multiple_pages/img/bg-photo.jpg';
					if($settings->reg_img !=''){
						$is_reg_img = true;
						$avatar= '/assets/images/theme_images/'.$settings->reg_img;
					}
					?>

					<input type="hidden" name="hidden_reg_img" value="<?= $settings->reg_img ?>" />

					<img id="output_registration"  src="<?php echo base_url().$avatar;?>" class="thumbnail" border="0" width="220px" />

					<?php if($is_reg_img) { ?>
					<span class="btn btn-sm btn-danger btn-delete-image" data-img_input="hidden_reg_img" data-img_ele="output_registration" data-img_placeholder="<?php echo base_url();?>assets/login/multiple_pages/img/bg-photo.jpg"><i class="fa fa-trash"></i></span>
					<?php } ?>																		
				</div>
			</div>
		</div>



		<div class="col-md-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.terms_page_text_area') ?></label>

				<textarea name="terms_content" rows="10" value="" class="form-control" type="text"><?php echo $settings->terms_content; ?></textarea>

			</div>

		</div>

		

		<div class="col-md-6">

			<div class="form-group">

				<label class="control-label"><?= __('admin.terms_page_background_image') ?></label>

				

				<div>
					<div class="fileUpload btn btn-sm btn-primary">
						<span><?= __('admin.choose_file') ?></span>
						<input id="avatar_terms" name="avatar_terms" class="upload" type="file" >
					</div>

					<?php
					$is_terms_img = false;
					$avatar= 'assets/login/multiple_pages/img/bg-photo.jpg';
					if($settings->terms_img !=''){
						$is_terms_img = true;
						$avatar= '/assets/images/theme_images/'.$settings->terms_img;
					}
					?>

					<input type="hidden" name="hidden_terms_img" value="<?= $settings->terms_img ?>" />

					<img id="output_terms"  src="<?php echo base_url().$avatar;?>" class="thumbnail" border="0" width="220px" />

					<?php if($is_terms_img) { ?>
					<span class="btn btn-sm btn-danger btn-delete-image" data-img_input="hidden_terms_img" data-img_ele="output_terms" data-img_placeholder="<?php echo base_url();?>assets/login/multiple_pages/img/bg-photo.jpg"><i class="fa fa-trash"></i></span>
					<?php } ?>																		

				</div>
			</div>
		</div>
	</div>
</fieldset>

<script type="text/javascript">
	$("#logo").change(function() {
  read_url(this,'hidden_logo','output_logo');
});

$("#faq_banner_image").change(function() {
  read_url(this,'hidden_faq_banner_image','output_faq_banner_image');
});

$("#contact_banner_image").change(function() {
  read_url(this,'hidden_contact_banner_image','output_contact_banner_image');
});

$("#homepage_video_section_bg").change(function() {
  read_url(this,'hidden_homepage_video_section_bg','output_homepage_video_section_bg');
});

$("#avatar_login").change(function() {
  read_url(this,'hidden_login_img','output_login');
});

$("#avatar_registration").change(function() {
  read_url(this,'hidden_reg_img','output_registration');
});

$("#avatar_terms").change(function() {
  read_url(this,'hidden_terms_img','output_terms');
});
</script>
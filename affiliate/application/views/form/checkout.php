<link rel="stylesheet" href="<?= base_url('assets/plugins/tel/css/intlTelInput.css') ?>?v=<?= av() ?>">
<script src="<?= base_url('assets/plugins/tel/js/intlTelInput.js') ?>"></script>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="author" content="">
<link href="<?php echo base_url('assets/plugins/store/') ?>/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo base_url('assets/plugins/store/') ?>/shop-homepage.css" rel="stylesheet">

  <!--  CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/store/default/'); ?>fonts/fonts.css" />
    <link rel="stylesheet" href="<?= base_url('assets/store/default/'); ?>fonts/fonts.css" />
    <link rel="stylesheet" href="<?= base_url('assets/store/default/'); ?>css/placeholder-loading.css" />
    <link rel="stylesheet" href="<?= base_url('assets/store/default/'); ?>css/sweetalert2.min.css" />
    <link rel="stylesheet" href="<?= base_url('assets/store/default/'); ?>css/nouislider.css" />
    <link rel="stylesheet" href="<?= base_url('assets/store/default/'); ?>css/style.css" />

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" crossorigin="anonymous">
    <script src="<?= base_url('assets/store/default/'); ?>js/jquery-3.5.1.slim.min.js"></script>
    <script src="<?= base_url('assets/store/default/'); ?>js/jquery.min.js"></script>
    <script src="<?= base_url('assets/store/default/'); ?>js/bootstrap.min.js"></script>
    <script src="<?= base_url('assets/plugins/store/') ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/plugins/store/') ?>jquery.star-rating-svg.js"></script>
    <script src="<?= base_url('assets/store/default/') ?>js/nouislider.min.js"></script>
    <script src="<?= base_url('assets/store/default/') ?>js/sweetalert2.all.min.js"></script>
    <script src="<?= base_url('assets/plugins/') ?>mustache.js"></script>

    
<script src="<?php echo base_url('assets/plugins/store/') ?>/vendor/jquery/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/builder_layout/font-awesome.min.css') ?>">
<meta name="twitter:card" content="summary_large_image">
<title><?php echo $page ?></title>
<?php if($analytics != ''){ ?>
	<?= $analytics ?>
<?php } ?>

<script type="text/javascript">
	
  (function ($) {
    $.fn.btn = function (action) {
        var self = $(this);
        if (action == 'loading') {
            if ($(self).attr("disabled") == "disabled") {
            }
            $(self).attr("disabled", "disabled");
            $(self).attr('data-btn-text', $(self).html());
            $(self).html('<div class="spinner-border spinner-border-sm"></div></i> ' + $(self).text());
        }
        if (action == 'reset') {
            $(self).html($(self).attr('data-btn-text'));
            $(self).removeAttr("disabled");
        }
    }
})(jQuery);
</script>
</head>
<body style="padding-top: 0;">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<ul class="list-inline float-right mb-0">
					<li class="list-inline-item dropdown">
						<?= $LanguageHtml ?>
					</li>
					<li class="list-inline-item dropdown">
						<?= $CurrencyHtml ?>
					</li>
					<?php if($is_logged) { ?>
					<li class="list-inline-item dropdown login-icon">
						<a class="nav-link dropdown-toggle arrow-none waves-effect" data-toggle="dropdown" href="#" role="button"
						aria-haspopup="false" aria-expanded="false">
		                    <?php $avatar = $is_logged['avatar'] != '' ? $is_logged['avatar'] : '../no-user_image.jpg' ; ?>
	                    	<img src="<?php echo base_url('assets/images/users/'. $avatar);?>" class="thumbnail user-img" border="0" width="25" height="25">
						</a>
						<div class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-menu-lg">
							<div class="dropdown-item noti-title">
								<span><?php echo $is_logged['firstname'] . ' ' . $is_logged['lastname'] ?></span>
							</div>
						</div>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
	<div class="container" id="body-checkout">
		<div class="row ">
			<div class="col-md-12 offset-md-0 col-lg-8 offset-lg-2 box-body dynamic-content-body">
				<h1 class="page_heading text-center"><?php echo $page ?></h1>
				<?php echo $description ?>
			</div>
			<div class="col-md-12 offset-md-0 col-lg-8 offset-lg-2 box-body">
				<div class="clearfix"><br></div>
			    <div class="row">
			        <div class="col-sm-12">
						<div class="checkout-setp cart-step">
							<div class="step-head"><h4><?= __('store.purchase_of_details') ?></h4></div>
							<div class="step-body">
								<div class="cart-loader"></div>
								<div class="cart-body"></div>
							</div>
							<div class="step-footer"></div>
							<input type="hidden" name="cookies_consent" id="cookies_consent" value="true"  />
						</div>

						<?php if(!$is_logged){ ?>
						<div class="checkout-setp auth-step" style="<?= isset($_SESSION['guestFlow']) ? "display:none" : ""?>">
							<div class="step-head"><h4><?= __('store.personal_details') ?></h4></div>
							<div class="step-body">
								<div class="row">
									<div class="col-10 offset-1 col-sm-8 offset-sm-2 text-center">
										<ul class="nav nav-pills">
										  	<li class="nav-item login">
										    	<a class="nav-link" data-toggle="pill" href="#login"><?= __('store.login') ?></a>
										  	</li>
										  	<li class="login-or-register">
										  		Or
										  	</li>
										  	<li class="nav-item register">
										    	<a class="nav-link active" data-toggle="pill" href="#register"><?= __('store.register') ?></a>
										  	</li>
										  	<li class="login-or-register">
										  		<?= __('store.or') ?>
										  	</li>
										  	<li class="nav-item register">
										    	<a href="javascript:void(0)" id="btnGuestcontinues" class="nav-link bg-secondary"><?= __('store.guest_checkout') ?></a>
										  	</li>
										  	
										</ul>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-8 offset-sm-2">
										<div class="tab-content">
										  	<div class="tab-pane container" id="login">
												<h5 class="sub-title"><?= __('store.login_with_existing_account') ?></h5>
												<form id="login-form">
													<div class="form-group">
														<input class="form-control" name="username" placeholder="<?= __('store.username') ?>" type="text">
													</div>
													<div class="form-group">
														<input class="form-control" name="password" placeholder="<?= __('store.password') ?>" type="password">
													</div>
													<div class="form-group text-right">
														<button class="btn btn-primary btn-submit">
															<?= __('store.login') ?></button>
													</div>
												</form>
										  	</div>
										  	<div class="tab-pane container active" id="register">
												<h5 class="sub-title"><?= __('store.create_a_new_account') ?>
												</h5>
												<form id="register-form">
													<div class="form-group">
														<input class="form-control" name="f_name" placeholder="<?= __('store.first_name') ?>" type="text">
													</div>
													<div class="form-group">
														<input class="form-control" name="l_name" placeholder="<?= __('store.last_name') ?>" type="text">
													</div>
													<div class="form-group">
														<input class="form-control" name="username" placeholder="<?= __('store.username') ?>" type="text">
													</div>
													<input type="hidden" name="PhoneNumberInput" id="rephonenumber-input" value="" class="form-control" placeholder="<?= __('store.phone_number') ?>" />
													
							        				<div class="form-group">
							        					<label for=""><?= __('store.phone_number') ?></label>
							        					<div>
															<input onkeypress="return isNumberKey(event);" id="phoneergister" type="text" name="phone" value="">
							        					</div>
							        				</div>
							        				<script type="text/javascript">
							        					var tel_inputre = intlTelInput(document.querySelector("#phoneergister"), {
															initialCountry: "auto",
															utilsScript: "<?= base_url('/assets/plugins/tel/js/utils.js?1562189064761') ?>",
															separateDialCode:true,
															geoIpLookup: function(success, failure) {
																$.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
																	var countryCode = (resp && resp.country) ? resp.country : "";
																	success(countryCode);
																});
															},
														});
							        				</script>
													<div class="form-group">
														<input class="form-control" name="email" placeholder="<?= __('store.email') ?>" type="email">
													</div>
													<div class="form-group">
														<input class="form-control" name="password" placeholder="<?= __('store.password') ?>" type="password">
													</div>
													<div class="form-group">
														<input class="form-control" name="c_password" placeholder="<?= __('store.confirm_password') ?>" type="password">
													</div>
													<div class="form-group text-right">
														<button class="btn btn-primary btn-submit"><?= __('store.register') ?></button>
													</div>
												</form>
										  	</div>
										</div>
									</div>
								</div>
							</div>
							<div class="step-footer"></div>
						</div>
						<?php } ?>


						<div class="non-confirm">
							<?php if ($is_logged && ! $allow_shipping): ?>
								<div class="checkout-form">	
									<h2><?= __('store.billing_address') ?></h2>
									<div class="form-checkout-wrapper">

										<div class="form-row">
											<div class="form-group">
												<label><?php echo __('store.enter_your_first_name')?></label>
												<input type="text" placeholder="<?php echo __('store.enter_your_first_name')?>" name="firstname" class="form-control" type="text" value="<?php echo $is_logged==true ? $_SESSION['client']['firstname']:''?>" required="">
											</div>
											<div class="form-group">
												<label><?php echo __('store.enter_your_last_name')?></label>
												<input type="text" placeholder="<?php echo __('store.enter_your_last_name')?>" name="lastname" class="form-control" type="text" value="<?php echo $is_logged==true ? $_SESSION['client']['lastname']:''?>" required="">
											</div>
											<div class="form-group">
												<label><?php echo __('store.enter_your_email_address')?></label>
												<input type="text" placeholder="<?php echo __('store.enter_your_email_address')?>" name="email" class="form-control" type="text" required=""  value="<?php echo $is_logged==true ? $_SESSION['client']['email']:''?>">	
												<input type="hidden" name="classified_checkout" value="1">	
											</div>

											<div class="form-group">
												<label for=""><?php echo __('store.phone')?></label>
												<input type="hidden" id="phonenumber-input" name='PhoneNumberInput' value="" class="form-control" placeholder="<?= __('store.phone_number') ?>" />
												<div>
													<input onkeypress="return isNumberKey(event);" id="phoneguest" class="form-control" placeholder="<?php echo __('store.phone')?>" type="text" name="phone"  value="<?php echo $is_logged==true ? $_SESSION['client']['phone']:''?>">
												</div>
											</div>

											<script type="text/javascript">
												var tel_input = intlTelInput(document.querySelector("#phoneguest"), {
													initialCountry: "auto",
													utilsScript: "<?=base_url()?>assets/plugins/tel/js/utils.js?1562189064761",
													separateDialCode:true,
													geoIpLookup: function(success, failure) {
														$.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
															var countryCode = (resp && resp.country) ? resp.country : "";
															success(countryCode);
														});
													},
												});
											</script>
										</div>
									</div>
								</div>	
							<?php endif ?>

							<?php if(isset($_SESSION['guestFlow']) || $allow_shipping){ ?>
							<div class="checkout-setp shipping-step" <?= (!$is_logged) ? (!isset($_SESSION['guestFlow']) ? 'style="display:none;"' : "") : ""; ?>>
								<div class="step-head">
									<h4><?php echo $allow_shipping == 1 ? __('store.billing_shipping_address') : __('store.billing_address');?></h4>
								</div>
								
								<div class="step-body">
									<?php if($show_blue_message){ ?>
										<div class="alert alert-info"><?= $shipping_error_message ?></div>
									<?php } ?>
									<?php if(isset($shipping_not_allow_error_message)){ ?>
										<div class="alert alert-danger">
											<?= $shipping_not_allow_error_message ?>
										</div>
									<?php } ?>
									<div class="cart-loader"></div>
									<div class="cart-body">
										<?php if (!$allow_shipping && !$is_logged) { ?>
											

											<div class="row">
												<div class="col-6">
													<div class="form-group">
														<label><?php echo __('store.enter_your_first_name')?></label>
														<input type="text" placeholder="<?php echo __('store.enter_your_first_name')?>" name="firstname" class="form-control" type="text" value="" required="">
													</div>
												</div>
												<div class="col-6">
													<div class="form-group">
														<label><?php echo __('store.enter_your_last_name')?></label>
														<input type="text" placeholder="<?php echo __('store.enter_your_last_name')?>" name="lastname" class="form-control" type="text" value="" required="">
													</div>
												</div>
												<div class="col-6">
													<div class="form-group">
														<label><?php echo __('store.enter_your_email_address')?></label>
														<input type="text" placeholder="<?php echo __('store.enter_your_email_address')?>" name="email" class="form-control" type="text" value="" required="">	
														<input type="hidden" name="classified_checkout" value="1">	
													</div>
												</div>
												<div class="col-6">
													<div class="form-group">
														<label for=""><?php echo __('store.phone')?></label>
												<input type="hidden" id="phonenumber-input" name='PhoneNumberInput' value="" class="form-control" placeholder="<?= __('store.phone_number') ?>" />
														<div>
													<input onkeypress="return isNumberKey(event);" id="phoneguest" placeholder="<?php echo __('store.phone')?>" class="form-control" type="text" name="phone" value="">
														</div>
													</div>
												</div>
												<script type="text/javascript">
													var tel_input = intlTelInput(document.querySelector("#phoneguest"), {
													initialCountry: "auto",
													utilsScript: "<?=base_url()?>assets/plugins/tel/js/utils.js?1562189064761",
													separateDialCode:true,
													geoIpLookup: function(success, failure) {
														$.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
															var countryCode = (resp && resp.country) ? resp.country : "";
															success(countryCode);
														});
													},
												});
												</script>
											</div>
										<?php } ?>

									</div>
								</div>
								<div class="step-footer"></div>
							</div>
							<?php } ?>

							<div class="checkout-setp" <?= (!$is_logged) ? (!isset($_SESSION['guestFlow']) ? 'style="display:none;"' : "") : ""; ?>>
								<div class="step-head"><h4>PAYMENT DETAILS</h4></div>
								<div class="step-body">
									<div class="dynamic-payment"></div>
									<br>
									<?php if($allow_upload_file){ ?>
										<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/jquery.uploadPreviewer.css') ?>">
										<div class="form-group downloadable_file_div well" style="white-space: inherit;">
											<div class="file-preview-button btn btn-primary">
									            <?= __('store.order_upload_file') ?>
									            <input type="file" class="downloadable_file_input" multiple="">
									        </div>
									        <div id="priview-table" class="table-responsive" style="display: none;">
									            <table class="table table-hover">
									                <tbody></tbody>
									            </table>
									        </div>
									    </div>
									<?php } ?>
									<div class="checkbox">
										<label>
											<input type="checkbox" value="1" name="agree">
											<?= __('store.agree_text') ?>
										</label>
									</div>
									<br><div class="warning-div"></div>
								</div>
								<div class="step-footer">
									<button class="btn btn-info confirm-order"><?= __('store.confirm_and_pay') ?></button>
								</div>
							</div>
						</div>
						<div class="confirm-checkout">
							<div class="checkout-setp confirm-step">
								<div class="step-head">
									<h4><?= __('store.confirm_order') ?></h4>
								</div>
								<div class="step-body">
									<div class="">
										<div id="checkout-confirm"></div>
										
									</div>
								</div>
								<div class="step-footer"></div>
							</div>
						</div>


			        </div>
			    </div>
			</div>
		</div>
	</div>

<?php  
$storelogowidth=110;
$storelogoheight=36;

  if($store_setting['store_custom_logo_size']==1)
  {
    $storelogowidth=$store_setting['store_logo_custom_width'];
    $storelogoheight=$store_setting['store_logo_custom_height'];
  }
$logo = ($store_setting['logo']) ? base_url('assets/images/site/'.$store_setting['logo']) : base_url('assets/store/default/').'img/logo.png'; 
?>
 <footer class="text-white">
      <div class="container">
        <div class="row">
          <div class="col-12 col-md-3">
            <div class="ft-left">
              <div class="logo-ft">
                <a href="<?php echo base_url("store/") ?>">
                    <img alt="<?= __('store.image') ?>" src="<?= $logo ?>" width="<?php echo $storelogowidth; ?>" height="<?php echo $storelogoheight; ?>"/>
                </a>
              </div>
              <div class="ft-social">
                <div class="row" style="max-width:140px; margin-top:10px;">
                  <?php
                  $social_links = json_decode($store_setting['social_links']);
                  foreach($social_links as $link){
                  $social_links_available = true;

                  $icon = (!empty($link->image)) ? base_url('assets/images/site/'.$link->image) : base_url('assets/store/default/img/wf.png');
                  ?>
                  <div class="col-3 py-1" style="padding-left:10px !important; padding-right:10px !important;"><a href="<?= $link->url; ?>"><img alt="<?= __('store.image') ?>" src="<?= $icon; ?>" height="13px"/></a></div>
                  <?php 
                  }
                  if(!isset($social_links_available)) {
                  ?>
                  <div class="col-3 py-1"><a href="#"><img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/facebook-logo.png"/></a></div>
                  <div class="col-3 py-1"><a href="#"><img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/twitt.png" /></a></div>
                  <div class="col-3 py-1"><a href="#"><img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/youtube.png" /></a></div>
                  <div class="col-3 py-1"><a href="#"><img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/wf.png" /></a></div>
                  <?php
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-9">
            <div class="row">
              <div class="col-6 col-md-2">
                <div class="ft-col">
                  <h3><?= __('store.contact_us') ?></h3>
                  <ul>
                    <li><a href="#"><img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/phone-call.png" /> <?= !empty($store_setting['contact_number']) ? $store_setting['contact_number'] : '+90 555 555 5555';?></a></li>
                    <li><a href="#"><img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/pin.png" /><?= !empty($store_setting['address']) ? $store_setting['address'] : '+90 555 555 5555';?></a></li>
                    <li><a href="#"><img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/email.png" /> <?= !empty($store_setting['email']) ? $store_setting['email'] : '+90 555 555 5555';?></a></li>
                  </ul>
                </div>
              </div>
              <?php
              $footer_menu = json_decode($store_setting['footer_menu']);
              foreach($footer_menu as $fm){
                $footer_menu_are_available = true;
                $letpreIndex = $fm->index - 1;
                ?>
                <div class="col-6 col-md-2">
                  <div class="ft-col">
                    <h3><?= $fm->title; ?></h3>
                    <ul>
                    <?php
                        for ($i=0; $i < sizeOf($fm->links); $i++) { 
                          $text .= ($i == 0) ? $fm->links[$i]->title : ", ".$fm->links[$i]->title;
                          ?>
                          <li><a href="<?= $fm->links[$i]->url; ?>" class="nav-link"><?= $fm->links[$i]->title; ?></a></li>
                          <?php
                        }
                      ?>
                    </ul>
                  </div>
                </div>
                <?php 
              }

              if(!isset($footer_menu_are_available)) {
                ?>
                <div class="col-6 col-md-2">
                <div class="ft-col">
                  <h3><?= __('store.policies_info') ?></h3>
                  <ul>
                    <li><a href="#">Lorem Ipsum</a></li>
                    <li><a href="#">Lorem Ipsum</a></li>
                    <li><a href="#">Lorem Ipsum</a></li>
                  </ul>
                </div>
              </div>

              <div class="col-6 col-md-2">
                <div class="ft-col">
                  <h3><?= __('store.quick_link') ?></h3>
                  <ul>
                    <li><a href="#">Lorem Ipsum</a></li>
                    <li><a href="#">Lorem Ipsum</a></li>
                    <li><a href="#">Lorem Ipsum</a></li>
                  </ul>
                </div>
              </div>

              <div class="col-6 col-md-2">
                <div class="ft-col">
                  <h3><?= __('store.my_account') ?></h3>
                  <ul>
                    <li><a href="#">Lorem Ipsum</a></li>
                    <li><a href="#">Lorem Ipsum</a></li>
                    <li><a href="#">Lorem Ipsum</a></li>
                  </ul>
                </div>
              </div>
              <div class="col-6 col-md-2">
                <div class="ft-col">
                  <h3>Lorem Ipsum</h3>
                  <ul>
                    <li><a href="#">Lorem Ipsum</a></li>
                    <li><a href="#">Lorem Ipsum</a></li>
                    <li><a href="#">Lorem Ipsum</a></li>
                  </ul>
                </div>
              </div>
              <div class="col-6 col-md-2">
                <div class="ft-col">
                  <h3>Lorem Ipsum</h3>
                  <ul>
                    <li><a href="#">Lorem Ipsum</a></li>
                    <li><a href="#">Lorem Ipsum</a></li>
                    <li><a href="#">Lorem Ipsum</a></li>
                  </ul>
                </div>
              </div> <?php
              }
              ?>
            </div>
          </div>
        </div>
      </div>
    </footer>

    <div class="footer-bottom">
      <div class="container">
        <div class="footer-row">
          <p><?= ($settings['footer'] != '') ? $settings['footer'] : __('store.all_rights_reserved')." ".date('Y')."."?> <a href="<?php echo $base_url ?>policy" class="text-light"><?= __('store.policy') ?></a></p>
          <ul class="pg-listing">
            <?php 
            $payments = get_payment_gateways();
            foreach ($payments as $key => $payment) {
                if($payment['status']){
                    echo '<li><a href="javaScript:void(0);"><img alt="'. $payment['title'] .'" src="'. base_url($payment['icon']) .'" width="68" height="32"/></a></li>';
                }
            }
            ?>
          </ul>
        </div>
      </div>
    </div>
<?php
  $db =& get_instance();
  $products = $db->Product_model;
?>

<script type="text/javascript">
	var isGuest = '<?=isset($_SESSION["guestFlow"])?>';
	var allow_shipping = '<?=$allow_shipping?>';


		$(".cart-step").delegate(".btn-remove-cart","click",function(){
			$this = $(this);
			$.ajax({
				url:$this.attr("data-href"),
				type:'POST',
				dataType:'json',
				beforeSend:function(){},
				complete:function(){},
				success:function(json){
					getCart($('select[name="country"]').val());			
				},
			})
			return false;
		});
		var xhr;
		$(".cart-step").delegate(".qty-input","change",function(){
			if(xhr && xhr.readyState != 4) xhr.abort();
			$this = $(this);
			xhr = $.ajax({
				url:'<?= $cart_update_url ?>',
				type:'POST',
				dataType:'json',
				data:$("#checkout-cart-form").serialize(),
				beforeSend:function(){},
				complete:function(){},
				success:function(json){
					getCart($('select[name="country"]').val());
				},
			})
			return false;
		})
		$('[name="payment_gateway"]').on('change',function(){
			if($(this).val() == 'bank_transfer'){
				$('.bank-transfer-instruction').slideDown();
			}else{
				$('.bank-transfer-instruction').slideUp();
			}
		});
		$(".cart-step").delegate(".submit-coupon","click",function(){
			$this = $(this);
			$('.error-coupon-msg').text('');
			$.ajax({
				url:'<?= base_url('form/add_coupon') ?>',
				type:'POST',
				dataType:'json',
				data:{
					coupon_code : $('.coupon_code').val()
				},
				beforeSend:function(){$this.btn("loading");},
				complete:function(){$this.btn("reset");},
				success:function(json){
					if(json.error){
						$('.error-coupon-msg').text(json.error);
						return false;
					}else{
						getCart($('select[name="country"]').val());
					}
				},
			})
			return false;
		})
		function getCart(countryId= null) {
			if(countryId != null) {
				$(".cart-step .cart-body").load('<?= base_url('form/checkout_cart') ?>/'+countryId);
			} else {
				$(".cart-step .cart-body").load('<?= base_url('form/checkout_cart') ?>');
			}
		}


		function getShipping(countryCode = null) {
			if(countryCode != null) {
				$(".shipping-step .cart-body").load('<?= base_url('form/checkout_shipping') ?>/'+countryCode);
			} else {
				$(".shipping-step .cart-body").load('<?= base_url('form/checkout_shipping') ?>');
			}
		}

		function getConfirm() {
			$("#checkout-confirm").load('<?= base_url('form/checkout_confirm') ?>');
		}
		var formDataFilter = function(formData) {
            if (!(window.FormData && formData instanceof window.FormData)) return formData
            if (!formData.keys) return formData
            var newFormData = new window.FormData()
            Array.from(formData.entries()).forEach(function(entry) {
                var value = entry[1]
                if (value instanceof window.File && value.name === '' && value.size === 0) {
                    newFormData.append(entry[0], new window.Blob([]), '')
                } else {
                    newFormData.append(entry[0], value)
                }
            })
            
            return newFormData
        }
        function getPaymentMethods(){
			$.ajax({
				url:'<?= base_url('store/get_payment_mothods') ?>',
				type:'POST',
				dataType:'json',
				data:{
					data:$("#checkout-cart-form").serialize(),
				},
				beforeSend:function(){},
				complete:function(){},
				success:function(json){
					$(".dynamic-payment").html(json['html']);
				},
			})
		}

		<?php if(!$allow_shipping){ ?>
			getCart();
		<?php } ?>
		if(allow_shipping)
			getShipping();
		
		getPaymentMethods();

		function backCheckout(){
			$("#checkout-confirm").html('');
			$(".confirm-checkout").hide();
			$(".non-confirm").show();
		}
		
		$(".confirm-order").on('click',function(){

			let phoneNumberInp = null;

			if($("#phone").length) {
				phoneNumberInp = $("#phone");
			} else if($("#phoneguest").length) {
				phoneNumberInp = $("#phoneguest");
			}

			if(phoneNumberInp !== null) {
			var errorMap = ['<?= __('store.invalid_number') ?>','<?= __('store.invalid_country_code') ?>','<?= __('store.too_short') ?>','<?= __('store.too_long') ?>','<?= __('store.invalid_number') ?>'];

			var is_valid = false;
			
			var errorInnerHTML = '';

			if (phoneNumberInp.val().trim()) {
				if (tel_input.isValidNumber()) {
						is_valid = true;
						tel_input.setNumber(phoneNumberInp.val().trim());
						$("#phonenumber-input").val("+"+tel_input.getSelectedCountryData().dialCode +' '+ phoneNumberInp.val().trim());
					} else {
						var errorCode = tel_input.getValidationError();
						errorInnerHTML = errorMap[errorCode];
					}
				} else {
					errorInnerHTML = '<?= __('store.mobile_number_is_required') ?>';
				}

				$(".text-danger").remove();

				if(!is_valid){
					phoneNumberInp.parents(".form-group").addClass("has-error");
					phoneNumberInp.parents(".form-group").find('> div').after("<span class='text-danger'>"+ errorInnerHTML +"</span>");

					return false;
				}
			}
			$this = $(this);
			$container = $(".checkout-setp");

			var formData = new FormData();
			$container.find("input[type=text],input[type=hidden],input[type=file],select,input[type=checkbox]:checked,input[type=radio]:checked,textarea").each(function(i,j){
				
				formData.append($(j).attr("name"),$(j).val());	
			})

			formData.append('is_form',1);	
			if(typeof fileArray != 'undefined'){
				$.each(fileArray, function(i,j){ formData.append("downloadable_file[]", j.rawData); });
			}
			
			formData = formDataFilter(formData);

			$.ajax({
				url:'<?= base_url("store/confirm_order") ?>',
				type:'POST',
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
	                        console.log( 'Uploaded percent', percentComplete );
	                        $('.loading-submit').text(percentComplete + "% Loading");
	                    }
	                }, false );

	                jqXHR.addEventListener( "progress", function ( evt ){
	                    if ( evt.lengthComputable ){
	                        var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
	                        $('.loading-submit').text("Save");
	                    }
	                }, false );
	                return jqXHR;
	            },
				beforeSend:function(){$this.btn("loading");},
				complete:function(){$this.btn("reset");},
				success:function(result){
					$container.find(".has-error").removeClass("has-error");
					$container.find("span.text-danger,.alert-danger").remove();
					$('.loading-submit').hide();
                    if(IsJsonString(result)){
                    	var result = $.parseJSON(result);
    					if(result['success']){}
    					if(result['confirm']){
    						$("#checkout-confirm").html(result['confirm']);
    						$(".confirm-checkout").show();
    						$(".non-confirm").hide();
    					}
    					if(result.error){
    						$(".warning-div").html('<div class="alert alert-danger">'+ result['error'] +'</div>');
    					}
    					if(result['errors']){
    					    $.each(result['errors'], function(i,j){
    					        $ele = $container.find('[name="'+ i +'"]');
    					        if($ele){
    					            $ele.parents(".form-group").addClass("has-error");
    					            $ele.after("<span class='text-danger'>"+ j +"</span>");
    					        }
    					    })
    					}
    				} else {
    				    $("#checkout-confirm").html(result);
						$(".confirm-checkout").show();
						$(".non-confirm").hide();
    				}
				},
			})
		});


		function IsJsonString(str) {
            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }
            return true;
        }
			
		$("#login-form").on('submit',function(){
			$this = $(this);
			$.ajax({
				url:'<?= base_url("form/ajax_login") ?>',
				type:'POST',
				dataType:'json',
				data:$this.serialize(),
				beforeSend:function(){$this.find(".btn-submit").btn("loading");},
				complete:function(){$this.find(".btn-submit").btn("reset");},
				success:function(result){
					$this.find(".has-error").removeClass("has-error");
					$this.find("span.text-danger").remove();
					
					if(result['success']){
						$(".auth-step").remove();
						location.reload();
					}
					if(result['errors']){
					
					    $.each(result['errors'], function(i,j){
					        $ele = $this.find('[name="'+ i +'"]');
					        if($ele){
					            $ele.parents(".form-group").addClass("has-error");
					            $ele.after("<span class='text-danger'>"+ j +"</span>");
					        }
					    })
					}
				},
			})
			return false;
		})
		$("#register-form").on('submit',function(e){
			e.preventDefault();
			$this = $(this);

			var errorMap = ['<?= __('store.invalid_number') ?>','<?= __('store.invalid_country_code') ?>','<?= __('store.too_short') ?>','<?= __('store.too_long') ?>','<?= __('store.invalid_number') ?>'];
			var is_valid = false;
			var errorInnerHTML = '';
			
			if ($("#phoneergister").val().trim()) {
				if (tel_inputre.isValidNumber()) {
					is_valid = true;
					tel_inputre.setNumber($("#phoneergister").val().trim());
					$("#register-form").find('input[name="PhoneNumberInput"]').val("+"+tel_inputre.getSelectedCountryData().dialCode +' '+ $("#phoneergister").val().trim());
				} else {
					var errorCode = tel_inputre.getValidationError();
					errorInnerHTML = errorMap[errorCode];
				}
			} else {
				errorInnerHTML = '<?= __('store.mobile_number_is_required') ?>';
			}

			$("#phoneergister").parents(".form-group").removeClass("has-error");

			$("#register-form .text-danger").remove();

			if(! is_valid){
				$("#phoneergister").parents(".form-group").addClass("has-error");
				$("#phoneergister").parents(".form-group").find('> div').after("<span class='text-danger'>"+ errorInnerHTML +"</span>");
				return false;
			}

			$.ajax({
				url:'<?= base_url("form/ajax_register") ?>',
				type:'POST',
				dataType:'json',
				data:$this.serialize(),
				beforeSend:function(){$this.find(".btn-submit").btn("loading");},
				complete:function(){$this.find(".btn-submit").btn("reset");},
				success:function(result){
					$this.find(".has-error").removeClass("has-error");
					$this.find("span.text-danger").remove();
					if(result['success']){
						$(".auth-step").remove();
						location.reload();
					}
					
					if(result['errors']){
					    $.each(result['errors'], function(i,j){
					        $ele = $this.find('[name="'+ i +'"]');
					        if($ele){
					            $ele.parents(".form-group").addClass("has-error");
					            $ele.after("<span class='text-danger'>"+ j +"</span>");
					        }
					    })
					}
				},
			})
			return false;
		})
		$(document).delegate(".number-input div span","click",function(){
            var val = $(this).parents(".number-input").find("input").val();
            if($(this).hasClass("plus")) { val ++ }
            else { val -- }
            if(val <= 0) val = 1;
            $(this).parents(".number-input").find("input").val(val).trigger("change")
        })

		var selected_state = '<?= isset($shipping) ? $shipping->state_id : '' ?>';
		
		$(document).delegate('[name="country"]',"change",function(){
			$this = $(this);
			let countryCode = $(this).val();	
			if(isGuest)
				renderStateAndCart(countryCode);
			else	
				getShipping(countryCode);
		})

		function renderStateAndCart(countryId) {
			$.ajax({
				url:'<?= base_url('form/getState') ?>',
				type:'POST',
				dataType:'json',
				data:{id:countryId},
				success:function(json){
					var html = '<option value="">Select State</option>';
					$.each(json['states'], function(i,j){
						var s = '';
						if(selected_state && selected_state == j['id']){
							s = 'selected';
						}
						html += "<option "+ s +" value='"+ j['id'] +"'>"+ j['name'] +"</option>";
					})
					$('[name="state"]').html(html);
					getCart(countryId);
				},
			})
		}

		var iframes = $('#body-checkout .dynamic-content-body').find('iframe');
		$.each(iframes, function(i,v){
		   $(v).before($('<div class="videoWrapper">'+ v.outerHTML +'</div>'));
		$(v).remove();
		})
	</script>
	<script src='https://www.google.com/recaptcha/api.js'></script>
	<script src="<?php echo base_url('assets/plugins/store/') ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
	
	
	<script>
    <?php
        if(isset($_SESSION['setLocalStorageAffiliateAjax'])) {
            $setLocalStorageAffiliateAjax = json_decode($_SESSION['setLocalStorageAffiliateAjax']);
            $_SESSION['localStorageAffiliate'] = (int) $setLocalStorageAffiliateAjax[0];
            ?>
            var setLocalStorageAffiliateAjax = <?= $_SESSION['setLocalStorageAffiliateAjax'] ?>;
            setWithExpiry("affiliate_id", setLocalStorageAffiliateAjax[0], setLocalStorageAffiliateAjax[1]);
            <?php
            
            unset($_SESSION['setLocalStorageAffiliateAjax']);
        }
    ?>

    function setWithExpiry(key, value, ttl) {
    	const now = new Date()
    	const item = {
    		value: value,
    		expiry: now.getTime() + ttl,
    	}
    	localStorage.setItem(key, JSON.stringify(item))
    }
    
    function getWithExpiry(key) {
    	const itemStr = localStorage.getItem(key)
    
    	if (!itemStr) {
    		return 1
    	}
    
    	const item = JSON.parse(itemStr)
    	const now = new Date()
    
    	if (now.getTime() > item.expiry) {
    		localStorage.removeItem(key)
    		return 1
    	}
    	return item.value
    }

    $("#btnGuestcontinues").click(function(){
		$.ajax({
			url:'<?= base_url() ?>store/guestCheckout',
			type:'POST',
			dataType:'json',
			success:function(result){
				if(result.status) {
					$(".checkout-form").show();
					$(".checkout-payments").show();
					$(".auth-step").hide();
					window.location.reload();
				}
			},
		})
	})
</script>
<?php
include __DIR__ . "/cookies_consent.php";
?>
	</body>
</html>

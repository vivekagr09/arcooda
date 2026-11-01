<?php
  $db =& get_instance();
  $products = $db->Product_model;
  $cart_store_side_font = $products->getSettings('site','cart_store_side_font');
  $cookies_consent = $products->getSettings('site','cookies_consent');
  $cookies_consent_mesag = $products->getSettings('site', 'cookies_consent_mesag');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    
    <meta name="author" content=""/>
    
    <meta property='og:url' content='<?= $_SERVER['REQUEST_URI']; ?>'/>
    <?php if(isset($meta_title)){ ?> <meta property="og:title" content="<?php echo $meta_title ?>"/><?php } ?>
    <?php if(isset($meta_description)){ ?> 
      <meta name="description" content="<?php echo $meta_description ?>"/>
      <meta property="og:description" content="<?php echo $meta_description ?>"/>
    <?php } ?>
    <?php if(isset($meta_image)){ ?> <meta property="og:image" content="<?php echo $meta_image ?>"/><?php } ?>
    <?php 
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    ?>
    <meta property="og:url" content="<?= $actual_link ?>"/>
    <meta name="twitter:card" content="summary_large_image"/>

    <?php if($store_setting['favicon']){ ?>
        <link rel="icon" href="<?= base_url('assets/images/site/'.$store_setting['favicon']) ?>" type="image/*" sizes="16x16">
    <?php } ?>

    <title><?= $store_setting['name'] ?>  <?= isset($meta_title) ? '- ' . $meta_title : '' ?></title>

    <!--  CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/store/default/'); ?>css/bootstrap.min.css?v=<?= av() ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/store/default/'); ?>fonts/fonts.css?v=<?= av() ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/store/default/'); ?>css/placeholder-loading.css?v=<?= av() ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/store/default/'); ?>css/sweetalert2.min.css?v=<?= av() ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/store/default/'); ?>css/nouislider.css?v=<?= av() ?>" />
    
    <link rel="stylesheet" href="<?= base_url('assets/store/default/'); ?>css/style.css?v=<?= av() ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/store/default/'); ?>css/thankyou.css" />
    <link rel="stylesheet" href="<?= base_url('assets/store/default/'); ?>css/responsive.css?v=<?= av() ?>" />

    <link href="<?= base_url('assets/template/css/bootstrap-icons.css') ?>" rel="stylesheet" />

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/store/default/fontawesome/css/all.min.css')?>" />
    <script src="<?= base_url('assets/store/default/'); ?>js/jquery-3.5.1.slim.min.js"></script>
    <script src="<?= base_url('assets/store/default/'); ?>js/jquery.min.js"></script>
    <script src="<?= base_url('assets/store/default/'); ?>js/bootstrap.min.js"></script>
    <script src="<?= base_url('assets/plugins/store/') ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/plugins/store/') ?>jquery.star-rating-svg.js"></script>
    <script src="<?= base_url('assets/store/default/') ?>js/nouislider.min.js"></script>
    <script src="<?= base_url('assets/store/default/') ?>js/sweetalert2.all.min.js"></script>
    <script src="<?= base_url('assets/plugins/') ?>mustache.js"></script>

    <script type="text/javascript">
      try {
          <?php 
          if($store_setting['google_analytics'] != ''){
            $ana = preg_replace('/<script>/', '', $store_setting['google_analytics']);
            $ana = preg_replace('/<\/script>/', '', $ana);
            echo $ana;
          } 
          ?>
      } catch (error) {
        console.log(error);
      }
    </script>

    
    <?php 
    
    if(isset($store_setting['per_task']) && !empty($store_setting['per_task'])){
      $per_tasks = json_decode($store_setting['per_task'], true);
      if(!empty($per_tasks)){
        ?><script type="text/javascript"><?php
        foreach ($per_tasks as $per_task){
          $per_task_new = preg_replace('/<script>/', '', $per_task);
          $per_task_new = preg_replace('/<\/script>/', '', $per_task_new);
          ?>
          try {
            <?php  echo $per_task_new; ?>
          } catch (error) {
            console.log(error);
          }
      <?php }
      ?> 
      </script><?php
      }
    } 
    ?>
   

    <?php 
    $global_script_status = (array)json_decode($SiteSetting['global_script_status'],1);
    if(in_array('store', $global_script_status)){
        echo $SiteSetting['global_script'];
    }
    ?>

    <script type="text/javascript">
        (function ($) {
            $.fn.btn = function (action) {
                var self = $(this);
                if (action == 'loading') {
                    if ($(self).attr("disabled") == "disabled") {
                    }
                    $(self).attr("disabled", "disabled");
                    $(self).attr('data-btn-text', $(self).html());
                    $(self).html('<div class="spinner-border spinner-border-sm"></div>&nbsp;' + $(self).text());
                }
                if (action == 'reset') {
                    $(self).html($(self).attr('data-btn-text'));
                    $(self).removeAttr("disabled");
                }
            }
        })(jQuery);
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
            });
            return newFormData;
        }
    </script>

    <?php if (is_rtl()) { ?>
      <!-- place here your RTL css code -->
      <link rel="stylesheet" href="<?= base_url('assets/store/default/'); ?>css/rtl.css?v=<?= av() ?>" />
    <?php } ?>

    <style type="text/css">
      {
        font-family: <?= $cart_store_side_font['cart_store_side_font'] ?>;
      }
      .banner-caption-inner p, .category-listing li a, .section-title, .price, .blog-more, .blog-less, .blog-para, .footer-row p, .regular-price, .sale-price, .btn.btn-cart-detail.bg-main2, .apply-coupon input, .description-reviews-tabs a, .description-content p{
        font-family: <?= $cart_store_side_font['cart_store_side_font'] ?>;
      }
      h1, h2, h3, h4, h5, h6, label, span, p {
        font-family: <?= $cart_store_side_font['cart_store_side_font'] ?> !important;
      }
    </style>
</head>

<body style="font-family: <?= $cart_store_side_font['cart_store_side_font'] ?> !important;">
    <?php 
    $fbmessager_status = (array)json_decode($SiteSetting['fbmessager_status'],1);
    if(in_array('store', $fbmessager_status)){
        echo $SiteSetting['fbmessager_script'];
    }
    ?>
          
    <?php
    if(isset($store_setting['notification']) && sizeOf(json_decode($store_setting['notification'])) > 0 && !empty(json_decode($store_setting['notification'])[0])) { 
    ?>
<!-- Top notification bar -->
<div class="top-bar bg-main text-white text-center">
  <div class="container">
    <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/top-icon.png" /> <?= json_decode($store_setting['notification'])[0]; ?>
  </div>
</div>
<?php
} else {
?>
<!-- Dummy Top notification bar -->
<div class="top-bar bg-main text-white text-center">
  <div class="container">
    <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/top-icon.png" /> Lorem Ipsum is simply dummy text of the printing and typesetting industry.
  </div>
</div>

    <?php
    }
    $storelogoheight=36;
    $storelogowidthstr='';
      if($store_setting['store_custom_logo_size']!=0)
      {
        $storelogowidth=$store_setting['store_logo_custom_width'];
        $storelogoheight=$store_setting['store_logo_custom_height'];
        $storelogowidthstr= 'width="'.$storelogowidth.'"'; 
      }
    ?>
    <!-- Header  -->
  <div class="headerbar"></div>
    <header id="myHeader">
        <div class="container-fluid">
          <!--nav bar start here-->
            <nav class="navbar navbar-expand-lg">
                <?php  $logo = ($store_setting['logo']) ? base_url('assets/images/site/'.$store_setting['logo']) : base_url('assets/store/default/').'img/logo.png'; ?>
                <a class="navbar-brand" href="<?= $home_link ?>"><img alt="<?= __('store.image') ?>" src="<?= $logo ?>" onerror="this.src='<?=base_url('assets/store/default/').'img/logo.png'?>';"  height="<?php echo $storelogoheight;?>" <?php echo $storelogowidthstr;  ?> /></a>
                
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><img src="<?= base_url('assets/store/default/'); ?>img/menu.png" class="img-toggler" alt="<?= __('store.menu') ?>"></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item <?= ($page == 'home') ? 'active' : ''; ?>"><a href="<?= $home_link ?>" class="nav-link"><?= __('store.products') ?></a></li>
                        
                        <li id="dropdownMenu2" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle nav-item <?= ($page == 'product'||$page == 'product_list'|| $page == 'category') ? 'active' : ''; ?>"><a href="<?= $base_url ?>category" class="nav-link"><?= __('store.categories') ?></a>

                        <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
                          <?php
                          function display_with_children_maincategory($parentRow, $level = 0) { 
                              $space = $level > 0 ? str_repeat("", $level).' ' : '';
                              foreach ($parentRow as $key => $row) {
                                  echo '<li data-id="'. $row['id'] .'" class="'. ($row['children'] ? 'has-children' : '') .'" ><span>'. $space .'<a href="'. base_url('store/category/'. $row['slug']) .'">'. $row['name']."</a></span>".($row['children'] ? "" : ""); 
                                  if ($row['children']) {
                                      echo '<ul>';display_with_children_maincategory($row['children'], $level + 1);echo '</ul>';
                                  }
                                  echo '</li>';
                              }
                            }
                            echo '<li data-id="0" ><span><a href="'. base_url('store/category/') .'">'.__('store.all_categories').'</a></span>'; 
                            display_with_children_maincategory($category_tree, 0);
                            ?>
                        </ul>
                      </li>

                        <li class="nav-item <?= ($page == 'about') ? 'active' : ''; ?>"><a href="<?= $base_url ?>about" class="nav-link"><?= __('store.about') ?></a></li>
                        <li class="nav-item <?= ($page == 'contact') ? 'active' : ''; ?>"><a href="<?= $base_url ?>contact" class="nav-link"><?= __('store.contact') ?></a></li>
                    </ul>

                    <div class="header-right-listing">
                        <ul class="d-flex">
                            <li id="store_currency_menu" class="dropdown"><?= $CurrencyHtml ?></li>
                            <li id="store_lang_menu" class="dropdown"><?= $LanguageHtml ?></li>

                            <?php if($is_logged){ ?>
                              <div class="dropdown">
                                <?php 
                                $avatar = $client['avatar'] != '' ? base_url('assets/images/users/'. $client['avatar']) : base_url('assets/store/default/img/blog1.png');
                                ?>
                                <a href="javascript::void(0)" class="js-link2">
                                  <img alt="<?= __('store.image') ?>" src="<?= $avatar; ?>" class="mr-1" width="24" height="24"/>
                                </a>
                                <ul class="js-dropdown-list2">
                                  <li class="d-flex"><a class="text-dark" href="<?php echo $base_url ?>profile"><i class="fa fa-user"></i> &nbsp;&nbsp;<?= __('store.profile') ?></a></li>
                                  <li class="d-flex"><a class="text-dark" href="<?php echo $base_url ?>order"><i class="fa fa-gift"></i> &nbsp;&nbsp;<?= __('store.order') ?></a></li>
                                  <li class="d-flex"><a class="text-dark" href="<?php echo $base_url ?>shipping"><i class="fa fa-truck"></i> &nbsp;&nbsp;<?= __('store.shipping') ?></a></li>
                                  <li class="d-flex"><a class="text-dark" href="<?php echo $base_url ?>wishlist"><i class="fa fa-heart  "></i> &nbsp;&nbsp;<?= __('store.wishlist') ?></a></li>
                                  <li class="d-flex"><a class="text-dark" href="<?php echo $base_url ?>logout"><i class="fa fa-power-off"></i> &nbsp;&nbsp;<?= __('store.logout') ?></a></li>
                                </ul>
                              </div>
                            </li>
                            <?php } else { ?>
                              <li><a href="<?php echo $base_url ?>login" class="top-login-btn btn bg-main2 text-white d-flex align-items-center"><img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/signin.png" class="mr-2" /><?= __('store.login') ?></a></li>
                            <?php } ?>
                            

                            <li class="d-flex align-items-center position-relative cart-top">
                                <img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/cart.png" /><span><?= __('store.my_cart') ?> <small id="cart-sub-total" class="d-block"></small></span>
                                <span class="cart-count position-absolute">0</span>
                                <div class="cart-dropdown">
                                    <div class="cart-empty">
                                        <img src="<?= base_url('assets/store/default/'); ?>img/cart-icon-empty.png" alt="<?= __('store.icon') ?>">
                                        <p><?= __('store.cart_is_blank') ?></p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
        <!--nav bar end here-->
    </header>

  <div class="page-wrapper">
      <?php echo $content; ?>
  </div>
  
    <?php 
    $storelogowidth='';
    $storelogoheight=36;

      if($store_setting['store_custom_logo_size']==1)
      {
        $storelogowidth=$store_setting['store_logo_custom_width'];
        $storelogoheight=$store_setting['store_logo_custom_height'];
      }
    ?>
<!-- Footer Code -->
<footer class="text-white bg-dark py-4">
    <div class="container">
        <div class="row">
            <!-- Left Section -->
            <div class="col-12 col-md-3">
                <div class="ft-left mb-4">
                    <!-- Logo -->
                    <div class="logo-ft mb-3">
                        <a href="<?= $home_link ?>">
                            <img src="<?= $logo ?>" alt="<?= __('store.image') ?>" width="<?= $storelogowidth ?>" height="<?= $storelogoheight ?>">
                        </a>
                    </div>
                    <!-- Social Media Icons -->
                    <div class="ft-social">
                        <div class="row gx-2">
                            <?php
                            $social_links = json_decode($store_setting['social_links'], true);
                            foreach ($social_links as $link) {
                            ?>
                                <div class="col-3">
                                    <a href="<?= $link['url']; ?>">
                                        <img src="<?= !empty($link['image']) ? base_url('assets/images/site/'.$link['image']) : base_url('assets/store/default/img/wf.png') ?>" alt="<?= __('store.image') ?>" height="13">
                                    </a>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Right Section -->
            <div class="col-12 col-md-9">
                <div class="row">
                    <!-- Contact Us -->
                    <div class="col-6 col-md-3">
                        <div class="ft-col mb-4">
                            <h5 class="mb-3"><?= __('store.contact_us') ?></h5>
                            <ul class="list-unstyled">
                                <li><a href="#"><img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/phone-call.png" /> <?= !empty($store_setting['contact_number']) ? $store_setting['contact_number'] : '+90 555 555 5555';?></a></li>
                                <li><a href="#"><img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/pin.png" /> <?= !empty($store_setting['address']) ? $store_setting['address'] : 'Default Address'; ?></a></li>
                                <li><a href="#"><img alt="<?= __('store.image') ?>" src="<?= base_url('assets/store/default/'); ?>img/email.png" /> <?= !empty($store_setting['email']) ? $store_setting['email'] : 'Default Email'; ?></a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Dynamic Footer Menu -->
                    <?php
                    $footer_menu = json_decode($store_setting['footer_menu'], true);
                    foreach ($footer_menu as $fm) {
                    ?>
                        <div class="col-6 col-md-3">
                            <div class="ft-col mb-4">
                                <h5 class="mb-3"><?= $fm['title']; ?></h5>
                                <ul class="list-unstyled">
                                    <?php foreach ($fm['links'] as $link) { ?>
                                        <li><a href="<?= $link['url']; ?>" class="text-white"><?= $link['title']; ?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- End of Footer -->


    <!-- flash message -->
    <div class="print-message"><?php print_message($this); ?></div>
    <!-- flash message -->

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
    <div style="display:none;">
        <a href="<?= base_url() ?>"><?= __('store.affiliate_pro') ?></a>
    </div>

<div class="modal fade" id="cart-confirm" tabindex="-1" aria-labelledby="cart-confirm" aria-hidden="true">
  <div class="modal-dialog">
  <div class="modal-content">
    <div class="popup-content">
    <img src="<?= base_url('assets/store/default/'); ?>img/shopping-cart.png" class="pop-cart-img" alt="<?= __('store.icon') ?>">
    <h2 id="product-name-prev"></h2>
    <p><?= __('store.has_beent_added_to_your_cart') ?></p>
    <img src="<?= base_url('assets/store/default/'); ?>img/popline.png" class="img-fluid img-popline" alt="<?= __('store.icon') ?>">
    <div class="pop-btn-row">
      <a href="<?= $base_url ?>checkout" class="btn btn-poup bg-main2"><?= __('store.procceed_to_checkout') ?></a>
      <a href="javascript:void(0);" type="button" class="btn btn-poup bg-main" data-bs-dismiss="modal">
        <?= __('store.continue_shopping') ?>
      </a>
    </div>
    </div>
  </div>
  </div>
</div>

<?php if(!empty($cookies_consent) && $cookies_consent['cookies_consent'] == 1){ ?>
<script>
  document.addEventListener("DOMContentLoaded", function () {
        // Check if the user has already given consent
        if (!localStorage.getItem("cookieConsent")) {
          // If not, show the popup
          document.getElementById("cookie-consent-popup").style.display = "flex";
        }
        // When the user clicks "Accept"
        document.getElementById("cookie-consent-accept").addEventListener("click", function () {
          localStorage.setItem("cookieConsent", "accepted");
           $("#cookie-consent-popup").remove();
          
        });
        // When the user clicks "Decline"
        document.getElementById("cookie-consent-decline").addEventListener("click", function () {
          localStorage.setItem("cookieConsent", "declined");
          $("#cookie-consent-popup").remove();
        });
      });
</script>
<?php }?>

<script type="text/javascript">
  /* flash message auto remove script */
  window.setTimeout(function() {
    $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 4000);
/* flash message auto remove script */
</script>

<script type="text/javascript">
    $('.btn-cart').tooltip({
      trigger: 'click',
      placement: 'top'
    });

    function setTooltip(message) {
      $('.btn-cart').tooltip('hide').attr('data-original-title', message).tooltip('show');
    }

    function hideTooltip() {
      $('.btn-cart').tooltip('hide');
    }


  $(function(){ 
    $(document).on('click', ".btn-cart", function(){
      let quantity = ($('input#product-quantity').length) ? $('input#product-quantity').val() : 1;
      let product_name = $(this).data('product_name');
      let product_id = $(this).data('product_id');
      $this = $(this);

      let variationNotSelected = [];
      let variationSelected = {};

      if($('.variation-row .variations').length != 0) {
        $('.variation-row .variations').each(function(){
          let type = $(this).find('span:first-child').data('variation-type');
          let optionSpan = $(this).find('.active');
          if(optionSpan.length) {
            variationSelected['price'] = optionSpan.data('variation-price');
            if(type == 'colors') {
              variationSelected[type] = optionSpan.data('variation-code')+"-"+optionSpan.data('variation-name');
            } else {
              variationSelected[type] = optionSpan.data('variation-option');
            }
          } else {
            variationNotSelected.push(type);
          }
        });
      }

      if(variationNotSelected.length){
        let warningMessage = '<?= __('store.please_select') ?>' + ' ';
        for (let index = 0; index < variationNotSelected.length; index++) {
          const element = variationNotSelected[index];
          warningMessage += (index == 0) ? element : ", "+element
        }
        setTooltip(warningMessage+' '+'<?= __('store.before_add_to_cart') ?>');
      } else {
        $.ajax({
          url:'<?= $add_tocart_url ?>',
          type:'POST',
          dataType:'json',
          data: {
            quantity:quantity,
            product_id:product_id,
            variation:variationSelected,
          },
          beforeSend: function(){$this.btn("loading");},
          complete: function(){$this.btn("reset");},
          success: function(json) {
            if(json['location']){
              updateCart();
              $('#cart-confirm #product-name-prev').text(product_name)
              $("#cart-confirm").modal("show");
            }
          }
        });
      }
    });

    $(document).on("click", ".cart-dropdown .btn-remove-cart", function(){
      $this = $(this);
      $.ajax({
          url:$this.attr("data-href"),
          type:'POST',
          dataType:'json',
          beforeSend:function(){},
          complete:function(){},
          success:function(json){
              updateCart();              
          },
      })
      return false;
    });

    $(document).on('click', ".cart-top", function(){
      $(".js-dropdown-list").hide();
      $(".js-dropdown-list1").hide();
      $(".js-dropdown-list2").hide();
      $(".cart-dropdown").slideToggle();
    });

    updateCart();
  });

  $(function(){
    $("#login-form input, #register-form input").focus(function(){
      if($(document).width() <= 408){
        $(".navbar-expand-lg,footer").hide();
      }
    });

    $("#login-form input, #register-form input").blur(function(){
      $(".navbar-expand-lg,footer").show();
    });
  });
  
  $(function(){
    function updateSymbol(e) {
      var selected = $(".currency-selector option:selected");
      $(".currency-symbol").text(selected.data("symbol"));
      $(".currency-amount").prop("placeholder", selected.data("placeholder"));
      $(".currency-addon-fixed").text(selected.text());
    }

    $(".currency-selector").on("change", updateSymbol);

    updateSymbol(); 
  });
  
  $(function () {
    var list = $(".js-dropdown-list");
    var link = $(".js-link");
    link.click(function (e) {
      e.preventDefault();
      $(".js-dropdown-list1").hide();
      $(".js-dropdown-list2").hide();
      $(".cart-dropdown").hide();
      list.slideToggle(200);
    });
    list.find("li").click(function () {
      var text = $(this).html();
      link.html(text);
      list.slideToggle(200);
      if (text === "* Reset") {
        link.html('<?= __('store.select_one_option') ?>' + icon);
      }
    });
  });

  $(function() {
    var list = $('.js-dropdown-list1');
    var link = $('.js-link1');
    link.click(function(e) {
        e.preventDefault();
        $(".js-dropdown-list").hide();
        $(".js-dropdown-list2").hide();
        $(".cart-dropdown").hide();
        list.slideToggle(200);
    });
    list.find('li').click(function() {
        var text = $(this).html();
        link.html(text);
        list.slideToggle(200);
        if (text === '* Reset') {
        link.html('<?= __('store.select_one_option') ?>'+icon);
        }
    });
  });

  $(function () {
    var list = $(".js-dropdown-list2");
    var link = $(".js-link2");
    link.click(function (e) {
      e.preventDefault();
      $(".js-dropdown-list1").hide();
      $(".js-dropdown-list").hide();
      $(".cart-dropdown").hide();
      list.slideToggle(200);
    });
  });
    
  window.onscroll = function() {
    let header = document.getElementById("myHeader");
    let sticky = header.offsetTop;
    if (window.pageYOffset > sticky) {
        header.classList.add("sticky");
    } else {
        header.classList.remove("sticky");
    }
  }
</script>

<script type="text/javascript">
<?php 
if(isset($store_setting['notification']) && sizeOf(json_decode($store_setting['notification'])) > 0) { 
?>
  $(document).ready(function() {
    var items = <?= $store_setting['notification']; ?>,
    $text = $('.top-bar .container'),
    delay = 2;

    var filtered = items.filter(function (el) {
      return (el != null && el != ""  );
    });

    if(filtered.length > 0) {
      filtered.push(filtered.shift());
      function loop ( delay ) {
          $.each(filtered, function ( i, elm ){
            $text.delay( delay*1E3).fadeOut();
            $text.queue(function(){
                $text.html('<img alt="'+'<?= __('store.image') ?>'+'" src="<?= base_url('assets/store/default/'); ?>img/top-icon.png" /> '+filtered[i]);
                $text.dequeue();
            });
            $text.fadeIn();
            $text.queue(function(){
                if ( i == filtered.length -1 ) {
                    loop(delay);   
                }
                $text.dequeue();
            });
          });
      }
      loop(delay);
    }
  });
  <?php } ?>

    function updateCart(){
      $.ajax({
          url:'<?= $base_url ?>/mini_cart',
          type:'POST',
          dataType:'json',
          beforeSend:function(){},
          complete:function(){},
          success:function(json){
              $(".cart-top .cart-dropdown").html(json['cart']);
              $(".cart-top .cart-count").html(json['total']);
              $('#cart-sub-total').text(json['sub_total']);
          },
      });
    }
</script>


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
</script>

<?= $page_custom_script; ?>
<?php
include __DIR__ . "/cookies_consent.php";
?>
</body>
</html>
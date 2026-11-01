<!DOCTYPE html>
<?php 
  $db =& get_instance();
  $userdetails = $db->Product_model->userdetails('user', true); 
  $SiteSetting = $db->Product_model->getSiteSetting();
  $MembershipSetting =$db->Product_model->getSettings('membership');
  $user_side_font = $db->Product_model->getSettings('site','user_side_font');
  $user_button_color = $db->Product_model->getSettings('theme','user_button_color'); 
  $user_button_hover_color = $db->Product_model->getSettings('theme','user_button_hover_color');
  $loginUser = $_SESSION['user'];

  if($userdetails['reg_approved'] != 1 && !isset($notcheckapproval)){
    redirect('usercontrol/approval_status');die;
  }
  if($MembershipSetting['status']){
    $user = App\User::auth();
    if((int)$user->plan_id == 0){
      if(!isset($notcheckmember)){
        redirect('usercontrol/purchase_plan');die;
      }
    } else if($user->plan_id == -1){
    } else if($user){
      $plan = $user->plan();
      if(!$plan){
        if(!isset($notcheckmember)){
          redirect('usercontrol/purchase_plan');die;
        }
      } else if(!isset($notcheckmember) && $plan->status_id != 1){
          redirect('usercontrol/purchase_plan_expire');
      }else if($plan->isExpire() || !$plan->strToTimeRemains() > 0){
        $lifetime = ($plan->is_lifetime && $plan->status_id) ? true : false;
        if(!isset($notcheckmember) && !$lifetime){
          redirect('usercontrol/purchase_plan_expire');
          die;
        }
      }
    }
  }
  /*==============Side Bar File Code=====================*/
  $store_setting =$db->Product_model->getSettings('store');
  $refer_status =$db->Product_model->my_refer_status($userdetails['id']);
  $db->Product_model->ping($userdetails['id']);
  $vendor_setting = $db->Product_model->getSettings('vendor');
  $market_vendor = $db->Product_model->getSettings('market_vendor');
  $membership = $db->Product_model->getSettings('membership');
  $user_side_bar_color = $db->Product_model->getSettings('theme','user_side_bar_color');
  $user_side_bar_text_color = $db->Product_model->getSettings('theme','user_side_bar_text_color');
  $marketvendorpanelmode = $market_vendor['marketvendorpanelmode'] ?? 0;
  $userdashboard_settings = getUserDashboardSettings();

  if (isset($this->session) && $this->session->userdata('userLang') !== FALSE)
  $language_id=$this->session->userdata('userLang');
  $tutorials=$db->Tutorial_model->getAllRecords($language_id);
  /*==============Nav Bar File Code=====================*/
    $this->CI        =& get_instance(); 
    $method=$this->CI->router->fetch_method();
    $products        = $db->Product_model; 
    $Withdrawal      = $db->Withdrawal_payment_model; 
    $notifications       = $products->getnotificationnew('user',$userdetails['id'],5);
    $notifications_count = $products->getnotificationnew_count('user',$userdetails['id']);
    $paymentlist         = $products->getPaymentWarning();
    $LanguageHtml        = $products->getLanguageHtmlUser('usercontrol');
    $CurrencyHtml        = $products->getCurrencyHtmlUser('usercontrol');

    $PrimaryPaymentMethodStatus = $products->getUserPaymentMethodStatus($userdetails['id'],$userdetails['primary_payment_method']);
    if(empty($payment_methods) && ($method != 'purchase_plan' && $method !='user_reports' && $method !='buy_membership')){
      
      $payment_methods = $Withdrawal->getPaymentMethods(); 
    }

    $this->session->set_userdata('payment_methods',$payment_methods);
    
    $loginUser = $_SESSION['user'];
    if(isset($loginUser['is_vendor']) && $loginUser['is_vendor'] == 1) {
        $marketVendorStatus= $db->Product_model->getSettings('market_vendor', 'marketvendorstatus');
        $vendoerMinDeposit = $db->Product_model->getSettings('site', 'vendor_min_deposit');
        $userdepbal['vendor_min_deposit'] = isset($vendoerMinDeposit['vendor_min_deposit']) ? $vendoerMinDeposit['vendor_min_deposit'] : 0;

        $db->load->model('Total_model');
        $depbalence = $db->Total_model->getUserBalance($loginUser['id']);

        $userdepbal['show_deposit_warning'] = ($depbalence < $userdepbal['vendor_min_deposit']) ? 1 : 0;
        $userdepbal['vendor_min_deposit_warning'] = __('user.minimum_deposit_warning');

        $vendorDepositStatus = $this->Product_model->getSettings('vendor', 'depositstatus');
        $userdepbal['vendor_deposit_status'] = isset($vendorDepositStatus['depositstatus']) ? $vendorDepositStatus['depositstatus'] : 0;
     }

    $membership = $this->Product_model->getSettings('membership', 'status');
    $award_level = $this->Product_model->getSettings('award_level', 'status');
    $userPlan = App\MembershipUser::select('membership_plans.commission_sale_status','award_level.level_number','award_level.sale_comission_rate')->join('membership_plans','membership_plans.id','=','membership_user.plan_id')->join('award_level','award_level.id','=','membership_plans.level_id','left')->where('is_active',1)->where('user_id',$userdetails['id'])->first();
    $levels = $this->Product_model->getAll('award_level',false,0,'id desc');

    require APPPATH."config/breadcrumb.php";
      $pageKey = $db->Product_model->page_id();

      $user_side_bar_text_hover_color = $products->getSettings('theme','user_side_bar_text_hover_color');
      $user_top_bar_color = $products->getSettings('theme','user_top_bar_color');
      $user_side_bar_clock_text_color = $products->getSettings('theme','user_side_bar_clock_text_color');


?>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title><?= $SiteSetting['name'] ?> - <?= $loginUser['is_vendor']== 1 ? __('user.top_title_vendor') : __('user.top_title_affiliate') ?>
    </title>

    <meta content="<?= $SiteSetting['meta_author'] ?>" name="author" />
    <meta content="<?= $SiteSetting['meta_description'] ?>" name="description" />
    <meta content="<?= $SiteSetting['meta_keywords'] ?>" name="keywords" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!--summNote jquery files-->
    <script src="<?= base_url('assets/template/summernote/jquery-3.4.1.slim.min.js'); ?>"></script>
    <!--summNote jquery files-->

    <!-- Bootstrap 5 Css -->
    <link rel="stylesheet" href="<?= base_url('assets/template/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/template/css/bootstrap-icons.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/template/css/bootstrap-toggle.min.css') ?>">

    <!-- Custom Css -->
    <link rel="stylesheet" href="<?= base_url('assets/template/css/custom.css?v='.av()); ?>"/>
    <link rel="stylesheet" href="<?= base_url('assets/template/css/rtl.min.css?v='.av()); ?>"/>
    <link rel="stylesheet" href="<?= base_url('assets/template/css/dark.css?v='.av()); ?>"/>
    <link rel="stylesheet" href="<?= base_url('assets/template/css/all.css?v='.av()); ?>"/>
    

    <!--Plugins Css -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/magnific-popup/magnific-popup.css') ?>"/>
    <link rel="stylesheet" href="<?= base_url('assets/js/jquery-confirm.min.css') ?>"/>
    <link rel="stylesheet" href="<?= base_url('assets/css/jquery.uploadPreviewer.css') ?>"/>
    <link rel="stylesheet" href="<?= base_url('assets/js/jssocials-1.4.0/jssocials.css'); ?>"/>
    <link rel="stylesheet" href="<?= base_url('assets/js/jssocials-1.4.0/jssocials-theme-flat.css'); ?>"/>
    <link rel="stylesheet" href="<?= base_url('assets/plugins/datetimepicker/jquery.datetimepicker.min.css') ?>"/>
    <link rel="stylesheet" href="<?= base_url('assets/plugins/datatable/select2.css') ?>"/>

    <!--summNote css files-->
    <link rel="stylesheet" href="<?= base_url('assets/template/summernote/summernote-lite.min.css') ?>">
    <!--summNote css files-->

    <style>
        .sticky-footer-container {
            min-height: 100vh;
            grid-template-rows: 1fr auto;
        }
    </style>


    <?php if($SiteSetting['favicon']){ ?>
    <link rel="icon" href="<?= base_url('assets/template/images/site/'.$SiteSetting['favicon']) ?>" type="image/*" sizes="16x16">
    <?php } ?>
    

    <!--bootstrap 5 js files-->
    <script src="<?= base_url('assets/template/js/jquery-3.6.0.min.js'); ?>"></script>
    <script src="<?= base_url('assets/template/js/popper.min.js'); ?>"></script>
    <script src="<?= base_url('assets/template/js/bootstrap.min.js'); ?>"></script>
    <script src="<?= base_url('assets/template/js/jquery-migrate-3.0.0.min.js'); ?>"></script>
    <!--bootstrap 5 js files-->
    

    <?php if($SiteSetting['custom_logo_size']): ?>
      <style type="text/css">
        .customLogoClass{
            width: <?= (int) $SiteSetting['log_custom_width'] ?>px !important;
            height: <?= (int) $SiteSetting['log_custom_height'] ?>px !important;
        }
      </style>
    <?php endif ?>
    <style type="text/css">
      .nav-tabs .nav-link, .nav-pills .nav-link {
        font-family: <?= $user_side_font['user_side_font'] ?> !important;
      }
      h1, h2, h3, h4, h5, h6, th, label {
        font-family: <?= $user_side_font['user_side_font'] ?> !important;
      }
      .form-control {
        font-family: <?= $user_side_font['user_side_font'] ?> !important;
      }
      .user_button_color, .btn-primary, .btn-info, .btn-secondary {
        background-color: <?= $user_button_color['user_button_color'] ?> !important;
        border: 1px solid <?= $user_button_color['user_button_color'] ?> !important ;
      }
      .user_button_color:hover, .btn-primary:hover, .btn-info:hover, .btn-secondary:hover {
      background-color: <?= $user_button_hover_color['user_button_hover_color'] ?> !important;
        border: 1px solid <?= $user_button_hover_color['user_button_hover_color'] ?> !important ;
      }
      body { padding-right: 0 !important }
    </style>
  </head>

<body>
<?php 
  $fbmessager_status = (array)json_decode($SiteSetting['fbmessager_status'],1);
  if(in_array('affiliate', $fbmessager_status)){
    echo $SiteSetting['fbmessager_script'];
  }
?>




  <script src="<?= base_url('assets/plugins/datetimepicker/jquery.datetimepicker.full.min.js') ?>"></script>
  <script src="<?= base_url('assets/plugins/datatable/select2.min.js') ?>"></script>  
  <script src='<?= base_url('assets/sweetalert/sweetalert.min.js') ?>'></script>

    <script type="text/javascript">
        (function ($) {
            $.fn.btn = function (action) {
                var self = $(this);
                var tagName = self.prop("tagName");
                if(tagName == 'A'){
                    if (action == 'loading') {
                        $(self).attr('data-text',$(self).text());
                        $(self).text("Loading..");
                    }
                    if (action == 'reset') { $(self).text($(self).attr('data-text')); }
                }
                else {
                    if (action == 'loading') { $(self).addClass("btn-loading"); }
                    if (action == 'reset') { $(self).removeClass("btn-loading"); }
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
            })
            return newFormData
        }
    </script>

    <aside class="sidebar sidebar-default navs-rounded-all" style="background-color: <?= $user_side_bar_color['user_side_bar_color'] ?>;">
      <div class="sidebar-header d-flex align-items-center justify-content-start">
        <a href="<?= base_url('usercontrol/dashboard') ?>" class="navbar-brand">
          <!--Logo start-->
          <?php $logo = $SiteSetting['admin-side-logo'] ? base_url('assets/images/site/'. $SiteSetting['admin-side-logo'] ) : base_url('assets/template/images/user-logo.png'); ?>

          <img src="<?= $logo;?>" alt="logo" class="logo <?= ($SiteSetting['custom_logo_size']) ? 'customLogoClass' : '' ?>">
          <img src="<?= base_url('assets/images/user-logo.png');?>" alt="<?= __('user.logo') ?>" class="mini-logo">
          <!--logo End-->
        </a>
        <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
          <i class="fa-solid fa-arrow-left icon-arrow"></i>
        </div>
      </div>
      <div class="sidebar-body pt-0 data-scrollbar">
        <div class="sidebar-list">
          
          <!-- Sidebar Menu Start -->
            <ul class="navbar-nav iq-main-menu" id="sidebar-menu">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="<?= base_url('usercontrol/dashboard'); ?>">
                <i class="fa-sharp fa-solid fa-list-ul icon pt-1"></i>
                <span class="item-name"><?= __('user.page_title_dashboard') ?></span>
              </a>
            </li>
            <li>
              <hr class="hr-horizontal">
            </li>
          <!-------User tutorial------->
          <?php 
          if(isset($SiteSetting["tutorial_module_status"]) && $SiteSetting["tutorial_module_status"]==1) 
          {
            if(isset($tutorials) && is_array($tutorials) && count($tutorials)>0)
            {
              ?>
              <!--Main Category-->
              <li class="nav-item  submenu-sidebar">
                  <a class="nav-link collapsed text-truncate" href="#submenu5" data-bs-toggle="collapse"  data-bs-target="#submenu5"><i class="fa fa-table"></i> <span class="d-sm-inline" ><?= __('user.page_title_tutorial') ?></span><i class="fas fa-chevron-right right-icon"></i></a>
               
                      <ul class="flex-column pl-2 sub-nav collapse" id="submenu5" aria-expanded="false">
                          <?php
                            $tutorialCategoryId=0;
                              $pageString="";
                              $previousCateroyName="";

                              foreach($tutorials as $tutorial )
                              { 
                                if($tutorialCategoryId!=$tutorial['category_id'])
                                {
                                  if($tutorialCategoryId!="" && $tutorialCategoryId!=0)
                                  {
                                    //first category pages
                                    $pageString='<div class="collapse" id="submenu1'.$tutorialCategoryId.'" aria-expanded="false">
                                          <ul class="flex-column sub-nav pl-4">'.$pageString.'</ul>
                                        </div></li>';
                                      echo $pageString;
                                      $pageString="";
                                  }
                                ?>
                            
                          <!--sub-category-->
                          <li class="nav-item">
                              <a class="nav-link  text-truncate collapsed py-1" href="#submenu1<?=$tutorial['category_id']?>" data-bs-toggle="collapse" data-bs-target="#submenu1<?=$tutorial['category_id']?>"> <i class="fa-solid fa-circle icon"></i>
                                <i class="sidenav-mini-icon"> B </i>
                                <span class="item-name"><?=$tutorial['name'] ?></span><i class="fas fa-chevron-right right-icon"></i></a>
                              
                                <?php
                                    }
                                      //tutorial page
                                      $pageString.='<li class="nav-item">
                                                <a class="nav-link p-1 text-truncate" href="'. base_url('usercontrol/tutorial/'.$tutorial['id']).'" style="color: '. $user_side_bar_text_color['user_side_bar_text_color'] .'">
                                                  <i class="fa-solid fa-square"></i> '.$tutorial['title'].' </a>
                                                </li>';
                                    //tutorial page

                                      $tutorialCategoryId=$tutorial['category_id'];  
                                }
                                //other category pages
                                if($pageString!="")
                                echo '<div class="collapse" id="submenu1'.$tutorialCategoryId.'" aria-expanded="false">
                                    <ul class="sub-nav">'.$pageString.'</ul>
                                    </div></li>';
                                //other category pages
                                ?>
                                <!--sub-category-->  
                      </ul>
                  
              </li>
              <?php }
          }
        ?>
          <!-------User tutorial------->


          <!-------Useful Links------->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#sidebar-special" role="button" aria-expanded="false" aria-controls="sidebar-special">
                  <i class="fa-solid fa-gamepad icon"></i>
                  <span class="item-name"><?= __('user.useful_links') ?></span>
                  <i class="fas fa-chevron-right right-icon"></i>
                </a>
                <ul class="sub-nav collapse" id="sidebar-special" data-bs-parent="#sidebar-menu">
                  <li class="nav-item">
                    <a class="nav-link " href="<?= base_url('usercontrol/wallet_requests_list'); ?>">
                      <i class="fa-solid fa-circle icon"></i>
                      <i class="sidenav-mini-icon"> B </i>
                      <span class="item-name"><?= __('user.usercontrol_wallet_requests_list') ?></span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link " href="<?php echo base_url('ReportController/user_reports');?>">
                      <i class="fa-solid fa-circle icon"></i>
                      <i class="sidenav-mini-icon"> B </i>
                      <span class="item-name"><?= __('user.page_title_user_reports') ?></span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link " href="<?= base_url('usercontrol/editProfile'); ?>">
                      <i class="fa-solid fa-circle icon"></i>
                      <i class="sidenav-mini-icon"> B </i>
                      <span class="item-name"><?= __('user.profile_details') ?></span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link " href="<?= base_url('usercontrol/payment_details');?>">
                      <i class="fa-solid fa-circle icon"></i>
                      <i class="sidenav-mini-icon"> B </i>
                      <span class="item-name"><?= __('user.payment_details') ?></span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link " href="<?= base_url('usercontrol/changePassword');?>">
                      <i class="fa-solid fa-circle icon"></i>
                      <i class="sidenav-mini-icon"> B </i>
                      <span class="item-name"><?= __('user.page_title_changePassword') ?></span>
                    </a>
                  </li>
                  
                <?php if(isShowUserControlParts($userdashboard_settings['tickets_page'])){ ?> 
                  <li class="nav-item">
                    <a class="nav-link " href="<?= base_url('usercontrol/tickets');?>">
                      <i class="fa-solid fa-circle icon"></i>
                      <i class="sidenav-mini-icon"> B </i>
                      <span class="item-name"><?= __('user.page_title_tickets') ?></span>
                    </a>
                  </li>
                  <?php } ?>
                  
                </ul>
              </li>
          <!-------Useful Links------->


          <!-------Admin Marketplace Links------->
              <?php if($userdetails['is_vendor']==0 || ($userdetails['is_vendor']==1  && $market_vendor['marketvendorpanelmode'] ==0 )) { ?>
              
             <li class="nav-item">
                <a class="nav-link" href="<?= base_url('usercontrol/store_markettools'); ?>" style="color: <?= $user_side_bar_text_color['user_side_bar_text_color'] ?>;">
                  <i class="fas fa-network-wired icon"></i>
                  <span class="item-name"><?= __('user.page_title_my_links') ?></span>
                </a>
              </li>

          <?php } ?>
        <!-------Admin Marketplace Links------->


        <!-------User commission------->
              <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('usercontrol/mywallet');?>" style="color: <?= $user_side_bar_text_color['user_side_bar_text_color'] ?>;">
                      <i class="fa-solid fa-wallet icon"></i>
                 
                  <span class="item-name"> <?= __('user.page_title_my_wallet') ?></span>
                </a>
          </li>
            <!-------User commission------->


        <!-------User Network------->
        <?php if($refer_status) { ?>
              <li class="nav-item">
                    <a class="nav-link " href="<?php echo base_url('usercontrol/my_network');?>" style="color: <?= $user_side_bar_text_color['user_side_bar_text_color'] ?>;">
                      <i class="fas fa-network-wired icon"></i>
                  <span class="item-name"> <?= __('user.page_title_my_network') ?></span>
                </a>
          </li>
          <?php } ?>
       <!-------User Network------->


       <!-------User Payments------->
              <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('usercontrol/all_transaction');?>" style="color: <?= $user_side_bar_text_color['user_side_bar_text_color'] ?>;">
                      <i class="fas fa-shopping-cart icon"></i>
                 <span class="item-name">  <?= __('user.page_title_all_trans_user') ?></span>
                </a>
          </li>
       <!-------User Payments------->


       <!-------User membership------->
         <?php if(($membership['status'] == 1) || (($membership['status'] == 2) && ($userdetails['is_vendor'] == 1)) || (($membership['status'] == 3) && ($userdetails['is_vendor'] == 0))){ ?>

              <li class="nav-item  submenu-sidebar">
                <a class="nav-link collapsed text-truncate" href="#submenu1" data-bs-toggle="collapse"  data-bs-target="#submenu1"><i class="fas fa-users icon"></i> <span class="d-sm-inline"><?= __('user.page_title_membership') ?></span><i class="fas fa-chevron-right right-icon"></i></a>
                    <ul class="flex-column pl-2 sub-nav collapse" id="submenu1" aria-expanded="false">
                      <li class="nav-item">
                      <a class="nav-link " href="<?= base_url('usercontrol/purchase_plan');?>">
                        <i class="fa-solid fa-circle icon"></i>
                        <i class="sidenav-mini-icon"></i>
                        <span class="item-name"><?= __('user.page_title_buy_membership') ?></span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link " href="<?= base_url('usercontrol/purchase_history');?>">
                        <i class="fa-solid fa-circle icon"></i>
                        <i class="sidenav-mini-icon"></i>
                        <span class="item-name"><?= __('user.page_title_purchase_history') ?></span>
                      </a>
                    </li>
                    </ul>
              </li>
          <?php } ?>
      <!-------User membership------->
           



          <!-------vendor marketing menu------->
              <?php if((isset($userdetails['is_vendor']) && $userdetails['is_vendor']) && ((int)$market_vendor['marketvendorstatus'] == 1 )) { ?>
            
                <li class="nav-item">
                  <a class="nav-link" data-bs-toggle="collapse" href="#vendor-markrt" role="button" aria-expanded="false" aria-controls="vendor-markrt">
                    <i class="fas fa-users icon"></i>
                    <span class="item-name"><?= __('user.page_title_marketing') ?></span>
                    <i class="fas fa-chevron-right right-icon"></i>
                  </a>
                  <ul class="sub-nav collapse" id="vendor-markrt" data-bs-parent="#sidebar-menu">
                    <li class="nav-item">
                      <a class="nav-link " href="<?php echo base_url('usercontrol/programs');?>">
                        <i class="fa-solid fa-circle icon"></i>
                        <i class="sidenav-mini-icon"> C </i>
                        <span class="item-name"><?= __('user.ven_programs') ?></span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link " href="<?php echo base_url('usercontrol/integration_tools');?>">
                        <i class="fa-solid fa-circle icon"></i>
                        <i class="sidenav-mini-icon"> C </i>
                        <span class="item-name"><?= __('user.page_title_campaigns') ?></span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link " href="<?php echo base_url('usercontrol/integration');?>">
                        <i class="fa-solid fa-circle icon"></i>
                        <i class="sidenav-mini-icon"> C </i>
                        <span class="item-name"><?= __('user.page_title_plugins') ?></span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link " href="<?php echo base_url('usercontrol/wallet_setting');?>">
                        <i class="fa-solid fa-circle icon"></i>
                        <i class="sidenav-mini-icon"> C </i>
                        <span class="item-name"><?= __('user.page_title_vendor_setting') ?></span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link " href="<?php echo base_url('usercontrol/external_vendor_orders');?>">
                        <i class="fa-solid fa-circle icon"></i>
                        <i class="sidenav-mini-icon"> C </i>
                        <span class="item-name"><?= __('user.page_title_my_orders') ?></span>
                      </a>
                    </li>
                  </ul>
                </li>
              <?php } ?>
          <!-------vendor marketing menu------->


          <!-------vendor store menu------->
              <?php if((isset($userdetails['is_vendor']) && $userdetails['is_vendor']) && (int)$vendor_setting['storestatus'] == 1 && (int)$store_setting['status'] == 1){ ?>

                <li class="nav-item">
                  <a class="nav-link" data-bs-toggle="collapse" href="#vendor-store" role="button" aria-expanded="false" aria-controls="vendor-store">
                    <i class="fas fa-shopping-bag icon"></i>
                    <span class="item-name"><?= __('user.page_title_vendor_store') ?></span>
                    <i class="fas fa-chevron-right right-icon"></i>
                  </a>
                    <ul class="sub-nav collapse" id="vendor-store" data-bs-parent="#sidebar-menu">
                  <li class="nav-item">
                        <a class="nav-link " href="<?php echo base_url('usercontrol/store_dashboard');?>">
                          <i class="fa-solid fa-circle icon"></i>
                          <i class="sidenav-mini-icon"> B </i>
                          <span class="item-name"><?= __('user.page_title_store_dashboard') ?></span>
                        </a>
                      </li>
                      <?php if($store_setting['store_mode'] == 'cart') {?>
                      <li class="nav-item">
                        <a class="nav-link " href="<?php echo base_url('usercontrol/store_products');?>">
                          <i class="fa-solid fa-circle icon"></i>
                          <i class="sidenav-mini-icon"> B </i>
                          <span class="item-name"><?= __('user.page_title_common_title_store_products') ?></span>
                        </a>
                      </li>
                      <?php } ?>
                      <?php if($store_setting['store_mode'] == 'cart') {?>
                      <li class="nav-item">
                        <a class="nav-link " href="<?php echo base_url('usercontrol/store_coupon');?>">
                          <i class="fa-solid fa-circle icon"></i>
                          <i class="sidenav-mini-icon"> B </i>
                          <span class="item-name"><?= __('user.page_title_store_coupons') ?></span>
                        </a>
                      </li>
                      <?php } ?>
                      <?php if($store_setting['store_mode'] == 'sales') {?>
                      <li class="nav-item">
                        <a class="nav-link " href="<?php echo base_url('usercontrol/sales_products');?>">
                          <i class="fa-solid fa-circle icon"></i>
                          <i class="sidenav-mini-icon"> B </i>
                          <span class="item-name"><?= __('user.page_title_common_title_store_products') ?></span>
                        </a>
                      </li>
                      <?php } ?>
                      <li class="nav-item">
                        <a class="nav-link " href="<?php echo base_url('usercontrol/store_venodr_orders');?>">
                          <i class="fa-solid fa-circle icon"></i>
                          <i class="sidenav-mini-icon"> B </i>
                          <span class="item-name"><?= __('user.vendor_orders_small') ?></span>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link " href="<?php echo base_url('usercontrol/listclients');?>">
                          <i class="fa-solid fa-circle icon"></i>
                          <i class="sidenav-mini-icon"> B </i>
                          <span class="item-name"><?= __('user.store_clients') ?></span>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link " href="<?php echo base_url('usercontrol/store_setting');?>">
                          <i class="fa-solid fa-circle icon"></i>
                          <i class="sidenav-mini-icon"> B </i>
                          <span class="item-name"><?= __('user.page_title_store_setting') ?></span>
                        </a>
                      </li>
                  </ul>
              </li>
              <?php } ?>
          <!-------vendor store menu------->
            
            
            
          <!-------vendor mlm/wallet menu------->
          <?php if(isset($market_vendor) && $market_vendor['vendormlmmodule'] == 1) {

           if((isset($userdetails['is_vendor']) && $userdetails['is_vendor']) && ((int)$market_vendor['marketvendorstatus'] == 1 ) || (isset($userdetails['is_vendor']) && $userdetails['is_vendor']) && (int)$vendor_setting['storestatus'] == 1 && (int)$store_setting['status'] == 1) { ?>

               <li class="nav-item ">
                    <a class="nav-link " href="<?php echo base_url('usercontrol/mlm_levels');?>" style="color: <?= $user_side_bar_text_color['user_side_bar_text_color'] ?>;">
                      <i class="fas fa-money-bill icon"></i>
                  <span class="item-name"><?= __('user.page_title_mlm_setting') ?></span>
                </a>
            </li>
              <?php } }
          ?>
          <!-------vendor mlm/wallet menu------->

          <!-------Vendor deposits------->
              <?php if((isset($userdetails['is_vendor']) && $userdetails['is_vendor']) == 1){ ?>
                <li class="nav-item ">
                      <a class="nav-link " href="<?php echo base_url('usercontrol/my_deposits');?>" style="color: <?= $user_side_bar_text_color['user_side_bar_text_color'] ?>;">
                        <i class="fas fa-money-bill icon"></i>
                   <span class="item-name"> <?= __('user.page_title_my_deposits') ?></span>
                  </a>
            </li>
              <?php } ?>
          <!-------Vendor deposits------->

          <!--User contact us page-->
          <?php if(isShowUserControlParts($userdashboard_settings['contact_us_page'])){ ?>
          <li class="nav-item ">
            <a class="nav-link " href="<?= base_url('usercontrol/contact-us'); ?>" style="color: <?= $user_side_bar_text_color['user_side_bar_text_color'] ?>;">
              <i class="fas fa-money-bill icon"></i>
              <span class="item-name"> <?= __('user.page_title_contact_admin') ?></span>
              </a>
            </li>
            <?php } ?> 
            <!--User contact us page-->
          </ul>
          
        </div>
        <script async="" src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2012969363648405" crossorigin="anonymous" data-checked-head="true"></script>
        <?php 
        $googleAdsTop = $this->Setting_model->getGoogleAds(1,1);
        $googleAdsbottom = $this->Setting_model->getGoogleAds(2,1);
        if(!empty($googleAdsTop)){
        ?>
        <div class="sidebar-footer pt-2">
          <div class="googleadd-bg text-center py-5">
            Google Add Here 
            <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="<?= @$googleAdsTop[0]['client_key']?>"
             data-ad-slot="<?= @$googleAdsTop[0]['unit_key']?>"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
          </div>
        </div>
      <?php } if(!empty($googleAdsbottom)){?>
        <div class="googleadd-bg text-center py-5 mt-3"> 
          Google Add Here 
          <ins class="adsbygoogle"
           style="display:block"
           data-ad-client="<?= @$googleAdsbottom[0]['client_key']?>"
           data-ad-slot="<?= @$googleAdsbottom[0]['unit_key']?>"
           data-ad-format="auto"
           data-full-width-responsive="true"></ins>
          <script>
               (adsbygoogle = window.adsbygoogle || []).push({});
          </script> 
        </div>
      <?php }?>
      </div>
      </div>
  </aside>
 
  <main class="main-content">
    <div class="position-relative">
    <!--Nav Start-->
        <nav class="nav navbar navbar-expand-lg navbar-light iq-navbar" style="background-color: <?= $user_top_bar_color['user_top_bar_color'] ?>;">
          <div class="container-fluid navbar-inner">
            <a href="#_" class="navbar-brand">
              <!--Logo start-->
              <?php $logo = $SiteSetting['admin-side-logo'] ? base_url('assets/images/site/'. $SiteSetting['admin-side-logo'] ) : base_url('assets/template/images/user-logo.png'); ?>

              <img src="<?= $logo;?>" alt="logo" class="logo <?= ($SiteSetting['custom_logo_size']) ? 'customLogoClass' : '' ?>">
              <img src="<?= base_url('assets/images/user-logo.png');?>" alt="<?= __('user.logo') ?>" class="mini-logo">
              <!--logo End-->
            </a>
            <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
              <i class="fa-solid fa-arrow-right icon"></i>
            </div>
            <div class="input-group search-input">
              <span class="input-group-text" id="search-input">
                <i class="fa-solid fa-magnifying-glass"></i>
              </span>
              <input type="search" class="form-control" placeholder="Search Keyword...">
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon">
                <span class="navbar-toggler-bar bar1 mt-2"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav ms-auto align-items-center navbar-list mb-2 mb-lg-0">
                <li class="nav-item dropdown"><?= $CurrencyHtml ?></li>
                <li class="nav-item dropdown"><?= $LanguageHtml ?></li>
                <li class="nav-item dropdown">
                  <a href="#" class="nav-link notification-icon" id="notification-drop" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-bell icon-symbol"></i>
                   <span class="badge"><?= $notifications_count > 99 ? "99+": $notifications_count; ?></span>
                  </a>
                  <div class="sub-drop dropdown-menu dropdown-menu-end p-0 notifications" aria-labelledby="notification-drop">
                    <div class="card shadow-none m-0">
                      <div class="card-header d-flex justify-content-between bg-primary py-3">
                        <div class="header-title heading-notification">
                          <h5 class="mb-0 text-white"><?= __('user.all_notification') ?>
                            
                            <strong><?= $notifications_count > 99 ? "99+": $notifications_count; ?></strong>
                          </h5>
                        </div>
                      </div>
                      <div class="card-body p-0">
                        <?php $image = !empty($userdetails['avatar']) ? base_url('assets/images/users/'. $userdetails['avatar']) : base_url('assets/vertical/assets/images/users/avatar-1.jpg') ?>
                        <?php
            if($notifications){
                         foreach ($notifications as $key => $notification) {

                          ?>
                        <a href="javascript:void(0)" onclick="shownofication(<?= $notification['notification_id'].',\''.base_url('usercontrol').$notification['notification_url'] . '\''; ?>)"  class="iq-sub-card">
                          <div class="d-flex align-items-center">
                            <img class="avatar-40 rounded-pill bg-soft-primary p-1" src="<?= $image;?>" alt="">
                            <div class="ms-3 w-100">
                              <h6 class="mb-0 "><?= $notification['notification_title']; ?></h6>
                              <div class="d-flex justify-content-between align-items-center">
                                <p class="mb-0"><?= $notification['notification_description']; ?></p>
                               <!-- <small class="float-end font-size-12">Just Now</small>-->
                              </div>
                            </div>
                          </div>
                        </a>
                      <?php }?>
                        <div class="text-center"> 
                        <a class="dropdown-item view-area" href="<?= base_url('usercontrol/notification') ?>"><?= __('user.view_all') ?></a> 
                      </div>
                        <?php }else{?>  
                          <a href="javascript:void(0)"   class="iq-sub-card">
                          <div class="d-flex align-items-center">
                            <img class="avatar-40 rounded-pill bg-soft-primary p-1" src="assets/images/shapes/01.png" alt="">
                            <div class="ms-3 w-100">
                              
                              <div class="d-flex justify-content-between align-items-center">
                                <p class="mb-0"><?= __('user.no_notifications') ?></p>
                              </div>
                            </div>
                          </div>
                        </a>
                     <?php }?>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="nav-item dropdown">
                  <a class="nav-link py-0 d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php $image = !empty($userdetails['avatar']) ? base_url('assets/images/users/'. $userdetails['avatar']) : base_url('assets/vertical/assets/images/users/avatar-1.jpg') ?>
                    <img src="<?= $image ?>" alt="" class="img-fluid avatar avatar-50 avatar-rounded">
                    <div class="caption ms-3 d-md-block ">
                      <p class="mb-0 caption-sub-title"><?= ($setting['top_left_text']) ? $setting['top_left_text'] : ($loginUser['is_vendor']== 1 ? __('user.vendor') : __('user.affiliate'))  ?> </p>
                      <h6 class="mb-0 caption-title"><?= __('user.welcome_user') ?> <strong><?= $loginUser['username'] ?></strong></h6>
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li>
                      <a class="dropdown-item" href="<?= base_url('usercontrol/editProfile'); ?>"><?= __('user.profile') ?></a>
                    </li>
                    
                    <li>
                      <hr class="dropdown-divider">
                    </li>
                    <li>
                      <a class="dropdown-item" href="<?= base_url('usercontrol/logout'); ?>"><?= __('user.logout') ?></a>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
          </div>
        </nav>
        <?php if($method == "dashboard"){?>
      <!-- Nav Header Component Start -->
        <div class="iq-navbar-header" style="height: 215px;">
          <div class="container-fluid iq-container">
            <div class="row">
              <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                  <div>
                    <h1><?= ($setting['top_left_text']) ? $setting['top_left_text'] : ($loginUser['is_vendor']== 1 ? __('user.top_title_vendor') : __('user.affiliate_panel'))  ?></h1>
                    <p class="user-level-info" style="color: <?= $user_side_bar_clock_text_color['user_side_bar_clock_text_color'] ?>;">
                      <?= __('user.user_level_commission_plan') ?>:
                    <?php
                      $user_level;
                      $user_sale_comission_value;

                      if($award_level['status']){
                       
                        if($membership['status'] && $userPlan->commission_sale_status){

                          if($userPlan->level_number){
                            $user_level = $userPlan->level_number;
                            $user_sale_comission_value = '['.$userPlan->sale_comission_rate.'%]';
                          } else {

                            $user_level = __('admin.default');
                          }
                        } else if($userdetails['level_id']){
                          
                          foreach ($levels as $key => $value){
                            if($userdetails['level_id'] == $value['id']){
                              $user_level = $value['level_number'];
                              $user_sale_comission_value = '['.$value['sale_comission_rate'].'%]';
                            }
                          }
                        } else {
                         
                          $user_level = __('admin.default');
                        }
                      } else {
                        
                        $user_level = __('admin.default');
                      }
                      echo  $user_level.' '.$user_sale_comission_value;
                    ?>
                  </p>
                  </div>
                  <div></div>
                </div>
              </div>
            </div>
          </div>
          <div class="iq-header-img">
            <img src="<?= base_url('assets/template/images/top-header.jpg');?>" alt="header" class="img-fluid w-100 h-100 animated-scaleX">
          </div>
        </div>
        <!-- Nav Header Component End -->
    <?php }?>
        <!--Nav End-->
    </div>
  <style>
   .sidebar-list ul > li.show > a{
        background-color: <?= $user_side_bar_text_hover_color['user_side_bar_text_hover_color'] ?> !important;
  }
  </style>


<div class="conatiner-fluid content-inner mt-n5 py-0">
      <?php $image = !empty($userdetails['avatar']) ? base_url('assets/images/users/'. $userdetails['avatar']) : base_url('assets/vertical/assets/images/users/avatar-1.jpg') ?>
<!--hello section-->
    <div class="row">
      <div class="col-lg-12">
        <?php if($method == "dashboard"){?>
        <div class="card">
          <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
              <div class="d-flex flex-wrap align-items-center">
                <div class="profile-img position-relative me-3 mb-3 mb-lg-0 profile-logo profile-logo1">
                  <img src="<?= $image;?>" class="img-fluid rounded-pill avatar-80" alt="">
                </div>
                <div class="d-flex flex-wrap align-items-center mb-3 mb-sm-0">
                  <h4 class="me-2 h4"><?=__('user.hello');?> <?= $loginUser['username'] ?>,</h4>
                    <small class="ml-1 mb-1">
                        <?= __('admin.session_timeout') ?>: 
                        <span class="session-timer" style="color: <?= $user_side_bar_clock_text_color['user_side_bar_clock_text_color'] ?>;width:115px;">
                            <em>
                                <?php
                                    $hours = floor($timeout / 3600);
                                    $minutes = floor(($timeout % 3600) / 60);
                                    $seconds = ($timeout % 3600) % 60;
                                    echo sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
                                ?>
                            </em>
                        </span>
                    </small>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php }?>
      </div>
    </div>
<!--hello section-->


<div class="container-fluid <?= ($method != "dashboard")?'mt-3':'';?>">
  <div class="bd-example mb-3 mt-3">
  
    <?php if(($marketVendorStatus['marketvendorstatus'] == 1) || (int)$vendor_setting['storestatus'] == 1 && (int)$store_setting['status'] == 1): ?>
      <?php if(isset($userdepbal['show_deposit_warning']) &&  $userdepbal['show_deposit_warning'] == 1 && $userdepbal['vendor_deposit_status'] == 1 && $userdetails['reg_approved'] == 1): ?>
        <div class="notification-bg">
          <span><?= $userdepbal['vendor_min_deposit_warning'] ?> <?= c_format($userdepbal['vendor_min_deposit']);?><a href="<?= base_url('usercontrol/my_deposits') ?>"> <?= __('admin.click_to_deposit') ?></a></span>
        </div>
      <?php endif; ?>
    <?php endif; ?>
    
    <?php if ($userdetails['reg_approved'] == 1): ?>
      <?php $paymentDetailsSet = false; ?>

      <?php if ($PrimaryPaymentMethodStatus == 0): ?>
        <div class="notification-bg mt-2">
          <span><?= __('user.payment_details_not_set_message'); ?> <a href="<?= base_url('usercontrol/payment_details') ?>"><?= __('user.click_here'); ?></a></span>
        </div>
        <?php $paymentDetailsSet = true; ?>
      <?php endif; ?>

      <?php if (!$paymentDetailsSet && isset($payment_methods) && is_array($payment_methods['bank_transfer']) && $payment_methods['bank_transfer']['status'] == 1): ?>
        <?php if ($paymentlist['payment_account_number'] == '' || $paymentlist['payment_bank_name'] == '' || $paymentlist['payment_account_name'] == '' || $paymentlist['payment_ifsc_code'] == ''): ?>
          <div class="notification-bg mt-2">
            <span><?= __('user.payment_details_not_set_message'); ?> <a href="<?= base_url('usercontrol/payment_details') ?>"><?= __('user.click_here'); ?></a></span>
          </div>
          <?php $paymentDetailsSet = true; ?>
        <?php endif; ?>
      <?php endif; ?>

      <?php if (!$paymentDetailsSet && isset($payment_methods) && is_array($payment_methods['paypal']) && $payment_methods['paypal']['status'] == 1): ?>
        <?php if ($paymentlist['paypalaccounts']['paypal_email'] == ''): ?>
          <div class="notification-bg mt-2">
            <span><?= __('user.payment_details_not_set_message'); ?> <a href="<?= base_url('usercontrol/payment_details') ?>"><?= __('user.click_here'); ?></a></span>
          </div>
        <?php endif; ?>
      <?php endif; ?>

    <?php endif; ?>
  </div>
</div>

<?php if(isset($pageSetting[$pageKey])): ?>
  <ul class="breadcrumb hide-breadcrumb">
    <?php
      if(count($pageSetting)>0 && isset($pageSetting[$pageKey]) && in_array("breadcrumb", $pageSetting[$pageKey]))
        $count = count($pageSetting[$pageKey]['breadcrumb']);
      else 
        $count=0; 

      foreach ($pageSetting[$pageKey]['breadcrumb'] as $key => $value): ?>
        <li class="breadcrumb-item <?= $count == $key ? 'active' : '' ?>">
          <a href="<?= $value['link'] ?>"><?= $value['title'] ?></a>
        </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

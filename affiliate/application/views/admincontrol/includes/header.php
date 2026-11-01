<!doctype html>
<?php
  $db =& get_instance();
  $userdetails=$db->Product_model->userdetails(); 
  $SiteSetting =$db->Product_model->getSiteSetting();
  $db->Product_model->ping($userdetails['id']);
  $products = $db->Product_model;
  $settings = $db->Setting_model;
  $notifications = $products->getnotificationnew('admin', null, 5);//show correct
  $notifications_count = $products->getnotificationnew_count('admin', null);
  $license = $products->getLicese();
  $LanguageHtml = $products->getLanguageHtml();
  $CurrencyHtml = $products->getCurrencyHtml();
  $noti_order = $products->hold_noti();
  $admin_side_bar_color = $products->getSettings('theme','admin_side_bar_color');
  $admin_side_bar_scroll_color = $products->getSettings('theme','admin_side_bar_scroll_color');
  $admin_top_bar_color = $products->getSettings('theme','admin_top_bar_color');
  $admin_logo_color = $products->getSettings('theme','admin_logo_color');
  $admin_side_font = $products->getSettings('site','admin_side_font');
  $admin_footer_color = $products->getSettings('theme','admin_footer_color');
  $admin_button_color = $products->getSettings('theme','admin_button_color'); 
  $admin_button_hover_color = $products->getSettings('theme','admin_button_hover_color');
  $allToDo = $settings->allToDo();

  $commonSetting = array(
    'site' => array('notify_email'),
    'store' => array('affiliate_cookie'),
    'email' => array('from_email'),
    'productsetting' => array('product_commission', 'product_ppc', 'product_noofpercommission'),
    'affiliateprogramsetting' => array('affiliate_commission', 'affiliate_ppc'),
    'paymentsetting' => array('api_username', 'api_password', 'api_signature'),
  );

  $allSettings = array();
  foreach ($commonSetting as $key => $value) {
    $allSettings[$key] = $products->getSettings($key);
  }
  $required = '';
  $validate = true;
  foreach ($commonSetting as $key => $fields) {
    $data = $allSettings[$key];
    foreach ($fields as $field) {
      if (!isset($data[$field]) || $data[$field] == '') {
        $required .= "{$key} - {$field} \n";
        $validate = false;
      }
    }
  }

  $page_id = $products->page_id();

  $serverReq = checkReq();

  require APPPATH."config/breadcrumb.php";
  $pageKey = $db->Product_model->page_id();

  ?>
  <html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $SiteSetting['name'] ?> - <?= __('admin.menu_admin_panel') ?></title>
    <meta content="<?= $SiteSetting['meta_description'] ?>" name="description" />
    <meta content="<?= $SiteSetting['meta_author'] ?>" name="author" />
    <meta content="<?= $SiteSetting['meta_keywords'] ?>" name="keywords" />
    <?php if($SiteSetting['favicon']){ ?>
    <link rel="icon" href="<?= base_url('assets/images/site/'.$SiteSetting['favicon']) ?>" type="image/*" sizes="16x16">
    <?php } ?>

    <!--summNote jquery files-->
    <script src="<?= base_url('assets/template/summernote/jquery-3.4.1.slim.min.js'); ?>"></script>
    <!--summNote jquery files-->
    
<!--Plugins Css-->
<link href="<?= base_url('assets/plugins/magnific-popup/magnific-popup.css') ?>?v=<?= av() ?>" rel="stylesheet" type="text/css"/>
<link href="<?= base_url('assets/js/jquery-confirm.min.css') ?>?v=<?= av() ?>" rel="stylesheet"/>
<link href="<?= base_url('assets/plugins/morris/morris.css') ?>?v=<?= av() ?>" rel="stylesheet"/>

<link href="<?= base_url('assets/css/jquery-ui.css') ?>?v=<?= av() ?>" rel="stylesheet" type="text/css">
<link href="<?= base_url('assets/plugins/chartist/css/chartist.min.css') ?>?v=<?= av() ?>" rel="stylesheet" type="text/css"/>
<link href="<?= base_url('assets/vertical/assets/css/icons.css') ?>?v=<?= av() ?>" rel="stylesheet" type="text/css"/>

<link href="<?= base_url('assets/plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css') ?>?v=<?= av() ?>" rel="stylesheet" type="text/css" media="screen">

<link href="<?= base_url('assets/css/jquery.uploadPreviewer.css') ?>?v=<?= av() ?>" rel="stylesheet" type="text/css" media="screen">
<link href="<?= base_url('assets/plugins/datetimepicker/jquery.datetimepicker.min.css') ?>?v=<?= av() ?>" rel="stylesheet"/>
<link href="<?= base_url('assets/plugins/datatable/select2.css') ?>?v=<?= av() ?>" rel="stylesheet"/>
<link href="<?= base_url('assets/js/jssocials-1.4.0/jssocials.css') ?>?v=<?= av() ?>" type="text/css" rel="stylesheet"/>
<link href="<?= base_url('assets/js/jssocials-1.4.0/jssocials-theme-flat.css') ?>?v=<?= av() ?>" type="text/css" rel="stylesheet"/>
<!--Plugins Css-->


<!-- Custom Css -->
<link href='<?= base_url('assets/css/admin-common.css') ?>?v=<?= av() ?>' rel='stylesheet' type="text/css"/>

<link href="<?= base_url('assets/template/css/admin.style.css') ?>?v=<?= av() ?>" rel="stylesheet"/>
<link href="<?= base_url('assets/template/css/admin.responsive.css') ?>?v=<?= av() ?>" rel="stylesheet"/>
<!-- Custom Css -->

<!-- Bootstrap 5 Css -->
<link rel="stylesheet" href="<?= base_url('assets/template/css/bootstrap.min.css') ?>?v=<?= av() ?>">
<link rel="stylesheet" href="<?= base_url('assets/template/css/bootstrap-icons.css') ?>?v=<?= av() ?>">
<link rel="stylesheet" href="<?= base_url('assets/template/css/bootstrap-toggle.min.css') ?>?v=<?= av() ?>">
<link rel="stylesheet" href="<?= base_url('assets/template/css/all.min.css') ?>?v=<?= av() ?>">
<!--Bootstrap 5 Css-->

<!--summNote css files-->
<link rel="stylesheet" href="<?= base_url('assets/template/summernote/summernote-lite.min.css') ?>?v=<?= av() ?>">
<!--summNote css files-->


<!--bootstrap 5 js files-->
     <script src="<?= base_url('assets/template/js/jquery-3.6.0.min.js'); ?>?v=<?= av() ?>"></script>
     <script src="<?= base_url('assets/template/js/popper.min.js'); ?>?v=<?= av() ?>"></script>
     <script src="<?= base_url('assets/template/js/bootstrap.min.js'); ?>?v=<?= av() ?>"></script>
<!--bootstrap 5 js files-->

    <script src="<?= base_url('assets/plugins/datatable/select2.min.js'); ?>?v=<?= av() ?>"></script>
    <script src="<?= base_url('assets/plugins/datetimepicker/jquery.datetimepicker.full.min.js'); ?>?v=<?= av() ?>">
    </script>

    
    <style type="text/css">
      .scroll-bar::-webkit-scrollbar-thumb {
        background: <?= $admin_side_bar_scroll_color['admin_side_bar_scroll_color'] ?>;
      }
      .scroll-bar::-webkit-scrollbar-thumb:hover {
        background: <?= $admin_side_bar_scroll_color['admin_side_bar_scroll_color'] ?>;
      }
      .nav-tabs .nav-link, .nav-pills .nav-link {
        font-family: <?= $admin_side_font['admin_side_font'] ?> !important;
      }
      h1, h2, h3, h4, h5, h6, th, label {
        font-family: <?= $admin_side_font['admin_side_font'] ?> !important;
      }
      .form-control {
        font-family: <?= $admin_side_font['admin_side_font'] ?> !important;
      }
      fieldset.custom-design {
        font-family: <?= $admin_side_font['admin_side_font'] ?> !important;
      }
      .admin_side_bar_color {
        background-color: <?= $admin_side_bar_color['admin_side_bar_color'] ?> !important;
      }
      .admin_logo_color {
        background-color: <?= $admin_logo_color['admin_logo_color'] ?> !important;
      }
      .admin_top_bar_color {
        background-color: <?= $admin_top_bar_color['admin_top_bar_color'] ?> !important;
      }
      .admin_footer_color {
        background-color: <?= $admin_footer_color['admin_footer_color'] ?> !important;
      }
      .admin_button_color, .btn-primary {
        background-color: <?= $admin_button_color['admin_button_color'] ?> !important;
        border: 1px solid <?= $admin_button_color['admin_button_color'] ?> !important ;
      }
      .admin_button_color:hover, .btn-primary:hover {
      background-color: <?= $admin_button_hover_color['admin_button_hover_color'] ?> !important;
        border: 1px solid <?= $admin_button_hover_color['admin_button_hover_color'] ?> !important ;
      }
    </style>

    <?php if($SiteSetting['google_analytics'] != '') echo $SiteSetting['google_analytics']; ?>

    <script type="text/javascript">
      window.affiliatePro ={
        base_url:"<?= base_url() ?>"
      }
    </script>

<!--To Do list script-->
<script type="text/javascript">
  function gettodoList() {
          $.ajax({
            url:'<?= base_url("todo/getodolist"); ?>',
            type:'GET',
            dataType:'json',
            async:false,
            success:function(data){
              if(data.length != 0 && data != "null") {
                var htmllist = "";
                for (var i = 0; i < data.length; i++) {
                  if(data[i].id){
                    var ichecked= data[i].is_done == 1 ? 'checked':'';
                    var iscompleted= data[i].is_done == 1 ? 'completed':'';
                    htmllist += "<li class='"+iscompleted+" ' title='"+data[i].todo_date+"' ><div class='form-check'><label class='form-check-label'><input class='checkbox completedTodo' data-id='"+data[i].id+"' type='checkbox' "+ichecked+" />" + data[i].notes + "<i class='input-helper'></i></label></div><i class='remove fa fa-times-circle removetodolist' data-id='"+data[i].id+"'></i><i class= 'update fa fa-pencil edittodolist' data-id='"+data[i].id+"' data-note='"+data[i].notes+"' data-date='"+data[i].todo_date+"'></i></li>";
                  }
                }

                $('.todo-list').html(htmllist);


              } else {
                $('.todo-list').html(`<div class="events py-4 border-bottom px-3">
                  <div class="wrapper d-flex mb-2">
                  <span>No todo list</span>
                  </div>
                  </div>`)
              }
            },
          })
        }
        (function($) {
          'use strict';
          $(function() {
            $("#datetodoList,#tododateCal").datepicker({
             autoclose: true, 
             todayHighlight: true,
             minDate:new Date(),
             changeMonth:true,
             changeYear:true,
             defaultDate:new Date(),
             dateFormat:"yy-mm-dd"
           });

            var todoListInput = $('.todo-list-input');
            
      $('.todo-list-add-btn').on("click", function(event) {
          event.preventDefault();

          var item = $(this).prevAll('.todo-list-input').val();
          var id  = $("#todoListItemid").val();

          if (item) {
              $.ajax({
                  url:'<?= base_url("todo/addtodolist"); ?>',
                  type:'POST',
                  dataType:'json',
                  async:false,
                  data: { note :item, id:id, todo_date:$("#datetodoList").val() },
                  success:function(data){
                      if(data.status){
                          showPrintMessage(data.message,'success');
                          gettodoList();
                          todoListInput.val("");
                          $("#datetodoList").val('');
                          $("#todoListItemid").val(0);
                          $('#add-task-todo').text('Add');
                          var cuUrl = window.location.href;
                          const lastSegment = cuUrl.split("/").pop();
                          if(lastSegment =="todolist" || lastSegment =="todolist#" || lastSegment=="dashboard" || lastSegment=="dashboard#") {
                              $('#calendar').fullCalendar('prev');
                              $('#calendar').fullCalendar('next');
                          }
                      } else {
                          showPrintMessage(data.message,'error');
                      }
                  },
              });
          } else {
            showPrintMessage('<?= __('admin.to_do_details_missing') ?>','error')
          }
      });


      $(document).on('change', '.completedTodo', function() {
         var id = $(this).data('id');
         var is_completed = 0;
         if ($(this).attr('checked')) {
          $(this).removeAttr('checked');
          is_completed=0;
        } else {
          $(this).attr('checked', 'checked');
          is_completed=1;
        }
        var id = $(this).data('id');
        var $that = $(this);
        $.ajax({
          url:'<?= base_url("todo/actiontodolist"); ?>',
          type:'POST',
          dataType:'json',
          data:{id:id,action:2,is_completed:is_completed},
          async:false,
          success:function(data){
           if(data.status) {
            showPrintMessage(data.message,'success');
            var cuUrl = window.location.href;
            const lastSegment = cuUrl.split("/").pop();
            if(lastSegment =="todolist" || lastSegment =="todolist#" || lastSegment=="dashboard" || lastSegment=="dashboard#")  {
              $('#calendar').fullCalendar('prev');
              $('#calendar').fullCalendar('next'); 
            }
          }
          else{
            showPrintMessage(data.message,'error');
          }
        },
      });
        $(this).closest("li").toggleClass('completed');
      });

      $(document).on('click', '.removetodolist', function() {
          var id = $(this).data('id');
          var $that = $(this);
          
          Swal.fire({
             icon: 'warning',
             text: '<?= __("admin.are_you_sure_to_delete") ?>',
             showCancelButton: true,
             cancelButtonText: 'cancel'
          }).then((result) => {
              if (result.value) {
                  $.ajax({
                      url: '<?= base_url("todo/actiontodolist"); ?>',
                      type: 'POST',
                      dataType: 'json',
                      data: { id: id, action: 1 },
                      async: false,
                      success: function(data) {
                          if (data.status) {
                              showPrintMessage(data.message, 'success');
                              $that.parent().remove();
                              var cuUrl = window.location.href;
                              const lastSegment = cuUrl.split("/").pop();
                              if (lastSegment == "todolist" || lastSegment == "todolist#" || lastSegment == "dashboard" || lastSegment == "dashboard#") {
                                  $('#calendar').fullCalendar('prev');
                                  $('#calendar').fullCalendar('next');
                              }
                          } else {
                              showPrintMessage(data.message, 'error');
                          }
                      },
                      error: function() {
                          showPrintMessage('An error occurred', 'error');
                      }
                  });
              }
          });
      });

      $(document).on('click', '.edittodolist', function() {
        var id = $(this).data('id');
        var note = $(this).data('note');
        $('.todo-list-input').val(note)
        $('#add-task-todo').text('Update');
        $("#todoListItemid").val(id);
        $("#datetodoList").val($(this).data('date'));
      });
          });
        })(jQuery);
</script>
<!--To Do list script-->


<script type="text/javascript">
      (function ($) {
        $.fn.btn = function (action) {
          var self = $(this);
          var tagName = self.prop("tagName");

          if(tagName == 'A'){
            if(action == 'loading'){
              $(self).addClass("disabled");
              $(self).attr('data-text',$(self).text());
              $(self).text('<?= ('admin.loading') ?>'+"..");
            }

            if(action == 'reset') $(self).text($(self).attr('data-text')); $(self).removeClass("disabled");

          } else {
            if(action == 'loading') $(self).addClass("btn-loading");

            if(action == 'reset')  $(self).removeClass("btn-loading");
          }
        }
      })(jQuery);

      $(document).delegate('a.disabled',"click",function (e){
        e.preventDefault();
      });

      var formDataFilter = function(formData){
        if(!(window.FormData && formData instanceof window.FormData)) return formData

          if(!formData.keys) return formData

            var newFormData = new window.FormData()

          Array.from(formData.entries()).forEach(function(entry){
            var value = entry[1]
            if(value instanceof window.File && value.name === '' && value.size === 0)
              newFormData.append(entry[0], new window.Blob([]), '')
            else
              newFormData.append(entry[0], value)

          })

          return newFormData
        }

        function apply_color(ele){  
          $(ele).spectrum({ 
            preferredFormat: "rgb", 
            showInput: true,  
            className: "full-spectrum", 
            showInitial: true,  
            showPalette: true,  
            showSelectionPalette: true, 
            maxSelectionSize: 10, 
            localStorageKey: "bolly_fashion", 
            allowEmpty:true,  
            palette: [  
            ["transparent"],  
            ["rgb(255, 255, 255)","rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)",   
            "rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)",   
            "rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)",   
            "rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)",   
            "rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)",   
            "rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)", 
            "rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)", 
            "rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)",  
            "rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)",  
            "rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)"]  
            ] 
          }); 
        }

(function($) {
  'use strict';
  $(function() {
    $(".nav-settings").on("click", function() {

      $("#right-sidebar").toggleClass("open"); 

    });
    $(".settings-close").on("click", function() {
      $("#right-sidebar,#theme-settings").removeClass("open");
    });

          //background constants
          var navbar_classes = "navbar-danger navbar-success navbar-warning navbar-dark navbar-light navbar-primary navbar-info navbar-pink";
          var sidebar_classes = "sidebar-light sidebar-dark";
          var $body = $("body")

          var admin_theme_selected = localStorage.getItem("admin_theme");
          if(admin_theme_selected == 1 ){
            $body.removeClass(sidebar_classes);
            $body.addClass("sidebar-dark");
            $(".sidebar-bg-options").removeClass("selected");
            $("#admin_theme").val(1)
          }else {
           $body.removeClass(sidebar_classes);
           $body.addClass("sidebar-light");
           $(".sidebar-bg-options").removeClass("selected");
           $("#admin_theme").val(0)
         }
        //sidebar backgrounds
        $("#sidebar-light-theme").on("click" , function(){
          $body.removeClass(sidebar_classes);
          $body.addClass("sidebar-light");
          $(".sidebar-bg-options").removeClass("selected");
          $(this).addClass("selected");
        });
        $("#sidebar-dark-theme").on("click" , function(){
          $body.removeClass(sidebar_classes);
          $body.addClass("sidebar-dark");
          $(".sidebar-bg-options").removeClass("selected");
          $(this).addClass("selected");
        });


        //Navbar Backgrounds
        $(".tiles.primary").on("click" , function(){
          $(".navbar").removeClass(navbar_classes);
          $(".navbar").addClass("navbar-primary");
          $(".tiles").removeClass("selected");
          $(this).addClass("selected");
        });
        $(".tiles.success").on("click" , function(){
          $(".navbar").removeClass(navbar_classes);
          $(".navbar").addClass("navbar-success");
          $(".tiles").removeClass("selected");
          $(this).addClass("selected");
        });
        $(".tiles.warning").on("click" , function(){
          $(".navbar").removeClass(navbar_classes);
          $(".navbar").addClass("navbar-warning");
          $(".tiles").removeClass("selected");
          $(this).addClass("selected");
        });
        $(".tiles.danger").on("click" , function(){
          $(".navbar").removeClass(navbar_classes);
          $(".navbar").addClass("navbar-danger");
          $(".tiles").removeClass("selected");
          $(this).addClass("selected");
        });
        $(".tiles.light").on("click" , function(){
          $(".navbar").removeClass(navbar_classes);
          $(".navbar").addClass("navbar-light");
          $(".tiles").removeClass("selected");
          $(this).addClass("selected");
        });
        $(".tiles.info").on("click" , function(){
          $(".navbar").removeClass(navbar_classes);
          $(".navbar").addClass("navbar-info");
          $(".tiles").removeClass("selected");
          $(this).addClass("selected");
        });
        $(".tiles.dark").on("click" , function(){
          $(".navbar").removeClass(navbar_classes);
          $(".navbar").addClass("navbar-dark");
          $(".tiles").removeClass("selected");
          $(this).addClass("selected");
        });
        $(".tiles.default").on("click" , function(){
          $(".navbar").removeClass(navbar_classes);
          $(".tiles").removeClass("selected");
          $(this).addClass("selected");
        });
        var body = $('body');
        $('[data-bs-toggle="minimize"]').on("click", function() { 
          if ( (body.hasClass('sidebar-toggle-display')) || (body.hasClass('sidebar-absolute'))) {
            body.toggleClass('sidebar-hidden');
          } else {
            body.toggleClass('sidebar-icon-only');
          }
        });

      });
})(jQuery);
(function($) {
  'use strict';
  $(function() {
    $('[data-bs-toggle="offcanvas"]').on("click", function() {
      $('.sidebar-offcanvas').toggleClass('active')
    });
  });
})(jQuery);
</script>

<?php if(is_rtl()){ ?>
  <!-- place here your RTL css code -->
<?php } ?>
</head>

<body class="body-common-class admin_side_bar_color" 
      style="font-family: <?= $admin_side_font['admin_side_font'] ?> !important;" 
      data-demo-mode="<?php echo (ENVIRONMENT === 'demo') ? 'true' : 'false'; ?>">

  <?php 
  $fbmessager_status = (array)json_decode($SiteSetting['fbmessager_status'],1);
  if(in_array('admin', $fbmessager_status))
    echo $SiteSetting['fbmessager_script'];
  ?>
<div class="main">
  <div class="overly"></div>
  <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row default-layout-navbar">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center admin_logo_color">
       <a class="navbar-brand brand-logo" href="<?php base_url('admincontrol/dashboard'); ?>">
         <?php $logo = $SiteSetting['admin-side-logo'] ? base_url('assets/images/site/'. $SiteSetting['admin-side-logo'] ) : base_url('assets/template/images/logo.png'); ?> <a href="
         <?= base_url('admincontrol/dashboard'); ?>" class="navbar-brand brand-logo">
         <img src="
         <?= $logo  ?>" alt="
         <?= __('admin.logo') ?>" />
       </a>
       <a class="navbar-brand brand-logo-mini" href="<?php base_url('admincontrol/dashboard'); ?>"><?php $logo = $SiteSetting['admin-side-logo'] ? base_url('assets/images/site/'. $SiteSetting['admin-side-logo'] ) : base_url('assets/images/logo-mini.png'); ?> <a href="
        <?= base_url('admincontrol/dashboard'); ?>" class="navbar-brand brand-logo-mini">
        <img src="
        <?= $logo  ?>" alt="
        <?= __('admin.logo') ?>" /></a>
      </div>

  <div class="navbar-menu-wrapper justify-content-between d-flex align-items-center admin_top_bar_color">
    <button class="navbar-toggler navbar-toggler align-self-center d-lg-none order-first ms-2" type="button" data-bs-toggle="minimize" data-bs-auto-toggle="false">
      <span class="fas fa-bars"></span>
    </button>
    <div class="header-right admin_top_bar_color">
      <ul class="d-flex flex-row bd-highlight pull-right">
        <!-- Theme Switcher -->
        <!-- This element will be hidden on screens smaller than 768px -->
        <li class="nav-item hide-switcher d-flex align-items-center d-none d-md-flex">
            <div class="theme-setting-wrapper" id="theme-setting-wrapper">
                <div id="settings-trigger" onclick="change_admin_theme();">
                    <input type="hidden" name="admin_theme" id="admin_theme" value="0" />
                    <i id="admin_theme_selected" class="admin_theme_selected fas fa-adjust fa-2x"></i>
                </div>
            </div>
        </li>
        <!-- Currency Dropdown -->
        <li id="currency_dropdown" class="nav-item dropdown">
            <?= $CurrencyHtml ?>
        </li>
        <!-- Language Dropdown -->
        <li id="language_dropdwon" class="nav-item dropdown">
            <?= $LanguageHtml ?>
        </li>

        <!-- Notification Dropdown -->
        <li class="nav-item dropdown notification-area arrow-position">
            <a class="nav-link dropdown-toggle text-white align-items-center border-0" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onclick="resetNotify();">
                <i class="fas fa-bell me-3" style="font-size: 1.2rem;"></i>
                <span class="badge bg-danger text-white notifications-count ajax-notifications_count" style="font-size: 0.9rem;"><?= $notifications_count; ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-end shadow border-0 user-setting p-0 rounded">
                <div class="d-flex justify-content-between align-items-center p-3 bg-primary text-white">
                    <h5 class="m-0"><?= __('admin.notification') ?></h5>
                    <span class="badge bg-light text-dark ajax-top_notifications_count"><?= $notifications_count; ?></span>
                </div>
                <div id="allnotification" class="overflow-auto" style="max-height: 300px;">
                    <?php
                    $last_id_notifications = 0;
                    foreach ($notifications as $key => $notification) {
                        if ($last_id_notifications <= $notification['notification_id']) {
                            $last_id_notifications = $notification['notification_id'];
                        }
                    ?>
                        <a class="dropdown-item py-2 px-3" href="javascript:void(0)" onclick="shownofication(<?= $notification['notification_id'] . ',\'' . base_url('admincontrol') . $notification['notification_url'] . '\''; ?>)">
                            <h6 class="mb-1 fw-bold"><?= $notification['notification_title']; ?></h6>
                            <p class="m-0 text-muted"><?= $notification['notification_description']; ?></p>
                        </a>
                    <?php } ?>
                    <input type="hidden" id="last_id_notifications" value="<?= $last_id_notifications ?>">
                </div>
                <div class="text-center p-3">
                    <a class="dropdown-item view-area" href="<?= base_url('admincontrol/notification') ?>">+ <?= __('admin.common_view_all') ?></a>
                </div>
            </div>
        </li>

        <!-- User Dropdown -->
        <li id="user_dropdown" class="nav-item dropdown user-right">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?php
                    $login_user_profile_avatar = (!empty($userdetails['avatar'])) ? base_url('assets/images/users/'.$userdetails['avatar']) : base_url('assets/vertical/assets/images/no-image.jpg');
                ?>
                <img class="profile-image rounded-circle" src="<?= $login_user_profile_avatar; ?>" alt="<?= $this->session->userdata('administrator')['firstname'].' '.$this->session->userdata('administrator')['lastname'] ?>">
                <i class="fas fa-chevron-down"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-end shadow border-0">
                <i class="arrow"></i>
                <a class="dropdown-item" href="<?= base_url('admincontrol/editProfile'); ?>"><i class="fas fa-user me-2"></i><?= __('admin.top_profile') ?></a>
                <a class="dropdown-item" href="<?= base_url('admincontrol/changePassword'); ?>"><i class="fas fa-lock me-2"></i><?= __('admin.top_change_password') ?></a>
                <a class="dropdown-item" href="<?= base_url('admincontrol/mywallet'); ?>"><i class="fas fa-wallet me-2"></i><?= __('admin.top_my_wallet') ?></a>
                <a class="dropdown-item" href="<?= base_url('admincontrol/paymentsetting'); ?>"><i class="fas fa-cog me-2"></i><?= __('admin.top_settings') ?></a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?= base_url('admincontrol/logout'); ?>"><i class="fas fa-sign-out-alt me-2"></i><?= __('admin.top_logout') ?></a>
            </div>
        </li>
        <!-- More Settings -->
        <li class="nav-item nav-settings d-lg-block">
            <a class="nav-link" href="#">
                <i class="fas fa-ellipsis-h"></i>
            </a>
        </li>
      </ul>
    </div>
  </nav>

<!-- To Do List Right-Side-->
<div id="right-sidebar" class="settings-panel">
    <i class="settings-close fa fa-times"></i>
    <ul class="nav nav-tabs bg-secondary text-white" id="setting-panel" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="todo-tab" data-bs-toggle="tab" href="#todo-section" role="tab"
               aria-controls="todo-section" aria-expanded="true"><?= __('admin.to_do_list') ?></a>
        </li>
    </ul>
    <div class="tab-content" id="setting-content">
        <div class="tab-pane fade show active" id="todo-section" role="tabpanel"
             aria-labelledby="todo-section" style="max-height: 600px; overflow-y: auto;">

            <div class="add-items d-flex px-3 mb-0">
                <form class="form w-100">
                    <div class="form-group d-flex">
                        <input type="text" class="form-control todo-list-input me-2" placeholder="Add To-do">
                        <input type="text" class="form-control me-2" id="datetodoList" value="" required
                               placeholder="To-do date">
                        <button type="submit" class="add btn btn-primary todo-list-add-btn" id="add-task-todo">
                            <?= __('admin.add') ?>
                        </button>
                        <input type="hidden" id="todoListItemid" value="0">
                    </div>
                </form>
            </div>

            <div class="list-wrapper p-3">
              <ul class="list-group">
                  <?php
                  if (sizeof($allToDo) > 0) {
                      foreach ($allToDo as $todo) {
                          $ichecked = $todo['is_done'] == 1 ? ' checked' : '';
                          $iscompleted = $todo['is_done'] == 1 ? 'completed' : '';
                          ?>
                          <li class="list-group-item d-flex justify-content-between align-items-center <?php echo $iscompleted ?>"
                              title="<?php echo $todo['todo_date'] ?>">
                              <div class="form-check text-truncate" style="max-width: 80%;">
                                  <label class="form-check-label">
                                      <input class="checkbox completedTodo" data-id="<?php echo $todo['id'] ?>"
                                             type="checkbox" <?php echo $ichecked ?>>
                                      <span class="text-truncate" style="max-width: calc(100% - 30px);">
                                          <?php echo $todo['notes'] ?>
                                      </span>
                                  </label>
                              </div>
                              <div>
                                  <i class="remove fa fa-times-circle removetodolist text-danger me-2"
                                     data-id="<?php echo $todo['id'] ?>"></i>
                                  <i class="update fa fa-pencil edittodolist" data-id="<?php echo $todo['id'] ?>"
                                     data-note="<?php echo $todo['notes'] ?>"
                                     data-date="<?php echo $todo['todo_date'] ?>"></i>
                              </div>
                          </li>
                          <?php
                      }
                  } else {
                      ?>
                      <div class="events py-4 border-bottom px-3">
                          <div class="wrapper d-flex mb-2">
                              <span>No todo list</span>
                          </div>
                      </div>
                      <?php
                  }
                  ?>
              </ul>
            </div>
        </div>
    </div>
</div>
<!-- To Do List Right-Side-->

</div>
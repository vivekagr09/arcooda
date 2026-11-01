<?php
if(!file_exists(FCPATH."/install/version.php") || !defined('SCRIPT_VERSION') || !defined('CODECANYON_LICENCE') || SCRIPT_VERSION == "" || CODECANYON_LICENCE == "") { ?>

  <script type="text/javascript">
    $("#model-adminpassword").remove();
    $.ajax({
      url:'<?= base_url("installversion/confirm_password") ?>',
      type:'POST',
      dataType:'json',
      data:{for:"license"},
      success:function(json){
        if(json['html']){
          $("body").append(json['html']);
          $("#model-adminpassword").modal("show");
          $('#model-adminpassword').on('hidden.bs.modal', function (){
            $("#model-adminpassword").modal("show");
          });
        }
      }
    });

  </script>
<?php } ?>

<script type="text/javascript">
  function resetNotify(){
    $.ajax({
      url:'<?= base_url('admincontrol/resetnotify') ?>',
      type:'POST',
      dataType:'json',
      beforeSend:function(){},
      success:function(response){
        if(response.status == 1)
          $(".ajax-notifications_count").text(0);
      },
    })
  }
</script>

<?php
$db =& get_instance(); 
$products = $db->Product_model; 
$userdetails=$db->Product_model->userdetails(); 
$SiteSetting =$db->Product_model->getSiteSetting(); 
$license = $products->getLicese();
$class_method=$db->router->fetch_method();

if(isset($license['is_lifetime']) && $license['is_lifetime'] == false){ ?>

  <div class="license-expire">
    <span><?= __('admin.your_license_expire_in') ?> <span class="timer"><?= $license['remianing_time'] ?></span> </span>
  </div>

  <script type="text/javascript">
    var distance = <?= (float)$license['remianing_time'] ?>;
    var x = setInterval(function() {
      var days = Math.floor(distance / (60 * 60 * 24));
      var hours = Math.floor((distance % (60 * 60 * 24)) / (60 * 60));
      var minutes = Math.floor((distance % (60 * 60)) / (60));
      var seconds = Math.floor((distance % (60)));

      var timer = '';
      if(days > 0) timer += days + "d ";
      if(hours > 0) timer += hours + "h ";

      $(".license-expire .timer").html(timer +  minutes + "m " + seconds + "s ");
      distance--;
      if(distance < 0){
        clearInterval(x);
        $(".license-expire .timer").html('<?= __('admin.expired') ?>');
        window.location.reload();
      }
    }, 1000);
  </script>

<?php } ?>

</div>
</div>

<?php
$global_script_status = (array)json_decode($SiteSetting['global_script_status'],1);
if(in_array('admin', $global_script_status))
  echo $SiteSetting['global_script'];
require APPPATH . 'views/common/setting_widzard.php';
?>

<!--common code to show progress bar for all curd functions-->
<div class="progress fixed-top" style="display: none; height: 4px;">
    <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuemin="0" aria-valuemax="100"></div>
</div>
<!--common code to show progress bar for all curd functions-->

  <input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
      <footer class="bg-light py-3 footer">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6">
                        <small class="text-muted"><?= $SiteSetting['footer'] ?></small>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <a href="<?= base_url('admincontrol/script_details') ?>" class="text-decoration-none">
                            <small class="text-muted"><?= __('admin.script_version') ?> <?php echo SCRIPT_VERSION ?></small>
                        </a>
                    </div>
                </div>
            </div>
        </footer>
</div>

<script src="<?= base_url('assets/js/jquery-ui.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery-confirm.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/modernizr.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/detect.js'); ?>"></script>
<script src="<?= base_url('assets/js/fastclick.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.slimscroll.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.blockUI.js'); ?>"></script>
<script src="<?= base_url('assets/js/waves.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.nicescroll.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery.scrollTo.min.js'); ?>"></script>
<script src="<?= base_url('assets/plugins/skycons/skycons.min.js'); ?>"></script>
<script src="<?= base_url('assets/plugins/raphael/raphael-min.js'); ?>"></script>
<script src="<?= base_url('assets/plugins/morris/morris.min.js'); ?>"></script>
<script src="<?= base_url('assets/plugins/magnific-popup/jquery.magnific-popup.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/jssocials-1.4.0/jssocials.min.js'); ?>"></script>
<script src='<?= base_url('assets/sweetalert/sweetalert.min.js') ?>'></script>

<!--summNote js files-->
<script src="<?= base_url('assets/template/summernote/summernote-lite.min.js'); ?>"></script>
<!--summNote js files-->


<!--JS files-->
<script src="<?= base_url('assets/template/js/jscolor.js'); ?>"></script>
<script src="<?= base_url('assets/template/js/darkmode.js'); ?>"></script>
<script src="<?= base_url('assets/template/js/colorsmode.js'); ?>"></script>
<script src="<?= base_url('assets/template/js/app.js'); ?>"></script>
<script src="<?= base_url('assets/template/js/footer-scripts.js'); ?>"></script>
<!--JS files-->

<!--pdf/excel/image js-->
<script src="<?= base_url('assets/template/js/xlsx.full.min.js'); ?>?v=<?= av() ?>"></script>
<script src="<?= base_url('assets/template/js/jspdf.umd.min.js'); ?>?v=<?= av() ?>"></script>
<script src="<?= base_url('assets/template/js/jspdf.plugin.autotable.min.js'); ?>?v=<?= av() ?>"></script>
<!--pdf/excel/image js-->


<!--Remember last tab script-->
<script>
    $(document).ready(function(){ 
        function manageTabClasses(target) {
            // Remove existing background and text color classes from all tabs
            $('#TabsNav .nav-link').removeClass('bg-secondary bg-primary text-white');

            // Add 'bg-secondary' and 'text-white' classes to all tabs
            $('#TabsNav .nav-link').addClass('bg-secondary text-white');

            // Add 'bg-primary' and 'text-white' classes to the target tab
            $(target).addClass('bg-primary text-white').removeClass('bg-secondary');
        }

        // Polyfill for window.location.pathname in older browsers
        var pagePath = window.location.pathname || (window.location.href.split(window.location.host)[1]);
        
        // Unique key for localStorage
        var localStorageKey = 'activeTab' + pagePath;

        // Initialize default active tab
        var defaultActiveTab = $('#TabsNav .nav-link').first();
        manageTabClasses(defaultActiveTab);

        // Event handler for tab change
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            manageTabClasses(e.target);
            if (typeof(Storage) !== "undefined") {  // Check if localStorage is supported
                localStorage.setItem(localStorageKey, $(e.target).attr('href'));
            }
        });

        // Retrieve stored tab from localStorage and activate it
        if (typeof(Storage) !== "undefined") {  // Check if localStorage is supported
            var activeTab = localStorage.getItem(localStorageKey);
            if (activeTab) {
                $('#TabsNav a[href="' + activeTab + '"]').tab('show');
                manageTabClasses($('#TabsNav a[href="' + activeTab + '"]'));
            }
        }
        else {
            // If localStorage is not supported, use the default active tab
            manageTabClasses(defaultActiveTab);
        }
    });
</script>
<!--Remember last tab script-->


<!-- flash message div -->
<div class="print-message"><?php print_message($this); ?></div>
<!-- flash message div -->

<!--flash message script-->
<script>
  function showPrintMessage(message, type, redirectUrl = "") {
    let messageStr = '';
    if (type === 'success') {
      messageStr = `<div class="alert alert-success alert-dismissible fade show" role="alert">
                      <i class="bi bi-check-circle-fill me-2"></i>${message}
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`;
    } else if (type === 'error') {
      messageStr = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <i class="bi bi-exclamation-triangle-fill me-2"></i>${message}
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`;
    } else {
      throw new Error('Invalid message type');
    }
    $(".print-message").html(messageStr);

    // Using jQuery to fade and remove the alert after 5 seconds
    window.setTimeout(function() {
      $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
      });
    }, 5000);

    if (redirectUrl !== "") {
      window.setTimeout(function() {
        window.location.href = redirectUrl;
      }, 4000);
    }
  }
</script>
<!--flash message script-->


<!--Remove alert message script-->
<script>
  // Using jQuery to fade and remove the alert after 5 seconds
  window.setTimeout(function() {
    $(".alert").fadeTo(500, 0).slideUp(500, function(){
      $(this).remove(); 
    });
  }, 5000);
</script>
<!--Remove alert message script-->

<!--Loading spinner for all save buttons-->
<script>
   // Wait for the document to be fully loaded
  document.addEventListener("DOMContentLoaded", function() {
      
      // Select all buttons with the class 'btn-submit'
      const submitButtons = document.querySelectorAll('.btn-submit');
      
      // Attach a click event listener to each submit button
      submitButtons.forEach(button => {
          button.addEventListener('click', function() {
              
              // Select the spinner icon and remove the 'd-none' class to display it
              const spinner = button.querySelector('.loading-submit');
              
              // Check if spinner is null before proceeding
              if (spinner) {
                  spinner.classList.remove('d-none');
              }
          });
      });
  });
</script>
<!--Loading spinner for all save buttons-->

<!--confirmpopup edit script-->
<script>
  function confirmpopup(url)
    { 
       Swal.fire({
       icon: 'warning',
       text: '<?= __('admin.are_you_sure_to_edit') ?>',
       showCancelButton: true,
       cancelButtonText: 'cancel'
    }).then(function(dismiss){
        if(dismiss.value==true)
        {
          window.location=url;
          return true;
        }
        else
        {
          return false;
        }
    });
    return false;
  };
</script>
<!--confirmpopup script-->

<?php if(true || count($status) > 0){ ?>
  <script type="text/javascript">
  
    $(document).delegate(".only-number-allow","keypress",function (e) {
      if (e.which != 46 && e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57))
        return false;
    });

    $(document).ready(function(){
      if(getCookie('hide_welcome') != 'true')
        $("#welcome-modal").modal("show");
    })

    function setCookie(cname, cvalue, exdays){
      var d = new Date();
      d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));

      var expires = "expires="+d.toUTCString();
      document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function readURLAndSetValue(input,name,placeholder){
      if(input.files && input.files[0]){
        var reader = new FileReader();

        reader.onload = function(e){
          $("input[name='"+name+"']").val('image.jpg');
          $(placeholder).attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
      }
    }

    function readURL(input,placeholder){
      if(input.files && input.files[0]){
        var reader = new FileReader();

        reader.onload = function(e){
          $(placeholder).attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
      }
    }

    function getCookie(cname){
      var name = cname + "=";
      var ca = document.cookie.split(';');

      for(var i = 0; i < ca.length; i++){
        var c = ca[i];

        while (c.charAt(0) == ' '){
          c = c.substring(1);
        }
        if(c.indexOf(name) == 0)
          return c.substring(name.length, c.length);
      }
      return "";
    }

    $('.hide-welcome').on('click',function(){
      setCookie("hide_welcome","true", 365)
      $("#welcome-modal").modal("hide");

    })
  </script>  
<?php } ?>

<div class="modal" id="model-shorturl"></div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<script type="text/javascript">
  let leftHeight = $(".left-menu").height();
  let navbarHeight = $(".dashboard-navbar").height();
  let errorHeight = $(".server-errors").height();
  let footerHeight = $(".dashboard-footer").height();
  let elTotalheight = navbarHeight + errorHeight + footerHeight;
  let contentHeight = leftHeight - elTotalheight - 26;
  $(".content-wrapper").css('min-height',contentHeight);

  $(document).delegate(".copy-input input",'click', function(){
    $(this).select();
  })

  $(document).delegate('[copyToClipboard]',"click", function(){
    $this = $(this);
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(this).attr('copyToClipboard')).select();
    document.execCommand("copy");
    $temp.remove();
    $this.tooltip('hide').attr('data-original-title','<?= __('admin.copied') ?>').tooltip('show');
    setTimeout(function() { $this.tooltip('hide'); }, 500);
  });

  $('[copyToClipboard]').tooltip({
    trigger: 'click',
    placement: 'bottom'
  });

  (function ($) {
    $.fn.button = function (action){
      var self = $(this);
      if(action == 'loading'){
        if($(self).attr("disabled") == "disabled"){
          }
          $(self).attr("disabled", "disabled");
          $(self).attr('data-btn-text', $(self).html());
          $(self).html('<div class="spinner-border spinner-border-sm"></div>' + $(self).text());
        }
        if(action == 'reset'){
          $(self).html($(self).attr('data-btn-text'));
          $(self).removeAttr("disabled");
        }
      }
    })(jQuery);
  </script>

<!--copyToClipboard Common Function-->
<script>
  $(document).delegate('[copyToClipboard]', "click", function(){
      $this = $(this);
      var $temp = $("<input>");

      $("body").append($temp);
      $temp.val($this.attr('copyToClipboard')).select();
      document.execCommand("copy");
      $temp.remove();

      // Get the span and update its content
      var $statusSpan = $this.find('.copy-status');
      $statusSpan.text('<?= __('admin.copied') ?>');

      // Set a timeout to clear the message after a short time
      setTimeout(function() {
          $statusSpan.text('');
      }, 1500);
  });
</script>
<!--copyToClipboard Common Function-->

  <div class="modal fade" id="ip-flag_model">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><?= __('admin.all_ips_details') ?></h4>
          <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
        </div>
      </div>
    </div>
  </div>

<!--summNote without image and video-->
    <script>
      $('.summernote-img').summernote({
        tabsize: 2,
        height: 300,
        minHeight: null,
        maxHeight: null,
        focus: true,
        toolbar: [
        ['style', ['style']],
        ['image', ['image']],
        ['font', ['bold', 'underline', 'clear']],
        ['fontname', ['fontname']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', []],
        ['insert', ['link']],
        ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
    </script>
<!--summNote without image and video-->

<!--summNote full-->
 <script>
       $('.summernote').summernote({
        tabsize: 2,
        height: 300,
    });
  </script>
<!--summNote full-->


<script>
    $(".select2-input").select2();
    $(document).delegate(".view-all",'click',function(){
      var data = $(this).find("span").html();
      var html = '<table class="table table-hover">';

      data = JSON.parse(data);
      html += '<tr>';
      html += ' <th>'+'<?= ('admin.ip') ?>'+'</th>';
      html += ' <th width="30px">'+'<?= ('admin.country') ?>'+'</th>';
      html += '</tr>';

      $.each(data, function(i,j){
        html += '<tr>';
        html += ' <td>'+ j['ip'] +'</td>';
        html += ' <td><img style="width: 20px;" src="<?= base_url('assets/vertical/assets/images/flags/') ?>'+ j['country_code'].toLowerCase() +'.png" ></td>';

        html += '</tr>';
      })

      html += '</table>';

      $("#ip-flag_model").modal("show");

      $("#ip-flag_model .modal-body").html(html);
    })
    $('[data-toggle="tooltip"]').tooltip();   
    if($('#morris-area-chart').length > 0){
      var areaData = [
      {y: '2011', a: 10, b: 15},
      {y: '2012', a: 30, b: 35},
      {y: '2013', a: 10, b: 25},
      {y: '2014', a: 55, b: 45},
      {y: '2015', a: 30, b: 20},
      {y: '2016', a: 40, b: 35},
      {y: '2017', a: 10, b: 25},
      {y: '2018', a: 25, b: 30}
      ];

      Morris.Area({
        element: 'morris-area-chart',
        pointSize: 3,
        lineWidth: 2,
        data: areaData,
        xkey: 'y',
        ykeys: ['a', 'b'],
        labels: ['Orders', 'Sales'],
        resize: true,
        hideHover: 'auto',
        gridLineColor: '#eef0f2',
        lineColors: ['#00c292', '#03a9f3'],
        lineWidth: 0,
        fillOpacity: 0.1,
        xLabelMargin: 10,
        yLabelMargin: 10,
        grid: false,
        axes: false,
        pointSize: 0

      });
    }
    $(document).ready(function(){
      if($('#morris-donut-chart').length > 0){
        var donutData = [
        <?php $str = '';
        $country_list = isset($country_list)?$country_list:[];
        foreach($country_list as $key => $one_item){ 
          $str .= '{label: "' . $one_item->name . '", value: ' . (int)$one_item->num . '},'; 
        }
        echo rtrim($str,", ");
        ?>
        ];
        Morris.Donut({
          element: 'morris-donut-chart',
          data: donutData,
          resize: true,
          colors: ['#40a4f1', '#5b6be8', '#c1c5e2', '#e785da', '#00bcd2']
        });
      }

      if($("#boxscroll").length > 0)
        $("#boxscroll").niceScroll({cursorborder:"",cursorcolor:"#cecece",boxzoom:true});

      if($("#boxscroll2").length > 0)
        $("#boxscroll2").niceScroll({cursorborder:"",cursorcolor:"#cecece",boxzoom:true}); 

      if($(".clickable-row").length > 0){
        $(".clickable-row").on('click',function(){
          window.location = $(this).data("href");
        });
      }
      if($("#Country").length > 0){
        $('#Country').on('change', function(){
          country_id = $(this).val();

          $.ajax({

            type: "POST",

            url: "<?= base_url();?>admincontrol/getstate",

            data:'country_id='+country_id,

            success: function(data){

              $("#StateProvince").html(data);

            }

          });

        });
      }
    });

    function shownofication(id,url){
      $.ajax({
        type: "POST",
        url: "<?= base_url('admincontrol/updatenotify');?>",
        data:'id='+id,
        dataType:'json',
        success: function(data){
        window.location.href=data['location'];
        }
      });
    }
</script>

<!--session script-->
<script>
  document.addEventListener("DOMContentLoaded", function() {
    var timerElement = document.querySelector(".session-timer em");
    var initialTime = <?php echo isset($timeout) ? $timeout : 60; ?>;
    var remainingTime;

    // Retrieve remainingTime from local storage
    if (localStorage.getItem('remainingTime')) {
      remainingTime = parseInt(localStorage.getItem('remainingTime'));
    } else {
      remainingTime = initialTime;
      localStorage.setItem('remainingTime', initialTime);
    }

    // Reset the timer when the page is refreshed or redirected
    window.addEventListener('load', function() {
      localStorage.setItem('remainingTime', initialTime);
      remainingTime = initialTime;
    });

    var timer = setInterval(function() {
      remainingTime--;
      localStorage.setItem('remainingTime', remainingTime);

      if (remainingTime <= 0) {
        clearInterval(timer);
        localStorage.removeItem('remainingTime');
        window.location.href = "<?php echo base_url('admincontrol/logout'); ?>";
        return;
      }

      if (timerElement) {
        var h = Math.floor(remainingTime / 3600);
        var m = Math.floor(remainingTime % 3600 / 60);
        var s = Math.floor(remainingTime % 3600 % 60);
        var timeString = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        timerElement.innerText = timeString;
      }
    }, 1000);
  });
</script>
<!--session script-->

</body>
</html>
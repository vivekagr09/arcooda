</div>
<?php 
$db =& get_instance(); 
$userdetails =$db->Product_model->userdetails('user'); 
$SiteSetting =$db->Product_model->getSiteSetting();
$global_script_status = (array)json_decode($SiteSetting['global_script_status'],1);
if(in_array('affiliate', $global_script_status))
	echo $SiteSetting['global_script'];

$user_footer_color = $db->Product_model->getSettings('theme','user_footer_color');
?>	    		
<!-- Spacer for Fixed Footer -->
<div class="mb-5 pb-5"></div>

<!-- Footer Section Start -->
<footer class="footer fixed-bottom py-3" style="background-color: <?= $user_footer_color['user_footer_color'] ?>;">
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <ul class="left-panel list-inline mb-0">
        </ul>
      </div>
      <div class="col-md-6 text-end">
        <div class="right-panel"> <?= $SiteSetting['footer'] ?></div>
      </div>
    </div>
  </div>
</footer>
<!-- Footer Section End -->




</main>
    <!-- Wrapper End-->
    <!-- offcanvas start -->
    <a class="btn btn-fixed-end btn-warning btn-icon btn-setting" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" role="button" aria-controls="offcanvasExample">
      <i class="fa-solid fa-gear animated-rotate"></i>
    </a>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" data-bs-scroll="true" data-bs-backdrop="true" aria-labelledby="offcanvasExampleLabel" aria-modal="true" role="dialog" style="visibility: visible;">
      <div class="offcanvas-header">
        <div class="d-flex align-items-center">
          <h3 class="offcanvas-title me-3 text-white" id="offcanvasExampleLabel">Settings</h3>
        </div>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body data-scrollbar">
        <div class="row">
          <div class="col-lg-12">
            <h5 class="mb-3">Scheme</h5>
            <div class="d-grid gap-x-3 grid-cols-3 mb-4">
              <div class="btn-border" data-setting="color-mode" data-name="color" data-value="dark">
                <i class="fa-solid fa-moon icon-symbol py-0"></i>
                <span class="ms-2 "> Dark </span>
              </div>
              <div class="btn-border active" data-setting="color-mode" data-name="color" data-value="light">
                <i class="fa-solid fa-sun icon-symbol py-0"></i>
                <span class="ms-2 "> Light</span>
              </div>
            </div>
            <hr class="hr-horizontal">
            <h5 class="mb-3 mt-4">RTl to LTR mode </h5>
            <div class="d-grid gap-x-1 grid-cols-5 mb-4">
              <div class="text-center">
                <img src="<?= base_url('assets/template/images/04.jpg')?>" alt="ltr" class="mode dark-img img-fluid btn-border p-0 flex-column active" data-setting="dir-mode" data-name="dir" data-value="ltr" height="70" width="100">
                <img src="<?= base_url('assets/template/images/04.jpg')?>" alt="ltr" class="mode light-img img-fluid btn-border p-0 flex-column active" data-setting="dir-mode" data-name="dir" data-value="ltr" height="70" width="100">
              </div>
              <div class="text-center">
                <img src="<?= base_url('assets/template/images/03.jpg')?>" alt="" class="mode dark-img img-fluid btn-border p-0 flex-column active" data-setting="dir-mode" data-name="dir" data-value="rtl" height="70" width="100">
                <img src="<?= base_url('assets/template/images/03.jpg')?>" alt="" class="mode light-img img-fluid btn-border p-0 flex-column" data-setting="dir-mode" data-name="dir" data-value="rtl" height="70" width="100">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

<!-- flash message div -->
<div class="print-message">
	<?php print_message($this); ?>
</div>
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
  }
</script>
<!--flash message script-->


<!--Remove alert message script-->
<script>
  // Using jQuery to fade and remove the alert after 5 seconds
		$(document).ready(function() {
		  setInterval(function() {
		    $(".print-message .alert").each(function() {
		      const $this = $(this);
		      if (!$this.data('polled')) {
		        $this.data('polled', true);
		        window.setTimeout(function() {
		          $this.fadeTo(500, 0).slideUp(500, function() {
		            $(this).remove();
		          });
		        }, 5000);
		      }
		    });
		  }, 500);
		});
		
		try {
		  if (typeof redirectUrl !== "undefined" && redirectUrl !== "") {
		    window.setTimeout(function() {
		      window.location.href = redirectUrl;
		    }, 4000);
		  }
		} catch (error) {
		  console.error("An error occurred: ", error);
		}
</script>
<!--Remove alert message script-->




<!--Remember last tab script-->
<script>
    $(document).ready(function(){
        $('a[data-bs-toggle="tab"]').on('show.bs.tab', function(e) {
            localStorage.setItem('activeTab', $(e.target).attr('href'));
        });
        var activeTab = localStorage.getItem('activeTab');
        if(activeTab){
            $('#TabsNav a[href="' + activeTab + '"]').tab('show');
        }
    });
</script>
<!--Remember last tab script-->


<script type="text/javascript">
	$(".select2-input").select2();
</script>

  <script src="<?= base_url('assets/template/js/external.min.js') ?>"></script>
  <script src="<?= base_url('assets/template/js/dashboard.js') ?>"></script>
  <script src="<?= base_url('assets/template/js/setting.js') ?>"></script>
  <script src="<?= base_url('assets/template/js/vendor.js') ?>"></script>
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
	<script src="<?= base_url('assets/js/jssocials-1.4.0/jssocials.min.js'); ?>"></script>
	<script src="<?= base_url('assets/template/js/app.js'); ?>"></script>

<!--summNote js files-->
<script src="<?= base_url('assets/template/summernote/summernote-lite.min.js'); ?>"></script>
<!--summNote js files-->


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

<!--Summernote - Call the toggleStyle function directly to execute-->
<script>
  function toggleStyle() {
    var styleEle = $("style#fixed");
    if (styleEle.length == 0) {
      $("<style id=\"fixed\">.note-editor .dropdown-toggle::after { all: unset; } .note-editor .note-dropdown-menu { box-sizing: content-box; } .note-editor .note-modal-footer { box-sizing: content-box; }</style>")
        .prependTo("body");
    } else {
      styleEle.remove();
    }
  }
  toggleStyle();
</script>
<!--Call the toggleStyle function directly to execute-->
	

<!--Enable tooltips everywhere-->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
<!--Enable tooltips everywhere-->

<script type="text/javascript">
	$(document).delegate('[name="slug"]','keyup',function(){
		var slug = $(this).val();
		var base_url = "<?= base_url() ?>";
		$('#slugtting form').find('.slug-url input').val(base_url+slug);
	});

	$(document).delegate('.btn-model-slug,.dashboard-model-slug', 'click', function(){
		$form = $('#slugtting form');
		$form[0].reset();
		$form.find(".alert").remove();
		$form.find(".has-error").removeClass("has-error");
		$form.find("span.text-danger").remove();

		var type = $(this).attr('data-type');
		var related_id = $(this).attr('data-related-id');
		var target = $(this).attr('data-input-class');

		$this = $(this);
		$.ajax({
			url:"<?php echo base_url('usercontrol/get_slug') ?>",
			type:'POST',
			dataType:'json',
			data:{
				type: type,
				related_id: related_id
			},
			success:function(json){

				$form.find('.slug-url').hide();
				$form.find('.btn-delete-slug').hide();

				if(json['success']){
					$form.find('.slug-url a').attr('copyToClipboard',json.slug_url);
					$form.find('.slug-url input').val(json.slug_url);
					$form.find('.slug-url').show();
					$form.find('.btn-delete-slug').show();
					$('#slugtting').find('[name="slug"]').val(json['slug']);
				}

				$('#slugtting').find('[name="type"]').val(type);
				$('#slugtting').find('[name="related_id"]').val(related_id);
				$('#slugtting').find('[name="target"]').val(target);

				$('#slugtting').modal('show',{'keyboard':false} );
			},
		})
	});
	$('#slugtting').delegate('form', 'submit', function(e){
		e.preventDefault();

		$this = $(this);
		$target = $this.find('[name="target"]').val();

		$.ajax({
			url:$this.attr('action'),
			type:'POST',
			dataType:'json',
			data:$this.serialize(),
			success:function(json){
				$container = $this;
				$container.find(".has-error").removeClass("has-error");
				$container.find("span.text-danger").remove();
				$container.find(".alert").remove();

				if(json['errors']){
					$.each(json['errors'], function(i,j){
						$ele = $container.find('[name="'+ i +'"]');
						if($ele){
							$ele.parents(".form-group").addClass("has-error");
							$ele.after("<span class='text-danger'>"+ j +"</span>");
						}
					})
					$this.find('.slug-url').hide();
				}

				if(json['error']){
					$this.find('.modal-body').prepend('<div class="alert bg-danger text-white">'+json['error']+'</div>');
				}

				if(json['success']){
					$.each($('.'+$target), function(k,v){
						if($(v).attr('data-addition-url')){
							var addition_url = $(v).attr('data-addition-url');
							$(v).val(json.slug_url + addition_url);
							$(v).next('[copyToClipboard]').attr('copyToClipboard', json.slug_url + addition_url);
						}else{
							$(v).val(json.slug_url);
							$(v).next('[copyToClipboard]').attr('copyToClipboard', json.slug_url);
						}
					});

					$this.find('.slug-url').show();
					$this.find('.slug-url a').attr('copyToClipboard',json.slug_url);
					$this.find('.slug-url input').val(json.slug_url);
					$this.find('.modal-body').prepend('<div class="alert alert-success">'+json['success']+'</div>');
				}
			},
		})
	});

	$('#slugtting').delegate('.btn-delete-slug', 'click', function(){
		if(!confirm('<?= __('user.are_you_sure') ?>')) return false;

		$this = $('#slugtting form');
		$this_btn = $(this);
		$target = $this.find('[name="target"]').val();

		$.ajax({
			url: '<?php echo base_url('/usercontrol/delete_slug') ?>',
			type:'POST',
			dataType:'json',
			data:$this.serialize(),
			success:function(json){
				$container = $this;
				$container.find(".alert").remove();

				if(json['error']){
					$this.find('.modal-body').prepend('<div class="alert alert-danger">'+json['error']+'</div>');
				}

				if(json['success']){
					$.each($('.'+$target), function(k,v){
						if($(v).attr('data-addition-url')){
							var addition_url = $(v).attr('data-addition-url');
							$(v).val(json.url + addition_url);
							$(v).next('[copyToClipboard]').attr('copyToClipboard', json.url + addition_url);
						}else{
							$(v).val(json.url);
							$(v).next('[copyToClipboard]').attr('copyToClipboard', json.url);
						}
					});

					$this.find('.slug-url').hide();
					$this.find('.slug-url input').val(json.url);
					$this.find('[name="slug"]').val('');
					$this_btn.hide();
					$this.find('.modal-body').prepend('<div class="alert alert-success">'+json['success']+'</div>');
					setTimeout(function(){
						$('#slugtting').modal('hide');
					}, 2000);
				}
			},
		})
	});
</script>

<script type="text/javascript">
	let leftHeight = $(".left-menu").height();
	let navbarHeight = $(".dashboard-navbar").height();
	let errorHeight = $(".server-errors").height();
	let footerHeight = $(".dashboard-footer").height();
	let elTotalheight = navbarHeight + errorHeight + footerHeight;
	let contentHeight = leftHeight - elTotalheight + 146;
	$(".content-wrapper").css('min-height',contentHeight);


	$(document).delegate(".only-number-allow","keypress",function (e) {
		if (e.which != 46 && e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
			return false;
		}
	});

	function readURL(input,placeholder) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function(e) {
				$(placeholder).attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	function sumNote(element){

		var height = $(element).attr("data-height") ? $(element).attr("data-height") : 500;
		$(element).summernote({
			disableDragAndDrop: true,
			height: height,
			toolbar: [
			['style', ['style']],
			['font', ['bold', 'underline', 'clear']],
			['fontname', ['fontname']],
			['color', ['color']],
			['para', ['ul', 'ol', 'paragraph']],
			['table', ['table']],
			['insert', ['link', 'image', 'video']],
			['view', ['fullscreen', 'codeview', 'help']]
			],
			buttons: {
				image: function() {
					var ui = $.summernote.ui;
                    // create button
                    var button = ui.button({
                    	contents: '<i class="fa fa-image" />',
                    	tooltip: false,
                    	click: function () {
                    		$('#modal-image').remove();

                    		$.ajax({
                    			url: '<?= base_url("filemanager") ?>',
                    			dataType: 'html',
                    			beforeSend: function() {
                    			},complete: function() {
                    			},success: function(html) {
                    				$('body').append('<div id="modal-image" class="modal fade">' + html + '</div>');
                    				$('#modal-image').modal('show');
                    				$('#modal-image').delegate('.image-box .thumbnail','click', function(e) {
                    					e.preventDefault();
                    					$(element).summernote('insertImage', $(this).attr('href'));
                    					$('#modal-image').modal('hide');
                    				});
                    			}
                    		});                     
                    	}
                    });

                    return button.render();
                }
            }
        });
	}
	
	$(document).delegate(".view-all",'click',function(){
		var data = $(this).find("span").html();
		var html = '<table class="table table-hover">';
		data = JSON.parse(data);
		html += '<tr>';
		html += '	<th>IP</th>';
		html += '	<th width="30px">Country</th>';
		html += '</tr>';

		$.each(data, function(i,j){
			html += '<tr>';
			html += '	<td>'+ j['ip'] +'</td>';
			html += '	<td><img style="width: 20px;" src="<?= base_url('assets/vertical/assets/images/flags/') ?>'+ j['country_code'].toLowerCase() +'.png" ></td>';
			html += '</tr>';
		})
		html += '</table>';

		$("#ip-flag_model").modal("show");
		$("#ip-flag_model .modal-body").html(html);
	})
	$(document).delegate(".copy-input input",'click', function(){
		$(this).select();
	})
	$(document).delegate('[copyToClipboard]',"click", function(){

		$this = $(this);
		var $temp = $("<input>");
		$("body").append($temp);
		$temp.val($(this).attr('copyToClipboard')).select();
		
		var copyText=$(this).attr('copyToClipboard');
		
		document.addEventListener('copy', function(e) {

		      e.clipboardData.setData('text/plain', copyText);
		      e.preventDefault();
		 }, true);
		
		document.execCommand("copy");
		$temp.remove();
		$this.tooltip('hide').attr('data-original-title','<?= __('user.copied') ?>').tooltip('show');
		setTimeout(function() { 
			$this.tooltip('hide');
			if(typeof($this.attr('aria-describedby')) != "undefined" && $this.attr('aria-describedby') !== null) {
				$this.click();
			}
			 }, 500);
	});
	
	/* BEGIN SVG WEATHER ICON */  
	if (typeof Skycons !== 'undefined'){
		var icons = new Skycons(
			{"color": "#fff"},
			{"resizeClear": true}
			),
		list  = [
		"clear-day", "clear-night", "partly-cloudy-day",
		"partly-cloudy-night", "cloudy", "rain", "sleet", "snow", "wind",
		"fog"
		],
		i;
		
		for(i = list.length; i--; )
			icons.set(list[i], list[i]);
		icons.play();
	};
	
	// scroll
	$( document ).ready(function() {
		if($("#boxscroll").length > 0){
			$("#boxscroll").niceScroll({cursorborder:"",cursorcolor:"#cecece",boxzoom:true});
		}
		if($("#boxscroll2").length > 0){
			$("#boxscroll2").niceScroll({cursorborder:"",cursorcolor:"#cecece",boxzoom:true}); 
		}
	});
	
	function shownofication(id,url){
		$.ajax({
			type: "POST",
			url: "<?php echo base_url();?>usercontrol/updatenotify",
			data:{'id':id},
			dataType:'json',
			success: function(data){
				window.location.href=data['location'];
			}
		});
	}
</script>
   <script>
	function start_plan_expiration_timer() {
		if($('span[data-time-remains]').length) {
			let countdown = $('span[data-time-remains]').data('time-remains');
			if(countdown > 0) {
				 
				var d = new Date();
				d.setTime(countdown);
				Window.GlobaleCountDownDate = d;

				var GlobaleCountDownDateInterval = setInterval(function() {
				 
					var distance = Window.GlobaleCountDownDate--;

					var days        = Math.floor(distance/24/60/60);
					var hoursLeft   = Math.floor((distance) - (days*86400));
					var hours       = Math.floor(hoursLeft/3600);
					var minutesLeft = Math.floor((hoursLeft) - (hours*3600));
					var minutes     = Math.floor(minutesLeft/60);
					var remainingSeconds = distance % 60;

					let string = "";
 

					string += (days > 0) ? (''+days)+" days " : ""; 

					string += (hours > 0) ? ('0'+hours).slice(-2)+" Hours " : ""; 

					string += (minutes > 0) ? ('0'+minutes).slice(-2)+" Minutes " : ""; 

					string += (remainingSeconds > 0) ? ('0'+remainingSeconds).slice(-2)+" Seconds " : "00 Seconds"; 

					$('span[data-time-remains]').text(string);
					if (distance <= 0) {
						$('span[data-time-remains]').text('Plan Has Expired');
						window.location.reload();
						clearInterval(GlobaleCountDownDateInterval);
					}
				}, 1000);
			}
		}	
	}
</script>

<!--Auto scroll menu-->
<script type="text/javascript">
$(document).ready(function() {
  let activeMenuItem = $('.sidebar-list ul > li > .sub-nav a.active');
  if (activeMenuItem.length > 0) {
    activeMenuItem.parents('.sub-nav').addClass('show');
    $('.sidebar-thumb').animate({
      scrollTop: activeMenuItem.offset().top - ($('.sidebar-thumb').height() / 2) + (activeMenuItem.outerHeight() / 2)
    }, 1800);
  }

  $('.sidebar-list .nav-link').on('click', function() {
    if ($(this).parent().hasClass('sub-nav')) {
      $(this).parents('.sub-nav').addClass('show');
    }
    $('.sidebar-thumb').animate({
      scrollTop: $(this).offset().top - ($('.sidebar-thumb').height() / 2) + ($(this).outerHeight() / 2)
    }, 1800);
  });

  let sidebarHeight = $('.sidebar').height();
  let windowHeight = $(window).height();
  if (sidebarHeight > windowHeight) {
    $('.scrollbar-track-y').css('display', 'block');
  }
});
</script>
<!--Auto scroll menu-->


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
        window.location.href = "<?php echo base_url('usercontrol/logout'); ?>";
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
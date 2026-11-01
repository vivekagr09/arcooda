<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
   <meta name="description" content=""/>
   <meta name="author" content=""/>
   <?php  if(isset($meta_title)){ ?> 
      <meta property="og:title" content="<?php echo $meta_title ?>"/>
   <?php } ?>
   <?php if(isset($meta_description)){ ?> 
      <meta property="og:description" content="<?php echo $meta_description ?>"/>
   <?php } ?>
   <?php if(isset($meta_image)){ ?> 
      <meta property="og:image" content="<?php echo $meta_image ?>"/>
   <?php } ?>
   <?php 
   $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
   ?>
   <meta property="og:url" content="<?= $actual_link ?>"/>
   <meta name="twitter:card" content="summary_large_image"/>
   <?php if($store_setting['favicon']){ ?>
      <link rel="icon" href="<?= base_url('assets/images/site/'.$store_setting['favicon']) ?>" type="image/*" sizes="16x16">
   <?php } ?>
   <title><?= $store_setting['name'] ?>  <?= isset($meta_title) ? '- ' . $meta_title : '' ?></title>
   <link rel="stylesheet" href="<?= base_url('assets/store/lms/')?>css/bootstrap.min.css" />
   <link rel="stylesheet" href="<?= base_url('assets/store/lms/')?>css/style.css" />
   <link rel="stylesheet" href="<?= base_url('assets/store/lms/')?>css/responsive.css" />
   <link rel="stylesheet" href="<?= base_url('assets/store/lms/')?>css/all.min.css">
   <link rel="stylesheet" href="<?= base_url('assets/store/lms/')?>css/darkmode.css">
   <style type="text/css">
     .course-list-item, .course-list-item * {
      cursor: pointer;
     }

      .list-group-item >  a {
         font-size: 10px !important;
         padding: 0px !important;
      }
   </style>

   <?php if (is_rtl()) { ?>
      <!-- place here your RTL css code -->
      <link rel="stylesheet" href="<?= base_url('assets/store/lms/'); ?>css/rtl.css?v=<?= av() ?>" />
   <?php } ?>

</head>
<body>
   <!--HEADER-->
   

   <header class="header">
      <nav class="navbar navbar-expand-md navbar-dark">
         <div class="container-fluid">
               <?php  $logo = ($store_setting['logo']) ? base_url('assets/images/site/'.$store_setting['logo']) : base_url('assets/store/default/').'img/logo.png'; ?>

               <div class="logo-head">
                <a href="<?=base_url('store');?>"><img src="<?= $logo; ?>"></a>
               </div>
            <div class="get-started course-name">
               <a href="<?php echo base_url("store/". base64_encode($user_id) . "/product/". $products[0]['product_slug']);?>"><?= $products[0]['product_name'] ?></a>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
               <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
               <div class="right-design">
                  <label class="switch">
                     <input type="checkbox">
                     <span class="slider round"></span>
                  </label>
                  <form class="d-flex">
                     <input class="form-control me-2" type="search" placeholder="Search" id="search" aria-label="Search">
                  </form>
                  <div class="get-started">
                     <a href="#" id="totalPrgorsss"> 0%</a>
                  </div>
            
                  <?php if(isset($languages) && isset($languages['SelectedLanguage'])) { ?>
                  <div class="dropdown mx-3">
                    <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                     <img class="mx-2" src="<?= base_url($languages['SelectedLanguageFlag']); ?>" width="20" height="20"><?= $languages['SelectedLanguage']; ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <?php foreach ($languages['LanguageHtml'] as $lang) { ?>
                          <li>
                           <a class="dropdown-item" href="<?= $lang['href']; ?>"><img class="mx-2" src="<?= base_url($lang['flag']); ?>" width="20" height="20"><?= $lang['name']; ?></a>
                          </li>
                        <?php } ?>
                    </ul>
                  </div>
                  <?php } ?>

                  <div class="login-tab">
                    <a href="<?=base_url('store/logout')?>" onclick="return confirm('<?= __('store.are_sure_logout')?>')"><i class="fa fa-lock" aria-hidden="true"></i></a>
                 </div>
              </div>
           </div>
         </div>
      </nav>
  </header>



  <!--VIDEO PART AND REVIEW-->
  <div class="main-part">
   <div class="row">
     <section class="about-wrapper ">
      <div id="mySidebar" class="sidebar">
       <a href="javascript:void(0)" class="closebtn" id="closebtn" onclick="closeNav()">
         <?= __('store.close')?>
      </a>

       <!-- start sidebar code -->

       <div class="scrollbar" id="style-3">
         <div class="force-overflow">
            <div class="progress_bar">
               <div class="pro-bar">
                  <h6 class="progress_bar_title">
                     <?= __('store.course_progress');?>
                  </h6>
                  <span class="progress-bar-inner" style="background-color: #06979d; width: 3%;" data-value="3" data-percentage-value="3"></span>
               </div>
            </div>
            <h5 class="py-2"><?= __('store.video_playlist');?></h5>
            <div class="accordion sidebar-accordian" id="accordionExample">
               <?php $vcount= 0;$sectionInc= 0; foreach ($products as $key => $product) {  
                  foreach ($product['downloadable_files'] as $sectionKey => $sectionValue){ ?>

                     <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?=$sectionInc?>">
                           <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?=$sectionInc?>" aria-expanded="true" aria-controls="collapse<?=$sectionInc?>">
                              <?= $sectionValue['title']; ?>
                           </button>
                        </h2>
                        <div id="collapse<?=$sectionInc?>" class="accordion-collapse collapse <?php echo $sectionInc==0 ? 'show':''?>" aria-labelledby="heading<?=$sectionInc?>" data-bs-parent="#accordionExample">
                           <div class="checkbody">

                              <?php foreach ($sectionValue['data'] as $InnerListkey => $InnerListvalue) { 

                                 $Title =$type=$video_id="";
                                 if ($product['product_type'] =='video'){ 
                                    $type = 'video';
                                    $video_id= $InnerListvalue['mask'];

                                 }
                                 if ($product['product_type'] =='videolink') {
                                    $link = determineVideoUrlType($InnerListvalue['mask']); 
                                    if($link['video_type']=='youtube') {
                                       $type = $link['video_type'];
                                       $video_id = $link['video_id'];

                                    } else {

                                       $video_id = $link['video_type'] =='vimeo' ?  $link[0]['id'] : $InnerListvalue['mask'];
                                       $type = $link['video_type'] =='vimeo' ? "vimeo" : "none";
                                    } 
                                 }

                                 if ($video_id) { ?>

                                    <?php 

                                       if($videoStatus[$InnerListvalue['name']]['duration'] > $InnerListvalue['duration']) {
                                          $videoStatus[$InnerListvalue['name']]['isWatched'] = 1;
                                       }

                                    ?>
                                    <div class="list-group mx-0 mb-3 w-auto">
                                       <div class="course-list-item">
                                          <div class="classleft"><input class="form-check-input flex-shrink-0 videocheck" type="checkbox" value="" data-value="<?=$InnerListvalue['name']?>" <?=$videoStatus[$InnerListvalue['name']]['isWatched']==1 ? 'checked="checked"':''; ?>> </div>
                                          <div class="contentright playvideo"  data-type="<?=$type?>" data-value="<?=$video_id?>" data-title=" <?=$InnerListvalue['videotext']; ?>" data-id="<?=$InnerListvalue['name']?>" data-duration="<?=$videoStatus[$InnerListvalue['name']]['duration']?>" data-totalDuration="<?=$InnerListvalue['duration']?>" data-isplaying="<?=$videoStatus[$InnerListvalue['name']]['isPlaying']?>"  data-index="<?=$vcount++;?>" data-parent="collapse<?=$sectionInc?>">
                                            <div class="chk">
                                             <label class="list-group-item">
                                                <span>
                                                 <?=$InnerListvalue['videotext']; ?>
                                                 <small class="d-block text-muted"><?=$InnerListvalue['description']??null ?></small>
                                              </span>
                                           </label>
                                        </div>
                                        <div class="time-minutes">
                                          <div class="time">
                                             <div class="mb-3">
                                                <i class="fa fa-play-circle"></i>
                                                <span><?=secToHR($InnerListvalue['duration']); ?></span>
                                             </div>
                                          </div>
                                       </div>
                                    </div>  
                                 </div>
                                 <?php if ($InnerListvalue['zip']['name']){ ?>
                                    <div class="btn-group vedio-detail">
                                       <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                          <?= __('store.lms_resources');?>
                                       </button>
                                       <ul class="dropdown-menu">
                                          <li class="list-group-item dowanloadcon" data-value="<?php echo base_url('store/downloadable_file/'. $InnerListvalue['zip']['name'] . '/' .$InnerListvalue['zip']['mask'])."/".$order_id."?resource=1" ?>"><?=$InnerListvalue['zip']['title']?></li>
                                       </ul>

                                    </div>
                                 <?php } ?>
                              </div>
                           <?php } } ?>


                        </div>
                     </div>
                  </div>

                  <?php $sectionInc++; }  } ?>

               </div>
            </div>
         </div>
      </div>
   </div>

</section>


<!-- End sidebar code -->

<div class="col-lg-12" id="main">
   <button class="openbtn" id="openbtn" onclick="openNav()" style="cursor: pointer;z-index: 999;"><i class="fa fa-share" aria-hidden="true"></i><?= __('store.video_playlist')?><span style="display: none">☰</span></button>  

   <div class="video-part">
      <div class="embed-responsive embed-responsive-16by9" id="video_div">
         <iframe id="video" class="embed-responsive-item"allowfullscreen></iframe>
      </div>
   </div>

   <div class="row  px-5">
      <div class="col-lg-10 ">
         <div class="best mt-2">
            <div class="rating-star">
               <h5><?= __('store.lesson_content');?></h5>
               <div class="your-rating py-2">
                  <a href="#">
                     <i class="fa fa-<?=(int)$products[0]['product_ratting'] >= 1 ? 'star':'star-o'?>"></i>
                     <i class="fa fa-<?=(int)$products[0]['product_ratting'] >= 2 ? 'star':'star-o'?>"></i>
                     <i class="fa fa-<?=(int)$products[0]['product_ratting'] >= 3 ? 'star':'star-o'?>"></i>
                     <i class="fa fa-<?=(int)$products[0]['product_ratting'] >= 4 ? 'star':'star-o'?>"></i>
                     <i class="fa fa-<?=(int)$products[0]['product_ratting'] >= 5 ? 'star':'star-o'?>"></i>
                     <span><?= __('store.rating')?></span>
                  </a>
               </div>
            </div>
         </div>
      </div>
      <div class="col-lg-2">
         <div class="share pull-right">
            <?php $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>
            <a data-social-share data-share-url="<?= $actual_link;?>" href="#"> <?= __('store.share');?> <i class="fa fa-share" aria-hidden="true"></i></a>
         </div>
      </div>
   </div>
   <div class="table-listing my-5  px-5">
      <ul class="nav nav-pills mb-3 justify-content-start" id="pills-tab" role="tablist">
         <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-home-tab1" data-bs-toggle="pill" data-bs-target="#demo1" type="button" role="tab" aria-controls="demo1" aria-selected="true"><?=__('store.product_description');?></button>
         </li>
      </ul>
   </div>
   <div class="tab-content mt-4 mb-5  px-5">
      <div role="tabpanel" class="tab-pane active" id="demo1">
         <div class="serivice-page">
            <div class="content-services"> 
               <p><?=$products[0]['product_description'];?></p>
            </div>
         </div>
      </div>
   </div>

   <footer class="footer">
      <div class="container-fluid">
         <div class="row">
            <div class="col-4 col-md-3">
               <h3>Lorem Ipsum</h3>
               <ul>
                  <li><a href="#">doller</a></li>
                  <li><a href="#">doller</a></li>
                  <li><a href="#">doller</a></li>
                  <li><a href="#">doller</a></li>
                  <li><a href="#">doller</a></li>
               </ul>
            </div>
            <div class="col-4 col-md-3">
               <h3>Lorem Ipsum</h3>
               <ul>
                  <li><a href="#">doller</a></li>
                  <li><a href="#">doller</a></li>
                  <li><a href="#">doller</a></li>
                  <li><a href="#">doller</a></li>
                  <li><a href="#">doller</a></li>
               </ul>
            </div>
            <div class="col-4 col-md-3">
               <h3>Lorem Ipsum</h3>
               <ul>
                  <li><a href="#">doller</a></li>
                  <li><a href="#">doller</a></li>
                  <li><a href="#">doller</a></li>
                  <li><a href="#">doller</a></li>
                  <li><a href="#">doller</a></li>
               </ul>
            </div>
            <div class="col-4 col-md-3">
               <h3>Lorem Ipsum</h3>
               <ul>
                  <li><a href="#">doller</a></li>
                  <li><a href="#">doller</a></li>
                  <li><a href="#">doller</a></li>
                  <li><a href="#">doller</a></li>
                  <li><a href="#">doller</a></li>
               </ul>
            </div>
         </div>

         <hr>
         <div class="copiright">
            <p>© 2022 lms website, Inc.</p>
         </div></div>
      </footer>
   </div>
   <!--SIDEBAR ACCORDEIAN-->
</div>
</div>

<script src="<?=base_url('assets/store/lms/')?>js/jquery.min.js"></script>
<script src="<?=base_url('assets/store/lms/')?>js/bootstrap.bundle.min.js"></script>
<?= $social_share_modal; ?> 

<script>
   var currentVideoTime = 0;
   var orderId = '<?= $order['id']?>';
   $( document ).ready(function() { 
      $("#search").on("keyup", function(e) {
         e.preventDefault()
         var value = $(this).val().toLowerCase();
         $(".playvideo ").filter(function() {
            $(this).parent().parent().parent().toggle($(this).data('title').toLowerCase().indexOf(value) > -1)
         });
      });

      $(window).on('shown.bs.modal', function(){
         $('#social-share-modal').find('.close').addClass('btn modalclose')
      });

      $(document).on('click','.modalclose',function(){
         $("#social-share-modal").modal('hide')
      });

      function progressBar() {
         TotalVideoChcked    = $(".videocheck:checked").length;
         var totalPer = 0;
         var  totalPrgorss = Pervideo*TotalVideoChcked; 
         totalPrgorss = TotalVideo==TotalVideoChcked ? 100 :totalPrgorss
         if(totalPrgorss==0)  totalPrgorss =3;
         totalPrgorss = parseInt(totalPrgorss.toFixed())+parseInt(totalPer);
         var width = totalPrgorss < 3 ? 3 :totalPrgorss;
         $(".progress-bar-inner").attr('data-value',totalPrgorss)
         $(".progress-bar-inner").attr('data-percentage-value',totalPrgorss)
         $(".progress-bar-inner").css({
            'background-color': '#06979d',
            'width':width+'%'
         });

         $("#totalPrgorsss").html(totalPrgorss == 3 ? 'Start': totalPrgorss+'%')
      }


      $(document).on('change', '.videocheck', function(event) {
         var name =  $(this).attr('data-value');
         var action = 0;
         if($(this).is(':checked')) {
            action = 1;
         }

         $.ajax({
            url:'<?= base_url('store/make_complete') ?>',
            type:'POST',
            dataType:'json',
            data: { order_id :orderId,action:action,name:name,duration:0},
            success:function(json){

            },
         });
         progressBar();
      });

      function updateDuration(name, duration, nexttrack='', isPlaying='') {
         try {
            $.ajax({
             url:'<?= base_url('store/make_complete') ?>',
             type:'POST',
             dataType:'json',
             async:false,
             data: { order_id :orderId,action:3,name:name,duration:duration,nexttrack:nexttrack,isPlaying:isPlaying},
                success:function(json){

                },
            });
         } catch (err) {
            console.log(err);
         }
      }

      $( ".slider" ).click(function() { 
         $("body").toggleClass("dark-mode");
      });

      $(window).scroll(function() {
         if ($(document).scrollTop() > 50) {
            $(".sidebar").addClass("topminus");
         } else {
            $(".sidebar").removeClass("topminus");
         }
      });

      $(".dowanloadcon").on('click',function(e){
         e.preventDefault();
         var downaloadcon =  $(this).data('value');
         if(downaloadcon!="")
            window.open(downaloadcon, '_self'); 
      });

      $(document).on('click', '.course-list-item', function(e) {
         if($(e.target).hasClass("course-list-item")) {
            $(this).find('.playvideo').click();
         }
      })

      $(document).on('click',".playvideo", function(e){
         e.preventDefault();
         var type = $(this).data('type');
         var videoId = $(this).data('value');
         var id = $(this).data('id');
         var duration = $(this).data('duration') ?? 0;
         var totalduration = $(this).data('totalduration') ?? 0;

         if(duration > totalduration && $('#accordionExample input[type="checkbox"]:not(:checked)').length > 0) {
            duration = totalduration;
            updateDuration(id, 0);
            $(this).parent().find('.videocheck').click();
            $(this).parent().find('.videocheck').trigger('change');
            playNextVideo();
         } else {
            var $that = $(this);

            if(duration > totalduration) {
               duration = 0;
               updateDuration(id,duration);
            }

            $('.course-list-item').css('background', '');
            $('.course-list-item .list-group-item').css('background', '');
               
            $(this).parent().css('background', '#f9c32c6b');
            $(this).parent().find('.list-group-item').css('background', '#fce6a6');

            $(this).closest('.accordion-item').find('.accordion-collapse.collapse:not(.show)').parent().find('.accordion-button').click();         
            
            if(type=="youtube") {
               $("#video_div").html('<iframe id="videoplayer" class="embed-responsive-item" width="100%" height="730px" src="https://www.youtube.com/embed/'+videoId+'?t=4m42s"></iframe><input id="pause" type="submit" value="pause" /><input id="play" type="submit" value="play" />');
            }

            if(type=="video") {
               var  base_url = '<?=base_url("store/play?track=")?>'+videoId+'&orderId='+orderId;
               var  base_url = '<?=base_url("application/downloads/")?>'+videoId;
               
               $("#video_div").html('<video id="videoplayer" data-id="'+id+'" class="embed-responsive-item" controls preload="auto" src="'+base_url+'" width="100%" controlsList="nodownload"  oncontextmenu="return false;" height="730px" ></video>');

               document.getElementById("videoplayer").addEventListener("loadedmetadata", function() {
                  this.currentTime = duration??0;
               }, false);

               document.getElementById("videoplayer").addEventListener("pause", function() {
                  var id         = $("#videoplayer").data('id');
                  var stopTime   = currentVideoTime.toFixed();
                  updateDuration(id,stopTime);
               }, false);
               
               document.getElementById("videoplayer").addEventListener("ended", function() {
                  var id         = $("#videoplayer").data('id');
                  var stopTime   = currentVideoTime.toFixed();


                  var currentIndex =  parseInt($that.attr('data-index'));

                  let currCheckbox = $(".playvideo").parent().find('.videocheck')[currentIndex];

                  if($(currCheckbox).attr('checked') !== 'checked') {
                     $(currCheckbox).click();
                  }


                  let playvideoLengthIdx = $(".playvideo").length - 1;
                  if(currentIndex == playvideoLengthIdx){
                     $(".playvideo")[0].click();
                  } else {
                     $that.attr('data-isplaying',"0");
                     var cuacording  = $(`[data-index='`+currentIndex+`']`).attr('data-parent');
                     currentIndex++;
                     $(".playvideo")[currentIndex].click();
                     var acording  = $(`[data-index='`+currentIndex+`']`).attr('data-parent');
                     if(cuacording!=acording)
                        $('#'+acording).collapse('toggle');
                     var cdur  =$(`[data-index='`+currentIndex+`']`).attr('data-duration')??0;
                     var nexttrack  = $(`[data-index='`+currentIndex+`']`).attr('data-id');
                     var nexttrack  = $(`[data-index='`+currentIndex+`']`).attr('data-isplaying',"1");
                     updateDuration(id,stopTime,nexttrack);
                  }
               }, false);
               $("#videoplayer").on('timeupdate',function(event){
                currentVideoTime = this.currentTime
                $that.attr('data-duration',currentVideoTime.toFixed());
                progressBar();
               });
            }

            if(type=="vimeo") {
               $("#video_div").html('<iframe id="videoplayer" src="https://player.vimeo.com/video/'+videoId+'" width="100%" height="730px" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen class="embed-responsive-item"></iframe>');
            }

            if(type=="none") {
               $("#video_div").html('<video id="videoplayer" class="embed-responsive-item" controls preload="auto" src="'+videoId+'"  width="100%" controlsList="nodownload"  oncontextmenu="return false;" height="730px" autoplay></video>');
               document.getElementById("videoplayer").addEventListener("loadedmetadata", function() {

                  this.currentTime = duration??0;
               }, false);
               document.getElementById("videoplayer").addEventListener("pause", function() {
                  var id         = $("#videoplayer").data('id');
                  var stopTime   = currentVideoTime.toFixed();
                  updateDuration(id,stopTime);
               }, false);
               document.getElementById("videoplayer").addEventListener("ended", function() {
                  var id         = $("#videoplayer").data('id');
                  var stopTime   = currentVideoTime.toFixed();
               }, false);
               $("#videoplayer").on('ended',function(event){
               });
               $("#videoplayer").on('timeupdate',function(event){
                  currentVideoTime = this.currentTime
                  $that.attr('data-duration',currentVideoTime.toFixed());
                  progressBar();
               });
            }
         }

         try {
            $.ajax({
             url:'<?= base_url('store/continue_last_watch') ?>',
             type:'POST',
             dataType:'json',
             async:false,
             data: { order_id :orderId,video_id:id},
             success:function(json){},
            });
         } catch (err) {
            console.log(err);
         }         
      });

      var TotalVideo          = $(".videocheck").length;
      var Pervideo = (100/TotalVideo).toFixed();
      var TotalVideoChcked    = $(".videocheck:checked").length;
      progressBar();
      playNextVideo();
   });

function openNav() {
   document.getElementById("mySidebar").style.width = "20%";
   document.getElementById("main").style.marginRight = "20%";
   document.getElementById("main").style.width ="calc(100% - 20%)";
   document.getElementById("openbtn").style.display = "none";
   document.getElementById("closebtn").style.display = "block";
   $(".sidebar").show();

}

function closeNav() {
   $(".sidebar").hide();
   document.getElementById("mySidebar").style.width = "0";
   document.getElementById("main").style.marginRight= "0";
   document.getElementById("main").style.width ="100%";
   document.getElementById("openbtn").style.display = "block";
   document.getElementById("closebtn").style.display = "none";

}


function playNextVideo() {
   let videoToPlay = $(".playvideo")[0];
   
   if($('.playvideo[data-isplaying="1"]').length > 0) {
      videoToPlay = $('.playvideo[data-isplaying="1"]');
   }

   $(videoToPlay).click();
}
</script>
</body>
</html>
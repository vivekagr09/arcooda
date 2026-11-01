<?php include(APPPATH.'/views/usercontrol/login/multiple_pages/header.php'); ?>
<?php 
$faq_banner_title = (isset($theme_settings[0])) ? $theme_settings[0]->faq_banner_title : null;
$faq_section_title = (isset($theme_settings[0])) ? $theme_settings[0]->faq_section_title : null;
$faq_section_subtitle = (isset($theme_settings[0])) ? $theme_settings[0]->faq_section_subtitle : null;
$faq_banner_image = (isset($theme_settings[0])) ? $theme_settings[0]->faq_banner_image : null;
?>

<?php 
if ($faq_banner_image != '' || !empty($faq_banner_image)) { 
    $faq_banner =  base_url().'assets/images/theme_images/'.$faq_banner_image;
}else{ 
    $faq_banner =  base_url('assets/login/multiple_pages/img/faq-bg.jpg');
} 
?>
    <!--Hero-->
    <div class="wlc-hero-area inner-hero-area d-flex align-items-center" style="background-image: url(<?= $faq_banner; ?>)">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="wlc-hero-content text-center">
                        <h1><?= (!empty($faq_banner_title)) ? $faq_banner_title : __('front.faq_title');?></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Hero-->
  
    

    <div class="inner-page-area">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="section-title text-center">
                        <i class="fa fa-snowflake-o fa-lg front_theme_text_color" aria-hidden="true"></i>
                        <h2 class="front_theme_text_color"><?= (!empty($faq_section_title)) ? $faq_section_title : "What is Lorem Ipsum?";?></h2>
                        <p><?= (!empty($faq_section_subtitle)) ? $faq_section_subtitle : "Lorem Ipsum is simply dummy text of the printing and typesetting industry.";?></p>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col">
                <div class="accordion faq-accodion" id="accordionFaq">
                  <?php 
                  if(isset($theme_faqs)) {
                    foreach($theme_faqs as $faq) {
                      if($faq->status == 1) {
                    ?>
                    <div class="card">
                      <div class="card-header" id="faq-sec-<?= $faq->faq_id; ?>">
                        <h2 class="mb-0">
                          <button class="btn btn-link btn-block text-left <?= (isset($is_faq_available)) ? "collapsed": ""; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $faq->faq_id; ?>" aria-expanded="<?= (isset($is_faq_available)) ? "false": "true"; ?>" aria-controls="collapse-<?= $faq->faq_id; ?>">
                            <?= (!empty($faq->faq_question)) ? $faq->faq_question : __('front.faq_question_if_not_exist'); ?>?
                            <i class="fa fa-plus"></i>
                          </button>
                        </h2>
                      </div>

                      <div id="collapse-<?= $faq->faq_id; ?>" class="collapse <?= (!isset($is_faq_available)) ? "show": ""; ?>" aria-labelledby="faq-sec-<?= $faq->faq_id; ?>" data-bs-parent="#accordionFaq">
                        <div class="card-body">
                          <?= (!empty($faq->faq_answer)) ? $faq->faq_answer : __('front.faq_answer_if_not_exist'); ?>
                        </div>
                      </div>
                    </div>
                    <?php    
                      $is_faq_available = true;
                      }                
                    } 
                  }
                  ?>
                  
                  <?php
                   if(!isset($is_faq_available)) {
                     ?>

                  <div class="card">
                    <div class="card-header" id="headingTwo">
                      <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                          Where can I get some?
                          <i class="fa fa-plus"></i>
                        </button>
                      </h2>
                    </div>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionFaq">
                      <div class="card-body">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.</div>
                    </div>
                  </div>
                  <div class="card">
                    <div class="card-header" id="headingThree">
                      <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Where can I get some?
                          <i class="fa fa-plus"></i>
                        </button>
                      </h2>
                    </div>
                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-bs-parent="#accordionFaq">
                      <div class="card-body">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.</div>
                    </div>
                  </div>                  
                  <div class="card">
                    <div class="card-header" id="headingFour">
                      <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                         Where can I get some?
                          <i class="fa fa-plus"></i>
                        </button>
                      </h2>
                    </div>
                    <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-bs-parent="#accordionFaq">
                      <div class="card-body">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.</div>
                    </div>
                  </div>                 
                  <div class="card">
                    <div class="card-header" id="headingFive">
                      <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                          Where can I get some?
                          <i class="fa fa-plus"></i>
                        </button>
                      </h2>
                    </div>
                    <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-bs-parent="#accordionFaq">
                      <div class="card-body">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.</div>
                    </div>
                  </div>


                     <?php
                   }
                  ?>

                  
                </div>
                    
                </div>
            </div>
        </div>
    </div>

    
<?php include(APPPATH.'/views/usercontrol/login/multiple_pages/footer.php'); ?>

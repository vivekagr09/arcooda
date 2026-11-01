<?php
  $SiteSetting = $this->Product_model->getSettings('site');
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $SiteSetting['name'] ?></title>
    <?php
      if($SiteSetting['favicon']){
        echo '<link rel="icon" href="'. base_url('assets/images/site/'.$SiteSetting['favicon']) .'" type="image/*" sizes="16x16">';
      }
    ?>
    <meta content="<?= $SiteSetting['meta_description'] ?>" name="description" />

    <meta content="<?= $SiteSetting['meta_author'] ?>" name="author" />

    <meta content="<?= $SiteSetting['meta_keywords'] ?>" name="keywords" />

    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/login/terms/css/') ?>/bootstrap.min.css?v=<?= av() ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/login/terms/css/') ?>/font-awesome.min.css?v=<?= av() ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/login/terms/css/') ?>/icon-font.min.css?v=<?= av() ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/login/terms/css/') ?>/material-design-iconic-font.min.css?v=<?= av() ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/login/terms/css/') ?>/animate.css?v=<?= av() ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/login/terms/css/') ?>/hamburgers.min.css?v=<?= av() ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/login/terms/css/') ?>/animsition.min.css?v=<?= av() ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/login/terms/css/') ?>/select2.min.css?v=<?= av() ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/login/terms/css/') ?>/daterangepicker.css?v=<?= av() ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/login/terms/css/') ?>/util.css?v=<?= av() ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/login/terms/css/') ?>/main.css?v=<?= av() ?>">

    <script src="<?= base_url('assets/js/popper.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/main.js') ?>"></script>

    <style type="text/css">body {padding-top: 5rem;}</style>
  </head>

  <body>

    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">

      <a class="navbar-brand" href="#"><?= $SiteSetting['name'] ?></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item"> <a class="nav-link" href="<?php echo base_url() ?>"><?= __('user.home') ?></a> </li>
          <li class="nav-item active"> <a class="nav-link" href="<?php echo base_url('term-condition') ?>"><?= __('user.terms_condition') ?></a> </li>
        </ul>
      </div>
    </nav>

     <?php if($store['language_status']){ ?>
    <div class="language-changer">  <?= $LanguageHtml ?> </div>
  <?php } ?>
 
    <main role="main" class="container">
        <h1><?= $page['heading'] ?></h1><br>
        <?= $page['content'] ?>

    </main><!-- /.container -->
  </body>
</html>

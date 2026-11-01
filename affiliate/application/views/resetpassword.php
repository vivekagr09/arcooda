<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= __('admin.admin_resetpassword') ?></title>
  <!-- Include layout.php -->
  <?php include(APPPATH.'views/includes/layout.php'); ?>
  <!-- Include layout.php -->
</head>
<body class="bg-light d-flex align-items-center vh-100">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <div class="h6 mb-0"><?= __('admin.admin_resetpassword') ?></div>
            <div class="nav-item dropdown">
              <?= $LanguageHtml ?>
            </div>
          </div>
          <div class="card-body">
            <div class="text-center mb-4">
              <?php 
                $logo = $SiteSetting['admin-side-logo'] ? 
                base_url('assets/images/site/'. $SiteSetting['admin-side-logo']) : 
                base_url('assets/template/images/user-logo.png'); 
              ?>
              <img src="<?= $logo; ?>" alt="logo" class="img-fluid <?= ($SiteSetting['custom_logo_size']) ? 'customLogoClass' : '' ?>">
            </div>
            <form method="post">
              <div class="mb-3">
                <label for="password" class="form-label"><?= __('admin.new_password') ?></label>
                <div class="input-group">
                  <span class="input-group-text"><img src="<?= base_url('assets/admin/'); ?>img/password.png" alt="icon"></span>
                  <input type="password" id="password" name="password" class="form-control" placeholder="New Password" required>
                </div>
              </div>
              <div class="mb-3">
                <label for="conf_password" class="form-label"><?= __('admin.confirm_new_password') ?></label>
                <div class="input-group">
                  <span class="input-group-text"><img src="<?= base_url('assets/admin/'); ?>img/password.png" alt="icon"></span>
                  <input type="password" id="conf_password" name="conf_password" class="form-control" placeholder="Confirm New Password" required>
                </div>
              </div>
              <div class="mb-3">
                <input type="submit" class="btn btn-primary btn-block" value="<?= __('admin.change_password') ?>">
              </div>
              <div class="text-center">
                <a href="<?= $redirect_url; ?>" class="text-muted"><?= __('admin.cancel') ?></a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
<?php include_once "header.php"; ?>
<div class="register-content">
  <ul class="nav nav-tabs bg-transparent" role="tablist">
    <li class="nav-item">
      <a class="nav-link" href="<?= base_url() ?>">
        <span><?= __('front.login') ?></span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link active" href="<?= base_url('register') ?>">
        <span><?= __('front.register') ?></span>
      </a>
    </li>
  </ul>
  <p class="mt-4">
    <?= __('front.enter_your_information_to_setup_a_new_account') ?>
  </p>
  <div class="register-form">
    <?= $register_fomm ?>
  </div>
</div>
<?php include_once "footer.php"; ?>
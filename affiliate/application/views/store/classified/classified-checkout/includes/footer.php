<?php
  $db =& get_instance();
  $products = $db->Product_model;
  $cookies_consent = $products->getSettings('site','cookies_consent');
  $cookies_consent_mesag = $products->getSettings('site', 'cookies_consent_mesag');
?>
<?php
include __DIR__ . "/cookies_consent.php";
?>

  <script> const BASE_URL = "<?= base_url(); ?>"; </script>
  <script src="<?= base_url('assets/plugins/') ?>mustache.js"></script>
  <script src="<?= base_url('assets/store/') ?>affclassifiedstore.js"></script>
</body>
</html>
<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$payment_id    = '';
$payment       = array();
$payment_notes = array();

$doadd_url           = $get_vars;
$doadd_url['action'] = 'do_add';
$doadd_url           = '.?' . http_build_query($doadd_url);

$action = 'add';
?>
<form action="<?=$doadd_url?>" method="post" enctype="multipart/form-data">
<?php
include_once 'templates/addedit_payment.tpl.php';
?>
</form>
<?php

include_once __DIR__ . '/footer.inc.php';

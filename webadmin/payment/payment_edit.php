<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$payment_id    = intval($posted['payment_id']);
$portal        = $posted['portal'];
$payment       = paymentFromDatabase($payment_id, $portal);
$payment_notes = paymentNotesFromDatabase($payment_id, $portal);

$doedit_url           = $get_vars;
$doedit_url['action'] = 'do_edit';
$doedit_url           = '.?' . http_build_query($doedit_url);

$action = 'edit';
?>
<form action="<?=$doedit_url?>" method="post" enctype="multipart/form-data">
<?php
include_once 'templates/addedit_payment.tpl.php';
?>
</form>
<?php

include_once __DIR__ . '/footer.inc.php';

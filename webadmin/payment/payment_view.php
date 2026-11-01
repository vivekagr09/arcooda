<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$payment_id = intval($posted['payment_id']);
$payment    = paymentFromDatabase($payment_id);
$action     = 'view';

include __DIR__ . '/templates/view_payment.tpl.php';

include_once __DIR__ . '/footer.inc.php';

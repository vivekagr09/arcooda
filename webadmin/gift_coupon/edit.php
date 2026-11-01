<?php
include_once __DIR__ . "/common.inc.php";

include_once __DIR__ . "/header.inc.php";

$action = 'edit';
$id     = intval($posted['id']);
if (!$id) {
    die('Invalid ID Passed');
}

$giftcoupon      = giftcouponFromDatabase($id);
$giftcoupon_lock = giftcouponGetLock($id);
?>
<h2>Edit Coupon - <?= giftcouponFormatNumber($id) ?></h2>
<?php

include __DIR__ . '/addedit.tpl.php';

include_once __DIR__ . "/footer.inc.php";

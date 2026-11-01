<?php
include_once 'voucher_common.inc.php';
include_once 'templates/voucher_header.tpl.php';

$action     = 'edit';
$voucher_id = intval($posted['voucher_id']);
if (!$voucher_id) {
    die('Invalid ID Passed');
}

$voucher      = voucherFromDatabase($voucher_id);
$voucher_lock = voucherGetLock($voucher_id);
?>
<h2>Edit Voucher - <?=voucherFormatNumber($voucher_id)?></h2>
<?php

include 'templates/addedit_voucher.tpl.php';

include_once 'templates/voucher_footer.tpl.php';

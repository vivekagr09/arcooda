<?php
include_once 'voucher_common.inc.php';
include_once 'templates/voucher_header.tpl.php';

$action     = 'add';
$voucher_id = 0;
$voucher    = array();
?>
<h2>Add New Voucher</h2>
<?php

include 'templates/addedit_voucher.tpl.php';

include_once 'templates/voucher_footer.tpl.php';

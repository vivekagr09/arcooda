<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action    = 'view';
$supplier_id = intval($posted['supplier_id']);
$staff_id  = $_GLOBALS['staff']['id'];

$supplier_comments = supplierCommentSearch(array('supplier_id' => $supplier_id, 'comment_type' => 'comment'));

?>
<h2>Supplier Comments <?=supplierFormatNumber($supplier_id)?> - <?=date('d/m/Y', time())?></h2>
<?php

include __DIR__ . '/comments.tpl.php';

include_once __DIR__ . '/footer.inc.php';

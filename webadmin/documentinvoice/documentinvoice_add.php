<?php
include_once __DIR__ . '/common.inc.php';

trigger_error('Obsolete file');
die();

include_once __DIR__ . '/header.inc.php';

$action      = 'add';
$dinvoice_id = 0;

$sinvoice                = array('invoice_status' => 'proforma');
$sinvoice_products       = array();
$sinvoice_charges        = array();
$sinvoice_history        = array();
$sinvoice_courier_quotes = array();
$sinvoice_payments       = array();

if ($posted['site_id'] > 0) {
    $invoice['site_id'] = intval($posted['site_id']);
}

?>
<h2>Add New Document Invoice</h2>
<?php

include __DIR__ . '/templates/addedit_documentinvoice.tpl.php';

include_once __DIR__ . '/footer.inc.php';

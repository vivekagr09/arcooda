<?php
include_once __DIR__ . '/common.inc.php';

$action     = 'view';
$invoice_id = intval($posted['invoice_id']);

$invoice          = invoiceFromDatabase($invoice_id);
$invoice_products = invoiceProductsFromDatabase($invoice_id);

//Make it a purchase summary
$invoice['invoice_status'] = 'purchase';

$invoice_buffer = 0;
include __DIR__ . '/templates/jpeg_invoice.tpl.php';
if ($size_failure) {
    include __DIR__ . '/templates/pdf_invoice.tpl.php';
    if (!headers_sent()) {
        header('Content-Type: image/gif');
    }

    echo $invoice_buffer;
} else {
    header('Content-Type: image/gif');
    imagegif($invoice_buffer);
}
die();

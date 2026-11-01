<?php
include_once __DIR__ . '/common.inc.php';

$action     = 'shipview';
$invoice_id = intval($posted['invoice_id']);

$invoice          = invoiceFromDatabase($invoice_id);
$invoice_products = invoiceProductsFromDatabase($invoice_id);
$invoice_charges  = invoiceChargesFromDatabase($invoice_id);
$invoice_time = 0;
if (array_key_exists('invoice_time', $_GET)) {
    $invoice_time = intval($_GET['invoice_time']);
}

foreach ($invoice_products as $key => $product) {
    unset($invoice_products[$key]['product_notes']);
}

foreach ($invoice_charges as $key => $product) {
    unset($invoice_charges[$key]['charge_notes']);
}

unset($invoice['ship_product_notes']);

unset($invoice['bill_address_full']);
if ('' == $invoice['notify_address_full']) {
    $invoice['notify_address_full'] = $invoice['ship_address_full'];
}
unset($invoice['mark_paid_on_invoice']);
$DB->query(sprintf(
    "UPDATE `highwaygroup`.`invoices` SET `access_date` = NOW() WHERE `invoice_id` = %d LIMIT 1",
    $invoice_id
));

if ('' != $posted['no_sig']) {
    $staff_signature = false;
}

$invoice_buffer = 0;
include __DIR__ . '/templates/jpeg_invoice.tpl.php';

if ($size_failure || 'pdf' == $posted['format']) {
    if (function_exists('imagedestroy')) {
        imagedestroy($invoice_buffer);
    }
    $invoice_buffer = false;
    if (!headers_sent()) {
        header('Content-Type: application/pdf');
    }

    include __DIR__ . '/templates/pdf_invoice.tpl.php';
    echo $invoice_buffer;
} else {
    header('Content-Type: image/gif');
    imagegif($invoice_buffer);
}

die();

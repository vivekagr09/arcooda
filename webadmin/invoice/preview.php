<?php
include_once __DIR__ . '/common.inc.php';

$action     = 'preview';
$invoice_id = intval($posted['invoice_id']);

$invoice          = invoiceFromPost($posted);
$invoice_products = invoiceProductsFromPost($posted);
$invoice_charges  = invoiceChargesFromPost($posted);

if (0 != $posted['invoice_time']) {
    $invoice_time = intval($posted['invoice_time']);
}

$invoice_buffer = 0;
include __DIR__ . '/templates/jpeg_invoice.tpl.php';

if (!$size_failure && 'pdf' == $posted['view']) {
    $size_failure = true;
}

if ($size_failure) {
    if (function_exists('imagedestroy')) {
        imagedestroy($invoice_buffer);
    }
    $invoice_buffer = 0;

    if ('true' == $posted['tcpdf']) {
        include __DIR__ . '/templates/pdf_invoice2.tpl.php';
    } else {
        include __DIR__ . '/templates/pdf_invoice.tpl.php';
    }

    if (!headers_sent()) {
        header('Content-Type: application/pdf');
    }

    echo $invoice_buffer;
} else {
    header('Content-Type: image/gif');
    imagegif($invoice_buffer);
}

die();

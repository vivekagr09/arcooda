<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceExists

$dinvoice_id = intval($posted['dinvoice_id']);
if (invoiceExists($posted['invoice_id'])) {
    $dinvoice_id = intval($posted['invoice_id']);
}

$staff_id = staffFromUser($_SERVER['REMOTE_USER']);

$action = 'view';

$sinvoice          = documentinvoiceFromPost($posted);
$sinvoice_products = documentinvoiceProductsFromPost($posted);
$sinvoice_charges  = documentinvoiceChargesFromPost($posted);

foreach ($sinvoice_products as $key => $product) {
    unset($sinvoice_products[$key]['product_notes']);
}

$sinvoice_buffer = 0;
if ('pdf' == $posted['format']) {
    include __DIR__ . '/templates/pdf_documentinvoice.tpl.php';
    if (!headers_sent()) {
        header('Content-Type: application/pdf');
    }

    echo $sinvoice_buffer;
    die();
}

include __DIR__ . '/templates/jpeg_documentinvoice.tpl.php';
if ($size_failure) {
    // i.e too big for the gif invoice display the pdf invoice..
    if (function_exists('imagedestroy')) {
        imagedestroy($sinvoice_buffer);
    }
    $sinvoice_buffer = false;
    include __DIR__ . '/templates/pdf_documentinvoice.tpl.php';
    if (!headers_sent()) {
        header('Content-Type: application/pdf');
    }

    echo $sinvoice_buffer;
} else {
    header('Content-Type: image/gif');
    imagegif($sinvoice_buffer);
}

die();

<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceExists

$sinvoice_id = intval($posted['sinvoice_id']);
if (invoiceExists($posted['invoice_id'])) {
    $sinvoice_id = intval($posted['invoice_id']);
}

$staff_id = staffFromUser($_SERVER['REMOTE_USER']);

$action = 'view';

$sinvoice          = shipinvoiceFromPost($posted);
$sinvoice_products = shipinvoiceProductsFromPost($posted);
$sinvoice_charges  = shipinvoiceChargesFromPost($posted);

foreach ($sinvoice_products as $key => $product) {
    unset($sinvoice_products[$key]['product_notes']);
}

$sinvoice_buffer = 0;
if ('pdf' == $posted['format']) {
    include __DIR__ . '/templates/pdf_shipinvoice.tpl.php';
    if (!headers_sent()) {
        header('Content-Type: application/pdf');
    }

    echo $sinvoice_buffer;
    die();
}

include __DIR__ . '/templates/jpeg_shipinvoice.tpl.php';
if ($size_failure) {
    // i.e too big for the gif invoice display the pdf invoice..
    if (function_exists('imagedestroy')) {
        imagedestroy($sinvoice_buffer);
    }
    $sinvoice_buffer = false;
    include __DIR__ . '/templates/pdf_shipinvoice.tpl.php';
    if (!headers_sent()) {
        header('Content-Type: application/pdf');
    }

    echo $sinvoice_buffer;
} else {
    header('Content-Type: image/gif');
    imagegif($sinvoice_buffer);
}

die();

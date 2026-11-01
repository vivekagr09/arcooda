<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceChargesFromDatabase, invoiceProductsFromDatabase, invoiceExists, invoiceFromDatabase

$sinvoice_id = intval($posted['sinvoice_id']);
if (invoiceExists($posted['invoice_id'])) {
    $sinvoice_id = intval($posted['invoice_id']);
}

$sinvoice_time = 0;
if (array_key_exists('invoice_time', $_GET)) {
    $sinvoice_time = intval($_GET['invoice_time']);
}

$staff_id = staffFromUser($_SERVER['REMOTE_USER']);

$action = 'view';

if (shipinvoiceExists($sinvoice_id)) {
    $sinvoice          = shipinvoiceFromDatabase($sinvoice_id);
    $sinvoice_products = shipinvoiceProductsFromDatabase($sinvoice_id);
    $sinvoice_charges  = shipinvoiceChargesFromDatabase($sinvoice_id);
} else {
    $sinvoice          = invoiceFromDatabase($sinvoice_id);
    $sinvoice_products = invoiceProductsFromDatabase($sinvoice_id);
    $sinvoice_charges  = invoiceChargesFromDatabase($sinvoice_id);
}

foreach ($sinvoice_products as $key => $product) {
    unset($sinvoice_products[$key]['product_notes']);
}

if ($sinvoice_id > 0) {
    $sinvoice_id = $sinvoice_id;
}

// if (0 != $posted['sinvoice_time']) {
//     $sinvoice_time = intval($posted['sinvoice_time']);
// }

$sinvoice_buffer = 0;
//include __DIR__ . '/templates/jpeg_invoice.tpl.php';
if ('pdf' == $posted['format']) {
    include __DIR__ . '/templates/pdf_shipinvoice.tpl.php';
    if (!headers_sent()) {
        header('Content-Type: application/pdf');
    }

    echo $sinvoice_buffer;
    die();
}
if ('mhtml' == $posted['format']) {
    header('Content-Type: text/html');
    include __DIR__ . '/templates/mhtml_shipinvoice.tpl.php';
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

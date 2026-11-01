<?php
include_once __DIR__ . '/common.inc.php';

//No browser-side caching
header("Cache-Control: no-cache, no-store, must-revalidate", true); // HTTP 1.1.
header("Pragma: no-cache", true); // HTTP 1.0.
header("Expires: 0", true); // Proxies.

$action     = 'view';
$invoice_id = intval($posted['order_id']);

$invoice          = purchaseOrderFromDatabase($invoice_id);
$invoice_products = purchaseOrderProductsFromDatabase($invoice_id);
$invoice_charges  = purchaseOrderChargesFromDatabase($invoice_id);
if (0 != $posted['invoice_time']) {
    $invoice_time = intval($posted['invoice_time']);
}

//if ($invoice_id == '3585' || $invoice_id == '3582') $invoice['ship_address_full'] = ' ';

$DB->query("UPDATE `purchase_order` SET `access_date` = NOW() WHERE `invoice_id` = " . $invoice_id . " LIMIT 1");

if ($invoice['site_id'] <= 0) {
    $invoice['site_id'] = 3;
}

$force_images = array();
if ("true" == $posted["no_sig"]) {
    $force_signature = $force_images["signature"] = "";
}

if ("true" == $posted["no_stamp"]) {
    $force_images["stamp"] = "";
}

$tax_view = false;
if (1 == $posted["tax_view"]) {
    $tax_view = true;
}

$invoice_buffer = 0;

switch ($posted['view']) {
    case 'eml':
        include __DIR__ . '/templates/setup_invoice.inc.php';
        include __DIR__ . '/templates/mhtml_invoice.tpl.php';
        header('Content-Type: application/email');
        header('Content-Disposition: inline; filename="I' . $invoice_id . '.eml"');
        echo $invoice_buffer;
        break;

    case 'html':
        include __DIR__ . '/templates/setup_invoice.inc.php';
        include __DIR__ . '/templates/html_invoice.tpl.php';
        echo $invoice_buffer;
        break;

    case 'gif':
        include __DIR__ . '/templates/jpeg_invoice.tpl.php';
        header('Content-Type: image/gif');
        imagegif($invoice_buffer);
        break;

    case 'jpg':
    case 'jpeg':
        include __DIR__ . '/templates/jpeg_invoice.tpl.php';
        header('Content-Type: image/jpeg');
        imagejpeg($invoice_buffer);
        break;

    case 'png':
        include __DIR__ . '/templates/jpeg_invoice.tpl.php';
        header('Content-Type: image/png');
        imagepng($invoice_buffer);
        break;

    default:
    case 'pdf':
        if ('true' == $posted['tcpdf']) {
            include __DIR__ . '/templates/tcpdf_invoice.tpl.php';
        } elseif ('true' == $posted['testing']) {
            include __DIR__ . '/templates/pdf_invoice_coe.tpl.php';
        } else {
            include __DIR__ . '/templates/pdf_invoice.tpl.php';
        }

        if (!headers_sent()) {
            header('Content-Type: application/pdf');
        }

        echo $invoice_buffer;
        break;
}
die();

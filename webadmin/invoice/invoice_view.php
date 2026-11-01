<?php
include_once __DIR__ . '/common.inc.php';

//No browser-side caching
header("Cache-Control: no-cache, no-store, must-revalidate", true); // HTTP 1.1.
header("Pragma: no-cache", true); // HTTP 1.0.
header("Expires: 0", true); // Proxies.

$action     = 'view';
$invoice_id = intval($posted['invoice_id']);

$invoice          = invoiceFromDatabase($invoice_id);
$invoice_products = invoiceProductsFromDatabase($invoice_id);
$invoice_charges  = invoiceChargesFromDatabase($invoice_id);
$invoice_gifts    = invoiceGiftsFromDatabasePrint($invoice_id);
$display_product_code = false;
$invoice_time = 0;
if (array_key_exists('invoice_time', $_GET)) {
    $invoice_time = intval($_GET['invoice_time']);
}

if ('3585' == $invoice_id || '3582' == $invoice_id) {
    $invoice['ship_address_full'] = ' ';
}

$DB->query(sprintf(
    "UPDATE `highwaygroup`.`invoices` SET `access_date` = NOW() WHERE `invoice_id` = %d LIMIT 1",
    $invoice_id
));

$force_images    = array();
$force_signature = null;
if (array_key_exists('no_sig', $_GET) && "true" == $_GET["no_sig"]) {
    $force_signature = $force_images["signature"] = "";
}
if (array_key_exists('no_stamp', $_GET) && "true" == $_GET["no_stamp"]) {
    $force_images["stamp"] = "";
}

$tax_view = false;
if (array_key_exists('tax_view', $_GET) && 1 == $_GET["tax_view"]) {
    $tax_view = true;
}

$invoice_buffer = 0;

switch ((array_key_exists('view', $_GET)) ? $_GET['view'] : '') {
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
        //no break
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

    case 'html2':
        include __DIR__ . '/templates/setup_invoice.inc.php';
        include __DIR__ . '/templates/html_invoice2.tpl.php';
        echo $invoice_buffer;
        break;

    case 'auto':
        include __DIR__ . '/templates/jpeg_invoice.tpl.php';
        if (!$size_failure) {
            header('Content-Type: image/gif');
            imagegif($invoice_buffer);
            break;
        }
        if (function_exists('imagedestroy')) {
            imagedestroy($invoice_buffer);
        }
        $invoice_buffer = 0;
        //no break
    default:
        //no break
    case 'pdf':
        if (array_key_exists('tcpdf', $_GET) && 'true' == $_GET['tcpdf']) {
            include __DIR__ . '/templates/pdf_invoice2.tpl.php';
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

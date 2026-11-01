<?php
include_once __DIR__ . '/common.inc.php';

$action     = 'view';
$invoice_id = intval($posted['invoice_id']);

if ('' != $_SERVER['REDIRECT_INVOICE_ID']) {
    $invoice_id = intval($_SERVER['REDIRECT_INVOICE_ID']);
}

$invoice          = invoiceFromDatabase($invoice_id);
$invoice_products = invoiceProductsFromDatabase($invoice_id);
$invoice_charges  = invoiceChargesFromDatabase($invoice_id);

$DB->query("UPDATE `invoices` SET `access_date` = " . formatSQLDateTime($_SERVER['REQUEST_TIME']) . " WHERE `invoice_id` = " . $invoice_id);

if ($invoice['site_id'] <= 0) {
    $invoice['site_id'] = 11;
}

$invoice_buffer = 0;
switch ($_SERVER['REDIRECT_EXTENSION']) {
    case 'jpg':
    case 'jpeg':
        include __DIR__ . '/templates/jpeg_invoice2.tpl.php';
        header('Content-Type: image/jpeg');
        imagejpeg($invoice_buffer);
        break;
    case 'png':
        include __DIR__ . '/templates/jpeg_invoice2.tpl.php';
        header('Content-Type: image/png');
        imagepng($invoice_buffer);
        break;
    default:
    case 'gif':
        include __DIR__ . '/templates/jpeg_invoice2.tpl.php';
        header('Content-Type: image/gif');
        imagegif($invoice_buffer);
        break;
    case 'html':
    case 'htm':
        include __DIR__ . '/templates/html_invoice.tpl.php';
        header('Content-Type: text/html');
        echo $invoice_buffer;
        break;
    case 'pdf':
        include __DIR__ . '/templates/pdf_invoice.tpl.php';
//    header('Content-Type: text/plain');
        //    print_r($pdf);
        //    die();
        header('Content-Type: application/pdf');
        header('Content-Length: ' . strlen($invoice_buffer));
        echo $invoice_buffer;
        break;
    case 'mht':
    case 'mhtml':
        include __DIR__ . '/templates/mhtml_invoice.tpl.php';
        header('Content-Type: text/mhtml');
        echo $invoice_buffer;
        break;
}

die();

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

$DB->query(sprintf(
    "UPDATE `highwaygroup`.`invoices` SET `access_date` = NOW() WHERE `invoice_id` = %d LIMIT 1",
    $invoice_id
));

$invoice_buffer = 0;
switch ($_SERVER['REDIRECT_EXTENSION']) {
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

    default:
        //no break
    case 'gif':
        include __DIR__ . '/templates/jpeg_invoice.tpl.php';
        header('Content-Type: image/gif');
        imagegif($invoice_buffer);
        break;

    case 'html':
        //no break
    case 'htm':
        include __DIR__ . '/templates/html_invoice.tpl.php';
        header('Content-Type: text/html');
        echo $invoice_buffer;
        break;

    case 'pdf':
        include __DIR__ . '/templates/pdf_invoice.tpl.php';
        header('Content-Type: application/pdf');
        //header('Content-Length: '.strlen($invoice_buffer));
        echo $invoice_buffer;
        break;

    case 'mht':
        //no break
    case 'mhtml':
        include __DIR__ . '/templates/mhtml_invoice.tpl.php';
        header('Content-Type: text/mhtml');
        echo $invoice_buffer;
        break;
}

die();

<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceProductsFromDatabase

$action     = 'view';
$invoice_id = intval($posted['invoice_id']);

$invoice          = invoiceFromDatabase($invoice_id);
$invoice_products = invoiceProductsFromDatabase($invoice_id);

if ($invoice['site_id'] <= 0) {
    $invoice['site_id'] = 11;
}

//Make it a purchase summary
$invoice['invoice_status'] = 'purchase';

$invoice_buffer = 0;
//if(count($invoice_products)>3)
{
    include __DIR__ . '/templates/html_invoice.tpl.php';
    echo $invoice_buffer;
}
/*
else
{
include __DIR__ . '/templates/jpeg_invoice2.tpl.php';
header('Content-Type: image/gif');
imagegif($invoice_buffer);
}
 */
die();

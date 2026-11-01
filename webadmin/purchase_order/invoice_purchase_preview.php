<?php
include_once __DIR__ . '/common.inc.php';

$action     = 'view';
$invoice_id = intval($posted['invoice_id']);

$invoice          = invoiceFromPost($posted);
$invoice_products = invoiceProductsFromPost($posted);
$invoice_charges  = invoiceChargesFromPost($posted);
if ($invoice['site_id'] <= 0) {
    $invoice['site_id'] = 3;
}

//Make it a purchase summary
$invoice['invoice_status'] = 'purchase';

if ($invoice['site_id'] <= 0) {
    $invoice['site_id'] = 3;
}

//if($invoice['site_id']==19) $invoice['site_id']=9;

$invoice_buffer = 0;
//if(count($invoice_products)>3)
{
    include __DIR__ . '/templates/html_invoice.tpl.php';
    echo $invoice_buffer;
}
/*else
{
include __DIR__ . '/templates/jpeg_invoice2.tpl.php';
header('Content-Type: image/gif');
imagegif($invoice_buffer);
}
 */
die();

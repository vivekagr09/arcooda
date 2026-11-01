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

if ($invoice['site_id'] <= 0) {
    $invoice['site_id'] = 11;
}

//if($invoice['site_id']==19) $invoice['site_id']=9;

$invoice_buffer = 0;
include __DIR__ . '/templates/jpeg_invoice2.tpl.php';
/*
if(!$size_failure && $posted['view']=='pdf') $size_failure=true;

if($size_failure)
{
imagedestroy($invoice_buffer);
$invoice_buffer=0;

if($posted['tcpdf']=='true')
include __DIR__ . '/templates/pdf_invoice2.tpl.php';
else
include __DIR__ . '/templates/pdf_invoice.tpl.php';
if(!headers_sent()) header('Content-Type: application/pdf');
echo $invoice_buffer;
}
else
{
 */
header('Content-Type: image/gif');
imagegif($invoice_buffer);
/*
}
 */

die();

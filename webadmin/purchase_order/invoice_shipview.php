<?php
include_once __DIR__ . '/common.inc.php';

$action     = 'shipview';
$invoice_id = intval($posted['order_id']);

$invoice          = invoiceFromDatabase($invoice_id);
$invoice_products = invoiceProductsFromDatabase($invoice_id);
$invoice_charges  = invoiceChargesFromDatabase($invoice_id);

foreach ($invoice_products as $key => $product) {
    unset($invoice_products[$key]['product_notes']);
}

foreach ($invoice_charges as $key => $product) {
    unset($invoice_charges[$key]['charge_notes']);
}

if (0 != $posted['invoice_time']) {
    $invoice_time = intval($posted['invoice_time']);
}

unset($invoice['ship_product_notes']);

unset($invoice['bill_address_full']);
if ('' == $invoice['notify_address_full']) {
    $invoice['notify_address_full'] = $invoice['ship_address_full'];
}

$DB->query("UPDATE `invoices` SET `access_date` = " . formatSQLDateTime($_SERVER['REQUEST_TIME']) . " WHERE `invoice_id` = " . $invoice_id);

if ($invoice['site_id'] <= 0) {
    $invoice['site_id'] = 3;
}

if ('' != $posted['no_sig']) {
    $staff_signature = false;
}

$invoice_buffer = 0;
/*
include __DIR__ . '/templates/jpeg_invoice.tpl.php';

if($size_failure || $posted['format']=='pdf')
{
imagedestroy($invoice_buffer);
 */
if (!headers_sent()) {
    header('Content-Type: application/pdf');
}

include __DIR__ . '/templates/pdf_invoice.tpl.php';
echo $invoice_buffer;
/*
}
else
{
header('Content-Type: image/gif');
imagegif($invoice_buffer);
}
 */
die();

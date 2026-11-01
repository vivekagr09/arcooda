<?php
set_time_limit(720);

include_once __DIR__ . '/common.inc.php';

$action = 'view';
foreach (invoiceSearch() as $invoice_id) {
    if ($invoice_id > 3514) {
        $invoice          = invoiceFromDatabase($invoice_id);
        $invoice_products = invoiceProductsFromDatabase($invoice_id);
        $invoice_charges  = invoiceChargesFromDatabase($invoice_id);

        if (0 != $posted['invoice_time']) {
            $invoice_time = intval($posted['invoice_time']);
        }

        if ('3585' == $invoice_id || '3582' == $invoice_id) {
            $invoice['ship_address_full'] = ' ';
        }

        $invoice_buffer = 0;
        include __DIR__ . '/templates/jpeg_invoice.tpl.php';
        if (function_exists('imagedestroy')) {
            imagedestroy($invoice_buffer);
        }
        $invoice_buffer = false;

        echo $invoice_id . " " . (($size_failure) ? 'FAIL' : 'PASS') . "<br />\n";
    }
}
die();

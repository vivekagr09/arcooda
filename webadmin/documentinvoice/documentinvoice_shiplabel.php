<?php
include_once __DIR__ . '/common.inc.php';

$action     = 'shiplabel';
$invoice_id = intval($posted['dinvoice_id']);
$invoice    = documentinvoiceFromDatabase($invoice_id);
//print_array($invoice);
//die();

switch ($posted['label_type']) {
    case 'pieces':
        $filename = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/invoice/shiplabels/shiplabel_large.rtf';
        break;
    default:
        $filename = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/invoice/shiplabels/shiplabel_site_' . $invoice['site_id'] . '.rtf';
        break;
}
$label = file_get_contents($filename);

if ('' != $posted['label_type']) {
    $invoice_products = documentinvoiceProductsFromDatabase($invoice_id);
    $invoice_charges  = documentinvoiceChargesFromDatabase($invoice_id);
    $template_vars    = documentinvoiceTemplateVars($invoice, $invoice_products, $invoice_charges);

    foreach ($template_vars as $key => $value) {
        $label = str_replace($key, formatRTFString($value), $label);
    }

    header(sprintf('Content-Disposition: inline; filename="' . urlencode($posted['label_type']) . '_label_%05d.rtf"', $invoice_id));
} else {
    foreach ($invoice as $key => $value) {
        $find = '[' . strtoupper($key) . ']';

        $replace     = explode("\n", $value);
        $replace     = implode("\\par\n", $replace);
        $old_replace = '';
        while ($old_replace != $replace) {
            $old_replace = $replace;
            $replace     = str_replace("\\par\n\\par\n", "\\par\n", $replace);
        }

        $label = str_replace($find, $replace, $label);
    }
    header('Content-Disposition: inline; filename="shiplabel_' . substr('00000' . intval($invoice_id), -5) . '.rtf"');
}

header('Content-Type: text/rtf');
echo $label;
die();

<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceChargesFromDatabase, invoiceProductsFromDatabase

$action     = 'shiplabel';
$invoice_id = intval($posted['invoice_id']);
$invoice    = invoiceFromDatabase($invoice_id);

switch ($posted['label_type']) {
    case 'pieces':
        $filename = 'shiplabels/shiplabel_large.rtf';
        break;
    default:
        $filename = 'shiplabels/shiplabel_site_' . $invoice['site_id'] . '.rtf';
        break;
}
$label = file_get_contents($filename);

if ('' != $posted['label_type']) {
    $invoice_products = invoiceProductsFromDatabase($invoice_id);
    $invoice_charges  = invoiceChargesFromDatabase($invoice_id);
    $template_vars    = invoiceTemplateVars($invoice, $invoice_products, $invoice_charges);

    foreach ($template_vars as $key => $value) {
        $label = str_replace($key, formatRTFString($value), $label);
    }

    header(sprintf('Content-Disposition: inline; filename="' . urlencode($posted['label_type']) . '_label_%05d.rtf"', $invoice_id));
} else {
    if (count(explode("\n", $invoice['ship_address_full'])) > 6) {
        $label = str_replace(' [SHIP_ADDRESS_FULL]', '{\fs20 [SHIP_ADDRESS_FULL]}', $label);
    }

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

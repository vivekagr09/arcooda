<?php
//Shared Setup for printable invoices (JPEG,HTML,MHTML,PDF)
/*
Input variables:
$force_images        - force the given image urls
$force_signature    - force the given signature file
$sinvoice            - the invoice to display
$sinvoice_time        - force the given time to display on invoice
$staff_signature    - true/false whether to display signature

Output variables:
$charges_list        - array of charge ids to charge names
$country_list        - array of country_ids to country names
$images              - url to selected available images
$sinvoice_inforow    - the inforow details of the invoice
$sinvoice_labels     - company information for invoice company
$sinvoice_reference  - reference number for payment methods
$sinvoice_type       - a number representing the invoice status
$sinvoice_type_text  - title of invoice to display
$payment_data        - method information for invoice payment method
$staff               - information about staff member
$terms_list          - array of term ids to term names
$time_to_use         - time to display on invoice
$warranty_terms_list - array of warranty ids to warranty names
$website_info        - website information for invoice company
$website_path        - directory holding the invoice's resource website
$website_url         - url for the invoice's resource website
 */
global $_GLOBALS;

//Some hacked invoices:
//$change_array = array('3693', '3929', '3861');
//if (in_array($sinvoice_id, $change_array)) $sinvoice['site_id'] = 1;
//if (in_array($sinvoice_id, $change_array)) $sinvoice['site_id'] = 9;

//$sinvoice, $_SERVER, $sinvoice_time
//$sinvoice_type, $time_to_use, $sinvoice_type_text
$sinvoice_type      = array_search($sinvoice['invoice_status'], array(
    'inquiry',
    'enquiry',
    'closed',
    'proforma',
    'sales',
    'processed',
    'cancelled',
    'return',
    'completed',
    'purchase',
));
$time_to_use        = ($sinvoice['proforma_time'] > 0) ? $sinvoice['proforma_time'] : $sinvoice['open_time'];
$sinvoice_type_text = (('AU' == $sinvoice['ship_country'] && 'AU' == $sinvoice['bill_country']) ? 'TAX' : 'SALES') . ' INVOICE';

if (0 != $sinvoice_time) {
    $time_to_use = $sinvoice_time;
}

//$_GLOBALS, $force_signature, $staff_signature
//$staff
$staff = $_GLOBALS['staff'];
if (2 == $sinvoice_type || 6 == $sinvoice_type) {
    $staff['signature'] = '';
}

if (isset($force_signature)) {
    $staff['signature'] = $force_signature;
}

if (isset($staff_signature) && !$staff_signature) {
    $staff['signature'] = '';
}

//$charges_list, $terms_list, $warranty_terms_list
$charges_list = array();
$query        = $DB->query("SELECT charge_id,charge_name FROM `highwaygroup`.`charges` ORDER BY charge_name");
while ($row = $DB->fetch_assoc($query)) {
    $charges_list[$row['charge_id']] = $row['charge_name'];
}

$terms_list          = getTermsList();
$warranty_terms_list = shipinvoiceGetOptionList('warranty_terms_id');
$country_list        = getCountryList(false);

//$sinvoice
//$payment_data, $sinvoice_labels, $website_info
$payment_data = array();
if ('y' == $sinvoice['show_payment']) {
    $payment_data = $DB->query_first("SELECT * FROM `highwaygroup`.`payment_methods` WHERE site_id=" . intval($sinvoice['site_id']) . " AND payment_method_id=" . formatSQLString($sinvoice['paymenttype_id']) . " LIMIT 1", MYSQLI_ASSOC);
}

$sinvoice_labels = $DB->query_first("SELECT * FROM `highwaygroup`.`company_info` WHERE company_id=" . intval($sinvoice['site_id']) . " LIMIT 1", MYSQLI_ASSOC);
if (36137 == $sinvoice_id) {
    $sinvoice_labels["company_name"] = "HIGHWAYGAMES.COM PTY LTD";
}

if (0 == $sinvoice['site_id']) {
    $sinvoice_labels['company_invoiceheader']    = $sinvoice['company_name'] . "\n" . $sinvoice['company_address'];
    $sinvoice_labels['company_invoiceheader_au'] = $sinvoice['company_name'] . "\n" . $sinvoice['company_address'];
    $sinvoice_labels['company_name']             = $sinvoice['company_name'];
}
if (100 == $sinvoice['site_id']) {
    $sinvoice_labels['company_invoiceheader']    = "Jones Amusements\n29 Buttermere Drive,\nLakelands 2282, NSW, Australia\nPhone #: +61 421 929 068";
    $sinvoice_labels['company_invoiceheader_au'] = "Jones Amusements\n29 Buttermere Drive,\nLakelands 2282, NSW, Australia\nPhone #: 0421 929 068";
    $sinvoice_labels['company_name']             = 'Jones Amusements';
}
if (101 == $sinvoice['site_id']) {
    $sinvoice_labels['company_invoiceheader']    = "Deith Distributing\n145 Manorhaven Blvd.\nPort Washington, NY 11050\nTel: 516-621-1234\nFax: 516-621-1238\nE-Mail: deith@deith.com";
    $sinvoice_labels['company_invoiceheader_au'] = "Deith Distributing\n145 Manorhaven Blvd.\nPort Washington, NY 11050\nTel: 516-621-1234\nFax: 516-621-1238\nE-Mail: deith@deith.com";
    $sinvoice_labels['company_name']             = 'Deith Distributing';
}
if($sinvoice['invoice_address'] > 0){
    $sinvoice_labels['company_invoiceheader'] = $sinvoice_labels['company_name']."\n" .getAllInvoiceAddressList()[$sinvoice['invoice_address']];
    $sinvoice_labels['company_invoiceheader_au'] = $sinvoice_labels['company_name']."\n" .getAllInvoiceAddressList()[$sinvoice['invoice_address']];
}
$website_info = $DB->query_first("SELECT * FROM `highwaygroup`.`websites` WHERE id=" . intval($sinvoice['site_id']) . " LIMIT 1", MYSQLI_ASSOC);

//$website_url, $website_path
$website_url  = 'http://www.highwaygroup.net/';
$website_path = '/home/hwgroup/public_html/';
if (substr($website_url, -1) != '/') {
    $website_url .= '/';
}

if (substr($website_path, -1) != '/') {
    $website_path .= '/';
}

//$sinvoice, $force_images
//$images
$images                   = array();
$images['company_logo']   = 'webadmin/images/sites/site_' . $sinvoice['site_id'] . '/logo_invoice.png';
$images['payment_method'] = 'webadmin/images/payment/' . $payment_data['payment_method_image'];
if ('y' == $sinvoice['show_stamp']) {
    $images['stamp'] = 'webadmin/images/sites/site_' . $sinvoice['site_id'] . '/stamp.png';
    if (!is_file($website_path . $images['stamp'])) {
        $images['stamp'] = 'webadmin/images/sites/site_' . $sinvoice['site_id'] . '/stamp.gif';
    }
}
if ('y' == $sinvoice['show_signature']) {
    $images['signature'] = 'images/sigs/' . $staff['signature'];
}

//foreach($images as $key => $value) if (is_file($website_path . $value)) $images[$key] = $website_url . $value;
foreach ($images as $key => $value) {
    if (!is_file($website_path . $value)) {
        $images[$key] = '';
    }
}

if (is_array($force_images)) {
    foreach ($force_images as $key => $value) {
        $images[$key] = $value;
    }
}

//$sinvoice
//$sinvoice_reference
$sinvoice_reference = strtoupper(str_replace('_', '', formatFileSafe(shipinvoiceFormatNumber($sinvoice['invoice_id'], $sinvoice['revision_id'], $sinvoice['site_id']))));
if ('BPAY' == $sinvoice['paymenttype_id']) {
    $sinvoice_reference = str_replace("\r\n", " ", chunk_split(bpayGenerateNumber($sinvoice['member_id'], $sinvoice['invoice_id']), 5));
}

$formatted_sinvoice_number = shipinvoiceFormatNumber($sinvoice_id, ((3 == $sinvoice_type || 4 == $sinvoice_type) ? $sinvoice['revision_id'] : null), $sinvoice['site_id']);

//$sinvoice_contacts
$sinvoice_contacts = array();
if ('' != $sinvoice['ship_address_full']) {
    $sinvoice_contacts['SHIP TO'] = $sinvoice['ship_address_full'];
}

if ('' != $sinvoice['notify_address_full']) {
    $sinvoice_contacts['NOTIFY PARTY'] = $sinvoice['notify_address_full'];
}

//$sinvoice_inforow
$sinvoice_inforow = array();
if ('' != $payment_data['payment_method_name'] && 'y' == $sinvoice['show_payment']) {
    $sinvoice_inforow['PAYMENT TYPE'] = $payment_data['payment_method_name'];
}

if ('' != $sinvoice['proforma_purchaseorder'] && 'y' == $sinvoice['show_order']) {
    $sinvoice_inforow['PURCHASE ORDER'] = $sinvoice['proforma_purchaseorder'];
}

if ('' != $terms_list[$sinvoice['terms_id']]) {
    $sinvoice_inforow['SHIPPING TERMS'] = $terms_list[$sinvoice['terms_id']];
} elseif ('' != $terms_list[$sinvoice['def_terms']]) {
    $sinvoice_inforow['SHIPPING TERMS'] = $terms_list[$sinvoice['def_terms']];
}

if ('' != $sinvoice['supply_country'] && 'AU' != $sinvoice['supply_country']) {
    $sinvoice_inforow['SHIPS FROM'] = $country_list[$sinvoice['supply_country']];
}

if ('' != $sinvoice['ship_product_name'] && 'y' == $sinvoice['show_freight']) {
    $sinvoice_inforow['SHIP VIA'] = $sinvoice['ship_product_name'];
}

if ('' != $sinvoice['ship_account_num']) {
    $sinvoice_inforow['SHIP ACCOUNT'] = $sinvoice['ship_account_num'];
}

if ('' != $sinvoice['proforma_tracknumber']) {
    $sinvoice_inforow['TRACKING #'] = $sinvoice['proforma_tracknumber'];
}

if ('' != $all_staff_list[$sinvoice['handler_id']]) {
    $sinvoice_inforow['SALES REP'] = $all_staff_list[$sinvoice['handler_id']];
}

//$sinvoice_comments
$sinvoice_comments = array();
if ('' != $sinvoice['supply_notes'] && 9 == $sinvoice_type) {
    $sinvoice_comments['REMARKS'] = $sinvoice['supply_notes'];
}

if ('' != $sinvoice['invoice_comments'] && 9 != $sinvoice_type) {
    $sinvoice_comments['COMMENTS/NOTES'] = $sinvoice['invoice_comments'];
}

if (0 != $sinvoice['waranty_terms_id'] && 9 != $sinvoice_type) {
    $sinvoice_comments['WARRANTY TERMS'] = $warranty_terms_list[$sinvoice['waranty_terms_id']];
}
$origin_country = ('' != $sinvoice['origin_country'] && $sinvoice['is_origin_country'] == 'y') ? $country_list[$sinvoice['origin_country']] : '';
$harmonised_code = ('' != $sinvoice['harmonised_code'] && $sinvoice['is_harmonised_code'] == 'y') ? $sinvoice['harmonised_code'] : '';
$invoice_materials = ('' != $sinvoice['invoice_materials'] && $sinvoice['is_invoice_materials'] == 'y') ? $sinvoice['invoice_materials'] : '';
$invoice_usage = ('' != $sinvoice['invoice_usage'] && $sinvoice['is_invoice_usage'] == 'y') ? $sinvoice['invoice_usage'] : '';

// if('' != $harmonised_code || '' != $origin_country) {
//     $sinvoice_comments[sprintf('HARMONIZED CODE: %s        COUNTRY OF ORIGIN: %s', $harmonised_code, $origin_country)] = '';
// }
// if ('' != $invoice_materials || '' != $invoice_usage) {
//     $sinvoice_comments[sprintf('%s %s           %s %s',(!empty($invoice_materials)?'MATERIALS:':''), $invoice_materials,(!empty($invoice_usage)?'USAGE:':''), $invoice_usage)] = '';
// }
$s1 = ('' != $harmonised_code) ? 15 : (('' != $origin_country) ? 18 : 0);
$s2 = ('' != $harmonised_code) ? 15 : 0;
$s3 = ('' != $invoice_materials) ? 52 : (('' != $harmonised_code) ? 22 : (('' != $origin_country) ? 25 : 0));
if('' != $invoice_materials) {
    $max_length = strlen($invoice_materials) * 1.5;
    $s3 -= $max_length;
}

if('' != $harmonised_code || '' != $origin_country) {
    // $invoice_comments[sprintf('HARMONIZED CODE: %s        COUNTRY OF ORIGIN: %s', $harmonised_code, $origin_country)] = '';
    $sinvoice_comments[sprintf('%s %s%s %s',(!empty($harmonised_code)?'HARMONIZED CODE:':''), $harmonised_code,(!empty($origin_country)?str_repeat(' ',$s2).'COUNTRY OF ORIGIN:':''), $origin_country)] = '';
}
if ('' != $invoice_materials || '' != $invoice_usage) {
    $sinvoice_comments[sprintf('%s %s%s %s',(!empty($invoice_materials)?str_repeat(' ',$s1).'MATERIALS:':''), $invoice_materials,(!empty($invoice_usage)?str_repeat(' ',$s3).'USAGE:':''), $invoice_usage)] = '';
}
//if ($sinvoice['shipping_method'] == 'customer_forwarder') $sinvoice_comments['SHIPPING NOTES'] = 'Customer is responsible for arranging freight, loading and any damages in transit';

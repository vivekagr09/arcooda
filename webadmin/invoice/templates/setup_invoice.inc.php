<?php
/**
 * @var mixed[]   $invoice
 * @var mixed[][] $invoice_charges
 * @var mixed[][] $invoice_products
 */

?><?php

//Shared Setup for printable invoices (JPEG,HTML,MHTML,PDF)
/*
Input variables:
$force_images        - force the given image urls
$force_signature    - force the given signature file
$invoice            - the invoice to display
$invoice_charges    - the invoice charges
$invoice_products    - the invoice products
$invoice_time        - force the given time to display on invoice
$staff_signature    - true/false whether to display signature
$tax_view            - display alternate invoice id for tax purposes

Output variables:
$charges_list        - array of charge ids to charge names
$images              - url to selected available images
$invoice_comments    - the comments at the bottom of the invoice
$invoice_contacts    - the address boxes of the invoice
$invoice_fields      - the inventory fields to display
$invoice_inforow     - the inforow details of the invoice
$invoice_inventory   - All the itemised inventory to display
$invoice_labels      - company information for invoice company
$invoice_reference   - reference number for payment methods
$invoice_type        - a number representing the invoice status
$invoice_type_text   - title of invoice to display
$payment_data        - method information for invoice payment method
$staff               - information about staff member
$terms_list          - array of term ids to term names
$time_to_use         - time to display on invoice
$warranty_terms_list - array of warranty ids to warranty names
$website_info        - website information for invoice company
$website_path        - directory holding the invoice's resource website
$website_url         - url for the invoice's resource website
$invoice_diagonal    - text to display diagonally across invoice
 */

global $_GLOBALS;
global $DB;

// Hack: make global for pdf_invoice access
global $formatted_invoice_number;
global $images;
global $invoice_comments;
global $invoice_contacts;
global $invoice_diagonal;
global $invoice_inforow;
global $invoice_labels;
global $invoice_reference;
global $invoice_type;
global $invoice_type_text;
global $payment_data;
global $time_to_use;
global $website_path;

if (!isset($all_staff_list) || !is_array($all_staff_list)) {
    $all_staff_list = array();
    $query          = $DB->query("SELECT `id`, `first_name`, `last_name` FROM `highwaygroup`.`users`");
    while ($row = $DB->fetch_assoc($query)) {
        $all_staff_list[$row['id']] = trim($row['first_name'] . ' ' . $row['last_name']);
    }

    $DB->free_result($query);
}

//Some hacked invoices:
//$change_array = array('3693', '3929', '3861');
//if (in_array($invoice['invoice_id'], $change_array)) $invoice['site_id'] = 1;
//if (in_array($invoice['invoice_id'], $change_array)) $invoice['site_id'] = 9;
//if($invoice['site_id']==19) $invoice['site_id']=9; // hack for the temporary converters.com.hk invoices...
//$invoice, $_SERVER, $invoice_time
//$invoice_type, $time_to_use, $invoice_type_text
$invoice_type = array_search($invoice['invoice_status'], array(
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
$time_to_use  = ($invoice['proforma_time'] > 0) ? $invoice['proforma_time'] : $invoice['open_time'];
//@todo revisit this numbering
if ($invoice_type < 3) {
    $time_to_use       = time();
    $invoice_type_text = 'QUOTATION';
} elseif ($invoice_type < 7) {
    if (20 != $invoice['site_id'] && 26 != $invoice['site_id'] && 'AU' == $invoice['ship_country'] && 'AU' == $invoice['bill_country']) {
        $invoice_type_text = 'TAX INVOICE';
    } else {
        $invoice_type_text = 'SALES INVOICE';
    }
} elseif (9 == $invoice_type) {
    $invoice_type_text = 'PURCHASE SUMMARY';
} elseif (7 == $invoice_type) {
    $invoice_type_text = 'INVOICE';
} else {
    $invoice_type_text = strtoupper($invoice['invoice_status']) . ' INVOICE';
}
if (isset($invoice_time) && 0 != $invoice_time) {
    $time_to_use = $invoice_time;
}

//$_GLOBALS, $force_signature, $staff_signature
//$staff
$staff = array();
if (array_key_exists("staff", $_GLOBALS)) {
    $staff = $_GLOBALS['staff'];
}

if (2 == $invoice_type || 6 == $invoice_type) {
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
$warranty_terms_list = invoiceGetOptionList('warranty_terms_id');
$country_list        = getCountryList(false);

//$invoice
//$payment_data, $invoice_labels, $website_info
$payment_data = $DB->query_first(sprintf(
    "SELECT * FROM `highwaygroup`.`payment_methods` WHERE `site_id` = %d AND `payment_method_id` = %s LIMIT 1",
    $invoice['site_id'],
    formatSQLString($invoice['paymenttype_id'])
), MYSQLI_ASSOC);
$invoice_labels = $DB->query_first(sprintf(
    "SELECT * FROM `highwaygroup`.`company_info` WHERE `company_id` = %d LIMIT 1",
    $invoice['site_id']
), MYSQLI_ASSOC);
if($invoice['invoice_address'] > 0 && $action == 'shipview'){
    $invoice_labels['company_invoiceheader'] = $sinvoice_labels['company_name']."\n" .getAllInvoiceAddressList()[$invoice['invoice_address']];
    $invoice_labels['company_invoiceheader_au'] = $sinvoice_labels['company_name']."\n" .getAllInvoiceAddressList()[$invoice['invoice_address']];
}
$website_info = $DB->query_first(sprintf(
    "SELECT * FROM `highwaygroup`.`websites` WHERE `id` = %d LIMIT 1",
    $invoice['site_id']
), MYSQLI_ASSOC);

//$website_url, $website_path
$website_url  = 'http://www.highwaygroup.net/';
$website_path = '/home/hwgroup/public_html/';

//$invoice_diagonal
$invoice_diagonal = (isset($make_preview) && $make_preview) ? 'PREVIEW ONLY' : '';

//$invoice, $force_images
//$images
$images                   = array();
$images['company_logo']   = 'webadmin/images/sites/site_' . $invoice['site_id'] . '/logo_invoice.png';
$images['payment_method'] = 'webadmin/images/payment/' . $payment_data['payment_method_image'];
$images['stamp']          = 'webadmin/images/sites/site_' . $invoice['site_id'] . '/stamp.png';
if (!is_file($website_path . $images['stamp'])) {
    $images['stamp'] = 'webadmin/images/sites/site_' . $invoice['site_id'] . '/stamp.gif';
}

$images["signature"] = "";
if (array_key_exists("signature", $staff)) {
    $images['signature'] = 'images/sigs/' . $staff['signature'];
}

//foreach($images as $key => $value) if (is_file($website_path . $value)) $images[$key] = $website_url . $value;
foreach ($images as $key => $value) {
    if (!is_file($website_path . $value)) {
        $images[$key] = '';
    }
}

if (isset($force_images) && is_array($force_images)) {
    foreach ($force_images as $key => $value) {
        $images[$key] = $value;
    }
}

//$invoice
//$invoice_reference
$invoice_reference = strtoupper(str_replace('_', '', formatFileSafe(invoiceFormatNumber($invoice['invoice_id'], $invoice['revision_id'], $invoice['site_id']))));
if ('BPAY' == $invoice['paymenttype_id']) {
    $invoice_reference = str_replace("\r\n", " ", chunk_split(bpayGenerateNumber($invoice['member_id'], $invoice['invoice_id']), 5));
}

$formatted_invoice_number = invoiceFormatNumber($invoice['invoice_id'], (in_array($invoice_type, array(3, 4, 5, 6)) ? $invoice['revision_id'] : null), $invoice['site_id']);
if (isset($tax_view) && $tax_view) {
    $formatted_invoice_number = 'T' . substr(invoiceFormatNumber($invoice['tax_id'], 0, $invoice['site_id']), 1);
    //$formatted_invoice_number = "T" . substr(preg_replace("/[0-9]+/", sprintf("%05d", $invoice["tax_id"]), $formatted_invoice_number), 1);
    if ($invoice["tax_time"] > 0) {
        $invoice_time = $invoice["tax_time"];
    }
}
if (isset($action) && 'shipview' == $action) {
    $formatted_invoice_number = 'S' . substr($formatted_invoice_number, 1);
}
//$invoice_contacts
$invoice_contacts = array();
if (9 == $invoice_type) {
    if ('' != $invoice['bill_address_full']) {
        $invoice_contacts['SOLD TO'] = $invoice['bill_address_full'];
    }
} else {
    if ('' != $invoice['bill_address_full']) {
        $cleaned_data = preg_replace('/^\s*$/m', '', $invoice['bill_address_full']);  // Remove empty lines
        $lines = explode("\n", $cleaned_data);  // Split the text into lines
        $cleaned_data = array_map('trim', $lines);  // Trim each line
        $cleaned_data = implode("\n", $cleaned_data); 
        $invoice_contacts['BILL TO'] = $cleaned_data;
    }

    if ('' != $invoice['ship_address_full']) {
        $invoice_contacts['SHIP TO'] = $invoice['ship_address_full'];
    }

    if ('pickup' == $invoice['shipping_method']) {
        $invoice_contacts['SHIP TO'] = 'Pickup';
    }

    if ('customer_forwarder' == $invoice['shipping_method'] && 'forwarder' == $invoice['own_freight_option']) {
        unset($invoice_contacts['SHIP TO']);
        //---------------------------------------------------------------------
        //Can probably move this to invoiceFromDatabase
        $temp = array(('' != $invoice['forwarder_company_name']) ? trim($invoice['forwarder_company_name']) : trim($invoice['forwarder_contact']), ('' != $invoice['forwarder_company_name'] && '' != $invoice['forwarder_contact']) ? ('Attn: ' . $invoice['forwarder_contact']) : '', trim($invoice['forwarder_address']), trim(ltrim(trim($invoice['forwarder_city'] . ', ' . $invoice['forwarder_state'] . ' ' . $invoice['forwarder_post_code']), ",")), trim($country_list[$invoice['forwarder_country']]), "\r", ('' != $invoice['forwarder_phone_no']) ? ('Ph: ' . $invoice['forwarder_phone_no']) : '', ('' != $invoice['forwarder_email']) ? ('Email: ' . $invoice['forwarder_email']) : '', ('' != $invoice['forwarder_account']) ? ('Account: ' . $invoice['forwarder_account']) : '');
        foreach ($temp as $key => $value) {
            if ('' == $temp) {
                unset($temp[$key]);
            }
        }

        $invoice['forwarder_address_full'] = implode("\n", $temp);
        //---------------------------------------------------------------------
        $invoice_contacts['RELEASE TO'] = $invoice['forwarder_address_full'];
    }
    if (isset($action) && 'shipview' == $action && '' != $invoice['notify_address_full']) {
        $invoice_contacts['NOTIFY PARTY'] = $invoice['notify_address_full'];
    }
}

//$invoice_inforow
$invoice_inforow = array();
if ('' != $payment_data['payment_method_name']) {
    $invoice_inforow['PAYMENT TYPE'] = $payment_data['payment_method_name'];
}else{
    $invoice_inforow['PAYMENT TYPE'] = 'N/A';
}

if ('' != $invoice['proforma_purchaseorder']) {
    $invoice_inforow['PURCHASE ORDER'] = $invoice['proforma_purchaseorder'];
}

if (array_key_exists($invoice['terms_id'], $terms_list) && '' != $terms_list[$invoice['terms_id']]) {
    $invoice_inforow['SHIPPING TERMS'] = $terms_list[$invoice['terms_id']];
} elseif (array_key_exists($invoice['def_terms'], $terms_list) && '' != $terms_list[$invoice['def_terms']]) {
    $invoice_inforow['SHIPPING TERMS'] = $terms_list[$invoice['def_terms']];
}

if ('' != $invoice['forwarder_company_name'] && 'customer_forwarder' == $invoice['shipping_method']) {
    $invoice_inforow['SHIP VIA'] = $invoice['forwarder_company_name'];
}

if (array_key_exists($invoice['supply_country'], $country_list) && '' != $invoice['supply_country'] && 'AU' != $invoice['supply_country']) {
    $invoice_inforow['SHIPS FROM'] = $country_list[$invoice['supply_country']];
}

if ('' != $invoice['ship_supplier_name']) {
    $invoice_inforow['SHIP VIA'] = $invoice['ship_supplier_name'];
}

if($key=array_search('GFR', array_column($invoice_products, 'generic_code'))){
    $invoice_inforow['SHIP VIA'] = $invoice_products[$key]['product_name'];
}
if ('' != $invoice['ship_product_name']) {
    // $invoice_inforow['SHIP VIA'] = $invoice['ship_product_name'];
    // Updated on 27th Aug 2023 by Swapnil
    $row = $DB->query_first("SELECT * FROM `freight3`.`services` WHERE `id` = " . intval($invoice['ship_id']) . " LIMIT 1", MYSQLI_ASSOC);
    $invoice_inforow['SHIP VIA'] = ($row['online_name'] != '') ? $row['online_name'] : $invoice['ship_product_name'];
}

if ('' != $invoice['ship_account_num']) {
    $invoice_inforow['SHIP ACCOUNT'] = $invoice['ship_account_num'];
}

if ('' != $invoice['proforma_tracknumber']) {
    $invoice_inforow['TRACKING #'] = $invoice['proforma_tracknumber'];
}

if (array_key_exists($invoice['handler_id'], $all_staff_list) && '' != $all_staff_list[$invoice['handler_id']]) {
    $invoice_inforow['SALES REP'] = $all_staff_list[$invoice['handler_id']];
}

if (isset($tax_view) && $tax_view) {
    $invoice_inforow["OFFICE REF"] = invoiceFormatNumber($invoice['invoice_id'], (in_array($invoice_type, array(3, 4, 5, 6)) ? $invoice['revision_id'] : null), $invoice['site_id']);
}

//$invoice_comments
$invoice_comments = array();
if ('' != $invoice['supply_notes'] && 9 == $invoice_type) {
    $invoice_comments['REMARKS'] = $invoice['supply_notes'];
}

if ('' != $invoice['vat_number']) {
    $invoice_comments[sprintf('VAT NUMBER : %s', $invoice['vat_number'])] = '';
}

if ('' != $invoice['invoice_comments'] && 9 != $invoice_type) {
    $invoice_comments['COMMENTS/NOTES'] = $invoice['invoice_comments'];
}
// if ('' != $invoice['customer_requests'] && 9 != $invoice_type) {
//     $invoice_comments['CUSTOMER REQUEST'] = $invoice['customer_requests'];
// }

if (0 != $invoice['waranty_terms_id'] && 9 != $invoice_type) {
    //$invoice_comments['WARRANTY TERMS'] = $warranty_terms_list[$invoice['waranty_terms_id']];
}

if ('customer_forwarder' == $invoice['shipping_method'] && '8457' != $invoice['invoice_id']) {
    $invoice_comments['SHIPPING NOTES'] = 'Customer is responsible for arranging freight, loading and any damages in transit';
}
$invoice_row_comment = array();
if ('EUR_BANK5' == $invoice['paymenttype_id']) {
    $invoice_row_comment[] = 'This EURO account can only accept payments in EURO. Payments made in USD to this account will be rejected by the bank.  Please send $'.($invoice['invoice_offereu'] > 0 ?  $invoice['invoice_offereu'] : convertCurrency($invoice['invoice_offer'],'USD','EUR')).' Euro.';
}
// if ('' != $invoice['harmonised_code'] || '' != $invoice['origin_country']) {
//     $invoice_comments[sprintf('HARMONIZED CODE: % 10s        COUNTRY OF ORIGIN: %s', $invoice['harmonised_code'], $country_list[$invoice['origin_country']])] = '';
// }

$origin_country = ('' != $invoice['origin_country'] && $invoice['is_origin_country'] == 'y') ? $country_list[$invoice['origin_country']] : '';
$harmonised_code = ('' != $invoice['harmonised_code'] && $invoice['is_harmonised_code'] == 'y') ? $invoice['harmonised_code'] : '';
$invoice_materials = ('' != $invoice['invoice_materials'] && $invoice['is_invoice_materials'] == 'y') ? $invoice['invoice_materials'] : '';
$invoice_usage = ('' != $invoice['invoice_usage'] && $invoice['is_invoice_usage'] == 'y') ? $invoice['invoice_usage'] : '';

$s1 = ('' != $harmonised_code) ? 15 : (('' != $origin_country) ? 18 : 0);
$s2 = ('' != $harmonised_code) ? 15 : 0;
$s3 = ('' != $invoice_materials) ? 52 : (('' != $harmonised_code) ? 22 : (('' != $origin_country) ? 25 : 0));
if('' != $invoice_materials) {
    $max_length = strlen($invoice_materials) * 1.5;
    $s3 -= $max_length;
}

if('' != $harmonised_code || '' != $origin_country) {
    // $invoice_comments[sprintf('HARMONIZED CODE: %s        COUNTRY OF ORIGIN: %s', $harmonised_code, $origin_country)] = '';
    $invoice_comments[sprintf('%s %s%s %s',(!empty($harmonised_code)?'HARMONIZED CODE:':''), $harmonised_code,(!empty($origin_country)?str_repeat(' ',$s2).'COUNTRY OF ORIGIN:':''), $origin_country)] = '';
}
if ('' != $invoice_materials || '' != $invoice_usage) {
    $invoice_comments[sprintf('%s %s%s %s',(!empty($invoice_materials)?str_repeat(' ',$s1).'MATERIALS:':''), $invoice_materials,(!empty($invoice_usage)?str_repeat(' ',$s3).'USAGE:':''), $invoice_usage)] = '';
}

//$invoice_fields
$invoice_fields          = array('product_str' => 'ITEM ID', 'product_name' => 'ITEM NAME', 'product_qty' => 'QTY', 'product_offer' => 'PRICE EACH', 'product_total' => 'AMOUNT');
$invoice_field_explode   = 'product_name';
$invoice_inventory       = array();
$invoice_inventory_extra = array();
switch ($invoice_type) {
    default:
        if (is_array($invoice_products)) {
            foreach ($invoice_products as $product) {
                if ('c' != $product['generic']) {
                    $new_product                = array();
                    $new_extra                  = array();
                    $new_product['product_str'] = $product['product_id'];
                    if ($product['product_id'] > 0 && $product['sub_id'] > 0) {
                        $new_product['product_str'] = sprintf(
                            '%05d - %04d',
                            $product['product_id'],
                            $product['sub_id']
                        );
                    }

                    if ('' != $product['generic_code'] && '0' == $product['product_id']) {
                        $new_product['product_str'] = $product['generic_code'];
                    }

                    //if ($product['product_condition'] == 2) $new_product['product_str'].= 'U';
                    $new_product['product_name']  = $product['product_name'];
                    $new_product['product_qty']   = $product['product_qty'];
                    $new_product['product_offer'] = convertCurrency($product['product_offer'], $invoice['invoice_currency'], '', false, $product['decimal']);
                    $new_product['product_total'] = convertCurrency(intval($product['product_qty']) * $product['product_offer'], $invoice['invoice_currency'], '', false, $product['decimal']);
                    if ('' != $product['product_notes']) {
                        $new_extra['product_name'] = $product['product_notes'];
                    }

                    $invoice_inventory[]       = $new_product;
                    $invoice_inventory_extra[] = $new_extra;
                }
            }
        }

        if (0 != $invoice['ship_id']) {
            $new_product = array();
            $new_extra   = array();

            if (20 == $invoice["site_id"]) {
                $row = $DB->query_first("SELECT `type` FROM `gdelta_gdelta`.`freight_services` WHERE `id` = " . intval($invoice['ship_id']) . " LIMIT 1", MYSQLI_ASSOC);
            } elseif (39 == $invoice["site_id"]) {
                $row = $DB->query_first("SELECT `product_type` AS `type` FROM `freight3`.`services` WHERE `id` = " . intval($invoice['ship_id']) . " LIMIT 1", MYSQLI_ASSOC);
            } else {
                $row = $DB->query_first("SELECT `type` FROM `highwaygroup`.`freight_services` WHERE `id` = " . intval($invoice['ship_id']) . " LIMIT 1", MYSQLI_ASSOC);
            }

            //$new_product['product_str'] = sprintf('F%02d',$invoice['ship_id']) . (($invoice['ship_insurance'] == 'y') ? 'I' : 'N') . (($row['type'] == 'truck') ? 'T' : '');
            $new_product["product_str"]   = sprintf("F%02d", $invoice["ship_id"]) . (("y" == $invoice["ship_insurance"]) ? "I" : "N") . (("truck" == $row["type"]) ? "T" : "") . (("y" == $invoice["ship_registered"]) ? "R" : "") . (("y" == $invoice["ship_receipted"]) ? "A" : "") . (("y" == $invoice["ship_express"]) ? "E" : "");
            $new_product['product_name']  = $invoice['ship_product_name'];
            $new_product['product_offer'] = convertCurrency($invoice['ship_offer'], $invoice['invoice_currency']);
            $new_product['product_total'] = $new_product['product_offer'];
            if ('' != $invoice['ship_product_notes']) {
                $new_extra['product_name'] = $invoice['ship_product_notes'];
            }

            $invoice_inventory[]       = $new_product;
            $invoice_inventory_extra[] = $new_extra;
        }
        if (is_array($invoice_charges)) {
            foreach ($invoice_charges as $charge) {
                if (1 != $charge['charge_id']) {
                    $new_product                  = array();
                    $new_extra                    = array();
                    $new_product['product_str']   = sprintf(
                        'C%02d',
                        $charge['charge_id']
                    );
                    $new_product['product_name']  = $charge['charge_name'];
                    $new_product['product_qty']   = '';
                    $new_product['product_offer'] = convertCurrency($charge['charge_offer'], $invoice['invoice_currency']);
                    $new_product['product_total'] = $new_product['product_offer'];
                    if ('' != $charge['charge_notes']) {
                        $new_extra['product_name'] = $charge['charge_notes'];
                    }

                    $invoice_inventory[]       = $new_product;
                    $invoice_inventory_extra[] = $new_extra;
                }
            }
        }

        break;

    case 9:
        $invoice_fields = array('supplier_name' => 'SUPPLIER', 'supplier_code' => 'ITEM ID', 'product_name' => 'NAME OF ITEM', 'product_qty' => 'QTY', 'product_offer' => 'COST PRICE (AUD)', 'product_total' => 'AMOUNT TOTAL');
        if (is_array($invoice_products)) {
            foreach ($invoice_products as $product) {
                $new_product                  = array();
                $new_extra                    = array();
                $new_product['supplier']      = $product['supplier_name'];
                $new_product['supplier_code'] = $product['supplier_code'];
                $new_product['product_name']  = $product['product_name'];
                $new_product['product_qty']   = $product['product_qty'];
                $new_product['product_offer'] = convertCurrency($product['product_costau'], 'AUD');
                $new_product['product_total'] = convertCurrency($product['product_costau'] * $product['product_qty'], 'AUD');
                if ('' != $product['supplier_notes']) {
                    $new_extra['product_name'] = $product['supplier_notes'];
                }

                $invoice_inventory[]       = $new_product;
                $invoice_inventory_extra[] = $new_extra;
            }
        }

        break;
}

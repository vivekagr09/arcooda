<?php
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
$formatted_invoice_number    - invoice number to display
$images                - url to selected available images
$invoice_comments    - the comments at the bottom of the invoice
$invoice_contacts    - the address boxes of the invoice
$invoice_fields        - the inventory fields to display
$invoice_inforow    - the inforow details of the invoice
$invoice_inventory    - All the itemised inventory to display
$invoice_labels        - company information for invoice company
$invoice_reference    - reference number for payment methods
$invoice_type        - a number representing the invoice status
$invoice_type_text    - title of invoice to display
$payment_data        - method information for invoice payment method
$staff                - information about staff member
$terms_list            - array of term ids to term names
$time_to_use        - time to display on invoice
$warranty_list        - array of warranty ids to warranty names
$website_info        - website information for invoice company
$website_path        - directory holding the invoice's resource website
$website_url        - url for the invoice's resource website
$invoice_diagonal    - text to display diagonally across invoice
 */

moduleLoad('invoice');

global $_GLOBALS;

//Some hacked invoices:
//$change_array = array('3693', '3929', '3861');
//if (in_array($invoice_id, $change_array)) $invoice['site_id'] = 1;
//if (in_array($invoice_id, $change_array)) $invoice['site_id'] = 9;
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
//@todo revisit
if ($invoice_type < 3) {
    $time_to_use       = time();
    $invoice_type_text = 'QUOTATION';
} elseif (3 == $invoice_type || 4 == $invoice_type) {
    if (3 != $invoice["site_id"] && 20 != $invoice['site_id'] && 26 != $invoice['site_id'] && 'AU' == $invoice['ship_country'] && 'AU' == $invoice['bill_country']) {
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
if (0 != $invoice_time) {
    $time_to_use = $invoice_time;
}

//$_GLOBALS, $force_signature, $staff_signature
//$staff
$staff = $_GLOBALS['staff'];
if (2 == $invoice_type || 6 == $invoice_type) {
    $staff['signature'] = '';
}

if (isset($force_signature)) {
    $staff['signature'] = $force_signature;
}

if (isset($staff_signature) && !$staff_signature) {
    $staff['signature'] = '';
}

//$charges_list, $terms_list, $warranty_list
$charges_list = array();
$query        = $DB->query("SELECT `charge_id`, `charge_name` FROM `highwaygroup`.`charges` ORDER BY `charge_name`");
while ($row = $DB->fetch_assoc($query)) {
    $charges_list[$row['charge_id']] = $row['charge_name'];
}

$terms_list    = getTermsList();
$warranty_list = getWarrantyTermsList();

//$invoice
//$payment_data, $invoice_labels, $website_info
$payment_data   = $DB->query_first("SELECT * FROM payment_methods WHERE site_id=" . intval($invoice['site_id']) . " AND payment_method_id=" . formatSQLString($invoice['paymenttype_id']) . " LIMIT 1", MYSQLI_ASSOC);
$invoice_labels = $DB->query_first(sprintf(
    "SELECT * FROM `highwaygroup`.`company_info` WHERE `company_id` = %d LIMIT 1",
    $invoice['site_id']
), MYSQLI_ASSOC);
$website_info   = $DB->query_first("SELECT * FROM `highwaygroup`.`websites` WHERE `id` = " . intval($invoice['site_id']) . " LIMIT 1", MYSQLI_ASSOC);

//$website_url, $website_path
$website_url  = 'https://' . $_SERVER['SERVER_NAME'] . '/';
$website_path = $_SERVER['DOCUMENT_ROOT'] . '/';
if (substr($website_url, -1) != '/') {
    $website_url .= '/';
}

if (substr($website_path, -1) != '/') {
    $website_path .= '/';
}

//$invoice_diagonal
$invoice_diagonal = ($make_preview) ? 'PREVIEW ONLY' : '';

//$invoice, $force_images
//$images
$images                   = array();
$images['company_logo']   = 'webadmin/images/sites/site_' . $invoice['site_id'] . '/logo_invoice.png';
$images['payment_method'] = 'webadmin/images/payment/' . $payment_data['payment_method_image'];
$images['stamp']          = 'webadmin/images/sites/site_' . $invoice['site_id'] . '/stamp.png';
if (!is_file($website_path . $images['stamp'])) {
    $images['stamp'] = 'webadmin/images/sites/site_' . $invoice['site_id'] . '/stamp.gif';
}

$images['signature'] = 'images/sigs/' . $staff['signature'];
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

//$invoice
//$invoice_reference
$invoice_reference = strtoupper(str_replace('_', '', formatFileSafe(invoiceFormatNumber($invoice['invoice_id'], $invoice['revision_id'], $invoice['site_id']))));
if ('BPAY' == $invoice['paymenttype_id']) {
    $invoice_reference = str_replace("\r\n", " ", chunk_split(bpayGenerateNumber($invoice['member_id'], $invoice['invoice_id']), 5));
}

$formatted_invoice_number = invoiceFormatNumber($invoice_id, (in_array($invoice_type, array(3, 4, 5, 6)) ? $invoice['revision_id'] : null), $invoice['site_id']);
if ($tax_view) {
    $formatted_invoice_number = "T" . substr(preg_replace("/[0-9]+/", sprintf("%05d", $invoice["tax_id"]), $formatted_invoice_number), 1);
}

if ('shipview' == $action) {
    $formatted_invoice_number = 'S' . substr($formatted_invoice_number, 1);
}

//$invoice_contacts
$invoice_contacts = array();
$forwarder_country_list = invoiceGetOptionList('forwarder_country');
if (9 == $invoice_type) {
    if ('' != $invoice['bill_address_full']) {
        $invoice_contacts['SOLD TO'] = $invoice['bill_address_full'];
    }

} else {
    if ('' != $invoice['bill_address_full']) {
        $invoice_contacts['Invoice To'] = $invoice['bill_address_full'];
    }

    if ('' != $invoice['ship_address_full']) {
        $invoice_contacts['Deliver To'] = $invoice['ship_address_full'];
    }

    //if ($invoice['shipping_method'] == 'pickup') $invoice_contacts['Deliver To'] = 'Pickup';
    if ('customer_forwarder' == $invoice['shipping_method'] && 'forwarder' == $invoice['own_freight_option']) {
        unset($invoice_contacts['SHIP TO']);
        //---------------------------------------------------------------------
        //Can probably move this to invoiceFromDatabase
        $temp = array(('' != $invoice['forwarder_company_name']) ? trim($invoice['forwarder_company_name']) : trim($invoice['forwarder_contact']), ('' != $invoice['forwarder_company_name'] && '' != $invoice['forwarder_contact']) ? ('Attn: ' . $invoice['forwarder_contact']) : '', trim($invoice['forwarder_address']), trim(ltrim(trim($invoice['forwarder_city'] . ', ' . $invoice['forwarder_state'] . ' ' . $invoice['forwarder_post_code']), ",")), trim($forwarder_country_list[$invoice['forwarder_country']]), "\r", ('' != $invoice['forwarder_phone_no']) ? ('Ph: ' . $invoice['forwarder_phone_no']) : '', ('' != $invoice['forwarder_email']) ? ('Email: ' . $invoice['forwarder_email']) : '', ('' != $invoice['forwarder_account']) ? ('Account: ' . $invoice['forwarder_account']) : '');
        foreach ($temp as $key => $value) {
            if ('' == $temp) {
                unset($temp[$key]);
            }
        }

        $invoice['forwarder_address_full'] = implode("\n", $temp);
        //---------------------------------------------------------------------
        $invoice_contacts['RELEASE TO'] = $invoice['forwarder_address_full'];
    }
    if ('' != $invoice['notify_address_full'] && 'shipview' == $action) {
        $invoice_contacts['NOTIFY PARTY'] = $invoice['notify_address_full'];
    }

}

//$invoice_inforow
$invoice_inforow = array();
if ('' != $payment_data['payment_method_name']) {
    $invoice_inforow['PAYMENT TYPE'] = $payment_data['payment_method_name'];
}

if ('' != $invoice['proforma_purchaseorder']) {
    $invoice_inforow['PURCHASE ORDER'] = $invoice['proforma_purchaseorder'];
}

if ('' != $terms_list[$invoice['terms_id']]) {
    $invoice_inforow['SHIPPING TERMS'] = $terms_list[$invoice['terms_id']];
} elseif ('' != $terms_list[$invoice['def_terms']]) {
    $invoice_inforow['SHIPPING TERMS'] = $terms_list[$invoice['def_terms']];
}

if ('' != $invoice['forwarder_company_name'] && 'customer_forwarder' == $invoice['shipping_method']) {
    $invoice_inforow['SHIP VIA'] = $invoice['forwarder_company_name'];
}

$supply_country_list = invoiceGetOptionList('supply_country');
if ('' != $invoice['supply_country'] && 'HK' != $invoice['supply_country']) {
    $invoice_inforow['SHIPS FROM'] = $supply_country_list[$invoice['supply_country']];
}

if ('' != $invoice['ship_supplier_name']) {
    $invoice_inforow['SHIP VIA'] = $invoice['ship_supplier_name'];
}

if ('' != $invoice['ship_account_num']) {
    $invoice_inforow['SHIP ACCOUNT'] = $invoice['ship_account_num'];
}

if ('' != $invoice['proforma_tracknumber']) {
    $invoice_inforow['TRACKING #'] = $invoice['proforma_tracknumber'];
}

if ('' != $all_staff_list[$invoice['handler_id']]) {
    $invoice_inforow['SALES REP'] = $all_staff_list[$invoice['handler_id']];
}

if ($tax_view) {
    $invoice_inforow["OFFICE REF"] = invoiceFormatNumber($invoice_id, (in_array($invoice_type, array(3, 4, 5, 6)) ? $invoice['revision_id'] : null), $invoice['site_id']);
}

//$invoice_comments
$invoice_comments = array();
if ('' != $invoice['supply_notes'] && 9 == $invoice_type) {
    $invoice_comments['REMARKS'] = $invoice['supply_notes'];
}

if ('' != $invoice['bill_vat']) {
    $invoice_comments['VAT NUMBER'] = $invoice['bill_vat'];
}

if ('' != $invoice['invoice_comments'] && 9 != $invoice_type) {
    $invoice_comments['COMMENTS/NOTES'] = $invoice['invoice_comments'];
}

if (0 != $invoice['waranty_terms_id'] && 9 != $invoice_type) {
    $invoice_comments['WARRANTY TERMS'] = $warranty_list[$invoice['waranty_terms_id']];
}

if ('customer_forwarder' == $invoice['shipping_method']) {
    $invoice_comments['SHIPPING NOTES'] = 'Customer is responsible for arranging freight, loading and any damages in transit';
}

$origin_country_list = invoiceGetOptionList('origin_country');
if ('' != $invoice['harmonised_code'] || '' != $invoice['origin_country']) {
    $invoice_comments[sprintf('HARMONIZED CODE: % 10s        COUNTRY OF ORIGIN: %s', $invoice['harmonised_code'], $origin_country_list[$invoice['origin_country']])] = '';
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
                        $new_product['product_str'] = sprintf('%05d - %04d', $product['product_id'], $product['sub_id']);
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
            $row         = $DB->query_first("SELECT `type` FROM `gdelta_gdelta`.`freight_services` WHERE `id` = " . intval($invoice['ship_id']) . " LIMIT 1", MYSQLI_ASSOC);
            //$new_product['product_str'] = sprintf('F%02d', $invoice['ship_id']) . (($invoice['ship_insurance'] == 'y') ? 'I' : 'N') . (($row['type'] == 'truck') ? 'T' : '');
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
                    $new_product['product_str']   = sprintf('C%02d', $charge['charge_id']);
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
        $invoice_fields = array('supplier_name' => 'SUPPLIER', 'supplier_code' => 'ITEM ID', 'product_name' => 'NAME OF ITEM', 'product_qty' => 'QTY', 'product_offer' => 'COST PRICE (USD)', 'product_total' => 'AMOUNT TOTAL');
        if (is_array($invoice_products)) {
            foreach ($invoice_products as $product) {
                $new_product                  = array();
                $new_extra                    = array();
                $new_product['supplier']      = $product['supplier_name'];
                $new_product['supplier_code'] = $product['supplier_code'];
                $new_product['product_name']  = $product['product_name'];
                $new_product['product_qty']   = $product['product_qty'];
                $new_product['product_offer'] = convertCurrency($product['product_costus'], 'USD');
                $new_product['product_total'] = convertCurrency($product['product_costus'] * $product['product_qty'], 'USD');
                if ('' != $product['supplier_notes']) {
                    $new_extra['product_name'] = $product['supplier_notes'];
                }

                $invoice_inventory[]       = $new_product;
                $invoice_inventory_extra[] = $new_extra;
            }
        }

        break;
}
unset($invoice_field_explode, $invoice_inventory_extra, $key, $new_extra, $new_product, $product, $value);

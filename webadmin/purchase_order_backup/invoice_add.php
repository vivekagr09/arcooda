<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('member'); //memberContactsFromDatabase, memberCouriersFromDatabase, memberForwardersFromDatabase, memberNotifyPartiesFromDatabase, memberShippingAddressesFromDatabase, memberFromDatabase, memberRequestsFromDatabase

include_once __DIR__ . '/header.inc.php';
$action     = 'add';
$invoice_id = 0;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$invoice                = array(
    'handler_id'       => 159, // Default to Jody
    'invoice_status'   => 'proforma',
    'invoice_currency' => 'USD',
    'supply_country'   => 'CN',
    'site_id'          => $_GLOBALS["site_settings"]["site_id"],
    'paymenttype_id'   => 'BANK_USD',
);
$invoice_products       = array();
if(isset($_REQUEST['supplierproduct']) && $_REQUEST['supplierproduct']!=''){
   $invoice_products=getProductForSupplier($_REQUEST['supplierproduct']);
}
if(isset($_REQUEST['supplier_id']) && $_REQUEST['supplier_id']!=''){
   $invoice_supplier=supplierFromDatabase($_REQUEST['supplier_id']);
   if(count($invoice_supplier)){
        $invoice['supplier_id']=$invoice_supplier['supplier_id'];
        $invoice['bill_name']=$invoice_supplier['full_name'];
        $invoice['bill_address']=$invoice_supplier['address_1'];
        $invoice['bill_city']=$invoice_supplier['city'];
        $invoice['bill_state']=$invoice_supplier['state'];
        $invoice['bill_country']=$invoice_supplier['country'];
        $invoice['bill_postcode']=$invoice_supplier['post_code'];
        $invoice['bill_phone']=$invoice_supplier['phone_number'];
        $invoice['bill_vat']=$invoice_supplier['vat_number'];
        $invoice['bill_company']=$invoice_supplier['company_name'];
        $invoice['bill_email']=$invoice_supplier['email_address'];
   }
}
$invoice_charges        = array();
$invoice_history        = array();
$invoice_courier_quotes = array();
$invoice_payments       = array();
$invoice_porders        = array();
$invoice_freight_quotes = array();

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

$member             = array();
$member_information = array();
$member_addresses   = array();
$member_notify      = array();
$member_requests    = array();
$member_contacts    = array();
$member_couriers    = array();
$member_forwarders  = array();
//deleteproductimagesnotsaved();
$courier_list    = []; //getCourierproviderList();
if ($posted['site_id'] > 0) {
    $invoice['site_id'] = intval($posted['site_id']);
}

//else
//    $invoice['site_id']=11;

if ('' != $posted['status']) {
    $invoice['invoice_status'] = $posted['status'];
}

if (0 != $posted['member_id']) {
    $member_id            = intval($posted['member_id']);
    $invoice['member_id'] = $member_id;

    $member_information = $member = memberFromDatabase($member_id);
    $member_addresses   = memberShippingAddressesFromDatabase($member_id);
    $member_notify      = memberNotifyPartiesFromDatabase($member_id);
    $member_requests    = memberRequestsFromDatabase($member_id);
    $member_contacts    = memberContactsFromDatabase($member_id);
    $member_couriers    = memberCouriersFromDatabase($member_id);
    $member_forwarders  = memberForwardersFromDatabase($member_id);

    $invoice['bill_name']        = trim($member['first_name'] . ' ' . $member['last_name']);
    $invoice['bill_email']       = $member['email_address'];
    $invoice['bill_company']     = $member['company_name'];
    $invoice['bill_address']     = $member['address'];
    $invoice['bill_city']        = $member['city'];
    $invoice['bill_state']       = $member['state'];
    $invoice['bill_country']     = $member['country'];
    $invoice['bill_vat']         = $member['vat_number'];
    $invoice['bill_postcode']    = $member['post_code'];
    $invoice['bill_phone']       = $member['phone_no'];
    $invoice['invoice_currency'] = $member['currency'];

    if ('AU' == $member['country']) {
        $state_code               = substr($member['post_code'], 0, 1);
        $invoice['bill_timezone'] = 12;
        if ('0' == $state_code) {
            $invoice['bill_timezone'] = 8;
        }
        //Darwin
        if ('2' == $state_code) {
            $invoice['bill_timezone'] = 1;
        }
        //Sydney
        if ('3' == $state_code) {
            $invoice['bill_timezone'] = 2;
        }
        //Melbourne
        if ('4' == $state_code) {
            $invoice['bill_timezone'] = 6;
        }
        //Brisbane
        if ('5' == $state_code) {
            $invoice['bill_timezone'] = 7;
        }
        //Adelaide
        if ('6' == $state_code) {
            $invoice['bill_timezone'] = 9;
        }
        //Perth
        if ('7' == $state_code) {
            $invoice['bill_timezone'] = 2;
        }
        //Hobart
        if ('9' == $state_code) {
            $invoice['bill_timezone'] = 6;
        }
        //Brisbane
    }

    $ship                     = reset($member_addresses);
    $invoice['ship_name']     = $ship['contact'];
    $invoice['ship_email']    = $ship['email'];
    $invoice['ship_company']  = $ship['company_name'];
    $invoice['ship_address']  = $ship['address'];
    $invoice['ship_city']     = $ship['city'];
    $invoice['ship_state']    = $ship['state'];
    $invoice['ship_country']  = $ship['country'];
    $invoice['ship_postcode'] = $ship['post_code'];
    $invoice['ship_vat']      = $ship['vat_number'];
    $invoice['ship_phone']    = $ship['phone_no'];

    $invoice['notify_name']     = '';
    $invoice['notify_email']    = '';
    $invoice['notify_company']  = '';
    $invoice['notify_address']  = '';
    $invoice['notify_city']     = '';
    $invoice['notify_state']    = '';
    $invoice['notify_country']  = '';
    $invoice['notify_postcode'] = '';
    $invoice['notify_vat']      = '';
    $invoice['notify_phone']    = '';
}

?>
<h2>Add New <?= ($display_inquiry) ? 'Supplier Inquiry' : 'Supplier Invoice' ?></h2>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include __DIR__ . '/templates/addedit_invoice.tpl.php';

include_once __DIR__ . '/footer.inc.php';

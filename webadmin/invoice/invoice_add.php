<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('member'); //memberContactsFromDatabase, memberCouriersFromDatabase, memberForwardersFromDatabase, memberNotifyPartiesFromDatabase, memberShippingAddressesFromDatabase, memberFromDatabase, memberRequestsFromDatabase, memberSpecialsFromDatabase, memberWarningsFromDatabase, memberPrefsFromDatabase, memberNewPrefsFromDatabase

include_once __DIR__ . '/header.inc.php';

$action     = 'add';
$invoice_id = 0;

$invoice                = array_merge(invoiceDefault(), array(
    'invoice_status' => 'proforma',
));
if($posted['status'] == 'return'){
   $invoice['invoice_action'] = 'Working_on_solution';
}
$invoice_products       = array();
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

$member_shippingaddresses = array();
$member_contacts          = array();
$member_couriers          = array();
$member_forwarders        = array();
$member_information       = array();
$member_notifyparties     = array();
$member_requests          = array();
$member_specials          = array();
$member_warnings          = array();

if (array_key_exists('site_id', $posted) && $posted['site_id'] > 0) {
    $invoice['site_id'] = intval($posted['site_id']);
}

if (array_key_exists('status', $posted) && '' != $posted['status']) {
    $invoice['invoice_status'] = $posted['status'];
}

if (array_key_exists('member_id', $posted) && 0 != $posted['member_id']) {
    $member_id            = intval($posted['member_id']);
    $invoice['member_id'] = $member_id;

    $member_information       = $member = memberFromDatabase($member_id);
    $member_shippingaddresses = memberShippingAddressesFromDatabase($member_id);
    $member_notifyparties     = memberNotifyPartiesFromDatabase($member_id);
    //$member_requests          = memberRequestsFromDatabase($member_id);
    $member_contacts          = memberContactsFromDatabase($member_id);
    $member_couriers          = memberCouriersFromDatabase($member_id);
    $member_specials          = memberSpecialsFromDatabase($member_id);
    $member_forwarders        = memberForwardersFromDatabase($member_id);
    $member_warnings          = memberWarningsFromDatabase($member_id);
    $prefs                    = memberPrefsFromDatabase($member_id);
    $cnv_prefs                = memberNewPrefsFromDatabase($member_id);

    $invoice['bill_name']        = trim($member['first_name'] . ' ' . $member['last_name']);
    $invoice['bill_email']       = $member['email_address'];
    $invoice['bill_company']     = $member['company_name'];
    $invoice['bill_address']     = $member['address'];
    $invoice['bill_city']        = $member['city'];
    $invoice['bill_state']       = $member['state'];
    $invoice['bill_country']     = $member['country'];
    $invoice['bill_postcode']    = $member['post_code'];
    $invoice['bill_phone']       = $member['phone_no'];
    $invoice['invoice_currency'] = ('' != $cnv_prefs['currency']) ? $cnv_prefs['currency'] : $prefs['he_currency'];

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

    $member_shippingaddress   = reset($member_shippingaddresses);
    $invoice['ship_name']     = $member_shippingaddress['contact'];
    $invoice['ship_email']    = $member_shippingaddress['email'];
    $invoice['ship_company']  = $member_shippingaddress['company_name'];
    $invoice['ship_address']  = $member_shippingaddress['address'];
    $invoice['ship_city']     = $member_shippingaddress['city'];
    $invoice['ship_state']    = $member_shippingaddress['state'];
    $invoice['ship_country']  = $member_shippingaddress['country'];
    $invoice['ship_postcode'] = $member_shippingaddress['post_code'];
    $invoice['ship_phone']    = $member_shippingaddress['phone_no'];

    $invoice['notify_name']     = '';
    $invoice['notify_email']    = '';
    $invoice['notify_company']  = '';
    $invoice['notify_address']  = '';
    $invoice['notify_city']     = '';
    $invoice['notify_state']    = '';
    $invoice['notify_country']  = '';
    $invoice['notify_postcode'] = '';
    $invoice['notify_phone']    = '';
}

$invoice_addresses='';
if ($row = $DB->query_first(sprintf(
    "SELECT `id` FROM `invoice_address` WHERE  `flag`='0' AND `main`='y' LIMIT 1"
), MYSQLI_ASSOC)) {       
    $invoice_addresses = $row['id'];
}

?>
<h2>Add New <?=($display_inquiry) ? 'Inquiry' : 'Invoice'?></h2>
<?php

include __DIR__ . '/templates/addedit_invoice.tpl.php';

include_once __DIR__ . '/footer.inc.php';

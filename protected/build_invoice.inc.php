<?php
/**
 * Construct a new invoice for processing
 *
 * Returns:
 *  $payment_methods
 *  $invoice
 *  $invoice_charges
 *  $invoice_courier_quotes
 *  $invoice_products
 *  $invoice_gifts
 *  $invoice_discounts
 *  $issues
 */
?><?php

namespace app;

use Freight_Service_Handler;
use app\models\TwigProduct;
session_start();
global $_GLOBALS;
global $DB;

moduleLoad('bulk'); //bulkFromDatabase, bulkProductsFromDatabase
moduleLoad('freight'); //getFreightServices
moduleLoad('insurance'); //insuranceCalculateAmount
moduleLoad('invoice'); //invoiceGetOptionList, invoiceSearch
moduleLoad('member'); //memberDiscountsFromDatabase, memberProductSpecialPrices
moduleLoad('packing'); //packingFromCache
moduleLoad('payment'); //paymentMethodsFromDatabase, paymentMethodQuery
moduleLoad('product'); //productFromDatabase
moduleLoad('voucher');

if (!function_exists('bcceil')) {
    /**
     * @param string $number
     *
     * @return string
     */
    function bcceil($number)
    {
        $diff = bcsub($number, bcadd($number, '0.0', 0), 4);
        if (bccomp($diff, '0.0', 4) != 0) {
            return bcadd($number, bcsub('1.0', $diff, 4), 0);
        }

        return bcadd($number, '0.0', 0);
    }
}

//Just in case
bcscale(6);
$payment_methods = [];
if (function_existS('paymentMethodsFromDatabase') && function_exists('paymentMethodQuery')) {
    foreach (paymentMethodsFromDatabase(paymentMethodQuery([
        'site_id'               => $_GLOBALS['site_settings']['site_id'],
        'payment_method_public' => 'y',
    ])) as $row) {
        $payment_methods[$row['payment_method_id']] = $row;
    }
} else {
    // @todo phase this out, use the module functions
    if ($query = $DB->query(sprintf(
        "SELECT *, `payment_method_id` AS `id` FROM `highwaygroup`.`payment_methods` WHERE `site_id` = %d AND `payment_method_public` = 'y' ORDER BY `payment_method_name` ASC",
        $_GLOBALS['site_settings']['site_id']
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $payment_methods[$row['id']] = $row;
        }
        $DB->free_result($query);
    }
}

$null_match = [null, '0000-00-00', '0000-00-00 00:00:00'];

$timezone_by_postcode = [
    '0' => 8, //Darwin
    '2' => 1, //Sydney
    '3' => 2, //Melbourne
    '4' => 6, //Brisbane
    '5' => 7, //Adelaide
    '6' => 9, //Perth
    '7' => 2, //Hobart
    '9' => 6, //Brisbane
];
$currency_keys = ['', 'au', 'us'];
$price_keys    = ['cost', 'offer', 'online'];
$section_keys  = ['products', 'ship', 'charges'];
$product_cache = [];

$plug_list    = range(0, 999);
$voltage_list = [
    1 => '110-127V',
    5 => '220-240V',
    7 => '110-240V',
    8 => '240V',
    9 => '110/220V',
];

$availability_list = [];
$condition_list    = [];
$location_list     = [];
$manufacturer_list = [];
$supplier_list     = [];
$warranty_list     = [];
if (function_exists('productGetOptionList')) {
    $availability_list = productGetOptionList('availability');
    $condition_list    = productGetOptionList('condition');
    $location_list     = productGetOptionList('location');
    $manufacturer_list = productGetOptionList('manufacturer');
    $supplier_list     = productGetOptionList('supplier');
    $warranty_list     = productGetOptionList('warranty');
    unset($condition_list[1]);
}

$courier_list = [];
if (function_exists('getFreightServices')) {
    $courier_list = getFreightServices('personal', '', true);
}

$packages = [];
$issues   = [];

$invoice = [
    'ship_mass'              => '0.000',

    'access_date'            => formatDateTime(),
    'handler_id'             => 2,
    'he_id'                  => ('dom' == ($_GLOBALS['site_settings']['dom_int'] ?? '')) ? 'dom' : 'int',
    'invoice_currency'       => $_GLOBALS['site_options']['DEFAULT_CURRENCY'] ?? 'USD',
    'newsletter_id'          => (array_key_exists('newsletter', $_GLOBALS)) ? $_GLOBALS['newsletter']['id'] : 0,
    'open_date'              => formatDateTime(),
    'site_id'                => $_GLOBALS['site_settings']['site_id'],
    'write_date'             => formatDateTime(),

    'bill_name'              => '',
    'bill_address'           => '',
    'bill_city'              => '',
    'bill_company'           => '',
    'bill_country'           => '',
    'bill_email'             => '',
    'bill_phone'             => '',
    'bill_postcode'          => '',
    'bill_state'             => '',

    'ship_name'              => '',
    'ship_address'           => '',
    'ship_city'              => '',
    'ship_company'           => '',
    'ship_country'           => '',
    'ship_email'             => '',
    'ship_phone'             => '',
    'ship_postcode'          => '',
    'ship_state'             => '',

    'notify_name'            => '',
    'notify_address'         => '',
    'notify_city'            => '',
    'notify_company'         => '',
    'notify_country'         => '',
    'notify_email'           => '',
    'notify_phone'           => '',
    'notify_postcode'        => '',
    'notify_state'           => '',

    'needby_date'            => null,
    'backorder_flag'         => 'n',
    'customer_requests'      => '',
    'frequency_id'           => 0,
    'invoice_action'         => 'payment',
    'invoice_comments'       => '',
    'invoice_status'         => 'sales',
    'own_freight_option'     => 'courier',
    'pack_requirement_id'    => 0,
    'payment_terms_id'       => 0,
    'payments_locate_date'   => null,
    'payments_staff_id'      => 0,
    'paymenttype_id'         => '',
    'proforma_tracknumber'   => '',
    'harmonised_code'        => '',
    'origin_country'         => '',
    'plug_id'                => 0,
    'products_locate_date'   => formatDateTime(),
    'products_staff_id'      => 0,
    'proforma_date'          => formatDateTime(),
    'discount_code'          => '',
    'proforma_purchaseorder' => '',
    'rand_num'               => mt_rand(0, 65535),
    'revision_id'            => 0,
    'ship_account_num'       => '',
    'ship_cubic'             => '0.000',
    'ship_volume'            => '0.000',
    'ship_express'           => 'n',
    'ship_id'                => 0,
    'ship_insurance'         => 'n',
    'ship_locate_date'       => null,
    'ship_product_name'      => '',
    'ship_product_notes'     => '',
    'ship_receipted'         => 'n',
    'ship_registered'        => 'n',
    'ship_staff_id'          => 0,
    'ship_supplier_id'       => 0,
    'ship_supplier_name'     => '',
    'ship_tailgate'          => 'y',
    'ship_weight'            => '0.000',
    'ship_weight_free'            => '0.000',
    'shipping_method'        => '',
    'supply_country'         => $_GLOBALS['site_settings']['supply_country'],
    'supply_locate_date'     => null,
    'supply_notes'           => '',
    'terms_id'               => 0,
    'voltage_id'             => 0,
    'waranty_terms_id'       => 0,
    'forwarder_account'      => '',
    'forwarder_address'      => '',
    'forwarder_city'         => '',
    'forwarder_company_name' => '',
    'forwarder_contact'      => '',
    'forwarder_country'      => '',
    'forwarder_email'        => '',
    'forwarder_fax_no'       => '',
    'forwarder_mobile_no'    => '',
    'forwarder_phone_no'     => '',
    'forwarder_post_code'    => '',
    'forwarder_state'        => '',
    'def_condition'          => 0,
    'def_contacts'           => '',
    'def_country'            => '',
    'def_currency'           => '',
    'def_packing'            => 0,
    'def_plug'               => 0,
    'def_terms'              => 0,
    'def_voltage'            => 0,
    'def_warranty'           => 0,
    'products_cost'          => '0.00',
    'products_costau'        => '0.00',
    'products_costus'        => '0.00',
    'products_offer'         => '0.00',
    'products_offerau'       => '0.00',
    'products_offerus'       => '0.00',
    'products_online'        => '0.00',
    'products_onlineau'      => '0.00',
    'products_onlineus'      => '0.00',
    'ship_cost'              => '0.00',
    'ship_costau'            => '0.00',
    'ship_costus'            => '0.00',
    'ship_offer'             => '0.00',
    'ship_offerau'           => '0.00',
    'ship_offerus'           => '0.00',
    'ship_online'            => '0.00',
    'ship_onlineau'          => '0.00',
    'ship_onlineus'          => '0.00',
    'charges_cost'           => '0.00',
    'charges_costau'         => '0.00',
    'charges_costus'         => '0.00',
    'charges_offer'          => '0.00',
    'charges_offerau'        => '0.00',
    'charges_offerus'        => '0.00',
    'charges_online'         => '0.00',
    'charges_onlineau'       => '0.00',
    'charges_onlineus'       => '0.00',
    'invoice_cost'           => '0.00',
    'invoice_costau'         => '0.00',
    'invoice_costus'         => '0.00',
    'invoice_offer'          => '0.00',
    'invoice_offerau'        => '0.00',
    'invoice_offerus'        => '0.00',
    'invoice_offereu'        => '0.00',
    'invoice_online'         => '0.00',
    'invoice_onlineau'       => '0.00',
    'invoice_onlineus'       => '0.00',
    'invoice_profit'         => '0.00',
    'invoice_profitau'       => '0.00',
    'invoice_profitus'       => '0.00',
];

/**
 * Set visitor related data
 */
if (array_key_exists('visitor', $_GLOBALS) && is_array($_GLOBALS['visitor'])) {
    foreach ([
        'visitor_id'       => 'id',
        'bill_name'        => 'full_name',
        'bill_address'     => 'address',
        'bill_city'        => 'city',
        'bill_company'     => 'company_name',
        'bill_country'     => 'country',
        'bill_email'       => 'email_address',
        'bill_phone'       => 'phone_no',
        'bill_postcode'    => 'post_code',
        'bill_state'       => 'state',
        'invoice_currency' => 'currency',
        'notify_name'      => 'full_name',
        'notify_address'   => 'address',
        'notify_city'      => 'city',
        'notify_company'   => 'company_name',
        'notify_country'   => 'country',
        'notify_email'     => 'email_address',
        'notify_phone'     => 'phone_no',
        'notify_postcode'  => 'post_code',
        'notify_state'     => 'state',
        'ship_name'        => 'full_name',
        'ship_address'     => 'address',
        'ship_city'        => 'city',
        'ship_company'     => 'company_name',
        'discount_code'    => 'discount_code',
        'ship_country'     => 'country',
        'ship_email'       => 'email_address',
        'ship_phone'       => 'phone_no',
        'ship_postcode'    => 'post_code',
        'ship_state'       => 'state',
        'timezone'         => 'timezone',
        'vat_number'       => 'vat_number',
    ] as $k => $v) {
        if (array_key_exists($v, $_GLOBALS['visitor']) && !is_null($_GLOBALS['visitor'][$v])) {
            $invoice[$k] = $_GLOBALS['visitor'][$v];
        }
    }
    if(isset($_POST['checkout']['ship_country']) && $_POST['checkout']['ship_country']!=''){
        $invoice['ship_country']=$_POST['checkout']['ship_country'];
    }
    $invoice['bill_timezone'] = ('AU' == $_GLOBALS['visitor']['country'] && preg_match('/^[02-79]\\d{3}$/', $_GLOBALS['visitor']['post_code'])) ? $timezone_by_postcode[substr($_GLOBALS['visitor']['post_code'], 0, 1)] : 0;

    if ($row = $DB->query_first(sprintf(
        "SELECT `voltage`, `plugs` FROM `world`.`country` WHERE `code` = %s LIMIT 1",
        $DB->escape($invoice['ship_country'])
    ), MYSQLI_ASSOC)) {
        $temp = intval($row['voltage']);
        if (240 == $temp) {
            $invoice['voltage_id'] = 8;
        } elseif ($temp >= 110 && $temp <= 127) {
            $invoice['voltage_id'] = 1;
        } elseif ($temp >= 220 && $temp <= 240) {
            $invoice['voltage_id'] = 5;
        } elseif ($temp >= 110 && $temp <= 240) {
            $invoice['voltage_id'] = 7;
        }

        $invoice['plug_id'] = 0;
        foreach (explode(',', $row['plugs']) as $v) {
            if (preg_match('/#(\\d+)$/', $v, $matches)) {
                $invoice['plug_id'] = intval($matches[1]);
                break;
            }
        }
    }
}

/**
 * Set member related data
 */
if (array_key_exists('member', $_GLOBALS) && is_array($_GLOBALS['member'])) {
    foreach ([
        'member_id'        => 'id',
        'bill_name'        => 'full_name',
        'bill_address'     => 'address',
        'bill_city'        => 'city',
        'bill_company'     => 'company_name',
        'bill_country'     => 'country',
        'bill_email'       => 'email_address',
        'bill_phone'       => 'phone_no',
        'bill_postcode'    => 'post_code',
        'bill_state'       => 'state',
        'invoice_currency' => 'currency',
        'notify_name'      => 'full_name',
        'notify_address'   => 'address',
        'notify_city'      => 'city',
        'notify_company'   => 'company_name',
        'notify_country'   => 'country',
        'notify_email'     => 'email_address',
        'notify_phone'     => 'phone_no',
        'notify_postcode'  => 'post_code',
        'notify_state'     => 'state',
        'ship_name'        => 'full_name',
        'ship_address'     => 'address',
        'ship_city'        => 'city',
        'ship_company'     => 'company_name',
        'user_account_type'=> 'account_type',
        'discount_code'    => 'discount_code',
        'ship_country'     => 'country',
        'ship_email'       => 'email_address',
        'ship_phone'       => 'phone_no',
        'ship_postcode'    => 'post_code',
        'ship_state'       => 'state',
        'timezone'         => 'timezone',
        'vat_number'       => 'vat_number',
    ] as $k => $v) {
        if (array_key_exists($v, $_GLOBALS['member']) && !is_null($_GLOBALS['member'][$v])) {
            $invoice[$k] = $_GLOBALS['member'][$v];
        }
    }

    $invoice['bill_timezone'] = ('AU' == $_GLOBALS['member']['country'] && preg_match('/^[02-79]\\d{3}$/', $_GLOBALS['member']['post_code'])) ? $timezone_by_postcode[substr($_GLOBALS['member']['post_code'], 0, 1)] : 0;

    /**
     * Set the default shipping address
     */
    foreach ($_GLOBALS['member']['ship_addr'] ?? [] as $member_shippingaddress) {
        $member_shippingaddress['contact']=($member_shippingaddress['contact'] ?? $member_shippingaddress['name'] ?? '').' '.($member_shippingaddress['contact_last'] ?? $member_shippingaddress['surname'] ?? '');
        foreach ([
            'ship_name'     => 'contact',
            'ship_address'  => 'address',
            'ship_city'     => 'city',
            'ship_company'  => 'company_name',
            'ship_country'  => 'country',
            'ship_email'    => 'email',
            'ship_needs'    => 'instructions',
            'ship_phone'    => 'phone_no',
            'ship_postcode' => 'post_code',
            'ship_state'    => 'state',
        ] as $k => $v) {
            if (array_key_exists($v, $member_shippingaddress)) {
                $invoice[$k] = $member_shippingaddress[$v];
            }
        }
        $invoice['shipaddr_id']  = $member_shippingaddress['id'];
        if ('y' == $member_shippingaddress['default']) {
            break;
        }
    }
    if ($row = $DB->query_first(sprintf(
        "SELECT `voltage`, `plugs` FROM `world`.`country` WHERE `code` = %s LIMIT 1",
        $DB->escape($invoice['bill_country'])
    ), MYSQLI_ASSOC)) {
        $temp = intval($row['voltage']);
        if (240 == $temp) {
            $invoice['voltage_id'] = 8;
        } elseif ($temp >= 110 && $temp <= 127) {
            $invoice['voltage_id'] = 1;
        } elseif ($temp >= 220 && $temp <= 240) {
            $invoice['voltage_id'] = 5;
        } elseif ($temp >= 110 && $temp <= 240) {
            $invoice['voltage_id'] = 7;
        }

        $invoice['plug_id'] = 0;
        foreach (explode(',', $row['plugs']) as $v) {
            if (preg_match('/#(\\d+)$/', $v, $matches)) {
                $invoice['plug_id'] = intval($matches[1]);
                break;
            }
        }
    }
    if ($row = $DB->query_first(sprintf(
        "SELECT `voltage`, `plugs` FROM `world`.`country` WHERE `code` = %s LIMIT 1",
        $DB->escape($invoice['ship_country'])
    ), MYSQLI_ASSOC)) {
        $temp = intval($row['voltage']);
        if (240 == $temp) {
            $invoice['voltage_id'] = 8;
        } elseif ($temp >= 110 && $temp <= 127) {
            $invoice['voltage_id'] = 1;
        } elseif ($temp >= 220 && $temp <= 240) {
            $invoice['voltage_id'] = 5;
        } elseif ($temp >= 110 && $temp <= 240) {
            $invoice['voltage_id'] = 7;
        }

        $invoice['plug_id'] = 0;
        foreach (explode(',', $row['plugs']) as $v) {
            if (preg_match('/#(\\d+)$/', $v, $matches)) {
                $invoice['plug_id'] = intval($matches[1]);
                break;
            }
        }
    }
}

/**
 * Validate the discount codes
 */
$invoice_discounts = [];
if (trim($invoice['discount_code']) != '') {
    //Check if customer has recent processed orders
    $temp = 0;
    if (0 != $_GLOBALS['member']['id']) {
        $criteria                   = $_GLOBALS['criteria_options']['invoice'];
        $criteria['member_id']      = $_GLOBALS['member']['id'];
        $criteria['invoice_status'] = 'processed';
        $temp                       = (count(invoiceSearch($criteria, strtotime('-1 year'))));
    }

    //Matchup all the given discount codes
     if ($query = $DB->query(sprintf(
        "SELECT * FROM `highwaygroup`.`gift_coupon` WHERE FIND_IN_SET(`name`, %s) AND (`expiry_date` = '0000-00-00 00:00:00' OR `expiry_date` > NOW()) AND `active` = 'y' AND (`site_id` = '' OR `site_id` = %d)",
        $DB->escape(trim($invoice['discount_code'])),
        $_GLOBALS['site_settings']['site_id']
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            if (0 != $temp && bccomp($row['bonus'], '0.00', 2) != 0) {
                $row['value'] = bcadd($row['value'], $row['bonus'], 2);
                $row['bonus'] = '0.00';
            }
            $row['price_deducted'] = '0.00';

            $invoice_discounts[$row['name']] = $row;
        }
        $DB->free_result($query);
    }

    //Rebuild the discount code
    $invoice['discount_code'] = implode(',', array_keys($invoice_discounts));
}
/**
 * Try to match world timezones
 */
if ($row = $DB->query_first(sprintf(
    "SELECT `id` FROM `world`.`time_zones` WHERE `country` = %s ORDER BY IF(`name` = %s, 2, IF(`name` = %s, 1, 0)) DESC LIMIT 1",
    $DB->escape($invoice['bill_country']),
    $DB->escape($invoice['bill_state']),
    $DB->escape($invoice['bill_city'])
), MYSQLI_ASSOC)) {
    $invoice['bill_timezone'] = $row['id'];
}

/**
 * Override with checkout choices
 */
if (array_key_exists('checkout', $_REQUEST) && is_array($_REQUEST['checkout'])) {

    /**
     * Override billing address
     */
    foreach ([
        'bill_name',
        'bill_company',
        'bill_country',
        'bill_address',
        'bill_city',
        'bill_state',
        'bill_postcode',
        'bill_email',
        'bill_phone',
    ] as $k) {
        if (array_key_exists($k, $_REQUEST['checkout'])) {
            $invoice[$k] = $_REQUEST['checkout'][$k];
        }
    }

    /**
     * Override shipping address
     */
    if (array_key_exists('shipaddr_id', $_REQUEST['checkout']) && is_array($_GLOBALS['member']['ship_addr'])) {
        foreach ($_GLOBALS['member']['ship_addr'] as $member_shippingaddress) {
            if ($_REQUEST['checkout']['shipaddr_id'] == $member_shippingaddress['id']) {
                $member_shippingaddress['contact']=($member_shippingaddress['contact'] ?? $member_shippingaddress['name'] ?? '').' '.($member_shippingaddress['contact_last'] ?? $member_shippingaddress['surname'] ?? '');
                foreach ([
                    'ship_name'     => 'contact',
                    'ship_address'  => 'address',
                    'ship_city'     => 'city',
                    'ship_company'  => 'company_name',
                    'ship_country'  => 'country',
                    'ship_email'    => 'email',
                    'ship_needs'    => 'instructions',
                    'ship_phone'    => 'phone_no',
                    'ship_postcode' => 'post_code',
                    'ship_state'    => 'state',
                ] as $k => $v) {
                    if (array_key_exists($v, $member_shippingaddress)) {
                        $invoice[$k] = $member_shippingaddress[$v];
                    }
                }
                $invoice['shipaddr_id']  = $member_shippingaddress['id'];
                break;
            }
        }
    }

    /**
     * Override shipping instructions
     */
    if (array_key_exists('ship_needs', $_REQUEST['checkout'])) {
        $invoice['ship_needs'] = $_REQUEST['checkout']['ship_needs'];
    }

    /**
     * Override customer requests
     */
    if (array_key_exists('customer_requests', $_REQUEST['checkout'])) {
        $invoice['customer_requests'] = $_REQUEST['checkout']['customer_requests'];
    }

    /**
     * Override purchase order number
     */
    if (array_key_exists('proforma_purchaseorder', $_REQUEST['checkout'])) {
        $invoice['proforma_purchaseorder'] = $_REQUEST['checkout']['proforma_purchaseorder'];
    }

    /**
     * Override currency and payment type
     */
    if (array_key_exists('paymenttype_id', $_REQUEST['checkout'])) {
        foreach ($payment_methods as $method) {
            if ($method['payment_method_id'] == $_REQUEST['checkout']['paymenttype_id']) {
                $invoice['paymenttype_id'] = $method['payment_method_id'];
                $accepted                  = explode(',', $method['accepted_currencies']);

                if ('' != $method['accepted_currencies'] && !in_array($invoice['invoice_currency'], $accepted)) {
                    $invoice['invoice_currency'] = reset($accepted);
                }

                if (array_key_exists('checkout', $_REQUEST) &&
                    array_key_exists('payment_currency', $_REQUEST['checkout']) &&
                    array_key_exists($invoice['paymenttype_id'], $_REQUEST['checkout']['payment_currency']) &&
                    in_array($_REQUEST['checkout']['payment_currency'][$invoice['paymenttype_id']], $accepted)) {
                    $invoice['invoice_currency'] = $_REQUEST['checkout']['payment_currency'][$invoice['paymenttype_id']];
                }
                break;
            }
        }
    }

    /**
     * Override default contacts
     */
    /*
    $temp = [];
    if (is_array($_GLOBALS['member']['contacts']) && is_array($_REQUEST['checkout']['notify'])) {
        foreach ($_GLOBALS['member']['contacts'] as $contact) {
            if (in_array($contact['id'], $_REQUEST['checkout']['notify'])) {
                $temp[] = $contact['id'];
            }
        }
    }
    $invoice['def_contacts'] = implode(',', $temp);
    */
}

/**
 * Always force Arcade Spare Parts to USD
 */
if (20 == $_GLOBALS['site_settings']['site_id'] && in_array($invoice['paymenttype_id'], ['AMEX', 'VISA', 'MASTERCARD'])) {
    $invoice['invoice_currency'] = 'USD';
}

/**
 * Always force Arcooda to USD
 */
if (39 == $_GLOBALS['site_settings']['site_id']) {
    $invoice['invoice_currency'] = 'USD';
}

/**
 * Set the default cubic divisor
 */
$cubic_divisor = '5000';
// if ($invoice['supply_country'] != $invoice['ship_country']) {
//     $cubic_divisor = '4000';
// }

$invoice['invoice_exchange']   = convertCurrencyNEw('1.0000', $invoice['invoice_currency'], 'AUD', false, 4);
$invoice['invoice_exchangeus'] = convertCurrencyNEw('1.0000', $invoice['invoice_currency'], 'USD', false, 4);

$count            = 0;
$invoice_products = [];
$invoice_gifts = [];
/**
 * Set the shopping cart products
 */
$cart_array_data = ('quote' == ($_POST['type'] ?? $_GET['type'] ?? 'shopping')) ? ($_GLOBALS['quote_cart'] ?? []) : ($_GLOBALS['shopping_cart'] ?? []);

if (is_array($cart_array_data)) {
    /**
     * Hack for old shopping_cart
     */
    foreach ($cart_array_data as $k => $v) {
        if (!is_array($v)) {
            $cart_array_data[$k] = [
                'product_id'       => $k,
                'sub_id'           => 0,
                'product_qty'      => $v,
                'is_gift'          => 'n',
                'generic'          => 'n',
                'site_id'          => $_GLOBALS['site_settings']['site_id'],
                'power_id'         => 0,
                'adapter_id'       => 0,
                'used'             => 'n',
                'dependant_id'     => 0,
                'dependant_sub_id' => 0,
                'generic_code'     => '',
            ];
        }
    }

    /**
     * Gift dependancy check - First build a list of possible gifts
     * Note: needs rewrite, there is currently no check for correct dependant
     */
    $base_cart = [];
    $max_gifts = [];

    foreach ($cart_array_data as $k => $v) {
        if ('y' != ($v['is_gift'] ?? 'n') && 'n' == ($v['generic'] ?? 'n')) {
            $criteria               = $_GLOBALS['criteria_options']['product'];
            $criteria['product_id'] = $v['product_id'];
            $criteria['sub_id']     = $v['sub_id'];
            $product_query          = productQuery($criteria);
            if (count(productSearch($product_query)) > 0) {
                $product_str = productStrJoin($v['product_id'], $v['sub_id']);
                if (array_key_exists($product_str, $base_cart)) {
                    $base_cart[$product_str] += $v['product_qty'];
                } else {
                    $base_cart[$product_str] = $v['product_qty'];
                }

                if (array_key_exists($product_str, $product_cache)) {
                    $product = $product_cache[$product_str];
                } else {
                    $product = productFromDatabase($product_str);
                    if (function_exists('productAlternatesFromDatabase')) {
                        foreach (productAlternatesFromDatabase($product_str) as $v3) {
                            if ($v3['site_id'] == $_GLOBALS['site_settings']['site_id']) {
                                if ('' != $v3['image_file']) {
                                    unset($product['thumb_file']);
                                }

                                foreach ($v3 as $k2 => $v2) {
                                    if ('id' != $k2 && '' != $v2) {
                                        $product[$k2] = $v2;
                                    }
                                }
                            }
                        }
                    }

                    $product_cache[$product_str] = $product;
                }
                if (array_key_exists('promotion', $product) && 'y' == $product['promotion'] && in_array('gift', explode(',', $product['promotion_type']))) {
                    if (function_exists('productGiftsFromDatabase')) {
                        foreach (productGiftsFromDatabase($v['product_id'], $v['sub_id']) as $v2) {
                            $product_str = productStrJoin($v2['linked_id'], $v2['linked_sub_id']);
                            if (array_key_exists($product_str, $max_gifts)) {
                                $max_gifts[$product_str] += $v['product_qty'] * $v2['qty'];
                            } else {
                                $max_gifts[$product_str] = $v['product_qty'] * $v2['qty'];
                            }
                        }
                    }
                }
            } else {
                unset($cart_array_data[$k]);
            }
        }
    }

    /**
     * Gift dependancy check - Remove any gifts that aren't accountable
     */
    foreach ($cart_array_data as $k => $v) {
        if ('y' == ($v['is_gift'] ?? 'n')) {
            $product_str = productStrJoin($v['product_id'], $v['sub_id']);
            if (!array_key_exists($product_str, $max_gifts)) {
                unset($cart_array_data[$k]);
            } elseif ($max_gifts[$product_str] > 0) {
                if ($max_gifts[$product_str] >= $v['product_qty']) {
                    $max_gifts[$product_str] -= $v['product_qty'];
                } else {
                    $cart_array_data[$k]['product_qty'] = $max_gifts[$product_str];
                    $max_gifts[$product_str]                      = 0;
                }
            } else {
                unset($cart_array_data[$k]);
            }
        }
    }
    $iterationCount = 0;
    foreach ($cart_array_data as $cart_key => $v) {
        if (!array_key_exists('site_id', $v)) {
            $v['site_id'] = intval($_GLOBALS['site_settings']['site_id']);
        }

        switch ($v['generic'] ?? 'n') {
            default:
            case 'n':
                $product_str = productStrJoin(intval($v['product_id']), intval($v['sub_id']));
                if (array_key_exists($product_str, $product_cache)) {
                    $product = $product_cache[$product_str];
                } else {
                    $product = productFromDatabase($product_str);
                    if (function_exists('productAlternatesFromDatabase')) {
                        foreach (productAlternatesFromDatabase($product_str) as $v3) {
                            if ($v3['site_id'] == $_GLOBALS['site_settings']['site_id']) {
                                if ('' != $v3['image_file']) {
                                    unset($product['thumb_file']);
                                }

                                foreach ($v3 as $k2 => $v2) {
                                    if ('id' != $k2 && '' != $v2) {
                                        $product[$k2] = $v2;
                                    }
                                }
                            }
                        }
                    }

                    $product_cache[$product_str] = $product;
                }

                $product['normal_price']       = convertCurrencyNEw($product['primary_price'], $product['primary_currency'], $invoice['invoice_currency'], false, $product['precision']);
                $product['normal_currency']    = $invoice['invoice_currency'];
                $product['effective_price']    = convertCurrencyNEw($product['secondary_price'], $product['secondary_currency'], $invoice['invoice_currency'], false, $product['precision']);
                $product['effective_currency'] = $invoice['invoice_currency'];
                $product['effective_savings']  = (bccomp($product['normal_price'], $product['effective_price'], $product['precision']) > 0) ? bcdiv(bcmul(bcsub($product['normal_price'], $product['effective_price'], $product['precision']), '100.00', 2), $product['normal_price'], 0) : '0';

                $dimensions = [];
                if (function_exists('productDimensionFromDatabase')) {
                    $dimensions = productDimensionFromDatabase($product_str);
                    if (count($dimensions) && !is_array(reset($dimensions))) {
                        $dimensions = [$dimensions];
                    }
                }

                $packing = [];
                if (function_exists('productPackingFromDatabase')) {
                    $packing = productPackingFromDatabase($product_str);
                    if (count($packing) && !is_array(reset($packing))) {
                        $packing = [$packing];
                    }
                }

                /**
                 * Collect the packaging sizes
                 */
                $bulk_sizes = [];
                foreach ($dimensions as $row) {
                    $cubic = bcmul($row['width'] ?? '0.0', bcmul($row['depth'] ?? '0.0', $row['height'] ?? '0.0', 3), 3);
                    $bulk_sizes[1] = [
                        [
                            'qty'    => 1,
                            'weight' => $row['weight'] ?? '0.000',
                            'cubic'  => (bccomp($cubic, '0.000', 3) > 0) ? $cubic : bcmul($row['cubic'] ?? '0.000', (0 != ($row['cubic_divisor'] ?? 0)) ? $row['cubic_divisor'] : $cubic_divisor, 3),
                            'width'  => $row['width'] ?? '0.0',
                            'depth'  => $row['depth'] ?? '0.0',
                            'height' => $row['height'] ?? '0.0',
                        ],
                    ];
                }
                if (function_exists('productBulkWeightsFromDatabase')) {
                    foreach (productBulkWeightsFromDatabase($product_str) as $row) {
                        $cubic                   = bcmul($row['width'], bcmul($row['depth'], $row['height'], 3), 3);
                        $bulk_sizes[$row['qty']] = [
                            [
                                'qty'    => $row['qty'],
                                'weight' => $row['weight'],
                                'cubic'  => (bccomp($cubic, '0.000', 3) > 0) ? $cubic : bcmul($row['cubic'], (0 != $row['cubic_divisor']) ? $row['cubic_divisor'] : $cubic_divisor, 3),
                                'width'  => $row['width'],
                                'depth'  => $row['depth'],
                                'height' => $row['height'],
                            ],
                        ];
                    }
                }

                if (count($packing)) {
                    $bulk_sizes[1] = [];
                    foreach ($packing as $row) {
                        $bulk_sizes[1][] = [
                            'qty'    => 1,
                            'weight' => $row['weight'],
                            'cubic'  => bcmul($row['width'], bcmul($row['depth'], $row['height'], 3), 3),
                            'width'  => $row['width'],
                            'depth'  => $row['depth'],
                            'height' => $row['height'],
                        ];
                    }
                }
                unset($bulk_sizes[0]);
                krsort($bulk_sizes);

                /**
                 * Estimate packages with no dimensions
                 */
                $changes = true;
                while ($changes) {
                    $changes = false;
                    foreach ($bulk_sizes as $k1 => $v1) {
                        if (count($v1) == 1 && (bccomp($v1[0]['width'], '0.00', 2) == 0 || bccomp($v1[0]['depth'], '0.00', 2) == 0 || bccomp($v1[0]['height'], '0.00', 2) == 0)) {
                            foreach ($bulk_sizes as $k2 => $v2) {
                                if ($k2 < $k1 && count($v2) == 1) {
                                    if (bccomp($v2[0]['width'], '0.00', 2) != 0 && bccomp($v2[0]['depth'], '0.00', 2) != 0 && bccomp($v2[0]['height'], '0.00', 2) != 0) {
                                        $multiplier = (float)$k1 / (float)$k2;
                                        $root_x     = ceil(pow($multiplier, 1.0 / 3.0));
                                        $root_y     = ceil(sqrt($multiplier / $root_x));
                                        $root_z     = ceil($multiplier / ($root_x * $root_y));
                                        $dim        = [
                                            $v2[0]['width'],
                                            $v2[0]['depth'],
                                            $v2[0]['height'],
                                        ];
                                        bcscale(2);
                                        usort($dim, 'bccomp');
                                        $bulk_sizes[$k1][0]['width']  = bcmul($dim[0], (string)$root_x, 2);
                                        $bulk_sizes[$k1][0]['depth']  = bcmul($dim[1], (string)$root_y, 2);
                                        $bulk_sizes[$k1][0]['height'] = bcmul($dim[2], (string)$root_z, 2);
                                        $changes                      = true;
                                    }
                                    break;
                                }
                            }
                        }
                    }
                }

                $packages  = [];
                $qty_count = $v['product_qty'];
                foreach ($bulk_sizes as $qty => $row) {
                    while ($qty <= $qty_count) {
                        $packages = array_merge($packages, $row);
                        $qty_count -= $qty;
                    }
                }

                foreach ($packages as $k1 => $v1) {
                    if (bccomp($v1['weight'], '0.000', 3) == 0) {
                        if (bccomp($product['weight_inc'], '0.000', 3) == 0) {
                            $packages[$k1]['weight'] = bcmul($product['weight'], $v1['qty'], 3);
                        } else {
                            $packages[$k1]['weight'] = bcadd($product['weight'], bcmul($product['weight_inc'], strval($v1['qty'] - 1), 3), 3);
                        }
                    }
                    if (bccomp($v1['cubic'], '0.000', 3) == 0) {
                        $packages[$k1]['cubic'] = bcmul($v1['width'], bcmul($v1['depth'], $v1['height'], 3), 3);
                    }
                }

                /**
                 * Calculate overall shipping weight
                 */
                $ship_weight = '0.000';
                $ship_mass   = '0.000';
                $ship_volume = '0.000';
                $ship_cubic  = '0.000';
                foreach ($packages as $row) {
                    $ship_mass   = bcadd($ship_mass, $row['weight'], 3);
                    $ship_volume = bcadd($ship_volume, $row['cubic'], 3);
                }
                $ship_cubic = bcdiv($ship_volume, $cubic_divisor, 3) ?? '0.000';
                if($product['bypass_freight'] !='y'){
                    if (bccomp($ship_mass, $ship_cubic, 3) >= 0) {
                        $ship_weight = $ship_mass;
                    } else {
                        $ship_weight = $ship_cubic;
                    }
                }
                $productsobj=new TwigProduct($product['id']);
                if(isset($v['sub_product_id']) && $v['sub_product_id'] > 0){
                    $row = $DB->query_first(sprintf("SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  Where `%s` = %d AND `deleted_at` IS NULL LIMIT 1",
                            '`product_sub_module`',
                            'id',
                            $v['sub_product_id'],
                        ));
                    $product['sales_price'] = $productsobj->SubProductSalesPrice($row);
                    $product['product_name']=$product['product_name'].'-'.$row['name'];
                    $product['online']=$product['sales_price'];
                    $product['cost_price']=$row['price_usd'];
                }else{
                    $product['sales_price'] = ('y' == $product['price_online']) ? $product['effective_price'] : '0.0000';
                    $product['online']=convertCurrencyNEw(('y' == $product['price_online']) ? $product['primary_price'] : '0.0000', $product['primary_currency'], $invoice['invoice_currency'], false, $product['precision']);
                }
                $invoice_product = [
                    'cart_key'              => $cart_key,
                    'remove_url'            => 'removefromcart_' . $cart_key . '.html',
                    'availability'          => $product['available'],
                    'product_total_volume'  => $ship_volume,
                    'product_total_cubic'   => $ship_cubic,
                    'product_total_mass'    => $ship_mass,
                    'promotion_id'          => (array_key_exists('promotion_id', $product)) ? $product['promotion_id'] : 0,

                    'decimal'               => $product['precision'],
                    'generic'               => 'n',
                    'generic_code'          => '',
                    'is_gift'               => ('y' == ($v['is_gift'] ?? 'n')) ? 'y' : 'n',
                    'prod_count'            => $count,
                    'product_condition'     => $product['condition'],
                    'product_id'            => $product['id'],
                    'product_name'          => $product['product_name'],
                    'product_notes'         => '',
                    'product_online'        => $product['online'],
                    'product_plug'          => $v['adapter_id'],
                    'product_price_changed' => 'n',
                    'product_qty'           => $v['product_qty'],
                    'colour_id'             => $v['colour_id'],
                    'sub_product_id'        => $v['sub_product_id'],
                    'available'             => $product['available'],
                    'arrival_date'          => $product['arrival_date'],
                    'deposit_needed'        => $product['deposit_needed'],
                    'product_rank'          => $count++,
                    'supplier_notes'        => ("y" == $product["price_online"]) ?'' :'sales price :'.$product["effective_price"].'',
                    'product_section'       => $product['section'] ?? 0,
                    'product_status'        => 'quoted',
                    'product_total_weight'  => $ship_weight,
                    'product_updated'       => 'n',
                    'product_voltage'       => (array_key_exists('power_110', $product) && $product['power_110'] == $product['power_240'] && 0 != $product['power_110']) ? 7 : (
                        (110 == $v['power_id']) ? 1 : (
                            (240 == $v['power_id']) ? 5 : 0
                        )
                    ),
                    'product_warranty'      => $product['warranty'],
                    'product_weight'        => (array_key_exists('weight', $product)) ? $product['weight'] : (bcdiv($ship_weight, $v['product_qty'], 3) ?? '0.000'),
                    'product_weight_inc'    => (array_key_exists('weight_inc', $product)) ? $product['weight_inc'] : (bcdiv($ship_weight, $v['product_qty'], 3) ?? '0.000'),
                    'site_id'               => $_GLOBALS['site_settings']['site_id'],
                    'sub_id'                => $product['sub_id'] ?? 0,
                    'supplier_country'      => $product['country_location'],
                    'supplier_currency'     => 'USD',
                    'product_cost'          => convertCurrencyNEw($product['cost_price'], $product['cost_currency'],'USD', false, $product['decimal']),
                    'product_offer'         => $product['sales_price'],
                    'is_free'               => $v['is_free'],
                    'free_with_product'     => $v['free_with_product'],
                    'product' => $productsobj,
                ];
                if ($product['available'] < 0 && !in_array($product['arrival_date'], $null_match)) {
                    $invoice['needby_date'] = date('Y-m-d', strtotime($product['arrival_date']));
                }

                $invoice_product['availability'] = 1; //In Stock
                if ('consignment' == $product['stock_type']) {
                    $invoice_product['availability'] = 4; //Consignment
                    //$invoice['invoice_status']       = 'inquiry';
                    $issues['consignment']           = true;
                }

                $product_qty=$product['qty'];
                if($invoice_product['colour_id']){
                    $product_qty=inventoryStockGetValue($product['id'],$invoice_product['colour_id'],'qty');        
                }

                /*
                if (($product_qty) < $invoice_product['product_qty']) {
                    $invoice_product['availability'] = 0; //Backorder
                    $invoice['invoice_status']       = 'inquiry';
                    $invoice['products_locate_date'] = null;
                    $issues['backorder']             = true;
                }
                */
                if('quote' == ($_POST['type'] ?? $_GET['type'] ?? 'shopping')){
                    $invoice_product['availability'] = 0; //Backorder
                    $invoice['invoice_status']       = 'inquiry';
                    $invoice['products_locate_date'] = null;
                    $issues['backorder']             = true;
                }
                /*
                if (isset($force_backorder) && $force_backorder) {
                    $invoice_product['availability'] = 0;
                    $invoice['invoice_status']       = 'inquiry';
                    $invoice['products_locate_date'] = null;
                    $issues['backorder']             = true;
                }
                */
                if ('inquiry_for_everyone' == $product['order_rule']) {
                    $invoice['invoice_status']  = 'inquiry';
                    $invoice['shipping_method'] = 'offer';
                    $issues['block_invoice']    = true;
                    $issues['weight']           = true;
                }

                if ('y' == $v['used']) {
                    $floor_price = convertCurrencyNEw($product['floor_price'], $product['primary_currency'], $invoice['invoice_currency'], false, $product['precision']);
                    if (bccomp($floor_price, '0.0000', $product['precision']) > 0 && bccomp($floor_price, $invoice_product['product_offer'], $product['precision']) < 0) {
                        $invoice_product['product_condition'] = 2;
                        $invoice_product['product_warranty']  = $product['used_warranty'];
                    }
                }

                if (bccomp($ship_weight, '0.0000', 4) <= 0 && $product['bypass_freight'] !='y') {
                    $invoice['shipping_method'] = 'offer';
                    $issues['weight']           = true;
                }

                if ('n' == $invoice_product['is_gift'] && bccomp($invoice_product['product_offer'], '0.0000', $invoice_product['decimal']) <= 0) {
                    $invoice['invoice_status'] = 'inquiry';
                    $issues['price']           = true;
                }

                $notes = [];
                if (!empty($invoice['customer_requests']) && $iterationCount == 0) {
                    $notes[] = $invoice['customer_requests'];
                    $iterationCount++;
                }
                if ('y' == $v['used']) {
                    $notes[] = 'Used/Floor Stock requested.';
                }
                
                if ($product['available'] < 0) {
                    $notes[] = 'PRE ORDER :';
                    if($product['full_payment'] == 'y'){
                        $notes[] = sprintf(
                            ' - Full payment needed by %s.',
                            date('jS F, Y' , strtotime($product['pre_order_close_date']))
                        );
                    }else{
                        $notes[] = sprintf(
                            ' - $%s deposit needed, balance prior to shipping.',
                            $product['deposit_needed']
                        );
                    }
                    
                    if (!in_array($product['arrival_date'], $null_match)) {
                        $notes[] = sprintf(
                            ' - Estimated delivery date is %s.',
                            date('jS F, Y' , strtotime($product['arrival_date']))
                        );
                    }
                    $notes[] = '';
                }
                if($invoice_product['colour_id'] > 0) {
                    $colour_name = productGenericFromDatabase($invoice_product['colour_id'], 'product_colour')['colour'];
                    $colour_code = $invoice_product['product_id'].productGenericFromDatabase($invoice_product['colour_id'], 'product_colour')['code2'];
                    $notes[] = 'Colour: '.$colour_name.' ('.$colour_code.')';
                }
                if (array_key_exists($invoice_product['product_voltage'], $voltage_list) && '' != $voltage_list[$invoice_product['product_voltage']]) {
                    $notes[] = 'Power Supply Voltage: ' . $voltage_list[$invoice_product['product_voltage']];
                }

                if (array_key_exists($invoice_product['product_plug'], $plug_list) && '' != $plug_list[$invoice_product['product_plug']]) {
                    $notes[] = 'Power Adapter ID: ' . $plug_list[$invoice_product['product_plug']];
                }

                if (array_key_exists($invoice_product['product_warranty'], $warranty_list) && '' != $warranty_list[$invoice_product['product_warranty']]) {
                    $notes[] = 'Warranty: ' . $warranty_list[$invoice_product['product_warranty']];
                }

                if (array_key_exists($invoice_product['product_condition'], $condition_list) && '' != $condition_list[$invoice_product['product_condition']]) {
                    $notes[] = 'Condition: ' . $condition_list[$invoice_product['product_condition']];
                }

                if (array_key_exists($product['country_location'], $location_list) && '' != $location_list[$product['country_location']]) {
                    $notes[] = 'Location: ' . $location_list[$product['country_location']];
                }
                $notes[] = '';

                //Promotion/Special/Member Discount
                if (array_key_exists('promotion', $product) && 'y' == $product['promotion'] && in_array('voucher', explode(',', $product['promotion_type'])) && function_exists('productGiftVoucherFromDatabase') && $voucher = productGiftVoucherFromDatabase($product_str)) {
                    switch ($voucher['expiry_value']) {
                        case 0:
                            $notes[] = 'A ' . getCurrencySymbol($voucher['currency']) . convertCurrencyNEw($voucher['amount'], $voucher['currency']) . ' ' . $voucher['currency'] . ' gift voucher will be added to your account once this order is processed';
                            break;
                        case 1:
                            $notes[] = 'A ' . getCurrencySymbol($voucher['currency']) . convertCurrencyNEw($voucher['amount'], $voucher['currency']) . ' ' . $voucher['currency'] . ' gift voucher will be added to your account once this order is processed (Expires after 1 month)';
                            break;
                        default:
                            $notes[] = 'A ' . getCurrencySymbol($voucher['currency']) . convertCurrencyNEw($voucher['amount'], $voucher['currency']) . ' ' . $voucher['currency'] . ' gift voucher will be added to your account once this order is processed (Expires after ' . $voucher['expiry_value'] . ' months)';
                            break;
                    }
                }

                $last_note = '';

                /**
                 * Bulk Pricing
                 */
                //Method 2: By cheapest price
                if (function_exists('productBulkPricingFromDatabase')) {
                    foreach (productBulkPricingFromDatabase($invoice_product['product_id'], $invoice_product['sub_id']) as $special) {
                        if ($special['qty'] <= $invoice_product['product_qty']) {
                            $new_price = convertCurrencyNEw($special['unit_sale_price'], $product['primary_currency'], $invoice['invoice_currency'], false, $invoice_product['decimal']);
                            if (bccomp($new_price, $invoice_product['product_offer'], $invoice_product['decimal']) < 0) {
                                $invoice_product['product_offer'] = $new_price;
                                $savings                          = bcsub($invoice_product['product_online'], $invoice_product['product_offer'], $invoice_product['decimal']);
                                $last_note                        = sprintf(
                                    'Bulk Discount: %d%% (RRP: %s%s %s, save %s%s %s)',
                                    bcdiv(bcmul($savings, '100.0', 0), $invoice_product['product_online'], 0) ?? '0',
                                    getCurrencySymbol($invoice['invoice_currency']),
                                    $invoice_product['product_online'],
                                    $invoice['invoice_currency'],
                                    getCurrencySymbol($invoice['invoice_currency']),
                                    $savings,
                                    $invoice['invoice_currency']
                                );
                            }
                        }
                    }
                }

                /**
                 * Special Pricing
                 */
                if ('y' == $product['on_special'] && bccomp($invoice_product['product_online'], $invoice_product['product_offer'], 2) > 0) {
                    $savings   = bcsub($invoice_product['product_online'], $invoice_product['product_offer'], $invoice_product['decimal']);
                    $percent   = bcceil(bcdiv(bcmul($savings, '100.0', 4), $invoice_product['product_online'], 4) ?? '0.0000');
                    $last_note = sprintf(
                        'Sale Discount: %s%% (RRP: %s%s %s, save %s%s %s)',
                        $percent,
                        getCurrencySymbol($invoice['invoice_currency']),
                        $invoice_product['product_online'],
                        $invoice['invoice_currency'],
                        getCurrencySymbol($invoice['invoice_currency']),
                        $savings,
                        $invoice['invoice_currency']
                    );
                }elseif($productsobj->arcooda_discount() && bccomp($invoice_product['product_online'], $invoice_product['product_offer'], 2) > 0){
                     $savings   = bcsub($invoice_product['product_online'], $invoice_product['product_offer'], $invoice_product['decimal']);
                     $percent   = bcceil(bcdiv(bcmul($savings, '100.0', 4), $invoice_product['product_online'], 4) ?? '0.0000');
                    $last_note = sprintf(
                        'Member Discount: VIP  (RRP: %s%s %s, save %s%s %s)',
                        getCurrencySymbol($invoice['invoice_currency']),
                        $invoice_product['product_online'],
                        $invoice['invoice_currency'],
                        getCurrencySymbol($invoice['invoice_currency']),
                        $savings,
                        $invoice['invoice_currency']
                    );
                }elseif($productsobj->pre_offer_discount() && bccomp($invoice_product['product_online'], $invoice_product['product_offer'], 2) > 0){
                     $savings   = bcsub($invoice_product['product_online'], $invoice_product['product_offer'], $invoice_product['decimal']);
                     $percent   = bcceil(bcdiv(bcmul($savings, '100.0', 4), $invoice_product['product_online'], 4) ?? '0.0000');
                    $last_note = sprintf(
                        'Pre Order Discount:(RRP: %s%s %s, save %s%s %s)',
                        getCurrencySymbol($invoice['invoice_currency']),
                        $invoice_product['product_online'],
                        $invoice['invoice_currency'],
                        getCurrencySymbol($invoice['invoice_currency']),
                        $savings,
                        $invoice['invoice_currency']
                    );
                }
                /**
                 * Member Discount
                 */
                if (($_GLOBALS['member']['id'] ?? 0) && function_exists('memberDiscountsFromDatabase') && ($percent = (memberDiscountsFromDatabase($_GLOBALS['member']['id'], $_GLOBALS['site_settings']['site_id']))) != 0) {
                    $new_offer = convertCurrencyNEw(
                        bcmul(
                            $invoice_product['product_offer'],
                            bcmul('0.01', bcsub('100', $percent, 0), 4),
                            4
                        ),
                        $invoice['invoice_currency'],
                        '',
                        false,
                        $invoice_product['decimal']
                    );
                    if (bccomp($invoice_product['product_offer'], $new_offer, 2) > 0) {
                        $invoice_product['product_offer'] = $new_offer;
                        $savings                          = bcsub($invoice_product['product_online'], $new_offer, $invoice_product['decimal']);
                        $percent                          = bcceil(bcdiv(bcmul($savings, '100.0', 4), $invoice_product['product_online'], 4) ?? "0.0000");
                        $last_note                        = sprintf('Member Discount: %s%% (RRP: %s%s %s, save %s%s %s)', $percent, getCurrencySymbol($invoice['invoice_currency']), $invoice_product['product_online'], $invoice['invoice_currency'], getCurrencySymbol($invoice['invoice_currency']), $savings, $invoice['invoice_currency']);
                    }
                }

                /**
                 * Apply member pricing
                 */
                if (($_GLOBALS['member']['id'] ?? 0) && function_exists('memberProductSpecialPrices') && is_array($special_prices = memberProductSpecialPrices($_GLOBALS['member']['id'], $invoice_product['product_id'], $_GLOBALS['site_settings']['site_id']))) {
                    foreach ($special_prices as $special) {
                        $new_offer = convertCurrencyNEw($special['special_price'], $product['primary_currency'], $invoice['invoice_currency'], false, $invoice_product['decimal']);
                        if ($special['min_order'] <= $invoice_product['product_qty'] && bccomp($invoice_product['product_offer'], $new_offer, 2) > 0) {
                            $invoice_product['product_offer'] = $new_offer;
                            $savings                          = bcsub($invoice_product['product_online'], $new_offer, $invoice_product['decimal']);
                            $percent                          = bcceil(bcdiv(bcmul($savings, '100.0', 4), $invoice_product['product_online'], 4) ?? '0.0000');
                            $last_note                        = sprintf('Member Special Price: %s%0.2f %s (RRP: %s%s %s, save %s%%)', getCurrencySymbol($invoice['invoice_currency']), $new_offer, $invoice['invoice_currency'], getCurrencySymbol($invoice['invoice_currency']), $invoice_product['product_online'], $invoice['invoice_currency'], $percent);
                        }
                    }
                }

                // APPLY VIP member discount


                /**
                 * Apply discount pricing
                 */
                foreach ($invoice_discounts as $k1 => $invoice_discount) {
                    switch ($invoice_discount['coupon_type']) {
                        case 2: //Individual Product
                            $test = productStrSplit($invoice_discount['product_id'], 2);
                            if (0 != $test['product_id'] && $test['product_id'] != $invoice_product['product_id']) {
                                break;
                            }

                            if (0 != $test['sub_id'] && $test['sub_id'] != $invoice_product['sub_id']) {
                                break;
                            }

                            // no break

                        case 1: //Category
                            if (0 != $invoice_discount['section'] && $invoice_discount['section'] != $invoice_product['product_section']) {
                                break;
                            }

                            // no break

                        case 0: //All
                            if (0 != $invoice_discount['site_id'] && $invoice_discount['site_id'] != $_GLOBALS['site_settings']['site_id']) {
                                break;
                            }

                            $new_offer = $invoice_product['product_offer'];
                            switch ($invoice_discount['discount_type']) {
                                case 0: //% Discount
                                    $new_offer = convertCurrencyNEw(
                                        bcmul(
                                            $invoice_product['product_offer'],
                                            bcsub('1.00', $invoice_discount['value'], 2),
                                            4
                                        ),
                                        $invoice['invoice_currency'],
                                        '',
                                        false,
                                        4
                                    );

                                    break;
                                case 1: //Fixed Price
                                    $new_offer = convertCurrencyNEw(
                                        $invoice_discount['fixed_price'],
                                        $invoice_discount['currency'],
                                        $invoice['invoice_currency'],
                                        false,
                                        4
                                    );
                                    break;
                                default: //Undefined
                                    break;
                            }
                            if (bccomp($invoice_product['product_offer'], $new_offer, 4) > 0) {
                                $invoice_discounts[$k1]['price_deducted'] = bcadd(
                                    $invoice_discount['price_deducted'],
                                    bcmul(
                                        $invoice_product['product_qty'],
                                        bcsub($invoice_product['product_offer'], $new_offer, 4),
                                        3
                                    ),
                                    3
                                );
                            }
                            break;
                        default: //Undefined
                            break;
                    }
                }
                if(isset($_SESSION['bracket-discount'][$invoice_product['product_id']])){
                   if($product['backet_discunt_active'] == 'y' && $product['backet_discunt_enable'] == 'y' && $product['backet_discunt_value'] > 0)
                   $invoice_discounts[] = [
                        'price_deducted' => $product['backet_discunt_value'],
                        'name' => 'Monitor Bracket Discount',
                        'section' => 'product'
                   ];
                }    
                $notes[] = $last_note;
                $invoice_product['product_notes'] = trim(implode("\n", $notes));

                if ('y' == ($v['is_gift'] ?? 'n') && function_exists('productGiftsFromDatabase')) {
                    foreach (productGiftsFromDatabase($v['dependant_id'], $v['dependant_sub_id']) as $v2) {
                        if ($v2['linked_id'] == $v['product_id'] && $v2['linked_sub_id'] == $v['sub_id']) {
                            $invoice_product['product_offer'] = convertCurrencyNEw($v2['price'], $v2['currency'], $invoice['invoice_currency'], false, $v2['precision']);
                        }
                    }
                }

                $invoice['ship_mass']  = bcadd($invoice['ship_mass'], $invoice_product['product_total_mass'], 3);
                $invoice['ship_volume'] = bcadd($invoice['ship_volume'], $invoice_product['product_total_volume'], 3);
                $invoice['ship_cubic'] = bcadd($invoice['ship_cubic'], $invoice_product['product_total_cubic'], 3);
                if($product['bypass_freight'] !='y') {
                    if($productsobj->bonus() == 'y' && $productsobj->free_freight() == 'y') { 
                        if (bccomp($invoice['ship_mass'], $invoice['ship_cubic'], 3) > 0) {
                            $invoice['ship_weight_free'] =  bcadd($invoice['ship_weight_free'], $invoice_product['product_total_mass'], 3);
                        } else {
                            $invoice['ship_weight_free'] =  bcadd($invoice['ship_weight_free'], $invoice_product['product_total_cubic'], 3);
                        } 
                    }
                    if (bccomp($invoice['ship_mass'], $invoice['ship_cubic'], 3) > 0) {
                        $invoice['ship_weight'] = $invoice['ship_mass'];
                    } else {
                        $invoice['ship_weight'] = $invoice['ship_cubic'];
                    }                        
                }
                // to check free product and set value for only more then 1 product quantity

                $productQtyAftrFree = $invoice_product['product_qty'];
                if($v['is_free'] == 'y'){
                    $productQtyAftrFree = $productQtyAftrFree - 1;
                }
                foreach ($price_keys as $k2) {
                    $invoice_product['product_' . $k2 . '_total'] = bcmul($productQtyAftrFree, $invoice_product['product_' . $k2], $invoice_product['decimal']);
                    $invoice_product['product_' . $k2 . 'au'] = convertCurrencyNEw($invoice_product['product_' . $k2], $invoice['invoice_currency'], 'AUD');
                    $invoice_product['product_' . $k2 . 'us'] = convertCurrencyNEw($invoice_product['product_' . $k2], $invoice['invoice_currency'], 'USD');
                }
                foreach ($currency_keys as $k1) {
                    foreach ($price_keys as $k2) {
                        $invoice['products_' . $k2 . $k1] = bcadd($invoice['products_' . $k2 . $k1], bcmul($productQtyAftrFree, $invoice_product['product_' . $k2 . $k1], 4), 4);
                    }
                }
                if(($productsobj->bonus() == 'y' && $productsobj->free_gifts() == 'y') || $productsobj->is_promotion_gift_available()) { 
                    $giftsProduct = $productsobj->getGiftProducts();
                    if(count($giftsProduct) > 0){
                        foreach ($giftsProduct as $gift) {
                                $image_url = sprintf(
                                    '/p/%s-%d.jpg',
                                    urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $gift['name']), '-')),
                                    $gift['id']
                                );
                                $proobj = new TwigProduct($gift['id']);
                                $invoice_gifts[] = [
                                    'id'        => $gift['id'],
                                    'image_url' => $proobj->images($gift['color_id'])[0],
                                    'qty'       => $gift['qty']*$invoice_product['product_qty'],
                                    'name'      => $gift['name'],
                                    'color_id' => $gift['color_id'],
                                    'price'     => $gift['price'],
                                    'offer'     => $gift['offer'],
                                    'total'     => bcmul($gift['price'],$gift['qty'],2),
                                    'url'       => $gift['product']->url(),
                                    'warranty'  => $gift['product']->warranty(),
                                    'gift_detail' => 'Warranty: ' .$gift['product']->warranty(),
                                    'location'  => $gift['product']->location(),
                                    'product_id'=> $invoice_product['product_id']
                                ];
                        }
                    }
                }
                $invoice_products[] = $invoice_product;
                if (array_key_exists('promotion_type', $product) && strpos($product['promotion_type'], 'freight') !== false && array_key_exists('promotion_freight_service_id', $product) && $product['promotion_freight_service_id']) {
                    $invoice_products[] = [
                        'generic'       => 'y',
                        'generic_code'  => 'GFR',
                        'product_name'  => 'Free Shipping',
                        'product_notes' => 'For promotion: Free shipping on ' . $product['product_name'],
                        'product_offer' => 0,
                    ];
                }

                break;

            case 'y':
                //Does this ever happen? Maybe some kind of discount?
                break;

            case 'b':
                if (!function_exists('bulkFromDatabase')) {
                    break;
                }

                if (!function_exists('bulkProductsFromDatabase')) {
                    break;
                }

                $bulk = bulkFromDatabase($v['generic_code']);
                if ('n' == $bulk['active'] || 'y' != $bulk['status']) {
                    break;
                }

                //Note: Some bright spark made bulk_offer as GST inclusive
                if ('AUD' == $bulk['bulk_currency']) {
                    $bulk['bulk_offer'] = bcdiv($bulk['bulk_offer'], '1.1', 2);
                }

                $invoice_product = [
                    'cart_key'              => $cart_key,
                    'remove_url'            => 'removefromcart.php?cart_key=' . $cart_key,
                    'availability'          => 0,
                    'promotion_id'          => $bulk['promotion_id'],

                    'decimal'               => 2,
                    'generic'               => 'b',
                    'generic_code'          => $bulk['bulk_number'],
                    'prod_count'            => $count,
                    'product_condition'     => $bulk['bulk_condition'],
                    'product_cost'          => convertCurrencyNEw($bulk['bulk_cost'], $bulk['bulk_currency'], $invoice['invoice_currency']),
                    'product_id'            => 0,
                    'product_name'          => $bulk['title'],
                    'product_offer'         => convertCurrencyNEw($bulk['bulk_offer'], $bulk['bulk_currency'], $invoice['invoice_currency']),
                    'product_online'        => convertCurrencyNEw($bulk['bulk_online'], $bulk['bulk_currency'], $invoice['invoice_currency']),
                    'product_price_changed' => 'n',
                    'product_qty'           => $v['product_qty'],
                    'product_rank'          => $count++,
                    'product_section'       => $bulk['bulk_section'],
                    'product_terms'         => $bulk['bulk_terms'],
                    'product_total_weight'  => bcmul($v['product_qty'], $bulk['bulk_weight'], 3),
                    'product_updated'       => 'n',
                    'product_voltage'       => $bulk['bulk_voltage'],
                    'product_warranty'      => $bulk['bulk_warranty'],
                    'product_weight'        => $bulk['bulk_weight'],
                    'product_weight_inc'    => $bulk['bulk_weight'],
                    'site_id'               => $_GLOBALS['site_settings']['site_id'],
                    'supplier_cost'         => convertCurrencyNEw($bulk['bulk_cost'], $bulk['bulk_currency'], $invoice['invoice_currency']),
                    'supplier_country'      => $bulk['bulk_country'],
                    'supplier_currency'     => $bulk['bulk_currency'],
                    'supplier_id'           => $bulk['bulk_supplier'],
                    'supplier_name'         => $supplier_list[$bulk['bulk_supplier']],
                ];

                $notes = [];

                $temp = bulkProductsFromDatabase($v['generic_code']);
                if (count($temp)) {
                    $notes[] = 'Products Included in this Bulk offer :';
                    foreach ($temp as $temp) {
                        $notes[] = sprintf(
                            '#%s - %s - %dpc',
                            productFormatNumber($temp['product_id'], $temp['sub_id']),
                            $temp['product_name'],
                            $temp['product_qty']
                        );
                    }

                    $notes[] = '';
                }

                if ('y' == $v['used']) {
                    $notes[] = 'Used/Floor Stock requested.';
                }

                if ('' != $voltage_list[$invoice_product['product_voltage']]) {
                    $notes[] = 'Power Supply Voltage: ' . $voltage_list[$invoice_product['product_voltage']];
                }

                if ('' != $plug_list[$invoice_product['product_plug']]) {
                    $notes[] = 'Power Adapter ID: ' . $plug_list[$invoice_product['product_plug']];
                }

                if ('' != $warranty_list[$invoice_product['product_warranty']]) {
                    $notes[] = 'Warranty: ' . $warranty_list[$invoice_product['product_warranty']];
                }

                if ('' != $condition_list[$invoice_product['product_condition']]) {
                    $notes[] = 'Condition: ' . $condition_list[$invoice_product['product_condition']];
                }

                if ('' != $location_list[$bulk['bulk_country']]) {
                    $notes[] = 'Location: ' . $location_list[$bulk['bulk_country']];
                }

                $notes[] = '';

                $last_note = '';

                /**
                 * Special Pricing
                 */
                if (bccomp($invoice_product['product_online'], $invoice_product['product_offer'], 2) > 0) {
                    $savings   = bcsub($invoice_product['product_online'], $invoice_product['product_offer'], $invoice_product['decimal']);
                    $percent   = bcceil(bcdiv(bcmul($savings, '100.0', 4), $invoice_product['product_online'], 4) ?? "0.0000");
                    $last_note = sprintf('Sale Discount: %s%% (RRP: %s%s %s, save %s%s %s)', $percent, getCurrencySymbol($invoice['invoice_currency']), $invoice_product['product_online'], $invoice['invoice_currency'], getCurrencySymbol($invoice['invoice_currency']), $savings, $invoice['invoice_currency']);
                }

                $notes[] = $last_note;

                $invoice_product['product_notes'] = trim(implode("\n", $notes));

                foreach ($price_keys as $k2) {
                    $invoice_product['product_' . $k2 . 'au'] = convertCurrencyNEw($invoice_product['product_' . $k2], $invoice['invoice_currency'], 'AUD');
                    $invoice_product['product_' . $k2 . 'us'] = convertCurrencyNEw($invoice_product['product_' . $k2], $invoice['invoice_currency'], 'USD');
                }
                foreach ($currency_keys as $k1) {
                    foreach ($price_keys as $k2) {
                        $invoice['products_' . $k2 . $k1] = bcadd($invoice['products_' . $k2 . $k1], bcmul($invoice_product['product_qty'], $invoice_product['product_' . $k2 . $k1], 4), 4);
                    }
                }
                $invoice_products[] = $invoice_product;
                break;
        }
    }
}
foreach ($invoice_discounts as $k => $v) {
    $invoice_product = [
        'remove_url'      => 'discount.php?action=reset&return_url=%2Fcheckout%2F&code=' . urlencode($v['name']),
        'availability'    => 1,
        'decimal'         => 2,
        'generic'         => 'y',
        'generic_code'    => 'C39', // C39 is newly created code for discount code.
        'prod_count'      => $count,
        'product_name'    => 'Discount Coupon : ' . $v['name'],
        'product_online'  => convertCurrencyNEw(bcsub('0.00', $v['price_deducted'], 2), $invoice['invoice_currency'], '', false, 2),
        'product_qty'     => 1,
        'product_rank'    => $count++,
        'product_section' => $v['section'],
        'site_id'         => $_GLOBALS['site_settings']['site_id'],
        'product_cost'    => '0.0000',
        'product_offer'   => convertCurrencyNEw(bcsub('0.00', $v['price_deducted'], 2), $invoice['invoice_currency'], '', false, 2),
    ];
    $notes = [];
    if ('' != $v['description']) {
        $notes[] = $v['description'];
    }

    if (count($notes)) {
        $invoice_product['product_notes'] = trim(implode("\n", $notes));
    }

    foreach ($price_keys as $k2) {
        $invoice_product['product_' . $k2 . 'au'] = convertCurrencyNEw($invoice_product['product_' . $k2], $invoice['invoice_currency'], 'AUD');
        $invoice_product['product_' . $k2 . 'us'] = convertCurrencyNEw($invoice_product['product_' . $k2], $invoice['invoice_currency'], 'USD');
    }
    foreach ($currency_keys as $k1) {
        foreach ($price_keys as $k2) {
            $invoice['products_' . $k2 . $k1] = bcadd($invoice['products_' . $k2 . $k1], bcmul($invoice_product['product_qty'], $invoice_product['product_' . $k2 . $k1], 4), 4);
        }
    }

    $invoice_products[] = $invoice_product;
}
/**
 * Disable freight quote for arcade machine purchases
 */
/*
foreach ($invoice_products as $invoice_product) {
    if (2 /SECTION_ARCADE_MACHINES/ == $invoice_product['product_section']) {
        if (!array_key_exists('checkout', $_REQUEST) || !array_key_exists('ship_number', $_REQUEST['checkout']) || '' == $_REQUEST['checkout']['ship_number']) {
            $_REQUEST['checkout']['ship_number'] = 'offer';
        }
        $issues['weight'] = true;
        break;
    }
}
*/

/**
 * Find and set minimum precision
 */
$precision = 0;
foreach ($invoice_products as $v) {
    if ($v['decimal'] > $precision) {
        $temp = explode('.', $v['product_offer_total']);
        $temp = rtrim($temp[1] ?? '', '0');
        $precision = max($precision, strlen($temp));
    }
}
foreach ($invoice_products as $k => $v) {
    if ($precision !== $v['decimal']) {
        $invoice_products[$k]['product_offer_total'] = bcadd('0.0000', $v['product_offer_total'], $precision);
    }
}

/**
 * Set reserve product quentity
 */
$reserve_product = [];
foreach ($invoice_products as $k => $v) {
    $temp = [];
    $temp['product_id'] = $v['product_id'];
    $temp['colour_id']  = $v['colour_id'];
    $temp['qty']        = $v['product_qty'];
    $reserve_product[]  = $temp;
}

$count                  = 0;
$invoice_courier_quotes = [];
$invoice_courier_quotes_free = [];
$is_free = false;
if (count($invoice_products) == 1 && $invoice_products[0]['product']->bonus() == 'y' && $invoice_products[0]['product']->free_freight() == 'y'){
    $is_free = true;
}
if (bccomp($invoice['ship_weight'], '0.0000', 4) != 0 && !array_key_exists('weight', $issues)) {
    if (class_exists('Freight_Service_Handler')) {
        $FSH = Freight_Service_Handler::getInstance();
        $courier_quotes = $FSH->hintInternational($invoice['supply_country'], $invoice['ship_country'], '0', '0', '0', $invoice['ship_weight'], true);
        $courier_quotes = $FSH->calculateServices($courier_quotes, $invoice['products_offer'], $invoice['invoice_currency']);
        foreach ($courier_quotes as $k => $v) {
            $container_list = [true];
            if (function_exists('packingFromCache')) {
                $max_dim = [$v['max_width'], $v['max_depth'], $v['max_height']];
                usort($max_dim, function($a, $b) { return bccomp($a, $b, 1); });
                if (bccomp($max_dim[1], '0.000', 1) > 0) {
                    $packing                     = [];
                    $packing['packing_products'] = $invoice_products;
                    $packing['satchel']          = [
                        'width'  => $v['max_width'],
                        'depth'  => $v['max_depth'],
                        'height' => $v['max_height'],
                        'weight' => $v['max_weight'],
                        'name'   => $v['service'],
                    ];
                    $packing['packing_action'] = 'checkout_satchel';
                    $packing['spareparts']     = true;
                    if (isset($error_list)) {
                        $safety         = $error_list;
                        $container_list = packingFromCache($packing);
                        $error_list     = $safety;
                    } else {
                        $container_list = packingFromCache($packing);
                    }
                    if (count($container_list) == 1) {
                        $courier_quotes[$k]['packing_string'] = 'b64_gz_ser:' . base64_encode(gzcompress(serialize($container_list)));
                    }
                }
            }
        }
        // usort($courier_quotes, function($a, $b) {
        //     return strcmp($b['insurance'], $a['insurance']) ?: bccomp($a['offer_price'], $b['offer_price']) ?: bccomp($a['cost_price'], $b['cost_price']);
        // });

        usort($courier_quotes, function($a, $b) {
            return bccomp($a['offer_price'], $b['offer_price']) ?: bccomp($a['cost_price'], $b['cost_price']);
        });
        foreach ($courier_quotes as $v) {
            $invoice_courier_quotes[] = [
                'quote_count'    => count($invoice_courier_quotes),
                'flag'           => count($invoice_courier_quotes) ? 'n' : 'y',
                'name'           => $v['service'],
                'del_time'       => $v['delivery_time'],
                'track'          => $v['tracking'],
                'insurance'      => $v['insurance'],
                'tail_gate'      => $v['tailgate'],
                'cost_price'     => $v['cost_price'],
                'sell_price'     => $v['offer_price'],
                'retail_price'   => $v['online_price'],
                'cost_priceau'   => convertCurrencyNEw($v['cost_price'], $v['currency'], 'AUD', 4),
                'sell_priceau'   => convertCurrencyNEw($v['offer_price'], $v['currency'], 'AUD', 4),
                'cost_priceus'   => convertCurrencyNEw($v['cost_price'], $v['currency'], 'USD', 4),
                'sell_priceus'   => convertCurrencyNEw($v['offer_price'], $v['currency'], 'USD', 4),
                'supplier'       => $v['forwarder_id'],
                'supplier_name'  => $v['forwarder'],

                'ship_number'    => $v['ship_number'],
                'packing_string' => '',
                'registered'     => $v['registered'],
                'receipted'      => $v['receipted'],
                'express'        => $v['express'],
                'service_id'     => $v['service_id'],
                'freight_notes'  => $v['freight_notes'],
                'is_free' => $is_free
            ];
            $is_free =false;
        }
    } elseif (function_exists('freightSortArray') && function_exists('freightSearchCouriers')) {
        $found = [];
        foreach (freightSortArray(freightSearchCouriers($invoice['ship_country'], $invoice['ship_postcode'], $invoice['ship_state'], $invoice['ship_city'], $invoice['ship_weight'], $invoice['products_offer'], $invoice['invoice_currency'])) as $v) {
            $key = $v['ship_number'];

            $container_list = [true];
            if (function_exists('packingFromCache')) {
                list($width, $depth, $height) = [
                    $v['width'],
                    $v['depth'],
                    $v['height'],
                ];
                if ($width > 0 && $depth > 0) {
                    $packing                     = [];
                    $packing['packing_products'] = $invoice_products;
                    $packing['satchel']          = [
                        'width'  => $v['width'],
                        'depth'  => $v['depth'],
                        'height' => $v['height'],
                        'weight' => $v['weight'],
                        'name'   => $v['name'],
                    ];
                    $packing['packing_action'] = 'checkout_satchel';
                    $packing['spareparts']     = true;
                    if (isset($error_list)) {
                        $safety         = $error_list;
                        $container_list = packingFromCache($packing);
                        $error_list     = $safety;
                    } else {
                        $container_list = packingFromCache($packing);
                    }
                    if (count($container_list) == 1) {
                        if (false !== ($gzcompress = gzcompress(serialize($container_list)))) {
                            $v['packing_string'] = 'b64_gz_ser:' . base64_encode($gzcompress);
                        }
                    }
                }
            }

            if (!array_key_exists($key, $found) && count($container_list) == 1) {
                $invoice_courier_quotes[] = [
                    'ship_number'    => $v['ship_number'],
                    'service_id'     => $v['id'],
                    'express'        => $v['express'] ?? 'n',
                    'receipted'      => $v['receipted'] ?? 'n',
                    'registered'     => $v['registered'] ?? 'n',

                    'width'          => $v['width'],
                    'depth'          => $v['depth'],
                    'height'         => $v['height'],
                    'weight'         => $v['weight'],

                    'quote_count'    => $count,
                    'flag'           => (0 == $count) ? 'y' : 'n',
                    'name'           => $v['name'],
                    'del_time'       => $v['del_time'],
                    'track'          => $v['track'],
                    'insurance'      => $v['insurance'],
                    'tail_gate'      => $v['tail_gate'],
                    'cost_price'     => convertCurrencyNEw($v['cost_price'], $v['currency'], $invoice['invoice_currency']),
                    'sell_price'     => convertCurrencyNEw($v['sell_price'], $v['currency'], $invoice['invoice_currency']),
                    'retail_price'   => convertCurrencyNEw($v['retail_price'], $v['currency'], $invoice['invoice_currency']),
                    'cost_priceau'   => convertCurrencyNEw($v['cost_price'], $v['currency'], 'AUD'),
                    'sell_priceau'   => convertCurrencyNEw($v['sell_price'], $v['currency'], 'AUD'),
                    'cost_priceus'   => convertCurrencyNEw($v['cost_price'], $v['currency'], 'USD'),
                    'sell_priceus'   => convertCurrencyNEw($v['sell_price'], $v['currency'], 'USD'),
                    'supplier'       => $v['supplier'],
                    'supplier_name'  => $v['supplier_name'],

                    'packing_string' => (array_key_exists('packing_string', $v)) ? $v['packing_string'] : '',

                    'notes'          => $v['notes'],
                    'is_free' => $is_free
                ];
                $found[$key] = $key;
                $is_free =false;
                $count++;
            }
        }
    }
}

// Calculate free fright cost 
if (bccomp($invoice['ship_weight_free'], '0.0000', 4) != 0 && !array_key_exists('weight', $issues) && count($invoice_products) > 1) {
    if (class_exists('Freight_Service_Handler')) {
        $FSH = Freight_Service_Handler::getInstance();
        $courier_quotes = $FSH->hintInternational($invoice['supply_country'], $invoice['ship_country'], '0', '0', '0', $invoice['ship_weight_free'], true);
        $courier_quotes = $FSH->calculateServices($courier_quotes, $invoice['products_offer'], $invoice['invoice_currency']);

        usort($courier_quotes, function($a, $b) {
            return bccomp($a['offer_price'], $b['offer_price']) ?: bccomp($a['cost_price'], $b['cost_price']);
        });
        foreach ($courier_quotes as $v) {
            $invoice_courier_quotes_free[$v['ship_number']] = [
                'sell_price'     => $v['offer_price'],
                'retail_price'   => $v['online_price'],
        
                'sell_priceau'   => convertCurrencyNEw($v['offer_price'], $v['currency'], 'AUD', 4),
                'sell_priceus'   => convertCurrencyNEw($v['offer_price'], $v['currency'], 'USD', 4),
            ];
            break;
        }
    } elseif (function_exists('freightSortArray') && function_exists('freightSearchCouriers')) {
        $found = [];
        foreach (freightSortArray(freightSearchCouriers($invoice['ship_country'], $invoice['ship_postcode'], $invoice['ship_state'], $invoice['ship_city'], $invoice['ship_weight_free'], $invoice['products_offer'], $invoice['invoice_currency'])) as $v) {
            $key = $v['ship_number'];

            if (!array_key_exists($key, $found) && count($container_list) == 1) {
                $invoice_courier_quotes_free[$v['ship_number']] = [
                    'sell_price'     => convertCurrencyNEw($v['sell_price'], $v['currency'], $invoice['invoice_currency']),
                    'retail_price'   => convertCurrencyNEw($v['retail_price'], $v['currency'], $invoice['invoice_currency']),
                    
                    'sell_priceau'   => convertCurrencyNEw($v['sell_price'], $v['currency'], 'AUD'),
                    
                    'sell_priceus'   => convertCurrencyNEw($v['sell_price'], $v['currency'], 'USD'),
                ];
                $found[$key] = $key;
                break;
            }
        }
    }
}
if(isset($_GLOBALS['member']['member_id'])){
    $member_courier = memberCourierGetMain($_GLOBALS['member']['member_id']);
    $member_couriers = memberCouriersFromDatabase($_GLOBALS['member']['member_id']);
    if(is_array($member_courier) && count($member_courier)){
        foreach ($invoice_courier_quotes as $k2 => $v2) {
                $invoice_courier_quotes[$k2]['flag'] = 'n';
            }
        $invoice['ship_number']='own_0';
        $invoice['shipping_method']    = 'customer_forwarder';
        $invoice['ship_account_num']   = $member_courier['account_num'];
        $invoice['ship_id'] = $member_courier['courier_id'];
        $invoice['ship_supplier_name'] = $member_courier['courier_name'];
    }
   // print_r($member_couriers); die;
}
/**
 * Override with member preference
 */
if (array_key_exists('member', $_GLOBALS) && -1 == $_GLOBALS['member']['freight_preference']) {
    foreach ($invoice_courier_quotes as $k => $v) {
        if ('y' == $v['track']) {
            foreach ($invoice_courier_quotes as $k2 => $v2) {
                $invoice_courier_quotes[$k2]['flag'] = 'n';
            }
            $invoice_courier_quotes[$k]['flag'] = 'y';
            break;
        }
    }
}

/**
 * Override with checkout choices
 */
if (array_key_exists('checkout', $_REQUEST) && is_array($_REQUEST['checkout'])) {
    /**
     * Override shipping method
     */
    if (array_key_exists('ship_number', $_REQUEST['checkout']) && '' != $_REQUEST['checkout']['ship_number']) {
        foreach ($invoice_courier_quotes as $k => $v) {
            if ($_REQUEST['checkout']['ship_number'] == $v['ship_number']) {
                foreach ($invoice_courier_quotes as $k2 => $v2) {
                    $invoice_courier_quotes[$k2]['flag'] = 'n';
                }
                $invoice_courier_quotes[$k]['flag'] = 'y';
                break;
            }
        }
        if ('pickup' == $_REQUEST['checkout']['ship_number']) {
            foreach ($invoice_courier_quotes as $k2 => $v2) {
                $invoice_courier_quotes[$k2]['flag'] = 'n';
            }
            $invoice['ship_number']='';
            $invoice['shipping_method'] = 'pickup';
        } elseif ('offer' == $_REQUEST['checkout']['ship_number']) {
            foreach ($invoice_courier_quotes as $k2 => $v2) {
                $invoice_courier_quotes[$k2]['flag'] = 'n';
            }
            $invoice['shipping_method'] = 'offer';
        } elseif ('own_0' == $_REQUEST['checkout']['ship_number']) {
            foreach ($invoice_courier_quotes as $k2 => $v2) {
                $invoice_courier_quotes[$k2]['flag'] = 'n';
            }

            $invoice['shipping_method']    = 'customer_forwarder';
            $invoice['ship_product_notes'] = 'Customer Account #: ' . $_REQUEST['checkout']['own_account_num'];
            $invoice['ship_id']            = $_REQUEST['checkout']['own_courier_id'];
            $invoice['ship_supplier_id']   = $_REQUEST['checkout']['own_courier_id'];
            $invoice['ship_product_name']  = ((array_key_exists($_REQUEST['checkout']['own_courier_id'], $courier_list)) ? $courier_list[$_REQUEST['checkout']['own_courier_id']] : $_REQUEST['checkout']['own_courier_name']) . ' (Personal Account)';
            $invoice['ship_supplier_name'] = (array_key_exists($_REQUEST['checkout']['own_courier_id'], $courier_list)) ? $courier_list[$_REQUEST['checkout']['own_courier_id']] : $_REQUEST['checkout']['own_courier_name'];
            $invoice['ship_account_num']   = $_REQUEST['checkout']['own_account_num'];
        } elseif (preg_match('/^own_(\\d+)$/', $_REQUEST['checkout']['ship_number'], $matches)) {
            foreach ($_GLOBALS['member']['couriers'] as $v) {
                if ($matches[1] == $v['id']) {
                    foreach ($invoice_courier_quotes as $k2 => $v2) {
                        $invoice_courier_quotes[$k2]['flag'] = 'n';
                    }
                    $invoice['shipping_method']    = 'customer_forwarder';
                    $invoice['ship_product_notes'] = 'Customer Account #: ' . $v['account_num'];
                    $invoice['ship_id']            = $v['courier_id'];
                    $invoice['ship_supplier_id']   = $v['courier_id'];
                    $invoice['ship_product_name']  = $v['courier_name'] . ' (Personal Account)';
                    $invoice['ship_supplier_name'] = $v['courier_name'];
                    $invoice['ship_account_num']   = $v['account_num'];
                    break;
                }
            }
        }
        $_SESSION['shipping_method']=$invoice['shipping_method'];
        $_SESSION['ship_number']=$_REQUEST['checkout']['ship_number'];
    }
}
// used this session to make freight selectable when move to other pages. 
if(isset($_SESSION['shipping_method']) && isset($_SESSION['ship_number'])){
         foreach ($invoice_courier_quotes as $k => $v) {
            if ($_SESSION['ship_number'] == $v['ship_number']) {
                foreach ($invoice_courier_quotes as $k2 => $v2) {
                    $invoice_courier_quotes[$k2]['flag'] = 'n';
                }
                $invoice_courier_quotes[$k]['flag'] = 'y';
                break;
            }
        }
        $invoice['shipping_method'] = $_SESSION['shipping_method'];
        $invoice['ship_number'] = $_SESSION['ship_number'];
}
/**
 * Set the selected shipping method
 */
foreach ($invoice_courier_quotes as $k => $v) {
    if ('y' == $v['flag']) {
        $discount_price = 0.00;
        if(isset($invoice_courier_quotes_free[$v['ship_number']]) && $v['sell_price'] > $invoice_courier_quotes_free[$v['sell_price']]){
            $discount_price = $v['sell_price'] - $invoice_courier_quotes_free[$v['ship_number']]['sell_price'];
        }
        $invoice['ship_number']    = $v['ship_number'];
        $invoice['ship_discount']  = $discount_price;
        $invoice['ship_weight_free']  = $invoice['ship_weight_free'];
        $invoice['packing_string'] = $v['packing_string'];
        $invoice['ship_online']    = $v['is_free'] ? 0.00 : ($v['sell_price'] - $discount_price);
        $invoice['ship_onlineau']  = $v['is_free'] ? 0.00 : convertCurrencyNEw(($v['retail_price'] - $discount_price), $invoice['invoice_currency'], 'AUD');
        $invoice['ship_onlineus']  = $v['is_free'] ? 0.00 : convertCurrencyNEw(($v['retail_price'] - $discount_price), $invoice['invoice_currency'], 'USD');

        $invoice['shipping_method']    =  $v['shipping_method'] ?? 'other';
        $invoice['ship_insurance']     = $v['insurance'];
        $invoice['ship_tailgate']      = $v['tail_gate'];
        $invoice['ship_registered']    = $v['registered'];
        $invoice['ship_receipted']     = $v['receipted'];
        $invoice['ship_express']       = $v['express'];
        $invoice['ship_offer']         = $v['is_free'] ? 0.00 : ($v['sell_price'] - $discount_price);
        $invoice['ship_cost']          = $v['cost_price'];
        $invoice['ship_offerau']       =  $v['is_free'] ? 0.00 : ($v['sell_priceau'] - $discount_price);
        $invoice['ship_costau']        = $v['cost_priceau'];
        $invoice['ship_offerus']       =  $v['is_free'] ? 0.00 : ($v['sell_priceus'] - $discount_price);
        $invoice['ship_costus']        = $v['cost_priceus'];
        $invoice['ship_id']            = $v['service_id'];
        $invoice['ship_supplier_id']   = $v['supplier'];
        $invoice['ship_product_name']  = $v['name'];
        $invoice['ship_supplier_name'] = $v['supplier_name'];
    }
}
if(count($invoice_courier_quotes) == 0 && $invoice['ship_weight'] > 0 && !array_key_exists('weight', $issues)){
    $invoice_products[] = array(
         'generic'       => 'y',
         'generic_code'  => 'GFR',
         'product_name'  => 'Freight Costs - to be advised',
         'product_notes' => 'Freight will be quoted by our support team',
         'product_offer' => 0,
     );
}

/**
 * Override with checkout choices
 */
/*if('o' == $invoice['ship_insurance']){
    $invoice['terms_id'] = 4;
}*/
if (array_key_exists('checkout', $_REQUEST) && is_array($_REQUEST['checkout'])) {
    /**
     * Override shipping insurance
     */
    /*if (array_key_exists('ship_insurance', $_REQUEST['checkout']) && 'n' == $invoice['ship_insurance'] && 'y' == $_REQUEST['checkout']['ship_insurance'] && 'other' == $invoice['shipping_method']) {
        $invoice['terms_id'] = 4;
    }*/
    if (array_key_exists('ship_insurance', $_REQUEST['checkout']) && 'o' == $invoice['ship_insurance'] && 'y' == $_REQUEST['checkout']['ship_insurance']) {
         $invoice['terms_id'] = 4;
    }elseif(array_key_exists('ship_insurance', $_REQUEST['checkout']) && 'o' == $invoice['ship_insurance'] && 'n' == $_REQUEST['checkout']['ship_insurance']){
        $invoice['terms_id'] = 0;
    }else{

    }
}

$count           = 0;
$invoice_charges = [];

/**
 * Add any forwarding fees (Once per manufacturer)
 */
$forwarder_fees = [];
foreach ($invoice_products as $k => $v) {
    if ('n' == $v['generic'] && 'y' != $v['is_gift']) {
        $product_str = productStrJoin($v['product_id'], $v['sub_id']);
        if (array_key_exists($product_str, $product_cache)) {
            $product = $product_cache[$product_str];
        } else {
            $product = productFromDatabase($product_str);
            if (function_exists('productAlternatesFromDatabase')) {
                foreach (productAlternatesFromDatabase($product_str) as $v3) {
                    if ($v3['site_id'] == $_GLOBALS['site_settings']['site_id']) {
                        foreach ($v3 as $k2 => $v2) {
                            if ('id' != $k2 && '' != $v2) {
                                $product[$k2] = $v2;
                            }
                        }
                    }
                }
            }

            $product_cache[$product_str] = $product;
        }
        if ('y' == ($product['additional_fees'] ?? 'n') && (!isset($forwarder_fees[$product['manufacturer']]) || bccomp(convertCurrencyNEw($product['forwarder_fee'], $product['forwarder_fee_currency'], $invoice['invoice_currency']), $forwarder_fees[$product['manufacturer']]['forwarder_fee']) > 0)) {
            $forwarder_fees[$product['manufacturer']] = [
                'charge_id'     => 30,
                'charge_name'   => 'Special order fees',
                'charge_qty'    => 1,
                'charge_notes'  => 'Additional fee of ' . getCurrencySymbol($product['forwarder_fee_currency']) . convertCurrencyNEw($product['forwarder_fee'], $product['forwarder_fee_currency']) . ' ' . htmlspecialchars($product['forwarder_fee_currency']) . ' to transport the goods to our distribution warehouse',
                'charge_offer'  => convertCurrencyNEw($product['forwarder_fee'], $product['forwarder_fee_currency'], $invoice['invoice_currency']),
                'charge_cost'   => convertCurrencyNEw($product['forwarder_fee'], $product['forwarder_fee_currency'], $invoice['invoice_currency']),
                'charge_online' => convertCurrencyNEw($product['forwarder_fee'], $product['forwarder_fee_currency'], $invoice['invoice_currency']),
            ];
        }
    }
}

foreach ($forwarder_fees as $invoice_charge) {
    $invoice_charge['charge_count'] = $count;
    $invoice_charge['charge_rank']  = $count++;
    foreach ($price_keys as $k2) {
        $invoice_charge['charge_' . $k2 . 'au'] = convertCurrencyNEw($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'AUD');
        $invoice_charge['charge_' . $k2 . 'us'] = convertCurrencyNEw($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'USD');
    }
    foreach ($currency_keys as $k1) {
        foreach ($price_keys as $k2) {
            $invoice['charges_' . $k2 . $k1] = bcadd($invoice['charges_' . $k2 . $k1], bcmul($invoice_charge['charge_qty'], $invoice_charge['charge_' . $k2 . $k1], 4), 4);
        }
    }

    $invoice_charges[] = $invoice_charge;
    unset($invoice_charge);
}
unset($forwarder_fees);

/**
 * Add Packing And Handling Charge
 */
/*
$us_am = convertCurrencyNEw($invoice['products_offer'], $invoice['invoice_currency'], 'USD');
if (bccomp($_GLOBALS['site_settings']['handling_minimum_us'], '0.00', 2) > 0 && bccomp($invoice['products_offerus'], $_GLOBALS['site_settings']['handling_minimum_us'], 2) < 0) {
    $invoice_charge = [
        'charge_count'  => $count,
        'charge_rank'   => $count++,
        'charge_id'     => 5,
        'charge_name'   => 'Packing and Handling Fee',
        'charge_qty'    => 1,
        'charge_notes'  => sprintf(
            'For Orders less than $%s USD there will be a $5 USD packing & handling fee added to your order. (This does not include freight costs)',
            convertCurrencyNEw($_GLOBALS['site_settings']['handling_minimum_us'], 'USD')
        ),
        'charge_offer'  => convertCurrencyNEw('5.00', 'USD', $invoice['invoice_currency']),
        'charge_cost'   => convertCurrencyNEw('5.00', 'USD', $invoice['invoice_currency']),
        'charge_online' => convertCurrencyNEw('5.00', 'USD', $invoice['invoice_currency']),
    ];
    foreach ($price_keys as $k2) {
        $invoice_charge['charge_' . $k2 . 'au'] = convertCurrencyNEw($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'AUD');
        $invoice_charge['charge_' . $k2 . 'us'] = convertCurrencyNEw($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'USD');
    }
    foreach ($currency_keys as $k1) {
        foreach ($price_keys as $k2) {
            $invoice['charges_' . $k2 . $k1] = bcadd($invoice['charges_' . $k2 . $k1], bcmul($invoice_charge['charge_qty'], $invoice_charge['charge_' . $k2 . $k1], 4), 4);
        }
    }

    $invoice_charges[] = $invoice_charge;
    unset($invoice_charge);
}
*/

/**
 * Add Highway Insurance
 */
$invoice['insurance_offer'] = '';
if (function_exists('insuranceCalculateAmount')) {
    $au_am = convertCurrencyNEw($invoice['products_offer'], $invoice['invoice_currency'], 'AUD');
    if ('AU' == $invoice['supply_country'] && 'AU' == $invoice['bill_country'] && 'AU' == $invoice['ship_country']) {
        $au_am = convertCurrencyNEw(bcmul($au_am, '1.1', 2), 'AUD');
    }

    $invoice['insurance_offer'] = $insure_amount = convertCurrencyNEw(insuranceCalculateAmount(1, $au_am), 'AUD', $invoice['invoice_currency']);
    if (4/* && bccomp($insure_amount, '0.0', 2) > 0*/ == $invoice['terms_id']) {
        $invoice_charge = [
            'charge_count'  => $count,
            'charge_rank'   => $count++,
            'charge_id'     => 21,
            'charge_name'   => 'Shipping Insurance',
            'charge_qty'    => 1,
            'charge_notes'  => 'Covers damage in transit, loss of goods or non-delivery up to ' . getCurrencySymbol($invoice['invoice_currency']) . convertCurrencyNEw($invoice['products_offer'], $invoice['invoice_currency'], $invoice['invoice_currency']) . ' ' . $invoice['invoice_currency'],
            'charge_offer'  => $insure_amount,
            'charge_cost'   => $insure_amount,
            'charge_online' => $insure_amount,
        ];
        foreach ($price_keys as $k2) {
            $invoice_charge['charge_' . $k2 . 'au'] = convertCurrencyNEw($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'AUD');
            $invoice_charge['charge_' . $k2 . 'us'] = convertCurrencyNEw($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'USD');
        }
        foreach ($currency_keys as $k1) {
            foreach ($price_keys as $k2) {
                $invoice['charges_' . $k2 . $k1] = bcadd($invoice['charges_' . $k2 . $k1], bcmul($invoice_charge['charge_qty'], $invoice_charge['charge_' . $k2 . $k1], 4), 4);
            }
        }

        $invoice_charges[] = $invoice_charge;
        unset($invoice_charge);
    }
}
/**
 * Set the default handler
 */
if ($row = $DB->query_first(sprintf(
    "SELECT `staff_id` FROM `highwaygroup`.`site_default_handler` WHERE `site_id` = %d LIMIT 1",
    $invoice['site_id']
), MYSQLI_ASSOC)) {
    $invoice['handler_id'] = $row['staff_id'];
}

/**
 * Add GST
 */
if ('AU' == $invoice['supply_country'] && 'AU' == $invoice['bill_country'] && ('AU' == $invoice['ship_country'] || 'pickup' == $invoice['shipping_method'])) {
    $affected = bcadd($invoice['products_offer'], bcadd($invoice['ship_offer'], $invoice['charges_offer'], 2), 2);
    $percent  = '0.1';

    $invoice_charge = [
        'charge_count'  => $count,
        'charge_rank'   => $count++,
        'charge_id'     => 1,
        'charge_name'   => 'GST (10%)',
        'charge_qty'    => 1,
        'charge_notes'  => '',
        'charge_offer'  => convertCurrencyNEw(bcmul($percent, $affected, 2), $invoice['invoice_currency']),
        'charge_cost'   => convertCurrencyNEw(bcmul($percent, $affected, 2), $invoice['invoice_currency']),
        'charge_online' => convertCurrencyNEw(bcmul($percent, $affected, 2), $invoice['invoice_currency']),
    ];

    $rounded = bcmul(bcdiv($invoice_charge['charge_offer'], '0.05', 0), '0.05', 2);
    $diff    = bcsub($invoice_charge['charge_offer'], $rounded, 2);
    if (bccomp($diff, bcdiv('0.05', '2', 3), 3) < 0) {
        $invoice_charge['charge_offer'] = $rounded;
    } else {
        $invoice_charge['charge_offer'] = bcadd($rounded, '0.05', 2);
    }

    $invoice_charge['charge_cost'] = $invoice_charge['charge_online'] = $invoice_charge['charge_offer'];

    foreach ($price_keys as $k2) {
        $invoice_charge['charge_' . $k2 . 'au'] = convertCurrencyNEw($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'AUD');
        $invoice_charge['charge_' . $k2 . 'us'] = convertCurrencyNEw($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'USD');
    }
    foreach ($currency_keys as $k1) {
        foreach ($price_keys as $k2) {
            $invoice['charges_' . $k2 . $k1] = bcadd($invoice['charges_' . $k2 . $k1], bcmul($invoice_charge['charge_qty'], $invoice_charge['charge_' . $k2 . $k1], 4), 4);
        }
    }

    $invoice_charges[] = $invoice_charge;
    unset($invoice_charge);
}

/**
 * Add any gift vouchers
 */
if (function_exists('voucherSearch')) {
    $criteria              = $_GLOBALS['criteria_options']['voucher'] ?? [];
    $criteria['member_id'] = $_GLOBALS['member']['id'] ?? 0;
    $life_vouchers         = voucherSearch(array_merge($criteria, ['expiry_date' => $null_match]));
    $vouchers              = array_merge(
        voucherSort(array_diff(voucherSearch($criteria), $life_vouchers), 'expiry_date'),
        array_reverse(voucherSort($life_vouchers, 'amount'))
    );
    $total = $invoice['products_offer'];

    $now_time = formatDateTime();
    foreach ($vouchers as $voucher_id) {
        $voucher = voucherFromDatabase($voucher_id);
        if (in_array($voucher['expiry_date'], $null_match) || strcmp($voucher['expiry_date'], $now_time) > 0) {
            $voucher_amount = convertCurrencyNEw($voucher['amount'], $voucher['currency'], $invoice['invoice_currency']);

            $row = $DB->query_first(sprintf(
                "SELECT * FROM `highwaygroup`.`gift_terms` WHERE `id` = %d LIMIT 1",
                $voucher['terms']
            ), MYSQLI_ASSOC);
            if (bccomp($voucher_amount, $total, 2) < 0 && (
                !$row
                || ('min' == $row['type'] && bccomp(convertCurrencyNEw($row['amount'], $voucher['currency'], $invoice['invoice_currency']), $invoice['products_offer'], 2) <= 0)
                || ('max' == $row['type'] && bccomp(convertCurrencyNEw($row['amount'], $voucher['currency'], $invoice['invoice_currency']), $invoice['products_offer'], 2) >= 0)
            )) {
                $total          = bcsub($total, $voucher_amount, 2);
                $invoice_charge = [
                    'charge_count'  => $count,
                    'charge_rank'   => $count++,
                    'charge_id'     => 22,
                    'charge_name'   => 'Gift Voucher (' . voucherFormatNumber($voucher_id) . ')',
                    'charge_qty'    => 1,
                    'charge_notes'  => getCurrencySymbol($invoice['invoice_currency']) . $voucher_amount . ' ' . $invoice['invoice_currency'] . ' (' . getCurrencySymbol($voucher['currency']) . convertCurrencyNEw($voucher['amount'], $voucher['currency']) . ' ' . $voucher['currency'] . ') will be processed with this invoice.' . ((0 != $voucher['expiry_time']) ? ("\n\nExpiry Date: " . date(_('jS F, Y'), $voucher['expiry_time'])) : ''),
                    'charge_offer'  => '-' . $voucher_amount,
                    'charge_cost'   => '-' . $voucher_amount,
                    'charge_online' => '-' . $voucher_amount,

                    'voucher_id'    => $voucher_id,
                ];
                foreach ($price_keys as $k2) {
                    $invoice_charge['charge_' . $k2 . 'au'] = convertCurrencyNEw($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'AUD');
                    $invoice_charge['charge_' . $k2 . 'us'] = convertCurrencyNEw($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'USD');
                }
                foreach ($currency_keys as $k1) {
                    foreach ($price_keys as $k2) {
                        $invoice['charges_' . $k2 . $k1] = bcadd($invoice['charges_' . $k2 . $k1], bcmul($invoice_charge['charge_qty'], $invoice_charge['charge_' . $k2 . $k1], 4), 4);
                    }
                }

                $invoice_charges[] = $invoice_charge;
                unset($invoice_charge);
            }
        }
    }
}

/**
 * Disable invoice if freight offer selected
 */
if ('offer' == $invoice['shipping_method']) {
    //$invoice['invoice_status'] = 'inquiry';
}

/**
 * Disable credit card for arcade machine purchases
 */
foreach ($invoice_products as $invoice_product) {
    if (2 /* SECTION_ARCADE_MACHINES */ == $invoice_product['product_section']) {
        // if (in_array($invoice['paymenttype_id'], ['AMEX', 'VISA', 'MASTERCARD', 'CREDIT', 'CREDIT_AX', 'CREDIT_MC', 'CREDIT_VC'])) {
        //     $invoice['paymenttype_id'] = '';
        // }

        // if (is_array($payment_methods)) {
        //     foreach ($payment_methods as $k => $method) {
        //         if (in_array($method['payment_method_id'], ['AMEX', 'VISA', 'MASTERCARD', 'CREDIT', 'CREDIT_AX', 'CREDIT_MC', 'CREDIT_VC'])) {
        //             unset($payment_methods[$k]);
        //         }
        //     }
        // }
        
        //Code Updated by Swapnil
       /* $us_am = bcadd($invoice['products_offerus'], bcadd($invoice['ship_offerus'], $invoice['charges_offerus'], 2), 2);
        if (is_array($payment_methods)) {
            foreach ($payment_methods as $k => $method) {
                if ($method['payment_limit_acceptance'] != '0.00' && bccomp($us_am, $method['payment_limit_acceptance'], 2) > 0) {
                    if ($invoice['paymenttype_id'] == $method['payment_method_id']) {
                        $invoice['paymenttype_id'] = '';
                    }
                    unset($payment_methods[$k]);
                    continue;
                }
            }
        }*/
        //end

        break;
    }
}

/**
 * Disable bpay for non-Australian customers or website
 */
if (('AU' != $invoice['bill_country'] && 'AU' != $invoice['ship_country']) || 'AU' != $_GLOBALS['site_settings']['supply_country']) {
    if (in_array($invoice['paymenttype_id'], ['BPAY'])) {
        $invoice['paymenttype_id'] = '';
    }

    foreach ($payment_methods as $k => $method) {
        if ('BPAY' == $method['payment_method_id']) {
            unset($payment_methods[$k]);
        }
    }
}

/**
 * Check payment method price ranges
 */
$au_am = bcadd($invoice['products_offerau'], bcadd($invoice['ship_offerau'], $invoice['charges_offerau'], 2), 2);
$us_am = bcadd($invoice['products_offerus'], bcadd($invoice['ship_offerus'], $invoice['charges_offerus'], 2), 2);
foreach ($payment_methods as $k => $method) {
    if($method['payment_limit_acceptance']<=0){
        $key = strtolower($method['payment_method_id'] . '_minimum_au');
        if (array_key_exists($key, $_GLOBALS['site_settings']) && bccomp($au_am, $_GLOBALS['site_settings'][$key], 2) < 0) {
            if ($invoice['paymenttype_id'] == $method['payment_method_id']) {
                $invoice['paymenttype_id'] = '';
            }
            unset($payment_methods[$k]);
            continue;
        }

        $key = strtolower($method['payment_method_id'] . '_maximum_au');
        if (array_key_exists($key, $_GLOBALS['site_settings']) && bccomp($au_am, $_GLOBALS['site_settings'][$key], 2) > 0) {
            if ($invoice['paymenttype_id'] == $method['payment_method_id']) {
                $invoice['paymenttype_id'] = '';
            }
            unset($payment_methods[$k]);
            continue;
        }

        $key = strtolower($method['payment_method_id'] . '_minimum_usd');
        if (array_key_exists($key, $_GLOBALS['site_settings']) && bccomp($us_am, $_GLOBALS['site_settings'][$key], 2) < 0) {
            if ($invoice['paymenttype_id'] == $method['payment_method_id']) {
                $invoice['paymenttype_id'] = '';
            }
            unset($payment_methods[$k]);
            continue;
        }

        $key = strtolower($method['payment_method_id'] . '_maximum_usd');
        if (array_key_exists($key, $_GLOBALS['site_settings']) && bccomp($us_am, $_GLOBALS['site_settings'][$key], 2) > 0) {
            if ($invoice['paymenttype_id'] == $method['payment_method_id']) {
                $invoice['paymenttype_id'] = '';
            }
            unset($payment_methods[$k]);
            continue;
        }
    }else{
          $key = strtolower($method['payment_method_id'] . '_minimum_au');
            if (array_key_exists($key, $_GLOBALS['site_settings']) && bccomp($au_am, $_GLOBALS['site_settings'][$key], 2) < 0) {
                if ($invoice['paymenttype_id'] == $method['payment_method_id']) {
                    $invoice['paymenttype_id'] = '';
                }

                unset($payment_methods[$k]);
                continue;
            }
            $key = strtolower($method['payment_method_id'] . '_minimum_usd');
            if (array_key_exists($key, $_GLOBALS['site_settings']) && bccomp($us_am, $_GLOBALS['site_settings'][$key], 2) < 0) {
                if ($invoice['paymenttype_id'] == $method['payment_method_id']) {
                    $invoice['paymenttype_id'] = '';
                }

                unset($payment_methods[$k]);
                continue;
            }
            if (bccomp($us_am, $method['payment_limit_acceptance'], 2) > 0) {
                if ($invoice['paymenttype_id'] == $method['payment_method_id']) {
                    $invoice['paymenttype_id'] = '';
                }

                unset($payment_methods[$k]);
                continue;
            }
    }
}

/**
 * Check credits
 */

/**
 * Add Payment Processing Fee
 */
if ('' != $invoice['paymenttype_id']) {
    foreach ($payment_methods as $method) {
        if ($invoice['paymenttype_id'] == $method['payment_method_id']) {
            if (0.0 == $method['payment_charge_percent']) {
                break;
            }

            $affected = bcadd($invoice['products_offer'], bcadd($invoice['ship_offer'], $invoice['charges_offer'], 2), 2);
            $percent  = bcmul($method['payment_charge_percent'], '0.01', 4);

            $amount         = convertCurrencyNEw(bcmul($percent, $affected, 2), $invoice['invoice_currency']);
            $invoice_charge = [
                'charge_count'  => $count,
                'charge_rank'   => $count,
                'charge_id'     => 0,
                'charge_name'   => $method['payment_method_name'] . ' Processing Fee (' . $method['payment_charge_percent'] . '%)',
                'charge_qty'    => 1,
                'charge_notes'  => '',
                'charge_offer'  => $amount,
                'charge_cost'   => $amount,
                'charge_online' => $amount,
            ];

            $row = $DB->query_first(sprintf(
                "SELECT * FROM `highwaygroup`.`charges` WHERE `site_id` = %d AND `payment_method_link` = %s LIMIT 1",
                $_GLOBALS['site_settings']['site_id'],
                $DB->escape($invoice['paymenttype_id'])
            ), MYSQLI_ASSOC);
            if (!$row) {
                $row = $DB->query_first(sprintf(
                    "SELECT * FROM `highwaygroup`.`charges` WHERE `site_id` = 0 AND `payment_method_link` = %s LIMIT 1",
                    $DB->escape($invoice['paymenttype_id'])
                ), MYSQLI_ASSOC);
            }

            if ($row) {
                $amount = convertCurrencyNEw(bcadd(bcmul(bcmul($row['charge_percent'], '0.01', 4), $affected, 2), convertCurrencyNEw($row['charge_amount'], $row['charge_amount_currency'], $invoice['invoice_currency']), 2), $invoice['invoice_currency']);
                if (bccomp($row['charge_rounding'], '0.0000', 4) > 0) {
                    $rounded = bcmul(bcdiv($amount, $row['charge_rounding'], 0) ?? '0', $row['charge_rounding'], 2);
                    $diff    = bcsub($amount, $rounded, 2);
                    if (bccomp($diff, bcdiv($row['charge_rounding'], '2', 3), 3) < 0) {
                        $amount = $rounded;
                    } else {
                        $amount = bcadd($rounded, $row['charge_rounding'], 2);
                    }
                }
                $invoice_charge = [
                    'charge_count'  => $count,
                    'charge_rank'   => $count,
                    'charge_id'     => $row['charge_id'],
                    'charge_name'   => $row['charge_name'],
                    'charge_qty'    => 1,
                    'charge_notes'  => '',
                    'charge_offer'  => $amount,
                    'charge_cost'   => $amount,
                    'charge_online' => $amount,
                ];
            }

            $count++;

            foreach ($price_keys as $k2) {
                $invoice_charge['charge_' . $k2 . 'au'] = convertCurrencyNEw($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'AUD');
                $invoice_charge['charge_' . $k2 . 'us'] = convertCurrencyNEw($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'USD');
            }
            foreach ($currency_keys as $k1) {
                foreach ($price_keys as $k2) {
                    $invoice['charges_' . $k2 . $k1] = bcadd($invoice['charges_' . $k2 . $k1], bcmul($invoice_charge['charge_qty'], $invoice_charge['charge_' . $k2 . $k1], 4), 4);
                }
            }

            $invoice_charges[] = $invoice_charge;
            unset($invoice_charge);

            break;
        }
    }
}

/**
 * Add Total Rounding
 */
$excess = bcmod(bcmul('100', bcadd($invoice['products_offer'], bcadd($invoice['ship_offer'], $invoice['charges_offer'], 2), 2), 0), '5');
if ('AUD' == $invoice['invoice_currency'] && 'CASH' == $invoice['paymenttype_id'] && bccomp($excess, '0', 0) > 0) {
    $invoice_charge = [
        'charge_count'  => $count,
        'charge_rank'   => $count++,
        'charge_id'     => 3,
        'charge_name'   => 'Cash Rounding',
        'charge_qty'    => 1,
        'charge_notes'  => '',
        'charge_offer'  => convertCurrencyNEw(bcmul('0.01', bcsub((bccomp($excess, '3', 0) < 0) ? '0' : '5', $excess, 0), 2), $invoice['invoice_currency']),
        'charge_cost'   => convertCurrencyNEw('0.00', $invoice['invoice_currency']),
        'charge_online' => convertCurrencyNEw(bcmul('0.01', bcsub((bccomp($excess, '3', 0) < 0) ? '0' : '5', $excess, 0), 2), $invoice['invoice_currency']),
    ];
    foreach ($price_keys as $k2) {
        $invoice_charge['charge_' . $k2 . 'au'] = convertCurrencyNEw($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'AUD');
        $invoice_charge['charge_' . $k2 . 'us'] = convertCurrencyNEw($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'USD');
    }
    foreach ($currency_keys as $k1) {
        foreach ($price_keys as $k2) {
            $invoice['charges_' . $k2 . $k1] = bcadd($invoice['charges_' . $k2 . $k1], bcmul($invoice_charge['charge_qty'], $invoice_charge['charge_' . $k2 . $k1], 4), 4);
        }
    }

    $invoice_charges[] = $invoice_charge;
    unset($invoice_charge);
}

/**
 * Add up and round all totals
 */
foreach ($currency_keys as $k1) {
    foreach ($price_keys as $k2) {
        foreach ($section_keys as $k3) {
            $invoice[$k3 . '_' . $k2 . $k1] = bcadd($invoice[$k3 . '_' . $k2 . $k1], '0.00', 2);
        }

        $invoice['invoice_' . $k2 . $k1] = bcadd($invoice['products_' . $k2 . $k1], bcadd($invoice['ship_' . $k2 . $k1], $invoice['charges_' . $k2 . $k1], 2), 2);
    }
    $invoice['invoice_profit' . $k1] = bcsub($invoice['invoice_offer' . $k1], $invoice['invoice_cost' . $k1], 2);
}

/**
 * Set the extended invoice values
 */
$country_list                 = invoiceGetOptionList('bill_country');
$invoice['bill_address_full'] = trim(preg_replace('/\\n\\n+/', "\n\n", implode("\n", [
    '<b>'.(($invoice['user_account_type']=='company')?$invoice['bill_company']:$invoice['bill_name']).'</b>',
    
    $invoice['bill_address'],'',
    $invoice['bill_city'] . (('' != $invoice['bill_state']) ? (', ' . $invoice['bill_state']) : '') . ' ' . $invoice['bill_postcode'],
    $country_list[$invoice['bill_country']] ?? $invoice['bill_country'] ?? '',
    '',
    ('' != $invoice['bill_phone']) ? ('Ph: ' . $invoice['bill_phone']) : '',
    ('' != $invoice['bill_email']) ? ('Email: ' . $invoice['bill_email']) : '',
    (('' != $invoice['bill_company']) ? 'Attn: ' : '') . $invoice['bill_name'],
])) ?? '');

$country_list                 = invoiceGetOptionList('ship_country');
$invoice['ship_address_full'] = trim(preg_replace('/ *\\n( *\\n)+/', "\n\n", implode("\n", [
    $invoice['ship_company'],
    (('' != $invoice['ship_company']) ? 'Attn: ' : '') . $invoice['ship_name'],
    $invoice['ship_address'],'',
    $invoice['ship_city'] . (('' != $invoice['ship_state']) ? (', ' . $invoice['ship_state']) : '') . ' ' . $invoice['ship_postcode'],
    $country_list[$invoice['ship_country']] ?? $invoice['ship_country'] ?? '',
    '',
    ('' != $invoice['ship_phone']) ? ('Ph: ' . $invoice['ship_phone']) : '',
    ('' != $invoice['ship_email']) ? ('Email: ' . $invoice['ship_email']) : '',
])) ?? '');

$country_list                   = invoiceGetOptionList('notify_country');
$invoice['notify_address_full'] = trim(preg_replace('/ *\\n( *\\n)+/', "\n\n", implode("\n", [
    $invoice['notify_company'],
    (('' != $invoice['notify_company']) ? 'Attn: ' : '') . $invoice['notify_name'],
    $invoice['notify_address'],
    $invoice['notify_city'] . (('' != $invoice['notify_state']) ? (', ' . $invoice['notify_state']) : '') . ' ' . $invoice['notify_postcode'],
    $country_list[$invoice['notify_country']] ?? $invoice['notify_country'] ?? '',
    '',
    ('' != $invoice['notify_phone']) ? ('Ph: ' . $invoice['notify_phone']) : '',
    ('' != $invoice['notify_email']) ? ('Email: ' . $invoice['notify_email']) : '',
])) ?? '');

/**
 * Fill in the invoice defaults
 */
foreach ([
    'product_warranty'  => 'def_warranty',
    'product_voltage'   => 'def_voltage',
    'product_plug'      => 'def_plug',
    'product_condition' => 'def_condition',
    'product_packing'   => 'def_packing',
    'product_terms'     => 'def_terms',
    'supplier_currency' => 'def_currency',
    'supplier_country'  => 'def_country',
    'supplier_id'       => 'default_supplier',
] as $k => $v) {
    $temp = [];
    foreach ($invoice_products as $invoice_product) {
        if (array_key_exists($k, $invoice_product) && '' != $invoice_product[$k]) {
            $temp[$invoice_product[$k]] = $invoice_product[$k];
        }
    }

    unset($temp[0]);
    if (count(array_unique($temp)) == 1) {
        $invoice[$v] = reset($temp);
    }
}
$invoice['pack_requirement_id'] = $invoice['def_packing'];
//$invoice['waranty_terms_id']    = $invoice['def_warranty'];
if (0 != $invoice['def_voltage'] || 0 != $invoice['def_plug']) {
    $invoice['voltage_id'] = $invoice['def_voltage'];
    $invoice['plug_id']    = $invoice['def_plug'];
}

$invoice['open_time']     = strtotime($invoice['open_date']);
$invoice['proforma_time'] = strtotime($invoice['proforma_date']);
$invoice['tax_time']      = 0;
$invoice['tax_fyear']     = 0;
$invoice['tax_year']      = 0;
$invoice['tax_month']     = 0;

$invoice['products_located'] = (is_null($invoice['products_locate_date'])) ? 'n' : 'y';
$invoice['ship_located']     = (is_null($invoice['ship_locate_date'])) ? 'n' : 'y';
$invoice['payments_located'] = (is_null($invoice['payments_locate_date'])) ? 'n' : 'y';
$invoice['open_year']        = mktime(0, 0, 0, 1, 1, date('Y', $invoice['open_time']));
$invoice['open_quarter']     = mktime(0, 0, 0, floor((date('n', $invoice['open_time']) - 1) / 3) * 3 + 1, 1, date('Y', $invoice['open_time']));
$invoice['open_month']       = mktime(0, 0, 0, date('n', $invoice['open_time']), 1, date('Y', $invoice['open_time']));
$invoice['open_week']        = mktime(0, 0, 0, date('n', $invoice['open_time']), date('j', $invoice['open_time']) - date('N', $invoice['open_time']), date('Y', $invoice['open_time']));
$invoice['open_day']         = mktime(0, 0, 0, date('n', $invoice['open_time']), date('j', $invoice['open_time']), date('Y', $invoice['open_time']));

unset($product_cache);
usort($invoice_products, function($a, $b) {
    return $b['product_offer'] <=> $a['product_offer'];
});
if($invoice['paymenttype_id'] == 'EUR_BANK5'){
    $invoice['invoice_offereu']=  convertCurrencyNEw($invoice['invoice_offer'], $invoice['invoice_currency'],'EUR'); 
   // echo '<pre>'; print_r($invoice);  die;
}
/*
unset($condition_list);
unset($currency_keys);
unset($location_list);
unset($manufacturer_list);
unset($packages);
unset($price_keys);
unset($section_keys);
unset($supplier_list);
unset($timezone_by_postcode);
unset($warranty_list);
*/

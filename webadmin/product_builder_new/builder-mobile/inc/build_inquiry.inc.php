<?php
/**
 * Returns:
 *  $payment_methods
 *  $invoice
 *  $invoice_charges
 *  $invoice_courier_quotes
 *  $invoice_products
 *  $invoice_discounts
 *  $issues
 */

moduleLoad('invoice'); //invoiceSearch, invoiceGetOptionList
moduleLoad('product'); //productFromDatabase

if (!function_exists('bcceil')) {
    function bcceil($number)
    {
        $diff = bcsub($number, bcadd($number, '0.0', 0), 4);
        if (bccomp($diff, '0.0', 4) != 0) {
            return bcadd($number, bcsub('1.0', $diff, 4), 0);
        }

        return bcadd($number, '0.0', 0);
    }
}

if (!isset($DB)) {
    $DB = moduleLoad('DB');
}

//Just in case
bcscale(6);

$payment_methods = array();
$query           = $DB->query(sprintf(
    "SELECT *, `payment_method_id` AS `id` FROM `payment_methods` WHERE `site_id` = %d AND `payment_method_public` = 'y' ORDER BY `payment_method_name` ASC",
    $_GLOBALS['site_settings']['site_id']
));
while ($row = $DB->fetch_assoc($query)) {
    $payment_methods[$row['id']] = $row;
}
$DB->free_result($query);

$timezone_by_postcode = array(
    '0' => 8, //Darwin
    '2' => 1, //Sydney
    '3' => 2, //Melbourne
    '4' => 6, //Brisbane
    '5' => 7, //Adelaide
    '6' => 9, //Perth
    '7' => 2, //Hobart
    '9' => 6, //Brisbane
);
$currency_keys = array('', 'au', 'us');
$price_keys    = array('cost', 'offer', 'online');
$section_keys  = array('products', 'ship', 'charges');
$product_cache = array();

$plug_list    = range(0, 999);
$voltage_list = array(1 => '110-127V', 5 => '220-240V', 7 => '110-240V', 8 => '240V', 9 => '110/220V');

$availability_list = array();
$condition_list    = array();
$location_list     = array();
$manufacturer_list = array();
$supplier_list     = array();
$warranty_list     = array();
if (function_exists('productGetOptionList') || moduleLoad('product')) {
    $availability_list = productGetOptionList('availability');
    $condition_list    = productGetOptionList('condition');
    $location_list     = productGetOptionList('location');
    $manufacturer_list = productGetOptionList('manufacturer');
    $supplier_list     = productGetOptionList('supplier');
    $warranty_list     = productGetOptionList('warranty');
    unset($condition_list[1]);
}

$courier_list = array();
if (function_exists('getFreightServices') || moduleLoad('freight')) {
    $courier_list = getFreightServices('personal', '', true);
}

$packages = array();
$issues   = array();

$invoice = array(
    'ship_mass'              => '0.000',

    'access_date'            => formatDateTime(),
    'handler_id'             => 2,
    'he_id'                  => ('dom' == $_GLOBALS['site_settings']['dom_int']) ? 'dom' : 'int',
    'invoice_currency'       => $_GLOBALS['site_options']['DEFAULT_CURRENCY'],
    'newsletter_id'          => (array_key_exists('newsletter', $_GLOBALS)) ? $_GLOBALS['newsletter']['id'] : 0,
    'open_date'              => formatDateTime(),
    'site_id'                => $_GLOBALS['site_settings']['site_id'],
    'write_date'             => formatDateTime(),

    'bill_address'           => '',
    'bill_city'              => '',
    'bill_company'           => '',
    'bill_country'           => '',
    'bill_email'             => '',
    'bill_phone'             => '',
    'bill_postcode'          => '',
    'bill_state'             => '',

    'ship_address'           => '',
    'ship_city'              => '',
    'ship_company'           => '',
    'ship_country'           => '',
    'ship_email'             => '',
    'ship_phone'             => '',
    'ship_postcode'          => '',
    'ship_state'             => '',

    'notify_address'         => '',
    'notify_city'            => '',
    'notify_company'         => '',
    'notify_country'         => '',
    'notify_email'           => '',
    'notify_phone'           => '',
    'notify_postcode'        => '',
    'notify_state'           => '',

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
    'products_locate_date'   => formatDateTime(), //null,
    'products_staff_id'      => 0,
    'proforma_date'          => formatDateTime(),
    'discount_code'          => '',
    'proforma_purchaseorder' => '',
    'rand_num'               => mt_rand(0, 65535),
    'revision_id'            => 0,
    'ship_account_num'       => '',
    'ship_cubic'             => '0.000',
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
    /*
    [default_supplier] => 0
    [goods_packed_by] => 80
    [goods_shipped_by] => 80
    [harmonised_code] =>
    [origin_country] =>
    [packed_by] => y
    [packing_string] =>
    [payment_id] => 0
    [proforma_payterms] =>
    [proforma_shipvia] =>
    [proforma_terms] =>
    [proforma_tracknumber] =>
    [shipped_by] => y
    [stock_received] => n
    [supply_staff_id] => 0
    [wanted_id] =>
     */
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
    'invoice_online'         => '0.00',
    'invoice_onlineau'       => '0.00',
    'invoice_onlineus'       => '0.00',
    'invoice_profit'         => '0.00',
    'invoice_profitau'       => '0.00',
    'invoice_profitus'       => '0.00',
);

/**
 * Set visitor related data
 */
if (array_key_exists('visitor', $_GLOBALS) && is_array($_GLOBALS['visitor'])) {
    foreach (array(
        'visitor_id'       => 'id',
        'bill_address'     => 'address',
        'bill_city'        => 'city',
        'bill_company'     => 'company_name',
        'bill_country'     => 'country',
        'bill_email'       => 'email_address',
        'bill_phone'       => 'phone_no',
        'bill_postcode'    => 'post_code',
        'bill_state'       => 'state',
        'invoice_currency' => 'currency',
        'notify_address'   => 'address',
        'notify_city'      => 'city',
        'notify_company'   => 'company_name',
        'notify_country'   => 'country',
        'notify_email'     => 'email_address',
        'notify_phone'     => 'phone_no',
        'notify_postcode'  => 'post_code',
        'notify_state'     => 'state',
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
    ) as $k => $v) {
        if (array_key_exists($v, $_GLOBALS['visitor'])) {
            $invoice[$k] = $_GLOBALS['visitor'][$v];
        }
    }

    $invoice['bill_timezone'] = ('AU' == $_GLOBALS['visitor']['country'] && preg_match('/^[02-79][0-9]{3}$/', $_GLOBALS['visitor']['post_code'])) ? $timezone_by_postcode[substr($_GLOBALS['visitor']['post_code'], 0, 1)] : 0;
    @$invoice['bill_name']    = trim($_GLOBALS['visitor']['first_name'] . ' ' . $_GLOBALS['visitor']['last_name']);
    @$invoice['notify_name']  = trim($_GLOBALS['visitor']['first_name'] . ' ' . $_GLOBALS['visitor']['last_name']);
    @$invoice['ship_name']    = trim($_GLOBALS['visitor']['first_name'] . ' ' . $_GLOBALS['visitor']['last_name']);

    if ($row = $DB->query_first(sprintf(
        "SELECT `voltage`, `plugs` FROM `world`.`country` WHERE `code` = %s LIMIT 1",
        formatSQLString($invoice['ship_country'])
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
            if (preg_match('/#([0-9]+)$/', $v, $matches)) {
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
    foreach (array(
        'bill_address'     => 'address',
        'bill_city'        => 'city',
        'bill_company'     => 'company_name',
        'bill_country'     => 'country',
        'bill_email'       => 'email_address',
        'bill_phone'       => 'phone_no',
        'bill_postcode'    => 'post_code',
        'bill_state'       => 'state',
        'invoice_currency' => 'currency',
        'member_id'        => 'id',
        'notify_address'   => 'address',
        'notify_city'      => 'city',
        'notify_company'   => 'company_name',
        'notify_country'   => 'country',
        'notify_email'     => 'email_address',
        'notify_phone'     => 'phone_no',
        'notify_postcode'  => 'post_code',
        'notify_state'     => 'state',
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
    ) as $k => $v) {
        if (array_key_exists($v, $_GLOBALS['member'])) {
            $invoice[$k] = $_GLOBALS['member'][$v];
        }
    }

    $invoice['bill_timezone'] = ('AU' == $_GLOBALS['member']['country'] && preg_match('/^[02-79][0-9]{3}$/', $_GLOBALS['member']['post_code'])) ? $timezone_by_postcode[substr($_GLOBALS['member']['post_code'], 0, 1)] : 0;
    @$invoice['bill_name']    = trim($_GLOBALS['member']['first_name'] . ' ' . $_GLOBALS['member']['last_name']);
    @$invoice['notify_name']  = trim($_GLOBALS['member']['first_name'] . ' ' . $_GLOBALS['member']['last_name']);
    @$invoice['ship_name']    = trim($_GLOBALS['member']['first_name'] . ' ' . $_GLOBALS['member']['last_name']);

    /**
     * Set the default shipping address
     */
    if (array_key_exists('ship_addr', $_GLOBALS['member']) && is_array($_GLOBALS['member']['ship_addr'])) {
        foreach ($_GLOBALS['member']['ship_addr'] as $member_shippingaddress) {
            foreach (array(
                'ship_address'  => 'address',
                'ship_city'     => 'city',
                'ship_company'  => 'company_name',
                'ship_country'  => 'country',
                'ship_email'    => 'email',
                'ship_name'     => 'contact',
                'ship_needs'    => 'instructions',
                'ship_phone'    => 'phone_no',
                'ship_postcode' => 'post_code',
                'ship_state'    => 'state'
            ) as $k => $v) {
                $invoice[$k] = $member_shippingaddress[$v];
            }

            if ('y' == $member_shippingaddress['default']) {
                break;
            }

        }
    }

    if ($row = $DB->query_first(sprintf(
        "SELECT `voltage`, `plugs` FROM `world`.`country` WHERE `code` = %s LIMIT 1",
        formatSQLString($invoice['bill_country'])
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
            if (preg_match('/#([0-9]+)$/', $v, $matches)) {
                $invoice['plug_id'] = intval($matches[1]);
                break;
            }
        }

    }
    if ($row = $DB->query_first(sprintf(
        "SELECT `voltage`, `plugs` FROM `world`.`country` WHERE `code` = %s LIMIT 1",
        formatSQLString($invoice['ship_country'])
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
            if (preg_match('/#([0-9]+)$/', $v, $matches)) {
                $invoice['plug_id'] = intval($matches[1]);
                break;
            }
        }

    }
}

/**
 * Validate the discount codes
 */
$invoice_discounts = array();
if (trim($invoice['discount_code']) != '') {
    //Check if customer has recent processed orders
    $temp = 0;
    if (0 != $_GLOBALS['member']['id']) {
        $criteria                   = $_GLOBALS['criteria_options']['invoice'];
        $criteria['member_id']      = $_GLOBALS['member']['id'];
        $criteria['invoice_status'] = 'processed';
        $temp                       = (count(invoiceSearch($criteria, mktime(0, 0, 0, date('n', time()) - 12))));
    }

    //Matchup all the given discount codes
    $query = $DB->query(sprintf(
        "SELECT * FROM `gift_coupon` WHERE FIND_IN_SET(`name`, %s) AND (`expiry_date` = '0000-00-00 00:00:00' OR `expiry_date` > NOW()) AND `active` = 'y' AND (`site_id` = '' OR `site_id` = %d)",
        formatSQLString(trim($invoice['discount_code'])),
        $_GLOBALS['site_settings']['site_id']
    ));
    while ($row = $DB->fetch_assoc($query)) {
        if (0 != $temp && bccomp($row['bonus'], '0.00', 2) != 0) {
            $row['value'] = bcadd($row['value'], $row['bonus'], 2);
            $row['bonus'] = '0.00';
        }
        $row['price_deducted'] = '0.00';

        $invoice_discounts[$row['name']] = $row;
    }
    $DB->free_result($query);

    //Rebuild the discount code
    $invoice['discount_code'] = implode(',', array_keys($invoice_discounts));
}

/**
 * Try to match world timezones
 */
if ($row = $DB->query_first(sprintf(
    "SELECT `id` FROM `world`.`time_zones` WHERE `country` = %s ORDER BY IF(`name` = %s, 2, IF(`name` = %s, 1, 0)) DESC LIMIT 1",
    formatSQLString($invoice['bill_country']),
    formatSQLString($invoice['bill_state']),
    formatSQLString($invoice['bill_city'])
), MYSQLI_ASSOC)) {
    $invoice['bill_timezone'] = $row['id'];
}

/**
 * Override with builder choices
 */
if (true) { //No identifying qualities?

    /**
     * Override shipping address
     */
    $invoice['ship_address']  = '';
    $invoice['ship_city']     = '';
    $invoice['ship_company']  = trim($_POST['fname'] . ' ' . $_POST['lname']);
    $invoice['ship_country']  = $_POST['country'];
    $invoice['ship_email']    = $_POST['email'];
    $invoice['ship_name']     = trim($_POST['fname'] . ' ' . $_POST['lname']);
    $invoice['ship_phone']    = '';
    $invoice['ship_postcode'] = '';
    $invoice['ship_state']    = '';

    /**
     * Override currency and payment type
     */
    $invoice['invoice_currency'] = (array_key_exists('currency', $_POST)) ? $_POST['currency'] : 'USD';

    /**
     * Override voltage
     */
    $invoice['voltage_id'] = (in_array('110V', $_POST['items'])) ? 1 : 5;

    /**
     * Force as inquiry
     */
    $invoice['invoice_status'] = 'inquiry';
    $invoice['invoice_action'] = 'working';

    /*
    mail_list: unchecked
    send_copy: unchecked
    machine_name: 4 Player Vertical Fish Machine Cabinet (HG029)
    machine_id: 17602
    items[]: 4 Player Vertical Fish Machine Cabinet (HG029) ($1395)
    items[]: 110V
    items[]: Shipping Method: Sea Freight ($890)
    items[]: No Coin Mechanism ($0 x 4)
    items[]: No Bill Acceptor ($0 x 4)
    items[]: No Printer ($0 x 4)
    items[]: No Protection ($0 x 1)
    items[]: Standard Graphics ($0 x 1)
    items[]: Ocean King 3 - Monster Awaken Software (IGS Original) ($3099 x 1)
    price: 5384
    sub_id: 1
    */
}

/**
 * Set the default cubic divisor
 */
$cubic_divisor = '5000';
if ($invoice['supply_country'] != $invoice['ship_country']) {
    $cubic_divisor = '4000';
}

$invoice['invoice_exchange']   = convertCurrency('1.0000', $invoice['invoice_currency'], 'AUD', false, 4);
$invoice['invoice_exchangeus'] = convertCurrency('1.0000', $invoice['invoice_currency'], 'USD', false, 4);

$count            = 0;
$invoice_products = array();

if (true) {
    $product_str = productStrJoin(intval($_POST['machine_id']), intval($_POST['sub_id']));
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

    $product['normal_price']       = convertCurrency($product['primary_price'], $product['primary_currency'], $invoice['invoice_currency'], false, $product['precision']);
    $product['normal_currency']    = $invoice['invoice_currency'];
    $product['effective_price']    = intval($_POST['price']);
    $product['effective_currency'] = $invoice['invoice_currency'];
    $product['effective_savings']  = (bccomp($product['normal_price'], $product['effective_price'], $product['precision']) > 0) ? bcdiv(bcmul(bcsub($product['normal_price'], $product['effective_price'], $product['precision']), '100.00', 2), $product['normal_price'], 0) : '0';

    $invoice_product = array(
        'availability'          => 1, //In Stock
        'promotion_id'          => 0,

        'decimal'               => $product['precision'],
        'generic'               => 'n',
        'generic_code'          => '',
        'is_gift'               => 'n',
        'prod_count'            => $count,
        'product_condition'     => $product['condition'],
        'product_id'            => $product['id'],
        'product_name'          => $_POST['machine_name'],
        'product_notes'         => trim(implode("\n", $_POST['items']) . "\n" . $_POST['comments']),
        'product_online'        => convertCurrency(('y' == $product['price_online']) ? $product['primary_price'] : '0.0000', $product['primary_currency'], $invoice['invoice_currency'], false, $product['precision']),
        'product_plug'          => $invoice['plug_id'],
        'product_price_changed' => 'n',
        'product_qty'           => 1,
        'product_rank'          => $count++,
        'product_section'       => $product['section'],
        'product_status'        => 'quoted',
        'product_total_weight'  => '0.000',
        'product_updated'       => 'n',
        'product_voltage'       => (in_array('110V', $_POST['items'])) ? 1 : 5,
        'product_warranty'      => $product['warranty'],
        'product_weight'        => '0.000',
        'product_weight_inc'    => '0.000',
        'site_id'               => $_GLOBALS['site_settings']['site_id'],
        'sub_id'                => $product['sub_id'],
        'supplier_country'      => $product['location'],
        'supplier_currency'     => $product['cost_currency'],
        'product_cost'          => (array_key_exists('cost_price', $product)) ? $product['cost_price'] : convertCurrency($product['product_cost'], $product['cost_currency'], $invoice['invoice_currency'], false, $product['precision']),
        'product_offer'         => ('y' == $product['price_online']) ? $product['effective_price'] : '0.0000',
    );

    if ('c' == $product['stock']) {
        $invoice_product['availability'] = 4; //Consignment
        $issues['consignment']           = true;
    }
    if (($product['qty'] - $product['reserved']) < $invoice_product['product_qty']) {
        $invoice_product['availability'] = 0; //Backorder
        $invoice['products_locate_date'] = null;
    }

    foreach ($price_keys as $k2) {
        $invoice_product['product_' . $k2 . 'au'] = convertCurrency($invoice_product['product_' . $k2], $invoice['invoice_currency'], 'AUD');
        $invoice_product['product_' . $k2 . 'us'] = convertCurrency($invoice_product['product_' . $k2], $invoice['invoice_currency'], 'USD');
    }
    foreach ($currency_keys as $k1) {
        foreach ($price_keys as $k2) {
            $invoice['products_' . $k2 . $k1] = bcadd($invoice['products_' . $k2 . $k1], bcmul($invoice_product['product_qty'], $invoice_product['product_' . $k2 . $k1], 4), 4);
        }
    }

    $invoice_products[] = $invoice_product;

    //air/sea/pickup?
    $invoice['shipping_method'] = 'offer';
    $issues['weight']           = true;
    /*
    $invoice_product = array(
        'generic'       => 'y',
        'generic_code'  => 'GFR',
        'product_name'  => 'Free Shipping',
        'product_notes' => 'For promotion: Free shipping on ' . $product['product_name'],
        'product_offer' => 0,
    );
    foreach ($price_keys as $k2) {
        $invoice_product['product_' . $k2 . 'au'] = convertCurrency($invoice_product['product_' . $k2], $invoice['invoice_currency'], 'AUD');
        $invoice_product['product_' . $k2 . 'us'] = convertCurrency($invoice_product['product_' . $k2], $invoice['invoice_currency'], 'USD');
    }
    foreach ($currency_keys as $k1) {
        foreach ($price_keys as $k2) {
            $invoice['products_' . $k2 . $k1] = bcadd($invoice['products_' . $k2 . $k1], bcmul($invoice_product['product_qty'], $invoice_product['product_' . $k2 . $k1], 4), 4);
        }
    }
    $invoice_products[] = $invoice_product;
    */

}

foreach ($invoice_discounts as $k => $v) {
    $invoice_product = array(
        //'cart_key' => 'discount:' . $v['name'],
        'remove_url'      => 'discount.html?action=reset&return_url=%2Fcheckout%2F&code=' . urlencode($v['name']),
        'availability'    => 1,
        //'product_total_cubic' => $ship_cubic,
        //'product_total_mass' => $ship_mass,
        //'promotion_id' => $product['promotion_id'],
        'decimal'         => 2,
        'generic'         => 'y',
        'generic_code'    => 'C39', // C39 is newly created code for discount code.
        'prod_count'      => $count,
        //'product_condition' => $product['condition'],
        //'product_id' => $product['id'],
        'product_name'    => 'Discount Coupon : ' . $v['name'],
        //'product_notes' => '',
        'product_online'  => convertCurrency(bcsub('0.00', $v['price_deducted'], 2), $invoice['invoice_currency'], '', false, 2),
        //'product_packing' => 0,
        //'product_plug' => $v['adapter_id'],
        //'product_price_changed' => 'n',
        'product_qty'     => 1,
        'product_rank'    => $count++,
        'product_section' => $v['section'],
        //'product_status' => 'quoted',
        //'product_terms' => 0,
        //'product_total_weight' => $ship_weight,
        //'product_updated' => 'n',
        //'product_voltage' => ($product['power_110'] == $product['power_240'] && $product['power_110'] != 0) ? 7 : (($v['power_id'] == 110) ? 1 : (($v['power_id'] == 240) ? 5 : 0)),
        //'product_warranty' => $product['warranty'],
        //'product_weight' => $product['weight'],
        //'product_weight_inc' => $product['weight_inc'],
        'site_id'         => $_GLOBALS['site_settings']['site_id'],
        //'sub_id' => $product['sub_id'],
        //'supplier_country' => $product['location'],
        //'supplier_currency' => $product['cost_currency'],
        //'supplier_found' => 'y','n','s',
        //'supplier_id' => '',
        'product_cost'    => '0.0000',
        'product_offer'   => convertCurrency(bcsub('0.00', $v['price_deducted'], 2), $invoice['invoice_currency'], '', false, 2),
    );

    $notes = array();
    //if (bccomp($v['price_deducted'], '0.00', 2) > 0) $notes[] = 'Total discount applied ' . getCurrencySymbol($invoice['invoice_currency'], 'UTF8') . convertCurrency($v['price_deducted'], $invoice['invoice_currency'], '', false, 2);
    if ('' != $v['description']) {
        $notes[] = $v['description'];
    }

    if (count($notes)) {
        $invoice_product['product_notes'] = trim(implode("\n", $notes));
    }

    foreach ($price_keys as $k2) {
        $invoice_product['product_' . $k2 . 'au'] = convertCurrency($invoice_product['product_' . $k2], $invoice['invoice_currency'], 'AUD');
        $invoice_product['product_' . $k2 . 'us'] = convertCurrency($invoice_product['product_' . $k2], $invoice['invoice_currency'], 'USD');
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
foreach ($invoice_products as $invoice_product) {
    if (1 == $invoice_product['product_section']) {
        $issues['weight'] = true;
        break;
    }
}

$count                  = 0;
$invoice_courier_quotes = array();

if (moduleLoad('freight') && bccomp($invoice['ship_weight'], '0.0000', 4) != 0 && !array_key_exists('weight', $issues)) {
    $found = array();
    foreach (freightSortArray(freightSearchCouriers($invoice['ship_country'], $invoice['ship_postcode'], $invoice['ship_state'], $invoice['ship_city'], $invoice['ship_weight'], $invoice['products_offer'], $invoice['invoice_currency'])) as $v) {
        //$key = $v['supplier'] . '_' . $v['service_type'] . '_' . $v['track'] . '_' . $v['insurance'] . '_' . $v['stock'] . '_' . $v['type'] . '_' . $v['tail_gate'] . '_' . $v['product_type'];
        $key = $v['ship_number'];

        $container_list = array(true);
        if (moduleLoad('packing')) {
            list($width, $depth, $height) = array($v['width'], $v['depth'], $v['height']);
            if ($width > 0 && $depth > 0) {
                $packing                     = array();
                $packing['packing_products'] = $invoice_products;
                $packing['satchel']          = array('width' => $v['width'], 'depth' => $v['depth'], 'height' => $v['height'], 'weight' => $v['weight'], 'name' => $v['name']);
                $packing['packing_action']   = 'checkout_satchel';
                $packing['spareparts']       = true;
                if (isset($error_list)) {
                    $safety                      = $error_list;
                    $container_list              = packingFromCache($packing);
                    $error_list                  = $safety;
                } else {
                    $container_list              = packingFromCache($packing);
                }
                //$container_list=packingOptimisePacking($container_list);
                //if (count($container_list) > 1 || !count($container_list)) unset($invoice_courier_quotes[$k]);
                //else
                if (count($container_list) == 1) {
                    $v['packing_string'] = 'b64_gz_ser:' . base64_encode(gzcompress(serialize($container_list)));
                }

            }
        }

        if (!array_key_exists($key, $found) && count($container_list) == 1) {
            $invoice_courier_quotes[] = array(
                'ship_number'    => $v['ship_number'],
                'service_id'     => $v['id'],
                'express'        => $v['express'],
                'receipted'      => $v['receipted'],
                'registered'     => $v['registered'],

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
                'cost_price'     => convertCurrency($v['cost_price'], $v['currency'], $invoice['invoice_currency']),
                'sell_price'     => convertCurrency($v['sell_price'], $v['currency'], $invoice['invoice_currency']),
                'retail_price'   => convertCurrency($v['retail_price'], $v['currency'], $invoice['invoice_currency']),
                'cost_priceau'   => convertCurrency($v['cost_price'], $v['currency'], 'AUD'),
                'sell_priceau'   => convertCurrency($v['sell_price'], $v['currency'], 'AUD'),
                'cost_priceus'   => convertCurrency($v['cost_price'], $v['currency'], 'USD'),
                'sell_priceus'   => convertCurrency($v['sell_price'], $v['currency'], 'USD'),
                'supplier'       => $v['supplier'],
                'supplier_name'  => $v['supplier_name'],

                'packing_string' => (array_key_exists('packing_string', $v)) ? $v['packing_string'] : '',

                'notes'          => $v['notes'],
            );
            $found[$key] = $key;
            $count++;
        }
    }
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
 * Set the selected shipping method
 */
foreach ($invoice_courier_quotes as $k => $v) {
    if ('y' == $v['flag']) {
        $invoice['ship_number']    = $v['ship_number'];
        $invoice['packing_string'] = $v['packing_string'];
        $invoice['ship_online']    = $v['retail_price'];
        $invoice['ship_onlineau']  = convertCurrency($v['retail_price'], $invoice['invoice_currency'], 'AUD');
        $invoice['ship_onlineus']  = convertCurrency($v['retail_price'], $invoice['invoice_currency'], 'USD');

        $invoice['shipping_method']    = 'other';
        $invoice['ship_insurance']     = $v['insurance'];
        $invoice['ship_tailgate']      = $v['tail_gate'];
        $invoice['ship_registered']    = $v['registered'];
        $invoice['ship_receipted']     = $v['receipted'];
        $invoice['ship_express']       = $v['express'];
        $invoice['ship_offer']         = $v['sell_price'];
        $invoice['ship_cost']          = $v['cost_price'];
        $invoice['ship_offerau']       = $v['sell_priceau'];
        $invoice['ship_costau']        = $v['cost_priceau'];
        $invoice['ship_offerus']       = $v['sell_priceus'];
        $invoice['ship_costus']        = $v['cost_priceus'];
        $invoice['ship_id']            = $v['service_id'];
        $invoice['ship_supplier_id']   = $v['supplier'];
        $invoice['ship_product_name']  = $v['name'];
        $invoice['ship_supplier_name'] = $v['supplier_name'];
    }
}


$count           = 0;
$invoice_charges = array();

/**
 * Add any forwarding fees (Once per manufacturer)
 */
$forwarder_fees = array();
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
        if ('y' == $product['additional_fees'] && (!isset($forwarder_fees[$product['manufacturer']]) || bccomp(convertCurrency($product['forwarder_fee'], $product['forwarder_fee_currency'], $invoice['invoice_currency']), $forwarder_fees[$product['manufacturer']]['forwarder_fee']) > 0)) {
            $forwarder_fees[$product['manufacturer']] = array(
                'charge_id'     => 30,
                'charge_name'   => 'Forwarding Fee from ' . (('' != $manufacturer_list[$manufacturer_id]) ? $manufacturer_list[$manufacturer_id] : 'Manufacturer'),
                'charge_qty'    => 1,
                'charge_notes'  => 'Additional fee of ' . getCurrencySymbol($product['forwarder_fee_currency'], 'UTF8') . convertCurrency($product['forwarder_fee'], $product['forwarder_fee_currency']) . ' ' . htmlspecialchars($product['forwarder_fee_currency']) . ' to transport the goods to our distribution warehouse',
                'charge_offer'  => convertCurrency($product['forwarder_fee'], $product['forwarder_fee_currency'], $invoice['invoice_currency']),
                'charge_cost'   => convertCurrency($product['forwarder_fee'], $product['forwarder_fee_currency'], $invoice['invoice_currency']),
                'charge_online' => convertCurrency($product['forwarder_fee'], $product['forwarder_fee_currency'], $invoice['invoice_currency']),
            );
        }

    }
}

foreach ($forwarder_fees as $invoice_charge) {
    $invoice_charge['charge_count'] = $count;
    $invoice_charge['charge_rank']  = $count++;
    foreach ($price_keys as $k2) {
        $invoice_charge['charge_' . $k2 . 'au'] = convertCurrency($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'AUD');
        $invoice_charge['charge_' . $k2 . 'us'] = convertCurrency($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'USD');
    }
    foreach ($currency_keys as $k1) {
        foreach ($price_keys as $k2) {
            $invoice['charges_' . $k2 . $k1] = bcadd($invoice['charges_' . $k2 . $k1], bcmul($invoice_charge['charge_qty'], $invoice_charge['charge_' . $k2 . $k1], 4), 4);
        }
    }

    $invoice_charges[] = $invoice_charge;
}
unset($forwarder_fees);

/**
 * Add Packing And Handling Charge
 */
//$us_am = convertCurrency(bcadd($invoice['products_offer'], bcadd($invoice['ship_offer'], $invoice['charges_offer'], 2), 2), $invoice['invoice_currency'], 'USD');
$us_am = convertCurrency($invoice['products_offer'], $invoice['invoice_currency'], 'USD');
if (bccomp($_GLOBALS['site_settings']['handling_minimum_us'], '0.00', 2) > 0 && bccomp($invoice['products_offerus'], $_GLOBALS['site_settings']['handling_minimum_us'], 2) < 0) {
    $invoice_charge = array(
        'charge_count'  => $count,
        'charge_rank'   => $count++,
        'charge_id'     => 5,
        'charge_name'   => 'Packing and Handling Fee',
        'charge_qty'    => 1,
        'charge_notes'  => sprintf(
            'For Orders less than $%s USD there will be a $5 USD packing & handling fee added to your order. (This does not include freight costs)',
            convertCurrency($_GLOBALS['site_settings']['handling_minimum_us'], 'USD')
        ),
        'charge_offer'  => convertCurrency('5.00', 'USD', $invoice['invoice_currency']),
        'charge_cost'   => convertCurrency('5.00', 'USD', $invoice['invoice_currency']),
        'charge_online' => convertCurrency('5.00', 'USD', $invoice['invoice_currency']),
    );
    foreach ($price_keys as $k2) {
        $invoice_charge['charge_' . $k2 . 'au'] = convertCurrency($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'AUD');
        $invoice_charge['charge_' . $k2 . 'us'] = convertCurrency($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'USD');
    }
    foreach ($currency_keys as $k1) {
        foreach ($price_keys as $k2) {
            $invoice['charges_' . $k2 . $k1] = bcadd($invoice['charges_' . $k2 . $k1], bcmul($invoice_charge['charge_qty'], $invoice_charge['charge_' . $k2 . $k1], 4), 4);
        }
    }

    $invoice_charges[] = $invoice_charge;
}

/**
 * Add Highway Insurance
 */
moduleLoad('insurance');
$invoice['insurance_offer'] = '';
if (function_exists('insuranceCalculateAmount')) {
    $au_am = convertCurrency($invoice['products_offer'], $invoice['invoice_currency'], 'AUD');
    if ('AU' == $invoice['supply_country'] && 'AU' == $invoice['bill_country'] && 'AU' == $invoice['ship_country']) {
        $au_am = convertCurrency(bcmul($au_am, '1.1', 2), 'AUD');
    }

    $invoice['insurance_offer'] = $insure_amount = convertCurrency(insuranceCalculateAmount(1, $au_am), 'AUD', $invoice['invoice_currency']);
    if (4/* && bccomp($insure_amount, '0.0', 2) > 0*/ == $invoice['terms_id']) {
        $invoice_charge = array(
            'charge_count'  => $count,
            'charge_rank'   => $count++,
            'charge_id'     => 21,
            'charge_name'   => 'Shipping Insurance',
            'charge_qty'    => 1,
            'charge_notes'  => 'Covers damage in transit, loss of goods or non-delivery up to ' . getCurrencySymbol($invoice['invoice_currency'], 'UTF-8') . convertCurrency($invoice['products_offer'], $invoice['invoice_currency'], $invoice['invoice_currency']) . ' ' . $invoice['invoice_currency'],
            'charge_offer'  => $insure_amount,
            'charge_cost'   => $insure_amount,
            'charge_online' => $insure_amount,
        );
        foreach ($price_keys as $k2) {
            $invoice_charge['charge_' . $k2 . 'au'] = convertCurrency($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'AUD');
            $invoice_charge['charge_' . $k2 . 'us'] = convertCurrency($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'USD');
        }
        foreach ($currency_keys as $k1) {
            foreach ($price_keys as $k2) {
                $invoice['charges_' . $k2 . $k1] = bcadd($invoice['charges_' . $k2 . $k1], bcmul($invoice_charge['charge_qty'], $invoice_charge['charge_' . $k2 . $k1], 4), 4);
            }
        }

        $invoice_charges[] = $invoice_charge;
    }
}

/**
 * Set the default handler
 */
if ($row = $DB->query_first(sprintf(
    "SELECT `staff_id` FROM `site_default_handler` WHERE `site_id` = %d LIMIT 1",
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

    $invoice_charge = array(
        'charge_count'  => $count,
        'charge_rank'   => $count++,
        'charge_id'     => 1,
        'charge_name'   => 'GST (10%)',
        'charge_qty'    => 1,
        'charge_notes'  => '',
        'charge_offer'  => convertCurrency(bcmul($percent, $affected, 2), $invoice['invoice_currency']),
        'charge_cost'   => convertCurrency(bcmul($percent, $affected, 2), $invoice['invoice_currency']),
        'charge_online' => convertCurrency(bcmul($percent, $affected, 2), $invoice['invoice_currency']),
    );

    $rounded = bcmul(bcdiv($invoice_charge['charge_offer'], '0.05', 0), '0.05', 2);
    $diff    = bcsub($invoice_charge['charge_offer'], $rounded, 2);
    if (bccomp($diff, bcdiv('0.05', '2', 3), 3) < 0) {
        $invoice_charge['charge_offer'] = $rounded;
    } else {
        $invoice_charge['charge_offer'] = bcadd($rounded, '0.05', 2);
    }

    $invoice_charge['charge_cost'] = $invoice_charge['charge_online'] = $invoice_charge['charge_offer'];

    foreach ($price_keys as $k2) {
        $invoice_charge['charge_' . $k2 . 'au'] = convertCurrency($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'AUD');
        $invoice_charge['charge_' . $k2 . 'us'] = convertCurrency($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'USD');
    }
    foreach ($currency_keys as $k1) {
        foreach ($price_keys as $k2) {
            $invoice['charges_' . $k2 . $k1] = bcadd($invoice['charges_' . $k2 . $k1], bcmul($invoice_charge['charge_qty'], $invoice_charge['charge_' . $k2 . $k1], 4), 4);
        }
    }

    $invoice_charges[] = $invoice_charge;
}

/**
 * Add any gift vouchers
 */
if (function_exists('voucherSearch') || moduleLoad('voucher')) {
    $criteria              = $_GLOBALS['criteria_options']['voucher'];
    $criteria['member_id'] = $_GLOBALS['member']['id'];
    $life_vouchers         = voucherSearch(array_merge($criteria, array('expiry_date' => '0000-00-00 00:00:00')));
    $vouchers              = array_merge(
        voucherSort(array_diff(voucherSearch($criteria), $life_vouchers), 'expiry_date'),
        array_reverse(voucherSort($life_vouchers, 'amount'))
    );
    $total = $invoice['products_offer'];

    $now_time = formatDateTime();
    foreach ($vouchers as $voucher_id) {
        $voucher = voucherFromDatabase($voucher_id);
        if ('0000-00-00 00:00:00' == $voucher['expiry_date'] || strcmp($voucher['expiry_date'], $now_time) > 0) {
            $voucher_amount = convertCurrency($voucher['amount'], $voucher['currency'], $invoice['invoice_currency']);

            $row = $DB->query_first(sprintf(
                "SELECT * FROM `gift_terms` WHERE `id` = %d LIMIT 1",
                $voucher['terms']
            ), MYSQLI_ASSOC);
            if (bccomp($voucher_amount, $total, 2) < 0 && (
                !$row
                || ('min' == $row['type'] && bccomp(convertCurrency($row['amount'], $voucher['currency'], $invoice['invoice_currency']), $invoice['products_offer'], 2) <= 0)
                || ('max' == $row['type'] && bccomp(convertCurrency($row['amount'], $voucher['currency'], $invoice['invoice_currency']), $invoice['products_offer'], 2) >= 0)
            )) {
                $total          = bcsub($total, $voucher_amount, 2);
                $invoice_charge = array(
                    'charge_count'  => $count,
                    'charge_rank'   => $count++,
                    'charge_id'     => 22,
                    'charge_name'   => 'Gift Voucher (' . voucherFormatNumber($voucher_id) . ')',
                    'charge_qty'    => 1,
                    'charge_notes'  => getCurrencySymbol($invoice['invoice_currency'], 'UTF8') . $voucher_amount . ' ' . $invoice['invoice_currency'] . ' (' . getCurrencySymbol($voucher['currency'], 'UTF8') . convertCurrency($voucher['amount'], $voucher['currency']) . ' ' . $voucher['currency'] . ') will be processed with this invoice.' . ((0 != $voucher['expiry_time']) ? ("\n\nExpiry Date: " . date('jS F, Y', $voucher['expiry_time'])) : ''),
                    'charge_offer'  => '-' . $voucher_amount,
                    'charge_cost'   => '-' . $voucher_amount,
                    'charge_online' => '-' . $voucher_amount,

                    'voucher_id'    => $voucher_id,
                );
                foreach ($price_keys as $k2) {
                    $invoice_charge['charge_' . $k2 . 'au'] = convertCurrency($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'AUD');
                    $invoice_charge['charge_' . $k2 . 'us'] = convertCurrency($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'USD');
                }
                foreach ($currency_keys as $k1) {
                    foreach ($price_keys as $k2) {
                        $invoice['charges_' . $k2 . $k1] = bcadd($invoice['charges_' . $k2 . $k1], bcmul($invoice_charge['charge_qty'], $invoice_charge['charge_' . $k2 . $k1], 4), 4);
                    }
                }

                $invoice_charges[] = $invoice_charge;
            }
        }
    }
}

/**
 * Disable invoice if freight offer selected
 */
if ('offer' == $invoice['shipping_method']) {
    $invoice['invoice_status'] = 'inquiry';
}

/**
 * Disable credit card for arcade machine purchases
 */
foreach ($invoice_products as $invoice_product) {
    if (1 == $invoice_product['product_section']) {
        if (in_array($invoice['paymenttype_id'], array('AMEX', 'VISA', 'MASTERCARD'))) {
            $invoice['paymenttype_id'] = '';
        }

        if (is_array($payment_methods)) {
            foreach ($payment_methods as $k => $method) {
                if (in_array($method['payment_method_id'], array('AMEX', 'VISA', 'MASTERCARD'))) {
                    unset($payment_methods[$k]);
                }
            }
        }

        break;
    }
}

/**
 * Disable bpay for non-Australian customers or website
 */
if (('AU' != $invoice['bill_country'] && 'AU' != $invoice['ship_country']) || 'AU' != $_GLOBALS['site_settings']['supply_country']) {
    if (in_array($invoice['paymenttype_id'], array('BPAY'))) {
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
if (is_array($payment_methods)) {
    foreach ($payment_methods as $k => $method) {
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
    }
} else {
    $invoice['paymenttype_id'] = '';
}

/**
 * Check credit
 */

/**
 * Add Payment Processing Fee
 */
if (is_array($payment_methods) && '' != $invoice['paymenttype_id']) {
    foreach ($payment_methods as $method) {
        if ($invoice['paymenttype_id'] == $method['payment_method_id']) {
            if (0.0 == $method['payment_charge_percent']) {
                break;
            }

            $affected = bcadd($invoice['products_offer'], bcadd($invoice['ship_offer'], $invoice['charges_offer'], 2), 2);
            $percent  = bcmul($method['payment_charge_percent'], '0.01', 4);

            $amount         = convertCurrency(bcmul($percent, $affected, 2), $invoice['invoice_currency']);
            $invoice_charge = array(
                'charge_count'  => $count,
                'charge_rank'   => $count,
                'charge_id'     => 0,
                'charge_name'   => $method['payment_method_name'] . ' Processing Fee (' . $method['payment_charge_percent'] . '%)',
                'charge_qty'    => 1,
                'charge_notes'  => '',
                'charge_offer'  => $amount,
                'charge_cost'   => $amount,
                'charge_online' => $amount,
            );

            $row = $DB->query_first(sprintf(
                "SELECT * FROM `highwaygroup`.`charges` WHERE `site_id` = %d AND `payment_method_link` = %s LIMIT 1",
                $_GLOBALS['site_settings']['site_id'],
                formatSQLString($invoice['paymenttype_id'])
            ), MYSQLI_ASSOC);
            if (!$row) {
                $row = $DB->query_first(sprintf(
                    "SELECT * FROM `highwaygroup`.`charges` WHERE `site_id` = 0 AND `payment_method_link` = %s LIMIT 1",
                    formatSQLString($invoice['paymenttype_id'])
                ), MYSQLI_ASSOC);
            }

            if ($row) {
                $amount = convertCurrency(bcadd(bcmul(bcmul($row['charge_percent'], '0.01', 4), $affected, 2), convertCurrency($row['charge_amount'], $row['charge_amount_currency'], $invoice['invoice_currency']), 2), $invoice['invoice_currency']);
                if (bccomp($row['charge_rounding'], '0.0000', 4) > 0) {
                    $rounded = bcmul(bcdiv($amount, $row['charge_rounding'], 0), $row['charge_rounding'], 2);
                    $diff    = bcsub($amount, $rounded, 2);
                    if (bccomp($diff, bcdiv($row['charge_rounding'], '2', 3), 3) < 0) {
                        $amount = $rounded;
                    } else {
                        $amount = bcadd($rounded, $row['charge_rounding'], 2);
                    }

                    //$raw_sum = bcadd($invoice['products_offer'], bcadd($invoice['ship_offer'], bcadd($invoice['charges_offer'], $amount, 2), 2), 2);
                    //$amount = bcadd($amount, bcdiv(bcmod(bcmul($amount, '100.0', 0), bcmul($row['charge_rounding'], '100.0', 0)), '100.0', 2), 2);
                }
                $invoice_charge = array(
                    'charge_count'  => $count,
                    'charge_rank'   => $count,
                    'charge_id'     => $row['charge_id'],
                    'charge_name'   => $row['charge_name'],
                    'charge_qty'    => 1,
                    'charge_notes'  => '',
                    'charge_offer'  => $amount,
                    'charge_cost'   => $amount,
                    'charge_online' => $amount,
                );
            }

            $count++;

            foreach ($price_keys as $k2) {
                $invoice_charge['charge_' . $k2 . 'au'] = convertCurrency($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'AUD');
                $invoice_charge['charge_' . $k2 . 'us'] = convertCurrency($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'USD');
            }
            foreach ($currency_keys as $k1) {
                foreach ($price_keys as $k2) {
                    $invoice['charges_' . $k2 . $k1] = bcadd($invoice['charges_' . $k2 . $k1], bcmul($invoice_charge['charge_qty'], $invoice_charge['charge_' . $k2 . $k1], 4), 4);
                }
            }

            $invoice_charges[] = $invoice_charge;

            break;
        }
    }
}

/**
 * Add Total Rounding
 */
$excess = bcmod(bcmul('100', bcadd($invoice['products_offer'], bcadd($invoice['ship_offer'], $invoice['charges_offer'], 2), 2), 0), '5');
if ('AUD' == $invoice['invoice_currency'] && 'CASH' == $invoice['paymenttype_id'] && bccomp($excess, '0', 0) > 0) {
    $invoice_charge = array(
        'charge_count'  => $count,
        'charge_rank'   => $count++,
        'charge_id'     => 3,
        'charge_name'   => 'Cash Rounding',
        'charge_qty'    => 1,
        'charge_notes'  => '',
        'charge_offer'  => convertCurrency(bcmul('0.01', bcsub((bccomp($excess, '3', 0) < 0) ? '0' : '5', $excess, 0), 2), $invoice['invoice_currency']),
        'charge_cost'   => convertCurrency('0.00', $invoice['invoice_currency']),
        'charge_online' => convertCurrency(bcmul('0.01', bcsub((bccomp($excess, '3', 0) < 0) ? '0' : '5', $excess, 0), 2), $invoice['invoice_currency']),
    );
    foreach ($price_keys as $k2) {
        $invoice_charge['charge_' . $k2 . 'au'] = convertCurrency($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'AUD');
        $invoice_charge['charge_' . $k2 . 'us'] = convertCurrency($invoice_charge['charge_' . $k2], $invoice['invoice_currency'], 'USD');
    }
    foreach ($currency_keys as $k1) {
        foreach ($price_keys as $k2) {
            $invoice['charges_' . $k2 . $k1] = bcadd($invoice['charges_' . $k2 . $k1], bcmul($invoice_charge['charge_qty'], $invoice_charge['charge_' . $k2 . $k1], 4), 4);
        }
    }

    $invoice_charges[] = $invoice_charge;
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
$invoice['bill_address_full'] = trim(preg_replace('/\n\n+/', "\n\n", implode("\n", array(
    $invoice['bill_company'],
    (('' != $invoice['bill_company']) ? 'Attn: ' : '') . $invoice['bill_name'],
    $invoice['bill_address'],
    $invoice['bill_city'] . (('' != $invoice['bill_state']) ? (', ' . $invoice['bill_state']) : '') . ' ' . $invoice['bill_postcode'],
    $country_list[$invoice['bill_country']],
    '',
    ('' != $invoice['bill_phone']) ? ('Ph: ' . $invoice['bill_phone']) : '',
    ('' != $invoice['bill_email']) ? ('Email: ' . $invoice['bill_email']) : '',
))));

$country_list                 = invoiceGetOptionList('ship_country');
$invoice['ship_address_full'] = trim(preg_replace('/ *\n( *\n)+/', "\n\n", implode("\n", array(
    $invoice['ship_company'],
    (('' != $invoice['ship_company']) ? 'Attn: ' : '') . $invoice['ship_name'],
    $invoice['ship_address'],
    $invoice['ship_city'] . (('' != $invoice['ship_state']) ? (', ' . $invoice['ship_state']) : '') . ' ' . $invoice['ship_postcode'],
    $country_list[$invoice['ship_country']],
    '',
    ('' != $invoice['ship_phone']) ? ('Ph: ' . $invoice['ship_phone']) : '',
    ('' != $invoice['ship_email']) ? ('Email: ' . $invoice['ship_email']) : '',
))));

$country_list                   = invoiceGetOptionList('notify_country');
$invoice['notify_address_full'] = trim(preg_replace('/ *\n( *\n)+/', "\n\n", implode("\n", array(
    $invoice['notify_company'],
    (('' != $invoice['notify_company']) ? 'Attn: ' : '') . $invoice['notify_name'],
    $invoice['notify_address'],
    $invoice['notify_city'] . (('' != $invoice['notify_state']) ? (', ' . $invoice['notify_state']) : '') . ' ' . $invoice['notify_postcode'],
    $country_list[$invoice['notify_country']],
    '',
    ('' != $invoice['notify_phone']) ? ('Ph: ' . $invoice['notify_phone']) : '',
    ('' != $invoice['notify_email']) ? ('Email: ' . $invoice['notify_email']) : '',
))));

/**
 * Fill in the invoice defaults
 */
foreach (array('product_warranty' => 'def_warranty', 'product_voltage' => 'def_voltage', 'product_plug' => 'def_plug', 'product_condition' => 'def_condition', 'product_packing' => 'def_packing', 'product_terms' => 'def_terms', 'supplier_currency' => 'def_currency', 'supplier_country' => 'def_country', 'supplier_id' => 'default_supplier') as $k => $v) {
    $temp = array();
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

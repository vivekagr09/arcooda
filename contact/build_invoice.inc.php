<?php
/**
 * Returns:
 *  $invoice
 *  $invoice_charges
 *  $invoice_courier_quotes
 *  $invoice_products
 */

moduleLoad("invoice"); //invoiceGetOptionList
moduleLoad('member'); //memberDiscountsFromDatabase, memberProductSpecialPrices
moduleLoad('product'); //productFromDatabase

if (!function_exists("bcceil")) {
    function bcceil($number)
    {
        $diff = bcsub($number, bcadd($number, "0.0", 0), 4);
        if (bccomp($diff, "0.0", 4) != 0) {
            return bcadd($number, bcsub("1.0", $diff, 4), 0);
        }

        return bcadd($number, "0.0", 0);
    }
}

//Hard code
$default_staff = array(
    2 => 159, // Default to Jody
    1 => $_GLOBALS['site_options']['DEFAULT_PARTS_HANDLER'],
    2 => $_GLOBALS['site_options']['DEFAULT_MACHINES_HANDLER'],
    3 => $_GLOBALS['site_options']['DEFAULT_PCBS_HANDLER'],
    4 => $_GLOBALS['site_options']['DEFAULT_MERCHANDISES_HANDLER'],
);

$warranty_list     = [];
if (function_exists('productGetOptionList')) {
    $warranty_list     = productGetOptionList('warranty');
}

$currency_keys = array("", "au", "us");
$price_keys    = array("cost", "offer", "online");
$section_keys  = array("products", "ship", "charges");

$invoice = array_merge(invoiceDefault(), array(
    "ship_mass"              => "0.000",
    "handler_id"             => 2, // Default to steve
    "invoice_currency"       => $_GLOBALS["site_options"]["DEFAULT_CURRENCY"],
    "newsletter_id"          => $_GLOBALS["newsletter"]["id"] ?? 0,
    "site_id"                => $_GLOBALS["site_settings"]["site_id"],

    "invoice_comments"       => "",
    "invoice_status"         => "inquiry",
    "pack_requirement_id"    => 0,
    "plug_id"                => 0,
    "shipping_method"        => "",
    "voltage_id"             => 0,
    "waranty_terms_id"       => 0,
    "def_condition"          => 0,
    "def_country"            => "",
    "def_currency"           => "",
    "def_packing"            => 0,
    "def_plug"               => 0,
    "def_terms"              => 0,
    "def_voltage"            => 0,
    "def_warranty"           => 0,
    "invoice_cost"           => "0.00",
    "invoice_costau"         => "0.00",
    "invoice_costus"         => "0.00",
    "invoice_offer"          => "0.00",
    "invoice_offerau"        => "0.00",
    "invoice_offerus"        => "0.00",
    "invoice_profit"         => "0.00",
    "invoice_profitau"       => "0.00",
    "invoice_profitus"       => "0.00",
    'supply_country'         => $_GLOBALS['site_settings']['supply_country'],
));
if (array_key_exists("customer_requests", $_REQUEST)) {
    $invoice["customer_requests"] = $_REQUEST["customer_requests"];
}
/**
 * Set visitor related data
 */
if (array_key_exists("visitor", $_GLOBALS) && is_array($_GLOBALS["visitor"])) {
    foreach (array("bill_country" => "country", "notify_country" => "country", "ship_country" => "country") as $k => $v) {
        if (array_key_exists($v, $_GLOBALS["visitor"])) {
            $invoice[$k] = $_GLOBALS["visitor"][$v];
        }
    }
    if ($row = $DB->query_first(sprintf(
        "SELECT `voltage`, `plugs` FROM `world`.`country` WHERE `code` = %s LIMIT 1",
        $DB->escape($invoice['bill_country'])
    ), MYSQLI_ASSOC)) {
        $temp = intval($row["voltage"]);
        if (240 == $temp) {
            $invoice["voltage_id"] = 8;
        } elseif ($temp >= 110 && $temp <= 127) {
            $invoice["voltage_id"] = 1;
        } elseif ($temp >= 220 && $temp <= 240) {
            $invoice["voltage_id"] = 5;
        } elseif ($temp >= 110 && $temp <= 240) {
            $invoice["voltage_id"] = 7;
        }
        $invoice["plug_id"] = 0;
        foreach (explode(",", $row["plugs"]) as $v) {
            if (preg_match("/#([0-9]+)\$/", $v, $matches)) {
                $invoice["plug_id"] = intval($matches[1]);
                break;
            }
        }
    }
    if ($row = $DB->query_first(sprintf(
        "SELECT `voltage`, `plugs` FROM `world`.`country` WHERE `code` = %s LIMIT 1",
        $DB->escape($invoice['ship_country'])
    ), MYSQLI_ASSOC)) {
        $temp = intval($row["voltage"]);
        if (240 == $temp) {
            $invoice["voltage_id"] = 8;
        } elseif ($temp >= 110 && $temp <= 127) {
            $invoice["voltage_id"] = 1;
        } elseif ($temp >= 220 && $temp <= 240) {
            $invoice["voltage_id"] = 5;
        } elseif ($temp >= 110 && $temp <= 240) {
            $invoice["voltage_id"] = 7;
        }
        $invoice["plug_id"] = 0;
        foreach (explode(",", $row["plugs"]) as $v) {
            if (preg_match("/#([0-9]+)\$/", $v, $matches)) {
                $invoice["plug_id"] = intval($matches[1]);
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
            if(is_array($member_shippingaddress)){
                if (array_key_exists($v, $member_shippingaddress)) {
                    $invoice[$k] = $member_shippingaddress[$v];
                }
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
 * Try to match world timezones
 */
if ($row = $DB->query_first(sprintf(
    "SELECT `id` FROM `world`.`time_zones` WHERE `country` = %s ORDER BY IF(`name` = %s, 2, IF(`name` = %s, 1, 0)) DESC LIMIT 1",
    $DB->escape($invoice["bill_country"]),
    $DB->escape($invoice["bill_state"]),
    $DB->escape($invoice["bill_city"])
))) {
    $invoice["bill_timezone"] = $row["id"];
}
/**
 * Always force Arcooda to USD
 */
if (39 == $_GLOBALS['site_settings']['site_id']) {
    $invoice['invoice_currency'] = 'USD';
}
$invoice["invoice_exchange"]   = convertCurrency("1.0000", $invoice["invoice_currency"], "AUD", false, 4);
$invoice["invoice_exchangeus"] = convertCurrency("1.0000", $invoice["invoice_currency"], "USD", false, 4);

$count            = 0;
$invoice_products = array();


/**
 * Override with contact form
 */
if (count($_POST)) {
    if (array_key_exists("givenName", $_POST) || array_key_exists("familyName", $_POST)) {
        $invoice['bill_name'] = trim(sprintf('%s %s', $_POST['givenName'], $_POST['familyName']));
        $invoice['ship_name'] = trim(sprintf('%s %s', $_POST['givenName'], $_POST['familyName']));
    }
    if (array_key_exists("organization", $_POST)) {
        $invoice['bill_company'] = $_POST['organization'];
        $invoice['ship_company'] = $_POST['organization'];
    }
    if (array_key_exists("member_email", $_POST)) {
        $invoice["bill_email"] = $_POST["member_email"];
        $invoice["ship_email"] = $_POST["member_email"];
    }
    if (array_key_exists("member_location", $_POST)) {
        $invoice["bill_country"] = $_POST["member_location"];
        $invoice["ship_country"] = $_POST["member_location"];
    }
}
if (array_key_exists("id", $_POST) || array_key_exists("product_name", $_POST)) {
     $invoice_product = array(
        "product_id"        => 0,
        "site_id"           => $invoice["site_id"],
        "product_rank"      => $count++,
        "product_name"      => trim($_REQUEST["product_name"]),
        "product_notes"     => $_REQUEST["comments"],
        "supplier_id"       => 0,
        "product_offer"     => "0.0000",
        "product_online"    => "0.0000",
        "product_qty"       => intval($_REQUEST['product_qty']),
        "product_cost"      => "0.0000",
        "supplier_country"  => "",
        "supplier_currency" => "",
        "generic"           => "y",
        "product_warranty"  => 0,
        "product_voltage"   => 0,
        "product_condition" => 0,
        "product_packing"   => 0,
        "product_terms"     => 0,
        "product_plug"      => 0,
    );

    if (0 != $_POST["id"] && function_exists("productFromDatabase")) {

        $product     = productFromDatabase($_POST['id']);
        $invoice_product = array(
            "product_id"        => 0,
            "site_id"           => $invoice["site_id"],
            "product_rank"      => $count++,
            "product_name"      => trim( $product["product_name"]),
            "supplier_id"       => 0,
            "product_offer"     => "0.0000",
            "product_online"    => "0.0000",
            "product_qty"       => intval($_POST['product_qty']),
            "product_cost"      => "0.0000",
            "supplier_country"  => "",
            "supplier_currency" => "",
            "generic"           => "n",
            "product_warranty"  => 0,
            "product_voltage"   => 0,
            "product_condition" => 0,
            "product_packing"   => 0,
            "product_terms"     => 0,
            "product_plug"      => 0,
        );
        $product["normal_price"]       = convertCurrency($product["primary_price"], $product["primary_currency"], $invoice["invoice_currency"], false, $product["precision"]);
        $product["normal_currency"]    = $invoice["invoice_currency"];
        $product["effective_price"]    = convertCurrency($product["secondary_price"], $product["secondary_currency"], $invoice["invoice_currency"], false, $product["precision"]);
        $product["effective_currency"] = $invoice["invoice_currency"];
        $product["effective_savings"]  = (bccomp($product["normal_price"], $product["effective_price"], $product["precision"]) > 0) ? bcdiv(bcmul(bcsub($product["normal_price"], $product["effective_price"], $product["precision"]), "100.00", 2), $product["normal_price"], 0) : "0";

        $invoice_product = array_merge($invoice_product, array(
            "product_id"         => $product["id"],
            "sub_id"             => 0,
            "product_name"       => $product["product_name"],
            "supplier_notes"      => ("y" == $product["price_online"]) ?'' :'sales price :'.$product["effective_price"].'',
            "product_offer"      => ("y" == $product["price_online"]) ? $product["effective_price"] : "0.0000",
            "product_online"     => convertCurrency(("y" == $product["price_online"]) ? $product["primary_price"] : "0.0000", $product["primary_currency"], $invoice["invoice_currency"], false, $product["precision"]),
            "product_cost"       => convertCurrency($product['cost_price'], $product['cost_currency'],'USD', false, $product['decimal']),
            "supplier_currency"  => 'USD',
            "product_weight"     => $product["weight"],
            "product_weight_inc" => $product["weight_inc"],
            "product_warranty"   => $product["warranty"],
            "product_voltage"    => (($product["power_110"] ?? 0) == ($product["power_240"] ?? 0) && 0 != ($product["power_110"] ?? 0)) ? 7 : 0,
            "product_condition"  => $product["condition"],
            "decimal"            => $product["precision"],
            "product_section"    => $product["section"],
            "product_notes"      => ("n" == $product["price_online"])? 'Sales Price :-'.$product["effective_price"] :'',
            "promotion_id"       => $product["promotion_id"] ?? 0,
        ));

        // $notes = array($_POST["product_name"], $_POST["comments"]);
        if (array_key_exists($invoice_product['product_warranty'], $warranty_list) && '' != $warranty_list[$invoice_product['product_warranty']]) {
            $notes[] = 'Warranty: ' . $warranty_list[$invoice_product['product_warranty']];
        }

        $notes[] = $_POST["comments"] ?? $_POST["customer_requests"];

        $last_note = "";

        //Method 2: By cheapest price
        if (function_exists("productBulkPricingFromDatabase")) {
            foreach (productBulkPricingFromDatabase($invoice_product["product_id"], $invoice_product["sub_id"]) as $special) {
                if ($special["qty"] <= $invoice_product["product_qty"]) {
                    $new_price = convertCurrency($special["unit_sale_price_us"], "USD", $invoice["invoice_currency"], false, $invoice_product["decimal"]);
                    if (array_key_exists("unit_sale_price", $special)) {
                        $new_price = convertCurrency((strtoupper($product["selected"]) == "AUD") ? $special["unit_sale_price"] : $special["unit_sale_price_us"], (strtoupper($product["selected"]) == "AUD") ? "AUD" : "USD", $invoice["invoice_currency"], false, $invoice_product["decimal"]);
                    }
                    if (bccomp($new_price, $invoice_product["product_offer"], $invoice_product["decimal"]) < 0) {
                        $invoice_product["product_offer"] = $new_price;
                        $savings                          = bcsub($invoice_product["product_online"], $invoice_product["product_offer"], $invoice_product["decimal"]);
                        $last_note                        = sprintf(
                            "Bulk Discount: %d%% (RRP: %s%s %s, save %s%s %s)",
                            bcdiv(bcmul($savings, "100.0", 0), $invoice_product["product_online"], 0),
                            getCurrencySymbol($invoice["invoice_currency"]),
                            $invoice_product["product_online"],
                            $invoice["invoice_currency"],
                            getCurrencySymbol($invoice["invoice_currency"]),
                            $savings,
                            $invoice["invoice_currency"]
                        );
                    }
                }
            }
        }

        /**
         * Special Pricing
         */
        if ("y" == $product["on_special"] && bccomp($invoice_product["product_online"], $invoice_product["product_offer"], 2) > 0) {
            $savings   = bcsub($invoice_product["product_online"], $invoice_product["product_offer"], $invoice_product["decimal"]);
            $percent   = bcceil(bcdiv(bcmul($savings, "100.0", 4), $invoice_product["product_online"], 4));
            $last_note = sprintf("Sale Discount: %s%% (RRP: %s%s %s, save %s%s %s)", $percent, getCurrencySymbol($invoice["invoice_currency"]), $invoice_product["product_online"], $invoice["invoice_currency"], getCurrencySymbol($invoice["invoice_currency"]), $savings, $invoice["invoice_currency"]);
        }

        /**
         * Member Discount
         */
        if (function_exists("memberDiscountsFromDatabase") && ($percent = memberDiscountsFromDatabase($_GLOBALS['member']['id'] ?? 0, $invoice["site_id"])) != 0) {
            $new_offer = convertCurrency(bcmul($invoice_product["product_offer"], bcmul("0.01", 100 - $percent, 4), 4), $invoice["invoice_currency"], "", false, $invoice_product["decimal"]);
            if (bccomp($invoice_product["product_offer"], $new_offer, 2) > 0) {
                $invoice_product["product_offer"] = $new_offer;
                $savings                          = bcsub($invoice_product["product_online"], $new_offer, $invoice_product["decimal"]);
                $percent                          = bcceil(bcdiv(bcmul($savings, "100.0", 4), $invoice_product["product_online"], 4));
                $last_note                        = sprintf("Member Discount: %s%% (RRP: %s%s %s, save %s%s %s)", $percent, getCurrencySymbol($invoice["invoice_currency"]), $invoice_product["product_online"], $invoice["invoice_currency"], getCurrencySymbol($invoice["invoice_currency"]), $savings, $invoice["invoice_currency"]);
            }
        }

        /**
         * Apply member pricing
         */
        if (function_exists("memberProductSpecialPrices") && is_array($special_prices = memberProductSpecialPrices($_GLOBALS['member']['id'] ?? 0, $invoice_product["product_id"], $invoice['site_id']))) {
            foreach ($special_prices as $special) {
                $new_offer = convertCurrency($special["special_price"], $product["primary_currency"], $invoice["invoice_currency"], false, $invoice_product["decimal"]);
                if ($special["min_order"] <= $invoice_product["product_qty"] && bccomp($invoice_product["product_offer"], $new_offer, 2) > 0) {
                    $invoice_product["product_offer"] = $new_offer;
                    $savings                          = bcsub($invoice_product["product_online"], $new_offer, $invoice_product["decimal"]);
                    $percent                          = bcceil(bcdiv(bcmul($savings, "100.0", 4), $invoice_product["product_online"], 4));
                    $last_note                        = sprintf("Member Special Price: %s%0.2f %s (RRP: %s%s %s, save %s%%)", getCurrencySymbol($invoice["invoice_currency"]), $new_offer, $invoice["invoice_currency"], getCurrencySymbol($invoice["invoice_currency"]), $invoice_product["product_online"], $invoice["invoice_currency"], $percent);
                }
            }
        }

        $notes[] = $last_note;

        if (count($notes)) {
            $invoice_product["product_notes"] = trim(implode("\n", $notes));
        }
    }

    foreach ($price_keys as $k2) {
        $invoice_product["product_" . $k2 . "au"] = convertCurrency($invoice_product["product_" . $k2], $invoice["invoice_currency"], "AUD");
        $invoice_product["product_" . $k2 . "us"] = convertCurrency($invoice_product["product_" . $k2], $invoice["invoice_currency"], "USD");
    }
    foreach ($currency_keys as $k1) {
        foreach ($price_keys as $k2) {
            $invoice["products_" . $k2 . $k1] = bcadd($invoice["products_" . $k2 . $k1] ?? '0.0000', bcmul($invoice_product["product_qty"], $invoice_product["product_" . $k2 . $k1] ?? '0.0000', 4), 4);
        }
    }

    $invoice_products[] = $invoice_product;
}

/**
 * Set the default handler
 */
if ($row = $DB->query_first(sprintf(
    "SELECT `staff_id` FROM `highwaygroup`.`site_default_handler` WHERE `site_id` = %d LIMIT 1",
    $invoice["site_id"]
), MYSQLI_ASSOC)) {
    $invoice["handler_id"] = $row["staff_id"];
}

$count                  = 0;
$invoice_courier_quotes = array();

$count           = 0;
$invoice_charges = array();

/**
 * Add up and round all totals
 */
foreach ($currency_keys as $k1) {
    foreach ($price_keys as $k2) {
        foreach ($section_keys as $k3) {
            $invoice[$k3 . "_" . $k2 . $k1] = bcadd($invoice[$k3 . "_" . $k2 . $k1] ?? '0.00', "0.00", 2);
        }
        $invoice["invoice_" . $k2 . $k1] = bcadd($invoice["products_" . $k2 . $k1], bcadd($invoice["ship_" . $k2 . $k1], $invoice["charges_" . $k2 . $k1], 2), 2);
    }
    $invoice["invoice_profit" . $k1] = bcsub($invoice["invoice_offer" . $k1], $invoice["invoice_cost" . $k1], 2);
}

/**
 * Set the extended invoice values
 */
$country_list                 = invoiceGetOptionList("bill_country");
$invoice["bill_address_full"] = trim(preg_replace("/\\n\\n+/", "\n\n", implode("\n", array(
    $invoice["bill_company"],
    (("" != $invoice["bill_company"]) ? "Attn: " : "") . $invoice["bill_name"],
    $invoice["bill_address"],
    $invoice["bill_city"] . (("" != $invoice["bill_state"]) ? (", " . $invoice["bill_state"]) : "") . " " . $invoice["bill_postcode"],
    $country_list[$invoice["bill_country"]],
    "",
    ("" != $invoice["bill_phone"]) ? ("Ph: " . $invoice["bill_phone"]) : "",
    ("" != $invoice["bill_email"]) ? ("Email: " . $invoice["bill_email"]) : "",
))));

$country_list                 = invoiceGetOptionList("ship_country");
$invoice["ship_address_full"] = trim(preg_replace("/ *\\n( *\\n)+/", "\n\n", implode("\n", array(
    $invoice["ship_company"],
    (("" != $invoice["ship_company"]) ? "Attn: " : "") . $invoice["ship_name"],
    $invoice["ship_address"],
    $invoice["ship_city"] . (("" != $invoice["ship_state"]) ? (", " . $invoice["ship_state"]) : "") . " " . $invoice["ship_postcode"],
    $country_list[$invoice["ship_country"]],
    "",
    ("" != $invoice["ship_phone"]) ? ("Ph: " . $invoice["ship_phone"]) : "",
    ("" != $invoice["ship_email"]) ? ("Email: " . $invoice["ship_email"]) : "",
))));

$country_list                   = invoiceGetOptionList("notify_country");
$invoice["notify_address_full"] = trim(preg_replace("/ *\\n( *\\n)+/", "\n\n", implode("\n", array(
    $invoice["notify_company"],
    (("" != $invoice["notify_company"]) ? "Attn: " : "") . $invoice["notify_name"],
    $invoice["notify_address"],
    $invoice["notify_city"] . (("" != $invoice["notify_state"]) ? (", " . $invoice["notify_state"]) : "") . " " . $invoice["notify_postcode"],
    $country_list[$invoice["notify_country"]],
    "",
    ("" != $invoice["notify_phone"]) ? ("Ph: " . $invoice["notify_phone"]) : "",
    ("" != $invoice["notify_email"]) ? ("Email: " . $invoice["notify_email"]) : "",
))));

/**
 * Fill in the invoice defaults
 */
foreach (array(
    "product_warranty"  => "def_warranty",
    "product_voltage"   => "def_voltage",
    "product_plug"      => "def_plug",
    "product_condition" => "def_condition",
    "product_packing"   => "def_packing",
    "product_terms"     => "def_terms",
    "supplier_currency" => "def_currency",
    "supplier_country"  => "def_country",
    "supplier_id"       => "default_supplier",
    "product_section"   => "producttype_id",
) as $k => $v) {
    $temp = array();
    foreach ($invoice_products as $invoice_product) {
        if (array_key_exists($k, $invoice_product) && "" != $invoice_product[$k]) {
            $temp[$invoice_product[$k]] = $invoice_product[$k];
        }
    }
    unset($temp[0]);
    if (count(array_unique($temp)) == 1) {
        $invoice[$v] = reset($temp);
    }
}

/*if (array_key_exists($invoice['producttype_id'], $default_staff)) {
    $invoice['handler_id'] = $default_staff[$invoice['producttype_id']];
}*/

$invoice["pack_requirement_id"] = $invoice["def_packing"];
//$invoice["waranty_terms_id"]    = $invoice["def_warranty"];
if (0 != $invoice["def_voltage"] || 0 != $invoice["def_plug"]) {
    $invoice["voltage_id"] = $invoice["def_voltage"];
    $invoice["plug_id"]    = $invoice["def_plug"];
}
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
unset($warranty_list);
*/

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
    0 => 159, // Default to Jody
    1 => $_GLOBALS['site_options']['DEFAULT_PARTS_HANDLER'],
    2 => $_GLOBALS['site_options']['DEFAULT_MACHINES_HANDLER'],
    3 => $_GLOBALS['site_options']['DEFAULT_PCBS_HANDLER'],
    4 => $_GLOBALS['site_options']['DEFAULT_MERCHANDISES_HANDLER'],
);

$currency_keys = array("", "au", "us");
$price_keys    = array("cost", "offer", "online");
$section_keys  = array("products", "ship", "charges");

$invoice = array(
    "ship_mass"              => "0.000",

    "handler_id"             => 159, // Default to Jody
    "invoice_currency"       => ($_REQUEST['item']["currency"])?$_REQUEST['item']["currency"]:$_GLOBALS["site_options"]["DEFAULT_CURRENCY"],
    "newsletter_id"          => $_GLOBALS["newsletter"]["id"],
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
);
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
if (array_key_exists("member", $_GLOBALS) && is_array($_GLOBALS["member"])) {
    foreach (array(
        "bill_address"     => "address_1",
        "bill_city"        => "city",
        "bill_company"     => "company",
        "bill_country"     => "country",
        "bill_email"       => "email_address",
        "bill_phone"       => "phone_number",
        "bill_postcode"    => "zip",
        "bill_state"       => "state",
        "invoice_currency" => "currency",
        "member_id"        => "member_id",
        "notify_address"   => "address_1",
        "notify_city"      => "city",
        "notify_company"   => "company",
        "notify_country"   => "country",
        "notify_email"     => "email_address",
        "notify_phone"     => "phone_number",
        "notify_postcode"  => "zip",
        "notify_state"     => "state",
        "ship_address"     => "address_1",
        "ship_city"        => "city",
        "ship_company"     => "company",
        "ship_country"     => "country",
        "ship_email"       => "email_address",
        "ship_phone"       => "phone_number",
        "ship_postcode"    => "zip",
        "ship_state"       => "state",
        "vat_number"       => "vat_number",
    ) as $k => $v) {
        if (array_key_exists($v, $_GLOBALS["member"])) {
            $invoice[$k] = $_GLOBALS["member"][$v];
        }
    }

    @$invoice["bill_name"]    = $_GLOBALS['member']['full_name'];
    @$invoice["notify_name"]  = $_GLOBALS['member']['full_name'];
    @$invoice["ship_name"]    = $_GLOBALS['member']['full_name'];

    /**
     * Set the default shipping address
     */
    if (array_key_exists("ship_addr", $_GLOBALS["member"]) && is_array($_GLOBALS["member"]["ship_addr"])) {
        foreach ($_GLOBALS["member"]["ship_addr"] as $member_shippingaddress) {
            foreach (array(
                "ship_address"  => "address_1",
                "ship_city"     => "city",
                "ship_company"  => "company",
                "ship_country"  => "country",
                "ship_email"    => "email",
                "ship_name"     => "name",
                "ship_needs"    => "instructions",
                "ship_phone"    => "phone_number",
                "ship_postcode" => "zip",
                "ship_state"    => "state",
            ) as $k => $v) {
                $invoice[$k] = $member_shippingaddress[$v];
            }
            if ("y" == $member_shippingaddress["default"]) {
                break;
            }
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

$invoice["invoice_exchange"]   = convertCurrency("1.0000", $invoice["invoice_currency"], "AUD", false, 4);
$invoice["invoice_exchangeus"] = convertCurrency("1.0000", $invoice["invoice_currency"], "USD", false, 4);

$count            = 0;
$invoice_products = array();


/**
 * Override with contact form
 */
if (array_key_exists("member_name", $_REQUEST)) {
    $invoice["bill_name"] = $_REQUEST["member_name"];
    $invoice["ship_name"] = $_REQUEST["member_name"];
}
if (array_key_exists("member_email", $_REQUEST)) {
    $invoice["bill_email"] = $_REQUEST["member_email"];
    $invoice["ship_email"] = $_REQUEST["member_email"];
}
if (array_key_exists("member_location", $_REQUEST)) {
    $invoice["bill_country"] = $_REQUEST["member_location"];
    $invoice["ship_country"] = $_REQUEST["member_location"];
}
if (array_key_exists("member_phone", $_REQUEST)) {
    $invoice["bill_phone"] = $_REQUEST["member_phone"];
    $invoice["ship_phone"] = $_REQUEST["member_phone"];
}
if (array_key_exists("comments", $inquiry)) {
    $invoice["customer_requests"] = $_REQUEST["comments"];
}

if (array_key_exists("online_description", $_REQUEST['item']) || array_key_exists("online_item", $_REQUEST['item'])) {
    $invoice_product = array(
        "product_id"        => intval($_REQUEST['item']["online_item_id"]),
        "site_id"           => $invoice["site_id"],
        "product_rank"      => $count++,
        "product_name"      => $_REQUEST['item']["online_item"],
        "product_notes"     => $_REQUEST['item']["online_description"],
        "supplier_id"       => 0,
        "product_offer"     => "0.0000",
        "product_online"    => "0.0000",
        "product_qty"       => intval($_REQUEST['item']["online_qty_needed"]),
        "product_cost"      => $_REQUEST['item']["price"],
        "supplier_country"  => $_REQUEST['item']["currency"],
        "supplier_currency" => "",
        "generic"           => ($_REQUEST['item']["online_item_id"])?"y":"n",
        'product_section'   =>getcategoryParentFromDatabase($_REQUEST['item']["type"]),
        "product_warranty"  => 0,
        "product_voltage"   => 0,
        "product_condition" => 0,
        "product_packing"   => 0,
        "product_terms"     => 0,
        "product_plug"      => 0,
    );
    if (0 != $_REQUEST['item']["online_item_id"] && function_exists("productFromDatabase")) {
        $product     = productFromDatabase($_REQUEST['item']["online_item_id"]);
        $product["normal_price"]       = convertCurrency($_REQUEST['item']["price"], $product["primary_currency"], $_REQUEST['item']["currency"], false, $product["precision"]);
        $product["normal_currency"]    = $_REQUEST['item']["currency"];
        $product["effective_price"]    = convertCurrency($_REQUEST['item']["price"], $product["secondary_currency"], $invoice["invoice_currency"], false, $product["precision"]);
        $product["effective_currency"] = $invoice["invoice_currency"];
        $product["effective_savings"]  = (bccomp($product["normal_price"], $product["effective_price"], $product["precision"]) > 0) ? bcdiv(bcmul(bcsub($product["normal_price"], $product["effective_price"], $product["precision"]), "100.00", 2), $product["normal_price"], 0) : "0";

        $invoice_product = array_merge($invoice_product, array(
            "product_id"         => $product["id"],
            "product_name"       => trim($_REQUEST['item']["online_item"]),
            "product_offer"      => ("y" == $product["price_online"]) ? $product["effective_price"] : "0.0000",
            "product_online"     => convertCurrency(("y" == $product["price_online"]) ? $product["primary_price"] : "0.0000", $product["primary_currency"], $invoice["invoice_currency"], false, $product["precision"]),
            "product_cost"       => $product["cost_price"],
            "supplier_currency"  => $product["cost_currency"],
            "product_weight"     => $product["weight"],
            "product_weight_inc" => $product["weight_inc"],
            "product_warranty"   => $product["warranty"],
            "product_voltage"    => ($product["power_110"] == $product["power_240"] && 0 != $product["power_110"]) ? 7 : 0,
            "product_condition"  => $product["condition"],
            "decimal"            => $product["precision"],
            "product_section"    => getcategoryParentFromDatabase($_REQUEST['item']["type"]),
            "promotion_id"       => $product["promotion_id"],
        ));

        $notes = array(trim($_REQUEST['item']["online_item"]),$_REQUEST['item']["online_description"]);

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
        if (function_exists("memberDiscountsFromDatabase") && ($percent = memberDiscountsFromDatabase($_GLOBALS['member']['id'], $invoice["site_id"])) != 0) {
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
        if (function_exists("memberProductSpecialPrices") && is_array($special_prices = memberProductSpecialPrices($member['member_id'], $invoice_product["product_id"], $invoice['site_id']))) {
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
            $invoice["products_" . $k2 . $k1] = bcadd($invoice["products_" . $k2 . $k1], bcmul($invoice_product["product_qty"], $invoice_product["product_" . $k2 . $k1], 4), 4);
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
            $invoice[$k3 . "_" . $k2 . $k1] = bcadd($invoice[$k3 . "_" . $k2 . $k1], "0.00", 2);
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
if (array_key_exists($invoice['producttype_id'], $default_staff)) {
    $invoice['handler_id'] = $default_staff[$invoice['producttype_id']];
}

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

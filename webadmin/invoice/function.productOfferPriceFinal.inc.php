<?php
if (!function_exists('productOfferPriceFinal')) {
    function productOfferPriceFinal($product_id, $product_qty = 1, $member_id = 0, $site_id = 9)
    {
        moduleLoad('member'); //memberDiscountsFromDatabase, memberProductSpecialPrices
        moduleLoad('product'); //productFromDatabase

        $sub_id  = 0;
        $site_id = intval(reset(explode("|", $site_id)));
        //if ($member_id == 0) $member_id = $_GLOBALS["member"]["id"];
        $currency = "USD";

        $product_str = productStrJoin(intval($product_id), intval($sub_id));
        $product     = productFromDatabase($product_str);

        $result = array(
            "product_offer"    => convertCurrency($product["secondary_price"], $product["secondary_currency"], $currency, false, $product["precision"]),
            "product_offer_us" => convertCurrency($product["secondary_price"], $product["secondary_currency"], "USD", false, $product["precision"]),
            "product_offer_au" => convertCurrency($product["secondary_price"], $product["secondary_currency"], "AUD", false, $product["precision"]),
            "cost_price"       => convertCurrency($product["cost_price"], $product["cost_currency"], $currency),
            "cost_price_us"    => convertCurrency($product["cost_price"], $product["cost_currency"], "USD"),
            "cost_price_au"    => convertCurrency($product["cost_price"], $product["cost_currency"], "AUD"),
        );

        if (function_exists("productBulkPricingFromDatabase")) {
            foreach (productBulkPricingFromDatabase($product["id"], $product["sub_id"]) as $special) {
                if ($special["qty"] <= $product_qty) {
                    $new_price = convertCurrency((strtoupper($product["selected"]) == "AUD") ? $special["unit_sale_price"] : $special["unit_sale_price_us"], (strtoupper($product["selected"]) == "AUD") ? "AUD" : "USD", $currency, false, $product["precision"]);
                    if (bccomp($new_price, $result["product_offer"]) < 0) {
                        $result["product_offer"] = $new_price;
                    }

                    $new_price = convertCurrency((strtoupper($product["selected"]) == "AUD") ? $special["unit_sale_price"] : $special["unit_sale_price_us"], (strtoupper($product["selected"]) == "AUD") ? "AUD" : "USD", "USD", false, $product["precision"]);
                    if (bccomp($new_price, $result["product_offer_us"]) < 0) {
                        $result["product_offer_us"] = $new_price;
                    }

                    $new_price = convertCurrency((strtoupper($product["selected"]) == "AUD") ? $special["unit_sale_price"] : $special["unit_sale_price_us"], (strtoupper($product["selected"]) == "AUD") ? "AUD" : "USD", "AUD", false, $product["precision"]);
                    if (bccomp($new_price, $result["product_offer_au"]) < 0) {
                        $result["product_offer_au"] = $new_price;
                    }
                }
            }
        }

        if (function_exists("memberDiscountsFromDatabase") && ($percent = memberDiscountsFromDatabase($member_id, $site_id)) != 0) {
            $new_offer = convertCurrency(bcmul($result["product_offer"], bcmul("0.01", 100 - $percent, 4), 4), $currency, "", false, $product["precision"]);
            if (bccomp($result["product_offer"], $new_offer, 2) > 0) {
                $result["product_offer"] = $new_offer;
            }

            $new_offer = convertCurrency(bcmul($result["product_offer_us"], bcmul("0.01", 100 - $percent, 4), 4), "USD", "", false, $product["precision"]);
            if (bccomp($result["product_offer_us"], $new_offer, 2) > 0) {
                $result["product_offer_us"] = $new_offer;
            }

            $new_offer = convertCurrency(bcmul($result["product_offer_au"], bcmul("0.01", 100 - $percent, 4), 4), "AUD", "", false, $product["precision"]);
            if (bccomp($result["product_offer_au"], $new_offer, 2) > 0) {
                $result["product_offer_au"] = $new_offer;
            }
        }

        if (function_exists("memberProductSpecialPrices") && is_array($special_prices = memberProductSpecialPrices($member_id, $product["id"], $site_id))) {
            foreach ($special_prices as $special) {
                $new_offer = convertCurrency($special["special_price"], $product["primary_currency"], $currency, false, $product["precision"]);
                if ($special["min_order"] <= $product_qty && bccomp($result["product_offer"], $new_offer, 2) > 0) {
                    $result["product_offer"] = $new_offer;
                }

                $new_offer = convertCurrency($special["special_price"], $product["primary_currency"], "USD", false, $product["precision"]);
                if ($special["min_order"] <= $product_qty && bccomp($result["product_offer_us"], $new_offer, 2) > 0) {
                    $result["product_offer_us"] = $new_offer;
                }

                $new_offer = convertCurrency($special["special_price"], $product["primary_currency"], "AUD", false, $product["precision"]);
                if ($special["min_order"] <= $product_qty && bccomp($result["product_offer_au"], $new_offer, 2) > 0) {
                    $result["product_offer_au"] = $new_offer;
                }
            }
        }

        return $result;
    }
}

<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('member'); //memberDiscountsFromDatabase, memberProductSpecialPrices
moduleLoad('product'); //productFromDatabase

$product_id  = intval($posted['product_id']);
$sub_id      = intval($posted['sub_id']);
$site_id     = intval($posted['site_id']);
$member_id   = intval($posted['member_id']);
$currency    = strtoupper($posted['currency']);
$product_qty = intval($posted['product_qty']);

if (0 == $site_id) {
    $site_id = $_GLOBALS["site_settings"]["site_id"];
}

if (1 == $site_id) {
    $site_id = 3;
}

$product = productFromDatabase($product_id, $site_id);
$pricing = array(); //productPricingFromDatabase($product_id, $site_id);

function productOfferPriceFinal($product_id, $product_qty = 1, $member_id = 0, $site_id = 3)
{
    global $DB, $product, $pricing;

    if ('9|16' == $site_id) {
        $site_id = 9;
    }

    $currency                  = 'USD';
    $product['product_qty']    = $product_qty;
    $product['sales_currency'] = strtoupper($pricing['selected']);
    $price                     = $price_us                     = 0;
    $price_us                  = $pricing['sales_price_us'];
    if (0 == $price_us) {
        $price_us = convertCurrency($pricing['sales_price'], 'USD', 'USD', false);
    }

    $price = convertCurrency($pricing['sales_price'], 'USD', $currency, false);
    if (0 == $price) {
        $price = convertCurrency($pricing['sales_price_us'], 'USD', 'USD', false);
    }

    $member_discount = memberDiscountsFromDatabase($member_id, $site_id);
    $orig_price      = $price;
    $orig_priceus    = $price_us;
    $spec_price      = $spec_priceus      = '';

    $bulk_prices = array();

    //Product Bulk Pricing only for converters and car converters at the moment..
    if (9 == $site_id || 16 == $site_id) {
        $query = $DB->query("SELECT qty, unit_sale_price FROM products_bulk_pricing WHERE product_id=" . intval($product_id) . " ORDER BY qty DESC");
        while ($row = $DB->fetch_assoc($query)) {
            $bprice = convertCurrency($row['unit_sale_price'], 'USD', $currency, false);
            $bprice_us = convertCurrency($row['unit_sale_price'], 'USD', '', false);

            $new_row['use_count']     = 0;
            $new_row['qty']           = $row['qty'];
            $new_row['unit_price']    = $bprice;
            $new_row['unit_price_us'] = $bprice_us;
            $new_row['sale_price']    = $row['qty'] * $bprice;
            $new_row['sale_price_us'] = $row['qty'] * $bprice_us;
            $bulk_prices[]            = $new_row;
        }
    }

    //Product Special Pricing
    if ('y' == $product['special_flag'] && $pricing) {
        $ex_time = $product['special_expiry_unix'];
        if (0 != $ex_time) {
            $ex_time = mktime(0, 0, -1, date('m', $ex_time), date('d', $ex_time) + 1, date('y', $ex_time));
        }

        if ($ex_time > time() || 0 == $ex_time) {
            $price    = $spec_price    = convertCurrency($product['special_price'], strtoupper($pricing['selected']), $currency, false);
            $price_us = $spec_priceus = convertCurrency($product['special_price'], strtoupper($pricing['selected']), 'USD', false);
        }
    }

    //Members Specials Pricing.
    if ($member_id > 0) {
        $specials = memberProductSpecialPrices($member_id, $product_id, $site_id);
        //print_array($specials);
        //if($member_id==1344) print_array($specials);
        if (count($specials)) {
            $to_use = array();
            foreach ($specials as $row => $special) {
                if ($product_qty >= $special['min_order']) {
                    $to_use = $special;
                }
            }

            if (count($to_use)) {
                $new_row['use_count']     = 0;
                $new_row['qty']           = $product_qty;
                $new_row['unit_price']    = $to_use['special_price'];
                $new_row['unit_price_us'] = $to_use['special_priceus'];
                $new_row['sale_price']    = $product_qty * $to_use['special_price'];
                $new_row['sale_price_us'] = $product_qty * $to_use['special_priceus'];
                $bulk_prices[]            = $new_row;
            }
        }
    }

    //Finally the original RRP.
    $bulk_prices[] = array(
        'use_count'     => 0,
        'qty'           => 1,
        'unit_price'    => $price,
        'unit_price_us' => $price_us,
        'sale_price'    => $price,
        'sale_price_us' => $price_us,
    );

    //CURRENCY ADJUSTMENT IS HERE
    foreach ($bulk_prices as $key => $row) {
        $row['sale_price']    = convertCurrency($row['qty'] * $row['unit_price'], $currency, '', false);
        $row['sale_price_us'] = convertCurrency($row['qty'] * $row['unit_price_us'], 'USD', '', false);
    }

    //print_array($bulk_prices);
    $unit_prices    = array();
    $unit_prices_us = array();
    foreach ($bulk_prices as $key => $row) {
        $unit_prices[$key]    = floatval($row['unit_price']);
        $unit_prices_us[$key] = floatval($row['unit_price_us']);
    }
    array_multisort($unit_prices, SORT_ASC, $unit_prices_us, SORT_ASC, $bulk_prices);

    $bulk_qty         = $product_qty;
    $product_offer    = 0.0;
    $product_offer_us = 0.0;
    foreach ($bulk_prices as $key => $row) {
        $fit                            = floor($bulk_qty / $row['qty']);
        $bulk_prices[$key]['use_count'] = $fit;
        $bulk_qty -= $fit * $row['qty'];
        $product_offer += $fit * $row['sale_price'];
        $product_offer_us += $fit * $row['sale_price_us'];
    }
    if ($product_qty > 1) {
        $product_offer /= $product_qty;
        $product_offer_us /= $product_qty;
    }

    if ('' != $spec_price && '' != $spec_priceus) {
        $orig_price   = $spec_price;
        $orig_priceus = $spec_priceus;
    }
    if ($member_discount > 0) {
        $new_offer   = convertCurrency($orig_price * (1 - $member_discount / 100), $currency, '', false);
        $new_offerus = convertCurrency($orig_priceus * (1 - $member_discount / 100), $currency, '', false);
        if ($new_offer < $product_offer) {
            $product_offer    = $new_offer;
            $product_offer_us = $new_offerus;
        }
    }
    $temp = array('product_offer' => $product_offer, 'product_offer_us' => $product_offer_us);
    return $temp;
}

$member_id   = intval($posted['member_id']);
$cur         = $posted['currency'];
$product_qty = intval($posted['product_qty']);
$prices      = array();
if (9 == $site_id || 16 == $site_id) {
    $prices = productOfferPriceFinal($product_id, $product_qty, $member_id, $site_id);
} else {
    $member_discount = 0; //memberDiscountsFromDatabase($member_id,$site_id);
    $offer           = $sales_price           = convertCurrency($product['sales_price'], $product['sales_currency'], 'USD', false);
    $offer_us        = $sales_price_us        = convertCurrency($product['sales_price'], $product['sales_currency'], 'USD', false);
    $spec_offer      = $spec_offer_us      = 0;
    if ($member_id > 0) {
        $specials = array(); //memberProductSpecialPrices($member_id,$product_id,$site_id);
        //print_array($specials);
        //if($member_id==1344) print_array($specials);
        if (is_array($specials) && count($specials)) {
            $to_use = array();
            foreach ($specials as $row => $special) {
                if ($product_qty >= $special['min_order']) {
                    $to_use = $special;
                }
            }

            if (count($to_use)) {
                $spec_offer    = convertCurrency($to_use['special_price'], 'USD', $cur, false);
                $spec_offer_us = convertCurrency($to_use['special_priceus'], 'USD', '', false);
            }
        }
    }
    if ($member_discount > 0) {
        $new_offer   = convertCurrency($sales_price * (1 - $member_discount / 100), $cur, '', false);
        $new_offerus = convertCurrency($sales_price_us * (1 - $member_discount / 100), $cur, '', false);
        if ($new_offer < $sales_price) {
            $offer    = $new_offer;
            $offer_us = $new_offerus;
        }
    }

    $prices['product_offer']    = ($spec_offer < $offer && $spec_offer > 0) ? $spec_offer : $offer;
    $prices['product_offer_us'] = ($spec_offer_us < $offer_us && $spec_offer_us > 0) ? $spec_offer_us : $offer_us;
}

if ('USD' == $cur) {
    $sales_price = convertCurrency($prices['product_offer_us'], 'USD', '', false);
} elseif ('USD' == $cur) {
    $sales_price = convertCurrency($prices['product_offer'], 'USD', '', false);
} else {
    $sales_price = convertCurrency($prices['product_offer'], 'USD', $cur, false);
}

$sales_price_us = convertCurrency($prices['product_offer_us'], 'USD', '', false);
?>

<?= $sales_price ?>, <?= $sales_price_us ?>
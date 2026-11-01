<?php
function productOfferPriceFinal($product_id, $product_qty = 1, $member_id = 0, $site_id = 3)
{
	moduleLoad('member'); //memberDiscountsFromDatabase, memberProductSpecialPrices
	moduleLoad('product'); //productFromDatabase

	global $DB;

	$currency = 'USD';
	$product = productFromDatabase($product_id, $site_id);
	//$pricing = productPricingFromDatabase($product_id, $site_id);
	$pricing = array();
	$member_discount = memberDiscountsFromDatabase($member_id, $site_id);
	//$specials = ($member_id > 0) ? memberProductSpecialPrices($member_id, $product_id, $site_id) : array();
	if (!is_array($specials)) $specials = array();
	$price = $price_us = 0;
	$price_us = $pricing['sales_price_us'];
	if ($price_us == 0) $price_us = convertCurrency($pricing['sales_price'], 'USD', 'USD', true);
	$cost_price = $cost_price_us = 0;
	if ($site_id == 9 || $site_id == 16) {
		$cost_price_us = convertCurrency($pricing['cost_price_us'], 'USD', '', true);
		if ($cost_price_us == 0) $cost_price_us = convertCurrency($pricing['cost_price'], 'USD', 'USD', true);
		$cost_price = convertCurrency($pricing['cost_price'], 'USD', '', true);
		if ($cost_price == 0) $cost_price = convertCurrency($pricing['cost_price_us'], 'USD', 'USD', true);
		$price = $pricing['sales_price'];
		if ($price == 0) $price = convertCurrency($pricing['sales_price_us'], 'USD', 'USD', true);
		$orig_price = $price;
		$orig_priceus = $price_us;
		$spec_price = $spec_priceus = '';
		$bulk_prices = array();
		//Product Bulk Pricing only for converters and car converters at the moment..
		$query = $DB->query("SELECT qty, unit_sale_price FROM products_bulk_pricing WHERE product_id=" . intval($product_id) . " ORDER BY qty DESC");
		while ($row = $DB->fetch_assoc($query)) {
			$bprice = convertCurrency($row['unit_sale_price'], 'USD', $currency, true);
			$bprice_us = convertCurrency($row['unit_sale_price'], 'USD', '', true);
			$new_row['use_count'] = 0;
			$new_row['qty'] = $row['qty'];
			$new_row['unit_price'] = $bprice;
			$new_row['unit_price_us'] = $bprice_us;
			$new_row['sale_price'] = $row['qty'] * $bprice;
			$new_row['sale_price_us'] = $row['qty'] * $bprice_us;
			$bulk_prices[] = $new_row;
		}
		//Product Special Pricing
		if ($product_item['special_flag'] == 'y' && $pricing) {
			$ex_time = $product_item['special_expiry_unix'];
			if ($ex_time != 0) $ex_time = mktime(0, 0, -1, date('m', $ex_time), date('d', $ex_time) + 1, date('y', $ex_time));
			if ($ex_time > time() || $ex_time == 0) {
				$price = $spec_price = convertCurrency($product_item['special_price'], strtoupper($pricing['selected']), $currency, true);
				$price_us = $spec_priceus = convertCurrency($product_item['special_price'], strtoupper($pricing['selected']), 'USD', true);
			}
		}
		//Members Specials Pricing.
		if ($member_id > 0) {
			if (count($specials) > 0) {
				$to_use = array();
				foreach($specials as $row => $special) if ($product_qty >= $special['min_order']) $to_use = $special;
				if (count($to_use)) {
					$new_row['use_count'] = 0;
					$new_row['qty'] = $product_qty;
					$new_row['unit_price'] = $to_use['special_price'];
					$new_row['unit_price_us'] = $to_use['special_priceus'];
					$new_row['sale_price'] = $product_qty * $to_use['special_price'];
					$new_row['sale_price_us'] = $product_qty * $to_use['special_priceus'];
					$bulk_prices[] = $new_row;
				}
			}
		}
		//Finally the original RRP.
		$bulk_prices[] = array('use_count' => 0, 'qty' => 1, 'unit_price' => $price, 'unit_price_us' => $price_us, 'sale_price' => $price, 'sale_price_us' => $price_us);
		//CURRENCY ADJUSTMENT IS HERE
		foreach($bulk_prices as $key => $row) {
			$row['sale_price'] = convertCurrency($row['qty'] * $row['unit_price'], $currency, '', true);
			$row['sale_price_us'] = convertCurrency($row['qty'] * $row['unit_price_us'], 'USD', '', true);
		}
		//print_array($bulk_prices);
		$unit_prices = array();
		$unit_prices_us = array();
		foreach($bulk_prices as $key => $row) {
			$unit_prices[$key] = floatval($row['unit_price']);
			$unit_prices_us[$key] = floatval($row['unit_price_us']);
		}
		array_multisort($unit_prices, SORT_ASC, $unit_prices_us, SORT_ASC, $bulk_prices);
		$bulk_qty = $product_qty;
		$product_offer = 0.0;
		$product_offer_us = 0.0;
		foreach($bulk_prices as $key => $row) {
			$fit = floor($bulk_qty / $row['qty']);
			$bulk_prices[$key]['use_count'] = $fit;
			$bulk_qty-= $fit * $row['qty'];
			$product_offer+= $fit * $row['sale_price'];
			$product_offer_us+= $fit * $row['sale_price_us'];
		}
		if ($product_qty > 1) {
			$product_offer/= $product_qty;
			$product_offer_us/= $product_qty;
		}
		if ($spec_price != '' && $spec_priceus != '') {
			$orig_price = $spec_price;
			$orig_priceus = $spec_priceus;
		}
		if ($member_discount > 0) {
			$new_offer = convertCurrency($orig_price * (1 - $member_discount / 100), $currency, '', true);
			$new_offerus = convertCurrency($orig_priceus * (1 - $member_discount / 100), $currency, '', true);
			if ($new_offer < $product_offer) {
				$product_offer = $new_offer;
				$product_offer_us = $new_offerus;
			}
		}
	} else {
		$cost_price = convertCurrency($product['cost_price'], $product['cost_currency'], 'USD', true);
		$cost_price_us = convertCurrency($product['cost_price'], $product['cost_currency'], 'USD', true);
		$offer = $sales_price = convertCurrency($product['sales_price'], $product['sales_currency'], 'USD', true);
		$offer_us = $sales_price_us = convertCurrency($product['sales_price'], $product['sales_currency'], 'USD', true);
		$spec_offer = $spec_offer_us = 0;
		if ($member_id > 0) {
			if (count($specials) > 0) {
				$to_use = array();
				foreach($specials as $row => $special) if ($product_qty >= $special['min_order']) $to_use = $special;
				if (count($to_use)) {
					$spec_offer = convertCurrency($to_use['special_price'], 'USD', $cur, true);
					$spec_offer_us = convertCurrency($to_use['special_priceus'], 'USD', '', true);;
				}
			}
		}
		if ($member_discount > 0) {
			$new_offer = convertCurrency($sales_price * (1 - $member_discount / 100), $cur, '', true);
			$new_offerus = convertCurrency($sales_price_us * (1 - $member_discount / 100), $cur, '', true);
			if ($new_offer < $sales_price) {
				$offer = $new_offer;
				$offer_us = $new_offerus;
			}
		}
		$product_offer = ($spec_offer < $offer && $spec_offer > 0) ? $spec_offer : $offer;
		$product_offer_us = ($spec_offer_us < $offer_us && $spec_offer_us > 0) ? $spec_offer_us : $offer_us;
	}
	$temp = array('product_offer' => $product_offer, 'product_offer_us' => $product_offer_us, 'cost_price' => $cost_price, 'cost_price_us' => $cost_price_us);
	return $temp;
}
?>
<?php
/**
 * Returns:
 *  $invoice
 *  $invoice_charges
 *  $invoice_courier_quotes
 *  $invoice_products
 */

moduleLoad("invoice");

if (!function_exists("bcceil"))
{
	function bcceil($number)
	{
		$diff = bcsub($number, bcadd($number, "0.0", 0), 4);
		if (bccomp($diff, "0.0", 4) != 0) return bcadd($number, bcsub("1.0", $diff, 4), 0);
		return bcadd($number, "0.0", 0);
	}
}

$timezone_by_postcode = array(
	"0" => 8,	//Darwin
	"2" => 1,	//Sydney
	"3" => 2,	//Melbourne
	"4" => 6,	//Brisbane
	"5" => 7,	//Adelaide
	"6" => 9,	//Perth
	"7" => 2,	//Hobart
	"9" => 6,	//Brisbane
);
$currency_keys = array("", "au", "us");
$price_keys = array("cost", "offer", "online");
$section_keys = array("products", "ship", "charges");

$invoice = array(
	"ship_mass" => "0.000",

	"access_date" => substr(formatSQLDateTime($_SERVER["REQUEST_TIME"]), 1, -1),
	"handler_id" => 2,
	"he_id" => ($_GLOBALS["site_settings"]["dom_int"] == "dom") ? "dom" : "int",
	"invoice_currency" => $posted['inquiry']['currency'], //$_GLOBALS["site_options"]["DEFAULT_CURRENCY"],
	"newsletter_id" => $_GLOBALS["newsletter"]["id"],
	"open_date" => substr(formatSQLDateTime($_SERVER["REQUEST_TIME"]), 1, -1),
	"site_id" => $_GLOBALS["site_settings"]["site_id"],
	"write_date" => substr(formatSQLDateTime($_SERVER["REQUEST_TIME"]), 1, -1),

	"backorder_flag" => "n",
	"customer_requests" => "",
	"frequency_id" => 0,
	"invoice_action" => "payment",
	"invoice_comments" => "",
	"invoice_status" => "sales",
	"own_freight_option" => "courier",
	"pack_requirement_id" => 0,
	"payment_terms_id" => 0,
	"payments_locate_date" => null,
	"payments_staff_id" => 0,
	"paymenttype_id" => "",
	"plug_id" => 0,
	"products_locate_date" => substr(formatSQLDateTime($_SERVER["REQUEST_TIME"]), 1, -1), //null,
	"products_staff_id" => 0,
	"proforma_date" => substr(formatSQLDateTime($_SERVER["REQUEST_TIME"]), 1, -1),
	"discount_code" => "",
	"proforma_purchaseorder" => "",
	"rand_num" => mt_rand(0, 65535),
	"ship_account_num" => "",
	"ship_cubic" => "0.000",
	"ship_express" => "n",
	"ship_id" => 0,
	"ship_insurance" => "n",
	"ship_locate_date" => null,
	"ship_product_name" => "",
	"ship_product_notes" => "",
	"ship_receipted" => "n",
	"ship_registered" => "n",
	"ship_staff_id" => 0,
	"ship_supplier_id" => 0,
	"ship_supplier_name" => "",
	"ship_tailgate" => "y",
	"ship_weight" => "0.000",
	"shipping_method" => "",
	"supply_country" => $_GLOBALS["site_settings"]["supply_country"],
	"supply_locate_date" => null,
	"supply_notes" => "",
	"terms_id" => 0,
	"voltage_id" => 0,
	"waranty_terms_id" => 0,
	"forwarder_account" => "",
	"forwarder_address" => "",
	"forwarder_city" => "",
	"forwarder_company_name" => "",
	"forwarder_contact" => "",
	"forwarder_country" => "",
	"forwarder_email" => "",
	"forwarder_fax_no" => "",
	"forwarder_mobile_no" => "",
	"forwarder_phone_no" => "",
	"forwarder_post_code" => "",
	"forwarder_state" => "",
	"def_condition" => 0,
	"def_contacts" => "",
	"def_country" => "",
	"def_currency" => "",
	"def_packing" => 0,
	"def_plug" => 0,
	"def_terms" => 0,
	"def_voltage" => 0,
	"def_warranty" => 0,
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
	"products_cost" => "0.00",
	"products_costau" => "0.00",
	"products_costus" => "0.00",
	"products_offer" => "0.00",
	"products_offerau" => "0.00",
	"products_offerus" => "0.00",
	"products_online" => "0.00",
	"products_onlineau" => "0.00",
	"products_onlineus" => "0.00",
	"ship_cost" => "0.00",
	"ship_costau" => "0.00",
	"ship_costus" => "0.00",
	"ship_offer" => "0.00",
	"ship_offerau" => "0.00",
	"ship_offerus" => "0.00",
	"ship_online" => "0.00",
	"ship_onlineau" => "0.00",
	"ship_onlineus" => "0.00",
	"charges_cost" => "0.00",
	"charges_costau" => "0.00",
	"charges_costus" => "0.00",
	"charges_offer" => "0.00",
	"charges_offerau" => "0.00",
	"charges_offerus" => "0.00",
	"charges_online" => "0.00",
	"charges_onlineau" => "0.00",
	"charges_onlineus" => "0.00",
	"invoice_cost" => "0.00",
	"invoice_costau" => "0.00",
	"invoice_costus" => "0.00",
	"invoice_offer" => "0.00",
	"invoice_offerau" => "0.00",
	"invoice_offerus" => "0.00",
	"invoice_online" => "0.00",
	"invoice_onlineau" => "0.00",
	"invoice_onlineus" => "0.00",
	"invoice_profit" => "0.00",
	"invoice_profitau" => "0.00",
	"invoice_profitus" => "0.00",
);

/**
 * Set visitor related data
 */
if (array_key_exists("visitor", $_GLOBALS) && is_array($_GLOBALS["visitor"]))
{
	foreach(array("visitor_id" => "id", "bill_address" => "address", "bill_city" => "city", "bill_company" => "company_name", "bill_country" => "country", "bill_email" => "email_address", "bill_phone" => "phone_no", "bill_postcode" => "post_code", "bill_state" => "state", /*"invoice_currency" => "currency",*/ "notify_address" => "address", "notify_city" => "city", "notify_company" => "company_name", "notify_country" => "country", "notify_email" => "email_address", "notify_phone" => "phone_no", "notify_postcode" => "post_code", "notify_state" => "state", "ship_address" => "address", "ship_city" => "city", "ship_company" => "company_name", "discount_code" => "discount_code", "ship_country" => "country", "ship_email" => "email_address", "ship_phone" => "phone_no", "ship_postcode" => "post_code", "ship_state" => "state", "timezone" => "timezone", "vat_number" => "vat_number") as $k => $v) if (array_key_exists($v, $_GLOBALS["visitor"])) $invoice[$k] = $_GLOBALS["visitor"][$v];
	$invoice["bill_timezone"] = ($_GLOBALS["visitor"]["country"] == "AU" && preg_match("/^[02-79][0-9]{3}$/", $_GLOBALS["visitor"]["post_code"])) ? $timezone_by_postcode[substr($_GLOBALS["visitor"]["post_code"], 0, 1)] : 0;
	@$invoice["bill_name"] = trim($_GLOBALS["visitor"]["first_name"] . " " . $_GLOBALS["visitor"]["last_name"]);
	@$invoice["notify_name"] = trim($_GLOBALS["visitor"]["first_name"] . " " . $_GLOBALS["visitor"]["last_name"]);
	@$invoice["ship_name"] = trim($_GLOBALS["visitor"]["first_name"] . " " . $_GLOBALS["visitor"]["last_name"]);

	if ($row = $DB->query_first("SELECT `voltage`, `plugs` FROM `world`.`country` WHERE `code` = " . formatSQLString($invoice["ship_country"]) . " LIMIT 1", MYSQL_ASSOC))
	{
		$temp = intval($row["voltage"]);
		if ($temp == 240) $invoice["voltage_id"] = 8;
		elseif ($temp >= 110 && $temp <= 127) $invoice["voltage_id"] = 1;
		elseif ($temp >= 220 && $temp <= 240) $invoice["voltage_id"] = 5;
		elseif ($temp >= 110 && $temp <= 240) $invoice["voltage_id"] = 7;
		$invoice["plug_id"] = 0;
		foreach(explode(",", $row["plugs"]) as $v) if (preg_match("/#([0-9]+)\$/", $v, $matches))
		{
			$invoice["plug_id"] = intval($matches[1]);
			break;
		}
	}
}

/**
 * Set member related data
 */
if (array_key_exists("member", $_GLOBALS) && is_array($_GLOBALS["member"]))
{
	foreach(array("bill_address" => "address", "bill_city" => "city", "bill_company" => "company_name", "bill_country" => "country", "bill_email" => "email_address", "bill_phone" => "phone_no", "bill_postcode" => "post_code", "bill_state" => "state", /*"invoice_currency" => "currency",*/ "member_id" => "id", "notify_address" => "address", "notify_city" => "city", "notify_company" => "company_name", "notify_country" => "country", "notify_email" => "email_address", "notify_phone" => "phone_no", "notify_postcode" => "post_code", "notify_state" => "state", "ship_address" => "address", "ship_city" => "city", "ship_company" => "company_name", "discount_code" => "discount_code", "ship_country" => "country", "ship_email" => "email_address", "ship_phone" => "phone_no", "ship_postcode" => "post_code", "ship_state" => "state", "timezone" => "timezone", "vat_number" => "vat_number") as $k => $v) if (array_key_exists($v, $_GLOBALS["member"])) $invoice[$k] = $_GLOBALS["member"][$v];
	$invoice["bill_timezone"] = ($_GLOBALS["member"]["country"] == "AU" && preg_match("/^[02-79][0-9]{3}$/", $_GLOBALS["member"]["post_code"])) ? $timezone_by_postcode[substr($_GLOBALS["member"]["post_code"], 0, 1)] : 0;
	@$invoice["bill_name"] = trim($_GLOBALS["member"]["first_name"] . " " . $_GLOBALS["member"]["last_name"]);
	@$invoice["notify_name"] = trim($_GLOBALS["member"]["first_name"] . " " . $_GLOBALS["member"]["last_name"]);
	@$invoice["ship_name"] = trim($_GLOBALS["member"]["first_name"] . " " . $_GLOBALS["member"]["last_name"]);

	/**
	 * Set the default shipping address
	 */
	if (array_key_exists("ship_addr", $_GLOBALS["member"]) && is_array($_GLOBALS["member"]["ship_addr"])) foreach($_GLOBALS["member"]["ship_addr"] as $ship_addr)
	{
		foreach(array("ship_address" => "address", "ship_city" => "city", "ship_company" => "company_name", "ship_country" => "country", "ship_email" => "email", "ship_name" => "contact", "ship_needs" => "instructions", "ship_phone" => "phone_no", "ship_postcode" => "post_code", "ship_state" => "state",) as $k => $v) $invoice[$k] = $ship_addr[$v];

		if ($ship_addr["default"] == "y") break;
	}

	if ($row = $DB->query_first("SELECT `voltage`, `plugs` FROM `world`.`country` WHERE `code` = " . formatSQLString($invoice["bill_country"]) . " LIMIT 1", MYSQL_ASSOC))
	{
		$temp = intval($row["voltage"]);
		if ($temp == 240) $invoice["voltage_id"] = 8;
		elseif ($temp >= 110 && $temp <= 127) $invoice["voltage_id"] = 1;
		elseif ($temp >= 220 && $temp <= 240) $invoice["voltage_id"] = 5;
		elseif ($temp >= 110 && $temp <= 240) $invoice["voltage_id"] = 7;
		$invoice["plug_id"] = 0;
		foreach(explode(",", $row["plugs"]) as $v) if (preg_match("/#([0-9]+)\$/", $v, $matches))
		{
			$invoice["plug_id"] = intval($matches[1]);
			break;
		}
	}
	if ($row = $DB->query_first("SELECT `voltage`, `plugs` FROM `world`.`country` WHERE `code` = " . formatSQLString($invoice["ship_country"]) . " LIMIT 1", MYSQL_ASSOC))
	{
		$temp = intval($row["voltage"]);
		if ($temp == 240) $invoice["voltage_id"] = 8;
		elseif ($temp >= 110 && $temp <= 127) $invoice["voltage_id"] = 1;
		elseif ($temp >= 220 && $temp <= 240) $invoice["voltage_id"] = 5;
		elseif ($temp >= 110 && $temp <= 240) $invoice["voltage_id"] = 7;
		$invoice["plug_id"] = 0;
		foreach(explode(",", $row["plugs"]) as $v) if (preg_match("/#([0-9]+)\$/", $v, $matches))
		{
			$invoice["plug_id"] = intval($matches[1]);
			break;
		}
	}
}

/**
 * Try to match world timezones
 */
if ($row = $DB->query_first("SELECT `id` FROM `world`.`time_zones` WHERE `country` = " . formatSQLString($invoice["bill_country"]) . " ORDER BY IF(`name` = " . formatSQLString($invoice["bill_state"]) . ", 2, IF(`name` = " . formatSQLString($invoice["bill_city"]) . ", 1, 0)) DESC LIMIT 1")) $invoice["bill_timezone"] = $row["id"];

$invoice["invoice_exchange"] = convertCurrency("1.0000", $invoice["invoice_currency"], "AUD", false, 4);
$invoice["invoice_exchangeus"] = convertCurrency("1.0000", $invoice["invoice_currency"], "USD", false, 4);

$count = 0;
$invoice_products = array();

/**
 * Override with contact form
 */
if (array_key_exists("inquiry", $posted) && is_array($posted["inquiry"]))
{
	$inquiry = $posted["inquiry"];

	if (array_key_exists("name", $inquiry))
	{
		$invoice["bill_name"] = $inquiry["name"];
		$invoice["ship_name"] = $inquiry["name"];
	}
	if (array_key_exists("email", $inquiry))
	{
		$invoice["bill_email"] = $inquiry["email"];
		$invoice["ship_email"] = $inquiry["email"];
	}
	if (array_key_exists("country", $inquiry))
	{
		$invoice["bill_country"] = $inquiry["country"];
		$invoice["ship_country"] = $inquiry["country"];
	}
	if (array_key_exists("city", $inquiry))
	{
		$invoice["bill_city"] = $inquiry["city"];
		$invoice["ship_city"] = $inquiry["city"];
	}
	if (array_key_exists("post_code", $inquiry))
	{
		$invoice["bill_postcode"] = $inquiry["post_code"];
		$invoice["ship_postcode"] = $inquiry["post_code"];
	}
	if (array_key_exists("site_id", $inquiry))
	{
		$invoice["site_id"] = $inquiry["site_id"];
	}
	if (array_key_exists("message", $inquiry)) $invoice["customer_requests"] = $inquiry["message"];
	if (/*array_key_exists("message", $inquiry) ||*/ array_key_exists("where_purchased", $inquiry) || array_key_exists("invoice_id", $inquiry) || array_key_exists("serial_number", $inquiry))
	{
		$invoice_product = array(
			"site_id" => $invoice["site_id"],
			"product_rank" => $count++,
			"product_name" => trim(trim($inquiry["where_purchased"] . " " . $inquiry["invoice_id"]) . " " . $inquiry["serial_number"]),
			"product_notes" => $inquiry["message"],
			"generic" => "y",
			"product_qty" => 1
		);
		foreach($price_keys as $k2)
		{
			$invoice_product["product_" . $k2 . "au"] = convertCurrency($invoice_product["product_" . $k2], $invoice["invoice_currency"], "AUD");
			$invoice_product["product_" . $k2 . "us"] = convertCurrency($invoice_product["product_" . $k2], $invoice["invoice_currency"], "USD");
		}
		foreach($currency_keys as $k1) foreach($price_keys as $k2) $invoice["products_" . $k2 . $k1] = bcadd($invoice["products_" . $k2 . $k1], bcmul($invoice_product["product_qty"], $invoice_product["product_" . $k2 . $k1], 4), 4);
		$invoice_products[] = $invoice_product;
	}

	/**
	 * Set as an inquiry
	 */
	$invoice["invoice_status"] = "inquiry";
	$invoice["invoice_action"] = "working";

	if (/*array_key_exists("message", $inquiry) ||*/ array_key_exists("subject", $inquiry))
	{
		$invoice_product = array(
			"product_id" => 0,
			"sub_id" => 0,
			"site_id" => $invoice["site_id"],
			"product_rank" => $count++,
			"product_name" => trim($inquiry["subject"] . " " . $inquiry["subject2"]),
			"product_notes" => $inquiry["message"],
			"supplier_id" => 0,
			"product_offer" => "0.0000",
			"product_online" => "0.0000",
			"product_qty" => 1,
			"product_cost" => "0.0000",
			"supplier_country" => "",
			"supplier_currency" => "",
			"generic" => "y",
			"product_warranty" => 0,
			"product_voltage" => 0,
			"product_condition" => 0,
			"product_packing" => 0,
			"product_terms" => 0,
			"product_plug" => 0,
		);

		if ($inquiry["product_id"] != 0 && function_exists("productFromDatabase"))
		{
			$product_str = productStrJoin(intval($inquiry["product_id"]), intval($inquiry["sub_id"]));
			$product = productFromDatabase($product_str);
			if (function_exists("productAlternatesFromDatabase")) foreach(productAlternatesFromDatabase($product_str) as $v3) if ($v3["site_id"] == $invoice["site_id"]) foreach($v3 as $k2 => $v2) if ($k2 != "id" && $v2 != "") $product[$k2] = $v2;

			$product["normal_price"] = convertCurrency($product["primary_price"], $product["primary_currency"], $invoice["invoice_currency"], false, $product["precision"]);
			$product["normal_currency"] = $invoice["invoice_currency"];
			$product["effective_price"] = convertCurrency($product["secondary_price"], $product["secondary_currency"], $invoice["invoice_currency"], false, $product["precision"]);
			$product["effective_currency"] = $invoice["invoice_currency"];
			$product["effective_savings"] = (bccomp($product["normal_price"], $product["effective_price"], $product["precision"]) > 0) ? bcdiv(bcmul(bcsub($product["normal_price"], $product["effective_price"], $product["precision"]), "100.00", 2), $product["normal_price"], 0) : "0";

			/*$invoice_product = array(
				"prod_count" => $count,
				"product_id" => $product["id"],
				"sub_id" => $product["sub_id"],
				"site_id" => $invoice["site_id"],
				"product_rank" => $count++,
				"product_name" => $inquiry["subject"],
				"product_offer" => $inquiry['price'],
				"product_online" => $inquiry['price']
				"product_cost" => $inquiry['price'],
				"supplier_currency" => $inquiry['currency'],
				"product_weight" => $product["weight"],
				"product_weight_inc" => $product["weight_inc"],
				"product_warranty" => $product["warranty"],
				"product_voltage" => ($product["power_110"] == $product["power_240"] && $product["power_110"] != 0) ? 7 : 0,
				"product_condition" => $product["condition"],
				"decimal" => $product["precision"],
				"product_section" => $product["section"],
				"promotion_id" => $product["promotion_id"],
			);*/

			$invoice_product = array(
				"prod_count" => $count,
				"product_id" => $product["id"],
				"sub_id" => $product["sub_id"],
				"site_id" => $invoice["site_id"],
				"product_rank" => $count++,
				"product_name" => $inquiry['subject'],
				"product_offer" => convertCurrency($inquiry["price"], $inquiry['currency'], $invoice["invoice_currency"], false, $product["precision"]),
				"product_online" => convertCurrency($inquiry["price"], $inquiry['currency'], $invoice["invoice_currency"], false, $product["precision"]),
				"product_cost" => convertCurrency($inquiry["price"], $inquiry['currency'], $invoice["invoice_currency"], false, $product["precision"]),
				"supplier_currency" => $inquiry['currency'],
				"product_weight" => $product["weight"],
				"product_weight_inc" => $product["weight_inc"],
				"product_warranty" => $product["warranty"],
				"product_voltage" => ($product["power_110"] == $product["power_240"] && $product["power_110"] != 0) ? 7 : 0,
				"product_condition" => $product["condition"],
				"decimal" => $product["precision"],
				"product_section" => $product["section"],
				"promotion_id" => $product["promotion_id"],
			);

			$notes = array($inquiry["message"]);

			$last_note = "";

			//Method 2: By cheapest price
			if (function_exists("productBulkPricingFromDatabase"))
			{
				foreach(productBulkPricingFromDatabase($invoice_product["product_id"], $invoice_product["sub_id"]) as $special) if ($special["qty"] <= $invoice_product["product_qty"])
				{
					$new_price = convertCurrency($special["unit_sale_price_us"], "USD", $invoice["invoice_currency"], false, $invoice_product["decimal"]);
					if (array_key_exists("unit_sale_price", $special))
					{
						$new_price = convertCurrency((strtoupper($product["selected"]) == "AUD") ? $special["unit_sale_price"] : $special["unit_sale_price_us"], (strtoupper($product["selected"]) == "AUD") ? "AUD" : "USD", $invoice["invoice_currency"], false, $invoice_product["decimal"]);
					}
					if (bccomp($new_price, $invoice_product["product_offer"], $invoice_product["decimal"]) < 0)
					{
						$invoice_product["product_offer"] = $new_price;
						$savings = bcsub($invoice_product["product_online"], $invoice_product["product_offer"], $invoice_product["decimal"]);
						$last_note = sprintf(
							"Bulk Discount: %d%% (RRP: %s%s %s, save %s%s %s)",
							bcdiv(bcmul($savings, "100.0", 0), $invoice_product["product_online"], 0),
							getCurrencySymbol($invoice["invoice_currency"], "UTF8"),
							$invoice_product["product_online"],
							$invoice["invoice_currency"],
							getCurrencySymbol($invoice["invoice_currency"], "UTF8"),
							$savings,
							$invoice["invoice_currency"]
						);
					}
				}
			}

			/**
			 * Special Pricing
			 */
			if ($product["on_special"] == "y" && bccomp($invoice_product["product_online"], $invoice_product["product_offer"], 2) > 0)
			{
				$savings = bcsub($invoice_product["product_online"], $invoice_product["product_offer"], $invoice_product["decimal"]);
				$percent = bcceil(bcdiv(bcmul($savings, "100.0", 4), $invoice_product["product_online"], 4));
				$last_note = sprintf("Sale Discount: %s%% (RRP: %s%s %s, save %s%s %s)", $percent, getCurrencySymbol($invoice["invoice_currency"], "UTF8"), $invoice_product["product_online"], $invoice["invoice_currency"], getCurrencySymbol($invoice["invoice_currency"], "UTF8"), $savings, $invoice["invoice_currency"]);
			}

			/**
			 * Member Discount
			 */
			if (function_exists("memberDiscountsFromDatabase") && ($percent = memberDiscountsFromDatabase($_GLOBALS['member']['id'], $invoice["site_id"])) != 0)
			{
				$new_offer = convertCurrency(bcmul($invoice_product["product_offer"], bcmul("0.01", 100 - $percent, 4), 4), $invoice["invoice_currency"], "", false, $invoice_product["decimal"]);
				if (bccomp($invoice_product["product_offer"], $new_offer, 2) > 0)
				{
					$invoice_product["product_offer"] = $new_offer;
					$savings = bcsub($invoice_product["product_online"], $new_offer, $invoice_product["decimal"]);
					$percent = bcceil(bcdiv(bcmul($savings, "100.0", 4), $invoice_product["product_online"], 4));
					$last_note = sprintf("Member Discount: %s%% (RRP: %s%s %s, save %s%s %s)", $percent, getCurrencySymbol($invoice["invoice_currency"], "UTF8"), $invoice_product["product_online"], $invoice["invoice_currency"], getCurrencySymbol($invoice["invoice_currency"], "UTF8"), $savings, $invoice["invoice_currency"]);
				}
			}

			/**
			 * Apply member pricing
			 */
			if (function_exists("memberProductSpecialPrices") && is_array($special_prices = memberProductSpecialPrices($member['member_id'], $invoice_product["product_id"], $invoice['site_id'])))
			{
				foreach($special_prices as $special)
				{
					$new_offer = convertCurrency($special["special_price"], $product["primary_currency"], $invoice["invoice_currency"], false, $invoice_product["decimal"]);
					if ($special["min_order"] <= $invoice_product["product_qty"] && bccomp($invoice_product["product_offer"], $new_offer, 2) > 0)
					{
						$invoice_product["product_offer"] = $new_offer;
						$savings = bcsub($invoice_product["product_online"], $new_offer, $invoice_product["decimal"]);
						$percent = bcceil(bcdiv(bcmul($savings, "100.0", 4), $invoice_product["product_online"], 4));
						$last_note = sprintf("Member Special Price: %s%0.2f %s (RRP: %s%s %s, save %s%%)", getCurrencySymbol($invoice["invoice_currency"], "UTF8"), $new_offer, $invoice["invoice_currency"], getCurrencySymbol($invoice["invoice_currency"], "UTF8"), $invoice_product["product_online"], $invoice["invoice_currency"], $percent);
					}
				}
			}

			$notes[] = $last_note;

			if (count($notes)) $invoice_product["product_notes"] = trim(implode("\n", $notes));
		}

		foreach($price_keys as $k2)
		{
			$invoice_product["product_" . $k2 . "au"] = convertCurrency($invoice_product["product_" . $k2], $invoice["invoice_currency"], "AUD");
			$invoice_product["product_" . $k2 . "us"] = convertCurrency($invoice_product["product_" . $k2], $invoice["invoice_currency"], "USD");
		}
		foreach($currency_keys as $k1) foreach($price_keys as $k2) $invoice["products_" . $k2 . $k1] = bcadd($invoice["products_" . $k2 . $k1], bcmul($invoice_product["product_qty"], $invoice_product["product_" . $k2 . $k1], 4), 4);
		$invoice_products[] = $invoice_product;
	}

}

/**
 * Set the default handler
 */
if ($row = $DB->query_first("SELECT `staff_id` FROM `highwaygroup`.`site_default_handler` WHERE `site_id` = " . intval($invoice["site_id"]) . " LIMIT 1", MYSQL_ASSOC)) $invoice["handler_id"] = $row["staff_id"];

$count = 0;
$invoice_courier_quotes = array();

$count = 0;
$invoice_charges = array();

/**
 * Add GST
 */
if ($invoice["supply_country"] == "AU" && $invoice["bill_country"] == "AU" && $invoice["ship_country"] == "AU")
{
	$affected = bcadd($invoice["products_offer"], bcadd($invoice["ship_offer"], $invoice["charges_offer"], 2), 2);
	$percent = "0.1";

	$invoice_charge = array(
		"charge_count" => $count,
		"charge_rank" => $count++,
		"charge_id" => 1,
		"charge_name" => "GST (10%)",
		"charge_qty" => 1,
		"charge_notes" => "",
		"charge_offer" => convertCurrency(bcmul($percent, $affected, 2), $invoice["invoice_currency"], "", true, 2),
		"charge_cost" => convertCurrency(bcmul($percent, $affected, 2), $invoice["invoice_currency"], "", true, 2),
		"charge_online" => convertCurrency(bcmul($percent, $affected, 2), $invoice["invoice_currency"], "", true, 2),
	);
	foreach($price_keys as $k2)
	{
		$invoice_charge["charge_" . $k2 . "au"] = convertCurrency($invoice_charge["charge_" . $k2], $invoice["invoice_currency"], "AUD");
		$invoice_charge["charge_" . $k2 . "us"] = convertCurrency($invoice_charge["charge_" . $k2], $invoice["invoice_currency"], "USD");
	}
	foreach($currency_keys as $k1) foreach($price_keys as $k2) $invoice["charges_" . $k2 . $k1] = bcadd($invoice["charges_" . $k2 . $k1], bcmul($invoice_charge["charge_qty"], $invoice_charge["charge_" . $k2 . $k1], 4), 4);
	$invoice_charges[] = $invoice_charge;
}

/**
 * Add up and round all totals
 */
foreach($currency_keys as $k1)
{
	foreach($price_keys as $k2)
	{
		foreach($section_keys as $k3) $invoice[$k3 . "_" . $k2 . $k1] = bcadd($invoice[$k3 . "_" . $k2 . $k1], "0.00", 2);
		$invoice["invoice_" . $k2 . $k1] = bcadd($invoice["products_" . $k2 . $k1], bcadd($invoice["ship_" . $k2 . $k1], $invoice["charges_" . $k2 . $k1], 2), 2);
	}
	$invoice["invoice_profit" . $k1] = bcsub($invoice["invoice_offer" . $k1], $invoice["invoice_cost" . $k1], 2);
}

/**
 * Set the extended invoice values
 */
$country_list = invoiceGetOptionList("bill_country");
$invoice["bill_address_full"] = trim(preg_replace("/\\n\\n+/", "\n\n", implode("\n", array(
	$invoice["bill_company"],
	(($invoice["bill_company"] != "") ? "Attn: " : "") . $invoice["bill_name"],
	$invoice["bill_address"],
	$invoice["bill_city"] . (($invoice["bill_state"] != "") ? (", " . $invoice["bill_state"]) : "") . " " . $invoice["bill_postcode"],
	$country_list[$invoice["bill_country"]],
	"",
	($invoice["bill_phone"] != "") ? ("Ph: " . $invoice["bill_phone"]) : "",
	($invoice["bill_email"] != "") ? ("Email: " . $invoice["bill_email"]) : "",
))));

$country_list = invoiceGetOptionList("ship_country");
$invoice["ship_address_full"] = trim(preg_replace("/ *\\n( *\\n)+/", "\n\n", implode("\n", array(
	$invoice["ship_company"],
	(($invoice["ship_company"] != "") ? "Attn: " : "") . $invoice["ship_name"],
	$invoice["ship_address"],
	$invoice["ship_city"] . (($invoice["ship_state"] != "") ? (", " . $invoice["ship_state"]) : "") . " " . $invoice["ship_postcode"],
	$country_list[$invoice["ship_country"]],
	"",
	($invoice["ship_phone"] != "") ? ("Ph: " . $invoice["ship_phone"]) : "",
	($invoice["ship_email"] != "") ? ("Email: " . $invoice["ship_email"]) : "",
))));

$country_list = invoiceGetOptionList("notify_country");
$invoice["notify_address_full"] = trim(preg_replace("/ *\\n( *\\n)+/", "\n\n", implode("\n", array(
	$invoice["notify_company"],
	(($invoice["notify_company"] != "") ? "Attn: " : "") . $invoice["notify_name"],
	$invoice["notify_address"],
	$invoice["notify_city"] . (($invoice["notify_state"] != "") ? (", " . $invoice["notify_state"]) : "") . " " . $invoice["notify_postcode"],
	$country_list[$invoice["notify_country"]],
	"",
	($invoice["notify_phone"] != "") ? ("Ph: " . $invoice["notify_phone"]) : "",
	($invoice["notify_email"] != "") ? ("Email: " . $invoice["notify_email"]) : "",
))));

/**
 * Fill in the invoice defaults
 */
foreach(array("product_warranty" => "def_warranty", "product_voltage" => "def_voltage", "product_plug" => "def_plug", "product_condition" => "def_condition", "product_packing" => "def_packing", "product_terms" => "def_terms", "supplier_currency" => "def_currency", "supplier_country" => "def_country", "supplier_id" => "default_supplier") as $k => $v)
{
	$temp = array();
	foreach($invoice_products as $invoice_product) if (array_key_exists($k, $invoice_product) && $invoice_product[$k] != "") $temp[$invoice_product[$k]] = $invoice_product[$k];
	unset($temp[0]);
	if (count(array_unique($temp)) == 1) $invoice[$v] = reset($temp);
}
$invoice["pack_requirement_id"] = $invoice["def_packing"];
$invoice["waranty_terms_id"] = $invoice["def_warranty"];
if ($invoice["def_voltage"] != 0 || $invoice["def_plug"] != 0)
{
	$invoice["voltage_id"] = $invoice["def_voltage"];
	$invoice["plug_id"] = $invoice["def_plug"];
}

$invoice["open_time"] = strtotime($invoice["open_date"]);
$invoice["proforma_time"] = strtotime($invoice["proforma_date"]);
$invoice["tax_time"] = 0;
$invoice["tax_fyear"] = 0;
$invoice["tax_year"] = 0;
$invoice["tax_month"] = 0;

$invoice["products_located"] = (is_null($invoice["products_locate_date"])) ? "n" : "y";
$invoice["ship_located"] = (is_null($invoice["ship_locate_date"])) ? "n" : "y";
$invoice["payments_located"] = (is_null($invoice["payments_locate_date"])) ? "n" : "y";
$invoice["open_year"] = mktime(0, 0, 0, 1, 1, date("Y", $invoice["open_time"]));
$invoice["open_quarter"] = mktime(0, 0, 0, floor((date("n", $invoice["open_time"]) - 1) / 3) * 3 + 1, 1, date("Y", $invoice["open_time"]));
$invoice["open_month"] = mktime(0, 0, 0, date("n", $invoice["open_time"]), 1, date("Y", $invoice["open_time"]));
$invoice["open_week"] = mktime(0, 0, 0, date("n", $invoice["open_time"]), date("j", $invoice["open_time"]) - date("N", $invoice["open_time"]), date("Y", $invoice["open_time"]));
$invoice["open_day"] = mktime(0, 0, 0, date("n", $invoice["open_time"]), date("j", $invoice["open_time"]), date("Y", $invoice["open_time"]));

//Hack: agsales is to sell under Highway Games
if ($_GLOBALS["site_settings"]["site_id"] == 2 && $_GLOBALS["site_settings"]["dom_int"] == "int")
{
	//TODO: Very bad, these are supposed to be constants
	$_GLOBALS["site_settings"]["site_id"] = 1;
	$_GLOBALS["site_settings"]["email_sales"] = "sales@highwaygames.com";
	$invoice["site_id"] = 1;
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
unset($timezone_by_postcode);
unset($warranty_list);
*/
?>
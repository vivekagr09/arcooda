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

if (!function_exists("bcceil"))
{
	function bcceil($number)
	{
		$diff = bcsub($number, bcadd($number, "0.0", 0), 4);
		if (bccomp($diff, "0.0", 4) != 0) return bcadd($number, bcsub("1.0", $diff, 4), 0);
		return bcadd($number, "0.0", 0);
	}
}

if (!isset($DB))
{
	$DB = moduleLoad("DB");
}

//Just in case
bcscale(6);

$payment_methods = array();
$query = $DB->query("SELECT *, `payment_method_id` AS `id` FROM `highwaygroup`.`payment_methods` WHERE `site_id` = " . intval($_GLOBALS["site_settings"]["site_id"]) . " AND `payment_method_public` = 'y' ORDER BY `payment_method_name` ASC");
while ($row = $DB->fetch_assoc($query)) $payment_methods[$row["id"]] = $row;
$DB->free_result($query);

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
$product_cache = array();

$plug_list = range(0, 999);
$voltage_list = array(1 => "110-127V", 5 => "220-240V", 7 => "110-240V", 8 => "240V", 9 => "110/220V");

$availability_list = array();
$condition_list = array();
$location_list = array();
$manufacturer_list = array();
$supplier_list = array();
$warranty_list = array();
if (function_exists("productGetOptionList") || moduleLoad("product"))
{
	$availability_list = productGetOptionList("availability");
	$condition_list = productGetOptionList("condition");
	$location_list = productGetOptionList("location");
	$manufacturer_list = productGetOptionList("manufacturer");
	$supplier_list = productGetOptionList("supplier");
	$warranty_list = productGetOptionList("warranty");
	unset($condition_list[1]);
}

$courier_list = array();
if (function_exists("getFreightServices") || moduleLoad("freight"))
{
	$courier_list = getFreightServices('personal', '', true);
}

$packages = array();
$issues = array();

$invoice = array(
	"ship_mass" => "0.000",

	"access_date" => substr(formatSQLDateTime($_SERVER["REQUEST_TIME"]), 1, -1),
	"handler_id" => 2,
	"he_id" => ($_GLOBALS["site_settings"]["dom_int"] == "dom") ? "dom" : "int",
	"invoice_currency" => $_GLOBALS["site_options"]["DEFAULT_CURRENCY"],
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
	foreach(array("visitor_id" => "id", "bill_address" => "address", "bill_city" => "city", "bill_company" => "company_name", "bill_country" => "country", "bill_email" => "email_address", "bill_phone" => "phone_no", "bill_postcode" => "post_code", "bill_state" => "state", "invoice_currency" => "currency", "notify_address" => "address", "notify_city" => "city", "notify_company" => "company_name", "notify_country" => "country", "notify_email" => "email_address", "notify_phone" => "phone_no", "notify_postcode" => "post_code", "notify_state" => "state", "ship_address" => "address", "ship_city" => "city", "ship_company" => "company_name", "discount_code" => "discount_code", "ship_country" => "country", "ship_email" => "email_address", "ship_phone" => "phone_no", "ship_postcode" => "post_code", "ship_state" => "state", "timezone" => "timezone", "vat_number" => "vat_number") as $k => $v) if (array_key_exists($v, $_GLOBALS["visitor"])) $invoice[$k] = $_GLOBALS["visitor"][$v];
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
	foreach(array("bill_address" => "address", "bill_city" => "city", "bill_company" => "company_name", "bill_country" => "country", "bill_email" => "email_address", "bill_phone" => "phone_no", "bill_postcode" => "post_code", "bill_state" => "state", "invoice_currency" => "currency", "member_id" => "id", "notify_address" => "address", "notify_city" => "city", "notify_company" => "company_name", "notify_country" => "country", "notify_email" => "email_address", "notify_phone" => "phone_no", "notify_postcode" => "post_code", "notify_state" => "state", "ship_address" => "address", "ship_city" => "city", "ship_company" => "company_name", "discount_code" => "discount_code", "ship_country" => "country", "ship_email" => "email_address", "ship_phone" => "phone_no", "ship_postcode" => "post_code", "ship_state" => "state", "timezone" => "timezone", "vat_number" => "vat_number") as $k => $v) if (array_key_exists($v, $_GLOBALS["member"])) $invoice[$k] = $_GLOBALS["member"][$v];
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
 * Validate the discount codes
 */
$invoice_discounts = array();
if (trim($invoice["discount_code"]) != "")
{
	//Check if customer has recent processed orders
	$temp = 0;
	if ($_GLOBALS["member"]["id"] != 0)
	{
		$criteria = $_GLOBALS["criteria_options"]["invoice"];
		$criteria["member_id"] = $_GLOBALS["member"]["id"];
		$criteria["invoice_status"] = "processed";
		$temp = (count(invoiceSearch($criteria, mktime(0, 0, 0, date("n", $_SERVER["REQUEST_TIME"]) - 12))));
	}

	//Matchup all the given discount codes
	$query = $DB->query("SELECT * FROM `highwaygroup`.`gift_coupon` WHERE FIND_IN_SET(`name`, " . formatSQLString(trim($invoice["discount_code"])) . ") AND (`expiry_date` = '0000-00-00 00:00:00' OR `expiry_date` > NOW()) AND `active` = 'y' AND (`site_id` = '' OR `site_id` = " . intval($_GLOBALS["site_settings"]["site_id"]) . ")");
	while($row = $DB->fetch_assoc($query))
	{
		if ($temp != 0 && bccomp($row["bonus"], "0.00", 2) != 0)
		{
			$row["value"] = bcadd($row["value"], $row["bonus"], 2);
			$row["bonus"] = "0.00";
		}
		$row["price_deducted"] = "0.00";
		$invoice_discounts[$row["name"]] = $row;
	}
	$DB->free_result($query);

	//Rebuild the discount code
	$invoice["discount_code"] = implode(",", array_keys($invoice_discounts));
}

/**
 * Set the default handler
 */
if ($row = $DB->query_first("SELECT `staff_id` FROM `highwaygroup`.`site_default_handler` WHERE `site_id` = " . $_GLOBALS["site_settings"]["site_id"] . " LIMIT 1", MYSQL_ASSOC)) $invoice["handler_id"] = $row["staff_id"];

/**
 * Try to match world timezones
 */
if ($row = $DB->query_first("SELECT `id` FROM `world`.`time_zones` WHERE `country` = " . formatSQLString($invoice["bill_country"]) . " ORDER BY IF(`name` = " . formatSQLString($invoice["bill_state"]) . ", 2, IF(`name` = " . formatSQLString($invoice["bill_city"]) . ", 1, 0)) DESC LIMIT 1")) $invoice["bill_timezone"] = $row["id"];

/**
 * Override with checkout choices
 */
if (array_key_exists("checkout", $posted) && is_array($posted["checkout"]))
{
	/**
	 * Override shipping address
	 */
	if (array_key_exists("ship_addr_id", $posted["checkout"]) && is_array($_GLOBALS["member"]["ship_addr"])) foreach($_GLOBALS["member"]["ship_addr"] as $ship_addr) if ($posted["checkout"]["ship_addr_id"] == $ship_addr["id"]) foreach(array("ship_address" => "address", "ship_city" => "city", "ship_company" => "company_name", "ship_country" => "country", "ship_email" => "email", "ship_name" => "contact", "ship_phone" => "phone_no", "ship_postcode" => "post_code", "ship_state" => "state",) as $k => $v) $invoice[$k] = $ship_addr[$v];

	/**
	 * Override shipping instructions
	 */
	if (array_key_exists("ship_needs", $posted["checkout"]) && $posted["checkout"]["ship_needs"] != "" && $posted["checkout"]["ship_needs"] != "Please enter special shipping instructions here") $invoice["ship_needs"] = $posted["checkout"]["ship_needs"];

	/**
	 * Override purchase order number
	 */
	if (array_key_exists("proforma_purchaseorder", $posted["checkout"])) $invoice["proforma_purchaseorder"] = $posted["checkout"]["proforma_purchaseorder"];

	/**
	 * Override currency and payment type
	 */
	if (array_key_exists("paymenttype_id", $posted["checkout"]))
	{
		if (is_array($payment_methods)) foreach($payment_methods as $method) if ($method["payment_method_id"] == $posted["checkout"]["paymenttype_id"])
		{
			$invoice["paymenttype_id"] = $method["payment_method_id"];
			$accepted = explode(",", $method["accepted_currencies"]);
			if (count($accepted) > 0 && !in_array($invoice["invoice_currency"], $accepted)) $invoice["invoice_currency"] = $accepted[0];

			if ($posted["checkout"]["payment_currency"][$invoice["paymenttype_id"]] != "" && in_array($posted["checkout"]["payment_currency"][$invoice["paymenttype_id"]], $accepted)) $invoice["invoice_currency"] = $posted["checkout"]["payment_currency"][$invoice["paymenttype_id"]];

			break;
		}
	}

	/**
	 * Override default contacts
	 */
	$temp = array();
	if (is_array($_GLOBALS["member"]["contacts"]) && is_array($posted["checkout"]["notify"])) foreach($_GLOBALS["member"]["contacts"] as $contact) if (in_array($contact["id"], $posted["checkout"]["notify"])) $temp[] = $contact["id"];
	$invoice["def_contacts"] = implode(",", $temp);
}

/**
 * Set the default cubic divisor
 */
$cubic_divisor = "5000";
if ($invoice["supply_country"] != $invoice["ship_country"]) $cubic_divisor = "4000";

$invoice["invoice_exchange"] = convertCurrency("1.0000", $invoice["invoice_currency"], "AUD", false, 4);
$invoice["invoice_exchangeus"] = convertCurrency("1.0000", $invoice["invoice_currency"], "USD", false, 4);

$count = 0;
$invoice_products = array();

$shipping_packages = array();

/**
 * Set the shopping cart products
 */
if (is_array($_GLOBALS["shopping_cart"]))
{
	/**
	 * Hack for old shopping_cart
	 */
	foreach($_GLOBALS["shopping_cart"] as $k => $v) if (!is_array($v)) $_GLOBALS["shopping_cart"][$k] = array(
		"product_id" => $k,
		"sub_id" => 0,
		"product_qty" => $v,
		"is_gift" => "n",
		"generic" => "n",
		"site_id" => $_GLOBALS["site_settings"]["site_id"],
		"power_id" => 0,
		"adapter_id" => 0,
		"used" => "n",
		"dependant_id" => 0,
		"dependant_sub_id" => 0,
		"generic_code" => "",
	);

	/**
	 * Gift dependancy check - First build a list of possible gifts
	 * Note: needs rewrite, there is currently no check for correct dependant
	 */
	$base_cart = array();
	$max_gifts = array();
	foreach($_GLOBALS["shopping_cart"] as $k => $v) if ($v["is_gift"] != "y" && $v["generic"] == "n")
	{
		$criteria = $_GLOBALS["criteria_options"]["products"];
		$criteria["product_id"] = $v["product_id"];
		$criteria["sub_id"] = $v["sub_id"];
		if (count(productSearch($criteria)) > 0)
		{
			$product_str = productStrJoin($v["product_id"], $v["sub_id"]);
			if (array_key_exists($product_str, $base_cart)) $base_cart[$product_str] += $v["product_qty"];
			else $base_cart[$product_str] = $v["product_qty"];
			if (array_key_exists($product_str, $product_cache)) $product = $product_cache[$product_str];
			else
			{
				$product = productFromDatabase($product_str);
				if (function_exists("productAlternatesFromDatabase")) foreach(productAlternatesFromDatabase($product_str) as $v3) if ($v3["site_id"] == $_GLOBALS["site_settings"]["site_id"])
				{
					if ($v3["image_file"] != "") unset($product["thumb_file"]);
					foreach($v3 as $k2 => $v2) if ($k2 != "id" && $v2 != "") $product[$k2] = $v2;
				}
				$product_cache[$product_str] = $product;
			}
			if ($product["promotion"] == "y" && in_array("gift", explode(",", $product["promotion_type"]))) foreach(productGiftsFromDatabase($v["product_id"], $v["sub_id"]) as $v2)
			{
				$product_str = productStrJoin($v2["linked_id"], $v2["linked_sub_id"]);
				if (array_key_exists($product_str, $max_gifts)) $max_gifts[$product_str] += $v["product_qty"] * $v2["qty"];
				else $max_gifts[$product_str] = $v["product_qty"] * $v2["qty"];
			}
		}
		else unset($_GLOBALS["shopping_cart"][$k]);
	}

	/**
	 * Gift dependancy check - Remove any gifts that aren't accountable
	 */
	foreach($_GLOBALS["shopping_cart"] as $k => $v) if ($v["is_gift"] == "y")
	{
		$product_str = productStrJoin($v["product_id"], $v["sub_id"]);
		if (!array_key_exists($product_str, $max_gifts)) unset($_GLOBALS["shopping_cart"][$k]);
		elseif ($max_gifts[$product_str] > 0)
		{
			if ($max_gifts[$product_str] >= $v["product_qty"]) $max_gifts[$product_str] -= $v["product_qty"];
			else
			{
				$_GLOBALS["shopping_cart"][$k]["product_qty"] = $max_gifts[$product_str];
				$max_gifts[$product_str] = 0;
			}
		}
		else unset($_GLOBALS["shopping_cart"][$k]);
	}

	foreach($_GLOBALS["shopping_cart"] as $cart_key => $v)
	{
		if (!array_key_exists("site_id", $v)) $v["site_id"] = intval($_GLOBALS["site_settings"]["site_id"]);
		switch($v["generic"])
		{
		default:
		case "n":
			$product_str = productStrJoin(intval($v["product_id"]), intval($v["sub_id"]));
			if (array_key_exists($product_str, $product_cache)) $product = $product_cache[$product_str];
			else
			{
				$product = productFromDatabase($product_str);
				if (function_exists("productAlternatesFromDatabase")) foreach(productAlternatesFromDatabase($product_str) as $v3) if ($v3["site_id"] == $_GLOBALS["site_settings"]["site_id"])
				{
					if ($v3["image_file"] != "") unset($product["thumb_file"]);
					foreach($v3 as $k2 => $v2) if ($k2 != "id" && $v2 != "") $product[$k2] = $v2;
				}
				$product_cache[$product_str] = $product;
			}

			$product["normal_price"] = convertCurrency($product["primary_price"], $product["primary_currency"], $invoice["invoice_currency"], false, $product["precision"]);
			$product["normal_currency"] = $invoice["invoice_currency"];
			$product["effective_price"] = convertCurrency($product["secondary_price"], $product["secondary_currency"], $invoice["invoice_currency"], false, $product["precision"]);
			$product["effective_currency"] = $invoice["invoice_currency"];
			$product["effective_savings"] = (bccomp($product["normal_price"], $product["effective_price"], $product["precision"]) > 0) ? bcdiv(bcmul(bcsub($product["normal_price"], $product["effective_price"], $product["precision"]), "100.00", 2), $product["normal_price"], 0) : "0";

			$dimensions = productDimensionFromDatabase($product_str);
			if (!is_array(reset($dimensions))) $dimensions = array($dimensions);

			$packing = array();
			if (function_exists("productPackingFromDatabase")) $packing = productPackingFromDatabase($product_str);

			/**
			 * Collect the packaging sizes
			 */
			$bulk_sizes = array();
			foreach($dimensions as $row)
			{
				$cubic = bcmul($row["pack_width"], bcmul($row["pack_depth"], $row["pack_height"], 3), 3);
				$bulk_sizes[1] = array(array(
					"qty" => 1,
					"weight" => $row["weight"],
					"cubic" => (bccomp($cubic, "0.000", 3) > 0) ? $cubic : bcmul($row["cubic"], ($row["cubic_divisor"] != 0) ? $row["cubic_divisor"] : $cubic_divisor, 3),
					"width" => $row["pack_width"],
					"depth" => $row["pack_depth"],
					"height" => $row["pack_height"],
				));
			}
			foreach($dimensions as $row)
			{
				$cubic = bcmul($row["carton_width"], bcmul($row["carton_depth"], $row["carton_height"], 3), 3);
				$bulk_sizes[intval($row["carton_units"])] = array(array(
					"qty" => $row["carton_units"],
					"weight" => $row["carton_weight"],
					"cubic" => (bccomp($cubic, "0.000", 3) > 0) ? $cubic : bcmul($row["carton_cubic"], ($row["carton_cubic_divisor"] != 0) ? $row["carton_cubic_divisor"] : $cubic_divisor, 3),
					"width" => $row["carton_width"],
					"depth" => $row["carton_depth"],
					"height" => $row["carton_height"],
				));
			}
			if (function_exists("productBulkWeightsFromDatabase")) foreach(productBulkWeightsFromDatabase($product_str) as $row)
			{
				$cubic = bcmul($row["width"], bcmul($row["depth"], $row["height"], 3), 3);
				$bulk_sizes[$row["bulk_qty"]] = array(array(
					"qty" => $row["bulk_qty"],
					"weight" => $row["weight"],
					"cubic" => (bccomp($cubic, "0.000", 3) > 0) ? $cubic : bcmul($row["cubic"], ($row["cubic_divisor"] != 0) ? $row["cubic_divisor"] : $cubic_divisor, 3),
					"width" => $row["width"],
					"depth" => $row["depth"],
					"height" => $row["height"],
				));
			}
			if (count($packing))
			{
				/**
				 * Hack for invalid product updates
				 */
				foreach($packing as $k1 => $v1) if ($v1["pallet_enable"] == "y" /*$v1["pallet_id"] != 0*/)
				{
					$try1 = bcmul((bccomp($v1["pallet_width"], $v1["width"], 1) > 0) ? $v1["pallet_width"] : $v1["width"], (bccomp($v1["pallet_depth"], $v1["depth"], 1) > 0) ? $v1["pallet_depth"] : $v1["depth"], 2);
					$try2 = bcmul((bccomp($v1["pallet_width"], $v1["depth"], 1) > 0) ? $v1["pallet_width"] : $v1["depth"], (bccomp($v1["pallet_depth"], $v1["width"], 1) > 0) ? $v1["pallet_depth"] : $v1["width"], 2);
					if (bccomp($try1, $try2, 2) < 0) $packing[$k1]["volume"] = bcmul($try1, bcadd($v1["pallet_height"], $v2["height"], 1), 3);
					else $packing[$k1]["volume"] = bcmul($try2, bcadd($v1["pallet_height"], $v1["height"], 1), 3);
					$packing[$k1]["weight"] = bcadd($v1["weight"], $v1["pallet_weight"], 3);
					$packing[$k1]["volume_divisor"] = "1";
				}
				$bulk_sizes[1] = array();
				foreach($packing as $row)
				{
					$cubic = bcmul($row["width"], bcmul($row["depth"], $row["height"], 3), 3);
					$bulk_sizes[1][] = array(
						"qty" => 1,
						"weight" => $row["weight"],
						"cubic" => (bccomp($cubic, "0.000", 3) > 0) ? $cubic : bcmul($row["volume"], ($row["volume_divisor"] != 0) ? $row["volume_divisor"] : $cubic_divisor, 3),
						"width" => $row["width"],
						"depth" => $row["depth"],
						"height" => $row["height"],
					);
				}
			}
			unset($bulk_sizes[0]);
			krsort($bulk_sizes);

			/**
			 * Estimate packages with no dimensions
			 */
			$changes = true;
			while($changes)
			{
				$changes = false;
				foreach($bulk_sizes as $k1 => $v1) if (count($v1) == 1 && (bccomp($v1[0]["width"], "0.00", 2) == 0 || bccomp($v1[0]["depth"], "0.00", 2) == 0 || bccomp($v1[0]["height"], "0.00", 2) == 0)) foreach($bulk_sizes as $k2 => $v2) if ($k2 < $k1 && count($v2) == 1)
				{
					if (bccomp($v2[0]["width"], "0.00", 2) != 0 && bccomp($v2[0]["depth"], "0.00", 2) != 0 && bccomp($v2[0]["height"], "0.00", 2) != 0)
					{
						$multiplier = ($k1 + 0.0) / ($k2 + 0.0);
						$root_x = ceil(pow($multiplier, 1.0 / 3.0));
						$root_y = ceil(sqrt($multiplier / $root_x));
						$root_z = ceil($multiplier / ($root_x * $root_y));
						$dim = array($v2[0]["width"], $v2[0]["depth"], $v2[0]["height"]);
						bcscale(2);
						usort($dim, "bccomp");
						$bulk_sizes[$k1][0]["width"] = bcmul($dim[0], $root_x, 2);
						$bulk_sizes[$k1][0]["depth"] = bcmul($dim[1], $root_y, 2);
						$bulk_sizes[$k1][0]["height"] = bcmul($dim[2], $root_z, 2);
						$changes = true;
					}
					break;
				}
			}

			$packages = array();
			$qty_count = $v["product_qty"];
			foreach($bulk_sizes as $qty => $row) while($qty <= $qty_count)
			{
				$packages = array_merge($packages, $row);
				$qty_count -= $qty;
			}

			foreach($packages as $k1 => $v1)
			{
				if (bccomp($v1["weight"], "0.000", 3) == 0)
				{
					if (bccomp($product["weight_inc"], "0.000", 3) == 0) $packages[$k1]["weight"] = bcmul($product["weight"], $v1["qty"], 3);
					else $packages[$k1]["weight"] = bcadd($product["weight"], bcmul($product["weight_inc"], $v1["qty"] - 1, 3), 3);
				}
				if (bccomp($v1["cubic"], "0.000", 3) == 0) $packages[$k1]["cubic"] = bcmul($v1["width"], bcmul($v1["depth"], $v1["height"], 3), 3);
			}

			/**
			 * Calculate overall shipping weight
			 */
			$ship_weight = "0.000";
			$ship_mass = "0.000";
			$ship_cubic = "0.000";
			foreach($packages as $row)
			{
				/*
				if (bccomp($row["weight"], bcdiv($row["cubic"], $cubic_divisor, 3), 3) >= 0) $ship_weight = bcadd($ship_weight, $row["weight"], 3);
				else $ship_weight = bcadd($ship_weight, bcdiv($row["cubic"], $cubic_divisor, 3), 3);
				*/
				$ship_mass = bcadd($ship_mass, $row["weight"], 3);
				$ship_cubic = bcadd($ship_cubic, bcdiv($row["cubic"], $cubic_divisor, 3), 3);
			}
			if (bccomp($ship_mass, $ship_cubic, 3) >= 0) $ship_weight = $ship_mass;
			else $ship_weight = $ship_cubic;

			$invoice_product = array(
				"cart_key" => $cart_key,
				"remove_url" => "/cart/removefromcart_" . $cart_key . ".html",
				"availability" => $product["availability"],
				"product_total_cubic" => $ship_cubic,
				"product_total_mass" => $ship_mass,
				"promotion_id" => $product["promotion_id"],

				"decimal" => $product["precision"],
				"generic" => "n",
				"generic_code" => "",
				"is_gift" => ($v["is_gift"] == "y") ? "y" : "n",
				"prod_count" => $count,
				"product_condition" => $product["condition"],
				"product_id" => $product["id"],
				"product_name" => $product["product_name"],
				"product_notes" => "",
				"product_online" => convertCurrency(($product["price_online"] == "y") ? $product["primary_price"] : "0.0000", $product["primary_currency"], $invoice["invoice_currency"], false, $product["precision"]),
				//"product_packing" => 0,
				"product_plug" => $v["adapter_id"],
				"product_price_changed" => "n",
				"product_qty" => $v["product_qty"],
				"product_rank" => $count++,
				"product_section" => $product["section"],
				"product_status" => "quoted",
				//"product_terms" => 0,
				"product_total_weight" => $ship_weight,
				"product_updated" => "n",
				"product_voltage" => ($product["power_110"] == $product["power_240"] && $product["power_110"] != 0) ? 7 : (($v["power_id"] == 110) ? 1 : (($v["power_id"] == 240) ? 5 : 0)),
				"product_warranty" => $product["warranty"],
				"product_weight" => $product["weight"],
				"product_weight_inc" => $product["weight_inc"],
				"site_id" => $_GLOBALS["site_settings"]["site_id"],
				"sub_id" => $product["sub_id"],
				"supplier_country" => $product["location"],
				"supplier_currency" => $product["cost_currency"],
				//"supplier_found" => "y","n","s",
				//"supplier_id" => "",
				"product_cost" => $product["cost_price"],
				"product_offer" => ($product["price_online"] == "y") ? $product["effective_price"] : "0.0000",
			);

			$invoice_product["availability"] = 1;	//In Stock

			if ($product["stock"] == "c")
			{
				$invoice_product["availability"] = 4;	//Consignment
				$invoice["invoice_status"] = "inquiry";
				$issues["consignment"] = true;
			}

			if (($product["qty"] - $product['reserved']) < $invoice_product["product_qty"])
			{
				$invoice_product["availability"] = 0;	//Backorder
				$invoice["invoice_status"] = "inquiry";
				$invoice["products_locate_date"] = null;
				$issues["backorder"] = true;
			}

			if (isset($force_backorder) && $force_backorder)
			{
				$invoice_product["availability"] = 0;
				$invoice["invoice_status"] = "inquiry";
				$invoice["products_locate_date"] = null;
				$issues["backorder"] = true;
			}

			if ($product["allow_invoice"] == "n")
			{
				$invoice["invoice_status"] = "inquiry";
				$invoice["shipping_method"] = "offer";
				$issues["block_invoice"] = true;
				$issues["weight"] = true;
			}

			if ($v["used"] == "y")
			{
				$floor_price = convertCurrency($product['floor_price'], $product["primary_currency"], $invoice["invoice_currency"], false, $product["precision"]);
				if (bccomp($floor_price, "0.0000", $product["precision"]) > 0 && bccomp($floor_price, $invoice_product["product_offer"], $product["precision"]) < 0)
				{
					$invoice_product['product_condition'] = 2;
					$invoice_product['product_warranty'] = $product["used_warranty"];
				}
			}

			if (bccomp($ship_weight, "0.0000", 4) <= 0)
			{
				//$invoice["invoice_status"] = "inquiry";
				$invoice["shipping_method"] = "offer";
				$issues["weight"] = true;
			}

			if ($invoice_product["is_gift"] == "n" && bccomp($invoice_product["product_offer"], "0.0000", $invoice_product["decimal"]) <= 0)
			{
				$invoice["invoice_status"] = "inquiry";
				$issues["price"] = true;
			}

			$notes = array();

			if ($v["used"] == "y") $notes[] = 'Used/Floor Stock requested.';
			if (array_key_exists($invoice_product["product_voltage"], $voltage_list) && $voltage_list[$invoice_product["product_voltage"]] != "") $notes[] = "Power Supply Voltage: " . $voltage_list[$invoice_product["product_voltage"]];
			if (array_key_exists($invoice_product["product_plug"], $plug_list) && $plug_list[$invoice_product["product_plug"]] != "") $notes[] = "Power Adapter ID: " . $plug_list[$invoice_product["product_plug"]];
			if (array_key_exists($invoice_product["product_warranty"], $warranty_list) && $warranty_list[$invoice_product["product_warranty"]] != "") $notes[] = "Warranty: " . $warranty_list[$invoice_product["product_warranty"]];
			if (array_key_exists($invoice_product["product_condition"], $condition_list) && $condition_list[$invoice_product["product_condition"]] != "") $notes[] = "Condition: " . $condition_list[$invoice_product["product_condition"]];
			if (array_key_exists($product["location"], $location_list) && $location_list[$product["location"]] != "") $notes[] = "Location: " . $location_list[$product["location"]];
			$notes[] = "";

			//Promotion/Special/Member Discount
			if ($product["promotion"] == "y" && in_array("voucher", explode(",", $product["promotion_type"])))
			{
				$voucher = productGiftVoucherFromDatabase($product_str);
				switch($voucher["expiry_value"])
				{
				case 0:
					$notes[] = "A " . getCurrencySymbol($voucher["currency"], "UTF8") . convertCurrency($voucher["amount"], $voucher["currency"]) . " " . $voucher["currency"] . " gift voucher will be added to your account once this order is processed";
					break;
				case 1:
					$notes[] = "A " . getCurrencySymbol($voucher["currency"], "UTF8") . convertCurrency($voucher["amount"], $voucher["currency"]) . " " . $voucher["currency"] . " gift voucher will be added to your account once this order is processed (Expires after 1 month)";
					break;
				default:
					$notes[] = "A " . getCurrencySymbol($voucher["currency"], "UTF8") . convertCurrency($voucher["amount"], $voucher["currency"]) . " " . $voucher["currency"] . " gift voucher will be added to your account once this order is processed (Expires after " . $voucher["expiry_value"] . " months)";
					break;
				}
			}

			$last_note = "";

			/**
			 * Bulk Pricing
			 */
			/*
			//Method 1: Split by package size
			if (function_exists("productBulkPricingFromDatabase"))
			{
				$sum = "0.0000";
				$qty = $invoice_product["product_qty"];
				$special_prices = array();
				foreach(productBulkPricingFromDatabase($invoice_product["product_id"], $invoice_product["sub_id"]) as $special)
				{
					$special["effective_price"] = convertCurrency((strtoupper($product["selected"]) == "AUD") ? $special["unit_sale_price"] : $special["unit_sale_price_us"], (strtoupper($product["selected"]) == "AUD") ? "AUD" : "USD", $invoice["invoice_currency"], false, $product["precision"]);
					if ($special["qty"] != 0 && bccomp($special["effective_price"], "0.00", 2) > 0) $special_prices[$special["qty"]] = $special;
				}

				if (count($special_prices))
				{
					krsort($special_prices);
					foreach($special_prices as $special)
					{
						$need = floor($qty / $special["qty"]);
						if ($need > 0)
						{
							$qty -= $need * $special["qty"];
							$sum = bcadd($sum, bcmul($need, bcmul($special["qty"], $special["effective_price"], 4), 4), 4);
							$notes[] = sprintf("%d x %d units at %s%s %s each", $need, $special["qty"], getCurrencySymbol($invoice["invoice_currency"], "UTF8"), $special["effective_price"], $invoice["invoice_currency"]);
						}
					}

					if ($qty > 0)
					{
						$sum = bcadd($sum, bcmul($qty, $invoice_product["product_offer"], 4), 4);
						$notes[] = sprintf("%d x 1 unit at %s%s %s each", $qty, getCurrencySymbol($invoice["invoice_currency"], "UTF8"), $invoice_product["product_offer"], $invoice["invoice_currency"]);
					}
					$invoice_product["product_offer"] = bcdiv($sum, $invoice_product["product_qty"], 4);

					if (bccomp($invoice_product["product_online"], $invoice_product["product_offer"], 2) > 0)
					{
						$savings = bcsub($invoice_product["product_online"], $invoice_product["product_offer"], $invoice_product["decimal"]);
						$percent = bcceil(bcdiv(bcmul($savings, "100.0", 4), $invoice_product["product_online"], 4));
						$last_note = sprintf("Bulk Discount: %s%% (RRP: %s%s %s, save %s%s %s)", $percent, getCurrencySymbol($invoice["invoice_currency"], "UTF8"), $invoice_product["product_online"], $invoice["invoice_currency"], getCurrencySymbol($invoice["invoice_currency"], "UTF8"), $savings, $invoice["invoice_currency"]);
					}

				}
			}
			*/
			///*
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
			//*/

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
			if (function_exists("memberDiscountsFromDatabase") && ($percent = memberDiscountsFromDatabase($_GLOBALS['member']['id'], $_GLOBALS["site_settings"]["site_id"])) != 0)
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
			if (function_exists("memberProductSpecialPrices") && is_array($special_prices = memberProductSpecialPrices($member['member_id'], $invoice_product["product_id"], $_GLOBALS['site_settings']['site_id'])))
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

			/**
			 * Apply discount pricing
			 */
			foreach($invoice_discounts as $k1 => $invoice_discount) if ($invoice_discount["section"] == 0 || $invoice_discount["section"] == $invoice_product["product_section"])
			{
				$new_offer = convertCurrency(bcmul($invoice_product["product_offer"], bcsub("1.00", $invoice_discount["value"], 2), 4), $invoice["invoice_currency"], "", false, $invoice_product["decimal"]);
				if (bccomp($invoice_product["product_offer"], $new_offer, 2) > 0)
				{
					$invoice_discounts[$k1]["price_deducted"] = bcadd($invoice_discount["price_deducted"], bcsub($invoice_product["product_offer"], $new_offer, 2), 2);

					/*
					$invoice_product["product_offer"] = $new_offer;
					$savings = bcsub($invoice_product["product_online"], $new_offer, $invoice_product["decimal"]);
					$percent = bcceil(bcdiv(bcmul($savings, "100.0", 4), $invoice_product["product_online"], 4));
					$last_note = sprintf("Discount Coupon: %s%% (RRP: %s%s %s, save %s%s %s)", $percent, getCurrencySymbol($invoice["invoice_currency"], "UTF8"), $invoice_product["product_online"], $invoice["invoice_currency"], getCurrencySymbol($invoice["invoice_currency"], "UTF8"), $savings, $invoice["invoice_currency"]);
					*/
				}
			}

			$notes[] = $last_note;

			//if (bccomp($invoice_product["product_online"], $invoice_product["product_offer"], $invoice_product["decimal"]) > 0) $notes[] = sprintf("Sale Discount: %d
			if (count($notes)) $invoice_product["product_notes"] = trim(implode("\n", $notes));

			if ($v["is_gift"] == "y") foreach(productGiftsFromDatabase($v["dependant_id"], $v["dependant_sub_id"]) as $v2) if ($v2["linked_id"] == $v["product_id"] && $v2["linked_sub_id"] == $v["sub_id"]) $invoice_product["product_offer"] = convertCurrency($v2["price"], $v2["currency"], $invoice["invoice_currency"], false, $v2["precision"]);

			//$invoice["ship_weight"] = bcadd($invoice["ship_weight"], $invoice_product["product_total_weight"], 3);
			$invoice["ship_mass"] = bcadd($invoice["ship_mass"], $invoice_product["product_total_mass"], 3);
			$invoice["ship_cubic"] = bcadd($invoice["ship_cubic"], $invoice_product["product_total_cubic"], 3);
			if (bccomp($invoice["ship_mass"], $invoice["ship_cubic"], 3) > 0) $invoice["ship_weight"] = $invoice["ship_mass"];
			else $invoice["ship_weight"] = $invoice["ship_cubic"];

			foreach($price_keys as $k2)
			{
				$invoice_product["product_" . $k2 . "au"] = convertCurrency($invoice_product["product_" . $k2], $invoice["invoice_currency"], "AUD");
				$invoice_product["product_" . $k2 . "us"] = convertCurrency($invoice_product["product_" . $k2], $invoice["invoice_currency"], "USD");
			}
			foreach($currency_keys as $k1) foreach($price_keys as $k2) $invoice["products_" . $k2 . $k1] = bcadd($invoice["products_" . $k2 . $k1], bcmul($invoice_product["product_qty"], $invoice_product["product_" . $k2 . $k1], 4), 4);
			$invoice_products[] = $invoice_product;
			break;

		case "y":
			//Does this ever happen? Maybe some kind of discount?
			break;

		case "b":
			if (!function_exists("bulkFromDatabase") && !moduleLoad("bulk")) break;
			$bulk = bulkFromDatabase($v["generic_code"]);
			if ($bulk["active"] == "n" || $bulk["status"] != "y") break;

			//Note: Some bright spark made bulk_offer as GST inclusive
			if ($bulk["bulk_currency"] == "AUD") $bulk["bulk_offer"] = bcdiv($bulk["bulk_offer"], "1.1", 2);

			$invoice_product = array(
				"cart_key" => $cart_key,
				"remove_url" => "/cart/removefromcart_" . $cart_key . ".html",
				"availability" => 0,
				"promotion_id" => $bulk["promotion_id"],

				"decimal" => 2,
				"generic" => "b",
				"generic_code" => $bulk["bulk_number"],
				"prod_count" => $count,
				"product_condition" => $bulk["bulk_condition"],
				"product_cost" => convertCurrency($bulk["bulk_cost"], $bulk["bulk_currency"], $invoice["invoice_currency"]),
				"product_id" => 0,//$bulk["bulk_id"],
				"product_name" => $bulk["title"],
				"product_offer" => convertCurrency($bulk["bulk_offer"], $bulk["bulk_currency"], $invoice["invoice_currency"]),
				"product_online" => convertCurrency($bulk["bulk_online"], $bulk["bulk_currency"], $invoice["invoice_currency"]),
				//"product_packing" => 0,
				//"product_plug" => 0,
				"product_price_changed" => "n",
				"product_qty" => $v["product_qty"],
				"product_rank" => $count++,
				"product_section" => $bulk["bulk_section"],
				"product_terms" => $bulk["bulk_terms"],
				"product_total_weight" => bcmul($v["product_qty"], $bulk["bulk_weight"], 3),
				"product_updated" => "n",
				"product_voltage" => $bulk["bulk_voltage"],
				"product_warranty" => $bulk["bulk_warranty"],
				"product_weight" => $bulk["bulk_weight"],
				"product_weight_inc" => $bulk["bulk_weight"],
				"site_id" => $_GLOBALS["site_settings"]["site_id"],
				"supplier_cost" => convertCurrency($bulk["bulk_cost"], $bulk["bulk_currency"], $invoice["invoice_currency"]),
				"supplier_country" => $bulk["bulk_country"],
				"supplier_currency" => $bulk["bulk_currency"],
				//"supplier_found" => "y","n","s",
				"supplier_id" => $bulk["bulk_supplier"],
				"supplier_name" => $supplier_list[$bulk["bulk_supplier"]],
			);

			$notes = array();

			$temp = bulkProductsFromDatabase($v["generic_code"]);
			if (count($temp))
			{
				$notes[] = "Products Included in this Bulk offer :";
				foreach($temp as $temp) $notes[] = sprintf("#%s - %s - %dpc", productFormatNumber($temp["product_id"], $temp["sub_id"]), $temp["product_name"], $temp["product_qty"]);
				$notes[] = "";
			}

			if ($v["used"] == "y") $notes[] = 'Used/Floor Stock requested.';
			if ($voltage_list[$invoice_product["product_voltage"]] != "") $notes[] = "Power Supply Voltage: " . $voltage_list[$invoice_product["product_voltage"]];
			if ($plug_list[$invoice_product["product_plug"]] != "") $notes[] = "Power Adapter ID: " . $plug_list[$invoice_product["product_plug"]];
			if ($warranty_list[$invoice_product["product_warranty"]] != "") $notes[] = "Warranty: " . $warranty_list[$invoice_product["product_warranty"]];
			if ($condition_list[$invoice_product["product_condition"]] != "") $notes[] = "Condition: " . $condition_list[$invoice_product["product_condition"]];
			if ($location_list[$bulk["bulk_country"]] != "") $notes[] = "Location: " . $location_list[$bulk["bulk_country"]];
			$notes[] = "";

			$last_note = "";

			/**
			 * Special Pricing
			 */
			if (bccomp($invoice_product["product_online"], $invoice_product["product_offer"], 2) > 0)
			{
				$savings = bcsub($invoice_product["product_online"], $invoice_product["product_offer"], $invoice_product["decimal"]);
				$percent = bcceil(bcdiv(bcmul($savings, "100.0", 4), $invoice_product["product_online"], 4));
				$last_note = sprintf("Sale Discount: %s%% (RRP: %s%s %s, save %s%s %s)", $percent, getCurrencySymbol($invoice["invoice_currency"], "UTF8"), $invoice_product["product_online"], $invoice["invoice_currency"], getCurrencySymbol($invoice["invoice_currency"], "UTF8"), $savings, $invoice["invoice_currency"]);
			}

			$notes[] = $last_note;

			if (count($notes)) $invoice_product["product_notes"] = trim(implode("\n", $notes));

			foreach($price_keys as $k2)
			{
				$invoice_product["product_" . $k2 . "au"] = convertCurrency($invoice_product["product_" . $k2], $invoice["invoice_currency"], "AUD");
				$invoice_product["product_" . $k2 . "us"] = convertCurrency($invoice_product["product_" . $k2], $invoice["invoice_currency"], "USD");
			}
			foreach($currency_keys as $k1) foreach($price_keys as $k2) $invoice["products_" . $k2 . $k1] = bcadd($invoice["products_" . $k2 . $k1], bcmul($invoice_product["product_qty"], $invoice_product["product_" . $k2 . $k1], 4), 4);
			$invoice_products[] = $invoice_product;
			break;
		}
	}
}

foreach($invoice_discounts as $k => $v)
{
	$invoice_product = array(
		//"cart_key" => "discount:" . $v["name"],
		"remove_url" => "discount.html?action=reset&return_url=%2Fcheckout%2F&code=" . urlencode($v["name"]),
		"availability" => 1,
		//"product_total_cubic" => $ship_cubic,
		//"product_total_mass" => $ship_mass,
		//"promotion_id" => $product["promotion_id"],
		"decimal" => 2,
		"generic" => "y",
		"generic_code" => "GPA",
		//"is_gift" => ($v["is_gift"] == "y") ? "y" : "n",
		"prod_count" => $count,
		//"product_condition" => $product["condition"],
		//"product_id" => $product["id"],
		"product_name" => "Discount Coupon : " . $v["name"],
		//"product_notes" => "",
		"product_online" => convertCurrency(bcsub("0.00", $v["price_deducted"], 2), $invoice["invoice_currency"], "", false, 2),
		//"product_packing" => 0,
		//"product_plug" => $v["adapter_id"],
		//"product_price_changed" => "n",
		"product_qty" => 1,
		"product_rank" => $count++,
		"product_section" => $v["section"],
		//"product_status" => "quoted",
		//"product_terms" => 0,
		//"product_total_weight" => $ship_weight,
		//"product_updated" => "n",
		//"product_voltage" => ($product["power_110"] == $product["power_240"] && $product["power_110"] != 0) ? 7 : (($v["power_id"] == 110) ? 1 : (($v["power_id"] == 240) ? 5 : 0)),
		//"product_warranty" => $product["warranty"],
		//"product_weight" => $product["weight"],
		//"product_weight_inc" => $product["weight_inc"],
		"site_id" => $_GLOBALS["site_settings"]["site_id"],
		//"sub_id" => $product["sub_id"],
		//"supplier_country" => $product["location"],
		//"supplier_currency" => $product["cost_currency"],
		//"supplier_found" => "y","n","s",
		//"supplier_id" => "",
		"product_cost" => "0.0000",
		"product_offer" => convertCurrency(bcsub("0.00", $v["price_deducted"], 2), $invoice["invoice_currency"], "", false, 2),
	);

	$notes = array();
	//if (bccomp($v["price_deducted"], "0.00", 2) > 0) $notes[] = "Total discount applied " . getCurrencySymbol($invoice["invoice_currency"], "UTF8") . convertCurrency($v["price_deducted"], $invoice["invoice_currency"], "", false, 2);
	if ($v["description"] != "") $notes[] = $v["description"];
	if (count($notes)) $invoice_product["product_notes"] = trim(implode("\n", $notes));

	foreach($price_keys as $k2)
	{
		$invoice_product["product_" . $k2 . "au"] = convertCurrency($invoice_product["product_" . $k2], $invoice["invoice_currency"], "AUD");
		$invoice_product["product_" . $k2 . "us"] = convertCurrency($invoice_product["product_" . $k2], $invoice["invoice_currency"], "USD");
	}
	foreach($currency_keys as $k1) foreach($price_keys as $k2) $invoice["products_" . $k2 . $k1] = bcadd($invoice["products_" . $k2 . $k1], bcmul($invoice_product["product_qty"], $invoice_product["product_" . $k2 . $k1], 4), 4);
	$invoice_products[] = $invoice_product;
}

/**
 * Disable freight quote for arcade machine purchases
 */
foreach($invoice_products as $invoice_product) if ($invoice_product["product_section"] == 1)
{
	if (!array_key_exists("checkout", $posted) || !array_key_exists("ship_number", $posted["checkout"]) || $posted["checkout"]["ship_number"] == "") $posted["checkout"]["ship_number"] = "offer";
	$issues["weight"] = true;
	break;
}

$count = 0;
$invoice_courier_quotes = array();

if (moduleLoad("freight") && bccomp($invoice["ship_weight"], "0.0000", 4) != 0 && !(array_key_exists("weight", $issues) && $issues["weight"]))
{
	$found = array();
	foreach(freightSortArray(freightSearchCouriers($invoice["ship_country"], $invoice["ship_postcode"], $invoice["ship_state"], $invoice["ship_city"], $invoice["ship_weight"], $invoice["products_offer"], $invoice["invoice_currency"])) as $v)
	{
		//$key = $v["supplier"] . "_" . $v["service_type"] . "_" . $v["track"] . "_" . $v["insurance"] . "_" . $v["stock"] . "_" . $v["type"] . "_" . $v["tail_gate"] . "_" . $v["product_type"];
		$key = $v["ship_number"];

		$container_list = array(true);
		if (moduleLoad("packing"))
		{
			list($width, $depth, $height) = array($v['width'], $v['depth'], $v['height']);
			if ($width > 0 && $depth > 0)
			{
				$packing = array();
				$packing['packing_products'] = $invoice_products;
				$packing['satchel'] = array('width' => $v['width'], 'depth' => $v['depth'], 'height' => $v['height'], 'weight' => $v['weight'], 'name' => $v['name']);
				$packing['packing_action'] = 'checkout_satchel';
				$packing['spareparts'] = true;
				$safety = $error_list;
				$container_list = packingFromCache($packing);
				$error_list = $safety;
				//$container_list=packingOptimisePacking($container_list);
				//if (count($container_list) > 1 || !count($container_list)) unset($invoice_courier_quotes[$k]);
				//else
				if (count($container_list) == 1) $v["packing_string"] = "b64_gz_ser:" . base64_encode(gzcompress(serialize($container_list)));
			}
		}

		if (!array_key_exists($key, $found) && count($container_list) == 1)
		{
			$invoice_courier_quotes[] = array(
				"ship_number" => $v["ship_number"],
				"service_id" => $v["id"],
				"express" => $v["express"],
				"receipted" => $v["receipted"],
				"registered" => $v["registered"],

				"width" => $v["width"],
				"depth" => $v["depth"],
				"height" => $v["height"],
				"weight" => $v["weight"],

				"quote_count" => $count,
				"flag" => ($count == 0) ? "y" : "n",
				"name" => $v["name"],
				"del_time" => $v["del_time"],
				"track" => $v["track"],
				"insurance" => $v["insurance"],
				"tail_gate" => $v["tail_gate"],
				"cost_price" => convertCurrency($v["cost_price"], $v["currency"], $invoice["invoice_currency"]),
				"sell_price" => convertCurrency($v["sell_price"], $v["currency"], $invoice["invoice_currency"]),
				"retail_price" => convertCurrency($v["retail_price"], $v["currency"], $invoice["invoice_currency"]),
				"cost_priceau" => convertCurrency($v["cost_price"], $v["currency"], "AUD"),
				"sell_priceau" => convertCurrency($v["sell_price"], $v["currency"], "AUD"),
				"cost_priceus" => convertCurrency($v["cost_price"], $v["currency"], "USD"),
				"sell_priceus" => convertCurrency($v["sell_price"], $v["currency"], "USD"),
				"supplier" => $v["supplier"],
				"supplier_name" => $v["supplier_name"],

				"packing_string" => $v["packing_string"],

				"notes" => $v["notes"],
			);
			$found[$key] = $key;
			$count++;
		}
	}
}

/**
 * Override with checkout choices
 */
if (array_key_exists("checkout", $posted) && is_array($posted["checkout"]))
{
	/**
	 * Override shipping method
	 */
	if (array_key_exists("ship_number", $posted["checkout"]) && $posted["checkout"]["ship_number"] != "")
	{
		foreach($invoice_courier_quotes as $k => $v) if ($posted["checkout"]["ship_number"] == $v["ship_number"])
		{
			foreach($invoice_courier_quotes as $k2 => $v2) $invoice_courier_quotes[$k2]["flag"] = "n";
			$invoice_courier_quotes[$k]["flag"] = "y";
			break;
		}
		if ($posted["checkout"]["ship_number"] == "pickup")
		{
			foreach($invoice_courier_quotes as $k2 => $v2) $invoice_courier_quotes[$k2]["flag"] = "n";
			$invoice["shipping_method"] = "pickup";
		}
		if ($posted["checkout"]["ship_number"] == "offer")
		{
			foreach($invoice_courier_quotes as $k2 => $v2) $invoice_courier_quotes[$k2]["flag"] = "n";
			$invoice["shipping_method"] = "offer";
		}
		if ($posted["checkout"]["ship_number"] == "own_0")
		{
			foreach($invoice_courier_quotes as $k2 => $v2) $invoice_courier_quotes[$k2]["flag"] = "n";
			$invoice["shipping_method"] = "customer_forwarder";
			$invoice["ship_product_notes"] = "Customer Account #: " . $posted["checkout"]["own_account_num"];
			$invoice["ship_id"] = $posted["checkout"]["own_courier_id"];
			$invoice["ship_supplier_id"] = $posted["checkout"]["own_courier_id"];
			$invoice["ship_product_name"] = ((array_key_exists($posted["checkout"]["own_courier_id"], $courier_list)) ? $courier_list[$posted["checkout"]["own_courier_id"]] : $posted["checkout"]["own_courier_name"]) . " (Personal Account)";
			$invoice["ship_supplier_name"] = (array_key_exists($posted["checkout"]["own_courier_id"], $courier_list)) ? $courier_list[$posted["checkout"]["own_courier_id"]] : $posted["checkout"]["own_courier_name"];
			$invoice["ship_account_num"] = $posted["checkout"]["own_account_num"];
		}
		elseif (preg_match("/^own_([0-9]+)\$/", $posted["checkout"]["ship_number"], $matches)) foreach($_GLOBALS["member"]["couriers"] as $v) if ($matches[1] == $v["id"])
		{
			foreach($invoice_courier_quotes as $k2 => $v2) $invoice_courier_quotes[$k2]["flag"] = "n";
			$invoice["shipping_method"] = "customer_forwarder";
			$invoice["ship_product_notes"] = "Customer Account #: " . $v["account_num"];
			$invoice["ship_id"] = $v["courier_id"];
			$invoice["ship_supplier_id"] = $v["courier_id"];
			$invoice["ship_product_name"] = $v["courier_name"] . " (Personal Account)";
			$invoice["ship_supplier_name"] = $v["courier_name"];
			$invoice["ship_account_num"] = $v["account_num"];
			break;
		}
	}
}

/**
 * Set the selected shipping method
 */
foreach($invoice_courier_quotes as $k => $v) if ($v["flag"] == "y")
{
	$invoice["ship_number"] = $v["ship_number"];
	$invoice["packing_string"] = $v["packing_string"];
	$invoice["ship_online"] = $v["retail_price"];
	$invoice["ship_onlineau"] = convertCurrency($v["retail_price"], $invoice["invoice_currency"], "AUD");
	$invoice["ship_onlineus"] = convertCurrency($v["retail_price"], $invoice["invoice_currency"], "USD");

	$invoice["shipping_method"] = "other";
	$invoice["ship_insurance"] = $v["insurance"];
	$invoice["ship_tailgate"] = $v["tail_gate"];
	$invoice["ship_registered"] = $v["registered"];
	$invoice["ship_receipted"] = $v["receipted"];
	$invoice["ship_express"] = $v["express"];
	$invoice["ship_offer"] = $v["sell_price"];
	$invoice["ship_cost"] = $v["cost_price"];
	$invoice["ship_offerau"] = $v["sell_priceau"];
	$invoice["ship_costau"] = $v["cost_priceau"];
	$invoice["ship_offerus"] = $v["sell_priceus"];
	$invoice["ship_costus"] = $v["cost_priceus"];
	$invoice["ship_id"] = $v["service_id"];
	$invoice["ship_supplier_id"] = $v["supplier"];
	$invoice["ship_product_name"] = $v["name"];
	$invoice["ship_supplier_name"] = $v["supplier_name"];
}

/**
 * Override with checkout choices
 */
if (array_key_exists("checkout", $posted) && is_array($posted["checkout"]))
{
	/**
	 * Override shipping insurance
	 */
	if (array_key_exists("ship_insurance", $posted["checkout"]) && $invoice["ship_insurance"] == "n" && $posted["checkout"]["ship_insurance"] == "y" && $invoice["shipping_method"] == "other")
	{
		$invoice["terms_id"] = 4;
	}
}

$count = 0;
$invoice_charges = array();

/**
 * Add any forwarding fees (Once per manufacturer)
 */
$forwarder_fees = array();
foreach($invoice_products as $k => $v) if ($v["generic"] == "n" && $v["is_gift"] != "y")
{
	$product_str = productStrJoin($v["product_id"], $v["sub_id"]);
	if (array_key_exists($product_str, $product_cache)) $product = $product_cache[$product_str];
	else
	{
		$product = productFromDatabase($product_str);
		if (function_exists("productAlternatesFromDatabase")) foreach(productAlternatesFromDatabase($product_str) as $v3) if ($v3["site_id"] == $_GLOBALS["site_settings"]["site_id"])
		{
			if ($v3["image_file"] != "") unset($product["thumb_file"]);
			foreach($v3 as $k2 => $v2) if ($k2 != "id" && $v2 != "") $product[$k2] = $v2;
		}
		$product_cache[$product_str] = $product;
	}
	if ($product["additional_fees"] == "y" && (!isset($forwarder_fees[$product["manufacturer"]]) || bccomp(convertCurrency($product["forwarder_fee"], $product["forwarder_fee_currency"], $invoice["invoice_currency"]), $forwarder_fees[$product["manufacturer"]]["forwarder_fee"]) > 0)) $forwarder_fees[$product["manufacturer"]] = array(
		"charge_id" => 30,
		"charge_name" => "Forwarding Fee from " . (($manufacturer_list[$manufacturer_id] != "") ? $manufacturer_list[$manufacturer_id] : "Manufacturer"),
		"charge_qty" => 1,
		"charge_notes" => "Additional fee of " . getCurrencySymbol($product["forwarder_fee_currency"], "UTF8") . convertCurrency($product["forwarder_fee"], $product["forwarder_fee_currency"]) . " " . formatHTMLSafe($product["forwarder_fee_currency"]) . " to transport the goods to our distribution warehouse",
		"charge_offer" => convertCurrency($product["forwarder_fee"], $product["forwarder_fee_currency"], $invoice["invoice_currency"]),
		"charge_cost" => convertCurrency($product["forwarder_fee"], $product["forwarder_fee_currency"], $invoice["invoice_currency"]),
		"charge_online" => convertCurrency($product["forwarder_fee"], $product["forwarder_fee_currency"], $invoice["invoice_currency"]),
	);
}
foreach($forwarder_fees as $invoice_charge)
{
	$invoice_charge["charge_count"] = $count;
	$invoice_charge["charge_rank"] = $count++;
	foreach($price_keys as $k2)
	{
		$invoice_charge["charge_" . $k2 . "au"] = convertCurrency($invoice_charge["charge_" . $k2], $invoice["invoice_currency"], "AUD");
		$invoice_charge["charge_" . $k2 . "us"] = convertCurrency($invoice_charge["charge_" . $k2], $invoice["invoice_currency"], "USD");
	}
	foreach($currency_keys as $k1) foreach($price_keys as $k2) $invoice["charges_" . $k2 . $k1] = bcadd($invoice["charges_" . $k2 . $k1], bcmul($invoice_charge["charge_qty"], $invoice_charge["charge_" . $k2 . $k1], 4), 4);
	$invoice_charges[] = $invoice_charge;
}
unset($forwarder_fees);

/**
 * Add Packing And Handling Charge
 */
//$us_am = convertCurrency(bcadd($invoice["products_offer"], bcadd($invoice["ship_offer"], $invoice["charges_offer"], 2), 2), $invoice["invoice_currency"], "USD");
$us_am = convertCurrency($invoice["products_offer"], $invoice["invoice_currency"], "USD");
if (bccomp($_GLOBALS["site_settings"]["handling_minimum_us"], "0.00", 2) > 0 && bccomp($invoice["products_offerus"], $_GLOBALS["site_settings"]["handling_minimum_us"], 2) < 0)
{
	$invoice_charge = array(
		"charge_count" => $count,
		"charge_rank" => $count++,
		"charge_id" => 5,
		"charge_name" => "Packing and Handling Fee",
		"charge_qty" => 1,
		"charge_notes" => sprintf(
			"For Orders less than $%s USD there will be a $5 USD packing & handling fee added to your order. (This does not include freight costs)",
			convertCurrency($_GLOBALS["site_settings"]["handling_minimum_us"], "USD")
		),
		"charge_offer" => convertCurrency("5.00", "USD", $invoice["invoice_currency"]),
		"charge_cost" => convertCurrency("5.00", "USD", $invoice["invoice_currency"]),
		"charge_online" => convertCurrency("5.00", "USD", $invoice["invoice_currency"]),
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
 * Add Highway Insurance
 */
moduleLoad("insurance");
$invoice["insurance_offer"] = "";
if (function_exists("insuranceCalculateAmount"))
{
	$au_am = convertCurrency($invoice["products_offer"], $invoice["invoice_currency"], "AUD");
	if ($invoice["supply_country"] == "AU" && $invoice["bill_country"] == "AU" && $invoice["ship_country"] == "AU") $au_am = convertCurrency(bcmul($au_am, "1.1", 2), "AUD");
	$invoice["insurance_offer"] = $insure_amount = convertCurrency(insuranceCalculateAmount(1, $au_am), "AUD", $invoice["invoice_currency"]);
	if ($invoice["terms_id"] == 4/* && bccomp($insure_amount, "0.0", 2) > 0*/)
	{
		$invoice_charge = array(
			"charge_count" => $count,
			"charge_rank" => $count++,
			"charge_id" => 21,
			"charge_name" => "Shipping Insurance",
			"charge_qty" => 1,
			"charge_notes" => "Covers damage in transit, loss of goods or non-delivery up to " . getCurrencySymbol($invoice["invoice_currency"], "UTF-8") . convertCurrency($invoice["products_offer"], $invoice["invoice_currency"], $invoice["invoice_currency"]) . " " . $invoice["invoice_currency"],
			"charge_offer" => $insure_amount,
			"charge_cost" => $insure_amount,
			"charge_online" => $insure_amount,
		);
		foreach($price_keys as $k2)
		{
			$invoice_charge["charge_" . $k2 . "au"] = convertCurrency($invoice_charge["charge_" . $k2], $invoice["invoice_currency"], "AUD");
			$invoice_charge["charge_" . $k2 . "us"] = convertCurrency($invoice_charge["charge_" . $k2], $invoice["invoice_currency"], "USD");
		}
		foreach($currency_keys as $k1) foreach($price_keys as $k2) $invoice["charges_" . $k2 . $k1] = bcadd($invoice["charges_" . $k2 . $k1], bcmul($invoice_charge["charge_qty"], $invoice_charge["charge_" . $k2 . $k1], 4), 4);
		$invoice_charges[] = $invoice_charge;
	}
}

/**
 * Add GST
 */
if ($invoice["supply_country"] == "AU" && $invoice["bill_country"] == "AU" && ($invoice["ship_country"] == "AU" || $invoice["shipping_method"] == "pickup"))
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
		"charge_offer" => convertCurrency(bcmul($percent, $affected, 2), $invoice["invoice_currency"]),
		"charge_cost" => convertCurrency(bcmul($percent, $affected, 2), $invoice["invoice_currency"]),
		"charge_online" => convertCurrency(bcmul($percent, $affected, 2), $invoice["invoice_currency"]),
	);

	$rounded = bcmul(bcdiv($invoice_charge["charge_offer"], "0.05", 0), "0.05", 2);
	$diff = bcsub($invoice_charge["charge_offer"], $rounded, 2);
	if (bccomp($diff, bcdiv("0.05", "2", 3), 3) < 0) $invoice_charge["charge_offer"] = $rounded;
	else $invoice_charge["charge_offer"] = bcadd($rounded, "0.05", 2);
	$invoice_charge["charge_cost"] = $invoice_charge["charge_online"] = $invoice_charge["charge_offer"];

	foreach($price_keys as $k2)
	{
		$invoice_charge["charge_" . $k2 . "au"] = convertCurrency($invoice_charge["charge_" . $k2], $invoice["invoice_currency"], "AUD");
		$invoice_charge["charge_" . $k2 . "us"] = convertCurrency($invoice_charge["charge_" . $k2], $invoice["invoice_currency"], "USD");
	}
	foreach($currency_keys as $k1) foreach($price_keys as $k2) $invoice["charges_" . $k2 . $k1] = bcadd($invoice["charges_" . $k2 . $k1], bcmul($invoice_charge["charge_qty"], $invoice_charge["charge_" . $k2 . $k1], 4), 4);
	$invoice_charges[] = $invoice_charge;
}

/**
 * Add any gift vouchers
 */
if (function_exists("voucherSearch") || moduleLoad("voucher"))
{
	$criteria = $_GLOBALS["criteria_options"]["voucher"];
	$criteria["member_id"] = $_GLOBALS["member"]["id"];
	$life_vouchers = voucherSearch(array_merge($criteria, array("expiry_date" => "0000-00-00 00:00:00")));
	$vouchers = array_merge(
		voucherSort(array_diff(voucherSearch($criteria), $life_vouchers), "expiry_date"),
		array_reverse(voucherSort($life_vouchers, "amount"))
	);
	$total = $invoice["products_offer"];

	$now_time = substr(formatSQLDateTime($_SERVER["REQUEST_TIME"]), 1, -1);
	foreach($vouchers as $voucher_id)
	{
		$voucher = voucherFromDatabase($voucher_id);
		if ($voucher["expiry_date"] == "0000-00-00 00:00:00" || strcmp($voucher["expiry_date"], $now_time) > 0)
		{
			$voucher_amount = convertCurrency($voucher["amount"], $voucher["currency"], $invoice["invoice_currency"]);

			$row = $DB->query_first("SELECT * FROM `highwaygroup`.`gift_terms` WHERE `id` = " . intval($voucher["terms"]) . " LIMIT 1", MYSQL_ASSOC);
			if (bccomp($voucher_amount, $total, 2) < 0 && (
				!$row
				|| ($row["type"] == "min" && bccomp(convertCurrency($row["amount"], $voucher["currency"], $invoice["invoice_currency"]), $invoice["products_offer"], 2) <= 0)
				|| ($row["type"] == "max" && bccomp(convertCurrency($row["amount"], $voucher["currency"], $invoice["invoice_currency"]), $invoice["products_offer"], 2) >= 0)
			))
			{
				$total = bcsub($total, $voucher_amount, 2);
				$invoice_charge = array(
					"charge_count" => $count,
					"charge_rank" => $count++,
					"charge_id" => 22,
					"charge_name" => "Gift Voucher (" . voucherFormatNumber($voucher_id) . ")",
					"charge_qty" => 1,
					"charge_notes" => getCurrencySymbol($invoice["invoice_currency"], "UTF8") . $voucher_amount . " " . $invoice["invoice_currency"] . " (" . getCurrencySymbol($voucher["currency"], "UTF8") . convertCurrency($voucher["amount"], $voucher["currency"]) . " " . $voucher["currency"] . ") will be processed with this invoice." . (($voucher["expiry_time"] != 0) ? ("\n\nExpiry Date: " . date("jS F, Y", $voucher["expiry_time"])) : ""),
					"charge_offer" => "-" . $voucher_amount,
					"charge_cost" => "-" . $voucher_amount,
					"charge_online" => "-" . $voucher_amount,

					"voucher_id" => $voucher_id,
				);
				foreach($price_keys as $k2)
				{
					$invoice_charge["charge_" . $k2 . "au"] = convertCurrency($invoice_charge["charge_" . $k2], $invoice["invoice_currency"], "AUD");
					$invoice_charge["charge_" . $k2 . "us"] = convertCurrency($invoice_charge["charge_" . $k2], $invoice["invoice_currency"], "USD");
				}
				foreach($currency_keys as $k1) foreach($price_keys as $k2) $invoice["charges_" . $k2 . $k1] = bcadd($invoice["charges_" . $k2 . $k1], bcmul($invoice_charge["charge_qty"], $invoice_charge["charge_" . $k2 . $k1], 4), 4);
				$invoice_charges[] = $invoice_charge;
			}
		}
	}
}

/**
 * Disable invoice if freight offer selected
 */
if ($invoice["shipping_method"] == "offer")
{
	$invoice["invoice_status"] = "inquiry";
}

/**
 * Disable credit card for arcade machine purchases
 */
foreach($invoice_products as $invoice_product) if ($invoice_product["product_section"] == 1)
{
	if (in_array($invoice["paymenttype_id"], array("AMEX", "VISA", "MASTERCARD"))) $invoice["paymenttype_id"] = "";
	if (is_array($payment_methods)) foreach($payment_methods as $k => $method) if (in_array($method["payment_method_id"], array("AMEX", "VISA", "MASTERCARD"))) unset($payment_methods[$k]);
	break;
}

/**
 * Disable bpay for non-Australian customers or website
 */
if (($invoice["bill_country"] != "AU" && $invoice["ship_country"] != "AU") || $_GLOBALS["site_settings"]["supply_country"] != "AU")
{
	if (in_array($invoice["paymenttype_id"], array("BPAY"))) $invoice["paymenttype_id"] = "";
	foreach($payment_methods as $k => $method) if ($method["payment_method_id"] == "BPAY") unset($payment_methods[$k]);
}

/**
 * Check payment method price ranges
 */
$au_am = bcadd($invoice["products_offerau"], bcadd($invoice["ship_offerau"], $invoice["charges_offerau"], 2), 2);
$us_am = bcadd($invoice["products_offerus"], bcadd($invoice["ship_offerus"], $invoice["charges_offerus"], 2), 2);
if (is_array($payment_methods)) foreach($payment_methods as $k => $method)
{
	$key = strtolower($method["payment_method_id"] . "_minimum_au");
	if (array_key_exists($key, $_GLOBALS["site_settings"]) && bccomp($au_am, $_GLOBALS["site_settings"][$key], 2) < 0)
	{
		if ($invoice["paymenttype_id"] == $method["payment_method_id"]) $invoice["paymenttype_id"] = "";
		unset($payment_methods[$k]);
		continue;
	}
	$key = strtolower($method["payment_method_id"] . "_maximum_au");
	if (array_key_exists($key, $_GLOBALS["site_settings"]) && bccomp($au_am, $_GLOBALS["site_settings"][$key], 2) > 0)
	{
		if ($invoice["paymenttype_id"] == $method["payment_method_id"]) $invoice["paymenttype_id"] = "";
		unset($payment_methods[$k]);
		continue;
	}
	$key = strtolower($method["payment_method_id"] . "_minimum_usd");
	if (array_key_exists($key, $_GLOBALS["site_settings"]) && bccomp($us_am, $_GLOBALS["site_settings"][$key], 2) < 0)
	{
		if ($invoice["paymenttype_id"] == $method["payment_method_id"]) $invoice["paymenttype_id"] = "";
		unset($payment_methods[$k]);
		continue;
	}
	$key = strtolower($method["payment_method_id"] . "_maximum_usd");
	if (array_key_exists($key, $_GLOBALS["site_settings"]) && bccomp($us_am, $_GLOBALS["site_settings"][$key], 2) > 0)
	{
		if ($invoice["paymenttype_id"] == $method["payment_method_id"]) $invoice["paymenttype_id"] = "";
		unset($payment_methods[$k]);
		continue;
	}
}
else $invoice["paymenttype_id"] = "";

/**
 * Check credit
 */

/**
 * Add Payment Processing Fee
 */
if (is_array($payment_methods) && $invoice["paymenttype_id"] != "") foreach($payment_methods as $method) if ($invoice["paymenttype_id"] == $method["payment_method_id"])
{
	if ($method["payment_charge_percent"] == 0.0) break;

	$affected = bcadd($invoice["products_offer"], bcadd($invoice["ship_offer"], $invoice["charges_offer"], 2), 2);
	$percent = bcmul($method["payment_charge_percent"], "0.01", 4);

	$amount = convertCurrency(bcmul($percent, $affected, 2), $invoice["invoice_currency"]);
	$invoice_charge = array(
		"charge_count" => $count,
		"charge_rank" => $count,
		"charge_id" => 0,
		"charge_name" => $method["payment_method_name"] . " Processing Fee (" . $method["payment_charge_percent"] . "%)",
		"charge_qty" => 1,
		"charge_notes" => "",
		"charge_offer" => $amount,
		"charge_cost" => $amount,
		"charge_online" => $amount,
	);

	$row = $DB->query_first("SELECT * FROM `highwaygroup`.`charges` WHERE `site_id` = " . intval($_GLOBALS["site_settings"]["site_id"]) . " AND `payment_method_link` = " . formatSQLString($invoice["paymenttype_id"]) . " LIMIT 1");
	if (!$row) $row = $DB->query_first("SELECT * FROM `highwaygroup`.`charges` WHERE `site_id` = 0 AND `payment_method_link` = " . formatSQLString($invoice["paymenttype_id"]) . " LIMIT 1");
	if ($row)
	{
		$amount = convertCurrency(bcadd(bcmul(bcmul($row["charge_percent"], "0.01", 4), $affected, 2), convertCurrency($row["charge_amount"], $row["charge_amount_currency"], $invoice["invoice_currency"]), 2), $invoice["invoice_currency"]);
		if (bccomp($row["charge_rounding"], "0.0000", 4) > 0)
		{
			$rounded = bcmul(bcdiv($amount, $row["charge_rounding"], 0), $row["charge_rounding"], 2);
			$diff = bcsub($amount, $rounded, 2);
			if (bccomp($diff, bcdiv($row["charge_rounding"], "2", 3), 3) < 0) $amount = $rounded;
			else $amount = bcadd($rounded, $row["charge_rounding"], 2);
			//$raw_sum = bcadd($invoice["products_offer"], bcadd($invoice["ship_offer"], bcadd($invoice["charges_offer"], $amount, 2), 2), 2);
			//$amount = bcadd($amount, bcdiv(bcmod(bcmul($amount, "100.0", 0), bcmul($row["charge_rounding"], "100.0", 0)), "100.0", 2), 2);
		}
		$invoice_charge = array(
			"charge_count" => $count,
			"charge_rank" => $count,
			"charge_id" => $row["charge_id"],
			"charge_name" => $row["charge_name"],
			"charge_qty" => 1,
			"charge_notes" => "",
			"charge_offer" => $amount,
			"charge_cost" => $amount,
			"charge_online" => $amount,
		);
	}

	$count++;

	foreach($price_keys as $k2)
	{
		$invoice_charge["charge_" . $k2 . "au"] = convertCurrency($invoice_charge["charge_" . $k2], $invoice["invoice_currency"], "AUD");
		$invoice_charge["charge_" . $k2 . "us"] = convertCurrency($invoice_charge["charge_" . $k2], $invoice["invoice_currency"], "USD");
	}
	foreach($currency_keys as $k1) foreach($price_keys as $k2) $invoice["charges_" . $k2 . $k1] = bcadd($invoice["charges_" . $k2 . $k1], bcmul($invoice_charge["charge_qty"], $invoice_charge["charge_" . $k2 . $k1], 4), 4);
	$invoice_charges[] = $invoice_charge;

	break;
}

/**
 * Add Total Rounding
 */
$excess = bcmod(bcmul("100", bcadd($invoice["products_offer"], bcadd($invoice["ship_offer"], $invoice["charges_offer"], 2), 2), 0), "5");
if ($invoice["invoice_currency"] == "AUD" && $invoice["paymenttype_id"] == "CASH" && bccomp($excess, "0", 0) > 0)
{
	$invoice_charge = array(
		"charge_count" => $count,
		"charge_rank" => $count++,
		"charge_id" => 3,
		"charge_name" => "Cash Rounding",
		"charge_qty" => 1,
		"charge_notes" => "",
		"charge_offer" => convertCurrency(bcmul("0.01", bcsub((bccomp($excess, "3", 0) < 0) ? "0" : "5", $excess, 0), 2), $invoice["invoice_currency"]),
		"charge_cost" => convertCurrency("0.00", $invoice["invoice_currency"]),
		"charge_online" => convertCurrency(bcmul("0.01", bcsub((bccomp($excess, "3", 0) < 0) ? "0" : "5", $excess, 0), 2), $invoice["invoice_currency"]),
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
if ($_GLOBALS["site_settings"]["site_id"] == 2 && $_GLOBALS["site_settings"]["dom_int"] == "int" && $invoice["def_country"] != $invoice["supply_country"])
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
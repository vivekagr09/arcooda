<?php

	function generic_zone($courier, $form_country_code)
	{
		global $DB;
		$prop_id = $courier;
		$generic_zone_query = $DB->query_first("SELECT * FROM shipping_countries WHERE prop_id = '$prop_id' AND code = '$form_country_code'");
		$generic_zone = $generic_zone_query[zone];
		return $generic_zone;
	}
	function generic_base_currency($courier)
	{
		//check the currency for which the data was entered
		global $DB;

		$generic_base_currency_query = $DB->query_first("SELECT * FROM shipping_services WHERE prop_id = '$courier'");
		$generic_base_currency = $generic_base_currency_query[base_currency];
		return $generic_base_currency;
	}
	function service_available($courier, $form_country_code, $form_weight)
	{
		$zone = generic_zone($courier, $form_country_code);
		if($zone == "")
			return "NA";
		$weightlim = generic_weightlimit($courier, $form_country_code);
		if($weightlim > 0)
		{
			if($form_weight > $weightlim)
			{
				$str = "Overweight by ";
				$overweight = $form_weight - $weightlim;
				$str .= $overweight;
				$str .= "kg";
				return  $str;
			}
			return "true";
		}
		else
			return "true";
	}
	function generic_price($courier, $form_country_code, $form_weight, $client_type)
	{
		global $DB;
		$zone = generic_zone($courier, $form_country_code);
		//sql query for generic zone

			//find price
			$str = "shipping_";
			$str .= $courier;
			$sql = "SELECT *, (shipping_weight - $form_weight) AS temp FROM $str WHERE (shipping_weight - $form_weight) >= 0 ORDER BY temp";
			$generic_price_query = $DB->query($sql);

			if($DB->num_rows($generic_price_query) == 0)
				return -1;

			$row = $DB->fetch_array($generic_price_query);
			$generic_price = $row[$zone];

			//query surcharge and apply if neccesary
			$generic_price = $generic_price + generic_surcharge($courier, $form_country_code);

			//apply discounts
			if ($client_type == 'customer')
			{
				$price = generic_customer_discount($courier, $generic_price);
				$generic_price = priceAfterHandling($courier, $price);
			}
			if ($client_type == 'store')
			{
				$price = generic_store_discount($courier, $generic_price);
				$generic_price = priceAfterHandling($courier, $price);
			}
			return $generic_price;
	}
	function priceAfterHandling($courier, $price)
	{
		global $DB;
		$courierRow = $DB->query_first("SELECT * FROM shipping_services WHERE prop_id = '$courier'");

		$asPerc = $courierRow[hndasperc];
		$handling = $courierRow[handling];

		if($handling != '' && $handling != 0)
		{
			$hnd = $handling;
		}
		if($asPerc != '' && $asPerc != 0)
		{
			$asPerc = $price * ($asPerc / 100);
		}

		$price = $price + $hnd + $asPerc;
		return $price;
	}

	function generic_surcharge($courier, $form_country_code)
	{
		global $DB;
		$prop_id = $courier;

		$generic_surcharge_query = $DB->query_first("SELECT * FROM shipping_countries WHERE prop_id = '$prop_id' AND code = '$form_country_code'");
		$surcharge = $generic_surcharge_query[surcharge];

		return $surcharge;
	}
	function generic_tit($courier, $form_country_code)
	{
		global $DB;
		$prop_id = $courier;
		//sql query for generic zone
		$generic_tit_query = $DB->query_first("SELECT * FROM shipping_countries WHERE prop_id = '$prop_id' AND code = '$form_country_code'");

		//query time in transit
		$generic_tit = /*intval(*/$generic_tit_query['tit']/*)*/;
		if ($generic_tit != '')
		{
			$generic_tit .= " Working Days";
		}
		return $generic_tit;
	}
	function generic_weightlimit($courier, $form_country_code)
	{
		global $DB;
		$prop_id = $courier;
		$generic_weightlimit_query = $DB->query_first("SELECT * FROM shipping_countries WHERE prop_id = '$prop_id' AND code = '$form_country_code'");
		$generic_weightlimit = $generic_weightlimit_query[weightlimit];
		return $generic_weightlimit;
	}
	function generic_customer_discount($courier, $price)
	{
		global $DB;
		//discount query
		$generic_discount_query = $DB->query_first("SELECT * FROM shipping_services WHERE prop_id = '$courier'");
		$generic_customer_discount = $generic_discount_query[customer_discount];
		//turn into percentages
		$generic_customer_discount = $price - ($price * ($generic_customer_discount * (1 / 100)));
		return $generic_customer_discount;
	}

	function generic_store_discount($courier, $price)
	{
		global $DB;
		//discount query
		$generic_discount_query = $DB->query_first("SELECT * FROM shipping_services WHERE prop_id = '$courier'");
		$generic_store_discount = $generic_discount_query[store_discount];
		//turn into percentages
		$generic_store_discount = $price - ($price * ($generic_store_discount * (1 / 100)));
		return $generic_store_discount;
	}

	function getCourierQuotes($from,$to,$cartWeight,$currency)
	{
		global $DB;

		$cartWeight = floatval($cartWeight);
		if($cartWeight <= 0)
			return array();

		$sql = "SELECT scp.* FROM shipping_services scp
				WHERE scp.source='$from' ORDER BY scp.name";
		$result = $DB->query($sql);
		$services = array();
		while($row = $DB->fetch_array($result))
		{
			 $temp['name'] = $row['name'];
			 $temp['currency'] = $row['base_currency'];
			 $temp['custDiscount'] = $row['customer_discount'];
			 $temp['storeDiscount'] = $row['store_discount'];
			 $services[$row['prop_id']] = $temp;
		}
		$quoteArray = array();
		foreach($services AS $serviceID => $serviceArray)
		{
			$sql = "SELECT * FROM shipping_countries WHERE prop_id=$serviceID AND code='$to' AND weightlimit >= $cartWeight";
			$result = $DB->query($sql);
			while($row = $DB->fetch_array($result))
			{
				$sql = "SELECT ".$row['zone']." AS price, (shipping_weight - $cartWeight) AS temp FROM shipping_$serviceID
						WHERE (shipping_weight - $cartWeight) >= 0 ORDER BY temp";
				$temp = $DB->query_first($sql);
				$price = $temp['price'];
				$price -= ($serviceArray['custDiscount'] / 100);
				$price = showPriceFrom($price,$serviceArray['currency'],$currency);
				$price .= " ".$currency;
				if($row['surcharge'] == 'y')
					$price .= "<font size=\"-2\"> (plus surcharge)</font>";
				if($row['tit'] == "")
					$row['tit'] = "N/A";
				$courierName = $serviceArray['name'];
				$quoteArray[] = array('courierID' => $serviceID, 'name' => $courierName, 'price' => $price, 'tit' => $row['tit']);
			}
		}
		return $quoteArray;
	}

?>

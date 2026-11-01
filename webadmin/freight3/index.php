<?php
include_once __DIR__ . '/common.inc.php';

$action = $posted['action'];
$id = intval($posted['id']);

//Update Actions
switch ($action)
{
case 'do_add':
	unset($posted[$section_handler->section_name]['id']);
case 'do_edit':
case 'do_edit_destinations':
case 'do_edit_prices':
	if (is_array($posted[$section_handler->section_name]))
	{
		$posted_item = $posted[$section_handler->section_name];

		$item = $section_handler->fromPost($posted_item);
		$id = $section_handler->toDatabase($item);
		$posted_item["id"] = $id;
		$posted["id"] = $id;

		if ($posted_item["comment"] != "") $section_handler->commentToDatabase($id, $posted_item["comment"]);

		if (is_array($posted_item["zones"]))
		{
			$posted_zones = $posted_item["zones"];
			$handled_zones = array();
			foreach($posted_zones as $key_zone => $posted_zone) if (is_numeric($key_zone))
			{

				$zone = $section_handler->zoneFromPost($posted_zone["id"], $posted_zone);
				if ($zone["id"] != 0) $handled_zones[] = $zone["id"];
				else unset($zone["id"]);
				if ($id != 0) $zone["service_id"] = $id;
				unset($zone["countries"]);
				unset($zone["locations"]);
				unset($zone["rates"]);
				$zone_id = $section_handler->zoneToDatabase($zone);

				if (is_array($posted_zone["countries"]))
				{
					$posted_countries = $posted_zone["countries"];
					$handled_countries = array();
					foreach($posted_countries as $key_country => $posted_country) if (is_numeric($key_country))
					{

						$country = $section_handler->zoneCountryFromPost($posted_country["id"], $posted_country);
						if ($country["id"] != 0) $handled_countries[] = $country["id"];
						else unset($country["id"]);
						$country["zone_id"] = $zone_id;
						$section_handler->zoneCountryToDatabase($country);

					}
					if ($posted_countries["affected"] != "") foreach(preg_split("/[^0-9]+/", $posted_countries["affected"]) as $k) if (!in_array($k, $handled_countries)) $section_handler->zoneCountryDelete($k);
				}
				if (is_array($posted_zone["locations"]))
				{
					$posted_locations = $posted_zone["locations"];
					$handled_locations = array();
					foreach($posted_locations as $key_location => $posted_location) if (is_numeric($key_location))
					{

						$location = $section_handler->zoneLocationFromPost($posted_location["id"], $posted_location);
						if ($location["id"] != 0) $handled_locations[] = $location["id"];
						else unset($location["id"]);
						$location["zone_id"] = $zone_id;
						$section_handler->zoneLocationToDatabase($location);

					}
					if ($posted_locations["affected"] != "") foreach(preg_split("/[^0-9]+/", $posted_locations["affected"]) as $k) if (!in_array($k, $handled_locations)) $section_handler->zoneLocationDelete($k);
				}
				if (is_array($posted_zone["rates"]))
				{
					$posted_rates = $posted_zone["rates"];
					$handled_rates = array();
					foreach($posted_rates as $key_rate => $posted_rate) if (is_numeric($key_rate))
					{

						$rate = $section_handler->zoneRateFromPost($posted_rate["id"], $posted_rate);
						if ($rate["id"] != 0) $handled_rates[] = $rate["id"];
						else unset($rate["id"]);
						$rate["zone_id"] = $zone_id;
						$section_handler->zoneRateToDatabase($rate);

					}
					if ($posted_rates["affected"] != "") foreach(preg_split("/[^0-9]+/", $posted_rates["affected"]) as $k) if (!in_array($k, $handled_rates)) $section_handler->zoneRateDelete($k);
				}
			}
			if ($posted_zones["affected"] != "") foreach(preg_split("/[^0-9]+/", $posted_zones["affected"]) as $k) if (!in_array($k, $handled_zones)) $section_handler->zoneDelete($k);
		}
	}

	if (in_array($action, array("do_add", "do_edit")))
	{
		if ($posted["return_message"] != "") $action = "select";
		else $action = "edit";
	}
	else $action = 'select_zone';

	break;

case 'activate':
case 'deactivate':
case 'toggle':
	$active_id = explode(",", $section_handler->getValue($id, "active_id"));
	if (($k = array_search($_GLOBALS["site_settings"]["site_id"], $active_id)) !== false) unset($active_id[$k]);
	else $active_id[] = $_GLOBALS["site_settings"]["site_id"];
	$active_id = array_unique($active_id);
	$section_handler->setValue($id, "active_id", implode(",", $active_id));

	$action = "list";
	break;

case 'unlock':
	$section_handler->setLock($id);

	$action = 'list';
	break;
}

$get_vars = $posted;
foreach($get_vars as $k => $v) if (is_array($v)) unset($get_vars[$k]);

//Redirect Actions
switch ($action)
{
case 'add':
	header('Location: add.php?' . http_build_query($get_vars));
	break;

case 'edit':
	$item_lock = $section_handler->getLock($id);
	if ($item_lock === false || $item_lock['staff_id'] == $_GLOBALS['staff']['id']) $section_handler->setLock($id, $_GLOBALS['staff']['id']);
	header('Location: edit.php?' . http_build_query($get_vars));
	break;

case 'destinations':
case 'edit_destinations':
	header('Location: destinations.php?' . http_build_query($get_vars));
	break;

case 'international':
	header('Location: international.php?' . http_build_query($get_vars));
	break;

case 'prices':
case 'edit_prices':
	header('Location: prices.php?' . http_build_query($get_vars));
	break;

case 'search':
	header('Location: search.php?' . http_build_query($get_vars));
	break;

/*
case 'select':
	header('Location: select.php?' . http_build_query($get_vars));
	break;
*/

case 'select_zone':
	header('Location: select_zone.php?' . http_build_query($get_vars));
	break;

case 'list':
default:
	unset($get_vars['id']);
	header('Location: list.php?' . http_build_query($get_vars));
}
die();
?>
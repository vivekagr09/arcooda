<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = "destinations";

$zone_id = intval($posted['zone_id']);
$zone = $section_handler->zoneFromDatabase($zone_id);
$zone_countries = $section_handler->zoneCountriesFromDatabase($zone_id);
$zone_locations = $section_handler->zoneLocationsFromDatabase($zone_id);
$zone_rates = $section_handler->zoneRatesFromDatabase($zone_id);
$zone_key = $section_handler->section_name . "[zones][0]";

$id = $zone["service_id"];
$item = $section_handler->fromDatabase($id);

$country_list = $section_handler->getOptionList("country");
foreach($zone_countries as $k => $v) $zone_countries[$k]["country_name"] = $country_list[$v["country"]];

$location_list = $section_handler->countryLocationsFromDatabase($item["service_from"]);
foreach($zone_locations as $k => $v) $zone_locations[$k]["location_name"] = $location_list[$v["location_id"]];

?>
<script type="text/javascript">
//<![CDATA[
$(function() {
	var data = <?php jecho(array_merge($zone, array("countries" => $zone_countries, "locations" => $zone_locations, "rates" => $zone_rates, "return_message" => $posted["return_message"]))) ?>;
	if (window.opener != null && window.opener.setFreightServiceZoneData != null) window.opener.setFreightServiceZoneData(data);
	else alert("Unable to send zone back to form");
	window.close();
});
//]]>
</script>

<p>Action Completed</p>
<input type="button" value="Close Window" onclick="window.close()" />

<?php

include_once __DIR__ . '/footer.inc.php';
?>
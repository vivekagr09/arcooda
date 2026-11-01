<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = "destinations";

$zone_id = intval($posted['zone_id']);
$zone = $section_handler->zoneFromDatabase($zone_id);
$zone_countries = $section_handler->zoneCountriesFromDatabase($zone_id);
$zone_locations = $section_handler->zoneLocationsFromDatabase($zone_id);
$zone_key = $section_handler->section_name . "[zones][0]";

$id = $zone["service_id"];
$item = $section_handler->fromDatabase($id);

$doedit_url = $get_vars;
$doedit_url['action'] = 'do_edit_destinations';
$doedit_url = 'index.php?' . http_build_query($doedit_url);
?>
<h1><?php hecho($zone["name"]) ?></h1>

<form accept-charset="utf-8" autocomplete="off" method="post" id="editForm" action="<?php hecho($doedit_url) ?>" onsubmit="return validateForm();">
	<input type="hidden" name="<?php hecho($section_handler->section_name . "[id]") ?>" value="<?php hecho($id) ?>" />
	<input type="hidden" name="<?php hecho($section_handler->section_name . "[zones][affected]") ?>" value="<?php hecho($zone_id) ?>" />
	<input type="hidden" name="<?php hecho($zone_key . "[id]") ?>" value="<?php hecho($zone["id"]) ?>" />
<?php
include_once ('destinations.tpl.php');
?>

<div style="text-align:center;"><input type="button" onclick="this.form.submit()" value="Submit Changes" /></div>

</form>
<?php

include_once __DIR__ . '/footer.inc.php';
?>
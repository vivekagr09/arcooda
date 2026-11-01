<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = "prices";

$zone_id = intval($posted['zone_id']);
$zone = $section_handler->zoneFromDatabase($zone_id);
$zone_rates = $section_handler->zoneRatesFromDatabase($zone_id);
$zone_key = $section_handler->section_name . "[zones][0]";

$id = $zone["service_id"];
$item = $section_handler->fromDatabase($id);

$doedit_url = $get_vars;
$doedit_url['action'] = 'do_edit_prices';
$doedit_url = 'index.php?' . http_build_query($doedit_url);
?>
<h1><?php hecho($zone["name"]) ?></h1>

<form accept-charset="utf-8" autocomplete="off" method="post" id="editForm" action="<?php hecho($doedit_url) ?>" onsubmit="return validateForm();">
	<input type="hidden" name="<?php hecho($section_handler->section_name . "[id]") ?>" value="<?php hecho($id) ?>" />
	<input type="hidden" name="<?php hecho($section_handler->section_name . "[zones][affected]") ?>" value="<?php hecho($zone_id) ?>" />
	<input type="hidden" name="<?php hecho($zone_key . "[id]") ?>" value="<?php hecho($zone["id"]) ?>" />
<?php
include_once ('prices.tpl.php');
?>

<div style="text-align:center;"><input type="button" onclick="this.form.submit()" value="Submit Changes" /></div>

</form>
<?php

include_once __DIR__ . '/footer.inc.php';
?>
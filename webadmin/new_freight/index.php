<?php
include_once ('common.inc.php');

$action = $posted['action'];

//Update Actions
switch ($action)
{
	// any processing scenarios to go here...
}

$get_vars = $posted;
$keep_only = array('advanced', 'service_from');
if (count($get_vars) > 0) foreach($get_vars as $key => $value) if (is_array($value) || !in_array($key, $keep_only)) unset($get_vars[$key]);

//Redirect Actions
switch ($action)
{
case 'list':
	header('Location: list.php?' . http_build_query($get_vars));
	break;

case 'inventory':
	header('Location: inventory.php?' . http_build_query($get_vars));
	break;

case 'air':
	header('Location: air_calculator.php?' . http_build_query($get_vars));
	break;

case 'sea':
	header('Location: sea_calculator.php?' . http_build_query($get_vars));
	break;

case 'domestic':
	header('Location: domestic_calculator.php?' . http_build_query($get_vars));
	break;

case 'international':
default:
	header('Location: international_calculator.php?' . http_build_query($get_vars));
	break;
}
?>
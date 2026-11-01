<?php
include_once 'common.inc.php';

$result=$posted;

switch($posted['action'])
{
case 'insuranceCalculateAmount':
	$timestamp = 0;
	if ($posted["timestamp"] != 0) $timestamp = $posted["timestamp"];
	$result['result'] = insuranceCalculateAmount($posted['id'], $posted["coverage_aud"], $timestamp);
	break;
}

header('Content-Type: text/javascript');
jecho($result);
?>
<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/webadmin/common.inc.php";

moduleOverride("insurance", dirname(__FILE__) . "/functions/query_insurance.inc.php");
moduleLoad("insurance");

//Lock all actions to insurance_id 1
$_GET['insurance_id'] = 1;

$all_staff_list=array();
$query=$DB->query("SELECT id,first_name,last_name FROM highwaygroup.users");
while($row=$DB->fetch_assoc($query))
	$all_staff_list[$row['id']]=trim($row['first_name'].' '.$row['last_name']);
?>
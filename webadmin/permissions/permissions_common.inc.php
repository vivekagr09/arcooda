<?php
if(!isset($_SERVER['REQUEST_TIME'])) $_SERVER['REQUEST_TIME']=time();

include_once($_SERVER['DOCUMENT_ROOT'].'/webadmin/common.inc.php');
include_once('functions/query_permissions.inc.php');

include_once('permissions_values.inc.php');
?>
<?php
include_once('forwarders_common.inc.php');
include_once('templates/forwarders_header.tpl.php');

$action='add';
$forwarder_id=0;

$forwarder=array();
$forwarder_warehouses=array();
$forwarder_contacts=array();
$forwarder_accounts=array();
$forwarder_comments=array();
$forwarder_quotes=array();

?>
<h2>Add New Forwarder</h2>
<?php

include 'templates/forwarders_addedit.tpl.php';

include_once('templates/forwarders_footer.tpl.php');
?>
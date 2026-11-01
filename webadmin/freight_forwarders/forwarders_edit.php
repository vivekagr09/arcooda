<?php
include_once('forwarders_common.inc.php');
include_once('templates/forwarders_header.tpl.php');

$action='edit';
$forwarder_id=intval($posted['id']);

$forwarder_lock=forwardersGetLock($forwarder_id);

$forwarder=forwardersFromDatabase($forwarder_id);
$forwarder_warehouses=forwardersWarehousesFromDatabase($forwarder_id);
$forwarder_contacts=forwardersContactsFromDatabase($forwarder_id);
$forwarder_accounts=forwardersAccountsFromDatabase($forwarder_id);
$forwarder_comments=array();
$forwarder_quotes=forwardersQuotesFromDatabase($forwarder_id);

//$forwarder_comments=forwardersCommentsFromDatabase($forwarder_id);
?>
<h2>Edit Forwarder</h2>
<?php

include 'templates/forwarders_addedit.tpl.php';

include_once('templates/forwarders_footer.tpl.php');
?>
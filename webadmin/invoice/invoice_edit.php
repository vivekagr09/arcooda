<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('asset'); //assetSearch
moduleLoad('forwarder'); //forwardersQuotesSearch
moduleLoad('member'); //memberContactsFromDatabase, memberCouriersFromDatabase, memberForwardersFromDatabase, memberNotifyPartiesFromDatabase, memberShippingAddressesFromDatabase, memberFromDatabase, memberRequestsFromDatabase, memberSpecialsFromDatabase, memberWarningsFromDatabase
moduleLoad('order');
moduleLoad('staff');

global $_GLOBALS;
global $DB;

$invoice_id   = intval($posted['invoice_id']);
$invoice      = invoiceFromDatabase($invoice_id);
$invoice_type = array_search($invoice['invoice_status'], array(
    'inquiry',
    'enquiry',
    'closed',
    'proforma',
    'sales',
    'processed',
    'cancelled',
    'return',
    'completed',
    'purchase',
));
if ($invoice_type < 3) {
    $display_inquiry = true;
}

if (1 == $invoice['site_id'] && 2 != $_GLOBALS['site_settings']['site_id']) {
    header('Location: https://www.highway.net.au/webadmin/invoice/invoice_edit.php?invoice_id=' . $invoice_id);
    die();
} elseif (20 == $invoice['site_id'] && 20 != $_GLOBALS['site_settings']['site_id']) {
    header('Location: https://www.arcadespareparts.com/webadmin/invoice/invoice_edit.php?invoice_id=' . $invoice_id);
    die();
} elseif (26 == $invoice['site_id'] && 26 != $_GLOBALS['site_settings']['site_id']) {
    header('Location: https://www.factoryclearancehouse.com/webadmin/invoice/invoice_edit.php?invoice_id=' . $invoice_id);
    die();
/*
} elseif (39 == $invoice['site_id'] && 39 != $_GLOBALS['site_settings']['site_id']) {
    header('Location: https://www.arcooda.com/webadmin/invoice/invoice_edit.php?invoice_id=' . $invoice_id);
    die();
*/
}

if (20 != $invoice['site_id'] && 20 == $_GLOBALS['site_settings']['site_id']) {
    header('Location: https://www.highwaygroup.net/webadmin/invoice/invoice_edit.php?invoice_id=' . $invoice_id);
    die();
} elseif (26 != $invoice['site_id'] && 26 == $_GLOBALS['site_settings']['site_id']) {
    header('Location: https://www.highwaygroup.net/webadmin/invoice/invoice_edit.php?invoice_id=' . $invoice_id);
    die();
} elseif (39 != $invoice['site_id'] && 39 == $_GLOBALS['site_settings']['site_id']) {
    header('Location: https://www.highwaygroup.net/webadmin/invoice/invoice_edit.php?invoice_id=' . $invoice_id);
    die();
}

include_once __DIR__ . '/header.inc.php';

$action     = 'edit';
$invoice_id = intval($posted['invoice_id']);

$invoice                = invoiceFromDatabase($invoice_id);
$invoice_lock           = invoiceGetLock($invoice_id);
$invoice_products       = invoiceProductsFromDatabase($invoice_id);
$invoice_charges        = invoiceChargesFromDatabase($invoice_id);
$invoice_courier_quotes = invoiceCourierQuotesFromDatabase($invoice_id);
$invoice_history        = invoiceHistorySearch(array('invoice_id' => $invoice_id, 'message_type_id' => array(1, 2, 3, 5, 6, 8, 9, 11, 12)));
$invoice_admin_notes    = invoiceHistorySearch(array('invoice_id' => $invoice_id, 'message_type_id' => 4));
$invoice_dashboard_notes = invoiceDashboardHistorySearch(array('invoice_id' => $invoice_id),'created_at');
$invoice_assets         = assetSearch('invoice', array('invoice_id' => $invoice_id));
$invoice_payments       = invoicePaymentsFromDatabase($invoice_id);
$invoice_porders        = (function_exists('ordersSearch')) ? ordersSearch(array('invoice_id' => $invoice_id)) : array();
$invoice_freight_quotes = array();
if (function_exists('forwardersQuotesSearchNEW')) {
    $invoice_freight_quotes = forwardersQuotesSearchNEW(array('invoice_id' => $invoice_id));
}
$invoice_survey         = invoiceSurveyFromDatabase($invoice_id);
$invoice_links          = array();

$invoice['history_count'] = count($invoice_history);
$invoice_history          = array_chunk($invoice_history, 5);
$invoice_history          = $invoice_history[0];

$DB->query(sprintf(
    "UPDATE `highwaygroup`.`invoices` SET `access_date` = NOW() WHERE `invoice_id` = %d LIMIT 1",
    $invoice_id
));
$member_shippingaddresses = memberShippingAddressesFromDatabase($invoice['member_id']);
$member_contacts          = memberContactsFromDatabase($invoice['member_id']);
$member_couriers          = memberCouriersFromDatabase($invoice['member_id']);
$member_forwarders        = memberForwardersFromDatabase($invoice['member_id']);
$member_information       = memberFromDatabase($invoice['member_id']);
$member_notifyparties     = memberNotifyPartiesFromDatabase($invoice['member_id']);
$member_requests          = memberRequestsFromDatabase($invoice['member_id']);
$member_specials          = (20 == $invoice['site_id'] || 22 == $invoice['site_id']) ? array() : memberSpecialsFromDatabase($invoice['member_id']);
$member_warnings          = memberWarningsFromDatabase($invoice['member_id']);
$invoice_addresses='';
if ($row = $DB->query_first(sprintf(
    "SELECT `id` FROM `invoice_address` WHERE  `flag`='0' AND `main`='y' LIMIT 1"
), MYSQLI_ASSOC)) {       
    $invoice_addresses = $row['id'];
}

$invoice_addresses = ($invoice['invoice_address'] != '' && $invoice['invoice_address'] != 0) ? $invoice['invoice_address'] : $invoice_addresses;
?>
<h2>Edit <?=($display_inquiry) ? 'Inquiry' : 'Invoice'?> <?=invoiceFormatNumber($invoice_id, $invoice['revision_id'], $invoice['site_id'])?> - <?=date(_('d/m/Y'))?></h2>
<?php

include __DIR__ . '/templates/addedit_invoice.tpl.php';

include_once __DIR__ . '/footer.inc.php';

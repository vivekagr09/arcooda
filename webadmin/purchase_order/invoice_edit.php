<?php
include_once __DIR__ . '/common.inc.php';

$order_id   = intval($posted['order_id']);

if (!purchaseOrderExists($order_id)) {
    redirect('/webadmin/inquiry/inquiry_admin.php?action=edit&inquiry_id=' . $order_id);
    die();
}

$invoice      = purchaseOrderFromDatabase($order_id);
$invoice_type = array_search($invoice['invoice_status'], array('enquiry', 'closed', 'proforma', 'sales', 'processed', 'cancelled'));
if ($invoice_type < 2) {
    $display_enquiry = true;
}

include_once __DIR__ . '/header.inc.php';

$action     = 'edit';
$order_id = intval($posted['order_id']);
$staff_id   = $_GLOBALS['staff']['id'];

$invoice                = purchaseOrderFromDatabase($order_id);
$invoice_lock           = purchaseOrderGetLock($order_id);
$invoice_products       = purchaseOrderProductsFromDatabase($order_id);
$invoice_charges        = purchaseOrderChargesFromDatabase($order_id);
$courier_list    = getCourierproviderList();
$invoice_courier_quotes = [];//invoiceCourierQuotesFromDatabase($order_id);
$invoice_history        = purchaseOrderHistorySearch(array('order_id' => $order_id, 'message_type_id' => array(
    INVOICE_MESSAGE_GENERAL,
    INVOICE_MESSAGE_TO_CUSTOMER,
    INVOICE_MESSAGE_FROM_CUSTOMER,
    INVOICE_MESSAGE_TO_STAFF,
    INVOICE_MESSAGE_INVOICE_MODIFIED,
    INVOICE_MESSAGE_ATTACHMENT_ADDED,
    INVOICE_MESSAGE_ATTACHMENT_REMOVED,
    INVOICE_MESSAGE_ENQUIRY_MODIFIED,
    INVOICE_MESSAGE_INVOICE_SENT
)));
$invoice_admin_notes    = purchaseOrderHistorySearch(array('order_id' => $order_id, 'message_type_id' => INVOICE_MESSAGE_ADMIN));
$invoice_assets         = assetSearch('purchaseorder', array('order_id' => $order_id,'active'=>''));
$invoice_payments       = purchaseorderPaymentsFromDatabase($order_id);
$invoice_porders        = array(); //ordersSearch(array('invoice_id'=>$invoice_id));
$invoice_freight_quotes = [];//forwardersQuotesSearch(array('invoice_id' => $invoice_id));

$invoice['history_count'] = count($invoice_history);
$invoice_history          = array_chunk($invoice_history, 5);
$invoice_history          = $invoice_history[0];

purchaseOrderSetValue($order_id, 'access_date', substr(formatSQLDateTime($_SERVER['REQUEST_TIME']), 1, -1));
$member_information = $member = supplierFromDatabase($invoice['supplier_id']);
$member_addresses   = supplierShippingFromDatabase($invoice['supplier_id']);
$member_notify      = [];//memberNotifyFromDatabase($invoice['supplier_id']);
$member_requests    = supplierRequestsFromDatabase($invoice['supplier_id']);
$member_contacts    = supplierContactsFromDatabase($invoice['supplier_id']);
$member_forwarders  = [];//memberForwardersFromDatabase($invoice['supplier_id']);
$member_couriers    = [];//memberCouriersFromDatabase($invoice['supplier_id']);

?>
<h2>Edit <?= ((strpos($invoice['invoice_number'], 'PECO#') !== false)) ? 'Purchase Order Enquiry' : 'Purchase Order' ?> <?= purchaseOrderFormatNumber($order_id, $invoice['revision_id'], $invoice['site_id']) ?> - <?= date('d/m/Y', $_SERVER['REQUEST_TIME']) ?></h2>
<?php

include __DIR__ . '/templates/addedit_invoice.tpl.php';

include_once __DIR__ . '/footer.inc.php';

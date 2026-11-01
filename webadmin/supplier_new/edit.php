<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action    = 'edit';
$supplier_id = $id = intval($posted['supplier_id']);
$staff_id  = $_GLOBALS['staff']['id'];

$supplier               = supplierFromDatabase($supplier_id);
$supplier_lock          = supplierGetLock($supplier_id);
$supplier_comments      = supplierCommentSearch(array('supplier_id' => $supplier_id, 'comment_type' => 'admin'));
$supplier_contacts      = supplierContactsFromDatabase($supplier_id);

$supplier_shipping      = supplierShippingFromDatabase($supplier_id);
$supplier_bank_account  = supplierBankFromDatabase($supplier_id);
$supplier_requests      = supplierCommentSearch(array('supplier_id' => $supplier_id, 'comment_type' => 'warning'));
if (count($supplier_comments) > 5) {
    $supplier_comments = reset(array_chunk($supplier_comments, 5));
}
$member_invoices=[];
$member_inquiries=[];
$supplier['last_invoice_date'] = supplierLastPurchase($supplier_id,['sales','proforma','processed','completed']);
$supplier['total_orders'] = supplierTotalPurchase($supplier_id,['sales','proforma','processed','completed']);
$supplier['last_inquiry_date'] = supplierLastPurchase($supplier_id,['working','supplier','enquiry','inquiry', 'closed','cancelled']);
$supplier['total_inquiries'] = supplierTotalPurchase($supplier_id,['working','supplier','enquiry','inquiry','closed','cancelled']);
$supplier['open_invoices'] = supplierTotalPurchase($supplier_id,['proforma','sales','return']);
$supplier['invoice_purchase_total'] = supplierPurchaseTotal($supplier_id,['sales','proforma','processed','completed']);

?>
<h2>Edit Supplier <?=supplierFormatNumber($supplier_id)?> - <?=date('d/m/Y', time())?></h2>
<?php

include __DIR__ . '/addedit.tpl.php';

include_once __DIR__ . '/footer.inc.php';

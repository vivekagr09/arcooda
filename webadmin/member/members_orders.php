<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceProductsFromDatabase, invoiceSort, invoiceSearch, invoiceFromDatabase, invoiceGetOptionList

$popup_window = true;
include_once __DIR__ . '/header.inc.php';

$member_id = intval($posted['member_id']);

$member       = memberFromDatabase($member_id);
$invoices_all = array_reverse(invoiceSort(invoiceSearch(array('member_id' => $member_id)), 'open_date'));
foreach ($invoices_all as $invoice_id) {
    $invoice_cache[$invoice_id]  = invoiceFromDatabase($invoice_id);
    $invoiceP_cache[$invoice_id] = invoiceProductsFromDatabase($invoice_id);
}
$paymentMethods = getPaymentMethodsList();
$status         = invoiceGetOptionList("invoice_action");

?>
<h2>Customer Inquiries/Invoices - <?=htmlspecialchars((empty($member['company_name'])) ? ($member['first_name'] . ' ' . $member['last_name']) : $member['company_name'])?></h2>
<?php

include __DIR__ . '/templates/list_orders.tpl.php';

?>
<br />
<br />
<?php

include_once __DIR__ . '/footer.inc.php';

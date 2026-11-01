<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceProductsFromDatabase, invoiceSort, invoiceSearch, invoiceFromDatabase

global $DB;

$popup_window = true;
include_once __DIR__ . '/header.inc.php';

$member_id = intval($posted['member_id']);

$member       = memberFromDatabase($member_id);
$all_invoices = $only_invoices = array_reverse(invoiceSort(invoiceSearch(array('member_id' => $member_id, 'invoice_status' => array('sales', 'proforma', 'processed'))), 'open_date'));
foreach ($all_invoices as $inv_id) {
    $invoice_cache[$inv_id]  = invoiceFromDatabase($inv_id);
    $invoiceP_cache[$inv_id] = invoiceProductsFromDatabase($inv_id);
}

$use_cur  = $member['currency'];
$tinvs    = count($only_invoices);
$payments = $alltogether = array();

if ($query = $DB->query(sprintf(
    "SELECT *, UNIX_TIMESTAMP(`payment_date`) AS `payment_time` FROM `highwaygroup`.`invoice_payments` WHERE `invoice_id` IN(%s) ORDER BY `payment_date` DESC",
    implode(', ', $only_invoices)
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $payments[] = $row;
    }
    $DB->free_result($query);
}

// now combine the invoices + the payments, so we can sort them by time...
foreach ($only_invoices as $invoice_id) {
    $tmp           = $invoice_cache[$invoice_id];
    $alltogether[] = $tmp['open_time'];
}
foreach ($payments as $row) {
    $alltogether[] = $row['payment_time'];
}

arsort($alltogether, SORT_NUMERIC);

$balance = $sale = $payby = 0;
// firstly calculate the balance..
foreach ($alltogether as $key => $time) {
    $payment = $invoice = array();
    if ($key < $tinvs) {
        $invoice_id = $only_invoices[$key];
        $invoice    = $invoice_cache[$invoice_id];
        $pay        = $invoice['invoice_offerau'];
        if ('AUD' != $use_cur) {
            $pay = convertCurrency($invoice['invoice_offer'], $invoice['invoice_currency'], $use_cur);
        }

        $balance += $pay;
        $sale += $pay;
    } else {
        $nkey       = $key - $tinvs;
        $payment    = $payments[$nkey];
        $invoice_id = $payment['invoice_id'];
        $pay        = $payment['payment_amountau'];
        if ('' == $pay || 'AUD' != $use_cur) {
            $pay = convertCurrency($payment['payment_amount'], $payment['payment_currency'], $use_cur);
        }

        $balance -= $pay;
        $payby += $pay;
    }
}
$sale         = convertCurrency($sale, $use_cur);
$payby        = convertCurrency($payby, $use_cur);
$balance      = convertCurrency($balance, $use_cur);
$company_name = ('' != $member['company_name']) ? $member['company_name'] : ($member['first_name'] . ' ' . $member['last_name']);

?>
<h2>Customer Account Statement - <?=$company_name?></h2>
<?php
include __DIR__ . '/templates/list_statement.tpl.php';
?>
<br /><br />
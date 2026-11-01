<?php
/**
 * @var mixed[] $posted
 * @var mixed[] $get_vars
 */
include_once __DIR__ . '/common.inc.php';

moduleLoad('asset'); //assetSearch
moduleLoad('invoice'); //invoiceProductsFromDatabase, invoiceSearch, invoiceFromDatabase, invoiceChunk, invoiceSort

global $DB;

include_once __DIR__ . '/header.inc.php';

$member_id = intval($posted['member_id']);

if (0 === $member_id) {
    die('No member id selected');
}

$member                   = memberFromDatabase($member_id);
$member_assets            = assetSearch('member', array('member_id' => $member_id));
$member_comments          = memberCommentsFromDatabase($member_id);
$member_contacts          = memberContactsFromDatabase($member_id);
$member_couriers          = memberCouriersFromDatabase($member_id);
$member_discounts         = memberDiscountsFromDatabase($member_id);
$member_forwarders        = memberForwardersFromDatabase($member_id);
$member_login             = memberLoginFromDatabase($member_id);
$member_logins            = memberLoginHistoryFromDatabase($member_id, '', 0);
$member_mailinglists      = memberMailingListFromDatabase($member_id);
$member_notifyparties     = memberNotifyPartiesFromDatabase($member_id);
$member_pricing           = memberSpecialPriceFromDatabase($member_id);
$member_requests          = memberRequestsFromDatabase($member_id);
$member_shippingaddresses = memberShippingAddressesFromDatabase($member_id);
$member_specials          = memberSpecialPriceFromDatabase($member_id);
$member_warnings          = memberWarningsFromDatabase($member_id);
$member_survey            = memberSurveyFromDatabase($member_id);
$member_serials           = memberSerialsFromDatabase($member_id);
$member_wanted            = memberWantedSearch(array('member_id' => $member_id));

$member_authentication = array();
if ($query = $DB->query(sprintf(
    "SELECT * FROM `highwaygroup`.`new_members_logins` WHERE `member_id` = %d",
    $member_id
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $member_authentication[] = $row;
    }
    $DB->free_result($query);
}

$chunked_logins = memberLoginsChunk($member_logins); // chunk them per site..
$member_lock    = memberGetLock($member_id);
if ('' == $member['currency']) {
    $member['currency'] = 'USD';
}

$invoice_cache = $invoiceP_cache = array();
$all_invoices  = invoiceSearch(array('member_id' => $member_id));
foreach ($all_invoices as $inv_id) {
    $invoice_cache[$inv_id]  = invoiceFromDatabase($inv_id);
    $invoiceP_cache[$inv_id] = invoiceProductsFromDatabase($inv_id);
}
$chunk     = invoiceChunk($all_invoices, 'invoice_status');
$inquiries = array();
if (is_array($chunk['inquiry'])) {
    $inquiries = $chunk['inquiry'];
}

$open_inquiries = $inquiries;
if (is_array($chunk['closed'])) {
    $inquiries = array_merge($inquiries, $chunk['closed']);
}

$inquiries     = array_reverse(invoiceSort($inquiries, 'open_date'));
$last_inquiry  = $invoice_cache[reset($inquiries)];
$open_invoices = array();
if (is_array($chunk['sales'])) {
    $open_invoices = $chunk['sales'];
}

if (is_array($chunk['proforma'])) {
    $open_invoices = array_merge($open_invoices, $chunk['proforma']);
}

// if (is_array($chunk['processed'])) {
//     $open_invoices = array_merge($open_invoices, $chunk['processed']);
// }

$invs = $open_invoices;
if (is_array($chunk['processed'])) {
    $invs = array_merge($invs, $chunk['processed']);
}

$only_invoices = $invs;
if (is_array($chunk['cancelled'])) {
    $invs = array_merge($invs, $chunk['cancelled']);
}

$total_invoices        = $invs;
$only_invoices_chunked = (is_array($only_invoices)) ? invoiceChunk($only_invoices, 'site_id') : array();
$processed_invoices    = (array_key_exists('processed', $chunk)) ? $chunk['processed'] : array();
$last_order            = array();
if (count($processed_invoices)) {
    $processed_invoices = array_reverse(invoiceSort($processed_invoices, 'open_date'));
    $last_order         = $invoice_cache[reset($processed_invoices)];
}
// now need to work out the customer statement + outstanding balance etc...
if ($only_invoices) {
    $use_cur  = (('AU' == $member['country']) ? 'AUD' : 'USD');
    $tinvs    = count($only_invoices);
    $payments = $alltogether = array();
    $sql      = "SELECT `p`.*, UNIX_TIMESTAMP(`p`.`payment_date`) AS `payment_time` FROM `highwaygroup`.`invoice_payments` AS `p` WHERE `p`.`invoice_id` IN (" . implode(',', $only_invoices) . ") ORDER BY `p`.`payment_date` DESC";
    $res      = $DB->query($sql);
    while ($row = $DB->fetch_assoc($res)) {
        $payments[] = $row;
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
    $sale    = convertCurrency($sale, $use_cur);
    $payby   = convertCurrency($payby, $use_cur);
    $balance = convertCurrency($balance, $use_cur);
}
$doadd_url           = $get_vars;
$doadd_url['action'] = 'do_edit';
$doadd_url           = '.?' . http_build_query($doadd_url);
$action              = 'edit';
?>
<h2>Edit Member Account</h2>
<form action="<?=htmlspecialchars($doadd_url)?>" method="post">
<?php
include __DIR__ . '/templates/addedit_members.tpl.php';
?>
</form>
<?php

include_once __DIR__ . '/footer.inc.php';

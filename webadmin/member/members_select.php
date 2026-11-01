<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceHistorySearch, invoiceHistoryFromDatabase, invoiceSearch, invoiceChunk, invoiceSumValue

$member_id   = intval($posted['member_id']);
$member_list = $posted['member_list'];

$all_members = array();
if ($member_id) {
    $all_members[] = $member_id;
} elseif (count($member_list)) {
    $all_members = $member_list;
} else {
    die('Invalid ID passed');
}

$member_cache = array();
foreach ($all_members as $member_id) {
    $member                     = memberFromDatabase($member_id);
    $member['return_message']   = $posted['return_message'];
    $member['default_currency'] = $member['currency'];
    //$member['full_name']=$member['first_name'].' '.$member['last_name'];
    $member['time_joined_str'] = date(_('d/m/Y'), $member['time_joined']);
    $invoice_history           = invoiceHistorySearch(array('member_id' => $member_id, 'message_type_id' => 2));
    //that's ordered by date, so just grab the first:
    $last_message               = invoiceHistoryFromDatabase(reset($invoice_history));
    $time_last_contacted        = $last_message['message_time'];
    $member['last_contact_str'] = (0 != $time_last_contacted) ? date(_('d/m/Y'), $time_last_contacted) : '';

    $shipping = memberShippingAddressesFromDatabase($member_id);

    $requests     = memberRequestsFromDatabase($member_id);
    $new_requests = array();
    foreach ($requests as $request) {
        if ('' != $request['comments']) {
            $new_requests[] = $request['comments'];
        }
    }
    $member['requests'] = $new_requests;

    $warnings     = memberWarningsFromDatabase($member_id);
    $new_warnings = array();
    foreach ($warnings as $warning) {
        if ('' != $warning['comments']) {
            $new_warnings[] = $warning['comments'];
        }
    }
    $member['warnings'] = $new_warnings;

    foreach ($shipping as $key => $ship) {
        if ('' == $ship['contact']) {
            $shipping[$key]['contact'] = $member['full_name'];
        }

        if ('' == $ship['email']) {
            $shipping[$key]['email'] = $member['email_address'];
        }

        if ('' == $ship['company_name']) {
            $shipping[$key]['company_name'] = $member['company_name'];
        }

        $shipping[$key]['country_full'] = $country_list[$ship['country']];
    }
    $member['ship_addr'] = $shipping;
    $member['notify']    = memberNotifyPartiesFromDatabase($member_id);
    $member['contacts']  = memberContactsFromDatabase($member_id);
    $couriers            = memberCouriersFromDatabase($member_id);
    foreach ($couriers as $key => $courier) {
        $couriers[$key]['member_courier'] = 'y';
    }

    $member['couriers'] = $couriers;
    $member['specials'] = memberSpecialPriceFromDatabase($member_id);

    $all_invoices              = invoiceSearch(array('member_id' => $member_id, 'invoice_status' => array('proforma', 'sales', 'processed', 'cancelled')));
    $chunk                     = array_merge(
        array('open' => array(), 'proforma' => array(), 'processed' => array()),
        invoiceChunk($all_invoices, 'invoice_status')
    );
    $open_invoices             = count($chunk['open']) + count($chunk['proforma']);
    $processed_invoices        = $chunk['processed'];
    $member['total_sales_usd'] = convertCurrency(invoiceSumValue($processed_invoices, 'invoice_offerau'), 'AUD', 'USD');
    $member['open_invoices']   = $open_invoices;
    $member['total_invoices']  = count($all_invoices);

    $member_cache[] = $member;
}

$data = (count($member_cache) == 1) ? reset($member_cache) : array('return_message' => $posted['return_message'], 'member_list' => $member_cache);

$jsonp = (array_key_exists('jsonp', $_REQUEST)) ? $_REQUEST['jsonp'] : 'setMemberData';
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
    </head>
    <body><script type="text/javascript">
//<![CDATA[
var data = <?=json_encode($data)?>;
if (window.opener && window.opener.<?=$jsonp?>) {
    window.opener.<?=$jsonp?>(data);
} else if (window.parent && window.parent.<?=$jsonp?>) {
    window.parent.<?=$jsonp?>(data);
} else {
    alert('Unable to send member back to form');
}
window.close();
//]]>
    </script></body>
</html>

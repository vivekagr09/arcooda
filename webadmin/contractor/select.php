<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceSearch, invoiceChunk, invoiceSumValue

$contractor_id                  = intval($posted['contractor_id']);
$contractor                     = contractorFromDatabase($contractor_id);
$contractor['full_name']        = $contractor['first_name'] . ' ' . $contractor['last_name'];
$contractor['last_contact_str'] = '';
if (0 != $contractor['time_last_contacted']) {
    $contractor['last_contact_str'] = date('d/m/Y', $contractor['time_last_contacted']);
}

$shipping     = contractorShippingAddressesFromDatabase($contractor_id);
$requests     = contractorRequestsFromDatabase($contractor_id);
$new_requests = array();
foreach ($requests as $request) {
    if ('' != $request['comments']) {
        $new_requests[] = $request['comments'];
    }
}

$contractor['requests'] = $new_requests;
foreach ($shipping as $key => $ship) {
    if ('' == $ship['contact']) {
        $shipping[$key]['contact'] = $contractor['full_name'];
    }

    if ('' == $ship['email']) {
        $shipping[$key]['email'] = $contractor['email_address'];
    }

    if ('' == $ship['company_name']) {
        $shipping[$key]['company_name'] = $contractor['company_name'];
    }

    $shipping[$key]['country_full'] = getCountryName($ship['country']);
}
$contractor['ship_addr'] = $shipping;
$contractor['notify']    = contractorNotifyPartiesFromDatabase($contractor_id);
$contractor['contacts']  = contractorContactsFromDatabase($contractor_id);
$couriers            = contractorCouriersFromDatabase($contractor_id);
foreach ($couriers as $key => $courier) {
    $couriers[$key]['contractor_courier'] = 'y';
}

$contractor['couriers'] = $couriers;
$contractor['specials'] = contractorSpecialsFromDatabase($contractor_id);

$all_invoices              = invoiceSearch(array('contractor_id' => $contractor_id, 'invoice_status' => array('proforma', 'sales', 'processed', 'cancelled')));
$chunk                     = invoiceChunk($all_invoices, 'invoice_status');
$open_invoices             = count($chunk['open']) + count($chunk['proforma']);
$processed_invoices        = $chunk['processed'];
$contractor['total_sales_usd'] = convertCurrency(invoiceSumValue($processed_invoices, 'invoice_offerus'), 'USD', 'USD');
$contractor['open_invoices']   = $open_invoices;
$contractor['total_invoices']  = count($all_invoices);

$contractor['return_message']   = $posted['return_message'];
$contractor['default_currency'] = 'USD';
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title></title>
		<script type="text/javascript">
//<![CDATA[
var data = <?php jecho($contractor)?>;
if (window.opener != null && window.opener.setContractorData != null)
    window.opener.setContractorData(data);
else
    alert('Unable to send contractor back to form');
window.close();
//]]>
		</script>
	</head>
	<body>
	</body>
</html>
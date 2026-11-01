<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceChunk, invoiceSumValue
moduleLoad('member'); //memberSpecialsFromDatabase

$supplier_id                  = intval($posted['supplier_id']);
$supplier                     = supplierFromDatabase($supplier_id);
$supplier['full_name']        = $supplier['first_name'] . ' ' . $supplier['last_name'];
$supplier['last_contact_str'] = '';
if (0 != $supplier['time_last_contacted']) {
    $supplier['last_contact_str'] = date('d/m/Y', $supplier['time_last_contacted']);
}

$shipping     = [];
$new_requests = array();

$supplier['requests']  = $new_requests;
$supplier['ship_addr'] = $shipping;
$supplier['notify']    = [];
$supplier['contacts']  = supplierContactsFromDatabase($supplier_id);
$couriers              = [];

$supplier['couriers'] = $couriers;
$supplier['specials'] = [];//memberSpecialsFromDatabase($member_id);
$all_invoices              = [];
$chunk                     = invoiceChunk($all_invoices, 'invoice_status');
$open_invoices             = count($chunk['open'] ?? []) + count($chunk['proforma'] ?? []);
$processed_invoices        = $chunk['processed'] ?? [];
$supplier['total_sales_usd'] = convertCurrency(invoiceSumValue($processed_invoices, 'invoice_offerus'), 'USD', 'USD');
$supplier['open_invoices']   = $open_invoices;
$supplier['total_invoices']  = count($all_invoices);

$supplier['return_message']   = $posted['return_message'];
$supplier['default_currency'] = $supplier['currency'];
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title></title>
		<script type="text/javascript">
//<![CDATA[
var data = <?php jecho($supplier)?>;
if(data.return_message=='add_supplier'){
	if (window.opener != null && window.opener.setSupplierData != null){
		window.close();
	    window.opener.setSupplierData(data);
	}else{
		alert('Unable to send supplier back to form');
		window.close();
	}
}else{
	if (window.opener != null && window.opener.setMemberData != null){
		window.close();
	    window.opener.setMemberData(data);
	}else{
		alert('Unable to send supplier back to form');
		window.close();
	}
}
    
//]]>
		</script>
	</head>
	<body>
	</body>
</html>
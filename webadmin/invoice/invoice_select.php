<?php
include_once __DIR__ . '/common.inc.php';

$invoice_id                      = intval($posted['invoice_id']);
$invoice                         = invoiceFromDatabase($invoice_id);
$invoice['invoice_products']     = invoiceProductsFromDatabase($invoice_id);
$invoice['return_message']       = $posted['return_message'];
$invoice['invoice_id_formatted'] = invoiceFormatNumber($invoice_id, 0, $invoice['site_id']);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title></title>
		<script type="text/javascript">
//<![CDATA[
var data = <?php jecho($invoice)?>;
if (window.opener != null && window.opener.setInvoiceData != null) {
	window.opener.setInvoiceData(data);
	window.close();
} else {
	alert('Unable to send invoice back to form');
}
//]]>
		</script>
	</head>
	<body>
	</body>
</html>

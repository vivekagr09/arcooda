<?php
include_once __DIR__ . '/common.inc.php';

$dinvoice_id                  = intval($posted['dinvoice_id']);
$sinvoice                     = documentinvoiceFromDatabase($dinvoice_id);
$sinvoice['invoice_products'] = documentinvoiceProductsFromDatabase($dinvoice_id);
$sinvoice['return_message']   = $posted['return_message'];
?>
<html><head></head><body>
<script type="text/javascript">
<?=formatJSAssoc('data', $invoice)?>
if(window.opener!=null && window.opener.setInvoiceData!=null) {
	window.opener.setInvoiceData(data);
	window.close();
}
else
	alert('Unable to send invoice back to form');
</script>
</body></html>
<?php
include_once __DIR__ . '/common.inc.php';

$sinvoice_id                  = intval($posted['sinvoice_id']);
$sinvoice                     = shipinvoiceFromDatabase($sinvoice_id);
$sinvoice['invoice_products'] = shipinvoiceProductsFromDatabase($sinvoice_id);
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
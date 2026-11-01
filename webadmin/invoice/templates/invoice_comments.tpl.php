<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
$proforma_comments = $invoice['proforma_comments'];
if (!is_array($proforma_comments)) {
    $proforma_comments = explode(',', $proforma_comments);
}

?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionInvoiceComments\');">Invoice Comments</a>')?>

<div id="sectionInvoiceComments">
	<table class="wide">
		<tr>
			<td class="left_title" style="width:25%;"><input type="checkbox" name="proforma_comments[]" value="delivery_date"<?=(in_array('delivery_date', $proforma_comments)) ? ' checked="checked"' : ''?> /></td>
			<td>Delivery Date: <input type="text" name="delivery_date" value="<?=$invoice['delivery_date']?>" readonly="readonly" /><input type="button" value=" V " onclick="gfPop.fPopCalendar(getElement('delivery_date'))" /><input type="button" value="Clear" onclick="setValue('delivery_date','')" /></td>
		</tr>
		<tr>
			<td class="left_title"><input type="checkbox" name="proforma_comments[]" value="warranty"<?=(in_array('warranty', $proforma_comments)) ? ' checked="checked"' : ''?> /></td>
			<td>Warranty Period: <select name="waranty_terms_id" style="width:auto;">
				<option value=""></option>
				<?=formatHTMLOptions(invoiceGetOptionList('warranty_terms_id'), $invoice['waranty_terms_id'])?>
			</select></td>
		</tr>
		<tr>
			<td class="left_title"><input type="checkbox" name="proforma_comments[]" value="remarks"<?=(in_array('remarks', $proforma_comments)) ? ' checked="checked"' : ''?> /></td>
			<td>Remarks: <textarea cols="80" name="invoice_remarks" rows="1" style="width:98%;"><?=htmlspecialchars($invoice['invoice_remarks'])?></textarea></td>
		</tr>
		<tr>
			<td class="left_title"><input type="checkbox" name="proforma_comments[]" value="return_goods"<?=(in_array('return_goods', $proforma_comments)) ? ' checked="checked"' : ''?> /></td>
			<td>No Return of Goods Accepted without Written Authorization.</td>
		</tr>
		<tr>
			<td class="left_title"><input type="checkbox" name="proforma_comments[]" value="terms_conditions"<?=(in_array('terms_conditions', $proforma_comments)) ? ' checked="checked"' : ''?> /></td>
			<td>All business transactions subject to our standard trading terms and conditions.</td>
		</tr>
		<tr>
			<td class="left_title"><input type="checkbox" name="proforma_comments[]" value="bank_details"<?=(in_array('bank_details', $proforma_comments)) ? ' checked="checked"' : ''?> /></td>
			<td>Bank Details: <span name="bank_details">...</span></td>
		</tr>
	</table>
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionInvoiceComments_hideNote">
	Invoice Comments can be viewed by Clicking "Invoice Comments" above
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<!-- invoice_comments.tpl -->

<?php
$proforma_comments = $invoice['proforma_comments'];
if (!is_array($proforma_comments)) {
    $proforma_comments = explode(',', $proforma_comments);
}

?>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionInvoiceComments\');">Invoice Comments / Terms</a>') ?>

<!--div id="sectionInvoiceComments">
	<table class="wide">
		<tr>
			<td class="left_title" style="width:25%;"><input type="checkbox" name="proforma_comments[]" value="delivery_date"<?= (in_array('delivery_date', $proforma_comments)) ? ' checked="checked"' : '' ?> /></td>
			<td>Delivery Date: <input type="text" name="delivery_date" value="<?= $invoice['delivery_date'] ?>" readonly="readonly" /><input type="button" value=" V " onclick="gfPop.fPopCalendar(getElement('delivery_date'))" /><input type="button" value="Clear" onclick="setValue('delivery_date','')" /></td>
		</tr>
		<tr>
			<td class="left_title"><input type="checkbox" name="proforma_comments[]" value="warranty"<?= (in_array('warranty', $proforma_comments)) ? ' checked="checked"' : '' ?> /></td>
			<td>Warranty Period: <select name="waranty_terms_id" style="width:auto;">
				<option value=""></option>
				<?= formatHTMLOptions(invoiceGetOptionList('warranty_terms_id'), $invoice['waranty_terms_id']) ?>
			</select></td>
		</tr>
		<tr>
			<td class="left_title"><input type="checkbox" name="proforma_comments[]" value="remarks"<?= (in_array('remarks', $proforma_comments)) ? ' checked="checked"' : '' ?> /></td>
			<td>Remarks: <textarea cols="80" name="invoice_remarks" rows="1" style="width:98%;"><?php hecho($invoice['invoice_remarks'])?></textarea></td>
		</tr>
		<tr>
			<td class="left_title"><input type="checkbox" name="proforma_comments[]" value="return_goods"<?= (in_array('return_goods', $proforma_comments)) ? ' checked="checked"' : '' ?> /></td>
			<td>No Return of Goods Accepted without Written Authorization.</td>
		</tr>
		<tr>
			<td class="left_title"><input type="checkbox" name="proforma_comments[]" value="terms_conditions"<?= (in_array('terms_conditions', $proforma_comments)) ? ' checked="checked"' : '' ?> /></td>
			<td>All business transactions subject to our standard trading terms and conditions.</td>
		</tr>
		<tr>
			<td class="left_title"><input type="checkbox" name="proforma_comments[]" value="bank_details"<?= (in_array('bank_details', $proforma_comments)) ? ' checked="checked"' : '' ?> /></td>
			<td>Bank Details: <span name="bank_details">...</span></td>
		</tr>
	</table>
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionInvoiceComments_hideNote">
	Invoice Comments can be viewed by Clicking "Invoice Comments" above
</div-->

<div id="sectionInvoiceComments_">
	<table class="wide">
		<tr>
			<td class="left_title">
				Terms

				<input type="checkbox" name="proforma_comments[]" value="terms"<?= (in_array('terms', $proforma_comments)) ? ' checked="checked"' : '' ?> />
			</td>
			<td>
<?php
$proforma_terms = array(
	"",
    "30% deposit balance prior to shipping",
    "50% deposit balance prior to shipping",
    "100% payment in advance",
);
if (!in_array($invoice['proforma_terms'], $proforma_terms)) {
	$proforma_terms[] = $invoice['proforma_terms'];
}
?>
				<select name="proforma_terms" style="width: 98%">
					<?php foreach ($proforma_terms as $v): ?>
						<option value="<?=$v?>" <?=(($v == $invoice['proforma_terms']) ? ' selected="selected"' : '')?>><?=$v?></option>
					<?php endforeach;?>
				</select>
			</td>
		</tr>

		<tr>
			<td class="left_title">
				Shipment Via

				<input type="checkbox" name="proforma_comments[]" value="shipment"<?= (in_array('shipment', $proforma_comments)) ? ' checked="checked"' : '' ?> />
			</td>
			<td>
<?php
$proforma_shipvia = array(
	"",
    "LCL Sea Freight",
    "FCL Sea Freight",
	"Air Cargo",
	"Express Courier",
	"Pickup",
);
if (!in_array($invoice['proforma_shipvia'], $proforma_shipvia)) {
	$proforma_shipvia[] = $invoice['proforma_shipvia'];
}
?>
				<select name="proforma_shipvia" style="width: 98%">
					<?php foreach ($proforma_shipvia as $v): ?>
						<option value="<?=$v?>" <?=(($v == $invoice['proforma_shipvia']) ? ' selected="selected"' : '')?>><?=$v?></option>
					<?php endforeach;?>
				</select>
			</td>
		</tr>

		<tr>
			<td class="left_title" style="width:25%;"><input type="checkbox" name="proforma_comments[]" value="delivery_date"<?= (in_array('delivery_date', $proforma_comments)) ? ' checked="checked"' : '' ?> /></td>
			<td>Delivery Date: <input type="text" name="delivery_date" value="<?= $invoice['delivery_date'] ?>" readonly="readonly" /><input type="button" value=" V " onclick="gfPop.fPopCalendar(getElement('delivery_date'))" /><input type="button" value="Clear" onclick="setValue('delivery_date','')" /></td>
		</tr>

		<tr>
			<td class="left_title"><input type="checkbox" name="proforma_comments[]" value="warranty"<?= (in_array('warranty', $proforma_comments)) ? ' checked="checked"' : '' ?> /></td>
			<td>Warranty Period: <select name="waranty_terms_id" style="width:auto;">
				<option value=""></option>
				<?= formatHTMLOptions(invoiceGetOptionList('warranty_terms_id'), $invoice['waranty_terms_id']) ?>
			</select></td>
		</tr>

		<tr>
			<td class="left_title" style="vertical-align: top;">Remarks</td>
			<td>
				<ul style="list-style: none; padding: 0;">
					<li>
						<input type="checkbox" name="proforma_comments[]" value="return_goods"<?= (in_array('return_goods', $proforma_comments)) ? ' checked="checked"' : '' ?> />
						No Return of Goods Accepted without Written Authorization.
					</li>

					<li>
						<input type="checkbox" name="proforma_comments[]" value="terms_conditions"<?= (in_array('terms_conditions', $proforma_comments)) ? ' checked="checked"' : '' ?> />
						All business transactions subject to our standard trading terms and conditions.
					</li>

					<li>
						<input type="checkbox" name="proforma_comments[]" value="remarks"<?= (in_array('remarks', $proforma_comments)) ? ' checked="checked"' : '' ?> />
						<textarea cols="50" name="invoice_remarks" rows="1" style="width:78%;"><?php hecho($invoice['invoice_remarks'])?></textarea>
					</li>
				</ul>
			</td>
		</tr>
	</table>
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionInvoiceComments_hideNote">
	Invoice Comments can be viewed by Clicking "Invoice Comments" above
</div>

<!-- /invoice_comments.tpl -->

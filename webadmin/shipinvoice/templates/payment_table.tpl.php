<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
$payment_terms = array();
$query         = $DB->query("SELECT id,title FROM `highwaygroup`.`purchase_orders_payment`");
while ($row = $DB->fetch_assoc($query)) {
    $payment_terms[$row['id']] = $row['title'];
}

?>
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionPayment\');">Payment Details</a>')?>
<div id="sectionPayment">
<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;">Payment Method</td>
		<td><select name="paymenttype_id">
			<option value="">Not Selected</option>
			<?=formatHTMLOptions(getPaymentMethodsList(), $sinvoice['paymenttype_id'])?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title">Payment Terms</td>
		<td>
<?php
if ('' == $sinvoice['payment_terms_id'] || 0 == $sinvoice['payment_terms_id']) {
    $sinvoice['payment_terms_id'] = 4;
}
?>
			<select name="payment_terms_id" style="width:80%;">
				<option value="0">Not Selected</option>
				<?=formatHTMLOptions($payment_terms, $sinvoice['payment_terms_id'])?>
			</select>
			<a href="/webadmin/invoice/invoice_options.php?section=payment_terms" target="_blank">Add / Edit</a>
		</td>
	</tr>
<?php
if (!isset($active_staff_list[$sinvoice['handler_id']])) {
    $sinvoice['handler_id'] = $staff_id;
}
?>
	<tr>
		<td class="left_title" style="width:25%;">Handler</td>
		<td><select name="handler_id"><?=formatHTMLOptions($active_staff_list, $sinvoice['handler_id'])?></select></td>
	</tr>
	<tr>
		<td class="left_title">Invoice Date</td>
		<td>
			<input name="proforma_date" id="proforma_date" type="text" size="24" readonly="readonly" value="<?=$sinvoice['proforma_date']?>" />
			<input type="button" value=" V " onclick="javascript:gfPop.fPopCalendar(this.form.proforma_date);" />
			<input type="button" value="Clear" onclick="javascript:setValue('proforma_date','');" />
		</td>
	</tr>
</table>
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

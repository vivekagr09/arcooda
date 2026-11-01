<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionCharges\');">' . (($display_inquiry) ? 'Inquiry' : 'Invoice') . ' Charges/Handling</a>')?>

<div id="sectionCharges">
<table class="wide" id="charge_table">
	<tr>
		<th style="width:32px;">Enable</th>
		<th style="width:100%;">Charge Name</th>
		<th style="width:64px;">Charge %</th>
		<th style="width:64px;">Offer Price</th>
		<th style="width:64px;">Offer AUD</th>
		<th style="width:64px;">&nbsp;</th>
	</tr>
	<tr>
		<td style="text-align:center;"><input size="6" style="text-align:right;" type="checkbox" name="charge_gst" /></td>
		<td><input style="width:100%;" type="text" value="Goods & Services Tax" /></td>
		<td><input size="6" style="text-align:right;" type="text" value="10.0" /></td>
		<td><input size="6" style="text-align:right;" type="text" value="<?=sprintf('%.2f', $invoice['invoice_offer'] * 0.1)?>" /></td>
		<td><input size="6" style="text-align:right;" type="text" value="<?=sprintf('%.2f', $invoice['invoice_offerau'] * 0.1)?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td style="text-align:center;"><input size="6" style="text-align:right;" type="checkbox" name="charge_paypal" /></td>
		<td><input style="width:100%;" type="text" value="Paypal Surcharge" /></td>
		<td><input size="6" style="text-align:right;" type="text" value="4.15" /></td>
		<td><input size="6" style="text-align:right;" type="text" value="<?=sprintf('%.2f', $invoice['invoice_offer'] * 0.0415)?>" /></td>
		<td><input size="6" style="text-align:right;" type="text" value="<?=sprintf('%.2f', $invoice['invoice_offerau'] * 0.0415)?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3" style="text-align:right;font-weight:bold;">TOTAL</td>
		<td><input type="text" readonly="readonly" size="6" value="<?=sprintf('%.2f', $invoice['invoice_offer'])?>" style="text-align:right;" /></td>
		<td><input type="text" readonly="readonly" size="6" value="<?=sprintf('%.2f', $invoice['invoice_offerau'])?>" style="text-align:right;" /></td>
		<td style="font-weight:bold;" id="charge_currency"><?=$invoice['invoice_currency']?></td>
	</tr>
</table>
<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionCharges_hideNote">
All Charges requested can be viewed by Clicking "Charges Requested" above
<br />
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<!-- tab_freight_quotation_pickup.tpl -->

<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;"><input type="checkbox" name="shipping_method_pickup"<?= ('pickup' == $invoice['shipping_method']) ? ' checked="checked"' : '' ?> onchange="freightQuotationChangeShippingMethod('pickup')" /></td>
		<td>Use this shipping method</td>
	</tr>
</table>
<br />

<!-- /tab_freight_quotation_pickup.tpl -->

<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;"><input type="checkbox" name="shipping_method_pickup"<?=('pickup' == $invoice['shipping_method']) ? ' checked="checked"' : ''?> onchange="freightQuotationChangeShippingMethod('pickup')" /></td>
		<td><?=htmlspecialchars(_("Use this shipping method"))?></td>
	</tr>
</table>
<br />

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

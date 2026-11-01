<!-- products_pricing.tpl.php -->

<?php
if(is_array($product))
{
	$supplier=$DB->query_first("SELECT * FROM supplier_product WHERE product_id=".intval($product['id'])." LIMIT 1",MYSQLI_ASSOC);
?>

<script type="text/javascript">
<!--//--><![CDATA[//><!--
function pricingOnChange(target)
{
	switch(target)
	{
	case 'sales_currency':
		setValue('approx_cost_currency', getValue('product[sales_currency]'));
	case 'cost_price':
	case 'cost_currency':
		setValue('approx_cost_price', convertCurrency(getValue('product[cost_price]'), getValue('product[cost_currency]'), getValue('product[sales_currency]')));
		setValue('product[sales_price]', getValue('approx_cost_price')*(getValue('product[markup]')/100.0+1.0));
		break;
	case 'sales_price':
		var approx_cost_price=Number(getValue('approx_cost_price'));
		if(approx_cost_price>0)
			setValue('product[markup]', convertCurrency((getValue('product[sales_price]')/getValue('approx_cost_price')-1.0)*100.0,'','',true,3));
		else
			setValue('product[markup]', '0.000');
		break;
	case 'markup':
		setValue('product[sales_price]', getValue('approx_cost_price')*(getValue('product[markup]')/100.0+1.0));
		break;
	}
}
//--><!]]>
</script>

<!-- <?php echo formatHTMLTitle('<a href="javascript:void(toggleSection(\'sectionPricing\'))">Pricing</a>') ?> -->

<!-- <div id="sectionPricing"> -->
	<!-- <table class="wide"> -->
		<tr>
			<td class="left" style="width:25%;">Supplier :</td>
			<td class="right"><select name="product[preferred_supplier]">
				<option value=""></option>
				<?php echo formatHTMLOptions(productGetOptionList('preferred_supplier'),$supplier['supplier_id']) ?>
			</select></td>
		</tr>
		<tr>
			<td class="left">Cost Price :</td>
			<td class="right">
				<input type="text" name="product[cost_price]" style="text-align:right;" onchange="pricingOnChange('cost_price')" value="<?php echo convertCurrency($product['cost_price'],'','',true,3) ?>" size="6" />
				<select name="product[cost_currency]" onchange="pricingOnChange('cost_currency')">
					<option value="USD">U.S. Dollars - USD</option>
					<?php echo formatHTMLOptions(productGetOptionList('cost_currency'),$product['cost_currency']) ?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="left">Approx Cost Price :</td>
			<td class="right">
				<input type="text" name="approx_cost_price" style="text-align:right;" disabled="disabled" value="<?php echo convertCurrency($product['cost_price'],$product['cost_currency'],$product['sales_currency'],true,3) ?>" size="6" />
				<select name="approx_cost_currency" disabled="disabled">
					<option value="USD">U.S. Dollars - USD</option>
					<?php echo formatHTMLOptions(productGetOptionList('sales_currency'),$product['sales_currency']) ?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="left">Sales Price :</td>
			<td class="right">
				<input type="text" name="product[sales_price]" style="text-align:right;" value="<?php echo convertCurrency($product['sales_price'],'','',true,3) ?>" size="6" onchange="pricingOnChange('sales_price')" />
				<select name="product[sales_currency]" onchange="pricingOnChange('sales_currency')">
					<option value="USD">U.S. Dollars - USD</option>
					<?php echo formatHTMLOptions(productGetOptionList('sales_currency'),$product['sales_currency']) ?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="left">Markup % :</td>
			<td class="right"><input type="text" name="product[markup]" style="text-align:right;" onchange="pricingOnChange('markup')" value="<?php echo ($product['cost_price']!=0)?sprintf('%.3f',$product['sales_price']/convertCurrency($product['cost_price'],$product['cost_currency'],$product['sales_currency'],true,3)*100.0-100.0):'0.000' ?>" size="6" />%</td>
		</tr>
		<tr style="display:none;">
			<td class="left">Price Flux :<br /><small>(Allow automatic price fluctuation according to curreny exchanges)</small></td>
			<td class="right"><select name="product[price_flux]">
				<?php echo formatHTMLOptions(productGetOptionList('price_flux'),$product['price_flux']) ?>
			</select></td>
		</tr>
	<!-- </table> -->
	<!-- <br /><br /> -->
<!-- </div> -->
<!-- <div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionPricing_hideNote"> -->
	<!-- Pricing can be viewed by Clicking "Pricing" above<br /> -->
<!-- </div> -->
<?php
}
?>

<!-- /products_pricing.tpl.php -->

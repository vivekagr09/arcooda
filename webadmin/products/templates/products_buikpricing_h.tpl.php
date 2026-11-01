<!-- products_bulkpricing_h.tpl.php
Created and modified by Mingze Xi@ Feb 16, 2015

Last Update: March 30, 2015

Removed bulk price from webadmin system.

Finished: 50%

To-do: Code layout is completely massed up, needs to be clearify.
Also, currently, bulkpricing section is not displayed on webadmin page. Reason unknown.

Based on (CoE)templates/products_bulkpricing.tpl.php.
Seperated bulkpricing section from products_pricing2.tpl.php.

-->
<?php
function product_bulkpricing_row($count='[ROW_COUNT]',$row_data=array())
{
	global $DB;
	$hidden=array(
		'product_bulkpricing['.$count.'][id]'=>$row_data['id'],
		'product_bulkpricing['.$count.'][unit_sale_currency]'=>$row_data['unit_sale_currency']
	);
	ob_start();
?>
	<tr id="product_bulkpricing_rowa[<?php echo $count ?>]">
		<td><?php echo str_replace("\n","",formatHTMLInputs($hidden)) ?><input type="text" name="product_bulkpricing[<?php echo $count ?>][qty]" size="6" value="<?php echo htmlspecialchars($row_data['qty']) ?>" onchange="productUpdateBulkPricing(<?php echo $count ?>,'qty');" style="text-align:right;" /></td>
		<td><input type="text" name="product_bulkpricing[<?php echo $count ?>][unit_sale_price]" size="6" value="<?php echo htmlspecialchars($row_data['unit_sale_price']) ?>" onchange="productUpdateBulkPricing(<?php echo $count ?>,'unit_sale_price');" style="text-align:right;" /></td>
		<td><input type="text" name="product_bulkpricing[<?php echo $count ?>][unit_cost_price]" size="6" value="<?php echo htmlspecialchars($row_data['unit_cost_price']) ?>" readonly="readonly" style="text-align:right;" /></td>
		<td><input type="text" name="product_bulkpricing[<?php echo $count ?>][unit_profit]" size="6" value="<?php echo $row_data['unit_profit'] ?>" onchange="productUpdateBulkPricing(<?php echo $count ?>,'unit_profit')" style="text-align:right;" /></td>
		<td><input type="text" name="product_bulkpricing[<?php echo $count ?>][total_profit]" size="6" value="<?php echo $row_data['total_profit'] ?>" onchange="productUpdateBulkPricing(<?php echo $count ?>,'total_profit')" style="text-align:right;" /></td>
		<td><input type="text" name="product_bulkpricing[<?php echo $count ?>][markup]" size="6" value="<?php echo htmlspecialchars($row_data['markup']) ?>" onchange="productUpdateBulkPricing(<?php echo $count ?>,'markup')" style="text-align:right;" /></td>
		<td><input onclick="javascript:productDeleteBulkPricing(<?php echo $count ?>);" type="button" value="Delete" /></td>
	</tr>
<?php
	return ob_get_clean();
}
?>
<!--
<?php
if(is_array($product_bulkpricing))
{
?>
-->
	<script type="text/javascript">
	//<![CDATA[
	var product_bulkpricing_count=<?php echo max(100,count($product_bulkpricing)) ?>;
	function productUpdateBulkPricing(row,field)
	{
		var substr='product_bulkpricing['+row+']';
		var qty=Number(getValue(substr+'[qty]'));
		if(qty==0 || isNaN(qty)) qty=1;
		var unit_cost_price=Number(getValue(substr+'[unit_cost_price]'));

		var newValue=Number(getValue(substr+'['+field+']'));
		if(isNaN(newValue))
		{
			newValue=0;
			setValue(substr+'['+field+']','0');
		}
		switch(field)
		{
		case 'unit_cost_price':
			var unit_profit=unit_cost_price*(Number(getValue(substr+'[markup]'))*0.01);
			setValue(substr+'[unit_sale_price]',unit_profit+unit_cost_price);
			setValue(substr+'[unit_profit]',unit_profit);
			setValue(substr+'[total_profit]',unit_profit*qty);
			break;
		case 'unit_sale_price':
			setValue(substr+'[unit_profit]',newValue-unit_cost_price);
			setValue(substr+'[total_profit]',(newValue-unit_cost_price)*qty);
			setValue(substr+'[markup]',(newValue-unit_cost_price)/(unit_cost_price*0.01));
			break;
		case 'markup':
			var unit_profit=unit_cost_price*(newValue*0.01);
			setValue(substr+'[unit_sale_price]',unit_profit+unit_cost_price);
			setValue(substr+'[unit_profit]',unit_profit);
			setValue(substr+'[total_profit]',unit_profit*qty);
			break;
		case 'unit_profit':
			var total_profit=newValue*qty;
			setValue(substr+'[unit_sale_price]',newValue+unit_cost_price);
			setValue(substr+'[total_profit]',total_profit);
			setValue(substr+'[markup]',newValue/(unit_cost_price*0.01));
			break;
		case 'total_profit':
			var unit_profit=newValue/qty;
			setValue(substr+'[unit_sale_price]',unit_profit+unit_cost_price);
			setValue(substr+'[unit_profit]',unit_profit);
			setValue(substr+'[markup]',unit_profit/(unit_cost_price*0.01))
			break;
		case 'qty':
			var unit_profit=Number(getValue(substr+'[unit_profit]'));
			setValue(substr+'[total_profit]',unit_profit*qty);
			break;
		}
	}
	function productAddBulkPricing()
	{
		var success=false;
		var count=String(product_bulkpricing_count);
		var target_table=getElement("product_bulkpricing_table");
		var unit_sale_currency=getValue('prices[sales_currency]');
		var unit_cost_price=convertCurrency(getValue('prices[cost_price]'), getValue('prices[cost_currency]'), unit_sale_currency);
		if(target_table!=null)
		{
			success=true;
			var newRow1=addTableRow(target_table,'product_bulkpricing_rowa['+count+']');
			if(count&1) newRow1.className='alt';
<?php
	$count=0;
	foreach(explode("\n",product_bulkpricing_row('[ROW_COUNT]',array('unit_cost_price'=>'[UNIT_COST_PRICE]','unit_sale_currency'=>'[UNIT_SALE_CURRENCY]'))) as $row)
	{
		$row=trim($row);
		if(substr($row,0,3)=='<tr')
		{
			$count++;
		}
		elseif(substr($row,0,3)=='<td')
		{
?>
			addTableCell(newRow<?php echo $count ?>,<?php echo str_replace(array("[ROW_COUNT]","[UNIT_COST_PRICE]","[UNIT_SALE_CURRENCY]"),array('" + count + "', '" + unit_cost_price + "', '" + unit_sale_currency + "'), json_encode(substr($row,strpos($row,'>')+1,-5))) ?>).style.textAlign='center';
<?php
		}
	}
?>
		}
		if(success) product_bulkpricing_count++;
		return success;
	}
	function productDeleteBulkPricing(row)
	{
		var theRow=getElement("product_bulkpricing_rowa["+row+"]");
		if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	}
	//]]>
	</script>

<?php echo formatHTMLTitle('<a href="javascript:void(toggleSection(\'sectionBulkPricing\'))">Bulk Pricing</a>','<a href="javascript:void(productAddBulkPricing())">Add Bulk Price</a>') ?>

<div id="sectionBulkPricing">
<!--
<tr>
	<td class="left">Bulk Pricing (<a href="javascript:void(productAddBulkPricing())">Add Bulk Price</a>)</td>
	<td class="right">
-->
	<table style="width:95%;padding-left: 3;border: 1px solid #CCCCCC;border-collapse: collapse;margin-left: auto;margin-right: auto;" align="center" name="product_bulkpricing_table">
		<tr>
			<th style="width:12%">Qty</th>
			<th style="width:12%">Unit Sale Price (<span name="show_sales_currency"><?php echo htmlspecialchars($product['sales_currency']) ?></span>)</th>
			<th style="width:12%">Unit Cost Price (<span name="show_sales_currency"><?php echo htmlspecialchars($product['sales_currency']) ?></span>)</th>
			<th style="width:12%">Unit Profit (<span name="show_sales_currency"><?php echo htmlspecialchars($product['sales_currency']) ?></span>)</th>
			<th style="width:12%">Total Profit (<span name="show_sales_currency"><?php echo htmlspecialchars($product['sales_currency']) ?></span>)</th>
			<th style="width:12%">Markup (%)</th>
			<th style="width:16%">&nbsp;</th>
		</tr>
<?php
		$affected=array();
		$count=1;
		foreach($product_bulkpricing as $row_data)
		{
			$row_data['unit_sale_currency']=$product['sales_currency'];
			$row_data['unit_cost_price']=convertCurrency($product['cost_price'], $product['cost_currency'], $row_data['unit_sale_currency']);
			$row_data['unit_profit']=convertCurrency($row_data['unit_sale_price']-$row_data['unit_cost_price'], $row_data['unit_sale_currency']);
			$row_data['total_profit']=convertCurrency($row_data['qty']*$row_data['unit_profit'], $row_data['unit_sale_currency']);
			$row_data['markup']=sprintf('%.4f',$row_data['unit_profit']*100.0/$row_data['unit_cost_price']);
			echo product_bulkpricing_row($count++,$row_data);
			$affected[]=$row_data['id'];
		}
?>
	</table>
	<!--<input type="hidden" name="product_bulkpricing_affected" value="<?php echo implode("|",$affected) ?>" />-->
	</div>

<!--
<?php
}
?>
-->
<!-- /products_bulkpricing.tpl.php -->

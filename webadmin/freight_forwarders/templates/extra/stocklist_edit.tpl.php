<script type="text/javascript">
//<![CDATA[
function tickBox(id)
{
	document.forms[0].elements["stock["+id+"][update]"].checked = true;
}

function showPrintPage()
{
	alert("Print page goes here");
	var windowURL = "stocklist_admin.php?action=edit&view=print&supplier_id=<?php echo $_REQUEST['supplier_id'];?>";
	printwnd = window.open(windowURL,name,"toolbar,location,directories,status,menubar,scrollbars,copyhistory,resizable");
	printwnd.focus();
}
//]]>
</script>
<script type="text/javascript" src="functions/buysellmarkup.js"></script>
<h3 style="text-align: left"><?php
if((trim($supplier['supplier_name']) == "") || (trim($supplier['supplier_name_ch']) == ""))
{	// One of the name fields (or both) is (are) empty, cbf figuring which it is
	echo trim($supplier['supplier_name'] . $supplier['supplier_name_ch']);
}
else
{	// Both names are set, display them both
	echo trim($supplier['supplier_name']) . " / " . trim($supplier['supplier_name_ch']);
}
?></h3>
<form name="edit_stock_form" id="edit_stock_form" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<input name="supplier_id" type="hidden" value="<?php echo $_REQUEST['supplier_id'];?>">
	<input name="action" type="hidden" value="do_edit">
	<b>Showing: <?=$page_first?> to <?=$page_last?> of <?=$total_items?></b> | <?=$page_links?><br /><br />
	<table style="white-space: nowrap; text-align: center" class="wide vbox">
		<tr>
			<th>Qty<br />Available</th>
			<th>Name of Product </th>
			<th>Buy Price </th>
			<th>Sell Price </th>
			<th>% Markup </th>
			<th>Location</th>
			<th>Terms</th>
			<th>A</th>
			<th>F</th>
			<th>Notes</th>
			<th>Movement</th>
			<th>&nbsp;</th>
		</tr>
<?php
foreach($stock as $s): ?>
		<tr<?php echo ($i++ % 2)?" class=\"alt\"":""?>>
			<td><input name="stock[<?php echo $i?>][qty]" type="text" class="intext" value="<?php echo $s['qty'];?>" onchange="javascript: tickBox(<?php echo $i?>);" /></td>
			<td style="white-space: nowrap; text-align: left"><?php echo trimString($s['product_name'], 30, $s['product_id']);?></td>
			<td><input name="stock[<?php echo $i?>][buy]" id="stock[<?php echo $i?>][buy]" type="text" class="intext" value="<?php echo sprintf("%0.2f", $s['buy_price']);?>" onchange="javascript: tickBox(<?php echo $i?>);" onkeyup="calcSellMark(<?php echo $i?>);" /></td>
			<td><input name="stock[<?php echo $i?>][sell]" id="stock[<?php echo $i?>][sell]" type="text" class="intext" value="<?php echo sprintf("%0.2f", $s['sell_price']);?>" onchange="javascript: tickBox(<?php echo $i?>);" onkeyup="calcMark(<?php echo $i?>);" /></td>
			<td><input name="stock[<?php echo $i?>][markup]" id="stock[<?php echo $i?>][markup]" type="text" class="intext" value="<?php echo sprintf("%0.2f", $s['percent_markup']);?>" onchange="javascript: tickBox(<?php echo $i?>); " onkeyup="calcSell(<?php echo $i?>);" /></td>
			<td><?php echo $s['location_name']?></td>
			<td><?php echo $s['term'];?></td>
			<td><input name="stock[<?php echo $i?>][active]" type="checkbox" id="stock[<?php echo $i?>][active]" value="1" onchange="javascript: tickBox(<?php echo $i?>);"<?php echo $s['active']?" checked=\"checked\"":""?> /></td>
			<td><input name="stock[<?php echo $i?>][price_flux]" type="checkbox" id="stock[<?php echo $i?>][price_flux]" value="1" onchange="javascript: tickBox(<?php echo $i?>);"<?php echo $s['price_flux']?" checked=\"checked\"":""?> /></td>
			<td><input name="stock[<?php echo $i?>][notes]" type="text" class="intext" style="width: 100px"value="<?php echo $s['notes'];?>" onchange="javascript: tickBox(<?php echo $i?>);" /></td>
			<td><?php echo sprintf("%+02.2f", $s['movement']);?>%</td>
			<td style="white-space: nowrap; text-align: left">
				<input name="stock[<?php echo $i?>][update]" type="checkbox" value="1" />
				<a href="/webadmin/products/admin.php?table=products&amp;parent=<?php echo $s['parent'];?>&amp;action=edit&amp;id=<?php echo $s['product_id'];?>">Edit</a> |
				<a href="?action=delete_item&product_id=<?php echo $s['product_id'];?>&amp;supplier_id=<?php echo $s['supplier_id'];?>&amp;from=edit">Delete</a> |
				<a href="/products/machines/fsl/x_<?php echo $s['product_id'];?>.html?preview=true">View</a>
				<input name="stock[<?php echo $i?>][id]" type="hidden" value="<?php echo $s['product_id'];?>" />
			</td>
		</tr>
<?php
endforeach; ?>
	</table>
	<p><em>Notes:</em><br />
	The checkbox labelled <strong>A</strong> sets whether the item is active or not.<br />
	The checkbox labelled <strong>F</strong> turns Price Flux on or off.</p>
	<table style="text-align: center">
		<tr>
			<td>
				<input name="update" type="submit" value="Update" />
				<input name="print" type="button" value="Print" onclick="javascript:showPrintPage();" />
			</td>
		</tr>
	</table>
	<br /><b>Showing: <?=$page_first?> to <?=$page_last?> of <?=$total_items?></b> | <?=$page_links?><br />
</form>

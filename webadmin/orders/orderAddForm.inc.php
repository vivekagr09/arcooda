<?php
$result    = $DB->query("SELECT id, name FROM order_customers ORDER BY name");
$customers = array();
while ($row = $DB->fetch_array($result)) {
    $customers[$row['id']] = ucwords(stripslashes($row['name']));
}

$DB->free_result($result);

$countries = getCountryList(false);
?>
<script language="javascript">
<!--
	function openSearchWindow(elementName,index)
	{
		var searchStr = escape(document.forms[0].elements[elementName].value);
		var windowURL = "productSearch.php?param="+searchStr+"&i="+index;
		searchWnd = window.open(windowURL,"me","titlebar,resizable,scrollbars,width=650,height=400");
		searchWnd.focus();
	}

	function selectProduct(index,prodID,name,price)
	{
		var elNamePrefix = "order["+index+"]";
		var idEl = elNamePrefix+"[id]";
		var nameEl = elNamePrefix+"[name]";
		var priceEl = elNamePrefix+"[sales_price]";

		document.forms[0].elements[idEl].value = prodID;
		document.forms[0].elements[nameEl].value = name;
		document.forms[0].elements[priceEl].value = price;
	}
-->
</script>
<br/>
<?php
if ("" != $GLOBALS['errorMsg']) {?>
<div>
	<font color="#FF0000"><b>The following errors occured</b>
	<br/><br/><?=$GLOBALS['errorMsg']?></font>
</div>
<?php
}?>
<br/>
<table width="70%" align="center" cellspacing="1" cellpadding="4">
	<tr>
		<td width="30%" class="left">Select Customer :</td>
		<td width="70%" class="right">
			<select name="customer[id]">
				<option value=""></option>
				<?=showOptions($customers)?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="center" colspan="2"><b>OR</b></td>
	</tr>
	<tr>
		<td class="left">Customer Name :</td>
		<td class="right"><input type="text" name="customer[name]" style="width: 250px"></td>
	</tr>
	<tr>
		<td class="left">Customer Country :</td>
		<td class="right">
			<select name="customer[country]">
				<option value=""></option>
				<?=showOptions($countries)?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="left">Customer Email :</td>
		<td class="right"><input type="text" name="customer[email]" style="width: 250px"></td>
	</tr>
	<tr>
		<td class="left">Customer Phone :</td>
		<td class="right"><input type="text" name="customer[phone]" style="width: 250px"></td>
	</tr>
</table>
<br/><br/><br/>
<table width="90%" align="center" cellspacing="1" cellpadding="4">
	<tr class="tableHeader">
		<td>Product ID</td>
		<td>Product Name</td>
		<td>Qty</td>
		<td>Sell Price</td>
		<td>Buy Price</td>
		<td>Status</td>
	</tr>
<?php
for ($i = 0; $i < 5; $i++) {
    $class = $i % 2 == 0 ? "tableRow" : "tableRowAlt";
?>
	<tr align="center" class="<?=$class?>">
		<td>
			<input type="text" name="order[<?=$i?>][id]" value="" style="width: 50px" >
			<input type="button" name="search1" value="Search" onclick="openSearchWindow('order[<?=$i?>][id]',<?=$i?>)" style="width: 40px; font-size: xx-small;">
		</td>
		<td>
			<input type="text" name="order[<?=$i?>][name]" value="" style="width: 200px">
			<input type="button" name="search2" value="Search" onclick="openSearchWindow('order[<?=$i?>][name]',<?=$i?>)" style="width: 40px; font-size: xx-small;">
		</td>
		<td>
			<input type="text" name="order[<?=$i?>][qty]" value="" style="width: 30px">
		</td>
		<td>
			<input type="text" name="order[<?=$i?>][sales_price]" value="" style="width: 100px">
		</td>
		<td>
			<input type="text" name="order[<?=$i?>][buy_price]" value="" style="width: 100px">
		</td>
		<td>
			<select name="order[<?=$i?>][status]">
				<option value="open" selected>Open</option>
				<option value="closed">Closed</option>
			</select>
		</td>
	</tr>
<?php
}
?>
</table>
<br /><br />
<table width="70%" align="center" cellspacing="1" cellpadding="4">
	<tr>
		<td width="30%" class="left">Currency :</td>
		<td width="70%" class="right">
			<select name="other[currency]">
<?php
showCurrencyList("USD")?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="left">Stock Country :</td>
		<td class="right">
			<select name="other[country]">
				<option value=""></option>
				<?=showOptions($countries)?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="left">Notes :</td>
		<td class="right">
			<textarea name="other[notes]" style="width: 300px"></textarea>
		</td>
	</tr>
</table>
<br/><br/>
<div align="center">
	<input type="hidden" name="action" value="do_add">
	<input type="submit" value="Add">
</div>
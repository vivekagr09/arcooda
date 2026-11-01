<?php
addCalendarHeader();

$id   = intval($id);
$item = $DB->query_first(sprintf(
    "SELECT * FROM `shipping_aircargo` WHERE `id` = %d LIMIT 1",
    $id
));
?>
<table border="0" align="center" cellpadding="3" cellspacing="1" width="80%">
	<tr>
		<td class="left">Date :</td>
		<td class="right">
			<input name="data[date1]" type="text" id="date1" style="width:200px;" value="<?=htmlspecialchars($item['date'])?>" />
      <?php addCalendarBtn('date1');?>
		</td>
	</tr>
	<tr>
		<td class="left">Ship From :</td>
		<td class="right"><select name="data[ship_from]" style="width:200px;">
			<option value=""></option>
			<?=formatHTMLOptions(getCountryList(false), $item['ship_from'])?>
		</select></td>
	</tr>
	<tr>
		<td class="left">Ship To :</td>
		<td class="right"><select name="data[ship_to]" style="width:200px;">
			<option value=""></option>
			<?=formatHTMLOptions(getCountryList(false), $item['ship_to'])?>
		</select></td>
	</tr>
	<tr>
		<td class="left">Terms :</td>
		<td class="right">
			<input name="data[fob]" type="checkbox" <?=$item['fob']?> />FOB
      &nbsp;
			<input name="data[exw]" type="checkbox" <?=$item['exw']?> />Ex Works
      &nbsp;
			<input name="data[dtd]" type="checkbox" <?=$item['dtd']?>/>Door To Door
      &nbsp;
			<input name="data[dta]" type="checkbox" <?=$item['dta']?>/>Door To Airport
		</td>
	</tr>
	<tr>
		<td class="left"><b>IATA Weight :</b></td>
		<td class="right"><input type="text" name="data[weight]" value="<?=htmlspecialchars($item['weight'])?>" size="20" style="width:200px" /></td>
	</tr>
	<tr>
		<td class="left"><b>Cubic Weight :</b></td>
		<td class="right"><input type="text" name="data[c_weight]" value="<?=htmlspecialchars($item['c_weight'])?>" size="20" style="width:200px" />cm<sup>3</sup></td>
	</tr>
	<tr>
		<td class="left"><b>Quantity :</b></td>
		<td class='right'><input type="text" name="data[quantity]" value="<?=htmlspecialchars($item['quantity'])?>" size="20" style="width:200px" /></td>
	</tr>
	<tr>
		<td class="left"><b>Total Price :</b></td>
		<td class='right'><input type="text" name="data[price]" value="<?=htmlspecialchars($item['price'])?>" size="20" style="width:200px" /></td>
	</tr>
	<tr>
		<td class="left"><b>Sale Price :</b></td>
		<td class='right'><input type="text" name="data[s_price]" value="<?=htmlspecialchars($item['s_price'])?>" size="20" style="width:200px" />USD</td>
	</tr>
	<tr>
		<td class="left">Currency:</td>
		<td class="right"><select name="data[currency]" style="width:200px" title="This is the currency that you would like to view the prices in">
	    <option value="USD">U.S. Dollars</option>
		<?=formaTHTMLOptions(getCurrencyList(false), $item['currency'])?>
		</select></td>
	</tr>
	<tr>
		<td class="left"><b>Type :</b></td>
		<td class="right">
<?php
if ('quote' == $item['type']) {
?>
			Quote <input type="radio" name="data[type]" value="quote" checked="checked" />
      Actual <input type="radio" name="data[type]" value="actual" />
<?php
} else {
?>
    	Quote <input type="radio" name="data[type]" value="quote" />
      Actual <input type="radio" name="data[type]" value="actual" checked="checked" />
<?php
}
?>
		</td>
	</tr>
	<tr>
		<td class="left"><b>Service Type :</b></td>
  	<td class="right">
<?php
if ('direct' == $item['service_type']) {
?>
			Direct <input type="radio" name="data[service_type]" value="direct" checked="checked" />
	    Indirect <input type="radio" name="data[service_type]" value="indirect" />
<?php
} else {
?>
    	Direct <input type="radio" name="data[service_type]" value="direct" /> Indirect <input type="radio" name="data[service_type]" value="indirect" checked="checked" />
<?php
}
?>
    </td>
	</tr>
	<tr>
		<td class="left"><b>Transit Time :</b></td>
		<td class='right'><input type="text" name="data[tit]" value="<?=htmlspecialchars($item['tit'])?>" size="20" style="width:200px;">days</td>
	</tr>
	<tr>
		<td class="left"><b>Name of Goods :</b></td>
		<td class="right"><textarea name="data[goods_name]" rows="5" style="width:300px;"><?=htmlspecialchars($item['goods_name'])?></textarea></td>
	</tr>
	<tr>
		<td class="left"><b>Customer Name :</b></td>
		<td class="right"><input type="text" name="data[cust_name]" value="<?=htmlspecialchars($item['cust_name'])?>" size="20" style="width:200px;" /></td>
	</tr>
	<tr>
		<td class="left"><b>Supplier :</b></td>
		<td class="right"><input type="text" name="data[supplier]" size="20" style="width:200px;" value="<?=htmlspecialchars($item['supplier'])?>" /></td>
	</tr>
	<tr>
		<td class="left"><b>Carrier :</b></td>
		<td class="right"><input type="text" name="data[carrier]" size="20" style="width:200px;" value="<?=htmlspecialchars($item['carrier'])?>" /></td>
	</tr>
	<tr>
		<td class="left"><b>Notes :</b></td>
		<td class="right"><textarea name="data[notes]" rows="5" style="width:300px;"><?=htmlspecialchars(str_replace('(Created Automatically From Inquiry Section)', '', $item['notes']))?></textarea>
	</tr>
</table>
<br />
<br />
<div align="center">
	<input type="hidden" name="action" value="do_edit" />
	<input type="hidden" name="id" value="<?php echo $id ?>" />
  <input type="submit" name="add" value="Update" />
  <input type="reset" name="clear" value="Clear" />
</div>

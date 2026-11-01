<?php
/**
 * @var int $id
 */

global $DB;

addCalendarHeader();
$id   = intval($id);
$sql  = "SELECT * FROM shipping_seacargo WHERE id = '" . $id . "'";
$item = $DB->query_first($sql);


?>
<table border="0" align="center" cellpadding="3" cellspacing="1" width="80%">
<tr>
	<td class="left">Date :</td>
	<td class="right">
      <input name="data[date1]" type="text" id="date1" style="width:200px" value='<?=$item['date']?>'>
<?php
addCalendarBtn("date1");
?>
	</td>
</tr>
<tr>
    <td class="left">Ship From :</td>
    <td class="right">
		<select name="data[ship_from]" style="width:200px">
		<option value=""></option>
<?php
$statement = "SELECT code, name FROM shipping_location ORDER BY name ASC";

$result = $DB->query($statement);
while ($row = $DB->fetch_array($result)) {
    $selected = $item['ship_from'] == $row['code'] ? "selected" : "";
    echo "<option value=\"" . $row['code'] . "\" $selected>" . stripslashes($row['name']) . "</option>\r\n";
}
$DB->free_result($result);
?>
		</select>
  </td>
</tr>
<tr>
    <td class="left">Ship To :</td>
    <td class="right">
		<select name="data[ship_to]" style="width:200px">
		<option value=""></option>
<?php
$statement = "SELECT code, name FROM shipping_location ORDER BY name ASC";

$result = $DB->query($statement);
while ($row = $DB->fetch_array($result)) {
    $selected = $item['ship_to'] == $row['code'] ? "selected" : "";
    echo "<option value=\"" . $row['code'] . "\" $selected>" . stripslashes($row['name']) . "</option>\r\n";
}
$DB->free_result($result);
?>
		</select>
  </td>
</tr>
<tr>
	<td class="left">Terms :</td>
	<td class="right">
		<input name="data[fob]" type="checkbox" <?=$item['fob']?>/>FOB&nbsp;&nbsp;
		<input name="data[exw]" type="checkbox" <?=$item['exw']?>/>Ex Works&nbsp;&nbsp;
		<input name="data[dtd]" type="checkbox" <?=$item['dtd']?>/>Door To Door&nbsp;&nbsp;
		<input name="data[dta]" type="checkbox" <?=$item['dta']?>/>Door To Port
	</td>
</tr>
<tr>
	<td class="left"><b>IATA Weight :</b></td>
	<td class='right'><input type="text" name="data[weight]" value="<?=$item['weight']?>" size="20" style="width:200px"></td>
</tr>
<tr>
	<td class="left"><b>Cubic Weight :</b></td>
	<td class='right'><input type="text" name="data[c_weight]" value="<?=$item['c_weight']?>" size="20" style="width:200px">cm<sup>3</sup></td>
</tr>
<tr>
	<td class="left"><b>Quantity :</b></td>
	<td class='right'><input type="text" name="data[quantity]" value="<?=$item['quantity']?>" size="20" style="width:200px"></td>
</tr>
<tr>
	<td class="left"><b>Total Price :</b></td>
	<td class='right'><input type="text" name="data[price]" value="<?=$item['price']?>" size="20" style="width:200px"></td>
</tr>
<tr>
	<td class="left"><b>Sale Price :</b></td>
	<td class='right'><input type="text" name="data[s_price]" value="<?=$item['s_price']?>" size="20" style="width:200px">USD</td>
</tr>
<tr>
	<td class="left">Currency:</td>
	<td class="right">
		<select name="data[currency]" style="width:190px" tooltip="This is the currency that you would like to view the prices in">
			<?=formatHTMLOptions(getCurrencyList(false), $item['currency'])?>
	</td>
</tr>
<tr>
	<td class="left"><b>Type :</b></td>
<?php
if ('quote' == $item['type']) {
?>
		<td class='right'>Quote <input type="radio" name="data[type]" value="quote" checked> Actual<input type="radio" name="data[type]" value="actual"></td>
<?php
} else {
?>
		<td class='right'>Quote <input type="radio" name="data[type]" value="quote"> Actual<input type="radio" name="data[type]" value="actual" checked></td>
<?php
}
?>

</tr>
<tr>
	<td class="left"><b>Service Type :</b></td>
<?php
if ('direct' == $item['service_type']) {
?>
		<td class='right'>Direct <input type="radio" name="data[service_type]" value="direct" checked> Indirect<input type="radio" name="data[service_type]" value="indirect"></td>
<?php
} else {
?>
		<td class='right'>Direct <input type="radio" name="data[service_type]" value="direct" > Indirect<input type="radio" name="data[service_type]" value="indirect" checked></td>
<?php
}
?>

</tr>
<tr>
	<td class="left">Container Type:</td>
	<td class="right">
		<select name="data[container_type]" style="width:200px" tooltip="">
<?php
if ('20ft' == $item['container_type']) {
?>
				<option value='LCL'>LCL</option>
				<option value='20ft'selected>20ft</option>
				<option value='40ft'>40ft</option>
				<option value='40hq'>40ft HQ</option>
<?php
} else if ('40ft' == $item['container_type']) {
?>
				<option value='LCL'>LCL</option>
				<option value='20ft'>20ft</option>
				<option value='40ft'selected>40ft</option>
				<option value='40hq'>40ft HQ</option>
<?php
} else if ('LCL' == $item['container_type']) {
?>
				<option value='LCL'selected>LCL</option>
				<option value='20ft'>20ft</option>
				<option value='40ft'>40ft</option>
				<option value='40hq'>40ft HQ</option>
<?php
} else {
?>
				<option value='LCL'>LCL</option>
				<option value='20ft'>20ft</option>
				<option value='40ft'>40ft</option>
				<option value='40hq'selected>40ft HQ</option>
<?php
}
?>
	</td>
</tr>
<tr>
	<td class="left"><b>Transit Time :</b></td>
	<td class='right'><input type="text" name="data[tit]" value="<?=$item['tit']?>" size="20" style="width:200px">days</td>
</tr>
<tr>
	<td class="left"><b>Name of Goods :</b></td>
	<td class='right'><textarea name="data[goods_name]" rows="5" style="width:300px"><?=$item['goods_name']?></textarea></td>
</tr>
<tr>
	<td class="left"><b>Customer Name :</b></td>
	<td class='right'><input type="text" name="data[cust_name]" value="<?=$item['cust_name']?>" size="20" style="width:200px"></td>
</tr>

<tr>
	<td class="left"><b>Supplier :</b></td>
	<td class='right'><input type="text" name="data[supplier]" size="20" style="width:200px" value="<?=$item['supplier']?>"></td>
</tr>
<tr>
	<td class="left"><b>Carrier :</b></td>
	<td class='right'><input type="text" name="data[carrier]" size="20" style="width:200px" value="<?=$item['carrier']?>"></td>
</tr>
<tr>
	<td class="left"><b>Notes :</b></td>
	<td class='right'><textarea name="data[notes]" rows="5" style="width:300px"><?=$output = str_replace("(Created Automatically From Inquiry Section)", "", $item['notes']);?></textarea>
</tr>
</table>
<br><br>
      <div align="center">
		<input type="hidden" name="action" value="do_edit">
		<input type="hidden" name="id" value="<?=$id?>">
        <input type="submit" name="add" value="Update">
        <input type="reset" name="clear" value="Clear">
      </div>
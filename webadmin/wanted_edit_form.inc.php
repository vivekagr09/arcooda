<?php

addCalendarHeader();

?>

<?php $id = intval($id);

show_locked_msg($table, $id);

$item = $DB->query_first("SELECT *, DATE_FORMAT(expiry_date, '%Y-%m-%d') as expiry_date FROM wanted WHERE id = '$id'");

$product_ref = $DB->query_first("SELECT product_name, image FROM products WHERE id = '" . $item['product_ref'] . "'");

?>
<table border="0" width="10%" align="center" cellpadding="4" cellspacing="0">
  <tr>
    <td nowrap class="bottom">&nbsp;</td>
    <td class="bottom">&nbsp;</td>
  </tr>
  <tr>
    <td align="right" nowrap class="left"><b>Wanted Item :</b></td>
    <td class="right">
      <input type="text" name="data[item]" size="40" style="width:400px" value="<?=htmlspecialchars($item['item']);?>">
    </td>
  </tr>
  <tr>
    <td align="right" nowrap class="left"><b>Category / Type :</b></td>
    <td class="right">
	<select name="data[type]" style="width:400px">
	<option value=""></option>
<?php $statement = "SELECT t.*, c.description as parent_description
					FROM product_types t, product_types c
					WHERE t.parent = c.id
					ORDER BY parent_description ASC, t.name ASC
					";

$result = $DB->query($statement);
while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['id'] . '"';
    echo $row['id'] == $item['type'] ? ' selected' : '';
    echo '>' . stripslashes($row['parent_description'] . ' - ' . $row['name']) . '</option>' . "\n";
}
$DB->free_result($result);

?>
	</select>
    </td>
  </tr>
  <tr>
    <td align="right" nowrap class="left"><b>Manufacturer :</b></td>
    <td class="right">
	<select name="data[manufacturer]" style="width:400px">
	<option value=""></option>
<?php $statement = "SELECT id, manufacturer FROM manufacturers ORDER BY manufacturer ASC";

$result = $DB->query($statement);
while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['id'] . '"';
    echo $row['id'] == $item['manufacturer'] ? ' selected' : '';
    echo '>' . stripslashes($row['manufacturer']) . '</option>' . "\n";
}
$DB->free_result($result);

?>
	</select>
    </td>
  </tr>
  <tr>
    <td align="right" nowrap class="left"><b>Preferred Location :</b></td>
    <td class="right">
	<select name="data[pref_location]" style="width:400px">
		<option value=""></option>
    <?=formatHTMLOptions(getCountryList(false), $item['pref_location'])?>
	</select>
    </td>
  </tr>
  <tr>
    <td align="right" nowrap class="left"><b>Description :</b></td>
    <td class="right">
      <textarea name="data[description]" cols="20" rows="5" style="width:400px"><?=$item['description'];?></textarea>
    </td>
  </tr>
  <tr>
    <td align="right" nowrap class="left"><b>Current Image :</b></td>
    <td class="right">
<?php if (is_file($_SERVER['DOCUMENT_ROOT'] . '/' . $product_ref['image'])) {
    $img = $product_ref['image'];
} else {
    $img = $item['image'];
}

?>
      <input type="text" size="40" style="width:400px" value="<?=$img;?>" disabled>
    </td>
  </tr>
  <tr bgcolor="#EEEEEE">
    <td align="right" nowrap class="left"><b>New Images :</b></td>
    <td class="right">
      <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
      <input type="file" name="images[image]" style="width:400px"><br />
	  <?php html_or_bar('OR use image from products database');?>
	  <input type="text" name="product_ref_id" style="width:250px" id="pref_text" value="<?=$product_ref['product_name'];?>">
	  <input type="button" name="search" value="Search" style="width:70px" onClick="MM_openBrWindow('product_ref.php?table=products&query='+document.admin_form.pref_text.value,'productRef','scrollbars=yes,resizable=yes,width=650,height=450')">
	  <input type="button" name="clear_search" value="Clear" style="width:70px" onClick="javascript:clearSearch()">
	  <input type="hidden" name="data[product_ref]" id="pref_id" value="<?=$item['product_ref'];?>">
    </td>
  </tr>
  <tr bgcolor="#EEEEEEE">
    <td align="right" nowrap class="left"><b>Admin Notes :</b>
	<font color="red"><br>Admin notes are <br>never displayed<br>on the main site</font>
	</td>
    <td class="right">
      <textarea name="data[admin_notes]" cols="20" rows="3" style="width:400px"><?=$item['admin_notes'];?></textarea>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="centre">&nbsp;</td>
  </tr>
  <tr>
    <td nowrap align="right" class="left"><b>Customer Name :</b></td>
    <td class="right">
      <input type="text" name="data[customer_name]" size="10" style="width:200px" value="<?=$item['customer_name'];?>">
    </td>
  </tr>
  <tr>
    <td nowrap align="right" class="left"><b>Customer Email :</b></td>
    <td class="right">
      <input type="text" name="data[customer_email]" size="10" style="width:200px" value="<?=$item['customer_email'];?>">
    </td>
  </tr>
  <tr>
    <td nowrap align="right" class="left"><b>Target Price :</b></td>
    <td class="right" nowrap>
      <input type="text" name="data[target_price]" size="10" style="width:100px" value="<?=$item['target_price'];?>">
      <select name="data[target_currency]" style="width:300px">
        <?=formatHTMLOptions(getCurrencyList(false), $item['target_currency'])?>
      </select>

    </td>
  </tr>
  <tr>
    <td nowrap align="right" class="left"><b>Qty Needed :</b></td>
    <td class="right">
      <input type="text" name="data[qty_needed]" size="10" style="width:100px"  value="<?=$item['qty_needed'];?>">
    </td>
  </tr>
  <tr>
    <td nowrap align="right" class="left"><b>Expiry Date :</b></td>
    <td class="right">
      <input type="text" name="data[expiry_date]" id="expiry_date" size="10" value="<?=$item['expiry_date'];?>" style="width:100px"><?php addCalendarBtn("expiry_date");?>
    </td>
  </tr>
  <tr>
    <td align="right" nowrap class="left"><b>Active :</b></td>
    <td class="right">
      <select name="data[active]" style="width:100px">
		<option value="y" <?=$item['active'] == 'y' ? 'selected' : '';?>>Yes</option>
		<option value="n" <?=$item['active'] == 'n' ? 'selected' : '';?>>No</option>
	  </select>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="top">&nbsp;</td>
  </tr>
<?php
$statement = "SELECT *, DATE_FORMAT(date_added, '%d-%m-%Y') as date FROM wanted_offers WHERE wanted_id = '$id' ORDER BY date_added DESC";
$result    = $DB->query($statement);

if ($DB->num_rows($result) > 0) {
?>
  <tr>
    <td nowrap class="bottom" bgcolor="#EEEEEE" align="center"><b>Offers&nbsp;</b></td>
    <td class="bottom">&nbsp;</td>
  </tr>
<?php while ($row = $DB->fetch_array($result)) {
?>
<tr><td class="box" colspan="2">
<table width="100%" border="0" cellspacing="1" cellpadding="2">
  <tr bgcolor="#EEEEFF">
	<td width="20%"><?=$row['date'];?></td>
    <td width="40%"><b><a href="mailto:<?=$row['email'];?>"><?=$row['name'];?></a></b></td>
    <td width="40%">
      <div align="right"><?=$row['price'] . ' ' . $row['currency'];?></div>
    </td>
  </tr>
  <tr>
    <td colspan="3"><?=stripslashes($row['description']);?></td>
  </tr>
</table>
</td></tr>
<?php }
?>
  <tr>
    <td nowrap colspan="2">&nbsp;</td>

<?php
}

?>
  <tr>
    <td nowrap>&nbsp;</td>
    <td>
      <p>
		<input type="hidden" name="action" value="do_edit">
		<input type="hidden" name="id" value="<?=$id;?>">
        <input type="submit" name="add" value="Update">
        <input type="reset" name="clear" value="Clear">
      </p>
    </td>
  </tr>
</table>
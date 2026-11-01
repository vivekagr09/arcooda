<?php

global $_GLOBALS;

addCalendarHeader();

?>
<table border="0" align="center" cellpadding="4" cellspacing="0">
  <tr>
    <td nowrap class="bottom">&nbsp;</td>
    <td class="bottom">&nbsp;</td>
  </tr>
  <tr>
    <td nowrap align="right" class="left"><b>Wanted Item :</b></td>
    <td class="right">
      <input type="text" name="data[item]" size="40" style="width:400px">
    </td>
  </tr>
  <tr>
    <td nowrap align="right" class="left"><b>Category / Type :</b></td>
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
    echo '<option value="' . $row['id'] . '">' . stripslashes($row['parent_description'] . ' - ' . $row['name']) . '</option>' . "\n";
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
    echo '<option value="' . $row['id'] . '">' . stripslashes($row['manufacturer']) . '</option>' . "\n";
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
<?php
foreach (getCountryList(false) as $code => $name) {
    printf(
        '<option value="%s">%s</option>' . "\n",
        htmlspecialchars($code),
        htmlspecialchars($name)
    );
}
?>
	</select>
    </td>
  </tr>
  <tr>
    <td align="right" nowrap class="left"><b>Description :</b></td>
    <td class="right">
      <textarea name="data[description]" cols="20" rows="5" style="width:400px"></textarea>
    </td>
  </tr>
  <tr bgcolor="#EEEEEE">
    <td align="right" nowrap class="left"><b>Images :</b></td>
    <td class="right">
      <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
      <input type="file" name="images[image]" style="width:400px" /><br />
	  <?php html_or_bar('OR use image from products database');?>
	  <input type="text" name="product_ref_id" style="width:250px" id="pref_text">
	  <input type="button" name="search" value="Search" style="width:70px" onClick="MM_openBrWindow('product_ref.php?table=products&query='+document.admin_form.pref_text.value,'productRef','scrollbars=yes,resizable=yes,width=650,height=450')">
	  <input type="button" name="clear_search" value="Clear" style="width:70px" onClick="javascript:clearSearch()">
	  <input type="hidden" name="data[product_ref]" id="pref_id">
    </td>
  </tr>
  <tr bgcolor="#EEEEEEE">
    <td align="right" nowrap class="left"><b>Admin Notes :</b>
	<font color="red"><br>Admin notes are <br>never displayed<br>on the main site</font>
	</td>
    <td class="right">
      <textarea name="data[admin_notes]" cols="20" rows="3" style="width:400px"></textarea>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="centre">&nbsp;</td>
  </tr>
  <tr>
    <td nowrap align="right" class="left"><b>Customer Name :</b></td>
    <td class="right">
      <input type="text" name="data[customer_name]" size="10" style="width:200px">
    </td>
  </tr>
  <tr>
    <td nowrap align="right" class="left"><b>Customer Email :</b></td>
    <td class="right">
      <input type="text" name="data[customer_email]" size="10" style="width:200px">
    </td>
  </tr>
  <tr>
    <td nowrap align="right" class="left"><b>Target Price :</b></td>
    <td class="right">
      <input type="text" name="data[target_price]" size="10" style="width:100px">
      <select name="data[target_currency]" style="width:300px">
        <?=formatHTMLOptions(getCurrencyList(false), $_GLOBALS['site_options']['default_currency'])?>
      </select>

    </td>
  </tr>
  <tr>
    <td nowrap align="right" class="left"><b>Qty Needed :</b></td>
    <td class="right">
      <input type="text" name="data[qty_needed]" size="10" style="width:100px">
    </td>
  </tr>
  <tr>
    <td nowrap align="right" class="left"><b>Expiry Date :</b></td>
    <td class="right">
      <input type="text" name="data[expiry_date]" id="expiry_date" size="10" style="width:100px"><?php addCalendarBtn("expiry_date");?>
    </td>
  </tr>
  <tr>
    <td align="right" nowrap class="left"><b>Active :</b></td>
    <td class="right">
      <select name="data[active]" style="width:100px">
		<option value="y" selected>Yes</option>
		<option value="n">No</option>
	  </select>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="top">&nbsp;</td>
  </tr>
  <tr>
    <td nowrap>&nbsp;</td>
    <td>
      <p>
		<input type="hidden" name="action" value="do_add">
        <input type="submit" name="add" value="Add">
        <input type="reset" name="clear" value="Clear">
      </p>
    </td>
  </tr>
</table>
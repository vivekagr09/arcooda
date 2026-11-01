<?php
	$my_item = $DB->query_first("SELECT * FROM shipping_services WHERE prop_id='".$prop_id."'");
?>
<center>
<table align='center' border="0">
  <tr>
    <td class='left'><b>Name:</b></td>
    <td class='right'>
      <input type="text" name="name" size="40" style="width:400px" value="<?= stripslashes($my_item[name]) ?>">
    </td>
  </tr>
  <tr>
    <td class='left'><b>Account Number:</b></td>
    <td class='right'>
      <input type="text" name="accountno" size="40" style="width:400px" value="<?= stripslashes($my_item[accountno]) ?>">
    </td>
  </tr>
  <tr>
    <td class='left'><b>Base Currency:</b></td>
    <td class='right'>
		<select name="base_currency" style="width:190px">
      <?=formatHTMLOptions(getCurrencyList(false), $my_item['base_currency'])?>
    </td>
  </tr>
  <tr>
    <td class='left'><b>Customer Discount:</b></td>
    <td class='right'>
       <input type="text" name="customer_discount" size="40" style="width:400px" value="<?= stripslashes($my_item[customer_discount]) ?>">

    </td>
  </tr>
  <tr>
    <td class='left'><b>Store Discount:</b></td>
    <td class='right'>
       <input type="text" name="store_discount" size="40" style="width:400px" value="<?= stripslashes($my_item[store_discount]) ?>">

    </td>
</tr>
<tr>
    <td class='left'><b>Handling:</b></td>
    <td class='right'>
       <input type="text" name="handling" size="40" style="width:400px" value="<?= stripslashes($my_item[handling]) ?>">
    </td>
</tr>
<tr>
    <td class='left'><b>Handling as Percentage:</b></td>
    <td class='right'>
       <input type="text" name="hndasperc" size="40" style="width:400px" value="<?= stripslashes($my_item[hndasperc]) ?>">
    </td>
</tr>
  <tr>
    <td class='left'><b>Source:</b></td>
    <td class='right'>
		<select name="source" style="width:190px">
<?php
			$location_list = $DB->query("SELECT * FROM shipping_location ORDER BY name ASC");
			while ($row = $DB->fetch_array($location_list))
			{

				if ($my_item[source] == $row[code])
				{
					echo "<option value='$row[code]' selected >";
					echo $row[name];
					echo "	-	";
					echo $row[code];
					echo "</option>";
				}
				echo "<option value='$row[code]'>";
				echo $row[name];
				echo "	-	";
				echo $row[code];
				echo "</option>";
			}
?>
    </td>
  </tr>
  <tr>
    <td nowrap>&nbsp;</td>
    <td>
      <p>&nbsp;</p>
      <p>
		<input type="hidden" name="action" value="do_edit">
		<input type="hidden" name="prop_id" value="<?= $my_item[prop_id] ?>">
        <input type="submit" name="add" value="Update">
        <input type="reset" name="clear" value="Clear">
      </p>
    </td>
  </tr>
</table>
</center
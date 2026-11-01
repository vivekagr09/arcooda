<?php
if (intval($id) > 0) {
    $my_item = $DB->query_first("SELECT * FROM shipping_countries WHERE id = '$id'");
}
?>
<br><br>
<table align="center">
  <tr>
    <td class='left'>Destination:</td>
    <td class='right'>
		<select name="code" style="width:190px">
<?php
$result = $DB->query("SELECT * FROM shipping_location ORDER BY name ASC");
while ($row = $DB->fetch_array($result)) {
    if ($my_item[code] == $row[code]) {
        echo "<option value='$row[code]' selected>$row[name]";
        echo "	-	";
        echo $row[code];
        echo "</option>";
    } else {
        echo "<option value='$row[code]'>$row[name]";
        echo "	-	";
        echo $row[code];
        echo "</option>";
    }
}
?>
    </td>
  </tr>
  <tr>
    <td class='left'>Zone:</td>
    <td class='right'>
		<select name="zone" style="width:190px">
<?php

$prop_id    = $_REQUEST["courier"];
$code_query = $DB->query_first("SELECT * FROM shipping_services WHERE prop_id = '$prop_id'");

$table_name = "shipping_";
$table_name .= $prop_id;

$temp = $DB->table_fields($table_name);
array_shift($temp);
array_shift($temp);
echo formatHTMLOptions($temp, $my_item['zone']);
?>
		</SELECT>
    </td>
  </tr>
    <tr>
    <td class='left'>Time In Transit:</td>
    <td class='right'>
      <input type="text" name="tit" style="width:30px" value="<?=stripslashes($my_item[tit])?>">days
    </td>
  </tr>
    <tr>
   <td class='left'>Surcharge:</td>
    <td class='right'>
      <input type="text" name="surcharge" style="width:45px" value="<?=stripslashes($my_item[surcharge])?>">
    </td>
  </tr>
    <tr>
    <td class='left'>Weight Limit:</td>
    <td class='right'>
      <input type="text" name="weightlimit" style="width:30px" value="<?=stripslashes($my_item[weightlimit])?>">kg
    </td>
  </tr>
  <tr>
	  <td colspan='2'>
		<a href='location_admin?table=shipping_location&query='>Do you want to add a destination to the list?, Click Here</a>
	  </td>
  </tr>
  <tr>
    <td colspan='2' align='center'>
		<input type="hidden" name="action" value="do_edit">
		<input type="hidden" name="id" value="<?=$my_item[id]?>">
        <input type="submit" name="add" value="Update">
        <input type="reset" name="clear" value="Clear">
    </td>
  </tr>
</table>

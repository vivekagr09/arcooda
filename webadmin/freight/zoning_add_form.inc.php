<br><br>
<FORM ACTION="zoning_admin.php" METHOD="POST" ENCTYPE="multipart/form-data">
<table align="center">
  <tr>
    <td class='left'>Destination:</td>
    <td class='right'>
		<select name="code" style="width:190px">
<?php
$result = $DB->query("SELECT * FROM shipping_location ORDER BY name ASC");
while ($row = $DB->fetch_array($result)) {
    echo "<option value='$row[code]'>$row[name]";
    echo "	-	";
    echo $row[code];
    echo "</option>";
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
echo formatHTMLOptions($temp);
?>
    </td>
  </tr>
  <tr>
    <td class='left'><b>Time in Transit:</b></td>
    <td class='right'>
      <input type="text" name="tit" size="40" style="width:30px">days
    </td>
  </tr>
    <tr>
    <td class='left'><b>Surcharge:</b></td>
    <td class='right'>
      <input type="text" name="surcharge" size="40" style="width:45px">
    </td>
  </tr>
  <tr>
    <td class='left'><b>Weight Limit:</b></td>
    <td class='right'>
      <input type="text" name="weightlimit" style="width:30px">kg
    </td>
  </tr>
<tr>
  <td colspan='2'>
	<a href='location_admin?table=shipping_location&query='>Click Here to add a country to the destination list</a>
  </td>
 </tr>
  <tr>
    <td align='center' colspan='2'>
		<input type="hidden" name="action" value="do_add">
        <input type="submit" name="add" value="Add">
        <input type="reset" name="clear" value="Clear">
      </p>
    </td>
  </tr>
</table>
</FORM>

<?php
/**
 * @var string  $table
 * @var string  $id_field
 * @var int     $id
 * @var mixed[] $fields
 */
?><?php

global $DB;

$item = $DB->query_first("SELECT * FROM $table WHERE $id_field = '$id'");?>
<FORM ACTION="pricing_admin.php" METHOD="POST" ENCTYPE="multipart/form-data">
<table border="0" align="center">
<?php
foreach ($fields as $key => $v) {
    $field_name = ucwords(str_replace('_', ' ', $key));

    if ($key != $id_field) {

?>
			<tr>
              <td><b><?=('Shipping Weight' == $field_name) ? "Weight" : $field_name?>:</b></td>
              <td><b>
<?php
        if ('Shipping Weight' == $field_name) {
?>
                <input type="text" tooltip="The weight of a shipment" name="field_data[<?=$key?>]" value="<?=$item[$key]?>" style="width:100px">
<?php
        } else {
?>
                <input type="text" tooltip="The price to ship to zone <?=$field_name;?> for the above weight" name="field_data[<?=$key?>]"  value="<?=$item[$key]?>" style="width:62px">
<?php
        }
?>
              </b></td>
            </tr>


<?php
    }

    next($fields);
}

reset($fields);
?>
  <tr>
    <td nowrap>&nbsp;</td>
    <td>
      <p>&nbsp;</p>
      <p>
		<input type="hidden" name="action" value="do_add">
        <input type="submit" name="add" value="Add">
        <input type="reset" name="clear" value="Clear">
      </p>
    </td>
  </tr>

</table>
</FORM>


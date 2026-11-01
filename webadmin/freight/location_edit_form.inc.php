<?php
	show_locked_msg($table, $id);

	$item = $DB->query_first("SELECT * FROM $table WHERE $id_field = '$id'");     ?>

		<div align="center">
          <table border=0 cellpadding=2>
            <tr>
              <td colspan="2"><?= $error_msg ?>&nbsp;</td>
            </tr>
<?php
	for ($i = 0; $i < count($fields); $i++)
	{
		$key = key($fields);
		$field_name = ucwords(str_replace('_', ' ', $key));

?>
			<tr class="<?= $i%2 == 0 ? "tableRowAlt" : "tableRow" ?>">
              <td><b><?= $field_name ?>:</b></td>
              <td>
                <input type="text" name="field_data[<?= $key ?>]" size="20" value="<?= stripslashes($item[$key]) ?>" style="width:400px">
              </td>
            </tr>
<?php
		next($fields);
	}
	reset($fields);
?>
            <tr>
              <td colspan="2">
                <center>
		<input type="hidden" name="id" value="<?= $id ?>">
		<input type="hidden" name="action" value="do_edit">
        <input type="submit" name="add" value="Update">
        <input type="reset" name="clear" value="Clear">
                </center>
              </td>
            </tr>
          </table>
        </div><br>
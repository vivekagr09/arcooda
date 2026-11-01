<?php
$item = $DB->query_first("SELECT * FROM $table WHERE $id_field = '$id'");
?>

		<center>
          <table border=0 cellpadding=2 >
            <tr>
              <td colspan="2"><?php echo  $error_msg ?>&nbsp;</td>
            </tr>
<?php
	for ($i = 0; $i < count($fields); $i++)
	{
		$key = key($fields);
		$field_name = ucwords(str_replace('_', ' ', $key));

		if ($key != $id_field)
		{

?>

			<tr>
              <td><B><?php echo  $field_name ?>:</b></td>
<?php
                 if($field_name = "country")
                 {
                    //Do x
                 }
                 if($field_name = "date_added")
                 {
                    //Do y
                 }
                 if($field_name = "active")
                 {
                    //Do z
                 }
                 else
                 {  ?>

                 <td>
                     <input type="text" name="field_data[<?php echo  $key ?>]" size="20" value="<?php echo  $item[$key] ?>" style="width:400px">
                 </td>
<?php
                 }
?>

            </tr>


<?php
		}

		next($fields);
	}

	reset($fields);
?>

            <tr>
              <td colspan="2">
                <center>
		<input type="hidden" name="action" value="do_add">
        <input type="submit" name="add" value="Add">
        <input type="reset" name="clear" value="Clear">
                </center>
              </td>
            </tr>
          </table>
        </center>
        <p>

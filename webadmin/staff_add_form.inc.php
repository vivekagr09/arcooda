<?php
if(isset($_SESSION['error_user']) && count($_SESSION['error_user']) >0 ){
      echo '<div style="color:red;text-align:left;padding:5px;">';
        foreach($_SESSION['error_user'] as $error){
          echo '<div> '.$error.' </div>'; 
        }
      echo '</div>';
  }
$item = $DB->query_first("SELECT * FROM $table WHERE $id_field = '$id'");?>

		<center>
          <table border=0 cellpadding=2 >
            <tr>
              <td colspan="2"><?=$error_msg;?>&nbsp;</td>
            </tr>
<?php for ($i = 0; $i < count($fields); $i++) {
    $key        = key($fields);
    $field_name = ucwords(str_replace('_', ' ', $key));

    if ($key != $id_field) {

?>

			<tr>
              <td><B><?=$field_name;?>:</b></td>
              <td>
                <input type="text" name="field_data[<?=$key;?>]" size="20" value="<?=$item[$key];?>" style="width:400px">
              </td>
            </tr>


<?php }

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
<?php
if(isset($_SESSION['error_user']))
unset($_SESSION['error_user']);
?>
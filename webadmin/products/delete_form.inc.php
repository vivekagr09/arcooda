<?php
	show_locked_msg($table, $id);

	$item= $DB->query_first("SELECT * FROM $table WHERE $id_field = '$id'");     ?>

        <center>
          <table border=0>

<?php

	for ($i = 0; $i < count($item); $i++)
	{
		$key = key($item);

		if (!is_int($key))
		{
			echo '<tr bgcolor="'.(($i/2)%2 == 0?"#EEEEEE":"#FFFFFF").'">';
			echo '<td align="right"><b>'.$key.':</b></td>';
			echo '<td>'.$item[$key].'</td>';
			echo '</tr>';
		}
		next($item);
	}


?>

            <tr>
              <td colspan="2">
			  <br><br>
                <center>
	    <input type="hidden" name="id" value="<?php echo  $id ?>">
		<input type="hidden" name="action" value="do_delete">
        <input type="submit" name="add" value="Yes">
        <input type="reset" name="clear" value="No" onClick="javascript:history.back()">
                </center>
              </td>
            </tr>
          </table>
        </center>

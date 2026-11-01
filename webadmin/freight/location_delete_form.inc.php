<?php
	show_locked_msg($table, $id);

	$result = $DB->query_first("SELECT * FROM $table WHERE $id_field = '$id'");
?>
        <div align="center">
          <table border=0>
<?php
	foreach($result AS $key => $value)
	{
		if(!is_int($key))
		{
			$shaded = !$shaded;
			$class = $shaded ? "tableRow" : "tableRowAlt";
			echo "<tr class=\"$class\">";
			echo "<td align=\"right\"><b>$key:</b></td>";
			echo "<td>".stripslashes($value)."</td>";
			echo "</tr>";
		}
	}
?>
            <tr>
              <td colspan="2">
			  <br><br>
                <div align="center">
	    <input type="hidden" name="id" value="<?= $id ?>">
		<input type="hidden" name="action" value="do_delete">
        <input type="submit" name="add" value="Yes">
        <input type="reset" name="clear" value="No" onClick="javascript:history.back()">
                </div>
              </td>
            </tr>
          </table>
        </div><br>

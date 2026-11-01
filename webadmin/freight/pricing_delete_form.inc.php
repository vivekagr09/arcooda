<?php
	show_locked_msg($table, $id);
	$item= $DB->query_first("SELECT * FROM $table WHERE $id_field = '$id'");
	$item2= $DB->query_first("SELECT * FROM $table WHERE $id_field = '$id'"); ?>
<table align=center>
<tr>
<?php
			for ($i = 0; $i < count($item); $i++)
			{
				$key = key($item);

				if (!is_int($key))
				{
					echo '<td class="tableHeader">'.$key.':</td>';
				}
				next($item);
			}
?>
</tr>
<tr>
<?php
	for ($i = 0; $i < count($item2); $i++)
	{
		$key2 = key($item2);

		if (!is_int($key2))
		{
			echo '<td class="tableRowAlt">'.stripslashes($item2[$key2]).'</td>';
		}
		next($item2);
	}
?>
</tr>
            <tr>
              <td colspan='100%' align='center'>
				<input type="hidden" name="id" value="<?= $id ?>">
				<input type="hidden" name="action" value="do_delete">
				<input type="submit" name="add" value="Yes">
				<input type="reset" name="clear" value="No" onClick="javascript:history.back()">
              </td>
            </tr>
          </table>

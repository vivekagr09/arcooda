<?php

$email = $DB->query_first("SELECT * FROM mailinglist WHERE id = '$id'");
?>
          <table border="0" align="center">
<?php

for ($i = 0; $i < count($email); $i++) {
    $key = key($email);

    if (!is_int($key)) {
        echo '<tr bgcolor="' . (($i / 2) % 2 == 0 ? "#EEEEEE" : "#FFFFFF") . '">';
        echo '<td align="right"><b>' . $key . ':</b></td>';
        echo '<td>' . $email[$key] . '</td>';
        echo '</tr>';
    }
    next($email);
}

?>
            <tr>
              <td colspan="2">
				<input type="hidden" name="id" value="<?=$id;?>">
				<input type="hidden" name="action" value="do_delete">
				<input type="submit" name="add" value="Yes">
				<input type="button" name="clear" value="No" onClick="javascript:history.back()">
              </td>
            </tr>
          </table>
        <br>
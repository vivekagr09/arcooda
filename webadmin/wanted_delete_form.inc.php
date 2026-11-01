<?php

$item = $DB->query_first("SELECT * FROM wanted WHERE id = '$id'");

?>
<table border="0" align="center">
  <tr>
    <td nowrap align="right"><b>ID :</b></td>
    <td>
      <?=stripslashes($item[id]);?>
    </td>
  </tr>
  <tr bgcolor="#EEEEEE">
    <td nowrap align="right"><b>Item:</b></td>
    <td>
      <?=stripslashes($item[item]);?>
    </td>
  </tr>
  <tr>
    <td nowrap align="right"><b>Date Added :</b></td>
    <td>
      <?=stripslashes($item[date_added]);?>
    </td>
  </tr>
  <tr bgcolor="#EEEEEE">
    <td nowrap align="right"><b>Acitve:</b></td>
    <td>
      <?=stripslashes($item[active]);?>
    </td>
  </tr>
  <tr>
    <td nowrap>&nbsp;</td>
    <td>
      <p><br><b>Do you want to delete this screenshot?</b></p>
      <p>
	    <input type="hidden" name="id" value="<?=$id;?>">
		<input type="hidden" name="action" value="do_delete">
        <input type="submit" name="add" value="Yes">
        <input type="reset" name="clear" value="No" onClick="javascript:history.back()">
      </p>
    </td>
  </tr>
</table>

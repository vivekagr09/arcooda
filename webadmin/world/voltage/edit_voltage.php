<table border="0" align="center" cellpadding="4" cellspacing="0">
<form action="voltage.php" method="POST">
  <tr>
    <td nowrap class="bottom">&nbsp;</td>
    <td class="bottom">&nbsp;</td>
  </tr>
  </tr>
  <tr>
    <td class="left" nowrap><b>Voltage Value :</b></td>
<?php
$str = "SELECT * FROM `world`.`voltage` WHERE id = '$id'";
$res = $DB->query_first($str);
?>
    <td class="right"><input type="text" name="data[value]" value="<?=$res['value']?>" size="40" style="width:400px"></td>
  </tr>
    <tr>
    <td colspan="2" class="top">&nbsp;</td>
  </tr>
  <tr>
    <td nowrap>&nbsp;</td>
    <td> <p>
		<input type="hidden" name="id" value="<?=$id?>">
        <input type="hidden" name="action" value="do_edit">
        <input type="submit" name="do_action" value="Update">
        <input type="reset" name="clear" value="Clear">
      </p></td>
  </tr>
  </form>
</table>
<?php
if (intval($id) > 0)
{
	$my_item = $DB->query_first("SELECT * FROM `coinop_report` WHERE `id` = '$id'");
}
?>
<table border="0" align="center">

 <tr bgcolor="#EEEEEE">
    <td nowrap align="right"><b>Title :</b></td>
    <td>
      <input type="text" name="Title" size="40" style="width:370px" value="<?= stripslashes($my_item[Title]) ?>">
    </td>
  </tr>
  <tr bgcolor="#EEEEEE">
    <td nowrap align="right"><b>Description:</b></td>
    <td>
      <textarea name="Description" cols="20" rows="15" style="width:480px"><?= stripslashes($my_item[Description]) ?></textarea>
    </td>
  </tr>
  <tr>
    <td nowrap>&nbsp;</td>
    <td>
      <p>&nbsp;</p>
      <p>
		<input type="hidden" name="action" value="do_edit">
		<input type="hidden" name="id" value="<?= $my_item[id] ?>">
        <input type="submit" name="add" value="Update">
        <input type="reset" name="clear" value="Clear">
      </p>
    </td>
  </tr>
</table>

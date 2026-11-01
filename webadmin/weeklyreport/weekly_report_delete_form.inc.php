<?php
$item = $DB->query_first("SELECT *, DATE_FORMAT(`date`, '%Y%m%d') AS `date` FROM `highwaygroup`.`weekly_report` WHERE `id` = '$id'");

?>
<table border="0" align="center">
  <tr>
    <td nowrap align="right"><b>ID :</b></td>
    <td>
      <?= $item['id'] ?>
    </td>
  </tr>
  <tr bgcolor="#EEEEEE">
    <td nowrap align="right"><b>Date:</b></td>
    <td>
      <?= $item['date'] ?>
    </td>
  </tr>
  <tr bgcolor="#EEEEEE">
    <td nowrap align="right"><b>Title:</b></td>
    <td>
      <?= $item['Title'] ?>
    </td>
  </tr>
    <tr bgcolor="#EEEEEE">
    <td nowrap align="right"><b>Description:</b></td>
    <td>
      <?= $item['Description'] ?>
    </td>
   <tr>
    <td nowrap>&nbsp;</td>
    <td>
      <p><br><b>Do you want to delete this destination?</b></p>
      <p>
	  	<input type="hidden" name="from" value="<?= $from?>">
		<input type="hidden" name="to" value="<?= $to?>">
	    <input type="hidden" name="id" value="<?= $id ?>">
		<input type="hidden" name="action" value="do_delete">
        <input type="submit" name="add" value="Yes">
        <input type="reset" name="clear" value="No" onClick="javascript:history.back()">
      </p>
    </td>
  </tr>
</table>

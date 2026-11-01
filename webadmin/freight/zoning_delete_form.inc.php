<?php
$item = $DB->query_first("SELECT * FROM shipping_countries WHERE id = '$id'");
?>
<br><br>
<table align="center" width="40%">
  <tr>
    <td width="35%" class="left" >ID :</td>
    <td width="65%" class="right">
      <?= stripslashes($item[id]) ?>
    </td>
  </tr>
  <tr>
    <td class="left">Destination:</td>
    <td class="right">
      <?= stripslashes($item[code]) ?>
    </td>
  </tr>
  <tr>
    <td class="left" >Zone:</td>
    <td class="right">
      <?= stripslashes($item[zone]) ?>
    </td>
  </tr>
    <tr>
    <td class="left">Time in Transit</td>
    <td class="right">
      <?= stripslashes($item[tit]) ?>
    </td>
  </tr>
    <tr>
    <td class="left">Surcharge:</td>
    <td class="right">
      <?= stripslashes($item[surcharge]) ?>
    </td>
  </tr>
    <tr>
    <td class="left">Weight limit:</td>
    <td class="right">
      <?= stripslashes($item[weightlimit]) ?>
    </td>
  </tr>
  <tr>
    <td colspan='2' align='center'>
		Do you want to delete this destination?
	</td>
	</tr>
	<tr>
	<td colspan='2' align='center'>
	    <input type="hidden" name="id" value="<?= $id ?>">
		<input type="hidden" name="action" value="do_delete">
        <input type="submit" name="add" value="Yes">
        <input type="reset" name="clear" value="No" onClick="javascript:history.back()">
    </td>
  </tr>
</table>

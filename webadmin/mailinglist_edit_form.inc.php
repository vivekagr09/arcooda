<?php

$email = $DB->query_first("SELECT * FROM mailinglist WHERE id = '$id'");
?>
          <table border=0 cellpadding=2 align="center">
            <tr>
              <td colspan="2"><?=$error_msg;?>&nbsp;</td>
            </tr>
            <tr>
              <td>Full Name:</td>
              <td>
                <input type="text" name="name" size="20" value="<?=$email['name'];?>" style="width:300px">
              </td>
            </tr>
            <tr>
              <td><B>Email Address:</b></td>
              <td>
                <input type="text" name="email" size="20" value="<?=$email['email'];?>" style="width:300px">
              </td>
            </tr>
            <tr>
              <td>Country:</td>
              <td>
      <select name="country" style="width:300px">
<?php
$emptySel = "" == $email['country'] ? "selected" : "";
echo "<option value=\"$emptySel\"></option>";
?>
<?=formatHTMLOptions(getCountryList(false), $email['country'])?>
      </select>
              </td>
            </tr>
            <tr>
              <td>Company Name:</td>
              <td>
                <input type="text" name="company" size="20" value="<?=$email['company'];?>" style="width:300px">
              </td>
            </tr>
            <tr>
              <td>Comments:</td>
              <td>
                <textarea name="comments" cols="20" rows="7" style="width:300px"><?=$email['comments'];?></textarea>
              </td>
            </tr>
  <tr>
    <td nowrap><b>Active:</b></td>
    <td>
      <select name="active" style="width:70px">
		<option value="1" <?=$email['active'] == '1' ? "selected" : '';?>>Yes</option>
		<option value="0" <?=$email['active'] == '0' ? "selected" : '';?>>No</option>
	  </select>
    </td>
  </tr>
	<tr>
	  <td colspan="2">
		<input type="hidden" name="id" value="<?=$id;?>">
		<input type="hidden" name="action" value="do_edit">
		<input type="submit" name="add" value="Update">
		<input type="reset" name="clear" value="Clear">
	  </td>
	</tr>
  </table>
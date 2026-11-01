<?php
$id = intval($_GET['id']);

$item = $DB->query_first("SELECT * FROM $table WHERE $id_field = '$id'");
?>

<center>
  <table border="0" cellpadding="4" cellspacing="0">
              <tr>
              <td colspan="2" nowrap><div align="center"><b><font color="#990000">Hint:</font>
                  </b>adding <strong>[date]</strong> to the description will automatically
                  generate <br>
                  the date. Similarly <strong>[banner]</strong> will add the banner
                  image.</div></td>
            </tr>

  <tr>
    <td nowrap class="bottom">&nbsp;</td>
    <td class="bottom">&nbsp;</td>
  </tr>
    <tr>
      <td class="left"><b>Mailout Type:</b></td>
      <td class="right"> <input type="text" name="data[type_name]" size="20" style="width:300px" value="<?=$item['type_name']?>"/>
      </td>
    </tr>
    <tr>
      <td class="left"><b>Text Description:</b></td>
      <td class="right"> <textarea name="data[text_desc]" cols="20" rows="3" style="width:300px"><?=$item['text_desc']?></textarea>
      </td>
    </tr>
    <tr>
      <td class="left"><b>HTML Description:</b></td>
      <td class="right"> <textarea name="data[html_desc]" cols="20" rows="3" style="width:300px"><?=$item['html_desc']?></textarea>
      </td>
    </tr>
  <tr>
    <td colspan="2" nowrap class="centre">&nbsp;</td>
  </tr>
    <tr>
      <td class="left"><b>Current Banner:</b></td>
      <td class="right"> <input type="text" size="20" style="width:300px" value="<?=$item['banner_image']?>" disabled/>
      </td>
    </tr>
    <tr>
      <td class="left"><b>New Banner Image:</b></td>
      <td class="right">
        <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
        <input type="file" name="images[banner_image]" size="20" style="width:300px" />
      </td>
    </tr>
  <tr>
    <td colspan="2" class="top">&nbsp;</td>
  </tr>
  <tr>
      <td colspan="2"> <center>
          <input type="hidden" name="id" value="<?=$id?>" />
          <input type="hidden" name="action" value="do_edit" />
          <input type="submit" name="add" value="Update" />
          <input type="reset" name="clear" value="Reset" />
        </center></td>
    </tr>
  </table>
</center>

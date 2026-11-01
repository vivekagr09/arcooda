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
      <td class="left"><b>New Mailout Type:</b></td>
      <td class="right"> <input type="text" name="data[type_name]" size="20" style="width:300px" />
      </td>
    </tr>
    <tr>
      <td class="left"><b>Text Description:</b></td>
      <td class="right"> <textarea name="data[text_desc]" cols="20" rows="3" style="width:300px"></textarea>
      </td>
    </tr>
    <tr>
      <td class="left"><b>HTML Description:</b></td>
      <td class="right"> <textarea name="data[html_desc]" cols="20" rows="3" style="width:300px"></textarea>
      </td>
    </tr>
  <tr>
    <td colspan="2" nowrap class="centre">&nbsp;</td>
  </tr>
    <tr>
      <td class="left"><b>Banner Image:</b></td>
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
          <input type="hidden" name="action" value="do_add" />
          <input type="submit" name="add" value="Add" />
          <input type="reset" name="clear" value="Clear" />
        </center></td>
    </tr>
  </table>
</center>

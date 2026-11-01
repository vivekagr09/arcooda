<table border="0" align="center" cellpadding="4" cellspacing="0">
<form action="plugs.php" method="POST" enctype="multipart/form-data">
  <tr>
    <td nowrap class="bottom">&nbsp;</td>
    <td class="bottom">&nbsp;</td>
  </tr>
  </tr>
  <tr>
    <td class="left" nowrap><b>New Plug Type :</b></td>
    <td class="right"><input type="text" name="data[value]" size="40" style="width:400px"></td>
  </tr>
  <tr>
    <td class="left" align="left" nowrap><b>Plug Image :</b></td>
    <td class="right">
      <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
      <input name="file_path" type="file" />
    </td>
  </tr>
    <tr>
    <td colspan="2" class="top">&nbsp;</td>
  </tr>
  <tr>
    <td nowrap>&nbsp;</td>
    <td> <p>
        <input type="hidden" name="action" value="do_add">
        <input type="submit" name="do_action" value="Add">
        <input type="reset" name="clear" value="Clear">
      </p></td>
  </tr>
  </form>
</table>
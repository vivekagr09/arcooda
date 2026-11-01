<form accept-charset="utf-8" autocomplete="off" method="post" action="coinop_report.php">
<table border="0" align="center">
  <tr>
    <td nowrap class="right"><b>Title:</b></td>
    <td class="right">
      <input type="text" name="Title" size="40" style="width:370px" tooltip="Any text longer than the space provided will be cut">
    </td>
  </tr>
  <tr>
    <td nowrap class="right"><b>Description:</b></td>
    <td class="right">
      <textarea name="Description" rows="15" style="width:480px" tooltip="Please write you report in here"></textarea>
    </td>
  </tr>
  <tr>
    <td nowrap class="right">&nbsp;</td>
    <td class="right">
      <p>&nbsp;</p>
      <p>
		<input type="hidden" name="action" value="do_add">
        <input type="submit" name="add" value="Add" tooltip="Click here to add your report">
        <input type="reset" name="clear" value="Clear" tooltip="Click here to start again">
      </p>
    </td>
  </tr>

</table>
</FORM>

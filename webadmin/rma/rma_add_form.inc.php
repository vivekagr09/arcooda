<?php
global $_GLOBALS;

?>
<br />
<br />
<table border="0" align="center" cellpadding="4" cellspacing="0">
   <tr>
    <td nowrap class="bottom">&nbsp;</td>
    <td class="bottom">&nbsp;</td>
  </tr>
  <tr>
    <td nowrap class="left" height="28"><div align="left"><b>Customer :</b></div></td>
    <td class="right"><input type="text" name="data[customer]" size="40" style="width:300px">
    </td>
  </tr>
  <tr>
    <td nowrap class="left" height="28"><div align="left"><b>Customer Phone No:</b></div></td>
    <td class="right"><input type="text" name="data[phone_no]" size="40" style="width:300px">
    </td>
  </tr>
  <tr>
    <td nowrap class="left" height="28"><div align="left"><b>Customer Invoice No:</b></div></td>
    <td class="right"><input type="text" name="data[invoice_no]" value="HG#11111" onclick="this.value = ''" size="40" style="width:300px">
    </td>
  </tr>
  <tr>
    <td nowrap class="left" align="left"><b>Customer Reference# :</b></td>
    <td class="right"> <input type="text" name="data[cust_ref]" size="40" style="width:300px"></td>
  </tr>
  <tr>
    <td nowrap class="left" align="left"><b>Name of Item :</b></td>
    <td class="right"> <input type="text" name="data[item]" size="40" style="width:300px"></td>
  </tr>
  <tr>
    <td nowrap class="left" align="left"><b>Serial# :</b></td>
    <td class="right"> <input type="text" name="data[serial]" size="40" style="width:300px"></td>
  </tr>
  <tr>
    <td colspan="2" nowrap class="centre">&nbsp;</td>
  </tr>
  <tr>
    <td nowrap class="left" align="left" ><b>Fault :</b></td>
    <td class="right"><textarea name="data[fault]" cols="40" rows="5" id="data[fault]" style="width:300px"></textarea></td>
  </tr>
  <tr>
    <td colspan="2" nowrap class="centre">&nbsp;</td>
  </tr>
  <tr>
    <td nowrap class="left" align="left"><b>Priority :</b></td>
    <td class="right"><select name="data[priority]" style="width:300px">
<?php
$result = $DB->query("SELECT * FROM rma_priority ORDER BY level ASC");

while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['description'] . '"';
    echo trim($_GLOBALS['site_options']['default_priority']) == trim($row['description']) ? " selected " : "";
    echo '>' . ($row['description']) . '</option>' . "\n";
}
?>
      </select>
	  <a href="rma_admin.php?table=rma_priority&action=add" target="_blank">Add</a>
      | <a href="rma_admin.php?table=rma_priority" target="_blank">Edit</a>
	  </td>
  </tr>

  <tr>
    <td nowrap class="left" align="left"><b>Sent_to :</b></td>
    <td class="right"><select name="data[sent_to]" style="width:300px">
<?php
$result = $DB->query("SELECT * FROM rma_repairers ORDER BY repairer ASC");

while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['repairer'] . '"';
    echo trim($_GLOBALS['site_options']['default_repairer']) == trim($row['repairer']) ? " selected " : "";
    echo '>' . ($row['repairer']) . '</option>' . "\n";
}
?>
      </select>
	  <a href="rma_admin.php?table=rma_repairers&action=add" target="_blank">Add</a>
      | <a href="rma_admin.php?table=rma_repairers" target="_blank">Edit</a>
	  </td>
  </tr>

  <tr>
    <td nowrap class="left" align="left"><b>Status :</b></td>
    <td class="right"><select name="data[status]" style="width:300px">
<?php
$result = $DB->query("SELECT * FROM rma_status ORDER BY status DESC");

while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['status'] . '"';
    echo trim($_GLOBALS['site_options']['default_status']) == trim($row['status']) ? " selected " : "";
    echo '>' . ($row['status']) . '</option>' . "\n";
}
?>
      </select>
	  <a href="rma_admin.php?table=rma_status&action=add" target="_blank">Add</a>
      | <a href="rma_admin.php?table=rma_status" target="_blank">Edit</a>
	  </td>
  </tr>
  <tr>
    <td colspan="2" nowrap class="centre">&nbsp;</td>
  </tr>
  <tr>
    <td nowrap class="left" align="left"><b>Comments :</b> <br><font color="red">(comments will be shown <br>on admin section only)</font></td>
    <td class="right"><textarea name="data[comments]" cols="40" rows="5" style="width:300px"></textarea></td>
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

</table>

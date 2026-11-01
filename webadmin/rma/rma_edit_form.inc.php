<?php
//search statement
$statement = "SELECT * FROM rma_service WHERE id='$id'";

//echo "Id_selected: $id";
$result   = $DB->query($statement);
$rma      = $DB->fetch_array($result);
$customer = $rma['customer'];
$cust_ref = $rma['cust_ref'];
$item     = $rma['item'];
$serial   = $rma['serial'];
$fault    = trim($rma['fault']);
//echo $fault;
$priority = $rma['priority'];
$sent_to  = $rma['sent_to'];
$status   = $rma['status'];
$comments = $rma['comments'];
?>
<br><br>
<table border="0" align="center" cellpadding="4" cellspacing="0">
   <tr>
    <td nowrap class="bottom">&nbsp;</td>
    <td class="bottom">&nbsp;</td>
  </tr>
  <tr>
    <td nowrap class="left" align="left" height="28"><b>Customer :</b></td>
    <td class="right"> <input type="text" name="data[customer]" value="<?=$customer?>"style="width:300px">
    </td>
  </tr>
  <tr>
    <td nowrap class="left" align="left" height="28"><b>Phone :</b></td>
    <td class="right"> <input type="text" name="data[phone_no]" value="<?=$rma["phone_no"]?>"style="width:300px"></td>
  </tr>
  <tr>
    <td nowrap class="left" align="left" height="28"><b>Invoice :</b></td>
    <td class="right"> <input type="text" name="data[invoice_no]" value="<?=$rma["invoice_no"]?>"style="width:300px"></td>
  </tr>
  <tr>
    <td nowrap class="left" align="left"><b>Customer Reference# :</b></td>
    <td class="right"> <input type="text" name="data[cust_ref]" size="40" style="width:300px" value="<?=$cust_ref?>"></td>
  </tr>
  <tr>
    <td nowrap class="left" align="left"><b>Name of Item :</b></td>
    <td class="right"> <input type="text" name="data[item]" size="40" style="width:300px" value="<?=$item?>"></td>
  </tr>
  <tr>
    <td nowrap class="left" align="left"><b>Serial# :</b></td>
    <td class="right"> <input type="text" name="data[serial]" size="40" style="width:300px" value="<?=$serial?> "></td>
  </tr>
 <tr>
    <td colspan="2" nowrap class="centre">&nbsp;</td>
  </tr>
  <tr>
    <td nowrap class="left" align="left"><b>Fault :</b></td>
    <td class="right"><textarea name="data[fault]" cols="40" rows="8" style="width:300px"><?=$fault?> </textarea></td>
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
    echo $priority == trim($row['description']) ? " selected " : "";
    echo '>' . ($row['description']) . '</option>' . "\n";
}
?>
      </select>
	  <a href="rma_admin.php?table=rma_priority&action=add" target="_blank">Add</a>
      | <a href="rma_admin.php?table=rma_priority&&query=" target="_blank">Edit</a>
	  </td>
  </tr>

  <tr>
    <td nowrap class="left" align="left"><b>Sent_to :</b></td>
    <td class="right"><select name="data[sent_to]" style="width:300px">
<?php
$result = $DB->query("SELECT * FROM rma_repairers ORDER BY repairer ASC");

while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['repairer'] . '"';
    echo $sent_to == trim($row['repairer']) ? " selected " : "";
    echo '>' . ($row['repairer']) . '</option>' . "\n";
}
?>
      </select>
	  <a href="rma_admin.php?table=rma_repairers&action=add" target="_blank">Add</a>
      | <a href="rma_admin.php?table=rma_repairers&&query=" target="_blank">Edit</a>
	  </td>
  </tr>

  <tr>
    <td nowrap class="left" align="left"><b>Status :</b></td>
    <td class="right"><select name="data[status]" style="width:300px">
<?php
$result = $DB->query("SELECT * FROM rma_status ORDER BY status DESC");

while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['status'] . '"';
    echo $status == trim($row['status']) ? " selected " : "";
    echo '>' . ($row['status']) . '</option>' . "\n";
}
?>
      </select>
	  <a href="rma_admin.php?table=rma_status&action=add" target="_blank">Add</a>
      | <a href="rma_admin.php?table=rma_status&&query=" target="_blank">Edit</a>
	  </td>
  </tr>
 <tr>
    <td colspan="2" nowrap class="centre">&nbsp;</td>
  </tr>
  <tr>
     <td class="left" align="left"><b>Comments :</b> <br><font color="red">(comments will be shown <br>on admin section only)</font></td>
	 <td class="right"><textarea name="data[comments]" cols="40" rows="5" style="width:300px" ></textarea></td>
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
        <input type="reset" name="clear" value="Reset">
		<!--
		<input type="hidden" name="action" value="print">
		<input type="submit" name="do_action" value="Print Current RMA">
		-->
      </p></td>
  </tr>
</table>
<br><br>
</form>
<form target='_blank' action="rma_print_form.php?id=<?=$id?>" method="post" name="thingo">
<div align="center"><b>
<font size="+1">Print Current RMA</font><br>

<SCRIPT LANGUAGE="JavaScript">
	function uncheck()
	{
		if(document.thingo.warranty.checked == true)
		{
			document.thingo.repair.checked=false;
			document.thingo.reference.checked=false;
		}
	}
</script>
	<table>
	<tr>
		<td align='left'><input type='checkbox' name='details' onClick="uncheck()">Customer Details</input></td>
	</tr>
	<tr>
		<td align='left'><input type='checkbox' name='comments' onClick="uncheck()">With Job Comments</input></td>
	</tr>
	<tr>
		<td align='left'><input type='checkbox' name='handle' onClick="uncheck()">With Handling Details</input></td>
	</tr>
	<tr>
		<td align='left'><input type='checkbox' name='repair' onClick="uncheck()">With Repair Charges Field</input></td>
	</tr>
	<tr>
		<td align='left'><input type='checkbox' name='reference' onClick="uncheck()">With Reference Invoice # Field</input></td>
	</tr>
	<tr>
		<td align='left'><input type='checkbox' name='warranty' onClick="uncheck()">As a Warranty Repair</input></td>
	</tr>
	<tr>
        <td align='center'><input type="submit" name="print" value="Print"></td>
	</tr>
</table>
</form>
</div>
<br><br>

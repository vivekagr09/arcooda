<?php
if ("" != $errorMsg) {
    echo "<font color=\"#FF0000\"><b>The following errors occured</b><br>\r\n" .
        "<blockquote>$errorMsg</blockquote></font>";
}

?>
<br>
<table border="0" align="center" cellpadding="4" cellspacing="1" width="60%">
  <tr>
    <td nowrap class="left">Title :</td>
	<td class="right">
	<input type="text" name="jobs[topic]" value="<?= htmlSafe($_POST['jobs']['topic']) ?>" style="width:400px">
	</td>
  </tr>
  <tr>
    <td nowrap class="left">Description :</td>
    <td class="right">
      <textarea name="jobs[description]" cols="20" rows="5" style="width:400px"><?= htmlSafe($_POST['jobs']['description']) ?></textarea>
    </td>
  </tr>
  <tr>
    <td nowrap class="left">Reference :</td>
	<td class="right"><table border="0" width="100%" cellpadding="2" cellspacing="0">
		<tr>
			<td class="right">Quote ID :</td>
			<td class="right"><select name="jobs[quote_id]" style="width:100px">
			<option value="-1" selected>N/A</option>
<?php
if (is_array($quotes)) {
    foreach ($quotes as $quote_id) {
        $selected = $_POST['jobs']['quote_id'] == $quote_id ? "selected" : "";
        echo "<option value=\"$quote_id\" $selected>Q#" . padQuoteNum($quote_id) . "</option>\r\n";
    }
}
?></select></td>
			<td class="right">Invoice ID :</td>
			<td class="right"><select name="jobs[invoice_id]" style="width:100px">
			<option value="-1" selected>N/A</option>
<?php
if (is_array($invoices)) {
    foreach ($invoices as $quote_id => $invoice_id) {
        $selected = $_POST['jobs']['invoice_id'] == $quote_id ? "selected" : "";
        echo "<option value=\"$quote_id\" $selected>I#" . padQuoteNum($invoice_id) . "</option>\r\n";
    }
}
?></select>
			</td></tr></table>
		</td>
  </tr>
  <tr>
    <td nowrap class="left">Priority :</td>
	<td class="right">
	<select name="jobs[priority_id]" style="width:100px">
	<?= getJobPriorityList($_POST['jobs']['priority_id']) ?>
	</select>
	</td>
  </tr>
  <tr>
    <td nowrap class="left">Assign To :</td>
	<td class="right">
      <select name="jobs[receiver]" style="width:100px">
        <option value=""></option>
        <?= getUserList($_POST['jobs']['receiver']) ?>
      </select>
    </td>
  </tr>
</table><br>
<input type="hidden" name="action" value="do_add">
<input type="hidden" name="user" value="<?= $user ?>">
<div align="center">
	<input type="submit" value="Add Job">
</div>
<?php
moduleLoad('invoice'); //invoiceGetValue

$sql = "SELECT * FROM jobs_details jd
			LEFT JOIN jobs j ON jd.job_id = j.job_id
			LEFT JOIN jobs_priority jp ON jp.priority_id = jd.priority_id
			WHERE jd.id=" . $_REQUEST['id'];
$job_details = $DB->query_first($sql);

if ("" != $errorMsg) {
    echo "<font color=\"#FF0000\"><b>The following errors occured</b><br>\r\n" .
        "<blockquote>$errorMsg</blockquote></font>";
}

?>
<script type="text/javascript">
//<![CDATA[
function closeJob()
{
		if(confirm("Are you sure you wish to close this job?"))
		{
        document.forms[0].elements['action'].value="do_close_job";
        document.forms[0].submit();
		}
}
//]]>
</script>
<br>
<table border="0" align="center" cellpadding="4" cellspacing="1" width="70%">
  <tr>
    <td width="25%" nowrap class="left">Title :</td>
	<td width="75%" class="right"><?=$job_details['topic'];?></td>
  </tr>
  <tr>
    <td nowrap class="left">Comments :</td>
    <td class="right"><?=chunk_split($job_details['description']);?></td>
  </tr>
<?php
$value = "N/A";
if ($job_details['quote_id'] > 0) {
  $value = 'Q#' . padQuoteNum($job_details['quote_id']);
  if (function_exists('invoiceGetValue')) {
    $value = invoiceGetValue($job_details['quote_id'], 'invoice_number') ?? $value;
  }
}
?>
  <tr>
    <td nowrap class="left">Reference :</td>
	<td class="right"><?=$value;?></td>
  </tr>
  <tr>
    <td nowrap class="left">Sent By :</td>
	<td class="right"><?=$job_details['sender'];?></td>
  </tr>
</table>
<br>
<?php
$history = showJobHistory($job_details['job_id']);
?>
<br>
<?php
// THE CURRENT USER MUST BE THE RECIPIENT OR ADMIN TO FORWARD THIS JOB (AND IT MUST BE OPEN)
if ((strcasecmp($job_details['receiver'], $user) == 0 || has_permission('ALL_JOBS', $allowed_sections)) && "open" == $job_details['status']) {
?>
<table border="0" align="center" cellpadding="4" cellspacing="1" width="70%">
  <tr>
    <td nowrap class="left">New Comments :</td>
    <td class="right">
      <textarea name="jobs[description]" cols="20" rows="5" style="width:400px"><?=stripslashes($_POST['jobs']['description']);?></textarea>
    </td>
  </tr>
  <tr>
    <td width="25%" nowrap class="left">Send Job To :</td>
	<td width="75%" class="right"><select name="jobs[receiver]" style="width:150px">
        <option value=""></option>
        <?=getUserList($_POST['jobs']['receiver']);?>
      </select></td>
  </tr>
  <tr>
    <td width="25%" nowrap class="left">Priority :</td>
	<td width="75%" class="right"><select name="jobs[priority_id]" style="width:150px">
		<?=getJobPriorityList($job_details['priority_id']);?>
	</select></td>
  </tr>
</table>
<br>
<input type="hidden" name="action" value="do_new_message">
<input type="hidden" name="jobs[job_id]" value="<?=$job_details['job_id'];?>">
<input type="hidden" name="jobs[sender]" value="<?=$user;?>">
<input type="hidden" name="id" value="<?=$_REQUEST['id'];?>">
<table border=0 align="center" cellpadding="10" width="20%">
	<tr>
		<td><input type="submit" value="Send"></td>
<!--<td><input type="submit" value="Update" onclick="javascript: document.forms[0].elements['action'].value='do_update';"></td>-->
<?php
$sql = "SELECT sender, job_id, MIN(id) FROM jobs_details
			GROUP BY job_id
			HAVING job_id='" . $job_details['job_id'] . "'";
    $temp    = $DB->query_first($sql);
    $creator = $temp['sender'];
//    echo "JOB_CREATOR: $creator / CURRENT_USER: $user<br>";
    if (has_permission('ALL_JOBS', $allowed_sections) || strcasecmp($creator, $user) == 0) {
?>
		<td><input type="button" value="Close Job" onclick="javascript: closeJob();"></td>
<?php
}
?>
	</tr>
</table>
<br><br>
<?php
}
?>
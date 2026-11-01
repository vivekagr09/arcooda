<!-- direct_email_history.tpl -->

<?php
if (isset($contractor_history) && is_array($contractor_history) && count($contractor_history)) {

	$staff_list = contractorGetOptionList('staff_id');
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionContractorDirectEmailHistory\');return false">Direct Email History</a>')?>

<script type="text/javascript">
//<![CDATA[
function direct_email_history_view()
{
	var url='.?action=history&return_message=null&contractor_id=<?= $contractor_id ?>';
	var features='scrollbars, resizable, height=400, width=600';
	window.open(url,'history_popup',features);
}
//]]>
</script>

<div id="sectionContractorDirectEmailHistory">
<?php
foreach ($contractor_history as $comment_id) {
    $item = contractorCommentFromDatabase($comment_id);
?>
<table class="table_fields">
	<tr>
		<th style="width:10%;">Type :</th>
		<td style="width:40%;"><b>Message To Customer</b></td>
		<th style="width:10%;">Date :</th>
		<td style="width:40%;"><?= date(_('d/m/Y'), $item['comment_time']) ?></td>
	</tr>
	<tr>
		<th style="width:10%;">Sender :</th>
		<td style="width:40%;"><?php hecho((array_key_exists($item['staff_id'], $staff_list)) ? $staff_list[$item['staff_id']] : $item['staff_id'])?></td>
		<th style="width:10%;">Sent to :</th>
		<td style="width:40%;"><?php hecho($item['comment_sentto'])?></td>
	</tr>
	<tr>
		<th style="width:10%;">Title :</th>
		<td style="width:40%;"><?php hecho($item['comment_title'])?></td>
		<th style="width:10%;">Days Open :</th>
		<td style="width:40%;"><?= floor(($item['comment_time'] - $contractor['created_time']) / 86400) ?></td>
	</tr>
<?php
    if (trim($item['comment_text']) != '') {
?>
	<tr>
        <th style="width:10%;">Notes :</td>
        <td colspan="3" style="width:90%;overflow:scroll;"><?= nl2br(hecho($item['comment_text'], 1)) ?></td>
	</td></tr>
<?php
    }
?>
</table>
<br /><br />
<?php
}
?>
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionDirectEmailHistory_hideNote">
	Direct Email History can be viewed by Clicking "Direct Email History" above<br />
</div>

<?php
}
?>

<!-- /direct_email_history.tpl -->

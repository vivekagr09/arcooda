<!-- message_history.tpl -->

<?php
//Only show history if there is some to show
if (count($invoice_history) <= 0) {
    return;
}

?>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionJobHistory\');">Job History</a>') ?>

<script type="text/javascript">
//<![CDATA[
function viewJobHistory()
{
	var url='index.php?action=history&return_message=null&invoice_id=<?= $invoice_id ?>';
	var features='scrollbars, resizable, height=400, width=600';
	window.open(url,'history_popup',features);
}
//]]>
</script>

<div id="sectionJobHistory">
<?php
$style1 = "width:10%;white-space:nowrap;";
$style2 = "width:39%;text-align:center;background-color:#ffffff;white-space:nowrap;";
$style3 = "width:15%;white-space:nowrap;";
$style4 = "width:36%;text-align:center;background-color:#ffffff;white-space:nowrap;";
foreach ($invoice_history as $history_id) {
    $item = purchaseOrderHistoryFromDatabase($history_id);
?>
<table class="wide">
	<tr>
		<td class="left_title" style="<?= $style1 ?>">Type</td>
		<td style="width:39%;text-align:center;background-color:<?= $item['message_colour'] ?>"><b><?php hecho($item['message_description'])?></b></td>
		<td class="left_title" style="<?= $style3 ?>">Date</td>
		<td style="<?= $style4 ?>"><?= date('d/m/Y', $item['message_time']) ?></td>
	</tr>
	<tr>
		<td class="left_title" style="<?= $style1 ?>">Sender</td>
		<td style="<?= $style2 ?>"><?php hecho((array_key_exists($item['message_sender'], $all_staff_list)) ? $all_staff_list[$item['message_sender']] : $item['message_sender'])?></td>
		<td class="left_title" style="<?= $style3 ?>">Sent to</td>
		<td style="<?= $style4 ?>"><?php hecho($item['message_sentto'])?></td>
	</tr>
	<tr>
		<td class="left_title" style="<?= $style1 ?>">Title</td>
		<td style="<?= $style2 ?>"><?php hecho($item['message_title'])?></td>
		<td class="left_title" style="<?= $style3 ?>">Days Open</td>
		<td style="<?= $style4 ?>"><?= floor(($item['message_time'] - $invoice['open_time']) / 86400) ?></td>
	</tr>
<?php
if (trim($item['message_notes']) != '') {
        $msg = $item['message_notes'];
        $nl  = (strpos($msg, "<!DOCTYPE html") === false) ? true : false;
        if ($nl) {
            $msg = nl2br(formatHTMLSafe($msg));
        }

?>
	<tr><td class="left_title" valign="top" style="width:10%;">Notes</td><td colspan="3">
		<table style="width:100%;"><tr><td>
<?php
if ($nl) {
?>
			<div style="width:100%;overflow:scroll;"><?= $msg ?></div>
<?php
} else {
?>
			<iframe src="/webadmin/invoice/show_message.php?message_id=<?= $history_id ?>" onload="calcHeight(this)" style="width:100%;border-style:none;"></iframe>
<?php
}
?>
		</td></tr></table>
	</td></tr>
<?php
}
?>
</table>
<br /><br />
<?php
}
?>
<table class="wide">
	<tr class="alt"><td style="text-align:center;">
		<input type="button" value="Print History" onclick="window.print();" />
<?php
if (!$popup_window) {
?>
		<input type="button" value="View All(<?= $invoice['history_count'] ?>)" onclick="viewJobHistory();" />
<?php
}
?>
	</td></tr>
</table>
<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionJobHistory_hideNote">
	Job History can be viewed by Clicking "Job History" above<br />
</div>

<!-- /message_history.tpl -->

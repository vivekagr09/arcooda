<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionAdminNotes\');">Admin Notes</a>')?>

<div id="sectionAdminNotes">
<table class="wide">
	<tr><td class="left_title" style="width:25%;">Message<br /><span style="font-size:9px;color:red;font-weight:normal;">Admin notes are never displayed on the main site</span></td><td><textarea name="admin_notes" rows="8" onkeyup="javascript:textareaAutoSize(this,1,8);"></textarea></td></tr>
</table>
<br /><br />
</div>

<?php
if (count($sinvoice_comments)) {
    echo formatHTMLTitle('Previous Admin Comments');
?>
<table class="wide" cellpadding="2">
	<tr><th style="width:10%;">Date</th><th style="width:15%;">User</th><th style="width:65%;">Comments</th></tr>
<?php
$count = 0;
    foreach ($sinvoice_comments as $message_id) {
        $message = shipinvoiceHistoryFromDatabase($message_id);
?>
	<tr style="text-align:center" <?=$count % 2 == 1 ? 'class="alt"' : ''?>>
		<td style="width:10%;"><?=date('d/m/Y', $message['message_time'])?></td>
		<td style="width:15;"><?=$all_staff_list[$message['message_sender']]?></td>
		<td style="width:65%;"><?=nl2br($message['message_notes'])?></td>
	</tr>
<?php
$count++;
    }
?>
</table>
<?php
}

if (count($sinvoice_messages)) {
    $count_total       = count($sinvoice_messages);
    $temp              = array_chunk($sinvoice_messages, 5);
    $sinvoice_messages = $temp[0];
?>
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionShippingInvoiceHistory\');">Shipping Invoice History</a>')?>

<div id="sectionShippingInvoiceHistory">
<?php
foreach ($sinvoice_messages as $message_id) {
        $message = shipinvoiceHistoryFromDatabase($message_id);
?>
	<table class="wide">
		<tr>
			<td class="left_title">Type</td>
			<td style="text-align:center;background-color:rgb(255,153,153);"><b>Shipping Invoice Change</b></td>
		</tr>
		<tr>
			<td class="left_title">Date</td>
			<td style="text-align:center;white-space:nowrap;"><?=date('d/m/Y', $message['message_time'])?></td>
		</tr>
		<tr>
			<td class="left_title" style="white-space:nowrap;">Sender</td>
			<td style="text-align:center;white-space:nowrap;"><?=htmlspecialchars($all_staff_list[$message['message_sender']])?></td>
		</tr>
		<tr><td class="left_title" valign="top">Notes</td><td><table style="width:100%;"><tr>
				<td><div style="overflow:scroll;width:100%;"><?=nl2br(htmlspecialchars($message['message_notes']))?></td>
		</tr></table></td></tr>
	</table>
	<br />
<?php
}
    if ($count_total > 5) {
?>
	<center><input type="button" class="unfinished" value=" View All (<?=$count_total?>) " /></center>
<?php
    }
?>
</div>
<br />
<?php
}
?>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<!-- admin_history.tpl -->

<?php

if (count($invoice_admin_notes) > 0) {
?>
<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionAdminHistory\');">Admin History</a>') ?>

<div id="sectionAdminHistory">
<table class="wide spacetable">
	<tr><th>Date</th><th>User</th><th>Admin Note</th><th>Days Open</th></tr>
<?php
foreach ($invoice_admin_notes as $message_id) {
        $item = invoiceHistoryFromDatabase($message_id);
?>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td style="white-space:nowrap;text-align:center;"><?= date('d/m/Y', $item['message_time']) ?></td>
		<td style="white-space:nowrap;text-align:center;"><?php hecho($all_staff_list[$item['message_sender']])?></td>
		<td style="width:100%;"><?= nl2br(formatHTMLSafe($item['message_notes'])) ?></td>
		<td style="white-space:nowrap;text-align:center;"><?= floor(($item['message_time'] - $invoice['open_time']) / 86400) ?></td>
	</tr>
<?php
}
?>
</table><br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionAdminHistory_hideNote">
Admin History can be viewed by Clicking "Admin History" above<br />
</div>
<?php
}

?>

<!-- /admin_history.tpl -->

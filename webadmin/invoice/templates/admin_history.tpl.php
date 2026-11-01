<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
if (count($invoice_admin_notes) > 0) {
    /**
     * Apply caching to the template
     */
    if (is_object($CH)) {
        $CH->open();
        $CH->addVarDependancies(array(
            $invoice_admin_notes,
            $all_staff_list,
            //$invoice["open_time"], //Never changes
        ));
        if ($CH->fetch()) {
            echo "<!-- #admin_history.tpl -->";
            return;
        }
    }
?>

<?=formatHTMLTitle("<a href=\"#\" onclick=\"return toggleSection(&quot;sectionAdminHistory&quot;)\">" . htmlspecialchars(_("Admin History")) . "</a>")?>

<div id="sectionAdminHistory">
	<table class="wide spacetable">
		<tr>
			<th><?=htmlspecialchars(_("Date"))?></th>
			<th><?=htmlspecialchars(_("User"))?></th>
			<th><?=htmlspecialchars(_("Admin Note"))?></th>
			<th><?=htmlspecialchars(_("Days Open"))?></th>
		</tr>
<?php
    $alt = false;
    foreach ($invoice_admin_notes as $message_id) {
        $item = invoiceHistoryFromDatabase($message_id);
?>
		<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
			<td style="white-space:nowrap;text-align:center;"><?=date(_('d/m/Y'), $item['message_time'])?></td>
			<td style="white-space:nowrap;text-align:center;"><?=htmlspecialchars($all_staff_list[$item['message_sender']])?></td>
			<td style="width:100%;"><?=nl2br(htmlspecialchars($item['message_notes']))?></td>
			<td style="white-space:nowrap;text-align:center;"><?=floor(($item['message_time'] - $invoice['open_time']) / 86400)?></td>
		</tr>
<?php
    }
?>
	</table>
	<br /><br />
</div>
<div id="sectionAdminHistory_hideNote" style="background:#fff;text-align:center;padding:8px 8px 8px 8px;display:none;">
	<?=htmlspecialchars(sprintf(_("%s can be viewed by clicking \"%s\" above"), _("Admin History"), _("Admin History")))?><br />
</div>

<?php

    /**
     * Finish caching the template
     */
    if (is_object($CH)) {
        $CH->close();
    }
}
?>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

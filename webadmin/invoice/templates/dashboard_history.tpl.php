<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
if (count($invoice_dashboard_notes) > 0) {
    /**
     * Apply caching to the template
     */
    if (is_object($CH)) {
        $CH->open();
        $CH->addVarDependancies(array(
            $invoice_dashboard_notes,
            $all_staff_list,
            //$invoice["open_time"], //Never changes
        ));
        if ($CH->fetch()) {
            echo "<!-- #dashboard_history.tpl -->";
            return;
        }
    }
?>

<?=formatHTMLTitle("<a href=\"#\" onclick=\"return toggleSection(&quot;sectionDashboardHistory&quot;)\">" . htmlspecialchars(_("My Dashboard Display History")) . "</a>")?>

<div id="sectionDashboardHistory">
	<table class="wide spacetable">
		<tr>
			<th><?=htmlspecialchars(_("Date"))?></th>
			<th><?=htmlspecialchars(_("Staff"))?></th>
			<th><?=htmlspecialchars(_("Online Display to Customer Notes"))?></th>
			<th>&nbsp;</th>
		</tr>
<?php
    $alt = false;
    if (is_array($invoice_dashboard_notes)) {
        foreach ($invoice_dashboard_notes as $message_id) {
            $item = invoiceDashboardHistoryFromDatabase($message_id);
            $urlopen = "editDashboardHistory.php?id=".$item['id']."&action=edit&data=".$invoice['display_to_customer']."";
?>
		<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
			<td style="white-space:nowrap;text-align:center;"><?=date(_('d/m/Y'), strtotime($item['created_at']))?></td>
			<td style="white-space:nowrap;text-align:center;"><?=htmlspecialchars($all_staff_list[$item['staff_id']])?></td>
			<td style="width:100%;"><?=nl2br(htmlspecialchars($item['display_to_customer']))?></td>
			<td style="white-space:nowrap;text-align:center;"><input type="button" value="Edit" onclick="window.open('<?= $urlopen ?>','','width=700,height=500')"></td>
		</tr>
<?php
        }
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

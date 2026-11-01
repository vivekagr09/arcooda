<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
moduleLoad('shipinvoice');

$preview_time  = mktime(0, 0, 0);
$sinvoice_time = shipinvoiceGetValue($invoice_id, "last_modified_time");
$sinvoice_time = max($sinvoice_time, shipinvoiceGetValue($invoice_id, "open_time"));

/**
 * Apply caching to the template
 */
if (is_object($CH)) {
    $CH->open();
    $CH->addVarDependancies(array(
        $invoice_id,
        $invoice["site_id"],
        $invoice["open_time"],
        $preview_time,
        $sinvoice_time,
    ));
    if ($CH->fetch()) {
        echo "<!-- #action_buttons.tpl -->";
        return;
    }
}

$image_dir = "/webadmin/images/sites/site_" . $invoice["site_id"] . "/";
?>

<!-- <?=formatHTMLTitle("<a href=\"#\" onclick=\"return toggleSection(&quot;sectionActionButtons&quot;)\">" . htmlspecialchars(_("Actions")) . "</a>")?> -->

<div id="sectionActionButtons">
	<table style="width:auto;margin:auto;">
		<tr>
			<td style="text-align:center;"><a href="<?=htmlspecialchars(".?action=view&invoice_id=" . $invoice_id. "&invoice_time=" . $preview_time)?>" target="_blank" title="<?=htmlspecialchars(_("View Invoice"))?>"><img src="<?=htmlspecialchars($image_dir . "print.png")?>" border="0" /></a></td>
			<td style="text-align:center;"><a href="<?=htmlspecialchars(".?action=shipview&format=pdf&invoice_id=" . $invoice_id. "&invoice_time=" . $preview_time)?>" target="_blank" title="<?=htmlspecialchars(_("Shipping Invoice"))?>"><img src="<?=htmlspecialchars($image_dir . "shipping.png")?>" border="0" /></a></td>
			<td style="text-align:center;"><a href="<?=htmlspecialchars("/webadmin/invoice/ship_addresses.php?&invoice_id=" . $invoice_id)?>" target="_blank" title="<?=htmlspecialchars(_("Print Shipping Label"))?>" onclick="window.open(this.href, '_blank', 'width=404,height=626,toolbar=0,resizable=0');return false"><img src="<?=htmlspecialchars($image_dir . "label1.png")?>" border="0" /></a></td>
			<td style="text-align:center;"><a href="<?=htmlspecialchars("/webadmin/documentinvoice/?action=view&invoice_id=" . $invoice_id."&format=pdf")?>" title="<?=htmlspecialchars(_("View Delivery Order"))?>" target="_blank"><img src="<?=htmlspecialchars($image_dir . "delivery.jpeg")?>" border="0" /></a></td>
			<td style="text-align:center;"><a href="<?=htmlspecialchars("/webadmin/shipinvoice/?action=view&invoice_id=" . $invoice_id."&format=pdf")?>" title="<?=htmlspecialchars(_("View Shipping Invoice"))?>" target="_blank"><img src="<?=htmlspecialchars($image_dir . "dummy.png")?>" border="0" /></a></td>
			<!-- <td style="text-align:center;" class="unfinished"><a href="about:blank" onclick="return false;" target="_blank"><img src="<?=htmlspecialchars($image_dir . "con_note.png")?>" border="0" /></a></td> -->
	
			<td style="text-align:center;" class="unfinished"><a href="<?=htmlspecialchars(".?charges=0&shipping=0&action=purchase_list&invoice_id=" . $invoice_id)?>" title="<?=htmlspecialchars(_("View Products Sold Summary"))?>" target="_blank"><img src="<?=htmlspecialchars($image_dir . "con_note.png")?>" border="0" /></a></td>
			
		</tr>
		<tr>
			<td style="text-align:center;font-size:x-small;">
				<a href="<?=htmlspecialchars(".?action=view&invoice_id=" . $invoice_id . "&invoice_time=" . $invoice["open_time"])?>" target="_blank"><?=htmlspecialchars(_("View Original Dated"))?></a><br />
				<a href="<?=htmlspecialchars(".?action=view&invoice_id=" . $invoice_id . "&invoice_time=" . $preview_time)?>" target="_blank"><?=htmlspecialchars(_("View Current Dated"))?></a>
			</td>
			<td style="text-align:center;font-size:x-small;">
				<a href="<?=htmlspecialchars(".?action=shipview&format=pdf&invoice_id=" . $invoice_id . "&invoice_time=" . $invoice["open_time"])?>" target="_blank"><?=htmlspecialchars(_("View Original Dated"))?></a><br />
				<a href="<?=htmlspecialchars(".?action=shipview&format=pdf&invoice_id=" . $invoice_id . "&invoice_time=" . $preview_time)?>" target="_blank"><?=htmlspecialchars(_("View Current Dated"))?></a>
			</td>
			<td style="text-align:center;font-size:x-small;">
				
				<a href="<?=htmlspecialchars("/webadmin/invoice/ship_addresses.php?&invoice_id=" . $invoice_id)?>" target="_blank" title="<?=htmlspecialchars(_("Print Shipping Label"))?>" onclick="window.open(this.href, '_blank', 'width=404,height=626,toolbar=0,resizable=0');return false"><?=htmlspecialchars(_("View Label"))?></a><br />
				<a href="<?=htmlspecialchars(".?action=shiplabel&format=pdf&label_type=pieces&invoice_id=" . $invoice_id)?>" target="_blank" title="<?=htmlspecialchars(_("Print Shipping Label"))?>"><?=htmlspecialchars(_("Print Label"))?></a>
			</td>
			<td style="text-align:center;font-size:x-small;">
				<a href="<?=htmlspecialchars("/webadmin/documentinvoice/?action=edit&invoice_id=" . $invoice_id)?>" target="_blank"><?=htmlspecialchars(_("Edit Delivery Order"))?></a><br />
				<?=htmlspecialchars((0 != $sinvoice_time) ? sprintf(_("Last Updated: %s"), date(_("d/m/Y"), $sinvoice_time)) : "")?>
			</td>
			<td style="text-align:center;font-size:x-small;">
				<a href="<?=htmlspecialchars("/webadmin/shipinvoice/?action=edit&invoice_id=" . $invoice_id)?>" target="_blank"><?=htmlspecialchars(_("Edit Dummy Invoice"))?></a><br />
				<?=htmlspecialchars((0 != $sinvoice_time) ? sprintf(_("Last Updated: %s"), date(_("d/m/Y"), $sinvoice_time)) : "")?>
			</td>
			
			<td style="width:120px;text-align:center;font-size:x-small;"></td>
		</tr>
	</table>
	<br /><br />
</div>
<div id="sectionActionButtons_hideNote" style="background:#fff;text-align:center;padding:8px 8px 8px 8px;display:none;">
	<?=htmlspecialchars(sprintf(_("%s can be viewed by clicking \"%s\" above"), _("Actions"), _("Actions")))?><br />
</div>

<?php

/**
 * Finish caching the template
 */
if (is_object($CH)) {
    $CH->close();
}

?>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

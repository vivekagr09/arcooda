<!-- action_buttons.tpl -->

<?php
$image_dir = '/webadmin/images/sites/site_' . $invoice['site_id'] . '/';
?>

<script type="text/javascript">
function actionPreviewInvoice(theFrm)
{
	var oldact=theFrm.action;
	var oldtrg=theFrm.target;
	theFrm.action="invoice_preview.php";
	theFrm.target="_blank";
	theFrm.submit();
	theFrm.action=oldact;
	theFrm.target=oldtrg;
}
</script>

<table style="width:auto;" align="center">
	<tr>
		<td style="text-align:center;"><a href="index.php?action=view&amp;order_id=<?= $order_id ?>" target="_blank" title="View Invoice"><img alt="" src="<?= $image_dir ?>print.png" border="0" /></a></td>
		<td style="text-align:center;">
			<a href="index.php?action=shipview&amp;order_id=<?= $order_id ?>" target="_blank" title="Shipping Invoice"><img alt="" src="<?= $image_dir ?>shipping.png" border="0" /></a>
		</td>
		<td style="text-align:center;"><a href="index.php?action=shiplabel&amp;order_id=<?= $order_id ?>" target="_blank" title="Print Shipping Label"><img alt="" src="<?= $image_dir ?>label.png" border="0" /></a></td>
		<td style="text-align:center;"><a href="/webadmin/new_shipinvoice/shipinvoice_view.php?order_id=<?= $order_id ?>" title="View Shipping Invoice" target="_blank"><img alt="" src="<?= $image_dir ?>dummy.png" border="0" /></a></td>
		<!--td style="text-align:center;" class="unfinished"><a href="about:blank" onclick="return false;" target="_blank"><img alt="" src="<?= $image_dir ?>con_note.png" border="0" /></a></td-->
	</tr>
	<tr>
		<td style="text-align:center;font-size:x-small;">
<?php
if (false) {
?>
			<input type="button" style="width:120px;" value="Preview Invoice" onclick="actionPreviewInvoice(this.form);" />
<?php
}
?>
			<a href="index.php?action=view&amp;order_id=<?= $order_id ?>&amp;invoice_time=<?= $invoice['open_time'] ?>" target="_blank">View Original Dated</a><br />
			<a href="index.php?action=view&amp;order_id=<?= $order_id ?>&amp;invoice_time=<?= $_SERVER['REQUEST_TIME'] ?>" target="_blank">View Current Dated</a><br />
			<span style="background-color:yellow;"><input type="checkbox" /> Add Account Summary</span>
		</td>
		<td style="text-align:center;font-size:x-small;">
			<a href="index.php?action=shipview&amp;order_id=<?= $order_id ?>&amp;invoice_time=<?= $invoice['open_time'] ?>" target="_blank">View Original Dated</a><br />
			<a href="index.php?action=shipview&amp;order_id=<?= $order_id ?>&amp;invoice_time=<?= $_SERVER['REQUEST_TIME'] ?>" target="_blank">View Current Dated</a>
		</td>
		<td style="text-align:center;font-size:x-small;">
			<a href="index.php?action=shiplabel&amp;label_type=pieces&amp;order_id=<?= $order_id ?>" target="_blank" title="Print Shipping Label">Print A4 Label</a>
		</td>
		<td style="text-align:center;font-size:x-small;">
			<a href="/webadmin/new_shipinvoice/?action=edit&amp;order_id=<?= $order_id ?>" target="_blank">Edit Shipping Invoice</a><br />
<?php
$sinvoice_time = shipinvoiceGetValue($order_id, 'last_modified_time');
$sinvoice_time = max($sinvoice_time, shipinvoiceGetValue($order_id, 'open_time'));
?>
			<?= (0 != $sinvoice_time) ? ('Last Updated: ' . date('d/m/Y', $sinvoice_time)) : '' ?>
		</td>
		<!--td style="width:120px;text-align:center;font-size:x-small;"></td-->
	</tr>
</table>

<br /><br />

<!-- /action_buttons.tpl -->

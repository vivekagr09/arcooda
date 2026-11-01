<!-- login.tpl -->

<?php
moduleLoad('invoice'); //invoiceFormatNumber

if (isset($member_wanted) && is_array($member_wanted)) {
?>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionMemberWanted\');" class="unfinished">Wanted Summary</a>')?>

<div id="sectionMemberWanted">
	<table class="table_grid">
		<tr>
			<th style="width:15%">Date</th>
			<th style="width:10%">Type</th>
			<th style="width:35%">product</th>
			<th style="width:10%">Status</th>
			<th style="width:20%">Enquiry #</th>
			<th style="width:10%">View Wanted</th>
		</tr>
<?php
if (count($member_wanted)) {
        $active_list = array('y' => 'Online', 'n' => 'Offline');
        foreach ($member_wanted as $wanted_id) {
            $wanted = memberWantedFromDatabase($wanted_id);
?>
		<tr style="text-align:center" <?=($count % 2 == 1) ? 'class="alt"' : ''?>>
			<td><?=date(_('jS F, Y'), strtotime($wanted['date_added']))?></td>
			<td>Wanted Listing</td>
			<td style="text-align:left;white-space:wrap;"><?=($wanted['item_id']) ? '<a href="/webadmin/products_new/edit.php?&amp;id=' . $wanted['item_id'] . '" target="_blank"">' : ''?><?=htmlspecialchars($wanted['item'])?><?=($wanted['item_id']) ? '</a>' : ''?></td>
			<td><?=$active_list[$wanted['approved']]?></td>
			<td><?=($wanted['invoice_id']) ? '<a href="/webadmin/invoice/invoice_edit.php?invoice_id=' . $wanted['invoice_id'] . '" target="_blank">' . invoiceFormatNumber($wanted['invoice_id']) . '</a>' : ''?></td>
			<td>
				<a href="/webadmin/wanted/wanted_edit.php?wanted_id=<?=$wanted['id']?>" target="_blank">View</a>
			</td>
		</tr>
<?php
}}
    $wanted_offer = memberWantedOffersFromEmail($member['email_address']);
    if (count($wanted_offer)) {
        $active_list = array('y' => 'Online', 'n' => 'Offline');
        foreach ($wanted_offer as $offer) {
            $wanted = memberWantedFromDatabase($offer['wanted_id']);
?>
		<tr style="text-align:center" <?=($count % 2 == 1) ? 'class="alt"' : ''?>>
			<td><?=date(_('jS F, Y'), strtotime($wanted['date_added']))?></td>
			<td>Sale to us</td>
			<td style="text-align:left;white-space:wrap;"><?=($wanted['item_id']) ? '<a href="/webadmin/products_new/edit.php?&amp;id=' . $wanted['item_id'] . '" target="_blank"">' : ''?><?=htmlspecialchars($wanted['item'])?><?=($wanted['item_id']) ? '</a>' : ''?></td>
			<td><?=$active_list[$wanted['approved']]?></td>
			<td><?=($wanted['invoice_id']) ? '<a href="/webadmin/invoice/invoice_edit.php?invoice_id=' . $wanted['invoice_id'] . '" target="_blank">' . invoiceFormatNumber($wanted['invoice_id']) . '</a>' : ''?></td>
			<td>
				<a href="/webadmin/wanted/wanted_edit.php?wanted_id=<?=$wanted['id']?>" target="_blank">View</a>
			</td>
		</tr>
<?php
}}
?>
	</table>
	<br /><br />
</div>

<?php
}
?>

<!-- /login.tpl -->

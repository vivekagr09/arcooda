<!-- couriers.tpl -->

<?php
moduleLoad('member'); //memberGetOptionList

if (isset($member_couriers) && is_array($member_couriers)) {
?>

<script type="text/javascript">
//<![CDATA[
var couriers = 100;
function memberCourierAdd() {
    var ship_table = getElement('member_couriers_table');
    if (ship_table == null) return;
    var newRow = addTableRow(ship_table, 'member_couriers_rowa[' + couriers + ']');
    addTableCell(newRow, '<input type="hidden" name="member_couriers[' + couriers + '][courier_acc_id]" value="" /><input type="hidden" name="member_couriers[' + couriers + '][member_id]" value="<?=$member_id?>" /><select name="member_couriers[' + couriers + '][courier_id]" onchange="memberCourierChange(' + "'" + couriers + "','courier_id'" + ')"><option value="0">Other...</option>' + <?php jecho(formatHTMLOptions(memberGetOptionList('courier_id'), $courier['courier_id']))?> +'</select><input class="intext" type="text" name="member_couriers[' + couriers + '][courier_name]" value="" />');
    addTableCell(newRow, '<input class="intext" type="text" name="member_couriers[' + couriers + '][account_number]" value="" />');
    addTableCell(newRow, '<textarea rows="5" cols="80" name="member_couriers[' + couriers + '][notes]"></textarea>');
    addTableCell(newRow, '<input type="button" value=" Delete " onclick="memberCourierDelete(' + couriers + ')" />');
    couriers++;
}
function memberCourierDelete(row) {
    if (confirm('Are You Sure you want to delete this courier??')) {
        var theRow = getElement('member_couriers_rowa[' + row + ']');
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    }
}
function memberCourierChange(row, field) {
    switch (field) {
        case 'courier_id':
            toggleSection('member_couriers[' + row + '][courier_name]', getValue('member_couriers[' + row + '][courier_id]') == '0');
            break;
    }
}
//]]>
</script>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionMemberCourier\');">Couriers</a>', '<a href="#" style="color:#000;" onclick="memberCourierAdd();return false;">Add New Courier</a>')?>

<div id="sectionMemberCourier">
	<table class="table_grid" id="member_couriers_table">
		<tr>
			<th style="width:30%;">Courier</th>
			<th style="width:30%;">Account Number</th>
			<th style="width:30%;">Notes</th>
			<th></th>
		</tr>
<?php
$count    = 0;
    $affected = array();
    foreach ($member_couriers as $courier) {
        $count++;
        $affected[] = $courier['courier_acc_id'];
?>
		<tr id="member_couriers_rowa[<?=$count?>]">
			<td>
				<input type="hidden" name="member_couriers[<?=$count?>][courier_acc_id]" value="<?=$courier['courier_acc_id']?>" />
				<input type="hidden" name="member_couriers[<?=$count?>][member_id]" value="<?=$courier['member_id']?>" />
				<select name="member_couriers[<?=$count?>][courier_id]" onchange="memberCourierChange('<?=$count?>','courier_id')">
					<option value="0">Other...</option>
					<?=formatHTMLOptions(memberGetOptionList('courier_id'), $courier['courier_id'])?>
				</select>
				<input class="intext" type="text" name="member_couriers[<?=$count?>][courier_name]" value="<?php hecho($courier['courier_name'])?>" style="<?=(0 == $courier['courier_id']) ? '' : 'display:none;'?>" />
			</td>
			<td><input class="intext" type="text" name="member_couriers[<?=$count?>][account_number]" value="<?php hecho($courier['account_number'])?>" /></td>
			<td><textarea rows="5" cols="80" name="member_couriers[<?=$count?>][notes]"><?php hecho($courier['notes'])?></textarea></td>
			<th><input type="button" value=" Delete " onclick="memberCourierDelete(<?=$count?>)" /></th>
		</tr>
<?php
    }
?>
	</table>
	<input type="hidden" name="member_couriers_affected" value="<?=implode(',', $affected)?>" />
	<br /><br />
</div>

<div style="background-color:#fff;text-align:center;padding:8px;display:none;" id="sectionMemberCourier_hideNote">Couriers can be viewed by Clicking "Couriers" above</div>

<?php
}
?>

<!-- /couriers.tpl -->

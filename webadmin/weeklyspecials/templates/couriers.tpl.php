<!-- couriers.tpl.php -->

<?php
if(is_array($weeklyspecial_couriers))
{
?>

<script type="text/javascript">
//<![CDATA[
var couriers=100;
function weeklyspecialCourierAdd()
{
	var ship_table = getElement('weeklyspecial_couriers_table');
	if(ship_table == null) return;
	var newRow = addTableRow(ship_table,'weeklyspecial_couriers_rowa['+couriers+']');
	addTableCell(newRow,'<input type="hidden" name="weeklyspecial_couriers['+couriers+'][courier_acc_id]" value="" /><input type="hidden" name="weeklyspecial_couriers['+couriers+'][id]" value="<?= $id ?>" /><select name="weeklyspecial_couriers['+couriers+'][courier_id]" onchange="weeklyspecialCourierChange('+"'"+couriers+"','courier_id'"+')"><option value="0">Other...</option>'+<?= json_encode(formatHTMLOptions($section_handler->getOptionList('courier_id'),$courier['courier_id'])) ?>+'</select><input class="intext" type="text" name="weeklyspecial_couriers['+couriers+'][courier_name]" value="" />');
	addTableCell(newRow,'<input class="intext" type="text" name="weeklyspecial_couriers['+couriers+'][account_number]" value="" />');
	addTableCell(newRow,'<textarea rows="5" cols="80" name="weeklyspecial_couriers['+couriers+'][notes]"></textarea>');
	addTableCell(newRow,'<input type="button" value=" Delete " onclick="weeklyspecialCourierDelete('+couriers+')" />');
	couriers++;
}
function weeklyspecialCourierDelete(row)
{
	if(confirm('Are You Sure you want to delete this courier??'))
	{
		var theRow=getElement('weeklyspecial_couriers_rowa['+row+']');
		if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	}
}
function weeklyspecialCourierChange(row,field)
{
	switch(field)
	{
	case 'courier_id':
		toggleSection('weeklyspecial_couriers['+row+'][courier_name]',getValue('weeklyspecial_couriers['+row+'][courier_id]')=='0');
		break;
	}
}
//]]>
</script>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionWeeklySpecialCourier\');">Couriers</a>','<a href="#" style="color:#000;" onclick="weeklyspecialCourierAdd();return false;">Add New Courier</a>') ?>

<div id="sectionWeeklySpecialCourier">
	<table class="wide" id="weeklyspecial_couriers_table">
		<tr>
			<th style="width:30%;">Courier</th>
			<th style="width:30%;">Account Number</th>
			<th style="width:30%;">Notes</th>
			<th></th>
		</tr>
<?php
	$count=0;
	$affected=array();
	foreach($weeklyspecial_couriers as $courier)
	{
		$count++;
		$affected[]=$courier['courier_acc_id'];
?>
		<tr id="weeklyspecial_couriers_rowa[<?= $count ?>]">
			<td>
				<input type="hidden" name="weeklyspecial_couriers[<?= $count ?>][courier_acc_id]" value="<?= $courier['courier_acc_id'] ?>" />
				<input type="hidden" name="weeklyspecial_couriers[<?= $count ?>][id]" value="<?= $courier['id'] ?>" />
				<select name="weeklyspecial_couriers[<?= $count ?>][courier_id]" onchange="weeklyspecialCourierChange('<?= $count ?>','courier_id')">
					<option value="0">Other...</option>
					<?= formatHTMLOptions($section_handler->getOptionList('courier_id'),$courier['courier_id']) ?>
				</select>
				<input class="intext" type="text" name="weeklyspecial_couriers[<?= $count ?>][courier_name]" value="<?= htmlspecialchars($courier['courier_name']) ?>" style="<?= ($courier['courier_id']==0)?'':'display:none;' ?>" />
			</td>
			<td><input class="intext" type="text" name="weeklyspecial_couriers[<?= $count ?>][account_number]" value="<?= htmlspecialchars($courier['account_number']) ?>" /></td>
			<td><textarea rows="5" cols="80" name="weeklyspecial_couriers[<?= $count ?>][notes]"><?= htmlspecialchars($courier['notes']) ?></textarea></td>
			<td class="left_title"><input type="button" value=" Delete " onclick="weeklyspecialCourierDelete(<?= $count ?>)" /></td>
		</tr>
<?php
	}
?>
	</table>
	<input type="hidden" name="weeklyspecial_couriers_affected" value="<?= implode(',',$affected) ?>" />
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionWeeklySpecialCourier_hideNote">Couriers can be viewed by Clicking "Couriers" above<br /></div>

<?php
}
?>

<!-- /weeklyspecial_couriers.tpl.php -->

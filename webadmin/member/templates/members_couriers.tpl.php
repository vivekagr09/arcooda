<!-- members_courier.tpl.php -->

<?php
function courier_row($count = '[ROW_COUNT]', $courier = array())
{
    $c_list = getFreightServices('personal', '', true);
    $hidden = array(
        'courier[' . $count . '][id]' => $courier['id'],
    );

    ob_start();
?>
<tr id="courier_rowa[<?=$count?>]" class="alt" style="text-align:center;">
	<td class="right_input"><select name="couriers[<?=$count?>][courier_id]" onchange="changeCourier('<?=$count?>')"><option value="0">Other...</option><?=formatHTMLOptions($c_list, $courier['courier_id'], "a")?></select><br /><input type="text" name="couriers[<?=$count?>][courier_name]" value="<?=$courier['courier_name']?>" style="<?=$courier['courier_id'] == 0 ? '' : 'display:none;'?>" /></td>
	<td class="right_input"><input type="text" name="couriers[<?=$count?>][account_num]" value="<?=$courier['account_num']?>" /></td>
	<td class="right_input"><textarea name="couriers[<?=$count?>][notes]"><?=$courier['notes']?></textarea></td>
	<td><input type="button" value="Delete" onclick="deleteCourierRow('<?=$count?>')" /></td>

</tr>
<?php
return ob_get_clean();
}
?>

<script type="text/javascript">
//<![CDATA[
var courier_count=<?=max(100, count($member_couriers))?>;
function addCourierRow()
{
	var count=String(courier_count);
	var target_table=getElement("courier_table");
	if(target_table!=null)
	{
		var newRow1=addTableRow(target_table,'courier_rowa['+count+']');
		newRow1.className='alt';
		newRow1.style.textAlign = 'center';
<?php
$count = 0;
foreach (explode("\n", courier_row('[ROW_COUNT]', array())) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 3) == '<td') {
        $startPoint = 24;
        if (substr($row, 0, 4) == '<td>') {
            $startPoint = 4;
        }

?>
		var newCell = addTableCell(newRow<?=$count?>, <?=str_replace(array("[ROW_COUNT]"), array('" + count + "'), json_encode(substr($row, $startPoint, -5)))?>);
		newCell.style.textAlign = 'center';
<?php
if (substr($row, 0, 4) != '<td>') {
            echo "newCell.className='right_input';\n";
        }

    }
}
?>
			courier_count++;
	}
}
function deleteCourierRow(row)
{
	if(confirm('Are you sure you want to remove this courier address??'))
	{
		var theRow=getElement("courier_rowa["+row+"]");
		if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	}
}
function changeCourier(row)
{
	toggleSection('couriers['+row+'][courier_name]',(getValue('couriers['+row+'][courier_id]')=='0'));
}
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionCourier\'); return false;">Customer Courier Accounts</a>', '<a href="#" onclick="addCourierRow(); return false;">Add New</a>')?>
<div id="sectionCourier"><table class="wide spacetable">
	<tr><th>Courier</th><th>Account Number</th><th>Notes</th><th></th></tr>
	<tbody id="courier_table">
<?php
$affected = array();
foreach ($member_couriers as $courier) {
    $affected[] = $courier['id'];
    echo courier_row($courier['id'], $courier);
}
?>
	</tbody>
	<input type="hidden" name="couriers_affected" value="<?=implode('|', $affected)?>" />
</table></div><br />

<!-- /members_courier.tpl.php -->
<!-- members_forwarder.tpl.php -->

<?php
function forwarder_row($count = '[ROW_COUNT]', $forwarder = array())
{
    global $DB;
    $hidden = array(
        'forwarder[' . $count . '][id]' => $forwarder['id'],
    );

    ob_start();
?>
<tr id="forwarder_rowa[<?=$count?>]" style="text-align:center;">
	<td class="left_title left_input">Company Name :</td>
	<td class="right_input"><?=str_replace("\n", "", formatHTMLInputs($hidden))?><input type="text" name="forwarder[<?=$count?>][company_name]" value="<?=htmlspecialchars($forwarder['company_name'])?>" /></td>
	<td class="left_title left_input">Phone :</td>
	<td class="right_input"><input type="text" name="forwarder[<?=$count?>][phone_no]" value="<?=$forwarder['phone_no']?>" /></td>
</tr>
<tr id="forwarder_rowb[<?=$count?>]" style="text-align:center;">
	<td class="left_title left_input" rowspan="2" valign="top">Address :</td>
	<td class="right_input" rowspan="2"><textarea name="forwarder[<?=$count?>][address]"><?=$forwarder['address']?></textarea></td>
	<td class="left_title left_input">Fax :</td>
	<td class="right_input"><input type="text" name="forwarder[<?=$count?>][fax_no]" value="<?=$forwarder['fax_no']?>" /></td>
</tr>
<tr id="forwarder_rowc[<?=$count?>]" style="text-align:center;">
	<td class="left_title left_input">Mobile :</td>
	<td class="right_input"><input type="text" name="forwarder[<?=$count?>][mobile_no]" value="<?=$forwarder['mobile_no']?>" /></td>
</tr>
<tr id="forwarder_rowd[<?=$count?>]" style="text-align:center;">
	<td class="left_title left_input">City :</td>
	<td class="right_input"><input type="text" name="forwarder[<?=$count?>][city]" value="<?=$forwarder['city']?>" /></td>
	<td class="left_title left_input">State :</td>
	<td class="right_input"><input type="text" name="forwarder[<?=$count?>][state]" value="<?=$forwarder['state']?>" /></td>
</tr>
<tr id="forwarder_rowe[<?=$count?>]" style="text-align:center;">

	<td class="left_title left_input">Post Code :</td>
	<td class="right_input"><input type="text" name="forwarder[<?=$count?>][post_code]" value="<?=$forwarder['post_code']?>" /></td>
	<td class="left_title left_input">Country :</td>
	<td class="right_input"><select name="forwarder[<?=$count?>][country]"><?=formatHTMLOptions(getCountryList(), $forwarder['country'], "a")?></select></td>
</tr>
<tr id="forwarder_rowf[<?=$count?>]" style="text-align:center;">
	<td class="left_title left_input">Main Contact Name :</td>
	<td class="right_input"><input type="text" name="forwarder[<?=$count?>][contact]" value="<?=$forwarder['contact']?>" /></td>
	<td class="left_title left_input">Email :</td>
	<td class="right_input"><input type="text" name="forwarder[<?=$count?>][email]" value="<?=$forwarder['email']?>" /></td>
</tr>
<tr id="forwarder_rowg[<?=$count?>]">
	<td colspan="4" class="left_title" style="text-align:center;"><input type="button" value="Delete" onclick="deleteForwarderRow('<?=$count?>');" /></td>
</tr>
<?php
return ob_get_clean();
}
?>

<script type="text/javascript">
//<![CDATA[
var forwarder_count=<?=max(100, count($member_forwarders))?>;
function addForwarderRow()
{
	var count=String(forwarder_count);
	var target_table=getElement("forwarder_table");
	if(target_table!=null)
	{
		var newRow1=addTableRow(target_table,'forwarder_rowa['+count+']');
		newRow1.style.textAlign = 'center';
		var newRow2=addTableRow(target_table,'forwarder_rowb['+count+']');
		newRow2.style.textAlign = 'center';
		var newRow3=addTableRow(target_table,'forwarder_rowc['+count+']');
		newRow3.style.textAlign = 'center';
		var newRow4=addTableRow(target_table,'forwarder_rowd['+count+']');
		newRow4.style.textAlign = 'center';
		var newRow5=addTableRow(target_table,'forwarder_rowe['+count+']');
		newRow5.style.textAlign = 'center';
		var newRow6=addTableRow(target_table,'forwarder_rowf['+count+']');
		newRow6.style.textAlign = 'center';
		var newRow7=addTableRow(target_table,'forwarder_rowg['+count+']');
		newRow7.style.textAlign = 'center';
<?php
$count = 0;
foreach (explode("\n", forwarder_row('[ROW_COUNT]', array())) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 3) == '<td') {
        $rowspan    = $colspan    = $className    = $tAlign    = '';
        $startPoint = 23;
        if (substr($row, 0, 34) == '<td class="left_title left_input">') {
            $startPoint = 34;
            $className  = 'left_title left_input';
        } elseif (substr($row, 0, 24) == '<td class="right_input">') {
            $startPoint = 24;
            $className  = 'right_input';
        } elseif (substr($row, 0, 45) == '<td class="left_title left_input" rowspan="2"') {
            $startPoint = 59;
            $rowspan    = '2';
            $className  = 'left_title left_input';
        } elseif (substr($row, 0, 36) == '<td class="right_input" rowspan="2">') {
            $startPoint = 36;
            $rowspan    = '2';
            $className  = 'right_input';
        } elseif (substr($row, 0, 15) == '<td colspan="4"') {
            $colspan    = '4';
            $startPoint = '62';
            $className  = 'left_title';
            $tAlign     = 'center';
        }
?>
			var newCell = addTableCell(newRow<?=$count?>, <?=str_replace(array("[ROW_COUNT]"), array('" + count + "'), json_encode(substr($row, $startPoint, -5)))?>);
<?php
if ('' != $className) {
            echo "newCell.className='" . $className . "';\n";
        }

        if ('' != $rowspan) {
            echo "newCell.rowSpan='" . $rowspan . "';\n";
        }

        if ('' != $colspan) {
            echo "newCell.colSpan='" . $colspan . "';\n";
        }

        if ('' != $tAlign) {
            echo "newCell.style.textAlign = '" . $tAlign . "';\n";
        }

    }
}
?>
			forwarder_count++;
	}
}
function deleteForwarderRow(row)
{
	if(confirm('Are you sure you want to remove this freight forwarder??'))
	{
		var ar=Array('a','b','c','d','e','f','g');
		for(var i=0;i<ar.length;i++)
		{
			var theRow=getElement("forwarder_row"+ar[i]+"["+row+"]");
			if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
		}
	}
}
function getForwarderRows()
{
	var forwarder_table=getElement("forwarder_table");
	var forwarder_rows=new Array();
	if(forwarder_table!=null)
	{
		var theRows=contact_table.rows;
		for(var i=0;i<theRows.length;i++)
			if(theRows[i].id.substr(0,13)=='forwarder_rowa[')
				if(array_search(theRows[i].id.substr(12),forwarder_rows)==null)
					forwarder_rows[forwarder_rows.length]=theRows[i].id.substr(12);
	}
	return forwarder_rows;
}
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionForwarder\'); return false;">Freight Forwarders</a>', '<a href="#" onclick="addForwarderRow(); return false;">Add New</a>')?>
<div id="sectionForwarder"><table class="wide" id="forwarder_table">
<?php
$affected = array();
foreach ($member_forwarders as $forwarder) {
    $affected[] = $forwarder['id'];
    echo forwarder_row($forwarder['id'], $forwarder);
}
?>
	<input type="hidden" name="forwarder_affected" value="<?=implode('|', $affected)?>" />
</table></div><br />

<!-- /members_forwarder.tpl.php -->
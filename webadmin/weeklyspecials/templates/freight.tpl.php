<!-- freight.tpl.php -->

<?php
if(is_array($weeklyspecial_forwarders))
{
?>

<script type="text/javascript">
//<![CDATA[
var forwarders=100;
function weeklyspecialForwarderAdd()
{
	var ship_table = getElement('weeklyspecial_forwarders_table');
	if(ship_table == null) return;
	var newRow = addTableRow(ship_table,'weeklyspecial_forwarders_rowa['+forwarders+']');
	addTableCell(newRow,'<table style="width:100%;"><tr><td class="left_title" style="width:18%;">Company Name :</td><td style="width:32%;"><input type="hidden" name="weeklyspecial_forwarders['+forwarders+'][forwarder_id]" value="" /><input type="hidden" name="weeklyspecial_forwarders['+forwarders+'][id]" value="<?= $id ?>" /><input class="intext" type="text" name="weeklyspecial_forwarders['+forwarders+'][company]" value="" /></td><td class="left_title" style="width:18%;">Phone :</td><td style="width:32%;"><input class="intext" type="text" name="weeklyspecial_forwarders['+forwarders+'][phone]" value="" /></td></tr><tr><td class="left_title" style="width:18%;">Address :</td><td style="width:32%" rowspan="2"><textarea name="weeklyspecial_forwarders['+forwarders+'][address]"></textarea></td><td class="left_title" style="width:18%;">Fax :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_forwarders['+forwarders+'][fax]" value="" /></td></tr><tr><td class="left_title" style="width:18%;"></td><td class="left_title" style="width:18%;">Mobile :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_forwarders['+forwarders+'][mobile]" value="" /></td></tr><tr><td class="left_title" style="width:18%;">City :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_forwarders['+forwarders+'][city]" value="" /></td><td class="left_title" style="width:18%;">State :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_forwarders['+forwarders+'][state]" value="" /></td></tr><tr><td class="left_title" style="width:18%;">Post Code :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_forwarders['+forwarders+'][post_code]" value="" /></td><td class="left_title" style="width:18%;">Country :</td><td style="width:32%"><select name="weeklyspecial_forwarders['+forwarders+'][country]"><option value="">Not Selected</option>'+<?= json_encode(formatHTMLOptions($section_handler->getOptionList('country'))) ?>+'</select></td></tr><tr><td class="left_title" style="width:18%;">Main Contact Name :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_forwarders['+forwarders+'][contact_name]" value="" /></td><td class="left_title" style="width:18%;">Email :</td><td style="width:32%"><input class="intext" type="text" name="weeklyspecial_forwarders['+forwarders+'][contact_email]" value="" /></td></tr><tr><td class="left_title" colspan="4" style="text-align:center;"><input style="width:100px;" type="button" value=" Delete " onclick="weeklyspecialForwarderDelete('+forwarders+')" /></td></tr></table>');
	forwarders++;
}
function weeklyspecialForwarderDelete(row)
{
	if(confirm('Are You Sure you want to delete this freight forwarder??'))
	{
		var theRow=getElement('weeklyspecial_forwarders_rowa['+row+']');
		if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	}
}
//]]>
</script>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionWeeklySpecialFreight\');">Freight Forwarders</a>', '<a href="#" style="color:#000;" onclick="weeklyspecialForwarderAdd();return false;">Add New Forwarder</a>') ?>

<div id="sectionWeeklySpecialFreight">
	<table class="wide" id="weeklyspecial_forwarders_table">
<?php
	$count=0;
	$affected=array();
	foreach($weeklyspecial_forwarders as $forwarder)
	{
		$count++;
		$affected[]=$forwarder['forwarder_id'];
?>
		<tr id="weeklyspecial_forwarders_rowa[<?= $count ?>]">
			<td><table style="width:100%;">
				<tr>
					<td class="left_title" style="width:18%;">Company Name :</td>
					<td style="width:32%;">
						<input type="hidden" name="weeklyspecial_forwarders[<?= $count ?>][forwarder_id]" value="<?= $forwarder['forwarder_id'] ?>" />
						<input type="hidden" name="weeklyspecial_forwarders[<?= $count ?>][id]" value="<?= $forwarder['id'] ?>" />
						<input class="intext" type="text" name="weeklyspecial_forwarders[<?= $count ?>][company]" value="<?= htmlspecialchars($forwarder['company']) ?>" />
					</td>
					<td class="left_title" style="width:18%;">Phone :</td>
					<td style="width:32%;"><input class="intext" type="text" name="weeklyspecial_forwarders[<?= $count ?>][phone]" value="<?= htmlspecialchars($forwarder['phone']) ?>" /></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;">Address :</td>
					<td style="width:32%" rowspan="2"><textarea name="weeklyspecial_forwarders[<?= $count ?>][address]"><?= htmlspecialchars($forwarder['address']) ?></textarea></td>
					<td class="left_title" style="width:18%;">Fax :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_forwarders[<?= $count ?>][fax]" value="<?= htmlspecialchars($forwarder['fax']) ?>" /></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;"></td>
					<td class="left_title" style="width:18%;">Mobile :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_forwarders[<?= $count ?>][mobile]" value="<?= htmlspecialchars($forwarder['mobile']) ?>" /></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;">City :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_forwarders[<?= $count ?>][city]" value="<?= htmlspecialchars($forwarder['city']) ?>" /></td>
					<td class="left_title" style="width:18%;">State :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_forwarders[<?= $count ?>][state]" value="<?= htmlspecialchars($forwarder['state']) ?>" /></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;">Post Code :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_forwarders[<?= $count ?>][post_code]" value="<?= htmlspecialchars($forwarder['post_code']) ?>" /></td>
					<td class="left_title" style="width:18%;">Country :</td>
					<td style="width:32%"><select name="weeklyspecial_forwarders[<?= $count ?>][country]">
						<option value="">Not Selected</option>
						<?= formatHTMLOptions($section_handler->getOptionList('country'),$forwarder['country']) ?>
					</select></td>
				</tr>
				<tr>
					<td class="left_title" style="width:18%;">Main Contact Name :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_forwarders[<?= $count ?>][contact_name]" value="<?= htmlspecialchars($forwarder['contact_name']) ?>" /></td>
					<td class="left_title" style="width:18%;">Email :</td>
					<td style="width:32%"><input class="intext" type="text" name="weeklyspecial_forwarders[<?= $count ?>][contact_email]" value="<?= htmlspecialchars($forwarder['contact_email']) ?>" /></td>
				</tr>
				<tr>
					<td class="left_title" colspan="4" style="text-align:center;"><input style="width:100px;" type="button" value=" Delete " onclick="weeklyspecialForwarderDelete(<?= $count ?>)" /></td>
				</tr>
			</table></td>
		</tr>
<?php
	}
?>
	</table>
	<input type="hidden" name="weeklyspecial_forwarders_affected" value="<?= implode(',',$affected) ?>" />
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionWeeklySpecialFreight_hideNote">Freight Forwarders can be viewed by Clicking "Freight Forwarders" above<br /></div>

<?php
}
?>

<!-- /freight.tpl.php -->
<!-- freight.tpl -->

<?php
moduleLoad('member'); //memberGetOptionList

if (isset($member_forwarders) && is_array($member_forwarders)) {
?>

<script type="text/javascript">
//<![CDATA[
var forwarders = 100;
function memberForwarderAdd() {
    var ship_table = getElement('member_forwarders_table');
    if (ship_table == null) return;
    var newRow = addTableRow(ship_table, 'member_forwarders_rowa[' + forwarders + ']');
    addTableCell(newRow, '<table class="table_fields" style="width:100%;"><tr><th style="width:18%;">Company Name :</th><td style="width:32%;"><input type="hidden" name="member_forwarders[' + forwarders + '][forwarder_id]" value="" /><input type="hidden" name="member_forwarders[' + forwarders + '][member_id]" value="<?=$member_id?>" /><input class="intext" type="text" name="member_forwarders[' + forwarders + '][company]" value="" /></td><th style="width:18%;">Phone :</th><td style="width:32%;"><input class="intext" type="text" name="member_forwarders[' + forwarders + '][phone]" value="" /></td></tr><tr><th style="width:18%;">Address :</th><td style="width:32%" rowspan="2"><textarea name="member_forwarders[' + forwarders + '][address]"></textarea></td><th style="width:18%;">Fax :</th><td style="width:32%"><input class="intext" type="text" name="member_forwarders[' + forwarders + '][fax]" value="" /></td></tr><tr><th style="width:18%;"></th><th style="width:18%;">Mobile :</th><td style="width:32%"><input class="intext" type="text" name="member_forwarders[' + forwarders + '][mobile]" value="" /></td></tr><tr><th style="width:18%;">City :</th><td style="width:32%"><input class="intext" type="text" name="member_forwarders[' + forwarders + '][city]" value="" /></td><th style="width:18%;">State :</th><td style="width:32%"><input class="intext" type="text" name="member_forwarders[' + forwarders + '][state]" value="" /></td></tr><tr><th style="width:18%;">Post Code :</th><td style="width:32%"><input class="intext" type="text" name="member_forwarders[' + forwarders + '][post_code]" value="" /></td><th style="width:18%;">Country :</th><td style="width:32%"><select name="member_forwarders[' + forwarders + '][country]"><option value="">Not Selected</option>' + <?php jecho(formatHTMLOptions(memberGetOptionList('country')))?> +'</select></td></tr><tr><th style="width:18%;">Main Contact Name :</th><td style="width:32%"><input class="intext" type="text" name="member_forwarders[' + forwarders + '][contact_name]" value="" /></td><th style="width:18%;">Email :</th><td style="width:32%"><input class="intext" type="text" name="member_forwarders[' + forwarders + '][contact_email]" value="" /></td></tr><tr><th colspan="4" style="text-align:center;"><input style="width:100px;" type="button" value=" Delete " onclick="memberForwarderDelete(' + forwarders + ')" /></th></tr></table>');
    forwarders++;
}
function memberForwarderDelete(row) {
    if (confirm('Are You Sure you want to delete this freight forwarder??')) {
        var theRow = getElement('member_forwarders_rowa[' + row + ']');
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    }
}
//]]>
</script>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionMemberFreight\');">Freight Forwarders</a>', '<a href="#" style="color:#000;" onclick="memberForwarderAdd();return false;">Add New Forwarder</a>')?>

<div id="sectionMemberFreight">
	<table class="table_grid" id="member_forwarders_table">
<?php
    $count    = 0;
    $affected = array();
    foreach ($member_forwarders as $forwarder) {
        $count++;
        $affected[] = $forwarder['forwarder_id'];
?>
		<tr id="member_forwarders_rowa[<?=$count?>]">
			<td><table class="table_fields" style="width:100%;">
				<tr>
					<th style="width:18%;">Company Name :</th>
					<td style="width:32%;">
						<input type="hidden" name="member_forwarders[<?=$count?>][forwarder_id]" value="<?=$forwarder['forwarder_id']?>" />
						<input type="hidden" name="member_forwarders[<?=$count?>][member_id]" value="<?=$forwarder['member_id']?>" />
						<input class="intext" type="text" name="member_forwarders[<?=$count?>][company]" value="<?php hecho($forwarder['company'])?>" />
					</td>
					<th style="width:18%;">Phone :</th>
					<td style="width:32%;"><input class="intext" type="text" name="member_forwarders[<?=$count?>][phone]" value="<?php hecho($forwarder['phone'])?>" /></td>
				</tr>
				<tr>
					<th style="width:18%;">Address :</th>
					<td style="width:32%" rowspan="2"><textarea name="member_forwarders[<?=$count?>][address]"><?php hecho($forwarder['address'])?></textarea></td>
					<th style="width:18%;">Fax :</th>
					<td style="width:32%"><input class="intext" type="text" name="member_forwarders[<?=$count?>][fax]" value="<?php hecho($forwarder['fax'])?>" /></td>
				</tr>
				<tr>
					<th style="width:18%;"></th>
					<th style="width:18%;">Mobile :</th>
					<td style="width:32%"><input class="intext" type="text" name="member_forwarders[<?=$count?>][mobile]" value="<?php hecho($forwarder['mobile'])?>" /></td>
				</tr>
				<tr>
					<th style="width:18%;">City :</th>
					<td style="width:32%"><input class="intext" type="text" name="member_forwarders[<?=$count?>][city]" value="<?php hecho($forwarder['city'])?>" /></td>
					<th style="width:18%;">State :</th>
					<td style="width:32%"><input class="intext" type="text" name="member_forwarders[<?=$count?>][state]" value="<?php hecho($forwarder['state'])?>" /></td>
				</tr>
				<tr>
					<th style="width:18%;">Post Code :</th>
					<td style="width:32%"><input class="intext" type="text" name="member_forwarders[<?=$count?>][post_code]" value="<?php hecho($forwarder['post_code'])?>" /></td>
					<th style="width:18%;">Country :</th>
					<td style="width:32%"><select name="member_forwarders[<?=$count?>][country]">
						<option value="">Not Selected</option>
						<?=formatHTMLOptions(memberGetOptionList('country'), $forwarder['country'])?>
					</select></td>
				</tr>
				<tr>
					<th style="width:18%;">Main Contact Name :</th>
					<td style="width:32%"><input class="intext" type="text" name="member_forwarders[<?=$count?>][contact_name]" value="<?php hecho($forwarder['contact_name'])?>" /></td>
					<th style="width:18%;">Email :</th>
					<td style="width:32%"><input class="intext" type="text" name="member_forwarders[<?=$count?>][contact_email]" value="<?php hecho($forwarder['contact_email'])?>" /></td>
				</tr>
				<tr>
					<th colspan="4" style="text-align:center;"><input style="width:100px;" type="button" value=" Delete " onclick="memberForwarderDelete(<?=$count?>)" /></th>
				</tr>
			</table></td>
		</tr>
<?php
    }
?>
	</table>
	<input type="hidden" name="member_forwarders_affected" value="<?=implode(',', $affected)?>" />
	<br /><br />
</div>

<div style="background-color:#fff;text-align:center;padding:8px;display:none;" id="sectionMemberFreight_hideNote">Freight Forwarders can be viewed by Clicking "Freight Forwarders" above</div>

<?php
}
?>

<!-- /freight.tpl -->
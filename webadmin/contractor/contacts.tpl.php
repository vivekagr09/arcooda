<!-- contacts.tpl -->

<?php
if (isset($contractor_contacts) && is_array($contractor_contacts)) {
?>

<script type="text/javascript">
//<![CDATA[
var contacts = 100;
function contractorContactAdd() {
    var con_table = getElement('contractor_contacts_table');
    if (con_table == null) return;
    var newRow = addTableRow(con_table, 'contractor_contacts_rowa[' + contacts + ']');
    addTableCell(newRow, '<table class="table_fields" style="width:100%;" border="0"><tr><th rowspan="5" style="width:10%;text-align:center;vertical-align:middle;"><input type="hidden" name="contractor_contacts[' + contacts + '][id]" value="" /><input type="hidden" name="contractor_contacts[' + contacts + '][contractor_id]" value="<?=$contractor_id?>" /><input type="checkbox" name="contractor_contacts[' + contacts + '][main]" value="y" onclick="contractorContactSelect(' + contacts + ')" /><br /><span id="contractor_contacts_default[' + contacts + ']" style="display:none;"><small>Default</small></span></th><th style="width:25%;">Contact Name :</th><td style="width:55%;"><input class="intext" type="text" name="contractor_contacts[' + contacts + '][name]" value="" /></td><th rowspan="5" style="text-align:center;width:10%;vertical-align:middle;"><a href="#" onclick="contractorContactDelete(' + contacts + ');return false;">Delete</a></th></tr><tr><th>Title :</th><td><input class="intext" type="text" name="contractor_contacts[' + contacts + '][title]" value="" /></td></tr><tr><th>Email Address :</th><td><input class="intext" type="text" name="contractor_contacts[' + contacts + '][email]" value="" /></td></tr><tr><th>Phone :</th><td><input class="intext" type="text" name="contractor_contacts[' + contacts + '][phone]" value="" /></td></tr><tr><th>Emails sent to this address :</th><td><select name="contractor_contacts[' + contacts + '][send_email_cc]"><option value="y">Yes</option><option value="n" selected="selected">No</option></select></td></tr></table>');
    var con_rows = contractorContactGetRows();
    if (con_rows.length == 1) contractorContactSelect(con_rows[0]);
    contacts++;
}
function contractorContactDelete(row) {
    if (confirm('Are You Sure you want to delete this contact??')) {
        setValue("contractor_contacts_deleted", getValue("contractor_contacts_deleted") + "," + getValue('contractor_contacts[' + row + '][id]'));
        var theRow = getElement('contractor_contacts_rowa[' + row + ']');
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
        var con_rows = contractorContactGetRows();
        if (con_rows.length == 1) contractorContactSelect(con_rows[0]);
    }
}
function contractorContactGetRows() {
    var con_table = getElement("contractor_contacts_table");
    var con_rows = [];
    var con_rows2 = {};
    if (con_table != null) {
        var theRows = con_table.rows;
        for (var i = 0; i < theRows.length; i++) if (theRows[i].id.substr(0, 21) == 'contractor_contacts_rowa[') {
            var key = theRows[i].id.substr(21);
            key = key.substr(0, key.length - 1);
            if (con_rows2[key] == null) {
                con_rows2[key] = true;
                con_rows[con_rows.length] = key;
            }
        }
    }
    return con_rows;
}
function contractorContactSelect(row) {
    var con_rows = contractorContactGetRows();
    for (var i = 0; i < con_rows.length; i++) if (con_rows[i] == row) {
        getElement('contractor_contacts[' + con_rows[i] + '][main]').checked = true;
        getElement('contractor_contacts_default[' + con_rows[i] + ']').style.display = '';
    }
    else {
        getElement('contractor_contacts[' + con_rows[i] + '][main]').checked = false;
        getElement('contractor_contacts_default[' + con_rows[i] + ']').style.display = 'none';
    }
}
//]]>
</script>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectioncontractorContacts\');">Contractor Contacts</a>', '<a style="color:black;" href="#" onclick="contractorContactAdd();return false;">Add New Contact</a>')?>

<div id="sectioncontractorContacts">
	<table class="table_grid" id="contractor_contacts_table">
<?php
    $count    = 0;
    $affected = array();
    foreach ($contractor_contacts as $contact) {
        $count++;
        $affected[] = $contact['id'];
?>
		<tr id="contractor_contacts_rowa[<?=$count?>]">
			<td><table class="table_fields" style="width:100%;" border="0">
				<tr>
					<th rowspan="5" style="width:10%;text-align:center;vertical-align:middle;">
						<input type="hidden" name="contractor_contacts[<?=$count?>][id]" value="<?=$contact['id']?>" />
						<input type="hidden" name="contractor_contacts[<?=$count?>][contractor_id]" value="<?=$contact['contractor_id']?>" />
						<input type="checkbox" name="contractor_contacts[<?=$count?>][main]" value="y"<?=('y' == $contact['main'] ? ' checked="checked"' : '')?> onclick="contractorContactSelect(<?=$count?>)" /><br />
						<span id="contractor_contacts_default[<?=$count?>]"<?=('y' == $contact['main']) ? '' : ' style="display:none;"'?>><small>Default</small></span>
					</th>
					<th style="width:25%;">Contact Name :</th>
					<td style="width:55%;"><input class="intext" type="text" name="contractor_contacts[<?=$count?>][name]" value="<?php hecho($contact['name'])?>" /></td>
					<th rowspan="5" style="text-align:center;width:10%;vertical-align:middle;"><a href="#" onclick="contractorContactDelete(<?=$count?>);return false;">Delete</a></th>
				</tr>
				<tr>
					<th>Title :</th>
					<td><input class="intext" type="text" name="contractor_contacts[<?=$count?>][title]" value="<?php hecho($contact['title'])?>" /></td>
				</tr>
				<tr>
					<th>Email Address :</th>
					<td><input class="intext" type="text" name="contractor_contacts[<?=$count?>][email]" value="<?php hecho($contact['email'])?>" /></td>
				</tr>
				<tr>
					<th>Phone :</th>
					<td><input class="intext" type="text" name="contractor_contacts[<?=$count?>][phone]" value="<?php hecho($contact['phone'])?>" /></td>
				</tr>
				<tr>
					<th>Emails sent to this address :</th>
					<td><select name="contractor_contacts[<?=$count?>][send_email_cc]">
						<option value="y">Yes</option>
						<option value="n"<?=($contact['email'] != $contractor['email_address']) ? ' selected="selected"' : ''?>>No</option>
					</select></td>
				</tr>
			</table></td>
		</tr>
<?php
    }
?>
	</table>
	<input type="hidden" name="contractor_contacts_affected" value="<?=implode(',', $affected)?>" />
	<input type="hidden" name="contractor_contacts_deleted" value="" />
	<br /><br />
</div>

<div style="background-color:#fff;text-align:center;padding:8px;display:none;" id="sectioncontractorContacts_hideNote">Company Contacts can be viewed by Clicking "Company Contacts" above</div>

<?php
}
?>

<!-- /contacts.tpl -->

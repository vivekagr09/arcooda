<!-- contacts.tpl -->

<?php
if (isset($supplier_contacts) && is_array($supplier_contacts)) {
?>

<script type="text/javascript">
//<![CDATA[
var contacts = 100;
function supplierContactAdd() {
    console.log('supplier_new')
    var con_table = getElement('supplier_contacts_table');
    if (con_table == null) return;
    var newRow = addTableRow(con_table, 'supplier_contacts_rowa[' + contacts + ']');
    addTableCell(newRow, '<table class="table_fields" style="width:100%;" border="0"><tr><th rowspan="8" style="width:10%;text-align:center;vertical-align:middle;"><input type="hidden" name="supplier_contacts[' + contacts + '][id]" value="" /><input type="hidden" name="supplier_contacts[' + contacts + '][supplier_id]" value="<?=$supplier_id?>" /><input type="checkbox" name="supplier_contacts[' + contacts + '][main]" value="y" onclick="supplierContactNewSelect(' + contacts + ')" class="supplierContactSelect"/><br /><span id="supplier_contacts_default[' + contacts + ']"><small>Default</small></span></th><th style="width:25%;">Contact Name :</th><td style="width:55%;"><input class="intext" type="text" name="supplier_contacts[' + contacts + '][name]" value="" /></td><th rowspan="8" style="text-align:center;width:10%;vertical-align:middle;"><a href="#" onclick="supplierContactDelete(' + contacts + ');return false;">Delete</a></th></tr><tr><th>联系人姓名 :</th><td><input class="intext" type="text" name="supplier_contacts[' + contacts + '][name_chinese]" value="" /></td></tr><tr><th>Title :</th><td><input class="intext" type="text" name="supplier_contacts[' + contacts + '][title]" value="" /></td></tr><tr><th>标题 :</th><td><input class="intext" type="text" name="supplier_contacts[' + contacts + '][title_chinese]" value="" /></td></tr><tr><th>Email Address /电子邮件地址 :</th><td><input class="intext" type="text" name="supplier_contacts[' + contacts + '][email]" value="" /></td></tr><tr><th>Phone / 电话:</th><td><input class="intext" type="text" name="supplier_contacts[' + contacts + '][phone]" value="" /></td></tr><tr><th>Wechat / 微信:</th><td><input class="intext" type="text" name="supplier_contacts[' + contacts + '][wechat]" value="" /></td></tr><tr><th>Emails sent to this address / 发送到此地址的电子邮件 :</th><td><select name="supplier_contacts[' + contacts + '][send_email_cc]"><option value="y">Yes</option><option value="n" selected="selected">No</option></select></td></tr></table>');
    var con_rows = supplierContactGetRows();
    if (con_rows.length == 1) supplierContactNewSelect(con_rows[0]);
    contacts++;
}
function supplierContactDelete(row) {
    if (confirm('Are You Sure you want to delete this contact??')) {
        setValue("supplier_contacts_deleted", getValue("supplier_contacts_deleted") + "," + getValue('supplier_contacts[' + row + '][id]'));
        var theRow = getElement('supplier_contacts_rowa[' + row + ']');
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
        var con_rows = supplierContactGetRows();
        if (con_rows.length == 1) supplierContactNewSelect(con_rows[0]);
    }
}
function supplierContactGetRows() {
    var con_table = getElement("supplier_contacts_table");
    var con_rows = [];
    var con_rows2 = {};
    if (con_table != null) {
        var theRows = con_table.rows;
        for (var i = 0; i < theRows.length; i++){
            console.log(theRows[i].id.substr(0, 21));
            if (theRows[i].id.substr(0, 21) == 'supplier_contacts_rowa[') {
                var key = theRows[i].id.substr(21);
                key = key.substr(0, key.length - 1);
                if (con_rows2[key] == null) {
                    con_rows2[key] = true;
                    con_rows[con_rows.length] = key;
                }
            }
        }
    }
    return con_rows;
}
function supplierContactNewSelect(row) {
    $('.supplierContactSelect').prop('checked', false);
    $('input:checkbox[name="supplier_contacts['+row+'][main]"]').prop('checked', true);
}
//]]>
</script>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionSupplierContacts\');">Supplier Contacts</a>', '<a style="color:black;" href="#" onclick="supplierContactAdd();return false;">Add New Contact</a>')?>

<div id="sectionSupplierContacts">
	<table class="table_grid" id="supplier_contacts_table">
<?php
    $count    = 0;
    $affected = array();
    foreach ($supplier_contacts as $contact) {
        $count++;
        $affected[] = $contact['id'];
?>
		<tr id="supplier_contacts_rowa[<?=$count?>]">
			<td><table class="table_fields" style="width:100%;" border="0">
				<tr>
					<th rowspan="8" style="width:10%;text-align:center;vertical-align:middle;">
						<input type="hidden" name="supplier_contacts[<?=$count?>][id]" value="<?=$contact['id']?>" />
						<input type="hidden" name="supplier_contacts[<?=$count?>][supplier_id]" value="<?=$contact['supplier_id']?>" />
						<input type="checkbox" name="supplier_contacts[<?=$count?>][main]" value="y"<?=('y' == $contact['main'] ? ' checked="checked"' : '')?> onclick="supplierContactNewSelect(<?=$count?>)" class="supplierContactSelect"/><br />
						<span id="supplier_contacts_default[<?=$count?>]"><small>Default</small></span>
					</th>
					<th style="width:25%;">Contact Name :</th>
					<td style="width:55%;"><input class="intext" type="text" name="supplier_contacts[<?=$count?>][name]" value="<?php hecho($contact['name'])?>" /></td>
					<th rowspan="8" style="text-align:center;width:10%;vertical-align:middle;"><a href="#" onclick="supplierContactDelete(<?=$count?>);return false;">Delete</a></th>
				</tr>
                <tr>
                    <th>联系人姓名 :</th>
                    <td><input class="intext" type="text" name="supplier_contacts[<?=$count?>][name_chinese]" value="<?php hecho($contact['name_chinese'])?>" /></td>
                </tr>
				<tr>
					<th>Title :</th>
					<td><input class="intext" type="text" name="supplier_contacts[<?=$count?>][title]" value="<?php hecho($contact['title'])?>" /></td>
				</tr>
                <tr>
                    <th>标题 :</th>
                    <td><input class="intext" type="text" name="supplier_contacts[<?=$count?>][title_chinese]" value="<?php hecho($contact['title_chinese'])?>" /></td>
                </tr>
				<tr>
					<th>Email Address / 电子邮件地址:</th>
					<td><input class="intext" type="text" name="supplier_contacts[<?=$count?>][email]" value="<?php hecho($contact['email'])?>" /></td>
				</tr>
				<tr>
					<th>Phone / 电话:</th>
					<td><input class="intext" type="text" name="supplier_contacts[<?=$count?>][phone]" value="<?php hecho($contact['phone'])?>" /></td>
				</tr>
                <tr>
                    <th>Wechat / 微信:</th>
                    <td><input class="intext" type="text" name="supplier_contacts[<?=$count?>][wechat]" value="<?php hecho($contact['wechat'])?>" /></td>
                </tr>
				<tr>
					<th>Emails sent to this address / 发送到此地址的电子邮件 :</th>
					<td><select name="supplier_contacts[<?=$count?>][send_email_cc]">
						<option value="y">Yes</option>
						<option value="n"<?=($contact['email'] != $supplier['email_address']) ? ' selected="selected"' : ''?>>No</option>
					</select></td>
				</tr>
                <!-- <tr>
                    <th>发送到此地址的电子邮件 :</th>
                    <td><select name="supplier_contacts[<?=$count?>][send_email_cc_chinese]">
                        <option value="y">是的</option>
                        <option value="n"<?=($contact['email'] != $supplier['email_address']) ? ' selected="selected"' : ''?>>不</option>
                    </select></td>
                </tr> -->
			</table></td>
		</tr>
<?php
    }
?>
	</table>
	<input type="hidden" name="supplier_contacts_affected" value="<?=implode(',', $affected)?>" />
	<input type="hidden" name="supplier_contacts_deleted" value="" />
	<br /><br />
</div>

<!-- <div style="background-color:#fff;text-align:center;padding:8px;display:none;" id="sectionMemberContacts_hideNote">Company Contacts can be viewed by Clicking "Company Contacts" above</div> -->

<?php
}
?>

<!-- /contacts.tpl -->

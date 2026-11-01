<!-- contacts.tpl -->

<?php
if (isset($supplier_bank_account) && is_array($supplier_bank_account)) {
?>

<script type="text/javascript">
//<![CDATA[
var contacts = 100;
function supplierBankAdd() {
    var con_table = getElement('supplier_bank_table');
    if (con_table == null) return;
    var newRow = addTableRow(con_table, 'supplier_bank_rowa[' + contacts + ']');
    addTableCell(newRow, '<table class="table_fields" style="width:100%;" border="0"> <tr> <th rowspan="2" style="width:10%;text-align:center;vertical-align:middle;"><input type="hidden" name="supplier_bank[' + contacts + '][id]" value="" /><input type="hidden" name="supplier_bank[' + contacts + '][supplier_id]" value="<?=$supplier_id?>" /><input type="checkbox" name="supplier_bank[' + contacts + '][main]" value="y" onclick="supplierContactSelect(' + contacts + ')" /><br /><span id="supplier_bank_default[' + contacts + ']" style="display:none;"><small>Default</small></span></th> <th style="width:25%;">Payment Method :</th> <td> <select name="supplier_bank[' + contacts + '][payment_mode]"> <option value="rmb_bank_account">RMB Bank Account</option> <option value="hkd_bank_account">HKD Bank Account</option> <option value="usd_bank_account">USD Bank Account</option> <option value="paypal">Paypal</option> <option value="wester_union">Western Union</option> <option value="cash">Cash</option> </select> </td> <th rowspan="2" style="text-align:center;width:10%;vertical-align:middle;"><a href="#" onclick="supplierContactDelete(' + contacts + ');return false;">Delete</a></th> </tr> <tr> <th>Payment Details:</th> <td><textarea class="intext" type="text" name="supplier_bank[' + contacts + '][payment_detail]" value="" rows="7"></textarea> </td> </tr> </table>');
    var con_rows = supplierContactGetRows();
    if (con_rows.length == 1) supplierContactSelect(con_rows[0]);
    contacts++;
}
function supplierContactDelete(row) {
    if (confirm('Are You Sure you want to delete this contact??')) {
        setValue("supplier_bank_deleted", getValue("supplier_bank_deleted") + "," + getValue('supplier_bank[' + row + '][id]'));
        var theRow = getElement('supplier_bank_rowa[' + row + ']');
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
        var con_rows = supplierContactGetRows();
        if (con_rows.length == 1) supplierContactSelect(con_rows[0]);
    }
}
function supplierContactGetRows() {
    var con_table = getElement("supplier_bank_table");
    var con_rows = [];
    var con_rows2 = {};
    if (con_table != null) {
        var theRows = con_table.rows;
        for (var i = 0; i < theRows.length; i++){ 
                if (theRows[i].id.substr(0, 19) == 'supplier_bank_rowa[') {
                var key = theRows[i].id.substr(19);
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
function supplierContactSelect(row) {
    var con_rows = supplierContactGetRows();
    for (var i = 0; i < con_rows.length; i++) if (con_rows[i] == row) {
        getElement('supplier_bank[' + con_rows[i] + '][main]').checked = true;
        getElement('supplier_bank_default[' + con_rows[i] + ']').style.display = '';
    }
    else {
        getElement('supplier_bank[' + con_rows[i] + '][main]').checked = false;
        getElement('supplier_bank_default[' + con_rows[i] + ']').style.display = 'none';
    }
}
//]]>
</script>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionSupplierContacts\');">Supplier Bank Details</a>', '<a style="color:black;" href="#" onclick="supplierBankAdd();return false;">Add New Account Details</a>')?>

<div id="sectionSupplierContacts">
	<table class="table_grid" id="supplier_bank_table">
<?php
    $count    = 0;
    $affected = array();
    foreach ($supplier_bank_account as $contact) {
        $count++;
        $affected[] = $contact['id'];
?>
		<tr id="supplier_bank_rowa[<?=$count?>]">
			<td><table class="table_fields" style="width:100%;" border="0">
				<tr>
					<th rowspan="2" style="width:10%;text-align:center;vertical-align:middle;">
						<input type="hidden" name="supplier_bank[<?=$count?>][id]" value="<?=$contact['id']?>" />
						<input type="hidden" name="supplier_bank[<?=$count?>][supplier_id]" value="<?=$contact['supplier_id']?>" />
						<input type="checkbox" name="supplier_bank[<?=$count?>][main]" value="y"<?=('y' == $contact['main'] ? ' checked="checked"' : '')?> onclick="supplierContactSelect(<?=$count?>)" /><br />
						<span id="supplier_bank_default[<?=$count?>]"<?=('y' == $contact['main']) ? '' : ' style="display:none;"'?>><small>Default</small></span>
					</th>
					<th style="width:25%;">Payment Method :</th>
					<td style="width:55%;">
                         <select name="supplier_bank[<?=$count?>][payment_mode]"> 
                            <option value="rmb_bank_account" <?= ($contact['payment_mode']=='rmb_bank_account')?'selected':''?>>RMB Bank Account</option>
                            <option value="hkd_bank_account" <?= ($contact['payment_mode']=='hkd_bank_account')?'selected':''?>>HKD Bank Account</option> 
                            <option value="usd_bank_account" <?= ($contact['payment_mode']=='usd_bank_account')?'selected':''?>>USD Bank Account</option> 
                            <option value="paypal" <?= ($contact['payment_mode']=='paypal')?'selected':''?>>Paypal</option> 
                            <option value="wester_union" <?= ($contact['payment_mode']=='wester_union')?'selected':''?>>Western Union</option> 
                            <option value="cash" <?= ($contact['payment_mode']=='cash')?'selected':''?>>Cash</option> 
                        </select>
					   <th rowspan="2" style="text-align:center;width:10%;vertical-align:middle;"><a href="#" onclick="supplierContactDelete(<?=$count?>);return false;">Delete</a></th>
				</tr>
               <tr> 
                    <th>Payment Details:</th> 
                    <td>
                        <textarea class="intext" type="text" name="supplier_bank[<?=$count?>][payment_detail]" value="<?= $contact['payment_detail'] ?>" rows="7"><?= $contact['payment_detail'] ?>
                        </textarea> 
                    </td> 
               </tr>
				
			</table></td>
		</tr>
<?php
    }
?>
	</table>
	<input type="hidden" name="supplier_bank_affected" value="<?=implode(',', $affected)?>" />
	<input type="hidden" name="supplier_bank_deleted" value="" />
	<br /><br />
</div>

<?php
}
?>

<!-- /contacts.tpl -->

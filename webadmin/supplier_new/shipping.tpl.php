<!-- shipping.tpl -->

<?php
if (isset($supplier_shipping) && is_array($supplier_shipping)) {
?>

<script type="text/javascript">
function suppliershipingDefaultSelect(value){
	  $('.suppliermaincheckbox').prop('checked', false);
	  $('input:checkbox[name="supplier_shipping['+value+'][default]"]').prop('checked', true);
}
//<![CDATA[
var shipping = 100;
function supplierShippingAdd() {
    var ship_table = getElement('supplier_shipping_table');
    if (ship_table == null) return;
    var newRow = addTableRow(ship_table, 'supplier_shipping_rowa[' + shipping + ']');
    addTableCell(newRow, '<table class="table_fields" style="width:100%;"> <tr> <th rowspan="10" style="width:10%;text-align:center;vertical-align:middle;"><input type="checkbox" name="supplier_shipping[' + shipping + '][default]" value="y" onclick="suppliershipingDefaultSelect(' + shipping + ')" class="suppliermaincheckbox"><br/> <span><small>Default</small></span></th><th style="width:15%;">Company Name :</th> <td style="width:25%;"><input type="hidden" name="supplier_shipping[' + shipping + '][id]" value="" /><input type="hidden" name="supplier_shipping[' + shipping + '][supplier_id]" value="" /><input class="intext" type="text" name="supplier_shipping[' + shipping + '][company]" value="" /></td> <th style="width:15%;">公司名 :</th> <td style="width:25%;"><input class="intext" type="text" name="supplier_shipping[' + shipping + '][company_chinese]" value="" /></td> </tr> <tr> <th style="width:15%;">Address :</th> <td style="width:25%"><textarea name="supplier_shipping[' + shipping + '][address_1]" rows="2" cols="40"></textarea></td> <th style="width:15%;">地址 :</th> <td style="width:25%"><textarea name="supplier_shipping[' + shipping + '][address_1_chinese]" rows="2" cols="40"></textarea></td> </tr> <tr> <th style="width:15%;">City :</th> <td style="width:25%"><input class="intext" type="text" name="supplier_shipping[' + shipping + '][city]" value="" /></td> <th style="width:15%;">城市 :</th> <td style="width:25%"><input class="intext" type="text" name="supplier_shipping[' + shipping + '][city_chinese]" value="" /></td> </tr> <tr> <th style="width:15%;">State :</th> <td style="width:25%"><input class="intext" type="text" name="supplier_shipping[' + shipping + '][state]" value="" /></td> <th style="width:15%;">状态 :</th> <td style="width:25%"><input class="intext" type="text" name="supplier_shipping[' + shipping + '][state_chinese]" value="" /></td> </tr> <tr> <th style="width:15%;">Post Code :</th> <td style="width:25%"><input class="intext" type="text" name="supplier_shipping[' + shipping + '][zip]" value="" /></td> <th style="width:15%;">邮政编码 :</th> <td style="width:25%"><input class="intext" type="text" name="supplier_shipping[' + shipping + '][zip_chinese]" value="" /></td> </tr> <tr> <th style="width:15%;">Country / 国家:</th> <td style="width:25%"> <select name="supplier_shipping[' + shipping + '][country]"> <option value="">Not Selected</option> ' + <?php jecho(formatHTMLOptions(supplierGetOptionList('country')))?> +'</select> </td> <th style="width:15%;">VAT/Company/ 国家增值税/公司:</th> <td style="width:25%"><input class="intext" type="text" name="supplier_shipping[' + shipping + '][vat_number]" value="" /></td> </tr> <tr> <th style="width:15%;">Main Contact Name :</th> <td style="width:25%"><input class="intext" type="text" name="supplier_shipping[' + shipping + '][name]" value="" /></td> <th style="width:15%;">国家增值税/公司 :</th> <td style="width:25%"><input class="intext" type="text" name="supplier_shipping[' + shipping + '][name_chinese]" value="" /></td> </tr> <tr> <th style="width:15%;">Phone / 电话:</th> <td style="width:25%"><input class="intext" type="text" name="supplier_shipping[' + shipping + '][phone_number]" value="" /></td> <th style="width:15%;">2nd Contact / 第二联络号码 :</th> <td style="width:25%"><input class="intext" type="text" name="supplier_shipping[' + shipping + '][phone_no2]" value="" /></td> </tr> <tr> <th style="width:15%;">Fax / 传真 :</th> <td style="width:25%"><input class="intext" type="text" name="supplier_shipping[' + shipping + '][fax_number]" value="" /></td> <th style="width:15%;">Mobile / 移动的 :</th> <td style="width:25%"><input class="intext" type="text" name="supplier_shipping[' + shipping + '][mobile_number]" value="" /></td> </tr> <tr> <th style="width:15%;">Wechat / 微信:</th> <td style="width:25%"><input class="intext" type="text" name="supplier_shipping[' + shipping + '][wechat]" value="" /></td> <th style="width:15%;">Email / 电子邮件 :</th> <td style="width:25%"><input class="intext" type="text" name="supplier_shipping[' + shipping + '][email]" value="" /></td> </tr> <tr> <th colspan="4" style="text-align:center;"><input style="width:100px;" type="button" value=" Delete " onclick="supplierShippingDelete(' + shipping + ')" /></th> </tr> </table>');
    shipping++;
}
function supplierShippingDelete(row) {
    if (confirm('Are You Sure you want to delete this shipping address??')) {
        setValue("supplier_shipping_deleted", getValue("supplier_shipping_deleted") + "," + getValue('supplier_shipping[' + row + '][id]'));
        var theRow = getElement('supplier_shipping_rowa[' + row + ']');
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    }
}
function supplierShippingFromDetails() {
    var key = 'supplier_shipping[' + shipping + ']';
    supplierShippingAdd();
    setValue(key + '[company]', getValue('supplier[company]'));
    setValue(key + '[company_chinese]', getValue('supplier[company_chinese]'));
    setValue(key + '[name]', getValue('supplier[name]') + ' ' + getValue('supplier[surname]'));
    setValue(key + '[name_chinese]', getValue('supplier[name_chinese]') + ' ' + getValue('supplier[surname_chinese]'));
    setValue(key + '[email]', getValue('supplier[email_address]'));
    setValue(key + '[address_1]', getValue('supplier[address_1]'));
    setValue(key + '[address_1_chinese]', getValue('supplier[address_1_chinese]'));
    setValue(key + '[city]', getValue('supplier[city]'));
    setValue(key + '[city_chinese]', getValue('supplier[city_chinese]'));
    setValue(key + '[state]', getValue('supplier[state]'));
    setValue(key + '[state_chinese]', getValue('supplier[state_chinese]'));
    setValue(key + '[country]', getValue('supplier[country]'));
    setValue(key + '[wechat]', getValue('supplier[wechat]'));
    setValue(key + '[vat_number]', getValue('supplier[vat_number]'));
    setValue(key + '[zip]', getValue('supplier[post_code]'));
    setValue(key + '[zip_chinese]', getValue('supplier[post_code_chinese]'));
    setValue(key + '[phone_number]', getValue('supplier[phone_number]'));
    setValue(key + '[fax_number]', getValue('supplier[fax_number]'));
    setValue(key + '[mobile_number]', getValue('supplier[mobile_number]'));
    setValue(key + '[company]', getValue('supplier[company]'));
    setValue(key + '[phone_no2]', getValue('supplier[phone_no2]'));
}
function supplierShippingGetRows() {
    var ship_table = getElement("supplier_shipping_table");
    var ship_rows = [];
    var ship_rows2 = {};
    if (ship_table != null) {
        var theRows = ship_table.rows;
        for (var i = 0; i < theRows.length; i++) if (theRows[i].id.substr(0, 21) == 'supplier_shipping_rowa[') {
            var key = theRows[i].id.substr(21);
            key = key.substr(0, key.length - 1);
            if (ship_rows2[key] == null) {
                ship_rows2[key] = true;
                ship_rows[ship_rows.length] = key;
            }
        }
    }
    return ship_rows;
}
//]]>
</script>

<?=formatHTMLTitle(
	'<a href="#" onclick="toggleSection(\'sectionsupplierShipping\');return false">Supplier Address</a>',
	'<a href="#" style="color:#000;" onclick="supplierShippingFromDetails();return false;">Copy from Above</a> | <a href="#" style="color:#000;" onclick="supplierShippingAdd();return false;">Add New Address</a>'
)?>

<div id="sectionsupplierShipping">
	<table class="table_grid" id="supplier_shipping_table">
<?php
$count    = 0;
    $affected = array();
    foreach ($supplier_shipping as $shipping) {
        $count++;
        $affected[] = $shipping['id'];
?>
		<tr id="supplier_shipping_rowa[<?=$count?>]">
	<td><table class="table_fields" style="width:100%;">
		<tr>
			<th style="width:10%;text-align:center;vertical-align:middle;" rowspan="10"><input type="checkbox" name="supplier_shipping[<?=$count?>][default]" value="y" onclick="suppliershipingDefaultSelect(<?=$count?>)" <?= $shipping['default'] == 'y' ? 'checked':''?> class="suppliermaincheckbox">
				<br/>
				<span><small>Default</small></span>
			</th>
			<th style="width:15%;">Company Name :</th>
			<td style="width:25%;">
				<input type="hidden" name="supplier_shipping[<?=$count?>][id]" value="<?=$shipping['id']?>" />
				<input type="hidden" name="supplier_shipping[<?=$count?>][supplier_id]" value="<?=$shipping['supplier_id']?>" />
				<input class="intext" type="text" name="supplier_shipping[<?=$count?>][company]" value="<?php hecho($shipping['company'])?>" />
			</td>
			<th style="width:15%;">公司名 :</th>
			<td style="width:25%;">
				<input class="intext" type="text" name="supplier_shipping[<?=$count?>][company_chinese]" value="<?php hecho($shipping['company_chinese'])?>" />
			</td>
		</tr>
		<tr>
			<th style="width:15%;">Address :</th>
			<td style="width:25%">
				<textarea name="supplier_shipping[<?=$count?>][address_1]" rows="2" cols="40"><?php hecho(trim($shipping['address_1'] . "\n" . $shipping['address_2']))?></textarea>
				<input type="hidden" name="supplier_shipping[<?=$count?>][address_2]" value="" />
			</td>
			<th style="width:15%;">地址 :</th>
			<td style="width:25%">
				<textarea name="supplier_shipping[<?=$count?>][address_1_chinese]" rows="2" cols="40"><?php hecho(trim($shipping['address_1_chinese'] . "\n" . $shipping['address_2_chinese']))?></textarea>
				<input type="hidden" name="supplier_shipping[<?=$count?>][address_2_chinese]" value="" />
			</td>
		</tr>

		<tr>
			<th style="width:15%;">City :</th>
			<td style="width:25%"><input class="intext" type="text" name="supplier_shipping[<?=$count?>][city]" value="<?php hecho($shipping['city'])?>" /></td>
			<th style="width:15%;">城市 :</th>
			<td style="width:25%"><input class="intext" type="text" name="supplier_shipping[<?=$count?>][city_chinese]" value="<?php hecho($shipping['city_chinese'])?>" /></td>
			
		</tr>
		<tr>
			<th style="width:15%;">State :</th>
			<td style="width:25%"><input class="intext" type="text" name="supplier_shipping[<?=$count?>][state]" value="<?php hecho($shipping['state'])?>" /></td>
			<th style="width:15%;">状态 :</th>
			<td style="width:25%"><input class="intext" type="text" name="supplier_shipping[<?=$count?>][state_chinese]" value="<?php hecho($shipping['state_chinese'])?>" /></td>
		</tr>
		<tr>
			<th style="width:15%;">Post Code :</th>
			<td style="width:25%"><input class="intext" type="text" name="supplier_shipping[<?=$count?>][zip]" value="<?php hecho($shipping['zip'])?>" /></td>
			<th style="width:15%;">邮政编码 :</th>
			<td style="width:25%"><input class="intext" type="text" name="supplier_shipping[<?=$count?>][zip_chinese]" value="<?php hecho($shipping['zip_chinese'])?>" /></td>
		</tr>
		<tr>
			<th style="width:15%;">Country/ 国家:</th>
			<td style="width:25%"><select name="supplier_shipping[<?=$count?>][country]">
				<option value="">Not Selected</option>
				<?=formatHTMLOptions(supplierGetOptionList('country'), $shipping['country'])?>
			</select></td>
			<th style="width:15%;">VAT/Company/ 国家增值税/公司 :</th>
			<td style="width:25%"><input class="intext" type="text" name="supplier_shipping[<?=$count?>][vat_number]" value="<?php hecho($shipping['vat_number'])?>" /></td>
		</tr>
		<tr>
			<th style="width:15%;">Main Contact Name :</th>
			<td style="width:25%"><input class="intext" type="text" name="supplier_shipping[<?=$count?>][name]" value="<?php hecho(trim($shipping['name'] . ' ' . $shipping['surname']))?>" /><input type="hidden" name="supplier_shipping[<?=$count?>][surname]" value="" /></td>

			<th style="width:15%;">主要联系人姓名 :</th>
			<td style="width:25%"><input class="intext" type="text" name="supplier_shipping[<?=$count?>][name_chinese]" value="<?php hecho(trim($shipping['name_chinese'] . ' ' . $shipping['surname_chinese']))?>" /><input type="hidden" name="supplier_shipping[<?=$count?>][surname_chinese]" value="" /></td>
			
		</tr>
		<tr>
			<th style="width:15%;">Phone / 电话:</th>
			<td style="width:25%"><input class="intext" type="text" name="supplier_shipping[<?=$count?>][phone_number]" value="<?php hecho($shipping['phone_number'])?>" /></td>
			<th style="width:15%;">2nd Contact / 第二联络号码 :</th>
			<td style="width:25%"><input class="intext" type="text" name="supplier_shipping[<?=$count?>][phone_no2]" value="<?php hecho($shipping['phone_no2'])?>" /></td>
		</tr>
		<tr>
			<th style="width:15%;">Fax / 传真 :</th>
			<td style="width:25%"><input class="intext" type="text" name="supplier_shipping[<?=$count?>][fax_number]" value="<?php hecho($shipping['fax_number'])?>" /></td>
			<th style="width:15%;">Mobile / 移动的:</th>
			<td style="width:25%"><input class="intext" type="text" name="supplier_shipping[<?=$count?>][mobile_number]" value="<?php hecho($shipping['mobile_number'])?>" /></td>
		</tr>
		<tr>
			<th style="width:15%;">Wechat / 微信:</th>
			<td style="width:25%"><input class="intext" type="text" name="supplier_shipping[<?=$count?>][wechat]" value="<?php hecho($shipping['wechat'])?>" /></td>
			<th style="width:15%;">Email / 电子邮件:</th>
			<td style="width:25%"><input class="intext" type="text" name="supplier_shipping[<?=$count?>][email]" value="<?php hecho($shipping['email'])?>" /></td>
		</tr>
		<tr>
			<th colspan="4" style="text-align:center;"><input style="width:100px;" type="button" value=" Delete " onclick="supplierShippingDelete(<?=$count?>)" /></th>
		</tr>
	</table></td>
</tr>
<?php
}
?>
	</table>
	<input type="hidden" name="supplier_shipping_affected" value="<?=implode(',', $affected)?>" />
	<input type="hidden" name="supplier_shipping_deleted" value="" />
	<br /><br />
</div>


<?php
}
?>

<!-- /shipping.tpl -->

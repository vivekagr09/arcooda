<!-- member_table.tpl -->

<?php
///$site_list = invoiceGetOptionList('site_id');
$site_list=[3=>'Arcooda',5=>'Channel Beat',2=>'Fortune Wave',11=>'Highway Games'];
if ('' == $posted['site_id'] && ($invoice['site_id'] < 1 || 'do_add' == $form_action)) {
    /*if (1 != $invoice['site_id']) {
        unset($site_list[1]);
    }

    if (2 != $invoice['site_id']) {
        unset($site_list[2]);
    }*/

?>
<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;">Company</td>
		<td><select name="site_id">
			<option value="">Not Selected</option>
			<?= formatHTMLOptions($site_list, $invoice['site_id']) ?>
		</select></td>
	</tr>
</table>
<br />
<br />
<?php
} elseif ('do_add' == $form_action) {
?>
<input type="hidden" name="site_id" value="<?php hecho($posted['site_id'])?>" />
<?php
}
?>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionMemberInfo\');">Supplier Information</a>') ?>
<div id="sectionMemberInfo">

<script type="text/javascript">
//<![CDATA[
<?php
if (function_exists('getTimeZones')) {
    $info         = getTimeZones();
    $count1       = 0;
    $count2       = 0;
    $countryList  = array();
    $countryZones = array();
    $zoneIDs      = array();
    $zoneNames    = array();
    $zoneOffsets  = array();
    foreach ($info as $key => $value) {
        $countryList[$count1] = $key;
        foreach ($value as $zone) {
            $countryZones[$count1][] = $count2;
            $zoneIDs[$count2]        = $zone['id'];
            $zoneNames[$count2]      = $zone['zone'];
            $zoneOffsets[$count2]    = $zone['offset'] + (('y' == $zone['day_light']) ? 1 : 0);
            $count2++;
        }
        $count1++;
    }
?>
function countryTimezones(country_id, zone_id)
{
	var countryList = <?php jecho($countryList)?>;
	var countryZones = <?php jecho($countryZones)?>;
	var zoneIDs = <?php jecho($zoneIDs)?>;
	var zoneNames = <?php jecho($zoneNames)?>;
	var zoneOffsets = <?php jecho($zoneOffsets)?>;
	if (zone_id != null)
	{
		reverse_id = array_search(zone_id, zoneIDs);
		return zoneOffsets[reverse_id];
	}
	countryKey = array_search(country_id.toUpperCase(), countryList);
	if (countryKey == null) return null;
	var result = [];
	for(var i = 0;i < countryZones[countryKey].length;i++)
	{
		var zone_id = countryZones[countryKey][i];
		result[zoneIDs[zone_id]] = zoneNames[zone_id];
	}
	return result;
}
<?php
}
?>
var global_url = '/webadmin/supplier_new/';
function editMember()
{
	var supplier_id = getValue('supplier_id');
	if (supplier_id <= 0) return;
	var url = global_url + '?action=edit&return_message=edit&supplier_id=' + escape(supplier_id);
	window.open(url, 'member_popup', 'scrollbars, resizable, height=500, width=900');
}

function searchMember()
{
	var name = getValue('bill_name');
	var url = global_url + '?return_message=select&action=do_search&active=y&search_for='+escape(name);
	window.open(url, 'member_popup', 'scrollbars, resizable, height=500, width=900');
}

function addMember()
{
	var url = global_url + '?action=add&return_message=add';
	window.open(url, 'member_popup', 'scrollbars, resizable, height=500, width=900');
}

function setMemberData(data)
{

	if (data == null) return;
	switch(data['return_message'])
	{
	case 'select':
	case 'add':
	case 'edit':
		if(data['supplier_id']!=null)
		{
			if (document.delMemberAddresses != null) delMemberAddresses();
			if (document.delMemberEmails != null) delMemberEmails();

			setValue('supplier_id', data['supplier_id']);

			var bill_name = '';
			if (data['first_name'] != null) bill_name = data['first_name'];
			if (data['last_name'] != null) bill_name += ((bill_name == '') ? '' : ' ') + data['last_name'];
			if (bill_name != '') setValue('bill_name', bill_name);

			var edit_button = getElement("member_edit");
			if (edit_button) edit_button.disabled = '';

			setValue("bill_email", data['email_address']);
			if (data['company_name'] != null) setValue('bill_company', data['company_name']);
			setValue("bill_address", data['address']);
			setValue("bill_city", data['city']);
			setValue("bill_state", data['state']);
			setValue("bill_country", data['country']);
			setValue("bill_postcode", data['post_code']);
			setValue("bill_phone", data['phone_no']);
			setValue("invoice_currency", data['default_currency']);
			if (data['vat_number'] != null) setValue("bill_vat", data['vat_number']);

			if ($("[name^='product_id[']").length < 1)
			{
				//setValue('invoice_currency', 'USD');
				currencyChange();
			}

			countryChange();

			if (data['country'] == 'AU')
			{
				var state_code = data['post_code'].substr(0, 1);
				setValue('bill_timezone', 12);
				if (state_code == '0') setValue('bill_timezone', 8);	//Darwin
				if (state_code == '2') setValue('bill_timezone', 1);	//Sydney
				if (state_code == '3') setValue('bill_timezone', 2);	//Melbourne
				if (state_code == '4') setValue('bill_timezone', 6);	//Brisbane
				if (state_code == '5') setValue('bill_timezone', 7);	//Adelaide
				if (state_code == '6') setValue('bill_timezone', 9);	//Perth
				if (state_code == '7') setValue('bill_timezone', 2);	//Hobart
				if (state_code == '9') setValue('bill_timezone', 6);	//Brisbane
			}

			if (window.addShippingAddress != null)
			{
				var count = 0;
				clearShippingAddresses();
				if (data['ship_addr'] != null)
				{
					addShippingAddress(data['ship_addr']);
					if (data['ship_addr'][0] != null) setShippingAddress(data['ship_addr'][0]);
					else setShippingAddress(data);
				}
			}

			if (window.addNotifyAddress != null && data['notify'] != null)
			{
				addNotifyAddress(data['notify']);
				if (data['notify'][0] != null) setNotifyAddress(data['notify'][0]);
				else setNotifyAddress(data);
			}
			if (window.addSpecialRequests != null) addSpecialRequests(data['requests']);

			var count = 1;
			if (data['couriers'] != null)
			{
				var table = getElement('members_couriers');
				if (table != null)
				{
					var length = table.rows.length;
					for (i = 0;i < length;i++) table.deleteRow(0);
					for (var courier_id in data['couriers'])
					{
						var courier = data['couriers'][courier_id];
						cid = courier['courier_id'];
						var notes = 'Personal Account (' + courier['account_num'] + ')';
						var newRow = addTableRow(table, 'member_courier[' + count + ']');
						newRow.style.textAlign = 'center';
						addTableCell(newRow, '<input type="hidden" name="courier_id[' + count + ']" value="' + cid + '" /><input type="hidden" name="freightcourier_name[' + count + ']" value="' + courier['courier_name'] + '" /><input type="hidden" name="freightcourier_notes[' + count + ']" value="' + notes + '" /><input type="hidden" name="freightcourier_track[' + count + ']" value="N/A" /><input type="hidden" name="freightcourier_insurance[' + count + ']" value="N/A" /><input type="hidden" name="freightcourier_tail_gate[' + count + ']" value="" /><input type="hidden" name="freightcourier_cost[' + count + ']" value="0.00" /><input type="hidden" name="freightcourier_offer[' + count + ']" value="0.00" /><input type="hidden" name="freightcourier_online[' + count + ']" value="0.00" /><input type="hidden" name="freightcourier_costau[' + count + ']" value="0.00" /><input type="hidden" name="freightcourier_offerau[' + count + ']" value="0.00" /><input type="hidden" name="freightcourier_supplier_id[' + count + ']" value="'+cid+'" /><input type="hidden" name="freightcourier_supplier_name[' + count + ']" value="' + courier['account_num'] + '" /><input type="checkbox" name="freightcourier_flag[' + count + ']" onclick="selectFreightCourier(\'' + count + '\', true);" />');
						addTableCell(newRow, courier['courier_name']);
						addTableCell(newRow, notes);
						addTableCell(newRow, 'N/A');
						addTableCell(newRow, 'N/A');
						addTableCell(newRow, 'N/A');
					}
				}
			}

			if (window.addContactEmail != null)
			{
				clearContactEmails();
				if (data['contacts'] != null) for(var contact_id in data['contacts']) addContactEmail(data['contacts'][contact_id]);
				addContactEmail(data);
			}
		}
	}
}

function currencyChange()
{
	var newCurrency = getValue('invoice_currency');
	setAllValues('product_currency', newCurrency);
}

function countryChange()
{
	setSelectOptions('bill_timezone', countryTimezones(getValue('bill_country')));
	timezoneChange();
}

function timezoneChange()
{
	var country = getValue('bill_country');
	var timezone = getValue('bill_timezone');
	if (country == null || timezone == null) return false;
	var theTime = new Date();
	var theOffset1 = theTime.getTimezoneOffset();
	var theOffset2 = countryTimezones(country, timezone) * 60;
	if (isNaN(theOffset2)) setValue('LiveClockIE', "Time Not Available");
	else
	{
		var newTime = new Date(theTime.valueOf() + (theOffset1 + theOffset2) * 60 * 1000);
		setValue('LiveClockIE', newTime.toLocaleString());
	}
}

var category=<?= intval($invoice['producttype_id']) ?>;
function onChangeCategory()
{
	var warn=false;
	var ids=getProductTableIds();
	if(ids != null && ids.length>0)
	{
		for(key in ids)
		{
			var gen=getValue('generic['+ids[key]+']');
			if(gen=='n')
			{
				var id=getValue('product_id['+ids[key]+']');
				if(id>0) warn=true;
			}
		}
	}
	if(warn) setValue("producttype_id",category);
	else category=getValue("producttype_id");
}
//]]>
</script>
<input name="order_id" type="hidden" value="<?php hecho($order_id)?>" />
<input name="staff_id" type="hidden" value="<?php hecho($staff_id)?>" />
<input name="supplier_id" type="hidden" value="<?php hecho($invoice['supplier_id'])?>" />

<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;">Supplier Name</td>
		<td>
			<input name="bill_name" type="text" style="width:50%;" value="<?php hecho($invoice['bill_name'])?>" />
			<input onclick="searchMember();" type="button" value="Search" />
<?php
if (is_array($allowed_sections) && in_array('CUSTOMERS', $allowed_sections)) {
?>
			<input onclick="addMember()" type="button" value="Add" />
			<input onclick="editMember()" type="button" value="Edit" name="member_edit"<?php hecho(($invoice['supplier_id'] > 0) ? '' : ' disabled="disabled"')?> />
<?php
}
?>
		</td>
	</tr>
	<tr>
		<td class="left_title">Primary eMail</td>
		<td><input name="bill_email" style="width:98%;" type="text" value="<?php hecho($invoice['bill_email'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Company Name</td>
		<td><input name="bill_company" style="width:98%;" type="text" value="<?php hecho($invoice['bill_company'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Address</td>
		<td><textarea name="bill_address" rows="<?= max(2, count(explode("\n", $invoice['bill_address']))) ?>" cols="80" onkeyup="textareaAutoSize(this, 1, 2)" style="width:98%;"><?php hecho($invoice['bill_address'])?></textarea></td>
	</tr>
	<tr>
		<td class="left_title">City</td>
		<td><input name="bill_city" style="width:98%;" type="text" value="<?php hecho($invoice['bill_city'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title">State</td>
		<td><input name="bill_state" style="width:98%;" type="text" value="<?php hecho($invoice['bill_state'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Country</td>
		<td><select name="bill_country" onchange="countryChange()">
			<option value="">Not Selected</option>
			<?= formatHTMLOptions(invoiceGetOptionList('bill_country'), $invoice['bill_country']) ?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title">VAT/Company #</td>
		<td><input style="width:98%;" type="text" name="bill_vat" value="<?php hecho($invoice['bill_vat'])?>" /></td>
	<tr>
		<td class="left_title">Post Code</td>
		<td><input name="bill_postcode" style="width:98%;" type="text" value="<?php hecho($invoice['bill_postcode'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Phone</td>
		<td><input name="bill_phone" style="width:98%;" type="text" value="<?php hecho($invoice['bill_phone'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title"></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class="left_title"><?= ($display_enquiry) ? 'Enquiry' : 'Invoice' ?> Currency</td>
		<td><select name="invoice_currency" onchange="currencyChange()">
			<option value="USD">U.S. Dollars - USD</option>
			<option value="">Not Selected</option>
			<?= formatHTMLOptions(invoiceGetOptionList('invoice_currency'), $invoice['invoice_currency']) ?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title">Time Zone</td>
		<td><select name="bill_timezone" onchange="timezoneChange()">
<?php
$time_zones = getTimeZones();
$zone_list  = array();
if (is_array($time_zones[$invoice['bill_country']])) {
    foreach ($time_zones[$invoice['bill_country']] as $zone) {
        $zone_list[$zone['id']] = $zone['zone'];
    }
}

?>
			<?= formatHTMLOptions($zone_list, $invoice['bill_timezone']) ?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title">Current Time</td>
		<td><span id="LiveClockIE"></span></td>
	</tr>
</table>
<br />
<br />

</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionMemberInfo_hideNote">
	Member Information can be viewed by Clicking "Member Information" above
	<br />
</div>

<table class="wide">
	<tr>
		<td class="left_title" style="width: 25%; padding: 20px 0;">Product Type</td>
		<td>
			<select name="producttype_id" onchange="onChangeCategory();">
				<option value="">Not Selected</option>
				<?= formatHTMLOptions(getBaseProductTypes(), $invoice['producttype_id']) ?>
			</select>
		</td>
	</tr>
</table>

<!-- /member_table.tpl -->

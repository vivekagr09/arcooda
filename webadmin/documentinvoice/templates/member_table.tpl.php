<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
$sinvoice_companies = documentinvoiceGetOptionList('site_id');
//if($sinvoice['site_id']!=2) unset($sinvoice_companies[2]);
$sinvoice_companies[100] = 'Jones Amusements';
$sinvoice_companies[101] = 'Deith Distributing';
$country_list            = documentinvoiceGetOptionList('bill_country');

$companies = array();
$res       = $DB->query("SELECT * FROM `highwaygroup`.`company_info`");
while ($row = $DB->fetch_assoc($res)) {
    $companies[] = $row;
}

$companies[]  = array(
	'company_id'               => 100,
	'company_code'             => 'JA',
	'company_name'             => 'Jones Amusements',
	'company_invoiceheader'    => "Jones Amusements\n29 Buttermere Drive,\nLakelands 2282, NSW, Australia\nPhone #: +61 421 929 068",
	'company_invoiceheader_au' => "Jones Amusements\n29 Buttermere Drive,\nLakelands 2282, NSW, Australia\nPhone #: 0421 929 068",
);
$companies[]  = array(
	'company_id'               => 101,
	'company_code'             => 'DD',
	'company_name'             => 'Deith Distributing',
	'company_invoiceheader'    => "Deith Distributing\n145 Manorhaven Blvd.\nPort Washington, NY 11050\nTel: 516-621-1234\nFax: 516-621-1238\nE-Mail: deith@deith.com",
	'company_invoiceheader_au' => "Deith Distributing\n145 Manorhaven Blvd.\nPort Washington, NY 11050\nTel: 516-621-1234\nFax: 516-621-1238\nE-Mail: deith@deith.com",
);
$js_companies = array();
foreach ($companies as $array) {
    $address = ('AU' == $sinvoice['ship_country']) ? $array['company_invoiceheader_au'] : $array['company_invoiceheader'];
    $address = explode("\n", $address);
    unset($address[0]);
    $address = implode("\n", $address);
    if (100 != $array['company_id'] && 101 != $array['company_id']) {
        $address = str_replace("\n", '', $address);
    }

    $js_companies[$array['company_id']] = $address;
}
?>
<script type="text/javascript">
//<![CDATA[
var companies = <?=json_encode($js_companies)?>;
function addressSelect(id)
{
	if (id == null) {
		return;
	}
	setValue('company_address', companies[id]);
}
//]]>
</script>
<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;">Company</td>
		<td><select name="site_id" onchange="javascript:toggleSection('sectionCompanyInfo',this.value=='0');">
			<option value="0">Custom Company</option>
			<?=formatHTMLOptions($sinvoice_companies, $sinvoice['site_id'])?>
		</select></td>
	</tr>
</table>
<br /><br />

<div id="sectionCompanyInfo"<?=(isset($sinvoice_companies[$sinvoice['site_id']])) ? ' style="display:none;"' : ''?>>
<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;">Company Name</td>
		<td><input style="width:98%;" type="text" name="company_name" value="<?=htmlspecialchars($sinvoice['company_name'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">Company Address</td>
		<td><textarea name="company_address"><?=htmlspecialchars($sinvoice['company_address'])?></textarea></td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%">Default Addresses :</td>
		<td><small><i><a href="" onclick="toggleSection('address_tr');return false;">Hide / Expand</a></i></small></td>
	</tr>
	<tr id="address_tr" style="display:none">
		<td class="left_title" style="width:25%">&nbsp;</td>
		<td><table style="width:100%;">
			<tr><th>Select</th><th>Address</th></tr>
<?php
$count = 0;
foreach ($companies as $array) {
?>
			<tr <?=(($count % 2) == 1) ? 'class="alt"' : ''?>><td><input onclick="addressSelect('<?=$array['company_id']?>')" type="radio" name="default_address"></td><td><?=nl2br($array['company_invoiceheader'])?></td></tr>
<?php
$count++;
}
?>
		</table></td>
	</tr>
</table>
<br /><br />
</div>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionMemberInfo\');">Member Information</a>', '<a href="javascript:void(address_copy(\'ship\',\'bill\'))">Copy From Shipping Details</a> <a href="javascript:void(address_copy(\'notify\',\'bill\'))">Copy From Notify Party</a>')?>
<div id="sectionMemberInfo">

<script type="text/javascript">
function editMember()
{
	var member_id=getValue('member_id');
	if(member_id<=0) return;
	var url='/webadmin/member/?action=edit&return_message=edit&member_id='+escape(member_id);
	var features='scrollbars, resizable, height=400, width=600';
	window.open(url,'member_popup',features);
}

function searchMember()
{
	var name=getValue('bill_name');
	var url='/webadmin/member/?action=do_search&return_message=select&active=y&query='+escape(name);
	var features='scrollbars, resizable, height=400, width=600';
	window.open(url,'member_popup',features);
}

function addMember()
{
	var url='/webadmin/member/?action=add&return_message=add';
	var features='scrollbars, resizable, height=400, width=600';
	window.open(url,'member_popup',features);
}

function setMemberData(data)
{
	if(data==null) return;

	switch(data['return_message'])
	{
	case 'select':
	case 'add':
	case 'edit':
		if(data['member_id']!=null)
		{
			if(document.delMemberAddresses!=null) delMemberAddresses();
			if(document.delMemberEmails!=null) delMemberEmails();

			setValue('member_id',data['member_id']);

			var bill_name='';
			if(data['first_name']!=null) bill_name=data['first_name'];
			if(data['last_name']!=null) bill_name+=((bill_name=='')?'':' ')+data['last_name'];
			if(bill_name!='') setValue('bill_name',bill_name);

			var edit_button=getElement("member_edit");
			if(edit_button) edit_button.disabled='';

			setValue("bill_email",data['email_address']);
			if(data['company_name']!=null) setValue('bill_company',data['company_name']);
			setValue("bill_address",data['address']);
			setValue("bill_city",data['city']);
			setValue("bill_state",data['state']);
			setValue("bill_country",data['country']);
			setValue("bill_postcode",data['post_code']);
			setValue("bill_phone",data['phone_no']);

			if(window.addFreightCourier!=null)
			{
				clearFreightCouriers();
				if(data['couriers']!=null)
				{
					for(var courier_id in data['couriers'])
					{
						var courier=data['couriers'][courier_id];
						var new_name='Own Account ('+courier['account_num']+')';
						courier['id']=data['couriers'][courier_id]['courier_id'];
						courier['name']=courier['courier_name'];
						courier['del_time']='Own Account ('+courier['account_num']+')';
						courier['track']='N/A';
						courier['insurance']='N/A';
						courier['tail_gate']='n';
						courier['cost_price']='0.00';
						courier['retail_price']='0.00';
						courier['sell_price']='0.00';
						courier['cost_priceau']='0.00';
						courier['sell_priceau']='0.00';
						courier['supplier']=courier['courier_id'];
						courier['acc_num']=courier['account_num'];
						courier['supplier_name']=courier['account_num'];
						courier['currency']=getValue('invoice_currency');
						courier['notes']='Personal Account: '+courier['account_num'];
						courier['member_courier']='y';
						addFreightCourier(courier);
					}
				}
			}

			if(window.addContactEmail!=null)
			{
				clearContactEmails();
				if(data['contacts']!=null)
					for(var contact_id in data['contacts'])
						addContactEmail(data['contacts'][contact_id]);
				addContactEmail(data);
			}
		}
	}
}

function currencyChange()
{
	var newCurrency=getValue('invoice_currency');
	setAllValues('product_currency',newCurrency);
}

function countryChange(newCountry)
{
	var elemLocal='';
	var elemInt='';
	var value='';
	var ship_type=getElements("ship_type");
	for(i=0;i<ship_type.length;i++)
	{
		if(ship_type[i].value=='local') elemLocal=ship_type[i];
		else if(ship_type[i].value=='international') elemInt=ship_type[i];
		if(ship_type[i].checked) value=ship_type[i].value;
	}
	if(value=='international' || value=='local')
	{
		if(newCountry!='AU') elemInt.checked=true;
		else elemLocal.checked=true;
		if(window.courierFreightSelected != null) courierFreightSelected(value);
	}
}

function copyDetails(elem)
{
	var array=Array('name', 'company', 'address', 'city', 'state', 'postcode', 'country', 'phone', 'email');
	for(i=0;i<array.length;i++)
		setValue('notify_'+array[i],getValue(elem+'_'+array[i]));
}

function address_copy(src,dst)
{
	//var the_sects=['address','city','company','country','email','name','phone','postcode','state','type'];
	var the_sects=['address', 'array', 'assoc', 'au', 'button', 'city', 'code', 'companies', 'company', 'copy', 'country', 'courier', 'currency', 'edit', 'email', 'gate', 'hideNote', 'id', 'info', 'inquiry', 'invoiceheader', 'list', 'message', 'name', 'no', 'num', 'phone', 'popup', 'postcode', 'price', 'priceau', 'replace', 'search', 'sections', 'sects', 'state', 'table', 'time', 'title', 'tr', 'type'];

	for(var i=0;i<the_sects.length;i++)
	{
		var value=getValue(src+'_'+the_sects[i]);
		if(value!==null) setValue(dst+'_'+the_sects[i],value);
	}
}
</script>

<input name="invoice_id" type="hidden" value="<?=$dinvoice_id?>" />
<input name="staff_id" type="hidden" value="<?=$staff_id?>" />
<input name="member_id" type="hidden" value="<?=$sinvoice['member_id']?>" />

<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;">Member Name</td>
		<td>
			<input name="bill_name" type="text" style="width:50%;" value="<?=$sinvoice['bill_name']?>" />
			<input onclick="javascript:searchMember();" type="button" value="Search" />
<?php
if (has_permission('MEMBERS', $allowed_sections)) {
?>
			<input onclick="javascript:addMember();" type="button" value="Add" />
			<input onclick="javascript:editMember();" type="button" value="Edit" name="member_edit"<?=($sinvoice['member_id'] > 0) ? '' : ' disabled="disabled"'?> />
<?php
}
?>
		</td>
	</tr>
	<tr>
		<td class="left_title">Primary eMail</td>
		<td><input name="bill_email" style="width:98%;" type="text" value="<?=htmlspecialchars($sinvoice['bill_email'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Company Name</td>
		<td><input name="bill_company" style="width:98%;" type="text" value="<?=$sinvoice['bill_company']?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Address</td>
		<td><textarea name="bill_address" rows="<?=max(2, count(explode("\n", $sinvoice['bill_address'])))?>" onkeyup="javascript:textareaAutoSize(this,1,2);" style="width:98%;"><?=htmlspecialchars($sinvoice['bill_address'])?></textarea></td>
	</tr>
	<tr>
		<td class="left_title">City</td>
		<td><input name="bill_city" style="width:98%;" type="text" value="<?=htmlspecialchars($sinvoice['bill_city'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title">State</td>
		<td><input name="bill_state" style="width:98%;" type="text" value="<?=htmlspecialchars($sinvoice['bill_state'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Country</td>
		<td>
			<select name="bill_country"><option value="">Not Selected</option><?=formatHTMLOptions($country_list, $sinvoice['bill_country'])?></select>
		</td>
	</tr>
	<tr>
		<td class="left_title">Post Code</td>
		<td><input name="bill_postcode" style="width:98%;" type="text" value="<?=htmlspecialchars($sinvoice['bill_postcode'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Phone</td>
		<td><input name="bill_phone" style="width:98%;" type="text" value="<?=htmlspecialchars($sinvoice['bill_phone'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title"></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class="left_title"><?=($display_inquiry) ? 'Inquiry' : 'Invoice'?> Currency</td>
		<td><select name="invoice_currency" onchange="javascript:currencyChange();">
			<option value="">Not Selected</option>
			<?=formatHTMLOptions(documentinvoiceGetOptionList('invoice_currency'), $sinvoice['invoice_currency'])?>
		</select></td>
	</tr>
</table>
<br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionMemberInfo_hideNote">
Member Information can be viewed by Clicking "Member Information" above
<br />
</div>
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionShipInfo\');">Shipping Information</a>', '<a href="javascript:void(address_copy(\'bill\',\'ship\'))">Copy From Member Details</a> <a href="javascript:void(address_copy(\'notify\',\'ship\'))">Copy From Notify Party</a>')?>
<div id="sectionShipInfo">

<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;">Contact Name</td>
		<td><input name="ship_name" type="text" style="width:98%;" value="<?=$sinvoice['ship_name']?>" /></td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">Company Name</td>
		<td><input name="ship_company" type="text" style="width:98%;" value="<?=$sinvoice['ship_company']?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Shipping Address</td>
		<td><textarea name="ship_address" rows="<?=max(2, count(explode("\n", $sinvoice['ship_address'])))?>" onkeyup="javascript:textareaAutoSize(this,1,2);" style="width:98%;"><?=htmlspecialchars($sinvoice['ship_address'])?></textarea></td>
	</tr>
	<tr><td class="left_title">City</td><td><input name="ship_city" style="width:98%;" type="text" value="<?=htmlspecialchars($sinvoice['ship_city'])?>" /></td></tr>
	<tr><td class="left_title">State</td><td><input name="ship_state" style="width:98%;" type="text" value="<?=htmlspecialchars($sinvoice['ship_state'])?>" /></td></tr>
	<tr>
		<td class="left_title">Country</td>
		<td>
			<select name="ship_country" onchange="countryChange(this.value);"><option value="">Not Selected</option><?=formatHTMLOptions($country_list, $sinvoice['ship_country'])?></select>
		</td>
	</tr>
	<tr>
		<td class="left_title">Post Code</td><td><input name="ship_postcode" style="width:98%;" type="text" value="<?=htmlspecialchars($sinvoice['ship_postcode'])?>" /></td>
	</tr>
	<tr><td class="left_title">Phone</td><td><input name="ship_phone" style="width:98%;" type="text" value="<?=htmlspecialchars($sinvoice['ship_phone'])?>" /></td></tr>
	<tr><td class="left_title">Email</td><td><input name="ship_email" style="width:98%;" type="text" value="<?=htmlspecialchars($sinvoice['ship_email'])?>" /></td></tr>
	<tr><td class="left_title"></td><td>&nbsp;</td></tr>
</table>
<br /><br />
</div>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionShipInfo\');">Notify Party</a>', '<a href="javascript:void(address_copy(\'bill\',\'notify\'))">Copy From Member Details</a> <a href="javascript:void(address_copy(\'ship\',\'notify\'))">Copy From Shipping Details</a>')?>
<div id="sectionShipInfo">

<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;">Notify Name</td>
		<td><input name="notify_name" type="text" style="width:98%;" value="<?=$sinvoice['notify_name']?>" /></td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">Notify Company Name</td>
		<td><input name="notify_company" type="text" style="width:98%;" value="<?=$sinvoice['notify_company']?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Notify Address</td>
		<td><textarea name="notify_address" rows="<?=max(2, count(explode("\n", $sinvoice['notify_address'])))?>" onkeyup="javascript:textareaAutoSize(this,1,2);" style="width:98%;"><?=htmlspecialchars($sinvoice['notify_address'])?></textarea></td>
	</tr>
	<tr><td class="left_title">City</td><td><input name="notify_city" style="width:98%;" type="text" value="<?=htmlspecialchars($sinvoice['notify_city'])?>" /></td></tr>
	<tr><td class="left_title">State</td><td><input name="notify_state" style="width:98%;" type="text" value="<?=htmlspecialchars($sinvoice['notify_state'])?>" /></td></tr>
	<tr>
		<td class="left_title">Country</td>
		<td>
			<select name="notify_country"><option value="">Not Selected</option><?=formatHTMLOptions($country_list, $sinvoice['notify_country'])?></select>
		</td>
	</tr>
	<tr>
		<td class="left_title">Post Code</td><td><input name="notify_postcode" style="width:98%;" type="text" value="<?=htmlspecialchars($sinvoice['notify_postcode'])?>" /></td>
	</tr>
	<tr><td class="left_title">Phone</td><td><input name="notify_phone" style="width:98%;" type="text" value="<?=htmlspecialchars($sinvoice['notify_phone'])?>" /></td></tr>
	<tr><td class="left_title">Email</td><td><input name="notify_email" style="width:98%;" type="text" value="<?=htmlspecialchars($sinvoice['notify_email'])?>" /></td></tr>
</table>
<br /><br />
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

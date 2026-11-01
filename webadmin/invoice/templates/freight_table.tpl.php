<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
moduleLoad('freight');

function freightcourier_row($ccount = '[COURIER_COUNT]', $courier = array(), $class = '', $member = false)
{
    global $invoice;
    $name   = ($member) ? 'courier' : 'freightcourier';
    $select = ($member) ? 'true' : 'false';
    ob_start();
?>
<tr id="freightcourier_row[<?=$ccount?>]" <?=('' != $class) ? 'class="' . $class . '"' : ''?>>
	<td><input type="hidden" name="<?=$name?>_id[<?=$ccount?>]" value="<?=htmlspecialchars($courier['id'])?>" /><input type="hidden" name="freightcourier_name[<?=$ccount?>]" value="<?=htmlspecialchars($courier['name'])?>" /><input type="hidden" name="freightcourier_notes[<?=$ccount?>]" value="<?=htmlspecialchars($courier['del_time'])?>" /><input type="hidden" name="freightcourier_track[<?=$ccount?>]" value="<?=htmlspecialchars($courier['track'])?>" /><input type="hidden" name="freightcourier_insurance[<?=$ccount?>]" value="<?=htmlspecialchars($courier['insurance'])?>" /><input type="hidden" name="freightcourier_tail_gate[<?=$ccount?>]" value="<?=htmlspecialchars($courier['tail_gate'])?>" /><input type="hidden" name="freightcourier_cost[<?=$ccount?>]" value="<?=htmlspecialchars($courier['cost_price'])?>" /><input type="hidden" name="freightcourier_offer[<?=$ccount?>]" value="<?=htmlspecialchars($courier['sell_price'])?>" /><input type="hidden" name="freightcourier_online[<?=$ccount?>]" value="<?=htmlspecialchars($courier['sell_price'])?>" /><input type="hidden" name="freightcourier_costau[<?=$ccount?>]" value="<?=htmlspecialchars($courier['cost_priceau'])?>" /><input type="hidden" name="freightcourier_offerau[<?=$ccount?>]" value="<?=htmlspecialchars($courier['sell_priceau'])?>" /><input type="hidden" name="freightcourier_supplier_id[<?=$ccount?>]" value="<?=htmlspecialchars($courier['supplier'])?>" /><input type="hidden" name="freightcourier_supplier_name[<?=$ccount?>]" value="<?=htmlspecialchars($courier['supplier_name'])?>" /><input type="checkbox" name="freightcourier_flag[<?=$ccount?>]"<?=('y' == $courier['flag']) ? ' checked="checked"' : ''?> onclick="javascript:selectFreightCourier(<?=$ccount?>, <?=$select?>);" /></td>
	<td style="text-align:center;"><?=htmlspecialchars($courier['name'])?></td>
	<td style="text-align:center;"><?=htmlspecialchars($courier['del_time'])?></td>
	<td style="text-align:center;"><?=('y' == $courier['track']) ? 'Yes' : (('n' == $courier['track']) ? 'No' : htmlspecialchars($courier['track']))?></td>
	<td style="text-align:center;"><?=('y' == $courier['insurance']) ? 'Yes' : (('n' == $courier['insurance']) ? 'No' : htmlspecialchars($courier['insurance']))?></td>
	<td style="text-align:center;"><?=('y' == $courier['tail_gate']) ? 'Yes' : (('n' == $courier['tail_gate']) ? 'No' : htmlspecialchars($courier['tail_gate']))?></td>
	<td style="text-align:right;">$<?=htmlspecialchars($courier['cost_price'])?></td>
	<!--<td style="text-align:right;">$<?=htmlspecialchars($courier['retail_price'])?></td>-->
	<td style="text-align:right;background-color:#FFEAE6">$<?=htmlspecialchars($courier['sell_price'])?></td>
	<?php if ('AUD' != $invoice['invoice_currency']) {?>
	<td style="text-align:right;">$<?=htmlspecialchars($courier['cost_priceau'])?></td>
	<td style="text-align:right;">$<?=htmlspecialchars($courier['sell_priceau'])?></td>
	<?php }?>
</tr>
<?php
return ob_get_clean();
}

function address_row($acount = '[ADDRESS_COUNT]', $address = array())
{
    ob_start();
?>
<tr id="shipto_row[<?=$acount?>]">
	<td class="left_title">Other Shipping Address</td>
	<td><input type="checkbox" name="shipto_flag[<?=$acount?>]" onclick="javascript:selectShippingAddress(<?=$acount?>);" /></td>
	<td><table style="width:100%;"><tr><td class="left_title" style="width:25%;">Name</td><td style="width:75%;"><input type="text" name="shipto_name[<?=$acount?>]" value="<?=htmlspecialchars($address['contact'])?>" style="width:98%;" /></td></tr><tr><td class="left_title">Company</td><td><input type="text" name="shipto_company[<?=$acount?>]" value="<?=htmlspecialchars($address['company_name'])?>" style="width:98%;" /></td></tr><tr><td class="left_title">Address</td><td><textarea name="shipto_address[<?=$acount?>]" rows="2" cols="40" onkeyup="javascript:textareaAutoSize(this,1,2);"><?=htmlspecialchars($address['address'])?></textarea></td></tr><tr><td class="left_title">City</td><td><input type="text" name="shipto_city[<?=$acount?>]" value="<?=htmlspecialchars($address['city'])?>" style="width:98%;" /></td></tr><tr><td class="left_title">State</td><td><input type="text" name="shipto_state[<?=$acount?>]" value="<?=htmlspecialchars($address['state'])?>" style="width:98%;" /></td></tr><tr><td class="left_title">Postcode</td><td><input type="text" name="shipto_postcode[<?=$acount?>]" value="<?=htmlspecialchars($address['post_code'])?>" style="width:98%;" /></td></tr><tr><td class="left_title">Country</td><td><select name="shipto_country[<?=$acount?>]"><?=formatHTMLOptions(getCountryList(), $address['country'])?></select></td></tr><tr><td class="left_title">Phone</td><td><input type="text" name="shipto_phone[<?=$acount?>]" value="<?=htmlspecialchars($address['phone_no'])?>" style="width:98%;" /></td></tr></table></td>
</tr>
<?php
return ob_get_clean();
}

function searchCouriers($country, $code, $state, $destination, $weight, $goods_value, $currency, $site_id)
{
    $state = str_replace('.', '', $state);

    $courier_list = array();
    if ('AU' == $country && function_exists('freightDomesticCalculate') && function_exists('freightCalculatePrices')) {
        $code = substr('0000' . intval($code), -4);
        if (0 == $code) {
            $code = '----';
        }

        $search_order = array($destination . ' , ' . $code . ' , ' . $state, $destination . ' , , ' . $state, ' , ' . $code . ' , ' . $state, $destination . ' , ' . $code . ' , ', ' , ' . $code . ' , ', $destination . ' , , ');
        foreach ($search_order as $search_for) {
            if (count($results = postcodeSearch($search_for))) {
                break;
            }
        }

        if (count($results)) {
            $result      = reset($results);
            $destination = $result['Locality'];
            $state       = $result['State'];
            $code        = $result['Pcode'];
        }
        $courier_list = freightDomesticCalculate($destination, $state, $code, $weight);
        $courier_list = freightCalculatePrices($courier_list, $weight, $goods_value);
        foreach ($courier_list as $key => $value) {
            if ('' == $value['currency']) {
                $courier_list[$key]['currency'] = 'AUD';
            }

            $courier_list[$key]['Locality']      = $destination;
            $courier_list[$key]['State']         = $state;
            $courier_list[$key]['Pcode']         = $code;
            $courier_list[$key]['Country']       = 'AU';
            $courier_list[$key]['supplier_name'] = getFreightForwarderName($value['supplier']);
        }
    }

    if (count($courier_list) == 0 && function_existS('freightInternationalCalculate') && function_exists('freightCalculateIntPrices')) {
        $courier_list = freightInternationalCalculate($country, 'AU');
        $courier_list = freightCalculateIntPrices($courier_list, $weight, $currency, $goods_value);
        foreach ($courier_list as $key => $value) {
            if ('' == $value['currency']) {
                $courier_list[$key]['currency'] = $currency;
            }

            $courier_list[$key]['Country']       = $country;
            $courier_list[$key]['supplier_name'] = getFreightForwarderName($value['supplier']);
        }
    }
    // need to add the insurance & tail gate stuff to the couriers now..
    $add_list = array();
    foreach ($courier_list as $key => $value) {
        //print_array($value);
        if ('y' == $value['insurance'] && $value['insurance_cost'] > 0) {
            $value['sell_price'] += $value['insurance_cost'];
            $value['cost_price'] += $value['insurance_cost'];
            $value['retail_price'] += $value['insurance_cost'];
            $value['insurance'] = 'y';
            $add_list[]         = $value;
        }
        if (0.0 != $value['insurance_cost']) {
            $courier_list[$key]['insurance'] = 'n';
            if ($value['insurance_cost'] < 1.0) {
                unset($courier_list[$key]);
            }
        }
    }
    foreach ($add_list as $value) {
        $courier_list[] = $value;
    }

    $add_list = array();
    foreach ($courier_list as $key => $value) {
        if (0.0 != $value['tail_gate_charge'] && $weight > 25) {
            $value['sell_price'] += $value['tail_gate_charge'];
            $value['cost_price'] += $value['tail_gate_charge'];
            $value['retail_price'] += $value['tail_gate_charge'];
            $value['tail_gate'] = 'y';
            $value['notes']     = trim($value['notes'] . "\n" . 'Tail Gate Included');
            $add_list[]         = $value;
        }
        if (0.0 != $value['tail_gate_charge']) {
            $courier_list[$key]['tail_gate'] = 'n';
            if ($value['tail_gate_charge'] < 1.0) {
                unset($courier_list[$key]);
            }
        }
    }
    foreach ($add_list as $value) {
        $courier_list[] = $value;
    }

	if (function_exists('freightSortArray')) {
	    $courier_list = freightSortArray($courier_list);
	}
    return $courier_list;
}

foreach ($member_shippingaddresses as $key => $member_shippingaddress) {
    if ('' == $member_shippingaddress['contact']) {
        $member_shippingaddresses[$key]['contact'] = $invoice['bill_name'];
    }
    if ('' == $member_shippingaddress['email']) {
        $member_shippingaddresses[$key]['email'] = $invoice['bill_email'];
    }
    if ('' == $member_shippingaddress['company_name']) {
        $member_shippingaddresses[$key]['company_name'] = $invoice['bill_company'];
    }
}

$replace1 = array(
    'id'            => '[COURIER_ID]',
    'name'          => '[COURIER_NAME]',
    'del_time'      => '[COURIER_DEL_TIME]',
    'track'         => '[COURIER_TRACKING]',
    'insurance'     => '[COURIER_INSURANCE]',
    'tail_gate'     => '[COURIER_TAIL_GATE]',
    'cost_price'    => '[COURIER_COST]',
    'retail_price'  => '[COURIER_ONLINE]',
    'sell_price'    => '[COURIER_OFFER]',
    'cost_priceau'  => '[COURIER_COSTAU]',
    'sell_priceau'  => '[COURIER_OFFERAU]',
    'supplier'      => '[COURIER_SUPPLIER_ID]',
    'supplier_name' => '[COURIER_SUPPLIER_NAME]',
);
$replace2 = array(
    "[COURIER_COUNT]"         => '"+ccount+"',
    "[COURIER_ID]"            => '"+data["id"]+"',
    "[COURIER_NAME]"          => '"+data["name"]+"',
    "[COURIER_DEL_TIME]"      => '"+data["del_time"]+"',
    "[COURIER_TRACKING]"      => '"+tracking+"',
    "[COURIER_INSURANCE]"     => '"+insurance+"',
    "[COURIER_TAIL_GATE]"     => '"+data["tail_gate"]+"',
    "[COURIER_COST]"          => '"+cost_price+"',
    "[COURIER_ONLINE]"        => '"+online_price+"',
    "[COURIER_OFFER]"         => '"+offer_price+"',
    "[COURIER_COSTAU]"        => '"+Number(data["cost_price"]).toFixed(2)+"',
    "[COURIER_OFFERAU]"       => '"+Number(data["sell_price"]).toFixed(2)+"',
    "[COURIER_SUPPLIER_ID]"   => '"+data["supplier"]+"',
    "[COURIER_SUPPLIER_NAME]" => '"+data["supplier_name"]+"',
);
?>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionShippingInfo\');">Shipping Information</a>')?>
<div id="sectionShippingInfo">

<?php
$count = count($invoice_courier_quotes) + count($member_couriers);
?>
<script type="text/javascript">
//<![CDATA[
var ship_address        = <?=json_encode($member_shippingaddresses)?>;
var notify_address      = <?=json_encode($member_notifyparties)?>;
var firstAddress        = 1;
var lastAddress         = <?=max(0, count($member_shippingaddresses))?>;
var firstFreightCourier = 1;
var lastFreightCourier  = <?=max(0, $count)?>;
function addMissingWeight(product_id,product_name,product_url)
{
	toggleSection('missing_weights_row',true);
	var new_line='';
	if(product_url!=null && product_url!='')
		new_line=String(product_id)+' - <a target="_blank" href="'+product_url+'">'+str_replace('<','&lt',product_name)+'</a>';
	else
		new_line=String(product_id)+' - '+str_replace('<','&lt',product_name);
	setValue('missing_weights_span',getValue('missing_weights_span')+new_line+"<br />");
}
function setCourierData(data)
{
	clearFreightCouriers();
	var Locality='.';
	var State='.';
	var Pcode='.';
	var Country='.';
	for(var key in data)
	{
		addFreightCourier(data[key]);
		if(data[key]['Locality']!=null) Locality=data[key]['Locality'];
		if(data[key]['State']!=null) State=data[key]['State'];
		if(data[key]['Pcode']!=null) Pcode=data[key]['Pcode'];
		if(data[key]['Country']!=null) Country=data[key]['Country'];
	}
	if(Locality=='.' && State=='.' && Pcode=='.' && Country=='.')
	{
		//No valid quotes, search for possible reasons and display
		var reasons='';
		if(getValue('shipto_country[0]')=='') reasons+="No Shipping Country selected\n";
		if(getValue('shipto_postcode[0]')=='') reasons+="No Shipping Postcode selected\n";
		if(getValue('shipto_state[0]')=='') reasons+="No Shipping State selected\n";
		if(getValue('shipto_city[0]')=='') reasons+="No Shipping City selected\n";
		if(getValue('ship_weight')<=0) reasons="No Shipping Weight selected\n";
		if(getValue('products_offerau')<=0) reasons+="Products have no offer price\n";
		if(reasons!='') alert("Unable to find quotes, possible reasons:\n"+reasons);
	}
	setValue('freightcourier_locality',Locality);
	setValue('freightcourier_state',State);
	setValue('freightcourier_postcode',Pcode);
	setValue('freightcourier_country',Country);
}
function shippingShowCountryNotes(note_id)
{
	var country_id=getShippingCountry();
	if(country_id==null) return;
	var url='/webadmin/world/show_notes.php?var='+String(note_id)+'&con='+escape(country_id);
	var features='scrollbars, resizable, height=400, width=600';
	window.open(url,'shipnote_popup',features);
}
function shippingEditCountryNotes()
{
	var country_id=getShippingCountry();
	if(country_id==null || country_id=='') return;
	var url='/webadmin/world/?return_message=edit&code='+escape(country_id);
	var features='scrollbars, resizable, height=400, width=600';
	window.open(url,'shipnote_popup',features);
}
function setShippingCountryData(data)
{
	return;
}
function untickAllCouriers()
{
	var table1=getElement('freight_couriers');
	var table2=getElement('members_couriers');
	var length=table1.rows.length+table2.rows.length;
	for(i=0;i<=lastFreightCourier;i++)
	{
		var flag=getElement('freightcourier_flag['+i+']');
		if(flag && flag!=null) flag.checked=false;
	}
}
function selectFreightCourier(row,member)
{
	untickAllCouriers();
	var flag=getElement('freightcourier_flag['+row+']');
	flag.checked=true;

	var section = 'freightcourier';
	if(member) section='courier';
	var product_id=getValue(section+'_id['+row+']');
	var courier_id=product_id;

	setValue('ship_id',courier_id);
	setValue('ship_offerau',getValue('freightcourier_offerau['+row+']'));
	setValue('ship_costau',getValue('freightcourier_costau['+row+']'));

	var freightrow=getElement('product_ship_rowa');
	if(freightrow!=null) freightrow.style.display='';
	if(product_id.length<2) product_id='0'+product_id;
	var ins=getValue('freightcourier_insurance['+row+']');
	var ship_id_str='F'+product_id+((ins=='y' || ins=='Yes')?'I':'N');
	setValue('ship_product_id',ship_id_str);
	setValue('ship_qty','1');
	setValue('ship_cost',getValue('freightcourier_cost['+row+']'));
	setValue('ship_online',getValue('freightcourier_online['+row+']'));
	setValue('ship_offer',getValue('freightcourier_offer['+row+']'));
	setValue('ship_offerau',getValue('freightcourier_offerau['+row+']'));
	getElement('product_ship_rowb').style.display='';
	var product_notes='';
	if(ins == 'Yes' || ins=='y') product_notes += "Insurance Included.\n";

	var freightrow=getElement('supplier_ship_rowa');
	if(freightrow!=null) freightrow.style.display='';
	var freightrow=getElement('supplier_ship_rowb');
	if(freightrow!=null) freightrow.style.display='';
	setValue('supplier_ship_product_id',ship_id_str);
	var tempName=getValue('freightcourier_name['+row+']');
	//var courier_name=tempName+((courier_id>100) ? ' (Personal Account)' : '');
	var courier_name=tempName+(member ? ' (Personal Account)' : '');
	setValue('ship_product_name',courier_name);
	setValue('supplier_ship_product_name',tempName);
	setValue('ship_supplier_id',getValue('freightcourier_supplier_id['+row+']'));
	if(member)
	{
		//courier_id>=100
		product_notes += "Customer Account #: "+getValue('freightcourier_supplier_name['+row+']')+"\n";
		getElement('account_num_tr').style.display='';
		setValue('ship_account_num',getValue('freightcourier_supplier_name['+row+']'));
		var supplier_name=explode(' ',getValue('freightcourier_name['+row+']'));
		setValue('ship_supplier_name',supplier_name[0]);
	}
	else
	{
		getElement('account_num_tr').style.display='none';
		setValue('ship_account_num','');
		setValue('ship_supplier_name',getValue('freightcourier_supplier_name['+row+']'));
	}
	if(product_notes != '') setValue('ship_product_notes', product_notes);
	var name=getValue('freightcourier_name['+row+']')+((member) ? ' (Personal Account)': ''); //courier_id>=100
	setValue('ship_courier_name',name);
	setValue('ship_courier_price','$'+getValue('freightcourier_offerau['+row+']')+' AUD');
	var transit=(member) ? 'N/A' : getValue('freightcourier_notes['+row+']'); //courier_id>=100
	setValue('ship_courier_transit',transit);
	var insStr=(ins=='y' || ins=='Yes') ? 'Included' : 'No';
	setValue('ship_courier_insurance',insStr);
	setValue('supplier_ship_qty','1');
	setValue('supplier_ship_cost',getValue('freightcourier_cost['+row+']'));
	setValue('ship_costau',getValue('freightcourier_costau['+row+']'));
	setValue('ship_insurance',((ins=='Yes' || ins=='y') ? 'y' : 'n'));

	if(window.shipChange!=null) window.shipChange();
<?php
if ('add' != $action) {
?>
	for(key in prefer)
	{
		var obj=getElement('freightquote['+key+'][prefered]');
		obj.checked=false;
		obj.parentNode.style.backgroundColor='#FFFFFF';
		getElement('freightquote['+key+'][header]').style.backgroundColor="#FFEECC";
	}
	for(i=countStart;i<freightquote_count;i++)
	{
		var obj=getElement('freightquote['+i+'][prefered]');
		obj.checked=false;
		obj.parentNode.style.backgroundColor='#FFFFFF';
	}
<?php
}
?>
}
function selectShippingAddress(row)
{
	var flag=getElement('shipto_flag[0]');
	if(flag!=null) flag.checked=(row==0);
	for(var i=firstAddress;i<=lastAddress;i++)
	{
		flag=getElement('shipto_flag['+i+']');
		if(flag!=null) flag.checked=(row==i);
	}
	var country_id=getValue('shipto_country['+row+']');

	setValue('lblShipCountry',country_id);
	var ship_notes=countryShipNotes(country_id);

	for(var field in ship_notes)
	{
		var theButton=getElement(field+'Button');
		if(theButton!=null)
		{
			if(ship_notes[field]>0)
			{
				theButton.disabled=false;
				theButton.style.border="1px solid red";
			}
			else
			{
				theButton.disabled=true;
				theButton.style.borderStyle="none";
			}
		}
	}
}
function getShippingCountry()
{
	var flag=getElement('shipto_flag[0]');
	if(flag!=null) if(flag.checked==true) return getValue('shipto_country[0]');
	for(var i=firstAddress;i<=lastAddress;i++)
	{
		flag=getElement('shipto_flag['+i+']');
		if(flag!=null) if(flag.checked==true) return getValue('shipto_country['+i+']');
	}
}
function clearFreightCouriers()
{
	var table=getElement('freight_couriers');
	var length=table.rows.length;
	for(i=0;i<length;i++)
		table.deleteRow(0);
	firstFreightCourier=lastFreightCourier;
}
function clearShippingAddresses()
{
	for(var i=firstAddress;i<=lastAddress;i++)
	{
		var theCheck=getElement('shipto_flag['+i+']');
		if(theCheck!=null) theCheck.checked=false;
		var theRow=getElement("shipto_row["+i+"]");
		if(theRow!=null) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	}
	firstAddress=lastAddress;

	setValue('shipto_country[0]','');
	setValue('shipto_address[0]','');
	var check=getElement('shipto_flag[0]');
	selectShippingAddress(0);
}
function setShippingAddress(data)
{
	setValue('shipto_name[0]',data['contact']);
	setValue('shipto_company[0]',data['company_name']);
	setValue('shipto_address[0]',data['address']);
	setValue('shipto_city[0]',data['city']);
	setValue('shipto_state[0]',data['state']);
	setValue('shipto_postcode[0]',data['post_code']);
	setValue('shipto_country[0]',data['country']);
	setValue('shipto_phone[0]',data['phone_no']);
	setValue('shipto_email[0]',data['email']);
	selectShippingAddress(0);
}
function setNotifyAddress(data)
{
	setValue('notify_name[0]',data['contact']);
	setValue('notify_company[0]',data['company_name']);
	setValue('notify_address[0]',data['address']);
	setValue('notify_city[0]',data['city']);
	setValue('notify_state[0]',data['state']);
	setValue('notify_postcode[0]',data['post_code']);
	setValue('notify_country[0]',data['country']);
	setValue('notify_phone[0]',data['phone_no']);
	setValue('notify_email[0]',data['email']);
	//selectShippingAddress(0);
}
function addFreightCourier(data)
{
	lastFreightCourier++;
	var ccount=String(lastFreightCourier);
	var cour_table=(data['member_courier']=='y')?getElement('members_couriers'):getElement("freight_couriers");
	if(cour_table==null) return;

	var newRow1=addTableRow(cour_table,'freightcourier_row['+ccount+']');
	if(lastFreightCourier&1) newRow1.className='alt';

	var tracking='No';
	if(data['track']=='y') tracking='Yes';
	var insurance='No';
	if(data['insurance']=='y') insurance='Yes';
	if(data['tail_gate']=='y') data['tail_gate']='Yes';
	else if(data['tail_gate']=='n') data['tail_gate']='No';

	var cost_price=convertCurrency(data['cost_price'],'AUD',getValue('invoice_currency'));
	var offer_price=Math.ceil(convertCurrency(data['sell_price'],'AUD',getValue('invoice_currency'))).toFixed(2);
	var online_price=convertCurrency(data['retail_price'],'AUD',getValue('invoice_currency'));

	if(data['member_courier']=='y')
		addMemberCourier(data,tracking,insurance,cost_price,online_price,offer_price,ccount,newRow1);
	else
		addCourier(data,tracking,insurance,cost_price,online_price,offer_price,ccount,newRow1);
}
function addMemberCourier(data,tracking,insurance,cost_price,online_price,offer_price,ccount,newRow1)
{
<?php
$count = 0;
foreach (explode("\n", freightcourier_row('[COURIER_COUNT]', $replace1, '', true)) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "	addTableCell(newRow" . $count . "," . str_replace(array_keys($replace2), array_values($replace2), formatJSON(substr($row, 4, -5))) . ");\n";
    } elseif (substr($row, 0, 31) == '<td style="text-align:center;">') {
        echo "	addTableCell(newRow" . $count . "," . str_replace(array_keys($replace2), array_values($replace2), formatJSON(substr($row, 31, -5))) . ").style.textAlign='center';\n";
    } elseif (substr($row, 0, 30) == '<td style="text-align:right;">') {
        echo "	addTableCell(newRow" . $count . "," . str_replace(array_keys($replace2), array_values($replace2), formatJSON(substr($row, 30, -5))) . ").style.textAlign='right';\n";
    } elseif (substr($row, 0, 54) == '<td style="text-align:right;background-color:#FFEAE6">') {
        echo "var newCell = addTableCell(newRow" . $count . "," . str_replace(array_keys($replace2), array_values($replace2), formatJSON(substr($row, 54, -5))) . ");\n";
        echo "newCell.style.textAlign='right';\nnewCell.style.backgroundColor='#FFEAE6';\n";
    }
}
?>
}
function addCourier(data,tracking,insurance,cost_price,online_price,offer_price,ccount,newRow1)
{
<?php
$count = 0;
foreach (explode("\n", freightcourier_row('[COURIER_COUNT]', $replace1)) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "	addTableCell(newRow" . $count . "," . str_replace(array_keys($replace2), array_values($replace2), formatJSON(substr($row, 4, -5))) . ");\n";
    } elseif (substr($row, 0, 31) == '<td style="text-align:center;">') {
        echo "	addTableCell(newRow" . $count . "," . str_replace(array_keys($replace2), array_values($replace2), formatJSON(substr($row, 31, -5))) . ").style.textAlign='center';\n";
    } elseif (substr($row, 0, 30) == '<td style="text-align:right;">') {
        echo "	addTableCell(newRow" . $count . "," . str_replace(array_keys($replace2), array_values($replace2), formatJSON(substr($row, 30, -5))) . ").style.textAlign='right';\n";
    } elseif (substr($row, 0, 54) == '<td style="text-align:right;background-color:#FFEAE6">') {
        echo "var newCell = addTableCell(newRow" . $count . "," . str_replace(array_keys($replace2), array_values($replace2), formatJSON(substr($row, 54, -5))) . ");\n";
        echo "newCell.style.textAlign='right';\nnewCell.style.backgroundColor='#FFEAE6';\n";
    }
}
?>
}
function addShippingAddress(data)
{
	ship_address=new Array();
	var shipAddress=new Array();
	for(key in data)
	{
		ship_address.push(data[key]);
		var address=data[key];
		var state=(address['state']!='')?', '+address['state']:'';
		var str=address['contact']+' ('+address['address']+', '+address['city']+state+', '+address['post_code']+', '+address['country']+')';
		shipAddress.push(str);
	}
	//setShippingAddress(ship_address[0]);
	setSelectOptions('shipping_to',shipAddress);
	setValue('shipping_to',0);
}
function addNotifyAddress(data)
{
	notify_address=new Array();
	var notifyAddress=new Array();
	for(key in data)
	{
		notify_address.push(data[key]);
		var address=data[key];
		var state=(address['state']!='')?', '+address['state']:'';
		var str=address['contact']+' ('+address['address']+', '+address['city']+state+', '+address['post_code']+', '+address['country']+')';
		notifyAddress.push(str);
	}
	//setShippingAddress(ship_address[0]);
	setSelectOptions('notify_party',notifyAddress);
	setValue('notify_party',0);
}
function addSpecialRequests(data)
{
	if (getElement('member_requests') != null) {
		var requests = '';
		for (key in data) {
			requests += '- ' + data[key] + "\n";
		}
		setValue('member_requests', requests);
		textareaAutoSize(getElement('member_requests'), 3, 2, 25);
	}
}
function freightCourierSearch(theFrm)
{
	var oldact=theFrm.action;
	var oldtrg=theFrm.target;
	theFrm.action="/webadmin/invoice/courier_search.php";
	//theFrm.target='_blank';
	theFrm.target="freight_search";
	theFrm.submit();
	theFrm.action=oldact;
	theFrm.target=oldtrg;
}
function updateShippingAddress()
{
	var shipAddress=getValue('shipping_to');
	if(shipAddress != '') setShippingAddress(ship_address[shipAddress]);
}
function updateNotifyAddress()
{
	var notifyAddress=getValue('notify_party');
	if(notifyAddress != '') setNotifyAddress(notify_address[notifyAddress]);
}
function updateGoodsExtra(section)
{
	getElement(section+'_by_tr').style.display=((getValue(section+'_by')=="n")?'':'none');
}
function updateTrackingNum()
{
	if(getValue("proforma_tracknumber") != '')
		getElement("process_invoice_tr").style.display='';
}
function selectQuotation(type)
{
	var allowed=Array('SeaFreight','AirFreight','Couriers');
	for(i=0;i<allowed.length;i++)
		getElement('section'+allowed[i]).style.display= (allowed[i]==type) ? '' : 'none';
}
//]]>
</script>

<?php
$acount          = 0;
$compare_array   = array('ship_name' => 'contact', 'ship_company' => 'company_name', 'ship_address' => 'address', 'ship_city' => 'city', 'ship_state' => 'state', 'ship_postcode' => 'post_code', 'ship_country' => 'country', 'ship_phone' => 'phone_no');
$compare_array2  = array('name' => 'contact', 'company' => 'company_name', 'address' => 'address', 'city' => 'city', 'state' => 'state', 'postcode' => 'post_code', 'country' => 'country', 'phone' => 'phone_no');
$selected        = '';
$selectedN       = '';
$shippingOptions = array();
$notifyOptions   = array();
foreach ($compare_array2 as $key => $key2) {
    if ('' == $invoice['notify_' . $key] || !isset($invoice['notify_' . $key])) {
        $invoice['notify_' . $key] = $invoice['ship_' . $key];
    }
}

foreach ($member_shippingaddresses as $key => $member_shippingaddress) {
    $state             = ('' != $member_shippingaddress['state']) ? ', ' . $member_shippingaddress['state'] : '';
    $optionVal         = $member_shippingaddress['contact'] . ' (' . $member_shippingaddress['address'] . ', ' . $member_shippingaddress['city'] . $state . ', ' . $member_shippingaddress['post_code'] . ', ' . getCountryName($member_shippingaddress['country']) . ')';
    $shippingOptions[] = htmlspecialchars($optionVal);
    $count             = 0;
    foreach ($compare_array as $key1 => $key2) {
        if (formatCleanString($invoice[$key1]) == formatCleanString($member_shippingaddress[$key2])) {
            $count++;
        }
        if (count($compare_array) == $count) {
            $selected = $key;
        }
    }
    /*
	if ($flag) {
		$acount++;
		echo address_row($acount, $member_shippingaddress);
	}
	*/
}
foreach ($member_notifyparties as $key => $member_notifyparty) {
    $state           = ('' != $member_notifyparty['state']) ? ', ' . $member_notifyparty['state'] : '';
    $optionVal       = $member_notifyparty['contact'] . ' (' . $member_notifyparty['address'] . ', ' . $member_notifyparty['city'] . $state . ', ' . $member_notifyparty['post_code'] . ', ' . getCountryName($member_notifyparty['country']) . ')';
    $notifyOptions[] = htmlspecialchars($optionVal);
    $count           = 0;
    foreach ($compare_array2 as $key1 => $key2) {
        if (formatCleanString($invoice['notify_' . $key1]) == formatCleanString($member_notifyparty[$key2])) {
            $count++;
        }
        if (count($compare_array2) == $count) {
            $selectedN = $key;
        }
    }
}
if ('' == $invoice['ship_name']) {
    $invoice['ship_name'] = $invoice['bill_name'];
}

if ('' == $invoice['ship_company']) {
    $invoice['ship_company'] = $invoice['bill_company'];
}

if (count($member_requests)) {
    $requests = array();
    foreach ($member_requests as $request) {
        if ('' != $request['comments']) {
            $requests[] = '-' . $request['comments'];
        }
    }

    $requests = implode("\n", $requests);
}
if ('' != $invoice['ship_country']) {
    $row1 = $DB->query_first(sprintf(
		"SELECT COUNT(*) FROM `world`.`fumigation_notes` WHERE `country` = %s LIMIT 1",
		$DB->escape($invoice['ship_country'])
	), MYSQLI_NUM);
    $row2 = $DB->query_first(sprintf(
		"SELECT COUNT(*) FROM `world`.`certificates` WHERE `country` = %s LIMIT 1",
		$DB->escape($invoice['ship_country'])
	), MYSQLI_NUM);
    $row3 = $DB->query_first(sprintf(
		"SELECT COUNT(*) FROM `world`.`shipping_notes` WHERE `country` = %s LIMIT 1",
		$DB->escape($invoice['ship_country'])
	), MYSQLI_NUM);
    $row1 = ($row1[0] > 0);
    $row2 = ($row2[0] > 0);
    $row3 = ($row3[0] > 0);
} else {
    $row1 = $row2 = $row3 = false;
}
$missing_weights = array();
foreach ($invoice_products as $product) {
    if (0.0 == $product['product_weight']) {
        if ('' != $product['product_url']) {
            $missing_weights[] = $product['product_id'] . ' - <a target="_blank" href="' . $product['product_url'] . '">' . htmlspecialchars($product['product_name']) . '</a>';
        } else {
            $missing_weights[] = $product['product_id'] . ' - ' . htmlspecialchars($product['product_name']);
        }
    }
}

?>
<table class="wide"><tr>
	<th style="width:50%;">Shipping Address</th><th style="width:50%;">Notify Party</th>
</tr><tr>
	<td style="width:50%;"><table style="width:100%;">
		<tr>
			<td colspan="2" style="text-align:center" class="left_title">
				Select A Shipping Address :
				<select name="shipping_to" onchange="updateShippingAddress()" style="text-align:center">
					<option value="" selected>&nbsp;</option><?=formatHTMLOptions($shippingOptions, $selected)?>
				</select>
			</td>
		</tr>
		<tr><td class="left_title" style="width:25%;">Name</td><td style="width:75%;"><input type="text" name="shipto_name[0]" value="<?=htmlspecialchars($invoice['ship_name'])?>" style="width:98%;" /></td></tr>
		<tr><td class="left_title">Company</td><td><input type="text" name="shipto_company[0]" value="<?=htmlspecialchars($invoice['ship_company'])?>" style="width:98%;" /></td></tr>
		<tr><td class="left_title">Address</td><td><textarea name="shipto_address[0]" rows="<?=max(2, count(explode("\n", trim($invoice['ship_address']))))?>" onkeyup="javascript:textareaAutoSize(this,1,2);"><?=htmlspecialchars($invoice['ship_address'])?></textarea></td></tr>
		<tr><td class="left_title">City</td><td><input type="text" name="shipto_city[0]" value="<?=htmlspecialchars($invoice['ship_city'])?>" style="width:98%;" /></td></tr>
		<tr><td class="left_title">State</td><td><input type="text" name="shipto_state[0]" value="<?=htmlspecialchars($invoice['ship_state'])?>" style="width:98%;" /></td></tr>
		<tr><td class="left_title">Postcode</td><td><input type="text" name="shipto_postcode[0]" value="<?=htmlspecialchars($invoice['ship_postcode'])?>" style="width:98%;" /></td></tr>
		<tr><td class="left_title">Country</td><td><select name="shipto_country[0]"><option value=""></option><?=formatHTMLOptions(getCountryList(), $invoice['ship_country'])?></select></td></tr>
		<tr><td class="left_title">Phone</td><td><input type="text" name="shipto_phone[0]" value="<?=htmlspecialchars($invoice['ship_phone'])?>" style="width:98%;" /></td></tr>
		<tr><td class="left_title">Email</td><td><input type="text" name="shipto_email[0]" value="<?=htmlspecialchars($invoice['ship_email'])?>" style="width:98%;" /></td></tr>
	</table></td>
	<td style="width:50%;"><table style="width:100%;">
		<tr>
			<td colspan="3" style="text-align:center;" class="left_title">
				Select A Notify Party :
				<select name="notify_party" onchange="updateNotifyAddress();" style="text-align:center">
					<option value="" selected>&nbsp;</option><?=formatHTMLOptions($notifyOptions, $selectedN)?>
				</select>
			</td>
		</tr>
		<tr><td class="left_title" style="width:25%;">Name</td><td style="width:75%;"><input type="text" name="notify_name[0]" value="<?=htmlspecialchars($invoice['notify_name'])?>" style="width:98%;" /></td></tr>
		<tr><td class="left_title">Company</td><td><input type="text" name="notify_company[0]" value="<?=htmlspecialchars($invoice['notify_company'])?>" style="width:98%;" /></td></tr>
		<tr><td class="left_title">Address</td><td><textarea name="notify_address[0]" rows="<?=max(2, count(explode("\n", trim($invoice['notify_address']))))?>" onkeyup="javascript:textareaAutoSize(this,1,2);"><?=htmlspecialchars($invoice['notify_address'])?></textarea></td></tr>
		<tr><td class="left_title">City</td><td><input type="text" name="notify_city[0]" value="<?=htmlspecialchars($invoice['notify_city'])?>" style="width:98%;" /></td></tr>
		<tr><td class="left_title">State</td><td><input type="text" name="notify_state[0]" value="<?=htmlspecialchars($invoice['notify_state'])?>" style="width:98%;" /></td></tr>
		<tr><td class="left_title">Postcode</td><td><input type="text" name="notify_postcode[0]" value="<?=htmlspecialchars($invoice['notify_postcode'])?>" style="width:98%;" /></td></tr>
		<tr><td class="left_title">Country</td><td><select name="notify_country[0]"><option value=""></option><?=formatHTMLOptions(getCountryList(), $invoice['notify_country'])?></select></td></tr>
		<tr><td class="left_title">Phone</td><td><input type="text" name="notify_phone[0]" value="<?=htmlspecialchars($invoice['notify_phone'])?>" style="width:98%;" /></td></tr>
		<tr><td class="left_title">Email</td><td><input type="text" name="notify_email[0]" value="<?=htmlspecialchars($invoice['notify_email'])?>" style="width:98%;" /></td></tr>
	</table></td>
</tr></table><br />
<table class="wide">
	<tr>
		<td class="left_title">Customer Special Requests</td>
		<td colspan="2" style="height:50px;"><textarea readonly="readonly" name="member_requests"<?=('' != $requests) ? ' style="border:5px solid #FF1493;"' : ''?>><?=$requests?></textarea></td>
	</tr>
	<tr>
		<td class="left_title">Special Freight Needs<br /><span style="font-size:9px;color:red;font-weight:normal;">Special Requests From Customers</span></td>
		<td colspan="2"><textarea name="ship_needs" rows="<?=max(10, count(explode("\n", trim($invoice['ship_needs']))))?>" onkeyup="javascript:textareaAutoSize(this,1,10);"<?=(trim($invoice['ship_needs']) != '') ? ' style="border:5px solid #FF1493;"' : ''?>><?=htmlspecialchars(trim($invoice['ship_needs']))?></textarea></td>
	</tr>
	<tr>
		<td class="left_title">Invoice Comments/Notes<br /><span style="font-size:9px;color:red;font-weight:normal;">Displayed on Invoice</span></td>
		<td colspan="2"><textarea name="invoice_comments" rows="<?=max(10, count(explode("\n", trim($invoice['invoice_comments']))))?>" onkeyup="javascript:textareaAutoSize(this,1,10);"><?=htmlspecialchars(trim($invoice['invoice_comments']))?></textarea></td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">Shipping To <span name="lblShipCountry"><?=htmlspecialchars($invoice['ship_country'])?></span></td>
		<td colspan="2">
			<input name="fumButton" type="button" value="Fumigation" onclick="javascript:shippingShowCountryNotes('fum');"<?=($row1) ? ' style="border:1px solid red;"' : ' disabled="disabled"'?> />
			<input name="cerButton" type="button" value="Certificates" onclick="javascript:shippingShowCountryNotes('cer');"<?=($row2) ? ' style="border:1px solid red;"' : ' disabled="disabled"'?> />
			<input name="shiButton" type="button" value="Special Needs" onclick="javascript:shippingShowCountryNotes('spn');"<?=($row3) ? ' style="border:1px solid red;"' : ' disabled="disabled"'?> />
			<a href="javascript:void(0);" onclick="javascript:shippingEditCountryNotes();">Add / Edit</a>
		</td>
	</tr>
	<tr>
		<td class="left_title">Shipping Weight (kgs)</td>
		<td colspan="2"><input name="ship_weight" type="text" size="10" value="<?=round($invoice['ship_weight'], 2)?>" /></td>
	</tr>
	<tr name="missing_weights_row"<?=(count($missing_weights)) ? '' : ' style="display:none;"'?>>
		<td class="left_title"></td>
		<td colspan="2"><div name="missing_weights_span" style="border:5px solid #9314FF;">
			The following products have undefined weights:<br />
			<?=implode("<br />\n", $missing_weights)?>
		</div></td>
	</tr>
	<tr>
		<td class="left_title">Cubic Weight (cm<sup>3</sup>)</td>
		<td colspan="2"><input name="ship_cubic" type="text" size="10" value="<?=round($invoice['ship_cubic'], 2)?>" /></td>
	</tr>
</table>
<br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionShippingInfo_hideNote">
	Shipping Information can be viewed by Clicking "Shipping Information" above<br />
</div>
<?php
$selected_courier = array();
if (is_array($invoice_courier_quotes)) {
    $flagFound = false;
    foreach ($invoice_courier_quotes as $quote) {
        //print_array($quote);
        $quote['id'] = freightServiceId($quote['name']);
        if (!$flagFound) {
            if ('y' == $quote['flag']) {
                $flagFound = true;
            }

            if ($invoice['ship_id'] == $quote['id'] && $invoice['ship_insurance'] == $quote['insurance'] && $invoice['ship_tailgate'] == $quote['tail_gate']) {
                $flagFound     = true;
                $quote['flag'] = 'y';
            }
            if ($flagFound) {
                $selected_courier = $quote;
            }
        }
    }
}
if (is_array($member_couriers)) {
    foreach ($member_couriers as $courier) {
        if ($invoice['ship_id'] == $courier['courier_id'] || 'y' == $courier['flag']) {
            $courier['sell_price'] = $courier['sell_priceau'] = sprintf('%.2f', 0);
            $courier['del_time']   = 'N/A';
            $courier['insurance']  = $invoice['ship_insurance'];
            $selected_courier      = $courier;
        }
    }
}

?>
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionCouriers\');">Freight Quotation</a>')?>
<div align="center">
	<input type="button" value="Courier Quotes" onclick="selectQuotation('Couriers');" />
	<input type="button" value="Sea Freight Quotes" onclick="selectQuotation('SeaFreight');" />
	<input type="button" value="Air Freight Quotes" onclick="selectQuotation('AirFreight');" />
</div>
<?php
$hideCouriers = true;
foreach ($invoice_courier_quotes as $quote) {
    //print_array($quote);
    $quote['id'] = freightServiceId($quote['name']);
    if ($invoice['ship_id'] == $quote['id'] && $invoice['ship_insurance'] == $quote['insurance'] && $invoice['ship_tailgate'] == $quote['tail_gate']) {
        $hideCouriers = false;
    }

    if ('y' == $quote['flag']) {
        $hideCouriers = false;
    }
}
if (is_array($member_couriers)) {
    foreach ($member_couriers as $courier) {
        if ($invoice['ship_id'] == $courier['courier_id'] || 'y' == $courier['flag']) {
            $hideCouriers = false;
        }
    }
}

foreach ($invoice_freight_quotes as $quote_id) {
    if (forwardersQuotesGetValue($quote_id, 'prefered_flag') == 'y') {
        $hideCouriers = true;
    }
}

?>
<br />
<div id="sectionCouriers" style="<?=($hideCouriers) ? 'display:none' : ''?>">
<?=formatHTMLTitle('Courier Quotation')?>
<table class="wide">
	<?php if (count($selected_courier)) {
?>
	<tr>
		<td class="left_title" style="width:25%">Courier</td>
		<?php $courier_name = $invoice['ship_product_name'] . (($invoice['ship_id'] > 100) ? ' (Personal Account)' : '')?>
		<td><input style="width:98%" type="text" name="ship_courier_name" value="<?=htmlspecialchars($courier_name)?>" readonly="readonly"/></td>
	</tr>
	<tr>
		<?php
$price = ('USD' == $invoice['invoice_currency']) ? $selected_courier['sell_price'] : $selected_courier['sell_priceau'];
    if ('AUD' != $invoice['invoice_currency'] && 'USD' != $invoice['invoice_currency']) {
        $price = convertCurrency($price, 'USD', $invoice['invoice_currency']);
    }

?>
		<td class="left_title">Sale Price :</td>
		<td><input style="width:98%" name="ship_courier_price" value="$<?=htmlspecialchars($invoice['ship_offer'] . ' ' . $invoice['invoice_currency'])?>" readonly="readonly"/></td>
	</tr>
	<tr>
		<td class="left_title">Transit Time :</td>
		<td><input style="width:98%" type="text" name="ship_courier_transit" value="<?=htmlspecialchars($selected_courier['del_time'])?>" readonly="readonly"/></td>
	</tr>
	<tr>
		<td class="left_title">Insurance :</td>
		<?php $insurance = ('y' == $selected_courier['insurance']) ? 'Yes' : (('n' == $selected_courier['insurance']) ? 'No' : htmlspecialchars($selected_courier['insurance']))?>
		<td><input style="width:98%" type="text" name="ship_courier_insurance" value="<?=$insurance?>" readonly="readonly" /></td>
	</tr>
	<tr><td class="left_title" colspan="2" style="text-align:center"><input type="button" value=" View Other Courier Methods " onclick="toggleSection('other_couriers')"></td></tr>
	<?php }?>
	<tbody id="other_couriers" style="<?=count($selected_courier) ? "display:none" : ""?>">
	<tr>
		<td class="left_title" style="width:25%">Shipping Via</td>
		<td colspan="2">
			<input type="button" value="Search Couriers" onclick="javascript:freightCourierSearch(this.form);" />
			- Results for
			<span name="freightcourier_locality">.</span>
			<span name="freightcourier_postcode">.</span>
			<span name="freightcourier_state">.</span>
			<span name="freightcourier_country">.</span>
		</td>
	</tr>
	<tr>
		<td class="left_title"><iframe name="freight_search" style="width:16px;height:16px;visibility:hidden;"></iframe></td>
		<td colspan="2"><table name="freight_courier_table" class="spacetable" style="width:100%;text-align:center;">
			<tr>
				<th></th>
				<th>Service</th>
				<th>Delivery Time</th>
				<th>Track</th>
				<th>Insure</th>
				<th>Tail Gate</th>
				<th>Cost<br />Price<br />(<span name="product_currency"><?=htmlspecialchars($invoice['invoice_currency'])?></span>)</th>
				<!--<th>Online<br />Price<br />(<span name="product_currency"><?=htmlspecialchars($invoice['invoice_currency'])?></span>)</th>-->
				<th>Sell<br />Price<br />(<span name="product_currency"><?=htmlspecialchars($invoice['invoice_currency'])?></span>)</th>
				<?php if ('AUD' != $invoice['invoice_currency']) {?>
				<th>Cost<br />Price<br />(AUD)</th>
				<th>Sell<br />Price<br />(AUD)</th>
				<?php }?>
			</tr>
			<tbody id="freight_couriers" name="freight_couriers">
<?php

// Now searching for couriers...based on the info already entered in the invoice.
$courier_list = searchCouriers(
	$invoice['ship_country'],
	$invoice['ship_postcode'],
	$invoice['ship_state'],
	$invoice['ship_city'],
	$invoice['ship_weight'],
	$invoice['products_offerau'],
	'AUD',
	$invoice['site_id']
);
//print_array($invoice_courier_quotes);
//print_array($invoice);
$count = 0;
if (is_array($invoice_courier_quotes)) {
    $flagFound = false;
    foreach ($invoice_courier_quotes as $quote) {
        //print_array($quote);
        $quote['id'] = freightServiceId($quote['name']);
        if (!$flagFound) {
            if ('y' == $quote['flag']) {
                $flagFound = true;
            }

            if ($invoice['ship_id'] == $quote['id'] && $invoice['ship_insurance'] == $quote['insurance'] && $invoice['ship_tailgate'] == $quote['tail_gate']) {
                $flagFound     = true;
                $quote['flag'] = 'y';
            }
        }
        $class = '';
        if ($count % 2) {
            $class = "alt";
        }

        echo freightcourier_row($count, $quote, $class);
        $count++;
    }
}
?>
			</tbody>
			<tfoot id="members_couriers" name="members_couriers">
<?php
$member_selected = false;
if (is_array($member_couriers)) {
    foreach ($member_couriers as $member_courier) {
        $class = ($count % 2) ? "alt" : '';
        $name  = ($member_courier['courier_id'] < 100) ? getFreightServiceName($member_courier['courier_id']) : getFreightServiceProviderName($member_courier['courier_id']);
        $name .= " (" . $member_courier['account_num'] . ')';
        $quote = array(
            'code'          => 0.0,
            'id'            => $member_courier['courier_id'],
            'name'          => $member_courier['courier_name'],
            'del_time'      => 'Personal Account (' . $member_courier['account_num'] . ')',
            'track'         => 'N/A',
            'insurance'     => 'N/A',
            'cost_price'    => '0.00',
            'retail_price'  => '0.00',
            'sell_price'    => '0.00',
            'cost_priceau'  => '0.00',
            'sell_priceau'  => '0.00',
            'supplier'      => $member_courier['courier_id'],
            'acc_num'       => $member_courier['account_num'],
            'supplier_name' => $member_courier['account_num'],
        );
        if ($invoice['ship_id'] == $member_courier['courier_id']) {
            $quote['flag']   = 'y';
            $member_selected = true;
        }
        echo freightcourier_row($count, $quote, $class, true);
        $count++;
    }
}
?>
			</tfoot>
		</table>
<?php
if (count($couriers_list) > 0 || count($member_couriers) > 0) {
?>
        <input type="button" onclick="clearFreightCouriers()" value="Clear" />
<?php
}
?>
		</td>
	</tr>
	</tbody>
	<?php $yesno = array('y' => 'Yes', 'n' => 'No')?>
	<tr>
		<td class="left_title">Invoice Date</td>
		<td>
			<input name="proforma_date" id="proforma_date" type="text" size="24" readonly="readonly" value="<?=$invoice['proforma_date']?>" />
			<input type="button" value=" V " onclick="javascript:gfPop.fPopCalendar(this.form.proforma_date);" />
			<input type="button" value="Clear" onclick="javascript:setValue('proforma_date','');" />
		</td>
	</tr>
	<tr id="account_num_tr" name="account_num_tr" style="<?=($member_selected) ? '' : 'display:none'?>">
		<td class="left_title">Shipping Account Number</td>
		<td><input name="ship_account_num" style="width:98%;" type="text" value="<?=htmlspecialchars($invoice['ship_account_num'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Goods Packed By Me</td>
		<td><select onchange="updateGoodsExtra('packed')" name="packed_by"><?=formatHTMLOptions($yesno, $invoice['packed_by'])?></select></td>
	</tr>
	<tr id="packed_by_tr" name="packed_by_tr" style="<?=('y' == $invoice['packed_by'] || '' == $invoice['packed_by']) ? 'display:none' : ''?>">
		<td class="left_title">&nbsp;</td><td><select name="goods_packed_by"><?=formatHTMLOptions(invoiceGetOptionList('goods_packed_by'), $invoice['goods_packed_by'])?></select></td>
	</tr>
	<tr>
		<td class="left_title">Goods Shipped By Me</td>
		<td><select onchange="updateGoodsExtra('shipped')" name="shipped_by"><?=formatHTMLOptions($yesno, $invoice['shipped_by'])?></select></td>
	</tr>
	<tr id="shipped_by_tr" name="shipped_by_tr" style="<?=('y' == $invoice['shipped_by'] || '' == $invoice['shipped_by']) ? 'display:none' : ''?>">
		<td class="left_title">&nbsp;</td><td><select name="goods_shipped_by"><?=formatHTMLOptions(invoiceGetOptionList('goods_shipped_by'), $invoice['goods_shipped_by'])?></select></td>
	</tr>
	<tr id="process_invoice_tr" name="process_invoice_tr" style="display:none">
		<td class="left_title">Process Invoice</td>
		<td>
			<input type="radio" name="process_invoice" value="n" checked>&nbsp;No&nbsp;
			<input type="radio" name="process_invoice" value="y">&nbsp;Yes&nbsp;
		</td>
	</tr>
</table>
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

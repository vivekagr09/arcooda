<!-- freight_table2.tpl -->

<?php
$freightsupplier_url = '/webadmin/cargo/forwarder.php';
?>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionShippingInfo\');">Shipping Information</a>') ?>
<div id="sectionShippingInfo">
<?php
/*
LIST OF JAVASCRIPT FUNCTIONS:
setShippingAddress(addr)
addCustomerAddress(addr)
delCustomerAddresses()
selectCustomerAddress(row)
searchCouriers(theFrm)
addCourierQuote(id,service,delivery,offer,cost)
delCourierList()
selectCourier(row)
updateFreightPrices()
 */
?>
<script type="text/javascript">
var firstAddress=1;
var lastAddress=<?= max(0, count($address_list)) ?>;

function setShippingAddress(addr,con)
{
	setValue('shipto_address[0]',addr);
	if(con!=null && con!='') setValue('shipto_country[0]',con);
	selectCustomerAddress(0);
}
function addCustomerAddress(addr,con)
{
	//alert('Address = '+addr+' & Country = '+con);
	if(con==null) con='';
	var address_table=getElement("address_table");
	if(address_table==null) return;
	if(addr=='') return;
	lastAddress++;
	var newRow1=addTableRow(address_table,"shipto_row["+lastAddress+"]");
	addTableCell(newRow1,'Other Shipping Address').className='left_title';
	var newCol=addTableCell(newRow1,'');
	var newTab=document.createElement('table');
	newTab.style.borderWidth=0;
	newTab.style.width='100%';
	newCol.appendChild(newTab)
	var newRow2=addTableRow(newTab,'');
	addTableCell(newRow2,'<input type="hidden" name="shipto_country['+lastAddress+']" value="'+con+'" /><input type="checkbox" name="shipto_flag['+lastAddress+']" onclick="selectCustomerAddress('+lastAddress+');" />');
	addTableCell(newRow2,'<textarea readonly="readonly" name="shipto_address['+lastAddress+']" rows="5" cols="40">'+addr+'</textarea>').width="100%";
}
function delCustomerAddresses()
{
	for(var i=firstAddress;i<=lastAddress;i++)
	{
		setValue('shipto_flag['+i+']',false);
		var theRow=getElement("shipto_row["+i+"]");
		if(theRow!=null) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	}
	firstAddress=lastAddress;
}
function selectCustomerAddress(row)
{
	var con=getValue('shipto_country['+row+']');
	if(con!=null && con!='')
	{
		setValue('customer_country',con);
		if(document.time_options!=null) time_options(con);
		if(document.new_country!=null) new_country();
	}
	if(row==0)
	{
		for(var i=firstAddress;i<=lastAddress;i++)
			setValue('shipto_flag['+i+']',false);
		setValue('shipto_flag[0]',true);
	}
	else
	{
		setValue('shipto_flag[0]',false);
		for(var i=firstAddress;i<=lastAddress;i++)
			if(i==row)
				setValue('shipto_flag['+i+']',true);
			else
				setValue('shipto_flag['+i+']',false);
	}
}

var firstCourier=1;
var lastCourier=<?= max(0, (is_array($courier_list)) ? count($courier_list) : 0) ?>;

function searchCouriers(theFrm)
{
	var oldact=theFrm.action;
	var oldtrg=theFrm.target;
	theFrm.action="freightsearch.php";
	theFrm.target="hidden_frame";
	theFrm.submit();
	theFrm.action=oldact;
	theFrm.target=oldtrg;
}
function addCourierQuote(id,service,delivery,offer,cost)
{
	getElement('courier_table_hideme').style.display='inline';

	var courier_table=getElement("courier_table");
	if(courier_table==null) return;
	lastCourier++;
	var newRow=addTableRow(courier_table,"courier_row["+lastCourier+"]");

	var offerus=toCurrency(offer,jscurrency,'USD');
	var costus=toCurrency(cost,jscurrency,'USD');

	addTableCell(newRow,'<input type="hidden" name="courier_id['+lastCourier+']" value="'+id+'" /><input type="checkbox" name="courier_flag['+lastCourier+']" onclick="selectCourier('+lastCourier+')" />');
	addTableCell(newRow,'<input type="text" style="width:100%;" name="courier_name['+lastCourier+']" readonly="readonly" value="'+service+'" />').style.width="100%";
	addTableCell(newRow,'<input type="text" size="5" name="courier_cost['+lastCourier+']" readonly="readonly" value="'+cost+'" />');
	addTableCell(newRow,'<input type="text" size="5" name="courier_offer['+lastCourier+']" readonly="readonly" value="'+offer+'" />');
	addTableCell(newRow,'<input type="text" size="5" name="courier_costus['+lastCourier+']" readonly="readonly" value="'+costus+'" />');
	addTableCell(newRow,'<input type="text" size="5" name="courier_offerus['+lastCourier+']" readonly="readonly" value="'+offerus+'" />');
}
function delCourierList()
{
	for(var i=firstCourier;i<=lastCourier;i++)
	{
		setValue('courier_flag['+i+']',false);
		var theRow=getElement("courier_row["+i+"]");
		if(theRow!=null) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	}
	firstCourier=lastCourier;

	getElement('courier_table_hideme').style.display='none';
}
function selectCourier(row)
{
	for(var i=firstCourier;i<=lastCourier;i++)
		if(i!=row)
			setValue('courier_flag['+i+']',false);

	if(getElement('courier_flag['+row+']').checked)
	{
		setValue("couriersv_id",getValue("courier_id["+row+"]"));
		setValue("freight_offer",getValue("courier_offer["+row+"]"));
		setValue("freight_cost",getValue("courier_cost["+row+"]"));
		setValue("freight_name",getValue("courier_name["+row+"]"));
		setValue("supplier_freightname",getValue("courier_name["+row+"]"));
	}
	else
	{
		setValue("couriersv_id",0);
		setValue("freight_name","General Freight");
		setValue("supplier_freightname","General Freight");
	}

	updateTotals();
}

function updateFreightPrices()
{
	updateTotals();
}
//*********Obsolete**********
function searchFreight(theFrm)
{
	searchCouriers(theFrm);
}
function freightClearList()
{
	delCourierList();
}
function setFreight(offer,cost)
{
	setValue("freight_price",offer);
	setValue("product_freight",offer);
	setValue("supplier_freight",cost);
	updateTotals();
}
function freightAddItem(id,service,delivery,offer,cost)
{
	addCourierQuote(id,service,delivery,offer,cost);
}
//********/Obsolete**********

function searchWeight()
{
	var ids=String();
	var qty=String();
	var theRows=product_table.rows;
	for(i=0;i<theRows.length;i++) {
		var theTags=theRows[i].getElementsByTagName("input");
		if(theTags[0].value>0) {
			ids+=String(theTags[0].value)+',';
			qty+=String(theTags[4].value)+',';
		}
	}
	var url = <?php jecho($wsearch_url)?> + 'ids=' + ids + '&qty=' + qty;
	var features='scrollbars, resizable, height=450, width=600';
	window.open(url,'Calculate',features);
}
function setWeight(weight)
{
	setInputValue("approx_weight",weight);
}

function locationAdd(return_id,data)
{
	var theFrom=getElement('port_from');
	if(theFrom!=null)
	{
		var theOption=document.createElement('option');
		theOption.value=data['city_id'];
		theOption.innerHTML=data['city_name']+' - '+data['country_id'];
		theFrom.appendChild(theOption);
	}
	var theTo=getElement('port_to');
	if(theTo!=null)
	{
		var theOption=document.createElement('option');
		theOption.value=data['city_id'];
		theOption.innerHTML=data['city_name']+' - '+data['country_id'];
		theTo.appendChild(theOption);
	}
	setValue(return_id,data['country_id']);
}
function locationRemove(return_id,data)
{
	var theFrom=getElement('port_from');
	var theTo=getElement('port_to');

	if(theFrom!=null) theFrom.options[data['city_id']]=null;
	if(theTo!=null) theTo.options[data['city_id']]=null;

	if(getValue('port_from')==data['city_id']) setValue('port_from','');
	if(getValue('port_to')==data['city_id']) setValue('port_to','');
}

function foo(u)
{
	var win = window.open(u,"Notes","location=0, directories=no, status=no, menubar=yes, scrollbars=yes, resizable=no, width=400, height=260");
	//win.show();
	win.focus();
}


</script>
<script type="text/javascript" src="functions/trcloner.js"></script>
<script type="text/javascript" src="functions/delete_contact2.js"></script>

<table class="wide">
	<tr>
		<td colspan="3" style="text-align:center" class="left_title">
			Select A Shipping Address :
			<select name="shipping_to" onchange="updateShippingAddress()" style="text-align:center">
				<option value="" selected>&nbsp;</option><?= formatHTMLOptions($shippingOptions, $selected); ?>
			</select>
		</td>
	</tr>
	<tr>
		<td style="width:25%;" class="left_title">Shipping Address</td>
		<td colspan="2" style="width:71%;">
			<input type="hidden" name="shipto_flag[0]" onclick="selectShippingAddress(0);" checked="checked" />
<?php
if ('' == $invoice['ship_name']) {
    $invoice['ship_name'] = $invoice['bill_name'];
}

if ('' == $invoice['ship_company']) {
    $invoice['ship_company'] = $invoice['bill_company'];
}

?>
			<table style="width:100%;">
				<tr><td class="left_title" style="width:25%;">Name</td><td style="width:75%;"><input type="text" name="shipto_name[0]" value="<?php hecho($invoice['ship_name'])?>" style="width:98%;" /></td></tr>
				<tr><td class="left_title">Company</td><td><input type="text" name="shipto_company[0]" value="<?php hecho($invoice['ship_company'])?>" style="width:98%;" /></td></tr>
				<tr><td class="left_title">Address</td><td><textarea name="shipto_address[0]" rows="<?= max(2, count(explode("\n", trim($invoice['ship_address'])))) ?>" onkeyup="textareaAutoSize(this,1,2);"><?php hecho($invoice['ship_address'])?></textarea></td></tr>
				<tr><td class="left_title">City</td><td><input type="text" name="shipto_city[0]" value="<?php hecho($invoice['ship_city'])?>" style="width:98%;" /></td></tr>
				<tr><td class="left_title">State</td><td><input type="text" name="shipto_state[0]" value="<?php hecho($invoice['ship_state'])?>" style="width:98%;" /></td></tr>
				<tr><td class="left_title">Postcode</td><td><input type="text" name="shipto_postcode[0]" value="<?php hecho($invoice['ship_postcode'])?>" style="width:98%;" /></td></tr>
				<tr><td class="left_title">Country</td><td><select name="shipto_country[0]"><option value=""></option><?= formatHTMLOptions(getCountryList(), $invoice['ship_country']) ?></select></td></tr>
				<tr><td class="left_title">VAT/Company #</td><td><input type="text" name="shipto_vat[0]" value="<?php hecho($invoice['ship_vat'])?>" style="width:98%;" /></td></tr>
				<tr><td class="left_title">Phone</td><td><input type="text" name="shipto_phone[0]" value="<?php hecho($invoice['ship_phone'])?>" style="width:98%;" /></td></tr>
				<tr><td class="left_title">Email</td><td><input type="text" name="shipto_email[0]" value="<?php hecho($invoice['ship_email'])?>" style="width:98%;" /></td></tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="left_title">Special Freight Needs<br /><span style="font-size:9px;color:red;font-weight:normal;">Special Requests From Customers</span></td>
		<td colspan="2"><textarea name="ship_needs" rows="<?= max(10, count(explode("\n", trim($invoice['ship_needs'])))) ?>" onkeyup="textareaAutoSize(this,1,10);"<?= (trim($invoice['ship_needs']) != '') ? ' style="border:5px solid #FF1493;"' : '' ?>><?php hecho(trim($invoice['ship_needs']))?></textarea></td>
	</tr>
	<tr>
		<td class="left_title">Invoice Comments/Notes<br /><span style="font-size:9px;color:red;font-weight:normal;">Displayed on Invoice</span></td>
		<td colspan="2"><textarea name="invoice_comments" rows="<?= max(10, count(explode("\n", trim($invoice['invoice_comments'])))) ?>" onkeyup="textareaAutoSize(this,1,10);"><?php hecho(trim($invoice['invoice_comments']))?></textarea></td>
	</tr>
<?php
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

?>
	<tr>
		<td class="left_title" style="width:25%;">Shipping To <span name="lblShipCountry"><?php hecho($invoice['ship_country'])?></span></td>
		<td colspan="2">
			<input name="fumButton" type="button" value="Fumigation" onclick="shippingShowCountryNotes('fum');"<?= ($row1) ? ' style="border:1px solid red;"' : ' disabled="disabled"' ?> />
			<input name="cerButton" type="button" value="Certificates" onclick="shippingShowCountryNotes('cer');"<?= ($row2) ? ' style="border:1px solid red;"' : ' disabled="disabled"' ?> />
			<input name="shiButton" type="button" value="Special Needs" onclick="shippingShowCountryNotes('spn');"<?= ($row3) ? ' style="border:1px solid red;"' : ' disabled="disabled"' ?> />
			<a href="javascript:void(0);" onclick="shippingEditCountryNotes();">Add / Edit</a>
		</td>
	</tr>
	<tr>
		<td class="left_title">Shipping Weight (kgs)</td>
		<td colspan="2"><input name="ship_weight" type="text" size="10" value="<?= round($invoice['ship_weight'], 2) ?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Cubic Weight (cm<sup>3</sup>)</td>
		<td colspan="2"><input name="ship_cubic" type="text" size="10" value="<?= round($invoice['ship_cubic'], 2) ?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Transit Time (days)</td>
		<td><input name="ship_transit" type="text" size="10" value="<?= $invoice['ship_transit'] ?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Freight Cost (USD)</td>
		<td><input name="freight_cost" type="text" size="10" value="<?= sprintf('%.2f', $invoice['ship_costus']) ?>" readonly="readonly" /></td>
	</tr>
	<tr>
		<td class="left_title">Freight Sale Price (USD)</td>
		<td><input name="freight_offer" type="text" size="10" value="<?= sprintf('%.2f', $invoice['ship_offerus']) ?>" readonly="readonly" /></td>
	</tr>
<?php
$freight_type = explode('Freight Costs', $invoice['ship_product_name']);
$freight_type = $freight_type[0];
?>
	<tr>
		<td class="left_title">Freight Method</td>
		<td><input name="freight_type" type="text" value="<?= $freight_type ?>" /></td>
	</tr>
<?php
$airport_list = getAirPortsList();
$airfrom_list = array();
$airto_list   = array();
foreach ($airport_list as $key1 => $port_list) {
    if ($key1 == $quotedata['country_from'] || '' == $quotedata['country_from']) {
        foreach ($port_list as $key2 => $port_name) {
            $airfrom_list[$key2] = $port_name . ' - ' . $key1;
        }
    }

    if ($key1 == $quotedata['country_to'] || '' == $quotedata['country_to']) {
        foreach ($port_list as $key2 => $port_name) {
            $airto_list[$key2] = $port_name . ' - ' . $key1;
        }
    }

}
?>
		<tr>
			<td class="left_title">Ship From Port</td>
			<td>
				<select name="freight_from_port" style="width:270px;"<?= ('add' == $mode) ? ' disabled="disabled"' : '' ?>>
					<option value=""></option>
					<?= formatHTMLOptions($airport_list[$supplier_country], $quotedata['from']) ?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="left_title">Ship To Port</td>
			<td>
				<select name="freight_to_port" style="width:270px;" <?= ('add' == $mode) ? 'disabled' : '' ?>>
					<option value=""></option>
					<?= formatHTMLOptions($airport_list[$customer_country], $quotedata['to']) ?>
				</select>
			</td>
		</tr>
		<tr><td class="left_title">Freight Located</td><td><select name="freight_located">
			<option value="no" <?= ('no' == $freight_located) ? ' selected="selected"' : '' ?>>No</option>
			<option value="yes" <?= ('yes' == $freight_located) ? ' selected="selected"' : '' ?>>Yes</option>
		</select></td></tr>
<?php
if ('yes' == $freight_located) {
?>
		<tr>
			<td class="left_title">Authorised</td>
			<td><?= $freight_staff_name ?> - <?= formatDate($freight_locate_time) ?> <?= formatTime($freight_locate_time) ?></td>
		</tr>
<?php
}
?>
	</tfoot>

	<tbody id="address_table">
<?php
if (count($address_list) > 0) {
    $address_count = 0;
    foreach ($address_list as $key => $address) {
        $address_count++;
?>
		<tr id="shipto_row[<?= $address_count ?>]"<?= ($pr_man) ? ' style="display:none;"' : '' ?>>
			<td class="left_title">Other Shipping Address</td>
			<td><table style="border:0px solid white;width:100%;"><tr>
				<td><input type="hidden" name="shipto_country[<?= $address_count ?>]" value="<?= $shipto_countries[$key] ?>" /><input type="checkbox" name="shipto_flag[<?= $address_count ?>]" onclick="selectCustomerAddress(<?= $address_count ?>);" /></td>
				<td style="width:100%;"><textarea readonly="readonly" name="shipto_address[<?= $address_count ?>]" rows="5" cols="40"><?= str_replace('<', '&lt;', $address) ?></textarea></td>
			</tr></table></td>
		</tr>
<?php
} //foreach($address_list as $address)
} //if(count($address_list))
?>
	</tbody>
</table>
<br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionShippingInfo_hideNote">
Shipping Information can be viewed by Clicking "Shipping Information" above
</div>

<br />

<!-- /freight_table2.tpl -->

<?php
include_once 'forwarders_common.inc.php';
include_once 'templates/forwarders_header.tpl.php';

//print_array($posted);
$action = $_REQUEST['action'];
if ('do_add' == $action) {
    if ($_REQUEST['freightquote']['invoice_id']) {}
}

/**
 * @param mixed[] $posted
 *
 * @return void
 */
function display_template($posted)
{

    $sea_air      = $posted['sea_air'];
    $forwarder_id = $posted['id'];

    $sea             = (ucwords($posted['sea_air']) == 'Sea') ? true : false;
    $sea_fields      = array('wgt' => 'Ocean Freight', 'cfs' => 'CFS Charge', 'thc' => 'THC/ORC', 'doc' => 'Document Charge', 'tel' => 'Tele-Release Document', 'awb' => 'AWB', 'ams' => 'AMS', 'baf' => 'BAF', 'clr' => 'Clearance Fee', 'exp' => 'Export Licence Fee', 'han' => 'Handling Charge', 'tru' => 'Trucking Charge', 'cra' => 'Crating Charge', 'fum' => 'Fumigation', 'com' => 'Commodity Inspection', 'cus' => 'Customs Inspection', 'ins' => 'Insurance Charge', 'ori' => 'Certificate Origin', 'oth' => 'Other');
    $air_fields      = array('wgt' => 'Air Freight', 'thc' => 'THC/ORC', 'doc' => 'Document Charge', 'awb' => 'AWB', 'exp' => 'Export Licence Fee', 'han' => 'Handling Charge', 'tru' => 'Trucking Charge', 'cra' => 'Crating Charge', 'fum' => 'Fumigation', 'com' => 'Commodity Inspection', 'cus' => 'Customs Inspection', 'ins' => 'Insurance Charge', 'ori' => 'Certificate Origin', 'oth' => 'Other');
    $cost_fields     = ($sea) ? $sea_fields : $air_fields;
    $sea_flags       = array('lcl' => 'LCL', '20ft' => '20ft', '40ft' => '40ft', '40hq' => '40ft HQ');
    $air_flags       = array('direct' => 'Direct', 'indirect' => 'In-Direct');
    $container_flags = ($sea) ? $sea_flags : $air_flags;
    $terms           = array('fob' => 'FOB', 'exw' => 'Ex Works', 'dtd' => 'Door To Door', 'dta' => 'Door To Port');
    //$supplier_list=forwardersQuotesGetOptionList('product_id');
    $country_list = getCountryList();
    $ports        = ($sea) ? getseaportslist() : getairportslist();

    $sea_fields = array('lading_number' => '', 'container_number' => '', 'vessel_name' => ''); //'arrival_date'=>'ETA', 'departure_date'=>'ETD'
    $air_fields = array('master_number' => 'Master Airway Number', 'house_number' => 'House Airway Number', 'vessel_name' => 'Aircraft/Flight #');
    //'arrival_date'=>'ETA', 'departure_date'=>'ETD'
    $ext_fields = ($sea) ? $sea_fields : $air_fields;
    foreach ($ext_fields as $key => $name) {
        if ('' == $name) {
            $ext_fields[$key] = ucwords(str_replace('_', ' ', $key));
        }
    }

    $yesno = array('n' => 'No', 'y' => 'Yes');

?>
	<script>
	function totcheckQuote()
	{
		var on_off=getElement('freightquote[total_flag]').checked;
<?php
foreach ($cost_fields as $key => $name) {
        echo "	getElement('freightquote[cost_flags][" . $key . "]').checked=on_off;\n";
    }

?>
	}
	function changePorts(changed, type)
	{
		var country_from=getValue('freightquote[country_from]');
		var country_to=getValue('freightquote[country_to]');
		var toUse=((changed=='from') ? country_from : country_to);
		var frontPart='freightquote[';
		var name=frontPart+((changed=='from'?'port_from':'port_to')+']');
		var elem=getElement(name);
		setValue(name,'');
		if(elem)
		{
			var port_list=Array();
			if(type=='sea') port_list=countrySeaports(toUse);
			else if(type=='air') port_list=countryAirports(toUse);
			elem.options.length=0;
			elem.options[0]=new Option('','');
			for(var key in port_list)
				elem.options[elem.options.length]=new Option(port_list[key],key);
		}
	}

	function refreshQuote()
	{
		var total=0.0;
		var total_us=0.0;
		var all_checked=true;

		var quote_currency=getValue('freightquote[quote_currency]');
		var weight_rate=Number(getValue('freightquote[weight_rate]'));
		var insurance_rate=Number(getValue('freightquote[insurance_rate]'));
		var shipping_weight=Number(getValue('freightquote[cubic_weight]'));
		var newCurrency=getValue('invoice_currency');
		var invoice_value=convertCurrency(getValue('products_offerau'),'AUD',quote_currency);

		if(weight_rate!=0.0) setValue('freightquote[wgt_amount]',convertCurrency(weight_rate*shipping_weight,quote_currency));
		//setValue('freightquote[wgt_amountus]',convertCurrency(weight_rate*shipping_weight,quote_currency,newCurrency));
		if(insurance_rate!=0.0) setValue('freightquote[ins_amount]',convertCurrency((insurance_rate/100)*invoice_value,quote_currency));
		//setValue('freightquote[ins_amountus]',convertCurrency((insurance_rate/100)*invoice_value,quote_currency,newCurrency));
<?php
foreach ($cost_fields as $key => $name) {
?>
		if(getElement('freightquote[cost_flags][<?php echo $key ?>]').checked)
		{
			total+=Number(getValue('freightquote[<?php echo $key ?>_amount]'));
			total_us+=Number(getValue('freightquote[<?php echo $key ?>_amountus]'));
		}
		else
			all_checked=false;
<?php
}
?>
		setValue('freightquote[total_amount]',Number(total).toFixed(2));
		//setValue('freightquote[total_amountus]',Number(total_us).toFixed(2));
		getElement('freightquote[total_flag]').checked=all_checked;
	}

	function updateConfirm(value)
	{
		getElement('confirm_stuff').style.display=(value=='y') ? '' : 'none';
	}

	function searchInvoices()
	{
		var search_for=getValue('freightquote[invoice_id]');
		var url="/webadmin/invoice/?action=list&status=proforma|sales|processed|cancelled&return_message=test&search_for="+escape(search_for);
		window.open(url);
	}

	function resize(theElem,stepSize,minSize,maxSize)
	{
		if(theElem==null || theElem.rows==null || theElem.cols==null || theElem.value==null) return;
		if(stepSize==null || stepSize<1) stepSize=5;

		var rowContent=theElem.value.split('\n');
		var rowCount=rowContent.length;

		if(theElem.cols>0)
			for(i=0;i<rowContent.length;i++)
				rowCount+=Math.floor(rowContent[i].length/theElem.cols);

		rowCount=Math.floor(rowCount/stepSize)*stepSize+stepSize;

		if(minSize!=null && minSize>rowCount) rowCount=minSize;
		if(maxSize!=null && maxSize>minSize && maxSize<rowCount) rowCount=maxSize;

		theElem.rows=rowCount;
	}

	function setInvoiceData(data)
	{
		setValue('freightquote[invoice_id]',data['invoice_id']);
		setValue('products_offerau',data['products_offerau']);
		if(data['supply_country']=='') data['supply_country']=='AU';
		setValue('freightquote[country_from]',data['supply_country']);
		changePorts('from','<?php echo strtolower($posted['sea_air']) ?>');
		if(data['ship_country']!='')
		{
			setValue('freightquote[country_to]',data['ship_country']);
			changePorts('to','<?php echo strtolower($posted['sea_air']) ?>');
		}
		setValue('freightquote[quote_currency]',data['invoice_currency']);
		setValue('freightquote[shipping_weight]',data['ship_weight']);
		setValue('freightquote[cubic_weight]',data['ship_cubic']);
	}
	</script>
	<?php echo formatHTMLTitle('Add Freight Quote'); ?>
	<form accept-charset="utf-8" autocomplete="off" method="post" action="">
	<input type="hidden" name="action" value="do_add" />
	<input type="hidden" name="freightquote[sea_air]" value="<?php echo ucfirst($posted['sea_air']) ?>" />
	<input type="hidden" name="freightquote[product_id]" value="<?php echo $forwarder_id ?>" />
	<table class="wide">
		<tr><td class="left_title">Supplier :</td><td colspan="2"><b><?php echo getFreightServiceName($forwarder_id) ?></b></td></tr>
		<tr><td class="left_title">&nbsp;</td><td colspan="2">&nbsp;</td>
		<tr>
			<td class="left_title" style="width:25%">Invoice ID :</td>
			<td colspan="2"><input style="width:80%" type="text" name="freightquote[invoice_id]" />&nbsp;&nbsp;<input type="button" value="Search" onclick="searchInvoices()"/></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%">Invoice Products Offer (AUD) :</td>
			<td colspan="2"><input style="width:100%" type="text" name="products_offerau" /></td>
		</tr>
		<tr>
			<td class="left_title"><?php echo ($sea) ? 'Container' : 'Service' ?> Type :</td>
			<td colspan="2">
<?php
foreach($container_flags as $key=>$name) { ?><input type="radio" name="freightquote[container_flags]" value="<?php echo $key ?>"/><?php echo $name ?>&nbsp;&nbsp;
<?php
}
?>
			</td>
		</tr>
		<tr>
			<td class="left_title">Shipping Terms :</td>
			<td colspan="2"><?php
foreach($terms as $key=>$name) { ?><input type="radio" name="freightquote[term_flags]" value="<?php echo $key ?>" /><?php echo $name ?>&nbsp;&nbsp;<?php
} ?></td>
		</tr>
		<tr>
			<td class="left_title">Ship From Country</td>
			<td colspan="2"><select name="freightquote[country_from]" onchange="changePorts('from','<?php echo strtolower($posted['sea_air']) ?>')"><option value=""></option><?php echo formatHTMLOptions($country_list) ?></select></td>
		</tr>
		<tr>
			<td class="left_title">Ship From Port</td>
			<td colspan="2"><select name="freightquote[port_from]"><option value=""></option><?php echo formatHTMLOptions($ports_from) ?></select></td>
		</tr>
		<tr>
			<td class="left_title">Ship To Country</td>
			<td colspan="2"><select name="freightquote[country_to]" onchange="changePorts('to','<?php echo strtolower($posted['sea_air']) ?>')"><option value=""></option><?php echo formatHTMLOptions($country_list) ?></select></td>
		</tr>
		<tr>
			<td class="left_title">Ship To Port</td>
			<td colspan="2"><select name="freightquote[port_to]"><option value=""></option><?php echo formatHTMLOptions($ports_to) ?></select></td>
		</tr>
		<tr><td class="left_title">Shipping Weight (kgs)</td><td colspan="2"><input name="freightquote[shipping_weight]" type="text" value="" /></td></tr>
		<tr><td class="left_title">Cubic Weight (cm<sup>3</sup>)</td><td colspan="2"><input name="freightquote[cubic_weight]" type="text" value="" /></td></tr>
		<tr><td class="left_title">Transit Time (days)</td><td colspan="2"><input name="freightquote[transit_time]" type="text" value="" /></td></tr>
		<tr>
			<td class="left_title">Currency</td>
			<td colspan="2"><select name="freightquote[quote_currency]"><option value=""></option><?php echo formatHTMLOptions(getCurrencyList(), 'AUD') ?></select></td>
		</tr>
		<tr><td class="left_title">&nbsp;</td><td colspan="2">&nbsp;</td></tr>
<?php
    foreach ($cost_fields as $key => $name) {
        $amount = convertCurrency($quote[$key . '_amount'], $quote['quote_currency'], $invoice['invoice_currency']);
?>
		<tr>
			<td class="left_title"><?php echo $name ?></td>
			<td style="text-align:right;white-space:nowrap;width:20%">
<?php
if ('wgt' == $key) {
            echo '<input onchange="refreshQuote()" type="text" name="freightquote[weight_rate]" size="3" value="" /> per cm<sup>3</sup>';
        }

        if ('ins' == $key) {
            echo '<input onchange="refreshQuote()" type="text" name="freightquote[insurance_rate]" size="3" value="" />%';
        }

?>
				<input type="checkbox" onclick="refreshQuote();" name="freightquote[cost_flags][<?php echo $key ?>]" />
			</td>
			<td><input name="freightquote[<?php echo $key ?>_amount]" onchange="refreshQuote();" style="text-align:right;" type="text" value="" /></td>
		</tr>
<?php
}
?>
		<tr>
			<td class="left_title">TOTAL COST :</td>
			<td style="text-align:right">All Boxes <input type="checkbox" name="freightquote[total_flag]" onchange="totcheckQuote();refreshQuote();"<?= ($all_boxes) ? ' checked="checked"' : ''?> /></td>
			<td><input style="text-align:right;" type="text" readonly="readonly" name="freightquote[total_amount]" value="" /></td>
		</tr>
		<tr><td class="left_title">SALE PRICE :</td><td>&nbsp;</td><td><input style="text-align:right;" type="text" name="freightquote[sale_price]" value="" /></td></tr>
		<tr>
			<td class="left_title">General Notes :</td>
			<td colspan="2"><textarea name="freightquote[general_notes]" onkeyUp="resize(this,3,3,20)"></textarea></td>
		</tr>
		<tr>
			<td class="left_title">Validity :</td>
			<td colspan="2">
				<input type="text" name="freightquote[validity_date]" readonly="readonly" />
				<input type="button" value=" V " onclick="javascript:gfPop.fPopCalendar(getElement('freightquote[validity_date]'));" />
				<input type="button" value="Clear" onclick="setValue('freightquote[validity_date]','')" />
			</td>
		</tr>
		<tr>
			<td class="left_title">Confirm :</td>
			<td colspan="2"><select name="freightquote[confirmed_flag]" onchange="updateConfirm(this.value)"><?php echo formatHTMLOptions($yesno) ?></select></td>
		</tr>
		<tbody id="confirm_stuff" style="display:none">
<?php
foreach($ext_fields as $key=>$name) { ?><tr><td class="left_title"><?php echo $name ?> :</td><td colspan="2"><input name="freightquote[<?php echo $key ?>]" /></td></tr><?php
} ?>
		<tr>
			<td class="left_title">ETA</td>
			<td colspan="2">
				<input type="text" name="freightquote[arrival_date]" readonly="readonly" />
				<input type="button" value=" V " onclick="javascript:gfPop.fPopCalendar(getElement('freightquote[arrival_date]'));" />
				<input type="button" value="Clear" onclick="setValue('freightquote[arrival_date]','')" />
			</td>
		</tr>
		<tr>
			<td class="left_title">ETD</td>
			<td colspan="2">
				<input type="text" name="freightquote[departure_date]" readonly="readonly">
				<input type="button" value=" V " onclick="javascript:gfPop.fPopCalendar(getElement('freightquote[departure_date]'));" />
				<input type="button" value="Clear" onclick="setValue('freightquote[departure_date]','')" />
			</td>
		</tr>
		</tbody>
	</table>
	<br />
	<center><input type="submit" value=" Add Quote " /></center>
	</form>
<?php
}

function do_add($posted)
{
    global $DB;

    $sql          = "INSERT INTO `highwaygroup`.`freight_services_quotes` ";
    $set          = array();
    $allowed      = array('product_id', 'container_flags', 'term_flags', 'sea_air', 'invoice_id', 'country_from', 'port_from', 'country_to', 'port_to', 'shipping_weight', 'cubic_weight', 'transit_time', 'quote_currency', 'weight_rate', 'wgt_amount', 'cfs_amount', 'thc_amount', 'doc_amount', 'tel_amount', 'awb_amount', 'ams_amount', 'baf_amount', 'clr_amount', 'exp_amount', 'han_amount', 'tru_amount', 'cra_amount', 'fum_amount', 'com_amount', 'cus_amount', 'insurance_rate', 'ins_amount', 'ori_amount', 'oth_amount', 'sale_price', 'confirmed_flag', 'lading_number', 'container_number', 'vessel_name', 'master_number', 'house_number', 'validity_date', 'arrival_date', 'departure_date', 'general_notes');
    $freightquote = $posted['freightquote'];
    if (is_array($freightquote)) {
        foreach ($freightquote as $key => $value) {
            if (in_array($key, $allowed)) {
                $set[] = $key . '=' . $DB->escape($value);
            }
        }
    }

    if (is_array($freightquote['cost_flags'])) {
        $set[] = "cost_flags=" . $DB->escape(implode(',', array_keys($freightquote['cost_flags'])));
    }

    $set[] = 'cost_price=' . $DB->escape($freightquote['total_amount']);
    if (count($set)) {
        $sql .= " SET " . implode(', ', $set);
        $DB->query($sql);
        echo 'The freight quote has been added to the database and linked to Invoice ID#' . $freightquote['invoice_id'];
        //echo $sql;
    }

    //print_array($set);
}

switch ($action) {
    case 'do_add':
        do_add($_REQUEST);
        break;
    case '':
    default:
        display_template($_REQUEST);
        break;
}
<!-- freight_quotes.tpl -->

<?php
$suppliers_list = forwardersQuotesGetOptionList('product_id');
$prefer         = array();
$count          = 1;
foreach ($invoice_freight_quotes as $quote_id) {
    $prefer[$count] = $quote_id;
    $count++;
}

$default_selection = 'pickup';
if (0 != $invoice['ship_supplier_id']) {
    $default_selection = 'customer_forwarder';
}

if (0 != $invoice['ship_id']) {
    $default_selection = 'other';
}

//Set default tabs if shipping_method is set
if ('' != $invoice['shipping_method']) {
    $default_selection = 'other';
}

if ('customer_forwarder' == $invoice['shipping_method']) {
    $default_selection = 'customer_forwarder';
}

if ('pickup' == $invoice['shipping_method']) {
    $default_selection = 'selection';
}

//now for the couriers...
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

$hideCouriers = true;
foreach ($invoice_courier_quotes as $quote) {
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

$hideSea    = true;
$hideAir    = true;
$sea_quotes = array();
$air_quotes = array();
foreach ($invoice_freight_quotes as $quote_id) {
    $freightquote = forwardersQuoteFromDatabase($quote_id);
    if ('Sea' == $freightquote['sea_air']) {
        $sea_quotes[$quote_id] = $freightquote;
        if ('y' == $freightquote['prefered_flag']) {
            $hideSea = false;
        }

    } elseif ('Air' == $freightquote['sea_air']) {
        $air_quotes[$quote_id] = $freightquote;
        if ('y' == $freightquote['prefered_flag']) {
            $hideAir = false;
        }

    }
}
$qcount = 1;
?>
<script type="text/javascript">
//<![CDATA[
var freightquote_count=<?= max(100, count($invoice_freight_quotes)) ?>;
var countStart=freightquote_count;
var prefer = <?php jecho($prefer)?>;
var suppliers_list = <?php jecho($suppliers_list)?>;
function updatePrefered(id)
{
	for(key in prefer)
	{
		var obj=getElement('freightquote['+key+'][prefered]');
		obj.checked=((key != id) ? false : true);
		obj.parentNode.style.backgroundColor=((obj.checked) ? '#6688BB' : '#FFFFFF');
		getElement('freightquote['+key+'][header]').style.backgroundColor="#FFEECC";
	}
	for(i=countStart;i<freightquote_count;i++)
	{
		var obj=getElement('freightquote['+i+'][prefered]');
		obj.checked=((i != id) ? false : true);
		obj.parentNode.style.backgroundColor=((obj.checked) ? '#6688BB' : '#FFFFFF');
	}
	updateFreight(id);
}

function updateFreight(id)
{
	var courier_id=getValue('freightquote['+id+'][product_id]');
	if(courier_id=='') return;
	var q_currency=getValue('freightquote['+id+'][quote_currency]');
	var i_currency=getValue('invoice_currency');
	var c_price=getValue('freightquote['+id+'][total_amount]');
	var cost_price=convertCurrency(c_price,q_currency,i_currency);
	var cost_au=convertCurrency(c_price,q_currency,'AUD');
	var s_price=getValue('freightquote['+id+'][sale_price]');
	var sell_price=convertCurrency(s_price,q_currency,i_currency);
	var sell_au=convertCurrency(s_price,q_currency,'AUD');
	setValue('ship_id',courier_id);
	setValue('ship_cost',Number(cost_price).toFixed(2));
	setValue('ship_costau',Number(cost_au).toFixed(2));
	setValue('supplier_ship_cost',Number(cost_price).toFixed(2));
	setValue('ship_online',Number(sell_price).toFixed(2));
	setValue('ship_offer',Number(sell_price).toFixed(2));
	setValue('ship_offerau',Number(sell_au).toFixed(2));
	setValue('ship_supplier_name',suppliers_list[courier_id]);
	var freightrow=getElement('product_ship_rowa');
	if(freightrow!=null) freightrow.style.display='';
	var freightrow1=getElement('supplier_ship_rowa');
	if(freightrow1!=null) freightrow.style.display='';
	var freightrow1=getElement('supplier_ship_rowb');
	if(freightrow1!=null) freightrow.style.display='';
	var product_id=courier_id;
	if(product_id.length<2) product_id='0'+product_id;
	setValue('ship_product_id','F'+product_id);
	setValue('supplier_ship_product_id','F'+product_id);
	setValue('ship_supplier_id',courier_id);
	var elems=document.getElementsByName('freightquote['+id+'][container_flags]');
	var type='';
	for(i=0;i<elems.length;i++)
	{
		if(elems[i].checked)
		{
			type=elems[i].value.toUpperCase();
			break;
		}
	}
	var cargoType=getValue('freightquote['+id+'][sea_air]');
	cargoType=cargoType.charAt(0).toUpperCase()+cargoType.substring(1);
	var ship_product_name=type+' '+cargoType+' Freight';
	setValue('ship_product_name',ship_product_name);
	setValue('supplier_ship_product_name',ship_product_name);
	setValue('ship_qty','1');
	setValue('ship_product_notes',((getElement('freightquote['+id+'][cost_flags][ins]').checked && getValue('freightquote['+id+'][ins_amount]')>0.0) ? 'Insurance Included' : ''));
	shipChange();
}

function checkIfPrefered(id)
{
	var obj=getElement('freightquote['+id+'][prefered]');
	if(obj.checked)
	getElement('freightquote['+id+'][header]').style.backgroundColor=((getElement('freightquote_div['+id+']').style.display=='none') ? "#6688BB" : "#FFEECC");
}

function changePorts(changed, type, id)
{
	var country_from=getValue('freightquote['+id+'][country_from]');
	var country_to=getValue('freightquote['+id+'][country_to]');
	var toUse=((changed=='from') ? country_from : country_to);
	var frontPart='freightquote['+id+'][';
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

function deleteQuote(qcount,id)
{
	var affectedQuotes=getValue('freightquote_affected').split(',');
	var newQuotes=Array();
	for(i=0;i<=affectedQuotes.length;i++)
	{
		if(i==affectedQuotes.length) newQuotes[i]=id;
		else if(affectedQuotes[i]!='') newQuotes[i]=affectedQuotes[i];
	}
	var newValue=newQuotes.join(',');
	setValue('freightquote_affected',newValue);
	theCell=getElement('freightquote['+qcount+'][header]');
	if(theCell) theCell.parentNode.deleteCell(theCell.sectionCellIndex);
	var theTable=getElement('freightquote_div['+qcount+']');
	if(theTable) theTable.parentNode.removeChild(theTable);
}

function shippingMethodChanged(value,toggle)
{
	toggleSection('show_others',toggle);
	var allowed=Array('pickup', 'customer', 'other');
	for(i=0;i<allowed.length;i++) toggleSection(allowed[i]+"Div", value==allowed[i]);
}
//]]>
</script>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionFreightQuote\');">Freight Quotation</a>') ?>
<div id="sectionFreightQuote">
<table class="wide"><tr><td style="width:50%;">
	<input type="radio" name="invoice[shipping_method]" value="pickup" onclick="shippingMethodChanged('pickup',false);"<?= ('pickup' == $default_selection) ? ' checked="checked"' : '' ?> /> Pickup<br />
	<input type="radio" name="invoice[shipping_method]" value="customer_forwarder"<?= ('customer_forwarders' == $default_selection) ? ' checked="checked"' : '' ?> onclick="shippingMethodChanged('customer',false);" /> Customer's Freight Company<br />
	<input type="radio" name="invoice[shipping_method]" value="other" onclick="shippingMethodChanged('other',true);"<?= ('other' == $default_selection) ? ' checked="checked"' : '' ?> /> HGR Freight Company
</td><td style="width:50%;<?= ('other' == $default_selection) ? '' : 'display:none;' ?>" id="show_others">
	<input type="radio" name="ship_method" value="courier" onclick="selectQuotation('Couriers');"<?= (!$hideCouriers) ? ' checked="checked"' : '' ?> /> Courier<br />
	<input type="radio" name="ship_method" value="sea" onclick="selectQuotation('SeaFreight');"<?= (!$hideSea) ? ' checked="checked"' : '' ?> /> Sea Freight<br />
	<input type="radio" name="ship_method" value="air" onclick="selectQuotation('AirFreight');"<?= (!$hideAir) ? ' checked="checked"' : '' ?> /> Air Freight
</td></tr></table><br />

<table class="wide unfinished">
	<tr><th>Date</th><th>Supplier</th><th>Type</th><th>From</th><th>To</th><th>Price</th><th>Status</th></tr>
	<tr style="text-align:center;" class="unfinished"><td>30/09/08</td><td>Joe's Freight</td><td>Sea Freight</td><td>China (Beijing)</td><td>Great Britan (UK) (Manchester)</td><td>2565.85 GBP</td><td>Not Confirmed</td></tr>
	<tr style="text-align:center;" class="unfinished"><td>28/09/08</td><td>Jimbo Jones Moving</td><td>Air Freight</td><td>China (Beijing)</td><td>Great Britan (UK) (Manchester)</td><td>1855.85 GBP</td><td>Confirmed</td></tr>
</table><br />

<input type="hidden" name="freightquote_affected" value="<?= implode(',', $invoice_freight_quotes) ?>" />
<div id="pickupDiv"<?= ('pickup' == $default_selection) ? '' : ' style="display:none"' ?>></div>
<div id="customerDiv"<?= ('customer_forwarders' == $default_selection) ? '' : ' style="display:none"' ?>></div>
<div id="otherDiv"<?= ('other' == $default_selection) ? '' : ' style="display:none"' ?>>
<?php
include __DIR__ . "/courier_quote_row.tpl.php";
include __DIR__ . '/aircargo_quote_table.tpl.php';
include __DIR__ . '/seacargo_quote_table.tpl.php';
?>
	<div id="sectionSeaFreight" style="<?= ($hideSea) ? 'display:none' : '' ?>">
		<?= formatHTMLTitle('Sea Freight Quotes', ((function_exists('seaquote_row')) ? '<a href="#" onclick="addSeaQuote();return false;">Add Seafreight Quote</a>' : '')); ?>
		<div id="sectionSeaFreight_quotes">
<?php
if (!count($sea_quotes)) {
    $sea_quotes = array(array());
}

foreach ($sea_quotes as $quote_id => $freightquote) {
    if (function_exists('seaquote_row')) {
        echo seaquote_row($qcount++, $freightquote);
    }
}

?>
		</div>
	</div>

	<div id="sectionAirFreight" style="<?= ($hideAir) ? 'display:none' : '' ?>">
		<?= formatHTMLTitle('Air Freight Quotes', ((function_exists('airquote_row')) ? '<a href="#" onclick="addAirQuote();return false;">Add Airfreight Quote</a>' : '')); ?>
		<div id="sectionAirFreight_quotes">
<?php
if (!count($air_quotes)) {
    $air_quotes = array(array());
}

foreach ($air_quotes as $quote_id => $freightquote) {
    if (function_exists('airquote_row')) {
        echo airquote_row($qcount++, $freightquote);
    }
}

?>
		</div>
	</div>
</div>
<?php
if (!$flag_done_freight_located) {
?>
	<br />
	<div>
		<table>
			<tr><td class="left_title">Tracking Number</td><td><input onchange="updateTrackingNum()" name="proforma_tracknumber" style="width:98%;" type="text" value="<?php hecho($invoice['proforma_tracknumber'])?>" /></td></tr>
			<tr>
				<td class="left_title" style="width:25%;">Freight Located</td>
<?php
$back_colour = ('y' == $invoice['ship_located']) ? '#00FF00' : '#FF0000';
?>
				<td colspan="2"><select name="ship_located" style="background-color:<?= $back_colour ?>;" onchange="updateSelect(this);return false;"><?= formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $invoice['ship_located']) ?></select></td>
			</tr>
<?php
if ('y' == $invoice['ship_located']) {
?>
			<tr style="color: red;font-weight:bold">
				<td class="left_title">Authorised</td>
<?php
$staff = 'Customer';
        if ($invoice['ship_staff_id'] > 0) {
            $staff = $all_staff_list[$invoice['ship_staff_id']];
        }

?>
				<td colspan="2"><i><?= $staff ?> - <?= date('d/m/y h:i', $invoice['ship_locate_time']) ?></i></td>
			</tr>
<?php
}
?>
		</table>
		<br />
		<center><input type="button" value="Update Invoice"<?= ($invoice_lock['staff_id'] == $staff_id || 'add' == $action) ? ' onclick="if(validateInvoice()) this.form.submit();"' : ' disabled="disabled"' ?> /></center>
	</div>
<?php
}
?>

</div><br /><br />
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionFreightQuote_hideNote">
	Freight Quotation can be viewed by Clicking "Freight Quotation" above<br />
</div>

<!-- /freight_quotes.tpl -->

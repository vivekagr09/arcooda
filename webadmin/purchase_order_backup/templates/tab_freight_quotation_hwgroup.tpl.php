<!-- tab_freight_quotation_hwgroup.tpl -->

<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;"><input type="checkbox" name="shipping_method_other"<?= ('FreightQuotationHwgroup' == $default_tab) ? ' checked="checked"' : '' ?> onchange="freightQuotationChangeShippingMethod('other')" /></td>
		<td>Use this shipping method</td>
	</tr>
</table>
<br />

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
?>
<div align="center">
	<input type="button" value="Courier Quotes" onclick="selectQuotation('Couriers');" />
	<input type="button" value="Sea Freight Quotes" onclick="selectQuotation('SeaFreight');" />
	<input type="button" value="Air Freight Quotes" onclick="selectQuotation('AirFreight');" />
</div>
<?php
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
foreach ($invoice_freight_quotes as $quote_id) {
    if (forwardersQuotesGetValue($quote_id, 'prefered_flag') == 'y') {
        $hideCouriers = true;
    }
}

?>
<br />
<div id="sectionCouriers" style="<?= ($hideCouriers) ? 'display:none' : '' ?>">
	<?= formatHTMLTitle('Courier Quotation') ?>
	<table class="wide">
<?php
if (count($selected_courier)) {
    $courier_name = $invoice['ship_product_name'] . (($invoice['ship_id'] > 100) ? ' (Personal Account)' : '');
    $price        = ('USD' == $invoice['invoice_currency']) ? $selected_courier['sell_price'] : $selected_courier['sell_priceau'];
    if ('AUD' != $invoice['invoice_currency'] && 'USD' != $invoice['invoice_currency']) {
        $price = convertCurrency($price, 'USD', $invoice['invoice_currency']);
    }

    $insurance = ('y' == $selected_courier['insurance']) ? 'Yes' : (('n' == $selected_courier['insurance']) ? 'No' : htmlspecialchars($selected_courier['insurance']));
?>
	<tr>
		<td class="left_title" style="width:25%">Courier</td>
		<td><input style="width:98%" type="text" name="ship_courier_name" value="<?php hecho($courier_name)?>" readonly="readonly" /></td>
	</tr>
	<tr>
		<td class="left_title">Sale Price :</td>
		<td><input style="width:98%" name="ship_courier_price" value="<?php hecho($invoice['ship_offer'] . ' ' . $invoice['invoice_currency'])?>" readonly="readonly" /></td>
	</tr>
	<tr>
		<td class="left_title">Transit Time :</td>
		<td><input style="width:98%" type="text" name="ship_courier_transit" value="<?php hecho($selected_courier['del_time'])?>" readonly="readonly" /></td>
	</tr>
	<tr>
		<td class="left_title">Insurance :</td>
		<td><input style="width:98%" type="text" name="ship_courier_insurance" value="<?= $insurance ?>" readonly="readonly" /></td>
	</tr>
	<tr><td class="left_title" colspan="2" style="text-align:center"><input type="button" value=" View Other Courier Methods " onclick="toggleSection('other_couriers')"></td></tr>
<?php
}
?>
	<tbody id="other_couriers" style="<?= count($selected_courier) ? "display:none" : "" ?>">
		<tr>
			<td class="left_title" style="width:25%">Shipping Via</td>
			<td colspan="2"><input type="button" value="Search Couriers" onclick="freightCourierSearch(this.form);" /> - Results for <span name="freightcourier_locality">.</span> <span name="freightcourier_postcode">.</span> <span name="freightcourier_state">.</span> <span name="freightcourier_country">.</span></td>
		</tr>
		<tr>
			<td class="left_title"><iframe name="freight_search" style="width:16px;height:16px;visibility:hidden;"></iframe></td>
			<td colspan="2">
				<table name="freight_courier_table" class="spacetable" style="width:100%;text-align:center;">
					<tr>
						<th></th>
						<th>Service</th>
						<th>Delivery Time</th>
						<th>Track</th>
						<th>Insure</th>
						<th>Tail Gate</th>
						<th>Cost<br />Price<br />(<span name="product_currency"><?php hecho($invoice['invoice_currency'])?></span>)</th>
						<th>Sell<br />Price<br />(<span name="product_currency"><?php hecho($invoice['invoice_currency'])?></span>)</th>
<?php
if ('AUD' != $invoice['invoice_currency']) {
?>
						<th>Cost<br />Price<br />(AUD)</th>
						<th>Sell<br />Price<br />(AUD)</th>
<?php
}
?>
					</tr>
					<tbody id="freight_couriers" name="freight_couriers">
<?php
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
$count        = 0;
if (is_array($invoice_courier_quotes)) {
    $flagFound = false;
    foreach ($invoice_courier_quotes as $quote) {
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
				</table>
<?php
if (count($couriers_list) > 0) // || count($member_couriers) > 0)
{
?>
				<input type="button" onclick="clearFreightCouriers();" value="Clear" />
<?php
}
?>
			</td>
		</tr>
	</tbody>
<?php
$yesno = array('y' => 'Yes', 'n' => 'No');
?>
		<tr>
			<td class="left_title">Invoice Date</td>
			<td><input name="proforma_date" id="proforma_date" type="text" size="24" readonly="readonly" value="<?= $invoice['proforma_date'] ?>" /><input type="button" value=" V " onclick="gfPop.fPopCalendar(this.form.proforma_date);" /><input type="button" value="Clear" onclick="setValue('proforma_date','');" /></td>
		</tr>
		<tr id="account_num_tr" name="account_num_tr" style="<?= ($member_selected) ? '' : 'display:none' ?>">
			<td class="left_title">Shipping Account Number</td>
			<td><input name="ship_account_num" style="width:98%;" type="text" value="<?php hecho($invoice['ship_account_num'])?>" /></td>
		</tr>
		<tr>
			<td class="left_title">Goods Packed By Me</td>
			<td><select onchange="updateGoodsExtra('packed')" name="packed_by"><?= formatHTMLOptions($yesno, $invoice['packed_by']) ?></select></td>
		</tr>
		<tr id="packed_by_tr" name="packed_by_tr" style="<?= ('y' == $invoice['packed_by'] || '' == $invoice['packed_by']) ? 'display:none' : '' ?>">
			<td class="left_title">&nbsp;</td>
			<td><select name="goods_packed_by"><?= formatHTMLOptions($active_staff_list, $invoice['goods_packed_by']) ?></select></td>
		</tr>
		<tr>
			<td class="left_title">Goods Shipped By Me</td>
			<td><select onchange="updateGoodsExtra('shipped')" name="shipped_by"><?= formatHTMLOptions($yesno, $invoice['shipped_by']) ?></select></td>
		</tr>
		<tr id="shipped_by_tr" name="shipped_by_tr" style="<?= ('y' == $invoice['shipped_by'] || '' == $invoice['shipped_by']) ? 'display:none' : '' ?>">
			<td class="left_title">&nbsp;</td>
			<td><select name="goods_shipped_by"><?= formatHTMLOptions($active_staff_list, $invoice['goods_shipped_by']) ?></select></td>
		</tr>
		<tr id="process_invoice_tr" name="process_invoice_tr" style="display:none">
			<td class="left_title">Process Invoice</td>
			<td><input type="radio" name="process_invoice" value="n" checked>&nbsp;No&nbsp;<input type="radio" name="process_invoice" value="y">&nbsp;Yes&nbsp;</td>
		</tr>
	</table>
</div>
<?php
$suppliers_list = forwardersQuotesGetOptionList('product_id');
$prefer         = array();
$count          = 1;
foreach ($invoice_freight_quotes as $quote_id) {
    $prefer[$count] = $quote_id;
    $count++;
}
?>
<div id="sectionFreightQuotation">
<script type="text/javascript">
//<![CDATA[
var freightquote_count=<?= max(100, count($invoice_freight_quotes)) ?>;
var countStart=freightquote_count;
var prefer = <?php jecho($prefer)?>;
var suppliers_list = <?php jecho($suppliers_list)?>;
function updatePrefered(id)
{
	var checked=getElement('freightquote['+id+'][prefered]').checked;
	for(key in prefer)
	{
		var obj=getElement('freightquote['+key+'][prefered]');
		obj.checked=((key != id) ? false : checked);
		obj.parentNode.style.backgroundColor=((obj.checked) ? '#6688BB' : '#FFFFFF');
		getElement('freightquote['+key+'][header]').style.backgroundColor="#FFEECC";
	}
	for(i=countStart;i<freightquote_count;i++)
	{
		var obj=getElement('freightquote['+i+'][prefered]');
		obj.checked=((i != id) ? false : checked);
		obj.parentNode.style.backgroundColor=((obj.checked) ? '#6688BB' : '#FFFFFF');
	}
	updateFreight(id);
}
function updateFreight(id)
{
	var checked=getElement('freightquote['+id+'][prefered]').checked;
	if(checked)
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
		freightrow=getElement('product_ship_rowb');
		if(freightrow!=null) freightrow.style.display='';
		freightrow=getElement('supplier_ship_rowa');
		if(freightrow!=null) freightrow.style.display='';
		freightrow=getElement('supplier_ship_rowb');
		if(freightrow!=null) freightrow.style.display='';
		var product_id=courier_id;
		if(product_id.length<2) product_id='0'+product_id;
		setValue('ship_product_id','F'+product_id);
		setValue('supplier_ship_product_id','F'+product_id);
		setValue('ship_supplier_id',courier_id);
		var elems=document.getElementsByName('freightquote['+id+'][container_flags]');
		var type='';
		for(i=0;i<elems.length;i++) if(elems[i].checked)
		{
			type=elems[i].value.toUpperCase();
			break;
		}
		var cargoType=getValue('freightquote['+id+'][sea_air]');
		cargoType=cargoType.charAt(0).toUpperCase()+cargoType.substring(1);
		var ship_product_name=type+' '+cargoType+' Freight';
		setValue('ship_product_name',ship_product_name);
		setValue('supplier_ship_product_name',ship_product_name);
		setValue('ship_qty','1');
		setValue('ship_product_notes',((getElement('freightquote['+id+'][cost_flags][ins]').checked && getValue('freightquote['+id+'][ins_amount]')>0.0) ? 'Insurance Included' : ''));
	}
	else
	{
		setValue('ship_id','');
		setValue('ship_cost','0.00');
		setValue('ship_costau','0.00');
		setValue('supplier_ship_cost','0.00');
		setValue('ship_online','0.00');
		setValue('ship_offer','0.00');
		setValue('ship_offerau','0.00');
		setValue('ship_supplier_name','');
		setValue('ship_product_id','');
		setValue('supplier_ship_product_id','');
		setValue('ship_supplier_id','');
		setValue('ship_product_name','');
		setValue('supplier_ship_product_name','');
		setValue('ship_qty','0');
		setValue('ship_product_notes','');
		var freightrow=getElement('product_ship_rowa');
		if(freightrow!=null) freightrow.style.display='none';
		freightrow=getElement('product_ship_rowb');
		if(freightrow!=null) freightrow.style.display='none';
		freightrow=getElement('supplier_ship_rowa');
		if(freightrow!=null) freightrow.style.display='none';
		freightrow=getElement('supplier_ship_rowb');
		if(freightrow!=null) freightrow.style.display='none';
	}
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
//]]>
</script>

<?php
include __DIR__ . '/aircargo_quote_table.tpl.php';
include __DIR__ . '/seacargo_quote_table.tpl.php';
?>
<input type="hidden" name="freightquote_affected" value="<?= implode(',', $invoice_freight_quotes) ?>" />
<?php
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
<div id="sectionSeaFreight" style="<?= ($hideSea) ? 'display:none' : '' ?>">
<?= formatHTMLTitle('Sea Freight Quotes'); ?>
<div id="sectionSeaFreight_quotes">
<?php
foreach ($sea_quotes as $quote_id => $freightquote) {
    if (function_exists('seaquote_row')) {
        echo seaquote_row($qcount++, $freightquote);
    }
}

?>
</div>
<?php
if (function_exists('seaquote_row')) {
    echo '<input type="button" value="Add Seafreight Quote" onclick="addSeaQuote();" />';
}

?>
</div>
<div id="sectionAirFreight" style="<?= ($hideAir) ? 'display:none' : '' ?>">
<?= formatHTMLTitle('Air Freight Quotes'); ?>
<div id="sectionAirFreight_quotes">
<?php
foreach ($air_quotes as $quote_id => $freightquote) {
    if (function_exists('airquote_row')) {
        echo airquote_row($qcount++, $freightquote);
    }
}

?>
</div>
<?php
if (function_exists('airquote_row')) {
    echo '<input type="button" value="Add Airfreight Quote" onclick="addAirQuote();" />';
}

?>
</div>

<!-- /tab_freight_quotation_hwgroup.tpl -->

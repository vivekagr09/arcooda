<!-- freight_quotation.tpl -->

<?php
$suppliers_list = getFreightForwarderList();
$prefer         = array();
$count          = 1;
foreach ($invoice_freight_quotes as $quote_id) {
    $prefer[$count] = $quote_id;
    $count++;
}
?>

<!--<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionFreightQuotation\');">Freight Quotation</a>') ?>-->

<script type="text/javascript">
//<![CDATA[
var freightquote_count=<?= max(100, count($invoice_freight_quotes)) ?>;
var countStart=freightquote_count; // This is required for the updatePrefered function...
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
	var cost_us=convertCurrency(c_price,q_currency,'USD');
	var s_price=getValue('freightquote['+id+'][sale_price]');
	var sell_price=convertCurrency(s_price,q_currency,i_currency);
	var sell_au=convertCurrency(s_price,q_currency,'AUD');
	var sell_us=convertCurrency(s_price,q_currency,'USD');

	setValue('ship_id',courier_id);
	setValue('ship_cost',Number(cost_price).toFixed(2));
	setValue('ship_costau',Number(cost_au).toFixed(2));
	setValue('ship_costus',Number(cost_us).toFixed(2));
	setValue('supplier_ship_cost',Number(cost_price).toFixed(2));
	setValue('ship_online',Number(sell_price).toFixed(2));
	setValue('ship_offer',Number(sell_price).toFixed(2));
	setValue('ship_offerau',Number(sell_au).toFixed(2));
	setValue('ship_offerus',Number(sell_us).toFixed(2));
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
//]]>
</script>

<div id="sectionFreightQuotation">
<?php
include __DIR__ . '/aircargo_quote_table.tpl.php';
include __DIR__ . '/seacargo_quote_table.tpl.php';

?>

<input type="hidden" name="freightquote_affected" value="<?= implode(',', $invoice_freight_quotes) ?>" /> <!-- Hidden field used to store the deleted quotes.. -->
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

if (function_exists('seaquote_row')) {
?>
	<div id="sectionSeaFreight" style="<?= ($hideSea) ? 'display:none' : '' ?>">
		<?= formatHTMLTitle('Sea Freight Quotes') ?>
		<div id="sectionSeaFreight_quotes">
<?php
foreach ($sea_quotes as $quote_id => $freightquote) {
        echo seaquote_row($qcount++, $freightquote);
    }

?>
		</div>
		<input type="button" value="Add Seafreight Quote" onclick="addSeaQuote()" />
	</div>
<?php
}

if (function_exists('airquote_row')) {
?>
	<div id="sectionAirFreight" style="<?= ($hideAir) ? 'display:none' : '' ?>">
		<?= formatHTMLTitle('Air Freight Quotes') ?>
		<div id="sectionAirFreight_quotes">
<?php
foreach ($air_quotes as $quote_id => $freightquote) {
        echo airquote_row($qcount++, $freightquote);
    }

?>
		</div>
		<input type="button" value="Add Airfreight Quote" onclick="addAirQuote();" />
	</div>
<?php
}

if (!$flag_done_freight_located) {
?>
	<br />
	<div>
		<table class="wide">
			<tr>
				<td class="left_title">Tracking Number</td>
				<td><input onchange="updateTrackingNum()" name="proforma_tracknumber" style="width:98%;" type="text" value="<?php hecho($invoice['proforma_tracknumber'])?>" /></td>
			</tr>
			<tr>
				<td class="left_title" style="width:25%;">Freight Located</td>
<?php
$back_colour = ('y' == $invoice['ship_located']) ? '#00FF00' : '#FF0000';
?>
				<td colspan="2">
					<select name="ship_located" style="background-color:<?= $back_colour ?>;" onchange="updateSelect(this);return false;"><?= formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $invoice['ship_located']) ?></select>
				</td>
			</tr>
<?php
if ('y' == $invoice['ship_located']) {
?>
			<tr style="color: red;font-weight:bold">
				<td class="left_title">Authorised</td>
				<?php $staff = 'Customer';if ($invoice['ship_staff_id'] > 0) {
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
		<center>
			<input type="button" value="Update Invoice"<?= ($invoice_lock['staff_id'] == $staff_id || 'add' == $action) ? ' onclick="if(validateInvoice()) this.form.submit();"' : ' disabled="disabled"' ?> />
		</center>
	</div>
<?php
}
?>

	<br /><br />
</div>

<!-- /freight_quotation.tpl -->

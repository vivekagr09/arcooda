<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<script type="text/javascript">
//<![CDATA[
var fqcount=0;
function courierQuoteAdd()
{
	var theElem=getElement('div_freightquotes');
	if(theElem==null) return;
	var newElem=document.createElement('DIV');
	var shipping_weight=Number(getValue('freight_weight'));
	newElem.innerHTML=<?=preg_replace('/\[[A-Z_]*\]/', '', courier_quote_javascript(array('ID' => "'+fqcount+'", 'ACTION' => 'add', 'SHIP_WEIGHT' => "'+shipping_weight+'", 'con_flag' => '0')))?>;
	theElem.appendChild(newElem);
	var from=getValue('supply_from');
	var to=getValue('ship_country');
	setValue('courier_quote_from['+fqcount+']',from);
	setValue('courier_quote_to['+fqcount+']',to);
	fqcount++;
}
function searchCourierQuote(quote_id)
{
	var ship_from=getValue('courier_quote_from['+quote_id+']');
	var ship_to=getValue('courier_quote_to['+quote_id+']');
	var weight=getValue('courier_quote_weight['+quote_id+']');
	var url='/webadmin/courier/?action=quote_search&return_message='+quote_id;
	url+='&ship_from='+escape(ship_from)+'&ship_to='+escape(ship_to)+'&ship_weight='+escape(ship_weight);
	var features='scrollbars, resizable, height=400, width=600';
	window.open(url,'courier_popup',features);
}
function setCourierQuoteData(data)
{
	if(data==null || data['return_message']==null) return;
	var quote_id=data['return_message'];
	var theElem=getElement('courier_quote_table['+quote_id+']');
	if(theElem==null) return;

	for(var i=0;i<data['quotes'].length;i++)
	{
		alert('fssfdpisdhosi');
	}
}
function courierQuoteUpdateTotal(fq_id)
{
	var theElem1=document.getElementsByName('courier_quote_flag['+fq_id+']');
	var theElem2=document.getElementsByName('courier_quote_quote['+fq_id+']');
	var total=Number(0.0);
	for(var i=0;i<theElem1.length;i++)
		if(theElem1[i].checked)
			total+=Number(theElem2[i].value);
	setValue('courier_quote_total['+id+']',total.toFixed(2));
}
//]]>
</script>
<script type="text/javascript">
//<![CDATA[
var courier_list=Array();
function toggleCourierCargoConfirm(id,yesorno)
{
	toggleSection('freight_confirm_thing['+id+']');
	if(getValue('freight_quote_action['+id+']')=='add')
		toggleSection('freight_courier_tracking_upload['+id+']');
}

function courierUpdateTotal(c_id,id)
{
	var total=Number(0.0);
	var othFlag = getElement('freight_courier_flag_oth['+id+']');
	if(othFlag && othFlag.checked)
		total += Number(getValue('freight_courier_total_oth['+id+']'));

	if(c_id != '')
	{
		var theFlag = getElement('freight_courier_find_flag['+id+']['+c_id+']');
		if(theFlag && theFlag.checked)
			total += Number(getValue('freight_courier_find_total['+id+']['+c_id+']'));
	}
	else
		for(var i=0;i<courier_list.length;i++)
		{
			var flag = getElement('freight_courier_find_flag['+id+']['+courier_list[i]+']');
			if(flag && flag.checked)
				total += Number(getValue('freight_courier_find_total['+id+']['+courier_list[i]+']'));
		}
	setValue('freight_courier_total_tot['+id+']',total.toFixed(2));
}

function selectCourier(c_id,id)
{
	var theFlag = getElement('freight_courier_find_flag['+id+']['+c_id+']');
	var str = '';
	if(theFlag.checked)
		theFlag.parentNode.parentNode.style.fontWeight='bold';
	for(i=0;i<courier_list.length;i++)
		if(c_id != courier_list[i])
		{
			var obj = getElement('freight_courier_find_flag['+id+']['+courier_list[i]+']');
			if(obj)
			{
				obj.parentNode.parentNode.style.fontWeight = 'normal';
				obj.checked = false;
			}
		}
	updateCourierTotal(c_id,id);
}

function findFreight(id)
{
	var from = getValue('freight_courier_from['+id+']');
	var to = getValue('freight_courier_to['+id+']');
	var weight = getValue('freight_courier_weight['+id+']');
	var frame = getElement('freight_calculation');

	var empty_top = 'freight_quote_empty_top['+id+']';
	var empty_bot = 'freight_quote_empty_bottom['+id+']';
	var theTable = getElement('freight_courier_table['+id+']');
	var insIndex = deleteUnwantedRows(theTable,empty_top,empty_bot);
	var elem = theTable.insertRow(insIndex);
	addTableCell(elem,'');
	var right = addTableCell(elem,'<b>Please Wait Data is being calculated. (Do not click Calculate Button Again)</b>');
	right.style.textAlign="center";
	addTableCell(elem,'<span style="visibility:hidden;"><b>Cost(USD)<input type="text">Approx. Delivery</b></span>');

	frame.src = "findfreight.php?site_id=3&from="+from+"&to="+to+"&weight="+weight+"&currency=USD&id="+id;
	updateCourierTotal('',id);
}

function deleteUnwantedRows(theTable,top_row,bottom)
{
	var top = false;
	var bot = false;
	var insIndex = 0; // the index of the row where the data will be inserted.
	var rowIndices = Array();

	var count = 0;
	for(i=0;i<theTable.rows.length;i++)
	{
		if(!top)
		{
			if(theTable.rows[i] == getElement(top_row))
			{
				insIndex = theTable.rows[i].rowIndex + 1;
				top = true;
			}
		}
		else if(!bot)
			{
				if(theTable.rows[i] == getElement(bottom))
					bot = true;
				else
				{
					rowIndices[count] = theTable.rows[i].rowIndex;
					count++;
				}
			}
	}
	for(i=0;i<count;i++)
		theTable.deleteRow(rowIndices[0]);

	return insIndex;
}

function addFreight(data,id,ticked)
{
	if(data.length)
		for(i=0;i<data['service_id'].length;i++)
			courier_list[i] = data['service_id'][i];
	var empty_top = 'freight_quote_empty_top['+id+']';
	var empty_bot = 'freight_quote_empty_bottom['+id+']';
	var theElem = getElement(empty_bot);
	var theTable = getElement('freight_courier_table['+id+']');

	var insIndex = deleteUnwantedRows(theTable,empty_top,empty_bot);

	if(data.length)
	{
		var theader = document.createElement('TR');
		var elem = theTable.insertRow(insIndex);
		addTableCell(elem,'');
		var right = addTableCell(elem,'<b>Courier &nbsp;&nbsp;&nbsp;</b>');
		right.style.textAlign="right";
		addTableCell(elem,'<b>Cost(USD)<span style="visibility:hidden;"><input type="text"></span>Approx. Delivery</b>');
		for(i=0;i<data['Customer'].length;i++)
		{
			var newRow = theTable.insertRow(insIndex+1+i);
			addTableCell(newRow,'');
			if(data['service_id'][i] == ticked)
			{
				var cell = addTableCell(newRow,''+data['Service'][i]+' <input type="checkbox" onClick="selectCourier('+data['service_id'][i]+','+id+')" name="freight_courier_find_flag['+id+']['+data['service_id'][i]+']" checked>');
				cell.style.textAlign="right";
				cell.parentNode.style.fontWeight='bold';
			}
			else
			{
				var cell = addTableCell(newRow,''+data['Service'][i]+' <input type="checkbox" onClick="selectCourier('+data['service_id'][i]+','+id+')" name="freight_courier_find_flag['+id+']['+data['service_id'][i]+']">');
				cell.style.textAlign="right";
			}
			var delivery = data['Delivery'][i];
			var sub_del = delivery.substring(0,delivery.indexOf('D'))
			var inStuff = '<input type="text" name="freight_courier_find_total['+id+']['+data['service_id'][i]+']" value="'+data['Customer'][i]+'" readonly><span style="visibility:hidden;"><input type="text" size=5></span><input type="hidden" name="freight_courier_delivery['+id+']['+data['service_id'][i]+']" value="'+sub_del+'">'+delivery;
			addTableCell(newRow,inStuff);
		}
	}
	else
	{
		var theader = document.createElement('TR');
		var elem = theTable.insertRow(insIndex);
		addTableCell(elem,'');
		var right = addTableCell(elem,'<b>No Freight Data was found.</b>');
		right.style.textAlign="center";
		addTableCell(elem,'<span style="visibility:hidden;"><b>Cost(USD)<input type="text">Approx. Delivery</b></span>');
	}
	updateCourierTotal('',id);
}

function courierPreferClick(id)
{
	var theElem=getElement('freight_quote_prefered['+id+']');
	if(theElem==null) return;
	if(theElem.checked==true)
	{
		setValue('freight_cost',getValue('freight_courier_total_tot['+id+']'));
		setValue('freight_offer',getValue('freight_courier_sale_price['+id+']'));
		setValue('product_freightcost',getValue('freight_courier_total_tot['+id+']'));
		setValue('product_freightoffer1',toCurrency(getValue('freight_courier_sale_price['+id+']'),'USD',getValue('inquiry_currency')));
		setValue('product_freightoffer2',toCurrency(getValue('freight_courier_sale_price['+id+']'),'USD',getValue('inquiry_currency')));
		setValue('product_freightofferus',getValue('freight_courier_sale_price['+id+']'));
		setValue('freight_type','Courier');
		setValue('freight_name','Courier Freight Costs');
		buildCountryList(getElement('freight_from_port'),getValue('freight_courier_from['+id+']'));
		buildCountryList(getElement('freight_to_port'),getValue('freight_courier_to['+id+']'));
		for(i=0;i<courier_list.length;i++)
		{
			var obj = getElement('freight_courier_find_flag['+id+']['+courier_list[i]+']');
			if(obj && obj.checked)
				setValue('transit_time',getValue('freight_courier_delivery['+id+']['+courier_list[i]+']'));
		}
	}
	theElem.parentNode.style.backgroundColor=((theElem.checked)?'#6688BB':'#FFFFFF');
	prefer(id);
	updateTotals();
}
//]]>
</script>
<?php
function courier_quote_javascript($quotedata)
{
    $safety_first = array('ID', 'QUOTE_ID', 'SHIP_WEIGHT', 'CONFIRM_FLAG', 'OTH_TOTAL', 'TOT_TOTAL', 'SAL_TOTAL', 'PREFERED_FLAG', 'PREFER_HIGHLIGHT', 'NOTES', 'COURIER_ID', 'SHOW_LINK', 'SHOW_HREF', 'TRACKING_NUM', 'NOTES_HIGHLIGHT');

    foreach ($safety_first as $key) {
        if (!isset($quotedata[$key])) {
            $quotedata[$key] = '';
        }
    }

    if (0 == $quotedata['con_flag']) {
        $quotedata['CONFIRM_FLAG'] = ' style="display:none;"';
    }

    $outstring = formatJSString(courier_quote_raw($quotedata));
    foreach ($quotedata as $key => $value) {
        $outstring = str_replace('[' . strtoupper($key) . ']', $value, $outstring);
    }

    return $outstring;
}

function courier_quote_html($quotedata)
{
    $safety_first = array('ID', 'QUOTE_ID', 'SHIP_WEIGHT', 'CONFIRM_FLAG', 'OTH_TOTAL', 'TOT_TOTAL', 'SAL_TOTAL', 'PREFERED_FLAG', 'PREFER_HIGHLIGHT', 'NOTES', 'COURIER_ID', 'SHOW_LINK', 'SHOW_HREF', 'TRACKING_NUM', 'NOTES_HIGHLIGHT');

    foreach ($safety_first as $key) {
        if (!isset($quotedata[$key])) {
            $quotedata[$key] = '';
        }
    }

    if (0 == $quotedata['con_flag']) {
        $quotedata['CONFIRM_FLAG'] = ' style="display:none;"';
    }

    if ('checked' == $quotedata['prefered_flag']) {
        $quotedata['PREFER_HIGHLIGHT'] = 'background-color:#6688BB;';
    }

    if ('' == $quotedata['tracking_file']) {
        $quotedata['SHOW_LINK'] = 'display:none;';
    } else {
        $quotedata['SHOW_HREF'] = 'showAttachment.php?id=' . $quotedata['id'] . '&method=Courier';
    }

    if ('' != $quotedata['notes']) {
        $quotedata['NOTES_HIGHLIGHT'] = 'background-color:#66BB88;';
    }

    $outstring = courier_quote_raw($quotedata);
    foreach ($quotedata as $key => $value) {
        $outstring = str_replace('[' . strtoupper($key) . ']', $value, $outstring);
    }

    return $outstring;
}

function courier_quote_raw($quotedata)
{
    ob_start();
?>
<table class="wide">
	<tr>
		<th colspan="4" id="courier_quote_header[[ID]]">
			Courier Quote
			<a href="javascript:toggleSection('courier_quote_table[[ID]]');"><small>Expand/Collapse</small></a>
		</th>
	</tr>
</table>
<table class="wide" id="courier_quote_table[[ID]]">
	<tr class="alt">
		<td class="left_title">Ship From</td>
		<td>
			<select style="width:auto;" name="courier_quote_from[[ID]]">
				<option value="">Not Selected</option>
				<?=formatHTMLOptions(getCountryList(), $quotedata['from'])?>
			</select>
			<a href="/webadmin/world/seaport/seaports.php" target="_blank">Add/Edit</a>
		</td>
	</tr>
	<tr>
		<td class="left_title">Ship To</td>
		<td>
			<select name="courier_quote_to[[ID]]" style="width:auto;">
				<option value="">Not Selected</option>
				<?=formatHTMLOptions(getCountryList(), $quotedata['to'])?>
			</select>
			<a href="/webadmin/world/seaport/seaports.php" target="_blank">Add/Edit</a>
		</td>
	</tr>
	<tr class="alt">
		<td class="left_title">Shipping Weight</td>
		<td><input name="courier_quote_weight[[ID]]" type="text" value="[SHIP_WEIGHT]" readonly="readonly" /> kgs</td>
	</tr>
	<tr>
		<td class="left_title"></td>
		<td><input type="button" value="Calculate" onclick="javascript:searchCourierQuote([ID]);" /></td>
	</tr>

	<!-- Insert	quotes here -->

	<tr>
		<td class="left_title">Other</td>
		<td style="text-align:center;">
			<input name="courier_quote_flag_other[[ID]]" onclick="javascript:courierQuoteUpdateTotal([ID],'[COURIER_ID]');" type="checkbox"[OTHER_FLAG] />
			<input name="courier_quote_total_other[[ID]]" type="text" value="[TOTAL_OTHER]" onchange="javascript:courierQuoteUpdateTotal([ID],'[COURIER_ID]');" />
		</td>
	</tr>
	<tr class="alt">
		<td class="left_title">TOTAL COST</td>
		<td style="text-align:center;">
			<span style="visibility:hidden;"><input type="checkbox" /></span>
			<input name="courier_quote_total_total[[ID]]" readonly="readonly" type="text" value="[TOTAL_TOTAL]" />
		</td>
	</tr>
	<tr>
		<td class="left_title">SALE PRICE</td>
		<td style="text-align:center;">
			<span style="visibility:hidden;"><input type="checkbox" /></span>
			<input name="courier_quote_total_sale[[ID]]" type="text" value="[TOTAL_SALE]" />
		</td>
	</tr>
	<tr class="alt">
		<td class="left_title">General Notes</td>
		<td style="text-align:center;[NOTES_HIGHLIGHT]"><input type="button" onclick="javascript:toggleSection('courier_quote_notes[[ID]]');" value="Expand/Collapse" /></td>
	</tr>
	<tr>
		<td class="left_title"></td>
		<td><textarea name="courier_quote_notes[[ID]]" style="display:none;" rows="20" onkeyup="javascript:textareaAutoSize(this,1,20);">[NOTES]</textarea></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:center;border:1px solid grey;padding:8px 8px 8px 8px;[PREFER_HIGHLIGHT]"><input type="checkbox" name="freight_quote_selected[[ID]]" onclick="javascript:freightQuotePreferClick([ID]);"[PREFERED_FLAG] />PREFERRED</td>
	</tr>
	<tr>
		<td class="left_title">Confirm this Booking</td>
		<td><select style="width:auto;" name="freight_quote_confirm[[ID]]" onchange="javascript:freightQuoteToggleConfirm([ID],this.value);">
			<?=formatHTMLOptions(array('0' => 'No', '1' => 'Yes'), $quotedata['con_flag'])?>
		</select></td>
	</tr>
	<tr>
		<td colspan="2" id="freight_confirm_thing[[ID]]" <?=$quotedata['CONFIRM_FLAG']?>><table style="width:100%;"><tr>
			<td class="left_title">Tracking Number</td>
			<td>
				<input type="text" name="courier_quote_tracking_num[[ID]]" value="[TRACKING_NUM]" />
				<span id="courier_quote_tracking_file[[ID]]">
					<input type="button" value="Upload" onClick="addAttachments([ID],'Courier')"/>
					<a id="freight_quote_show_link[[ID]]" href="[SHOW_HREF]" style="[SHOW_LINK]" target="_blank" />Show</a>
				</span>
			</td>
		</tr></table></td>
	</tr>
</table>
<?php
return ob_get_clean();
}
?>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

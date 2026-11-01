<!-- aircargo_quote_table.tpl -->

<?php
moduleLoad('invoice');

if (is_array($suppliers_list) && count($suppliers_list)) {
    $air_supplier_list = $suppliers_list;
} else {
    $air_supplier_list = getFreightForwarderList();
}
//forwardersQuotesGetOptionList('air_product_id');
function airquote_row($qcount = '[QUOTE_COUNT]', $quote = array())
{
    global $invoice, $air_supplier_list;

    $alt          = true;
    $airports     = getairportslist();
    $country_from = 'HK';
    if ('' != $invoice['supply_from']) {
        $country_from = $invoice['supply_from'];
    }

    if ('' != $quote['country_from']) {
        $country_from = $quote['country_from'];
    }

    $country_to = $invoice['ship_country'];
    if ('' != $quote['country_to']) {
        $country_to = $quote['country_to'];
    }

    $ports_from = $airports[$invoice['supply_country']];
    if ('' != $quote['country_from']) {
        $ports_from = $airports[$quote['country_from']];
    }

    $ports_to = $airports[$invoice['ship_country']];
    if ('' != $quote['country_to']) {
        $ports_to = $airports[$quote['country_to']];
    }

    $ship_weight = $invoice['ship_weight'];
    if (0 != $quote['shipping_weight']) {
        $ship_weight = $quote['shipping_weight'];
    }

    $ship_cubic = $invoice['ship_cubic'];
    if (0 != $quote['cubic_weight']) {
        $ship_cubic = $quote['cubic_weight'];
    }

    $post_name = 'freightquote[' . $qcount . ']';

    $cost_fields = array('wgt' => 'Air Freight', 'thc' => 'THC/ORC', 'doc' => 'Document Charge', 'awb' => 'AWB', 'exp' => 'Export Licence Fee', 'han' => 'Handling Charge', 'tru' => 'Trucking Charge', 'cra' => 'Crating Charge', 'fum' => 'Fumigation', 'com' => 'Commodity Inspection', 'cus' => 'Customs Inspection', 'ins' => 'Insurance Charge', 'ori' => 'Certificate Origin', 'oth' => 'Other');

    $terms = array(
        'thc' => 'Terminal Handling Charges. Charges for the container handling.',
        'doc' => 'Document Charge',
        'awb' => 'The forwarding agreement or carrying agreement between shipper and air carrier and is issued only in nonnegotiable form.',
        'exp' => 'Export Licence Fee',
        'han' => 'Handling Charge',
        'tru' => 'Trucking Charge',
        'cra' => 'Crating Charge',
        'fum' => 'Fumigation',
        'com' => 'Commodity Inspection',
        'cus' => 'Customs Inspection',
        'ins' => 'Insurance Charge',
        'ori' => 'A certified document showing the origin of goods; used in international commerce.',
        'oth' => 'Other Charges',
    );

    $all_boxes = true;
    foreach ($cost_fields as $key => $name) {
        if (strpos($quote['cost_flags'], $key) === false) {
            $all_boxes = false;
        }
    }

    $direct_checked   = ('direct' == $quote['container_flags']) ? 'checked="checked"' : '';
    $indirect_checked = ('indirect' == $quote['container_flags']) ? 'checked="checked"' : '';
    ob_start();
?>
<table>
	<tr>
		<th style="<?= ('y' == $quote['prefered_flag']) ? "background-color:#6688BB" : "" ?>" id="<?= $post_name ?>[header]">
			Air Freight Quote
			<a id="collapse_<?= $qcount ?>" href="#" onclick="getElement('freightquote_div[<?= $qcount ?>]').style.display='none';this.style.display='none'; getElement('expand_<?= $qcount ?>').style.display='';checkIfPrefered(<?= $qcount ?>);return false;" style="<?= ($quote['id'] > 0) ? 'display:none;' : '' ?>">Collapse</a>
			<a id="expand_<?= $qcount ?>" href="#" onclick="getElement('freightquote_div[<?= $qcount ?>]').style.display='';this.style.display='none'; getElement('collapse_<?= $qcount ?>').style.display='';getElement('<?= $post_name ?>[header]').style.backgroundColor='#FFEECC';return false;" style="" style="<?= ($quote['id'] > 0) ? '' : 'display:none;' ?>">Expand</a>
			&nbsp;&nbsp;
		</th>
	</tr>
</table>
<table id="freightquote_div[<?= $qcount ?>]" style="<?= ($quote['id'] > 0) ? 'display:none;' : '' ?>">
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td class="left_title">Supplier</td>
		<td colspan="3">
			<input type="hidden" name="<?= $post_name ?>[sea_air]" value="Air" />
			<input type="hidden" name="<?= $post_name ?>[id]" value="<?= intval($quote['id']) ?>" />
			<select name="<?= $post_name ?>[product_id]"><option value=""></option><?= formatHTMLOptions($air_supplier_list, $quote['product_id']) ?></select>
		</td>
		<td><a href="/webadmin/freight_forwarders/index.php?action=<?= intval($quote['id']) == 0 ? 'add' : 'edit&id=' . $quote['product_id'] ?>" target="_blank">Add/Edit</a></td>
	</tr>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td class="left_title" style="width:20%;">Type</td>
		<td style="width:20%;"><input name="<?= $post_name ?>[container_flags]" type="radio" value="direct" <?= $direct_checked ?> />Direct</td>
		<td style="width:20%;"><input name="<?= $post_name ?>[container_flags]" type="radio" value="indirect" <?= $indirect_checked ?> />In-Direct</td>
		<td colspan="2" style="width:40%;">&nbsp;</td>
	</tr>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td class="left_title">Terms</td>
		<td><input name="<?= $post_name ?>[term_flags]" type="radio" value="fob"<?= ('fob' == $quote['term_flags']) ? ' checked="checked"' : '' ?> />FOB</td>
		<td><input name="<?= $post_name ?>[term_flags]" type="radio" value="exw"<?= ('exw' == $quote['term_flags']) ? ' checked="checked"' : '' ?> />Ex Works</td>
		<td><input name="<?= $post_name ?>[term_flags]" type="radio" value="dtd"<?= ('dtd' == $quote['term_flags']) ? ' checked="checked"' : '' ?> />Door To Door</td>
		<td><input name="<?= $post_name ?>[term_flags]" type="radio" value="dta"<?= ('dta' == $quote['term_flags']) ? ' checked="checked"' : '' ?> />Door To Port</td>
	</tr>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td class="left_title">Payment Terms</td>
		<td><input name="<?= $post_name ?>[payment_flag]" type="radio" value="collect"<?= $quote['payment_flag'] == 'collect' ? ' checked="checked"' : '' ?> />Collect</td>
		<td><input name="<?= $post_name ?>[payment_flag]" type="radio" value="prepaid"<?= $quote['payment_flag'] == 'prepaid' ? ' checked="checked"' : '' ?> />Prepaid</td>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr><td class="left_title">&nbsp;</td><td colspan="4">&nbsp;</td></tr>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td class="left_title">Ship From Country</td>
		<td colspan="4"><select name="<?= $post_name ?>[country_from]" onchange="changePorts('from','air',<?= $qcount ?>)"><option value=""></option><?= formatHTMLOptions(invoiceGetOptionList('country_from'), $country_from) ?></select></td>
		<!--<td><a href="">Add/Edit</a></td>-->
	</tr>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td class="left_title">Ship From Airport</td>
		<td colspan="4"><select name="<?= $post_name ?>[port_from]"><option value=""></option><?= formatHTMLOptions($ports_from, $quote['port_from']) ?></select></td>
		<!--<td><a href="">Add/Edit</a></td>-->
	</tr>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td class="left_title">Ship To Country</td>
		<td colspan="4"><select name="<?= $post_name ?>[country_to]" onchange="changePorts('to','air',<?= $qcount ?>)"><option value=""></option><?= formatHTMLOptions(invoiceGetOptionList('country_to'), $country_to) ?></select></td>
		<!--<td><a href="">Add/Edit</a></td>-->
	</tr>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td class="left_title">Ship To Airport</td>
		<td colspan="4"><select name="<?= $post_name ?>[port_to]"><option value=""></option><?= formatHTMLOptions($ports_to, $quote['port_to']) ?></select></td>
		<!--<td><a href="">Add/Edit</a></td>-->
	</tr>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td class="left_title">Shipping Weight (kgs)</td>
		<td colspan="4"><input name="<?= $post_name ?>[shipping_weight]" type="text" value="<?php hecho($ship_weight)?>" /></td>
	</tr>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td class="left_title">Cubic Weight (cm<sup>3</sup>)</td>
		<td colspan="4"><input name="<?= $post_name ?>[cubic_weight]" type="text" value="<?php hecho($ship_cubic)?>" /></td>
	</tr>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td class="left_title">No. of Packages</td>
		<td colspan="4"><input type="text" name="<?= $post_name ?>[no_packages]" value="<?php hecho($quote['no_packages'])?>" /></td>
	</tr>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>><td colspan="5" class="left_title" style="text-align:center;">
		<input type="button" value="Send For Quote" class="unfinished" />&nbsp;&nbsp;
		<input type="button" value="Manual" onclick="toggleSection('manual_quote<?= $qcount ?>',true);" />
	</td></tr>
	<tbody id="manual_quote<?= $qcount ?>" style="display:none;">
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td class="left_title">Transit Time (days)</td>
		<td colspan="4"><input name="<?= $post_name ?>[transit_time]" type="text" value="<?php hecho($quote['transit_time'])?>" /></td>
	</tr>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td class="left_title">Currency</td>
		<td></td>
		<td style="text-align:center"><b><span name="product_currency"><?= $invoice['invoice_currency'] ?></span></b></td>
		<td colspan="2"><select name="<?= $post_name ?>[quote_currency]"><option value=""></option><?= formatHTMLOptions(getCurrencyList(), $quote['quote_currency']) ?></select></td>
	</tr>
<?php
foreach ($cost_fields as $key => $name) {
        $amount = convertCurrency($quote[$key . '_amount'], $quote['quote_currency'], $invoice['invoice_currency']);
?>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td class="left_title" title="<?php hecho($terms[$key])?>"><?= $name ?></td>
		<td style="text-align:right;white-space:nowrap;">
<?php
if ('wgt' == $key) {
            echo '<input onchange="refreshAirQuote(' . $qcount . ')" type="text" name="' . $post_name . '[weight_rate]" size="3" value="' . htmlspecialchars($quote['weight_rate']) . '" /> per kg';
        }

        if ('ins' == $key) {
            echo '<input onchange="refreshAirQuote(' . $qcount . ')" type="text" name="' . $post_name . '[insurance_rate]" size="3" value="' . htmlspecialchars($quote['insurance_rate']) . '" />%';
        }

?>
			<input type="checkbox" onclick="refreshAirQuote(<?= $qcount ?>);" name="<?= $post_name ?>[cost_flags][<?= $key ?>]"<?= (strpos($quote['cost_flags'], $key) !== false) ? ' checked="checked"' : '' ?> />
		</td>
		<!--  -->
		<td><input name="<?= $post_name ?>[<?= $key ?>_amountus]" style="text-align:right;" type="text" value="<?= $amount ?>" onchange="setValue('<?= $post_name ?>[<?= $key ?>_amount]',convertCurrency(this.value,getValue('invoice_currency'),getValue('<?= $post_name ?>[quote_currency]')));refreshAirQuote(<?= $qcount ?>);"/></td>
		<td><input name="<?= $post_name ?>[<?= $key ?>_amount]" onchange="setValue('<?= $post_name ?>[<?= $key ?>_amountus]',convertCurrency(this.value,getValue('<?= $post_name ?>[quote_currency]'),getValue('invoice_currency')));refreshAirQuote(<?= $qcount ?>);" style="text-align:right;" type="text" value="<?= convertCurrency($quote[$key . '_amount']) ?>" /></td>
		<td></td>
	</tr>
<?php
}
?>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td class="left_title">TOTAL COST</td>
		<td style="text-align:right;">All Boxes <input type="checkbox" name="<?= $post_name ?>[total_flag]" onchange="totcheckAirQuote(<?= $qcount ?>);refreshAirQuote(<?= $qcount ?>);" <?= ($all_boxes) ? 'checked="checked"' : '' ?> /></td>
		<td>
			<input style="text-align:right;" type="text" readonly="readonly" name="<?= $post_name ?>[total_amountus]" value="<?= convertCurrency($quote['cost_price'], $quote['quote_currency'], $invoice['invoice_currency']) ?>" />
		</td>
		<td><input style="text-align:right;" type="text" readonly="readonly" name="<?= $post_name ?>[total_amount]" value="<?= $quote['cost_price'] ?>"/></td>
		<td></td>
	</tr>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td class="left_title">SALE PRICE</td><td>&nbsp;</td>
		<?php $sale_amount = convertCurrency($quote['sale_price'], $quote['quote_currency'], $invoice['invoice_currency']);?>
		<td><input style="text-align:right;" type="text" value="<?= $sale_amount ?>" name="sale_price" onchange="setValue('<?= $post_name ?>[sale_price]',convertCurrency(this.value,getValue('invoice_currency'),getValue('<?= $post_name ?>[quote_currency]')))" /></td>
		<td><input style="text-align:right;" type="text" value="<?= $quote['sale_price'] ?>" name="<?= $post_name ?>[sale_price]" onchange="setValue('sale_price',convertCurrency(this.value,getValue('<?= $post_name ?>[quote_currency]'),getValue('invoice_currency')))" /></td>
		<td>&nbsp;</td>
	</tr>
	</tbody>
	<tr><td colspan="5"></td></tr>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td class="left_title">General Notes</td>
		<td colspan="4" style="<?= $quote['general_notes'] != '' ? 'background-color:#66BB88;' : '' ?>"><input type="button" value="Expand/Collapse" onclick="toggleSection('<?= $post_name ?>[general_notes]');" /></td>
	</tr>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td class="left_title"></td><td colspan="4"><textarea style="display:none;" onKeyPress="textareaAutoSize(this,3,3,25);" name="<?= $post_name ?>[general_notes]"><?php hecho($quote['general_notes'])?></textarea></td>
	</tr>
	<tr><td colspan="5"></td></tr>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td class="left_title">Validity</td>
		<td colspan="4">
			<input type="text" name="<?= $post_name ?>[validity_date]" value="<?php hecho($quote['validity_date'])?>" />
			<input type="button" value=" V " onclick="gfPop.fPopCalendar(getElement('<?= $post_name ?>[validity_date]'));" />
			<input type="button" value="Clear" onclick="setValue('<?= $post_name ?>[validity_date]','')" />
		</td>
	</tr>
	<tr><td colspan="5"></td></tr>
	<tr>
		<td colspan="5" style="text-align:center;<?= $quote['prefered_flag'] == 'y' ? 'background-color:#6688BB' : '' ?>">
			<input name="<?= $post_name ?>[prefered]" type="checkbox" onclick="updatePrefered(<?= $qcount ?>);" <?= $quote['prefered_flag'] == 'y' ? 'checked="checked"' : '' ?> />
			<b>SELECT THIS QUOTE</b>
		</td>
	</tr>
	<tr><td colspan="5"></td></tr>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td class="left_title">Confirm this Booking</td>
		<td colspan="5">
			<select name="<?= $post_name ?>[confirmed_flag]" onchange="toggleSection('<?= $post_name ?>[confirm]',this.value=='y');"><?= formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $quote['confirmed_flag']) ?></select>
		</td>
	</tr>
	<tr id="<?= $post_name ?>[confirm]" style="<?= ('y' == $quote['confirmed_flag']) ? '' : 'display:none' ?>"><td colspan="5"><table style="width:100%;">
		<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
			<td class="left_title" style="width:20%;">Master Airway Bill</td>
			<td>
				<input name="<?= $post_name ?>[master_number]" type="text" value="<?= $quote['master_number'] ?>"/>
<?php
if ($quote['id'] > 0) {
?>
				<input type="button" value=" Upload " onclick="openWindow('addQuoteAttachment.php?quote_id=<?= $quote['id'] ?>&method=air',500,250,true);"/>
<?php
if ('' != $quote['lading_file']) {
?>
				&nbsp;&nbsp;<a href="addQuoteAttachment.php?action=view&quote_id=<?= $quote['id'] ?>&method=air"  target="_blank">Show</a>
<?php
}
    }
?>
			</td>
		</tr>
		<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
			<td class="left_title">House Airway Number</td>
			<td>
				<input name="<?= $post_name ?>[house_number]" type="text" value="<?= $quote['house_number'] ?>" />
<?php
if ($quote['id'] > 0) {
?>
				<input type="button" value=" Upload " onclick="openWindow('addQuoteAttachment.php?quote_id=<?= $quote['id'] ?>&method=air2',500,250,true);"/>
<?php
if ('' != $quote['house_airway_file']) {
?>
				&nbsp;&nbsp;<a href="addQuoteAttachment.php?action=view&quote_id=<?= $quote['id'] ?>&method=air2"  target="_blank">Show</a>
<?php
}
    }
?>
			</td>
		</tr>
		<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
			<td class="left_title">Aircraft/Flight #</td><td><input name="<?= $post_name ?>[vessel_name]" type="text" value="<?= $quote['vessel_name'] ?>" /></td>
		</tr>
		<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
			<td class="left_title">Cut-off Date</td>
			<td>
				<input name="<?= $post_name ?>[cutoff_date]" type="text" value="<?= $quote['cutoff_date'] ?>" readonly="readonly" />
				<input type="button" value=" V " onclick="gfPop.fPopCalendar(getElement('<?= $post_name ?>[cutoff_date]'));" />
				<input type="button" value="Clear" onclick="setValue('<?= $post_name ?>[cutoff_date]','')" />
			</td>
		</tr>
		<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
			<td class="left_title">ETA</td>
			<td>
				<input name="<?= $post_name ?>[arrival_date]" type="text" value="<?= $quote['arrival_date'] ?>" readonly="readonly" />
				<input type="button" value=" V " onclick="gfPop.fPopCalendar(getElement('<?= $post_name ?>[arrival_date]'));" />
				<input type="button" value="Clear" onclick="setValue('<?= $post_name ?>[arrival_date]','')" />
			</td>
		</tr>
		<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
			<td class="left_title">ETD</td>
			<td>
				<input name="<?= $post_name ?>[departure_date]" type="text" value="<?= $quote['departure_date'] ?>" readonly="readonly" />
				<input type="button" value=" V " onclick="gfPop.fPopCalendar(getElement('<?= $post_name ?>[departure_date]'));" />
				<input type="button" value="Clear" onclick="setValue('<?= $post_name ?>[departure_date]','')" />
			</td>
		</tr>
	</table></td></tr>
	<!--<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td colspan="5">Freight Quote Changes <a href="javascript:toggleSection('freightquote_changes[<?= $qcount ?>]');">Expand/Collapse</a></td>
	</tr>
	-->
	<tr id="freightquote_changes[<?= $qcount ?>]" style="display:none;"><td colspan="5"><table>
	</table></td></tr>
</table>
<?php
return ob_get_clean();
}
?>

<script type="text/javascript">
//<![CDATA[
function addAirQuote()
{
	var success=false;
	var qcount=String(freightquote_count);
	var quote_div=getElement("sectionAirFreight_quotes");
	if(quote_div!=null)
	{
		success=true;
		var new_div=document.createElement('div');
		new_div.innerHTML = <?= str_replace('[QUOTE_COUNT]', '" + freightquote_count + "', jecho(airquote_row(), 1)) ?>;
		quote_div.appendChild(new_div);
	}
	if(success) freightquote_count++;
	return success;
}

function deleteAirQuote(row)
{
	var theDiv=getElement("freightquote_div["+row+"]");
//	if(theDiv) theDiv.delete();
}

function totcheckAirQuote(row)
{
	var on_off=getElement('freightquote['+row+'][total_flag]').checked;
<?php
foreach (array('wgt', 'thc', 'doc', 'awb', 'exp', 'han', 'tru', 'cra', 'fum', 'com', 'cus', 'ins', 'ori', 'oth') as $key) {
    echo "	getElement('freightquote['+row+'][cost_flags][" . $key . "]').checked=on_off;\n";
}

?>
}

function refreshAirQuote(row)
{
	var total=0.0;
	var total_us=0.0;
	var all_checked=true;

	var quote_currency=getValue('freightquote['+row+'][quote_currency]');
	var weight_rate=getValue('freightquote['+row+'][weight_rate]');
	var insurance_rate=getValue('freightquote['+row+'][insurance_rate]');
	var shipping_weight=getValue('freightquote['+row+'][shipping_weight]');
	var newCurrency=getValue('invoice_currency');
	var invoice_value=convertCurrency(getValue('products_offerus'),'USD',quote_currency);

	if(weight_rate!=0.0)
	{
		setValue('freightquote['+row+'][wgt_amount]',convertCurrency(weight_rate*shipping_weight,quote_currency));
		setValue('freightquote['+row+'][wgt_amountus]',convertCurrency(weight_rate*shipping_weight,quote_currency,newCurrency));
	}
	if(insurance_rate!=0.0)
	{
		setValue('freightquote['+row+'][ins_amount]',convertCurrency((insurance_rate/100)*invoice_value,quote_currency));
		setValue('freightquote['+row+'][ins_amountus]',convertCurrency((insurance_rate/100)*invoice_value,quote_currency,newCurrency));
	}
<?php
foreach (array('wgt', 'thc', 'doc', 'awb', 'exp', 'han', 'tru', 'cra', 'fum', 'com', 'cus', 'ins', 'ori', 'oth') as $key) {
?>
	if(getElement('freightquote['+row+'][cost_flags][<?= $key ?>]').checked)
	{
		total+=Number(getValue('freightquote['+row+'][<?= $key ?>_amount]'));
		total_us+=Number(getValue('freightquote['+row+'][<?= $key ?>_amountus]'));
	}
	else
		all_checked=false;
<?php
}
?>
	setValue('freightquote['+row+'][total_amount]',Number(total).toFixed(2));
	setValue('freightquote['+row+'][total_amountus]',Number(total_us).toFixed(2));
	getElement('freightquote['+row+'][total_flag]').checked=all_checked;
}
//]]>
</script>

<!-- /aircargo_quote_table.tpl -->

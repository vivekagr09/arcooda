<!-- shipping_information_new.tpl -->

<?php
moduleLoad('freight');
moduleLoad('invoice');

function freightcourier_row($ccount = '[COURIER_COUNT]', $courier = array(), $class = '', $member = false)
{
    global $invoice;
    $name   = ($member) ? 'courier' : 'freightcourier';
    $select = ($member) ? 'true' : 'false';
    ob_start();
?>
<tr id="freightcourier_row[<?= $ccount ?>]" <?= ('' != $class) ? 'class="' . $class . '"' : '' ?>>
	<td><input type="hidden" name="<?= $name ?>_id[<?= $ccount ?>]" value="<?php hecho($courier['id'])?>" /><input type="hidden" name="freightcourier_name[<?= $ccount ?>]" value="<?php hecho($courier['name'])?>" /><input type="hidden" name="freightcourier_notes[<?= $ccount ?>]" value="<?php hecho($courier['del_time'])?>" /><input type="hidden" name="freightcourier_track[<?= $ccount ?>]" value="<?php hecho($courier['track'])?>" /><input type="hidden" name="freightcourier_insurance[<?= $ccount ?>]" value="<?php hecho($courier['insurance'])?>" /><input type="hidden" name="freightcourier_tail_gate[<?= $ccount ?>]" value="<?php hecho($courier['tail_gate'])?>" /><input type="hidden" name="freightcourier_cost[<?= $ccount ?>]" value="<?php hecho($courier['cost_price'])?>" /><input type="hidden" name="freightcourier_offer[<?= $ccount ?>]" value="<?php hecho($courier['sell_price'])?>" /><input type="hidden" name="freightcourier_online[<?= $ccount ?>]" value="<?php hecho($courier['sell_price'])?>" /><input type="hidden" name="freightcourier_costus[<?= $ccount ?>]" value="<?php hecho($courier['cost_priceus'])?>" /><input type="hidden" name="freightcourier_offerus[<?= $ccount ?>]" value="<?php hecho($courier['sell_priceus'])?>" /><input type="hidden" name="freightcourier_supplier_id[<?= $ccount ?>]" value="<?php hecho($courier['supplier'])?>" /><input type="hidden" name="freightcourier_supplier_name[<?= $ccount ?>]" value="<?php hecho($courier['supplier_name'])?>" /><input type="checkbox" name="freightcourier_flag[<?= $ccount ?>]"<?= ('y' == $courier['flag']) ? ' checked="checked"' : '' ?> onclick="selectFreightCourier(<?= $ccount ?>, <?= $select ?>);" /></td>
	<td style="text-align:center;"><?php hecho($courier['name'])?></td>
	<td style="text-align:center;"><?php hecho($courier['del_time'])?></td>
	<td style="text-align:center;"><?= ('y' == $courier['track']) ? 'Yes' : (('n' == $courier['track']) ? 'No' : htmlspecialchars($courier['track'])) ?></td>
	<td style="text-align:center;"><?= ('y' == $courier['insurance']) ? 'Yes' : (('n' == $courier['insurance']) ? 'No' : htmlspecialchars($courier['insurance'])) ?></td>
	<td style="text-align:center;"><?= ('y' == $courier['tail_gate']) ? 'Yes' : (('n' == $courier['tail_gate']) ? 'No' : htmlspecialchars($courier['tail_gate'])) ?></td>
	<td style="text-align:right;">$<?php hecho($courier['cost_price'])?></td>
	<!--<td style="text-align:right;">$<?php hecho($courier['retail_price'])?></td>-->
	<td style="text-align:right;background-color:#FFEAE6">$<?php hecho($courier['sell_price'])?></td>
<?php
if ('USD' != $invoice['invoice_currency']) {
?>
	<td style="text-align:right;">$<?php hecho($courier['cost_priceus'])?></td>
	<td style="text-align:right;">$<?php hecho($courier['sell_priceus'])?></td>
<?php
}
?>
</tr>
<?php
return ob_get_clean();
}
function address_row($acount = '[ADDRESS_COUNT]', $address = array())
{
    ob_start();
?>
<tr id="shipto_row[<?= $acount ?>]">
	<td class="left_title">Other Shipping Address</td>
	<td><input type="checkbox" name="shipto_flag[<?= $acount ?>]" onclick="selectShippingAddress(<?= $acount ?>);" /></td>
	<td><table style="width:100%;"><tr><td class="left_title" style="width:25%;">Name</td><td style="width:75%;"><input type="text" name="shipto_name[<?= $acount ?>]" value="<?= $address['contact'] ?>" style="width:98%;" /></td></tr><tr><td class="left_title">Company</td><td><input type="text" name="shipto_company[<?= $acount ?>]" value="<?= $address['company_name'] ?>" style="width:98%;" /></td></tr><tr><td class="left_title">Address</td><td><textarea name="shipto_address[<?= $acount ?>]" rows="2" cols="40" onkeyup="textareaAutoSize(this,1,2);"><?= $address['address'] ?></textarea></td></tr><tr><td class="left_title">City</td><td><input type="text" name="shipto_city[<?= $acount ?>]" value="<?= $address['city'] ?>" style="width:98%;" /></td></tr><tr><td class="left_title">State</td><td><input type="text" name="shipto_state[<?= $acount ?>]" value="<?= $address['state'] ?>" style="width:98%;" /></td></tr><tr><td class="left_title">Postcode</td><td><input type="text" name="shipto_postcode[<?= $acount ?>]" value="<?= $address['post_code'] ?>" style="width:98%;" /></td></tr><tr><td class="left_title">Country</td><td><select name="shipto_country[<?= $acount ?>]"><?= formatHTMLOptions(getCountryList(), $address['country']) ?></select></td></tr><tr><td class="left_title">VAT/Company #</td><td><input type="text" name="shipto_vat[<?= $acount ?>]" value="<?= $address['vat_number'] ?>" style="width:98%;" /></td></tr><tr><td class="left_title">Phone</td><td><input type="text" name="shipto_phone[<?= $acount ?>]" value="<?= $address['phone_no'] ?>" style="width:98%;" /></td></tr></table></td>
</tr>
<?php
return ob_get_clean();
}
function searchCouriers($country, $code, $state, $destination, $weight, $goods_value, $currency, $site_id)
{
    global $invoice;
    $site_id      = $invoice['site_id'];
    $state        = str_replace('.', '', $state);
    $courier_list = array();
    $domestic     = ((in_array($site_id, array(20, 26)) && 'HK' == $country) || (!in_array($site_id, array(20, 26)) && 'AU' == $country));

    if ($domestic && function_exists('freightDomesticCalculate') && function_exists('freightCalculatePrices')) {
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
                $courier_list[$key]['currency'] = ((in_array($site_id, array(20, 26))) ? 'HKD' : 'AUD');
            }

            $courier_list[$key]['Locality']      = $destination;
            $courier_list[$key]['State']         = $state;
            $courier_list[$key]['Pcode']         = $code;
            $courier_list[$key]['Country']       = ((in_array($site_id, array(20, 26))) ? 'HK' : 'AU');
            $courier_list[$key]['supplier_name'] = getFreightForwarderName($value['supplier']);
        }
    }

    if (count($courier_list) == 0 && function_exists('freightInternationalCalculate') && function_exists('freightCalculateIntPrices')) {
        $courier_list = freightInternationalCalculate($country, $currency, $weight);
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
foreach ($member_addresses as $key => $address) {
    if ('' == $address['contact']) {
        $member_addresses[$key]['contact'] = $invoice['bill_name'];
    }

    if ('' == $address['email']) {
        $member_addresses[$key]['email'] = $invoice['bill_email'];
    }

    if ('' == $address['company_name']) {
        $member_addresses[$key]['company_name'] = $invoice['bill_company'];
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
    'cost_priceus'  => '[COURIER_COSTUS]',
    'sell_priceus'  => '[COURIER_OFFERUS]',
    'supplier'      => '[COURIER_SUPPLIER_ID]',
    'supplier_name' => '[COURIER_SUPPLIER_NAME]',
);
$replace2 = array(
    '[COURIER_COUNT]'         => '" + ccount + "',
    '[COURIER_ID]'            => '" + data["id"] + "',
    '[COURIER_NAME]'          => '" + data["name"] + "',
    '[COURIER_DEL_TIME]'      => '" + data["del_time"] + "',
    '[COURIER_TRACKING]'      => '" + tracking + "',
    '[COURIER_INSURANCE]'     => '" + insurance + "',
    '[COURIER_TAIL_GATE]'     => '" + data["tail_gate"] + "',
    '[COURIER_COST]'          => '" + cost_price + "',
    '[COURIER_ONLINE]'        => '" + online_price + "',
    '[COURIER_OFFER]'         => '" + offer_price + "',
    '[COURIER_COSTAU]'        => '" + Number(data["cost_price"]).toFixed(2) + "',
    '[COURIER_OFFERAU]'       => '" + convertCurrency(offer_price, getValue("invoice_currency"), "AUD") + "',
    '[COURIER_COSTUS]'        => '" + Number(data["cost_priceus"]).toFixed(2) + "',
    '[COURIER_OFFERUS]'       => '" + Number(data["sell_priceus"]).toFixed(2) + "',
    '[COURIER_SUPPLIER_ID]'   => '" + data["supplier"] + "',
    '[COURIER_SUPPLIER_NAME]' => '" + data["supplier_name"] + "',
);
?>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionShippingInfo\');">Shipping Information</a>') ?>

<div id="sectionShippingInfo">

<script type="text/javascript">
//<![CDATA[
	var ship_address = <?php jecho($member_addresses)?>;
var notify_address = <?php jecho($member_notify)?>;
var firstAddress = 1;
var lastAddress = <?=max(0, count($member_addresses))?>;
var firstFreightCourier = 1;
var lastFreightCourier = <?=max(0, count($invoice_courier_quotes) + count($member_couriers))?>;

function addMissingWeight(product_id, product_name, product_url) {
    toggleSection('missing_weights_row', true);
    var new_line = '';
    if (product_url != null && product_url != '')
        new_line = String(product_id) + ' - <a target="_blank" href="' + product_url + '">' + str_replace('<', '&lt', product_name) + '</a>';
    else
        new_line = String(product_id) + ' - ' + str_replace('<', '&lt', product_name);
    setValue('missing_weights_span', getValue('missing_weights_span') + new_line + "<br />");
}

function setCourierData(data) {
    clearFreightCouriers();
    var Locality = '.';
    var State = '.';
    var Pcode = '.';
    var Country = '.';
    for (var key in data) {
        addFreightCourier(data[key]);
        if (data[key]['Locality'] != null) Locality = data[key]['Locality'];
        if (data[key]['State'] != null) State = data[key]['State'];
        if (data[key]['Pcode'] != null) Pcode = data[key]['Pcode'];
        if (data[key]['Country'] != null) Country = data[key]['Country'];
    }
    if (Locality == '.' && State == '.' && Pcode == '.' && Country == '.') {
        //No valid quotes, search for possible reasons and display
        var reasons = '';
        if (getValue('shipto_country[0]') == '') reasons += "No Shipping Country selected\n";
        if (getValue('shipto_postcode[0]') == '') reasons += "No Shipping Postcode selected\n";
        if (getValue('shipto_state[0]') == '') reasons += "No Shipping State selected\n";
        if (getValue('shipto_city[0]') == '') reasons += "No Shipping City selected\n";
        if (getValue('ship_weight') <= 0) reasons = "No Shipping Weight selected\n";
        if (getValue('products_offerus') <= 0) reasons += "Products have no offer price\n";
        if (reasons != '') alert("Unable to find quotes, possible reasons:\n" + reasons);
    }
    setValue('freightcourier_locality', Locality);
    setValue('freightcourier_state', State);
    setValue('freightcourier_postcode', Pcode);
    setValue('freightcourier_country', Country);
}

function shippingShowCountryNotes(note_id) {
    var country_id = getShippingCountry();
    if (country_id == null) return;
    var url = '/webadmin/world/show_notes.php?var=' + String(note_id) + '&con=' + escape(country_id);
    var features = 'scrollbars, resizable, height=400, width=600';
    window.open(url, 'shipnote_popup', features);
}

function shippingEditCountryNotes() {
    var country_id = getShippingCountry();
    if (country_id == null || country_id == '') return;
    var url = '/webadmin/world/index.php?return_message=edit&code=' + escape(country_id);
    var features = 'scrollbars, resizable, height=400, width=600';
    window.open(url, 'shipnote_popup', features);
}

function setShippingCountryData(data) {
    return;
}

function untickAllCouriers() {
    var table1 = getElement('freight_couriers');
    var table2 = getElement('members_couriers');
    var length = table1.rows.length + table2.rows.length;
    for (i = 0; i <= lastFreightCourier; i++) {
        var flag = getElement('freightcourier_flag[' + i + ']');
        if (flag && flag != null) flag.checked = false;
    }
}

function setShipToCourierQuote(quote) {
    untickAllCouriers();

    setValue("ship_number", quote.ship_number);
    setValue("packing_string", quote.packing_string);
    setValue("ship_online", quote.retail_price);
    setValue("ship_onlineau", convertCurrency(quote.retail_price, getValue("invoice_currency"), "AUD"));
    setValue("ship_onlineus", convertCurrency(quote.retail_price, getValue("invoice_currency"), "USD"));

    setValue("shipping_method", "other");
    setValue("ship_insurance", quote.insurance);
    setValue("ship_tailgate", quote.tail_gate);
    setValue("ship_registered", quote.registered);
    setValue("ship_receipted", quote.receipted);
    setValue("ship_express", quote.express);
    setValue("ship_offer", quote.sell_price);
    setValue("ship_cost", quote.cost_price);
    setValue("ship_offerau", quote.sell_priceau);
    setValue("ship_costau", quote.cost_priceau);
    setValue("ship_offerus", quote.sell_priceus);
    setValue("ship_costus", quote.cost_priceus);
    setValue("ship_id", quote.service_id);
    setValue("ship_supplier_id", quote.supplier);
    setValue("ship_product_name", quote.name);
    setValue("ship_supplier_name", quote.supplier_name);

    setValue("ship_product_id", quote.ship_number);
    setValue("supplier_ship_product_id", quote.ship_number);
    setValue("ship_qty", "1");
    setValue("supplier_ship_product_name", quote.name);
    setValue("ship_account_num", "");
    setValue('supplier_ship_qty', '1');
    setValue('supplier_ship_cost', quote.cost_price);

    if (quote.insurance == "y") setValue('ship_product_notes', "Insurance Included.\n");
    else setValue('ship_product_notes', "");

    $("#account_num_tr").fadeOut();
    $("#product_ship_rowa, #product_ship_rowb, #supplier_ship_rowa, #supplier_ship_rowb").fadeIn();

    if (freightQuotationChangeShippingMethod) freightQuotationChangeShippingMethod('other');
    if (shipChange) shipChange();

<?php
if ('add' != $action) {
?>
	for (key in prefer) {
        var obj = getElement('freightquote[' + key + '][prefered]');
        obj.checked = false;
        obj.parentNode.style.backgroundColor = '#FFFFFF';
        getElement('freightquote[' + key + '][header]').style.backgroundColor = "#FFEECC";
    }
    for (i = countStart; i < freightquote_count; i++) {
        var obj = getElement('freightquote[' + i + '][prefered]');
        obj.checked = false;
        obj.parentNode.style.backgroundColor = '#FFFFFF';
    }
<?php
}
?>
}

function selectFreightCourier(row, member) {
    untickAllCouriers();
    var flag = getElement('freightcourier_flag[' + row + ']');
    flag.checked = true;

    var section = 'freightcourier';
    if (member) section = 'courier';
    var product_id = getValue(section + '_id[' + row + ']');
    var courier_id = product_id;

    setValue('ship_id', courier_id);
    setValue('ship_offerau', getValue('freightcourier_offerau[' + row + ']'));
    setValue('ship_costau', getValue('freightcourier_costau[' + row + ']'));
    setValue('ship_offerus', getValue('freightcourier_offerus[' + row + ']'));
    setValue('ship_costus', getValue('freightcourier_costus[' + row + ']'));

    var freightrow = getElement('product_ship_rowa');
    if (freightrow != null) freightrow.style.display = '';
    if (product_id.length < 2) product_id = '0' + product_id;
    var ins = getValue('freightcourier_insurance[' + row + ']');
    var ship_id_str = 'F' + product_id + ((ins == 'y' || ins == 'Yes') ? 'I' : 'N');
    setValue('ship_product_id', ship_id_str);
    setValue('ship_qty', '1');
    setValue('ship_cost', getValue('freightcourier_cost[' + row + ']'));
    setValue('ship_online', getValue('freightcourier_online[' + row + ']'));
    setValue('ship_offer', getValue('freightcourier_offer[' + row + ']'));
    setValue('ship_offerau', getValue('freightcourier_offerau[' + row + ']'));
    setValue('ship_offerus', getValue('freightcourier_offerus[' + row + ']'));
    getElement('product_ship_rowb').style.display = '';
    var product_notes = '';
    if (ins == 'Yes' || ins == 'y') product_notes += "Insurance Included.\n";

    var freightrow = getElement('supplier_ship_rowa');
    if (freightrow != null) freightrow.style.display = '';
    var freightrow = getElement('supplier_ship_rowb');
    if (freightrow != null) freightrow.style.display = '';
    setValue('supplier_ship_product_id', ship_id_str);
    var tempName = getValue('freightcourier_name[' + row + ']');
    //var courier_name=tempName+((courier_id>100) ? ' (Personal Account)' : '');
    var courier_name = tempName + (member ? ' (Personal Account)' : '');
    setValue('ship_product_name', courier_name);
    setValue('supplier_ship_product_name', tempName);
    setValue('ship_supplier_id', getValue('freightcourier_supplier_id[' + row + ']'));
    if (member) {
        //courier_id>=100
        product_notes += "Customer Account #: " + getValue('freightcourier_supplier_name[' + row + ']') + "\n";
        getElement('account_num_tr').style.display = '';
        setValue('ship_account_num', getValue('freightcourier_supplier_name[' + row + ']'));
        var supplier_name = explode(' ', getValue('freightcourier_name[' + row + ']'));
        setValue('ship_supplier_name', supplier_name[0]);
    }
    else {
        getElement('account_num_tr').style.display = 'none';
        setValue('ship_account_num', '');
        setValue('ship_supplier_name', getValue('freightcourier_supplier_name[' + row + ']'));
    }
    if (product_notes != '') setValue('ship_product_notes', product_notes);
    var name = getValue('freightcourier_name[' + row + ']') + ((member) ? ' (Personal Account)' : ''); //courier_id>=100
    setValue('ship_courier_name', name);
    setValue('ship_courier_price', '$' + getValue('freightcourier_offerus[' + row + ']') + ' USD');
    var transit = (member) ? 'N/A' : getValue('freightcourier_notes[' + row + ']'); //courier_id>=100
    setValue('ship_courier_transit', transit);
    var insStr = (ins == 'y' || ins == 'Yes') ? 'Included' : 'No';
    setValue('ship_courier_insurance', insStr);
    setValue('supplier_ship_qty', '1');
    setValue('supplier_ship_cost', getValue('freightcourier_cost[' + row + ']'));
    setValue('ship_costau', getValue('freightcourier_costau[' + row + ']'));
    setValue('ship_costus', getValue('freightcourier_costus[' + row + ']'));
    setValue('ship_insurance', ((ins == 'Yes' || ins == 'y') ? 'y' : 'n'));

    if (window.shipChange != null) window.shipChange();

<?php
if ('add' != $action) {
?>
	for (key in prefer) {
        var obj = getElement('freightquote[' + key + '][prefered]');
        obj.checked = false;
        obj.parentNode.style.backgroundColor = '#FFFFFF';
        getElement('freightquote[' + key + '][header]').style.backgroundColor = "#FFEECC";
    }
    for (i = countStart; i < freightquote_count; i++) {
        var obj = getElement('freightquote[' + i + '][prefered]');
        obj.checked = false;
        obj.parentNode.style.backgroundColor = '#FFFFFF';
    }
<?php
}
?>
}

function selectShippingAddress(row) {
    var flag = getElement('shipto_flag[0]');
    if (flag != null) flag.checked = (row == 0);
    for (var i = firstAddress; i <= lastAddress; i++) {
        flag = getElement('shipto_flag[' + i + ']');
        if (flag != null) flag.checked = (row == i);
    }
    var country_id = getValue('shipto_country[' + row + ']');

    setValue('lblShipCountry', country_id);
    var ship_notes = countryShipNotes(country_id);

    for (var field in ship_notes) {
        var theButton = getElement(field + 'Button');
        if (theButton != null) {
            if (ship_notes[field] > 0) {
                theButton.disabled = false;
                theButton.style.border = "1px solid red";
            }
            else {
                theButton.disabled = true;
                theButton.style.borderStyle = "none";
            }
        }
    }
}

function getShippingCountry() {
    var flag = getElement('shipto_flag[0]');
    if (flag != null) if (flag.checked == true) return getValue('shipto_country[0]');
    for (var i = firstAddress; i <= lastAddress; i++) {
        flag = getElement('shipto_flag[' + i + ']');
        if (flag != null) if (flag.checked == true) return getValue('shipto_country[' + i + ']');
    }
}

function clearFreightCouriers() {
    var table = getElement('freight_couriers');
    var length = table.rows.length;
    for (i = 0; i < length; i++)
        table.deleteRow(0);
    firstFreightCourier = lastFreightCourier;
}

function clearShippingAddresses() {
    for (var i = firstAddress; i <= lastAddress; i++) {
        var theCheck = getElement('shipto_flag[' + i + ']');
        if (theCheck != null) theCheck.checked = false;
        var theRow = getElement("shipto_row[" + i + "]");
        if (theRow != null) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    }
    firstAddress = lastAddress;

    setValue('shipto_country[0]', '');
    setValue('shipto_address[0]', '');
    var check = getElement('shipto_flag[0]');
    selectShippingAddress(0);
}

function setShippingAddress(data) {
    setValue('shipto_name[0]', data['contact']);
    setValue('shipto_company[0]', data['company_name']);
    setValue('shipto_address[0]', data['address']);
    setValue('shipto_city[0]', data['city']);
    setValue('shipto_state[0]', data['state']);
    setValue('shipto_postcode[0]', data['post_code']);
    setValue('shipto_country[0]', data['country']);
    setValue('shipto_vat[0]', data['vat_number']);
    setValue('shipto_phone[0]', data['phone_no']);
    setValue('shipto_email[0]', data['email']);
    selectShippingAddress(0);
}

function setNotifyAddress(data) {
    setValue('notify_name[0]', data['contact']);
    setValue('notify_company[0]', data['company_name']);
    setValue('notify_address[0]', data['address']);
    setValue('notify_city[0]', data['city']);
    setValue('notify_state[0]', data['state']);
    setValue('notify_postcode[0]', data['post_code']);
    setValue('notify_country[0]', data['country']);
    setValue('notify_vat[0]', data['vat_number']);
    setValue('notify_phone[0]', data['phone_no']);
    setValue('notify_email[0]', data['email']);
    //selectShippingAddress(0);
}

function addFreightCourier(data) {
    lastFreightCourier++;
    var ccount = String(lastFreightCourier);
    var cour_table = (data['member_courier'] == 'y') ? getElement('members_couriers') : getElement("freight_couriers");
    if (cour_table == null) return;

    var newRow1 = addTableRow(cour_table, 'freightcourier_row[' + ccount + ']');
    if (lastFreightCourier & 1) newRow1.className = 'alt';

    var tracking = 'No';
    if (data['track'] == 'y') tracking = 'Yes';
    var insurance = 'No';
    if (data['insurance'] == 'y') insurance = 'Yes';
    if (data['tail_gate'] == 'y') data['tail_gate'] = 'Yes';
    else if (data['tail_gate'] == 'n') data['tail_gate'] = 'No';

    var cost_price = convertCurrency(data['cost_price'], 'HKD', getValue('invoice_currency'));
    var offer_price = Math.ceil(convertCurrency(data['sell_price'], 'HKD', getValue('invoice_currency'))).toFixed(2);
    var online_price = convertCurrency(data['retail_price'], 'HKD', getValue('invoice_currency'));

    if (data['member_courier'] == 'y')
        addMemberCourier(data, tracking, insurance, cost_price, online_price, offer_price, ccount, newRow1);
    else
        addCourier(data, tracking, insurance, cost_price, online_price, offer_price, ccount, newRow1);
}

function addMemberCourier(data, tracking, insurance, cost_price, online_price, offer_price, ccount, newRow1) {
<?php
$count = 0;
foreach (explode("\n", freightcourier_row('[COURIER_COUNT]', $replace1, '', true)) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "	addTableCell(newRow" . $count . "," . str_replace(array_keys($replace2), array_values($replace2), jecho(substr($row, 4, -5), 1)) . ");\n";
    } elseif (substr($row, 0, 31) == '<td style="text-align:center;">') {
        echo "	addTableCell(newRow" . $count . "," . str_replace(array_keys($replace2), array_values($replace2), jecho(substr($row, 31, -5), 1)) . ").style.textAlign='center';\n";
    } elseif (substr($row, 0, 30) == '<td style="text-align:right;">') {
        echo "	addTableCell(newRow" . $count . "," . str_replace(array_keys($replace2), array_values($replace2), jecho(substr($row, 30, -5), 1)) . ").style.textAlign='right';\n";
    } elseif (substr($row, 0, 54) == '<td style="text-align:right;background-color:#FFEAE6">') {
        echo "var newCell = addTableCell(newRow" . $count . "," . str_replace(array_keys($replace2), array_values($replace2), jecho(substr($row, 54, -5), 1)) . ");\n";
        echo "newCell.style.textAlign='right';\nnewCell.style.backgroundColor='#FFEAE6';\n";
    }
}
?>
}

function addCourier(data, tracking, insurance, cost_price, online_price, offer_price, ccount, newRow1) {
<?php
$count = 0;
foreach (explode("\n", freightcourier_row('[COURIER_COUNT]', $replace1)) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "	addTableCell(newRow" . $count . "," . str_replace(array_keys($replace2), array_values($replace2), jecho(substr($row, 4, -5), 1)) . ");\n";
    } elseif (substr($row, 0, 31) == '<td style="text-align:center;">') {
        echo "	addTableCell(newRow" . $count . "," . str_replace(array_keys($replace2), array_values($replace2), jecho(substr($row, 31, -5), 1)) . ").style.textAlign='center';\n";
    } elseif (substr($row, 0, 30) == '<td style="text-align:right;">') {
        echo "	addTableCell(newRow" . $count . "," . str_replace(array_keys($replace2), array_values($replace2), jecho(substr($row, 30, -5), 1)) . ").style.textAlign='right';\n";
    } elseif (substr($row, 0, 54) == '<td style="text-align:right;background-color:#FFEAE6">') {
        echo "var newCell = addTableCell(newRow" . $count . "," . str_replace(array_keys($replace2), array_values($replace2), jecho(substr($row, 54, -5), 1)) . ");\n";
        echo "newCell.style.textAlign='right';\nnewCell.style.backgroundColor='#FFEAE6';\n";
    }
}
?>
}

function addShippingAddress(data) {
    ship_address = [];
    var shipAddress = [];
    for (key in data) {
        ship_address.push(data[key]);
        var address = data[key];
        var state = (address['state'] != '') ? ', ' + address['state'] : '';
        var str = address['contact'] + ' (' + address['address'] + ', ' + address['city'] + state + ', ' + address['post_code'] + ', ' + address['country'] + ')';
        shipAddress.push(str);
    }
    //setShippingAddress(ship_address[0]);
    setSelectOptions('shipping_to', shipAddress);
    setValue('shipping_to', 0);
}

function addNotifyAddress(data) {
    notify_address = [];
    var notifyAddress = [];
    for (key in data) {
        notify_address.push(data[key]);
        var address = data[key];
        var state = (address['state'] != '') ? ', ' + address['state'] : '';
        var str = address['contact'] + ' (' + address['address'] + ', ' + address['city'] + state + ', ' + address['post_code'] + ', ' + address['country'] + ')';
        notifyAddress.push(str);
    }
    //setShippingAddress(ship_address[0]);
    setSelectOptions('notify_party', notifyAddress);
    setValue('notify_party', 0);
}

function addSpecialRequests(data) {
    if (getElement('member_requests') != null) {
        var requests = '';
        for (key in data)
            requests += '- ' + data[key] + "\n";
        setValue('member_requests', requests);
        textareaAutoSize(getElement('member_requests'), 3, 2, 25);
    }
}

function freightCourierSearch(theFrm) {
    var oldact = theFrm.action;
    var oldtrg = theFrm.target;
    theFrm.action = "courier_search.php";
    theFrm.target = "freight_search";
    theFrm.submit();
    theFrm.action = oldact;
    theFrm.target = oldtrg;
}

function updateShippingAddress() {
    var shipAddress = getValue('shipping_to');
    if (shipAddress != '') setShippingAddress(ship_address[shipAddress]);
}

function updateNotifyAddress() {
    var notifyAddress = getValue('notify_party');
    if (notifyAddress != '') setNotifyAddress(notify_address[notifyAddress]);
}

function updateGoodsExtra(section) {
    getElement(section + '_by_tr').style.display = ((getValue(section + '_by') == "n") ? '' : 'none');
}
function updateTrackingNum() {
    if (getValue("proforma_tracknumber") != '')
        getElement("process_invoice_tr").style.display = '';
}

function selectQuotation(type) {
    var allowed = Array('SeaFreight', 'AirFreight', 'Couriers');
    for (i = 0; i < allowed.length; i++)
        getElement('section' + allowed[i]).style.display = (allowed[i] == type) ? '' : 'none';
}
//]]>
</script>

<?php
$acount          = 0;
$compare_array   = array('ship_name' => 'contact', 'ship_company' => 'company_name', 'ship_address' => 'address', 'ship_city' => 'city', 'ship_state' => 'state', 'ship_postcode' => 'post_code', 'ship_country' => 'country', 'ship_vat' => 'vat_number', 'ship_phone' => 'phone_no');
$compare_array2  = array('name' => 'contact', 'company' => 'company_name', 'address' => 'address', 'city' => 'city', 'state' => 'state', 'postcode' => 'post_code', 'country' => 'country', 'vat' => 'vat', 'phone' => 'phone_no');
$selected        = '';
$selectedN       = '';
$shippingOptions = array();
$notifyOptions   = array();
foreach ($compare_array2 as $key => $key2) {
    if ('' == $invoice['notify_' . $key] || !isset($invoice['notify_' . $key])) {
        $invoice['notify_' . $key] = $invoice['ship_' . $key];
    }
}

foreach ($member_addresses as $key => $address) {
    $state             = ('' != $address['state']) ? ', ' . $address['state'] : '';
    $optionVal         = $address['contact'] . ' (' . $address['address'] . ', ' . $address['city'] . $state . ', ' . $address['post_code'] . ', ' . getCountryName($address['country']) . ')';
    $shippingOptions[] = $optionVal;
    $count             = 0;
    foreach ($compare_array as $key1 => $key2) {
        if (formatCleanString($invoice[$key1]) == formatCleanString($address[$key2])) {
            $count++;
        }

        if (count($compare_array) == $count) {
            $selected = $key;
        }

    }
}
foreach ($member_notify as $key => $address) {
    $state           = ('' != $address['state']) ? ', ' . $address['state'] : '';
    $optionVal       = $address['contact'] . ' (' . $address['address'] . ', ' . $address['city'] . $state . ', ' . $address['post_code'] . ', ' . getCountryName($address['country']) . ')';
    $notifyOptions[] = $optionVal;
    $count           = 0;
    foreach ($compare_array2 as $key1 => $key2) {
        if (formatCleanString($invoice['notify_' . $key1]) == formatCleanString($address[$key2])) {
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
	<table class="wide">
		<tr>
			<th style="width:50%;">Shipping Address</th>
			<th style="width:50%;">Notify Party</th>
		</tr>
		<tr>
			<td style="width:50%;"><table style="width:100%;">
				<tr><td colspan="2" style="text-align:center" class="left_title">
					Select A Shipping Address :
					<input type="hidden" name="shipto_flag" value="true" />
					<select name="shipping_to" onchange="updateShippingAddress()" style="text-align:center">
						<option value="">&nbsp;</option>
						<?= formatHTMLOptions($shippingOptions, $selected) ?>
					</select>
				</td></tr>
				<tr>
					<td class="left_title" style="width:25%;">Name</td>
					<td style="width:75%;"><input type="text" name="shipto_name[0]" value="<?= $invoice['ship_name'] ?>" style="width:98%;" /></td>
				</tr>
				<tr>
					<td class="left_title">Company</td>
					<td><input type="text" name="shipto_company[0]" value="<?= $invoice['ship_company'] ?>" style="width:98%;" /></td>
				</tr>
				<tr>
					<td class="left_title">Address</td>
					<td><textarea name="shipto_address[0]" cols="40" rows="<?= max(2, count(explode("\n", trim($invoice['ship_address'])))) ?>" onkeyup="textareaAutoSize(this,1,2);"><?= $invoice['ship_address'] ?></textarea></td>
				</tr>
				<tr>
					<td class="left_title">City</td>
					<td><input type="text" name="shipto_city[0]" value="<?= $invoice['ship_city'] ?>" style="width:98%;" /></td>
				</tr>
				<tr>
					<td class="left_title">State</td>
					<td><input type="text" name="shipto_state[0]" value="<?= $invoice['ship_state'] ?>" style="width:98%;" /></td>
				</tr>
				<tr>
					<td class="left_title">Postcode</td>
					<td><input type="text" name="shipto_postcode[0]" value="<?= $invoice['ship_postcode'] ?>" style="width:98%;" /></td>
				</tr>
				<tr>
					<td class="left_title">Country</td>
					<td><select name="shipto_country[0]">
						<option value=""></option>
						<?= formatHTMLOptions(getCountryList(), $invoice['ship_country']) ?>
					</select></td>
				</tr>
				<tr>
					<td class="left_title">VAT/Company #</td>
					<td><input type="text" name="shipto_vat[0]" value="<?= $invoice['ship_vat'] ?>" style="width:98%;" /></td>
				</tr>
				<tr>
					<td class="left_title">Phone</td>
					<td><input type="text" name="shipto_phone[0]" value="<?= $invoice['ship_phone'] ?>" style="width:98%;" /></td>
				</tr>
				<tr>
					<td class="left_title">Email</td>
					<td><input type="text" name="shipto_email[0]" value="<?= $invoice['ship_email'] ?>" style="width:98%;" /></td>
				</tr>
			</table></td>
			<td style="width:50%;"><table style="width:100%;">
				<tr><td colspan="2" style="text-align:center;" class="left_title">
					Select A Notify Party :
					<input type="hidden" name="notify_flag" value="true" />
					<select name="notify_party" onchange="updateNotifyAddress();" style="text-align:center">
						<option value="">&nbsp;</option>
						<?= formatHTMLOptions($notifyOptions, $selectedN) ?>
					</select>
				</td></tr>
				<tr>
					<td class="left_title" style="width:25%;">Name</td>
					<td style="width:75%;"><input type="text" name="notify_name[0]" value="<?= $invoice['notify_name'] ?>" style="width:98%;" /></td>
				</tr>
				<tr>
					<td class="left_title">Company</td>
					<td><input type="text" name="notify_company[0]" value="<?= $invoice['notify_company'] ?>" style="width:98%;" /></td>
				</tr>
				<tr>
					<td class="left_title">Address</td>
					<td><textarea name="notify_address[0]" cols="40" rows="<?= max(2, count(explode("\n", trim($invoice['notify_address'])))) ?>" onkeyup="textareaAutoSize(this,1,2);"><?= $invoice['notify_address'] ?></textarea></td>
				</tr>
				<tr>
					<td class="left_title">City</td>
					<td><input type="text" name="notify_city[0]" value="<?= $invoice['notify_city'] ?>" style="width:98%;" /></td>
				</tr>
				<tr>
					<td class="left_title">State</td>
					<td><input type="text" name="notify_state[0]" value="<?= $invoice['notify_state'] ?>" style="width:98%;" /></td>
				</tr>
				<tr>
					<td class="left_title">Postcode</td>
					<td><input type="text" name="notify_postcode[0]" value="<?= $invoice['notify_postcode'] ?>" style="width:98%;" /></td>
				</tr>
				<tr>
					<td class="left_title">Country</td>
					<td><select name="notify_country[0]">
						<option value=""></option>
						<?= formatHTMLOptions(getCountryList(), $invoice['notify_country']) ?>
					</select></td>
				</tr>
				<tr>
					<td class="left_title">VAT/Company #</td>
					<td><input type="text" name="notify_vat[0]" value="<?= $invoice['notify_vat'] ?>" style="width:98%;" /></td>
				</tr>
				<tr>
					<td class="left_title">Phone</td>
					<td><input type="text" name="notify_phone[0]" value="<?= $invoice['notify_phone'] ?>" style="width:98%;" /></td>
				</tr>
				<tr>
					<td class="left_title">Email</td>
					<td><input type="text" name="notify_email[0]" value="<?= $invoice['notify_email'] ?>" style="width:98%;" /></td>
				</tr>
			</table></td>
		</tr>
	</table>

	<br />

	<table class="wide">
		<tr>
			<td class="left_title">Customer Special Requests</td>
			<td style="height:50px;"><textarea readonly="readonly" rows="5" cols="80" name="member_requests"<?= ('' != $requests) ? ' style="border:5px solid #FF1493;"' : '' ?>><?= $requests ?></textarea></td>
		</tr>
		<tr>
			<td class="left_title">Special Freight Needs<br /><span style="font-size:9px;color:red;font-weight:normal;">Special Requests From Customers</span></td>
			<td><textarea name="ship_needs" cols="80" rows="<?= max(10, count(explode("\n", trim($invoice['ship_needs'])))) ?>" onkeyup="textareaAutoSize(this,1,10);"<?= (trim($invoice['ship_needs']) != '') ? ' style="border:5px solid #FF1493;"' : '' ?>><?php hecho(trim($invoice['ship_needs']))?></textarea></td>
		</tr>
		<tr>
			<td class="left_title">Invoice Comments/Notes<br /><span style="font-size:9px;color:red;font-weight:normal;">Displayed on Invoice</span></td>
			<td><textarea name="invoice_comments" cols="80" rows="<?= max(10, count(explode("\n", trim($invoice['invoice_comments'])))) ?>" onkeyup="textareaAutoSize(this,1,10);"><?php hecho(trim($invoice['invoice_comments']))?></textarea></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;">Shipping To <span name="lblShipCountry"><?php hecho($invoice['ship_country'])?></span></td>
			<td>
				<input name="fumButton" type="button" value="Fumigation" onclick="shippingShowCountryNotes('fum');"<?= ($row1) ? ' style="border:1px solid red;"' : ' disabled="disabled"' ?> />
				<input name="cerButton" type="button" value="Certificates" onclick="shippingShowCountryNotes('cer');"<?= ($row2) ? ' style="border:1px solid red;"' : ' disabled="disabled"' ?> />
				<input name="shiButton" type="button" value="Special Needs" onclick="shippingShowCountryNotes('spn');"<?= ($row3) ? ' style="border:1px solid red;"' : ' disabled="disabled"' ?> />
				<a href="javascript:void(0);" onclick="shippingEditCountryNotes();">Add / Edit</a>
			</td>
		</tr>
<?php
$harmonised_code_list   = invoiceGetOptionList('harmonised_code');
$harmonised_code_level0 = array();
$harmonised_code_level1 = array();
$harmonised_code_level2 = array();
foreach ($harmonised_code_list as $k => $v) {
    switch (strlen($k)) {
        case 2:
            $harmonised_code_level0[$k] = $k . ' - ' . $v;
            break;
        case 4:
            $harmonised_code_level1[$k] = $k . ' - ' . $v;
            break;
        default:
            $harmonised_code_level2[substr($k, 0, 2)][substr($k, 0, 4)][$k] = $k . ' - ' . $v;
            break;
    }
}

foreach ($harmonised_code_level2 as $k0 => $v0) {
    if (isset($harmonised_code_level0[$k0])) {
        unset($harmonised_code_level2[$k0]);
        $k0                          = $harmonised_code_level0[$k0];
        $harmonised_code_level2[$k0] = $v0;
        ksort($harmonised_code_level2);
    }
    foreach ($v0 as $k1 => $v1) {
        if (isset($harmonised_code_level1[$k1])) {
            unset($harmonised_code_level2[$k0][$k1]);
            $k1                               = $harmonised_code_level1[$k1];
            $harmonised_code_level2[$k0][$k1] = $v1;
            ksort($harmonised_code_level2[$k0]);
        }
    }

}
$harmonised_code_nest = $harmonised_code_level2;
?>
		<tr>
			<td class="left_title">Harmonised Code</td>
			<td>
				<select name="harmonised_code" style="width:80%;">
					<option value="">Not Selected</option>
					<?= formatHTMLOptions($harmonised_code_nest, $invoice['harmonised_code']) ?>
				</select>
				<a href="invoice_options.php?section=harmonised_code" target="_blank" class="addedit">Add / Edit</a>
			</td>
		</tr>
		<tr>
			<td class="left_title">Country Of Origin</td>
			<td>
				<select name="origin_country" style="width:80%;">
					<option value="">Not Selected</option>
					<?= formatHTMLOptions(invoiceGetOptionList('origin_country'), $invoice['origin_country']) ?>
				</select>
				<a href="invoice_options.php?section=origin_country" target="_blank" class="addedit">Add / Edit</a>
			</td>
		</tr>
		<tr>
			<td class="left_title">Shipping Weight (kgs)</td>
			<td><input name="ship_weight" type="text" size="10" value="<?= round($invoice['ship_weight'], 2) ?>" /></td>
		</tr>
		<tr name="missing_weights_row"<?= (count($missing_weights)) ? '' : ' style="display:none;"' ?>>
			<td class="left_title"></td>
			<td><div name="missing_weights_span" style="border:5px solid #9314FF;">
				The following products have undefined weights:<br />
				<?= implode("<br />\n", $missing_weights) ?>
			</div></td>
		</tr>
		<tr>
			<td class="left_title">Cubic Weight (cm<sup>3</sup>)</td>
			<td><input name="ship_cubic" type="text" size="10" value="<?= round($invoice['ship_cubic'], 2) ?>" /></td>
		</tr>
	</table>
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionShippingInfo_hideNote">
	Shipping Information can be viewed by Clicking "Shipping Information" above<br />
</div>

<!-- /shipping_information_new.tpl -->

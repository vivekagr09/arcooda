<!-- product_supplier.tpl -->

<?php
moduleLoad('invoice');
moduleLoad('product');

$show_usd       = in_array($_GLOBALS['site_settings']['site_id'], array(3, 20, 26));
$owarranty      = $ocondition      = $opacking      = $ovoltage      = $oshipping      = $oplug      = 0;
$defSup         = $invoice['default_supplier'];
foreach ($invoice_products as $product) {
    if ($product['product_warranty'] > 0) {
        if (0 == $owarranty) {
            $owarranty = $product['product_warranty'];
        } elseif ($owarranty != $product['product_warranty']) {
            $owarranty = 0;
        }
    }
    if ($product['product_condition'] > 0) {
        if (0 == $ocondition) {
            $ocondition = $product['product_condition'];
        } elseif ($ocondition != $product['product_condition']) {
            $ocondition = 0;
        }
    }
    if ($product['product_packing'] > 0) {
        if (0 == $opacking) {
            $opacking = $product['product_packing'];
        } elseif ($opacking != $product['product_packing']) {
            $opacking = 0;
        }
    }
    if ($product['product_voltage'] > 0) {
        if (0 == $ovoltage) {
            $ovoltage = $product['product_voltage'];
        } elseif ($ovoltage != $product['product_voltage']) {
            $ovoltage = 0;
        }
    }
    if ($product['product_terms'] > 0) {
        if (0 == $oshipping) {
            $oshipping = $product['product_terms'];
        } elseif ($oshipping != $product['product_terms']) {
            $oshipping = 0;
        }
    }
    if ($product['product_plug'] > 0) {
        if (0 == $oplug) {
            $oplug = $product['product_plug'];
        } elseif ($oplug != $product['product_plug']) {
            $oplug = 0;
        }
    }
    if (0 == $defSup && $product['supplier_id'] > 0) {
        $defSup = $product['supplier_id'];
    }
}
$supValues       = getSupplierValues($defSup);
$warranty_terms  = array('-1' => 'Not Applicable') + invoiceGetOptionList('warranty_terms_id');
$condition_terms = array('-1' => 'Not Applicable') + invoiceGetOptionList('condition');
$packing_terms   = array('-1' => 'Not Applicable') + invoiceGetOptionList('packing_requirement');
$voltage_terms   = array('-1' => 'Not Applicable') + invoiceGetOptionList('voltage_id2');
$shipping_terms  = array('-1' => 'Not Applicable') + invoiceGetOptionList('terms_id');
$plugs_list      = array('-1' => 'Not Applicable') + invoiceGetOptionList('plug_id');
function product_row($pcount = '[PRODUCT_COUNT]', $product = array(), $generic = false)
{
    global $invoice, $_GLOBALS, $show_usd;
    $inv = $invoice['invoice_currency'];
    if ('' == $inv) {
        $inv = 'USD';
    }
    if (!intval($product['decimal'])) {
        $product['decimal'] = 2;
    }
    $type_text = ' type="text" class="wide" ';
    $type_number = ' type="number" min="0" max="0" step="1" class="wide" ';
    $type_price = ' type="number" min="0.00" max="0.00" step="0.' . str_repeat('0', $product['decimal'] - 1) .'1" class="wide" ';
    $hidden = array(
        'sub_id[' . $pcount . ']'                => $product['sub_id'],
        'decimal[' . $pcount . ']'               => $product['decimal'],
        'generic[' . $pcount . ']'               => $product['generic'],
        'generic_code[' . $pcount . ']'          => $product['generic_code'],
        'prod_count[' . $pcount . ']'            => $product['prod_count'],
        'product_price_changed[' . $pcount . ']' => ('' != $product['product_price_changed']) ? $product['product_price_changed'] : 'n',
        'product_rank[' . $pcount . ']'          => $product['product_rank'],
        'product_section[' . $pcount . ']'       => $product['product_section'],
        'product_updated[' . $pcount . ']'       => $product['product_updated'],
        'product_weight[' . $pcount . ']'        => $product['product_weight'],
        'product_weight_inc[' . $pcount . ']'    => $product['product_weight_inc'],
    );
    ob_start();
?>
<tr id="product_rowa[<?=$pcount?>]">
    <td>
        <?=formatHTMLInputs($hidden)?>
        <input <?= $type_text ?> name="product_id[<?=$pcount?>]" value="<?php hecho($product['product_id'])?>" size="4" maxlength="8" />
    </td>
    <td>
<?php
    if ('y' != $product['generic']) {
?>
    	<input onclick="searchProductById(<?=$pcount?>, getValue('product_id[<?=$pcount?>]'))" type="button" value="S" />
<?php
    }
?>
    </td>
    <td><input <?= $type_text ?> name="product_name[<?=$pcount?>]" value="<?php hecho($product['product_name'])?>" /></td>
    <td>
<?php
    if ('y' != $product['generic']) {
?>
        <input onclick="searchProduct(<?=$pcount?>, getValue('product_name[<?=$pcount?>]'))" type="button" value="S" />
<?php
    }
?>
    </td>
    <td><input <?= $type_number ?> name="product_qty[<?=$pcount?>]" value="<?php hecho($product['product_qty'])?>" size="3" /></td>
    <td><input <?= $type_price ?> name="product_cost[<?=$pcount?>]" value="<?= bcadd("0", $product['product_cost'], $product['decimal']) ?>" size="6" /></td>
    <td><input <?= $type_price ?> name="product_online[<?=$pcount?>]" value="<?= bcadd("0", $product['product_online'], $product['decimal']) ?>" size="6" /></td>
    <td><input <?= $type_price ?> name="product_offer[<?=$pcount?>]" value="<?= bcadd("0", $product['product_offer'], $product['decimal']) ?>" size="6" /></td>
    <td><input <?= $type_text ?> name="product_offerau[<?=$pcount?>]" value="<?= $product['product_offerau'] ?>" size="6" readonly="readonly" /></td>
    <td><input <?= $type_text ?> name="product_offerus[<?=$pcount?>]" value="<?= $product['product_offerus'] ?>" size="6" readonly="readonly" /></td>
    <td><input onclick="deleteProduct(<?=$pcount?>)" type="button" value="Delete" /></td>
</tr>
<tr class="alt" id="product_rowb[<?=$pcount?>]">
    <td><a href="javascript:void(rankProduct(<?=$pcount?>, -1))"><img alt="/\" src="/webadmin/images/up_arrow.gif" border="0" /></a><a href="javascript:void(rankProduct(<?=$pcount?>, 1))"><img alt="\/" src="/webadmin/images/down_arrow.gif" border="0" /></a><br /><br />Notes</td>
    <td colspan="9"><textarea cols="80" name="product_notes[<?=$pcount?>]" rows="<?=max(3, count(explode("\n", trim($product['product_notes']))))?>" onkeyup="textareaAutoSize(this, 1, 3)" style="width:100%"><?php hecho(trim($product['product_notes']))?></textarea></td>
</tr>
<?php
return ob_get_clean();
}
function charge_row($ccount = '[CHARGE_COUNT]', $charge = array(), $site_id = 0)
{
    global $DB, $_GLOBALS, $show_usd;
    $hidden = array(
        'charge_updated[' . $ccount . ']' => $charge['charge_updated'],
        'charge_rank[' . $ccount . ']'    => $charge['charge_rank'],
        'charge_id[' . $ccount . ']'      => $charge['charge_id'],
    );
    ob_start();
?>
<tr id="product_charge_rowa[<?=$ccount?>]">
    <td>
    	<?=formatHTMLInputs($hidden)?>
	<input type="text" name="charge_product_id[<?=$ccount?>]" style="text-align:right" value="<?=sprintf('C%02d', $charge['charge_id'])?>" readonly="readonly" size="4" />
    </td>
    <td></td>
    <td><input name="charge_name[<?=$ccount?>]" type="text" value="<?php hecho($charge['charge_name'])?>" style="width:100%" /></td>
    <td></td>
    <td><input name="charge_qty[<?=$ccount?>]" readonly="readonly" size="3" style="text-align:right" type="text" value="<?=(0 != $charge['charge_qty']) ? $charge['charge_qty'] : 1?>" onfocus="this.select()"/></td>
    <td><input name="charge_cost[<?=$ccount?>]" size="6" style="text-align:right" type="text" value="<?=$charge['charge_cost']?>" onfocus="this.select()"/></td>
    <td><input name="charge_online[<?=$ccount?>]" size="6" style="text-align:right" type="text" value="<?=$charge['charge_online']?>" onfocus="this.select()"/></td>
    <td><input name="charge_offer[<?=$ccount?>]" size="6" style="text-align:right" type="text" value="<?=$charge['charge_offer']?>" onfocus="this.select()"/></td>
    <td><input name="charge_offerau[<?=$ccount?>]" readonly="readonly" size="6" style="text-align:right;<?=($show_usd) ? 'display:none;' : ''?>" type="text" value="<?=$charge['charge_offerau']?>" /><input name="charge_offerus[<?=$ccount?>]" readonly="readonly" size="6" style="text-align:right;<?=($show_usd) ? '' : 'display:none;'?>" type="text" value="<?=$charge['charge_offerus']?>" /></td>
    <td><input type="hidden" name="charge_count[<?=$ccount?>]" value="<?=$charge['charge_count']?>" /><input onclick="deleteCharge(<?=$ccount?>)" type="button" value="Delete" /></td>
</tr>
<tr class="alt" id="product_charge_rowb[<?=$ccount?>]">
    <td><a href="javascript:void(rankCharge(<?=$ccount?>, -1))"><img src="/webadmin/images/up_arrow.gif" border="0" /></a><a href="javascript:void(rankCharge(<?=$ccount?>, 1))"><img src="/webadmin/images/down_arrow.gif" border="0" /></a><br /><br />Notes</td>
    <td colspan="9"><textarea name="charge_notes[<?=$ccount?>]" rows="<?=max(3, count(explode("\n", trim($charge['charge_notes']))))?>" onkeyup="textareaAutoSize(this, 1, 3)" style="width:100%"><?php hecho(trim($charge['charge_notes']))?></textarea></td>
</tr>
<?php
return ob_get_clean();
}
function supplier_row($pcount = '[PRODUCT_COUNT]', $product = array(), $generic = false)
{
    global $invoice, $warranty_terms, $condition_terms, $packing_terms, $voltage_terms, $shipping_terms, $plugs_list, $products_need_update, $site_id, $_GLOBALS, $show_usd;
    $invoice_copied = (is_array($products_need_update)) ? true : false;
    $bgcolor        = '';
    if ($product['supplier_count'] < 1) {
        $bgcolor = '#FFAA99';
    } elseif ($product['supplier_count'] > 1) {
        $bgcolor = '#6688BB';
    } elseif ($product['product_weight'] <= 1 && 'y' != $product['generic']) {
        $bgcolor = '#8B00FF';
    }
    $sup_name = $product['supplier_name'];
    if (9 == $invoice['site_id']) {
        $qty = productGetValue($product['product_id'], 'qty');
        if ($qty <= 0) {
            $product['supplier_found'] = 'n';
        }
    }
    $sup_id = (0 != $product['supplier_id']) ? $product['supplier_id'] : productGetValue($product['product_id'], 'supplier');
    if ('' == $sup_name) {
        if (9 == $invoice['site_id'] || 16 == $invoice['site_id']) {
            $sup_name = getSupplierName($sup_id);
        }
    }
    $supCountry = '';
    $yesno      = array('n' => 'No', 'y' => 'Yes');
    $stock      = $yesno + array('s' => 'Stock');
    $disNotes   = $product['supplier_notes'];
    if ($product['product_id'] > 0 && 'y' != $product['generic'] && 9 == $invoice['site_id']) {
        if (9 == $invoice['site_id']) {
            $notes        = '';
            $inStock      = $qty - $product['product_qty'];
            $availability = getAvailabilityName(productGetValue($product['product_id'], 'availability'));
            if (count($product) > 0) {
                if ('y' == $product['supplier_found'] || 's' == $product['supplier_found']) {
                    $notes = 'Stock: Ready To Send (' . $inStock . ' remaining)';
                } else {
                    $notes = 'Out of Stock: ' . $availability;
                }
            }
            $disNotes = (trim('' != $product['supplier_notes'])) ? $product['supplier_notes'] : $notes;
            if ($inStock <= 0) {
                $disNotes = 'Out of Stock: ' . $availability;
            }
        }
        $weight_message = "\nProduct weight is not defined";
        if (strpos($disNotes, $weight_message) === false && $product['product_weight'] <= 0) {
            $disNotes .= $weight_message;
        }
        if (strpos($disNotes, $weight_message) !== false && $product['product_weight'] > 0) {
            $disNotes = str_replace($weight_message, '', $disNotes);
        }
    }
    $update_button = (9 == $invoice['site_id'] && 'y' != $product['generic']);
    $hidden        = array(
        'supplier_id[' . $pcount . ']'           => $sup_id,
        'supplier_product_cost[' . $pcount . ']' => $product['product_cost'],
    );
    ob_start();
?>
<tr id="supplier_rowa[<?=$pcount?>]">
    <th><?=str_replace("\n", "", formatHTMLInputs($hidden))?>PID</th>
    <th>Product Name</th>
    <th>Supplier</th>
    <th>&nbsp;</th>
    <th>Found</th>
    <th>Qty</th>
    <th>Cost <span name="span_supplier_currency[<?=$pcount?>]"><?php hecho($product['supplier_currency'])?></span></th>
    <th class="show_aud">Cost AUD</th>
    <th class="show_usd">Cost USD</th>
</tr>
<tr id="supplier_rowb[<?=$pcount?>]"<?=('' != $bgcolor) ? (' style="background-color:' . $bgcolor . '"') : ''?>>
    <td><input disabled="disabled" name="supplier_product_id[<?=$pcount?>]" size="4" style="text-align:right" type="text" value="<?=$product['product_id']?>" /></td>
    <td><input name="supplier_product_name[<?=$pcount?>]" disabled="disabled" size="12" style="width:100%;overflow:hidden" type="text" value="<?php hecho($product['product_name'])?>" /></td>
    <td><input name="supplier_name[<?=$pcount?>]" size="12" style="width:100%" type="text" value="<?php hecho($sup_name)?>" /></td>
    <td><input onclick="searchSupplier(<?=$pcount?>, getValue('product_id[<?=$pcount?>]'))" type="button" value="S" /></td>
    <td><select name="supplier_found[<?=$pcount?>]"><?=str_replace("\n", "", formatHTMLOptions($stock, $product['supplier_found']))?></select></td>
    <td><input name="supplier_product_qty[<?=$pcount?>]" disabled="disabled" size="3" style="text-align:right" type="text" value="<?=$product['product_qty']?>" onfocus="this.select()"/></td>
    <td><input name="supplier_cost[<?=$pcount?>]" size="6" style="text-align:right" type="text" value="<?=$product['supplier_cost']?>" onfocus="this.select()" /></td>
    <td><input name="product_costau[<?=$pcount?>]" readonly="readonly" size="6" style="text-align:right;<?=($show_usd) ? 'display:none;' : ''?>" type="text" value="<?=$product['product_costau']?>" /><input name="product_costus[<?=$pcount?>]" readonly="readonly" size="6" style="text-align:right;<?=($show_usd) ? '' : 'display:none;'?>" type="text" value="<?=$product['product_costus']?>" /></td>
</tr>
<tr class="alt" id="supplier_rowc[<?=$pcount?>]">
    <td colspan="2" class="left_title">Warranty :</td>
    <td colspan="2"><select name="product_warranty[<?=$pcount?>]" onchange="updateProductNotes('<?=$pcount?>');productChange(<?=$pcount?>)"><option value="0">Not Selected</option><?=str_replace("\n", "", formatHTMLOptions($warranty_terms, $product['product_warranty']))?></select></td>
    <td colspan="1" class="left_title">Voltage :</td>
    <td colspan="3"><select name="product_voltage[<?=$pcount?>]" onchange="updateProductNotes('<?=$pcount?>');productChange(<?=$pcount?>)"><option value="0">Not Selected</option><?=str_replace("\n", "", formatHTMLOptions($voltage_terms, $product['product_voltage']))?></select></td>
</tr>
<tr class="alt" id="supplier_rowd[<?=$pcount?>]">
    <td colspan="2" class="left_title">Plug Type :</td>
    <td colspan="2"><select name="product_plug[<?=$pcount?>]" onchange="updateProductNotes('<?=$pcount?>');productChange(<?=$pcount?>)"><option value="0">Not Selected</option><?=str_replace("\n", "", formatHTMLOptions($plugs_list, $product['product_plug']))?></select></td>
    <td colspan="1" class="left_title" style="white-space:nowrap">Condition :</td>
    <td colspan="3"><select name="product_condition[<?=$pcount?>]" onchange="updateProductNotes('<?=$pcount?>');productChange(<?=$pcount?>)"><option value="0">Not Selected</option><?=str_replace("\n", "", formatHTMLOptions($condition_terms, $product['product_condition']))?></select></td>
</tr>
<tr class="alt" id="supplier_rowe[<?=$pcount?>]">
    <td colspan="2" class="left_title">Packing :</td>
    <td colspan="2"><select name="product_packing[<?=$pcount?>]" onchange="productChange(<?=$pcount?>)"><option value="0">Not Selected</option><?=str_replace("\n", "", formatHTMLOptions($packing_terms, $product['product_packing']))?></select></td>
    <td colspan="1" class="left_title">Terms :</td>
    <td colspan="3"><select name="product_terms[<?=$pcount?>]" onchange="productChange(<?=$pcount?>)"><option value="0">Not Selected</option><?=str_replace("\n", "", formatHTMLOptions($shipping_terms, $product['product_terms']))?></select></td>
</tr>
<tr class="alt" id="supplier_rowf[<?=$pcount?>]">
    <td colspan="2" class="left_title">Currency :</td>
    <td colspan="2"><select name="supplier_currency[<?=$pcount?>]" onchange="productChange(<?=$pcount?>, 'ocost')"><option value="">Not Selected</option><?=str_replace("\n", "", formatHTMLOptions(getCurrencyList(), $product['supplier_currency']))?></select></td>
    <td colspan="1" class="left_title">Country :</td>
    <td colspan="3"><select name="supplier_country[<?=$pcount?>]" onchange="productChange(<?=$pcount?>)"><option value="">Not Selected</option><?=str_replace("\n", "", formatHTMLOptions(getCountryList(), $supCountry))?></select></td>
</tr>
<tr class="alt" id="supplier_rowg[<?=$pcount?>]" style="<?=('y' == $product['generic']) ? 'display:none' : ''?>">
    <td colspan="8" style="text-align:center">
<?php
if ($update_button) {
?>
	<input onclick="updateCostPrice(<?=$product['product_id']?>, <?=$invoice['site_id']?>, <?=$pcount?>)" type="button" value=" Update Product Cost Price " style="background-color:#CDEFEA" />
<?php
}
?>
    </td>
</tr>
<tr class="alt" id="supplier_rowh[<?=$pcount?>]">
    <td>Notes<br /><br /><input type="button" onclick="splitProduct(<?=$pcount?>)" value="Split" /></td>
    <td colspan="7"><textarea cols="80" style="width:100%" name="supplier_notes[<?=$pcount?>]" rows="<?=max(3, count(explode("\n", trim($disNotes))))?>" onkeyup="textareaAutoSize(this, 1, 3)"><?php hecho(trim($disNotes))?></textarea></td>
</tr>
<?php
return ob_get_clean();
}
?>
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionProducts\')">Products Requested</a>')?>
<div id="sectionProducts">
<script type="text/javascript">
//<![CDATA[
var product_count = <?php jecho(max(100, count($invoice_products)))?>;
var charge_count = <?php jecho(max(100, count($invoice_charges)))?>;
var charge_names = <?php jecho(invoiceGetOptionList('charge_id'))?>;
function setChargeData(data, return_message) {
    if (return_message == 'add') {
        var target_id = String(charge_count);
        var site_id = getValue('site_id');
        var mappings = {
            1: {
                1: 13,
                2: 14,
                9: 7,
                16: 16,
                11: 15
            },
            2: {
                11: 20
            },
            3: {
                1: 9,
                2: 10,
		3: 1,
                9: 2,
                16: 12,
                11: 11,
                27: 32,
                20: 26
            },
            4: {
                11: 8
            },
            5: {
                11: 1
            },
            6: {
	    	3: 15,
                11: 5
            },
            7: {
                11: 21
            },
            10: {
                11: data.charge_id
            }
        };
        var charge_id = 0;
        if (mappings[data.class_id] != null) {
            if (mappings[data.class_id][site_id] != null) charge_id = mappings[
                data.class_id][site_id];
            else charge_id = mappings[data.class_id][11];
        }
        var add_charge = true;
        //if (charge_id == 1 && gstAdded) add_charge = false;
        if (charge_id == 1 && site_id == 20) add_charge = false;
        if (add_charge) {
            addCharge();
            //if (charge_id == 1) gstAdded = true;
            setValue('charge_id[' + target_id + ']', charge_id);
            setValue('charge_product_id[' + target_id + ']', 'C' + charge_id);
            setValue('charge_name[' + target_id + ']', charge_names[charge_id]);
            chargeChange(charge_count);
        }
    }
}
function addCharge() {
    //global charge_count
    var success = false;
    var currency = getValue('invoice_currency');
    if (currency == null || currency == '') {
        alert('You are unable to add charges until a currency is selected');
        var currencyElem = getElement('invoice_currency');
        if (currencyElem != null && currencyElem.focus != null) currencyElem
            .focus();
        return false;
    }
    var ccount = String(charge_count);
    $("#charges_table").append(<?=str_replace("[CHARGE_COUNT]", '" + ccount + "', jecho(charge_row('[CHARGE_COUNT]', array('charge_rank' => '[CHARGE_COUNT]'), $invoice['site_id']), 1))?>);
    charge_count++;
    //return charge_count - 1;
    return true;
}
function searchVoucher() {
    var member_id = getValue('member_id');
    url = '/webadmin/gift_voucher/?return_message=invoice&member_id=' + escape(
        member_id);
    if (member_id > 0) openWindow(url, '700', '600', true, 'scrollbars=1');
    else {
        alert('Please select a Member before adding a Gift Voucher');
        var elem = getElement('bill_name');
        if (elem.select) elem.select();
        if (elem.focus) elem.focus();
    }
}
function setVoucherData(data) {
    if (data != null && data['return_message'] == 'invoice') {
        var target_id = String(charge_count);
        var site_id = getValue('site_id');
        addCharge();
        var charge_id = 22;
        setValue('charge_id[' + target_id + ']', charge_id);
        setValue('charge_product_id[' + target_id + ']', 'C' + charge_id);
        setValue('charge_name[' + target_id + ']', 'Gift Voucher (' + data[
            'formatted_id'] + ')');
        setValue('charge_cost[' + target_id + ']', '0.00');
        //setValue('charge_costau['+target_id+']', '0.00');
        // charge offer
        var amount = convertCurrency(0 - data['amount'], data['currency'],
            getValue('invoice_currency'));
        var amountau = convertCurrency(0 - data['amount'], data['currency'],
            'AUD');
        var amountus = convertCurrency(0 - data['amount'], data['currency'],
            'USD');
        setValue('charge_offer[' + target_id + ']', amount);
        setValue('charge_online[' + target_id + ']', amount);
        setValue('charge_offerau[' + target_id + ']', amountau);
        setValue('charge_offerus[' + target_id + ']', amountus);
        // now have to update the voucher_user field....
        setValue('voucher_used', data['id']);
        productUpdateTotals();
    }
}
function addProduct() {
    var success = false;
    var currency = getValue('invoice_currency');
    if (currency == null || currency == '') {
        alert('You are unable to add products until a currency is selected');
        var currencyElem = getElement('invoice_currency');
        if (currencyElem != null && currencyElem.focus != null) currencyElem
            .focus();
        return false;
    }
    var pcount = String(product_count);
    $("#product_table").append(<?=str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(product_row('[PRODUCT_COUNT]', array('product_rank' => '[PRODUCT_COUNT]', 'generic' => 'n')), 1))?>);
    $("#supplier_table").append(<?=str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(supplier_row('[PRODUCT_COUNT]', array('generic' => 'y')), 1))?>);
    product_count++;
    return product_count - 1;
}
function addGeneric() {
    //global product_count;
    var success = false;
    var currency = getValue('invoice_currency');
    if (currency == null || currency == '') {
        alert('You are unable to add products until a currency is selected');
        var currencyElem = getElement('invoice_currency');
        if (currencyElem != null && currencyElem.focus != null) currencyElem
            .focus();
        return false;
    }
    var pcount = String(product_count);
    $("#product_table").append(<?=str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(product_row('[PRODUCT_COUNT]', array('product_rank' => '[PRODUCT_COUNT]', 'product_qty' => '1', 'generic' => 'y')), 1))?>);
    $("#supplier_table").append(<?=str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(supplier_row('[PRODUCT_COUNT]', array('generic' => 'y')), 1))?>);
    product_count++;
    return product_count - 1;
}
function addBulk() {
    var result = addGeneric();
    if (result != false) setValue('generic[' + result + ']', 'b');
    return result;
}
function deleteCharge(row) {
    var charge_id = getValue('charge_id[' + row + ']');
    //if (charge_id == 1) gstAdded = false;
    theRow = getElement("product_charge_rowa[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    theRow = getElement("product_charge_rowb[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    chargeChange();
}
function deleteFreight() {
    setValue('ship_id', '');
    setValue('ship_product_id', '');
    setValue('ship_product_name', '');
    setValue('ship_courier_id', '');
    setValue('ship_notes', '');
    setValue('supplier_ship_notes', '');
    setValue('ship_cost', '');
    setValue('ship_offer', '');
    setValue('ship_online', '');
    setValue('ship_costau', '');
    setValue('ship_costus', '');
    setValue('ship_offerau', '');
    setValue('ship_offerus', '');
    var theRow = getElement("product_ship_rowa");
    if (theRow) theRow.style.display = 'none';
    theRow = getElement("product_ship_rowb");
    if (theRow) theRow.style.display = 'none';
    theRow = getElement("supplier_ship_rowa");
    if (theRow) theRow.style.display = 'none';
    theRow = getElement("supplier_ship_rowb");
    if (theRow) theRow.style.display = 'none';
    shipChange();
}
function deleteProduct(row) {
    var theRow = getElement("product_rowa[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    theRow = getElement("product_rowb[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    theRow = getElement("supplier_rowa[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    theRow = getElement("supplier_rowb[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    theRow = getElement("supplier_rowc[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    theRow = getElement("supplier_rowd[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    theRow = getElement("supplier_rowe[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    theRow = getElement("supplier_rowf[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    theRow = getElement("supplier_rowg[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    theRow = getElement("supplier_rowh[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    theRow = getElement("supplier_rowi[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    productChange();
}
function chargeChange(row, change_type) {
    var do_totals = false;
    do_totals = true;
    if (do_totals) productUpdateTotals();
}
function productChange(row, change_type) {
    var do_totals = false;
    switch (change_type) {
        case 'sid':
            setValue('product_id[' + row + ']', getValue(
                'supplier_product_id[' + row + ']'));
            setValue('sub_id[' + row + ']', getValue('supplier_sub_id[' + row +
                ']'));
            break;
        case 'sname':
            setValue("product_name[" + row + "]", getValue(
                "supplier_product_name[" + row + "]"));
            break;
        case 'sqty':
            setValue("product_qty[" + row + "]", getValue(
                "supplier_product_qty[" + row + "]"));
            productUpdateWeight();
            do_totals = true;
            break;
        case 'scost':
            var supplier_currency = getValue("supplier_currency[" + row + "]");
            var invoice_currency = getValue("invoice_currency");
            var supplier_product_cost = getValue("supplier_product_cost[" +
                row + "]");
            setValue("supplier_cost[" + row + "]", convertCurrency(
                supplier_product_cost, invoice_currency,
                supplier_currency, true));
            setValue("product_costau[" + row + "]", convertCurrency(
                supplier_product_cost, invoice_currency, "AUD", true));
            setValue("product_costus[" + row + "]", convertCurrency(
                supplier_product_cost, invoice_currency, "USD", true));
            setValue("product_cost[" + row + "]", supplier_product_cost);
            do_totals = true;
            break;
        case 'ocost':
            var supplier_currency = getValue("supplier_currency[" + row + "]");
            var invoice_currency = getValue("invoice_currency");
            var supplier_cost = getValue("supplier_cost[" + row + "]");
            setValue("supplier_product_cost[" + row + "]", convertCurrency(
                supplier_cost, supplier_currency, invoice_currency, true
                ));
            setValue("product_costau[" + row + "]", convertCurrency(
                supplier_cost, supplier_currency, "AUD", true));
            setValue("product_costus[" + row + "]", convertCurrency(
                supplier_cost, supplier_currency, "USD", true));
            setValue("product_cost[" + row + "]", convertCurrency(supplier_cost,
                supplier_currency, invoice_currency, true));
            do_totals = true;
            break;
        case 'scostus':
            var supplier_currency = getValue("supplier_currency[" + row + "]");
            var invoice_currency = getValue("invoice_currency");
            var product_costus = Number(getValue("product_costus[" + row +
            "]"));
            setValue("product_costau[" + row + "]", convertCurrency(
                product_costus, "USD", "AUD", true));
            setValue("supplier_cost[" + row + "]", convertCurrency(
                product_costus, "USD", supplier_currency, true));
            setValue("supplier_product_cost[" + row + "]", convertCurrency(
                product_costus, "USD", invoice_currency, true));
            setValue("product_cost[" + row + "]", convertCurrency(
                product_costus, "USD", invoice_currency, true));
            do_totals = true;
            break;
        case 'scostau':
            var supplier_currency = getValue("supplier_currency[" + row + "]");
            var invoice_currency = getValue("invoice_currency");
            var product_costau = Number(getValue("product_costau[" + row +
            "]"));
            setValue("product_costus[" + row + "]", convertCurrency(
                product_costau, "AUD", "USD", true));
            setValue("supplier_cost[" + row + "]", convertCurrency(
                product_costau, "AUD", supplier_currency, true));
            setValue("supplier_product_cost[" + row + "]", convertCurrency(
                product_costau, "AUD", invoice_currency, true));
            setValue("product_cost[" + row + "]", convertCurrency(
                product_costau, "AUD", invoice_currency, true));
            do_totals = true;
            break;
        default:
            do_totals = true;
            break;
    }
    if (do_totals) productUpdateTotals();
}
function getProductRows() {
    var product_table = getElement("product_table");
    var product_rows = [];
    if (product_table != null) {
        var theRows = product_table.rows;
        for (var i = 0; i < theRows.length; i++)
            if (theRows[i].id.substr(0, 13) == 'product_rowa[')
                if (searchArray(theRows[i].id.substr(12), product_rows) == null)
                    product_rows[product_rows.length] = theRows[i].id.substr(
                    12);
    }
    return product_rows;
}
function getChargeRows() {
    var charges_table = getElement("charges_table");
    var charge_rows = [];
    if (charges_table != null) {
        var theRows = charges_table.rows;
        for (var i = 0; i < theRows.length; i++)
            if (theRows[i].id.substr(0, 20) == 'product_charge_rowa[')
                if (searchArray(theRows[i].id.substr(19), charge_rows) == null)
                    charge_rows[charge_rows.length] = theRows[i].id.substr(19);
    }
    return charge_rows;
}
function rankProductsAlphabetically() {
    getElement('prodLink').innerHTML = 'Please Wait...';
    setTimeout("sortAlphabetically()", 500);
}
function sortAlphabetically() {
    // function to rank the products alphabetically....
    var product_rows = getProductRows();
    var ar = [];
    var first_prow = getElement('product_rowa' + product_rows[0]);
    var first_srow = getElement('supplier_rowa' + product_rows[0]);
    for (var i = 0; i < product_rows.length; i++) ar[i] = getValue(
        "product_name" + product_rows[i]) + '^' + product_rows[i];
    ar.sort();
    ar.reverse();
    // now have swap the rows around....
    for (i = 0; i < ar.length; i++) {
        var tmp = explode('^', ar[i]);
        //print_array(tmp);
        var source_row;
        var from_row = tmp[1];
        var target_table = getElement('product_table'); //target_row.parentNode;
        setValue('product_rank' + from_row, ar.length - i);
        if (source_row = getElement("product_rowa" + from_row)) target_table
            .insertBefore(source_row, first_prow);
        var tmpp = source_row;
        if (source_row = getElement("product_rowb" + from_row)) target_table
            .insertBefore(source_row, first_prow);
        first_prow = tmpp;
        var target_table = getElement(
        'supplier_table'); //target_row.parentNode;
        if (source_row = getElement("supplier_rowa" + from_row)) target_table
            .insertBefore(source_row, first_srow);
        var tmps = source_row;
        if (source_row = getElement("supplier_rowb" + from_row)) target_table
            .insertBefore(source_row, first_srow);
        if (source_row = getElement("supplier_rowc" + from_row)) target_table
            .insertBefore(source_row, first_srow);
        if (source_row = getElement("supplier_rowd" + from_row)) target_table
            .insertBefore(source_row, first_srow);
        if (source_row = getElement("supplier_rowe" + from_row)) target_table
            .insertBefore(source_row, first_srow);
        if (source_row = getElement("supplier_rowf" + from_row)) target_table
            .insertBefore(source_row, first_srow);
        if (source_row = getElement("supplier_rowg" + from_row)) target_table
            .insertBefore(source_row, first_srow);
        if (source_row = getElement("supplier_rowh" + from_row)) target_table
            .insertBefore(source_row, first_srow);
        if (source_row = getElement("supplier_rowi" + from_row)) target_table
            .insertBefore(source_row, first_srow);
        first_srow = tmps;
    }
    getElement('prodLink').innerHTML = 'Product Name';
}
function rankProduct(row, direction) {
    var from_row = '[' + row + ']';
    var from_rank = Number(getValue("product_rank" + from_row));
    var swap_row = null;
    var swap_rank = null;
    var product_rows = getProductRows();
    for (var i = 0; i < product_rows.length; i++) {
        var check_row = product_rows[i];
        var check_rank = Number(getValue("product_rank" + check_row));
        if (check_row != from_row && ((direction < 0 && check_rank <
                    from_rank && (swap_rank == null || check_rank > swap_rank)
                    ) || (direction > 0 && check_rank > from_rank && (
                    swap_rank == null || check_rank < swap_rank)))) {
            swap_row = check_row;
            swap_rank = check_rank;
        }
    }
    if (swap_row != null) {
        setValue("product_rank" + from_row, swap_rank);
        setValue("product_rank" + swap_row, from_rank);
        if (direction > 0) {
            var temp = from_row;
            from_row = swap_row;
            swap_row = temp;
        }
        var source_row;
        var target_row = getElement("product_rowa" + swap_row);
        var target_table = target_row.parentNode;
        if (source_row = getElement("product_rowa" + from_row)) target_table
            .insertBefore(source_row, target_row);
        if (source_row = getElement("product_rowb" + from_row)) target_table
            .insertBefore(source_row, target_row);
        var target_row = getElement("supplier_rowa" + swap_row);
        var target_table = target_row.parentNode;
        if (source_row = getElement("supplier_rowa" + from_row)) target_table
            .insertBefore(source_row, target_row);
        if (source_row = getElement("supplier_rowb" + from_row)) target_table
            .insertBefore(source_row, target_row);
        if (source_row = getElement("supplier_rowc" + from_row)) target_table
            .insertBefore(source_row, target_row);
        if (source_row = getElement("supplier_rowd" + from_row)) target_table
            .insertBefore(source_row, target_row);
        if (source_row = getElement("supplier_rowe" + from_row)) target_table
            .insertBefore(source_row, target_row);
        if (source_row = getElement("supplier_rowf" + from_row)) target_table
            .insertBefore(source_row, target_row);
        if (source_row = getElement("supplier_rowg" + from_row)) target_table
            .insertBefore(source_row, target_row);
        if (source_row = getElement("supplier_rowh" + from_row)) target_table
            .insertBefore(source_row, target_row);
        if (source_row = getElement("supplier_rowi" + from_row)) target_table
            .insertBefore(source_row, target_row);
    }
}
function rankCharge(row, direction) {
    var from_row = '[' + row + ']';
    var from_rank = Number(getValue("charge_rank" + from_row));
    var swap_row = null;
    var swap_rank = null;
    var charge_rows = getChargeRows();
    for (var i = 0; i < charge_rows.length; i++) {
        var check_row = charge_rows[i];
        var check_rank = Number(getValue("charge_rank" + check_row));
        if (check_row != from_row && ((direction < 0 && check_rank <
                    from_rank && (swap_rank == null || check_rank > swap_rank)
                    ) || (direction > 0 && check_rank > from_rank && (
                    swap_rank == null || check_rank < swap_rank)))) {
            swap_row = check_row;
            swap_rank = check_rank;
        }
    }
    if (swap_row != null) {
        setValue("charge_rank" + from_row, swap_rank);
        setValue("charge_rank" + swap_row, from_rank);
        if (direction > 0) {
            var temp = from_row;
            from_row = swap_row;
            swap_row = temp;
        }
        var source_row;
        var target_row = getElement("product_charge_rowa" + swap_row);
        var target_table = target_row.parentNode;
        if (source_row = getElement("product_charge_rowa" + from_row))
            target_table.insertBefore(source_row, target_row);
        if (source_row = getElement("product_charge_rowb" + from_row))
            target_table.insertBefore(source_row, target_row);
    }
}
function shipChange(change_type) {
    var do_totals = false;
    switch (change_type) {
        case 'pname':
            setValue("supplier_ship_product_name", getValue(
                "ship_product_name"));
            break;
        case 'pcost':
            setValue("ship_costau", convertCurrency(getValue("ship_cost"),
                getValue("invoice_currency"), "AUD", true));
            setValue("ship_costus", convertCurrency(getValue("ship_cost"),
                getValue("invoice_currency"), "USD", true));
            setValue("supplier_ship_cost", getValue("ship_cost"));
            do_totals = true;
            break;
        case 'sname':
            setValue("ship_product_name", getValue(
                "supplier_ship_product_name"));
            break;
        case 'sqty':
            setValue("ship_qty", getValue("supplier_ship_qty"));
            do_totals = true;
            break;
        case 'scost':
            setValue("ship_costau", convertCurrency(getValue(
                    "supplier_ship_cost"), getValue("invoice_currency"),
                "AUD", true));
            setValue("ship_costus", convertCurrency(getValue(
                    "supplier_ship_cost"), getValue("invoice_currency"),
                "USD", true));
            setValue("ship_cost", getValue("supplier_ship_cost"));
            do_totals = true;
            break;
        case 'scostus':
            setValue("supplier_ship_cost", convertCurrency(getValue(
                "ship_costus[" + row + "]"), "USD", getValue(
                "invoice_currency"), true));
            setValue("ship_costau", convertCurrency(getValue("ship_costus[" +
                row + "]"), "USD", "AUD", true));
            setValue("ship_cost", getValue("supplier_ship_cost"));
            do_totals = true;
            break;
        case 'scostau':
            setValue("supplier_ship_cost", convertCurrency(getValue(
                "ship_costau[" + row + "]"), "AUD", getValue(
                "invoice_currency"), true));
            setValue("ship_costus", convertCurrency(getValue("ship_costau[" +
                row + "]"), "AUD", "USD", true));
            setValue("ship_cost", getValue("supplier_ship_cost"));
            do_totals = true;
            break;
        default:
            do_totals = true;
            break;
    }
    if (do_totals) productUpdateTotals();
}
function productUpdateWeight() {
    var total_weight = 0.0;
    var product_rows = [];
    var theRows = getElement("product_table").rows;
    for (var i = 0; i < theRows.length; i++)
        if (theRows[i].id.substr(0, 13) == 'product_rowa[')
            if (searchArray(theRows[i].id.substr(12), product_rows) == null)
                product_rows[product_rows.length] = theRows[i].id.substr(12);
    for (var i = 0; i < product_rows.length; i++) {
        var rowNum = product_rows[i];
        var addMe = 0;
        var product_qty = Number(getValue("product_qty" + rowNum));
        var product_weight = Number(getValue("product_weight" + rowNum));
        var product_weight_inc = Number(getValue("product_weight_inc" +
        rowNum));
        if (product_qty > 0) {
            addMe = product_weight;
            if (!isNaN(addMe)) total_weight += addMe;
            if (product_qty > 1) {
                addMe = (product_qty - 1) * product_weight_inc;
                if (!isNaN(addMe)) total_weight += addMe;
            }
        }
    }
    setAllValues('ship_weight', Number(total_weight).toFixed(2));
}
function productUpdateTotals() {
    var total_cost = 0.0;
    var total_costau = 0.0;
    var total_costus = 0.0;
    var total_offer = 0.0;
    var total_offerau = 0.0;
    var total_offerus = 0.0;
    var total_online = 0.0;
    var total_onlineau = 0.0;
    var total_onlineus = 0.0;
    var product_rows = [];
    var theRows = getElement("product_table").rows;
    for (var i = 0; i < theRows.length; i++) {
        if (theRows[i].id.substr(0, 13) == 'product_rowa[')
            if (searchArray(theRows[i].id.substr(12), product_rows) == null)
                product_rows[product_rows.length] = theRows[i].id.substr(12);
    }
    var charge_rows = [];
    var theRows1 = getElement("charges_table").rows;
    for (var i = 0; i < theRows1.length; i++) {
        if (theRows1[i].id.substr(0, 20) == 'product_charge_rowa[')
            if (searchArray(theRows1[i].id.substr(19), charge_rows) == null)
                charge_rows[charge_rows.length] = theRows1[i].id.substr(19);
    }
    var in_cur = getValue('invoice_currency');
    for (var i = 0; i < product_rows.length; i++) {
        var rowNum = product_rows[i];
        var addMe = 0;
        var product_qty = Number(getValue("product_qty" + rowNum));
        var product_cost = Number(getValue("product_cost" + rowNum));
        var product_costau = Number(getValue("product_costau" + rowNum));
        var product_costus = Number(getValue("product_costus" + rowNum));
        var product_offer = Number(getValue("product_offer" + rowNum));
        var product_offerau = Number(getValue("product_offerau" + rowNum));
        var product_offerus = Number(getValue("product_offerus" + rowNum));
        var product_online = Number(getValue("product_online" + rowNum));
	var product_onlineau = Number(getValue("product_onlineau" + rowNum));
	var product_onlineus = Number(getValue("product_onlineus" + rowNum));
        addMe = product_qty * product_cost;
        if (!isNaN(addMe)) total_cost += addMe;
        addMe = product_qty * product_costau;
        if (!isNaN(addMe)) total_costau += addMe;
        addMe = product_qty * product_costus;
        if (!isNaN(addMe)) total_costus += addMe;
        addMe = product_qty * product_offer;
        if (!isNaN(addMe)) total_offer += addMe;
        addMe = product_qty * product_offerau;
        if (!isNaN(addMe)) total_offerau += addMe;
        addMe = product_qty * product_offerus;
        if (!isNaN(addMe)) total_offerus += addMe;
        addMe = product_qty * product_online;
        if (!isNaN(addMe)) total_online += addMe;
        addMe = product_qty * product_onlineau;
        if (!isNaN(addMe)) total_onlineau += addMe;
        addMe = product_qty * product_onlineus;
        if (!isNaN(addMe)) total_onlineus += addMe;
    }
    var products_cost = total_cost;
    var products_costau = total_costau;
    var products_costus = total_costus;
    var products_online = total_online;
    var products_onlineau = total_onlineau;
    var products_onlineus = total_onlineus;
    var products_offer = total_offer;
    var products_offerau = total_offerau;
    var products_offerus = total_offerus;
    setAllValues('products_cost', convertCurrency(products_cost.toFixed(2),
        in_cur, '', true));
    setAllValues('products_costau', convertCurrency(products_costau.toFixed(2),
        'AUD', '', true));
    setAllValues('products_costus', convertCurrency(products_costus.toFixed(2),
        'USD', '', true));
    setAllValues('products_offer', convertCurrency(products_offer.toFixed(2),
        in_cur, '', true));
    setAllValues('products_offerau', convertCurrency(products_offerau.toFixed(
        2), 'AUD', '', true));
    setAllValues('products_offerus', convertCurrency(products_offerus.toFixed(
        2), 'USD', '', true));
    setAllValues('products_online', convertCurrency(products_online.toFixed(2),
        in_cur, '', true));
    setAllValues('products_onlineau', convertCurrency(products_onlineau.toFixed(
    	2), 'AUD', '', true));
    setAllValues('products_onlineus', convertCurrency(products_onlineus.toFixed(
    	2), 'USD', '', true));
    if (getValue('ship_id') > 0) {
        addMe = Number(getValue('ship_cost'));
        if (!isNaN(addMe)) total_cost += addMe;
        addMe = Number(getValue('ship_costau'));
        if (!isNaN(addMe)) total_costau += addMe;
        addMe = Number(getValue('ship_costus'));
        if (!isNaN(addMe)) total_costus += addMe;
        addMe = Number(getValue('ship_offer'));
        if (!isNaN(addMe)) total_offer += addMe;
        addMe = Number(getValue('ship_offerau'));
        if (!isNaN(addMe)) total_offerau += addMe;
        addMe = Number(getValue('ship_offerus'));
        if (!isNaN(addMe)) total_offerus += addMe;
        addMe = Number(getValue('ship_online'));
        if (!isNaN(addMe)) total_online += addMe;
    }
    var ship_cost = total_cost - products_cost;
    var ship_costau = total_costau - products_costau;
    var ship_costus = total_costus - products_costus;
    var ship_offer = total_offer - products_offer;
    var ship_offerau = total_offerau - products_offerau;
    var ship_offerus = total_offerus - products_offerus;
    //Hack
    total_offerau = Number(total_offerau);
    total_offerus = Number(total_offerus);
<?php
$charges_amount   = array();
$charges_currency = array();
$charges_percent  = array();
$charges_rounding = array();
$charges_priority = array();
$query            = $DB->query("SELECT * FROM `highwaygroup`.`charges` ORDER BY `charge_priority` ASC");
while ($row = $DB->fetch_assoc($query)) {
    $charges_amount[$row['charge_id']]   = $row['charge_amount'];
    $charges_currency[$row['charge_id']] = $row['charge_amount_currency'];
    $charges_percent[$row['charge_id']]  = $row['charge_percent'];
    $charges_rounding[$row['charge_id']] = $row['charge_rounding'];
    $charges_priority[$row['charge_id']] = intval($row['charge_priority']);
}
?>
    var charges_ids = <?php jecho(array_keys($charges_amount))?>;
    var charges_amount = <?php jecho(array_values($charges_amount))?>;
    var charges_currency = <?php jecho(array_values($charges_currency))?>;
    var charges_percent = <?php jecho(array_values($charges_percent))?>;
    var charges_rounding = <?php jecho(array_values($charges_rounding))?>;
    var charges_priority = <?php jecho(array_values($charges_priority))?>;
    for (var j = 0; j < charges_ids.length; j++) {
            for (var i = 0; i < charge_rows.length; i++) {
                var rowNum = charge_rows[i];
                var charge_id = Number(getValue('charge_id' + rowNum));
                if (charge_id == charges_ids[j]) {
                    var addMe = 0;
                    var amount = charges_amount[j];
                    var currency = charges_currency[j];
                    var percent = Number(charges_percent[j]) / 100.0;
                    var rounding = Number(charges_rounding[j]);
		    var charge_cost = '0.00';
                    var charge_amount = '0.00';
                    var charge_amount_au = '0.00';
                    var charge_amount_us = '0.00';
                    if (rounding == 0.0 && percent == 0.0 && amount == '0.00') {
                    charge_amount = getValue('charge_cost' + rowNum);
                    charge_amount_au = convertCurrency(getValue('charge_cost' +
                            rowNum), getValue('invoice_currency'), 'AUD',
                        true);
                    charge_amount_us = convertCurrency(getValue('charge_cost' +
                            rowNum), getValue('invoice_currency'), 'USD',
                        true);
                } else {
                    charge_amount = Number(convertCurrency(amount, currency,
                        in_cur, true));
                    charge_amount += Number(total_offer) * percent;
                    if (rounding != 0.0) charge_amount = (Math.ceil((
                            total_offer + charge_amount) / rounding) *
                        rounding) - total_offer;
                    charge_amount = convertCurrency(Number(charge_amount)
                        .toFixed(2), in_cur, '', true);
                    charge_amount_au = convertCurrency(charge_amount, in_cur,
                        'AUD', true);
                    charge_amount_us = convertCurrency(charge_amount, in_cur,
                        'USD', true);
                    setValue('charge_cost' + rowNum, convertCurrency(
                        charge_amount, in_cur, '', true));
                    //setValue('charge_cost'+rowNum, charge_amount_au);
                    //setValue('charge_cost'+rowNum, charge_amount_us);
                    setValue('charge_offer' + rowNum, convertCurrency(
                        charge_amount, in_cur, '', true));
                    setValue('charge_offerau' + rowNum, convertCurrency(
                        charge_amount_au, 'AUD', '', true));
                    setValue('charge_offerus' + rowNum, convertCurrency(
                        charge_amount_us, 'USD', '', true));
                    setValue('charge_online' + rowNum, convertCurrency(
                        charge_amount, in_cur, '', true));
                }
                total_cost += Number(charge_amount);
                total_costau += Number(charge_amount_au);
                total_costus += Number(charge_amount_us);
                total_offer += Number(charge_amount);
                total_offerau += Number(charge_amount_au);
                total_offerus += Number(charge_amount_us);
                total_online += Number(charge_amount);
                /*
                total_cost+=convertCurrency(charge_amount, in_cur, '', true);
                total_costau+=convertCurrency(charge_amount_au, 'AUD', '', true);
                total_costus+=convertCurrency(charge_amount_us, 'USD', '', true);
                total_offer+=convertCurrency(charge_amount, in_cur, '', true);
                total_offerau+=convertCurrency(charge_amount_au, 'AUD', '', true);
                total_offerus+=convertCurrency(charge_amount_us, 'USD', '', true);
                total_online+=convertCurrency(charge_amount, in_cur, '', true);
                */
            }
        }
    }
    var charges_offer = total_offer - (products_offer + ship_offer);
    var charges_offerau = total_offerau - (products_offerau + ship_offerau);
    var charges_offerus = total_offerus - (products_offerus + ship_offerus);
    var charges_cost = total_cost - (products_cost + ship_cost);
    var charges_costau = total_costau - (products_costau + ship_costau);
    var charges_costus = total_costus - (products_costus + ship_costus);
    setAllValues('charges_offer', convertCurrency(Number(charges_offer).toFixed(
        2), in_cur, '', true));
    setAllValues('charges_offerau', convertCurrency(Number(charges_offerau)
        .toFixed(2), 'AUD', '', true));
    setAllValues('charges_offerus', convertCurrency(Number(charges_offerus)
        .toFixed(2), 'USD', '', true));
    setAllValues('charges_cost', convertCurrency(Number(charges_cost).toFixed(
        2), in_cur, '', true));
    setAllValues('charges_costau', convertCurrency(Number(charges_costau)
        .toFixed(2), 'AUD', '', true));
    setAllValues('charges_costus', convertCurrency(Number(charges_costus)
        .toFixed(2), 'USD', '', true));
    setAllValues('invoice_cost', convertCurrency(Number(total_cost).toFixed(2),
        in_cur, '', true));
    setAllValues('invoice_online', convertCurrency(Number(total_online).toFixed(
        2), in_cur, '', true));
    setAllValues('invoice_offer', convertCurrency(Number(total_offer).toFixed(
        2), in_cur, '', true));
    setAllValues('invoice_offerau', convertCurrency(Number(total_offerau)
        .toFixed(2), 'AUD', '', true));
    setAllValues('invoice_offerus', convertCurrency(Number(total_offerus)
        .toFixed(2), 'USD', '', true));
    setAllValues('invoice_costau', convertCurrency(Number(total_costau).toFixed(
        2), 'AUD', '', true));
    setAllValues('invoice_costus', convertCurrency(Number(total_costus).toFixed(
        2), 'USD', '', true));
    setAllValues('invoice_profit', convertCurrency(Number(total_offer -
        total_cost).toFixed(2), in_cur, '', true));
    setAllValues('invoice_profitau', convertCurrency(Number(total_offerau -
        total_costau).toFixed(2), 'AUD', '', true));
    setAllValues('invoice_profitus', convertCurrency(Number(total_offerus -
        total_costus).toFixed(2), 'USD', '', true));
}
function productShowOutlet() {
    var plug_id = Number(getValue('plug_id'));
    if (plug_id == null || plug_id < 1) return;
    var url = '/webadmin/world/plugs/show_image.php?id=' + plug_id;
    var features = 'scrollbars, resizable, height=400, width=600';
    window.open(url, 'powerplug_popup', features);
}
function searchProduct(row, searchFor) {
    var currency = getValue('invoice_currency');
    var site_id = getValue('site_id');
    var member_id = getValue('member_id');
    var producttype = getValue('producttype_id');
    if (site_id == '1' || site_id == '2') site_id = '1|2';
    //var url='/webadmin/products/index.php?action=list';
    //var url='productsearch.php?type='+producttype;
    var url = 'productsearch.php?';
    url += '&return_message=select_' + row;
    url += '&for=' + escape(searchFor);
    if (currency != null) url += '&currency=' + currency;
    //if(site_id!=null) url+='&site_id='+site_id;
    //if(member_id!=null) url+='&member_id='+member_id;
    var features = ''; //'scrollbars, resizable';//height=700, width=800
    window.open(url, 'product_popup', features);
}
function searchProductById(row, searchFor) {
    var currency = getValue('invoice_currency');
    var site_id = getValue('site_id');
    var member_id = getValue('member_id');
    var producttype = getValue('producttype_id');
    if (site_id == '1') site_id = '1|2';
    //var url='/webadmin/products/index.php?action=list';
    //var url='productsearch.php?type='+producttype;
    var url = 'productsearch.php?';
    url += '&return_message=select_' + row;
    url += '&search_id=' + escape(searchFor);
    if (currency != null) url += '&currency=' + currency;
    //if(site_id!=null) url+='&site_id='+site_id;
    //if(member_id!=null) url+='&member_id='+member_id;
    var features = ''; //'scrollbars, resizable';//height=700, width=800
    window.open(url, 'product_popup', features);
}
function searchSupplier(row, searchFor) {
    //var url='/webadmin/suppliers/index.php?action=list';
    var url = 'suppliersearch.php?action=list';
    url += '&return_message=select_' + row;
    if (searchFor != 'None') url += '&product_id=' + escape(searchFor);
    var features = 'scrollbars, resizable'; //height=700, width=800
    window.open(url, 'Search', features);
}
function selectSupplier(row, supplier_id) {
    var url = '/webadmin/suppliers/index.php?action=select';
    url += '&return_message=select_' + row;
    url += '&supplier_id=' + supplier_id;
    var features = 'scrollbars, resizable';
    window.open(url, 'Search', features);
}
function searchDefaultSupplier(searchFor) {
    var url = '/webadmin/suppliers/index.php?action=list';
    url += '&return_message=default';
    if (searchFor != 'None') url += '&search_for=' + escape(searchFor);
    var features = 'scrollbars, resizable'; //height=700, width=800
    window.open(url, 'Search', features);
}
function splitProduct(row) {
    var rowNum = '[' + row + ']';
    var product_qty = getValue('product_qty' + rowNum);
    if (product_qty == null || product_qty < 2) return;
    product_qty = Number(product_qty);
    var split_qty = 0;
    if (product_qty < 4) {
        split_qty = confirm(
            'Are you sure you would like to split this product?');
        if (!split_qty) return;
        split_qty = 1;
    } else {
        split_qty = prompt(
            'How many of this product would you like to split? (1 - ' + (
                product_qty - 1) + ')', '1');
        if (split_qty == null || split_qty < 1 || split_qty >= product_qty)
            return;
    }
    var new_product = product_count;
    addProduct();
    var fields = [
        'product_cost', 'product_id', 'product_name',
        'product_notes', 'product_offer', 'product_offerau',
        'product_offerus', 'product_online', 'product_weight',
        'product_weight_inc', 'supplier_product_cost',
        'supplier_cost_onload', 'supplier_cost_orig',
        'supplier_cost_orig_curr', 'product_costau', 'product_costus',
        'supplier_country', 'supplier_found', 'supplier_id',
        'supplier_id_orig', 'supplier_name', 'supplier_notes',
        'supplier_product_id', 'supplier_product_name',
        'supplier_product_qty'
    ];
    for (var i = 0; i < fields.length; i++) {
        var field = fields[i];
        setValue(field + '[' + new_product + ']', getValue(field + rowNum));
        setValue("invoice_products[" + new_product + "][" + field + "]",
            getValue("invoice_products" + rowNum + "[" + field + "]"));
    }
    setValue('product_qty[' + new_product + ']', split_qty);
    setValue('supplier_product_qty[' + new_product + ']', split_qty);
    setValue('product_qty' + rowNum, product_qty - split_qty);
    setValue('supplier_product_qty' + rowNum, product_qty - split_qty);
}
function setBulkData(data) {
    if (data == null) return;
    if (data['return_message'] == 'add_bulk') {
        var row = addBulk();
        var currency = getValue('invoice_currency');
        var bulk_number = '000000' + data['bulk_id'];
        bulk_number = 'BO-' + bulk_number.substring(bulk_number.length - 4);
        setValue('product_id[' + row + ']', bulk_number);
        setValue('sub_id[' + row + ']', '0');
        setValue('product_name[' + row + ']', data['title']);
        setValue('product_qty[' + row + ']', '1');
        setValue('product_cost[' + row + ']', convertCurrency(data['bulk_cost'],
            'AUD', currency));
        setValue('supplier_product_cost[' + row + ']', convertCurrency(data[
            'bulk_cost'], 'AUD', currency));
        setValue('product_costau[' + row + ']', data['bulk_cost']);
        setValue('product_costus[' + row + ']', convertCurrency(data[
            'bulk_cost'], 'AUD', 'USD'));
        setValue('product_online[' + row + ']', convertCurrency(data[
            'bulk_online'] / 1.1, 'AUD', currency));
        setValue('product_offer[' + row + ']', convertCurrency(data[
            'bulk_offer'] / 1.1, 'AUD', currency));
        setValue('product_offerau[' + row + ']', convertCurrency(data[
            'bulk_offer'] / 1.1, 'AUD'));
        setValue('product_offerus[' + row + ']', convertCurrency(data[
            'bulk_offer'] / 1.1, 'USD'));
        setValue('product_weight[' + row + ']', data['bulk_weight']);
        setValue("invoice_products[" + row + "][product_weight_inc]", data[
            'bulk_weight']);
        setValue("invoice_products[" + row + "][bulk_sizes]", data[
            'bulk_sizes']);
        setValue('product_warranty[' + row + ']', data['bulk_warranty']);
        setValue('product_condition[' + row + ']', data['bulk_condition']);
        setValue('product_terms[' + row + ']', data['bulk_terms']);
        setValue('product_voltage[' + row + ']', data['bulk_voltage']);
        setValue('generic_code[' + row + ']', bulk_number);
        setValue('product_price_changed[' + row + ']', 'n');
        if (data['bulk_notes'] != null) setValue('product_notes[' + row + ']',
            data['bulk_notes']);
        if (data['bulk_weight'] <= 0.0 && addMissingWeight != null)
            addMissingWeight(bulk_number, data['title'], data['bulk_url']);
        var data2 = data;
        data2['return_message'] = 'bulk_select_' + row;
        setSupplierData(data2);
        productUpdateWeight();
        productChange(row);
    }
}
function setProductData(data) {
    if (data == null) return;
    if (data['return_message'].substring(0, 7) == 'select_') {
        var row = data['return_message'].substring(7);
        /*
        generic[100]
        generic_code[100]
        prod_count[100]
        product_cost[100]
        product_id[100]
        product_name[100]
        product_notes[100]
        product_offer[100]
        product_offerau[100]
        product_offerus[100]
        product_online[100]
        product_price_changed[100]
        product_qty[100]
        product_rank[100]
        product_updated[100]
        product_weight[100]
        product_weight_inc[100]
        sub_id[100]
        */
        if (data['product_id'] == null) data['product_id'] = getValue(
            'product_id[' + row + ']');
        if (data['sub_id'] == null) data['sub_id'] = 0;
        if (data['min_order'] == null) data['min_order'] = 1;
        if (data['product_cost'] == null) data['product_cost'] = '0.00';
        if (data['product_online'] == null) data['product_online'] = '0.00';
        if (data['product_offer'] == null) data['product_offer'] = '0.00';
        if (data['product_weight'] == null) data['product_weight'] = 0.0;
        if (data['product_weight_inc'] == null) data['product_weight_inc'] =
        0.0;
        if (data['product_weight_inc'] == 0.0) data['product_weight_inc'] =
            data['product_weight'];
        if (data['product_notes'] == null) data['product_notes'] = '';
        if (data['product_condition'] == null) data['product_condition'] = 0;
        setValue('product_id[' + row + ']', data['product_id']);
        setValue('product_name[' + row + ']', data['product_name']);
        setValue('product_qty[' + row + ']', data['min_order']);
        setValue('product_cost[' + row + ']', data['product_cost']);
        setValue('supplier_product_cost[' + row + ']', data['product_cost']);
        setValue('product_costau[' + row + ']', data['product_costau']);
        setValue('product_costus[' + row + ']', data['product_costus']);
        setValue('product_online[' + row + ']', data['product_online']);
        setValue('product_offer[' + row + ']', data['product_offer']);
        setValue('product_offerau[' + row + ']', data['product_offerau']);
        setValue('product_offerus[' + row + ']', data['product_offerus']);
        setValue('product_weight[' + row + ']', data['product_weight']);
        setValue('product_weight_inc[' + row + ']', data['product_weight_inc']);
        setValue('decimal[' + row + ']', data['dPoints']);
        setValue('product_notes[' + row + ']', data['product_notes']);
        setValue('product_warranty[' + row + ']', data['warranty']);
        setValue('product_condition[' + row + ']', data['product_condition']);
        setValue('product_price_changed[' + row + ']', 'n');
        setValue('supplier_product_qty[' + row + ']', data['min_order']);
        //if(data['producttype_id']!=null)
        //setValue('producttype_id', data['producttype_id']);
        //category=data['producttype_id'];
        //if(data['product_weight']<=0.0 && addMissingWeight!=null)
        //addMissingWeight(data['product_id'], data['product_name'], data['product_url']);
    }
    setSupplierData(data);
    if (data['supplier'] != null) {
        if (typeof(data['supplier']) == "object") selectSupplier(row, data[
            'supplier'][0]);
        else selectSupplier(row, data['supplier']);
    }
    productUpdateWeight();
    productChange(row);
}
function setForwardersData(data) {
    if (data['return_message'].substring(0, 7) == 'select_') {
        var row = data['return_message'].substring(7);
        setValue('supplier_currency[' + row + ']', data['currency']);
        //setValue('supplier_cost['+row+']', data['product_cost']);
        setValue('supplier_id[' + row + ']', data['id']);
        //setValue('supplier_product_id['+row+']', data['product_id']);
        //setValue('supplier_sub_id['+row+']', data['sub_id']);
        //setValue('supplier_product_name['+row+']', data['supplier_product_name']);
        setValue('supplier_name[' + row + ']', data['name']);
        setValue('supplier_country[' + row + ']', data['country']);
        setValue('supplier_found[' + row + ']', 'y');
        productChange(row, 'sfound');
    }
}
function setForwarderData(data) {
    if (data['return_message'].substring(0, 7) == 'select_') {
        var row = data['return_message'].substring(7);
        //setValue('supplier_currency['+row+']', data['cost_currency']);
        //setValue('supplier_cost['+row+']', data['product_cost']);
        setValue('supplier_id[' + row + ']', data['id']);
        //setValue('supplier_product_id['+row+']', data['product_id']);
        //setValue('supplier_sub_id['+row+']', data['sub_id']);
        //setValue('supplier_product_name['+row+']', data['supplier_product_name']);
        setValue('supplier_name[' + row + ']', data['name']);
        setValue('supplier_country[' + row + ']', data['country']);
        setValue('supplier_found[' + row + ']', 'y');
        productChange(row, 'sfound');
    }
}
function setSupplierData(data) {
    if (data == null) return;
    if (data['return_message'] == 'default') {
        setValue('def_sup_name', data['supplier_name']);
        setValue('default_supplier', data['id']);
        if (data['warranty'] != null) setValue('def_warranty', data[
        'warranty']);
        if (data['condition'] != null) setValue('def_condition', data[
            'condition']);
        if (data['packing'] != null) setValue('def_packing', data['packing']);
        if (data['voltage'] != null) setValue('def_voltage', data['voltage']);
        if (data['plug'] != null) setValue('def_plug', data['plug']);
        if (data['terms'] != null) setValue('def_terms', data['terms']);
        if (data['currency'] != null) setValue('def_currency', data[
        'currency']);
        if (data['supplier_country'] != null) {
            setValue('def_country', data['country']);
            setValue('supply_country', data['country']);
        }
    }
    if (data['return_message'].substring(0, 7) == 'select_') {
        var row = data['return_message'].substring(7);
        /*
        product_condition[100]
        product_costau[100]
        product_costus[100]
        product_packing[100]
        product_plug[100]
        product_terms[100]
        product_voltage[100]
        product_warranty[100]
        span_supplier_currency[100]
        supplier_cost[100]
        supplier_country[100]
        supplier_currency[100]
        supplier_found[100]
        supplier_id[100]
        supplier_name[100]
        supplier_notes[100]
        supplier_product_cost[100]
        supplier_product_id[100]
        supplier_product_name[100]
        supplier_product_qty[100]
        supplier_sub_id[100]
        */
        var keys = {
            'supplier_country': 'country',
            'supplier_id': 'id',
            'supplier_product_name': 'product_name'
        };
        for (var i in keys)
            if (data[i] == '' || data[i] == null) data[i] = data[keys[i]];
        var keys = {
            'supplier_cost': 'product_cost',
            'supplier_country': 'supplier_country',
            'supplier_currency': 'cost_currency',
            'supplier_id': 'supplier_id',
            'supplier_name': 'supplier_name',
            'supplier_product_id': 'product_id',
            'supplier_product_name': 'supplier_product_name'
        };
        for (var i in keys)
            if (data[keys[i]] != null) setValue(i + "[" + row + "]", data[keys[
                i]]);
        setValue('supplier_found[' + row + ']', 'y');
        productChange(row, 'scost');
        productChange(row, 'sfound');
    }
}
function setExtraSupplierData() {
    var ids = getProductRows();
    var def_country = '';
    var pack = '';
    var terms = '';
    for (key in ids) {
        var tmp = getValue('supplier_country' + ids[key]);
        if (def_country == '' || tmp == def_country) def_country = tmp;
        else def_country = '';
        tmp = getValue('product_packing' + ids[key]);
        if (pack == '' || tmp == pack) pack = tmp;
        else pack = '';
        tmp = getValue('product_terms' + ids[key]);
        if (terms == '' || tmp == terms) terms = tmp;
        else terms = '';
    }
    var tm = getElement('terms_id');
    tm.value = terms;
    tm.style.backgroundColor = '#' + ((terms == 0) ? 'EE90A0' : 'FFFFFF');
    var dc = getElement('supply_country');
    dc.value = def_country;
    dc.style.backgroundColor = '#' + ((def_country == '') ? 'EE90A0' :
    'FFFFFF');
    var pr = getElement('pack_requirement_id');
    pr.value = pack;
    pr.style.backgroundColor = '#' + ((pack == 0) ? 'EE90A0' : 'FFFFFF');
}
function updateAllSuppliers(supplier_name, obj) {
    var old_val = obj.value;
    obj.value = 'Please Wait...Updating';
    var supplier_id = getValue('default_supplier');
    var rows = getElement('supplier_table').rows;
    var str = '';
    var ids = Array();
    var count = 0;
    for (key in rows) {
        var n = rows[key].id;
        if (n && n != null) {
            var t1 = n.split("[");
            var t2 = t1[1].split("]");
            if (!searchArray(t2[0], ids)) {
                ids[count] = t2[0];
                count++;
            }
        }
    }
    for (key in ids) {
        setValue('supplier_name[' + ids[key] + ']', supplier_name);
        setValue('supplier_id[' + ids[key] + ']', supplier_id);
        var updateNotes = false;
        if (getValue('def_warranty') != 0) {
            setValue('product_warranty[' + ids[key] + ']', getValue(
                'def_warranty'));
            updateNotes = true;
        }
        if (getValue('def_condition') != 0) {
            setValue('product_condition[' + ids[key] + ']', getValue(
                'def_condition'));
            updateNotes = true;
        }
        if (getValue('def_voltage') != 0) {
            setValue('product_voltage[' + ids[key] + ']', getValue(
                'def_voltage'));
            updateNotes = true;
        }
        if (getValue('def_plug') != 0) {
            setValue('product_plug[' + ids[key] + ']', getValue('def_plug'));
            updateNotes = true;
        }
        if (getValue('def_currency') != "") {
            setValue('supplier_currency[' + ids[key] + ']', getValue(
                'def_currency'));
            //productChange(ids[key], 'scostau');
            productChange(ids[key], 'scostus');
            updateNotes = true;
        }
        if (getValue('def_country') != "") {
            setValue('supplier_country[' + ids[key] + ']', getValue(
                'def_country'));
            setValue('supply_country', getValue('def_country'));
        }
        if (getValue('def_packing') != 0) setValue('product_packing[' + ids[
            key] + ']', getValue('def_packing'));
        if (getValue('def_terms') != 0) setValue('product_terms[' + ids[key] +
            ']', getValue('def_terms'));
        if (updateNotes) updateProductNotes(ids[key]);
    }
    obj.value = old_val;
}
function updateProductNotes(prod_count) {
    var warranty = <?php jecho($warranty_terms)?>;
    var voltage = <?php jecho($voltage_terms)?>;
    var notes = [];
    var val1 = getValue('product_voltage[' + prod_count + ']');
    var val2 = getValue('product_plug[' + prod_count + ']');
    var val3 = getValue('product_warranty[' + prod_count + ']');
    var val4 = getValue('product_condition[' + prod_count + ']');
    var count = 0;
    if (val1 != 0) {
        notes[count] = 'Power Supply Voltage: ' + voltage[val1];
        count++
    }
    if (val2 != 0) {
        notes[count] = 'Power Adapter ID: ' + val2;
        count++;
    }
    if (val3 != 0) {
        notes[count] = 'Warranty: ' + warranty[val3];
        count++;
    }
    if (val4 == 2) {
        notes[count] = 'Condition: Used';
        count++;
    }
    if (getValue('product_updated[' + prod_count + ']') == 'n') setValue(
        'product_notes[' + prod_count + ']', notes.join("\n"));
}
function actionPurchasePreview(theFrm) {
    var oldact = theFrm.action;
    var oldtrg = theFrm.target;
    theFrm.action = "invoice_purchase_preview.php";
    theFrm.target = "_blank";
    theFrm.submit();
    theFrm.action = oldact;
    theFrm.target = oldtrg;
}
function actionPurchaseOrder(theFrm) {
    //Do a check first
    var rows = getElement('product_table').rows;
    var ids = [];
    var count = 0;
    for (var i = 0; i < rows.length; i++) {
        var n = rows[i].id;
        if (n != null) {
            var t1 = n.split("[");
            var t2 = t1[1].split("]");
            if (!array_search(t2[0], ids)) ids[count++] = t2[0];
        }
    }
    for (var i = 0; i < ids.length; i++)
        if (getValue('supplier_id[' + ids[i] + ']') == 0) {
            alert(
                "One or more products do not have a supplier\n\nPlease correct this before trying again");
            return false;
        }
    var oldact = theFrm.action;
    theFrm.action = "?action=do_purchase";
    theFrm.submit();
    theFrm.action = oldact;
}
function updateCostPrice(id, site_id, row_id) {
    var url = 'update_cost_price.php?product_id=' + id + '&site_id=' + site_id +
        '&return_message=' + row_id + '&currency=' + getValue(
            'invoice_currency');
    if (confirm('This will update the cost price for ID#' + id +
        '. Continue??')) window.open(url, '', 'width=10,height=10');
}
function setCostPrice(data) {
    row_id = data['return_message'];
    setValue('supplier_product_cost[' + row_id + ']', data['product_cost']);
    setValue('product_costau[' + row_id + ']', data['product_cost_au']);
    setValue('product_costus[' + row_id + ']', data['product_cost_us']);
    productChange(row_id, 'scost');
    //productChange(row_id, 'scostau');
    productChange(row_id, 'scostus');
}
function updatePrices(http_request, row) {
    //alert(http_request.readyState);
    if (http_request.readyState == 4) {
        if (http_request.status == 200) {
            //alert(http_request.responseText);
            var prices = explode(', ', http_request.responseText);
            var price_changed = getValue('product_price_changed[' + row + ']');
            if (price_changed == 'n') {
                setValue('product_online[' + row + ']', prices[0]);
                setValue('product_offer[' + row + ']', prices[0]);
                //setValue('product_offerau[' + row + ']', prices[1]);
                setValue('product_offerus[' + row + ']', prices[1]);
            }
        } else alert('There was a problem with the request.');
    }
}
function makeProductIndividual(pcount) {
    var product_id = getValue('product_id[' + pcount + ']');
    var sub = getValue('sub_id[' + pcount + ']');
    var war = getValue('product_warranty[' + pcount + ']');
    var con = getValue('product_condition[' + pcount + ']');
    var vlt = getValue('product_voltage[' + pcount + ']');
    var pck = getValue('product_packing[' + pcount + ']');
    var trm = getValue('product_terms[' + pcount + ']');
    var cnt = getValue('supplier_country[' + pcount + ']');
    //var plg=getValue('product_plug['+pcount+']');
    var url = '/webadmin/products/products_add.php?link_id=' + product_id;
    if (sub != '') url += '&sub_id=' + sub;
    if (war != 0) url += '&warranty=' + war;
    if (vlt != 0) url += '&voltage=' + vlt;
    if (con != 0) url += '&condition=' + con;
    if (pck != 0) url += '&packing_terms=' + pck;
    if (trm != 0) url += '&ship_terms=' + trm;
    if (cnt != '') url += '&location=' + cnt;
    window.open(url);
}
function addFreight(theFrm) {
    var oldact = theFrm.action;
    var oldtrg = theFrm.target;
    theFrm.action = "courier_search_popup.php";
    theFrm.target = '_blank';
    theFrm.submit();
    theFrm.action = oldact;
    theFrm.target = oldtrg;
}

<?php
$charges = array();
$query = $DB->query("SELECT * FROM `highwaygroup`.`charges` ORDER BY `charge_priority` ASC");
while ($row = $DB->fetch_assoc($query)) {
    $charges[$row['charge_id']] = $row;
}
$DB->free_result($query);
?>
let Charges = <?php jecho($charges) ?>;

let CHARGE_PAYPAL = 1;
let CHARGE_SMALL_ORDER = 2;
let CHARGE_ADVANCEMENT = 3;
let CHARGE_COMMISSION = 4;
let CHARGE_CUSTOMS = 5;
let CHARGE_DISCOUNT2_5 = 6;
let CHARGE_DISCOUNT5 = 7;
let CHARGE_DISCOUNT10 = 8;
let CHARGE_DISCOUNT15 = 9;
let CHARGE_DISCOUNT20 = 10;
let CHARGE_DISCOUNT25 = 11;
let CHARGE_DISCOUNT30 = 12;
let CHARGE_COUPON = 13;
let CHARGE_DISCOUNT = 14;
let CHARGE_PACKING_AND_HANDLING = 15;
let CHARGE_SHIPPING_INSURANCE = 16;

function product_supplier_product_events(context) {
    //supplier_product_id = (product_id) => product_id;
    //supplier_sub_id = (sub_id) => sub_id;
    $(InvoiceProduct.selector(["product_id", "sub_id"]), context).change(function() {
        InvoiceProduct.set("supplier_" + InvoiceProduct.field(this), InvoiceProduct.row(this), $(this).val());
    });

    //product_id = (supplier_product_id) => supplier_product_id;
    //sub_id = (supplier_sub_id) => supplier_sub_id;
    $(InvoiceProduct.selector(["supplier_product_id", "supplier_sub_id"]), context).change(function() {
        //TODO: No bubble
        //InvoiceProduct.set(InvoiceProduct.field(this).substr(9), InvoiceProduct.row(this), $(this).val());
    });

    //product_updated = (product_notes) => 'y';
    $(InvoiceProduct.selector("product_notes"), context).change(function() {
        InvoiceProduct.set("product_updated", InvoiceProduct.row(this), "y");
    });

    //product_total_weight = (product_qty, product_weight, product_weight_inc) => {-1:"0.000", 0:weight, 1:bcadd(product_weight, bcmul(bcsub(product_qty, "1"), product_weight_inc, 3), 3)}[bccomp(product_qty, "1")];
    $(InvoiceProduct.selector(["product_qty", "product_weight", "product_weight_inc"]), context).change(function () {
        var row = InvoiceProduct.row(this);
        var product_qty = InvoiceProduct.get("product_qty", row);
        var comp = bccomp(product_qty, "1");
        if (comp < 0) return InvoiceProduct.set("product_total_weight", row, "0.000");
        var product_weight = InvoiceProduct.get("product_weight", row);
        if (comp == 0) return InvoiceProduct.set("product_total_weight", row, product_weight);
        var product_weight_inc = InvoiceProduct.get("product_weight_inc", row);
        InvoiceProduct.set("product_total_weight", row, bcadd(product_weight, bcmul(bcsub(product_qty, "1"), product_weight_inc, 3), 3));
    });

    //product_total_offer = (product_qty, product_offer, decimal) => bcmul(product_qty, product_offer, decimal);
    $(InvoiceProduct.selector(["product_qty", "product_offer", "decimal"]), context).change(function () {
        var row = InvoiceProduct.row(this);
        InvoiceProduct.set("product_total_offer", row, bcmul(
            InvoiceProduct.get("product_qty", row),
            InvoiceProduct.get("product_offer", row),
            InvoiceProduct.get("decimal", row)
        ));
    });

    //product_total_online = (product_qty, product_online, decimal) => bcmul(product_qty, product_online, decimal);
    $(InvoiceProduct.selector(["product_qty", "product_online", "decimal"]), context).change(function () {
        var row = InvoiceProduct.row(this);
        InvoiceProduct.set("product_total_online", row, bcmul(
            InvoiceProduct.get("product_qty", row),
            InvoiceProduct.get("product_online", row),
            InvoiceProduct.get("decimal", row)
        ));
    });

    //product_total_cost = (product_qty, product_cost, decimal) => bcmul(product_qty, product_cost, decimal);
    $(InvoiceProduct.selector(["product_qty", "product_cost", "decimal"]), context).change(function () {
        var row = InvoiceProduct.row(this);
        InvoiceProduct.set("product_total_cost", row, bcmul(
            InvoiceProduct.get("product_qty", row),
            InvoiceProduct.get("product_cost", row),
            InvoiceProduct.get("decimal", row)
        ));
    });

    //supplier_total_cost = (product_qty, supplier_cost, decimal) => bcmul(product_qty, supplier_cost, decimal);
    $(InvoiceProduct.selector(["product_qty", "supplier_cost", "decimal"]), context).change(function () {
        var row = InvoiceProduct.row(this);
        InvoiceProduct.set("supplier_total_cost", row, bcmul(
            InvoiceProduct.get("product_qty", row),
            InvoiceProduct.get("supplier_cost", row),
            InvoiceProduct.get("decimal", row)
        ));
    });

    //product_offer = (product_offerus, decimal) => convertCurrency(product_offerus, "USD", invoice_currency, decimal);
    $(InvoiceProduct.selector("product_offerus")).change(function () {
        //Don't bubble
        console.log(event);
        /*
        var row = InvoiceProduct.row(this);
        var decimal = InvoiceProduct.get("decimal", row);
        InvoiceProduct.set("product_offer", row, Invoice.fromCurrency($(this).val(), "USD", decimal);
        */
    });

    //product_offer = (product_offerau, decimal) => convertCurrency(product_offerau, "AUD", invoice_currency, decimal);
    $(InvoiceProduct.selector("product_offerau")).change(function () {
        //Don't bubble
        console.log(event);
        /*
        var row = InvoiceProduct.row(this);
        var decimal = InvoiceProduct.get("decimal", row);
        InvoiceProduct.set("product_offer", row, Invoice.fromCurrency($(this).val(), "AUD", decimal);
        */
    });

    //ship_weight = (product_total_weight) => sum(product_total_weight);
    $(InvoiceProduct.selector("product_total_weight"), context).change(function () {
        Invoice.set("ship_weight", InvoiceProduct.sum("product_total_weight", 3));
    });

    //products_offer = (product_total_offer) => sum(product_total_offer);
    $(InvoiceProduct.selector("product_total_offer"), context).change(function () {
        Invoice.set("products_offer", InvoiceProduct.sum("product_total_offer", 2));
    });

    //products_online = (product_total_online) => sum(product_total_online);
    $(InvoiceProduct.selector("product_total_online"), context).change(function () {
        Invoice.set("products_online", InvoiceProduct.sum("product_total_online", 2));
    });

    //products_cost = (product_total_cost) => sum(product_total_cost);
    $(InvoiceProduct.selector("product_total_cost"), context).change(function () {
        Invoice.set("products_cost", InvoiceProduct.sum("product_total_cost", 2));
    });

    //supplier_cost = (supplier_total_cost) => sum(supplier_total_cost);
    $(InvoiceProduct.selector("supplier_total_cost"), context).change(function () {
        Invoice.set("supplier_cost", InvoiceProduct.sum("supplier_total_cost", 2));
    });

    //product_costau  = (product_cost) => convertCurrency(product_cost, invoice_currency, "AUD");
    //product_costus  = (product_cost) => convertCurrency(product_cost, invoice_currency, "USD");
    //product_offerau = (product_offer) => convertCurrency(product_offer, invoice_currency, "AUD");
    //product_offerus = (product_offer) => convertCurrency(product_offer, invoice_currency, "USD");
    $(InvoiceProduct.selector(["product_cost", "product_offer"]), context).change(function () {
        var row = InvoiceProduct.row(this);
        var field = InvoiceProduct.field(this);
        InvoiceProduct.set(field + "us", row, Invoice.toCurrency($(this).val(), "USD", 4));
        InvoiceProduct.set(field + "au", row, Invoice.toCurrency($(this).val(), "AUD", 4));
    });

    //products_located = (supplier_found) => allMatch(supplier_found, "Y");
    $(InvoiceProduct.selector("supplier_found"), context).change(function () {
        Invoice.set("products_located", InvoiceProduct.allMatch("supplier_found", "y") ? "y" : "n");
    });
}

function product_supplier_charge_events(context) {
    //charge_updated = (charge_notes) => 'y';
    $(InvoiceCharge.selector("charge_notes"), context).change(function() {
        InvoiceCharge.set("charge_updated", InvoiceCharge.row(this), "y");
    });

    //charge_total_offer = (charge_qty, charge_offer, decimal) => bcmul(charge_qty, charge_offer, decimal);
    $(InvoiceCharge.selector(["charge_qty", "charge_offer", "decimal"]), context).change(function () {
        var row = InvoiceCharge.row(this);
        InvoiceCharge.set("charge_total_offer", row, bcmul(
            InvoiceCharge.get("charge_qty", row),
            InvoiceCharge.get("charge_offer", row),
            InvoiceCharge.get("decimal", row)
        ));
    });

    //charge_total_online = (charge_qty, charge_online, decimal) => bcmul(charge_qty, charge_online, decimal);
    $(InvoiceCharge.selector(["charge_qty", "charge_online", "decimal"]), context).change(function () {
        var row = InvoiceCharge.row(this);
        InvoiceCharge.set("charge_total_online", row, bcmul(
            InvoiceCharge.get("charge_qty", row),
            InvoiceCharge.get("charge_online", row),
            InvoiceCharge.get("decimal", row)
        ));
    });

    //charge_total_cost = (charge_qty, charge_cost, decimal) => bcmul(charge_qty, charge_cost, decimal);
    $(InvoiceCharge.selector(["charge_qty", "charge_cost", "decimal"]), context).change(function () {
        var row = InvoiceCharge.row(this);
        InvoiceCharge.set("charge_total_cost", row, bcmul(
            InvoiceCharge.get("charge_qty", row),
            InvoiceCharge.get("charge_cost", row),
            InvoiceCharge.get("decimal", row)
        ));
    });

    //charge_offer = (charge_offerus, decimal) => convertCurrency(charge_offerus, "USD", invoice_currency, decimal);
    $(InvoiceCharge.selector("charge_offerus")).change(function () {
        //Don't bubble
        console.log(event);
        /*
        var row = InvoiceCharge.row(this);
        var decimal = InvoiceCharge.get("decimal", row);
        InvoiceCharge.set("charge_offer", row, Invoice.fromCurrency($(this).val(), "USD", decimal);
        */
    });

    //charge_offer = (charge_offerau, decimal) => convertCurrency(charge_offerau, "AUD", invoice_currency, decimal);
    $(InvoiceCharge.selector("charge_offerau")).change(function () {
        //Don't bubble
        console.log(event);
        /*
        var row = InvoiceCharge.row(this);
        var decimal = InvoiceCharge.get("decimal", row);
        InvoiceCharge.set("charge_offer", row, Invoice.fromCurrency($(this).val(), "AUD", decimal);
        */
    });

    //charges_offer = (charge_total_offer) => sum(charge_total_offer);
    $(InvoiceCharge.selector("charge_total_offer"), context).change(function () {
        Invoice.set("charges_offer", InvoiceCharge.sum("charge_total_offer", 2));
    });

    //charges_online = (charge_total_online) => sum(charge_total_online);
    $(InvoiceCharge.selector("charge_total_online"), context).change(function () {
        Invoice.set("charges_online", InvoiceCharge.sum("charge_total_online", 2));
    });

    //charges_cost = (charge_total_cost) => sum(charge_total_cost);
    $(InvoiceCharge.selector("charge_total_cost"), context).change(function () {
        Invoice.set("charges_cost", InvoiceCharge.sum("charge_total_cost", 2));
    });

    //charge_costau  = (charge_cost) => convertCurrency(charge_cost, invoice_currency, "AUD");
    //charge_costus  = (charge_cost) => convertCurrency(charge_cost, invoice_currency, "USD");
    //charge_offerau = (charge_offer) => convertCurrency(charge_offer, invoice_currency, "AUD");
    //charge_offerus = (charge_offer) => convertCurrency(charge_offer, invoice_currency, "USD");
    $(InvoiceCharge.selector(["charge_cost", "charge_offer"]), context).change(function () {
        var row = InvoiceCharge.row(this);
        var field = InvoiceCharge.field(this);
        InvoiceCharge.set(field + "us", row, Invoice.toCurrency($(this).val(), "USD", 4));
        InvoiceCharge.set(field + "au", row, Invoice.toCurrency($(this).val(), "AUD", 4));
    });
}

$(function () {
    product_supplier_product_events($(".invoice_content"));
    product_supplier_charge_events($(".invoice_content"));

    //supplier_ship_product_name = (ship_product_name) => ship_product_name;
    $(Invoice.selector("ship_product_name")).change(function () {
        Invoice.set("supplier_ship_product_name", $(this).val());
    });

    //ship_product_name = (supplier_ship_product_name) => supplier_ship_product_name;
    $(Invoice.selector("supplier_ship_product_name")).change(function () {
        //TODO: Don't bubble
        console.log(event);
        //Invoice.set("ship_product_name", $(this).val());
    });

    //supplier_ship_cost = (ship_cost) => ship_cost;
    $(Invoice.selector("ship_cost")).change(function () {
        Invoice.set("supplier_ship_cost", $(this).val());
    });

    //ship_cost = (supplier_ship_cost) => supplier_ship_cost;
    $(Invoice.selector("supplier_ship_cost")).change(function () {
        //TODO: Don't bubble
        console.log(event);
        //Invoice.set("ship_cost", $(this).val());
    });

    //invoice_cost = (products_cost, ship_cost, charges_cost) => products_cost + ship_cost + charges_cost;
    $(Invoice.selector(["products_cost", "ship_cost", "charges_cost"])).change(function () {
        Invoice.set("invoice_cost", bcadd(bcadd(Invoice.get("products_cost"), Invoice.get("ship_cost"), 2), Invoice.get("charges_cost"), 2));
    });

    //invoice_online = (products_online, ship_online, charges_online) => products_online + ship_online + charges_online;
    $(Invoice.selector(["products_online", "ship_online", "charges_online"])).change(function () {
        Invoice.set("invoice_online", bcadd(bcadd(Invoice.get("products_online"), Invoice.get("ship_online"), 2), Invoice.get("charges_online"), 2));
    });

    //invoice_offer = (products_offer, ship_offer, charges_offer) => products_offer + ship_offer + charges_offer;
    $(Invoice.selector(["products_offer", "ship_offer", "charges_offer"])).change(function () {
        Invoice.set("invoice_offer", bcadd(bcadd(Invoice.get("products_offer"), Invoice.get("ship_offer"), 2), Invoice.get("charges_offer"), 2));
    });

    //invoice_profit = (invoice_offer, invoice_cost) => invoice_offer - invoice_cost;
    $(Invoice.selector(["invoice_offer", "invoice_cost"])).change(function () {
        Invoice.set("invoice_profit", bcsub(Invoice.get("invoice_offer"), Invoice.get("invoice_cost"), 2));
    });

    //TODO: This needs to be un-anonymous, and split into product_supplier_charge_events()
    $([Invoice.selector(["products_offer", "ship_offer"]), InvoiceCharge.selector(["charge_id", "charge_qty", "charge_offer", "charge_cost"])].join(",")).change(function () {
        //TODO: Update charges that depend on sub-total
        var products_cost  = Invoice.get("products_cost");
        var products_offer = Invoice.get("products_offer");
        var ship_cost      = Invoice.get("ship_offer");
        var ship_offer     = Invoice.get("ship_offer");
        var running_cost   = products_cost;
        var running_offer  = products_offer;
        $(Charges).each(function() {
            if (this.priority == 7) {
                running_cost = bcadd(running_cost, ship_cost, 2);
                running_offer = bcadd(running_offer, ship_offer, 2);
            }
            var charge = this;
            $(InvoiceCharge.selector("charge_id") + "[value='" + this.id + "']").each(function() {
                var row = InvoiceCharge.row(this);
                var charge_qty = InvoiceCharge.get("charge_qty", row);
                var charge_offer = InvoiceCharge.get("charge_offer", row);
                var charge_cost = InvoiceCharge.get("charge_cost", row);
                //TODO: Calc the charge based on running_*
                InvoiceCharge.set("charge_offer", charge_offer);
                InvoiceCharge.set("charge_cost", charge_cost);
                running_cost = bcadd(running_cost, charge_cost, 2);
                running_offer = bcadd(running_offer, charge_offer, 2);
            });
        });
    });

    //charges_costau   = (charges_cost)   => convertCurrency(charges_cost, invoice_currency, "AUD");
    //charges_costus   = (charges_cost)   => convertCurrency(charges_cost, invoice_currency, "USD");
    //charges_offerau  = (charges_offer)  => convertCurrency(charges_offer, invoice_currency, "AUD");
    //charges_offerus  = (charges_offer)  => convertCurrency(charges_offer, invoice_currency, "USD");
    //invoice_costau   = (invoice_cost)   => convertCurrency(invoice_cost, invoice_currency, "AUD");
    //invoice_costus   = (invoice_cost)   => convertCurrency(invoice_cost, invoice_currency, "USD");
    //invoice_offerau  = (invoice_offer)  => convertCurrency(invoice_offer, invoice_currency, "AUD");
    //invoice_offerus  = (invoice_offer)  => convertCurrency(invoice_offer, invoice_currency, "USD");
    //invoice_profitau = (invoice_profit) => convertCurrency(invoice_profit, invoice_currency, "AUD");
    //invoice_profitus = (invoice_profit) => convertCurrency(invoice_profit, invoice_currency, "USD");
    //products_costau  = (products_cost)  => convertCurrency(products_cost, invoice_currency, "AUD");
    //products_costus  = (products_cost)  => convertCurrency(products_cost, invoice_currency, "USD");
    //products_offerau = (products_offer) => convertCurrency(products_offer, invoice_currency, "AUD");
    //products_offerus = (products_offer) => convertCurrency(products_offer, invoice_currency, "USD");
    //ship_costau      = (ship_cost)      => convertCurrency(ship_cost, invoice_currency, "AUD");
    //ship_costus      = (ship_cost)      => convertCurrency(ship_cost, invoice_currency, "USD");
    //ship_offerau     = (ship_offer)     => convertCurrency(ship_offer, invoice_currency, "AUD");
    //ship_offerus     = (ship_offer)     => convertCurrency(ship_offer, invoice_currency, "USD");
    $(Invoice.selector(["charges_cost", "charges_offer", "invoice_cost", "invoice_offer", "invoice_profit", "products_cost", "products_offer", "ship_cost", "ship_offer"])).change(function () {
        var field = Invoice.field(this);
        Invoice.set(field + "us", row, currency($(this).val(), "USD"));
        Invoice.set(field + "au", row, currency($(this).val(), "AUD"));
    });

    $(Invoice.selector("invoice_currency")).change(function() {
        //TODO: This could possibly update every single field, or it could update all the au/us fields
        //Maybe request the user input to decide
    });
});
//]]>
</script>
<?php
$product_affected = array();
foreach ($invoice_products as $product) {
    $product_affected[] = $product['prod_count'];
}
?>
<input type="hidden" name="product_affected" value="<?=implode(',', $product_affected)?>" />
<?php
$charge_affected = array();
foreach ($invoice_charges as $charge) {
    $charge_affected[] = $charge['charge_count'];
}
?>
<input type="hidden" name="charge_affected" value="<?=implode(',', $charge_affected)?>" />
<table class="wide">
    <tr>
        <th style="width:32px">PID</th>
        <th>&nbsp;</th>
        <th style="width:100%">Product Name</th>
        <th>&nbsp;</th>
        <th style="width:32px">Qty.</th>
        <th style="width:64px">Cost Price (<span name="product_currency"><?php hecho($invoice['invoice_currency'])?></span>)</th>
        <th style="width:64px">Online Price (<span name="product_currency"><?php hecho($invoice['invoice_currency'])?></span>)</th>
        <th style="width:64px">Offer Price (<span name="product_currency"><?php hecho($invoice['invoice_currency'])?></span>)</th>
        <th class="show_aud" style="width:64px">Offer AUD</th>
        <th class="show_usd" style="width:64px">Offer USD</th>
        <th style="width:64px">&nbsp;</th>
    </tr>
    <tbody id="product_table" name="product_table">
<?php
$pcount = 1;
foreach ($invoice_products as $product) {
    $generic = ('n' == $product['generic']) ? false : true;
    echo product_row($pcount++, $product, $generic);
}
?>
    </tbody>
    <tr id="product_ship_rowa"<?=(0 == $invoice['ship_id']) ? ' style="display:none"' : ''?>>
        <td><input type="hidden" name="ship_id" value="<?php hecho($invoice['ship_id'])?>" /><input type="text" name="ship_product_id" size="4" value="F<?=str_pad($invoice['ship_id'], 2, '0', STR_PAD_LEFT)?><?=('y' == $invoice['ship_insurance']) ? 'I' : 'N'?>" disabled="disabled" style="text-align:right" /></td>
        <td colspan="3"><input name="ship_product_name" style="width:100%" type="text" value="<?php hecho($invoice['ship_product_name'])?>" onchange="shipChange('pname')" maxlength="64" /></td>
        <td><input name="ship_qty" size="3" style="text-align:right" type="text" value="1" onfocus="this.select()"/></td>
        <td><input name="ship_cost" size="6" style="text-align:right" type="text" value="<?=sprintf('%.2f', $invoice['ship_cost'])?>" onchange="shipChange('pcost')" onfocus="this.select()"/></td>
        <td><input name="ship_online" size="6" style="text-align:right" type="text" value="<?=sprintf('%.2f', $invoice['ship_online'])?>" onchange="shipChange('ponline')" onfocus="this.select()"/></td>
        <td><input name="ship_offer" size="6" style="text-align:right" type="text" value="<?=sprintf('%.2f', $invoice['ship_offer'])?>" onchange="shipChange('poffer')" onfocus="this.select()"/></td>
        <td><input name="ship_offerau" size="6" style="text-align:right;<?=($show_usd) ? 'display:none;' : ''?>" type="text" value="<?=sprintf('%.2f', $invoice['ship_offerau'])?>" onchange="shipChange('pofferau')" readonly="readonly" /><input name="ship_offerus" size="6" style="text-align:right;<?=($show_usd) ? '' : 'display:none;'?>" type="text" value="<?=sprintf('%.2f', $invoice['ship_offerus'])?>" readonly="readonly" /></td>
        <td><input onclick="deleteFreight()" type="button" value="Delete" /></td>
    </tr>
    <tr class="alt" id="product_ship_rowb"<?=(0 == $invoice['ship_id']) ? ' style="display:none"' : ''?>>
        <td>Notes</td>
        <td colspan="9"><textarea cols="80" name="ship_product_notes" rows="<?=max(3, count(explode("\n", trim($invoice['ship_product_notes']))))?>" onkeyup="textareaAutoSize(this, 1, 3)" style="width:100%"><?php hecho(trim($invoice['ship_product_notes']))?></textarea></td>
    </tr>
    <tbody id="charges_table" name="charges_table">
<?php
$ccount = 1;
foreach ($invoice_charges as $charge) {
    echo charge_row($ccount++, $charge);
}
?>
    </tbody>
    <tr class="alt">
        <td colspan="4">&nbsp;</td>
        <td style="font-weight:bold">TOTAL</td>
        <td><input type="text" name="invoice_cost" readonly="readonly" size="6" value="<?=sprintf('%.2f', $invoice['invoice_cost'])?>" style="text-align:right" /></td>
        <td><input type="text" name="invoice_online" readonly="readonly" size="6" value="<?=sprintf('%.2f', $invoice['invoice_online'])?>" style="text-align:right" /></td>
        <td><input type="text" name="invoice_offer" readonly="readonly" size="6" value="<?=sprintf('%.2f', $invoice['invoice_offer'])?>" style="text-align:right" /></td>
        <td><input type="text" name="invoice_offerau" readonly="readonly" size="6" value="<?=sprintf('%.2f', $invoice['invoice_offerau'])?>" style="text-align:right;<?=($show_usd) ? 'display:none;' : ''?>" /><input type="text" name="invoice_offerus" readonly="readonly" size="6" value="<?=sprintf('%.2f', $invoice['invoice_offerus'])?>" style="text-align:right;<?=($show_usd) ? '' : 'display:none;'?>" /></td>
        <td style="font-weight:bold" name="product_currency"><?=$invoice['invoice_currency']?></td>
    </tr>
    <tr class="alt">
        <td colspan="10" style="text-align:center">
            <input onclick="addProduct()" type="button" value="Add Product" />
            <input onclick="openWindow('addGeneric.php', 550, 250, true)" type="button" value="Add Generic" />
            <input onclick="openWindow('addCharge.php?return_message=add', 640, 480, true, 'scrollbars=1')" type="button" value="Add Charge" />
            <input type="hidden" name="products_cost" value="<?=sprintf('%.2f', $invoice['products_cost'])?>" />
            <input type="hidden" name="products_costau" value="<?=sprintf('%.2f', $invoice['products_costau'])?>" />
            <input type="hidden" name="products_costus" value="<?=sprintf('%.2f', $invoice['products_costus'])?>" />
            <input type="hidden" name="products_offer" value="<?=sprintf('%.2f', $invoice['products_offer'])?>" />
            <input type="hidden" name="products_offerau" value="<?=sprintf('%.2f', $invoice['products_offerau'])?>" />
            <input type="hidden" name="products_offerus" value="<?=sprintf('%.2f', $invoice['products_offerus'])?>" />
            <input type="hidden" name="charges_cost" value="<?=sprintf('%.2f', $invoice['charges_cost'])?>" />
            <input type="hidden" name="charges_costau" value="<?=sprintf('%.2f', $invoice['charges_costau'])?>" />
            <input type="hidden" name="charges_costus" value="<?=sprintf('%.2f', $invoice['charges_costus'])?>" />
            <input type="hidden" name="charges_offer" value="<?=sprintf('%.2f', $invoice['charges_offer'])?>" />
            <input type="hidden" name="charges_offerau" value="<?=sprintf('%.2f', $invoice['charges_offerau'])?>" />
            <input type="hidden" name="charges_offerus" value="<?=sprintf('%.2f', $invoice['charges_offerus'])?>" />
            <input type="hidden" name="invoice_profit" value="<?=sprintf('%.2f', $invoice['invoice_profit'])?>" />
            <input type="hidden" name="invoice_profitau" value="<?=sprintf('%.2f', $invoice['invoice_profitau'])?>" />
            <input type="hidden" name="invoice_profitus" value="<?=sprintf('%.2f', $invoice['invoice_profitus'])?>" />
        </td>
    </tr>
</table>
<br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none" id="sectionProducts_hideNote">
    All Products requested can be viewed by Clicking "Products Requested" above<br />
</div>
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionSuppliers\')">Product Suppliers</a>', '<a href="javascript:toggleSection(\'sectionDefSup\')">Default Supplier Values</a>')?>
<div id="sectionDefSup" style="display:none">
<table class="wide">
    <tr>
        <td class="left_title" style="width:25%">Supplier Search :</td>
        <td colspan="2" style="width:50%"><input type="hidden" name="default_supplier" value="<?=$invoice['default_supplier']?>" /><input style="width:100%" type="text" name="def_sup_name" value="<?php hecho(getSupplierName($invoice['default_supplier']))?>" /></td>
        <td style="width:25%"><input type="button" value=" Search " onclick="searchDefaultSupplier(getValue('def_sup_name'))"/></td>
    </tr>
    <tr>
        <td class="left_title" style="width:25%">Warranty :</td>
        <td style="width:25%">
            <select name="def_warranty"><option value="0">Not Selected</option><?=formatHTMLOptions($warranty_terms, $invoice['def_warranty'])?></select>
        </td>
        <td class="left_title" style="width:25%">Voltage :</td>
        <td style="width:25%">
            <select name="def_voltage"><option value="0">Not Selected</option><?=formatHTMLOptions($voltage_terms, $invoice['def_voltage'])?></select>
        </td>
    </tr>
    <tr>
        <td class="left_title">Plug Type :</td>
        <td style="width:25%"><select name="def_plug"><option value="0">Not Selected</option><?=formatHTMLOptions($plugs_list, $invoice['def_plug'])?></select></td>
        <td class="left_title">Condition :</td>
        <td><select name="def_condition"><option value="0">Not Selected</option><?=formatHTMLOptions($condition_terms, $invoice['def_condition'])?></select></td>
    </tr>
    <tr>
        <td class="left_title">Packing :</td>
        <td><select name="def_packing"><option value="0">Not Selected</option><?=formatHTMLOptions($packing_terms, $invoice['def_packing'])?></select></td>
        <td class="left_title">Terms :</td>
        <td><select name="def_terms"><option value="0">Not Selected</option><?=formatHTMLOptions($shipping_terms, $invoice['def_terms'])?></select></td>
    </tr>
    <tr>
        <td class="left_title">Currency :</td>
        <td><select name="def_currency"><option value="">Not Selected</option><?=formatHTMLOptions(getCurrencyList(), $invoice['def_currency'])?></select></td>
        <td class="left_title">Country :</td>
        <td><select name="def_country"><option value="">Not Selected</option><?=formatHTMLOptions(getCountryList(), $invoice['def_country'])?></select></td>
    </tr>
    <tr><td colspan="4" class="left_title" style="text-align:center"><input type="button" value=" Update Products with Default Values " onclick="updateAllSuppliers(getValue('def_sup_name'), this)" /></td></tr>
</table>
<br />
</div>
<div id="sectionDefSup_hideNote"></div>
<div id="sectionSuppliers">
<table class="wide">
    <tbody id="supplier_table">
<?php
$pcount = 1;
$alt    = true;
foreach ($invoice_products as $product) {
    echo supplier_row($pcount++, $product);
}
?>
    </tbody>
    <tr>
        <th style="width:32px">PID</th>
        <th style="width:50%">Product Name</th>
        <th style="width:50%">Supplier</th>
        <th>&nbsp;</th>
        <th style="width:256px">Found</th>
        <!--<th style="width:128px">Used</th>-->
        <th style="width:32px">Qty</th>
        <th style="width:64px">Cost</th>
        <th class="show_aud" style="width:64px">Cost AUD</th>
        <th class="show_usd" style="width:64px">Cost USD</th>
    </tr>
    <tr id="supplier_ship_rowa"<?=(0 == $invoice['ship_id']) ? ' style="display:none"' : ''?>>
        <td><input type="text" name="supplier_ship_product_id" size="4" value="F<?=str_pad($invoice['ship_id'], 2, '0', STR_PAD_LEFT)?><?=('y' == $invoice['ship_insurance']) ? 'I' : 'N'?>" disabled="disabled" style="text-align:right" onchange="shipChange('sid')" /></td>
        <td><input type="text" name="supplier_ship_product_name" style="width:100%" value="<?php hecho($invoice['ship_product_name'])?>" onchange="shipChange('sname')" /></td>
        <td colspan="2"><input type="hidden" name="ship_supplier_id" value="<?=intval($invoice['ship_supplier_id'])?>" /><input type="text" name="ship_supplier_name" value="<?php hecho($invoice['ship_supplier_name'])?>" size="12" style="width:100%" onchange="shipChange('ssupplier')" /></td>
        <td></td>
        <td><input type="text" name="supplier_ship_qty" size="3" style="text-align:right" value="1" onchange="shipChange('sqty')" onfocus="this.select()"/></td>
        <td><input type="text" size="6" style="text-align:right" name="supplier_ship_cost" value="<?=sprintf('%.2f', $invoice['ship_cost'])?>" onchange="shipChange('scost')" onfocus="this.select()"/></td>
        <td><input type="text" size="6" style="text-align:right;<?=($show_usd) ? 'display:none;' : ''?>" readonly="readonly" name="ship_costau" value="<?=sprintf('%.2f', $invoice['ship_costau'])?>" onchange="shipChange('scostau')" /><input type="text" size="6" style="text-align:right;<?=($show_usd) ? '' : 'display:none;'?>" readonly="readonly" name="ship_costus" value="<?=sprintf('%.2f', $invoice['ship_costus'])?>" onchange="shipChange('scostus')" /><input type="hidden" name="ship_insurance" value="<?=$invoice['ship_insurance']?>" /></td>
    </tr>
    <tr class="alt">
        <td colspan="7" style="font-weight:bold;text-align:right">TOTAL
            <input type="text" name="invoice_cost" readonly="readonly" size="10" value="<?=sprintf('%.2f', $invoice['invoice_cost'])?>" style="text-align:right" />
            <input type="hidden" name="invoice_costau" value="<?=sprintf('%.2f', $invoice['invoice_costau'])?>" style="text-align:right" />
            <input type="hidden" name="invoice_costus" value="<?=sprintf('%.2f', $invoice['invoice_costus'])?>" style="text-align:right" />
        </td>
        <td style="font-weight:bold" name="product_currency"><?=$invoice['invoice_currency']?></td>
    </tr>
    <tr class="alt"><td colspan="8"><table style="width:100%;height:100%;border-style:none">
        <tr><td class="left_title"></td><td>&nbsp;</td></tr>
        <tr>
            <td class="left_title" style="width:25%;vertical-align:top">Notes</td>
            <td>
                Products highlighted in <span style="color:#FFAA99">red</span> have no suppliers in the database<br />
                Products highlighted in <span style="color:#6688BB">blue</span> are available from two or more suppliers<br />
                Products highlighted in <span style="color:#8B00FF">violet</span> have unknown weights<br />
            </td>
        </tr>
        <tr><td class="left_title"></td><td>&nbsp;</td></tr>
<?php
if ('y' == $invoice['backorder_flag']) {
?>
        <tr><td class="left_title" colspan="2" style="text-align:center;color:red;padding:6px">Invoice Created as a Backorder, use box below to indicate when new stock arrives.<br />(Once new stock is received, the customer can use the re-checkout feature online)</td></tr>
        <tr>
            <td class="left_title">New Stock Received :</td>
            <td><select name="stock_received" style="width:100%"><?=formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $invoice['stock_received'])?></select></td>
        </tr>
<?php
}
?>
        <tr>
            <td class="left_title">Ships From</td>
            <td><select name="supply_country">
                <?=formatHTMLOptions(array('' => '') + invoiceGetOptionList('supply_country'), $invoice['supply_country'])?>
            </select></td>
        </tr>
        <tr>
            <td class="left_title">Products Located</td>
<?php
$colour = 'y' == $invoice['products_located'] ? '#00FF00' : '#FF0000';
?>
            <td><select name="products_located" style="background-color:<?=$colour?>" onchange="updateSelect(this)"><?=formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $invoice['products_located'])?></select></td>
        </tr>
<?php
if ('y' == $invoice['products_located']) {
    $staff = 'Customer';
    if ($invoice['products_staff_id'] > 0) {
        $staff = $all_staff_list[$invoice['products_staff_id']];
    }
?>
        <tr style="color: red;font-weight:bold">
            <td class="left_title">Authorised</td>
            <td><i><?=$staff?> - <?=date('d/m/y h:i', $invoice['products_locate_time'])?></i></td>
        </tr>
<?php
}
?>
        </table></td></tr>
    </table>
    <br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none" id="sectionSuppliers_hideNote">
    All Product Suppliers can be viewed by Clicking "Products Suppliers" above<br />
</div>

<!-- /product_supplier.tpl -->

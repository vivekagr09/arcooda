<!-- product_supplier_new.tpl -->

<?php
$owarranty = $ocondition = $opacking = $ovoltage = $oshipping = $oplug = 0;
$defSup    = $invoice['default_supplier'];
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
$plugs_list      = array('-1' => 'Not Applicable') + getInvoicePlugList();

function invoice_products_row($count = '[ROW_COUNT]', $row_data = array())
{
    $post_key = 'invoice_products[' . $count . ']';
    //$keys=array('decimal', 'generic', 'generic_code', 'product_condition', 'product_cost', 'product_costau', 'product_id', 'product_name', 'product_notes', 'product_offer', 'product_offerau', 'product_online', 'product_packing', 'product_plug', 'product_price_changed', 'product_qty', 'product_rank', 'product_terms', 'product_total_weight', 'product_updated', 'product_voltage', 'product_warranty', 'product_weight', 'product_weight_inc', 'sub_id', 'supplier_cost', 'supplier_country', 'supplier_currency', 'supplier_found', 'supplier_id', 'supplier_name', 'supplier_notes');
    foreach (array('decimal', 'generic', 'product_rank', 'product_weight', 'product_weight_inc') as $key) {
        $hidden[$key] = $row_data[$key];
    }

    ob_start();
?>
<tr class="alt" id="invoice_products_rowa[<?= $count ?>]">
<?php
switch ($row_data['generic']) {
        case 'c':
            //foreach(array() as $key) $hidden[$key] = $row_data[$key];
?>
	<td><?= str_replace("\n", "", formatHTMLInputs($post_key, $hidden)) ?><a href="#" onclick="invoiceProductsChangeRank(<?= $count ?>,-1)"><img src="/webadmin/images/up_arrow.gif" alt="Up" border="0" /></a><a href="#" onclick="invoiceProductsChangeRank(<?= $count ?>,1)"><img src="/webadmin/images/down_arrow.gif" alt="Down" border="0" /></a></td>
	<td colspan="7"><input type="text" name="<?= $post_key ?>[product_name]" style="width:100%;" value="<?php hecho($row_data['product_name'])?>" readonly="readonly" /></td>
	<td><input type="button" value="Delete" onclick="invoiceProductsDelete(<?= $count ?>)" /></td>
<?php
break;
        case 'n':
?>
	<td><?= str_replace("\n", "", formatHTMLInputs($post_key, $hidden)) ?><input type="text" name="<?= $post_key ?>[product_id]" size="4" style="text-align:right;" value="<?php hecho($row_data['product_id'])?>" onchange="invoiceProductsChange(<?= $count ?>,'product_id')" />-<input type="text" name="<?= $post_key ?>[sub_id]" size="2" style="text-align:right;" value="<?php hecho($row_data['sub_id'])?>" onchange="invoiceProductsChange(<?= $count ?>,'sub_id')" /><input type="hidden" name="<?= $post_key ?>[generic_code]" value="" /></td>
	<td><input type="text" name="<?= $post_key ?>[product_name]" style="width:100%;" value="<?php hecho($row_data['product_name'])?>" onchange="invoiceProductsChange(<?= $count ?>,'product_name')" /></td>
	<td><input type="button" value="S" onclick="invoiceProductsSearch(<?= $count ?>)" /></td>
	<td><input type="text" name="<?= $post_key ?>[product_qty]" size="3" style="text-align:right;" value="<?php hecho($row_data['product_qty'])?>" onchange="invoiceProductsChange(<?= $count ?>,'product_qty')" onfocus="this.select()" /></td>
	<td><input type="text" name="<?= $post_key ?>[product_cost]" size="6" style="text-align:right;" value="<?php hecho($row_data['product_cost'])?>" onchange="invoiceProductsChange(<?= $count ?>,'product_cost')" onfocus="this.select()" /></td>
	<td><input type="text" name="<?= $post_key ?>[product_online]" size="6" style="text-align:right;" value="<?php hecho($row_data['product_online'])?>" onchange="invoiceProductsChange(<?= $count ?>,'product_online')" onfocus="this.select()" /></td>
	<td><input type="text" name="<?= $post_key ?>[product_offer]" size="6" style="text-align:right;" value="<?php hecho($row_data['product_offer'])?>" onchange="invoiceProductsChange(<?= $count ?>,'product_offer')" onfocus="this.select()" /></td>
	<td><input type="text" name="<?= $post_key ?>[product_offerau]" size="6" style="text-align:right;" value="<?php hecho($row_data['product_offerau'])?>" onchange="invoiceProductsChange(<?= $count ?>,'product_offerau')" onfocus="this.select()" readonly="readonly" /></td>
	<td><input type="button" value="Delete" onclick="invoiceProductsDelete(<?= $count ?>)" /></td>
</tr>
<tr id="invoice_products_rowb[<?= $count ?>]">
	<td><a href="#" onclick="invoiceProductsChangeRank(<?= $count ?>,-1)"><img src="/webadmin/images/up_arrow.gif" alt="Up" border="0" /></a><a href="#" onclick="invoiceProductsChangeRank(<?= $count ?>,1)"><img src="/webadmin/images/down_arrow.gif" alt="Down" border="0" /></a><br /><br />Notes</td>
	<td colspan="8"><textarea name="<?= $post_key ?>[product_notes]" rows="<?= max(3, count(explode("\n", trim($row_data['product_notes'])))) ?>" cols="80" onkeyup="textareaAutoSize(this,1,3)" onchange="invoiceProductsChange(<?= $count ?>,'product_notes')" style="width:100%;"><?php hecho(trim($row_data['product_notes']))?></textarea></td>
<?php
break;
        case 'y':
?>
	<td><?= str_replace("\n", "", formatHTMLInputs($post_key, $hidden)) ?><input type="hidden" name="<?= $post_key ?>[product_id]" value="" /><input type="hidden" name="<?= $post_key ?>[sub_id]" value="" /><input type="text" name="<?= $post_key ?>[generic_code]" size="9" style="text-align:right;" value="<?php hecho($row_data['generic_code'])?>" onchange="invoiceProductsChange(<?= $count ?>,'generic_code')" /></td>
	<td colspan="2"><input type="text" name="<?= $post_key ?>[product_name]" style="width:100%;" value="<?php hecho($row_data['product_name'])?>" onchange="invoiceProductsChange(<?= $count ?>,'product_name')" /></td>
	<td><input type="text" name="<?= $post_key ?>[product_qty]" size="3" style="text-align:right;" value="<?php hecho($row_data['product_qty'])?>" onchange="invoiceProductsChange(<?= $count ?>,'product_qty')" onfocus="this.select()" /></td>
	<td><input type="text" name="<?= $post_key ?>[product_cost]" size="6" style="text-align:right;" value="<?php hecho($row_data['product_cost'])?>" onchange="invoiceProductsChange(<?= $count ?>,'product_cost')" onfocus="this.select()" /></td>
	<td><input type="text" name="<?= $post_key ?>[product_online]" size="6" style="text-align:right;" value="<?php hecho($row_data['product_online'])?>" onchange="invoiceProductsChange(<?= $count ?>,'product_online')" onfocus="this.select()" /></td>
	<td><input type="text" name="<?= $post_key ?>[product_offer]" size="6" style="text-align:right;" value="<?php hecho($row_data['product_offer'])?>" onchange="invoiceProductsChange(<?= $count ?>,'product_offer')" onfocus="this.select()" /></td>
	<td><input type="text" name="<?= $post_key ?>[product_offerau]" size="6" style="text-align:right;" value="<?php hecho($row_data['product_offerau'])?>" onchange="invoiceProductsChange(<?= $count ?>,'product_offerau')" onfocus="this.select()" readonly="readonly" /></td>
	<td><input type="button" value="Delete" onclick="invoiceProductsDelete(<?= $count ?>)" /></td>
</tr>
<tr id="invoice_products_rowb[<?= $count ?>]">
	<td><a href="#" onclick="invoiceProductsChangeRank(<?= $count ?>,-1)"><img src="/webadmin/images/up_arrow.gif" alt="Up" border="0" /></a><a href="#" onclick="invoiceProductsChangeRank(<?= $count ?>,1)"><img src="/webadmin/images/down_arrow.gif" alt="Down" border="0" /></a><br /><br />Notes</td>
	<td colspan="8"><textarea name="<?= $post_key ?>[product_notes]" rows="<?= max(3, count(explode("\n", trim($row_data['product_notes'])))) ?>" cols="80" onkeyup="textareaAutoSize(this,1,3)" onchange="invoiceProductsChange(<?= $count ?>,'product_notes')" style="width:100%;"><?php hecho(trim($row_data['product_notes']))?></textarea></td>
</tr>
<?php
break;
        case 'b':
?>
	<td><?= str_replace("\n", "", formatHTMLInputs($post_key, $hidden)) ?><input type="text" name="<?= $post_key ?>[product_id]" size="4" style="text-align:right;" value="<?php hecho($row_data['product_id'])?>" onchange="invoiceProductsChange(<?= $count ?>,'product_id')" /><input type="hidden" name="<?= $post_key ?>[sub_id]" value="" /><input type="hidden" name="<?= $post_key ?>[generic_code]" value="" /></td>
	<td><input type="text" name="<?= $post_key ?>[product_name]" style="width:100%;" value="<?php hecho($row_data['product_name'])?>" onchange="invoiceProductsChange(<?= $count ?>,'product_name')" /></td>
	<td><input type="button" value="S" onclick="invoiceProductsSearch(<?= $count ?>)" /></td>
	<td><input type="text" name="<?= $post_key ?>[product_qty]" size="3" style="text-align:right;" value="<?php hecho($row_data['product_qty'])?>" onchange="invoiceProductsChange(<?= $count ?>,'product_qty')" onfocus="this.select()" /></td>
	<td><input type="text" name="<?= $post_key ?>[product_cost]" size="6" style="text-align:right;" value="<?php hecho($row_data['product_cost'])?>" onchange="invoiceProductsChange(<?= $count ?>,'product_cost')" onfocus="this.select()" /></td>
	<td><input type="text" name="<?= $post_key ?>[product_online]" size="6" style="text-align:right;" value="<?php hecho($row_data['product_online'])?>" onchange="invoiceProductsChange(<?= $count ?>,'product_online')" onfocus="this.select()" /></td>
	<td><input type="text" name="<?= $post_key ?>[product_offer]" size="6" style="text-align:right;" value="<?php hecho($row_data['product_offer'])?>" onchange="invoiceProductsChange(<?= $count ?>,'product_offer')" onfocus="this.select()" /></td>
	<td><input type="text" name="<?= $post_key ?>[product_offerau]" size="6" style="text-align:right;" value="<?php hecho($row_data['product_offerau'])?>" onchange="invoiceProductsChange(<?= $count ?>,'product_offerau')" onfocus="this.select()" readonly="readonly" /></td>
	<td><input type="button" value="Delete" onclick="invoiceProductsDelete(<?= $count ?>)" /></td>
</tr>
<tr id="invoice_products_rowb[<?= $count ?>]">
	<td><a href="#" onclick="invoiceProductsChangeRank(<?= $count ?>,-1)"><img src="/webadmin/images/up_arrow.gif" alt="Up" border="0" /></a><a href="#" onclick="invoiceProductsChangeRank(<?= $count ?>,1)"><img src="/webadmin/images/down_arrow.gif" alt="Down" border="0" /></a><br /><br />Notes</td>
	<td colspan="8"><textarea name="<?= $post_key ?>[product_notes]" rows="<?= max(3, count(explode("\n", trim($row_data['product_notes'])))) ?>" onkeyup="textareaAutoSize(this,1,3)" onchange="invoiceProductsChange(<?= $count ?>,'product_notes')" style="width:100%;"><?php hecho(trim($row_data['product_notes']))?></textarea></td>
</tr>
<?php
break;
    }
?>
</tr>
<?php

    $output = array();
    $string = '';
    foreach (explode("\n", ob_get_clean()) as $value) {
        $value = trim($value);
        if (in_array(substr($value, 0, 3), array('<td', '<tr')) || '</tr>' == $value) {
            $output[] = $string;
            $string   = '';
        }
        $string .= $value;
    }
    return implode("\n", $output) . $string;
}
function invoice_shipping_row($count = '[ROW_COUNT]', $row_data = array())
{
    ob_start();
?>
<tr class="alt" id="product_ship_rowa"<?= (0 == $row_data['ship_id']) ? ' style="display:none;"' : '' ?>>
	<td><input type="hidden" name="ship_id" value="<?php hecho($row_data['ship_id'])?>" /><input type="text" name="ship_product_id" size="4" value="F<?= str_pad($row_data['ship_id'], 2, '0', STR_PAD_LEFT) ?><?= ('y' == $row_data['ship_insurance']) ? 'I' : 'N' ?>" disabled="disabled" style="text-align:right;" /></td>
	<td colspan="2"><input name="ship_product_name" style="width:100%;" type="text" value="<?php hecho($row_data['ship_product_name'])?>" onchange="shipChange('pname');" /></td>
	<td><input name="ship_qty" size="3" style="text-align:right;" type="text" value="1" onchange="shipChange('pqty');" onfocus="this.select();"/></td>
	<td><input name="ship_cost" size="6" style="text-align:right;" type="text" value="<?= sprintf('%.2f', $row_data['ship_cost']) ?>" onchange="shipChange('pcost');" onfocus="this.select();"/></td>
	<td><input name="ship_online" size="6" style="text-align:right;" type="text" value="<?= sprintf('%.2f', $row_data['ship_online']) ?>" onchange="shipChange('ponline');" onfocus="this.select();"/></td>
	<td><input name="ship_offer" size="6" style="text-align:right;" type="text" value="<?= sprintf('%.2f', $row_data['ship_offer']) ?>" onchange="shipChange('poffer');" onfocus="this.select();"/></td>
	<td><input name="ship_offerau" size="6" style="text-align:right;" type="text" value="<?= sprintf('%.2f', $row_data['ship_offerau']) ?>" onchange="shipChange('pofferau');" readonly="readonly" /></td>
	<td><input onclick="deleteFreight();" type="button" value="Delete" /></td>
</tr>
<tr id="product_ship_rowb"<?= (0 == $row_data['ship_id']) ? ' style="display:none;"' : '' ?>>
	<td>Notes</td>
	<td colspan="8"><textarea name="ship_product_notes" rows="<?= max(3, count(explode("\n", trim($row_data['ship_product_notes'])))) ?>" onkeyup="textareaAutoSize(this,1,3);" style="width:100%;"><?php hecho(trim($row_data['ship_product_notes']))?></textarea></td>
</tr>
<?php
return ob_get_clean();
}
function invoice_charges_row($count = '[ROW_COUNT]', $row_data = array())
{
    $post_key = 'invoice_charges[' . $count . ']';
    foreach (array('charge_rank') as $key) {
        $hidden[$key] = $row_data[$key];
    }

    ob_start();
?>
<tr id="invoice_charges_rowa[<?= $count ?>]">
	<td><?= str_replace("\n", "", formatHTMLInputs($hidden)) ?><input type="text" name="<?= $post_key ?>[charge_number]" size="4" style="text-align:right;" value="<?= invoiceChargesFormatNumber($row_data['charge_id']) ?>" readonly="readonly" /></td>
	<td colspan="2"><input type="text" name="<?= $post_key ?>[charge_name]" value="<?php hecho($row_data['charge_name'])?>" style="width:100%;" /></td>
	<td><input name="charge_qty[<?= $count ?>]" onchange="chargeChange(<?= $count ?>,'pqty');" size="3" style="text-align:right;" type="text" value="1" onfocus="this.select();"/></td>
	<td><input name="charge_cost[<?= $count ?>]" onchange="chargeChange(<?= $count ?>,'pcost');" size="6" style="text-align:right;" type="text" value="<?= $row_data['charge_cost'] ?>" onfocus="this.select();"/></td>
	<td><input name="charge_online[<?= $count ?>]" onchange="chargeChange(<?= $count ?>,'ponline');" size="6" style="text-align:right;" type="text" value="<?= $row_data['charge_online'] ?>" onfocus="this.select();"/></td>
	<td><input name="charge_offer[<?= $count ?>]" onchange="chargeChange(<?= $count ?>,'poffer');" size="6" style="text-align:right;" type="text" value="<?= $row_data['charge_offer'] ?>" onfocus="this.select();"/></td>
	<td><input name="charge_offerau[<?= $count ?>]" onchange="chargeChange(<?= $count ?>,'pofferau');" readonly="readonly" size="6" style="text-align:right;" type="text" value="<?= $row_data['charge_offerau'] ?>" /></td>
	<td><input type="hidden" name="charge_count[<?= $count ?>]" value="<?= $row_data['charge_count'] ?>" /><input onclick="deleteCharge(<?= $count ?>);" type="button" value="Delete" /></td>
</tr>
<tr class="alt" id="invoice_charges_rowb[<?= $count ?>]">
	<td><a href="javascript:void(rankCharge(<?= $count ?>,-1))"><img src="/webadmin/images/up_arrow.gif" alt="Up" border="0" /></a><a href="javascript:void(rankCharge(<?= $count ?>,1))"><img src="/webadmin/images/down_arrow.gif" alt="Down" border="0" /></a><br /><br />Notes</td>
	<td colspan="8"><textarea name="charge_notes[<?= $count ?>]" rows="<?= max(3, count(explode("\n", trim($row_data['charge_notes'])))) ?>" onkeyup="textareaAutoSize(this,1,3);" onchange="setValue('charge_updated[<?= $count ?>]','y')" style="width:100%;"><?php hecho(trim($row_data['charge_notes']))?></textarea></td>
</tr>
<?php
return ob_get_clean();
}
function supplier_row($count = '[PRODUCT_COUNT]', $product = array())
{
    global $invoice, $warranty_terms, $condition_terms, $packing_terms, $voltage_terms, $shipping_terms, $plugs_list, $products_need_update, $site_id;
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
    $prodDB   = (9 == $invoice['site_id'] && $product['product_id'] > 0) ? productFromDatabase($product['product_id']) : array();
    $sup_id   = (0 != $product['supplier_id']) ? $product['supplier_id'] : $prodDB['supplier'];
    if ('' == $sup_name) {
        if (9 == $invoice['site_id'] || 16 == $invoice['site_id']) {
            $sup_name = getSupplierName($sup_id);
        }
    }

    $sucountry = ('' != $product['supplier_country']) ? $product['supplier_country'] : 'AU';
    $yesno     = array('n' => 'No', 'y' => 'Yes');
    $stock     = $yesno + array('s' => 'Stock');
    $disNotes  = $product['supplier_notes'];
    if ($product['product_id'] > 0 && 'y' != $product['generic'] && 9 == $invoice['site_id']) {
        if (9 == $invoice['site_id']) {
            $notes        = '';
            $inStock      = $prodDB['qty'] - $prodDB['reserved'] - $product['product_qty'];
            $availability = getAvailabilityName($prodDB['availability']);
            if (count($product) > 0) {
                if (('y' == $product['supplier_found'] || 's' == $product['supplier_found']) && $inStock > 0) {
                    $notes = 'Stock: Ready To Send (' . $inStock . ' remaining)';
                } else {
                    if (1 == $prodDB['availability']) {
                        $availability = '2-4 Weeks';
                    }

                    $notes = 'Out of Stock: ' . $availability;
                }
            }
            $disNotes = ('' != $notes) ? $notes : trim('' != $product['supplier_notes']);
            if ($inStock <= 0) {
                $product['supplier_found'] = 'n';
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
    $hidden        = array('supplier_id[' . $count . ']' => $sup_id, 'supplier_product_cost[' . $count . ']' => $product['product_cost']);
    ob_start();
?>
<tr id="supplier_rowa[<?= $count ?>]">
<th><?= str_replace("\n", "", formatHTMLInputs($hidden)) ?>PID</th>
<th>Product Name</th>
<th>Supplier</th>
<th></th>
<th>Found</th>
<th>Qty</th>
<th>Cost <span name="span_supplier_currency[<?= $count ?>]"><?php hecho($product['supplier_currency'])?></span></th>
<th>Cost AUD</th>
</tr>
<tr id="supplier_rowb[<?= $count ?>]"<?= ('' != $bgcolor) ? (' style="background-color:' . $bgcolor . ';"') : '' ?>>
<td><input disabled="disabled" name="supplier_product_id[<?= $count ?>]" size="4" style="text-align:right;" type="text" value="<?= $product['product_id'] ?>" onchange="productChange(<?= $count ?>,'sid');" /><input disabled="disabled" name="supplier_sub_id[<?= $count ?>]" size="4" style="text-align:right;" type="text" value="<?= $product['sub_id'] ?>" onchange="productChange(<?= $count ?>,'sid');" /></td>
<td><input name="supplier_product_name[<?= $count ?>]" disabled="disabled" size="12" style="width:100%;overflow:hidden;" type="text" value="<?php hecho($product['product_name'])?>" /></td>
<td><input name="supplier_name[<?= $count ?>]" size="12" style="width:100%;" type="text" value="<?php hecho($sup_name)?>" /></td>
<td><input onclick="searchSupplier(<?= $count ?>,getValue('supplier_name[<?= $count ?>]'));" type="button" value="S" /></td>
<td><select name="supplier_found[<?= $count ?>]" onchange="productChange(<?= $count ?>,'sfound');"><?= str_replace("\n", "", formatHTMLOptions($stock, $product['supplier_found'])) ?></select></td>
<td><input name="supplier_product_qty[<?= $count ?>]" disabled="disabled" size="3" style="text-align:right;" type="text" value="<?= $product['product_qty'] ?>" onfocus="this.select();"/></td>
<td><input name="supplier_cost[<?= $count ?>]" size="6" style="text-align:right;" type="text" value="<?= $product['supplier_cost'] ?>" onfocus="this.select();" onchange="productChange(<?= $count ?>,'ocost');" /></td>
<td><input name="product_costau[<?= $count ?>]" readonly="readonly" size="6" style="text-align:right;" type="text" value="<?= $product['product_costau'] ?>" /></td>
</tr>
<tr class="alt" id="supplier_rowc[<?= $count ?>]">
<td colspan="2" class="left_title">Warranty :</td>
<td colspan="2"><select name="product_warranty[<?= $count ?>]" onchange="updateProductNotes('<?= $count ?>');"><option value="0">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions($warranty_terms, $product['product_warranty'])) ?></select></td>
<td colspan="1" class="left_title">Voltage :</td>
<td colspan="3"><select name="product_voltage[<?= $count ?>]" onchange="updateProductNotes('<?= $count ?>');"><option value="0">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions($voltage_terms, $product['product_voltage'])) ?></select></td>
</tr>
<tr class="alt" id="supplier_rowd[<?= $count ?>]">
<td colspan="2" class="left_title">Plug Type :</td>
<td colspan="2"><select name="product_plug[<?= $count ?>]" onchange="updateProductNotes('<?= $count ?>');"><option value="0">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions($plugs_list, $product['product_plug'])) ?></select></td>
<td colspan="1" class="left_title" style="white-space:nowrap;">Condition :</td>
<td colspan="3"><select name="product_condition[<?= $count ?>]" onchange="updateProductNotes('<?= $count ?>');"><option value="0">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions($condition_terms, $product['product_condition'])) ?></select></td>
</tr>
<tr class="alt" id="supplier_rowe[<?= $count ?>]">
<td colspan="2" class="left_title">Packing :</td>
<td colspan="2"><select name="product_packing[<?= $count ?>]"><option value="0">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions($packing_terms, $product['product_packing'])) ?></select></td>
<td colspan="1" class="left_title">Terms :</td>
<td colspan="3"><select name="product_terms[<?= $count ?>]"><option value="0">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions($shipping_terms, $product['product_terms'])) ?></select></td>
</tr>
<tr class="alt" id="supplier_rowf[<?= $count ?>]">
<td colspan="2" class="left_title">Currency :</td>
<td colspan="2"><select name="supplier_currency[<?= $count ?>]" onchange="productChange(<?= $count ?>,'ocost');"><option value="">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions(getCurrencyList(), $product['supplier_currency'])) ?></select></td>
<td colspan="1" class="left_title">Country :</td>
<td colspan="3"><select name="supplier_country[<?= $count ?>]"><option value="">Not Selected</option><?= str_replace("\n", "", formatHTMLOptions(getCountryList(), $sucountry)) ?></select></td>
</tr>
<tr class="alt" id="supplier_rowg[<?= $count ?>]" style="<?= ('y' == $product['generic']) ? 'display:none' : '' ?>">
<td colspan="8" style="text-align:center">
<?php
if ($update_button) {
?>
<input onclick="updateCostPrice(<?= $product['product_id'] ?>,<?= $invoice['site_id'] ?>,<?= $count ?>);" type="button" value=" Update Product Cost Price " style="background-color:#CDEFEA" />
<?php
}
?>
</td>
</tr>
<tr class="alt" id="supplier_rowh[<?= $count ?>]" style="<?= ('y' != $product['generic'] && count($product) && 2 == $site_id && 2 == $invoice['site_id'] && 0 == $product['sub_id']) ? '' : 'display:none;' ?>">
<td colspan="8" style="text-align:center;">
<?php
if ('y' != $product['generic'] && count($product) && 2 == $site_id && 2 == $invoice['site_id'] && 0 == $product['sub_id']) {
?>
<input type="button" value="Make Individual Product" onclick="makeProductIndividual('<?= $count ?>')" />
<?php
}
?>
</td>
</tr>
<tr class="alt" id="supplier_rowi[<?= $count ?>]">
<td>Notes<br /><br /><input type="button" onclick="splitProduct(<?= $count ?>);" value="Split" /></td>
<td colspan="7"><textarea style="width:100%;" name="supplier_notes[<?= $count ?>]" rows="<?= max(3, count(explode("\n", trim($disNotes)))) ?>" onkeyup="textareaAutoSize(this,1,3);"><?php hecho(trim($disNotes))?></textarea></td>
</tr>
<?php
return ob_get_clean();
}
?>
<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionProducts\');">Products Requested</a>') ?>
<div id="sectionProducts">
<script type="text/javascript">
//<![CDATA[
var product_count=<?= max(100, count($invoice_products)) ?>;
var charge_count=<?= max(100, count($invoice_charges)) ?>;
var charge_names = <?php jecho(invoiceGetOptionList('charge_id'))?>;
function setChargeData(data,return_message)
{
	if(return_message=='add')
	{
		var target_id=String(charge_count);
		var site_id=getValue('site_id');
		addCharge();
		var mappings={
			1:{1:13, 2:14, 9:7, 16:16, 11:15},
			2:{11:20},
			3:{1:9, 2:10, 9:2, 16:12, 11:11},
			4:{11:8},
			5:{11:1},
			6:{11:5},
			7:{11:21},
			10:{11:data.charge_id}
		};
		var charge_id=0;
		if(mappings[data.class_id]!=null)
		{
			if(mappings[data.class_id][site_id]!=null)
				charge_id=mappings[data.class_id][site_id];
			else
				charge_id=mappings[data.class_id][11];
			setValue('charge_id['+target_id+']',charge_id);
			setValue('charge_product_id['+target_id+']','C'+charge_id);
			setValue('charge_name['+target_id+']',charge_names[charge_id]);
		}
		chargeChange(charge_count);
	}
}
function addCharge()
{
	var success=false;
	var currency=getValue('invoice_currency');
	if(currency==null || currency=='')
	{
		alert('You are unable to add charges until a currency is selected');
		var currencyElem=getElement('invoice_currency');
		if(currencyElem!=null && currencyElem.focus!=null) currencyElem.focus();
		return;
	}
	var site_id=getValue('site_id');
	var ccount=String(charge_count);
	var prod_table=getElement("charges_table");
	if(prod_table!=null)
	{
		success=true;
		var newRow1=addTableRow(prod_table,'invoice_charges_rowa['+ccount+']');
		var newRow2=addTableRow(prod_table,'invoice_chrages_rowb['+ccount+']');
		newRow2.className='alt';
<?php
$count = 0;
foreach (explode("\n", invoice_charges_row('[CHARGE_COUNT]', array('charge_rank' => '[CHARGE_COUNT]'))) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[CHARGE_COUNT]", '" + ccount + "', jecho(substr($row, 4, -5), 1)) . ");\n";
    } elseif (substr($row, 0, 13) == '<td colspan="' && substr($row, 14, 2) == '">') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[CHARGE_COUNT]", '" + ccount + "', jecho(substr($row, strpos($row, '>') + 1, -5), 1)) . ").colSpan = " . substr($row, 13, 1) . ";\n";
    }

}
?>
	}
	if(success) charge_count++;
	return success;
}
function addProduct()
{
	var success=false;
	var currency=getValue('invoice_currency');
	if(currency==null || currency=='')
	{
		alert('You are unable to add products until a currency is selected');
		var currencyElem=getElement('invoice_currency');
		if(currencyElem!=null && currencyElem.focus!=null) currencyElem.focus();
		return;
	}
	var count=String(product_count);
	var prod_table=getElement("product_table");
	if(prod_table!=null)
	{
		success=true;
		var newRow1=addTableRow(prod_table,'invoice_products_rowa['+count+']');
		var newRow2=addTableRow(prod_table,'invoice_products_rowb['+count+']');
		newRow2.className='alt';
<?php
$count = 0;
foreach (explode("\n", invoice_products_row('[PRODUCT_COUNT]', array('product_rank' => '[PRODUCT_COUNT]', 'generic' => 'n'))) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + count + "', jecho(substr($row, 4, -5), 1)) . ");\n";
    } elseif (substr($row, 0, 13) == '<td colspan="' && substr($row, 14, 2) == '">') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + count + "', jecho(substr($row, strpos($row, '>') + 1, -5), 1)) . ").colSpan = " . substr($row, 13, 1) . ";\n";
    }

}
?>
	}
	var supp_table=getElement("supplier_table");
	if(supp_table!=null)
	{
		success=true;
		var newRow1=addTableRow(supp_table,'supplier_rowa['+count+']');
		var newRow2=addTableRow(supp_table,'supplier_rowb['+count+']');
		var newRow3=addTableRow(supp_table,'supplier_rowc['+count+']');
		var newRow4=addTableRow(supp_table,'supplier_rowd['+count+']');
		var newRow5=addTableRow(supp_table,'supplier_rowe['+count+']');
		var newRow6=addTableRow(supp_table,'supplier_rowf['+count+']');
		var newRow7=addTableRow(supp_table,'supplier_rowg['+count+']');
		var newRow8=addTableRow(supp_table,'supplier_rowh['+count+']');
		var newRow9=addTableRow(supp_table,'supplier_rowi['+count+']');
		newRow3.className=newRow4.className=newRow5.className=newRow6.className=newRow8.className=newRow9.className='alt'; newRow6.style.textAlign='center';
<?php
$count = 0;
foreach (explode("\n", supplier_row('[PRODUCT_COUNT]', array('generic' => 'y'))) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<th>') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + count + "', jecho(substr($row, 4, -5), 1)) . ").className = 'th';\n";
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + count + "', jecho(substr($row, 4, -5), 1)) . ");\n";
    } elseif (substr($row, 0, 13) == '<td colspan="' && substr($row, 14, 2) == '">') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + count + "', jecho(substr($row, strpos($row, '>') + 1, -5), 1)) . ").colSpan = " . substr($row, 13, 1) . ";\n";
    } elseif (substr($row, 0, 13) == '<td colspan="') {
        $cspan = substr($row, 13, 1);
        echo "var theCell = addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + count + "', jecho(substr($row, strpos($row, '>') + 1, -5), 1)) . ");\n";
        echo "theCell.colSpan = " . $cspan . ";\n";
        if (8 == $cspan) {
            echo "theCell.style.textAlign = 'center';\n";
        }

        echo "theCell.className = 'left_title';\n";
    }
}
?>
	}
	if(success)
	{
		product_count++;
		return product_count-1;
	}
	return false;
}
function addGeneric()
{
	var success=false;
	var currency=getValue('invoice_currency');
	if(currency==null || currency=='')
	{
		alert('You are unable to add products until a currency is selected');
		var currencyElem=getElement('invoice_currency');
		if(currencyElem!=null && currencyElem.focus!=null) currencyElem.focus();
		return;
	}
	var count=String(product_count);
	var prod_table=getElement("product_table");
	if(prod_table!=null)
	{
		success=true;
		var newRow1=addTableRow(prod_table,'invoice_products_rowa['+count+']');
		var newRow2=addTableRow(prod_table,'invoice_products_rowb['+count+']');
		newRow2.className='alt';
<?php
$count = 0;
foreach (explode("\n", invoice_products_row('[PRODUCT_COUNT]', array('product_rank' => '[PRODUCT_COUNT]', 'product_qty' => '1', 'generic' => 'y'))) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + count + "', jecho(substr($row, 4, -5), 1)) . ");\n";
    } elseif (substr($row, 0, 13) == '<td colspan="' && substr($row, 14, 2) == '">') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + count + "', jecho(substr($row, strpos($row, '>') + 1, -5), 1)) . ").colSpan = " . substr($row, 13, 1) . ";\n";
    }

}
?>
	}
	var supp_table=getElement("supplier_table");
	if(supp_table!=null)
	{
		success=true;
		var newRow1=addTableRow(supp_table,'supplier_rowa['+count+']');
		var newRow2=addTableRow(supp_table,'supplier_rowb['+count+']');
		var newRow3=addTableRow(supp_table,'supplier_rowc['+count+']');
		var newRow4=addTableRow(supp_table,'supplier_rowd['+count+']');
		var newRow5=addTableRow(supp_table,'supplier_rowe['+count+']');
		var newRow6=addTableRow(supp_table,'supplier_rowf['+count+']');
		var newRow7=addTableRow(supp_table,'supplier_rowg['+count+']');
		var newRow8=addTableRow(supp_table,'supplier_rowh['+count+']');
		var newRow9=addTableRow(supp_table,'supplier_rowi['+count+']');
		newRow3.className=newRow4.className=newRow5.className=newRow6.className=newRow8.className=newRow9.className='alt';
<?php
$count = 0;
foreach (explode("\n", supplier_row('[PRODUCT_COUNT]', array('generic' => 'y'))) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<th>') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + count + "', jecho(substr($row, 4, -5), 1)) . ").className = 'th';\n";
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + count + "', jecho(substr($row, 4, -5), 1)) . ");\n";
    } elseif (substr($row, 0, 13) == '<td colspan="' && substr($row, 14, 2) == '">') {
        echo "addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + count + "', jecho(substr($row, strpos($row, '>') + 1, -5), 1)) . ").colSpan = " . substr($row, 13, 1) . ";\n";
    } elseif (substr($row, 0, 13) == '<td colspan="') {
        $cspan = substr($row, 13, 1);
        echo "var theCell = addTableCell(newRow" . $count . ", " . str_replace("[PRODUCT_COUNT]", '" + count + "', jecho(substr($row, strpos($row, '>') + 1, -5), 1)) . ");\n";
        echo "theCell.colSpan=" . $cspan . ";\n";
        if (8 == $cspan) {
            echo "theCell.style.textAlign='center';\n";
        }

        echo "theCell.className='left_title';\n";
    }
}
?>
	}
	if(success)
	{
		product_count++;
		return product_count-1;
	}
	return false;
}
function addBulk()
{
	var result=addGeneric();
	if(result!=false) setValue('generic['+result+']','b');
	return result;
}
function deleteCharge(row)
{
	var theRow=getElement("invoice_charges_rowa["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	var theRow=getElement("invoice_charges_rowb["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	chargeChange();
}
function deleteFreight()
{
	setValue('ship_id','');
	setValue('ship_product_id','');
	setValue('ship_product_name','');
	setValue('ship_courier_id','');
	setValue('ship_notes','');
	setValue('supplier_ship_notes','');
	setValue('ship_cost','');
	setValue('ship_offer','');
	setValue('ship_online','');
	setValue('ship_costau','');
	setValue('ship_offerau','');
	var theRow=getElement("product_ship_rowa");
	if(theRow) theRow.style.display='none';
	var theRow=getElement("product_ship_rowb");
	if(theRow) theRow.style.display='none';
	var theRow=getElement("supplier_ship_rowa");
	if(theRow) theRow.style.display='none';
	var theRow=getElement("supplier_ship_rowb");
	if(theRow) theRow.style.display='none';
	shipChange();
}
function deleteProduct(row)
{
	var theRow=getElement("invoice_products_rowa["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	theRow=getElement("invoice_products_rowb["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	theRow=getElement("supplier_rowa["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	theRow=getElement("supplier_rowb["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	theRow=getElement("supplier_rowc["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	theRow=getElement("supplier_rowd["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	theRow=getElement("supplier_rowe["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	theRow=getElement("supplier_rowf["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	theRow=getElement("supplier_rowg["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	theRow=getElement("supplier_rowh["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	theRow=getElement("supplier_rowi["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	productChange();
}
function chargeChange(row,change_type)
{
	var do_totals=false;
	do_totals=true;
	switch(change_type)
	{
	case 'pofferau':
		setValue("charge_offer["+row+"]", convertCurrency(getValue("charge_offerau["+row+"]"), "AUD", getValue("invoice_currency"),true));
		break;
	}
	if(do_totals) productUpdateTotals();
}
function productChange(row,change_type)
{
var do_totals=false;
var do_located=false;
switch(change_type)
{
case 'pid':
setValue('supplier_product_id['+row+']',getValue('product_id['+row+']'));
setValue('supplier_sub_id['+row+']',getValue('sub_id['+row+']'));
break;
case 'pname':
if(getValue("product_id["+row+"]")<1 || getValue("supplier_id["+row+"]")<1)
setValue("supplier_product_name["+row+"]",getValue("product_name["+row+"]"));
break;
case 'pqty':
setValue("supplier_product_qty["+row+"]",getValue("product_qty["+row+"]"));
productUpdateWeight();
do_totals=true;
break;
case 'pcost':
var supplier_currency=getValue("supplier_currency["+row+"]");
var invoice_currency=getValue("invoice_currency");
var product_cost=getValue("product_cost["+row+"]");
setValue("supplier_cost["+row+"]",convertCurrency(product_cost,invoice_currency,supplier_currency,true));
setValue("supplier_product_cost["+row+"]",product_cost);
setValue("product_costau["+row+"]",convertCurrency(product_cost,invoice_currency,"AUD",true));
do_totals=true;
break;
case 'ponline':
do_totals=true;
break;
case 'poffer':
setValue("product_offerau["+row+"]", convertCurrency(getValue("product_offer["+row+"]"), getValue("invoice_currency"), "AUD",true));
setValue('product_price_changed['+row+']','y');
do_totals=true;
break;
case 'pofferau':
setValue("product_offer["+row+"]", convertCurrency(getValue("product_offerau["+row+"]"), "AUD", getValue("invoice_currency"),true));
do_totals=true;
break;
case 'sid':
setValue('product_id['+row+']',getValue('supplier_product_id['+row+']'));
setValue('sub_id['+row+']',getValue('supplier_sub_id['+row+']'));
break;
case 'sname':
setValue("product_name["+row+"]",getValue("supplier_product_name["+row+"]"));
break;
case 'sfound':
do_located=true;
break;
case 'sqty':
setValue("product_qty["+row+"]",getValue("supplier_product_qty["+row+"]"));
productUpdateWeight();
do_totals=true;
break;
case 'scost':
var supplier_currency=getValue("supplier_currency["+row+"]");
var invoice_currency=getValue("invoice_currency");
var supplier_product_cost=getValue("supplier_product_cost["+row+"]");
setValue("supplier_cost["+row+"]",convertCurrency(supplier_product_cost,invoice_currency,supplier_currency,true));
setValue("product_costau["+row+"]",convertCurrency(supplier_product_cost,invoice_currency,"AUD",true));
setValue("product_cost["+row+"]",supplier_product_cost);
do_totals=true;
break;
case 'ocost':
var supplier_currency=getValue("supplier_currency["+row+"]");
var invoice_currency=getValue("invoice_currency");
var supplier_cost=getValue("supplier_cost["+row+"]");
setValue("supplier_product_cost["+row+"]",convertCurrency(supplier_cost,supplier_currency,invoice_currency,true));
setValue("product_costau["+row+"]", convertCurrency(supplier_cost,supplier_currency,"AUD",true));
setValue("product_cost["+row+"]",convertCurrency(supplier_cost,supplier_currency,invoice_currency,true));
do_totals=true;
break;
case 'scostau':
var supplier_currency=getValue("supplier_currency["+row+"]");
var invoice_currency=getValue("invoice_currency");
var product_costau=Number(getValue("product_costau["+row+"]"));
setValue("supplier_cost["+row+"]",convertCurrency(product_costau,"AUD",supplier_currency,true));
setValue("supplier_product_cost["+row+"]",convertCurrency(product_costau,"AUD",invoice_currency,true));
setValue("product_cost["+row+"]",convertCurrency(product_costau,"AUD",invoice_currency,true));
do_totals=true;
break;
default:
do_totals=true;
break;
}
if(do_totals) productUpdateTotals();
if(do_located) productUpdateLocated();
}
function getProductRows()
{
var product_table=getElement("product_table");
var invoice_products_rows=[];
if(product_table!=null)
{
var theRows=product_table.rows;
for(var i=0;i<theRows.length;i++)
if(theRows[i].id.substr(0,13)=='invoice_products_rowa[')
if(searchArray(theRows[i].id.substr(12),invoice_products_rows)==null)
invoice_products_rows[invoice_products_rows.length]=theRows[i].id.substr(12);
}
return invoice_products_rows;
}
function getChargeRows()
{
var charges_table=getElement("charges_table");
var invoice_charges_rows=[];
if(charges_table!=null)
{
var theRows=charges_table.rows;
for(var i=0;i<theRows.length;i++)
if(theRows[i].id.substr(0,20)=='invoice_charges_rowa[')
if(searchArray(theRows[i].id.substr(19),invoice_charges_rows)==null)
invoice_chrages_rows[invoice_charges_rows.length]=theRows[i].id.substr(19);
}
return invoice_charges_rows;
}
function rankProduct(row,direction)
{
var from_row='['+row+']';
var from_rank=Number(getValue("product_rank"+from_row));
var swap_row=null;
var swap_rank=null;
var invoice_products_rows=getProductRows();
for(var i=0;i<invoice_products_rows.length;i++)
{
var check_row=invoice_products_rows[i];
var check_rank=Number(getValue("product_rank"+check_row));
if(check_row!=from_row && ((direction<0 && check_rank<from_rank && (swap_rank==null || check_rank>swap_rank)) || (direction>0 && check_rank>from_rank && (swap_rank==null || check_rank<swap_rank))))
{
swap_row=check_row;
swap_rank=check_rank;
}
}
if(swap_row!=null)
{
setValue("product_rank"+from_row,swap_rank);
setValue("product_rank"+swap_row,from_rank);
if(direction>0)
{
var temp=from_row;
from_row=swap_row;
swap_row=temp;
}
var source_row;
var target_row=getElement("invoice_products_rowa"+swap_row);
var target_table=target_row.parentNode;
if(source_row=getElement("invoice_products_rowa"+from_row)) target_table.insertBefore(source_row,target_row);
if(source_row=getElement("invoice_products_rowb"+from_row)) target_table.insertBefore(source_row,target_row);
var target_row=getElement("supplier_rowa"+swap_row);
var target_table=target_row.parentNode;
if(source_row=getElement("supplier_rowa"+from_row)) target_table.insertBefore(source_row,target_row);
if(source_row=getElement("supplier_rowb"+from_row)) target_table.insertBefore(source_row,target_row);
if(source_row=getElement("supplier_rowc"+from_row)) target_table.insertBefore(source_row,target_row);
if(source_row=getElement("supplier_rowd"+from_row)) target_table.insertBefore(source_row,target_row);
if(source_row=getElement("supplier_rowe"+from_row)) target_table.insertBefore(source_row,target_row);
if(source_row=getElement("supplier_rowf"+from_row)) target_table.insertBefore(source_row,target_row);
if(source_row=getElement("supplier_rowg"+from_row)) target_table.insertBefore(source_row,target_row);
if(source_row=getElement("supplier_rowh"+from_row)) target_table.insertBefore(source_row,target_row);
}
}
function rankCharge(row,direction)
{
var from_row='['+row+']';
var from_rank=Number(getValue("charge_rank"+from_row));
var swap_row=null;
var swap_rank=null;
var invoice_charges_rows=getChargeRows();
for(var i=0;i<invoice_charges_rows.length;i++)
{
var check_row=invoice_charges_rows[i];
var check_rank=Number(getValue("charge_rank"+check_row));
if(check_row!=from_row && ((direction<0 && check_rank<from_rank && (swap_rank==null || check_rank>swap_rank)) || (direction>0 && check_rank>from_rank && (swap_rank==null || check_rank<swap_rank))))
{
swap_row=check_row;
swap_rank=check_rank;
}
}
if(swap_row!=null)
{
setValue("charge_rank"+from_row,swap_rank);
setValue("charge_rank"+swap_row,from_rank);
if(direction>0)
{
var temp=from_row;
from_row=swap_row;
swap_row=temp;
}
var source_row;
var target_row=getElement("invoice_charges_rowa"+swap_row);
var target_table=target_row.parentNode;
if(source_row=getElement("invoice_charges_rowa"+from_row)) target_table.insertBefore(source_row,target_row);
if(source_row=getElement("invoice_charges_rowb"+from_row)) target_table.insertBefore(source_row,target_row);
}
}
function productUpdateLocated()
{
var all_found=true;
var supplier_rows=[];
var theRows=getElement("supplier_table").rows;
for(var i=0;i<theRows.length;i++)
if(theRows[i].id.substr(0,14)=='supplier_rowa[')
if(searchArray(theRows[i].id.substr(13),supplier_rows)==null)
supplier_rows[supplier_rows.length]=theRows[i].id.substr(13);
for(var i=0;i<supplier_rows.length;i++)
if(getValue("supplier_found"+supplier_rows[i])=='n')
all_found=false;
setValue('products_located',(all_found)?'y':'n');
}
function shipChange(change_type)
{
var do_totals=false;
switch(change_type)
{
case 'pname':
setValue("supplier_ship_product_name",getValue("ship_product_name"));
break;
case 'pqty':
setValue("supplier_ship_qty",getValue("ship_qty"));
do_totals=true;
break;
case 'pcost':
setValue("ship_costau",convertCurrency(getValue("ship_cost"),getValue("invoice_currency"),"AUD",true));
setValue("supplier_ship_cost",getValue("ship_cost"));
do_totals=true;
break;
case 'ponline':
do_totals=true;
break;
case 'poffer':
setValue("ship_offerau", convertCurrency(getValue("ship_offer"), getValue("invoice_currency"), "AUD", true));
do_totals=true;
break;
case 'pofferau':
setValue("ship_offer", convertCurrency(getValue("ship_offerau"), "AUD", getValue("invoice_currency"),true));
do_totals=true;
break;
case 'sname':
setValue("ship_product_name",getValue("supplier_ship_product_name"));
break;
case 'sqty':
setValue("ship_qty",getValue("supplier_ship_qty"));
do_totals=true;
break;
case 'scost':
setValue("ship_costau", convertCurrency(getValue("supplier_ship_cost"), getValue("invoice_currency"), "AUD",true));
setValue("ship_cost",getValue("supplier_ship_cost"));
do_totals=true;
break;
case 'scostau':
setValue("supplier_ship_cost", convertCurrency(getValue("ship_costau["+row+"]"), "AUD", getValue("invoice_currency"),true));
setValue("ship_cost",getValue("supplier_ship_cost"));
do_totals=true;
break;
default:
do_totals=true;
break;
}
if(do_totals) productUpdateTotals();
}
function productUpdateWeight()
{
var total_weight=0.0;
var invoice_products_rows=[];
var theRows=getElement("product_table").rows;
for(var i=0;i<theRows.length;i++)
if(theRows[i].id.substr(0,13)=='invoice_products_rowa[')
if(searchArray(theRows[i].id.substr(12),invoice_products_rows)==null)
invoice_products_rows[invoice_products_rows.length]=theRows[i].id.substr(12);
for(var i=0;i<invoice_products_rows.length;i++)
{
var rowNum=invoice_products_rows[i];
var addMe=0;
var product_qty=Number(getValue("product_qty"+rowNum));
var product_weight=Number(getValue("product_weight"+rowNum));
var product_weight_inc=Number(getValue("product_weight_inc"+rowNum));
if(product_qty>0)
{
addMe=product_weight;
if(!isNaN(addMe)) total_weight+=addMe;
if(product_qty>1)
{
addMe=(product_qty-1)*product_weight_inc;
if(!isNaN(addMe)) total_weight+=addMe;
}
}
}
setAllValues('ship_weight',Number(total_weight).toFixed(2));
}
function productUpdateTotals()
{
var total_cost=0.0;
var total_costau=0.0;
var total_offer=0.0;
var total_offerau=0.0;
var total_online=0.0;
var invoice_products_rows=[];
var invoice_charges_rows=[];
var theRows=getElement("product_table").rows;
var theRows1=getElement("charges_table").rows;
for(var i=0;i<theRows.length;i++)
{
if(theRows[i].id.substr(0,13)=='invoice_products_rowa[')
if(searchArray(theRows[i].id.substr(12),invoice_products_rows)==null)
invoice_products_rows[invoice_products_rows.length]=theRows[i].id.substr(12);
}
for(var i=0;i<theRows1.length;i++)
{
if(theRows1[i].id.substr(0,20)=='invoice_charges_rowa[')
if(searchArray(theRows1[i].id.substr(19),invoice_charges_rows)==null)
invoice_charges_rows[invoice_charges_rows.length]=theRows1[i].id.substr(19);
}
var in_cur=getValue('invoice_currency');
for(var i=0;i<invoice_products_rows.length;i++)
{
var rowNum=invoice_products_rows[i];
var addMe=0;
var product_qty=Number(getValue("product_qty"+rowNum));
var product_cost=Number(getValue("product_cost"+rowNum));
var product_costau=Number(getValue("product_costau"+rowNum));
var product_offer=Number(getValue("product_offer"+rowNum));
var product_offerau=Number(getValue("product_offerau"+rowNum));
var product_online=Number(getValue("product_online"+rowNum));
addMe=product_qty*product_cost;
if(!isNaN(addMe)) total_cost+=addMe;
addMe=product_qty*product_costau;
if(!isNaN(addMe)) total_costau+=addMe;
addMe=product_qty*product_offer;
if(!isNaN(addMe)) total_offer+=addMe;
addMe=product_qty*product_offerau;
if(!isNaN(addMe)) total_offerau+=addMe;
addMe=product_qty*product_online;
if(!isNaN(addMe)) total_online+=addMe;
}
var products_cost=total_cost;
var products_costau=total_costau;
var products_offer=total_offer;
var products_offerau=total_offerau;
setAllValues('products_cost',convertCurrency(products_cost.toFixed(2),in_cur,'',true));
setAllValues('products_costau',convertCurrency(products_costau.toFixed(2),'AUD','',true));
setAllValues('products_offer',convertCurrency(products_offer.toFixed(2),in_cur,'',true));
setAllValues('products_offerau',convertCurrency(products_offerau.toFixed(2),'AUD','',true));
if(getValue('ship_id')>0)
{
addMe=Number(getValue('ship_cost'));
if(!isNaN(addMe)) total_cost+=addMe;
addMe=Number(getValue('ship_costau'));
if(!isNaN(addMe)) total_costau+=addMe;
addMe=Number(getValue('ship_offer'));
if(!isNaN(addMe)) total_offer+=addMe;
addMe=Number(getValue('ship_offerau'));
if(!isNaN(addMe)) total_offerau+=addMe;
addMe=Number(getValue('ship_online'));
if(!isNaN(addMe)) total_online+=addMe;
}
var ship_cost=total_cost-products_cost;
var ship_costau=total_costau-products_costau;
var ship_offer=total_offer-products_offer;
var ship_offerau=total_offerau-products_offerau;
total_offerau=Number(total_offerau);
<?php
$charges_amount   = array();
$charges_currency = array();
$charges_percent  = array();
$charges_rounding = array();
$query            = $DB->query("SELECT * FROM `charges` ORDER BY `charge_priority` ASC");
while ($row = $DB->fetch_assoc($query)) {
    $charges_amount[$row['charge_id']]   = $row['charge_amount'];
    $charges_currency[$row['charge_id']] = $row['charge_amount_currency'];
    $charges_percent[$row['charge_id']]  = $row['charge_percent'];
    $charges_rounding[$row['charge_id']] = $row['charge_rounding'];
}
?>
var charges_ids = <?php jecho(array_keys($charges_amount))?>;
var charges_amount = <?php jecho(array_values($charges_amount))?>;
var charges_currency = <?php jecho(array_values($charges_currency))?>;
var charges_percent = <?php jecho(array_values($charges_percent))?>;
var charges_rounding = <?php jecho(array_values($charges_rounding))?>;
for(var j=0;j<charges_ids.length;j++)
{
for(var i=0;i<invoice_charges_rows.length;i++)
{
var rowNum=invoice_charges_rows[i];
var charge_id=Number(getValue('charge_id'+rowNum));
if(charge_id==charges_ids[j])
{
var addMe=0;
var amount=charges_amount[j];
var currency=charges_currency[j];
var percent=Number(charges_percent[j])/100.0;
var rounding=Number(charges_rounding[j]);
var charge_amount=charge_cost='0.00';
var charge_amount_au='0.00';
if(rounding==0.0 && percent==0.0 && amount=='0.00')
{
charge_cost=getValue('charge_cost'+rowNum);
charge_amount=getValue('charge_offer'+rowNum);
charge_amount_au=convertCurrency(charge_amount,in_cur,'AUD',true);
setValue('charge_online'+rowNum,charge_amount);
setValue('charge_offerau'+rowNum,charge_amount_au);
}
else
{
charge_amount=Number(convertCurrency(amount,currency,in_cur,true));
charge_amount+=Number(total_offer)*percent;
if(rounding!=0.0) charge_amount=(Math.ceil((total_offer+charge_amount)/rounding)*rounding)-total_offer;
charge_cost=charge_amount=convertCurrency(Number(charge_amount).toFixed(2),in_cur,'',true);
charge_amount_au=convertCurrency(charge_amount,in_cur,'AUD',true);
setValue('charge_cost'+rowNum,convertCurrency(charge_cost,in_cur,'',true));
setValue('charge_offer'+rowNum,convertCurrency(charge_amount,in_cur,'',true));
setValue('charge_offerau'+rowNum,convertCurrency(charge_amount_au,'AUD','',true));
setValue('charge_online'+rowNum,convertCurrency(charge_amount,in_cur,'',true));
}
total_cost+=Number(charge_cost);
total_costau+=Number(convertCurrency(charge_cost,in_cur,'AUD',true));
total_offer+=Number(charge_amount);
total_offerau+=Number(charge_amount_au);
total_online+=Number(charge_amount);
}
}
}
var charges_offer=total_offer-(products_offer+ship_offer);
var charges_offerau=total_offerau-(products_offerau+ship_offerau);
setAllValues('charges_offer',convertCurrency(Number(charges_offer).toFixed(2),in_cur,'',true));
setAllValues('charges_offerau',convertCurrency(Number(charges_offerau).toFixed(2),'AUD','',true));
setAllValues('invoice_cost',convertCurrency(Number(total_cost).toFixed(2),in_cur,'',true));
setAllValues('invoice_online',convertCurrency(Number(total_online).toFixed(2),in_cur,'',true));
setAllValues('invoice_offer',convertCurrency(Number(total_offer).toFixed(2),in_cur,'',true));
setAllValues('invoice_offerau',convertCurrency(Number(total_offerau).toFixed(2),'AUD','',true));
setAllValues('invoice_costau',convertCurrency(Number(total_costau).toFixed(2),'AUD','',true));
setAllValues('invoice_profit',convertCurrency(Number(total_offer-total_cost).toFixed(2),in_cur,'',true));
setAllValues('invoice_profitau',convertCurrency(Number(total_offerau-total_costau).toFixed(2),'AUD','',true));
}
function productShowOutlet()
{
var plug_id=Number(getValue('plug_id'));
if(plug_id==null || plug_id<1) return;
var url='/webadmin/world/plugs/show_image.php?id='+plug_id;
var features='scrollbars, resizable, height=400, width=600';
window.open(url,'powerplug_popup',features);
}
function searchProduct(row,searchFor)
{
var currency=getValue('invoice_currency');
var site_id=getValue('site_id');
var member_id=getValue('member_id');
if(site_id=='1' || site_id=='2') site_id='1|2';
var url='/webadmin/products/index.php?action=list&show_sites=n';
url+='&return_message=select_'+row;
url+='&search_for='+escape(searchFor);
if(currency!=null) url+='&currency='+currency;
if(site_id!=null) url+='&site_id='+site_id;
if(member_id!=null) url+='&member_id='+member_id;
var features='scrollbars, resizable';
window.open(url,'product_popup',features);
}
function searchSupplier(row,searchFor)
{
var generic=getValue('generic['+row+']');
var generic_code=getValue('generic_code['+row+']');
var url='/webadmin/suppliers/index.php?action=list';
if(generic=='y' && generic_code=='GFR') url='/webadmin/forwarders/?action=list';
url+='&return_message=select_'+row;
if(searchFor!='None') url+='&search_for='+escape(searchFor);
var features='scrollbars, resizable';
window.open(url,'Search',features);
}
function selectSupplier(row,supplier_id)
{
var url='/webadmin/suppliers/index.php?action=select';
url+='&return_message=select_'+row;
url+='&supplier_id='+supplier_id;
var features='scrollbars, resizable';
window.open(url,'Search',features);
}
function searchDefaultSupplier(searchFor)
{
var url='/webadmin/suppliers/index.php?action=list';
url+='&return_message=default';
if(searchFor!='None') url+='&search_for='+escape(searchFor);
var features='scrollbars, resizable';
window.open(url,'Search',features);
}
function splitProduct(row)
{
var rowNum='['+row+']';
var product_qty=getValue('product_qty'+rowNum);
if(product_qty==null || product_qty<2) return;
product_qty=Number(product_qty);
var split_qty=0;
if(product_qty<4)
{
split_qty=confirm('Are you sure you would like to split this product?');
if(!split_qty) return;
split_qty=1;
}
else
{
split_qty=prompt('How many of this product would you like to split? (1 - '+(product_qty-1)+')','1');
if(split_qty==null || split_qty<1 || split_qty>=product_qty) return;
}
var new_product=product_count;
addProduct();
var fields=Array('product_cost', 'product_id', 'sub_id', 'product_name', 'product_notes', 'product_offer', 'product_offerau', 'product_online', 'product_weight', 'product_weight_inc', 'supplier_product_cost', 'supplier_cost_onload', 'supplier_cost_orig', 'supplier_cost_orig_curr', 'product_costau', 'supplier_country', 'supplier_found', 'supplier_id', 'supplier_id_orig', 'supplier_name', 'supplier_notes', 'supplier_product_id', 'supplier_sub_id', 'supplier_product_name', 'supplier_product_qty');
for(var i=0;i<fields.length;i++)
{
var field=fields[i];
setValue(field+'['+new_product+']',getValue(field+rowNum));
}
setValue('product_qty['+new_product+']',split_qty);
setValue('supplier_product_qty['+new_product+']',split_qty);
setValue('product_qty'+rowNum,product_qty-split_qty);
setValue('supplier_product_qty'+rowNum,product_qty-split_qty);
}
function setBulkData(data)
{
if(data==null) return;
if(data['return_message']=='add_bulk')
{
var row=addBulk();
var currency=getValue('invoice_currency');
var bulk_number='000000'+data['bulk_id'];
bulk_number='BO-'+bulk_number.substring(bulk_number.length-4);
setValue('product_id['+row+']',				bulk_number);
setValue('sub_id['+row+']',					'0');
setValue('product_name['+row+']',			data['title']);
setValue('product_qty['+row+']',			'1');
setValue('product_cost['+row+']',			convertCurrency(data['bulk_cost'],'AUD',currency));
setValue('supplier_product_cost['+row+']',	convertCurrency(data['bulk_cost'],'AUD',currency));
setValue('product_costau['+row+']',			data['bulk_cost']);
setValue('product_online['+row+']',			convertCurrency(data['bulk_online']/1.1,'AUD',currency));
setValue('product_offer['+row+']',			convertCurrency(data['bulk_offer']/1.1,'AUD',currency));
setValue('product_offerau['+row+']',		convertCurrency(data['bulk_offer']/1.1,'AUD'));
setValue('product_weight['+row+']',			data['bulk_weight']);
setValue('product_weight_inc['+row+']',		data['bulk_weight']);
setValue('product_warranty['+row+']',		data['bulk_warranty']);
setValue('product_condition['+row+']',		data['bulk_condition']);
setValue('product_terms['+row+']',			data['bulk_terms']);
setValue('product_voltage['+row+']',		data['bulk_voltage']);
setValue('generic_code['+row+']',			bulk_number);
setValue('product_price_changed['+row+']',	'n');
if(data['bulk_notes']!=null) setValue('product_notes['+row+']', data['bulk_notes']);
if(data['bulk_weight']<=0.0 && addMissingWeight!=null) addMissingWeight(bulk_number,data['title'],data['bulk_url']);
var data2=data;
data2['return_message']='bulk_select_'+row;
setSupplierData(data2);
productUpdateWeight();
productChange(row);
}
}
function setProductData(data)
{
if(data==null) return;
if(data['return_message'].substring(0,7)=='select_')
{
var row=data['return_message'].substring(7);
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
product_online[100]
product_price_changed[100]
product_qty[100]
product_rank[100]
product_updated[100]
product_weight[100]
product_weight_inc[100]
sub_id[100]
*/
if(data['product_id']==null)			data['product_id']=getValue('product_id['+row+']');
if(data['sub_id']==null)				data['sub_id']=0;
if(data['min_order']==null)				data['min_order']=1;
if(data['product_cost']==null)			data['product_cost']='0.00';
if(data['product_online']==null)		data['product_online']='0.00';
if(data['product_offer']==null)			data['product_offer']='0.00';
if(data['product_weight']==null)		data['product_weight']=0.0;
if(data['product_weight_inc']==null)	data['product_weight_inc']=0.0;
if(data['product_weight_inc']==0.0)		data['product_weight_inc']=data['product_weight'];
if(data['product_notes']==null)			data['product_notes']='';
if(data['product_condition']==null)		data['product_condition']=0;
if(data['stock']==null)					data['stock']='c';
setValue('product_id['+row+']',				data['product_id']);
setValue('sub_id['+row+']',					data['sub_id']);
setValue('product_name['+row+']',			data['product_name']);
setValue('product_qty['+row+']',			data['min_order']);
setValue('product_condition['+row+']',		data['product_condition']);
setValue('product_cost['+row+']',			data['product_cost']);
setValue('product_costau['+row+']',			data['product_cost_au']);
setValue('product_notes['+row+']',			data['product_notes']);
setValue('product_offer['+row+']',			data['product_offer']);
setValue('product_offerau['+row+']',		data['product_offerau']);
setValue('product_online['+row+']',			data['product_online']);
setValue('product_price_changed['+row+']',	'n');
setValue('product_warranty['+row+']',		data['warranty']);
setValue('product_weight['+row+']',			data['product_weight']);
setValue('product_weight_inc['+row+']',		data['product_weight_inc']);
setValue('supplier_product_cost['+row+']',	data['product_cost']);
if(data['supplier_id']==0 && (data['stock']=='s' || data['stock']=='ss'))
{
data['supplier_id']='660';
data['supplier_name']='Highway Entertainment Pty Ltd';
data['country']='AU';
if(data['voltage']!=null)		setValue('product_voltage['+row+']',	data['voltage']);
if(data['plug_type']!=null)		setValue('product_plug['+row+']',		data['plug_type']);
if(data['condition']!=null)		setValue('product_condition['+row+']',	data['condition']);
if(data['packing_terms']!=null)	setValue('product_packing['+row+']',	data['packing_terms']);
if(data['ship_terms']!=null)	setValue('product_terms['+row+']',		data['ship_terms']);
}
setValue('supplier_product_qty['+row+']',	data['min_order']);
if(data['producttype_id']!=null)
setValue('producttype_id',data['producttype_id']);
category=data['producttype_id'];
if(data['product_weight']<=0.0 && addMissingWeight!=null)
addMissingWeight(data['product_id'],data['product_name'],data['product_url']);
}
setSupplierData(data);
if(data['supplier']!=null)
{
if(typeof(data['supplier'])=="object")
selectSupplier(row,data['supplier'][0]);
else
selectSupplier(row,data['supplier']);
}
productUpdateWeight();
productChange(row);
}
function setForwardersData(data)
{
if(data['return_message'].substring(0,7)=='select_')
{
var row=data['return_message'].substring(7);
setValue('supplier_currency['+row+']',data['currency']);
setValue('supplier_id['+row+']',data['id']);
setValue('supplier_name['+row+']',data['name']);
setValue('supplier_country['+row+']',data['country']);
setValue('supplier_found['+row+']','y');
productChange(row,'sfound');
}
}
function setForwarderData(data)
{
if(data['return_message'].substring(0,7)=='select_')
{
var row=data['return_message'].substring(7);
setValue('supplier_id['+row+']',data['id']);
setValue('supplier_name['+row+']',data['name']);
setValue('supplier_country['+row+']',data['country']);
setValue('supplier_found['+row+']','y');
productChange(row,'sfound');
}
}
function setSupplierData(data)
{
if(data == null) return;
if(data['return_message']=='default')
{
setValue('def_sup_name',data['supplier_name']);
setValue('default_supplier',data['id']);
if(data['warranty']!= null) setValue('def_warranty',data['warranty']);
if(data['condition']!= null) setValue('def_condition',data['condition']);
if(data['packing']!= null) setValue('def_packing',data['packing']);
if(data['voltage']!= null) setValue('def_voltage',data['voltage']);
if(data['plug']!= null) setValue('def_plug',data['plug']);
if(data['terms']!= null) setValue('def_terms',data['terms']);
if(data['currency']!=null) setValue('def_currency',data['currency']);
if(data['country']!=null)
{
setValue('def_country',data['country']);
setValue('supply_country',data['country']);
}
}
if(data['return_message'].substring(0,7)=='select_')
{
var row=data['return_message'].substring(7);
/*
product_condition[100]
product_costau[100]
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
if(data['supplier_id']=='' || data['supplier_id']==null)
data['supplier_id']=data['id'];
if(data['supplier_product_name']=='' || data['supplier_product_name']==null)
data['supplier_product_name']=data['product_name'];
if(data['cost_currency']!=null)			setValue('supplier_currency['+row+']',		data['cost_currency']);
if(data['cost_price']!=null)			setValue('supplier_cost['+row+']',			data['product_cost']);
if(data['supplier_id']!=null)			setValue('supplier_id['+row+']',			data['supplier_id']);
if(data['product_id']!=null)			setValue('supplier_product_id['+row+']',	data['product_id']);
if(data['sub_id']!=null)				setValue('supplier_sub_id['+row+']',		data['sub_id']);
if(data['supplier_product_name']!=null)	setValue('supplier_product_name['+row+']',	data['supplier_product_name']);
if(data['supplier_name']!=null)			setValue('supplier_name['+row+']',			data['supplier_name']);
if(data['currency']!=null)
{
setValue('supplier_currency['+row+']',	data['currency']);
productChange(row,'scost');
}
if(data['country']!=null)				setValue('supplier_country['+row+']',		data['country']);
if(data['warranty']!=null)				setValue('product_warranty['+row+']',		data['warranty']);
if(data['voltage']!=null)				setValue('product_voltage['+row+']',		data['voltage']);
if(data['plug_type']!=null)				setValue('product_plug['+row+']',			data['plug_type']);
if(data['condition']!=null)				setValue('product_condition['+row+']',		data['condition']);
if(data['packing_terms']!=null)			setValue('product_packing['+row+']',		data['packing_terms']);
if(data['ship_terms']!=null)			setValue('product_terms['+row+']',			data['ship_terms']);
if(data['qty']!=null && data['reserved']!=null)
setValue('supplier_found['+row+']',((data['qty']-data['reserved']>0)?((data['stock']=='s' || data['stock']=='ss')?'s':'y'):'n'));
productChange(row,'sfound');
}
if(data['return_message'].substring(0,12)=='bulk_select_')
{
var row=data['return_message'].substring(12);
var bulk_number='000000'+data['bulk_id'];
bulk_number='BO-'+bulk_number.substring(bulk_number.length-4);
if(data['supplier_id']=='' || data['supplier_id']==null)
data['supplier_id']=data['id'];
if(data['supplier_product_name']=='' || data['supplier_product_name']==null)
data['supplier_product_name']=data['product_name'];
setValue('supplier_country['+row+']',		data['bulk_country']);
setValue('supplier_id['+row+']',			data['bulk_supplier']);
setValue('supplier_name['+row+']',			data['bulk_supplier_name']);
setValue('supplier_product_qty['+row+']','1');
setValue('supplier_currency['+row+']','AUD');
setValue('supplier_cost['+row+']',data['bulk_cost']);
setValue('supplier_product_id['+row+']',bulk_number);
setValue('supplier_sub_id['+row+']','');
setValue('supplier_product_name['+row+']',data['title']);
setValue('supplier_found['+row+']','y');
productChange(row,'sfound');
}
}
function updateAllSuppliers(supplier_name, obj)
{
var old_val=obj.value;
obj.value='Please Wait...Updating';
var supplier_id=getValue('default_supplier');
var rows=getElement('supplier_table').rows;
var str='';
var ids=Array();
var count=0;
for(key in rows)
{
var n=rows[key].id;
if(n && n!=null)
{
var t1=n.split("[");
var t2=t1[1].split("]");
if(!searchArray(t2[0],ids))
{
ids[count]=t2[0];
count++;
}
}
}
for(key in ids)
{
setValue('supplier_name['+ids[key]+']',supplier_name);
setValue('supplier_id['+ids[key]+']',supplier_id);
var updateNotes=false;
if(getValue('def_warranty')!=0)	{setValue('product_warranty['+ids[key]+']',getValue('def_warranty')); updateNotes=true;}
if(getValue('def_condition')!=0){setValue('product_condition['+ids[key]+']',getValue('def_condition')); updateNotes=true;}
if(getValue('def_voltage')!=0)	{setValue('product_voltage['+ids[key]+']',getValue('def_voltage')); updateNotes=true;}
if(getValue('def_plug')!=0)		{setValue('product_plug['+ids[key]+']',getValue('def_plug')); updateNotes=true;}
if(getValue('def_currency')!="")
{
setValue('supplier_currency['+ids[key]+']',getValue('def_currency'));
productChange(ids[key],'scostau');
updateNotes=true;
}
if(getValue('def_country')!="")
{
setValue('supplier_country['+ids[key]+']',getValue('def_country'));
setValue('supply_country',getValue('def_country'));
}
if(getValue('def_packing')!=0)	setValue('product_packing['+ids[key]+']',getValue('def_packing'));
if(getValue('def_terms')!=0)	setValue('product_terms['+ids[key]+']',getValue('def_terms'));
if(updateNotes) updateProductNotes(ids[key]);
}
obj.value=old_val;
}
function updateProductNotes(prod_count)
{
	var warranty = <?php jecho($warranty_terms)?>;
	var voltage = <?php jecho($voltage_terms)?>;
	var notes = [];
	var val1=getValue('product_voltage['+prod_count+']');
	var val2=getValue('product_plug['+prod_count+']');
	var val3=getValue('product_warranty['+prod_count+']');
	var val4=getValue('product_condition['+prod_count+']');
	var count=0;
	if(val1!=0) { notes[count]='Power Supply Voltage: '+voltage[val1]; count++ }
	if(val2!=0) { notes[count]='Power Adapter ID: '+val2; count++;  }
	if(val3!=0) { notes[count]='Warranty: '+warranty[val3]; count++; }
	if(val4==2) { notes[count]='Condition: Used'; count++; }
	if(getValue('product_updated['+prod_count+']') == 'n') setValue('product_notes['+prod_count+']',notes.join("\n"));
}
function actionPurchasePreview(theFrm)
{
var oldact=theFrm.action;
var oldtrg=theFrm.target;
theFrm.action="/webadmin/invoice/invoice_purchase_preview.php";
theFrm.target="_blank";
theFrm.submit();
theFrm.action=oldact;
theFrm.target=oldtrg;
}
function actionPurchaseOrder(theFrm)
{
var rows=getElement('product_table').rows;
var ids=Array();
var count=0;
for(var i=0;i<rows.length;i++)
{
var n=rows[i].id;
if(n!=null)
{
var t1=n.split("[");
var t2=t1[1].split("]");
if(!array_search(t2[0],ids))
ids[count++]=t2[0];
}
}
for(var i=0;i<ids.length;i++)
if(getValue('supplier_id['+ids[i]+']')==0)
{
alert("One or more products do not have a supplier\n\nPlease correct this before trying again");
return false;
}
var oldact=theFrm.action;
theFrm.action="/webadmin/invoice/?action=do_purchase";
theFrm.submit();
theFrm.action=oldact;
}
function updateCostPrice(id, site_id, row_id)
{
var url='update_cost_price.php?product_id='+id+'&site_id='+site_id+'&return_message='+row_id+'&currency='+getValue('invoice_currency');
if(confirm('This will update the cost price for ID#'+id+'. Continue??')) window.open(url,'','width=10,height=10');
}
function setCostPrice(data)
{
row_id=data['return_message'];
setValue('supplier_product_cost['+row_id+']',data['product_cost']);
setValue('product_costau['+row_id+']',data['product_cost_au']);
productChange(row_id,'scost');
productChange(row_id,'scostau');
}
function updateProductPrice(row)
{
var product_id=getValue('product_id['+row+']');
var sub_id=getValue('sub_id['+row+']');
var product_qty=getValue('product_qty['+row+']');
var generic=getValue('generic['+row+']');
var site_id=getValue('site_id');
var member_id=getValue('member_id');
var currency=getValue('invoice_currency');
var url = 'invoice_update_price.php?product_id='+product_id;
if(sub_id!=null) url+='&sub_id='+sub_id;
if(site_id!=null) url += '&site_id='+site_id;
if(member_id!=null) url += '&member_id='+member_id;
if(currency!=null) url+= '&currency='+currency;
if(product_qty!=null) url += '&product_qty='+product_qty;
if(generic=='n')
{
if (window.XMLHttpRequest)
{
http_request = new XMLHttpRequest();
if (http_request.overrideMimeType) http_request.overrideMimeType('text/xml');
}
else if (window.ActiveXObject)
{
try
{
http_request = new ActiveXObject("Msxml2.XMLHTTP");
}
catch (e)
{
try
{
http_request = new ActiveXObject("Microsoft.XMLHTTP");
}
catch (e) {}
}
}
if (!http_request) {
alert('Giving up :( Cannot create an XMLHTTP instance');
return false;
}
http_request.onreadystatechange = function() { updatePrices(http_request,row); };
http_request.open('GET', url, true);
http_request.send(null);
}
}
function updatePrices(http_request,row)
{
if (http_request.readyState == 4) {
if (http_request.status == 200)
{
var prices=explode(', ',http_request.responseText);
var price_changed=getValue('product_price_changed['+row+']');
if(price_changed=='n')
{
setValue('product_online['+row+']',prices[0]);
setValue('product_offer['+row+']',prices[0]);
setValue('product_offerau['+row+']',prices[1]);
}
productChange(row,'pqty');
}
else alert('There was a problem with the request.');
}
}
function makeProductIndividual(count)
{
var product_id=getValue('product_id['+count+']');
var sub=getValue('sub_id['+count+']');
var war=getValue('product_warranty['+count+']');
var con=getValue('product_condition['+count+']');
var vlt=getValue('product_voltage['+count+']');
var pck=getValue('product_packing['+count+']');
var trm=getValue('product_terms['+count+']');
var cnt=getValue('supplier_country['+count+']');
var url='/webadmin/products/products_add.php?link_id='+product_id;
if(sub!='') url+='&sub_id='+sub;
if(war!=0) url+='&warranty='+war;
if(vlt!=0) url+='&voltage='+vlt;
if(con!=0) url+='&condition='+con;
if(pck!=0) url+='&packing_terms='+pck;
if(trm!=0) url+='&ship_terms='+trm;
if(cnt!='') url+='&location='+cnt;
window.open(url);
}
//]]>
</script>
<?php
$product_affected = array();
foreach ($invoice_products as $product) {
    $product_affected[] = $product['prod_count'];
}

?>
<input type="hidden" name="product_affected" value="<?= implode(',', $product_affected) ?>" />
<?php
$charge_affected = array();
foreach ($invoice_charges as $charge) {
    $charge_affected[] = $charge['charge_count'];
}

?>
<input type="hidden" name="charge_affected" value="<?= implode(',', $charge_affected) ?>" />

<table class="wide">
	<tr>
		<th style="width:32px;">Product ID</th>
		<th style="width:100%;">Product Name</th>
		<th style="width:10px;"></th>
		<th style="width:32px;">Qty</th>
		<th style="width:64px;">Cost Price (<span name="invoice[invoice_currency]"><?php hecho($invoice['invoice_currency'])?></span>)</th>
		<th style="width:64px;">Online Price (<span name="invoice[invoice_currency]"><?php hecho($invoice['invoice_currency'])?></span>)</th>
		<th style="width:64px;">Offer Price (<span name="invoice[invoice_currency]"><?php hecho($invoice['invoice_currency'])?></span>)</th>
		<th style="width:64px;">Offer Price (AUD)</th>
		<th style="width:64px;"></th>
	</tr>

	<tbody id="invoice_products_table">
<?php
foreach ($invoice_products as $key => $product) {
    echo invoice_products_row($key, $product);
}

?>
	</tbody>
	<tbody id="invoice_shipping_table">
<?php
$invoice_shipping = array($invoice);
foreach ($invoice_shipping as $key => $shipping) {
    echo invoice_shipping_row($key, $shipping);
}

?>
	</tbody>
	<tbody id="invoice_charges_table">
<?php
foreach ($invoice_charges as $key => $charge) {
    echo invoice_charges_row($key, $charge);
}

?>
	</tbody>

	<tr class="alt">
		<td class="left_title" colspan="4">TOTAL</td>
		<td><input type="text" size="6" name="invoice[invoice_cost]" readonly="readonly" value="<?= convertCurrency($invoice['invoice_cost'], $invoice['invoice_currency']) ?>" style="text-align:right;" /></td>
		<td><input type="text" size="6" name="invoice[invoice_online]" readonly="readonly" value="<?= convertCurrency($invoice['invoice_online'], $invoice['invoice_online']) ?>" style="text-align:right;" /></td>
		<td><input type="text" size="6" name="invoice[invoice_offer]" readonly="readonly" value="<?= convertCurrency($invoice['invoice_offer'], $invoice['invoice_currency']) ?>" style="text-align:right;" /></td>
		<td><input type="text" size="6" name="invoice[invoice_offerau]" readonly="readonly" value="<?= convertCurrency($invoice['invoice_offerau'], 'AUD') ?>" style="text-align:right;" /></td>
		<td style="font-weight:bold;" name="invoice[invoice_currency]"><?php hecho($invoice['invoice_currency'])?></td>
	</tr>

	<tr>
		<th colspan="9" style="text-align:center;">
			<input onclick="addProduct();" type="button" value="Add Product" />
			<input onclick="openWindow('/webadmin/invoice/addGeneric.php',640,480,true,'scrollbars=1')" type="button" value="Add Generic" />
			<input onclick="openWindow('/webadmin/invoice/addCharge.php?return_message=add',640,480,true,'scrollbars=1')" type="button" value="Add Charge" />
			<input type="hidden" name="products_cost" value="<?= sprintf('%.2f', $invoice['products_cost']) ?>" />
			<input type="hidden" name="products_costau" value="<?= sprintf('%.2f', $invoice['products_costau']) ?>" />
			<input type="hidden" name="products_offer" value="<?= sprintf('%.2f', $invoice['products_offer']) ?>" />
			<input type="hidden" name="products_offerau" value="<?= sprintf('%.2f', $invoice['products_offerau']) ?>" />
			<input type="hidden" name="charges_offer" value="<?= sprintf('%.2f', $invoice['charges_offer']) ?>" />
			<input type="hidden" name="charges_offerau" value="<?= sprintf('%.2f', $invoice['charges_offerau']) ?>" />
			<input type="hidden" name="invoice_profit" value="<?= sprintf('%.2f', $invoice['invoice_profit']) ?>" />
			<input type="hidden" name="invoice_profitau" value="<?= sprintf('%.2f', $invoice['invoice_profitau']) ?>" />
		</th>
	</tr>
</table>
<br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionProducts_hideNote">
All Products requested can be viewed by Clicking "Products Requested" above<br />
</div>
<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionSuppliers\');">Product Suppliers</a>', '<a href="javascript:toggleSection(\'sectionDefSup\');">Default Supplier Values</a>') ?>
<div id="sectionDefSup" style="display:none;">
<table class="wide">
<tr>
<td class="left_title" style="width:25%">Supplier Search :</td>
<td colspan="2" style="width:50%"><input type="hidden" name="default_supplier" value="<?= $invoice['default_supplier'] ?>" /><input style="width:100%" type="text" name="def_sup_name" value="<?php hecho(getSupplierName($invoice['default_supplier']))?>" /></td>
<td style="width:25%"><input type="button" value=" Search " onclick="searchDefaultSupplier(getValue('def_sup_name'))"/></td>
</tr>
<tr>
<td class="left_title" style="width:25%">Warranty :</td>
<td style="width:25%">
<select name="def_warranty"><option value="0">Not Selected</option><?= formatHTMLOptions($warranty_terms, $invoice['def_warranty']) ?></select>
</td>
<td class="left_title" style="width:25%">Voltage :</td>
<td style="width:25%">
<select name="def_voltage"><option value="0">Not Selected</option><?= formatHTMLOptions($voltage_terms, $invoice['def_voltage']) ?></select>
</td>
</tr>
<tr>
<td class="left_title">Plug Type :</td>
<td style="width:25%"><select name="def_plug"><option value="0">Not Selected</option><?= formatHTMLOptions($plugs_list, $invoice['def_plug']) ?></select></td>
<td class="left_title">Condition :</td>
<td><select name="def_condition"><option value="0">Not Selected</option><?= formatHTMLOptions($condition_terms, $invoice['def_condition']) ?></select></td>
</tr>
<tr>
<td class="left_title">Packing :</td>
<td><select name="def_packing"><option value="0">Not Selected</option><?= formatHTMLOptions($packing_terms, $invoice['def_packing']) ?></select></td>
<td class="left_title">Terms :</td>
<td><select name="def_terms"><option value="0">Not Selected</option><?= formatHTMLOptions($shipping_terms, $invoice['def_terms']) ?></select></td>
</tr>
<tr>
<td class="left_title">Currency :</td>
<td><select name="def_currency"><option value="">Not Selected</option><?= formatHTMLOptions(getCurrencyList(), $invoice['def_currency']) ?></select></td>
<td class="left_title">Country :</td>
<td><select name="def_country"><option value="">Not Selected</option><?= formatHTMLOptions(getCountryList(), $invoice['def_country']) ?></select></td>
</tr>
<tr><td colspan="4" class="left_title" style="text-align:center"><input type="button" value=" Update Products with Default Values " onclick="updateAllSuppliers(getValue('def_sup_name'),this)" /></td></tr>
</table>
<br />
</div>
<div id="sectionDefSup_hideNote"></div>
<div id="sectionSuppliers">
<table class="wide">
<tbody id="supplier_table">
<?php
$count = 1;
$alt   = true;
foreach ($invoice_products as $product) {
    echo supplier_row($count++, $product);
}

?>
</tbody>
<tr>
<th style="width:32px;">PID</th>
<th style="width:50%;">Product Name</th>
<th style="width:50%;">Supplier</th>
<th></th>
<th style="width:256px;">Found</th>
<!--<th style="width:128px;">Used</th>-->
<th style="width:32px;">Qty</th>
<th style="width:64px;">Cost</th>
<th style="width:64px;">Cost AUD</th>
</tr>

<tr id="supplier_ship_rowa"<?= (0 == $invoice['ship_id']) ? ' style="display:none;"' : '' ?>>
<td><input type="text" name="supplier_ship_product_id" size="4" value="F<?= str_pad($invoice['ship_id'], 2, '0', STR_PAD_LEFT) ?><?= ('y' == $invoice['ship_insurance']) ? 'I' : 'N' ?>" disabled="disabled" style="text-align:right;" onchange="shipChange('sid');" /></td>
<td><input type="text" name="supplier_ship_product_name" style="width:100%;" value="<?php hecho($invoice['ship_product_name'])?>" onchange="shipChange('sname');" /></td>
<td colspan="2"><input type="hidden" name="ship_supplier_id" value="<?= intval($invoice['ship_supplier_id']) ?>" /><input type="text" name="ship_supplier_name" value="<?php hecho($invoice['ship_supplier_name'])?>" size="12" style="width:100%;" onchange="shipChange('ssupplier');" /></td>
<td></td>
<td><input type="text" name="supplier_ship_qty" size="3" style="text-align:right;" value="1" onchange="shipChange('sqty');" onfocus="this.select();"/></td>
<td><input type="text" size="6" style="text-align:right;" name="supplier_ship_cost" value="<?= sprintf('%.2f', $invoice['ship_cost']) ?>" onchange="shipChange('scost');" onfocus="this.select();"/></td>
<td><input type="text" size="6" style="text-align:right;" readonly="readonly" name="ship_costau" value="<?= sprintf('%.2f', $invoice['ship_costau']) ?>" onchange="shipChange('scostau');" /><input type="hidden" name="ship_insurance" value="<?= $invoice['ship_insurance'] ?>" /></td>
</tr>
<tr id="supplier_ship_rowb"<?= (0 == $invoice['ship_id']) ? ' style="display:none;"' : '' ?>>
<td>Notes</td>
<td colspan="7"><textarea name="ship_supplier_notes" rows="<?= max(3, count(explode("\n", trim($row_data['ship_supplier_notes'])))) ?>" onkeyup="textareaAutoSize(this,1,3);" style="width:100%;"><?php hecho(trim($row_data['ship_supplier_notes']))?></textarea></td>
</tr>
<tr class="alt">
<td colspan="7" style="font-weight:bold;text-align:right;">TOTAL
<input type="text" name="invoice_cost" readonly="readonly" size="10" value="<?= sprintf('%.2f', $invoice['invoice_cost']) ?>" style="text-align:right;" />
<input type="hidden" name="invoice_costau" value="<?= sprintf('%.2f', $invoice['invoice_costau']) ?>" style="text-align:right;" />
</td>
<td style="font-weight:bold;" name="product_currency"><?= $invoice['invoice_currency'] ?></td>
</tr>
<tr class="alt"><td colspan="8"><table style="width:100%;height:100%;border-style:none;">
<tr><td class="left_title"></td><td></td></tr>
<tr>
<td class="left_title" style="width:25%;vertical-align:top;">Notes</td>
<td>
Products highlighted in <span style="color:#FFAA99;">red</span> have no suppliers in the database<br />
Products highlighted in <span style="color:#6688BB;">blue</span> are available from two or more suppliers<br />
Products highlighted in <span style="color:#8B00FF;">violet</span> have unknown weights<br />
</td>
</tr>
<tr><td class="left_title"></td><td></td></tr>
<?php
if ('y' == $invoice['backorder_flag']) {
?>
<tr><td class="left_title" colspan="2" style="text-align:center;color:red;padding:6px;">Invoice Created as a Backorder, use box below to indicate when new stock arrives.<br />(Once new stock is received, the customer can use the re-checkout feature online)</td></tr>
<tr>
<td class="left_title">New Stock Received :</td>
<td><select name="stock_received" style="width:100%"><?= formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $invoice['stock_received']) ?></select></td>
</tr>
<?php
}
?>
<tr>
<td class="left_title">Packing Terms</td>
<td><select name="pack_requirement_id" style="width:80%;">
<option value="0">Not Selected</option>
<?= formatHTMLOptions(invoiceGetOptionList('packing_requirement'), $invoice['pack_requirement_id']) ?>
</select>
<a href="invoice_options.php?section=pack_requirement" target="_blank" class="addedit">Add / Edit</a>
</td>
</tr>
<tr>
<td class="left_title">Shipping Terms</td>
<td><select name="terms_id" style="width:80%;">
<option value="0">Not Selected</option>
<?= formatHTMLOptions(invoiceGetOptionList('terms_id'), $invoice['terms_id']) ?>
</select>
<a href="invoice_options.php?section=ship_terms" target="_blank" class="addedit">Add / Edit</a>
</td>
</tr>
<tr>
<td class="left_title">Ships From</td>
<td><select name="supply_country">
<?= formatHTMLOptions(array('' => '') + invoiceGetOptionList('supply_country'), $invoice['supply_country']) ?>
</select></td>
</tr>
<tr>
<td class="left_title">Products Located</td>
<?php
$colour = 'y' == $invoice['products_located'] ? '#00FF00' : '#FF0000'
?>
<td><select name="products_located" style="background-color:<?= $colour ?>;" onchange="updateSelect(this);"><?= formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $invoice['products_located']) ?></select></td>
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
<td><i><?= $staff ?> - <?= date('d/m/y h:i', $invoice['products_locate_time']) ?></i></td>
</tr>
<?php
}
?>
</table></td></tr>
</table>
<br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionSuppliers_hideNote">
All Product Suppliers can be viewed by Clicking "Products Suppliers" above<br />
</div>
<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionOSummary\');">Order Summary</a>', '<small><i><a href="#" onclick="toggleSection(\'orderTable\',true);return false;" style="color:black">Expand</a> / <a href="#" onclick="toggleSection(\'orderTable\',false);return false;" style="color:black">Collapse</a></i></small>') ?>
<div id="sectionOSummary">
<table class="wide" id="orderTable" name="orderTable" style="display:none">
<tr>
<td style="text-align:center" colspan="2" class="left_title">Customer Needs</td>
<td style="text-align:center" colspan="2" class="left_title">Current Supply</td>
<td class="left_title"></td>
</tr>
<tr>
<td style="width:25%" class="left_title">Warranty :</td>
<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($warranty_terms, $owarranty) ?></select></td>
<td style="width:25%" class="left_title">Warranty :</td>
<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($warranty_terms, $invoice['def_warranty']) ?></select></td>
<td class="left_title"><img src="/webadmin/images/<?= ($invoice['def_warranty'] == $owarranty && $owarranty > 0) ? 'tick' : 'cross' ?>.gif" alt="<?= ($invoice['def_warranty'] == $owarranty && $owarranty > 0) ? 'tick' : 'cross' ?>" /></td>
</tr>
<tr>
<td style="width:25%" class="left_title">Condition :</td>
<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($condition_terms, $ocondition) ?></select></td>
<td style="width:25%" class="left_title">Condition :</td>
<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($condition_terms, $invoice['def_condition']) ?></select></td>
<td class="left_title"><img src="/webadmin/images/<?= ($invoice['def_condition'] == $ocondition && $ocondition > 0) ? 'tick' : 'cross' ?>.gif" alt="<?= ($invoice['def_condition'] == $ocondition && $ocondition > 0) ? 'tick' : 'cross' ?>" /></td>
</tr>
<tr>
<td style="width:25%" class="left_title">Voltage :</td>
<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($voltage_terms, $ovoltage) ?></select></td>
<td style="width:25%" class="left_title">Voltage :</td>
<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($voltage_terms, $invoice['def_voltage']) ?></select></td>
<td class="left_title"><img src="/webadmin/images/<?= ($invoice['def_voltage'] == $ovoltage && $ovoltage > 0) ? 'tick' : 'cross' ?>.gif" alt="<?= ($invoice['def_voltage'] == $ovoltage && $ovoltage > 0) ? 'tick' : 'cross' ?>" /></td>
</tr>
<tr>
<td style="width:25%" class="left_title">Plug Type :</td>
<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($plugs_list, $oplug) ?></select></td>
<td style="width:25%" class="left_title">Plug Type :</td>
<td style="width:25%"><select><option value="0">Not Selected</option><?= formatHTMLOptions($plugs_list, $invoice['def_plug']) ?></select></td>
<td class="left_title"><img src="/webadmin/images/<?= ($invoice['def_plug'] == $oplug && $oplug > 0) ? 'tick' : 'cross' ?>.gif" alt="<?= ($invoice['def_plug'] == $oplug && $oplug > 0) ? 'tick' : 'cross' ?>" /></td>
</tr>
<tr>
<td class="left_title">General Supply Notes<br /><span style="font-size:9px;color:red;font-weight:normal;">Not Displayed To Customer</span></td>
<td colspan="4"><textarea name="supply_notes" rows="<?= max(5, count(explode("\n", $invoice['supply_notes']))) ?>" onkeyup="textareaAutoSize(this,1,5);"><?php hecho($invoice['supply_notes'])?></textarea></td>
</tr>
<tr>
<td class="left_title">Authorize this supply as found :</td>
<td colspan="4"><select name="supply_located"><?= formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $invoice['supply_located']) ?></select></td>
</tr>
<?php
if ('y' == $invoice['supply_located']) {
    $staff = 'Customer';
    if ($invoice['supply_staff_id'] > 0) {
        $staff = $all_staff_list[$invoice['supply_staff_id']];
    }

?>
<tr style="color: red;font-weight:bold">
<td class="left_title">Authorised By :</td>
<td colspan="4"><i><?= $staff ?> - <?= date('d/m/y h:i', $invoice['supply_locate_time']) ?></i></td>
</tr>
<?php
}
?>
</table>
<table class="wide">
<tr>
<td style="width:25%" class="left_title"></td>
<td>
<input type="button" value="Preview Summary
Purchase Order" onclick="actionPurchasePreview(this.form);" />
<?php
if (count($invoice_porders) == 0) {
?>
<input type="button" value="Make Purchase
Order(s)" onclick="actionPurchaseOrder(this.form);" />
<?php
}
?>
</td>
</tr>
</table>
<span name="orderTable_hideNote"></span>
<br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionOSummary_hideNote">
The Order Summary can be viewed by Clicking "Order Summary" above<br />
</div>

<!-- /product_supplier_new.tpl -->

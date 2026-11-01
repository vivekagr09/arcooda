<!-- product_supplier.tpl -->
<style>
    .invoice_generic{
        background-color: #ffccf6de!important;
    }
</style>
<?php
moduleLoad('invoice');
moduleLoad('product');

$freightiption='';
$freightdata=getFreightList();
foreach ($freightdata as $key => $value) {
    $freid=$value["freight_id"];
    $selected=($freid==$invoice['supplier_product_courior'])?"selected":"";
    $accountid=$value['account_number'];
    $freightiption .= '<option value="'.$freid.'" '.$selected.' data-account="'.$accountid.'">'.$value["freight_name"].'</option>';
}
$apply_rounding = !in_array($_GLOBALS['site_settings']['site_id'], array(3, 20, 26));
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
    if (0 == $defSup && $product['product_supplier_id'] > 0) {
        $defSup = $product['product_supplier_id'];
    }
}
$supValues       = getSupplierValues($defSup);
$warranty_terms  = array('-1' => 'Not Applicable') + invoiceGetOptionList('warranty_terms_id');
$condition_terms = array('-1' => 'Not Applicable') + invoiceGetOptionList('condition');
$packing_terms   = array('-1' => 'Not Applicable') + invoiceGetOptionList('packing_requirement');
$voltage_terms   = array('-1' => 'Not Applicable') + invoiceGetOptionList('voltage_id2');
$shipping_terms  = array('-1' => 'Not Applicable') + invoiceGetOptionList('terms_id');
$plugs_list      = array('-1' => 'Not Applicable') + invoiceGetOptionList('plug_id');
function product_row($pcount = '[PRODUCT_COUNT]', $product = array(), $generic = 0)
{
    global $invoice, $_GLOBALS, $show_usd;
    $inv = $invoice['invoice_currency'];
    if ('' == $inv) {
        $inv = 'USD';
    }
    if (!intval($product['decimal'])) {
        $product['decimal'] = 2;
    }
    $hidden = array(
        'decimal[' . $pcount . ']'               => $product['decimal'],
        'generic[' . $pcount . ']'               => $product['generic'],
        'generic_code[' . $pcount . ']'          => $product['generic_code'],
        'product_supplier_id[' . $pcount . ']'               => $product['product_supplier_id'],
        'colour_id[' . $pcount . ']'             => $product['colour_id'],
        'prod_count[' . $pcount . ']'            => $product['prod_count'],
        'product_price_changed[' . $pcount . ']' => ('' != $product['product_price_changed']) ? $product['product_price_changed'] : 'n',
        'product_rank[' . $pcount . ']'          => $product['product_rank'],
        'product_section[' . $pcount . ']'       => $product['product_section'],
        'product_updated[' . $pcount . ']'       => $product['product_updated'],
        'product_weight[' . $pcount . ']'        => $product['product_weight'],
        'product_weight_inc[' . $pcount . ']'    => $product['product_weight_inc'],
        'color_id[' . $pcount . ']'        => $product['color_id'],
        'supplier_currency[' . $pcount . ']'        => $inv,
    );
    ob_start();
?>
<tr id="product_rowa_th[<?=$pcount?>]" style="line-height: 20px; font-size: 14px;">
       <th style="width:10%">Supplier Id</th>
        <th>&nbsp;</th>
        <th style="width:50%">Supplier Product Name</th>
        <th>&nbsp;</th>
        <th style="width:10%">Found</th>
        <th style="width:10%">Qty.</th>
        <th style="width:10%">Cost Price (<span name="product_currency"><?php hecho($invoice['invoice_currency'])?></span>)</th>
        
        <th ><!-- Offer Price (<span name="product_currency"><?php hecho($invoice['invoice_currency'])?></span>) --></th>
        <th style="width:10%">Offer <?=($show_usd) ? 'USD' : 'AUD'?></th>
        <th style="width:10%">&nbsp;</th>
    </tr>
<tr id="product_rowa[<?=$pcount?>]">
    <td>
        <?=formatHTMLInputs($hidden)?>
        <input name="sup_code[<?=$pcount?>]" onchange="productChange(<?=$pcount?>, 'pid')" type="text" value="<?php hecho($product['sup_code'])?>" style="text-align:right" size="10" maxlength="15" />

        <input name="sub_id[<?=$pcount?>]" onchange="productChange(<?=$pcount?>, 'pid')" type="hidden" value="<?php hecho($product['sub_id'])?>" style="text-align:right" size="4" maxlength="4" />
    </td>
    <td>
<?php
if ('y' != $product['generic']) {
?>
	<input onclick="searchSupplierById(<?=$pcount?>, getValue('sup_code[<?=$pcount?>]'),'search_id')" type="button" value="S" />
<?php
}
?>
    </td>
    <td><input class="invoice_product_name" name="product_name[<?=$pcount?>]" onchange="productChange(<?=$pcount?>, 'pname')" type="text" value="<?php hecho($product['product_name'])?>" style="width:100%" /></td>
    <td>
<?php
if ('y' != $product['generic']) {
?>
        <input onclick="searchSupplierById(<?=$pcount?>, getValue('product_name[<?=$pcount?>]'),'for')" type="button" value="S" />
<?php
}
?>
    </td>
    <td>
        <select name="product_found[<?=$pcount?>]">
            <option value="0">No</option>
            <option valuse="1">Yes</option>
        </select>
    </td>
    <td><input name="product_qty[<?=$pcount?>]" onchange="updateProductPrice(<?=$pcount?>)" type="text" value="<?php hecho($product['product_qty'])?>" style="text-align:right" size="10" onfocus="this.select()" /></td>
    <td><input name="product_cost[<?=$pcount?>]" onchange="productChange(<?=$pcount?>, 'pcost',0)" type="text" value="<?php hecho(convertCurrency($product['product_cost'], $inv, '', false, $product['decimal']))?>" style="text-align:right" size="10" onfocus="this.select()" /></td>
    
    <td><!-- <input name="product_offer[<?=$pcount?>]" onchange="productChange(<?=$pcount?>, 'poffer')" type="text" value="<?php hecho(convertCurrency($product['product_offer'], $inv, '', false, $product['decimal']))?>" style="text-align:right" size="10" onfocus="this.select()" /> --></td>
    <td><input name="product_offerau[<?=$pcount?>]" onchange="productChange(<?=$pcount?>, 'pofferau')" type="text" value="<?php hecho(convertCurrency($product['product_offerau'], 'AUD', '', false, $product['decimal']))?>" style="text-align:right;<?=($show_usd) ? 'display:none;' : ''?>" size="10" readonly="readonly" /><input name="product_offerus[<?=$pcount?>]" onchange="productChange(<?=$pcount?>, 'pofferus',0)" type="text" value="<?php hecho(convertCurrency($product['product_offerus'], 'USD', '', false, $product['decimal']))?>" style="text-align:right;<?=($show_usd) ? '' : 'display:none;'?>" size="10" readonly="readonly" /></td>
    <td><input onclick="deleteProduct(<?=$pcount?>)" type="button" value="Delete" /></td>
</tr>
<tr class="alt" id="product_rowb[<?=$pcount?>]">
    <td><a href="javascript:void(rankProduct(<?=$pcount?>, -1))"><img alt="/\" src="/webadmin/images/up_arrow.gif" border="0" /></a><a href="javascript:void(rankProduct(<?=$pcount?>, 1))"><img alt="\/" src="/webadmin/images/down_arrow.gif" border="0" /></a><br /><br />Notes</td>
    <td colspan="9"><textarea cols="80" name="product_notes[<?=$pcount?>]" rows="<?=max(3, count(explode("\n", trim($product['product_notes']))))?>" onkeyup="textareaAutoSize(this, 1, 3)" onchange="setValue('product_updated[<?=$pcount?>]', 'y')" style="width:100%"><?php hecho(trim($product['product_notes']))?></textarea></td>
</tr>
<?php
return ob_get_clean();
}
function product_row_generic($pcount = '[PRODUCT_COUNT]', $product = array(), $generic = false,$delete=false,$search=true)
{
    global $invoice, $warranty_terms, $condition_terms, $packing_terms, $voltage_terms, $shipping_terms, $plugs_list, $products_need_update, $site_id, $_GLOBALS, $show_usd;
    $inv = $invoice['invoice_currency'];
    if ('' == $inv) {
        $inv = 'USD';
    }
    if (!intval($product['decimal'])) {
        $product['decimal'] = 2;
    }
    $hidden = array(
        'decimal[' . $pcount . ']'               => $product['decimal'],
        'generic[' . $pcount . ']'               => ($generic)?'y':'n',
        'generic_code[' . $pcount . ']'          => $product['generic_code'],
        'colour_id[' . $pcount . ']'             => $product['colour_id'],
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
<?php 
$rowthid=($delete)?"product_rowa_th[".$pcount."]":"supplier_rowa_th[".$pcount."]";
$rowid=($delete)?"product_rowa[".$pcount."]":"supplier_rowa_inner[".$pcount."]";
?>
<tr id="<?=$rowthid ?>" class="invoice_generic"> <th class="invoice_generic" style="width:10%">Our Id </th>
    <th class="invoice_generic"></th>
    <th class="invoice_generic" style="width:50%">Our Product Name</th>
    <th class="invoice_generic"></th>
    <th class="invoice_generic" style="width:10%"><?=($generic)?'Qty':'Previous cost ('.$invoice["invoice_currency"].')' ?></th>
    <th class="invoice_generic" style="width:10%"><?=($generic)?'Cost Price ('.$invoice["invoice_currency"].')':'Previous cost (USD)'?></th>
    <th class="invoice_generic" style="width:10%"><?=($generic)?'Cost Price (USD)':'Last Date'?></th>
    <?php if($generic){ 
        echo '<th class="invoice_generic" style="width:10%"></th>';
    }else{ ?>
        <th class="invoice_generic" style="width:10%">Online Sales Price (USD) (<span name="product_currency"><?php hecho($invoice['invoice_currency'])?></span>)</th>
        
    <?php }?>
    <th class="invoice_generic" style="width:10%"></th>
    <th class="invoice_generic" style="width:10%"></th>
</tr>
<tr id="<?=$rowid?>">
    <td>
        <?=formatHTMLInputs($hidden)?>
        <input name="product_id[<?=$pcount?>]" onchange="productChange(<?=$pcount?>, 'pid')" type="text"  style="text-align:right" size="8" maxlength="15" value="<?php echo ($delete)? 'GME':hecho($product['product_id']);?>"/>
        <span id="color_id_sym[<?=$pcount?>]">
            <?php if($product['color_id']){ 
                $color_data=productGenericFromDatabase($product['color_id'],'product_colour') ;
            ?>
            <span onmouseover="<?php hecho("tt_in(" . jecho($color_data['colour'], 1) . ")") ?>" onmouseout="tt_out()" style="height:12px;width: 15px; border-radius: 50%; display: inline-block; text-align: center; padding-top: 3px; margin-right: 4px!important;background-color: <?php echo $color_data['color_val'];?>"></span>
            <?php } ?>
        </span>
    </td>
    <td>
        <?php
        if ($search) {
        ?>
                <input onclick="searchProductById(<?=$pcount?>, getValue('product_id[<?=$pcount?>]'))" type="button" value="S" />
        <?php
        }
        ?>
        </td>
    <td>
        <input class="invoice_product_name" name="supplier_productname[<?=$pcount?>]" onchange="productChange(<?=$pcount?>, 'pname')" type="text" value="<?php hecho($product['supplier_productname'])?>" style="width:98%" />
    </td>
    <td>
        <?php
        if ($search) {
        ?>
          
        <input onclick="searchProduct(<?=$pcount?>, getValue('supplier_productname[<?=$pcount?>]'))" type="button" value="S" /></td>
        <?php
        }
        ?>
    <?php  if($generic){ ?>
     <td><input name="product_qty[<?=$pcount?>]" onchange="updateProductPrice(<?=$pcount?>)" type="text" value="<?php hecho($product['product_qty'])?>" style="text-align:right" size="10" onfocus="this.select()" /></td>
     <?php }else { ?>
        <td><input name="supplier_product_cost[<?=$pcount?>]" type="text" value="<?php hecho($product['supplier_product_cost'])?>" style="text-align:right" size="10" onfocus="this.select()"/></td>
    <?php } ?>
    <?php  if($generic){ ?>
        <td><input name="product_cost[<?=$pcount?>]" onchange="productChange(<?=$pcount?>, 'pcost',<?= $generic ?>)" type="text" value="<?php hecho(convertCurrency($product['product_cost'], $inv, '', false, $product['decimal']))?>" style="text-align:right" size="10" onfocus="this.select()" /></td>
    <?php }else { ?> 
        <td><input name="supplier_product_cost_usd[<?=$pcount?>]" onchange="productChange(<?=$pcount?>, 'supplier_product_cost_usd')" type="text" value="<?php hecho(convertCurrency($product['supplier_product_cost_usd'], $inv, '', false, $product['decimal']))?>" style="text-align:right" size="10" onfocus="this.select()" /></td>
    <?php } ?>  
    <?php  if($generic){ ?>
         <td><input name="supplier_product_cost_usd[<?=$pcount?>]" type="text" value="<?php hecho($product['supplier_product_cost_usd'])?>" onchange="productChange(<?=$pcount?>, 'supplier_product_cost_usd',<?= $generic ?>)" style="text-align:right" size="10" onfocus="this.select()"/></td>
        
    <?php }else { ?>
        <td><input name="supplier_product_lastdate[<?=$pcount?>]"  type="text" value="<?php hecho($product['supplier_product_lastdate'])?>" style="text-align:right" size="10" onfocus="this.select()" /></td>
    <?php } ?>    
    <?php  if(!$generic){ ?>
    <td><input name="supplier_product_offer[<?=$pcount?>]" onchange="productChange(<?=$pcount?>, 'supplier_product_offer')" type="text" value="<?php hecho(convertCurrency($product['supplier_product_offer'], $inv, '', false, $product['decimal']))?>" style="text-align:right" size="10" onfocus="this.select()" /></td>
    <?php } ?> 
    <td></td>
    <td>
        <?php
         if($delete) {
        ?>
                <input onclick="deleteProduct(<?=$pcount?>)" type="button" value="Delete" />
        <?php
        }
        ?>
       </td>
</tr>
<tr class="alt invoice_generic" id="supplier_rowb[<?=$pcount?>]">
    <td>
        <?php
         if($delete) {
        ?>
        <a href="javascript:void(rankProduct(<?=$pcount?>, -1))"><img alt="/\" src="/webadmin/images/up_arrow.gif" border="0" /></a><a href="javascript:void(rankProduct(<?=$pcount?>, 1))"><img alt="\/" src="/webadmin/images/down_arrow.gif" border="0" /></a>
        <?php
        }
        ?>
        <br /><br />

    Notes</td>
    <td colspan="9"><textarea cols="80" name="supplier_product_notes[<?=$pcount?>]" rows="<?=max(3, count(explode("\n", trim($product['supplier_product_notes']))))?>" onkeyup="textareaAutoSize(this, 1, 3)" onchange="setValue('product_updated[<?=$pcount?>]', 'y')" style="width:100%"><?php hecho(trim($product['supplier_product_notes']))?></textarea></td>
</tr>
<tr class="alt invoice_generic" id="supplier_rowc[<?=$pcount?>]">
    <td></td>
    <td colspan="9"><input type="checkbox" name="to_productid[<?=$pcount?>]" <?php echo  ($product['to_productid'])?'checked':''; ?> value="yes"> Link supplier details to our Product Id</td>
</tr>
<tr class="alt" id="supplier_rowd[<?=$pcount?>]">
    <td colspan="2" class="left_title">Warranty :</td>
    <td colspan="2"><select name="product_warranty[<?=$pcount?>]" onchange="updateProductNotes('<?=$pcount?>');productChange(<?=$pcount?>)"><option value="0">Not Selected</option><?=str_replace("\n", "", formatHTMLOptions($warranty_terms, $product['product_warranty']))?></select></td>
    <td colspan="1" class="left_title">Voltage :</td>
    <td colspan="5"><select name="product_voltage[<?=$pcount?>]" onchange="updateProductNotes('<?=$pcount?>');productChange(<?=$pcount?>)"><option value="0">Not Selected</option><?=str_replace("\n", "", formatHTMLOptions($voltage_terms, $product['product_voltage']))?></select></td>
</tr>
<tr class="alt" id="supplier_rowe[<?=$pcount?>]">
    <td colspan="2" class="left_title">Plug Type :</td>
    <td colspan="2"><select name="product_plug[<?=$pcount?>]" onchange="updateProductNotes('<?=$pcount?>');productChange(<?=$pcount?>)"><option value="0">Not Selected</option><?=str_replace("\n", "", formatHTMLOptions($plugs_list, $product['product_plug']))?></select></td>
    <td colspan="1" class="left_title" style="white-space:nowrap">Condition :</td>
    <td colspan="5"><select name="product_condition[<?=$pcount?>]" onchange="updateProductNotes('<?=$pcount?>');productChange(<?=$pcount?>)"><option value="0">Not Selected</option><?=str_replace("\n", "", formatHTMLOptions($condition_terms, $product['product_condition']))?></select></td>
</tr>
<tr class="alt" id="supplier_rowf[<?=$pcount?>]">
    <td colspan="2" class="left_title">Packing :</td>
    <td colspan="2"><select name="product_packing[<?=$pcount?>]" onchange="productChange(<?=$pcount?>)"><option value="0">Not Selected</option><?=str_replace("\n", "", formatHTMLOptions($packing_terms, $product['product_packing']))?></select></td>
    <td colspan="1" class="left_title">Terms :</td>
    <td colspan="5"><select name="product_terms[<?=$pcount?>]" onchange="productChange(<?=$pcount?>)"><option value="0">Not Selected</option><?=str_replace("\n", "", formatHTMLOptions($shipping_terms, $product['product_terms']))?></select></td>
</tr>
<tr class="alt" id="supplier_rowg[<?=$pcount?>]">
    <td colspan="2" class="left_title">Ships From :</td>
    <td colspan="2"><select name="supplier_ships[<?=$pcount?>]" onchange="productChange(<?=$pcount?>, 'ocost')"><option value="">Not Selected</option><?=str_replace("\n", "", formatHTMLOptions(getCurrencyList(), $product['supplier_currency']))?></select></td>
    <td colspan="1" class="left_title"></td>
    <td colspan="5"></td>
</tr>
<tr class="alt" id="supplier_rowh[<?=$pcount?>]">
    <td colspan="10">
        <table class="wide" style="border: 0px;margin-top: 10px;">
            <tr class="alt">
                <td style="width:15%">
                    <h4 style="padding: 5px;">Image Refrences</h4>
                    <input onclick="addgenericimage(<?=$pcount?>,1);return false;" type="button" value="Add Image">
                </td>
                <td style="width:85%;">
                    <table class="wide" style="border: 0px;margin-top: 10px;">
                        <tr class="alt" id="supplier_generic_product_image_<?=$pcount ?>">
                           <?php 
                            $proimages=purchaseOrderimagesFromDatabase($product['order_id'],1,$product['id']);
                            foreach ($proimages as $key => $images) { ?>
                                <td style="width:20%" id="generic_product_attach_id_<?=$images['id']?>"> 
                                    <table class="wide" style="border: 0px;margin-top: 10px;"> 
                                        <tr class="alt"> <td colspan="2"> 
                                            <img src="addGenericAttachment.php?action=view&id=<?=$images['id']?>" width="100px;"></td> </tr> <tr class="alt"> <td class="right_title" style="width: 150px;"><?=$images['caption']?></td> <td><img src="/svg/remove.png" style="float:left;" onclick="deleteGenericImage(<?=$images['id']?>);"></td> </tr> <tr class="alt"> <td colspan="2"> <div><input type="hidden" name="product_image[<?=$pcount ?>][<?=$images['id']?>][id]" value="<?=$images['id']?>"><input type="hidden" name="product_image[<?=$pcount ?>][<?=$images['id']?>][type]" value="<?=$images['image_type']?>"><input type="checkbox" name="product_image[<?=$pcount ?>][<?=$images['id']?>][purchase_order_inc]" value="1" <?php echo ($images['purchase_order_inc']==1)?'checked':'';?>> Include In Purchase Order</div> <div><input type="checkbox" name="product_image[<?=$pcount ?>][<?=$images['id']?>][email_attachment]" value="1" <?php echo ($images['email_attachment']==1)?'checked':'';?> > Email Attachment</div> </td> </tr> </table> 
                                    </td>
                           <?php } ?>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
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
    <td><input name="charge_qty[<?=$ccount?>]" readonly="readonly" onchange="chargeChange(<?=$ccount?>, 'pqty')" size="3" style="text-align:right" type="text" value="<?=(0 != $charge['charge_qty']) ? $charge['charge_qty'] : 1?>" onfocus="this.select()"/></td>
    <td><input name="charge_cost[<?=$ccount?>]" onchange="chargeChange(<?=$ccount?>, 'pcost')" size="6" style="text-align:right" type="text" value="<?=$charge['charge_cost']?>" onfocus="this.select()"/></td>
    <td><input name="charge_online[<?=$ccount?>]" onchange="chargeChange(<?=$ccount?>, 'ponline')" size="6" style="text-align:right" type="text" value="<?=$charge['charge_online']?>" onfocus="this.select()"/></td>
    <td><input name="charge_offer[<?=$ccount?>]" onchange="chargeChange(<?=$ccount?>, 'poffer')" size="6" style="text-align:right" type="text" value="<?=$charge['charge_offer']?>" onfocus="this.select()"/></td>
    <td><input name="charge_offerau[<?=$ccount?>]" onchange="chargeChange(<?=$ccount?>, 'pofferau')" readonly="readonly" size="6" style="text-align:right;<?=($show_usd) ? 'display:none;' : ''?>" type="text" value="<?=$charge['charge_offerau']?>" /><input name="charge_offerus[<?=$ccount?>]" onchange="chargeChange(<?=$ccount?>, 'pofferus')" readonly="readonly" size="6" style="text-align:right;<?=($show_usd) ? '' : 'display:none;'?>" type="text" value="<?=$charge['charge_offerus']?>" /></td>
    <td><input type="hidden" name="charge_count[<?=$ccount?>]" value="<?=$charge['charge_count']?>" /><input onclick="deleteCharge(<?=$ccount?>)" type="button" value="Delete" /></td>
</tr>
<tr class="alt" id="product_charge_rowb[<?=$ccount?>]">
    <td><a href="javascript:void(rankCharge(<?=$ccount?>, -1))"><img src="/webadmin/images/up_arrow.gif" border="0" /></a><a href="javascript:void(rankCharge(<?=$ccount?>, 1))"><img src="/webadmin/images/down_arrow.gif" border="0" /></a><br /><br />Notes</td>
    <td colspan="9"><textarea name="charge_notes[<?=$ccount?>]" rows="<?=max(3, count(explode("\n", trim($charge['charge_notes']))))?>" onkeyup="textareaAutoSize(this, 1, 3)" onchange="setValue('charge_updated[<?=$ccount?>]', 'y')" style="width:100%"><?php hecho(trim($charge['charge_notes']))?></textarea></td>
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
    <th>Cost <?=($show_usd) ? 'USD' : 'AUD'?></th>
</tr>
<tr id="supplier_rowb[<?=$pcount?>]"<?=('' != $bgcolor) ? (' style="background-color:' . $bgcolor . '"') : ''?>>
    <td><input disabled="disabled" name="supplier_product_id[<?=$pcount?>]" size="4" style="text-align:right" type="text" value="<?=$product['product_id']?>" onchange="productChange(<?=$pcount?>, 'sid')" /></td>
    <td><input name="supplier_product_name[<?=$pcount?>]" disabled="disabled" size="12" style="width:100%;overflow:hidden" type="text" value="<?php hecho($product['product_name'])?>" /></td>
    <td><input name="supplier_name[<?=$pcount?>]" size="12" style="width:100%" type="text" value="<?php hecho($sup_name)?>" /></td>
    <td><input onclick="searchSupplier(<?=$pcount?>, getValue('product_id[<?=$pcount?>]'))" type="button" value="S" /></td>
    <td><select name="supplier_found[<?=$pcount?>]" onchange="productChange(<?=$pcount?>, 'sfound')"><?=str_replace("\n", "", formatHTMLOptions($stock, $product['supplier_found']))?></select></td>
    <td><input name="supplier_product_qty[<?=$pcount?>]" disabled="disabled" size="3" style="text-align:right" type="text" value="<?=$product['product_qty']?>" onfocus="this.select()"/></td>
    <td><input name="supplier_cost[<?=$pcount?>]" size="6" style="text-align:right" type="text" value="<?=$product['supplier_cost']?>" onfocus="this.select()" onchange="productChange(<?=$pcount?>, 'ocost')" /></td>
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
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionProducts\')">Products Requested</a>', '<a style="color:black;margin-right:8px;" href="#" onclick="addProduct();return false;">Add Product</a><a style="color:black;" href="javascript:void(0)" onclick="addGeneric()"return false;">Add Generic</a>')?> 
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
    
    $("#product_table").append(<?=str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(product_row_generic('[PRODUCT_COUNT]', array('product_rank' => '[PRODUCT_COUNT]', 'product_qty' => '1', 'generic' => 'y'),0,0,1), 1))?>);
    /*$("#supplier_table").append(<?=str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(supplier_row('[PRODUCT_COUNT]', array('generic' => 'y')), 1))?>);*/
    product_count++;
    $(".total_invoice_total").css("display", "");
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
    $("#product_table").append(<?=str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(product_row_generic('[PRODUCT_COUNT]', array('product_rank' => '[PRODUCT_COUNT]', 'product_qty' => '1', 'generic' => 'y'),1,1,0), 1))?>);
    /*$("#supplier_table").append(<?=str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(supplier_row('[PRODUCT_COUNT]', array('generic' => 'y')), 1))?>);*/
    product_count++;
    $(".total_invoice_total").css("display", "");
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
    var theRowth = document.getElementById("product_rowa_th[" + row + "]");
    theRowth.parentNode.removeChild(theRowth);
    var suprow= document.getElementById("supplier_rowa_th[" + row + "]");
    if(suprow){
        suprow.parentNode.removeChild(suprow);
    }
    var suprowinner= document.getElementById("supplier_rowa_inner[" + row + "]");
    if(suprowinner){
        suprowinner.parentNode.removeChild(suprowinner);
    }
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
function setInvoiceImagedata(data){
    if(data.type==1){
    $('#supplier_generic_product_image_'+data.row_id+'').append('<td style="width:20%" id="generic_product_attach_id_'+data.id+'"> <table class="wide" style="border: 0px;margin-top: 10px;"> <tr class="alt"> <td colspan="2"> <img src="addGenericAttachment.php?action=view&id='+data.id+'" width="100px;"></td> </tr> <tr class="alt"> <td class="right_title" style="width: 150px;">'+data.caption+'</td> <td><img src="/svg/remove.png" style="float:left;" onclick="deleteGenericImage('+data.id+');"></td> </tr> <tr class="alt"> <td colspan="2"> <div><input type="hidden" name="product_image['+data.row_id+']['+data.id+'][id]" value="'+data.id+'"><input type="hidden" name="product_image['+data.row_id+']['+data.id+'][type]" value="1"><input type="checkbox" name="product_image['+data.row_id+']['+data.id+'][purchase_order_inc]" value="1"> Include In Purchase Order</div> <div><input type="checkbox" name="product_image['+data.row_id+']['+data.id+'][email_attachment]" value="1"> Email Attachment</div> </td> </tr> </table> </td>'); 
        }else if(data.type==2){
        $('#supplier_order_information_attachment').append('<td style="width:20%" id="generic_product_attach_id_'+data.id+'"> <table class="wide" style="border: 0px;margin-top: 10px;"> <tr class="alt"> <td colspan="2"><img src="addGenericAttachment.php?action=view&id='+data.id+'" width="100px;"></td> </tr> <tr class="alt"> <td class="right_title" style="width: 150px;">'+data.caption+'</td> <td><img src="/svg/remove.png" style="float:left;" onclick="deleteGenericImage('+data.id+');"></td> </tr> <tr class="alt"> <td colspan="2"> <div><input type="hidden" name="order_image['+data.id+'][id]" value="'+data.id+'"><input type="hidden" name="order_image['+data.id+'][type]" value="2"><input type="checkbox" name="order_image['+data.id+'][email_attachment]" value="1"> Email Attachment</div> </td> </tr> </table> </td>');
    }else if(data.type==3){
        $('#supplier_payment_information_attachment').append('<td style="width:20%" id="generic_product_attach_id_'+data.id+'"> <table class="wide" style="border: 0px;margin-top: 10px;"> <tr class="alt"> <td colspan="2"><img src="addGenericAttachment.php?action=view&id='+data.id+'" width="100px;"></td> </tr> <tr class="alt"> <td class="right_title" style="width: 150px;">'+data.caption+'</td> <td><img src="/svg/remove.png" style="float:left;" onclick="deleteGenericImage('+data.id+');"></td> </tr> <tr class="alt"> <td colspan="2"> <div><input type="hidden" name="payment_image['+data.id+'][id]" value="'+data.id+'"><input type="hidden" name="payment_image['+data.id+'][type]" value="3"><input type="checkbox" name="payment_image['+data.id+'][purchase_order_inc]" value="1"> Include In Purchase Order</div> <div><input type="checkbox" name="payment_image['+data.id+'][email_attachment]" value="1"> Email Attachment</div> </td> </tr> </table> </td>');
    }else if(data.type==4){
        $('#supplier_generic_product_image_received_'+data.row_id+'').append('<td style="width:20%" id="generic_product_attach_id_'+data.id+'"> <table class="wide" style="border: 0px;margin-top: 10px;"> <tr class="alt"> <td colspan="2"> <img src="addGenericAttachment.php?action=view&id='+data.id+'" width="100px;"></td> </tr> <tr class="alt"> <td class="right_title" style="width: 150px;">'+data.caption+'</td> <td><img src="/svg/remove.png" style="float:left;" onclick="deleteGenericImage('+data.id+');"></td> </tr> <tr class="alt"> <td colspan="2"> <div><input type="hidden" name="product_image_received['+data.row_id+']['+data.id+'][id]" value="'+data.id+'"><input type="hidden" name="product_image_received['+data.row_id+']['+data.id+'][type]" value="4"><input type="checkbox" name="product_image_received['+data.row_id+']['+data.id+'][sales_invoice_received]" value="y"> Share with sales invoice</div> <div><input type="checkbox" name="product_image_received['+data.row_id+']['+data.id+'][email_attachment_received]" value="y"> Email Attachment</div> </td> </tr> </table> </td>');
    }else{
        alert('Not a valid request')
    }
}
function deleteGenericImage(id){
    openWindow('addGenericAttachment.php?action=delete&id='+id+'', 700, 300, true);
}
function deleteAttachmentdata(id){
    var theRowth = document.getElementById("generic_product_attach_id_"+id+"");
    theRowth.parentNode.removeChild(theRowth);
}
function addgenericimage(row,type){
    openWindow('addGenericAttachment.php?action=add&rowid='+row+'&type='+type+'', 700, 300, true);
}
function chargeChange(row, change_type) {
    var do_totals = false;
    do_totals = true;
    switch (change_type) {
        case 'pofferus':
            setValue("charge_offerau[" + row + "]", convertCurrency(getValue(
                "charge_offerus[" + row + "]"), "USD", "AUD"));
            setValue("charge_offer[" + row + "]", convertCurrency(getValue(
                "charge_offerus[" + row + "]"), "USD", getValue(
                "invoice_currency"), true));
            break;
        case 'pofferau':
            setValue("charge_offerus[" + row + "]", convertCurrency(getValue(
                "charge_offerau[" + row + "]"), "AUD", "USD"));
            setValue("charge_offer[" + row + "]", convertCurrency(getValue(
                "charge_offerau[" + row + "]"), "AUD", getValue(
                "invoice_currency"), true));
            break;
    }
    if (do_totals) productUpdateTotals();
}
function productChange(row, change_type,generic) {
    var do_totals = false;
    var do_located = false;
    switch (change_type) {
        case 'pcost':
            if(generic){
                setValue("supplier_product_cost_usd[" + row + "]", convertCurrency(
                getValue("product_cost[" + row + "]"),getValue(
                "invoice_currency"), "USD", true));
                /*setValue("product_costus[" + row + "]", convertCurrency(
                product_cost, invoice_currency, "USD", true));*/
            }
            
            do_totals = true;
            break;
        case 'supplier_product_cost_usd':
            if(generic){
                setValue("product_cost[" + row + "]", convertCurrency(
                getValue("supplier_product_cost_usd[" + row + "]"), "USD",getValue(
                "invoice_currency"), true));
                /*setValue("product_costus[" + row + "]", convertCurrency(
                product_cost, invoice_currency, "USD", true));*/
            }
            
            do_totals = true;
            break;
       /* case 'ponline':
            do_totals = true;
            break;
        case 'poffer':
            setValue("product_offerau[" + row + "]", convertCurrency(getValue(
                "product_offer[" + row + "]"), getValue(
                "invoice_currency"), "AUD", true));
            setValue("product_offerus[" + row + "]", convertCurrency(getValue(
                "product_offer[" + row + "]"), getValue(
                "invoice_currency"), "USD", true));
            setValue('product_price_changed[' + row + ']', 'y');
            do_totals = true;
            break;
        case 'pofferus':
            setValue("product_offerau[" + row + "]", convertCurrency(getValue(
                "product_offerus[" + row + "]"), "USD", "AUD", true));
            setValue("product_offer[" + row + "]", convertCurrency(getValue(
                "product_offerus[" + row + "]"), "USD", getValue(
                "invoice_currency"), true));
            do_totals = true;
            break;
        case 'pofferau':
            setValue("product_offerus[" + row + "]", convertCurrency(getValue(
                "product_offerau[" + row + "]"), "AUD", "USD", true));
            setValue("product_offer[" + row + "]", convertCurrency(getValue(
                "product_offerau[" + row + "]"), "AUD", getValue(
                "invoice_currency"), true));
            do_totals = true;
            break;
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
        case 'sfound':
            do_located = true;
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
            break;*/
        default:
            do_totals = true;
            break;
    }
    if (do_totals) productUpdateTotals();
    if (do_located) productUpdateLocated();
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
function productUpdateLocated() {
    var all_found = true;
    var supplier_rows = [];
    var theRows = getElement("supplier_table").rows;
    for (var i = 0; i < theRows.length; i++)
        if (theRows[i].id.substr(0, 14) == 'supplier_rowa[')
            if (searchArray(theRows[i].id.substr(13), supplier_rows) == null)
                supplier_rows[supplier_rows.length] = theRows[i].id.substr(13);
    for (var i = 0; i < supplier_rows.length; i++)
        if (getValue("supplier_found" + supplier_rows[i]) == 'n') all_found =
            false;
    setValue('products_located', (all_found) ? 'y' : 'n');
}
function shipChange(change_type) {
    var do_totals = false;
    switch (change_type) {
        case 'pname':
            setValue("supplier_ship_product_name", getValue(
                "ship_product_name"));
            break;
        case 'pqty':
            setValue("supplier_ship_qty", getValue("ship_qty"));
            do_totals = true;
            break;
        case 'pcost':
            setValue("ship_costau", convertCurrency(getValue("ship_cost"),
                getValue("invoice_currency"), "AUD", true));
            setValue("ship_costus", convertCurrency(getValue("ship_cost"),
                getValue("invoice_currency"), "USD", true));
            setValue("supplier_ship_cost", getValue("ship_cost"));
            do_totals = true;
            break;
        case 'ponline':
            do_totals = true;
            break;
        case 'poffer':
            setValue("ship_offerau", convertCurrency(getValue("ship_offer"),
                getValue("invoice_currency"), "AUD", true));
            setValue("ship_offerus", convertCurrency(getValue("ship_offer"),
                getValue("invoice_currency"), "USD", true));
            do_totals = true;
            break;
        case 'pofferus':
            setValue("ship_offer", convertCurrency(getValue("ship_offerus"),
                "USD", getValue("invoice_currency"), true));
            setValue("ship_offerau", convertCurrency(getValue("ship_offerus"),
                "USD", "AUD", true));
            do_totals = true;
            break;
        case 'pofferau':
            setValue("ship_offer", convertCurrency(getValue("ship_offerau"),
                "AUD", getValue("invoice_currency"), true));
            setValue("ship_offerus", convertCurrency(getValue("ship_offerau"),
                "AUD", "USD", true));
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
    setAllValues('order_cost', convertCurrency(products_cost.toFixed(2),
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
    var freight_cost=0.00;
        if(getValue('supplier_product_ship_method')=='supplier_account'){
           freight_cost= Number(getValue('supplier_freight_cost'));
        } 
        setValue('freight_cost',freight_cost);
        setValue('total_invoice_cost',(products_cost+freight_cost));
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
    url += '&action=supplier';
    if (currency != null) url += '&currency=' + currency;
    //if(site_id!=null) url+='&site_id='+site_id;
    //if(member_id!=null) url+='&member_id='+member_id;
    var features = ''; //'scrollbars, resizable';//height=700, width=800
    window.open(url, 'product_popup', features);
}
function searchSupplierById(row, searchFor,keyid) {
    var currency = getValue('invoice_currency');
    var site_id = getValue('site_id');
    var member_id = getValue('member_id');
    var producttype = getValue('producttype_id');
    if (site_id == '1') site_id = '1|2';
    //var url='/webadmin/products/index.php?action=list';
    //var url='productsearch.php?type='+producttype;
    var url = 'Supplierproductsearch.php?';
    url += '&return_message=select_' + row;
    url += '&'+escape(keyid)+'=' + escape(searchFor);
    url += '&action=supplier';
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
    var url = '/webadmin/supplier_new/index.php?action=select';
    url += '&return_message=select_' + row;
    url += '&supplier_id=' + supplier_id;
    var features = 'scrollbars, resizable';
    window.open(url, 'Search', features);
}
function searchDefaultSupplier(searchFor) {
    var url = '/webadmin/supplier_new/index.php?action=list';
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
    console.log(data);
    if (data == null) return;
    if (data['return_message'].substring(0, 7) == 'select_') {
        var row = data['return_message'].substring(7);
        var invoice_currency = getValue("invoice_currency");
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
        if (data['supplier_online_price_usd'] == null) data['supplier_online_price_usd'] = '0.00';
        if (data['supplier_cost_price_usd'] == null) data['supplier_cost_price_usd'] = '0.00';
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
        setValue('product_cost[' + row + ']',  convertCurrency(data['supplier_cost_price_usd'], "USD",invoice_currency, true));
        setValue('supplier_product_cost[' + row + ']', convertCurrency(data['product_costus'], "USD",invoice_currency, true));
        setValue('product_costau[' + row + ']', data['product_costau']);
        setValue('product_costus[' + row + ']', data['supplier_cost_price_usd']);
        setValue('product_online[' + row + ']', data['product_online']);
        setValue('product_offer[' + row + ']', data['product_offer']);
        setValue('product_offerau[' + row + ']', data['product_offerau']);
        setValue('product_offerus[' + row + ']', data['supplier_online_price_usd']);
        setValue('product_weight[' + row + ']', data['product_weight']);
        setValue('product_weight_inc[' + row + ']', data['product_weight_inc']);
        setValue('decimal[' + row + ']', data['dPoints']);
        setValue('product_notes[' + row + ']', data['product_notes']);
        setValue('product_warranty[' + row + ']', data['warranty']);
        setValue('product_condition[' + row + ']', data['product_condition']);
        setValue('product_price_changed[' + row + ']', 'n');
        setValue('supplier_product_qty[' + row + ']', data['min_order']);
        setValue('sup_code[' + row + ']', data['sup_code']);
        setValue('supplier_productname[' + row + ']', data['supplier_productname']);
        setValue('product_supplier_id[' + row + ']', data['product_supplier_id']);
        setValue('supplier_product_offer[' + row + ']', data['product_offerus']);
        setValue('color_id[' + row + ']', data['color_id']);
        if(data['color_id']){
            setValue('color_id_sym[' + row + ']','<span onmouseover="tt_in('+data['color_name']+')" onmouseout="tt_out()" style="height:12px;width: 15px; border-radius: 50%; display: inline-block; text-align: center; padding-top: 3px; margin-right: 4px!important;background-color:'+data['color_code']+'"></span>');
        }
        //if(data['producttype_id']!=null)
        //setValue('producttype_id', data['producttype_id']);
        //category=data['producttype_id'];
        //if(data['product_weight']<=0.0 && addMissingWeight!=null)
        //addMissingWeight(data['product_id'], data['product_name'], data['product_url']);
    }
   //setSupplierData(data);
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
    theFrm.action = ".?action=do_purchase";
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
function updateProductPrice(row) {
    var product_id = getValue('product_id[' + row + ']');
    var sub_id = getValue('sub_id[' + row + ']');
    var product_qty = getValue('product_qty[' + row + ']');
    var generic = getValue('generic[' + row + ']');
    var site_id = getValue('site_id');
    var member_id = getValue('member_id');
    var currency = getValue('invoice_currency');
    var url = 'invoice_update_price.php?product_id=' + product_id;
    if (site_id != null) url += '&site_id=' + site_id;
    if (member_id != null) url += '&member_id=' + member_id;
    if (currency != null) url += '&currency=' + currency;
    if (product_qty != null) url += '&product_qty=' + product_qty;
    productChange(row, 'pqty');
    if (generic == 'n') {
        //now start of the AJAX script.
        if (window.XMLHttpRequest) { // Mozilla, Safari, ...
            http_request = new XMLHttpRequest();
            if (http_request.overrideMimeType) http_request.overrideMimeType(
                'text/xml');
        } else if (window.ActiveXObject) { //IE
            try {
                http_request = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    http_request = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {}
            }
        }
        if (!http_request) {
            alert('Giving up :( Cannot create an XMLHTTP instance');
            return false;
        }
        http_request.onreadystatechange = function() {
            updatePrices(http_request, row);
        };
        http_request.open('GET', url, true);
        http_request.send(null);
    }
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
            productChange(row, 'pqty');
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
function updateshipto(data){

    var theRowth = document.getElementById("supplier_freight_account");
    if(theRowth)
    theRowth.parentNode.removeChild(theRowth);
    var theRowth = document.getElementById("supplier_product_terms");
    if(theRowth)
    theRowth.parentNode.removeChild(theRowth);
    var theRowth = document.getElementById("supplier_freight_costid");
    if(theRowth)
    theRowth.parentNode.removeChild(theRowth);
    var theRowth = document.getElementById("supplier_product_courior");
    if(theRowth)
    theRowth.parentNode.removeChild(theRowth);
    if(data.value=='our_account'){
        $('#invoice_shipping_table').append('<tr class="alt" id="supplier_product_courior"> <td style="font-weight:bold;text-align: right;width: 31%;">Freight Name :</td> <td> <select name="supplier_product_courior" style="width:80%;" class="change-freight"> <option value="">Select Freight</option><?=$freightiption?></select><a href="options.php?action=list&option_name=freight" target="_blank">(Add/Edit)</a> </td> </tr> <tr class="alt " id="supplier_freight_account"> <td style="font-weight:bold;text-align: right;width: 31%;">Account# :</td> <td> <input type="text" name="supplier_freight_account" style="width: 97%;" readonly id="supplier_freight_account_box"/> </td> </tr>');
    }else if(data.value=='supplier_account'){
        $('#invoice_shipping_table').append('<tr class="alt " id="supplier_product_terms"> <td style="font-weight:bold;text-align: right;width: 31%;">Freight Terms :</td> <td> <select name="supplier_product_terms" > <?=formatHTMLOptions(array('works' => 'Works', 'd2d' => 'Door to Door'),'works') ?> </select> </td> </tr> <tr class="alt " id="supplier_freight_costid"> <td style="font-weight:bold;text-align: right;width: 31%;">Freight Cost:</td> <td> <input type="text" name="supplier_freight_cost" style="width: 97%;" onchange="productChange(3, \'freight\',0)"/> </td> </tr>');
    }else{

    }
    productChange(3, 'freight',0);
}
$('body').on('change','.change-freight',function(){
 var account = $(this).find('option:selected').attr('data-account');
        $('#supplier_freight_account_box').val(account);
  });
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
  
    <tbody id="product_table" name="product_table">
<?php
$pcount = 1;
foreach ($invoice_products as $product) {
    $generic = ('GME' == $product['product_id']) ? true : false;
    if($generic){
        echo product_row_generic($pcount,$product,$generic,1,0,0);
    }else{
        echo product_row($pcount, $product, $generic);
        echo product_row_generic($pcount,$product, $generic);
    }
    $pcount++;
}
?>
    </tbody>
    <tr id="product_ship_rowa"<?=(count($invoice_products)==0) ? ' style="display:none"' : 'style="display:none"'?>>
        <td><input type="hidden" name="ship_id" value="<?php hecho($invoice['ship_id'])?>" /><input type="text" name="ship_product_id" size="4" value="F<?=str_pad($invoice['ship_id'], 2, '0', STR_PAD_LEFT)?><?=('y' == $invoice['ship_insurance']) ? 'I' : 'N'?>" disabled="disabled" style="text-align:right" /></td>
        <td colspan="3"><input name="ship_product_name" style="width:100%" type="text" value="<?php hecho($invoice['ship_product_name'])?>" onchange="shipChange('pname')" maxlength="64" /></td>
        <td><input name="ship_qty" size="3" style="text-align:right" type="text" value="1" onchange="shipChange('pqty')" onfocus="this.select()"/></td>
        <td><input name="ship_cost" size="6" style="text-align:right" type="text" value="<?=sprintf('%.2f', $invoice['ship_cost'])?>" onchange="shipChange('pcost')" onfocus="this.select()"/></td>
        <td><input name="ship_online" size="6" style="text-align:right" type="text" value="<?=sprintf('%.2f', $invoice['ship_online'])?>" onchange="shipChange('ponline')" onfocus="this.select()"/></td>
        <td><input name="ship_offer" size="6" style="text-align:right" type="text" value="<?=sprintf('%.2f', $invoice['ship_offer'])?>" onchange="shipChange('poffer')" onfocus="this.select()"/></td>
        <td><input name="ship_offerau" size="6" style="text-align:right;<?=($show_usd) ? 'display:none;' : ''?>" type="text" value="<?=sprintf('%.2f', $invoice['ship_offerau'])?>" onchange="shipChange('pofferau')" readonly="readonly" /><input name="ship_offerus" size="6" style="text-align:right;<?=($show_usd) ? '' : 'display:none;'?>" type="text" value="<?=sprintf('%.2f', $invoice['ship_offerus'])?>" onchange="shipChange('pofferus')" readonly="readonly" /></td>
        <td><input onclick="deleteFreight()" type="button" value="Delete" /></td>
    </tr>
    <tr class="alt" id="product_ship_rowb"<?=(count($invoice_products)==0) ? ' style="display:none"' : 'style="display:none"'?>>
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
</table>
<table class="wide total_invoice_total" style="width: 50%;margin-top: 10px; <?= (count($invoice_charges)==0)?'':'display:none;'?>" id="invoice_shipping_table">
    <tr class="alt ">
        <td style="font-weight:bold;text-align: right;width: 31%;">Ship To :</td>
        <td>
            <select name="ship_to" style="width:80%;">
               <?=formatHTMLOptions(getOrderAddress(),$invoice['ship_to']) ?> 
            </select><a href="options.php?action=list&option_name=address" target="_blank">(Add/Edit)</a>
        </td>
    </tr>
    <tr class="alt ">
        <td style="font-weight:bold;text-align: right;width: 31%;">Shipping Via :</td>
        <td>
            <select name="supplier_product_ship_method"  onchange="updateshipto(this)" >
                <?=formatHTMLOptions(array('pickup' => 'Pickup', 'our_account' => 'Our Account', 'supplier_account' => 'Supplier Account'),$invoice['supplier_product_ship_method'])?>
            </select>
        </td>
    </tr>
    <?php if($invoice['supplier_product_ship_method']=='supplier_account'){?>
        <tr id="supplier_product_terms">
            <td style="font-weight:bold;text-align: right;width: 31%;">Freight Terms :</td> 
            <td> <select name="supplier_product_terms" > <?=formatHTMLOptions(array('works' => 'Works', 'd2d' => 'Door to Door'),$invoice['supplier_product_terms']) ?> </select> 
            </td> 
            </tr> 
            <tr class="alt " id="supplier_freight_costid"> 
                <td style="font-weight:bold;text-align: right;width: 31%;">Freight Cost:</td> 
                <td>
                    <input type="text" name="supplier_freight_cost" style="width: 97%;" value="<?= $invoice['supplier_freight_cost'] ?>" onchange="productChange(3, 'freight',0)"/> 
                </td> 
            </tr>
    <?php }else if($invoice['supplier_product_ship_method']=='our_account'){ ?>
                <tr class="alt" id="supplier_product_courior"> 
                    <td style="font-weight:bold;text-align: right;width: 31%;">Freight Name :</td> 
                    <td> 
                        
                        <select name="supplier_product_courior" style="width:80%" class="change-freight"> 
                            <option value="">Select Freight</option>
                            <?php 
                                $freightdata=getFreightList();
                                foreach ($freightdata as $key => $value) {
                                    $freid=$value["freight_id"];
                                    $selected=($freid==$invoice['supplier_product_courior'])?"selected":"";
                                    $accountid=$value['account_number'];
                                    echo '<option value="'.$freid.'" '.$selected.' data-account="'.$accountid.'">'.$value["freight_name"].'</option>';
                                }

                            ?>
                            <!-- <?=formatHTMLOptions($courier_list,$invoice['supplier_product_courior']) ?> --> 
                         </select><a href="options.php?action=list&option_name=freight" target="_blank">(Add/Edit)</a>
                        </td> 
                </tr> 
                <tr class="alt " id="supplier_freight_account"> 
                    <td style="font-weight:bold;text-align: right;width: 31%;">Account# :</td> 
                    <td> 
                        <input type="text" name="supplier_freight_account" style="width: 97%;" value="<?= $invoice['supplier_freight_account'] ?>"   id="supplier_freight_account_box" readonly/> 
                    </td> 
                </tr>
    <?php } else{
    }?>
</table>
<table class="wide" style="width: 50%;margin-top: 10px;">
    <tr class="alt total_invoice_total" <?= (count($invoice_charges)==0)?'':'style="display:none;"'?>>
        <td style="font-weight:bold; text-align: right;">PURCHASE TOTAL :</td>
        <td>
            <table class="wide" style="border: 0px;">
            <tr class="alt">
                <td style="font-weight:bold; text-align: right;">Order Sub Total</td>
                <td>
                     <input type="text" name="order_cost" readonly="readonly" value="<?=sprintf('%.2f', $invoice['order_cost'])?>" style="text-align:right" />
                </td>
            </tr>
            <tr class="alt">
                <td style="font-weight:bold; text-align: right;">Freight Costs</td>
                <td>
                     <input type="text" name="freight_cost" readonly="readonly"  value="<?=sprintf('%.2f', $invoice['freight_cost'])?>" style="text-align:right" />
                </td>
            </tr>
             <tr class="alt">
                <td style="font-weight:bold; text-align: right;">TOTAL</td>
                <td>
                     <input type="text" name="total_invoice_cost" readonly="readonly" value="<?=sprintf('%.2f', $invoice['total_invoice_cost'])?>" style="text-align:right" />
                </td>
            </tr>
            </table>
        </td>
        
    </tr>
</table>
<br />
<table class="wide" style="width: 50%;">
      <tr class="alt total_invoice_total" <?= (count($invoice_charges))?'':'style="display:none;"'?>>
        <td style="font-weight:bold;text-align: right;width: 31%;">PRODUCTS LOCATED :</td>
        <td >
            <select name="supplier_product_located" style="background-color: rgb(255, 0, 0);" onchange="updateSelect(this)">
                <?=formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'),'n') ?>
            </select>
        </td>
    </tr>
</table>
<br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none" id="sectionProducts_hideNote">
    All Products requested can be viewed by Clicking "Products Requested" above<br />
</div>

<!-- /product_supplier.tpl -->

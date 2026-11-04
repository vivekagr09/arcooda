<!-- _product_status.tpl -->
<?php
/**
 * Product Status Template
 *
 * Inputs:
 * - $item
 */

if (!isset($item) || !is_array($item)) {
    return;
}

?>
<script type="text/javascript">
//<![CDATA[
$(function() {
    $(Product.selector("discontinued")).change(function() {
        $("#discontinued_tr").toggle($(this).val() == 'y');
        if($(this).val() == 'y'){
             $('select[name="item[available]"]').val('427').trigger('change');
         }
    }).change();

});
$('body').on('click','.deletereuleProduct',function(){
    if (confirm("Are you sure you want to Delete this product ??")) {
         $(this).parent().remove();

    }
});
function dispalyonlyavailableforowner(dataarray){
    dataarray.item_list.forEach(onlyavailableforowner);
} 
function onlyavailableforowner(data) {
        if(data['product_id'] == null) data['product_id'] = data['id'];
        $('#subproduct-available-for').append('<p><input type="hidden" name="item[product_availabe_for][]" value="'+data['product_id']+'" /> '+data['product_name']+' <span class="deletereuleProduct" style="cursor:pointer;color:blue;">(remove)</span></p>')
}
//]]>
</script>
<?php

/**
 * Limit the minimum order qty above one
 */
$item['min_order'] = max($item['min_order'], 1);

/**
 * Set the default precision to 2 decimal points
 */
if (!isset($item['precision']) || 0 == $item['precision']) {
    $item['precision'] = 2;
}

$percent_off = "0.00";
if (bccomp($item['primary_price'], "0.00", 2) > 0 && bccomp($item['secondary_price'], "0.00", 2) > 0) {
    $percent_off = bcmul("100", bcsub("1.00", bcdiv(
        convertCurrency($item['primary_price'], $item['primary_currency'], $item['secondary_currency'], false, 4),
        $item['secondary_price'],
        4
    ), 4), 2);
}
?>

<script type="text/javascript">
//<![CDATA[
function setDiscontinuedData(data) {
    if (data['return_message'] == 'discontinued') {
        setValue('item[discontinued_id]', data['id']);
        setValue('item[discontinued_name]', data['product_name']);
        setValue('disc_name', data['id'] + ' - ' + data['product_name']);
    }
}

function searchProduct(return_message) {
    var url = '.?action=list&return_message=' + return_message + '&search_for=' + escape(getValue('disc_name'));
    window.open(url);
}

function calculateSpecialPrice(percent) {
    var selling_price = getValue('item[sales_price]');
    if (selling_price > 0) {
        var sp = Number(Number(selling_price) * (1 - (percent / 100))).toFixed(2);
        setValue('item[special_priceus]', sp);
    }
    changeAllDiscountRows(percent);
}

function calculatePercent(price) {

    var salPrice = getValue('item[sales_price]');
    var saleCurrency = getValue('item[sales_currency]');   
    var selling_price =toCurrency(salPrice,'USD',saleCurrency);
    var percent = 0;
    if (selling_price > 0) percent = Number(100 * (1 - (price / selling_price))).toFixed(2);
    setValue('item[percent]', percent);
    changeAllDiscountRows(percent);
}
$(function() {
    const syncRadios = (sourceName, targetName) => {
        const sourceRadios = $(`input[name="${sourceName}"]`);
        const targetRadios = $(`input[name="${targetName}"]`);

        let selectedValue = null;
        sourceRadios.each(function() {
            if ($(this).is(':checked')) {
                selectedValue = $(this).val();
            }
        });

        targetRadios.each(function() {
            if ($(this).val() === selectedValue) {
                $(this).prop('checked', true);
            }
        });
    };
    $("[name='item[order_rule]']").change(function(){
        if($(this).val()=='invoice_only_for_restricted_owners'){
            $('#display_invoice_only_for_restricted_owners').show();
        }else{
            $('#display_invoice_only_for_restricted_owners').hide();
        }
    })
    $("[name='item[allow_invoice]']").add("[name='item[additional_fees]']").change(function() {
        var elem1 = $("[name='item[allow_invoice]']");
        var elem2 = $("[name='item[additional_fees]']");
        var val1 = elem1.is(':checked');
        var val2 = elem2.is(':checked');
        elem1.parent().css({
            'background': ((val1 || val2) ? 'pink' : null)
        });
        elem2.parent().css({
            'background': ((val1 || val2) ? 'pink' : null)
        });
    });
    $("[name='item[price_online]']").change(function() {
        $(".price_online_y,.price_online_n").stop().hide();
        $(".price_online_" + $(this).val()).stop().fadeIn();
    	$(this).css({
            "background": ("n" == $(this).val()) ? "pink" : "",
        });
    }).change();

    $(Product.selector("stock_type")).change(function() {
        if (this.checked) {
            $("[class|='stock-type']").stop().toggle(false);
            $(".stock-type-" + $(this).val()).stop().fadeIn();
        }
    }).change();
    $('input[name="item[stock_type]"]').change(function() {
        if (this.value === 'consignment' && <?= $item['qty'] ?> != 0) {
            alert('Cannot proceed: Because there is still inventory, this product cannot be changed to a consignment product.');
            // $('input[name="item[stock_type]][value=stock_for_sale]').prop('checked', true);
            // $('input[name="item[stock_type][value=stock_for_sale]"]').prop('checked', true);
            $('input.stockist[value="stock_for_sale"]').prop('checked', true);
            $('input.stockist1[value="stock_for_sale"]').prop('checked', true);
        }else{
            syncRadios('item[stock_type]', 'item[stock_type1]');
        }
    });
});
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionStatus\');return false">Product Status</a>')?>

<div id="sectionStatus"><table class="table_fields">
    <tr>
        <th>Minimum Order Quantity :</th>
        <td><input name="item[min_order]" style="text-align:right;" type="text" onchange="minOrderQtyChanged();" value="<?php hecho($item['min_order'])?>" /></td>
    </tr>
<?php
if (count($sub_product) == 0) {
        $is_special = false;
        $todayDate = date('Y-m-d');
        if ('y' == $item['special_flag'] && ($todayDate <= substr((string) $item['special_expiry'], 0, 10) || $item['special_expiry'] == '0000-00-00 00:00:00')) {
            $is_special = true;
        }else{
            $item['special_flag'] = 'n';
            $item['special_priceus'] = 0.00;
            $item['percent'] = 0.00;
            $item['special_expiry'] = '';
        }
?>
    <tr>
        <th>Specials :</th>
        <td><input name="item[special_flag]" value="y" type="checkbox" onclick="if (!this.checked) { setValue('percent',0); calculateSpecialPrice(0); } toggleSection('specials', this.checked); toggleSection('specials2', this.checked)"<?= $is_special ? ' checked="checked"' : ''?> /></td>
    </tr>
    <tr id="specials" <?=('y' == $item['special_flag']) ? '' : 'style="display:none"'?>>
        <th>Specials Price :</th>
        <td><input type="text" style="text-align:right;" name="item[special_priceus]" value="<?=convertCurrency($item['special_priceus'], 'USD', '', false, $item['precision'])?>" onchange="calculatePercent(this.value)" onblur="calculatePercent(this.value)"/> <b>USD</b>&nbsp;&nbsp;<input type="text" name="item[percent]" value="<?=$item['percent']?>" onchange="calculateSpecialPrice(this.value)" onblur="calculateSpecialPrice(this.value)" /> <b>% off</b></td>
    </tr>
    <tr id="specials2" <?=('y' == $item['special_flag']) ? '' : 'style="display:none"'?>>
        <th>Expires :</th>
        <td><input type="date" name="item[special_expiry]" value="<?=(is_null($item['special_expiry'])) ? '' : substr($item['special_expiry'], 0, 10) ?>" /><button onclick="Product.set(&quot;special_expiry&quot;, &quot;&quot;);return false"><i class="fas fa-trash-alt"></i></button></td>
    </tr>

    <tr>
        <th>Additional Fees :</th>
        <td><input
            type="checkbox"
            name="item[additional_fees]"
            value="y"<?=('y' == $item['additional_fees']) ? ' checked="checked"' : ''?>
            onchange="$('.additional_fees').toggle();if(!this.checked) $('[name=\'item[forwarder_fee]\']').val('0.00');"
            <?=('y' == $item['additional_fees']) ? ' style="background:pink;"' : ''?>
        /></td>
    </tr>
    <tr class="additional_fees"<?=('y' == $item['additional_fees']) ? '' : ' style="display:none;"'?>>
        <th>Special Order Fees :</th>
        <td>
            $<input type="text" name="item[forwarder_fee]" value="<?php hecho(('y' == $item['additional_fees']) ? $item['forwarder_fee'] : '0.00')?>" />
            <select name="item[forwarder_fee_currency]" style="width:auto;">
                <option value="USD">U.S. Dollars - USD</option>
                <?=formatHTMLOptions(productGetOptionList('forwarder_fee_currency'), $item['forwarder_fee_currency'])?>
            </select>
        </td>
    </tr>
        <?php } ?>
    <tr>
        <th>Order Rule :</th>
        <td>
            <select name="item[order_rule]" style="width:auto;">
                <?=formatHTMLOptions([
                    'inquiry_for_everyone'                    => 'Inquiry for Everyone',
                    'invoice_for_everyone'                    => 'Invoice for Everyone',
                    'invoice_only_for_restricted_owners'      => 'Invoice only for Restricted Owners',
                    'invoice_only_for_arcooda_machine_owners' => 'Invoice only for VIP Members',
                ], $item['order_rule'])?>
            </select>
        </td>
    </tr>
     <tr style="<?=  ($item['order_rule']!='invoice_only_for_restricted_owners')?'display:none;':''?>" id="display_invoice_only_for_restricted_owners">
        <th>Only Available for Owner of <small><i><a href="#" onclick="window.open('master_products.php?return_message=rule_product&search_for=');return false;">(Add)</a></i></small>:</th>
        <td id="subproduct-available-for" >
            <?php if($item['product_availabe_for'] && count(explode(',',$item['product_availabe_for']))>0){
                foreach (explode(',',$item['product_availabe_for']) as $key => $value) {
                    $prod =productFromDatabase($value);
                    echo' <p><input type="hidden" name="item[product_availabe_for][]" value="'.$value.'" /> '.$prod["product_name"].' <span class="deletereuleProduct" style="cursor:pointer;color:blue;">(remove)</span></p>';
                } 
            }
            ?>
            
        </td>
    </tr>
   <!--  <tr>
        <th>Allow Invoice :</th>
        <td><input type="checkbox"<?=('y' == $item['additional_fees'] || 'y' == $item['allow_invoice']) ? ' style="background:pink;"' : ''?> name="item[allow_invoice]" value="y"<?=('n' != $item['allow_invoice']) ? ' checked="checked"' : ''?> /></td>
    </tr> -->
    <tr>
        <th>Display Price Online :</th>
        <td><select name="item[price_online]"><?=formatHTMLOptions(productGetOptionList('price_online'), $item['price_online'])?></select></td>
    </tr>
    <tr class="price_online_n">
        <th>Lowest Price Guarantee :</th>
        <td><select name="item[price_guarantee]"><?=formatHTMLOptions(productGetOptionList('price_guarantee'), $item['price_guarantee'])?></select></td>
    </tr>
    <tr>
        <th>When Sold Out :</th>
        <td><select name="item[when_sold_out]"><?=formatHTMLOptions(productGetOptionList('when_sold_out'), $item['when_sold_out'])?></select></td>
    </tr>
    <tr>
        <th>Number of Decimal Points :</th>
        <td><select name="item[precision]">
            <?=formatHTMLOptions(array('2' => '2', '3' => '3', '4' => '4'), $item['precision'])?>
        </select></td>
    </tr>
    <tr>
        <th>Product Discontinued :</th>
        <td><select name="item[discontinued]">
            <?=formatHTMLOptions(productGetOptionList('discontinued'), $item['discontinued'])?>
        </select></td>
    </tr>
    <tr id="discontinued_tr">
        <th></th>
        <td>
            <?php         include __DIR__ . "/_recommended_products.tpl.php"; ?>
        </td>
    </tr>
    <tr>
        <th>Featured Product :</th>
        <td><select name="item[feature]" onclick="toggleSection('feature', this.value == 'y')"><?=formatHTMLOptions(productGetOptionList('active'), $item['feature'])?></select>
        </td>
    </tr>
    <tr name="feature" id="feature" <?=('y' == $item['feature']) ? '' : 'style="display:none"'?>>
        <th>Expires :</th>
        <td>
            <input type="date" name="item[featured_expiry]" value="<?php hecho((is_null($item['featured_expiry'])) ? '' : substr($item['featured_expiry'], 0, 10))?>"/>
            <button onclick="Product.set(&quot;featured_expiry&quot;, &quot;&quot;);return false"><i class="fas fa-trash-alt"></i></button>
        </td>
    </tr>
       <tr>
            <th>Stock Type :</th>
            <td>
                <input type="radio" name="item[stock_type]" class="stockist" value="stock_for_sale"<?=('stock_for_sale' == $item['stock_type']) ? ' checked="checked"' : ''?> /> Stock for Sale
                <input type="radio" name="item[stock_type]" value="consignment"<?=('consignment' == $item['stock_type']) ? ' checked="checked"' : ''?> /> Consignment
                <input type="radio" name="item[stock_type]" value="stock_sample"<?=('stock_sample' == $item['stock_type']) ? ' checked="checked"' : ''?> /> Stock Sample
                <input type="radio" name="item[stock_type]" value="repair_item"<?=('repair_item' == $item['stock_type']) ? ' checked="checked"' : ''?> /> Repair
            </td>
        </tr>
    <tr>
        <th>&nbsp;</th>
        <td>&nbsp;</td>
    </tr>
    
    <tr>
        <th>Active Online :</th>
        <td><select name="item[active]" onchange="this.style.backgroundColor=((this.value=='n')?'#FF0000':'#00FF00');" style="background:<?=('n' == $item['active']) ? '#f00;' : '#0f0;'?>">
            <?=formatHTMLOptions(productGetOptionList('active'), $item['active'])?>
        </select></td>
    </tr>
</table>
<br /><br />
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionStatus_hideNote">
    Product Status can be viewed by Clicking "Product Status" above<br />
</div>

<!-- /_product_status.tpl -->

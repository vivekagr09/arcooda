<!-- _bulk_pricing.tpl -->

<?php
/**
 * Product Bulk Pricing Template
 *
 * Inputs:
 * - $item
 * - - cost_currency
 * - - cost_price
 * - - id
 * - - precision
 * - - sales_currency
 * - $item_bulk_pricing[]
 * - - id
 * - - product_id
 * - - qty
 * - - unit_sale_price
 *
 * Overwrites:
 * - $temp
 * - $temp2
 */

if (!isset($item_bulk_pricing) || !is_array($item_bulk_pricing)) {
    return;
}

/**
 * Bulk Pricing entry template
 *
 * @param  $count
 * @param  $row_data
 * @return HTML Content of the entry
 */
function item_bulk_pricing_row($count = '[ROW_COUNT]', $row_data = array())
{
    global $item;
    $name_key = 'item_bulk_pricing[' . $count . ']';
    $hidden   = array(
        $name_key . '[id]'         => $row_data['id'],
        $name_key . '[product_id]' => $row_data['product_id'],
    );

    ob_start();
?>
<tr id="item_bulk_pricing_rowa[<?=$count?>]">
    <td>
        <?= formatHTMLInputs($hidden) ?>
        <input type="number" min="0" max="9999999999" name="<?=$name_key?>[qty]" value="<?php hecho($row_data['qty'])?>" />
    </td>
    <td><input type="number" min="<?= bcadd('0.0000', '0', $item['precision']) ?>" max="<?= bcadd('999999.9999', '0', $item['precision']) ?>" step="0.<?= ($item['precision'] > 1) ? str_repeat('0', $item['precision'] - 1) : '' ?>1" name="<?=$name_key?>[unit_sale_price]" value="<?=$row_data['unit_sale_price']?>" /></td>
    <td><input type="number" min="<?= bcadd('0.0000', '0', $item['precision']) ?>" max="<?= bcadd('999999.9999', '0', $item['precision']) ?>" step="0.<?= ($item['precision'] > 1) ? str_repeat('0', $item['precision'] - 1) : '' ?>1" name="<?=$name_key?>[unit_special_price]" value="<?=$row_data['unit_special_price']?>" /></td>
    <td><input type="number" style="width:60%;" step="any" name="<?=$name_key?>[sale_discount]" value="<?=$row_data['sale_discount']?>" /></td>
    <td><input type="number" min="<?= bcadd('0.0000', '0', $item['precision']) ?>" max="<?= bcadd('999999.9999', '0', $item['precision']) ?>" step="0.<?= ($item['precision'] > 1) ? str_repeat('0', $item['precision'] - 1) : '' ?>1" name="<?=$name_key?>[unit_cost_price]" value="<?= $row_data['unit_cost_price'] ?>" /></td>
    <td><input type="number" min="<?= bcadd('0.0000', '0', $item['precision']) ?>" max="<?= bcadd('999999.9999', '0', $item['precision']) ?>" step="0.<?= ($item['precision'] > 1) ? str_repeat('0', $item['precision'] - 1) : '' ?>1" name="<?=$name_key?>[unit_profit]" value="<?=$row_data['unit_profit']?>" /></td>
    <td><input type="number" min="<?= bcadd('0.0000', '0', $item['precision']) ?>" max="<?= bcadd('999999.9999', '0', $item['precision']) ?>" step="0.<?= ($item['precision'] > 1) ? str_repeat('0', $item['precision'] - 1) : '' ?>1" name="<?=$name_key?>[total_profit]" value="<?=$row_data['total_profit']?>" /></td>
    <td><input type="number" style="width:60%;" step="any" name="<?=$name_key?>[markup]" value="<?=$row_data['markup']?>" /></td>
    <td>
        <a href="javascript:;" onclick="item_bulk_pricing_delete(<?= $count ?>)"  style="color:#555;"><i class="fas fa-trash-alt"></i></a>
    </td>
</tr>
<?php
    return ob_get_clean();
}

?>

<script type="text/javascript">
//<![CDATA[
let ProductBulkPricing = {
    row: function (elem) {
        return $(elem).attr("name").split("[")[1].split("]")[0];
    },
    rows: function () {
        var result = [];
        var self = this;
        $(this.selector("id")).each(function() {
            result.push(self.row(this));
        });
        return result;
    },
    selector: function (field, row) {
        if (Array.isArray(field)) {
            var result = [];
            while (field.length) {
                result.push(this.selector(field.pop(), row));
            }
            return result.join(",");
        }
        if (Array.isArray(row)) {
            var result = [];
            while (row.length) {
                result.push(this.selector(field, row.pop()));
            }
            return result.join(",");
        }
        if (row === undefined) {
            return "[name^='item_bulk_pricing['][name$='][" + field + "]']";
        }
        return "[name='item_bulk_pricing[" + row + "][" + field + "]']";
    },
    get: function (field, row) {
        return $(this.selector(field, row)).val();
    },
    set: function (field, row, value) {
        var $elem = $(this.selector(field, row));
        if (value !== $elem.val()) {
            $elem.val(value).change();
        }
    }
};

var item_bulk_pricing_count = <?=max(100, count($item_bulk_pricing))?>;
function item_bulk_pricing_fieldChanged(row, field_name, field_value) {
    field_value = Number(field_value || 0);
    var base_name = 'item_bulk_pricing[' + row + ']';
    var cost = Number(getValue(base_name + '[unit_cost_price]'));
    var qty = Number(getValue(base_name + '[qty]'));
    var precision = Number(getValue('product[precision]')) || <?=intval($item['precision'])?>;
    switch (field_name) {
        case 'qty':
            var profit = Number(getValue(base_name + '[unit_profit]'));
            setValue(base_name + '[total_profit]', convertCurrency(field_value * profit, 'USD', '', false, precision));
            break;
        case 'unit_orig_price':
        case 'unit_sale_price':
            setValue(base_name + '[unit_profit]', convertCurrency(field_value - cost, 'USD', '', false, precision));
            setValue(base_name + '[total_profit]', convertCurrency((field_value - cost) * qty, 'USD', '', false, precision));
            if (cost != 0) setValue(base_name + '[markup]', Math.floor((field_value - cost) * 100 / cost));
            else setValue(base_name + '[markup]', '');
            break;
        case 'total_profit':
            if (qty == 0) break; //Can't set if there's no quantity to divide by
            field_value = field_value / qty;
            setValue(base_name + '[unit_profit]', convertCurrency(field_value, 'USD', '', false, precision));
            //Then follow on with unit_profit
        case 'unit_profit':
            setValue(base_name + '[unit_sale_price]', convertCurrency(field_value + cost, 'USD', '', false, precision));
            setValue(base_name + '[total_profit]', convertCurrency(field_value * qty, 'USD', '', false, precision));
            if (cost != 0) setValue(base_name + '[markup]', Math.floor(field_value * 100 / cost));
            else setValue(base_name + '[markup]', '');
            break;
        case 'markup':
            if (cost != 0) {
                setValue(base_name + '[unit_sale_price]', convertCurrency(field_value * cost / 100 + cost, 'USD', '', false, precision));
                setValue(base_name + '[unit_profit]', convertCurrency(field_value * cost / 100, 'USD', '', false, precision));
                setValue(base_name + '[total_profit]', convertCurrency(qty * field_value * cost / 100, 'USD', '', false, precision));
            } else setValue(base_name + '[markup]', '');
            break;
    }
}
function item_bulk_pricing_add() {
    var count = String(item_bulk_pricing_count);
    if (count <= 0) return;
    var target_table = getElement("item_bulk_pricing_table");
    if (target_table != null) {
        $(target_table).append($(<?= strtr(jecho(item_bulk_pricing_row('[ROW_COUNT]', array(
            'product_id'      => $item['id'],
            'unit_cost_price' => '[UNIT_COST_PRICE]',
            'markup'          => '100.00000',
        )), 1), array(
            '[ROW_COUNT]'       => '" + item_bulk_pricing_count + "',
            '[UNIT_COST_PRICE]' => '" + Product.get("cost_price") + "',
        )) ?>));
        item_bulk_pricing_count++;
    }
}
function item_bulk_pricing_delete(row) {
    setValue("item_bulk_pricing_deleted", getValue("item_bulk_pricing_deleted") + "," + ProductBulkPricing.get("id", row));
    var theRow = getElement("item_bulk_pricing_rowa[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
}
function minOrderQtyChanged() {
    var newValue = getValue('product[min_order]');
    var bulk_rows = ProductBulkPricing.rows();
    var weight_rows = [];
    if (ProductBulkWeight !== undefined) {
        weight_rows = ProductBulkWeight.rows();
    }
    var exists = wExists = false;
    for (i = 0; i < bulk_rows.length; i++) {
        var value = getValue('item_bulk_pricing' + bulk_rows[i] + '[qty]');
        if (newValue == value) {
            exists = true;
            break;
        }
    }
    for (i = 0; i < weight_rows.length; i++) {
        var value = ProductBulkWeight.get("bulk_qty", weight_rows[i]);
        if (newValue == value) {
            wExists = true;
            break;
        }
    }
    if (!exists && newValue != 1) {
        var key = item_bulk_pricing_count;
        item_bulk_pricing_add();
        ProductBulkPricing.set("qty", key, newValue);
    }
    if (!wExists && newValue != 1) {
        var key = item_bulk_weight_count;
        item_bulk_weights_add();
        ProductBulkWeight.set("bulk_qty", key, newValue);
    }
}
function oPriceChanged(row, price) {
    var percent = getValue('bulkD[' + row + ']');
    var sPrice = Number(price * (1 - (Number(percent) / 100))).toFixed(2);
    setValue('item_bulk_pricing[' + row + '][unit_sale_price]', sPrice);
    if (percent > 0) changeAllDiscountRows(percent, true);
}
function sPriceChanged(row, price) {
    var oPrice = Number(getValue('item_bulk_pricing[' + row + '][unit_orig_price]')).toFixed(2);
    var percent = 0;
    price = Number(price).toFixed(2);
    if (oPrice > 0 && price != oPrice) percent = Number((1 - (price / oPrice)) * 100).toFixed(2);
    setValue('bulkD[' + row + ']', percent);
    changeAllDiscountRows(percent, true);
}
function bDiscountChanged(row, percent, doAll) {
    if (doAll == null) doAll = false;
    var oPrice = getValue('item_bulk_pricing[' + row + '][unit_orig_price]');
    var nPrice = oPrice;
    if (percent > 0) nPrice = Number((1 - (percent / 100)) * oPrice).toFixed(2);
    setValue('item_bulk_pricing[' + row + '][unit_sale_price]', nPrice);
    if (doAll) changeAllDiscountRows(percent, true);
}
function changeAllDiscountRows(percent, doSpec) {
    if (doSpec == null) doSpec = false;
    var all_rows = ProductBulkPricing.rows();
    var prefix = 'item_bulk_pricing';
    for (i = 0; i < all_rows.length; i++) {
        var tmp = all_rows[i];
        var num = tmp.substr(1, tmp.length - 2);
        setValue('bulkD' + tmp, percent);
        bDiscountChanged(num, percent, false);
        item_bulk_pricing_fieldChanged(num, 'unit_sale_price', getValue(prefix + tmp + '[unit_sale_price]'));
    }
    if (doSpec) {
        //now have to update the actual special discount of the product..
        getElement('product[special_flag]').checked = (percent > 0);
        toggleSection('specials', (percent > 0));
        toggleSection('specials2', (percent > 0));
        setValue('percent', percent);
        calculateSpecialPrice(percent);
    }
}
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionBulkPricing&quot;);return false" class="unfinished">Bulk Pricing</a>', '<a href="#" onclick="item_bulk_pricing_add();return false">Add New</a>')?>

<div id="sectionBulkPricing">
    <table class="table_grid" id="item_bulk_pricing_table">
        <tr>
            <th style="width:5%;">Qty</th>
            <th style="width:13%;">Unit Bulk<br />Price (USD)</th>
            <th style="width:13%;">Unit Special<br />Price (USD)</th>
            <th style="width:13%;">Sale<br />Discount (%)</th>
            <th style="width:13%;">Unit Cost<br />Price (USD)</th>
            <th style="width:13%;">Unit Profit<br />(USD)</th>
            <th style="width:13%;">Total Profit<br />(USD)</th>
            <th style="width:10%;">Markup %</th>
            <th></th>
        </tr>
<?php
    $count2 = 0;
    foreach ($item_bulk_pricing as $temp) {
        $temp['unit_sale_price']    = bcadd('0.0000', $temp['unit_sale_price'], $item['precision']);
        $temp['unit_cost_price']    = $temp['unit_cost_price'];
        $temp['unit_profit']        = bcsub($temp['unit_sale_price'], $temp['unit_cost_price'], $item['precision']);
        $temp['total_profit']       = bcmul($temp['unit_profit'], $temp['qty'], $item['precision']);
        $temp['markup']             = (bccomp($temp['unit_cost_price'], '0.0000', 2)) ? bcdiv(bcmul($temp['unit_profit'], "100.0", 2), $temp['unit_cost_price'], 2) : '100.00';
        echo item_bulk_pricing_row($count2, $temp);
        $count2++;
    }
    unset($temp);
    unset($count2);
?>
    </table>
    <input type="hidden" name="item_bulk_pricing_deleted" value="" />
    <br /><br />
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionBulkPricing_hideNote">
    Bulk Pricing can be viewed by Clicking "Bulk Pricing" above<br />
</div>

<!-- /_bulk_pricing.tpl -->

<!-- stock_additions.tpl -->

<?php
$show_usd_aud = 'USD';

function stock_additions_row($count = '[ROW_COUNT]', $row_data = array())
{
    global $show_usd_aud;
    $name_key = 'stock_additions[' . $count . ']';
    $hidden   = array(
        $name_key . '[id]'         => $row_data['id'],
        $name_key . '[product_id]' => $row_data['product_id'],
        $name_key . '[color_id]' => $row_data['color_id'],
    );
    ob_start();
?>
    <tr id="stock_additions_rowa[<?=$count?>]"<?=(($row_data['hidden']) ? ' style="display:none;"' : '')?>>
        <td style="white-space:nowrap;">
            <?=formatHTMLInputs($hidden)?>
            <input class="required" type="text" name="<?=$name_key?>[product_str]" value="<?php hecho($row_data['product_id'])?>" size="8" onchange="stockAdditionsChange('<?=$count?>', 'product_str')" />
            <input type="button" value="S" onclick="stockAdditionsSearch('<?=$count?>')" />
        </td>
        <td style="width:100%;"><span name="<?=$name_key?>[product_name]"><?php hecho($row_data['product_name'])?></span></td>
        <td><input class="required" style="text-align:right;" type="text" name="<?=$name_key?>[product_qty]" value="<?php hecho($row_data['item_qty'])?>" size="4" onchange="stockAdditionsChange('<?=$count?>', 'item_qty')" /></td>
        <td><input class="required" style="text-align:right;" type="text" name="<?=$name_key?>[unit_cost]" value="<?php hecho($row_data['unit_cost'])?>" size="8" onchange="stockAdditionsChange('<?=$count?>', 'unit_cost')" /></td>
        <td style="text-align:right;<?=('USD' == $show_usd_aud) ? '' : 'display:none;'?>"><span name="<?=$name_key?>[unit_costus]"><?php hecho($row_data['unit_costus'])?></span></td>
        <td style="text-align:right;<?=('AUD' == $show_usd_aud) ? '' : 'display:none;'?>"><span name="<?=$name_key?>[unit_costau]"><?php hecho($row_data['unit_costau'])?></span></td>
        <td><input style="text-align:right;" type="text" readonly="readonly" name="<?=$name_key?>[product_weight]" value="<?php hecho($row_data['item_weight'])?>" size="8" onchange="stockAdditionsChange('<?=$count?>', 'item_weight')" /></td>
        <td><input class="required" style="text-align:right;" type="text" name="<?=$name_key?>[unit_freight]" value="<?php hecho($row_data['unit_freight'])?>" size="8" onchange="stockAdditionsChange('<?=$count?>', 'unit_freight')" /></td>
        <td style="text-align:right;<?=('USD' == $show_usd_aud) ? '' : 'display:none;'?>"><span name="<?=$name_key?>[unit_freightus]"><?php hecho($row_data['unit_freightus'])?></span></td>
        <td style="text-align:right;<?=('AUD' == $show_usd_aud) ? '' : 'display:none;'?>"><span name="<?=$name_key?>[unit_freightau]"><?php hecho($row_data['unit_freightau'])?></span></td>
        <td><a href="#" onclick="return stockAdditionsDelete(<?=$count?>)"><i class="fas fa-trash-alt"></i></a></td>
    </tr>
<?php
return ob_get_clean();
}
if (is_array($stock_additions)) {
?>
<script type="text/javascript">
//<![CDATA[
var stock_additions_count = <?=count($stock_additions) + 256?>;
var search_references = [];
function setProductData(dataval) {
    var data=dataval.item_list[0];
    var row_id = dataval.row_id;
    console.log(data);
    data.weight=(data.weight)?data.weight:0;
    setValue('stock_additions[' + row_id + '][product_id]', data.product_id);
    setValue('stock_additions[' + row_id + '][product_str]', data.product_id);
    setValue('stock_additions[' + row_id + '][product_name]', data.product_name);
    setValue('stock_additions[' + row_id + '][product_qty]', 1);
    setValue('stock_additions[' + row_id + '][unit_cost]', data.product_offer);
    setValue('stock_additions[' + row_id + '][unit_costus]', data.product_offerus);
    setValue('stock_additions[' + row_id + '][unit_costau]', data.product_offerau);
    setValue('stock_additions[' + row_id + '][product_weight]', data.weight);
    setValue('stock_additions[' + row_id + '][product_name]', data.product_name);
    setValue('stock_additions[' + row_id + '][color_id]', data.color_id);
}
function stockAdditionsSort(column) {
    var rows = [];
    var vals = [];
    $("[id^='stock_additions_rowa[']").each(function() {
        var row_id = $(this).attr("id").substr(20);
        rows.push(row_id);
        vals.push($("[name='stock_additions" + row_id + "[" + column + "]']").text());
    });
    var reverse = true;
    var bubble = true;
    while (bubble) {
        bubble = false;
        for (var i = 0; i < (rows.length - 1); i++)
            for (var j = i + 1; j < rows.length; j++)
                if (vals[i] > vals[j]) {
                    temp = rows[i];
                    rows[i] = rows[j];
                    rows[j] = temp;
                    temp = vals[i];
                    vals[i] = vals[j];
                    vals[j] = temp;
                    bubble = true;
                    reverse = false;
                }
    }
    if (reverse) rows = rows.reverse();
    var table = $("#stock_additions_table");
    for (var i = 0; i < rows.length; i++) {
        table.append($("[id='stock_additions_rowa" + rows[i] + "']"));
    }
    var count = 0;
    $("[id^='stock_additions_rowa[']").each(function() {
        $("[name$='[rank]']", this).val(count++);
    });
    $("[id^='stock_additions_rowa']").removeClass("alt").filter(":odd").addClass("alt");
    return false;
}
function stockAdditionsDelete(row_id) {
    if (confirm("Are you sure you want to delete this product ??")) {
        $("[id='stock_additions_rowa[" + row_id + "]']").remove();
        $("[id^='stock_additions_rowa']").removeClass("alt").filter(":odd").addClass("alt");
        $("[id^='stock_additions_rowa[']").first().each(function() {
            $("[name='stock_additions" + $(this).attr("id").substr(20) + "[product_qty]']").change();
        });
    }
    return false;
}
function stockAdditionsAddRow() {
    var row_string = <?php jecho(stock_additions_row())?>;
    row_string = row_string.replace(/\[ROW_COUNT\]/g, stock_additions_count);
    $("#stock_additions_table tr").last().before(row_string);
    $("[id^='stock_additions_rowa']").removeClass("alt").filter(":odd").addClass("alt");
    stock_additions_count++;
    return stock_additions_count - 1;
}
function stockAdditionsSearch(row_id) {
    var url = 'master_products.php';
    var options = {
        "search_for": $("[name='stock_additions[" + row_id + "][product_str]']").val(),
        "return_message": 'addstock',
        'row_id':row_id
    };
    search_references.push(row_id);
    window.open(url + "?" + $.param(options));
}
function stockAdditionsChange(row_id, field) {
    var name_key = "stock_additions[" + row_id + "]";
    var rows = [];
    $("[id^='stock_additions_rowa[']").each(function() {
        rows.push($(this).attr("id").substr(20));
    });
    switch (field) {
        case 'product_str':
            var options = {
                "action": "productFromDatabase",
                "return_row_id": row_id,
                "id": $("[name='stock_additions[" + row_id + "][product_str]']").val()
            };
            $.ajaxSetup({
                "cache": false
            });
            $.getJSON("/webadmin/products/ajax_request.php", options, function(data, textStatus) {
                if (textStatus == 'success') {
                    var row_id = data.return_row_id;
                    if (data.result.id == null || data.result.site_id == null || !in_array("<?=intval($_GLOBALS['site_settings']['site_id'])?>", data.result.site_id.split(","))) {
                        $("[name='stock_additions[" + row_id + "][product_name]']").empty();
                        $("[name='stock_additions[" + row_id + "][product_weight]']").val("").change();
                        return;
                    }
                    $("[name='stock_additions[" + row_id + "][product_str]']").val(data.result.product_str);
                    $("[name='stock_additions[" + row_id + "][product_name]']").empty().text(data.result.product_name).wrapInner('<span/>').children().hover(function() {
                        tt_in('<img src="' + data.result.thumb_url + '" alt="' + data.result.product_name + '" style="max-width:96px;max-height:96px;" />');
                    }, tt_out);
                    if (data.result.product_weight == null) {
                        options["action"] = "productDimensionFromDatabase";
                        $.getJSON("/webadmin/products/ajax_request.php", options, function(data, textStatus) {
                            if (textStatus == 'success') {
                                var row_id = data.return_row_id;
                                $("[name='stock_additions[" + row_id + "][product_weight]']").val((data.result.length > 0) ? data.result[0].online_weight : "").change();
                            } else alert('There was a problem fetching that request: '.textStatus);
                        });
                    } else $("[name='stock_additions[" + row_id + "][product_weight]']").val(data.result.product_weight);
                } else alert('There was a problem fetching that request: '.textStatus);
            });
            break;
        case 'unit_cost':
            var currency = $("[name='stock[currency]']").val();
            var calc_cost = 0.0;
            if (currency != null && currency != "") {
                for (var i = 0; i < rows.length; i++) {
                    var qty = parseInt($("[name='stock_additions" + rows[i] + "[product_qty]']").val());
                    if (isNaN(qty)) qty = 0;
                    var cost = parseFloat($("[name='stock_additions" + rows[i] + "[unit_cost]']").val());
                    if (isNaN(cost)) cost = 0.0;
                    calc_cost += qty * cost;
                }
                $("[name='stock_additions_cost']").text(convertCurrency(calc_cost, currency)).add("[name='stock[total_cost]']").val(convertCurrency(calc_cost, currency));
                $("[name='stock_additions_costus']").text(convertCurrency(calc_cost, currency, 'USD')).val(convertCurrency(calc_cost, currency, 'USD'));
                $("[name='stock_additions_costau']").text(convertCurrency(calc_cost, currency, 'AUD')).val(convertCurrency(calc_cost, currency, 'AUD'));
                var cost = parseFloat($("[name='stock_additions[" + row_id + "][unit_cost]']").val());
                $("[name='stock_additions[" + row_id + "][unit_costus]']").text(convertCurrency(cost, currency, 'USD')).val(convertCurrency(cost, currency, 'USD'));
                $("[name='stock_additions[" + row_id + "][unit_costau]']").text(convertCurrency(cost, currency, 'AUD')).val(convertCurrency(cost, currency, 'AUD'));
            }
            break;
        case 'item_qty':
            var calc_qty = 0;
            for (var i = 0; i < rows.length; i++) {
                var qty = parseInt($("[name='stock_additions" + rows[i] + "[product_qty]']").val());
                if (isNaN(qty)) qty = 0;
                calc_qty += qty;
            }
            $("[name='stock_additions_qty']").text(calc_qty).val(calc_qty);
            $("[name='stock_additions[" + row_id + "][product_weight]']").change();
            $("[name='stock_additions[" + row_id + "][unit_cost]']").change();
            break;
        case 'item_weight':
            var calc_weight = 0.0;
            for (var i = 0; i < rows.length; i++) {
                var qty = parseInt($("[name='stock_additions" + rows[i] + "[product_qty]']").val());
                var weight = parseFloat($("[name='stock_additions" + rows[i] + "[product_weight]']").val());
                if (isNaN(qty)) qty = 0;
                if (isNaN(weight)) weight = 0.0;
                calc_weight += qty * weight;
            }
            var stock_weight = parseFloat($("[name='stock[total_weight]']").val());
            $("[name='stock_additions_weight']").text(sprintf('%0.3f', calc_weight)).add("[name='stock[calc_weight]']").val(sprintf('%0.3f', calc_weight)).css("color", (calc_weight == stock_weight) ? "green" : "red");
            var currency = $("[name='stock[currency]']").val();
            var qty = parseInt($("[name='stock_additions[" + row_id + "][product_qty]']").val());
            var weight = parseFloat($("[name='stock_additions[" + row_id + "][product_weight]']").val());
            var stock_freight = parseFloat($("[name='stock[freight_cost]']").val());
            if (isNaN(stock_weight)) stock_weight = 0.0;
            if (isNaN(stock_freight)) stock_freight = 0.0;
            if (currency != null && currency != "" && stock_weight != 0) {
                var freight = convertCurrency(stock_freight * weight / stock_weight, currency);
                $("[name='stock_additions[" + row_id + "][unit_freight]']").text(freight).val(freight).change();
            }
            break;
        case 'unit_freight':
            var currency = $("[name='stock[currency]']").val();
            var calc_freight = 0.0;
            var stock_freight = parseFloat($("[name='stock[freight_cost]']").val());
            if (currency != null && currency != "") {
                for (var i = 0; i < rows.length; i++) {
                    var qty = parseInt($("[name='stock_additions" + rows[i] + "[product_qty]']").val());
                    var freight = parseFloat($("[name='stock_additions" + rows[i] + "[unit_freight]']").val());
                    calc_freight += qty * freight;
                }
                $("[name='stock_additions_freight']").text(convertCurrency(calc_freight, currency)).val(convertCurrency(calc_freight, currency)).css("color", (calc_freight == stock_freight) ? "green" : "red");
                $("[name='stock_additions_freightus']").text(convertCurrency(calc_freight, currency, 'USD')).val(convertCurrency(calc_freight, currency, 'USD'));
                $("[name='stock_additions_freightau']").text(convertCurrency(calc_freight, currency, 'AUD')).val(convertCurrency(calc_freight, currency, 'AUD'));
                var freight = parseFloat($("[name='stock_additions[" + row_id + "][unit_freight]']").val());
                $("[name='stock_additions[" + row_id + "][unit_freightus]']").text(convertCurrency(freight, currency, 'USD')).val(convertCurrency(freight, currency, 'USD'));
                $("[name='stock_additions[" + row_id + "][unit_freightau]']").text(convertCurrency(freight, currency, 'AUD')).val(convertCurrency(freight, currency, 'AUD'));
            }
            break;
    }
}
$(function() {
    $.getScript('/jquery/selectboxes&skipdeps.js');
    var list_storage = {};
    var list_schedule = [];
    processSchedule = function() {
        for (i = 0; i < list_schedule.length; i++) {
            var j = list_schedule[i];
            if (list_storage[j.rel] !== null) {
                var save_vals = $(j.target).val();
                $(j.target).empty();
                $(j.target).addOption('', '');
                $(j.target).addOption(list_storage[j.rel]);
                $(j.target).val(save_vals);
                list_schedule.splice(i, 1);
                i--;
            }
        }
    };
    $("select.on_demand").focus(function(event) {
        $(this).unbind("focus");
        var list_name = $(this).attr('rel');
        list_schedule.push({
            target: this,
            rel: list_name
        });
        if (list_storage[list_name] == null) $.getJSON('getoptionlist.json.php', {
            list: escape(list_name)
        }, function(data) {
            list_storage[list_name] = data;
            processSchedule();
        });
        else processSchedule();
    });
    $('.stock_additions_add_button').css('display', '').click(function() {
        stockAdditionsAddRow();
        return false;
    });
    $("[name='stock_additions_expand_freight']").css('display', '').click(function() {
        var currency = $("[name='stock[currency]']").val();
        var stock_weight = parseFloat($("[name='stock_additions_weight']").text());
        var stock_freight = parseFloat($("[name='stock[freight_cost]']").val());
        if (isNaN(stock_weight)) stock_weight = 0.0;
        if (isNaN(stock_freight)) stock_freight = 0.0;
        var rows = [];
        $("[id^='stock_additions_rowa[']").each(function() {
            rows.push($(this).attr("id").substr(20));
        });
        for (var i = 0; i < rows.length; i++) {
            var weight = parseFloat($("[name='stock_additions" + rows[i] + "[product_weight]']").val());
            if (isNaN(weight)) weight = 0.0;
            if (currency != null && currency != "" && stock_weight != 0) $("[name='stock_additions" + rows[i] + "[unit_freight]']").val(convertCurrency(stock_freight * weight / stock_weight, currency)).change();
        }
    });
});
//]]>
</script>

<?=formatHTMLTitle('Stock Additions', '<a href="#" class="stock_additions_add_button" style="display:none;">Add Product</a>')?>

<div id="sectionProducts">
    <table class="table_grid" id="stock_additions_table">
        <tr>
            <th><a href="#" onclick="return stockAdditionsSort('product_str')">Product ID</a></th>
            <th style="width:100%;"><a href="#" onclick="return stockAdditionsSort('product_name')">Product Name</a></th>
            <th><a href="#" onclick="return stockAdditionsSort('item_qty')">Qty</a></th>
            <th><a href="#" onclick="return stockAdditionsSort('unit_costus')">Unit Cost Price<br />(<span name="stock[currency]"><?=$stock['currency']?></span>)</a></th>
            <th<?=('USD' == $show_usd_aud) ? '' : ' style="display:none;"'?>><a href="#" onclick="return stockAdditionsSort('unit_costus')">Unit Cost Price<br />(USD)</a></th>
            <th<?=('AUD' == $show_usd_aud) ? '' : ' style="display:none;"'?>><a href="#" onclick="return stockAdditionsSort('unit_costus')">Unit Cost Price<br />(AUD)</a></th>
            <th><a href="#" onclick="return stockAdditionsSort('item_weight')">Unit Weight<br />(kgs)</a></th>
            <th><a href="#" onclick="return stockAdditionsSort('freight_costus')">Unit Freight Cost<br />(<span name="stock[currency]"><?=$stock['currency']?></span>)</a></th>
            <th<?=('USD' == $show_usd_aud) ? '' : ' style="display:none;"'?>><a href="#" onclick="return stockAdditionsSort('freight_costus')">Unit Freight Cost<br />(USD)</a></th>
            <th<?=('AUD' == $show_usd_aud) ? '' : ' style="display:none;"'?>><a href="#" onclick="return stockAdditionsSort('freight_costus')">Unit Freight Cost<br />(AUD)</a></th>
            <th></th>
        </tr>
<?php
$count                    = 0;
    $totals                   = array();
    foreach ($stock_additions as $stock_addition) {
        $product_id                 = $stock_addition['product_id'];
        $item                       = productFromDatabase($product_id);
        $item['thumb_url']          = '/products/images/' . $stock_addition['product_id'] . '_sml.jpg';
        $stock_addition             = array_merge($item, $stock_addition);
        $totals['item_qty'] += $stock_addition['item_qty'];
        $totals['unit_cost'] += $stock_addition['unit_cost'] * $stock_addition['item_qty'];
        $totals['unit_costus'] += $stock_addition['unit_costus'] * $stock_addition['item_qty'];
        $totals['unit_costau'] += $stock_addition['unit_costau'] * $stock_addition['item_qty'];
        $totals['item_weight'] += $stock_addition['item_weight'] * $stock_addition['item_qty'];
        $totals['unit_freight'] += $stock_addition['unit_freight'] * $stock_addition['item_qty'];
        $totals['unit_freightus'] += $stock_addition['unit_freightus'] * $stock_addition['item_qty'];
        $totals['unit_freightau'] += $stock_addition['unit_freightau'] * $stock_addition['item_qty'];
        echo stock_additions_row($count++, $stock_addition, true);
    }
?>
        <tr>
            <th></th>
            <th style="text-align:right;">TOTALS:</th>
            <th style="text-align:right;"><span name="stock_additions_qty"><?php printf("%d", $totals['item_qty'])?></span></th>
            <th style="text-align:right;"><span name="stock_additions_cost"><?=convertCurrency($totals['unit_cost'], $stock['currency'])?></span></th>
            <th style="text-align:right;<?=('USD' == $show_usd_aud) ? '' : 'display:none;'?>"><span name="stock_additions_costus"><?=convertCurrency($totals['unit_costus'], $stock['currency'])?></span></th>
            <th style="text-align:right;<?=('AUD' == $show_usd_aud) ? '' : 'display:none;'?>"><span name="stock_additions_costau"><?=convertCurrency($totals['unit_costau'], $stock['currency'])?></span></th>
            <th style="text-align:right;"><span name="stock_additions_weight"><?php printf("%0.3f", $totals['item_weight'])?></span></th>
            <th style="text-align:right;"><span name="stock_additions_freight"><?=convertCurrency($totals['unit_freight'], $stock['currency'])?></span></th>
            <th style="text-align:right;<?=('USD' == $show_usd_aud) ? '' : 'display:none;'?>"><span name="stock_additions_freightus"><?=convertCurrency($totals['unit_freightus'], $stock['currency'])?></span></th>
            <th style="text-align:right;<?=('AUD' == $show_usd_aud) ? '' : 'display:none;'?>"><span name="stock_additions_freightau"><?=convertCurrency($totals['unit_freightau'], $stock['currency'])?></span></th>
            <th></th>
        </tr>
    </table>
    <br />
    <input type="hidden" name="stock_additions_deleted" value="" />
    <div style="width:98%;text-align:right;"><input type="button" style="display:none;" name="stock_additions_expand_freight" value="Automatically adjust freight costs" /></div>
    <br />
    <br />

</div>

<?php
}
?>

<!-- /stock_additions.tpl -->

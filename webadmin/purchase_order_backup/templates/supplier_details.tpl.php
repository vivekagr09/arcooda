<!-- supplier_details.tpl -->
<?php
moduleLoad('product');
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
    $("[name='stock[currency]']").change(function() {
        var new_val = $(this).val();
       // $("[name='stock[currency]']").not(this).text(new_val).val(new_val).change();
        $("[name^='stock_additions['][name$='][unit_cost]']").change();
        $("[name^='stock_additions['][name$='][unit_freight]']").change();
    });
    $("[name='stock[freight_cost]']").add("[name='stock[total_weight]']").change(function() {
        $("[name^='stock_additions['][name$='][product_weight]']").change();
    });
});
//]]>
</script>
    <table class="wide">
        <tr>
            <td class="left_title"style="width:25%;">Supplier Name </br><a href="/webadmin/supplier_new">Add/Edit</a></td>
            <td>
                <select class="required" name="stock[supplier_id]">
                    <option value=""></option>
                    <?=formatHTMLOptions(purchaseOrderGetOptionList('supplier'), $invoice['supplier_id'])?>
                </select>
                
            </td>
        </tr>
        <tr>
            <td class="left_title">Invoice #/Reference #</td>
            <td><input class="required wide" type="text" name="stock[reference]" value="<?php hecho($stock['reference'])?>" /></td>
        </tr>
        <tr>
            <td class="left_title">Comments</td>
            <td><?=formatHTMLTextarea('stock[comment]', $stock['comment'], true, 'class="wide"', 1)?></td>
        </tr>
        <tr>
            <td class="left_title">Invoice Currency</td>
            <td><select class="required" name="stock[currency]">
                <option value=""></option>
                <?=formatHTMLOptions(productGetOptionList('cost_currency'), $invoice['invoice_currency'])?>
            </select></td>
        </tr>
        <tr>
            <td class="left_title">Total Freight Cost</td>
            <td>
                <input class="required" type="text" name="stock[freight_cost]" value="<?php hecho($invoice['freight_cost'])?>" />
                <span name="stock[currency]"><?=$invoice['invoice_currency']?></span>
            </td>
        </tr>
        <tr>
            <td class="left_title">Invoice Shipping Weight</td>
            <td><input class="required" type="text" name="stock[total_weight]" value="<?php hecho($stock['total_weight'])?>" /></td>
        </tr>
    </table>

<input type="hidden" name="stock[calc_weight]" value="<?php hecho($stock['calc_weight'])?>" />
<input type="hidden" name="stock[total_cost]" value="<?php hecho($stock['total_cost'])?>" />

<!-- /supplier_details.tpl -->

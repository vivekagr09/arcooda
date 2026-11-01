<!-- supplier_details.tpl -->

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

<?=formatHTMLTitle("Supplier Details")?>

<div id="sectionSupplierDetails">
    <table class="table_fields">
        <tr>
            <th style="width:25%;">Supplier Name</th>
            <td>
                <select class="required" name="stock[supplier_id]">
                    <option value=""></option>
                    <?=formatHTMLOptions(productGetOptionList('supplier'), $stock['supplier_id'])?>
                </select>
                <a href="/webadmin/supplier_new/list.php" target="_blank">Add/Edit</a>
            </td>
        </tr>
        <tr>
            <th>Invoice #/Reference #</th>
            <td><input class="required wide" type="text" name="stock[reference]" value="<?php hecho($stock['reference'])?>" /></td>
        </tr>
        <tr>
            <th>Comments</th>
            <td><?=formatHTMLTextarea('stock[comment]', $stock['comment'], true, 'class="wide"', 1)?></td>
        </tr>
        <tr>
            <th>Invoice Currency</th>
            <td><select class="required" name="stock[currency]">
                <option value=""></option>
                <?=formatHTMLOptions(productGetOptionList('cost_currency'), $stock['currency'])?>
            </select></td>
        </tr>
        <tr>
            <th>Total Freight Cost</th>
            <td>
                <input class="required" type="text" name="stock[freight_cost]" value="<?php hecho($stock['freight_cost'])?>" />
                <span name="stock[currency]"><?=$stock['currency']?></span>
            </td>
        </tr>
        <tr>
            <th>Invoice Shipping Weight</th>
            <td><input class="required" type="text" name="stock[total_weight]" value="<?php hecho($stock['total_weight'])?>" /></td>
        </tr>
    </table>
    <br />
</div>

<input type="hidden" name="stock[calc_weight]" value="<?php hecho($stock['calc_weight'])?>" />
<input type="hidden" name="stock[total_cost]" value="<?php hecho($stock['total_cost'])?>" />

<!-- /supplier_details.tpl -->

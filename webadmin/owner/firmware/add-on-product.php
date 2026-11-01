<!-- featured_products.tpl -->

<?php
moduleLoad('product');
$addOnProduct = getProductfirmware($firmware['id']);
function add_on_products_row($count = '[ROW_COUNT]', $row_data = array())
{
    global $DB;
    $name_key = 'item_addon[' . $count . ']';
    $hidden   = array($name_key . '[id]' => $row_data['id'], $name_key . '[product_id]' => $row_data['product_id']);
    ob_start();
?>
<tr id="add_on_products_rowa[<?=$count?>]" style="text-align:center;<?= $row_data['is_free'] == 'y' ? '    background: #9ff40f2b;':'' ?>;"<?=(is_int($count) && ($count & 1)) ? ' class="alt add_on_linking_row"' : 'class="add_on_linking_row"'?>>
    <td><?=str_replace("\n", "", formatHTMLInputs($hidden))?> <a href="#" onclick="return upgradeAddonMoveUp(this);"><img src="/webadmin/images/up_arrow.gif" alt="↑" border="0"></a> <a href="#" onclick="return upgradeAddonMoveDown(this);"><img src="/webadmin/images/down_arrow.gif" alt="↓" border="0"></a> <input type="hidden" class="row_count" name="<?=$name_key ?>[rank]" value="<?= $count ?>"> </td>
    <td><a href="/webadmin/products_new/edit.php?action=do_edit&id=<?=$row_data['product_id'] ?> target="_blank"><img src="<?=$row_data['thumb_url']?>" style="max-width:96px;max-height:96px;" /></a></td>
    <td>#<?php hecho($row_data['product_id'])?></td>
    <td><a href="/webadmin/products_new/edit.php?action=do_edit&id=<?=$row_data['product_id'] ?>" target="_blank"><?php hecho($row_data['product_name'])?></a>
    </td>
    <td> <input type="button" value="Delete" onclick="addon_products_delete(<?=$count?>,<?= $row_data['id'] ?>)" /></td>
</tr>
<?php
return ob_get_clean();
}
if (is_array($addOnProduct)) {
?>
<script type="text/javascript">
//<![CDATA[
function upgradeAddonMoveUp(elem) {
    var row = $(elem).parents(".add_on_linking_row");

    if (row.prev().hasClass("add_on_linking_row")) {
        var n = row.prev().find(".row_count").val();
        row.prev().find(".row_count").val(row.find(".row_count").val());
        row.find(".row_count").val(n);
        row.prev().before(row);
    }

    return false;
}

function upgradeAddonMoveDown(elem) {
    var row = $(elem).parents(".add_on_linking_row");

    if (row.next().hasClass("add_on_linking_row")) {
        var n = row.next().find(".row_count").val();
        row.next().find(".row_count").val(row.find(".row_count").val());
        row.find(".row_count").val(n);
        row.next().after(row);
    }

    return false;
}
function add_on_products_data(dataarray){
    console.log(dataarray);
    dataarray.forEach(add_on_products_data_byone);
} 
var addOn_products_count = <?=count($addOnProduct)?>;
function add_on_products_data_byone(data) {
    var tmp = Array();
    for (key in data['bulk_weights']) tmp[tmp.length] = data['bulk_weights'][key]['bulk_qty'];
    var match = true;
    if (match) {
        if (data['product_id'] == null) data['product_id'] = data['id'];
        var $row = $("table#add_on_products").append($(<?=strtr(jecho(add_on_products_row('[RELATED_COUNT]', array(
    'product_id'   => '[PRODUCT_ID]',
    'product_name' => '[PRODUCT_NAME]',
    'view_url'     => '[view_URL]',
    'thumb_url'    => '[THUMB_URL]',
)), 1), array(
    '[RELATED_COUNT]' => '" + addOn_products_count + "',
    '[PRODUCT_ID]'    => '" + data.product_id + "',
    '[PRODUCT_NAME]'  => '" + data.product_name + "',
    '[VIEW_URL]'      => '" + data.view_url + "',
    '[THUMB_URL]'     => '" + data.thumb_url + "',
))?>));
    }
    addOn_products_count++;
}
function addon_products_delete(id,dataid) {
    if (confirm("Are you sure you want to delete this Add on product ??")) {
        if(dataid)
        setValue("add_on_deleted", getValue("add_on_deleted") + "," + dataid);

        var theRow = getElement("add_on_products_rowa[" + id + "]");
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    }
}
$(function() {
    $('.add_on_add_button').click(function() {
        window.open("https://www.arcooda.com/webadmin/products_new?action=search2&show_sites=n&return_message=add_on_products&status=y&currency=USD&multiple=y&order_by=year_released&order=desc");
        return false;
    });
});
//]]>
</script>

<?=formatHTMLTitle('Linked Products', '<a href="#" class="add_on_add_button">Add Product(s)</a>')?>

<div id="sectionFeaturedProducts">
    <table class="table_grid" id="add_on_products">
        <tr>
            <!--
            <th></th>
            -->
            <th></th>
            <th></th>
            <th>ID</th>
            <th>Products to be Linked</th>
            <th></th>
        </tr>
<?php
    $count          = 0;
    foreach ($addOnProduct as $itemData) {
        $itempro  = productFromDatabase($itemData['product_id']);
         $itempro['product_id'] = $itemData['product_id'];
        $itempro['rank'] = $itemData['rank'];
        $itempro['id'] = $itemData['id'];
        echo add_on_products_row($count++, $itempro);
    }
?>
    </table>
        <input type="hidden" name="add_on_deleted" value="" />
    <br />
    <br />
</div>

<?php
}
?>
<!-- /featured_products.tpl -->

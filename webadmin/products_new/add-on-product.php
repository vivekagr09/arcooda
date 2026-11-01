<!-- featured_products.tpl -->

<?php
$addOnProduct = productAddonsFromDatabase($item['id']);
function add_on_products_row($count = '[ROW_COUNT]', $row_data = array())
{
    global $DB;
    $name_key = 'item_addon[' . $count . ']';
    $hidden   = array($name_key . '[id]' => $row_data['id'], $name_key . '[product_id]' => $row_data['product_id'], $name_key . '[related_id]' => $row_data['related_id']);
    ob_start();
?>
<tr id="add_on_products_rowa[<?=$count?>]" style="text-align:center;<?= $row_data['is_free'] == 'y' ? '    background: #9ff40f2b;':'' ?>;"<?=(is_int($count) && ($count & 1)) ? ' class="alt add_on_linking_row"' : 'class="add_on_linking_row"'?>>
    <td><?=str_replace("\n", "", formatHTMLInputs($hidden))?> <a href="#" onclick="return upgradeAddonMoveUp(this);"><img src="/webadmin/images/up_arrow.gif" alt="↑" border="0"></a> <a href="#" onclick="return upgradeAddonMoveDown(this);"><img src="/webadmin/images/down_arrow.gif" alt="↓" border="0"></a> <input type="hidden" class="row_count" name="<?=$name_key ?>[rank]" value="<?= $count ?>"> </td>
    <td><a href="<?=$row_data['view_url']?>" target="_blank"><img src="<?=$row_data['thumb_url']?>" style="max-width:96px;max-height:96px;" /></a></td>
    <td><a href="<?=$row_data['view_url']?>" target="_blank"><?php hecho($row_data['product_name'])?></a>
    <div style="margin-top: 10px;"><span ><svg style="width: 12px;" class="svg-inline--fa fa-question-circle fa-w-16" onmouseover="tt_in('This is the mouseover text online to the linked product');" onmouseout="tt_out();" aria-hidden="true" focusable="false" data-prefix="far" data-icon="question-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 448c-110.532 0-200-89.431-200-200 0-110.495 89.472-200 200-200 110.491 0 200 89.471 200 200 0 110.53-89.431 200-200 200zm107.244-255.2c0 67.052-72.421 68.084-72.421 92.863V300c0 6.627-5.373 12-12 12h-45.647c-6.627 0-12-5.373-12-12v-8.659c0-35.745 27.1-50.034 47.579-61.516 17.561-9.845 28.324-16.541 28.324-29.579 0-17.246-21.999-28.693-39.784-28.693-23.189 0-33.894 10.977-48.942 29.969-4.057 5.12-11.46 6.071-16.666 2.124l-27.824-21.098c-5.107-3.872-6.251-11.066-2.644-16.363C184.846 131.491 214.94 112 261.794 112c49.071 0 101.45 38.304 101.45 88.8zM298 368c0 23.159-18.841 42-42 42s-42-18.841-42-42 18.841-42 42-42 42 18.841 42 42z" ></path></svg></span>
        <input type="text" style="width: 80%;" name="<?=$name_key ?>[addontext]" value="<?= $row_data['addontext']?>"></div>
    </td>
    <td><?php 

    hecho($row_data['sales_price'] ? ($row_data['sales_price'].' USD'):'')?>
    </br>
     <input type="checkbox" name="<?=$name_key ?>[is_free]" <?= $row_data['is_free'] == 'y'? 'checked': '' ?> value="y">Free   

    </td>
    <td><input type="button" value="Edit" onclick="window.open('.?action=edit&amp;id=<?=$row_data['related_id']?>')" /> <input type="button" value="Delete" onclick="addon_products_delete(<?=$count?>,<?= $row_data['id'] ?>)" /></td>
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
    var weightRows = [];
    if (ProductBulkWeight !== undefined) {
        weightRows = ProductBulkWeight.rows();
    }
    var tmp = Array();
    for (key in data['bulk_weights']) tmp[tmp.length] = data['bulk_weights'][key]['bulk_qty'];
    var match = true;
    if (match) {
        if (data['product_id'] == null) data['product_id'] = data['id'];
        var $row = $("table#add_on_products").append($(<?=strtr(jecho(add_on_products_row('[RELATED_COUNT]', array(
    'product_id'   => $item['id'],
    'related_id'   => '[RELATED_ID]',
    'product_name' => '[PRODUCT_NAME]',
    'sales_price'  => '[SALES_PRICE]',
    'view_url'     => '[view_URL]',
    'thumb_url'    => '[THUMB_URL]',
)), 1), array(
    '[RELATED_COUNT]' => '" + addOn_products_count + "',
    '[RELATED_ID]'    => '" + data.product_id + "',
    '[PRODUCT_NAME]'  => '" + data.product_name + "',
    '[SALES_PRICE]'  => '" + data.consignment_sales_price + "',
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
        window.open(".?action=search2&show_sites=n&return_message=add_on_products&status=y&currency=USD&multiple=y&order_by=year_released&order=desc");
        return false;
    });
});
//]]>
</script>

<?=formatHTMLTitle('Add on Products', '<a href="#" class="add_on_add_button">Add Product(s)</a>')?>

<div id="sectionFeaturedProducts">
    <table class="table_grid" id="add_on_products">
        <tr>
            <!--
            <th></th>
            -->
            <th></th>
            <th></th>
            <th>Add-On Products to be Linked</th>
            <th>Price USD</th>
            <th></th>
        </tr>
<?php
    $count          = 0;
    foreach ($addOnProduct as $itemData) {
        $itempro             = productFromDatabase($itemData['related_id']);
        $itempro['addontext'] = $itemData['addontext'];
        $itempro['rank'] = $itemData['rank'];
        $itempro['related_id'] = $itemData['related_id'];
        $itempro['id'] = $itemData['id'];
        $itempro['is_free'] = $itemData['is_free'];
        if($itempro['stock_type'] == 'consignment'){
            $itempro['sales_price'] = number_format($itempro['consignment_sales_price'],2);
        }else{
             $itempro['sales_price'] = number_format($itempro['sales_price'],2);
        }
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

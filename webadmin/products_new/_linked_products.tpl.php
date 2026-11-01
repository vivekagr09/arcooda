<!-- _linked_products.tpl -->

<?php
/**
 * Related Products Template
 *
 * Inputs:
 * - $item_related
 */

if (!isset($item_related) || !is_array($item_related)) {
    return;
}

function linked_products_row($count = '[ROW_COUNT]', $row_data = array())
{
    global $DB;
    $name_key = 'item_related[' . $count . ']';
    $hidden   = array($name_key . '[id]' => $row_data['id'], $name_key . '[product_id]' => $row_data['product_id'], $name_key . '[related_id]' => $row_data['related_id']);
    ob_start();
?>
<tr id="linked_products_rowa[<?=$count?>]" style="text-align:center;"<?=(is_int($count) && ($count & 1)) ? ' class="alt relative_linking_row"' : 'class="relative_linking_row"'?>>
    <td><?=str_replace("\n", "", formatHTMLInputs($hidden))?> <a href="#" onclick="return upgraderelativeMoveUp(this);"><img src="/webadmin/images/up_arrow.gif" alt="↑" border="0"></a> <a href="#" onclick="return upgraderelativeMoveDown(this);"><img src="/webadmin/images/down_arrow.gif" alt="↓" border="0"></a> <input type="hidden" class="row_count" name="<?=$name_key ?>[rank]" value="<?= $count ?>"> </td>
    <td><a href="<?=$row_data['view_url']?>" target="_blank"><img src="<?=$row_data['thumb_url']?>" style="max-width:96px;max-height:96px;" /></a></td>
    <td><a href=".?action=edit&amp;id=<?=$row_data['related_id']?>" target="_blank"><?php hecho($row_data['product_name'])?></a></td>
    <td><input type="button" value="View" onclick="window.open('.?action=edit&amp;id=<?=$row_data['related_id']?>')" /> <input type="button" value="Delete" onclick="linked_products_delete(<?=$count?>,<?= $row_data['id'] ?>)" /></td>
</tr>
<?php
return ob_get_clean();
}
?>

<script type="text/javascript">
//<![CDATA[
function upgraderelativeMoveUp(elem) {
    var row = $(elem).parents(".relative_linking_row");

    if (row.prev().hasClass("relative_linking_row")) {
        var n = row.prev().find(".row_count").val();
        row.prev().find(".row_count").val(row.find(".row_count").val());
        row.find(".row_count").val(n);
        row.prev().before(row);
    }

    return false;
}

function upgraderelativeMoveDown(elem) {
    var row = $(elem).parents(".relative_linking_row");

    if (row.next().hasClass("relative_linking_row")) {
        var n = row.next().find(".row_count").val();
        row.next().find(".row_count").val(row.find(".row_count").val());
        row.find(".row_count").val(n);
        row.next().after(row);
    }

    return false;
}
function linked_products_data(dataarray){
    dataarray.item_list.forEach(linked_products_data_byone);
} 
var linked_products_count = <?=count($item_related)?>;
function linked_products_data_byone(data) {
    console.log(data);
    var weightRows = [];
    if (ProductBulkWeight !== undefined) {
        weightRows = ProductBulkWeight.rows();
    }
    var tmp = Array();
    for (key in data['bulk_weights']) tmp[tmp.length] = data['bulk_weights'][key]['bulk_qty'];
    var match = true;
    if (match) {
        if (data['product_id'] == null) data['product_id'] = data['id'];
        var $row = $("table#linked_products").append($(<?=strtr(jecho(linked_products_row('[RELATED_COUNT]', array(
    'product_id'   => $item['id'],
    'related_id'   => '[RELATED_ID]',
    'product_name' => '[PRODUCT_NAME]',
    'view_url'     => '[view_URL]',
    'thumb_url'    => '[THUMB_URL]',
)), 1), array(
    '[RELATED_COUNT]' => '" + linked_products_count + "',
    '[RELATED_ID]'    => '" + data.product_id + "',
    '[PRODUCT_NAME]'  => '" + data.product_name + "',
    '[VIEW_URL]'      => '" + data.view_url + "',
    '[THUMB_URL]'     => '" + data.thumb_url + "',
))?>));
    }
    linked_products_count++;
}
function linked_products_delete(id,dataid) {
    if (confirm("Are you sure you want to delete this linked product ??")) {
        if(dataid)
        setValue("item_related_deleted", getValue("item_related_deleted") + "," + dataid);

        var theRow = getElement("linked_products_rowa[" + id + "]");
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    }
}
function linked_products_add() {
    var url = "master_products.php?return_message=related";
    url += "&multiple=y";
    url += "&linked_id=<?=$item['id']?>";
    url += "&callback=linked_products_data";
    url += "&search_for=";
    window.open(url);
}
//]]>
</script>

<?=formatHTMLTitle(
        '<a href="#" onclick="toggleSection(&quot;sectionLinkedProducts&quot;);return false">Featured Products</a>',
        '<a href="#"  onclick="linked_products_add();return false">Add New</a>'
    )?>

<div id="sectionLinkedProducts">
    <table class="table_grid" id="linked_products">
        <tr>
            <th>Linked Product Image</th>
            <th>Linked Product Name</th>
            <th>Remove</th>
        </tr>
<?php
    $count = 0;
    foreach ($item_related as $linked_product) {
      
        $prod                 = productFromDatabase($linked_product['related_id']);
        $linked_product       = array('product_id' => $item['id'], 'related_id' => $linked_product['related_id'],'id'=>$linked_product['id']);
        $data                 = $linked_product;
        $data['product_name'] = $prod['product_name'] . ' - #' . $linked_product['related_id'];
        $data['view_url']     = $prod['view_url'];
        $data['thumb_url']    = $prod['thumb_url'];
         $data['id']     = $linked_product['id'];
        echo linked_products_row($count, $data);
        $count++;
    }
?>
    </table>
    <input type="hidden" name="item_related_deleted" value="" />
    <br />
    <br />
</div>

<!-- /_linked_products.tpl -->
<!-- _linked_products.tpl -->

<?php
/**
 * Related Products Template
 *
 * Inputs:
 * - $item_related
 */

if (!isset($item_recommended) || !is_array($item_recommended)) {
    return;
}

function recommended_products_row($count = '[ROW_COUNT]', $row_data = array())
{
    global $DB;
    $name_key = 'item_recommended[' . $count . ']';
    $hidden   = array($name_key . '[id]' => $row_data['id'], $name_key . '[product_id]' => $row_data['product_id'], $name_key . '[related_id]' => $row_data['related_id']);
    ob_start();
?>
<tr id="recommended_products_rowa[<?=$count?>]" style="text-align:center;"<?=(is_int($count) && ($count & 1)) ? ' class="alt"' : ''?>>
    <td><?=str_replace("\n", "", formatHTMLInputs($hidden))?><a href="<?=$row_data['view_url']?>" target="_blank"><img src="<?=$row_data['thumb_url']?>" style="max-width:96px;max-height:96px;" /></a></td>
    <td><a href=".?action=edit&amp;id=<?=$row_data['related_id']?>" target="_blank"><?php hecho($row_data['product_name'])?></a></td>
    <td><input type="button" value="View" onclick="window.open('.?action=edit&amp;id=<?=$row_data['related_id']?>')" /> <input type="button" value="Delete" onclick="recommended_products_delete(<?=$count?>,<?= $row_data['id'] ?>)" /></td>
</tr>
<?php
return ob_get_clean();
}
?>

<script type="text/javascript">
//<![CDATA[
function recommended_products_data(dataarray){
    dataarray.item_list.forEach(recommended_products_data_byone);
} 
var recommended_products_count = <?=max(100, count($item_related))?>;
function recommended_products_data_byone(data) {
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
        var $row = $("table#recommended_products").append($(<?=strtr(jecho(recommended_products_row('[RELATED_COUNT]', array(
    'product_id'   => $item['id'],
    'related_id'   => '[RELATED_ID]',
    'product_name' => '[PRODUCT_NAME]',
    'view_url'     => '[view_URL]',
    'thumb_url'    => '[THUMB_URL]',
)), 1), array(
    '[RELATED_COUNT]' => '" + recommended_products_count + "',
    '[RELATED_ID]'    => '" + data.product_id + "',
    '[PRODUCT_NAME]'  => '" + data.product_name + "',
    '[VIEW_URL]'      => '" + data.view_url + "',
    '[THUMB_URL]'     => '" + data.thumb_url + "',
))?>));
    }
    recommended_products_count++;
}
function recommended_products_delete(id,dataid) {
    if (confirm("Are you sure you want to delete this recommended product ??")) {
        if(dataid)
        setValue("item_recommended_deleted", getValue("item_recommended_deleted") + "," + dataid);

        var theRow = getElement("recommended_products_rowa[" + id + "]");
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    }
}
function recommended_products_add() {
    var url = "master_products.php?return_message=recommended_products_data";
    url += "&multiple=y";
    url += "&linked_id=<?=$item['id']?>";
    url += "&callback=recommended_products_data";
    url += "&search_for=";
    window.open(url);
}
//]]>
</script>

<?=formatHTMLTitle(
        '<a href="#" onclick="toggleSection(&quot;sectionrRecommendedProducts&quot;);return false">Recommended Products</a>',
        '<a href="#" class="unfinished" onclick="recommended_products_add();return false">Add New</a>'
    )?>

<div id="sectionrRecommendedProducts">
    <table class="table_grid" id="recommended_products">
        <tr>
            <th style="text-align: left;">Recommended Product Image</th>
            <th style="text-align: left;">Rinked Product Name</th>
            <th style="text-align: left;">Remove</th>
        </tr>
<?php
    $count = 0;
    foreach ($item_recommended as $linked_product) {
        $prod                 = productFromDatabase($linked_product['related_id']);
        $linked_product       = array('product_id' => $item['id'], 'related_id' => $linked_product['related_id'],'id'=>$linked_product['id']);
        $data                 = $linked_product;
        $data['product_name'] = $prod['product_name'] . ' - #' . $linked_product['related_id'];
        $data['view_url']     = $prod['view_url'];
        $data['thumb_url']    = $prod['thumb_url'];
         $data['id']     = $linked_product['id'];

        echo recommended_products_row($count, $data);
        $count++;
    }
?>
    </table>
    <input type="hidden" name="item_recommended_deleted" value="" />
    <br />
    <br />
</div>

<!-- /_linked_products.tpl -->
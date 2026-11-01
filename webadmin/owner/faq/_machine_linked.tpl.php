<!-- _machine_linked.tpl -->

<?php
/**
 * Product Part-of Template
 *
 * Inputs:
 * - $item_partof
 */

function product_partof_row($count = '[ROW_COUNT]', $row_data = array())
{
    global $DB;
    $hidden   = array(
        'productid[]'         => $row_data['id'],
    );

    $class = array();
    if (is_int($count) && ($count & 1)) {
        $class[] = 'alt';
    }

    $class = implode(' ', $class);

    ob_start();
?>
<tr id="product_partof_rowa[<?=$count?>]"<?=(('' != $class) ? (' class="' . $class . '"') : '') . (($row_data['overflow']) ? ' style="display:none;"' : '')?>>
    <td><?=str_replace("\n", "", formatHTMLInputs($hidden))?><a href="/webadmin/products_new?action=edit&id=<?=$row_data['id']?>" target="_blank"> <?php hecho($row_data['product_name'])?></a></td>
    <td><?php hecho( $row_data['active']) ?></td>
    <td><input type="button" value="Delete" onclick="deletePartof(<?=$count?>,<?=($row_data['id'])?$row_data['id']:0 ?>)" /></td>
</tr>
<?php
return ob_get_clean();
}

?>

<script type="text/javascript">
//<![CDATA[
var partof_count = <?=max(100, count($faq_product))?>;
function dispalyselecteddata(dataarray){
    dataarray.item_list.forEach(productsPartofData);
} 
function productsPartofData(data) {
    var rcount = "" + partof_count;
    var partof_table = getElement("products_partof");
    if (partof_table != null) {
        var newRow1 = addTableRow(partof_table, 'product_partof_rowa[' + rcount + ']', <?=(count($item_partof) > 5) ? -2 : -1?>);
        if (partof_count & 1) newRow1.className = 'alt';
        if (data['product_id'] == null) data['product_id'] = data['id'];
        var partof_id = data['product_id'];
        var product_name = data['product_name'];
        var view_url = data['view_url'];
        var active = data['active'] == 'y' ? 'Active': 'Inactive';
        var img_url = data['thumb_url'];
<?php
$count = 0;
foreach (explode("\n", product_partof_row('[PARTOF_COUNT]', array(
    'id'        => '[PRODUCT_ID]',
    'product_name'     => '[PRODUCT_NAME]',
    'view_url'         => '[VIEW_URL]',
    'thumb_url'        => '[THUMB_URL]',
    'active' => '[ACTIVE]',
))) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "var newCell = addTableCell(newRow" . $count . ", " . strtr(
            jecho(substr($row, 4, -5), 1),
            array(
                "[PARTOF_COUNT]"     => '" + rcount + "',
                '[PRODUCT_ID]'     => '" + partof_id + "',
                '[PRODUCT_NAME]'     => '" + product_name + "',
                '[VIEW_URL]'         => '" + view_url + "',
                '[THUMB_URL]'        => '" + img_url + "',
                '[ACTIVE]' => '" + active + "',
            )
        ) . ");\n";
    }
}
?>
    }
    partof_count++;
}
function deletePartof(id,dataid) {
    if (confirm("Are you sure you want to unlink this product ??")) {
        var theRow = getElement("product_partof_rowa[" + id + "]");
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
        if(dataid>0)
        setValue("item_partof_deleted", getValue("item_partof_deleted") + "," + dataid);
    }
}
function addPartofProduct() {
    var section="&section[]=2&&section[]=3";
    var url = "/webadmin/products_new/master_products.php?return_message=partof";
    url += "&multiple=y";
    url += "&search_for=";
    url += section;
    var partof_list = sectionPartofGetRows();
    var usedin_list = [];
    for (var i = 0; i < partof_list.length; i++) usedin_list.push(getValue('product[]'));
    url += "&hide_list=" + usedin_list.join("|");
    // open product search page...
    window.open(url);
}
function sectionPartofGetRows() {
    var result = [];
    $("[id^='product_partof_rowa']").each(function() {
        result.push(this.id.substr(20, this.id.length - 21));
    });
    return result;
}
//]]>
</script>



<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionPartof&quot;);return false">Linked To Product</a>','<a href="#" onclick="addPartofProduct();return false" style="color: #000;">Add New</a>')?>

<div id="sectionPartof">
    <table class="table_grid" id="products_partof" style="width: 90%;">
        <tr>
            <th>Name</th>
            <th>Product Status</th>
            <th></th>
        </tr>
<?php
    $count                = 0;
    foreach ($faq_product as $prodductid) {
        $prod                     = productFromDatabase($prodductid);
        $data                     = $partof;
        $data['id']     = $prodductid;
        $data['product_name']     = $prod['product_name'] . ' - #' . $prodductid;
        $data['view_url']         = $prod['view_url'];
        $data['thumb_url']        = $prod['thumb_url'];
        $data['active']           =  $prod['active'] == 'y' ? 'Active' : 'InActive' ;
        echo product_partof_row($count, $data);
        $count++;
    }
?>
    </table>
</div>



<!-- /_machine_linked.tpl -->

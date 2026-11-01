<!-- _machine_linked.tpl -->

<?php
/**
 * Product Part-of Template
 *
 * Inputs:
 * - $item_partof
 */

function product_software_row($count = '[ROW_COUNT]', $row_data = array())
{
    global $DB;
    $hidden   = array(
        'softwareid[]'         => $row_data['id'],
    );

    $class = array();
    if (is_int($count) && ($count & 1)) {
        $class[] = 'alt';
    }

    $class = implode(' ', $class);

    ob_start();
?>
<tr id="product_software_rowa[<?=$count?>]"<?=(('' != $class) ? (' class="' . $class . '"') : '') . (($row_data['overflow']) ? ' style="display:none;"' : '')?>>
    <td><?=str_replace("\n", "", formatHTMLInputs($hidden))?><a href="/webadmin/products_new?action=edit&id=<?=$row_data['id']?>" target="_blank"> <?php hecho($row_data['product_name'])?></a></td>
    <td><?php hecho( $row_data['active']) ?></td>
    <td><input type="button" value="Delete" onclick="deleteSoftware(<?=$count?>,<?=($row_data['id'])?$row_data['id']:0 ?>)" /></td>
</tr>
<?php
return ob_get_clean();
}

?>

<script type="text/javascript">
//<![CDATA[
var software_count = <?=max(100, count($faq_product_software))?>;
function dispalySoftwareselecteddata(dataarray){
    dataarray.item_list.forEach(productsSoftwareData);
} 
function productsSoftwareData(data) {
    var rcount = "" + software_count;
    var partof_table = getElement("products_software_of");
    if (partof_table != null) {
        var newRow1 = addTableRow(partof_table, 'product_software_rowa[' + rcount + ']', <?=(count($item_partof) > 5) ? -2 : -1?>);
        if (software_count & 1) newRow1.className = 'alt';
        if (data['product_id'] == null) data['product_id'] = data['id'];
        var partof_id = data['product_id'];
        var product_name = data['product_name'];
        var view_url = data['view_url'];
        var active = data['active'] == 'y' ? 'Active': 'Inactive';
        var img_url = data['thumb_url'];
<?php
$count = 0;
foreach (explode("\n", product_software_row('[PARTOF_COUNT]', array(
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
function deleteSoftware(id,dataid) {
    if (confirm("Are you sure you want to unlink this product ??")) {
        var theRow = getElement("product_software_rowa[" + id + "]");
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    }
}
function addSoftwareProduct() {
    var section="&section[]=3";
    var url = "/webadmin/products_new/master_products.php?return_message=softwareof";
    url += "&multiple=y";
    url += "&search_for=";
    url += section;
    var partof_list = sectionsoftwareGetRows();
    var usedin_list = [];
    for (var i = 0; i < partof_list.length; i++) usedin_list.push(getValue('product[]'));
    url += "&hide_list=" + usedin_list.join("|");
    // open product search page...
    window.open(url);
}
function sectionsoftwareGetRows() {
    var result = [];
    $("[id^='product_software_rowa']").each(function() {
        result.push(this.id.substr(20, this.id.length - 21));
    });
    return result;
}
//]]>
</script>



<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionPartof&quot;);return false">Linked To Software</a>','<a href="#" onclick="addSoftwareProduct();return false" style="color: #000;">Add New</a>')?>

<div id="sectionPartof">
    <table class="table_grid" id="products_software_of" style="width: 90%;">
        <tr>
            <th>Name</th>
            <th>Product Status</th>
            <th></th>
        </tr>
<?php
    $count                = 0;
    foreach ($faq_product_software as $prodductid) {
        $prod                     = productFromDatabase($prodductid);
        $data                     = $partof;
        $data['id']     = $prodductid;
        $data['product_name']     = $prod['product_name'] . ' - #' . $prodductid;
        $data['view_url']         = $prod['view_url'];
        $data['thumb_url']        = $prod['thumb_url'];
        $data['active']           =  $prod['active'] == 'y' ? 'Active' : 'InActive' ;
        echo product_software_row($count, $data);
        $count++;
    }
?>
    </table>
</div>



<!-- /_machine_linked.tpl -->

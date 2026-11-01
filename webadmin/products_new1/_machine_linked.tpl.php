<!-- _machine_linked.tpl -->

<?php
/**
 * Product Part-of Template
 *
 * Inputs:
 * - $item_partof
 */

if (!isset($item_partof) || !is_array($item_partof)) {
    return;
}

function product_partof_row($count = '[ROW_COUNT]', $row_data = array())
{
    global $DB;
    $name_key = 'item_partof[' . $count . ']';
    $hidden   = array(
        $name_key . '[id]'         => $row_data['id'],
        $name_key . '[product_id]' => $row_data['product_id'],
        $name_key . '[partof_id]'  => $row_data['partof_id'],
    );

    $class = array();
    if (is_int($count) && ($count & 1)) {
        $class[] = 'alt';
    }

    if ($row_data['overflow']) {
        $class[] = 'partof_overflow';
    }

    $class = implode(' ', $class);

    ob_start();
?>
<tr id="product_partof_rowa[<?=$count?>]"<?=(('' != $class) ? (' class="' . $class . '"') : '') . (($row_data['overflow']) ? ' style="display:none;"' : '')?>>
    <td><?=str_replace("\n", "", formatHTMLInputs($hidden))?><a href=".?action=edit&id=<?=$row_data['partof_id']?>" target="_blank" name="<?=$name_key?>[product_name]"><?php hecho($row_data['product_name'])?></a></td>
    <td><?php hecho($row_data['manufacturer_str'])?></td>
    <td><input type="button" value="Delete" onclick="deletePartof(<?=$count?>,<?=($row_data['id'])?$row_data['id']:0 ?>)" /></td>
</tr>
<?php
return ob_get_clean();
}

?>

<script type="text/javascript">
//<![CDATA[
var partof_count = <?=max(100, count($item_partof))?>;
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
        var manufacturer_str = data['manufacturer_str'];
        var img_url = data['thumb_url'];
<?php
$count = 0;
foreach (explode("\n", product_partof_row('[PARTOF_COUNT]', array(
    'product_id'       => $item['id'],
    'partof_id'        => '[PARTOF_ID]',
    'product_name'     => '[PRODUCT_NAME]',
    'view_url'         => '[VIEW_URL]',
    'thumb_url'        => '[THUMB_URL]',
    'manufacturer_str' => '[MANUFACTURER_STR]',
))) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "var newCell = addTableCell(newRow" . $count . ", " . strtr(
            jecho(substr($row, 4, -5), 1),
            array(
                "[PARTOF_COUNT]"     => '" + rcount + "',
                '[PARTOF_ID]'        => '" + partof_id + "',
                '[PRODUCT_NAME]'     => '" + product_name + "',
                '[VIEW_URL]'         => '" + view_url + "',
                '[THUMB_URL]'        => '" + img_url + "',
                '[MANUFACTURER_STR]' => '" + manufacturer_str + "',
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
    var section="<?php echo ($item['section']==2)?'&section[]=3&section[]=1&section[]=4':'&section[]=2';?>";
    var url = "master_products.php?return_message=partof";
    url += "&multiple=y";
    url += "&search_for=";
    url += section;
    var partof_list = sectionPartofGetRows();
    var usedin_list = [<?=$item['id']?>];
    for (var i = 0; i < partof_list.length; i++) usedin_list.push(getValue('item_partof[' + partof_list[i] + '][partof_id]'));
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

<?php
if (is_array($item_partof)) {
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionPartof&quot;);return false">'.(($item['section']==2)?"Parts & Pcb Linking":"Machine Linking").'</a>', '<a href="#" onclick="addPartofProduct();return false">Add New</a>')?>

<div id="sectionPartof">
    <table class="table_grid" id="products_partof">
        <tr>
            <th>Name</th>
            <th>Manufacturer</th>
            <th></th>
        </tr>
<?php
    $count                = 0;
    $manufacturer_list    = productGetOptionList('manufacturer');
    foreach ($item_partof as $partof) {
        $prod                     = productFromDatabase($partof['partof_id']);
        $data                     = $partof;
        $data['product_name']     = $prod['product_name'] . ' - #' . $partof['partof_id'];
        $data['manufacturer_str'] = $manufacturer_list[$prod['manufacturer']];
        $data['view_url']         = $prod['view_url'];
        $data['thumb_url']        = $prod['thumb_url'];
        $data['overflow']         = ($count >= 5);
        echo product_partof_row($count, $data);
        $count++;
    }
    if ($count > 5) {
?>
        <tr class="alt"><td colspan="3" style="text-align:center;"><input type="button" value="Show All (<?=count($item_partof)?>)" onclick="$('.partof_overflow').toggle();" /></td></tr>
<?php
}
?>
    </table>
    <input type="hidden" name="item_partof_deleted" value="" />
    <br /><br />
</div>

<?php
}
?>

<!-- /_machine_linked.tpl -->

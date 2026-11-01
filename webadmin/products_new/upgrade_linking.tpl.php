<!-- _machine_linked.tpl -->

<?php
/**
 * Product Part-of Template
 *
 * Inputs:
 * - $item_upgradelinking
 */
if (!isset($item_upgradelinking) || !is_array($item_upgradelinking)) {
    
    return;
}

function product_upgradelinking_row($count = '[ROW_COUNT]', $row_data = array())
{
    global $DB;
    $name_key = 'item_upgradelinking[' . $count . ']';
    $hidden   = array(
        $name_key . '[id]'         => $row_data['id'],
        $name_key . '[product_id]' => $row_data['product_id'],
        $name_key . '[linking_id]'  => $row_data['linking_id'],
    );

    $class = array();
    if (is_int($count) && ($count & 1)) {
        $class[] = 'alt';
    }

    if ($row_data['overflow']) {
        $class[] = 'upgradelinking_overflow';
    }

    $class = implode(' ', $class);

    ob_start();
?>
<tr id="product_upgradelinking_rowa[<?=$count?>]"<?=(('' != $class) ? (' class="' . $class . ' upgrade_linking_row"') : 'class="upgrade_linking_row"') . (($row_data['overflow']) ? ' style="display:none;"' : '')?>>
    <td><?=str_replace("\n", "", formatHTMLInputs($hidden))?> <a href="#" onclick="return upgradelinkingMoveUp(this);"><img src="/webadmin/images/up_arrow.gif" alt="↑" border="0"></a> <a href="#" onclick="return upgradelinkingMoveDown(this);"><img src="/webadmin/images/down_arrow.gif" alt="↓" border="0"></a> <input type="hidden" class="row_count" name="item_upgradelinking[<?=$count?>][rank]" value="<?= $count ?>"> </td>
     <td><a href=".?action=edit&id=<?=$row_data['linking_id']?>" target="_blank" name="<?=$name_key?>[linking_id]"><?php hecho($row_data['linking_id'])?></a></td>
    <td><?=str_replace("\n", "", formatHTMLInputs($hidden))?><a href=".?action=edit&id=<?=$row_data['linking_id']?>" target="_blank" name="<?=$name_key?>[product_name]"><?php hecho($row_data['product_name'])?></a></td>
    <td><?php hecho($row_data['manufacturer_str'])?></td>
     <td><?php hecho($row_data['online_price'])?></td>
     <td><input type="number" name="item_upgradelinking[<?=$count?>][offer_price]" value="<?= ($row_data['offer_price']>0)?$row_data['offer_price']:$row_data['online_price']?>" ></td>
     <td><input type="checkbox" name="item_upgradelinking[<?=$count?>][show_front]" value="y" <?= ($row_data['show_front']=='y')?'checked':'' ?>></td>
     <td><?= $row_data['product_status']?></td>
    <td><input type="button" value="Delete" onclick="deleteupgradelinking(<?=$count?>,<?=($row_data['id'])?$row_data['id']:0 ?>)" /></td>
</tr>
<?php
return ob_get_clean();
}

?>

<script type="text/javascript">
//<![CDATA[
function upgradelinkingMoveUp(elem) {
    var row = $(elem).parents(".upgrade_linking_row");

    if (row.prev().hasClass("upgrade_linking_row")) {
        var n = row.prev().find(".row_count").val();
        row.prev().find(".row_count").val(row.find(".row_count").val());
        row.find(".row_count").val(n);
        row.prev().before(row);
    }

    return false;
}
function upgradelinkingMoveDown(elem) {
    var row = $(elem).parents(".upgrade_linking_row");

    if (row.next().hasClass("upgrade_linking_row")) {
        var n = row.next().find(".row_count").val();
        row.next().find(".row_count").val(row.find(".row_count").val());
        row.find(".row_count").val(n);
        row.next().after(row);
    }

    return false;
}

var upgradelinking_count = <?=count($item_upgradelinking)?>;
function dispalyupgradelinkingproductddata(dataarray){
    console.log(dataarray);
    dataarray.item_list.forEach(productsupgradelinkingData);
} 
function productsupgradelinkingData(data) {
    //console.log(data);
    var rcount = "" + upgradelinking_count;
    var upgradelinking_table = getElement("products_upgradelinking");
    if (upgradelinking_table != null) {
        var newRow1 = addTableRow(upgradelinking_table, 'product_upgradelinking_rowa[' + rcount + ']', <?=(count($item_upgradelinking) > 5) ? -2 : -1?>);
        if (upgradelinking_count & 1) 
            newRow1.className = 'alt upgrade_linking_row';
        else
           newRow1.className = 'upgrade_linking_row'; 
        if (data['product_id'] == null) data['product_id'] = data['id'];
        var linking_id = data['product_id'];
        var product_name = data['product_name'];
        var view_url = data['view_url'];
        var manufacturer_str = data['manufacturer_str'];
        var img_url = data['thumb_url'];
        var secondary_price=data['secondary_price'];
        var product_status=(data['active']=='y')?'Active':'In Active';
<?php
$count = 0;
foreach (explode("\n", product_upgradelinking_row('[UPGRADE_LINKING_COUNT]', array(
    'product_id'       => $item['id'],
    'product_status'   =>'[STATUS]',
    'show_front'       =>'y',
    'online_price'      =>'[ONLINE_PRICE]',
    'offer_price'       =>'[OFFER_PRICE]',
    'linking_id'        => '[LINKING_ID]',
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
                "[UPGRADE_LINKING_COUNT]"     => '" + rcount + "',
                '[LINKING_ID]'        => '" + linking_id + "',
                '[PRODUCT_NAME]'     => '" + product_name + "',
                '[VIEW_URL]'         => '" + view_url + "',
                '[THUMB_URL]'        => '" + img_url + "',
                '[MANUFACTURER_STR]' => '" + manufacturer_str + "',
                '[ONLINE_PRICE]' => '" + secondary_price + "',
                '[OFFER_PRICE]' => '" + secondary_price + "',
                '[STATUS]' => '" + product_status + "',

            )
        ) . ");\n";
    }
}
?>
    }
    upgradelinking_count++;
}
function deleteupgradelinking(id,dataid) {
    if (confirm("Are you sure you want to unlink this product ??")) {
        var theRow = getElement("product_upgradelinking_rowa[" + id + "]");
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
        if(dataid>0)
        setValue("item_upgrade_linking_deleted", getValue("item_upgrade_linking_deleted") + "," + dataid);
    }
}
function addupgradelinkingProduct() {
    var section="<?php echo ($item['section']==2)?'&section[]=3&section[]=1&section[]=4':'&section[]=2';?>";
    var url = "master_products.php?return_message=upgradelinking";
    url += "&multiple=y";
    url += "&search_for=";
    url += section;
    var upgradelinking_list = sectionupgradelinkingGetRows();
    var usedin_list = [<?=$item['id']?>];
    for (var i = 0; i < upgradelinking_list.length; i++) usedin_list.push(getValue('item_upgradelinking[' + upgradelinking_list[i] + '][upgradelinking_id]'));
    url += "&hide_list=" + usedin_list.join("|");
    // open product search page...
    window.open(url);
}
function sectionupgradelinkingGetRows() {
    var result = [];
    $("[id^='product_upgradelinking_rowa']").each(function() {
        result.push(this.id.substr(20, this.id.length - 21));
    });
    return result;
}
//]]>
</script>

<?php
if (is_array($item_upgradelinking)) {
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionupgradelinking&quot;);return false">Upgrade Linking</a>', '<a href="#" onclick="addupgradelinkingProduct();return false">Add New</a>')?>

<div id="sectionupgradelinking">
    <table class="table_grid" id="products_upgradelinking">
        <tr>
            <th></th>
            <th>ID</th>
            <th>Name</th>
            <th>Manufacturer</th>
            <th>Online Price USD</th>
            <th>Offer Price USD *</th>
            <th>Front Page</th>
            <th>Status</th>
            <th></th>
        </tr>
<?php
    $count                = 0;
    $manufacturer_list    = productGetOptionList('manufacturer');
    foreach ($item_upgradelinking as $upgradelinking) {
        $prod                     = productFromDatabase($upgradelinking['linking_id']);
        $data                     = $upgradelinking;
        $data['product_name']     = $prod['product_name'];
        $data['linking_id']       = $upgradelinking['linking_id'];
        $data['offer_price']      = $upgradelinking['offer_price'];
        $data['show_front']       = $upgradelinking['show_front'];
        $data['product_status']   = ($prod['active']=='y')?'Active':'In Active';//$upgradelinking['status'];
        $data['manufacturer_str'] = $manufacturer_list[$prod['manufacturer']];
        $data['view_url']         = $prod['view_url'];
        $data['thumb_url']        = $prod['thumb_url'];
        $data['online_price']     =number_format((float)convertCurrency($prod["secondary_price"], $prod['secondary_currency'], 'USD', false, $prod['precision']), 2, '.', ''); 
        $data['overflow']         = ($count >= 5);
        echo product_upgradelinking_row($count, $data);
        $count++;
    }
    if ($count > 5) {
?>
        <tr class="alt"><td colspan="9" style="text-align:center;"><input type="button" value="Show All (<?=count($item_upgradelinking)?>)" onclick="$('.upgradelinking_overflow').toggle();" /></td></tr>
<?php
}
?>
     <tr class="alt"><td colspan="9" style="text-align:center;"><p style="color:red;">* If no tick boxes are ticked, then no “Available Options” text/design will show on the front product page.</p></td></tr>
    </table>
    <input type="hidden" name="item_upgrade_linking_deleted" value="" />
    <br /><br />
</div>

<?php
}
?>

<!-- /_machine_linked.tpl -->

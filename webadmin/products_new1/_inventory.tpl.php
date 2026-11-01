<!-- _inventory.tpl -->
<style>
    .hide-row{
        display:none;
    }
</style>
<?php
moduleLoad('invoice'); //invoiceProductsFromDatabase, invoiceFromDatabase

/**
 * Product Inventory Template
 *
 * Inputs:
 * - $action
 * - $item
 * - $item_inventory
 */

if (!isset($item_inventory) || !is_array($item_inventory)) {
    return;
}

if ('add' === $action) {
    return;
}
$pro_color=explode(',', $item['colour']);

$minus_options  = array(0 => 'Select an option', 1 => 'Sold', 2 => 'Used', 3 => 'Destroyed');
$minus_options2 = array(1 => array(1 => 'Invoice # :'), 2 => array(1 => 'Product /RMA ID :'), 3 => array(1 => 'Reason'));
$add_options2   = array(0 => 'Select pre-defined comments.', 1 => 'Stock Purchase', 2 => 'Warranty Stock', 3 => 'Sample Purchase', 4 => 'Stock Re-added');
$decimalPoints  = (1 == $item['section']) ? 4 : 2;
?>

<script type="text/javascript">
//<![CDATA[
$(function() {
<?php
//if ($item_lock['staff_id'] == $_GLOBALS['staff']['id']) {
?>
    $(".item_inventory_add_stock_btn").click(function() {
        $('.typecolor').hide();
        $('.typecolor'+$(this).attr('data-val')).show();
        $("#item_inventory_minus_form").stop().toggle(false);
        $("#item_inventory_add_form").stop().fadeIn();
    }).toggle(true);
    $(".item_inventory_minus_stock_btn").click(function() {
         $('.typecolor').hide();
        $('.typecolor'+$(this).attr('data-val')).show();
        $("#item_inventory_add_form").stop().toggle(false);
        $("#item_inventory_minus_form").stop().fadeIn();
    }).toggle(true);
<?php
//}
?>
});
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionInventory&quot;);return false">Inventory</a>')?>

<div id="sectionInventory">
    <table class="table_fields">
        <tr>
            <th>Current Warehouse Stock</th>
            <td style="width:25%;"><input class="wide" type="number" min="-9999999999" max="9999999999" step="1" name="item[qty]" value="<?php hecho($item['qty']) ?>"<?=('y' == $item['alert_flag']) ? ' style="border:3px dashed #f00;"' : ''?> disabled /></td>
            <th>Alert Level</th>
            <td style="width:25%;"><input class="wide" type="number" min="-9999999999" max="9999999999" step="1" name="item[alert_level]" value="<?php hecho($item['alert_level']) ?>" /></td>
        </tr>
        <tr>
<?php
$temp = 0;
if (isset($item_reserved) && is_array($item_reserved)) {
    foreach ($item_reserved as $temp2) {
        $temp += $temp2['qty'];
    }
}
?>
            <th>Reserved Stock</th>
            <td><input class="wide" type="number" min="-9999999999" max="9999999999" step="1" name="item_reserved" value="<?php hecho($temp) ?>" readonly="readonly" /></td>
            <th></th>
            <td class="unfinished"><i>When stock drops below this level, Email is sent to Purchasing Dept</i></td>
        </tr>
        <tr>
            <th>Available Stock For Sale</th>
            <td><input class="wide" type="number" min="-9999999999" max="9999999999" step="1" name="item_available" value="<?php hecho($item['qty'] - $temp) ?>" style="border:2px solid #f00;" readonly="readonly" /></td>
            <th>Enable Alerts</th>
            <td><select name="item[alert_enable]" class="wide">
                <option value=""></option>
                <?=formatHTMLOptions(productGetOptionList('alert_enable'), $item['alert_enable'])?>
            </select></td>
        </tr>
        <tr>
            <th></th>
            <td></td>
            <th>Recommended Purchase Quantity</th>
            <td><input class="wide" type="number" min="-9999999999" max="9999999999" step="1" name="item[alert_recommend]" value="<?php hecho($item['alert_recommend']) ?>" /></td>
        </tr>
        <tr>
            <th></th>
            <td style="text-align:center;" colspan="2">
            </td>
            <td></td>
        </tr>
    </table>
<?php if(count($pro_color)>1){
    $stockarray=[];
    $checkkey=1;
    $table_array=['Colour','Our Id','Factory Id','Action','Availability','Stock','Status'];
    echo '</br></br> <table class="table_grid"><th colspan="6">Inventory Detail</th>';
    foreach ($table_array as $datatable) {
        $ext_data='';
        if($datatable=='Availability'){
            $ext_data=' (<small><i><a href="options.php?action=list&amp;option_name=available" target="_blank">Add</a></i></small>)';
        }
        echo '<tr><td>'.$datatable.$ext_data.'</td>';
        foreach($pro_color as $key => $colour) {
            if($checkkey==1){
                $stock=inventoryStockGetValue($_REQUEST['id'],$colour, 'qty');
                $stockarray[$colour]=$stock;
            }
        if($stockarray[$colour]>0){
            $stock_class='highlight-stock-stock_for_sale';
        }else{
            $stock_class= 'highlight-stock-consignment';
        }
        if($datatable=='Colour'){
            $color_data=productGenericFromDatabase($colour,'product_colour') ;
?>
        <td class="<?= $stock_class ?>"><span style="height:12px;width: 15px; border-radius: 50%; display: inline-block; text-align: center; padding-top: 3px; margin-right: 4px!important;background-color: <?php echo $color_data['color_val'];?>"></span><?php echo $color_data['colour'];?></td>
<?php
        }
        if($datatable=='Our Id'){
            $color_data=productGenericFromDatabase($colour,'product_colour') ;
            echo '<td class="'.$stock_class.'">'.$_REQUEST['id'].$color_data['code3'].'</td>';
        }
        if($datatable=='Factory Id'){
            $factory_id=inventoryStockGetValue($_REQUEST['id'],$colour, 'factory_id');
            echo '<td class="'.$stock_class.'">'.$factory_id.'</td>';
        }
        if($datatable=='Action'){
            $action_url='.?action=inventory_history&id='.$_REQUEST['id'].'&color_id='.$colour.'';
?>
            <td class="<?= $stock_class ?>">
                <?php //if($item_lock['staff_id'] == $_GLOBALS['staff']['id']){ ?>
                <input class="item_inventory_add_stock_btn" data-val="<?= $colour ?>" type="button" value="Add" />
                <input class="item_inventory_minus_stock_btn"  type="button" value="Minus" data-val="<?=$colour ?>"/>
                <input type="button" value="History" onclick='window.open("<?= $action_url ?>")'/>
            <?php //} ?>
            </td>
<?php
        }
        if($datatable=='Status'){
            $sold_out_value=inventoryStockGetValue($_REQUEST['id'],$colour, 'when_sold_out');
            $color_data=productGenericFromDatabase($colour,'product_colour') ;
            $sold_out=['active'=>'Active','deactivate'=>'De-active'];
            echo "<td><select name='inventory_add[when_sold_out][".$color_data['colour']."]'>".formatHTMLOptions($sold_out,$sold_out_value)."</select></td>";
        }
        if($datatable=='Availability'){
           //$availability=($item['available'])?$item['available']:'N/A';
            $color_data=productGenericFromDatabase($colour,'product_colour') ;
            $availability=inventoryStockGetValue($_REQUEST['id'],$colour, 'available');
            $availability = $availability ?? (($item['available'])?$item['available']:'N/A');
            echo "<td><select name='inventory_add[available][".$color_data['colour']."]'>".formatHTMLOptions(productGetOptionList('available'),$availability)."</select></td>";
        }
        if($datatable=='Stock'){
            $stock=$stockarray[$colour];
            $reserve=productReservedByColourTotalStock($_REQUEST['id'],$colour);
            if($stock>0){
                $stock=$stock-$reserve;
                echo '<td class="highlight-stock-stock_for_sale">'.$stock.'</td>';
            }else{
                echo '<td class="highlight-stock-consignment">'.$stock.'</td>';
            }

        }
?>
    <?php  } echo '</tr>'; $checkkey++; }
echo '</table>';
} ?>
<?php

if ( count($pro_color)<=1) {
?>
    <div style="width:95%;margin:auto;text-align:center;">
        <input class="item_inventory_add_stock_btn" style="width:160px;display:none;" type="button" value="Add Stock" <?=('consignment' == $item['stock_type']) ? ' disabled="disabled"' : ''?> />
        &nbsp;&nbsp;
        <input class="item_inventory_minus_stock_btn" style="width:160px;display:none;" type="button" value="Minus Stock" <?=('consignment' == $item['stock_type']) ? ' disabled="disabled"' : ''?> />
    </div>
<?php
}
?>
    <br />
    <br />

<?php
$supplier_list = array();

foreach (getAvailableSupplierForProduct($id) as $k => $v) {
    $supplier_list[$k] = $v;
}

?>

<?php
//if ($item_lock['staff_id'] == $_GLOBALS['staff']['id']) {
?>
    <div id="item_inventory_add_form" style="display:none;">
        <table class="table_fields">
            <tr>
                <th>Add Stock :</th>
                <td style="width:25%;"><select class="wide" name="inventory_add[supplier_id]">
                    <option value="">Select Supplier</option>
                    <?= formatHTMLOptions($supplier_list) ?>
                </select></td>
                <?php if(count($pro_color)<=1){ ?>
                <th>Qty :</th>

                <td style="width:25%;"><input class="wide" type="number" min="0" max="9999999999" step="1" name="inventory_add[movement]" value="0" /></td>
                <?php } ?>
            </tr>
            <tr>
                <th>Comments :</th>
                <td><select class="wide" name="inventory_add[comment_1]">
                    <option value="">Select pre-defined comments.</option>
                    <option value="Stock Purchase">Stock Purchase</option>
                    <option value="Warranty Stock">Warranty Stock</option>
                    <option value="Sample Purchase">Sample Purchase</option>
                    <option value="Stock Re-added">Stock Re-added</option>
                </select></td>
                <td colspan="2"><input class="wide" type="text" name="inventory_add[comment_2]" placeholder="Extra comments" value="" /></td>
            </tr>
            <tr>
                <th>Unit Purchase Price :</th>
                <td><input class="wide" type="number" min="0.0000" max="999999.9999" step="0.0001" name="inventory_add[unit_cost]" value="0.0000" /></td>
                <th>Unit Freight Costs :</th>
                <td><input class="wide" type="number" min="0.0000" max="999999.9999" step="0.0001" name="inventory_add[unit_freight]" value="0.0000" /></td>
            </tr>
            <tr>
                <th>Unit Purchase Currency :</th>
                <td colspan="3"><select class="wide" name="inventory_add[unit_currency]">
                    <?=formatHTMLOptions(getCurrencyList(), 'USD')?>
                </select></td>
            </tr>
            </table>
            <?php if(count($pro_color)>1){
                echo '<table class="table_fields">';
                foreach($pro_color as $key => $colour) {
                $color_data=productGenericFromDatabase($colour,'product_colour') ;
?>

            <tr class="typecolor<?php echo $color_data['id'];?> typecolor">
                <input type="hidden" name="inventory_add[color][<?php echo $color_data['colour']; ?>]" value="<?php echo $color_data['id']; ?>">
                <th style="width:5%"><div style="padding:5px;border: 1px solid black;width: 5px; height: 5px;background-color: <?php echo $color_data['color_val'];?>"></div></th>
                <th style="width:15%;text-align: left"><?php echo $color_data['colour'];?></th>
                <th style="width:15%">Qty :</th>
                <td style="width:25%;"><input class="wide" type="number" min="0" max="9999999999" step="1" name="inventory_add[qty][<?php echo $color_data['colour']; ?>]" value="0" /></td>
                <th style="width:15%">Factory Id :</th>
                <td style="width:25%;"><input class="wide" type="text" maxlength="50" name="inventory_add[factory_id][<?php echo $color_data['colour']; ?>]" /></td>
                <th style="width:15%">Images:</th>
            </tr>
            <?php }
            echo '</table>';
            }?>

        <div style="width:95%;margin:auto;text-align:center;"><button type="submit" name="action" value="add_stock" style="width:160px;">Add Stock</button></div>
        <br />
        <br />
    </div>

    <div id="item_inventory_minus_form" style="display:none;">
        <table class="table_fields">
            <tr>
                <th>Minus Stock :</th>
                <td style="width:25%;"><select class="wide" name="inventory_minus[comment_1]">
                    <option value="">Select an option</option>
                    <option value="Sold">Sold with Invoice # :</option>
                    <option value="Used">Used with Product/RMA ID :</option>
                    <option value="Destroyed">Destroyed with Reason :</option>
                </select></td>
                <?php if(count($pro_color)<=1){ ?>
                <th>Qty :</th>

                <td style="width:25%;"><input class="wide" type="number" min="0" max="9999999999" step="1" name="inventory_minus[movement]" value="0" /></td>
            <?php } ?>
            </tr>
            <tr>
                <th>Comments :</th>
                <td colspan="3"><input class="wide" type="text" name="inventory_minus[comment_2]" placeholder="Extra comments" value="" /></td>
            </tr>
            <tr>
                <th>Unit Sale Price :</th>
                <td><input class="wide" type="number" min="0.0000" max="999999.9999" step="0.0001" name="inventory_minus[unit_cost]" value="0.0000" /></td>
                <th>Unit Freight Costs :</th>
                <td><input class="wide" type="number" min="0.0000" max="999999.9999" step="0.0001" name="inventory_minus[unit_freight]" value="0.0000" /></td>
            </tr>
            <tr>
                <th>Unit Sale Currency :</th>
                <td colspan="3"><select class="wide" name="inventory_minus[unit_currency]">
                    <?=formatHTMLOptions(getCurrencyList(), 'USD')?>
                </select></td>
            </tr>
        </table>
            <?php if(count($pro_color)>1){
                echo '<table class="table_fields">';
                foreach($pro_color as $key => $colour) {
                $color_data=productGenericFromDatabase($colour,'product_colour') ;
?>

            <tr class="typecolor<?php echo $color_data['id'];?> typecolor">
                 <input type="hidden" name="inventory_minus[color][<?php echo $color_data['colour']; ?>]" value="<?php echo $color_data['id']; ?>">
                <th style="width:5%"><div style="padding:5px;border: 1px solid black;width: 5px; height: 5px;background-color: <?php echo $color_data['color_val'];?>"></div></th>
                <th style="width:15%;text-align: left"><?php echo $color_data['colour'];?></th>
                <th style="width:15%">Qty :</th>
                <td style="width:25%;"><input class="wide" type="number" min="0" max="9999999999" step="1" name="inventory_minus[qty][<?php echo $color_data['colour']; ?>]" value="0" /></td>
            </tr>
            <?php }
            echo '</table>';
            }?>
        <div style="width:95%;margin:auto;text-align:center;"><button type="submit" name="action" value="minus_stock" style="width:160px;">Minus Stock</button></div>
        <br />
        <br />
    </div>
<?php
//}


if (count($item_inventory)) {
    $temp2 = productGetOptionList('created_by');
    // $temp =array_reverse($item_inventory);
    $temp = $item_inventory;

?>
    <table class="table_grid showinvoice">
        <tr><th colspan="<?=(has_permission('STOCK', $allowed_sections)) ? 5 : 4?>"><strong>Inventory Control</strong></td></tr>
        <tr style="background:#E3EBF7;text-align:center;">
            <td style="width:80px;"><b>Date</b></td>
            <td style="width:80px;"><b>Movement</b></td>
            <td style="width:<?=(has_permission('STOCK', $allowed_sections)) ? '65' : '70'?>%"><b>Description</b></td>
            <td style="width:80px;"><b>Available</b></td>
<?php
if (has_permission('STOCK', $allowed_sections)) {
?>
            <td style="width:80px;"><b>Edit Pricing</b></th>
<?php
}
?>
    </tr>
<?php

    $alt = false;
    $rowline=1;
    foreach ($temp as $row) {
        $comments = hecho($row['comments'], 1);
        if (preg_match('/(ECO|ICO|PI|I)#([0-9]+)/', $comments, $m)) {
            $invoice_id = intval(ltrim($m[2], '0'));
            $invoice    = invoiceFromDatabase($invoice_id);
            if ('' != $invoice['id']) {
                $invoice_products = invoiceProductsFromDatabase($invoice_id);

                ob_start();
                include __DIR__ . '/__inventory_invoice_tooltip.tpl.php';
                $invoice_tooltip = ob_get_clean();

                $comments = str_replace($m[0], '<a href="/webadmin/invoice/?action=view&amp;invoice_id=' . $invoice_id . '" target="_blank" onmouseover="' . hecho('tt_in(' . jecho($invoice_tooltip, 1) . ')', 1) . '" onmouseout="tt_out()">' . $m[0] . '</a>', $comments);
            }
        }
?>
        <tr style="text-align:center;" class="<?=($alt = !$alt)?(($rowline>5)?'alt hide-row row-hide-check':'alt') : (($rowline>5)?' hide-row row-hide-check':'')?>">
            <td style="white-space:nowrap;"><?= date(_('d-m-Y'), strtotime($row['created_at'])) ?></td>
            <td style="white-space:nowrap;">
<?php
                    if($row['color_id']){
                        $color_data=productGenericFromDatabase($row['color_id'],'product_colour') ;
?>
                    <span style="height:12px;width: 15px; border-radius: 50%; display: inline-block; text-align: center; padding-top: 3px; margin-right: 4px!important;background-color: <?php echo $color_data['color_val'];?>"></span><?php echo sprintf('%+d', $row['movement']);?>
<?php
                    }else{
                        echo sprintf('%+d', $row['movement']);
                    }
?>

            </td>
            <td style="text-align:left;"><?=$comments?></td>
            <td style="white-space:nowrap;"><?=$row['available']?></td>
<?php
        if (has_permission('STOCK', $allowed_sections) && $row['movement'] > 0) {
?>
            <td style="white-space:nowrap;">
                <input class="unfinished" type="button" value="EDIT"<?=($row['stock_left'] <= 0) ? ' disabled="disabled"' : ''?> onclick="openWindow('modify_inventory.php?inventory_id=<?=$row['id']?>', 800, 300, true);return false" />
            </td>
<?php
        }
?>
        </tr>
<?php
    $rowline++;
    }
?>
    </table>
    <div style="text-align:center">
        <button  id="showinvoice" type="button">View All (<?=count($item_inventory)?>)</button>
    </div>
    <br />
    <br />
<?php
}


if (count($item_reserved)) {
?>
    <table class="table_grid">
        <tr><th colspan="4"><b>Reserved Stock</b></td></tr>
        <tr style="background:#E3EBF7">
            <td style="width:160px;"><b>Date</b></td>
            <td style="width:128px"><b>Amount Reserved</b></td>
            <td><b>Invoice Details</b></td>
        </tr>
<?php

    $alt = false;
    foreach ($item_reserved as $row) {
        $invoice = invoiceFromDatabase($row['invoice_id']);
        $invoice_products = invoiceProductsFromDatabase($row['invoice_id']);

        ob_start();
        include __DIR__ . '/__inventory_invoice_tooltip.tpl.php';
        $invoice_tooltip = ob_get_clean();
?>
        <tr style="text-align:center;"<?=($alt = !$alt) ? ' class="alt"' : ''?>>
            <td><?=date(_('Y-m-d'), strtotime($row['created_at'])) ?></td>
            <td><?=$row['qty']?></td>
            <td style="text-align:left;" onmouseover="<?php hecho('tt_in(' . jecho($invoice_tooltip, 1) . ')')?>" onmouseout="tt_out();">Stock Reserved for <a href="/webadmin/invoice/?action=view&amp;invoice_id=<?=$invoice_id?>" target="_blank"><?php hecho($invoice['invoice_number']) ?></a> : <?php hecho($invoice['bill_name']) ?></td>
        </tr>
<?php
}
?>
        <tr style="text-align:center;"<?=($alt = !$alt) ? ' class="alt"' : ''?>>
            <td><b>Total Reserved Stock :</b></td>
            <td><b><?=$item['reserved']?></b></td>
            <td></td>
        </tr>
    </table>
<?php
}
?>
</div><br />

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionInventory_hideNote">
    Product Inventory can be viewed by Clicking "Inventory" above<br />
</div>
<script type="text/javascript">
    var htmltext='';
    $('#showinvoice').click(function(){
        if($('.row-hide-check').hasClass('hide-row')){
            $('.row-hide-check').removeClass('hide-row');
             htmltext=$('#showinvoice').html();
            $('#showinvoice').html('Show Less');
        }else{
            $('#showinvoice').html(htmltext);
            $('.row-hide-check').addClass('hide-row');
            $(window).scrollTop($('.showinvoice').offset().top);
        }

    });
</script>
<!-- /_inventory.tpl -->

<!-- _product_suppliers.tpl -->
<style type="text/css">
    .border-class-table  td {
  border: 1px solid black;
  text-align: center;
}
.border-class-table  th {
  border: 1px solid black;
  text-align: center;
}
.supplier_class th {
    width: 15%!important;
}
</style>
<script src="https://cdn.anychart.com/releases/8.10.0/js/anychart-base.min.js"></script>
<?php
/**
 * The product suppliers template
 *
 * Inputs:
 * - $item_suppliers
 */
if (!isset($item_suppliers) || !is_array($item_suppliers)) {
    return;
}

$supplier_list = productGetOptionList('supplier_id');

function item_suppliers_row($count = '[ROW_COUNT]', $row_data = array(),$selles_price_usd=0,$item_inventory=[])
{
    global $_GLOBALS, $supplier_list;

    $name_key = 'item_suppliers[' . $count . ']';
    $hidden   = array(
        'id'           => $row_data['id'],
        'supplier_id'  => $row_data['supplier_id'],
        'product_id'   => $row_data['product_id'],
        //'product_name' => $row_data['product_name'],
        'type'         => $row_data['type'],
        'offer_price'  => $row_data['offer_price'],
        'currency'     => $row_data['currency'],
        'preferred'    => $row_data['preferred'],
        'sales_price'  => $row_data['onlineprice_usd'],
    );
    ob_start();
?>
<table class="table_fields supplier_class" id="item_suppliers_row[<?= $count ?>]" style="margin-bottom: 10px;">

    <tr id="supplier_product_rowa[<?=$count?>]">
        <th rowspan="17" style="width:10%;text-align:center;vertical-align:middle;">
            <input type="checkbox" name="<?=$name_key?>[main]" value="y"<?=('y' == $row_data['main'] ? ' checked="checked"' : '')?> onclick="supplierDefaultSelect(<?=$count?>)" /><br />
            <span ><small>Default Supplier</small></span>
        </th>
        <th style="background:#F34B62">
            <?=formatHTMLInputs($hidden, $name_key)?>
            Supplier :
        </th>
        <td style="background:#F34B62" colspan="3">
        <?php
            if (array_key_exists($row_data['supplier_id'], $supplier_list)) {
        ?>
                    <a id="supplierProductAnchor[<?= $count ?>]" href="/webadmin/supplier_new/edit.php?site_id=<?= $_GLOBALS['site_settings']['site_id']?>&supplier_id=<?= $row_data['supplier_id'] ?>" target="_blank" class="unfinished"><?php hecho($supplier_list[$row_data['supplier_id']]) ?></a>
        <?php
            } else {
        ?>
            <!-- <input type="button" name="item_suppliers_search[<?=$count ?>]" value="Search" class="unfinished item_suppliers_search" style="float:right;display:none;" /> -->
            <input type="text" name="<?=$name_key?>[supplier_name]" value="<?php hecho($row_data['supplier_name']) ?>" style="width:70%;" />
<?php
    }
?>
        </td>
    </tr>
    <tr>
        <th>Supplier Code :</th>
        <td colspan="3"><input class="wide" type="text" name="<?=$name_key?>[sup_code]" value="<?php hecho($row_data['sup_code']) ?>" /></td>
    </tr>
    <tr>
        <th>Supplier Product Name :</th>
        <td colspan="3"><input class="wide" type="text" name="<?=$name_key?>[sup_name]" value="<?php hecho($row_data['sup_name']) ?>" /></td>
    </tr>
    <tr>
        <th style="font-size: 14px;"><b style="border: 1px solid red;">CONSIGNMENT DATA</b></th>
        <th colspan="3"></th>
    </tr>
    <tr>
        <th>Qty Available:</th>
        <td colspan="3" >
        <input style="width:33%" required name="<?=$name_key?>[qty]" type="text"  value="<?=$row_data['qty']?>" class="update_quantity"  rowid="<?= $count?>"/></td>
    </tr>
    <tr>
        <th>Location :</th>
        <td colspan="3">
            <select name="<?=$name_key?>[location]" style="width:33%">
                <!-- <option value="0">Not Selected</option> -->
                <?php $loc=($row_data['location'])?$row_data['location']:'CN'; ?>
                <?=formatHTMLOptions(getCountryList(), $loc )?>
            </select>
        </td>
    </tr>
    <tr>
        <th>Notes:</th>
        <td colspan="3" >
        <input style="width:98%" name="<?=$name_key?>[notes]" type="text"  value="<?=$row_data['notes']?>" /></td>
    </tr>
    <tr>
        <th>Cost Price :</th>
        <td colspan="3">
            <input style="width:25%" name="<?=$name_key?>[cost_price]" type="text"  value="<?=$row_data['cost_price']?>" required class="update_currency" rowid="<?= $count?>" curtype="other"  />
            <?php $cur=($row_data['sup_currency'])?$row_data['sup_currency']:'USD';?>
            <select style="width:25%" name="<?=$name_key?>[sup_currency]" class="update_currency" rowid="<?= $count?>" curtype="other"><?=formatHTMLOptions(productGetOptionList('currency'), $cur)?></select>
        </td>
    </tr>
    <tr>
        <th>Cost Price:</th>
        <td colspan="3" >
        <input style="width:31%;background-color: #aedaae;" name="<?=$name_key?>[cprice_usd]" type="text"  value="<?=$row_data['cprice_usd']?>" class="update_currency updatemarkup" rowid="<?= $count?>" curtype="usd" changetype="salesprice"/>USD</td>
    </tr>
    <tr>
        <th>Online Price:</th>
        <td colspan="2">
            <table style="margin-left: 0px;">
            <td>
                <?php $sell_online_price=($row_data['onlineprice_usd'])?$row_data['onlineprice_usd']:$selles_price_usd;?>
                <input  name="<?=$name_key?>[onlineprice_usd]" type="text"  value="<?=$sell_online_price?>" rowid="<?= $count?>" changetype="salesprice" class="updatemarkup"/ style="width:82%;">USD
            </td>
            <th >Markup %:</th>
            <td>
                 <input  name="<?=$name_key?>[markup]" type="text"  value="<?=$row_data['markup']?>" rowid="<?= $count?>" changetype="markup" class="updatemarkup"/>
             </td>
            </table>
        </td>
        <th style="background: #ffffff;text-align: center;">
            <?php if($row_data['supplier_id']){
        $item_history=getsupplieritemhistory($row_data['supplier_id'],$row_data['product_id']);
        if(count($item_history)>1){ 
        ?>
            <span style="cursor: pointer;" onclick="opepaymenthistory(<?=$row_data['supplier_id']?>,<?= $row_data['product_id']?>);return false;"><p>Cost History</p><img src="/img/payment_status/graphimage.png" width="100px;" height="70px;" /></span>
        <?php } } ?>
        </th>
    </tr>
    <tr>
        <th>Last Update:</th>
        <td colspan="3" >
        <input style="background-color: #aedaae;" name="<?=$name_key?>[last_update]" type="checkbox" class="last_update_checked" rowid="<?= $count?>" value = "y"/></td>
    </tr>
    <?php if($row_data['supplier_id']){
        $item_history=getsupplieritemhistory($row_data['supplier_id'],$row_data['product_id']);
        if(count($item_history)>1){
    ?>
    <?php } } ?>
    <?php 
    $item_history_full=[];
     if($row_data['supplier_id']){
        $item_history_full=getsupplieritemfullhistory($row_data['supplier_id'],$row_data['product_id']);
        if(count($item_history_full)>0){
    ?>
        <tr>
            <th></th>
            <td colspan="3">
                <table class="table_grid border-class-table" style="margin-top: 10px; margin-bottom: 10px;">
                <tr>
                    <th>Date</th>
                    <th>Cost (Currency)</th>
                    <th>Cost USD</th>
                    <th>Sell Price USD</th>
                    <th>Markup %</th>
                    <th>Movement</th>
                </tr>
                <?php foreach ($item_history_full as $history) { ?>
                    <tr>
                        <td><?= date('d-m-Y',strtotime($history['created_at']))?></td>
                        <td><?= $history['cost_price']?> (<?= $history['sup_currency']?>)</td>
                        <td><?= $history['cprice_usd']?></td>
                        <td><?= $history['sales_price']?></td>
                        <td><?= $history['markup']?> %</td>
                         <td><?= $history['movement']?> %</td>
                    </tr>
                <?php } ?>
                </table>
            </td>
        </tr>
        <tr>
            <th></th>
            <th colspan="3"><input type="button" value="View All History" onclick="viewallhistory(<?= $row_data['supplier_id'] ?>,<?= $row_data['product_id'] ?>)" style="float: left; margin-left: 5px;"/></th>
        </tr>
    <?php } } ?>
    <tr>
        <th style="font-size: 14px;"><b style="border: 1px solid red;">PURCHASE DATA</b></th>
        <th colspan="3"></th>
    </tr>
    <tr>
        <th>Total Units Purchased:</th>
        <td colspan="3" >
            <?php $total_purchase=($row_data['supplier_id'])?totalunitPurchasesSupplierProductsFromDatabase($row_data['product_id'],$row_data['supplier_id']):0;?>
        <input style="width:33%"  type="text"  value="<?= $total_purchase ?>" readonly/></td>
    </tr>
    <tr>
        <th>Total purchase value:</th>
        <td colspan="3" >
            <?php $total_cost_price=($row_data['supplier_id'])?supplierTotalCostPrice($row_data['product_id'],$row_data['supplier_id']):0;?>
        <input style="width:33%" name="<?=$name_key?>[purchase_value]" type="text"  value="<?=$total_cost_price ?>" readonly/></td>
    </tr>
    <tr>
        <th>Current average cost:</th>
        <td colspan="2">
            <?php
                $averate_cost=0.00;
                if($row_data['product_id']>0){

                     $averate_cost= productAverageCostPrice($row_data['product_id'],$row_data['supplier_id']);
                }

            ?>
            <input  style="width: 42%;" name="<?=$name_key?>[purchase_average_price]" type="text"  value="<?= $averate_cost ?>" readonly>
        </td>
        <th style="background: #ffffff;text-align: center;">
        <?php if($row_data['supplier_id']){
           $temp = array();
            if (isset($item_inventory) && is_array($item_inventory)) {
                foreach ($item_inventory as $row) {
                    if ($row['supplier_id'] == $row_data['supplier_id']) {
                        $temp[] = $row;
                    }
                }
            }
        if(count($temp)>1){ 
        ?>
            <span style="cursor: pointer;" onclick="opepaymenthistorypurchase(<?=$row_data['supplier_id']?>,<?= $row_data['product_id']?>);return false;"><p>Purchase History</p><img src="/img/payment_status/graphimage.png" width="100px;" height="70px;" /></span>
        <?php } } ?>
        </th>
    </tr>
    <tr>
        <th></th>
        <th></th>
        <td colspan="3">
            <?php
            if($row_data['supplier_id']){
               if (count($temp)) {
                    $columns = array_column($temp, "created_at");
                    array_multisort($columns, SORT_DESC, $temp);
            ?>
                    <div><table class="table_grid border-class-table" style="width:100%">
                        <tr><th colspan="8"><b>Purchase Summary - <?=$supplier_list[$row_data['supplier_id']]?></b></th></tr>
                        <tr>
                            <th style="background:#EEF;width:5%"><b>Date</b></th>
                            <th style="background:#EEF;width:5%"><b>Qty</b></th>
                            <th style="background:#EEF;width:10%"><b>Unit Cost (USD)</b></th>
                            <th style="background:#EEF;width:15%"><b>Unit Freight Cost (USD)</b></th>
                            <th style="background:#EEF;width:10%"><b>Unit Total Cost (USD)</b></th>
                            <th style="background:#EEF;width:10%"><b>Unit Order Cost (USD)</b></th>
                            <th style="background:#EEF;width:45%"><b>Comments</b></th>
                        </tr>
            <?php
                    $alt = false;
                    $count = 0;
                    foreach ($temp as $row) {
                        $count++;
                        /*if ($count > 5) {
                            break;
                        }*/
                        ob_start();
                        ?>
                        Original Price: <?= convertCurrency($row['unit_cost'], $row['unit_currency'], '', false, $item['precision']) ?> <?= $row['unit_currency'] ?>
                        <?php
                                    $tt_in = ob_get_clean();

                                    ob_start();
                        ?>
                        Original Freight: <?= convertCurrency($row['unit_freight'], $row['unit_currency'], '', false, $item['precision']) ?> <?= $row['unit_currency'] ?>
                        <?php
                                    $tt_in2 = ob_get_clean();
                        ?>
                        <tr style="text-align:center;" class="<?=($alt = !$alt)?(($count>5)?'alt hide-row row-hide-check-sup-'.$supplier['supplier_id'].'':'alt') : (($count>5)?' hide-row row-hide-check-sup-'.$supplier['supplier_id'].'':'')?>">
                            <td><?=date(_('d/m/Y'), (is_null($row['created_at'])) ? time() : strtotime($row['created_at']))?></td>
                            <td>
                                <?php
                                  if($row['color_id']){
                                    $color_data=productGenericFromDatabase($row['color_id'],'product_colour') ;
                                ?>
                                <span onmouseover="<?php hecho("tt_in(" . jecho($color_data['colour'], 1) . ")") ?>" onmouseout="tt_out()" style="height:12px;width: 15px; border-radius: 50%; display: inline-block; text-align: center; padding-top: 3px; margin-right: 4px!important;background-color: <?php echo $color_data['color_val'];?>" ></span>
                                <?php } ?>
                                <?=$row['movement']?>
                                

                            </td>
                            <td onmouseover="<?php hecho("tt_in(" . jecho($tt_in, 1) . ")") ?>" onmouseout="tt_out()">$<?php 
                                echo number_format((float)  convertCurrency($row['unit_cost'], $row['unit_currency'], 'USD'), 2, '.', '');

                            ?></td>
                            <td onmouseover="<?php hecho("tt_in(" . jecho($tt_in2, 1) . ")") ?>" onmouseout="tt_out()">$<?php
                           
                                echo number_format((float) convertCurrency($row['unit_freight'], $row['unit_currency'], 'USD'), 2, '.', '');
                            ?></td>
                            <td>
                                <?php 
                                $unit_total_cost=$row['unit_cost']+$row['unit_freight'];
                                $unit_total_cost = convertCurrency($unit_total_cost, $row['unit_currency'], 'USD');
                                ?>
                               $<?php 
                               echo number_format((float) $unit_total_cost, 2, '.', '');
                               ?>
                            </td>
                            <td>
                                <?php 
                                $unit_freight_total_cost=$unit_total_cost*$row['movement'];

                                ?>
                                $<?php //$cost=convertCurrency($unit_freight_total_cost, $row['currency'], 'USD', false, $item['precision']);
                                    echo number_format((float) $unit_freight_total_cost, 2, '.', '');
                                ?>
                            </td>
                            <?php
                                // Find position of $supplier_name in $comment
                                $position = strpos($row['comments'], $supplier_list[$row_data['supplier_id']]);

                                // Ensure $supplier_name was found in $comment
                                if ($position !== false) {
                                    // Extract the substring after $supplier_name
                                    $purchase_info = substr($row['comments'], $position + strlen($supplier_list[$row_data['supplier_id']]));

                                    // Trim to remove any leading or trailing spaces and commas
                                    $purchase_info = trim($purchase_info, ", ");
                                } else {
                                    $purchase_info = $row['comments'];
                                }
                            ?>
                            <td style="text-align:left;"><?=nl2br($purchase_info)?></td>
                        </tr>
            <?php
                    }
            ?>
                    </table></div>
                    <!-- <div style="text-align:center;">
                        <input type="button"  value="View All (<?= count($temp) ?>)" class="unfinished Show_supplier_purchase" sup-id="<?= $supplier['supplier_id'] ?>" id="Show_supplier_purchase-<?= $supplier['supplier_id'] ?>"/>
                    </div> -->
                    <hr size="1" color="black" />
            <?php
                } }
            ?>  

        </td>
    </tr>

    <tr><th></th><th></th><th colspan="3" style="text-align:center;padding: 10px;">
        <!-- <input type="button" value="Set Cost Price" name="item_suppliers_set_cost[<?=$count ?>]" class="item_suppliers_set_cost unfinished" style="display:none;float: left; margin-left: 5px;" /> -->
                <?php if(count($item_history_full)>0){ ?>
        <input type="button" value="View All History" onclick="viewpurchaseallhistory(<?= $row_data['supplier_id'] ?>,<?= $row_data['product_id'] ?>)" style="float: left; margin-left: 5px;"/>
    <?php } ?>
    <input type="button" value="Update"<?=(1) ? ' onclick="if(productValidate()) this.form.submit();"' : ''?>  style="float: left; margin-left: 5px;"/>
    <input type="button" value="Delete" class="item_suppliers_delete" data-id="<?= $row_data['id']?>" data-count="<?= $count?>"style="display:none;float: right; margin-right: 5px;" />

    </th></tr>
</table>
<?php
    return ob_get_clean();
}

$count = 0;
?>

<script type="text/javascript">
function supplierContactGetRows() {
    var con_table = $(".supplier_class");
    var con_rows = [];
    var con_rows2 = {};

    if (con_table != null) {
        for (var i = 0; i < con_table.length; i++){ 
                if (con_table[i].id.substr(0, 19) == 'item_suppliers_row[') {
                var key = con_table[i].id.substr(19);
                key = key.substr(0, key.length - 1);
                if (con_rows2[key] == null) {
                    con_rows2[key] = true;
                    con_rows[con_rows.length] = key;
                }
            }
        }
    }
   return con_rows;
}
function supplierDefaultSelect(row) {
    var con_rows = supplierContactGetRows();
    
        for (var i = 0; i < con_rows.length; i++){
            if (con_rows[i] == row) {
                if($('input[name="item[stock_type]"]:checked').val()=='consignment'){
                    getElement('item_suppliers[' + con_rows[i] + '][main]').checked = true;
                    //getElement('supplier_default[' + con_rows[i] + ']').style.display = '';
                    
                    setValue('item[cost_price]',toCurrency(getValue('item_suppliers['+con_rows[i]+'][cost_price]'),getValue('item_suppliers['+con_rows[i]+'][sup_currency]'),getValue('item[consignment_cost_currency]')));
                    setValue('item[sales_price]',toCurrency(getValue('item_suppliers['+con_rows[i]+'][onlineprice_usd]'),'USD',getValue('item[consignment_sales_currency]')));
                    console.log(getValue('item[sales_price]'))
                    setValue('item[markup]',getValue('item_suppliers['+con_rows[i]+'][markup]'));
                    // var salespriceusd=toCurrency(getValue('item[sales_price]'),getValue('item[consignment_sales_currency]'),'USD');
                    // var costpriceusd=toCurrency(getValue('item[cost_price]'),getValue('item[consignment_cost_currency]'),'USD');
                    var salespriceusd = toCurrency(getValue('item_suppliers['+con_rows[i]+'][onlineprice_usd]'),'USD','USD');
                    var costpriceusd=toCurrency(getValue('item_suppliers['+con_rows[i]+'][cost_price]'),getValue('item_suppliers['+con_rows[i]+'][sup_currency]'),'USD');
                    setValue('item[product_profitus]',(salespriceusd-costpriceusd).toFixed(2));
                    setValue('item[consignment_qty]',getValue('item_suppliers['+con_rows[i]+'][qty]'));
                    setValue('item[average_cost]',getValue('item_suppliers['+con_rows[i]+'][cprice_usd]'));
                    // updated code on 31st july 24
                    // setValue('item[consignment_cost_price]',toCurrency(getValue('item_suppliers['+con_rows[i]+'][cost_price]'),getValue('item_suppliers['+con_rows[i]+'][sup_currency]'),getValue('item[consignment_cost_currency]')));
                    setValue('item[consignment_cost_price]',getValue('item_suppliers['+con_rows[i]+'][cost_price]'));
                    setValue('item[consignment_cost_currency]',getValue('item_suppliers['+con_rows[i]+'][sup_currency]'));
                    setValue('item[sales_price]',toCurrency(getValue('item_suppliers['+con_rows[i]+'][onlineprice_usd]'),'USD',getValue('item[consignment_sales_currency]')));
                    setValue('item[consignment_markup]',getValue('item_suppliers['+con_rows[i]+'][markup]'));
                    if(getValue('item[sales_price_container]') < 1){ 
                       $('input[name="item[sales_price_container]"]').val(getValue('item[sales_price]')).trigger('change');
                    }
                    if(getValue('item[sales_price_distributor]') < 1){
                        $('input[name="item[sales_price_distributor]"]').val(getValue('item[sales_price]')).trigger('change');
                    }
                    if(getValue('item[sales_price_arcooda]') < 1){
                       $('input[name="item[sales_price_arcooda]"]').val(getValue('item[sales_price]')).trigger('change');
                    }
                   /* var sourceAnchor = document.getElementById('supplierProductAnchor['+con_rows[i]+']');
                    var targetAnchor = document.getElementById("supplierAnchor");

                    // Copy the href attribute and text content
                    var newHref = sourceAnchor.href;
                    var newText = sourceAnchor.textContent;*/

                    // Update the target anchor
                    /*targetAnchor.href = newHref;
                    targetAnchor.textContent = newText;*/
                }
        }
        else {
            getElement('item_suppliers[' + con_rows[i] + '][main]').checked = false;
           //getElement('supplier_default[' + con_rows[i] + ']').style.display = 'none';
        }
    }
}
function viewallhistory(supplier_id,product_id){
    openWindow('displaySupplierProductHistory.php?action=view&supplier_id='+supplier_id+'&product_id='+product_id+'', 700, 300, true);
}
function viewpurchaseallhistory(supplier_id,product_id){
    openWindow('displayPurchaseProductHistory.php?action=view&supplier_id='+supplier_id+'&product_id='+product_id+'', 700, 300, true);
}
var exRates = <?php jecho(getExchangeRateList()) ?>;
function sprint_price(price)
{
    var sprice=String(price+0.005);
    var counting=false;
    var thecount=0;
    var outtext="";
    for(var i=0;(i<sprice.length && thecount<2);i++)
    {
        var ch=sprice.charAt(i);
        if(counting) thecount++;
        if(ch=='.') counting=true;
        outtext+=ch;
    }
    return outtext;
}
function toCurrency(price,fromCurrency,toCurrency)
{
    fromCurrency=fromCurrency.slice(0,3).toUpperCase();
    toCurrency=toCurrency.slice(0,3).toUpperCase();
    if(fromCurrency==toCurrency) return price;
    var r = exRates[fromCurrency]/exRates[toCurrency];
    var newP = price * r;
    return sprint_price(newP);
}
$('document').ready(function(){
    $('body').on('change', '.update_quantity', function(){
        var rowid=$(this).attr('rowid');
         if(($('input[name="item_suppliers['+rowid+'][main]"]').is(':checked') || supplierContactGetRows().length==1) && $('input[name="item[stock_type]"]:checked').val()=='consignment'){
             setValue('item[consignment_qty]',$(this).val());
         }
    });    
    $('body').on('change', '.update_currency', function(){
        var rowid=$(this).attr('rowid'); 
        var curtype=$(this).attr('curtype'); 
        if(curtype=='usd'){
            var fromElem='item_suppliers['+rowid+'][cprice_usd]';
            var toElem='item_suppliers['+rowid+'][cost_price]';
            var to=getValue('item_suppliers['+rowid+'][sup_currency]');
            var from='USD';
        }else if(curtype=='other'){
            var toElem='item_suppliers['+rowid+'][cprice_usd]';
            var fromElem='item_suppliers['+rowid+'][cost_price]';
            var from=getValue('item_suppliers['+rowid+'][sup_currency]');
            var to='USD';
        }else{
            //alert('not found');
        }
        setValue(toElem, toCurrency(getValue(fromElem), from, to));
        var salesprice=getValue('item_suppliers['+rowid+'][onlineprice_usd]');
        var costprice=getValue('item_suppliers['+rowid+'][cprice_usd]');
        var markup=(((salesprice-costprice)/costprice)*100).toFixed(2)
        setValue('item_suppliers['+rowid+'][markup]', markup);
        setValue('item_suppliers['+rowid+'][sales_price]', salesprice);
        if(($('input[name="item_suppliers['+rowid+'][main]"]').is(':checked') || supplierContactGetRows().length==1) && $('input[name="item[stock_type]"]:checked').val()=='consignment'){
            setValue('item[consignment_cost_price]',toCurrency(getValue('item_suppliers['+rowid+'][cost_price]'),getValue('item_suppliers['+rowid+'][sup_currency]'),getValue('item[consignment_cost_currency]')));
            setValue('item[sales_price]',toCurrency(getValue('item_suppliers['+rowid+'][onlineprice_usd]'),'USD',getValue('item[consignment_sales_currency]')));
            setValue('item[consignment_markup]',getValue('item_suppliers['+rowid+'][markup]'));
            var salespriceusd=toCurrency(getValue('item[sales_price]'),getValue('item[consignment_sales_currency]'),'USD');
            var costpriceusd=toCurrency(getValue('item[consignment_cost_price]'),getValue('item[consignment_cost_currency]'),'USD');
            setValue('item[product_profitus]',(salespriceusd-costpriceusd).toFixed(2));
        }
    });
    
    $('body').on('click', '.last_update_checked', function(){
        var rowid=$(this).attr('rowid'); 
        if($(this).is(":checked")){
            $('#item_suppliers').append('<input type ="hidden" value="<?=formatDateTime()?>" id="item_suppliers['+rowid+'][updated_at]" name="item_suppliers['+rowid+'][updated_at]"/>');
        }else{
            var theTable=getElement('item_suppliers['+rowid+'][updated_at]');
            if(theTable) theTable.parentNode.removeChild(theTable);
        }
    });
      $('body').on('change', '.updatemarkup', function(){
            var rowid=$(this).attr('rowid'); 
            var changetype=$(this).attr('changetype'); 
            if(changetype=='markup'){
                var markup=getValue('item_suppliers['+rowid+'][markup]');
                var costprice=getValue('item_suppliers['+rowid+'][cprice_usd]');
                var salesprice=(((costprice*markup)/100)+parseInt(costprice)).toFixed(2);
                setValue('item_suppliers['+rowid+'][onlineprice_usd]', salesprice);
                 setValue('item_suppliers['+rowid+'][sales_price]', salesprice);
            }else if(changetype=='salesprice'){
                var salesprice=getValue('item_suppliers['+rowid+'][onlineprice_usd]');
                var costprice=getValue('item_suppliers['+rowid+'][cprice_usd]');
                var markup=(((salesprice-costprice)/costprice)*100).toFixed(2)
                setValue('item_suppliers['+rowid+'][markup]', markup);
                setValue('item_suppliers['+rowid+'][sales_price]', salesprice);
            }else{
                //alert('not found');
            }
            if(($('input[name="item_suppliers['+rowid+'][main]"]').is(':checked') || supplierContactGetRows().length==1) && $('input[name="item[stock_type]"]:checked').val()=='consignment'){
                setValue('item[consignment_cost_price]',toCurrency(getValue('item_suppliers['+rowid+'][cost_price]'),getValue('item_suppliers['+rowid+'][sup_currency]'),getValue('item[consignment_cost_currency]')));
                setValue('item[sales_price]',toCurrency(getValue('item_suppliers['+rowid+'][onlineprice_usd]'),'USD',getValue('item[consignment_sales_currency]')));
                setValue('item[consignment_markup]',getValue('item_suppliers['+rowid+'][markup]'));


                var salespriceusd=toCurrency(getValue('item[sales_price]'),getValue('item[consignment_sales_currency]'),'USD');
                var costpriceusd=toCurrency(getValue('item[consignment_cost_price]'),getValue('item[consignment_cost_currency]'),'USD');
                setValue('item[product_profitus]',(salespriceusd-costpriceusd).toFixed(2));
            }
        });
    
})
//<![CDATA[
let ProductSupplier = {
    row: function (elem) {
        return $(elem).attr("name").split("[")[1].split("]")[0];
    },
    rows: function () {
        var result = [];
        var self = this;
        $(this.selector("supplier_id")).each(function() {
            result.push(self.row(this));
        });
        return result;
    },
    selector: function (field, row) {
        if (Array.isArray(field)) {
            var result = [];
            while (field.length) {
                result.push(this.selector(field.pop(), row));
            }
            return result.join(",");
        }
        if (Array.isArray(row)) {
            var result = [];
            while (row.length) {
                result.push(this.selector(field, row.pop()));
            }
            return result.join(",");
        }
        if (row === undefined) {
            return "[name^='item_suppliers['][name$='][" + field + "]']";
        }
        return "[name='item_suppliers[" + row + "][" + field + "]']";
    },
    get: function (field, row) {
        return $(this.selector(field, row)).val();
    },
    set: function (field, row, value) {
        var $elem = $(this.selector(field, row));
        if (value !== $elem.val()) {
            $elem.val(value).change();
        }
    }
};

function item_suppliers_apply_events(scope) {
    $(".item_suppliers_delete", scope).click(function() {
        var $table = $(this).closest("table");
        if (confirm("Are you sure you want to delete this supplier??")) {
           //var row = ProductSupplier.row(this);
            setValue("item_suppliers_deleted", getValue("item_suppliers_deleted") + "," + $(this).attr('data-id'));
            var theTable=getElement('item_suppliers_row['+ $(this).attr('data-count')+']');
            if(theTable) theTable.parentNode.removeChild(theTable);
            //$table.detach();
        }
        return false;
    }).toggle(true);

    $(".item_suppliers_search", scope).click(function() {
        //var row   = ProductSupplier.row(this);
        var query = {};
        query.action = "list";
        query.return_message = 'add_supplier';
        //query.search_for = ProductSupplier.get("supplier_name", row);
        window.open("/webadmin/supplier_new/?" + $.param(query));
        return false;
    }).toggle(true);

    $(".item_suppliers_set_cost", scope).click(function() {
        var row           = ProductSupplier.row(this);
        var qty           = ProductSupplier.get("qty", row);
        Product.set("cost_price", bcdiv(ProductSupplier.get("offer_price", row), qty, 4));
        Product.set("cost_currency", ProductSupplier.get("currency", row));
    }).toggle(true);
}

var item_suppliers_count = <?= count($item_suppliers) ?>;

$(function() {
    item_suppliers_apply_events();

    $(".item_suppliers_add").click(function() {

        var $table = $(<?= strtr(jecho(item_suppliers_row('[ROW_COUNT]', array(
            'product_id' => $item['id'],
            'currency' => 'USD',
        ), $selles_price_usd), 1), array(
            '[ROW_COUNT]' => '" + item_suppliers_count + "',
        )) ?>);
        item_suppliers_count++;
        $("#item_suppliers").append($table);
        item_suppliers_apply_events($table);
        return false;
    }).toggle(true);
});
function setSupplierData(data){
    if(data['supplier_id']!=null && data['return_message']!=null)
    {
         var $table = $(<?= strtr(jecho(item_suppliers_row('[ROW_COUNT]', array(
            'product_id' => $item['id'],
            'currency' => 'USD',
        ), $selles_price_usd), 1), array(
            '[ROW_COUNT]' => '" + item_suppliers_count + "',
        )) ?>);
        $("#item_suppliers").append($table);
        item_suppliers_apply_events($table);
        setValue("item_suppliers["+item_suppliers_count+"][supplier_id]", data['supplier_id']);
        setValue("item_suppliers["+item_suppliers_count+"][supplier_name]",data['company']);
        item_suppliers_count++;
        return false;
        
    }
}
function opepaymenthistory(sup_id,product_id){
    openWindow('displayHistoryGraph.php?action=add&supplier_id='+sup_id+'&product_id='+product_id+'', 700, 700, true);
}
function opepaymenthistorypurchase(sup_id,product_id){
    openWindow('DispalyGraphPurchase.php?action=add&supplier_id='+sup_id+'&product_id='+product_id+'', 700, 700, true);
}
//]]>
</script>

<?=formatHTMLTitle(
    '<a href="#" onclick="toggleSection(&quot;sectionProductSuppliers&quot;);return false">Product Suppliers</a>',
    '<a href="#" class="item_suppliers_search" style="display:none;">Add New</a>'
)?>

<div id="sectionProductSuppliers">
<div id="item_suppliers">
<?php
foreach ($item_suppliers as $supplier) {
    echo item_suppliers_row($count++, $supplier,$selles_price_usd,$item_inventory);
}
?>
    </div>
    <input type="hidden" name="item_suppliers_deleted" value="" />
    <br />
    <br />
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionProductSuppliers_hideNote">
    Product Suppliers can be viewed by Clicking "Product Suppliers" above<br />
</div>
<script type="text/javascript">
    var htmltext='';
    $('.Show_supplier_purchase').click(function(){
        var supid=$(this).attr('sup-id');
        if($(this).val()!='Show Less'){
            htmltext=$(this).val();
        }
        if($('.row-hide-check-sup-'+supid).hasClass('hide-row')){
            $('.row-hide-check-sup-'+supid).removeClass('hide-row');
            $(this).val('Show Less');
        }else{
            $(this).val(htmltext);
            $('.row-hide-check-sup-'+supid).addClass('hide-row');
            //$(window).scrollTop($('#Show_supplier_purchase-'+supid).offset().top);
        }

    });
</script>
<!-- /_product_suppliers.tpl -->
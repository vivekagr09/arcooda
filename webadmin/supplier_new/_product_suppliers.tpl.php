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

moduleLoad('product');

if (!isset($item_suppliers) || !is_array($item_suppliers)) {
    return;
}

$supplier_list = productGetOptionList('supplier_id');

function item_suppliers_row($count = '[ROW_COUNT]', $row_data = array(),$selles_price_usd=0)
{
    global $_GLOBALS, $supplier_list;

    $name_key = 'item_suppliers[' . $count . ']';
    $hidden   = array(
        'id'           => $row_data['id'],
        'supplier_id'  => $row_data['supplier_id'],
        'product_id'   => $row_data['product_id'],
        'type'         => $row_data['type'],
        'offer_price'  => $row_data['offer_price'],
        'currency'     => $row_data['currency'],
        'qty'          => $row_data['qty'],
        'preferred'    => $row_data['preferred'],
    );
    ob_start();
?>
<table class="table_fields supplier_class" id="item_suppliers_row[<?= $count ?>]" style="margin-bottom: 10px;">

    <tr id="supplier_product_rowa[<?=$count?>]">
        <th rowspan="11" style="width:10%;text-align:center;vertical-align:middle;">
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
                    <a href="/webadmin/supplier/?action=view&amp;id=<?= $row_data['supplier_id'] ?>" target="_blank" class="unfinished"><?php hecho($supplier_list[$row_data['supplier_id']]) ?></a>
        <?php
            } else {
        ?>
            <input type="button" name="item_suppliers_search[<?=$count ?>]" value="Search" class="unfinished item_suppliers_search" style="float:right;display:none;" />
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
        <th>Qty Available:</th>
        <td colspan="3" >
        <input style="width:33%" name="<?=$name_key?>[qty]" type="text"  value="<?=$row_data['qty']?>" /></td>
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
            <input style="width:25%" name="<?=$name_key?>[cost_price]" type="text"  value="<?=$row_data['cost_price']?>" class="update_currency" rowid="<?= $count?>" curtype="other"/>
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
    <?php } } ?>
    <tr><th></th><th colspan="3" style="text-align:center;padding: 10px;">
        <input type="button" value="Set Cost Price" name="item_suppliers_set_cost[<?=$count ?>]" class="item_suppliers_set_cost unfinished" style="display:none;float: left; margin-left: 5px;" />
                <?php if(count($item_history_full)>0){ ?>
        <input type="button" value="View All History" onclick="viewallhistory(<?= $row_data['supplier_id'] ?>,<?= $row_data['product_id'] ?>)" style="float: left; margin-left: 5px;"/>
    <?php } ?>
    <input type="button" value="Update"<?=(1) ? ' onclick="if(productValidate()) this.form.submit();"' : ''?>  style="float: left; margin-left: 5px;"/>
    <input type="button" value="Delete" class="item_suppliers_delete" data-id="<?= $row_data['id']?>" data-count="<?= $count?>"style="display:none;float: right; margin-right: 5px;" />

    </th></tr>
</table>
<?php
    return ob_get_clean();
}

$count = 1;
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
            getElement('item_suppliers[' + con_rows[i] + '][main]').checked = true;
            //getElement('supplier_default[' + con_rows[i] + ']').style.display = '';
            
            setValue('item[cost_price]',toCurrency(getValue('item_suppliers['+con_rows[i]+'][cost_price]'),getValue('item_suppliers['+con_rows[i]+'][sup_currency]'),getValue('item[cost_currency]')));
            setValue('item[sales_price]',toCurrency(getValue('item_suppliers['+con_rows[i]+'][onlineprice_usd]'),'USD',getValue('item[sales_currency]')));
            setValue('item[markup]',getValue('item_suppliers['+con_rows[i]+'][markup]'));
            var salespriceusd=toCurrency(getValue('item[sales_price]'),getValue('item[sales_currency]'),'USD');
            var costpriceusd=toCurrency(getValue('item[cost_price]'),getValue('item[cost_currency]'),'USD');
            setValue('item[product_profitus]',(salespriceusd-costpriceusd).toFixed(2));
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
        if($('input[name="item_suppliers['+rowid+'][main]"]').is(':checked') || supplierContactGetRows().length==1){
            setValue('item[cost_price]',toCurrency(getValue('item_suppliers['+rowid+'][cost_price]'),getValue('item_suppliers['+rowid+'][sup_currency]'),getValue('item[cost_currency]')));
            setValue('item[sales_price]',toCurrency(getValue('item_suppliers['+rowid+'][onlineprice_usd]'),'USD',getValue('item[sales_currency]')));
            setValue('item[markup]',getValue('item_suppliers['+rowid+'][markup]'));
            var salespriceusd=toCurrency(getValue('item[sales_price]'),getValue('item[sales_currency]'),'USD');
            var costpriceusd=toCurrency(getValue('item[cost_price]'),getValue('item[cost_currency]'),'USD');
            setValue('item[product_profitus]',(salespriceusd-costpriceusd).toFixed(2));
        }
        updateProfits();
    });
      $('body').on('change', '.updatemarkup', function(){
            var rowid=$(this).attr('rowid'); 
            var changetype=$(this).attr('changetype'); 
            if(changetype=='markup'){
                var markup=getValue('item_suppliers['+rowid+'][markup]');
                var costprice=getValue('item_suppliers['+rowid+'][cprice_usd]');
                var salesprice=(((costprice*markup)/100)+parseInt(costprice)).toFixed(2);
                setValue('item_suppliers['+rowid+'][onlineprice_usd]', salesprice);
            }else if(changetype=='salesprice'){
                var salesprice=getValue('item_suppliers['+rowid+'][onlineprice_usd]');
                var costprice=getValue('item_suppliers['+rowid+'][cprice_usd]');
                var markup=(((salesprice-costprice)/costprice)*100).toFixed(2)
                setValue('item_suppliers['+rowid+'][markup]', markup);
            }else{
                //alert('not found');
            }
            if($('input[name="item_suppliers['+rowid+'][main]"]').is(':checked') || supplierContactGetRows().length==1){
                setValue('item[cost_price]',toCurrency(getValue('item_suppliers['+rowid+'][cost_price]'),getValue('item_suppliers['+rowid+'][sup_currency]'),getValue('item[cost_currency]')));
                setValue('item[sales_price]',toCurrency(getValue('item_suppliers['+rowid+'][onlineprice_usd]'),'USD',getValue('item[sales_currency]')));
                setValue('item[markup]',getValue('item_suppliers['+rowid+'][markup]'));
                var salespriceusd=toCurrency(getValue('item[sales_price]'),getValue('item[sales_currency]'),'USD');
                var costpriceusd=toCurrency(getValue('item[cost_price]'),getValue('item[cost_currency]'),'USD');
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
        var row   = ProductSupplier.row(this);
        var query = {};
        query.action = "list";
        query.return_message = row;
        query.search_for = ProductSupplier.get("supplier_name", row);
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

var item_suppliers_count = <?= max(100, count($item_suppliers)) ?>;

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
function setMemberData(data){
    if(data['supplier_id']!=null && data['return_message']!=null)
    {
        console.log(data);
        setValue("item_suppliers["+data['return_message']+"][supplier_id]", data['supplier_id']);
        setValue("item_suppliers["+data['return_message']+"][supplier_name]",data['company']);
        /*setValue("item_suppliers["+data['return_message']+"][sup_code]",data['supplier_number']);*/
        
    }
}
function opepaymenthistory(sup_id,product_id){
    openWindow('displayHistoryGraph.php?action=add&supplier_id='+sup_id+'&product_id='+product_id+'', 700, 700, true);
}
//]]>
</script>

<?=formatHTMLTitle(
    '<a href="#" onclick="toggleSection(&quot;sectionProductSuppliers&quot;);return false">Product Suppliers</a>',
    '<a href="#" class="item_suppliers_add" style="display:none;">Add New</a>'
)?>

<div id="sectionProductSuppliers">
    <div id="item_suppliers">
<?php
foreach ($item_suppliers as $supplier) {
    echo item_suppliers_row($count++, $supplier,$selles_price_usd);

    $temp = array();
    if (isset($item_inventory) && is_array($item_inventory)) {
        foreach ($item_inventory as $row) {
            if ($row['supplier_id'] == $supplier['supplier_id']) {
                $temp[] = $row;
            }
        }
    }
    if (count($temp)) {
?>
        <div><table class="table_grid">
            <tr><th colspan="6"><b>Purchase Summary - <?=$supplier_list[$supplier['supplier_id']]?></b></th></tr>
            <tr>
                <th style="background:#EEF;width:5%"><b>Date</b></th>
                <th style="background:#EEF;width:5%"><b>Qty</b></th>
                <th style="background:#EEF;width:10%"><b>Unit Cost (USD)</b></th>
                <th style="background:#EEF;width:15%"><b>Unit Freight Cost (USD)</b></th>
                <th style="background:#EEF;width:65%"><b>Comments</b></th>
            </tr>
<?php
        $alt = false;
        $count = 0;
        foreach ($temp as $row) {
            $count++;
            if ($count > 5) {
                break;
            }
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
            <tr style="text-align:center;"<?=($alt = !$alt) ? ' class="alt"' : ''?>>
                <td><?=date(_('Y-m-d'), (is_null($row['created_time'])) ? time() : $row['created_time'])?></td>
                <td><?=$row['movement']?></td>
                <td onmouseover="<?php hecho("tt_in(" . jecho($tt_in, 1) . ")") ?>" onmouseout="tt_out()">$<?=convertCurrency($row['unit_cost_usd'], 'USD', '', false, $item['precision'])?></td>
                <td onmouseover="<?php hecho("tt_in(" . jecho($tt_in2, 1) . ")") ?>" onmouseout="tt_out()">$<?=convertCurrency($row['unit_freight_usd'], 'USD', '', false, $item['precision'])?></td>
                <td style="text-align:left;"><?=nl2br($row['comments'])?></td>
            </tr>
<?php
        }
?>
        </table></div>
        <div style="text-align:center;">
            <input type="button" value="View All (<?= count($temp) ?>)" class="unfinished" />
        </div>
        <hr size="1" color="black" />
<?php
    }
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

<!-- /_product_suppliers.tpl -->
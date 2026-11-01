<?php
include_once __DIR__ . '/common.inc.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

moduleLoad('product');

include_once __DIR__ . '/header.inc.php';
$id        = intval($_REQUEST['id']);
$item_list = $_REQUEST['item_list'];
$big_list = $all_products = array();
$supplier_id=$_GET['supplier_id'];
if (count($item_list)) {
    $big_list = $item_list;
} elseif ($id) {
    $big_list[] = $id;
}
if($_POST['action']=='add-product'){
     if (array_key_exists('item_suppliers', $_POST)) {
        $datapost=productSuppliersFromPost($_POST['item_suppliers'],'product');
         productSuppliersHistoryToDatabase(0,0, productSuppliersHistoryFromPost($_POST['item_suppliers']));
        productSuppliersToDatabase($id, productSuppliersFromPost($_POST['item_suppliers'],'product'));
    }
    ?>
    <script type="text/javascript">
         if (window.opener != null && window.opener.supplier_products_data != null) 
            window.opener.supplier_products_data();
            else alert("Unable to send tag back to form");
            window.close();
    </script>
<?php
}
function item_suppliers_row($count = '[ROW_COUNT]', $row_data = array(),$selles_price_usd=0)
{
    global $_GLOBALS, $supplier_list;

    $name_key = 'item_suppliers[' . $count . ']';
    $hidden   = array(
        'id'           => '',
        'supplier_id'  => $row_data['supplier_id'],
        'product_id'   => $row_data['id'],
        //'product_name' => $row_data['product_name'],
        'type'         => $row_data['type'],
        'offer_price'  => $row_data['offer_price'],
        'currency'     => $row_data['currency'],
        'qty'          => 0,
        'preferred'    => $row_data['preferred'],
    );
    ob_start();
?>
<table class="table_fields supplier_class" id="item_suppliers_row[<?= $count ?>]" style="margin-bottom: 10px;">
    <tr>
        <th>PRODUCT FOR :</th>
        <td style="padding: 20px">
            <?php hecho($row_data['name']) ?>
        </td>
    </tr>
    <tr id="supplier_product_rowa[<?=$count?>]">
        <th style="background:#F34B62">
            <?=formatHTMLInputs($hidden, $name_key)?>
            Supplier :
        </th>
        <td style="background:#F34B62" colspan="3">
      
        <input type="text" name="<?=$name_key?>[supplier_name]" value="<?php hecho($row_data['supplier_name']) ?>" style="width:70%;" />
        </td>
    </tr>
    <tr>
        <th>Supplier Code :</th>
        <td colspan="3"><input class="wide" type="text" name="<?=$name_key?>[sup_code]" >
        </td>
    </tr>
    <tr>
        <th>Supplier Product Name :</th>
        <td colspan="3"><input class="wide" type="text" name="<?=$name_key?>[sup_name]" /></td>
    </tr>
    <tr>
        <th>Qty Available:</th>
        <td colspan="3" >
        <input style="width:33%" name="<?=$name_key?>[qty]" type="text"  /></td>
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
        <input style="width:98%" name="<?=$name_key?>[notes]" type="text"  /></td>
    </tr>
    <tr>
        <th>Cost Price :</th>
        <td colspan="3">
            <input style="width:25%" name="<?=$name_key?>[cost_price]" type="text"   class="update_currency" rowid="<?= $count?>" curtype="other"/>
            <?php $cur=($row_data['sup_currency'])?$row_data['sup_currency']:'USD';?>
            <select style="width:25%" name="<?=$name_key?>[sup_currency]" class="update_currency" rowid="<?= $count?>" curtype="other"><?=formatHTMLOptions(getCurrencyList(), $cur)?></select>
        </td>
    </tr>
    <tr>
        <th>Cost Price:</th>
        <td colspan="3" >
        <input style="width:31%;background-color: #aedaae;" name="<?=$name_key?>[cprice_usd]" type="text"   class="update_currency updatemarkup" rowid="<?= $count?>" curtype="usd" changetype="salesprice"/>USD</td>
    </tr>
    <tr>
        <th>Online Price:</th>
        <td colspan="2">
            <table style="margin-left: 0px;width: 100%;">
            <td>
                <?php $sell_online_price=$selles_price_usd;?>
                <input  name="<?=$name_key?>[onlineprice_usd]" type="text"  value="<?=$sell_online_price?>" rowid="<?= $count?>" changetype="salesprice" class="updatemarkup"/ style="width:82%;">USD
            </td>
            <th >Markup %:</th>
            <td>
                 <input  name="<?=$name_key?>[markup]" type="text"  rowid="<?= $count?>" changetype="markup" class="updatemarkup"/>
             </td>
            </table>
        </td>
    </tr>
</table>
<?php
    return ob_get_clean();
}

$supplier  = supplierFromDatabase($supplier_id);
?>
<!-- <form method="post">
    <input type="hidden" name="action" value="add-product"> -->
<?php
$count=1;
foreach ($big_list as $prod_id) {
    echo "<script type=\"text/javascript\">
        window.open('/webadmin/products_new/edit.php?action=edit&id=".$prod_id."', '_blank')
    </script>";

}
?>
<!-- <input type="submit" value="Submit"> 
</form> -->
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script type="text/javascript">
         if (window.opener != null && window.opener.supplier_products_data != null) 
            window.opener.supplier_products_data();
            else alert("Unable to send tag back to form");
            window.close();
    </script>
    <script>
        var exRates = <?php jecho(getExchangeRateList()) ?>;
        function toCurrency(price,fromCurrency,toCurrency)
        {
            fromCurrency=fromCurrency.slice(0,3).toUpperCase();
            toCurrency=toCurrency.slice(0,3).toUpperCase();
            if(fromCurrency==toCurrency) return price;
            var r = exRates[fromCurrency]/exRates[toCurrency];
            var newP = price * r;
            return sprint_price(newP);
        }
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
        });
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
        });
    
    })
    </script>
</head>
<body></body>
</html>

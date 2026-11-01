<!-- _pricing.tpl -->

<?php
/**
 * Product Pricing template
 *
 * Inputs:
 * - $item
 */
$default_suppler=productDefaultSuppliersFromDatabase($item['id']); 
if (!isset($item) || !is_array($item)) {
    return;
}

$dpoints = $item['precision'];
if (0 == $dpoints) {
    $dpoints = 2;
}
?>

<script type="text/javascript">
$(function() {
    var matches = {
        cost: "consignment_cost_price",
        cost_cur: "consignment_cost_currency",
        sale: "sales_currency",
        sales_price : "sales_price",
        sale_cur: "sales_currency",
        cost_usd: "product_costus",
        sale_usd: "product_offerus",
        mark: "consignment_markup",
        flux: "price_flux",
        prof_usd: "product_profitus",
        sales_price_arcooda: "consignment_sales_price_arcooda",
        product_profitus_arcooda:"product_profitus_arcooda",
        markup_arcooda: "consignment_markup_arcooda",
        sales_price_distributor: "consignment_sales_price_distributor",
        product_profitus_distributor:"product_profitus_distributor",
        markup_distributor: "consignment_markup_distributor",
        sales_price_container: "consignment_sales_price_container",
        markup_container: "consignment_markup_container",
        product_profitus_container:"product_profitus_container",
        precision: "precision",
        unitcost : "consignment_unit_cost_price",
        unitcurrency : "consignment_unit_cost_currency",
        unitcostusd : "consignment_unit_cost_price_usd",
        freightprice : "consignment_freight_price",
        freightcurrency : "consignment_freight_currency",
        freightpriceusd : "consignment_freight_price_usd",
    };
    for (var k in matches) {
        matches[k] = $("[name='item[" + matches[k] + "]']").eq(0).attr("raw_name", matches[k]).change(pricingOnChange);
    }
    function pricingOnChange() {
        var io = {};
        for (var k in matches) {
            io[k] = matches[k].val();
        }
        //io.mark = io.mark / 100.0 + 1.0;
        io.flux = (io.flux == "y");
        io.precision = Number(io.precision);
        if (io.precision <= 0) io.precision = <?=intval($item['precision'])?>;
        if (io.precision <= 0) io.precision = 2;
        console.log($(this).attr("raw_name"));
        switch ($(this).attr("raw_name")) {
            case 'sales_price':
            case 'sales_currency':
            case  'consignment_unit_cost_price':
            case  'consignment_unit_cost_currency':
            case  'consignment_freight_currency':
            case  'consignment_freight_price':
                io.unitcostusd = convertCurrency(io.unitcost, io.unitcurrency,"USD" , false, io.precision);
                io.freightpriceusd = convertCurrency(io.freightprice, io.freightcurrency,"USD" , false, io.precision);
                io.cost = (Number(io.unitcostusd) || 0) + (Number(io.freightpriceusd) || 0);
                io.cost_usd = convertCurrency(io.cost, io.cost_cur,"USD" , false, io.precision);
                if(io.sales_price_arcooda == 0){
                     io.sales_price_arcooda = io.sales_price;
                }
                if(io.sales_price_distributor == 0){
                     io.sales_price_distributor = io.sales_price;
                }
                 if(io.sales_price_container == 0){
                     io.sales_price_container = io.sales_price;
                }
                io.product_profitus_arcooda = (io.sales_price_arcooda - io.cost_usd).toFixed(2);
                io.markup_arcooda=(((io.sales_price_arcooda-io.cost_usd)/io.cost_usd)*100).toFixed(2);
                io.product_profitus_distributor = (io.sales_price_distributor - io.cost_usd).toFixed(2);;
                io.markup_distributor=(((io.sales_price_distributor-io.cost_usd)/io.cost_usd)*100).toFixed(2);
                io.product_profitus_container = (io.sales_price_container - io.cost_usd).toFixed(2);;
                io.markup_container=(((io.sales_price_container-io.cost_usd)/io.cost_usd)*100).toFixed(2);
                io.sale_usd = convertCurrency(io.sale, io.sale_cur, "USD", false, io.precision)

                io.prof_usd = convertCurrency(io.sale_usd - io.cost_usd, "USD", "", false, io.precision);
                io.mark=(((io.sale_usd-io.cost_usd)/io.cost_usd)*100).toFixed(2)   
            case 'item_cost':
                io.cost = convertCurrency(io.cost, io.cost_cur, "", false, io.precision);
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.sale_usd = convertCurrency(io.sale, io.sale_cur, "USD", false, io.precision);
                io.prof_usd = convertCurrency(io.sale_usd - io.cost_usd, "USD", "", false, io.precision);
                io.mark=(((io.sale_usd-io.cost_usd)/io.cost_usd)*100).toFixed(2)
                break;
            case 'consignment_sales_price_arcooda':
                console.log('test');
                io.cost = convertCurrency(io.cost, io.cost_cur, "", false, io.precision);
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.product_profitus_arcooda = (io.sales_price_arcooda - io.cost_usd).toFixed(2);
                io.markup_arcooda=(((io.sales_price_arcooda-io.cost_usd)/io.cost_usd)*100).toFixed(2)
                break;
            case 'consignment_sales_price_distributor':
                io.cost = convertCurrency(io.cost, io.cost_cur, "", false, io.precision);
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.product_profitus_distributor = (io.sales_price_distributor - io.cost_usd).toFixed(2);;
                io.markup_distributor=(((io.sales_price_distributor-io.cost_usd)/io.cost_usd)*100).toFixed(2)
                break;
            case 'consignment_sales_price_container':
                io.cost = convertCurrency(io.cost, io.cost_cur, "", false, io.precision);
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);;
                console.log(io.sales_price_container);
                io.product_profitus_container = (io.sales_price_container - io.cost_usd).toFixed(2);;
                io.markup_container=(((io.sales_price_container-io.cost_usd)/io.cost_usd)*100).toFixed(2)
                break;
            case 'consignment_markup_container':
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision)
                io.sales_price_container = (((io.cost_usd*io.markup_container)/100)+parseInt(io.cost_usd)).toFixed(2);
                io.product_profitus_container = (io.sales_price_container - io.cost_usd).toFixed(2);;
                break;
            case 'consignment_markup_arcooda':
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.sales_price_arcooda = (((io.cost_usd*io.markup_arcooda)/100)+parseInt(io.cost_usd)).toFixed(2);
                io.product_profitus_arcooda = (io.sales_price_arcooda - io.cost_usd).toFixed(2);;
                break;
            case 'consignment_markup_distributor':
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.sales_price_distributor = (((io.cost_usd*io.markup_distributor)/100)+parseInt(io.cost_usd)).toFixed(2);
                io.product_profitus_distributor = (io.sales_price_distributor - io.cost_usd).toFixed(2);;
                break;
            case 'product_profitus_container' :
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.sales_price_container = (parseFloat(io.cost_usd)+parseFloat(io.product_profitus_container)).toFixed(2);;
                io.markup_container=(((io.sales_price_container-io.cost_usd)/io.cost_usd)*100).toFixed(2);
                break;
            case 'consignment_product_profitus_arcooda' :
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.sales_price_arcooda = (parseFloat(io.cost_usd)+parseFloat(io.product_profitus_arcooda)).toFixed(2);;
                io.markup_arcooda=(((io.sales_price_arcooda-io.cost_usd)/io.cost_usd)*100).toFixed(2);
                break;
            case 'product_profitus_arcooda' :
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.sales_price_arcooda = (parseFloat(io.cost_usd)+parseFloat(io.product_profitus_arcooda)).toFixed(2);;
                io.markup_arcooda=(((io.sales_price_arcooda-io.cost_usd)/io.cost_usd)*100).toFixed(2);
                break;
            case 'profitus_distributor' :
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.sales_price_distributor = (parseFloat(io.cost_usd)+parseFloat(io.product_profitus_distributor)).toFixed(2);;
                io.markup_distributor=(((io.sales_price_distributor-io.cost_usd)/io.cost_usd)*100).toFixed(2);
                break;
            case 'product_profitus_distributor' :
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.sales_price_distributor = (parseFloat(io.cost_usd)+parseFloat(io.product_profitus_distributor)).toFixed(2);;
                io.markup_distributor=(((io.sales_price_distributor-io.cost_usd)/io.cost_usd)*100).toFixed(2);
                break;
            case 'item_profitus':
                io.sale_usd = Number(io.prof_usd) + Number(io.cost_usd);
            case 'product_profitus' :
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.sale = convertCurrency((parseFloat(io.cost_usd)+parseFloat(io.prof_usd)), "USD",io.sale_cur, false, io.precision);
                io.sale_usd = convertCurrency(io.sale, io.sale_cur, "USD", false, io.precision).toFixed(2);;
                io.mark=(((io.sale_usd-io.cost_usd)/io.cost_usd)*100).toFixed(2);
                break;
            case 'item_offerus':
            case 'sales_currency':
                io.sale = convertCurrency(io.sale_usd, "USD", io.sale_cur, false, io.precision);
                io.flux = (io.sale_cur == io.cost_cur);
            case 'item_offer':
                io.sale = convertCurrency(io.sale, io.sale_cur, "", false, io.precision);
                if (io.cost_usd != 0.0) io.mark = io.sale_usd / io.cost_usd;
                else io.mark = 1.0;
                io.sale_usd = convertCurrency(io.sale, io.sale_cur, "USD", false, io.precision);
                io.prof_usd = convertCurrency(io.sale_usd - io.cost_usd, "USD", "", false, io.precision);
                break;
            case 'consignment_markup':
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                var salesprice=(((io.cost_usd*io.mark)/100)+parseInt(io.cost_usd)).toFixed(2);
                io.sale = convertCurrency(salesprice, 'USD', io.sale_cur, false, io.precision);
                io.sale_usd = convertCurrency(io.sale, io.sale_cur, "USD", false, io.precision);
                io.prof_usd = convertCurrency(io.sale_usd - io.cost_usd, "USD", "", false, io.precision);
                break;
            case 'price_flux':
                if (io.flux) {
                    //io.sale_cur = io.cost_cur;
                    //io.sale = convertCurrency(io.cost * io.mark, io.cost_cur, io.sale_cur, false, io.precision);
                }
                io.sale_usd = convertCurrency(io.sale, io.sale_cur, "USD", false, io.precision);
                io.prof_usd = convertCurrency(io.sale_usd - io.cost_usd, "USD", "", false, io.precision);
                break;
            case 'precision':
                io.cost = convertCurrency(io.cost, io.cost_cur, "", false, io.precision);
                io.sale = convertCurrency(io.sale, io.sale_cur, "", false, io.precision);
                io.cost_usd = convertCurrency(io.cost, "USD", "", false, io.precision);
                io.sale_usd = convertCurrency(io.sale, "USD", "", false, io.precision);
                io.prof_usd = convertCurrency(io.prof_usd, "USD", "", false, io.precision);
                break;
        }
        io.precision = String(io.precision);
        io.flux = (io.flux ? "y" : "n");
        //io.mark = sprintf("%0.4f", (io.mark - 1.0) * 100.0);
        for (var k in matches) {
            matches[k].val(io[k]);
        }
        SetsupplierDefaultvalue();
    }
});
$(document).ready(function(){
     var matches = {
        sales_price_arcooda: "consignment_sales_price_arcooda",
        sales_price_distributor: "consignment_sales_price_distributor",
        sales_price_container: "consignment_sales_price_container",
    };
    for (var k in matches) {
        $("[name='item[" + matches[k] + "]']").eq(0).attr("raw_name", matches[k]).trigger('change');
    }
});
//]]>
function SetsupplierDefaultvalue() {
    var con_rows = supplierContactGetRows();
    for (var i = 0; i < con_rows.length; i++){ 
        console.log($('input[name="item[stock_type]"]:checked').val());
        if(($('input[name="item_suppliers['+con_rows[i]+'][main]"]').is(':checked') || con_rows.length==1) &&  $('input[name="item[stock_type]"]:checked').val()=='consignment'){
            setValue('item_suppliers['+con_rows[i]+'][unit_sup_currency]',getValue('item[consignment_unit_cost_currency]'));
            setValue('item_suppliers['+con_rows[i]+'][unit_cost_price]',getValue('item[consignment_unit_cost_price]'));
            setValue('item_suppliers['+con_rows[i]+'][sup_currency_freight]',getValue('item[consignment_freight_currency]'));
            setValue('item_suppliers['+con_rows[i]+'][cost_price_freight]',getValue('item[consignment_freight_price]'));
            setValue('item_suppliers['+con_rows[i]+'][cost_price_usd]',getValue('item[consignment_unit_cost_price_usd]'));
            setValue('item_suppliers['+con_rows[i]+'][cost_price_freight_usd]',getValue('item[consignment_freight_price_usd]'))
            setValue('item_suppliers['+con_rows[i]+'][cost_price]',getValue('item[consignment_cost_price]'));
            setValue('item_suppliers['+con_rows[i]+'][onlineprice_usd]',toCurrency(getValue('item[sales_price]'),getValue('item[sales_currency]'),'USD'));
            setValue('item_suppliers['+con_rows[i]+'][markup]',getValue('item[consignment_markup]'));
        }
    }
}
function openpricingtab(evt, id) {
  // Declare all variables
  $('.hover-color').css({'background-color':''});
  var i, tabcontent, tablinks;

  // Get all elements with class="tabcontent" and hide them
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  // Get all elements with class="tablinks" and remove the class "active"
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  // Show the current tab, and add an "active" class to the button that opened the tab
  document.getElementById('product_price_'+id).style.display = "block";
  evt.currentTarget.className += " active";
    $('.tab .active').css({'background-color':'#fff'});
}
function UpdatePriceMarkup(obj) {
  if($(obj).is(":checked")){
        if(getValue('item[cost_currency]')!=getValue('item[sales_currency]')  && getValue('item[price_flux]')=='n'){
            alert('The cost/sell currencies are different for this product and you have not turned PRICE FLUX to ON, are you sure you want to continue?');
        }
  }
}
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'consectionPricing\');return false">Consigment Pricing</a>')?>
<?php $consignment_update_price=getConsigmentUpdatePrice($item['id']) ; 
?>
<div id="consectionPricing">
    <?php if(count($consignment_update_price)>0){ ?>
    <table class="table_fields">
        <tr>
            <th></th>
            <td><?= date('d/m/Y',$consignment_update_price['created_time'])?></td>
            <td><?= date('d/m/Y') ?></td>
        </tr>
        <tr>
            <th>Exchange Rate:</th>
            <td><?=  '1 '.$item['consignment_cost_currency'].'=' .$consignment_update_price['exchange_rate']; ?> USD</td>
           <td><?php $exRates = getExchangeRateList();
                echo '1 '.$item['consignment_cost_currency'].'=' .$exRates[$consignment_update_price['cost_currency']];
           ?> USD</td>
        </tr>
        <tr>
            <th>Cost price (<?= $consignment_update_price['cost_currency'] ?>):</th>
            <td><?= $consignment_update_price['cost_price'].' '.$consignment_update_price['cost_currency']?></td>
           <td><?php $cost_price_usd= convertCurrency($consignment_update_price['cost_proce_usd'],'USD', $item['consignment_cost_currency'], false, $item['precision']);
                echo $cost_price_usd;
           ?> <?= $consignment_update_price['cost_currency']?></td>
        </tr>
        <tr>
            <th>Approx. Cost price (USD):</th>
            <td><?= $consignment_update_price['cost_proce_usd']?> USD</td>
            <td><?php  echo convertCurrency($item['consignment_cost_price'],$item['consignment_cost_currency'],'USD', false, $item['precision'])
            ?> USD</td>
        </tr>
        <tr>
            <th>Sales Price:</th>
            <td><?= $consignment_update_price['sales_price'].' '. $consignment_update_price['sales_currency']?></td>
            <td><?php $seles_price_usd= ((($consignment_update_price['cost_proce_usd']*$item['consignment_markup'])/100)+$consignment_update_price['cost_proce_usd']);
               echo number_format((float) $seles_price_usd, 2, '.', '');
                ?> USD</td>
        </tr>
        <tr>
            <th>Profit:</th>
            <td><?= $consignment_update_price['profit']?> USD</td>
            <td><?php  $consigment_profit=($seles_price_usd - $consignment_update_price['cost_proce_usd']); 
             echo number_format((float) $consigment_profit, 2, '.', '');
            ?> USD</td>
        </tr>
        <tr>
            <th>Markup:</th>
            <td><?= $consignment_update_price['markup']?>%</td>
           <td><?= number_format((float) $item['consignment_markup'], 2, '.', '')?>%</td>
        </tr>
    </table>
    <br>
    <br>
<?php } ?>
    <table class="table_fields">
        <tr>
            <th style="width:12%">Unit Cost Price :</th>
            <td><input name="item[consignment_unit_cost_price]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16"  value="<?= $item['consignment_unit_cost_price'] ??  number_format((float) $default_suppler['unit_cost_price'], 2, '.', '') ?>" <?=($item['stock_type']!='consignment')?'readonly':'' ?>/>
                <select style="width:25%" name="item[consignment_unit_cost_currency]" ><?=formatHTMLOptions(productGetOptionList('currency'),$item['consignment_unit_cost_currency'] ?? $default_suppler['sup_currency'] )?></select>
            <input style="width:15%" name="item[consignment_unit_cost_price_usd]" type="text"  value="<?=$item['consignment_unit_cost_price_usd']?>" readonly /> USD
        </td>
        <tr>
            <th style="width:12%">Unit Freight Price :</th>
            <td>
                <input name="item[consignment_freight_price]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16"  value="<?= $item['consignment_freight_price'] ??  number_format((float) $default_suppler['cost_price_freight'], 2, '.', '') ?>" />
                <select style="width:25%" name="item[consignment_freight_currency]" ><?=formatHTMLOptions(productGetOptionList('currency'),$item['consignment_freight_currency'] ?? $default_suppler['sup_currency_freight'] )?>
                </select>
                 <input style="width:15%" name="item[consignment_freight_price_usd]" type="text"  value="<?=$item['consignment_freight_price_usd']?>" readonly /> USD
            </td>
        </tr>
        <tr>
            <th style="width:12%">Total Cost Price :</th>
            <td>
                <input name="item[consignment_cost_price]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16"  value="<?= $item['consignment_cost_price'] ??  number_format((float) $default_suppler['cost_price'], 2, '.', '') ?>" readonly ?> USD
                <input type="hidden" name="item[consignment_cost_currency]" value="USD">
                <span style="width:25%;padding-left: 23%;"><a href="/webadmin/supplier_new/edit.php?site_id=<?= $_GLOBALS['site_settings']['site_id']?>&supplier_id=<?= $default_suppler['supplier_id'] ?>" target="_blank"><?= $default_suppler['supplier_name']?></a></span>
            </td>
        </tr>
        <tr>
            <th style="width:12%">Approx. Cost Price :</th>
            <td><input name="item[average_cost]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16"  value="<?= convertCurrency($item['consignment_cost_price'], $item['consignment_cost_currency'], 'USD', false, $item['precision']) ?>" disabled /> USD
                <!-- <input type="hidden" name="item[cost_currency]" value="USD"> -->
                <!-- <select style="width:25%" name="item[cost_currency]" disabled><?=formatHTMLOptions(productGetOptionList('currency'),$item['cost_currency'])?></select> -->
                </td>
        </tr>
        <tr style="height: 20px;">
            <th style="width:12%">Sales Price :</th>
            <td><input name="item[sales_price]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?= number_format((float) $item['sales_price'], $item['precision'], '.', '')?>" /> USD
                <input type="hidden" name="item[sales_currency]" value="USD">
            </td>
        </tr>
        <tr style="height: 20px;">
            <th style="width:12%">Markup :</th>
           <td style="width: 25%;"><input name="item[consignment_markup]" type="number" size="16" step="any" value="<?php hecho(number_format((float) $item['consignment_markup'], 2, '.', ''))?>" /> <b>%</b></td>
        </tr>
         <tr style="height: 20px;">
            <th style="width:12%">Profit :</th>
           <td style="width: 34%;"><input name="item[product_profitus]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?=bcsub($item['sales_price'],convertCurrency($item['consignment_cost_price'], $item['consignment_cost_currency'], 'USD', false, $item['precision']),$item['precision']) ?>" /> <b>USD</b></td>
        </tr>
        <tr style="height: 20px;">
            <th style="width:12%">Price Flux :</th>
            <td>
               <select style="width:25%" name="item[price_flux]"><?=formatHTMLOptions(['y'=>'Yes','n'=>'No'],$item['price_flux'])?></select>
               <input type="checkbox" name="item[is_price_flux]" value="1" onchange="UpdatePriceMarkup(this)">Update price markup and currency
           </td>
        </tr>
        <tr style="height: 20px;">
            <th style="width:12%">Qty Available:</th>
            <td>
               <input  name="item[qty]" type="text"  value="<?=$default_suppler['qty']?>" /></td>        
           </tr>
    </table>
    <br>
    <br>
    <table class="table_fields">
        <tr style="background: #eed;">
            <th>VIP Member :</th>
            <td>
                <table style="margin-left: 0px;width: 100%;">
                    <tr style="background: #eed;">
                      
                        <td style="width: 30%;">
                        <!-- <input name="item[consignment_sales_price_arcooda]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?=($item['consignment_sales_price_arcooda'])?number_format((float)($item['consignment_sales_price_arcooda']), 2, '.', ''):'0.000000' ?>" /><b> USD</b> -->
                        <input name="item[consignment_sales_price_arcooda]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?=($item['consignment_sales_price_arcooda'] && $item['consignment_sales_price_arcooda'] > 0) ? number_format((float)($item['consignment_sales_price_arcooda']), 2, '.', ''):(($item['consignment_sales_price'] && $item['consignment_sales_price'] > 0) ? $item['consignment_sales_price'] :'0.000000') ?>" /><b> USD</b>
                        <?php if($item['consignment_sales_price']>0 && $item['consignment_sales_price_arcooda']>0){ ?>
                            <span style="color:red;font-size: 9px;"><i>Discount $ <?= $item['consignment_sales_price']-$item['consignment_sales_price_arcooda']?> USD (Save <?= number_format((float)(($item['consignment_sales_price']-$item['consignment_sales_price_arcooda'])*100/$item['consignment_sales_price']), 2, '.', '') ?> %)</i></span>
                        <?php } ?>
                        </td>
                        <?php
                            $consignmentProfitArc = ($item['consignment_sales_price_arcooda'] && $item['consignment_sales_price_arcooda'] > 0) ? (float)($item['consignment_sales_price_arcooda']) :(($item['consignment_sales_price'] && $item['consignment_sales_price'] > 0) ? $item['consignment_sales_price'] : '0.000000');
                        ?>
                        <th style="width: 7%;">Profit :</th>
                        <td style="width: 34%;"><input name="item[product_profitus_arcooda]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?=number_format((float)(bcsub($consignmentProfitArc,
                            convertCurrency($item['consignment_cost_price'], $item['consignment_cost_currency'], 'USD', false, $item['precision']),$item['precision'])), 2, '.', '')  ?>" /> <b>USD</b>

                        </td>
                         <th style="width: 7%;">Markup :</th>
                        <td style="width:25%"><input name="item[consignment_markup_arcooda]" type="number" size="16" step="any" value="<?php hecho(number_format((float)($item['consignment_markup_arcooda']), 2, '.', ''))?>" /> <b>%</b></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="alt">
            <th style="background: none;">Distributor Sample :</th>
            <td>
                <table style="margin-left: 0px;    width: 100%;">
                    <tr class="alt">
                      
                        <td style="width: 30%;">
                            <!-- <input name="item[consignment_sales_price_distributor]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?=($item['consignment_sales_price_distributor'])?number_format((float)($item['consignment_sales_price_distributor']), 2, '.', ''):'0.000000' ?>" /><b> USD</b> -->
                            <input name="item[consignment_sales_price_distributor]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?=($item['consignment_sales_price_distributor'] && $item['consignment_sales_price_distributor'] > 0 ) ? number_format((float)($item['consignment_sales_price_distributor']), 2, '.', ''):(($item['consignment_sales_price'] && $item['consignment_sales_price'] > 0) ? $item['consignment_sales_price'] :'0.000000') ?>" /><b> USD</b>
                        <?php if($item['consignment_sales_price']>0 && $item['consignment_sales_price_arcooda']>0){ ?>
                            <span style="color:red;font-size: 9px;"><i>Discount $ <?= $item['consignment_sales_price']-$item['consignment_sales_price_distributor']?> USD (Save <?= number_format((float)(($item['consignment_sales_price']-$item['consignment_sales_price_distributor'])*100/$item['consignment_sales_price']), 2, '.', '') ?> %)</i></span> 
                        <?php } ?>
                        </td>
                        <?php
                            $consignmentProfitDis = ($item['consignment_sales_price_distributor'] && $item['consignment_sales_price_distributor'] > 0) ? (float)($item['consignment_sales_price_distributor']) :(($item['consignment_sales_price'] && $item['consignment_sales_price'] > 0) ? $item['consignment_sales_price'] : '0.000000');
                        ?>
                        <th style="width: 7%;background: none;">Profit :</th>
                        <td style="width: 34%;"><input name="item[product_profitus_distributor]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?=number_format((float)(bcsub($consignmentProfitDis,
                            convertCurrency($item['consignment_cost_price'], $item['consignment_cost_currency'], 'USD', false, $item['precision']),$item['precision'])), 2, '.', '')  ?>" /> <b>USD</b></td>
                         <th style="width: 7%;background: none;">Markup :</th>
                        <td style="width: 25%;"><input name="item[consignment_markup_distributor]" type="number" size="16" step="any" value="<?php hecho(number_format((float)($item['consignment_markup_distributor']), 2, '.', ''))?>" /> <b>%</b></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style="background: #eed;">
            <th>Distributor Container :</th>
            <td>
                <table style="margin-left: 0px;width: 100%;">
                    <tr style="background: #eed;">
                      
                        <td style="width: 30%;">
                            <!-- <input name="item[consignment_sales_price_container]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" type="number" size="16" step="any" value="<?=($item['consignment_sales_price_container'])?number_format((float)($item['consignment_sales_price_container']), 2, '.', ''):'0.000000' ?>" /><b> USD</b> -->
                            <input name="item[consignment_sales_price_container]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" type="number" size="16" step="any" value="<?=($item['consignment_sales_price_container'] && $item['consignment_sales_price_container'] > 0 ) ? number_format((float)($item['consignment_sales_price_container']), 2, '.', ''):(($item['consignment_sales_price'] && $item['consignment_sales_price'] > 0) ? $item['consignment_sales_price'] :'0.000000') ?>" /><b> USD</b>
                        <?php if($item['consignment_sales_price']>0 && $item['consignment_sales_price_arcooda']>0){ ?>
                           <span style="color:red;font-size: 9px;"><i>Discount $ <?= $item['sales_price']-$item['consignment_sales_price_container']?> USD (Save <?= number_format((float)(($item['consignment_sales_price']-$item['consignment_sales_price_container'])*100/$item['consignment_sales_price']), 2, '.', '') ?> %)</i></span>
                           <?php } ?> 
                        </td>
                        <?php
                            $consignmentProfitCon = ($item['consignment_sales_price_container'] && $item['consignment_sales_price_container'] > 0) ? (float)($item['consignment_sales_price_container']) :(($item['consignment_sales_price'] && $item['consignment_sales_price'] > 0) ? $item['consignment_sales_price'] : '0.000000');
                        ?>
                        <th style="width: 7%;">Profit :</th>
                        <td style="width: 34%;"><input name="item[product_profitus_container]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?=number_format((float)(bcsub($consignmentProfitCon,
                            convertCurrency($item['consignment_cost_price'], $item['consignment_cost_currency'], 'USD', false, $item['precision']),$item['precision'])), 2, '.', '')  ?>" /> <b>USD</b><span style="color:red;font-size: 9px">
                                <?php $temp = reset($item_containers); ?>
                                <i>20ft =$<?= number_format((bcsub($item['consignment_sales_price_container'],
                            $item['consignment_cost_price'],$item['precision']))*$temp['contload_20ft_qty'])?> USD 40ft =$ <?= number_format((bcsub($item['consignment_sales_price_container'],$item['consignment_cost_price'],$item['precision']))*$temp['contload_40ft_qty'])?> USD</i></span></td>
                         <th style="width: 7%;">Markup :</th>
                        <td style="width: 25%;"><input name="item[consignment_markup_container]" type="number" size="16" step="any" value="<?php hecho(number_format((float)($item['consignment_markup_container']), 2, '.', ''))?>" /> <b>%</b></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br /><br />
<div class="tab" style="text-align: left; width: 95%; margin: 0 auto;">


<?php
if (isset($item_price_changes) && is_array($item_price_changes) && count($item_price_changes)) {
?>
    <table class="table_grid">
        <tr><th colspan="4">Previous Price Changes</th></tr>
        <tr style="background:#eef;">
            <td width="5%"><b>Date</b></td>
            <td width="10%"><b>User</b></td>
            <td><b>Comments</b></td>
        </tr>
<?php
$alt = false;
    foreach ($item_price_changes as $change) {
?>
        <tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
            <td style="text-align:center;"><?=date(_('Y-m-d'), $change['change_time'])?></td>
            <td style="text-align:center;"><?php hecho($change['user'])?></td>
            <td style="text-align:left;"><?=nl2br(hecho($change['query'], 1))?></td>
        </tr>
<?php
}
?>
    </table>
    <br /><br />
<?php
}
?>
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionPricing_hideNote">
    Pricing can be viewed by Clicking "Pricing" above<br />
</div>

<!-- /_pricing.tpl -->

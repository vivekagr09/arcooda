<!-- _pricing.tpl -->

<?php
/**
 * Product Pricing template
 *
 * Inputs:
 * - $item
 */

if (!isset($item) || !is_array($item)) {
    return;
}

$dpoints = $item['precision'];
if (0 == $dpoints) {
    $dpoints = 2;
}
?>

<script type="text/javascript">
$('body').on('change','.change_sales_price',function(){
    var rowid=$(this).attr('data-row');
    var cost_usd = getValue('subproductprice['+rowid+'][price_usd]');
    setValue('subproductprice['+rowid+']['+$(this).attr('data-profit')+']',($(this).val()-cost_usd).toFixed(2));
    var markup=((($(this).val()-cost_usd)/cost_usd)*100).toFixed(2);
    setValue('subproductprice['+rowid+']['+$(this).attr('data-markup')+']',markup);
})
$('body').on('change','.change_markup',function(){
    var rowid=$(this).attr('data-row');
    var cost_usd = getValue('subproductprice['+rowid+'][price_usd]');
    var salesprice= (((cost_usd*$(this).val())/100)+parseInt(cost_usd)).toFixed(2);
    var profitus=(salesprice-cost_usd).toFixed(2);
    setValue('subproductprice['+rowid+']['+$(this).attr('data-sales')+']',salesprice);
    setValue('subproductprice['+rowid+']['+$(this).attr('data-profit')+']',profitus);
});
$('body').on('change','.change_profit',function(){
    var rowid=$(this).attr('data-row');
    var cost_usd = getValue('subproductprice['+rowid+'][price_usd]');
    var salesprice= (parseFloat(cost_usd)+parseFloat($(this).val())).toFixed(2);
    var markup=(((salesprice-cost_usd)/cost_usd)*100).toFixed(2);
    setValue('subproductprice['+rowid+']['+$(this).attr('data-sales')+']',salesprice);
    setValue('subproductprice['+rowid+']['+$(this).attr('data-markup')+']',markup);
});
$('body').on('click','.copy-default-data',function(){
    var defaultkey=$(this).attr('data-default');
    var datakey=$(this).attr('data-key');
    setValue('subproductprice['+datakey+'][markup]',getValue('subproductprice['+defaultkey+'][markup]'));
    setValue('subproductprice['+datakey+'][markup_arcooda]',getValue('subproductprice['+defaultkey+'][markup_arcooda]'));
    setValue('subproductprice['+datakey+'][markup_distributor]',getValue('subproductprice['+defaultkey+'][markup_distributor]'));
    setValue('subproductprice['+datakey+'][markup_container]',getValue('subproductprice['+defaultkey+'][markup_container]'));
    
    $('.change_markup').trigger("change")
});
$('body').on('change','.change_codt_price',function(){
    var rowid=$(this).attr('data-row');
    var cost_usd = $(this).val();
    setValue('subproductprice['+rowid+'][product_profitus_arcooda]',getValue('subproductprice['+rowid+'][sales_price_arcooda]')-cost_usd);
    setValue('subproductprice['+rowid+'][markup_arcooda]',(((getValue('subproductprice['+rowid+'][sales_price_arcooda]')-cost_usd)/cost_usd)*100).toFixed(2));
    setValue('subproductprice['+rowid+'][product_profitus_distributor]',getValue('subproductprice['+rowid+'][sales_price_distributor]')-cost_usd);
    setValue('subproductprice['+rowid+'][markup_distributor]',(((getValue('subproductprice['+rowid+'][sales_price_distributor]')-cost_usd)/cost_usd)*100).toFixed(2));
    setValue('subproductprice['+rowid+'][product_profitus_container]',getValue('subproductprice['+rowid+'][sales_price_container]')-cost_usd);
    setValue('subproductprice['+rowid+'][markup_container]',(((getValue('subproductprice['+rowid+'][sales_price_container]')-cost_usd)/cost_usd)*100).toFixed(2));
    setValue('subproductprice['+rowid+'][product_profitus]',getValue('subproductprice['+rowid+'][sell_price]')-cost_usd);
    setValue('subproductprice['+rowid+'][markup]',(((getValue('subproductprice['+rowid+'][sell_price]')-cost_usd)/cost_usd)*100).toFixed(2));
    
});
//<![CDATA[
$(function() {
    var vip_price = getValue('item[sales_price_arcooda]');
    var dist_sample = getValue('item[sales_price_distributor]');
    var dist_container = getValue('item[sales_price_container]');
    var matches = {
        cost: "cost_price",
        cost_cur: "cost_currency",
        sale: "sales_price",
        sale_cur: "sales_currency",
        cost_usd: "product_costus",
        sale_usd: "product_offerus",
        mark: "markup",
        flux: "price_flux",
        prof_usd: "product_profitus",
        sales_price_arcooda: "sales_price_arcooda",
        product_profitus_arcooda:"product_profitus_arcooda",
        markup_arcooda: "markup_arcooda",
        sales_price_distributor: "sales_price_distributor",
        product_profitus_distributor:"product_profitus_distributor",
        markup_distributor: "markup_distributor",
        sales_price_container: "sales_price_container",
        markup_container: "markup_container",
        product_profitus_container:"product_profitus_container",
        precision: "precision",
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
        switch ($(this).attr("raw_name")) {
            case 'cost_price':
                io.cost_usd = convertCurrency(io.cost, "USD", io.cost_cur, false, io.precision);
                io.product_profitus_arcooda = (io.sales_price_arcooda - io.cost_usd);
                io.markup_arcooda=(((io.sales_price_arcooda-io.cost_usd)/io.cost_usd)*100).toFixed(2);
                io.product_profitus_distributor = (io.sales_price_distributor - io.cost_usd);
                io.markup_distributor=(((io.sales_price_distributor-io.cost_usd)/io.cost_usd)*100).toFixed(2);
                io.product_profitus_container = (io.sales_price_container - io.cost_usd);
                io.markup_container=(((io.sales_price_container-io.cost_usd)/io.cost_usd)*100).toFixed(2);
            case 'sales_price':
            case 'sales_currency':
            case 'cost_currency':
                if (io.flux) io.sale_cur = io.cost_cur;
                else io.flux = (io.sale_cur == io.cost_cur);
            case 'item_cost':
                io.cost = convertCurrency(io.cost, io.cost_cur, "", false, io.precision);
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.sale_usd = convertCurrency(io.sale, io.sale_cur, "USD", false, io.precision);
                io.prof_usd = convertCurrency(io.sale_usd - io.cost_usd, "USD", "", false, io.precision);
                io.mark=(((io.sale_usd-io.cost_usd)/io.cost_usd)*100).toFixed(2)
                break;
            case 'sales_price_arcooda':
                io.cost = convertCurrency(io.cost, io.cost_cur, "", false, io.precision);
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.product_profitus_arcooda = (io.sales_price_arcooda - io.cost_usd);
                io.markup_arcooda=(((io.sales_price_arcooda-io.cost_usd)/io.cost_usd)*100).toFixed(2)
                break;
            case 'sales_price_distributor':
                io.cost = convertCurrency(io.cost, io.cost_cur, "", false, io.precision);
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.product_profitus_distributor = (io.sales_price_distributor - io.cost_usd);
                io.markup_distributor=(((io.sales_price_distributor-io.cost_usd)/io.cost_usd)*100).toFixed(2)
                break;
            case 'sales_price_container':
                io.cost = convertCurrency(io.cost, io.cost_cur, "", false, io.precision);
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.product_profitus_container = (io.sales_price_container - io.cost_usd);
                io.markup_container=(((io.sales_price_container-io.cost_usd)/io.cost_usd)*100).toFixed(2)
                break;
            case 'markup_container':
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.sales_price_container = (((io.cost_usd*io.markup_container)/100)+parseInt(io.cost_usd)).toFixed(2);
                io.product_profitus_container = (io.sales_price_container - io.cost_usd);
                break;
            case 'markup_arcooda':
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.sales_price_arcooda = (((io.cost_usd*io.markup_arcooda)/100)+parseInt(io.cost_usd)).toFixed(2);
                io.product_profitus_arcooda = (io.sales_price_arcooda - io.cost_usd);
                break;
            case 'markup_distributor':
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.sales_price_distributor = (((io.cost_usd*io.markup_distributor)/100)+parseInt(io.cost_usd)).toFixed(2);
                io.product_profitus_distributor = (io.sales_price_distributor - io.cost_usd);
                break;
            case 'product_profitus_container' :
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.sales_price_container = (parseFloat(io.cost_usd)+parseFloat(io.product_profitus_container));
                io.markup_container=(((io.sales_price_container-io.cost_usd)/io.cost_usd)*100).toFixed(2);
                break;
            case 'product_profitus_arcooda' :
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.sales_price_arcooda = (parseFloat(io.cost_usd)+parseFloat(io.product_profitus_arcooda));
                io.markup_arcooda=(((io.sales_price_arcooda-io.cost_usd)/io.cost_usd)*100).toFixed(2);
                break;
            case 'product_profitus_distributor' :
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.sales_price_distributor = (parseFloat(io.cost_usd)+parseFloat(io.product_profitus_distributor));
                io.markup_distributor=(((io.sales_price_distributor-io.cost_usd)/io.cost_usd)*100).toFixed(2);
                break;
            case 'item_profitus':
                io.sale_usd = Number(io.prof_usd) + Number(io.cost_usd);
            case 'product_profitus' :
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                io.sale = convertCurrency((parseFloat(io.cost_usd)+parseFloat(io.prof_usd)), "USD",io.sale_cur, false, io.precision);
                io.sale_usd = convertCurrency(io.sale, io.sale_cur, "USD", false, io.precision);
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
            case 'markup':
                io.cost_usd = convertCurrency(io.cost, io.cost_cur, "USD", false, io.precision);
                var salesprice=(((io.cost_usd*io.mark)/100)+parseInt(io.cost_usd)).toFixed(2);
                io.sale = convertCurrency(salesprice, 'USD', io.sale_cur, false, io.precision);
                io.sale_usd = convertCurrency(io.sale, io.sale_cur, "USD", false, io.precision);
                io.prof_usd = convertCurrency(io.sale_usd - io.cost_usd, "USD", "", false, io.precision);
                break;
            case 'price_flux':
                if (io.flux) {
                    io.sale_cur = io.cost_cur;
                    io.sale = convertCurrency(io.cost * io.mark, io.cost_cur, io.sale_cur, false, io.precision);
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
     $('body').on('keyup change','#item_sales_price',function(){
        var val=$(this).val();
        if(vip_price == 0 & val != '' & val > 0) {
            setValue('item[sales_price_arcooda]',val);
            $("[name='item[sales_price_arcooda]']").change();
        }
        if(dist_sample == 0 & val != '' & val > 0) {
            setValue('item[sales_price_distributor]',val);
            $("[name='item[sales_price_distributor]']").change();
        }
        if(dist_container == 0 & val != '' & val > 0) {
            setValue('item[sales_price_container]',val);
            $("[name='item[sales_price_container]']").change();
        }
    })
});
//]]>
function SetsupplierDefaultvalue() {
    var con_rows = supplierContactGetRows();
    for (var i = 0; i < con_rows.length; i++){ 
        if($('input[name="item_suppliers['+con_rows[i]+'][main]"]').is(':checked') || con_rows.length==1){
            setValue('item_suppliers['+con_rows[i]+'][cost_price]',toCurrency(getValue('item[cost_price]'),getValue('item[cost_currency]'),getValue('item_suppliers['+con_rows[i]+'][sup_currency]')));
            setValue('item_suppliers['+con_rows[i]+'][cprice_usd]',toCurrency(getValue('item[cost_price]'),getValue('item[cost_currency]'),'USD'));
            setValue('item_suppliers['+con_rows[i]+'][onlineprice_usd]',toCurrency(getValue('item[sales_price]'),getValue('item[sales_currency]'),'USD'));
            setValue('item_suppliers['+con_rows[i]+'][markup]',getValue('item[markup]'));
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
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionPricing\');return false">Pricing</a>')?>

<div id="sectionPricing">
    <table class="table_fields" style="<?= ((count($sub_product)>0)?'display:none;':'')?>">
        <tr>
            <th>Cost Price :</th>
              <td><input name="item[cost_price]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16"  value="<?=convertCurrency($item['cost_price'], $item['cost_currency'], 'USD', false, 2);?>" <?=($item['stock_type']!='consignment')?'readonly':'' ?>/> <b>USD</b>
                <input type="hidden" name="item[cost_currency]" value="USD">
                </td>
        </tr>
        <tr style="height: 20px;">
            <td></td>
            <td></td>
        </tr>
        <tr class="alt">
            <th style="background: none;">Retail Price :</th>
            <td>
                <table style="margin-left: 0px;width: 100%;">
                    <tr class="alt">
                      
                       <td style="width: 30%;"><input id="item_sales_price" name="item[sales_price]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?=convertCurrency($item['sales_price'], $item['sales_currency'], '', false, $item['precision'])?>"/><b> USD</b>
                            <input type="hidden" name="item[sales_currency]" value="USD">
                        </td>
                        <th style="width: 7%;background: none;">Profit :</th>
                        <td style="width: 34%;"><input name="item[product_profitus]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?=bcsub($item['sales_price'],$item['product_costus'],$item['precision']) ?>" /> <b>USD</b></td>
                         <th style="width: 7%;background: none;">Markup :</th>
                        <td style="width: 25%;"><input name="item[markup]" type="number" size="16" step="any" value="<?php hecho(number_format((float) $item['markup'], 2, '.', ''))?>" /> <b>%</b></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style="background: #eed;">
            <th>VIP Member :</th>
            <td>
                <table style="margin-left: 0px;width: 100%;">
                    <tr style="background: #eed;">
                      
                        <td style="width: 30%;"><input name="item[sales_price_arcooda]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?=($item['sales_price_arcooda'])?number_format((float)($item['sales_price_arcooda']), 2, '.', ''):'0.000000' ?>" /><b> USD</b>
                            <?php if($item['sales_price']>0 && $item['sales_price_arcooda']>0){ ?>
                            <span style="color:red;font-size: 9px;"><i>Discount $ <?= $item['sales_price']-$item['sales_price_arcooda']?> USD (Save <?= number_format((float)(($item['sales_price']-$item['sales_price_arcooda'])*100/$item['sales_price']), 2, '.', '') ?> %)</i></span>
                        <?php } ?>
                        </td>
                        <th style="width: 7%;">Profit :</th>
                        <td style="width: 34%;"><input name="item[product_profitus_arcooda]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?=number_format((float)(bcsub($item['sales_price_arcooda'],
                            $item['product_costus'],$item['precision'])), 2, '.', '')  ?>" /> <b>USD</b>

                        </td>
                         <th style="width: 7%;">Markup :</th>
                        <td style="width:25%"><input name="item[markup_arcooda]" type="number" size="16" step="any" value="<?php hecho(number_format((float)($item['markup_arcooda']), 2, '.', ''))?>" /> <b>%</b></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="alt">
            <th style="background: none;">Distributor Sample :</th>
            <td>
                <table style="margin-left: 0px;    width: 100%;">
                    <tr class="alt">
                      
                        <td style="width: 30%;"><input name="item[sales_price_distributor]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?=($item['sales_price_distributor'])?number_format((float)($item['sales_price_distributor']), 2, '.', ''):'0.000000' ?>" /><b> USD</b>
                            <?php if($item['sales_price']>0 && $item['sales_price_arcooda']>0){ ?>
                            <span style="color:red;font-size: 9px;"><i>Discount $ <?= $item['sales_price']-$item['sales_price_distributor']?> USD (Save <?= number_format((float)(($item['sales_price']-$item['sales_price_distributor'])*100/$item['sales_price']), 2, '.', '') ?> %)</i></span> 
                        <?php } ?>
                        </td>
                        <th style="width: 7%;background: none;">Profit :</th>
                        <td style="width: 34%;"><input name="item[product_profitus_distributor]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?=number_format((float)(bcsub($item['sales_price_distributor'],
                            $item['product_costus'],$item['precision'])), 2, '.', '')  ?>" /> <b>USD</b></td>
                         <th style="width: 7%;background: none;">Markup :</th>
                        <td style="width: 25%;"><input name="item[markup_distributor]" type="number" size="16" step="any" value="<?php hecho(number_format((float)($item['markup_distributor']), 2, '.', ''))?>" /> <b>%</b></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style="background: #eed;">
            <th>Distributor Container :</th>
            <td>
                <table style="margin-left: 0px;width: 100%;">
                    <tr style="background: #eed;">
                      
                        <td style="width: 30%;"><input name="item[sales_price_container]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" type="number" size="16" step="any" value="<?=($item['sales_price_container'])?number_format((float)($item['sales_price_container']), 2, '.', ''):'0.000000' ?>" /><b> USD</b>
                            <?php if($item['sales_price']>0 && $item['sales_price_arcooda']>0){ ?>
                           <span style="color:red;font-size: 9px;"><i>Discount $ <?= $item['sales_price']-$item['sales_price_container']?> USD (Save <?= number_format((float)(($item['sales_price']-$item['sales_price_container'])*100/$item['sales_price']), 2, '.', '') ?> %)</i></span>
                           <?php } ?> 
                        </td>
                        <th style="width: 7%;">Profit :</th>
                        <td style="width: 34%;"><input name="item[product_profitus_container]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?=number_format((float)(bcsub($item['sales_price_container'],
                            $item['product_costus'],$item['precision'])), 2, '.', '')  ?>" /> <b>USD</b><span style="color:red;font-size: 9px">
                                <?php $temp = reset($item_containers); ?>
                                <i>20ft =$<?= number_format((bcsub($item['sales_price_container'],
                            $item['cost_price'],$item['precision']))*$temp['contload_20ft_qty'])?> USD 40ft =$ <?= number_format((bcsub($item['sales_price_container'],$item['cost_price'],$item['precision']))*$temp['contload_40ft_qty'])?> USD</i></span></td>
                         <th style="width: 7%;">Markup :</th>
                        <td style="width: 25%;"><input name="item[markup_container]" type="number" size="16" step="any" value="<?php hecho(number_format((float)($item['markup_container']), 2, '.', ''))?>" /> <b>%</b></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br /><br />
<div class="tab" style="text-align: left; width: 95%; margin: 0 auto;">

<?php
if(count($sub_product)>0){
$clolorlist=productGetOptionList('colour');
foreach ($sub_product as $key => $pro) {
        echo '<span style="background-color: '.$clolorlist[$key+1].'"><a class="tablinks hover-color '.(($pro["default"]=="y")?"active":"").'" onclick="openpricingtab(event, '.$pro['id'].')" style="border: 3px solid '.$clolorlist[$key+1].';padding: 13px 5px;margin-left: 2px;'.(($pro["default"]=="y")?"background-color:rgb(255, 255, 255);":"").'"><span style="color:#fff;background-color:'.$clolorlist[$key+1].' ; padding: 9px;">'.$pro['name'].'</span></a></span>';

}
$defaultkey = array_search('y',array_column($sub_product, 'default'));
echo '</div>';
foreach ($sub_product as $key => $pro) { 
    $itemkeyname='subproductprice['.$pro['id'].']';
    ?>
    <input type="hidden" name="<?= $itemkeyname ?>[id]" value="<?php echo  $pro['id']; ?>" >
     <table class="table_fields tabcontent" id="product_price_<?php echo  $pro['id']; ?>" style="<?=(($pro["default"]=="y")?"display:block;":"")?>">
        <tr>
            <th>Cost Price :</th>
            <td><input name="<?= $itemkeyname ?>[price_usd]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" data-row="<?= $pro['id'] ?>" value="<?= $pro['price_usd'] ?>" class="change_codt_price"/> <b>USD</b>
                </td>
        </tr>
        <tr style="height: 20px;">
            <td></td>
            <td></td>
        </tr>
        <tr class="alt">
            <th style="background: none;">Retail Price :</th>
            <td>
                <table style="margin-left: 0px;width: 100%;">
                    <tr class="alt">
                      
                        <td style="width: 30%;"><input name="<?= $itemkeyname ?>[sell_price]" data-row="<?= $pro['id'] ?>" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?= $pro['sell_price']?>" class="change_sales_price" data-profit="product_profitus" data-markup="markup"/><b> USD</b>
                        </td>
                        <th style="width: 7%;background: none;">Profit :</th>
                        <td style="width: 33%;"><input name="<?= $itemkeyname ?>[product_profitus]" data-row="<?= $pro['id'] ?>" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?=bcsub($pro['sell_price'],$pro['price_usd'], $item['precision']) ?>" class="change_profit" data-sales="sell_price" data-markup="markup"/> <b>USD</b></td>
                         <th style="width: 7%;background: none;">Markup :</th>
                        <td style="width: 26%;"><input name="<?= $itemkeyname ?>[markup]" type="number" size="16" data-row="<?= $pro['id'] ?>" step="any" value="<?php hecho($pro['markup'])?>" class="change_markup" data-sales="sell_price" data-profit="product_profitus"/> <b>%</b></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style="background: #eed;">
            <th>VIP Member:</th>
            <td>
                <table style="margin-left: 0px;width: 100%;">
                    <tr style="background: #eed;">
                      
                        <td style="width: 30%;"><input name="<?= $itemkeyname ?>[sales_price_arcooda]" data-row="<?= $pro['id'] ?>" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?=($pro['sales_price_arcooda'])?$pro['sales_price_arcooda']:'0.000000' ?>" class="change_sales_price" data-profit="product_profitus_arcooda" data-markup="markup_arcooda"/><b> USD</b>
                            <?php if($pro['sell_price']>0 && $pro['sales_price_arcooda']>0){ ?>
                            <span style="color:red;font-size: 9px;"><i>Discount $ <?= $pro['sell_price']-$pro['sales_price_arcooda']?> USD (Save <?= number_format((float)(($pro['sell_price']-$pro['sales_price_arcooda'])*100/$pro['sell_price']), 2, '.', '') ?> %)</i></span>
                        <?php } ?>
                        </td>
                        <th style="width: 7%;">Profit :</th>
                        <td style="width: 33%;"><input name="<?= $itemkeyname ?>[product_profitus_arcooda]" data-row="<?= $pro['id'] ?>" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?=number_format(bcsub($pro['sales_price_arcooda'],
                            $pro['price_usd'],$item['precision'])) ?>" class="change_profit" data-sales="sales_price_arcooda" data-markup="markup_arcooda"/> <b>USD</b>

                        </td>
                         <th style="width: 7%;">Markup :</th>
                        <td style="width:26%"><input name="<?= $itemkeyname ?>[markup_arcooda]" data-row="<?= $pro['id'] ?>" type="number" size="16" step="any" value="<?php hecho($pro['markup_arcooda'])?>" class="change_markup" data-sales="sales_price_arcooda" data-profit="product_profitus_arcooda"/> <b>%</b></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="alt">
            <th style="background: none;">Distributor Sample :</th>
            <td>
                <table style="margin-left: 0px;    width: 100%;">
                    <tr class="alt">
                      
                        <td style="width: 30%;"><input name="<?= $itemkeyname ?>[sales_price_distributor]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" data-row="<?= $pro['id'] ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?=($pro['sales_price_distributor'])?$pro['sales_price_distributor']:'0.000000' ?>" class="change_sales_price" data-profit="product_profitus_distributor" data-markup="markup_distributor"/><b> USD</b>
                            <?php if($pro['sell_price']>0 && $pro['sales_price_distributor']>0){ ?>
                            <span style="color:red;font-size: 9px;"><i>Discount $ <?= $pro['sell_price']-$pro['sales_price_distributor']?> USD (Save <?= number_format((float)(($pro['sell_price']-$pro['sales_price_distributor'])*100/$pro['sell_price']), 2, '.', '') ?> %)</i></span> 
                        <?php } ?>
                        </td>
                        <th style="width: 7%;background: none;">Profit :</th>
                        <td style="width: 33%;"><input name="<?= $itemkeyname ?>[product_profitus_distributor]" data-row="<?= $pro['id'] ?>" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" step="any" type="number" size="16" value="<?=number_format(bcsub($pro['sales_price_distributor'],
                            $pro['price_usd'],$item['precision'])) ?>" class="change_profit" data-sales="sales_price_distributor" data-markup="markup_distributor"/> <b>USD</b></td>
                         <th style="width: 7%;background: none;">Markup :</th>
                        <td style="width: 26%;"><input name="<?= $itemkeyname ?>[markup_distributor]" type="number" data-row="<?= $pro['id'] ?>" size="16" step="any" value="<?php hecho($pro['markup_distributor'])?>"  class="change_markup" data-sales="sales_price_distributor" data-profit="product_profitus_distributor"/> <b>%</b></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style="background: #eed;">
            <th>Distributor Container :</th>
            <td>
                <table style="margin-left: 0px;width: 100%;">
                    <tr style="background: #eed;">
                      
                        <td style="width: 30%;"><input name="<?= $itemkeyname ?>[sales_price_container]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" data-row="<?= $pro['id'] ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" type="number" size="16" step="any" value="<?=($pro['sales_price_container'])?$pro['sales_price_container']:'0.000000' ?>" class="change_sales_price" data-profit="product_profitus_container" data-markup="markup_container"/><b> USD</b>
                            <?php if($pro['sell_price']>0 && $pro['sales_price_container']>0){ ?>
                           <span style="color:red;font-size: 9px;"><i>Discount $ <?= $item['sales_price']-$pro['sales_price_container']?> USD (Save <?= number_format((float)(($pro['sell_price']-$pro['sales_price_container'])*100/$pro['sell_price']), 2, '.', '') ?> %)</i></span>
                           <?php } ?> 
                        </td>
                        <th style="width: 7%;">Profit :</th>
                        <td style="width: 33%;"><input name="<?= $itemkeyname ?>[product_profitus_container]" min="<?= bcadd("0.0", "0.0", $item['precision']) ?>" max="<?= bcadd("9999999.999", "0.0", $item['precision']) ?>" data-row="<?= $pro['id'] ?>" step="any" type="number" size="16" value="<?=number_format(bcsub($pro['sales_price_container'],
                            $pro['price_usd'],$item['precision'])) ?>" class="change_profit" data-sales="sales_price_container" data-markup="markup_container"/> <b>USD</b><span style="color:red;font-size: 9px">
                                <?php $temp = reset($item_containers); ?>
                                <i>20ft =$<?= number_format((bcsub($pro['sales_price_container'],
                            $pro['price_usd'],$item['precision']))*$temp['contload_20ft_qty'])?> USD 40ft =$ <?= number_format((bcsub($pro['sales_price_container'],$pro['price_usd'],$item['precision']))*$pro['contload_40ft_qty'])?> USD</i></span></td>
                         <th style="width: 7%;">Markup :</th>
                        <td style="width: 26%;"><input name="<?= $itemkeyname ?>[markup_container]" type="number" data-row="<?= $pro['id'] ?>" size="16" step="any" class="change_markup" data-sales="sales_price_container" data-profit="product_profitus_container" value="<?php hecho($pro['markup_container'])?>" /> <b>%</b></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style="background: #eed;">
            <th>Specials :</th>
            <td>
                <table style="margin-left: 0px;width: 100%;">
                    <tr style="background: #eed;">
                      
                        <td style="width: 10%;">
                            <input name="<?= $itemkeyname ?>[special_flag]" value="y" type="checkbox" onclick="toggleSection('specialstd<?= $pro['id']?>', this.checked); toggleSection('specialsth<?= $pro['id']?>', this.checked);toggleSection('special1th<?= $pro['id']?>', this.checked);toggleSection('special1td<?= $pro['id']?>', this.checked); "<?=('y' == $pro['special_flag']) ? ' checked="checked"' : ''?> />
                        </td>
                        <th id="specialsth<?=$pro['id']?>" style="width: 20%;<?=('y' == $pro['special_flag']) ? '' : 'display:none'?>">Specials Price :</th>
                        <td id="specialstd<?=$pro['id']?>" style="width: 33%;<?=('y' == $pro['special_flag']) ? '' : 'display:none'?>" >
                            <input type="text" style="text-align:right;" name="<?= $itemkeyname ?>[special_priceus]" value="<?= $pro['special_priceus'] ?>"/> <b>USD</b>
                        </td>
                        <th id="special1th<?=$pro['id']?>" style="width: 10%;<?=('y' == $pro['special_flag']) ? '' : 'display:none'?>" >Expires :</th>
                        <td id="special1td<?=$pro['id']?>" style="width: 26%;<?=('y' == $pro['special_flag']) ? '' : 'display:none'?>"  ><input type="date" name="<?= $itemkeyname ?>[special_expiry]" value="<?=(is_null($pro['special_expiry'])) ? '' : substr($pro['special_expiry'], 0, 10) ?>" />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <?php if($pro["default"]!="y"){ ?>
            <tr style="background: #eed;">
                <th></th>
                <td><input type="button" style="padding: 3px; float: right; margin: 5px;" data-default="<?= $sub_product[$defaultkey]['id']?>" data-key="<?= $pro['id']?>" class="copy-default-data" value="Copy Markup % from  <?= $sub_product[$defaultkey]['name']?>"></td>
            </tr>
        <?php } ?>
    </table>
<?php } 
    }
?>

<?php
if (is_array($item_price_changes) && count($item_price_changes)) {
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

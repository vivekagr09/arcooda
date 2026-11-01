<?php
/**
 * @param mixed[]   $product
 * @param mixed[]   $item_dimension
 * @param mixed[][] $item_packings
 */
?>
<!-- _weight_shipping.tpl -->

<?php
/**
 * Product Weight/Shipping template
 *
 * Inputs:
 * - $item_dimension
 */

/**
 * TODO: Add the mass and cubic fields to database, and remove this hack
 */
$item['cubic'] = '0.000';
$item['mass'] = '0.000';
$total_cuboc=0;
$total_weight=0;

foreach ($item_packings as $k => $v) {
    $total_cuboc = bcadd($total_cuboc, $v['_volume'], 3);
    $total_weight  = bcadd($total_weight, $v['weight'], 3);
}
$total_cuboc=($total_cuboc/5000);
$item['cubic']=($total_cuboc>$total_weight)?$total_cuboc:$total_weight;
$item['mass']=$item_dimension['weight'];
$item['weight']=($item['cubic']>$item['mass'])?$item['cubic']:$item['mass'];
if (!function_exists('kg2lb')) {
    /**
     * @param string $kg
     *
     * @return string
     */
    function kg2lb($kg = '0.001') {
        return bcmul($kg, "2.20462", 3);
    }
}
if (!function_exists('cubic_weight')) {
    /**
     * @param string $height, $width, $depth
     *
     * @return string
     */
    function cubic_weight($width, $height, $depth) {
        return bcdiv(bcmul(bcmul($width,$height,2), $depth, 2),"1000000",2);
    }
}
if (!function_exists('shipping_weight')) {
    /**
     * @param string $height, $width, $depth
     *
     * @return string
     */
    function shipping_weight($width, $height, $depth) {
        return bcdiv(bcmul(bcmul($width,$height,2), $depth, 2),"5000",2);
    }
}
if (!function_exists('lb2kg')) {
    /**
     * @param string $lb
     *
     * @return string
     */
    function lb2kg($lb) {
        return bcmul($lb, "0.453592", 3);
    }
}
if (!function_exists('cm2in')) {
    /**
     * @param string $cm
     *
     * @return string
     */
    function cm2in($cm = '0.001') {
        return bcmul($cm, "0.393701", 3);
    }
}
if (!function_exists('in2cm')) {
    /**
     * @param string $in
     *
     * @return string
     */
    function in2cm($in) {
        return bcmul($in, "2.54", 1);
    }
}

/**
 * Packing Dimension entry template
 *
 * @param mixed $count
 * @param array{id:int, product_id:int, width:string, depth:string, height:string, weight:string, ...} $row_data
 *
 * @return string HTML Content of the entry
 */
function item_packings_row($count = '[ROW_COUNT]', $row_data = array())
{
    $name_key = 'item_packings[' . $count . ']';

    $hidden = $row_data;
    unset($hidden['width']);
    unset($hidden['depth']);
    unset($hidden['height']);
    unset($hidden['weight']);
    unset($hidden['name']);
    ob_start();
     if(!isset($hidden['_volume'])){
       $hidden['_volume']='';
    }
?>
<table class="table_fields" id="item_packings_rowa[<?=$count?>]" style="width:100%;margin:0;border:none;margin-bottom:10px;">
    <tr class="alt">
        <th style="width:17%;" rowspan="2">
            #<?=++$count?>
            <input type="text" name="<?= $name_key ?>[name]" value="<?= $row_data['name'] ?>" placeholder="Package Name" style="margin-left:15px;width:70%"/>
        </th>
        <th style="width:8%;white-space:nowrap;">(cm/kg) :</th>
        <td>
            <?=formatHTMLInputs($hidden, $name_key)?>
            Width = 
            <input type="number" min="0.0" step="any" style="width:5em;" name="<?=$name_key?>[width]" value="<?=$row_data['width']?>" />
            Depth = 
            <input type="number" min="0.0" step="any" style="width:5em;" name="<?=$name_key?>[depth]" value="<?=$row_data['depth']?>" />
            Height =
            <input type="number" min="0.0" step="any" style="width:5em;" name="<?=$name_key?>[height]" value="<?=$row_data['height']?>" />
            Weight = 
            <input type="number" min="0.000" step="any" style="width:5em;" name="<?=$name_key?>[weight]" value="<?=$row_data['weight']?>" />
        </td>
        <th style="width:auto;" rowspan="2"><button name="<?=$name_key?>[delete]" style="display:none;"><i class="fas fa-trash-alt"></i></button></th>
    </tr>
    <tr>
        <th style="width:5%;">(in/lb) :</th>
        <td>
            Width = 
            <input type="number" min="0.000" step="any" style="width:5em;" name="<?=$name_key?>[width_in]" value="<?=cm2in($row_data['width'])?>" />
            Depth =
            <input type="number" min="0.000" step="any" style="width:5em;" name="<?=$name_key?>[depth_in]" value="<?=cm2in($row_data['depth'])?>" />
            Height =
            <input type="number" min="0.000" step="any" style="width:5em;" name="<?=$name_key?>[height_in]" value="<?=cm2in($row_data['height'])?>" />
            Weight =
            <input type="number" min="0.000" step="any" style="width:5em;" name="<?=$name_key?>[weight_lb]" value="<?=kg2lb($row_data['weight'])?>" />
        </td>
    </tr>
</table>
<!-- <table style="border:none;">
    <tr class="alt"><td></td></tr>
    <tr class="alt"><td></td></tr>
    <tr class="alt"><td></td></tr>
</table> -->
<?php
    return ob_get_clean();
}
?>

<script type="text/javascript">
//<![CDATA[
function kg2lb($kg) {
    return bcmul($kg, "2.20462", 3);
}
function lb2kg($lb) {
    return bcmul($lb, "0.453592", 3);
}
function cm2in($cm) {
    return bcmul($cm, "0.393701", 3);
}
function in2cm($in) {
    return bcmul($in, "2.54", 1);
}

$(function() {

    // item_dimension related:
    function item_dimension_events(context) {
        $(ProductDimension.selector(["width", "height", "depth"]), context).change(function () {
            var field  = ProductDimension.field(this);
            var width  = ProductDimension.get("width");
            var height = ProductDimension.get("height");
            var depth  = ProductDimension.get("depth");
            ProductDimension.set(field + "_in", cm2in($(this).val()), true);
            ProductDimension.set("_volume", bcmul(bcmul(width, depth, 2), height, 3));
            updateFreeApprox();
        });
        $(ProductDimension.selector(["width_in", "height_in", "depth_in"]), context).change(function () {
            var field = ProductDimension.field(this);
            ProductDimension.set(substr(field, 0, -3), in2cm($(this).val()));
        });
        $(ProductDimension.selector(["weight"]), context).change(function () {
            ProductDimension.set("weight_lb", kg2lb($(this).val()), true);
              setValue("item[mass]", $(this).val());
              setValue("item[mass_lb]", kg2lb($(this).val()));
              //setValue('item[weight]',($(this).val()>getValue('item[cubic]'))?$(this).val():getValue('item[cubic]'));
              // setValue('item[weight_lb]',kg2lb(getValue('item[cubic]')));
           });
        $(ProductDimension.selector(["weight_lb"]), context).change(function () {
            ProductDimension.set("weight", lb2kg($(this).val()));
              setValue("item[mass]", lb2kg($(this).val()));
              setValue("item[mass_lb]", $(this).val());
        });
        $(ProductDimension.selector(["_volume"])).change(function() {
            ProductDimension.set("volume_weight", bcdiv(ProductDimension.get("_volume"), "5000.000", 3));
        });
        $(ProductDimension.selector(["weight", "volume_weight"]), context).change(function() {
            var sum1 = ProductDimension.get("weight");
            var sum2 = ProductDimension.get("volume_weight");
            ProductDimension.set("_weight", (bccomp(sum1, sum2, 3) >= 0) ? sum1 : sum2);
        });
    }
    item_dimension_events();

    // item_packings related:
    function item_packing_events(context) {
        $(ProductPacking.selector(["width", "height", "depth"]), context).change(function () {
            var row    = ProductPacking.row(this);
            var field  = ProductPacking.field(this);
            var width  = ProductPacking.get("width", row);
            var height = ProductPacking.get("height", row);
            var depth  = ProductPacking.get("depth", row);
            var row_volume=bcmul(bcmul(width, depth, 3), height, 3);
            ProductPacking.set(field + "_in", row, cm2in($(this).val()), true);
            ProductPacking.set("_volume", row,row_volume);
            //var total_weight=ProductPacking.sum("weight", 3);
            var total_volume=ProductPacking.sum("_volume", 3);
            var total_volume_div=bcdiv(ProductPacking.sum("_volume", 3),5000,3);
            Product.set("weight",total_volume_div);
            Product.set("cubic",total_volume_div);
            Product.set("cubic_lb",kg2lb(total_volume_div));
            Product.set("cubic_weight",bcdiv(total_volume,1000000,3));
            updateCratingApprox();
            $('#shipping_weight_'+row).html(bcdiv(row_volume,5000,3));
            $('#shipping_weight_total').html(total_volume_div+' kgs');
            $('#cubic_weight_'+row).html(bcdiv(row_volume,1000000,3));
            $('#cubic_weight_total').html(bcdiv(total_volume,1000000,3)+' kgs');
            
        });
        $(ProductPacking.selector(["width_in", "height_in", "depth_in"]), context).change(function () {
            var row   = ProductPacking.row(this);
            var field = ProductPacking.field(this);
            ProductPacking.set(substr(field, 0, -3), row, in2cm($(this).val()));
        });
        $(ProductPacking.selector(["weight"]), context).change(function () {
            var row = ProductPacking.row(this);
            $('#physical_weight_'+row).html($(this).val());
            var total_weight=ProductPacking.sum("weight", 2);
            setValue('item[product]',total_weight);
            setValue('item[product_lb]', kg2lb(total_weight));
            ProductPacking.set("weight_lb", row, kg2lb($(this).val()), true);
            $('#physical_weight_total').html(total_weight+' kgs');
            
        });
        $(ProductPacking.selector(["weight_lb"]), context).change(function () {
            var row   = ProductPacking.row(this);
            ProductPacking.set("weight", row, lb2kg($(this).val()));
            var total_weight=ProductPacking.sum("weight", 2);
            setValue('item[product]',total_weight);
            setValue('item[product_lb]', kg2lb(total_weight));
            $('#physical_weight_'+row).html($(this).val());
            $('#physical_weight_total').html(total_weight+' kgs');
            
        });
        $(ProductPacking.selector(["_volume"]), context).change(function() {
            var row = ProductPacking.row(this);
            ProductPacking.set("_cubic", row, bcdiv(ProductPacking.get("_volume", row), "5000.000", 3));
        });
        $(ProductPacking.selector(["weight", "_cubic"]), context).change(function() {
            var row  = ProductPacking.row(this);
            var sum1 = ProductPacking.get("weight", row);
            var sum2 = ProductPacking.get("_cubic", row);;
            ProductPacking.set("_weight", (bccomp(sum1, sum2, 3) >= 0) ? sum1 : sum2);
        });  
        $(ProductPacking.selector("_cubic"), context).change(function() {
            Product.set("cubic", ProductPacking.sum("_cubic", 3));
        });
        $(ProductPacking.selector("delete"), context).click(function () {
            var row = ProductPacking.row(this);
            setValue("item_packings_deleted", getValue("item_packings_deleted") + "," + ProductPacking.get("packing_id", row));
            $(this).closest("table").detach();
            $('#item_packings_des_rowa_'+row+'').remove();
            var total_volume=ProductPacking.sum("_volume", 3);
            var total_volume_div=bcdiv(ProductPacking.sum("_volume", 3),5000,3);
            Product.set("weight",total_volume_div);
            Product.set("cubic",total_volume_div);
            Product.set("cubic_lb",kg2lb(total_volume_div));
            Product.set("cubic_weight",bcdiv(total_volume,1000000,3));
            var total_weight=ProductPacking.sum("weight", 2);
            setValue('item[product]',total_weight);
            setValue('item[product_lb]', kg2lb(total_weight));
            updateCratingApprox();
            $('#shipping_weight_total').html(total_volume_div+' kgs');
            $('#cubic_weight_total').html(bcdiv(total_volume,1000000,3)+' kgs');
            var total_weight=ProductPacking.sum("weight", 2);
            $('#physical_weight_total').html(total_weight+' kgs');
            return false;
        }).fadeIn();
    }
    item_packing_events();

    // item related:
    $(Product.selector(["mass", "cubic", "weight", "weight_inc"])).change(function () {
        var field = Product.field(this);
        Product.set(field + "_lb", kg2lb($(this).val()), true);
        
    });
    $(Product.selector(["mass_lb", "cubic_lb", "weight_lb", "weight_inc_lb"])).change(function () {
        var field = Product.field(this);
        Product.set(substr(field, 0, -3), lb2kg($(this).val()));
    });
    $(Product.selector(["mass", "cubic"])).change(function() {
        var sum1 = Product.get("mass");
        var sum2 = Product.get("cubic");
        Product.set("weight", (bccomp(sum1, sum2, 3) >= 0) ? sum1 : sum2);
    });

    function weight_shipping_packing_add() {
        var $row = $(<?=strtr(jecho(item_packings_row('[ROW_COUNT]', array(
            '' => '',
        )), 1), array(
            '[ROW_COUNT]' => '" + item_packings_count + "',
        ))?>);
        $("#item_packings_table").append($row);
        item_packing_events($row);
        var des_row='<tr id="item_packings_des_rowa_'+item_packings_count+'">\
                    <td style="text-align:center;background: #fec;" ><strong>Package #'+item_packings_count+' :</strong></td>\
                    <td style="text-align:center;" id="cubic_weight_'+item_packings_count+'">0.00 cm3</td>\
                    <td style="text-align:center;" id="physical_weight_'+item_packings_count+'"> 0.00 kgs</td>\
                    <td style="text-align:center;" id="shipping_weight_'+item_packings_count+'">0.00 kgs</td>\
                    </tr>';
        $("#packagind_data_description").append(des_row);
        item_packings_count++;

        return item_packings_count - 1;
    }

    $(".weight_shipping_packing_copy").click(function() {
        var row = weight_shipping_packing_add();
        ProductPacking.set('width', row, ProductDimension.get('width'));
        ProductPacking.set('depth', row, ProductDimension.get('depth'));
        ProductPacking.set('height', row, ProductDimension.get('height'));
        ProductPacking.set('weight', row, ProductDimension.get('weight'));
        return false;
    }).show();

    // var item_packings_count = <?=max(100, count($item_packings))?>;
    var item_packings_count = <?=count($item_packings)?>;
    $(".weight_shipping_packing_add").click(function() {
        weight_shipping_packing_add();
        return false;
    }).show();

    $(".weight_shipping_loaded").show();
});
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionShipping&quot;);return false">Weight/Shipping</a>')?>

<input type="hidden" name="item[_volume]" value="" />

<div id="sectionShipping">
    <table class="table_fields">
    <tr>
        <th colspan="7" style="text-align:left; background:#fff;"><strong>Actual Size & Weight</strong>
        </th>
    </tr>
<?php
if ('' !== $item['dimension']) {
?>
        <tr>
            <th>Dimensions (legacy) :</th>
            <td><input type="text" name="item[dimension]" value="<?=$item['dimension']?>" /></td>
        </tr>
<?php
}
if (isset($item_dimension) && is_array($item_dimension)) {
?>
        <tr class="alt">
            <th>Unit Size (cm) :</th>
            <td>
                <input type="hidden" name="item_dimension[_volume]" value="<?php hecho($item['_volume']) ?>" />
                <input type="hidden" name="item_dimension[_weight]" value="<?php hecho($item['_weight']) ?>" />
                <input type="hidden" name="item_dimension[volume_weight]" value="<?php hecho($item['volume_weight']) ?>" />
                Width = 
                <input type="number" min="0.0" step="any" style="width:5em;" name="item_dimension[width]" value="<?=$item_dimension['width']?>" /> Depth =
                <input type="number" min="0.0" step="any" style="width:5em;" name="item_dimension[depth]" value="<?=$item_dimension['depth']?>" /> Height =
                <input type="number" min="0.0" step="any" style="width:5em;" name="item_dimension[height]" value="<?=$item_dimension['height']?>" /> Weight =
                <input type="number" min="0.000" step="any" style="width:5em;" name="item_dimension[weight]" value="<?=$item_dimension['weight']?>" /> (kg)
            </td>
        </tr>
        <tr class="weight_shipping_loaded" style="display:none;">
            <th>Unit Size (inches) :</th>
            <td>
                Width =
                <input type="number" min="0.000" step="any" style="width:5em;" name="item_dimension[width_in]" value="<?=cm2in($item_dimension['width'])?>" /> Depth =
                <input type="number" min="0.000" step="any" style="width:5em;" name="item_dimension[depth_in]" value="<?=cm2in($item_dimension['depth'])?>" /> Height =
                <input type="number" min="0.000" step="any" style="width:5em;" name="item_dimension[height_in]" value="<?=cm2in($item_dimension['height'])?>" /> Weight =
                <input type="number" min="0.000" step="any" style="width:5em;" name="item_dimension[weight_lb]" value="<?=kg2lb($item_dimension['weight'])?>" /> (lb)
            </td>
        </tr>
<?php
}
?>
</table>
<table class="table_fields" style="margin-top:20px;">
    <tr>
    <th colspan="7" style="text-align:left; background:#fff;"><strong>Packing Size & Weight</strong>
    <?php
        if (isset($item_packings) && is_array($item_packings)) {
            if (isset($item_dimension) && is_array($item_dimension)) {
        ?>
           <!--  <button class="weight_shipping_packing_copy" style="display:none;" title="Copy Unit Dimensions To Packing Dimensions"><i class="fas fa-copy"></i></button> -->
        <?php
            }
        ?>
            <button class="weight_shipping_packing_add" style="display:none;" title="Add Packing Dimension"><i class="fas fa-plus"></i></button>
        <?php
        }
        ?>
    </th>
</tr>
<tr><td colspan="2" style="background:#eeeee0;"><div id="item_packings_table">

    

<?php
$temp2 = 0;
foreach ($item_packings as $temp) {
    echo item_packings_row($temp2, $temp);
    $temp2++;
}
unset($temp2);
?>
    <input type="hidden" name="item_packings_deleted" value="" />
    </div></td></tr>
    <tr class="alt">
        <td>
            <table class="table_grid" id="products_shipping_info" style="width:50%;margin-bottom:20px;">
                  <thead>
                    <tr>
                        <th style="background: #fec;"></th>
                        <th style="text-align:center;background: #fec;">Cubic Weight</th>
                        <th style="text-align:center;background: #fec;">Physical Weight</th>
                        <th style="text-align:center;background: #fec;">Shipping Weight</th>
                    </tr>
                    </thead>
                     <tbody id="packagind_data_description">
                    <?php 
                    $pkg_count = 0;
                    $cubic_total = 0;
                    $shipping_total = 0;
                    $weight_total = 0;
                    $rowid=0; 

                    foreach($item_packings as $key => $temp) {    
                    ?>
                   
                    <tr id="item_packings_des_rowa_<?=$rowid ?>">
                        <td style="text-align:center;background: #fec;" ><strong>Package #<?=++$pkg_count?> :</strong></td>
                        <td style="text-align:center;" id="cubic_weight_<?= $rowid ?>"><?=cubic_weight($temp['width'],$temp['height'],$temp['depth'])?></td>
                        <td style="text-align:center;" id="physical_weight_<?= $rowid ?>"><?=$temp['weight']?></td>
                        <td style="text-align:center;" id="shipping_weight_<?= $rowid ?>"><?=shipping_weight($temp['width'],$temp['height'],$temp['depth'])?></td>
                    </tr>
                    
                    <?php
                    $cubic_total    = bcadd($cubic_total,cubic_weight($temp['width'],$temp['height'],$temp['depth']),2);
                    $shipping_total = bcadd($shipping_total,shipping_weight($temp['width'],$temp['height'],$temp['depth']),2);
                    $weight_total   = bcadd($weight_total,$temp['weight'],2);
                    $rowid++;
                } 
                    unset($pkg_count);
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td style="text-align:center;background: #fec;"><strong>TOTALS :</strong></td>
                            <td style="text-align:center;background: #fec;" id="cubic_weight_total"><strong><?=$cubic_total?> cm3</strong></td>
                            <td style="text-align:center;background: #fec;" id="physical_weight_total"><strong><?=$weight_total?> kgs</strong></td>
                            <td style="text-align:center;background: #fec;" id="shipping_weight_total"><strong><?=$shipping_total?> kgs</strong></td>
                        </tr>
                    </tfoot>
            </table>
        </td>
    </tr>
</table>
    <table class="table_fields" style="margin-top:20px;">
        <tr>
            <th colspan="7" style="text-align:left; background:#fff;"><strong>Summary</strong>
            </th>
        </tr>
        <tr class="alt">
            <th>Product Weight :</th>
            <td>
                <input type="number" min="0.000" step="any" style="width:20em;" name="item[mass]" value="<?=$item['mass']?>" /> kg
                <input type="number" min="0.000" step="any" style="width:20em;" name="item[mass_lb]" value="<?=kg2lb($item['mass'])?>" /> lb
            </td>
        </tr>
        <tr class="alt">
            <th>Physical Package Weight :</th>
            <td>
                <input type="number" min="0.000" step="any" style="width:20em;" name="item[product]" value="<?=$weight_total?>" /> kg
                <input type="number" min="0.000" step="any" style="width:20em;" name="item[product_lb]" value="<?=kg2lb($weight_total)?>" /> lb
            </td>
        </tr>
        <tr class="alt">
            <th>Volume Package Weight :</th>
            <td>
                <input type="number" min="0.000" step="any" style="width:20em;" name="item[cubic]" value="<?=$shipping_total?>" /> kg
                <input type="number" min="0.000" step="any" style="width:20em;" name="item[cubic_lb]" value="<?=kg2lb($shipping_total)?>" /> lb <span style="color: red;margin-left: 6px;font-size: 9px;font-style: italic;">Volume Calculation = W x D x H / 5000</span>
            </td>
        </tr>
        <tr class="alt">
            <th>Shipping Package Weight :</th>
            <td>
                <input type="number" min="0.000" step="any" style="border:2px solid red;width:20em;" name="item[weight]" value="<?=$item['weight']?>" /> kg
                <input type="number" min="0.000" step="any" style="width:20em;" name="item[weight_lb]" value="<?=kg2lb($item['weight'])?>" /> lb
            </td>
        </tr>
        <tr class="alt">
            <th>Incremental Weight :</th>
            <td>
                <input type="number" min="0.000" step="any" style="width:20em;" name="item[weight_inc]" value="<?=$item['weight_inc']?>" /> kg
                <input type="number" min="0.000" step="any" style="width:20em;" name="item[weight_inc_lb]" value="<?=kg2lb($item['weight_inc'])?>" /> lb
            </td>
        </tr>
        <tr class="alt">
            <th>Cubic Weight :</th>
            <td>
                <input type="number" min="0.000" step="any" style="width:20em;" name="item[cubic_weight]" value="<?=$cubic_total?>" /> cm3
            </td>
        </tr>
        <tr class="alt"><th><td></td></th></tr>
        <tr class="alt"><th><td></td></th></tr>
        <tr class="alt">
            <th>Online Notes :</th>
            <td>
                <textarea name="item[packing_text]" id="item[packing_text]" cols="75" rows="2"><?= $item['packing_text'] ?></textarea>
            </td>
        </tr>
        <tr class="alt"><th><td></td></th></tr>
        <tr class="alt"><th><td></td></th></tr>
    </table>
    <br /><br />
     <div style="width:90%;display:flex;gap:20px;margin:0 auto;margin-bottom:20px;">
        <div>
            <input type="checkbox" value="y" id="item[bypass_freight]" name="item[bypass_freight]" <?=('y' == $item['bypass_freight']) ? ' checked="checked"' : ''?> style="width:20px;height:20px;">
        </div>
        <div style="color:red;">
            <b style="font-size:14px;">Bypass Freight System (Digital Download or Free Freight)</b><br><span style="font-style:italic;font-size:10px;">Freight will not show in the checkout and allow the person to checkout without freight options being displayed.</span>
        </div>
    </div>
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionShipping_hideNote">
    Weight/Shipping can be viewed by Clicking "Weight/Shipping" above<br />
</div>

<!-- /_weight_shipping.tpl -->

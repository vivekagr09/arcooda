<!-- _product_item.tpl -->

<?php
/**
 * Product Item Template
 *
 * Inputs:
 * - $item
 * - - available
 * - - ce
 * - - condition
 * - - consignment_desc
 * - - country_location
 * - - ctick
 * - - image
 * - - image_url
 * - - pcb_type
 * - - product_name
 * - - rohs
 * - - section
 * - - storage
 * - - thumb_url
 * - - type
 * - - ul
 * - - version
 * - - warranty
 * - - year_released
 */

if (!isset($item) || !is_array($item)) {
    return;
}

$type_list = productGetOptionList('type');
?>

<script type="text/javascript">
//<![CDATA[
let ProductCategory = {
    row: function (elem) {
        return $(elem).attr("name").split("[")[1].split("]")[0];
    },
    rows: function () {
        var result = [];
        var self = this;
        $(this.selector("id")).each(function() {
            result.push(self.row(this));
        });
        return result;
    },
    field: function (elem) {
        return $(elem).attr("name").split("[")[2].split("]")[0];
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
            return "[name^='item_categories['][name$='][" + field + "]']";
        }
        return "[name='item_categories[" + row + "][" + field + "]']";
    },
    get: function (field, row) {
        return $(this.selector(field, row)).val();
    },
    set: function (field, row, value) {
        var $elem = $(this.selector(field, row));
        if (value !== $elem.val()) $elem.val(value).change();
    }
};

$(function() {

    /**
     * Setup the previous-value saving mechanism
     */
    $(Product.selector(["product_name", "type", "pcb_type", "available", "country_location", "consignment_desc", "condition", "warranty", "version", "year_released", "ce", "ctick", "ul", "rohs"])).each(function() {
        $(this).data("prev", $(this).val());
    });

    /**
     * When main category changes, auto-update categories
     */
    $(Product.selector(["type"])).change(function() {
        $(ProductCategory.selector("category_id", $(this).data("prev"))).removeAttr("checked").change();
        $(ProductCategory.selector("category_id", $(this).val())).attr("checked", "checked").change();
        $(this).data("prev", $(this).val());
    }).change();


    $("[name='item_image']").change(function() {
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#item_image_img").attr("src", e.target.result);
            $("#item_image_a").attr("href", e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    });
    $("[name='item_thumb']").change(function() {
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#item_thumb_img").attr("src", e.target.result);
            $("#item_thumb_a").attr("href", e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    });
// Code updated 25th apr 2024
    $("[name='item[section]']").change(function() {
        alert('You need to select the expanded category again after changing the main category.');
        var id = $(this).val();
        var cat = [1, 2, 3, 4]; // All category IDs

        // Hide all categories
        cat.forEach(function(c) {
            $('.categories_' + c).hide();
        });

        // Show the selected category
        $('.categories_' + id).show();
        $('.category_checked').prop('checked', false);
    });
    $('.category_checked').click(function(){ 
    if($(this).is(':checked')){
        $('#item_categories_'+$(this).attr('data-parent')).prop('checked', true);
        }else{
            var categoryValue = $(this).attr('data-parent'); 
            var selectedCheckboxes = $('.category_checked[data-parent="' + categoryValue + '"]:checked');
            var count = selectedCheckboxes.length;
            if(count <= 1){
                $('#item_categories_'+$(this).attr('data-parent')).prop('checked', false);
            }
        }
    })
    var initialId = $("[name='item[section]']").val();
    var cat = [1, 2, 3, 4]; // All category IDs

    // Hide all categories
    cat.forEach(function(c) {
        $('.categories_' + c).hide();
    });

    // Show the category that matches the initialId
    $('.categories_' + initialId).show();
//end
});
//]]>
$(document).ready(function() {
    $('.select2').select2();
});
 $('body').on('change','.arvivalchnage',function(){
    if($(this).val()<0){
        $('#arrival_date').show();
        $('#deposit_needed').show();
    }else{
        $('#arrival_date').hide();
        $('#deposit_needed').hide();
        $('#arrival_date').val('');
        $('#deposit_needed').val(0.00);
    }
 });
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionItem&quot;);return false">Product Item</a>')?>

<div id="sectionItem">
    <table class="table_fields">
        <tr>
            <th style="width:25%;">Product Name :</th>
            <td><input class="wide" type="text" name="item[product_name]" value="<?php hecho($item['product_name'])?>" /></td>
        </tr>
        <tr>
            <th>Product Section :</th>
            <td><select name="item[section]">
                <option value="0"></option>
                <?=formatHTMLOptions(productGetOptionList('section'), $item['section'])?>
            </select></td>
        </tr>

        <tr>
            <th>Category (<small><i><a href="/webadmin/products/sections_admin.php?table=product_types&view=tree" target="_blank" >Add</a></i></small>) :</th>
            <td>
                <select name="item[type]" style="width:80%;">
                    <option value="">Not Selected</option>
<?php
$type_tree = productGetOptionList('type_tree');
foreach (productGetOptionList('section') as $section_id => $section_name) {
?>
    <optgroup label="<?php hecho($section_name)?>" class="optgrp_section-<?=$section_id?>">
<?php
    if (array_key_exists($section_id, $type_tree)) {
        $temp = array_reverse($type_tree[$section_id]);
        unset($type_tree[$section_id]);
        while (count($temp)) {
            $type_id = array_pop($temp);
?>
        <option value="<?=$type_id?>"<?=($item['type'] == $type_id) ? ' selected="selected"' : ''?>><?php hecho($type_list[$type_id])?></option>
<?php
            if (array_key_exists($type_id, $type_tree)) {
                $temp += array_reverse($type_tree[$type_id]);
            }
        }
    }
?>
    </optgroup>
<?php
}
unset($type_tree[0]);
foreach ($type_tree as $temp) {
    foreach ($temp as $type_id) {
?>
    <option value="<?=$type_id?>"<?=($item['type'] == $type_id) ? ' selected="selected"' : ''?>><?php hecho($type_list[$type_id])?></option>
<?php
}
}
?>
                </select>
                <span id="sectionItemCategory_hideNote">(<a href="#" onclick="toggleSection('sectionItemCategory');return false;">Expand</a>)</span>
                <span id="sectionItemCategory" style="display:none;">
                    (<a href="#" onclick="toggleSection('sectionItemCategory');return false;">Collapse</a>)
                    <table class="table_grid">
<?php
$type_tree = productGetOptionList('type_tree');
// Code updated 25th apr 2024
$list_cat = array_column($item_categories, 'category_id');
$found_key = null;

foreach ($type_tree as $key => $sub_array) {
    // Check if there are common elements between $list_cat and $sub_array
    $intersection = array_intersect($list_cat, $sub_array);
    
    if (!empty($intersection)) { // If there is any common element
        $found_key = $key; // Store the key where common elements are found
        break; // Exit the loop once a key is found
    }
}
//end
$temp          = array_reverse($type_tree[0]);
unset($type_tree[0]);
$alt = false;
$indent = '';
while (count($temp)) {
    $type_id = array_pop($temp);
    if ('>' === $type_id) {
        $indent .= '&nbsp;&nbsp;&nbsp;&nbsp;';
    } elseif ('<' === $type_id) {
        $indent = substr($indent, 0, -24);
    } else {
        // Code updated 25th apr 2024
        foreach ($type_tree as $key => $sub_array) {
            if (in_array($type_id, $sub_array)) { // Check if the value exists in the sub-array
                $key_id = $key; // Store the key where it's found
                break; // Exit the loop after finding the key
            } else {
                $key_id = $type_id;
            }
        }
        // Default class name
        $class_name = 'categories_' . $key_id;
    
        // Add "alt" if the condition meets
        if ($alt = !$alt) {
            $class_name = 'alt ' . $class_name; // Add "alt" at the beginning
        }
        //end
?>
                        <tr class="<?= $class_name ?>">
                            <td><?=$indent?><span>
                            <input type="checkbox" class="category_checked" id="item_categories_<?=$type_id?>" name="item_categories[<?=$type_id?>][category_id]" data-parent="<?= $key_id ?>" value="<?=$type_id?>" <?= in_array($type_id, array_column($item_categories, 'category_id'))?'checked':'';?> <?= ($found_key == $type_id) ? 'checked': ''?>/>
                             <label for="item_categories_<?=$type_id?>"><?php hecho($type_list[$type_id])?></label></span></td></tr>
<?php
if (array_key_exists($type_id, $type_tree)) { 
            $temp[] = '<';
            foreach(array_reverse($type_tree[$type_id]) as $value){
               $temp[] = $value;  
            }
            $temp[] = '>';
        }
    }
}
?>
                    </table>
                </span>
                <input type="hidden" name="item_categories_deleted" value="" />
            </td>
        </tr>
        <tr class="section-1 section-4">
            <th>Colour(select multiple to create variant) (<small><i><a href="/webadmin/color" target="_blank">Add</a></i></small>) :</th>
            <td><select name="item[colour][]" class="select2" multiple="multiple">
                <?=formatHTMLOptions(productGetOptionList('colour'), explode(',', $item['colour']))?>
            </select></td>
        </tr>
      <!--   <tr class="section-2 section-3 section-4">
            <th>Product Style (<small><i><a href="options.php?action=list&amp;option_name=style" target="_blank">Add</a></i></small>) :</th>
            <td><select name="item[style]">
                <option value="0">Not Selected</option>
                <?=formatHTMLOptions(productGetOptionList('style'), $item['style'])?>
            </select></td>
        </tr>
        <tr class="section-3">
            <th>PCB Type (<small><i><a href="options.php?action=list&amp;option_name=pcb_type" target="_blank">Add</a></i></small>) :</th>
            <td><select name="item[pcb_type]">
                <option value="0">Not Selected</option>
                <?=formatHTMLOptions(productGetOptionList('pcb_type'), $item['pcb_type'])?>
            </select></td>
        </tr> -->
         <tr>
            <th>Product Series(<small><i><a href="/webadmin/product_series/addedit.php?action=do_add" target="_blank">Add</a></i></small>) :</th>
            <td><select name="item[product_series]">
                <option value="0">Not Selected</option>
                <?=formatHTMLOptions(productGetOptionList('product_series'), $item['product_series'])?>
            </select></td>
        </tr>
        <tr>
            <th>Storage Location (<small><i><a href="options.php?action=list&amp;option_name=storage" target="_blank">Add</a></i></small>) :</th>
            <td><select name="item[storage]">
                <option value="0">Not Selected</option>
                <?=formatHTMLOptions(productGetOptionList('storage'), $item['storage'])?>
            </select></td>
        </tr>
        <tr>
            <th>Availability (<small><i><a href="options.php?action=list&amp;option_name=available" target="_blank">Add</a></i></small>) :</th>
            <td>
                <?php $pre_order=productGetOptionList('available');
                      $pre_order[-1]='Pre Order';
                ?>
                <select name="item[available]" class="arvivalchnage"  <?= (count(explode(',', $item['colour'])) > 1 ? 'disabled':'') ?>>
                <option value="0">Not Selected</option>
                <?=formatHTMLOptions($pre_order, $item['available'])?>
            </select></td>
        </tr>
        <tr <?=('-1' == $item['available']) ? '' : 'style="display:none"'?> id="arrival_date">
            <th>Arrival Date</th>
            <td>
                    <input type="date" name="item[arrival_date]" value="<?php hecho((is_null($item['arrival_date'])) ? '' : substr($item['arrival_date'], 0, 10))?>"/>
                        <button onclick="Product.set(&quot;arrival_date&quot;, &quot;&quot;);return false"><i class="fas fa-trash-alt"></i></button>
            </td>
        </tr>
        <tr <?=('-1' == $item['available']) ? '' : 'style="display:none"'?> id="deposit_needed">
            <th>Deposit Needed</th>
            <td>
                   <input type="number" name="item[deposit_needed]" value="<?= $item['deposit_needed'] ?>" >
                   <span style="font-size: 16px;">
                   <input type="checkbox" name="item[full_payment]" value="y" <?= ($item['full_payment']=='y')?'checked':''; ?>>
                   Full Payment
               </span>
             </td>
        </tr>
        <tr>
            <th>Country Location :</th>
            <td><select name="item[country_location]">
                <option value="0">Not Selected</option>
                <?=formatHTMLOptions(productGetOptionList('country_location'), $item['country_location'])?>
            </select></td>
        </tr>
        <tr class="stock-type-consignment" id="consignment_product">
            <th>Special Notes (for Consignment Product) :</th>
            <td>
                <b>THIS PRODUCT CAN ONLY BE ORDERED MANUALLY</b><br />
                <textarea name="item[consignment_desc]" rows="4" cols="" onkeyup="textareaAutoSize(this,4,4,25);"><?php hecho($item['consignment_desc'])?></textarea>
            </td>
        </tr>
        <tr>
            <th>Condition (<small><i><a href="options.php?action=list&amp;option_name=condition" target="_blank">Add</a></i></small>) :</th>
            <td><select name="item[condition]">
                <option value="0">Not Selected</option>
                <?=formatHTMLOptions(productGetOptionList('condition'), $item['condition'])?>
            </select></td>
        </tr>
       <tr>
            <th>Product Type :</th>
            <td><select name="item[product_type_con]">
                <option value="">Not Selected</option>
                <?=formatHTMLOptions(['Original Product'=>'Original Product','Reproduction'=>'Reproduction'], $item['product_type_con'])?>
            </select></td>
        </tr> 
        <tr>
            <th>Warranty (<small><i><a href="options.php?action=list&amp;option_name=warranty" target="_blank">Add</a></i></small>) :</th>
            <td><select name="item[warranty]">
                <option value="0">Not Selected</option>
                <?=formatHTMLOptions(productGetOptionList('warranty'), $item['warranty'])?>
            </select></td>
        </tr>
        <?php if($item['section']==2 || $item['section']==3){ ?>
        <tr class="section-2 section-3">
            <th>Coin Operated Options:</th>
            <td><select name="item[coin_operated]">
                <?=formatHTMLOptions(['y'=>'Yes','n'=>'No'], $item['coin_operated'])?>
            </select></td>
        </tr>
        <?php } ?>

        <tr>
            <th>Year Product Released :</th>
            <td><select class="wide" name="item[year_released]">
                <option value="0"></option>
                <?=formatHTMLOptions(productGetOptionList('year_released'), $item['year_released'])?>
            </select></td>
        </tr>
        <!-- <tr class="section-2 section-1 section-4">
            <th>Product Approvals :</th>
            <td>
                <input type="checkbox" value="y" name="item[ce]"<?=('y' == $item['ce']) ? ' checked="checked"' : ''?>/> CE
                <input type="checkbox" value="y" name="item[ctick]"<?=('y' == $item['ctick']) ? ' checked="checked"' : ''?>/> Ctick
                <input type="checkbox" value="y" name="item[ul]"<?=('y' == $item['ul']) ? ' checked="checked"' : ''?>/> UL
                <input type="checkbox" value="y" name="item[rohs]"<?=('y' == $item['rohs']) ? ' checked="checked"' : ''?>/> ROHS
            </td>
        </tr> -->
        <tr>
            <th>Product Picture :</th>
            <td>
                <div id="productPictureRemote">
                    <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
                    <input style="width:272px;" name="item_image" type="file" accept="image/*" />
                    <!-- <input type="button" value="Link Thumbnail" class="unfinished" onclick="window.open(&quot;.?action=image&amp;return_message=product_image_local&quot;);" /> -->
                </div>
                <div id="productPictureLocal" style="display:none;">
                    <input style="width:272px;" type="hidden" name="item_image_local" value="<?=('add' == $action && is_file($test = productGetOptionList('image_path') . '/' . $item['image'])) ? hecho($test, 1) : ''?>" />
                    <span id="product_image_note"></span>
                    <!-- <input type="button" value="Link Thumbnail" onclick="window.open(&quot;.?action=image&amp;return_message=product_image_local&quot;);" /> -->
                </div>
                <div id="sectionItemImage">
                    <a href="<?php hecho(array_key_exists('image_url', $item) ? $item['image_url'] : '#')?>" target="_blank" id="item_image_a"><img id="item_image_img" src="<?php hecho((array_key_exists('image_url', $item)) ? $item['image_url'] : '/_.gif')?>" style="vertical-align:middle;max-width:96px;max-height:96px;" /></a>
<?php
if (array_key_exists('image_url', $item)) {
?>
                    <br />
                    <small style="color:#f00;"><i>NB: Uploading new image will overwrite existing image</i></small>
<?php
}
?>
                </div>
            </td>
        </tr>
        <tr>
            <th>Product Thumbnail :</th>
            <td>
                <div>
                    <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
                    <input name="item_thumb" type="file" accept="image/*" />
                </div>
                <div>
                    <a href="<?php hecho((array_key_exists('thumb_url', $item)) ? $item['thumb_url'] : '#')?>" target="_blank" id="item_thumb_a"><img id="item_thumb_img" src="<?php hecho((array_key_exists('thumb_url', $item)) ? $item['thumb_url'] : '/_.gif')?>" style="vertical-align:middle;max-width:96px;max-height:96px;" /></a>
<?php
if (array_key_exists('thumb_url', $item)) {
?>
                    <br />
                    <small style="color:#f00;"><i>NB: Uploading new image will overwrite existing image</i></small>
<?php
}
?>
                </div>
            </td>
        </tr>
        <tr>
            <th style="padding-top: 20px;padding-bottom: 10px;">Stock Type :</th>
            <td style="padding-top: 20px;padding-bottom: 10px;">
                <input type="radio" name="item[stock_type1]" value="stock_for_sale"<?=('stock_for_sale' == $item['stock_type']) ? ' checked="checked"' : ''?> /> Stock for Sale
                <input type="radio" name="item[stock_type1]" value="consignment"<?=('consignment' == $item['stock_type']) ? ' checked="checked"' : ''?> /> Consignment
                <input type="radio" name="item[stock_type1]" value="stock_sample"<?=('stock_sample' == $item['stock_type']) ? ' checked="checked"' : ''?> /> Stock Sample
                <input type="radio" name="item[stock_type1]" value="repair_item"<?=('repair_item' == $item['stock_type']) ? ' checked="checked"' : ''?> /> Repair
            </td>
        </tr>
    </table>
    <br /><br />
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionItem_hideNote">
    Product item can be viewed by Clicking "Product Item" above<br />
</div>

<!-- /_product_item.tpl -->

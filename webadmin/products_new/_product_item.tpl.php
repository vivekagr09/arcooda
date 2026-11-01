<!-- _product_item.tpl -->
<style type="text/css">
.expend-category-parent{
    text-align: left!important;
    background: #e8e8f1;
    padding-left: 20px!important;
}
.expend-category-child{
    text-align: left!important;
    padding-left: 30px!important;
}
</style>
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
<style>
    #display_comment_description {
        width: 95%;
        height: 68px !important;
    }
    #cboxOverlay {
	    background: #333;
	}

	#cboxOverlay {
	    position: fixed;
	    width: 100%;
	    height: 100%;
	}
	#cboxLoadedContent {
	    background: #fff;
	    color: #000;
	    padding: 8px;
	}
	#cboxContent {
	    margin-top: 32px;
	    overflow: visible;
	}
	#cboxMiddleLeft, #cboxBottomLeft {
	    clear: left;
	}
	#cboxWrapper {
	    position: absolute;
	    top: 0;
	    left: 0;
	    z-index: 9999;
	    overflow: hidden;
	}
	#cboxPrevious, #cboxNext, #cboxSlideshow, #cboxClose {
	    border: 0;
	    padding: 0;
	    margin: 0;
	    overflow: visible;
	    text-indent: -9999px;
	    width: 20px;
	    height: 20px;
	    position: absolute;
	    top: 11px;
	    background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAH0AAAAyCAMAAABGUCSuAAAAS1BMVEW/v7+fn5/v7+////+Hh4cPDw8/Pz9/f3/f39+AgIAAAADPz8+Xl5e3t7dvb2+vr6/n5+f39/cvLy+np6ePj4/Hx8dfX1/X19dPT08As0YMAAABqklEQVR4Xu3V2WrsMAwAUC3enX2W9v+/9HrChNKxzERQeh9qPSRgER0UIgX4f0bXu36TgduqA8IiU4QIfEMkSV+HKANxEPh1MA9kMXUSYhb1ECMyxhgEfTGxpccC1UQ53R8CITUq9VzwiIiX78iMiCVRdbO7Jj8ui6DHy6rRIR7x0vcRo8THGj9qDXReH2NLf8cvLOvR0Gkdfl4fzuuc3+lZ+eZx5bP6UWwEgJdKAONuSLiB5lc3KyeOhvbEDaScuMxKnde5oYvjs5jnthF6N4G1envTypuL6LhVCrEYBPDBHwDU/3Fd/+3oete73vWrDFxJWTLLVHDO89W5IOm0JRlIm8DTZh9ItnXSJxR1SMmxSwkEPdvU0lOBaqKc7g95ITUpdSx4cs59fkfu5agkqm521+LjkgU9fZJG9+mIl76PmCQ+1fhRawvn9Sm19Hd8ZllPNpzW/c/r23md8Z2OyjfviM/qR7HJe/9SyftpNyTc+uZXd1dOXNjaE7cF5cQhK3Wme0MXxyfb57YRerfAWr29aVE8DeG4VUpgMYL3M8/eh7/4j+t617v+DxheWmCszl0eAAAAAElFTkSuQmCC) no-repeat 0 0;
	}
	#cboxClose {
	    background-position: -50px 0px;
	    right: 15px;
	}
	#cboxClose:hover {
	    background-position: -50px -25px;
	}
	#colorbox, #cboxOverlay, #cboxWrapper {
	    top: 0;
	    left: 0;
	    z-index: 9999;
	    overflow: hidden;
	}
    .table_grid th, .table_grid td {
        border: 1px solid #ccc;
        padding: 4px;
        text-align: center;
    }
</style>
<?php
/**
 * @param int|string $count
 * @param mixed[]    $item
 *
 * @return string|false
 */
function product_gift_row($count = "[ROW_COUNT]", $item = [])
{
    $row_name = "product_gift[" . $count . "]";
    $hidden = [
        $row_name . "[id]" => $item["id"] ?? "",
        $row_name . "[product_id]" => $item["product_id"] ?? "",
        $row_name . "[color_id]" => $item["color_id"] ?? "",
        $row_name . "[sub_id]" => $item["sub_id"] ?? "",
        $row_name . "[currency]" => $item["currency"] ?? "",
        $row_name . "[precision]" => $item["precision"] ?? "",
        $row_name . "[generic_code]" => $item["generic_code"] ?? "",
    ];
    ob_start();
    ?>
<tr id="product_gift_rowa[<?= $count ?>]" class="promotion_gift_row">
	<td style="white-space:nowrap;">
		<?= str_replace("\n", "", formatHTMLInputs($hidden)) ?>
		<input name="<?= $row_name ?>[linked_id]" type="text" value="<?= htmlspecialchars($item["linked_id"] ?? "") ?>" size="5" maxlength="8" />
	</td>
	<td><input name="<?= $row_name ?>[name]" type="text" value="<?= htmlspecialchars($item["name"] ?? "") ?>" style="width:96%;" /></td>
	<td><input name="<?= $row_name ?>[qty]" type="text" min="1" step="1" value="<?= htmlspecialchars($item["qty"] ?? "") ?>" size="3" /></td>
	<td><input name="<?= $row_name ?>[cost]" type="text" min="0.00" step="0.01" value="<?= htmlspecialchars(round($item["cost"], 2) ?? "") ?>" size="6" readonly style="border: none;text-align: center;"/></td>
	<td><input name="<?= $row_name ?>[online]" type="text" min="0.00" step="0.01" value="<?= htmlspecialchars(round($item["online"], 2) ?? "") ?>" size="6" readonly style="border: none;width: 96%;text-align: center;"/></td>
	<td><input name="<?= $row_name ?>[price]" type="text" min="0.00" step="0.01" value="<?= htmlspecialchars(round($item["price"], 2) ?? "") ?>" size="6" /></td>
	<td style="text-align:center;vertical-align:middle;"><input name="<?= $row_name ?>[generic]" type="checkbox" value="<?= "y" == $item["generic"] ? "n" : $item["generic"] ?>"<?= "y" == $item["generic"] ? "" : ' checked="checked"' ?> /></td>
	<td style="vertical-align:middle;"><a href="#" onclick="promotionDeleteGift(<?= $count ?>);return false"><i class="bi bi-trash">Delete</i></a></td>
</tr>
<?php return ob_get_clean();
}  ?>
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
        $('#pre_order_close_date').show();
        $('.bonusDisplay').show();
        $('select[name="item[discontinued]"]').val('n').trigger('change');
       // $('#discontinued-container').hide();
    }else if($(this).val() == 427 ){
        $('select[name="item[discontinued]"]').val('y').trigger('change');
        //$('#discontinued-container').show();
        $('#arrival_date').hide();
        $('#pre_order_close_date').hide();
        $('#deposit_needed').hide();
        $('#arrival_date').val('');
        $('.bonusDisplay').hide();
        $('#pre_order_close_date').val('');
        $('#deposit_needed').val(0.00);
         if (confirm('Please also add a recommended product for the discontinued product from the Product Status section.')) {
            const $el = $('#discontinued_tr');
            $el.attr('tabindex', '-1').focus();
            $el[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
         }
    }else{
        $('select[name="item[discontinued]"]').val('n').trigger('change');
        $('#arrival_date').hide();
        $('#pre_order_close_date').hide();
        $('#deposit_needed').hide();
        $('#arrival_date').val('');
        $('.bonusDisplay').hide();
        $('#pre_order_close_date').val('');
        $('#deposit_needed').val(0.00);
        //$('#discontinued-container').hide();
    }
 });
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionItem&quot;);return false">Product Item</a>')?>

<div id="sectionItem">
    <div style="display:none;">
		<div id="promotion_add_gift_widget" style="text-align:center;">
		<div style="border:1px solid #00f;background-color:#9cf;margin:auto;text-align:left;width:66%;padding:8px 16px;min-height:256px;">
			<input type="radio" name="promotion_add_gift_generic" id="promotion_add_gift_generic_n" value="n" />
			<label for="promotion_add_gift_generic_n">Product</label><br />
			<div class="promotion_add_gift_generic_n" style="padding:8px 16px;display:none;text-align:center;">
				<label for="promotion_add_gift_product_search">Search Product Name / ID</label> :
				<input id="promotion_add_gift_product_search" type="text" name="gift_search_for" style="width:85%;" />
				<input type="button" value="Search" onclick="$.colorbox.close();promotionSearchProduct()" />
			</div>
		</div>
		<br />
		<input type="button" id="promotion_add_gift_widget_button" style="display:none;" value="Add Gift" />
	</div></div>
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
                            <td class="<?= $type_id == $key_id ? 'expend-category-parent':'expend-category-child' ?>"><?=$indent?><span>
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
        <!-- <tr class="section-2 section-3 section-4 display-monitor"  <?= ($item['type'] == '305' ? '' : 'style="display:none;"')?> >
            <th>Monitor Type (<small><i><a href="options.php?action=list&amp;option_name=monitor_type" target="_blank">Add</a></i></small>) :</th>
            <td><select name="item[monitor_type]">
                <option value="0">Not Selected</option>
                <?=formatHTMLOptions(productGetOptionList('monitor_type'), $item['monitor_type'])?>
            </select></td>
        </tr>
         <tr class="section-2 section-3 section-4 display-monitor" <?= ($item['type'] == '305' ? '' : 'style="display:none;"')?>>
            <th>Monitor Resolution (<small><i><a href="options.php?action=list&amp;option_name=monitor_resolution" target="_blank">Add</a></i></small>) :</th>
            <td><select name="item[monitor_resolution]">
                <option value="0">Not Selected</option>
                <?=formatHTMLOptions(productGetOptionList('monitor_resolution'), $item['monitor_resolution'])?>
            </select></td>
        </tr>
         <tr class="section-2 section-3 section-4 display-monitor" <?= ($item['type'] == '305' ? '' : 'style="display:none;"')?>>
            <th>Monitor Ratio (<small><i><a href="options.php?action=list&amp;option_name=monitor_ratio" target="_blank">Add</a></i></small>) :</th>
            <td><select name="item[monitor_ratio]">
                <option value="0">Not Selected</option>
                <?=formatHTMLOptions(productGetOptionList('monitor_ratio'), $item['monitor_ratio'])?>
            </select></td>
        </tr> -->
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
                      $is_preorder = false;

                      if(isset($item['available']) && $item['available'] < 0 && $item['arrival_date'] < date('Y-m-d')){
                        $is_preorder = true;
                      }
                ?>
                <select name="item[available]" class="arvivalchnage"  <?= ($is_preorder) ? 'style="background-color:pink"':"" ?>>
                <option value="0">Not Selected</option>
                <?=formatHTMLOptions($pre_order, $item['available'])?>
            </select></td>
        </tr>
        <tr <?=('-1' == $item['available']) ? '' : 'style="display:none"'?> id="pre_order_close_date">
            <th>Pre Order Close Date</th>
            <td>
                    <input type="date" name="item[pre_order_close_date]" value="<?php hecho((is_null($item['pre_order_close_date'])) ? '' : substr($item['pre_order_close_date'], 0, 10))?>" <?= ($is_preorder) ? 'style="background-color:pink"':"" ?>/>
                        <button onclick="Product.set(&quot;pre_order_close_date&quot;, &quot;&quot;);return false"><i class="fas fa-trash-alt"></i></button>
            </td>
        </tr>
        <tr <?=('-1' == $item['available']) ? '' : 'style="display:none"'?> id="arrival_date">
            <th>Arrival Date</th>
            <td>
                    <input type="date" name="item[arrival_date]" value="<?php hecho((is_null($item['arrival_date'])) ? '' : substr($item['arrival_date'], 0, 10))?>" <?= ($is_preorder) ? 'style="background-color:pink"':"" ?>/>
                        <button onclick="Product.set(&quot;arrival_date&quot;, &quot;&quot;);return false"><i class="fas fa-trash-alt"></i></button>
            </td>
        </tr>
        <tr <?=('-1' == $item['available']) ? '' : 'style="display:none"'?> id="deposit_needed">
            <th>Deposit Needed</th>
            <td>
                   <input type="number" name="item[deposit_needed]" value="<?= $item['deposit_needed'] ?>">
                   <span style="font-size: 16px;">
                   <input type="checkbox" name="item[full_payment]" value="y" <?= ($item['full_payment']=='y')?'checked':''; ?>>
                   Full Payment
                   <input type="checkbox" class="valueUpdate" name="item[bonus]" value="y" <?= ($item['bonus']=='y')?'checked':''; ?>>
                   Bonus
               </span>
             </td>
        </tr>
        <tr <?=('y' == $item['bonus'] && '-1' == $item['available']) ? '' : 'style="display:none"'?> class="bonusDisplay">
            <th> (<small><i><a href="#" id="promotion_add_gift_button">Add</a></i></small>)  Free Gifts<input type="checkbox" class="valueUpdate" name="item[free_gifts]" value="y" <?= ($item['free_gifts']=='y')?'checked':''; ?>>
            </th>
            <td>
              <div class="paneldata" <?=('y' == $item['free_gifts']) ? '' : 'style="display:none"'?> id="free_gift_table">  
                    <table class="table_grid" id="promotion_gift_table" style="width: 100%;margin-bottom: 10px;">
                        <tr>
                            <th style="width:5%">PID</th>
                            <th style="width:30%">Product Name</th>
                            <th style="width:10%">Qty.</th>
                            <th style="width:10%">Cost Price (USD)</th>
                            <th style="width:15%">Online Price (USD)</th>
                            <th style="width:10%">Offer Price (USD)</th>
                            <th style="width:5%">Link Stock</th>
                            <th style="width:5%"></th>
                        </tr>
                        <?php
                        $count = 1;
                        $gift_sum = 0;
                        $gift_currency = '';
                        if(isset($product_gift)) {
                            foreach ($product_gift as $gift) {
                                switch ($gift["generic"]) {
                                    case "n":
                                        if (
                                            $linked = productFromDatabase(
                                                productStrJoin($gift["linked_id"], $gift["linked_sub_id"])
                                            )
                                        ) {
                                            $gift["precision"] = "y" == $linked["decimal_points"] ? 4 : 2;
                                        }
                                        break;

                                    case "b":
                                        if (
                                            function_exists("bulkFromDatabase") &&
                                            ($linked = bulkFromDatabase($gift["linked_id"]))
                                        ) {
                                            $gift["cost"] = $linked["cost"] ?? $gift["cost"];
                                            $gift["online"] = $linked["online"] ?? $gift["online"];
                                            $gift["price"] = $linked["price"] ?? $gift["price"];
                                            $gift["currency"] = $linked["currency"] ?? $gift["currency"];
                                        }
                                        break;
                                }

                                $gift["price"] = bcadd($gift["price"], "0.0000", $gift["precision"]);
                                $gift_sum      = bcadd(bcmul($gift["online"],$gift["qty"],2),$gift_sum,2);
                                $gift_currency = $gift["currency"];
                                echo product_gift_row($count++, $gift);
                            }
                        }
                        ?>
                    </table>
                    <input type="hidden" name="gift_sum" id="gift_sum" value="<?= $gift_sum ?>">
                    <input type="hidden" name="gift_currency" id="gift_currency" value="<?= $gift_currency ?>">
                    <input type="hidden" name="product_gift_deleted" value="" />
                </div>
            </td>
        </tr>

        <tr <?=('y' == $item['bonus'] && '-1' == $item['available']) ? '' : 'style="display:none"'?> class="bonusDisplay">
            <th>Free Freight <input type="checkbox" class="valueUpdate" name="item[free_freight]" value="y" <?= ($item['free_freight']=='y')?'checked':''; ?>></th>
            <td></td>
        </tr>
        <tr <?=('y' == $item['bonus'] && '-1' == $item['available']) ? '' : 'style="display:none"'?> class="bonusDisplay">
            <th>Discount <input type="checkbox" class="valueUpdate" name="item[bonus_discount]" value="y" <?= ($item['bonus_discount']=='y')?'checked':''; ?>> 
            </th>
            <td>
                <div id="bonus_discount_amount" <?=('y' == $item['bonus_discount']) ? '' : 'style="display:none"'?>>
                    <input class="wide" type ="number" name="item[bonus_discount_amount]" value="<?= $item['bonus_discount_amount']?>" style="width: 25%;">
                </div>
            </td>
        </tr>
        <tr <?=('y' == $item['bonus'] && '-1' == $item['available']) ? '' : 'style="display:none"'?> class="bonusDisplay">
            <th>Comments <input type="checkbox" class="valueUpdate" name="item[display_comment]" value="y" <?= ($item['display_comment']=='y')?'checked':''; ?> style="vertical-align:top;"></th>
            <td>
                <div id="display_comment_description_div" <?=('y' == $item['display_comment']) ? '' : 'style="display:none"'?>>
                <textarea name="item[display_comment_description]" id="display_comment_description" rows="4" onkeyup="textareaAutoSize(this,4,4,25);"><?php hecho($item['display_comment_description'])?></textarea>
                </div>
            </td>
        </tr>
        <?php
            //include "discontinued.tpl.php";
        ?>
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
                    <a href="<?php hecho(array_key_exists('image_url', $item) ? $item['image_url'] : '#')?>" target="_blank" id="item_image_a"><img id="item_image_img" src="<?php hecho((array_key_exists('image_url', $item)) ? $item['image_url'] : '/_.gif')?>" style="vertical-align:middle;max-width:96px;max-height:96px;" /></a> <a target="_blank" href="<?php hecho((array_key_exists('image_url', $item)) ? $item['image_url'] : '#')?>?original=true">Original Image</a>
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
                    <a href="<?php hecho((array_key_exists('thumb_url', $item)) ? $item['thumb_url'] : '#')?>" target="_blank" id="item_thumb_a"><img id="item_thumb_img" src="<?php hecho((array_key_exists('thumb_url', $item)) ? $item['thumb_url'] : '/_.gif')?>" style="vertical-align:middle;max-width:96px;max-height:96px;" /></a> <a target="_blank" href="<?php hecho((array_key_exists('thumb_url', $item)) ? $item['thumb_url'] : '#')?>?original=true">Original Image</a>
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
                <input type="radio" name="item[stock_type1]" class="stockist1" value="stock_for_sale"<?=('stock_for_sale' == $item['stock_type']) ? ' checked="checked"' : ''?> /> Stock for Sale
                <input type="radio" name="item[stock_type1]" value="consignment"<?=('consignment' == $item['stock_type']) ? ' checked="checked"' : ''?> /> Consignment
                <input type="radio" name="item[stock_type1]" value="stock_sample"<?=('stock_sample' == $item['stock_type']) ? ' checked="checked"' : ''?> /> Stock Sample
                <input type="radio" name="item[stock_type1]" value="repair_item"<?=('repair_item' == $item['stock_type']) ? ' checked="checked"' : ''?> /> Repair
            </td>
        </tr>
          <tr>
            <th style="padding-top: 20px;padding-bottom: 10px;">Factory Seconds :</th>
            <td style="padding-top: 20px;padding-bottom: 10px;">
                <select class="wide" name="item[factory_second]">
                 <?=formatHTMLOptions(['no'=>'No','yes'=>'Yes'], $item['factory_second'])?>
                </select>
            </td>
        </tr>
        <tr style="<?= $item['factory_second'] == 'yes'?'':'display:none;'?>" class="display-factory-used">
            <th>Factory Seconds Description:</th>
                <td><textarea name="item[factory_second_description]" id="item[factory_second_description]" cols="75" rows="7" oninput="adjustTextareaHeight(this);" style="min-height: 50px;"><?= $item['factory_second_description'] ?></textarea></td>
        </tr>
        <tr style="<?= $item['factory_second'] == 'yes'?'':'display:none;'?>" class="display-factory-used"><td class="left_title">&nbsp;</td><td>
            <input style="width:96px;" type="button" value="Insert Link" onclick="generic_insert('item[factory_second_description]','a',' href=&quot;https://www.WEBSITE.com/&quot; target=&quot;_blank&quot;');" />
            <input style="width:96px;font-weight:bold;" type="button" value="Bold" onclick="generic_insert('item[factory_second_description]', 'b');" />
            <input style="width:96px;font-style:italic;" type="button" value="Italic" onclick="generic_insert('item[factory_second_description]', 'i');" />
            <input style="width:96px;text-decoration:underline;" type="button" value="Underline" onclick="generic_insert('item[factory_second_description]', 'u');" />
            <input style="width:96px;" type="button" value="Bullet Point" onclick="generic_insert('item[factory_second_description]','li');" />
            <input type="button" value="YouTube" onclick="javascript:insertYouTubeIframe('item[factory_second_description]');" />
            <span>
                    Text Color:
                    <input type="color" id="textColorPicker" name="data1[text_color]" value="#000000" 
                           onmousedown="saveSelection('item[factory_second_description]');" 
                           onchange="applyTextColorWithPicker('item[factory_second_description]','textColorPicker');">
                </span>
        </td></tr>
    </table>
    <br /><br />


 

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionItem_hideNote">
    Product item can be viewed by Clicking "Product Item" above<br />
</div>
<script type="text/javascript">
    
     $(function() {
             const syncRadios = (sourceName, targetName) => {
                const sourceRadios = $(`input[name="${sourceName}"]`);
                const targetRadios = $(`input[name="${targetName}"]`);

                let selectedValue = null;
                sourceRadios.each(function() {
                    if ($(this).is(':checked')) {
                        selectedValue = $(this).val();
                    }
                });

                targetRadios.each(function() {
                    if ($(this).val() === selectedValue) {
                        $(this).prop('checked', true);
                    }
                });
            };

            $('input[name="item[stock_type1]"]').on('change', function() {
                if (this.value === 'consignment' && <?= $item['qty'] ?> != 0) {
                    alert('Cannot proceed: Because there is still inventory, this product cannot be changed to a consignment product.');
                    $('input.stockist1[value="stock_for_sale"]').prop('checked', true);
                     $('input.stockist[value="stock_for_sale"]').prop('checked', true);
                }else{
                    syncRadios('item[stock_type1]', 'item[stock_type]');
                }
                
            });

            // $('input[name="item[stock_type]"]').on('change', function() {
                 
            //     syncRadios('item[stock_type]', 'item[stock_type1]');
            // });

            $('input[name="item[bonus]"]').on('change', function() {
                if ($('input[name="item[bonus]"]').is(':checked')) {
                    $('.bonusDisplay').show();
                } else {
                    $('.bonusDisplay').hide();
                }
            });                                
            $('input[name="item[free_gifts]"]').on('change', function() {
                if ($('input[name="item[free_gifts]"]').is(':checked')) {
                    $('#free_gift_table').show();
                } else {
                    $('#free_gift_table').hide();
                }
            });
              $('input[name="item[display_comment]"]').on('change', function() {
                if ($('input[name="item[display_comment]"]').is(':checked')) {
                    $('#display_comment_description_div').show();
                } else {
                    $('#display_comment_description_div').hide();
                    $('#display_comment_description').val('');
                }
            });
            $('input[name="item[bonus_discount]"]').on('change', function() {
                if ($('input[name="item[bonus_discount]"]').is(':checked')) {
                    $('#bonus_discount_amount').show();
                } else {
                    $('#bonus_discount_amount').hide();
                }
            });
            $('.valueUpdate').on('change', function() {
                if ($(this).is(':checked')) {
                    $(this).val('y');
                } else {
                    $(this).val('n');
                }
            });

            $("#promotion_add_gift_button").colorbox({
                href:"#promotion_add_gift_widget",
                inline:true,
                width:640,
                height:480
            }).show();

            $("#promotion_add_gift_widget_button").click(function () {
                $.colorbox.close();
            }).show();

            $("[name='promotion_add_gift_generic']").change(function () {
                $("[class^='promotion_add_gift_generic_']").stop().slideUp();
                $(".promotion_add_gift_generic_" + $(this).val()).stop().slideDown();
            });

        });
        var product_gift_count = <?=max(100, count($product_gift))?>;
        function promotionAddGift()
        {
            $("#promotion_gift_table").append(<?= strtr(
            json_encode(
                product_gift_row("[ROW_COUNT]", [
                    "currency" => "",
                    "generic" => "n",
                    "precision" => "2",
                    "price" => "0.00",
                    "qty" => "1",
                ]) ?:
                ""
            ) ?:
            "",
            [
                "[ROW_COUNT]" => '" + product_gift_count + "',
            ]
        ) ?>);
            product_gift_count++;
            $("[name='products[promotion_type][gift]']").attr("checked", "checked").change();
        }
        function promotionSearchProduct()
        {
            var name = getValue('gift_search_for');
            window.open('/webadmin/products_new/?action=list&return_message=add_gift&multiple=y&search_for=' + escape(name));
        }
        function promotionDeleteGift(row)
        {
            if (confirm('Are you sure you want to remove this gift?')) $("[id='product_gift_rowa[" + row + "]']").detach();
        }
        function setProductData2(data)
        {
            if (data['product_list'] != null)
            {
                for(var i in data['product_list'])
                {
                    var temp = data['product_list'][i];
                    temp['return_message'] = data['return_message'];
                    setProductData2(temp);
                }
                return;
            }
            switch(data['return_message'])
            {
            case 'add_gift':
                var currency = "USD";
                var row_name = "product_gift[" + product_gift_count + "]";
                promotionAddGift();
               
                $("[name='" + row_name + "[cost]']").val(convertCurrency(data["consignment_cost_price"], data["consignment_cost_currency"], currency));
                $("[name='" + row_name + "[currency]']").val(currency);
                $("[name='" + row_name + "[name]']").val(data["product_name"]);
                $("[name='" + row_name + "[online]']").val(convertCurrency(data["consignment_sales_price"], data["consignment_sales_currency"], currency));
                $("[name='" + row_name + "[linked_id]']").val(data["id"]);
                $("[name='" + row_name + "[color_id]']").val(data["color_id"]);
                $('#cboxClose').click();
                break;
            }
        }
        function setProductsAddData(data)
        {
            console.log(data);
            for(var key in data) setProductData2(data[key]);
        }


    </script>
    <script type="text/javascript">
            $('select[name="item[type]"]').on('change', function() {
                if ($(this).val() == 305) {
                    $('.display-monitor').show();
                }else{
                    $('.display-monitor').hide();
                }
            });
             $('select[name="item[factory_second]"]').on('change', function() {
                if ($(this).val() == 'yes') {
                    $('.display-factory-used').show();
                }else{
                    $('.display-factory-used').hide();
                }
            });
    </script>
<!-- /_product_item.tpl -->

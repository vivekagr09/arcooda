<!-- _product_colour_images.tpl -->

<style type="text/css">
/*<![CDATA[*/
.hover-color:hover{background-color:#fff !important}
/*]]>*/
</style>

<script type="text/javascript">
//<![CDATA[
function deleteimage(id){
    var theRow = getElement("colour_product_images[" + id + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    setValue("item_screenshots_deleted_colour", getValue("item_screenshots_deleted_colour") + "," + id);
}
//]]>
</script>

<?php
/**
 * Product Screenshots template
 *
 * Inputs:
 * - $item_screenshots
 */
$product_colour_images   = productColorImagesFromDatabase($id);
$pro_color=explode(',', $item['colour']);
if (!isset($product_colour_images) || !is_array($product_colour_images) || count($pro_color)<=1) {
    return;
}

$created_by_list = productGetOptionList('created_by');

function colour_product_images($count = '[ROW_COUNT]', $row_data = array()) {
    global $created_by_list, $_GLOBALS;

    $name_key = 'item_screenshots_colour[' . $count . ']';
    $hidden   = array(
        'id'     => $row_data['id'],
        //'file_orig' => $row_data['file_orig'],
        //'filename'  => $row_data['filename'],
        'rank'      => $row_data['rank'],
        'active'    => $row_data['active'],
    );
    ob_start();
?>
<tr name="colour_product_images[<?= $row_data['id'] ?>]">
    <td>
        <?= formatHTMLInputs($hidden, $name_key) ?>
        <a class="item_screenshots_a_<?= $count ?>" href="<?php hecho((array_key_exists('view_url', $row_data)) ? $row_data['view_url'] : '#') ?>" target="_blank">
            <img class="item_screenshots_img_<?= $count ?>" src="addGenericAttachment.php?action=view&id=<?= $row_data['id']?>" style="max-width:96px;max-height:96px;" />
        </a>
    </td>
    <td><?php echo $row_data['colour']; ?></td>
    <td><?php echo $row_data['file_orig'].'-'.$row_data['product_id']; ?></td>
    <td><input type="text" name="<?= $name_key?>[caption]" value="<?php echo $row_data['caption']; ?>"></td>
    <td><?= date(_('Y-m-d'), (is_null($row_data['created_time'])) ? time() : $row_data['created_time']) ?></td>
    <td>
<?php
    if (array_key_exists('created_by', $row_data)) {
?>
        <?php hecho($created_by_list[$row_data['created_by']]) ?>
<?php
    } else {
?>
        <?php hecho($created_by_list[$_GLOBALS['staff']['id']]) ?>
<?php
    }
?>
    </td>
    <td style="font-size:24px;">
        <a href="#" class="item_screenshots_rank_up_colour" style="display:none;color:#555;"><i class="fas fa-long-arrow-alt-up"></i></a>
        <a href="#" class="item_screenshots_rank_down_colour" style="display:none;color:#555;"><i class="fas fa-long-arrow-alt-down"></i></a>
       
         <input type="button" onclick="deleteimage(<?= $row_data['id'] ?>)" value="Delete"  style="">
        
    </td>
</tr>
<?php
    return ob_get_clean();
}

$count = 1;
?>

<script type="text/javascript">
//<![CDATA[
let ProductColourshot = {
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
            return "[name^='item_screenshots_colour['][name$='][" + field + "]']";
        }
        return "[name='item_screenshots_colour[" + row + "][" + field + "]']";
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

function item_screenshots_color_rerank() {
    var count = 1;
    $(ProductColourshot.rows()).each(function() {
        ProductColourshot.set("rank", this, count++);
    });
}

function item_screenshots_apply_colour_events(scope) {
    $(".item_screenshots_rank_up_colour", scope).click(function() {
        var $tr = $(this).closest("tr");
        var $prev = $tr.prev("tr");
        if ($prev) {
            $tr.insertBefore($prev);
        }
        item_screenshots_color_rerank();
        return false;
    }).toggle(true);

    $(".item_screenshots_rank_down_colour", scope).click(function() {
        var $tr = $(this).closest("tr");
        var $next = $tr.next("tr");
        if ($next) {
            $tr.insertAfter($next);
        }
        item_screenshots_color_rerank();
        return false;
    }).toggle(true);

    $(".item_screenshots_delete_colour", scope).click(function() {
        var $tr = $(this).closest("tr");
        if (confirm("Are you sure you want to delete the screenshot??")) {
            var row = ProductColourshot.row(this);
            setValue("item_screenshots_deleted", getValue("item_screenshots_deleted") + "," + ProductColourshot.get("id", row));
            $tr.detach();
            item_screenshots_color_rerank();
        }
        return false;
    }).toggle(true);
    $(".item_screenshots_toggle_colour", scope).click(function() {
        var row   = ProductColourshot.row(this);
        var value = ProductColourshot.get("active", row);
        ProductColourshot.set("active", row, (value == "y") ? "n" : "y");
        $(this).val((value == "y") ? "Activate" : "De-Activate");
        return false;
    }).toggle(true);


    item_screenshots_color_rerank();
}

var item_screenshots_count = <?= max(100, count($item_screenshots)) ?>;

$(function() {
    item_screenshots_apply_colour_events();

   /* $(".item_screenshots_add_actual").click(function() {
        var $tr = $(<?= strtr(jecho(colour_product_images('[ROW_COUNT]', array(
            'active' => 'y',
            'ss_type' => PRODUCT_SCREENSHOT_TYPE_ACTUAL,
        )), 1), array(
            '[ROW_COUNT]' => '" + item_screenshots_count + "',
        )) ?>);
        item_screenshots_count++;
        $("#item_screenshots_actual").append($tr);
        item_screenshots_apply_colour_events($tr);
        return false;
    }).toggle(true);

    $(".item_screenshots_add_reference").click(function() {
        var $tr = $(<?= strtr(jecho(colour_product_images('[ROW_COUNT]', array(
            'active' => 'y',
            'ss_type' => PRODUCT_SCREENSHOT_TYPE_REFERENCE,
        )), 1), array(
            '[ROW_COUNT]' => '" + item_screenshots_count + "',
        )) ?>);
        item_screenshots_count++;
        $("#item_screenshots_reference").append($tr);
        item_screenshots_apply_colour_events($tr);
        return false;
    }).toggle(true);*/
});
//]]>

</script>
<script type="text/javascript">
function opencolour(evt, cityName) {
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
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
    $('.tab .active').css({'background-color':'#fff'});
}
function AddnewImage(colour_id){
   openWindow('addGenericAttachment.php?action=add&colour_id='+colour_id+'&product_id='+<?=$id ?>, 700, 300, true);  
}
var product_image_count = 200;
function setproductcolourimage(data){
    var product_id = data.product_id;
    var id = data.id;
    var color_id = data.colour_id;
    var caption = data.caption;
    var file_orig=data.file_orig;
    var user_id = data.user_id;
    var colour = data.colour;
    var rank = 0;
    var active = 'y';
    $('#product_table_'+color_id+'').append('<tr name="colour_product_images['+id+']" id="colour_product_images['+id+']">\
    <td>\
     <input type="hidden" name="item_screenshots_colour['+id+'][id]" value="'+id+'">\
        <input type="hidden" name="item_screenshots_colour['+id+'][rank]" value="1">\
        <input type="hidden" name="item_screenshots_colour['+id+'][active]" value="y">\
        <a class="item_screenshots_a_1" href="#" target="_blank">\
            <img class="item_screenshots_img_1" src="addGenericAttachment.php?action=view&amp;id='+id+'" style="max-width:96px;max-height:96px;"></a>\
    </td>\
    <td>'+colour+'</td>\
    <td>'+file_orig+'-'+product_id+'</td>\
    <td><input type="text" name="item_screenshots_colour['+id+'][caption]"  value="'+caption+'"></td>\
    <td><?= date(_('Y-m-d'), time()) ?></td>\
    <td>Dev</td>\
    <td style="font-size:24px;">\
        <a href="#" class="item_screenshots_rank_up_colour" style="color: rgb(85, 85, 85);"><svg class="svg-inline--fa fa-long-arrow-alt-up fa-w-8" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="long-arrow-alt-up" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg=""><path fill="currentColor" d="M88 166.059V468c0 6.627 5.373 12 12 12h56c6.627 0 12-5.373 12-12V166.059h46.059c21.382 0 32.09-25.851 16.971-40.971l-86.059-86.059c-9.373-9.373-24.569-9.373-33.941 0l-86.059 86.059c-15.119 15.119-4.411 40.971 16.971 40.971H88z"></path></svg><!-- <i class="fas fa-long-arrow-alt-up"></i> Font Awesome fontawesome.com --></a>\
        <a href="#" class="item_screenshots_rank_down_colour" style="color: rgb(85, 85, 85);"><svg class="svg-inline--fa fa-long-arrow-alt-down fa-w-8" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="long-arrow-alt-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg=""><path fill="currentColor" d="M168 345.941V44c0-6.627-5.373-12-12-12h-56c-6.627 0-12 5.373-12 12v301.941H41.941c-21.382 0-32.09 25.851-16.971 40.971l86.059 86.059c9.373 9.373 24.569 9.373 33.941 0l86.059-86.059c15.119-15.119 4.411-40.971-16.971-40.971H168z"></path></svg></a>\
            <input type="button" onclick="deleteimage('+id+')" value="Delete"  style="">\
    </td></tr>');
    $('#no_image_'+colour).hide()
    product_image_count++;
}
</script>

<?=formatHTMLTitle(
    '<a href="#" onclick="toggleSection(&quot;productcolourimages&quot;);return false">Product Colour images</a>',
    false
)?>
<div class="tab" style="text-align: left; width: 95%; margin: 0 auto;">

<?php
foreach ($pro_color as $key => $colour_id) {
        $color_detail=getProductColourById($colour_id);
        echo '<span style="background-color: '.$color_detail["color_val"].'"><a class="tablinks hover-color" onclick="opencolour(event, `'.$color_detail["colour"].'`)" style="border: 3px solid '.$color_detail["color_val"].';padding: 13px 5px;margin-left: 2px;"><span style="color:#fff;background-color:'.$color_detail["color_val"].' ; padding: 9px;">'.$color_detail["colour"].'</span></a></span>';

}
?>
</div>
<div id="productcolourimages">
<?php
    foreach ($pro_color as $key => $colour_id) {
        $color_detail=getProductColourById($colour_id)
    ?>
     <div id="<?php echo  $color_detail["colour"]; ?>" class="tabcontent">
         <input type="button" value="Add New Image" onclick="AddnewImage(<?= $colour_id ?>);" style="padding: 5px; margin: 5px; left: 10px; margin-left: 29px;">
          <table class="table_grid" id="product_table_<?= $colour_id ?>">
            <tr><th colspan="7">Product Colour images</th></tr>
            <tr>
                <th style="background:#E3EBF7;width:20%;">Image</th>
                <th style="background:#E3EBF7;width:20%;">Colour</th>
                 <th style="background:#E3EBF7;width:20%;">Image name</th>
                    <th style="background:#E3EBF7;width:20%;">Caption</th>
                <th style="background:#E3EBF7;width:20%;">Attach Date</th>
                <th style="background:#E3EBF7;width:20%;">Attached By</th>
                <th style="background:#E3EBF7;width:20%;">Action</th>
            </tr>
<?php
        if(array_key_exists($colour_id, $product_colour_images)){
?>
               
                <?php
                        foreach ($product_colour_images[$colour_id]["value"] as $screenshot) {
                             echo colour_product_images($count++, $screenshot);
                        }
                ?>
                
    <?php }else{
        echo '<tr id="no_image_'.$color_detail["colour"].'"><th colspan="6">No Images Added yet</th></tr>';
    }
    echo '</table></div>';
    }?>
    <br />

    <input type="hidden" name="item_screenshots_deleted_colour" value="" />
    <br />
    <br />
</div>
<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionProductScreenshots_hideNote">
    Product Screenshots can be viewed by Clicking "Product Screenshots" above<br />
</div>

<!-- /_product_colour_images.tpl -->

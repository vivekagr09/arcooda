<!-- _product_screenshots.tpl -->

<?php
 ini_set('display_errors', 'Off');
 error_reporting(0);
/**
 * Product Screenshots template
 *
 * Inputs:
 * - $item_screenshots
 */

if (!isset($item_screenshots) || !is_array($item_screenshots)) {
    return;
}

$created_by_list = productGetOptionList('created_by');

function item_screenshots_row($count = '[ROW_COUNT]', $row_data = array()) {
    global $created_by_list, $_GLOBALS;

    $name_key = 'item_screenshots[' . $count . ']';
    $hidden   = array(
        'ss_id'     => $row_data['ss_id'],
         'id'        => $row_data['id'],
        'ss_type'   => $row_data['ss_type'],
        'rank'      => $row_data['rank'],
        'active'    => $row_data['active'],
    );
    ob_start();
?>
<tr name="item_screenshots_row[<?= $count ?>]" <?= $row_data['active'] == 'n' ? 'style="background-color:#fcddde;"' : ''?>>
    <td>
        <?= formatHTMLInputs($hidden, $name_key) ?>
        <a class="item_screenshots_a_<?= $count ?>" href="addScreenshot.php?ss_id=<?=$row_data['ss_id'] ?>&amp;action=view" target="_blank">
            <img class="item_screenshots_img_<?= $count ?>" src="addScreenshot.php?ss_id=<?=$row_data['ss_id'] ?>&amp;action=view" style="max-width:96px;max-height:96px;" />
        </a>
<?php
    if (!array_key_exists('image_url', $row_data)) {
?>
        <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
        <input type="file" class="item_screenshots_image_file" name="<?= $name_key ?>[image]" style="max-width:96px;max-height:96px;" accept="image/*" />
<?php
    }
?>
    </td>
    <td style="text-align:left;"><input class="wide" type="text" name="<?= $name_key ?>[caption]" value="<?php hecho($row_data['caption'])?>" /></td>
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
    <td class="item_screenshots_mime_<?= $count ?>">
<?php
    if (array_key_exists('filename', $row_data) && is_file($test = productGetOptionList('screenshot_path') . $row_data['filename'])) {
        $img = getimagesize($test);
?>
        <?php hecho($img['mime']) ?>
<?php
    }
?>
    <center><input type="button" value="Update" onclick="window.open('updateScreenshot.php?action=add&id=<?= $row_data['ss_id']?>','','width=700,height=500')" style="margin-top:10px;"/></center>
    </td>
    <td class="item_screenshots_size_<?= $count ?>">
<?php
    if (array_key_exists('filename', $row_data) && is_file($test = productGetOptionList('screenshot_path') . $row_data['filename'])) {
?>
        <?php hecho(formatFileSize(filesize($test))) ?>
<?php
    }
?>
    </td>
    <td style="font-size:24px;">
        <a href="#" class="item_screenshots_rank_up" style="color:#555;"><i class="fas fa-long-arrow-alt-up"></i></a>
        <a href="#" class="item_screenshots_rank_down" style="color:#555;"><i class="fas fa-long-arrow-alt-down"></i></a>
        <a href="#" class="item_screenshots_toggle[<?= $count ?>]" style="display:none;color:#555;"><i class="fas fa-<?= ($row_data['active'] == 'n') ? 'eye-slash' : 'eye' ?>"></i></a>
        <input type="button" name="item_screenshots_delete[<?=$count?>]" class="item_screenshots_delete" deleteid="<?= $row_data['ss_id']?>" style="color:#555" value="delete" />
        <input type="button" name="item_screenshots_toggle[<?=$count?>]" value="<?= ($row_data['active'] == 'n') ? 'Activate' : 'De-Activate' ?>" class="item_screenshots_toggle"  />
    </td>
</tr>
<?php
    return ob_get_clean();
}

$count = 1;
?>

<script type="text/javascript">
//<![CDATA[
let ProductScreenshot = {
    row: function (elem) {
        return $(elem).attr("name").split("[")[1].split("]")[0];
    },
    rows: function () {
        var result = [];
        var self = this;
        $(this.selector("ss_id")).each(function() {
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
            return "[name^='item_screenshots['][name$='][" + field + "]']";
        }
        return "[name='item_screenshots[" + row + "][" + field + "]']";
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

function item_screenshots_rerank() {
    var count = 1;
    $(ProductScreenshot.rows()).each(function() {
        ProductScreenshot.set("rank", this, count++);
    });
}
function getValue(elemName)
{
    var theElem=getElement(elemName);
    if(theElem==null) return '';
    if(theElem.value) return theElem.value;
    return '';
}
function item_screenshots_apply_events(scope) {
    $(".item_screenshots_rank_up", scope).click(function() {
        var $tr = $(this).closest("tr");
        var $prev = $tr.prev("tr");
        if ($prev) {
            $tr.insertBefore($prev);
        }
        item_screenshots_rerank();
        return false;
    }).toggle(true);

    $(".item_screenshots_rank_down", scope).click(function() {
        var $tr = $(this).closest("tr");
        var $next = $tr.next("tr");
        if ($next) {
            $tr.insertAfter($next);
        }
        item_screenshots_rerank();
        return false;
    }).toggle(true);

    $(".item_screenshots_delete", scope).click(function() {
        var $tr = $(this).closest("tr");
        if (confirm("Are you sure you want to delete the screenshot??")) {
            var dataid = $(this).attr("deleteid");
            if(dataid){
            setValue("item_screenshots_deleted_val", getValue("item_screenshots_deleted_val") + "," + dataid);
            }
            $tr.detach();
            item_screenshots_rerank();
        }
        return false;
    }).toggle(true);

    $(".item_screenshots_toggle", scope).click(function() {
        var row   = ProductScreenshot.row(this);
        var value = ProductScreenshot.get("active", row);
        ProductScreenshot.set("active", row, (value == "y") ? "n" : "y");
        $(this).val((value == "y") ? "Activate" : "De-Activate");
        return false;
    }).toggle(true);

    $(".item_screenshots_image_file", scope).change(function() {
        var row = ProductScreenshot.row(this);
        var file = this.files[0];
        var reader = new FileReader();
        reader.onload = function (e) {
            $(".item_screenshots_img_" + row).attr("src", e.target.result);
            $(".item_screenshots_a_" + row).attr("href", e.target.result);
            $(".item_screenshots_mime_" + row).text(file.type);
            $(".item_screenshots_size_" + row).text(file.size);
            $(ProductScreenshot.selector("image", row)).toggle(false);
        };
        reader.readAsDataURL(file);
    });

    item_screenshots_rerank();
}

var item_screenshots_count = <?= max(100, count($item_screenshots)) ?>;

$(function() {
    item_screenshots_apply_events();

    $(".item_screenshots_add_actual").click(function() {
        var $tr = $(<?= strtr(jecho(item_screenshots_row('[ROW_COUNT]', array(
            'active' => 'y',
            'ss_type' => PRODUCT_SCREENSHOT_TYPE_ACTUAL,
        )), 1), array(
            '[ROW_COUNT]' => '" + item_screenshots_count + "',
        )) ?>);
        item_screenshots_count++;
        $("#item_screenshots_actual").append($tr);
        item_screenshots_apply_events($tr);
        return false;
    }).toggle(true);

    $(".item_screenshots_add_reference").click(function() {
        var $tr = $(<?= strtr(jecho(item_screenshots_row('[ROW_COUNT]', array(
            'active' => 'y',
            'ss_type' => PRODUCT_SCREENSHOT_TYPE_REFERENCE,
        )), 1), array(
            '[ROW_COUNT]' => '" + item_screenshots_count + "',
        )) ?>);
        item_screenshots_count++;
        $("#item_screenshots_reference").append($tr);
        item_screenshots_apply_events($tr);
        return false;
    }).toggle(true);
});
//]]>
</script>

<?=formatHTMLTitle(
    '<a href="#" onclick="toggleSection(&quot;sectionProductScreenshots&quot;);return false">Product Screenshots</a>',
    '<a href="#" class="item_screenshots_add_actual">Add Actual</a> <a href="#" class="item_screenshots_add_reference">Add Reference</a><a style="margin-left:10px;"href="#" onclick="toggleSection(&quot;sectionBulkProductScreenshots&quot;);return false">Add Bulk</a>',
     false
)?>
<div id="sectionBulkProductScreenshots" style="display:none;">
    <div style="width: 95%;margin-left: auto; margin-right: auto;margin-bottom: 20px;">
        <?php include 'addBulkImage.php'; ?>
    </div>
</div>
<div id="sectionProductScreenshots">
    <table class="table_grid" id="item_screenshots_actual">
        <tr><th colspan="8">Actual Screenshots</th></tr>
        <tr>
            <th style="background:#E3EBF7;width:96px;">Image</th>
            <th style="background:#E3EBF7;">Description</th>
            <th style="background:#E3EBF7;width:80px;">Attach Date</th>
            <th style="background:#E3EBF7;width:80px;">Attached By</th>
            <th style="background:#E3EBF7;width:80px;">File Type</th>
            <th style="background:#E3EBF7;width:80px;">File Size</th>
            <th style="background:#E3EBF7;width:80px;">Action</th>
        </tr>
<?php
    foreach ($item_screenshots as $screenshot) {
        if ($screenshot['ss_type'] == PRODUCT_SCREENSHOT_TYPE_ACTUAL) {
            echo item_screenshots_row($count++, $screenshot);
        }
    }
?>
    </table>
    <br />

    <table class="table_grid" id="item_screenshots_reference">
        <tr><th colspan="8">Reference Screenshots</th></tr>
        <tr>
            <th style="background:#E3EBF7;width:96px;">Image</th>
            <th style="background:#E3EBF7;">Description</th>
            <th style="background:#E3EBF7;width:80px;">Attach Date</th>
            <th style="background:#E3EBF7;width:80px;">Attached By</th>
            <th style="background:#E3EBF7;width:80px;">File Type</th>
            <th style="background:#E3EBF7;width:80px;">File Size</th>
            <th style="background:#E3EBF7;width:80px;">Action</th>
        </tr>
<?php
    foreach ($item_screenshots as $screenshot) {
        if ($screenshot['ss_type'] == PRODUCT_SCREENSHOT_TYPE_REFERENCE) {
            echo item_screenshots_row($count++, $screenshot);
        }
    }
?>
    </table>

    <input type="hidden" name="item_screenshots_deleted_val" value="" />
    <br />
    <br />
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionProductScreenshots_hideNote">
    Product Screenshots can be viewed by Clicking "Product Screenshots" above<br />
</div>

<!-- /_product_screenshots.tpl -->

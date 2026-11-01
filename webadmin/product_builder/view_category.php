<style>
    input[type='file'] {
  color: transparent;
}
</style>
<?php 
// Common
include_once "common.inc.php";

// Builder Header
include_once "templates/builder_header.tpl.php";

// Initiate module
$pb = new ProductBuilder($DB);

// Get categories list
$categories = $pb->getAllCategoryNames();


function item_screenshots_row($count = '[ROW_COUNT]', $row_data = array()) {
    global $created_by_list, $_GLOBALS;

    $name_key = 'category[' . $count . ']';
    $hidden   = array(
        'id'        => $row_data['id']
    );
    ob_start();
?>
<tr name="category[<?= $count ?>]">
  	<?= formatHTMLInputs($hidden, $name_key) ?>
    <td ><?= $row_data['id']?></td>
    <td style="text-align:left;"><input class="wide" type="text" name="<?= $name_key ?>[name]" value="<?php hecho($row_data['name'])?>" /></td>
      <td width="150">
        <div>
        <a class="item_screenshots_a_<?= $count ?>" href="<?php hecho((array_key_exists('view_url', $row_data)) ? $row_data['view_url'] : '#') ?>" target="_blank">
            <img class="item_screenshots_img_<?= $count ?>" src="displaycategoryImage.php?name=<?=$row_data['filename'] ?>&action=view" style="max-width:96px;max-height:96px;" />

        </a>

            <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
            <input type="file" class="item_screenshots_image_file" name="<?= $name_key ?>[image]" style="max-width:96px;max-height:96px;" accept="image/*" />
        </div>
    </td>

    <td style="font-size:24px;">
        <input type="button" name="item_category_delete[<?=$count?>]" class="item_screenshots_delete" style="color:#555;" value="delete"/>
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
            return "[name^='category['][name$='][" + field + "]']";
        }
        return "[name='category[" + row + "][" + field + "]']";
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
        if (confirm("Are you sure you want to delete the Category??")) {
            var row = ProductScreenshot.row(this);
            console.log(row)
            setValue("item_category_deleted_val", getValue("item_category_deleted_val") + "," + ProductScreenshot.get("id", row));
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
            //$(ProductScreenshot.selector("image", row)).toggle(false);
        };
        reader.readAsDataURL(file);
    });
}

var item_screenshots_count = <?= max(100, count($categories)) ?>;

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
    '<a href="#" onclick="toggleSection(&quot;sectionProductScreenshots&quot;);return false">Categories</a>',
    '<a href="#" class="item_screenshots_add_actual" style="display:none;">Add</a>',
    false
)?>

<div id="sectionProductScreenshots">
	<form action="index.php?action=add_update_category" method="post" enctype="multipart/form-data">
		    <table class="table_grid" id="item_screenshots_actual">
		        <tr><th colspan="8">Category</th></tr>
		        <tr>
                    <th style="background:#E3EBF7;">ID</th>
		        	<th style="background:#E3EBF7;">Category Name</th>
		            <th style="background:#E3EBF7;width:96px;">Image</th>
		            <th style="background:#E3EBF7;width:80px;">Action</th>
		        </tr>
		<?php
		    foreach ($categories as $screenshot) {

		            echo item_screenshots_row($count++, $screenshot);
		    }
		?>
		    </table>

	    <input type="hidden" name="item_category_deleted_val" value="" />
	    <br />
	    <br />
	    <div style="text-align:center;width:95%;margin:auto;">
	    	<input type="submit" value="Submit Changes">
	    </div>
	</form>
</div>
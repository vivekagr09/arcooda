<!-- _bulk_weights.tpl -->

<?php
/**
 * Product Bulk Weights Template
 *
 * Inputs:
 * - $item_bulk_weights[]
 * - - depth
 * - - height
 * - - id
 * - - product_id
 * - - qty
 * - - weight
 * - - width
 *
 * Overwrites:
 * - $temp
 * - $temp2
 */

if (!isset($item_bulk_weights) || !is_array($item_bulk_weights)) {
    return;
}

/**
 * Bulk Weight entry template
 *
 * @param  $count
 * @param  $row_data
 * @return HTML Content of the entry
 */
function item_bulk_weights_row($count = '[ROW_COUNT]', $row_data = array())
{
    $name_key = 'item_bulk_weights[' . $count . ']';
    $hidden   = array(
        $name_key . '[id]'         => $row_data['id'],
        $name_key . '[product_id]' => $row_data['product_id'],
    );
    ob_start();
?>
<table class="table_fields" id="item_bulk_weights_rowa[<?=$count?>]">
    <tr>
        <th style="width:5%;" rowspan="6"><span name="<?=$name_key?>[qty_span]" style="border:1px solid red;color:#fff;padding:5px;"><?=$row_data['qty']?></span></th>
        <th>Bulk Carton Units (qty) :</th>
        <td>
            <?=formatHTMLInputs($hidden)?>
            <input type="number" min="1" max="99999" type="number" name="<?=$name_key?>[qty]" value="<?=$row_data['qty']?>" />
        </td>
        <th style="width:5%;display:none;" rowspan="6"><input type="button" name="<?=$name_key?>[delete]" value="Delete Bulk Weight" /></th>
    </tr>
    <tr>
        <th>Size of Bulk Carton (cm) :</th>
        <td>
            <input type="number" min="0.0" max="999.9" step="0.1" name="<?=$name_key?>[width]" value="<?=$row_data['width']?>" placeholder="Width (cms)" />
            x
            <input type="number" min="0.0" max="999.9" step="0.1" name="<?=$name_key?>[depth]" value="<?=$row_data['depth']?>" placeholder="Depth (cms)" />
            x
            <input type="number" min="0.0" max="999.9" step="0.1" name="<?=$name_key?>[height]" value="<?=$row_data['height']?>" placeholder="Height (cms)" />
        </td>
    </tr>
    <tr>
        <th>Size of Bulk Carton (inch) :</th>
        <td>
            <input type="number" min="0.00" max="99.99" step="0.01" name="<?=$name_key?>[width_in]" value="<?=bcdiv($row_data['width'], '2.54', 2)?>" placeholder="Width (inches)" />
            x
            <input type="number" min="0.00" max="99.99" step="0.01" name="<?=$name_key?>[depth_in]" value="<?=bcdiv($row_data['depth'], '2.54', 2)?>" placeholder="Depth (inches)" />
            x
            <input type="number" min="0.00" max="99.99" step="0.01" name="<?=$name_key?>[height_in]" value="<?=bcdiv($row_data['height'], '2.54', 2)?>" placeholder="Height (inches)" />
        </td>
    </tr>
    <tr>
        <th>Physical Weight (kg) :</th>
        <td>
            <input type="number" min="0.000" max="999.999" step="0.001" name="<?=$name_key?>[weight]" value="<?=$row_data['weight']?>" placeholder="Weight (lbs)" />
            <input type="number" min="0.000" max="999.999" step="0.001" name="<?=$name_key?>[weight_lb]" value="<?=bcdiv($row_data['weight'], '0.45', 3)?>" />
        </td>
    </tr>
</table>
<?php
return ob_get_clean();
}

?>
<script type="text/javascript">
//<![CDATA[
let ProductBulkWeight = {
    row: function (elem) {
        return $(elem)[0].name.split("[")[1].split("]")[0];
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
            return "[name^='item_bulk_weights['][name$='][" + field + "]']";
        }
        return "[name='item_bulk_weights[" + row + "][" + field + "]']";
    },
    get: function (field, row) {
        return $(this.selector(field, row)).val();
    },
    set: function (field, row, value) {
        var $elem = $(this.selector(field, row));
        if (value !== $elem.val()) $elem.val(value).change();
    },
    sum: function (field, scale) {
        var total = bcadd("0", "0", scale);
        $(this.selector(field)).each(function () {
            total = bcadd(total, $(this).val(), scale);
        });
        return total;
    }
};

function item_bulk_weights_events(context) {
    //qty_span = (qty) => qty;
    $(ProductBulkWeight.selector("qty"), context).change(function () {
        var row = ProductBulkWeight.row(this);
        ProductBulkWeight.set("qty_span", row, $(this).val());
    });

    $(ProductBulkWeight.selector(["width", "height", "depth"]), context).change(function () {
        var row = ProductBulkWeight.row(this);
        var field = ProductBulkWeight.field(this);
        ProductBulkWeight.set(field + "_in", row, bcdiv($(this).val(), "2.54", 2));
    });

    //width = (width_in) => width_in * 2.54;
    //depth = (depth_in) => depth_in * 2.54;
    //height = (height_in) => height_in * 2.54;
    $(ProductBulkWeight.selector(["width_in", "height_in", "depth_in"]), context).change(function () {
        //TODO: Don't bubble
        /*
        var row = ProductBulkWeight.row(this);
        var field = ProductBulkWeight.field(this);
        ProductBulkWeight.set(substr(field, 0, -3), row, bcmul($(this).val(), "2.54", 1));
        */
    });

    //weight_lb = (weight) => weight / 0.45;
    $(ProductBulkWeight.selector("weight"), context).change(function () {
        var row = ProductBulkWeight.row(this);
        ProductBulkWeight.set("weight_lb", row, bcdiv($(this).val(), "0.45", 3));
    });

    //weight = (weight_lb) => weight_lb * 0.45;
    $(ProductBulkWeight.selector("weight_lb"), context).change(function () {
        //TODO: Don't bubble
        /*
        var row = ProductBulkWeight.row(this);
        var field = ProductBulkWeight.field(this);
        ProductBulkWeight.set("weight", row, bcmul($(this).val(), "0.45", 3));
        */
    });

    //volume = (width, depth, height) => bcmul(width, depth, height, 3)
    $(ProductBulkWeight.selector(["width", "height", "depth"]), context).change(function () {
        var row = ProductBulkWeight.row(this);
        var width = ProductBulkWeight.get("width", row);
        var depth = ProductBulkWeight.get("depth", row);
        var height = ProductBulkWeight.get("height", row);
        ProductBulkWeight.set("volume", row, bcmul(bcmul(width, depth, 3), height, 3));
    });

    $(ProductBulkWeight.selector("delete"), context).click(function () {
        var row = ProductBulkWeight.row(this);
        setValue("item_bulk_weights_deleted", getValue("item_bulk_weights_deleted") + "," + ProductBulkWeight.get("id", row));
        $(this).closest("table").detach();
    }).fadeIn();
}

$(function () {
    item_bulk_weights_events();
});

var item_bulk_weights_count = <?=max(100, count($item_bulk_weights))?>;
function item_bulk_weights_add() {
    var $row = $(<?=strtr(jecho(item_bulk_weights_row('[ROW_COUNT]', array(
        '' => '',
    )), 1), array(
        '[ROW_COUNT]' => '" + item_bulk_weights_count + "',
    ))?>);
    $("#item_bulk_weights_table").append($row);
    item_bulk_weights_events($row);
    item_bulk_weights_count++;
}
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionBulkCartonWeights&quot;);return false">Bulk Carton Weights</a>', '<a href="#" onclick="item_bulk_weights_add();return false">Add New</a>')?>

<div id="sectionBulkCartonWeights">
<div id="item_bulk_weights_table">
</div>
<?php
    $temp2 = 0;
    foreach ($item_bulk_weights as $temp) {
        echo item_bulk_weights_row($temp2, $temp);
        $temp2++;
    }
    unset($temp2);
?>
    <input type="hidden" name="item_bulk_weights_deleted" value="" />

</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionBulkCartonWeights_hideNote">
    Bulk Carton Weights can be viewed by Clicking "Bulk Carton Weights" above<br />
</div>

<!-- /_bulk_weights.tpl -->
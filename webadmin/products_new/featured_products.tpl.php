<!-- featured_products.tpl -->

<?php
function featured_products_row($count = '[ROW_COUNT]', $row_data = array())
{
    static $alt = false;

    $name_key = 'item_list[' . $count . ']';
    $hidden   = array(
        $name_key . '[id]' => $row_data['id'],
    );
    ob_start();
?>
    <tr id="featured_products_rowa[<?=$count?>]"<?=(($alt = !$alt) ? ' class="alt"' : '') . (($row_data['hidden']) ? ' style="display:none;"' : '')?> onmouseover="this.style.backgroundColor='#9DD6D2';" onmouseout="this.style.backgroundColor='';" >
        <td style="white-space:nowrap;">
            <?=formatHTMLInputs($hidden)?>
            <span name="<?=$name_key?>[product_number]"><a href="/webadmin/products_new/.?action=edit&id=<?= $row_data["id"] ?>" target="_blank"><?php hecho($row_data["product_number"])?></a></span>
        </td>
        <td style="width:100%;text-align:left;">
<?php
if (array_key_exists('view_url', $row_data)) {
?>
            <a target="_blank" href="<?php hecho($row_data['view_url'])?>" onmouseover="<?php hecho("tt_in(" . jecho('<img style="max-width:96px;max-height:96px;" src="' . hecho($row_data['thumb_url'], 1) . '" alt="' . hecho($row_data['product_name'], 1) . '" />', 1))?>)" onmouseout="tt_out()">
<?php
}
?>
                <span name="<?=$name_key?>[product_name]"><?php hecho($row_data["product_name"])?></span>
<?php
if (array_key_exists('view_url', $row_data)) {
?>
            </a>
<?php
}
?>
        </td>
        <td style="white-space:nowrap;"><input type="date" name="<?=$name_key?>[featured_expiry]" value="<?php hecho((is_null($row_data['featured_expiry'])) ? '' : substr($row_data['featured_expiry'], 0, 10)) ?>" /></td>
        <td><a href="#" onclick="return featuredProductsDelete(<?=$count?>,<?=$row_data["id"] ?>)">Remove</a></td>
    </tr>
<?php
return ob_get_clean();
}
if (is_array($item_list)) {
?>
<script type="text/javascript">
//<![CDATA[
var featured_products_count = <?=count($item_list) + 256?>;
function setProductData(data) {
    if (data.product_list != null) {
        for (k in data.product_list) setProductData(data.product_list[k]);
        return;
    }
    if (data[0] != null) {
        for (i in data) setProductData(data[i]);
        return;
    }
    var row_id = featuredProductsAddRow();
    featuredProductsSetRow(row_id, data);
}
var setProductsData = setProductData;
function featuredProductsSort(column) {
    var rows = [];
    var vals = [];
    $("[id^='featured_products_rowa[']").each(function() {
        var row_id = $(this).attr("id").substr(19);
        rows.push(row_id);
        var val = $("[name='featured_products" + row_id + "[" + column + "]']").text();
        if (val == "") val = $("[name='featured_products" + row_id + "[" + column + "]']").val();
        vals.push(val);
    });
    var reverse = true;
    var bubble = true;
    while (bubble) {
        bubble = false;
        for (var i = 0; i < (rows.length - 1); i++)
            for (var j = i + 1; j < rows.length; j++)
                if (vals[i] > vals[j]) {
                    temp = rows[i];
                    rows[i] = rows[j];
                    rows[j] = temp;
                    temp = vals[i];
                    vals[i] = vals[j];
                    vals[j] = temp;
                    bubble = true;
                    reverse = false;
                }
    }
    if (reverse) rows = rows.reverse();
    var table = $("#featured_products_table");
    for (var i = 0; i < rows.length; i++) {
        table.append($("[id='featured_products_rowa" + rows[i] + "']"));
    }
    var count = 0;
    $("[id^='featured_products_rowa[']").each(function() {
        $("[name$='[rank]']", this).val(count++);
    });
    $("[id^='featured_products_rowa']").removeClass("alt").filter(":odd").addClass("alt");
    return false;
}
function featuredProductsDelete(row_id,productid) {
    if (confirm("Are you sure you want to cancel this featured product ??")) {
        $("[id='featured_products_rowa[" + row_id + "]']").remove();
        $("[id^='featured_products_rowa']").removeClass("alt").filter(":odd").addClass("alt");
        setValue("products_affected", getValue("products_affected") + "," + productid);
        $('#featured_save').click();
    }
    return false;
}
function featuredProductsAddRow() {
    var row_string = <?php jecho(featured_products_row())?>;
    row_string = row_string.replace(/\[ROW_COUNT\]/g, featured_products_count);
    $("#featured_products_table tr").eq(0).after(row_string);
    //$("#featured_products_table").append(row_string);
    var count = 0;
    $("[id^='featured_products_rowa']").each(function() {
        $("[name$='[rank]']", this).val(count++);
    });
    $("[id^='featured_products_rowa']").removeClass("alt").filter(":odd").addClass("alt");
    featured_products_count++;
    return featured_products_count - 1;
}
function featuredProductsSetRow(row_id, data) {
    var row_key = "product_list[" + row_id + "]";
    var $anchor = $("[name='" + row_key + "[product_name]']").closest("a");
    if ($anchor.length == 0) $anchor = $("[name='" + row_key + "[product_name]']").wrap('<a target="_blank"></a>').closest("a");
    $anchor.attr("href", data["view_url"]).attr("thumb_url", data["thumb_url"]);
    $anchor.unbind("hover").hover(function() {
        if ($(this).attr("thumb_url") != "") tt_in('<img style="max-width:96px;max-height:96px;" src="' + $(this).attr("thumb_url") + '" />');
    }, function() {
        tt_out();
    });
    $("[name='" + row_key + "[product_name]']").text(data["product_name"]);
    $("[name='" + row_key + "[product_number]']").text(data["product_number"]);
    $("[name='" + row_key + "[product_id]']").val(data["product_id"]);
    $("[name='" + row_key + "[featured_expiry]']").val("");
}
$(function() {
    $('.featured_products_add_button').css('display', '').click(function() {
        window.open(".?action=search2&show_sites=n&return_message=featured_products&currency=USD&multiple=y&order_by=year_released&order=desc");
        return false;
    });
});
//]]>
</script>

<?=formatHTMLTitle('Featured Products', '<a href="#" class="featured_products_add_button" style="display:none;">Add Product(s)</a>')?>

<div id="sectionFeaturedProducts">
    <table class="table_grid" id="featured_products_table">
        <tr>
            <!--
            <th></th>
            -->
            <th><a href="#" onclick="return featuredProductsSort('item_number')">ID</a></th>
            <th><a href="#" onclick="return featuredProductsSort('product_name')">Name</a></th>
            <th><a href="#" onclick="return featuredProductsSort('featured_expiry')">Expiry Date</a></th>
            <th></th>
        </tr>
<?php
    $count          = 0;
    foreach ($item_list as $item_id) {
        $item             = productFromDatabase($item_id);
        echo featured_products_row($count++, $item, true);
    }
?>
    </table>
    <input type="hidden" name="products_affected" value="" />
    <br />
</div>

<?php
}
?>
<style>
    #featured_products_table tr td {
    padding: 5px !important;
}
</style>
<!-- /featured_products.tpl -->

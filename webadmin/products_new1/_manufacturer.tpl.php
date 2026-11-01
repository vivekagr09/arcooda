<!-- _manufacturer.tpl -->

<?php
/**
 * Product Manufacture Template
 *
 * Inputs:
 * - $item
 * - - country_manufacture
 * - - factory_part_name
 * - - man_code
 * - - man_name
 * - - manufacturer_online
 * - - manufacturer
 */

if (!isset($item) || !is_array($item)) {
    return;
}

/**
 * Fetch only the spare parts manufacturers
 */
$manufacturer_list = productGetOptionList('manufacturer');
$manufacturer_active_list=productGetOptionList('manufacturer-active-list');
?>

<script type="text/javascript">
//<![CDATA[

$(function() {
    var manufacturer_acive_list=<?php echo json_encode($manufacturer_active_list);?>;
    /**
     * When any manufacturer option changes, update the factory part name
     */
    $(Product.selector(["manufacturer", "man_code", "man_name"])).change(function() {
        var mann_id=Product.get("manufacturer");
         $("#item_manufacturer_online").text(("y" == manufacturer_acive_list[mann_id]) ? "Yes" : "No");
        Product.set("factory_part_name", sprintf(
            "%s Parts %s %s",
            $(Product.selector("manufacturer") + " :selected").text(),
            Product.get("man_code"),
            Product.get("man_name")
        ));
    });

    /**
     * When manufacturer changes, update the display notice
     */
  /*  $(Product.selector("manufacturer_online")).change(function() {
        $("#item_manufacturer_online").text(("y" == $(this).val()) ? "Yes" : "No");
    }).change();*/

});
//]]>
</script>

<?=formatHTMLTitle('Manufacturer')?>

<div id="sectionManufacturer">
    <table class="table_fields">
        <tr>
            <th>Manufacturer :</th>
            <td>
                <select name="item[manufacturer]" style="width:50%;">
                    <option value="0">Not Selected</option>
                    <?=formatHTMLOptions($manufacturer_list, $item['manufacturer'])?>
                </select>
                <b>Displayed Online :</b> <span id="item_manufacturer_online"><?=('y' == $manufacturer_active_list[$item['manufacturer']]) ? 'Yes' : 'No'?></span>
            </td>
        </tr>
        <tr>
            <th>Display Online :</th>
            <td><select name="item[manufacturer_online]">
                <option value="">Not Selected</option>
                <?=formatHTMLOptions(productGetOptionList('manufacturer_online'), $item['manufacturer_online'])?>
            </select></td>
        </tr>
        <tr>
            <th>Product Code :</th>
            <td><input class="wide" type="text" name="item[man_code]" value="<?php hecho($item['man_code'])?>" /></td>
        </tr>
        <tr>
            <th>Product Name :</th>
            <td><input class="wide" type="text" name="item[man_name]" value="<?php hecho($item['man_name'])?>" /></td>
        </tr>
        <tr>
            <th>Country :</th>
            <td><select name="item[country_manufacture]">
                <option value="">Not Selected</option>
                <?=formatHTMLOptions(productGetOptionList('country_manufacture'), $item['country_manufacture'])?>
            </select></td>
        </tr>
        <tr>
            <th>Full Name Of Product :</th>
            <td><input type="text" name="item[factory_part_name]" value="<?php hecho($item['factory_part_name'])?>" style="width:98%;" /></td>
        </tr>
    </table>
    <br />
    <br />
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionManufacturer_hideNote">
    Product item can be viewed by Clicking "Product Item" above<br />
</div>

<!-- /_manufacturer.tpl -->

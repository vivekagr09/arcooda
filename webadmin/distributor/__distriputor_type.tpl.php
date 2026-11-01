<!-- status.tpl -->

<?php
lo
//?>

<script type="text/javascript">
//<![CDATA[
$(function() {
    $("[name='item[site_id]']").change(function() {
        if ($(this).val() == '3') $(this).css('background', '#0f0').css('color', '#000');
        else $(this).css('background', '#f00').css('color', '#fff');
    }).change();
})
function openCategoryPopup(categoryId) {

    var textBox = document.getElementById('category_text_' + categoryId);
    var anyText = textBox ? textBox.value : '';

    var url = "/webadmin/products_new/master_products.php";
    url += "?return_message=distributorcategory";
    url += "&section[]=" + categoryId;
    url += "&search_for=" + encodeURIComponent(anyText);

   window.open(url);
}
function add_distributordata_data(data){
   var section = data['requestionsection']; 
   var view_url = data['view_url'];
  $('#category_product_'+section).val(data['product_id']);
  $('#product_url_'+section).html('https://www.arcooda.com/webadmin/products_new/edit.php?action=edit&id='+data['product_id']);
}
function deleteCategory(k) {
    if (confirm("Are you sure you want to delete this item?")) {

        // Clear text input
        document.getElementById('category_text_' + k).value = '';

        // Clear hidden input
        document.getElementById('category_product_' + k).value = '';

        // Clear span content
        document.getElementById('product_url_' + k).innerHTML = '';

    }
}
//]]>
</script>

<?=formatHTMLTitle(
    '<a href="#" onclick="toggleSection(\'sectionDistributorType\');return false">Distributor Type</a>',
    ''//'<a style="color:black;" href=".?action=edit&amp;id=' . $id . '" target="_blank">Distributor Page Link</a>'
)?>

<div id="sectionDistributorType">
    <table class="table_fields">
        <tr>
            <th>Country :</th>
            <td><select name="item[country_location]" class="wide">
                <option value="">Not Selected</option>
                <?=formatHTMLOptions(distributorGetOptionList('country_location'), $item['country_location'])?>
            </select></td>
        </tr>
        <tr>
            <th>Continent :</th>
            <td><select name="item[continent]" class="wide">
                <option value="">Not Selected</option>
                <?=formatHTMLOptions(distributorGetOptionList('continent'), $item['continent'])?>
            </select></td>
        </tr>
        <tr>
            <th>Category :</th>
            <td style="padding-top:10px;padding-bottom:10px">
                                 <table>
<?php 
$category_selected = json_decode($item['category']) ?? [];
$categoryproductarray = ['1'=>'parts_product','2'=>'machine_product','3'=>'game_product','4'=>'merchandises_product'];

foreach (distributorGetOptionList('category') as $k => $v) { 
?>

                    <tr>
                    <td>
                        <input type="checkbox" id="item_category_<?=$k?>" name="item[category][]" value="<?=$k?>"<?=in_array($k, $category_selected) ? ' checked="checked"' : ''?> /><label for="item_category_<?=$k?>"><?=htmlspecialchars($v)?></label>
                    </td>
                    <td>
                        <input
                            type="text"
                            id="category_text_<?=$k?>"
                            name="item[category_text][<?=$k?>]"
                            value=""
                            style="width:100px;margin-right:5px;margin-left: 5px;"
                        />
                        <input
                            type="button"
                            value="browse"
                            onclick="openCategoryPopup(<?=$k?>);"
                        />
                        <input
                            type="hidden"
                            id="category_product_<?=$k?>"
                            name="item[<?= $categoryproductarray[$k]?>]"
                            value="<?= $item[$categoryproductarray[$k]]?>"
                        />
                        <span id="product_url_<?=$k?>"> 
                            <?php 
                                if($item[$categoryproductarray[$k]] > 0){
                                    echo 'https://www.arcooda.com/webadmin/products_new/edit.php?action=edit&id='.$item[$categoryproductarray[$k]];
                                }

                            ?>

                        </span>
                        <i class="fas fa-trash" 
                           style="cursor:pointer; color:red;" 
                           onclick="deleteCategory(<?=$k?>)">
                        </i>
                    </td>
                </tr>
   
<?php
}
?>
            </table>
            </td>

        </tr>
        <tr>
            <th>Type :</th>
            <td><select name="item[distributor_type]" class="wide">
                <option value="">Not Selected</option>
                <?=formatHTMLOptions(distributorGetOptionList('distributor_type'), $item['distributor_type'])?>
            </select></td>
        </tr>
    </table>
    <br />
    <br />
</div>

<!-- /__distributor_type.tpl -->

<?php $product_supported=productGetOptionList('product_supported');
if(count($product_supported)){
    $supported_array=[];
    if($item['product_supported']){
        $supported_array=explode(',', $item['product_supported']);
    }
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'product_supported\');return false">Products Supported</a>','<a href="/webadmin/product_supported/addedit.php?action=do_add" target="_blank">Add</a>')?>
<div id="product_supported">
<table class="table_fields">
   <tr>
        <th>Product Supported:</th>
        <td>
                <?php foreach($product_supported as $key => $supported) { ?>
               
               <div style="padding:3px;">
                    <input type="checkbox" name="item[product_supported][]" value="<?= $key?>" <?= (in_array($key, $supported_array))?'checked':''?>> <?= $supported?>
                </div>
            <?php  } ?>
            </ul>
        </td>
    </tr>
</table>
<br /><br />
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionStatus_hideNote">
    Product supported can be viewed by Clicking "Product supporder" above<br />
</div>
<?php } ?>



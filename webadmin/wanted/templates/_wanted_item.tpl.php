<?php
moduleLoad('member'); //memberGetOptionList
moduleLoad('product'); //productFromDatabase
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;productdetail&quot;);return false">Product Details</a>')?>

<div id="productdetail">
	<table class="table_fields">
		 <tr>
            <th style="width:25%;">Product Name :</th>
            <td><input class="wide" type="text" name="item[product_name]" value="<?php hecho($wanted['item'])?>" />
            	<input type="hidden" value="<?= $wanted['item_id']?>" name="item[id]"?>
            </td>
        </tr>
        <tr>
            <th>Category/Type :</th>
            <td>
            	<select name="item[type]"  required>
                <?php
                foreach (productGetOptionList('category_section') as $section_id => $section_name) {
                ?>
                    <?php 
                        if(($wanted['section_id'] == $section_id)){
                            $categoryName= ucfirst($section_name);  
                        }
                    ?>
                    <option value="<?=$section_id?>" <?=($wanted['section_id'] == $section_id) ? ' selected="selected"' : ''?>> <?= $section_name?></option>
                <?php
                }
               
                ?>
               
            </select>
            <!-- <div style="font-size: 14px;color:#dc3545;"><i><?= $categoryName?></i></div> -->
            </td>
        </tr>
        <tr>
        	<th>Preferred Location :</th>
        	<td>
        		<select name="item[pref_location]" class="form-control col-sm-9"  required>
	            <option value="" class="redText">Please choose country/Delivery Location</option>
	            <?=formatHTMLOptions(memberGetOptionList('country'),$wanted['pref_location'])?>
	            </select>
        	</td>
        </tr>
        <tr>
            <th style="width:25%;">Target Price :</th>
            <td>
                <input type="text" class="form-control col-sm-3" name="item[price]" placeholder="Price" style="width: 40%" value="<?= $wanted['target_price']?>">
                <select name="item[currency]" class="form-control col-sm-5" style="margin-left: 3px;width:40%">
                <?=formatHTMLOptions(productGetOptionList('sales_currency'), $wanted['target_currency'])?>
            </select>
            </td>
        </tr>
        <tr>
            <th>Qty Needed :</th>
            <td>
                 <input type="number" class="form-control col-sm-3" name="item[qty]" placeholder="Quantity" min="1" style="width:80%" value="<?= $wanted['qty_needed']?>">
            </td>
        </tr>
        <tr>
            <th>Request Duration :</th>
            <td>
                 <select name="item[request_duration]" class="form-control col-sm-3" style="width:50%">
                <?=formatHTMLOptions(['2 weeks'=>'2 weeks','4 weeks'=>'4 weeks','unlimited'=>'unlimited'], $wanted['duration_days'])?>
            </select>
            <label for="_product_name" class="col-sm5 col-form-label" style="margin-left: 3px;font-size: 10px;color:#dc3545;width:30%"><i> How long do you want us to search</i></label>
            </td>
        </tr>
         <tr>
            <th>Order Time :</th>
            <td>
                 <select name="item[order_time]" class="form-control col-sm-3" style="width:50%">
                <?=formatHTMLOptions(['immediately'=>'immediately','2 weeks'=>'2 weeks','4 weeks'=>'4 weeks','unknown'=>'Unknown'], $wanted['order_time'])?>
            </select>
            <label for="_product_name" class="col-sm5 col-form-label" style="margin-left: 3px;font-size: 10px;color:#dc3545;width:30%"><i> If located how soon will you order</i></label>
            </td>
        </tr>
        <tr>
        	<th>Customer notes :</th>
        	<td>
        		<textarea name="item[wanted_notes]"class="form-control col-sm-9" rows="8"><?= $wanted['description']?></textarea> 
        	</td>
        </tr>
        <tr>
            <?php 
            $pro_id=($wanted['online_item_id'])?$wanted['online_item_id']:$wanted['item_id'];
            $product=productFromDatabase($pro_id); 
            ?>
            <th>Image :</th>
            <td>
                <?php 

                if($product['thumb_url']){
                    echo '<a href="'.$product['thumb_url'].'" target="_blank"><img src="'.$product['thumb_url'].'" width="100px;"></a>';
                }
                if($wanted['image']){
                    echo '<a href="/images/img_wanted/'.$wanted['image'].'" target="_blank"><img src="/images/img_wanted/'.$wanted['image'].'" width="100px;"></a>';
                }
                ?> 
            </td>
        </tr>
	</table>
</div>
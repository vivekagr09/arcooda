<?php
moduleLoad('product');
?>

<script type="text/javascript">

	function online_products_add() {
	    var url = "/webadmin/products_new/master_products.php?return_message=addstock";
	    url += "&callback=linked_products_data";
	    url += "&search_for=";
	    window.open(url);
	}
	function setProductData(data){
		if(data.item_list[0]){
			setValue('item[online_item]',data.item_list[0].product_name);
			setValue('item[online_type]',data.item_list[0].type);
			setValue('item[online_item_id]',data.item_list[0].id);
		}
	}
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;onlinepricedetail&quot;);return false">Online Wanted Listing</a>','<span style="background: #080;"><a href="#" class="" onclick="online_products_add();return false">Add</a></span>')?>
<div id="onlinepricedetail">
	<table class="table_fields">
		 <tr>
            <th style="width:25%;">Product Name :</th>
            <td><input class="wide" type="text" name="item[online_item]" value="<?= ($wanted['online_item'])?$wanted['online_item']:$wanted['item']?>" />
            	<input type="hidden" value="<?= ($wanted['online_item_id'])?$wanted['online_item_id']:$wanted['item_id']?>" name="item[online_item_id]">
            </td>
        </tr>
         <tr>
            <th>Category/Type :</th>
            <td>
            	<select name="item[online_type]"  required>
            <?php
                $onlinecategoryName='';
                $selected_type=($wanted['online_type'])?$wanted['online_type']:$wanted['type'];
                foreach (productGetOptionList('category_section') as $section_id => $section_name) {
                ?>
                    <?php 
                        if(($wanted['section_id'] == $selected_type)){
                            $categoryName= ucfirst($section_name);  
                        }
                    ?>
                    <option value="<?=$section_id?>" <?=($selected_type == $section_id) ? ' selected="selected"' : ''?>> <?= $section_name?></option>
                <?php
                }
            
            ?>
            </select>
            </td>
        </tr>
         <tr>
        	<th>Preferred Location :</th>
        	<td>
        		<?php $pref_location=($wanted['online_pref_location'])?$wanted['online_pref_location']:$wanted['pref_location']?>
        		<select name="item[online_pref_location]" class="form-control col-sm-9"  required>
	            <option value="" class="redText">Please choose country/Delivery Location</option>
	            <?=formatHTMLOptions(getCountryList(),$pref_location)?>
	            </select>
        	</td>
        </tr>
         <tr>
            <th>Qty Needed :</th>
            <td>
                 <input type="number" class="form-control col-sm-3" name="item[online_qty_needed]" placeholder="Quantity" min="1" style="width:80%" value="<?= ($wanted['online_qty_needed'])?$wanted['online_qty_needed']:$wanted['qty_needed']?>">
            </td>
        </tr>
        <tr>
        	<th>Online notes :</th>
        	<td>
        		<textarea name="item[online_description]"class="form-control col-sm-9" rows="8"><?= ($wanted['online_description'])?$wanted['online_description']:$wanted['description']?></textarea> 
        	</td>
        </tr>
        <tr style="margin-top:15px;">
            <th style="width:25%;">Approved:</th>
            <td>
	             <select class="form-control col-sm-9" id="active" name="item[approved]"  onchange="this.style.backgroundColor=((this.value=='n')?'rgb(255 0 0)':'#00FF00');" style="width:90%;background:<?=('y' == $wanted['approved']) ?'#00FF00;':'rgb(255 0 0)'?>">
	                <?= formatHTMLOptions(['n'=>'No','y'=>'Yes'], $wanted['approved']) ?>
	            </select>
            </td>
        </tr>
	</table>
</div>
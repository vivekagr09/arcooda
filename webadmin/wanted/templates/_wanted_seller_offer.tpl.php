<script type="text/javascript">
	var item_seller_count = <?= count($wanted_offers)+1; ?>;
	function addSellerOffer(){
		var $table = $(<?= strtr(jecho(item_seller_row('[ROW_COUNT]', array(
        ), $wanted), 1), array(
            '[ROW_COUNT]' => '" + item_seller_count + "',
        )) ?>);
        item_seller_count++;
        console.log($table);
        $("#item_seller").append($table);
        return false;
	}

</script>
<?php
function item_seller_row($count = '[ROW_COUNT]', $row_data = array(),$wanted=array()){
	$name_key = 'item_seller[' . $count . ']';
    $hidden   = array(
        'id'           => $row_data['id'],
        'wanted_id'    => $wanted['id'],
    );
    ob_start();
?>
	<table class="table_fields supplier_class" id="item_seller_row[<?= $count ?>]" style="margin-bottom: 10px;">
		<?=formatHTMLInputs($hidden, $name_key)?>
		<tr>
	        <th ></th>
	        <td style="font-size: 15px; padding: 5px;">Offer#<?= ++$count ?></td>
	    </tr>
		<tr>
	        <th>Product Name :</th>
	        <td><input class="wide" type="text" name="<?=$name_key?>[product_name]" value="<?php hecho($wanted['item']) ?>" readonly/></td>
	    </tr>
	    <tr>
	        <th>Product Location :</th>
	        <td>
		        <select name="<?=$name_key?>[product_location]" class="wide">
	                <?=formatHTMLOptions(getCountryList(), $row_data['product_location'] )?>
	            </select>
        	</td>
	    </tr>
	    <tr>
	        <th>Quentity Offered :</th>
	        <td><input class="wide" type="text" name="<?=$name_key?>[quantity_offered]" value="<?php hecho($row_data['quantity_offered']) ?>"/></td>
	    </tr>
	    <tr>
	        <th>Sell Price to US :</th>
	        <td><input class="wide" type="text" name="<?=$name_key?>[price]" value="<?php hecho($row_data['price']) ?>"/></td>
	    </tr>
	    <tr>
	        <th>Sellers Comments :</th>
	        <td><textarea class="wide"  name="<?=$name_key?>[description]"><?php hecho($row_data['description']) ?></textarea></td>
	    </tr>
	     <tr>
	        <th>Sellers Name :</th>
	        <td><input class="wide" type="text" name="<?=$name_key?>[name]" value="<?php hecho($row_data['name']) ?>"/></td>
	    </tr>
	    <tr>
	        <th>Sellers Email Address :</th>
	        <td><input class="wide" type="text" name="<?=$name_key?>[email]" value="<?php hecho($row_data['email']) ?>"/></td>
	    </tr>
	    <tr>
	        <th>Sellers Country:</th>
	        <td>
	        	<select name="<?=$name_key?>[country]" class="wide">
	                <?=formatHTMLOptions(getCountryList(), $row_data['country'] )?>
	            </select>
	        </td>
	    </tr>
	    <tr>
	        <th>Status:</th>

	        <td><select name="<?=$name_key?>[status]" class="wide" onchange="this.style.backgroundColor=((this.value=='accepted')?'#00FF00':((this.value=='rejected')?'rgb(255 0 0)':'rgb(254 0 255)'));" style="width:90%;background:<?=('accepted' == $row_data['status']) ? '#00FF00;' : (($row_data['status']=='rejected')?'rgb(255 0 0)':'rgb(254 0 255)');?>">
	                <?=formatHTMLOptions(['under consideration'=>'Under Consideration', 'accepted'=>'Accepted','rejected'=>'Rejected'], $row_data['status'] )?>
	            </select></td>
	    </tr>
	</table>
<?php return ob_get_clean(); } ?>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;selleroffer&quot;);return false">Seller Offer</a>','<span style="background: #080;"><a  style="cursor:pointer;" onclick="addSellerOffer();return false">Add</a></span>')?>
<div id="item_seller">
<?php 
	$count=0;
	foreach($wanted_offers as $offer) {
		echo item_seller_row($count++,$offer,$wanted);
	}


?>
</div>
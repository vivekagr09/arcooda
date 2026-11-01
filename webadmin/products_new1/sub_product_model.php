<!-- sub_product_model.tpl -->

<?php 
$section_name='sub_product';
function sub_product_row($count = "[ROW_COUNT]", $row_data = array(),$item_upgradelinking)
{
    $section_name='sub_product';
    $name_key = $section_name . "[" . $count . "]";
    $hidden   = array(
        $name_key . "[id]"   => $row_data["id"],
        $name_key . "[product_id]" => $row_data["product_id"],
    );
    $option_list=($row_data['option_list'])?explode(',', $row_data['option_list']):[];
    ob_start();
?>
	<table class="table_fields" id="item_sub_product_rowa[<?= $count ?>]" style="margin-bottom: 10px;">
		<?=formatHTMLInputs($hidden)?>

					<tr>
				        <th>Name :</th>
				        <td><input class="wide" type="text" name="<?=$name_key?>[name]" value="<?php hecho($row_data['name']) ?>" />
				        </td>
				    </tr>
				    <tr>
				        <th>Description :</th>
				        <td><textarea class="wide" rows="5" name="<?=$name_key?>[description]" value="<?php hecho($row_data['description']) ?>" style="width: 99%;"><?php hecho($row_data['description']) ?></textarea> 
				        </td>
				    </tr>
				     <tr>
				        <th>Product Picture :</th>
				        <td>
				        	<div>
				        		<img src="/webadmin/uploads/product/art_work/<?= $row_data['product_image'] ?>" width="100" height="100" style="display:<?= ($row_data['product_image'])?'':'none;'?>" id="preview_product_image_<?= $count; ?>">
				        	</div>
							<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
				        	<input type="file" data-key="<?= $count ?>" data-type="product" name="<?=$name_key?>[product_file]" class="upload_image_file"/>
				        </td>
				    </tr>
				    <tr>
				        <th>Option List :</th>
				        <td>
				        	<?php 

				        	foreach($item_upgradelinking as $upgradelinking) {
				        		$prod = productFromDatabase($upgradelinking['linking_id']);
				        	echo 	'<p><input type="checkbox" name="'.$name_key.'[option_list][]" value="'.$upgradelinking['linking_id'].'" '.(in_array($upgradelinking['linking_id'],$option_list)?"checked":'').'/>'.$prod['product_name'] . " - #" . $upgradelinking['linking_id'].'</p>';
				        	}

				        	?> 
				        </td>
				    </tr>

				    <tr>
				        <th>Cost Price :</th>
				        <td><input class="wide update_sub_product_price" type="number" name="<?=$name_key?>[cost_price]" value="<?php echo round(convertCurrency($row_data['price_usd'], 'USD', $row_data['currency'], false, $item['precision']),2);  ?>" style="width:50%"  rowid="<?= $count?>"/>
				       	<select class="wide update_sub_product_price" name="<?=$name_key?>[currency]" style="width:40%"  rowid="<?= $count?>">
			                       <?=formatHTMLOptions(getCurrencyList(), ($row_data['currency'])?$row_data['currency']:'CNY')?>
			            </select>
				        </td>
				    </tr>
				    <tr>
				        <th>Cost Price :</th>
				        <td><input class="wide update_sub_product_cost_price" type="number" name="<?=$name_key?>[price_usd]" value="<?php hecho($row_data['price_usd']) ?>" style="width:50%"  readonly/> USD
				        </td>
				    </tr>
				    <tr>
				        <th>Sell Price :</th>
				        <td><input class="wide update_sub_product_sales_price" type="number" name="<?=$name_key?>[sell_price]" value="<?php hecho($row_data['sell_price']) ?>" style="width:50%" rowid="<?= $count?>" /> USD
				        </td>
				    </tr>
				    <tr>
				        <th>Make This the Default Listing:</th>
				        <td>
				       	<select style="width:50%" class="wide mark_as_default" name="<?=$name_key?>[default]">
			                       <?=formatHTMLOptions(['y'=>'Yes','n'=>'No'], ($row_data['default'])?$row_data['default']:'n')?>
			            </select>
				        </td>
				    </tr>
				    <?php if($row_data['created_at']) { ?>
				    	<tr>
				        <th>Created At :</th>
				        <td> <?= date('d/m/Y',strtotime($row_data['created_at']))?>
				        </td>
				    </tr>
				    <?php } ?>
				    <tr>
				        <th colspan="2"> <input type="button" value="Delete" class="item_sub_product_delete" data-id="<?= $row_data['id']?>" data-count="<?= $count?>"style="float: right; margin-right: 5px;" /></th>
				        
				    </tr>
				</table>

<?php
return ob_get_clean();
}

$randumnumber=rand();
?>
<script type="text/javascript">
	$('body').on('change', '.update_sub_product_price', function(){
        var rowid=$(this).attr('rowid'); 
        var fromElem='sub_product['+rowid+'][cost_price]';
       	var toElem='sub_product['+rowid+'][price_usd]';
        var from=getValue('sub_product['+rowid+'][currency]');
        var to='USD';
        setValue(toElem, toCurrency(getValue(fromElem), from, to));
    });
    $('body').on('change','.mark_as_default',function(){
		var selectbox=document.getElementsByClassName('mark_as_default');
		for(var i=0;i<selectbox.length;i++)
		 {
		 	if($(this).val()=='y'){
				selectbox[i].value='n';
				$(this).val('y');
		 	}
			
		}
	})

   /* $('body').on('change', '.update_sub_product_sales_price', function(){
    	var art_sales=0;
        $(".update_sub_product_sales_price").each(function () {
			art_sales=art_sales+parseFloat($(this).val());
		});
        setValue('item[sales_price]',art_sales.toFixed(2));
        $("[name='item[sales_price]']").trigger("change");
    });*/
	 $(document).on('click', '.item_sub_product_delete', function() {
        var $table = $(this).closest("table");
        if (confirm("Are you sure you want to delete this sub product?")) {
           //var row = ProductSupplier.row(this);
            setValue("item_sub_product_id_deleted", getValue("item_sub_product_id_deleted") + "," + $(this).attr('data-id'));
            var theTable=getElement('item_sub_product_rowa['+ $(this).attr('data-count')+']');
            if(theTable) theTable.parentNode.removeChild(theTable);
            //$table.detach();
        }
        return false;
    }).toggle(true);
	var currentsubproduct=0
	var sub_product_count="<?= count($sub_product)+1;?>";
	function addsub_product(){
		var row_string = <?=json_encode(sub_product_row('[ART_NUMBER_COUNT]', ['product_id'=>$item['id'],'serial_id'=> '[SERIAL_ID]','id'=> '[ROW_ID]'],$item_upgradelinking))?>;
	    	row_string = row_string.replace(/\[ART_NUMBER_COUNT\]/g, sub_product_count);
	    $("#sub_product_number").append(row_string);	
	    sub_product_count++;
	}
	function item_sub_product_delete(id,dataid) {
	    if (confirm("Are you sure you want to delete  this serial no ??")) {
	        var theRow = getElement("item_sub_product_rowa["+id+"]");
	        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	        if(dataid>0)
	        setValue("item_sub_product_id_deleted", getValue("item_sereal_id_deleted") + "," + dataid);
	    }
	}
	$(document).on('change', '.upload_image_file', function() {
        var row = $(this).attr('data-key');
        var type = $(this).attr('data-type');
        var file = this.files[0];
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#preview_"+type+"_image_" + row).attr("src", e.target.result);
            $("#preview_"+type+"_image_" + row).show();
        };
        reader.readAsDataURL(file);
    });
</script>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sub_product&quot;);return false">Sub product Module</a>','<a stylr="cursor:pointer;" onclick="addsub_product();">Add</a>')?>

<div id="sub_product_number">

<?php
$count    = 0;
    $affected = array();

    foreach ($sub_product as $data) {

        echo sub_product_row($count++, $data,$item_upgradelinking);
    }
?>
	<input type="hidden" name="item_sub_product_id_deleted" value="" />
</div>

<!-- /sub_product_model.tpl -->

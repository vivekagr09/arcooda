<!-- custom_sticker.tpl -->

<?php 
$section_name='artwork';
function item_serial_row($count = "[ROW_COUNT]", $row_data = array())
{
    $section_name='artwork';
    $name_key = $section_name . "[" . $count . "]";
    $hidden   = array(
        $name_key . "[id]"   => $row_data["id"],
        $name_key . "[product_id]" => $row_data["product_id"],
    );
    ob_start();
?>
	<table class="table_fields" id="item_art_work_rowa[<?= $count ?>]" style="margin-bottom: 10px;">
		<?=formatHTMLInputs($hidden)?>
		<tr>
			<th style="width:10%;text-align:center; top:5px;padding: 15px; " valign="top">
				<p style="background-color:#fff; color: #000; font-size:15px;vertical-align:middle;padding:20px;border:1px solid red; "><?= ($row_data['id']!='[ROW_ID]')?$row_data['id']:'N/A'?></p>
			</th>
			<td>
				<table style="width: 100%;">
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
			            <th>Product Material(<small><i><a href="options.php?action=list&amp;option_name=product_material" target="_blank">Add</a></i></small>) :</th>
			            <td><select name="<?=$name_key?>[material]">
			                <option value="0">Not Selected</option>
			                <?=formatHTMLOptions(productGetOptionList('product_material'), $row_data['material'])?>
			            </select></td>
			        </tr>
			        <tr>
				        <th>Size :</th>
				        <td>
				        	<input style="width:30%;" class="wide" type="text" name="<?=$name_key?>[length]" value="<?php hecho($row_data['length']) ?>"placeholder="Length" /> mm Length
				        	<input style="width:30%;" class="wide" type="text" name="<?=$name_key?>[width]" value="<?php hecho($row_data['width']) ?>" placeholder="Width"/> mm Width
				        </td>
				    </tr>
				    <tr>
				        <th>Template Name :</th>
				        <td><input class="wide" type="text" name="<?=$name_key?>[template]" value="<?php hecho($row_data['template']) ?>" />
				        </td>
				    </tr>
				    <tr>
				        <th>Template Notes :</th>
				        <td><textarea class="wide" rows="5" name="<?=$name_key?>[tempate_notes]" value="<?php hecho($row_data['tempate_notes']) ?>" style="width: 99%;"><?php hecho($row_data['tempate_notes']) ?></textarea> 
				        </td>
				    </tr>
				    <tr>
				        <th>Template File :</th>
				        <td>
				        	<div>
				        		<img src="/webadmin/uploads/product/art_work/<?= $row_data['template_image'] ?>" width="100" height="100" style="display:<?= ($row_data['template_image'])?'':'none;'?>" id="preview_template_image_<?= $count; ?>">
				        	</div>
							<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
				        	<input type="file" data-key="<?= $count ?>" data-type="template" name="<?=$name_key?>[template_file]" class="upload_image_file" />
				        </td>
				    </tr>
				    <tr>
				        <th>File Resolution (Recommended 300 DPI):</th>
				        <td><input class="wide" type="text" name="<?=$name_key?>[file_resolution]" value="<?php echo ($row_data['file_resolution'])? $row_data['file_resolution']:'300 DPI';  ?>" />
				        </td>
				    </tr>
				    <tr>
				        <th>Colour Profile (Recommended CMYK):</th>
				        <td><input class="wide" type="text" name="<?=$name_key?>[color_profile]" value="<?php echo ($row_data['color_profile'])? $row_data['color_profile']:'CMYK';  ?>" />
				        </td>
				    </tr>
				    <tr>
			            <th>File Type (Recommended PDF ) <small><i><a href="options.php?action=list&amp;option_name=product_file_type" target="_blank">Add</a></i></small> :</th>
			            <td><select name="<?=$name_key?>[file_type]">
			                <?=formatHTMLOptions(productGetOptionList('product_file_type'), $row_data['file_type'])?>
			            </select></td>
			        </tr>
				    <tr>
				        <th>Product Picture :</th>
				        <td>
				        	<div>
				        		<img src="/webadmin/uploads/product/art_work/<?= $row_data['product_image'] ?>" width="100" height="100" style="display:<?= ($row_data['product_image'])?'':'none;'?>" id="preview_product_image_<?= $count; ?>">
				        	</div>
							<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
				        	<input type="file" data-key="<?= $count ?>" data-type="product" name="<?=$name_key?>[product_file]" class="upload_image_file" />
				        </td>
				    </tr>
				    <tr>
				        <th>Cost Price :</th>
				        <td><input class="wide update_art_price" type="number" name="<?=$name_key?>[cost_price]" value="<?php hecho($row_data['cost_price']) ?>" style="width:50%"  rowid="<?= $count?>"/>
				       	<select class="wide update_art_price" name="<?=$name_key?>[currency]" style="width:40%"  rowid="<?= $count?>">
			                       <?=formatHTMLOptions(getCurrencyList(), ($row_data['currency'])?$row_data['currency']:'CNY')?>
			            </select>
				        </td>
				    </tr>
				    <tr>
				        <th>Cost Price :</th>
				        <td><input class="wide update_art_cost_price" type="number" name="<?=$name_key?>[price_usd]" value="<?php hecho($row_data['price_usd']) ?>" style="width:50%"  readonly/> USD
				        </td>
				    </tr>
				    <tr>
				        <th>Sell Price :</th>
				        <td><input class="wide update_art_seles_price" type="number" name="<?=$name_key?>[sell_price]" value="<?php hecho($row_data['sell_price']) ?>" style="width:50%" rowid="<?= $count?>" /> USD
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
				        <th colspan="2"> <input type="button" value="Delete" class="item_artwork_delete" data-id="<?= $row_data['id']?>" data-count="<?= $count?>"style="float: right; margin-right: 5px;" /></th>
				        
				    </tr>
				</table>
			</td>
		</tr>
	</table>
<?php
return ob_get_clean();
}

$randumnumber=rand();
?>
<script type="text/javascript">
	$('body').on('change', '.update_art_price', function(){
        var rowid=$(this).attr('rowid'); 
        var fromElem='artwork['+rowid+'][cost_price]';
       	var toElem='artwork['+rowid+'][price_usd]';
        var from=getValue('artwork['+rowid+'][currency]');
        var to='USD';
        setValue(toElem, toCurrency(getValue(fromElem), from, to));
        var art_cost=0;
        $(".update_art_cost_price").each(function () {
			art_cost=art_cost+parseFloat($(this).val());
		});
        setValue('item[cost_price]',art_cost.toFixed(2));
        $("[name='item[cost_price]']").trigger("change");
    });
    $('body').on('change', '.update_art_seles_price', function(){
    	var art_sales=0;
        $(".update_art_seles_price").each(function () {
			art_sales=art_sales+parseFloat($(this).val());
		});
        setValue('item[sales_price]',art_sales.toFixed(2));
        $("[name='item[sales_price]']").trigger("change");
    });
	 $(document).on('click', '.item_artwork_delete', function() {
        var $table = $(this).closest("table");
        if (confirm("Are you sure you want to delete this art work?")) {
           //var row = ProductSupplier.row(this);
            setValue("item_art_work_id_deleted", getValue("item_art_work_id_deleted") + "," + $(this).attr('data-id'));
            var theTable=getElement('item_art_work_rowa['+ $(this).attr('data-count')+']');
            if(theTable) theTable.parentNode.removeChild(theTable);
            //$table.detach();
        }
        return false;
    }).toggle(true);
	var currentserialno=0
	var art_work_count="<?= count($art_work)+1;?>";
	function addart_work(){
		var row_string = <?=json_encode(item_serial_row('[ART_NUMBER_COUNT]', ['product_id'=>$item['id'],'serial_id'=> '[SERIAL_ID]','id'=> '[ROW_ID]']))?>;
	    	row_string = row_string.replace(/\[ART_NUMBER_COUNT\]/g, art_work_count);
	    $("#art_work_number").append(row_string);	
	    art_work_count++;
	}
	function item_art_work_delete(id,dataid) {
	    if (confirm("Are you sure you want to delete  this serial no ??")) {
	        var theRow = getElement("item_art_work_rowa["+id+"]");
	        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	        if(dataid>0)
	        setValue("item_art_work_id_deleted", getValue("item_sereal_id_deleted") + "," + dataid);
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
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;art_work&quot;);return false">Art Work</a>','<a stylr="cursor:pointer;" onclick="addart_work();">Add</a>')?>

<div id="art_work_number">

<?php
$count    = 0;
    $affected = array();

    foreach ($art_work as $data) {

        echo item_serial_row($count++, $data);
    }
?>
	<input type="hidden" name="item_art_work_id_deleted" value="" />
</div>

<!-- /custom_sticker.tpl -->

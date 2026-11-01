
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;Firmware&quot;);return false">Firmware</a>')?>
<div id="Firmware">
    <table class="table_fields">
    	 <tr>
            <th>Firmware Name :</th>
            <td>
            	<input type="text" placeholder="Name" name="firmware[name]" value="<?= $firmware['name'] ?>" style="width:95%" />
            </td>
        </tr>
        <tr>
            <th>Online Text <span ><svg style="width: 12px;" class="svg-inline--fa fa-question-circle fa-w-16" onmouseover="tt_in('This text will be displayed online, try to explain what change was made from the previous version');" onmouseout="tt_out();" aria-hidden="true" focusable="false" data-prefix="far" data-icon="question-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 448c-110.532 0-200-89.431-200-200 0-110.495 89.472-200 200-200 110.491 0 200 89.471 200 200 0 110.53-89.431 200-200 200zm107.244-255.2c0 67.052-72.421 68.084-72.421 92.863V300c0 6.627-5.373 12-12 12h-45.647c-6.627 0-12-5.373-12-12v-8.659c0-35.745 27.1-50.034 47.579-61.516 17.561-9.845 28.324-16.541 28.324-29.579 0-17.246-21.999-28.693-39.784-28.693-23.189 0-33.894 10.977-48.942 29.969-4.057 5.12-11.46 6.071-16.666 2.124l-27.824-21.098c-5.107-3.872-6.251-11.066-2.644-16.363C184.846 131.491 214.94 112 261.794 112c49.071 0 101.45 38.304 101.45 88.8zM298 368c0 23.159-18.841 42-42 42s-42-18.841-42-42 18.841-42 42-42 42 18.841 42 42z" ></path></svg></span>:</th>
            <td>
            	<textarea name="firmware[comment]" value="<?= $firmware['comment'] ?>" style="width:95%"><?= $firmware['comment'] ?></textarea>
            </td>
        </tr>
        <tr>
        	<th style="padding: 5px;color: red; border: 1px solid red;">NOT FOR ONLINE</th>
        	<td></td>
        </tr>
        <tr>
            <th>Version#:</th>
            <td>
            	<input type="text" placeholder="Name" name="firmware[version]" value="<?= $firmware['version'] ?>" style="width:50%" />
            </td>
        </tr>
         <tr>
            <th>Firmware date:</th>
            <td>
            	<input type="date" placeholder="Name" name="firmware[firmware_date]" value="<?= $firmware['firmware_date'] ?>"  />
            </td>
        </tr>
         <tr>
            <th>Admin Notes:</th>
            <td>
            	<textarea name="firmware[notes]" value="<?= $firmware['notes'] ?>" style="width:95%" rows="10"><?= $firmware['notes'] ?></textarea>
            </td>
        </tr>
        <tr>
            <th>Firmware File:</th>
            <td>
                <div>
					<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
                    <input name="file_name" type="file"  />
                </div>
                <div>
                    <!-- <a href="/webadmin/assets/owner/firmware/<?php hecho((array_key_exists('file_name', $firmware)) ? $firmware['file_name'] : '#')?>" target="_blank" id="item_thumb_a"><?= $firmware['file_orig']?></a> -->
					<a href="/webadmin/assets/owner/firmware/<?php echo (array_key_exists('file_name', $firmware)) ? htmlspecialchars($firmware['file_name']) : '#' ?>" 
					target="_blank" 
					id="item_thumb_a"
					<?php if (array_key_exists('file_name', $firmware)) : ?>
						<?php
						$fileExtension = pathinfo($firmware['file_name'], PATHINFO_EXTENSION);
						$allowedInlineTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'mp4', 'webm']; // Add more inline types as needed

						if (!in_array(strtolower($fileExtension), $allowedInlineTypes)) {
							echo 'download="' . htmlspecialchars($firmware['name']) . '"';
						}
						?>
					<?php endif; ?>>
					<?= htmlspecialchars($firmware['file_orig']) ?>
					</a>
					<?php
					if (array_key_exists('file_name', $firmware)) {
					?>
					    <br />
					   <small style="color:#f00;"><i>NB: Uploading new file will overwrite existing file</i></small>
					<?php
					}
					?>
                </div>
            </td>
        </tr>
    </table>
</div>
<?php 

include "add-on-product.php";
if(isset($firmware['id'])) {
	include "admin_files.tpl.php";
} 
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sttaus&quot;);return false">Status</a>')?>
<div id="sttaus">
    <table class="table_fields">
        <tr>
            <th>Show Firmware File Online <span ><svg style="width: 12px;" class="svg-inline--fa fa-question-circle fa-w-16" onmouseover="tt_in('Default is No (admin use only), if choosing “Owner Only” the customer must register their product');" onmouseout="tt_out();" aria-hidden="true" focusable="false" data-prefix="far" data-icon="question-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 448c-110.532 0-200-89.431-200-200 0-110.495 89.472-200 200-200 110.491 0 200 89.471 200 200 0 110.53-89.431 200-200 200zm107.244-255.2c0 67.052-72.421 68.084-72.421 92.863V300c0 6.627-5.373 12-12 12h-45.647c-6.627 0-12-5.373-12-12v-8.659c0-35.745 27.1-50.034 47.579-61.516 17.561-9.845 28.324-16.541 28.324-29.579 0-17.246-21.999-28.693-39.784-28.693-23.189 0-33.894 10.977-48.942 29.969-4.057 5.12-11.46 6.071-16.666 2.124l-27.824-21.098c-5.107-3.872-6.251-11.066-2.644-16.363C184.846 131.491 214.94 112 261.794 112c49.071 0 101.45 38.304 101.45 88.8zM298 368c0 23.159-18.841 42-42 42s-42-18.841-42-42 18.841-42 42-42 42 18.841 42 42z" ></path></svg></span>:</th>
            <td>
	        	<select  name="firmware[online]" onchange="this.style.backgroundColor=((this.value=='0')?'rgb(255 0 0)':'#00FF00');" style="width:90%; background:<?= ('0' == $firmware['online']) ? 'rgb(255 0 0);' : '#00FF00;'?>">
	        		<?=formatHTMLOptions(['0' => 'No' ,'1'=>'Owner Only','2'=>'Every One'], $firmware['online'])?>
	        	</select>
            </td>
        </tr>
       	<tr>
            <th>Show Firmware Name & Text Online :</th>
            <td>
	        	<select class="form-control col-sm-9" id="status" name="firmware[status]"  onchange="this.style.backgroundColor=((this.value=='n')?'rgb(255 0 0)':'#00FF00');" style="width:90%;background:<?=('y' == $firmware['status']) ?  '#00FF00;':'rgb(255 0 0);';?>">
	                <?= formatHTMLOptions(['n'=>'Deactive','y'=>'Active'], $firmware['status']) ?>
	            </select>
            </td>
        </tr>
    </table>
</div>

<center>
	<input type="hidden" name="id" value="<?= $firmware['id']?>">
	<input type="button" value="<?= ($add)?'Add':'Update'?>" onclick="javascript:this.form.submit();" />
</center>
<script type="text/javascript">
	function addProduct(type,returnmessgae) {
		    var section="&section[]="+type;
		    var url = "/webadmin/products_new/master_products.php?return_message="+returnmessgae;
		    url += "&multiple=y";
		    url += "&search_for=";
		    url += section;
		    // open product search page...
		    window.open(url);
	}
	function linked_products_data(dataarray){
    	dataarray.item_list.forEach(productsData);
	} 
	function productsData(data) {
   		if(data.return_message && data.id){
   			$('#'+data.return_message).append('<div> <input type="checkbox" name="products[]" value="'+data.id+'" checked  /> '+data.product_name+' </div>') 
   		}
	}
</script>
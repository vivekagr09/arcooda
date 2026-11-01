<style type="text/css">
	.divwide {
	    display: flex;
	    flex-wrap: wrap;
	    font-size: 14px;
	}

	.divwide > div {
	    width: 33%; 
	    box-sizing: border-box;
	    padding: 5px; 
	}
</style>
<div align="center">
	<?= formatHTMLTitle('Name');?>
</div>

<table class="wide" align="center" >
	<tr>
		<td><input type="text" placeholder="Name" name="manual[name]" value="<?= $manual['name'] ?>" style="width:100%" /></td>
	</tr>
</table>
<div align="center">
	<?= formatHTMLTitle('Comment');?>
</div>
<table class="wide" align="center" >
	<tr>
		<td><input type="text" placeholder="Comment" name="manual[comment]" value="<?= $manual['comment'] ?>" style="width:100%" /></td>
	</tr>
</table>
<div align="center">
	<?= formatHTMLTitle('Linked To Machine','<a href="#" onclick="addProduct(2,\'machine-data\');return false" style="color:black">Add New</a>')?>
</div>
<table class="wide" align="center" >
	<tr>
		
		<td style="width:100%">
			<div class="divwide" id="machine-data">

				<?php 
					foreach ($manual_machine_product as $key => $product) {
					?>
						<div >
						<input type="checkbox" name="products[]" value="<?= $product['product_id']?>" checked  /> <?= $product['product_name'] ?>
						</div>
					<?php
					}
				?>

			</div>
		</td>
	</tr>
</table>
<div align="center">
	<?= formatHTMLTitle('Linked To Software','<a href="#" onclick="addProduct(3,\'software-data\');return false" style="color:black">Add New</a>');?>
</div>
<table class="wide" align="center" >
	<tr>
		
		<td style="width:100%">
			<div class="divwide" id="software-data">

				<?php 
					foreach ($manual_software_product as $key => $product) {
					?>
						<div >
						<input type="checkbox" name="products[]" value="<?= $product['product_id']?>" checked  /> <?= $product['product_name'] ?>
						</div>
					<?php
					}
				?>

			</div>
		</td>
	</tr>
</table>
<div align="center">
	<?= formatHTMLTitle('Linked To Parts','<a href="#" onclick="addProduct(1,\'parts-data\');return false" style="color:black">Add New</a>');?>
	<table class="wide" align="center" >
		<tr>
			
			<td style="width:100%">
				<div class="divwide" id="parts-data">

					<?php 
						foreach ($manual_parts_product as $key => $product) {
						?>
							<div >
							<input type="checkbox" name="products[]" value="<?= $product['product_id']?>" checked  /> <?= $product['product_name'] ?>
							</div>
						<?php
						}
					?>

				</div>
			</td>
		</tr>
	</table>
</div>
<div align="center">
	<?= formatHTMLTitle('Linked To Merchandise','<a href="#" onclick="addProduct(4,\'merchandise-data\');return false" style="color:black">Add New</a>');?>
	<table class="wide" align="center" >
		<tr>
			
			<td style="width:100%">
				<div class="divwide" id="merchandise-data">

					<?php 
						foreach ($manual_merchandise_product as $key => $product) {
						?>
							<div>
							<input type="checkbox" name="products[]" value="<?= $product['product_id']?>" checked  /> <?= $product['product_name'] ?>
							</div>
						<?php
						}
					?>

				</div>
			</td>
		</tr>
	</table>
</div>
<div align="center">
	<?= formatHTMLTitle('File');?>
</div>
<table class="table_fields" align="center" >
            
        <tr>
            <td>
                <div>
					<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
                    <input name="file_name" type="file"  />
                </div>
                <div>
                    <!-- <a href="/webadmin/assets/owner/manual/<?php hecho((array_key_exists('file_name', $manual)) ? $manual['file_name'] : '#')?>" target="_blank" id="item_thumb_a"><?= $manual['file_orig']?></a> -->
					<a href="/webadmin/assets/owner/manual/<?php echo (array_key_exists('file_name', $manual)) ? htmlspecialchars($manual['file_name']) : '#' ?>" 
					target="_blank" 
					id="item_thumb_a"
					<?php if (array_key_exists('file_name', $manual)) : ?>
						<?php
						$fileExtension = pathinfo($manual['file_name'], PATHINFO_EXTENSION);
						$allowedInlineTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'mp4', 'webm']; // Add more inline types as needed

						if (!in_array(strtolower($fileExtension), $allowedInlineTypes)) {
							echo 'download="' . htmlspecialchars($manual['name']) . '"';
						}
						?>
					<?php endif; ?>>
					<?= htmlspecialchars($manual['file_orig']) ?>
					</a>

					<?php
					if (array_key_exists('file_name', $manual)) {
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
<div align="center">
	<?= formatHTMLTitle('Status');?>
</div>
<table class="table_fields" align="center" >
         <tr>
            <td>
	              <select class="form-control col-sm-9" id="status" name="manual[status]"  onchange="this.style.backgroundColor=((this.value=='n')?'rgb(255 0 0)':'#00FF00');" style="width:90%;background:<?=('y' == $manual['status']) ?  '#00FF00;':'rgb(255 0 0);';?>">
	                <?= formatHTMLOptions(['n'=>'In Active','y'=>'Active'], $manual['status']) ?>
	            </select>
            </td>
        </tr>
        
	</table>
<br />
<center>
	<input type="hidden" name="id" value="<?= $manual['id']?>">
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
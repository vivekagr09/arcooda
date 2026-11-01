
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
	<?= formatHTMLTitle('Linked To Machine');?>
</div>
<table class="wide" align="center" >
	<tr>
		
		<td style="width:100%">
			<table style="width:100%">
			<?php 
				$products=array_chunk($product_list, 3);
				foreach ($products as $key => $chunkvalue) {
					echo '<tr>';
					foreach ($chunkvalue as $key => $product) {
					?>
					<td>
					<input type="checkbox" name="products[]" value="<?= $product['id']?>" <?= in_array($product['id'],$manual_product)?'checked':''?> /> <?= $product['name'] ?></td>
				<?php

					}
					echo '</tr>';
				}


			?>
		</table>
		</td>
	</tr>
</table>
<div align="center">
	<?= formatHTMLTitle('Linked To Software');?>
</div>
<table class="wide" align="center" >
	<tr>
		
		<td style="width:100%">
			<table style="width:100%">
			<?php 
				$products=array_chunk($product_list_software, 3);
				foreach ($products as $key => $chunkvalue) {
					echo '<tr>';
					foreach ($chunkvalue as $key => $product) {
					?>
					<td>
					<input type="checkbox" name="products[]" value="<?= $product['id']?>" <?= in_array($product['id'],$manual_product)?'checked':''?> /> <?= $product['name'] ?></td>
				<?php

					}
					echo '</tr>';
				}


			?>
		</table>
		</td>
	</tr>
</table>

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
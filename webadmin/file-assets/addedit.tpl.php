
<div align="center">
	<?= formatHTMLTitle('File Name');?>
</div>

<table class="wide" align="center" >
	<tr>
		<td><input type="text" placeholder="Name" name="assets[name]" value="<?= $assets['name'] ?>" style="width:100%" /></td>
	</tr>
</table>
<div align="center">
	<?= formatHTMLTitle('Our Notes');?>
</div>
<table class="wide" align="center" >
	<tr>
		<td><input type="text" placeholder="Comment" name="assets[comment]" value="<?= $assets['comment'] ?>" style="width:100%" /></td>
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
                    <input name="file_name" type="file" />
                </div>
                <div>
                    <a href="/webadmin/assets/fileassets/<?php hecho((array_key_exists('file_name', $assets)) ? $assets['file_name'] : '#')?>" target="_blank" id="item_thumb_a"><?= $assets['file_orig']?></a>
					<?php
					if (array_key_exists('file_name', $assets)) {
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
	              <select class="form-control col-sm-9" id="status" name="assets[status]" >
	                <?= formatHTMLOptions(['private'=>'Private','public'=>'Public'], $assets['status']) ?>
	            </select>
            </td>
        </tr>
        
	</table>
<br />
<center>
	<input type="hidden" name="id" value="<?= $assets['id']?>">
	<input type="button" value="<?= ($add)?'Add':'Update'?>" onclick="javascript:this.form.submit();" />
</center>
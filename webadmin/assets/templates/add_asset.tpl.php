<?= formatHTMLTitle('Add New '.$section_singular) ?>

<table align="center" class="wide" style="border:1px solid blue;">
	<tr>
		<td class="left_title" style="width:25%;">File:</td>
		<td>
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input type="hidden" name="return_message" value="<?= $posted['return_message']?>">
			<input size="50" style="width:98%;" type="file" name="asset_file" />
		</td>
	</tr>
	<tr>
		<td class="left_title">Caption:</td>
		<td><input size="50" style="width:98%;" type="text" name="asset_caption" /></td>
	</tr>
<?php
if($asset_type=='convert_screenshots')
{
	$options=array(1=>'Actual',2=>'Reference');
?>
	<tr>
		<td class="left_title">Screenshot Type:</td>
		<td><select style="width:98%;" name="ss_type"><?= formatHTMLOptions($options,$asset['ss_type']) ?></select></td>
	</tr>
<?php
}
?>
</table>

<br /><br />

<div align="center">
	<?= formatHTMLTitle('Page META Tag Details');?>
</div>

<?php
$meta_title = '';
$meta_keys = '';
$meta_desc = '';
$total_title = $total_keys = $total_desc = 0;
if ($id != 0)
{
	$meta_title = ($meta['meta_title'] != '') ? htmlspecialchars($meta['meta_title']) : '';
	$total_title = strlen($meta_title);
	$meta_keys = ($meta['meta_keys'] != '') ? htmlspecialchars($meta['meta_keys']) : '';
	$ar = explode(',', $meta_keys);
	$total_keys = 0;
	foreach($ar as $key => $value) if (trim($value) != '') $total_keys++;
	$meta_desc = ($meta['meta_desc'] != '') ? htmlspecialchars($meta['meta_desc']) : '';
	$total_desc = strlen($meta_desc);
}
?>

<table class="wide" align="center" style="width:98%">
	<tr>
		<td class="left_title" style="width:20%">Page Name :</td>
		<td colspan="2"><input type="text" name="meta[page]" value="<?= $meta['page'] ?>" style="width:100%" /></td>
	</tr>
	<tr>
		<td class="left_title">
			<a href="" onclick="window.open('help.php?action=title','','width=400,height=400');return false" style="color:blue">Title :</a>
		</td>
		<td><input type="text" name="meta[meta_title]" style="width:100%" value="<?= $meta_title?>" onchange="updateTotals('meta')"></td>
		<td id="meta_title" name="meta_title"><?= $total_title?></td>
	</tr>
	<tr>
		<td class="left_title">
			<a href="" onclick="javascript:window.open('help.php?action=keywords','','width=400,height=400');return false" style="color:blue">Keywords :</a>
		</td>
		<td><textarea name="meta[meta_keys]" style="width:100%" rows=5 onkeypress="resize(this)" onchange="updateTotals('meta')"><?= $meta_keys?></textarea></td>
		<td id="meta_keys" name="meta_keys"><?= $total_keys?></td>
	</tr>
	<tr>
		<td class="left_title">
			<a href="" onclick="javascript:window.open('help.php?action=desc','','width=400,height=400');return false" style="color:blue">Description :</a>
		</td>
		<td><textarea name="meta[meta_desc]" style="width:100%" rows=5 onkeypress="resize(this)" onchange="updateTotals('meta')"><?= $meta_desc?></textarea></td>
		<td id="meta_desc" name="meta_desc"><?= $total_desc?></td>
	</tr>
	<?php if (!$add) { ?>
	<tr style="background-color:#FFEECC">
		<td class="left_title">META Tags Last Changed :</td>
		<td style="padding-left:3px;">
			<?= ($meta['last_modified_unix'] != 0) ? (date('d/m/y',$meta['last_modified_unix']).', By : '.getStaffName($meta['staff'])) : 'Never'?>
		</td>
		<td>&nbsp;</td>
	</tr>
	<?php } ?>
</table>
<br />
<center>
	<input type="hidden" name="id" value="<?= $meta['id']?>">
	<input type="button" value="<?= ($add)?'Add':'Update'?>" onclick="javascript:this.form.submit();" />
</center>
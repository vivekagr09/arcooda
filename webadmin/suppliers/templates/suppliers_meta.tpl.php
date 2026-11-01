<?php
$ar = explode(',',$supplier['meta_keys']);
$total_keys=0;
foreach($ar as $key=>$value) if(trim($value) != '') $total_keys++;
?>
<script>
function updateTags()
{
	var name=getValue('supplier[manufacturer]');
	setValue('supplier[meta_title]', name);
	setValue('supplier[meta_keys]', formatMETA(getValue('supplier[short_desc]'),'keyword'));
	setValue('supplier[meta_desc]', getValue('supplier[short_desc]'));
	setValue('supplier[url_keys]', generateURLKeys(name));

	updateTotals();
}

function updateTotals()
{
	var meta = getValue('supplier[meta_keys]');
	//now update the length of the words
	var keywords = meta.split(',');
	var len = 0;
	for(i=0;i<keywords.length;i++) if(keywords[i] != '') len++;
	getElement('meta_title').innerHTML = getValue('supplier[meta_title]').length;
	getElement('meta_keys').innerHTML = len;
	getElement('meta_desc').innerHTML = getValue('supplier[meta_desc]').length;
}

function clearTags()
{
	setValue('supplier[meta_title]','');
	setValue('supplier[meta_keys]','');
	setValue('supplier[meta_desc]','');
	setValue('supplier[url_keys]','');
}
</script>
<div align="center"><?= formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionMETA\');">META Tags</a>') ?></div>
<div id="sectionMETA"><table class="wide" align="center" style="width:98%;">
	<tr><td class="left_title"><a href="" onclick="javascript:openWindow('templates/help.php?action=title','400','400', true);return false" style="color:blue">Title :</td><td><input type="text" name="supplier[meta_title]" style="width:100%" value="<?= $supplier['meta_title'] ?>" onchange="updateTotals();"></td><td id="meta_title" name="meta_title"><?= strlen($supplier['meta_title']) ?></td>
	</tr>
	<tr>
		<td class="left_title"><a href="" onclick="javascript:openWindow('templates/help.php?action=keywords',400,400, true);return false" style="color:blue">Keywords :</a></td>
		<td><textarea name="supplier[meta_keys]" style="width:100%" onkeypress="textareaAutoSize(this,3,3,25)" onchange="updateTotals();" rows=8><?= $supplier['meta_keys'] ?></textarea></td>
		<td id="meta_keys" name="meta_keys"><?=$total_keys?></td>
	</tr>
	<tr>
		<td class="left_title"><a href="" onclick="javascript:openWindow('templates/help.php?action=desc',400,400,true);return false" style="color:blue">Description :</td>
		<td><textarea name="supplier[meta_desc]" style="width:100%" onkeypress="textareaAutoSize(this,3,3,25)" onchange="updateTotals();" rows=10><?= $supplier['meta_desc'] ?></textarea></td>
		<td id="meta_desc" name="meta_desc"><?=$total_desc?></td>
	</tr>
	<tr>
		<td class="left_title"><a href="" onclick="javascript:openWindow('templates/help.php?action=url',400,400,true);return false" style="color:blue">URL Keywords :</td>
		<td><input type="text" name="supplier[url_keys]" style="width:100%" value="<?= $supplier['url_keys'] ?>" onchange="updateTotals();"></td>
		<td id="url_keys" name="url_keys"></td>
	</tr>
	<tr><td class="left_title">&nbsp;</td><td colspan="2" style="background-color:#FFEECC;" align="center">Last Change: <?= ($supplier['meta_time']!='') ? date('d/m/Y', $supplier['meta_time']) : 'Never' ?>, Changed By: <?= $all_staff_list[$supplier['changed_by']] ?></td></tr>
</table><br /><center><input type="button" value="Auto Update Tags" onclick="updateTags()"/><input type="button" value="Clear" onclick="clearTags()"/></center><br /></div>
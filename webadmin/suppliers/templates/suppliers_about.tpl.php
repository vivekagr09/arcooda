<?php
$short_rows = 0;
$rows       = explode("\n", $supplier['short_desc']);
foreach ($rows as $row) {
    $short_rows += floor(strlen($row) / 60.0) + 1;
}

$long_rows = 0;
$rows      = explode("\n", $supplier['long_desc']);
foreach ($rows as $row) {
    $long_rows += floor(strlen($row) / 60.0) + 1;
}

$icodes = explode(',', $supplier['industry_code']);
?>
<div align="center"><?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionAbout\');">About Manufacturer</a>')?></div>
<div id="sectionAbout"><table class="wide" align="center" style="width:98%;">
	<tr>
		<td class="left_title">Short Description :</td>
		<td><textarea name="supplier[short_desc]" rows="<?=max(5, $short_rows)?>" onkeypress="textareaAutoSize(this,3,3,25);"><?=htmlspecialchars($supplier['short_desc'])?></textarea></td>
	</tr>
	<tr><td class="left_title">&nbsp;</td><td>
		<input style="width:96px;" type="button" value="Insert Link" onclick="generic_insert('supplier[short_desc]','a',' href=&quot;https://www.WEBSITE.com/&quot; target=&quot;_blank&quot;');" />
		<input style="width:96px;font-weight:bold;" type="button" value="Bold" onclick="generic_insert('supplier[short_desc]', 'b');" />
		<input style="width:96px;font-style:italic;" type="button" value="Italic" onclick="generic_insert('supplier[short_desc]', 'i');" />
		<input style="width:96px;text-decoration:underline;" type="button" value="Underline" onclick="generic_insert('supplier[short_desc]', 'u');" /><br />
		<input type="button" value="Copy Description from Above" onclick="setValue('supplier[long_desc]', getValue('supplier[short_desc]'))">
	</td></tr>
	<tr>
		<td class="left_title">Long Description :</td>
		<td><textarea name="supplier[long_desc]" rows="<?=max(5, $long_rows)?>" onkeypress="textareaAutoSize(this,3,3,25);"><?=htmlspecialchars($supplier['long_desc'])?></textarea></td>
	</tr>
	<tr><td class="left_title">&nbsp;</td><td>
		<input style="width:96px;" type="button" value="Insert Link" onclick="generic_insert('supplier[long_desc]','a',' href=&quot;https://www.WEBSITE.com/&quot; target=&quot;_blank&quot;');" />
		<input style="width:96px;font-weight:bold;" type="button" value="Bold" onclick="generic_insert('supplier[long_desc]', 'b');" />
		<input style="width:96px;font-style:italic;" type="button" value="Italic" onclick="generic_insert('supplier[long_desc]', 'i');" />
		<input style="width:96px;text-decoration:underline;" type="button" value="Underline" onclick="generic_insert('supplier[long_desc]', 'u');" />
	</td></tr>
	<tr>
		<td class="left_title">Image :</td>
		<td>
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input type="file" name="supp_image" /><?=('' != $supplier['image']) ? '<a href="getimage.php?supplier_id=' . $supplier_id . '" target="_blank"><img border="0" style="width:96px;height:96px;" src="getimage.php?supplier_id=' . $supplier_id . '" /></a>' : ''?>
		</td>
	</tr>
	<tr>
		<td class="left_title">Thumbnail :</td>
		<td>
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input type="file" name="supp_thmb" /><?=('' != $supplier['image']) ? '<img src="getimage.php?supplier_id=' . $supplier_id . '&thumb=true" />' : ''?>
		</td>
	</tr>
	<tr><td class="left_title">&nbsp;</td><td>&nbsp;</td></tr>
	<tr><td class="left_title">Industry Code :</td><td>
		<input type="checkbox" name="icode[]" value="amusement"<?=(in_array('amusement', $icodes)) ? ' checked' : ''?>/>Amusement Products<br />
		<input type="checkbox" name="icode[]" value="gaming"<?=(in_array('gaming', $icodes)) ? ' checked' : ''?>/>Gaming Products<br />
		<input type="checkbox" name="icode[]" value="vending"<?=(in_array('vending', $icodes)) ? ' checked' : ''?>/>Vending Products<br />
		<input type="checkbox" name="icode[]" value="ride"<?=(in_array('ride', $icodes)) ? ' checked' : ''?>/>Parks and Rides<br />
		<input type="checkbox" name="icode[]" value="spare"<?=(in_array('spare', $icodes)) ? ' checked' : ''?>/>Spare Parts
	</td></tr>
</table></div>
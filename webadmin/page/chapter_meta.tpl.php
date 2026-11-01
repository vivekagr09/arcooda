<!-- chapter_meta.tpl -->

<?php
$title = ('' != $chapter['title']) ? $chapter['title'] : formatMETA($chapter['heading']);
$description  = ('' != $chapter['description']) ? $chapter['description'] : formatMETA($chapter['heading']);

$keyword    = formatMETA($chapter['heading'], 'keyword');
$keywords  = ('' != $chapter['keywords']) ? $chapter['keywords'] : $keyword;
$total_keys = 0;
foreach (explode(',', $keywords) as $value) {
    if (trim($value) != '') {
        $total_keys++;
    }
}

$modified_staff_id_list = pageGetOptionList('modified_staff_id');

//Note: Disabled due to confusion, but was here for a reason...
//if($chapter['modified']==0) $keywords=$keyword;
?>

<script type="text/javascript">
function pageUpdateTags()
{
	var name=getValue('chapter[heading]');
	setValue('chapter[title]', name);
	setValue('chapter[keywords]',formatMETA(name,'keyword'));
	//setValue('chapter[description]','');
	pageUpdateTagTotals();
}
function pageClearTags()
{
	setValue('chapter[title]','');
	setValue('chapter[keywords]','');
	setValue('chapter[description]','');
	pageUpdateTagTotals();
}
function pageUpdateTagTotals()
{
	var title=getValue('chapter[title]');
	var meta=getValue('chapter[keywords]');
	var desc=getValue('chapter[description]');
	var keywords=meta.split(',');
	var len=0;
	for(i=0;i<keywords.length;i++) if(keywords[i]!='') len++;
	setValue('title',title.length);
	setValue('keywords',len);
	setValue('description',desc.length);
}
</script>

<?=formatHTMLTitle('<a href="#" onclick="$(\'#sectionMetaTags\').toggle(\'slow\'); return false;">Chapter META Tags</a>')?>

<div id="sectionMetaTags">
	<table class="wide">
		<tr>
			<td class="left_title" style="width:25%;">Title :</td>
			<td><input type="text" name="chapter[title]" style="width:100%;" value="<?php hecho($title)?>" onchange="pageUpdateTagTotals();" /></td>
			<td id="title" name="title"><?=strlen($title)?></td>
		</tr>
		<tr>
			<td class="left_title">Keywords :</td>
			<td><textarea name="chapter[keywords]" style="width:100%;" onkeypress="textareaAutoSize(this);" onchange="pageUpdateTagTotals();" rows="8"><?php hecho($keywords)?></textarea></td>
			<td id="keywords" name="keywords"><?=$total_keys?></td>
		</tr>
		<tr>
			<td class="left_title">Description :</td>
			<td><textarea name="chapter[description]" style="width:100%;" onkeypress="textareaAutoSize(this);" onchange="pageUpdateTagTotals();" rows="10"><?php hecho($description)?></textarea></td>
			<td id="description" name="description"><?=strlen($description)?></td>
		</tr>
		<tr style="background-color:#FFEECC">
			<td class="left_title">META Tags Last Changed :</td>
			<td colspan="2"><?=(0 != $chapter['modified_unix']) ? (date('d/m/Y', $chapter['modified_unix']) . ', By :' . $modified_staff_id_list[$chapter['modified_staff_id']]) : 'Never'?></td>
		</tr>
	</table>
	<br />
	<center>
		<input type="button" value="Auto Update Tags" onclick="pageUpdateTags();" />
		<input type="button" value="Clear" onclick="pageClearTags();" />
	</center>
</div><br /><br />

<!-- /chapter_meta.tpl -->

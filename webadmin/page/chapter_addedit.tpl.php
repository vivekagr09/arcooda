<!-- chapter_addedit.tpl -->

<?php
$form_action = 'list';
if ('chapter_edit' == $action) {
    $form_action = 'do_edit_chapter';
}
if ('chapter_add' == $action) {
    $form_action = 'do_add_chapter';
}

?>
<script type="text/javascript">
//<![CDATA[
function chapterValidate() {
	return true;
}
//]]>
</script>
<?php

$rows      = explode("\n", $chapter['content']);
$row_count = 0;
foreach ($rows as $row) {
    $row_count += floor(strlen($row) / 60.0) + 1;
}

$item_images = pageImageSearch(array('page_id' => $id));

?>
<input type="hidden" name="page_id" value="<?=$id?>" />
<input type="hidden" name="id" value="<?=$id?>" />
<?=formatHTMLTitle('<a href="#" onclick="$(\'#sectionCContent\').toggle(\'slow\'); return false;">Chapter Content</a>')?>
<div id="sectionCContent"><table class="table_fields">
	<tr><th>Main Menu :</th><td><?=$page['name']?></td></tr>
	<tr><th>Sub-Menu Title :</th><td><input style="width:98%;" type="text" name="chapter[heading]" value="<?=$chapter['heading']?>" /></td></tr>
	<tr><th>Content :</th><td><textarea name="chapter[content]" rows="<?=max(20, $row_count)?>" onkeyup="textareaAutoSize(this,1,20);"><?php hecho($chapter['content'])?></textarea></td></tr>
	<tr><th></th><td style="text-align:justify;">
		<input style="width:96px;" type="button" value="Insert Link" onclick="generic_insert('chapter[content]','a',' href=&quot;http://www.WEBSITE.com/&quot; target=&quot;_blank&quot;');" />
		<input style="width:96px;" type="button" value="Header" onclick="generic_insert('chapter[content]','div',' class=&quot;flagHeader&quot;'); generic_insert('chapter[content]','span');" />
		<input style="width:96px;" type="button" value="List" onclick="generic_insert('chapter[content]','ul');" />
		<input style="width:96px;" type="button" value="Numbered List" onclick="generic_insert('chapter[content]','ol');" />
		<input style="width:96px;" type="button" value="Bullet Point" onclick="generic_insert('chapter[content]','li');" />
		<br />
		<input style="width:96px;font-weight:bold;" type="button" value="Bold" onclick="generic_insert('chapter[content]','b');" />
		<input style="width:96px;font-style:italic;" type="button" value="Italic" onclick="generic_insert('chapter[content]','i');" />
		<input style="width:96px;text-decoration:underline;" type="button" value="Underline" onclick="generic_insert('chapter[content]','u');" />
		<input style="width:96px;text-decoration:line-through;" type="button" value="Strikethru" onclick="generic_insert('chapter[content]','s');" />
		<input style="width:96px;" type="button" value="Insert Email" onclick="generic_insert('chapter[content]','a', ' href=&quot;mailto:EMAIL ADDRESS&quot');" />
		<br />
		<input style="width:96px;color:red;" type="button" value="Red" onclick="generic_insert('chapter[content]','span',' style=&quot;color:red;&quot;');" />
		<input style="width:96px;color:green;" type="button" value="Green" onclick="generic_insert('chapter[content]','span',' style=&quot;color:green;&quot;');" />
		<input style="width:96px;color:blue;" type="button" value="Blue" onclick="generic_insert('chapter[content]','span',' style=&quot;color:blue;&quot;');" />
		<input style="width:96px;color:yellow;" type="button" value="Yellow" onclick="generic_insert('chapter[content]','span',' style=&quot;color:yellow;&quot;');" />
		<br />
		<input style="width:96px;background-color:red;" type="button" value="Red" onclick="generic_insert('chapter[content]','span',' style=&quot;background-color:red;&quot;');" />
		<input style="width:96px;background-color:green;" type="button" value="Green" onclick="generic_insert('chapter[content]','span',' style=&quot;background-color:green;&quot;');" />
		<input style="width:96px;background-color:blue;" type="button" value="Blue" onclick="generic_insert('chapter[content]','span',' style=&quot;background-color:blue;&quot;');" />
		<input style="width:96px;background-color:yellow;" type="button" value="Yellow" onclick="generic_insert('chapter[content]','span',' style=&quot;background-color:yellow;&quot;');" />
		<br />
	</td></tr>
</table></div><br /><br />
<?php

include __DIR__ . "/images.tpl.php";
include __DIR__ . "/chapter_meta.tpl.php";

?>
<input type="button" value="Submit Changes"<?=($chapter_lock['staff_id'] == $_GLOBALS['staff']['staff_id'] || 'chapter_add' == $action) ? ' onclick="if(chapterValidate()) this.form.submit();"' : ' disabled="disabled"'?> />

<!-- /chapter_addedit.tpl -->

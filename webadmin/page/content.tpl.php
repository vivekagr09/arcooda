<!-- content.tpl -->

<?php
$rows      = explode("\n", $item['content']);
$row_count = 0;
foreach ($rows as $row) {
    $row_count += floor(strlen($row) / 60.0) + 1;
}

?>

<?= formatHTMLTitle('<a href="#" onclick="return toggleSection(\'sectionContent\')">Page Content</a>') ?>

<script type="text/javascript">
//<![CDATA[
$(function() {
	var content_editor = null;
	$("[name='item[content_type]']").change(function() {
		if ($(this).val() == "html" && content_editor === null) content_editor = CKEDITOR.replace('item_content', {
			height:600,
			width:971,

			language : 'en',
			baseHref : <?php jecho($_GLOBALS["site_settings"]["home_uri"])?>,
			allowedContent: true,
			format_h2 : {
				element : "h2",
				attributes : {
					style : "background-color:#eee; border:1px solid #ccc; padding:5px 10px; color:#080; font-size:24px; font-weight:bold;"
				}
			},
			contentsCss : "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css",
			bodyClass : "page_body",
			bodyId : "bd"
		});
		else if ($(this).val() != "html" && content_editor !== null)
		{
			content_editor.destroy();
			content_editor = null;
		}
	}).change();

	$('#contact_form').change(function() {
    if(this.checked) {
        $("input[name='item[form_layout]']").prop('disabled', false);
    } else {
	   $("input[name='item[form_layout]']").prop('disabled', true);
	   $("input[name='item[form_layout]']").prop('checked', false);

	}       
});
});
//]]>
</script>

<div id="sectionContent">
	<input type="hidden" name="id" value="<?= $id ?>" />
	<input type="hidden" name="item[id]" value="<?= $id ?>" />
	<table class="table_fields">
		<tr>
			<th>Parent Topic</th>
			<td><select name="item[parent_id]">
				<option value=""></option>
				<?= formatHTMLOptions(pageGetOptionList('parent_id'), $item['parent_id']) ?>
			</select></td>
		</tr>
		<tr>
			<th><a href="<?php hecho($item['page_url'])?>" target="_blank">Page Name</a></th>
			<td><input type="text" class="intext" name="item[name]" value="<?php hecho($item['name'])?>" style="width:394px;"/></td>
		</tr>
		<tr>
			<th>Content Type</th>
			<td><select name="item[content_type]">
				<option value="semihtml"></option>
				<?= formatHTMLOptions(pageGetOptionList('content_type'), $item['content_type']) ?>
			</select></td>
		</tr>
		<tr>
			<th>Content</th>
			<td><textarea id="item_content" name="item[content]" rows="<?= max(20, $row_count) ?>" cols="80" onkeyup="textareaAutoSize(this,1,20);"><?php hecho($item['content'])?></textarea></td>
		</tr>
		<tr>
			<th>Contact Form</th>
			<td>
				<input type="checkbox" name="item[contact_form]" value="y" id="contact_form" <?=("y" == $item['contact_form']) ? 'checked' : ''?>>
				<input type="radio" id="left_aligned" name="item[form_layout]" value="left" <?=("left" == $item['form_layout']) ? 'checked' : ''?>>
				<label for="left_aligned">Left Aligned</label>
				<input type="radio" id="right_aligned" name="item[form_layout]" value="right" <?=("right" == $item['form_layout']) ? 'checked' : ''?>>
 				<label for="right_aligned">Right Aligned</label>
				<input type="radio" id="below_body" name="item[form_layout]" value="center" <?=("center" == $item['form_layout']) ? 'checked' : ''?>>
 				<label for="below_body">Below Body</label>
			</td>
		</tr>
		<tr>
			<th>Still Have a Question</th>
			<td><input type="checkbox" name="item[have_question]" value="y" <?=("y" == $item['have_question']) ? 'checked' : ''?>></td>
		</tr>
<?php
if ("semihtml" == $item["content_type"]) {
?>
		<tr>
			<th></th>
			<td style="text-align:justify;">
				<input style="width:96px;" type="button" value="Unordered List" onclick="generic_insert('item[content]','ul');" />
				<input style="width:96px;" type="button" value="Ordered List" onclick="generic_insert('item[content]','ol');" />
				<input style="width:96px;" type="button" value="Bullet Point" onclick="generic_insert('item[content]','li');" />
				<input style="width:96px;" type="button" value="Insert Email" onclick="generic_insert('item[content]','a', ' href=&quot;mailto:EMAIL ADDRESS&quot');" />
				<br />
				<div>
<?php
foreach (array(
        array("link.gif", "Insert Link", "a", ' href="http://www.WEBSITE.com/" target="_blank"'),
        array("header1.gif", "Heading", "h2", ' class="flagHeader"'),
        array("header2.gif", "Sub-Heading", "h3", ' class="greenHeader"'),
        array("bold.gif", "Bold", "b"),
        array("italic.gif", "Italic", "i"),
        array("underline.gif", "Underline", "u"),
        array("strike.gif", "Strikethru", "s"),
        array("textred.gif", "Red", "span", ' style="color:red;"'),
        array("textgreen.gif", "Green", "span", ' style="color:green;"'),
        array("textblue.gif", "Blue", "span", ' style="color:blue;"'),
        array("textyellow.gif", "Yellow", "span", ' style="color:yellow;"'),
        array("backred.gif", "Red", "span", ' style="background:red;"'),
        array("backgreen.gif", "Green", "span", ' style="background:green;"'),
        array("backblue.gif", "Blue", "span", ' style="background:blue;"'),
        array("backyellow.gif", "Yellow", "span", ' style="background:yellow;"'),
    ) as $row) {
?>
					<input type="image" src="/webadmin/images/wysiwyg/<?= $row[0] ?>" title="<?php hecho($row[1])?>" onclick="<?php hecho("generic_insert(" . jecho("item[content]", 1) . ", " . jecho($row[2], 1) . (("" != $row[3]) ? (", " . jecho($row[3], 1)) : "") . ");return false")?>" style="border:1px solid #aaa;padding:2px;" />
<?php
}
?>
<!-- <input type="image" src="/webadmin/images/wysiwyg/ul.gif" title="Bullet List" onclick="<?php hecho("bullet_insert(" . jecho("item[content]", 1) . ");return false")?>" style="border:1px solid #aaa;padding:2px;" /> -->
				</div>
			</td>
		</tr>
<?php
}
?>
	</table>

	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px;display:none;" id="sectionContent_hideNote">
	Content can be viewed by Clicking "Page Content" above<br />
</div>

<!-- /content.tpl -->

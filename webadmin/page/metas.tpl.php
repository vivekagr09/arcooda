<!-- metas.tpl -->

<?php
/**
 * Meta Tags Template
 *
 * Expects:
 *  $item
 */
$section_name = "item";

?>
<script type="text/javascript">
//<![CDATA[
function metaUpdateTotals()
{
	var title = getValue("item[title]");
	var keys = getValue("item[keywords]");
	var desc = getValue("item[description]");
	var keywords = keys.split(",");
	var len = 0;
	for (var i = 0;i < keywords.length;i++) if (keywords[i] != "") len++;
	setValue("title", title.length);
	setValue("keywords", len);
	setValue("description", desc.length);
}
function metaUpdateTags()
{
	var title = getValue("item[name]");
	setValue("item[title]", title);



	setValue("item[keywords]", formatMETA(title, "keyword"));
	setValue("item[description]", getValue("item[content]").replace(/<[^>]+>/g, ""));
	setValue('item[slug]', generateURLKeys(title));
	metaUpdateTotals();
}
function metaClearTags()
{
	setValue("item[title]", "");
	setValue("item[keywords]", "");
	setValue("item[description]", "");
	metaUpdateTotals();
}
$(function() {
	$(<?php jecho("[name='" . $section_name . "[title]']")?>).change(function() {
		$("#title_length").text($(this).val().length);
	}).change();
	$(<?php jecho("[name='" . $section_name . "[keywords]']")?>).change(function() {
		$("#keywords_count").text(($(this).val() == "") ? "0" : $(this).val().split(/[^0-9a-zA-Z]+/g).length);
	}).change();
	$(<?php jecho("[name='" . $section_name . "[description]']")?>).change(function() {
		$("#description_length").text($(this).val().length);
	}).change();
	$(<?php jecho("[name='" . $section_name . "[slug]']")?>).change(function() {
		$("#slug_length").text($(this).val().length);
	}).change();
});
//]]>
</script>

<?=formatHTMLTitle("<a href=\"#\" onclick=\"return toggleSection(&quot;sectionMetaTags&quot;)\">" . hecho(_("Meta Tags"), 1) . "</a>")?>

<div id="sectionMetaTags">
	<table class="table_fields">
		<tr>
			<th><a href="help.php?action=title&amp;popup=1&amp;TB_iframe=true" target="_blank" class="thickbox"><?php hecho(_("Title"))?></a></th>
			<td style="width:70%;"><input type="text" class="wide" name="<?php hecho($section_name . "[title]")?>" value="<?php hecho($item["title"])?>" maxlength="255" /></td>
			<td style="width:5%;" id="title_length"></td>
		</tr>
		<tr>
			<th><a href="help.php?action=keywords&amp;popup=1&amp;TB_iframe=true" target="_blank" class="thickbox"><?php hecho(_("Keywords"))?></a></th>
			<td style="width:70%;"><textarea onkeypress="textareaAutoSize(this);" class="wide" rows="5" cols="80" name="<?php hecho($section_name . "[keywords]")?>"><?php hecho($item["keywords"])?></textarea></td>
			<td style="width:5%;" id="keywords_count"></td>
		</tr>
		<tr>
			<th><a href="help.php?action=desc&amp;popup=1&amp;TB_iframe=true" target="_blank" class="thickbox"><?php hecho(_("Description"))?></a></th>
			<td style="width:70%;"><textarea onkeypress="textareaAutoSize(this);" class="wide" rows="8" cols="80" name="<?php hecho($section_name . "[description]")?>"><?php hecho($item["description"])?></textarea></td>
			<td style="width:5%;" id="description_length"></td>
		</tr>
		<tr>
			<th><a href="help.php?action=slug&amp;popup=1&amp;TB_iframe=true" target="_blank" class="thickbox"><?php hecho(_("URL Keywords"))?></a></th>
			<td style="width:70%;"><input type="text" class="wide" name="<?php hecho($section_name . "[slug]")?>" value="<?php hecho($item["slug"])?>" maxlength="255" /></td>
			<td style="width:5%;" id="slug_length"></td>
		</tr>


		<tr style="background-color:#FFEECC">
			<th>META Tags Last Changed :</th>
			<td colspan="2"><?=(0 != $item['modified_unix']) ? (date('d/m/Y', $item['modified_unix']) . ', By :' . $item['modified_staff_id']) : 'Never'?></td>
		</tr>


	</table>
	<br />
	<div style="text-align:center;">
		<input id="meta_button_auto" type="button" value="Auto Update Tags" onclick="metaUpdateTags();" />
		<input id="meta_button_clear" type="button" value="Clear" onclick="metaClearTags();" />
	</div>
	<br />
	<br />
</div>
<div id="sectionMetaTags_hideNote" style="background:#fff;text-align:center;padding:8px 8px 8px 8px;display:none;">
	<?php hecho(sprintf(_("%s can be viewed by clicking \"%s\" above"), _("Meta Tags"), _("Meta Tags")))?><br />
</div>

<!-- /metas.tpl -->

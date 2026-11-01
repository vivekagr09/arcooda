<!-- description.tpl.php -->

<?php
$styles_list = $section_handler->styleFromDatabase(array());
foreach ($styles_list as $key => $style) {
    $styles_list[$key]['subject'] = str_replace('[CURRENT_DATE]', date('jS F, Y'), $style['subject']);
}

if ('' != $item['site_id']) {
    $site_ids = explode(',', $item['site_id']);
    foreach ($styles_list as $k => $style) {
        if (!in_array($style['site_id'], $site_ids)) {
            unset($styles_list[$k]);
        }
    }

}
$style_list = array();
foreach ($styles_list as $k => $v) {
    $style_list[$k] = $v['style_description'];
}

?>

<script type="text/javascript">
//<![CDATA[
var styles_list = <?php jecho($styles_list) ?>;
function sectionDescriptionOnChangeStyle()
{
	var new_style_id = getValue(<?php jecho($section_name . '[style_id]') ?>);
	if(styles_list[new_style_id]!=null)
	{
		setValue(<?php jecho($section_name . '[subject]') ?>,styles_list[new_style_id]['subject']);
		//getElement(<?php jecho($section_name . '[description]') ?>).disabled=true;
		//if(sectionListBuilderSelectSite) sectionListBuilderSelectSite(styles_list[new_style_id]['site_id']);
	}
	else
	{
		setValue(<?php jecho($section_name . '[subject]') ?>,'');
		//getElement(<?php jecho($section_name . '[description]') ?>).disabled=false;
		//if(sectionListBuilderSelectSite) sectionListBuilderSelectSite('');
	}
	$('#sectionConfigure').slideUp().load('ajax_config.php?style_id='+new_style_id,function() {
		$('#sectionConfigure').slideDown();
	});
}
//]]>
</script>

<?=formatHTMLTitle('Description')?>

<div id="sectionDescription">
	<table class="wide">
		<tr>
			<td class="left_title" style="width:25%;">Template</td>
			<td style="width:75%;"><select name="<?=$section_name?>[style_id]" onchange="sectionDescriptionOnChangeStyle()">
				<option value="0">Basic Newsletter</option>
				<?=formatHTMLOptions($style_list, $item['style_id'])?>
			</select></td>
		</tr>
		<tr>
			<td class="left_title">Email Subject</td>
			<td><input type="text" class="intext" name="<?=$section_name?>[subject]" value="<?php hecho($item['subject'])?>" /></td>
		</tr>
<!--
		<tr>
			<td class="left_title">General Message</td>
			<td><?=('' != $item['description']) ? ('<iframe style="width:100%;height:256px;" src="index.php?action=view&id=' . $item['id'] . '"></iframe>') : formatHTMLTextarea($section_name . '[description]', (0 == $item['style_id']) ? $item['description'] : '', true, (0 == $item['style_id']) ? '' : ' disabled="disabled"', 5)?></td>
		</tr>
		<tr>
			<td class="left_title">Alternative Text</td>
			<td><?=formatHTMLTextarea($section_name . '[alt_text]', $item['alt_text'], true, '', ('' == $item['alt_text']) ? 5 : 12)?></td>
		</tr>
-->
		<tr>
			<td class="left_title">Short Description</td>
			<td><?=formatHTMLTextarea($section_name . '[short_desc]', $item['short_desc'], true, "", 5)?></td>
		</tr>
	</table>
	<br /><br />
</div>

<div id="sectionDescription_hideNote" style="display:none;">
	<br /><br />
</div>

<!-- /description.tpl.php -->

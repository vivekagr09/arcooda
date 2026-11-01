<!-- config_0.tpl -->

<?php
include 'common_0.inc.php';
foreach($sections as $k => $v) if (!isset($item_settings[$k])) $item_settings[$k] = $v['defaults'];
?>

<table class="wide">
	<tr>
		<td class="left_title">Comments / Description</td>
		<td>
			<?php echo formatHTMLTextarea($section_name . '_settings[paragraph][value]', $item_settings['paragraph']['value'], true, " placeholder=\"" . hecho("Dear [FIRST_NAME],\n\nStaff insert text here.", 1) . "\"", 20) ?><br />
			<input style="width:96px;" type="button" value="Insert Link" onclick="javascript:generic_insert('<?php hecho($section_name . '_settings[paragraph][value]') ?>','a',' href=&quot;https://www.WEBSITE.com/&quot; target=&quot;_blank&quot;');" />
			<input style="width:96px;font-weight:bold;" type="button" value="Bold" onclick="javascript:generic_insert('<?php hecho($section_name . '_settings[paragraph][value]') ?>','b');" />
			<input style="width:96px;font-style:italic;" type="button" value="Italic" onclick="javascript:generic_insert('<?php hecho($section_name . '_settings[paragraph][value]') ?>','i');" />
			<input style="width:96px;text-decoration:underline;" type="button" value="Underline" onclick="javascript:generic_insert('<?php hecho($section_name . '_settings[paragraph][value]') ?>','u');" />
		</td>
	</tr>
</table>
<br /><br />

<!-- /config_0.tpl -->

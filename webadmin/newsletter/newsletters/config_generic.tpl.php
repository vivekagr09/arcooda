<!-- config_generic.tpl -->

<?php
/**
 * Generic configuration page
 *
 * Expects:
 *  $style_id
 */

include 'common_' . $style_id . '.inc.php';
if (!count($item_settings)) foreach($sections as $k => $v) if (!isset($item_settings[$k])) $item_settings[$k] = $v['defaults'];
function settings_row($section, $key, $row = array())
{
	global $section_name;
	static $alt = false;
	$main_key = $section_name . '_settings[' . $section . '][items][' . $key . ']';
	ob_start();
?>
<tr id="<?php echo $main_key ?>"<?php echo ($alt = !$alt) ? ' class="alt"' : '' ?>>
	<td style="white-space:nowrap;">
		<a href="#" onclick="settings_row_move('<?php echo $main_key ?>',-1,1);return false"><img src="/webadmin/images/up_arrow.gif" alt="&uarr;" border="0" /></a>
		<a href="#" onclick="settings_row_move('<?php echo $main_key ?>',1,0);return false"><img src="/webadmin/images/down_arrow.gif" alt="&darr;" border="0" /></a>
	</td>
	<td style="white-space:nowrap;"><input size="8" type="text" name="<?php echo $main_key ?>[str]" value="<?php hecho($row['str']) ?>" onchange="settingsItemChange('<?php echo $section ?>',<?php echo $key ?>,'str')" /><input type="button" value="S" onclick="settingsItemSearch('<?php echo $section ?>',getElement('<?php echo $main_key ?>[str]'))" /></td>
	<td>
		<input name="<?php echo $main_key ?>[name]" value="<?php hecho($row['name']) ?>" maxlength="255" size="64" style="width:100%;" />
<?php
	if (!in_array($section, array('top_banner', 'large_banner', 'bottom_banner')))
	{
?>
		<div id="<?php echo $main_key ?>[advanced]"<?php echo ($row['text'] == '') ? ' style="display:none;"' : '' ?>>
			<?php echo formatHTMLTextarea($main_key . '[text]', $row['text'], true) ?>

<?php
		if ($section == 'latest_offers')
		{
?>
			Pricing Position: <select name="<?php hecho($main_key . '[price_position]') ?>" style="width:auto;">
				<option value=""></option>
				<?php echo formatHTMLOptions(array("left" => "Left Column", "right" => "Right Column"), $row['price_position']) ?>
			</select> (Affects 'Detailed' view only)
<?php
		}
?>
		</div>
<?php
	}
?>
	</td>
	<td><input type="button" value="S" onclick="<?php hecho('settingsItemSearch(' . jecho($section, 1) . ', getElement(' . jecho($main_key . '[name]', 1) . '), getElement(' . jecho($main_key . '[str]', 1) . '))') ?>" /></td>
<?php
	if (!in_array($section, array('top_banner', 'large_banner', 'bottom_banner')))
	{
?>
	<td>
		<input type="button" value="+/-" onclick="<?php hecho('$(' . jecho("[id='" . $main_key . "[advanced]']", 1) . ').slideToggle();') ?>" />
		<div id="<?php echo $main_key ?>[advanced]"<?php echo ($row['text'] == '') ? ' style="display:none;"' : '' ?>>
			<input type="button" value="&crarr;" onclick="settingsItemChange('<?php echo $section ?>',<?php echo $key ?>,'text')" />
		</div>
	</td>
<?php
	}
?>
</tr>
<?php
	return ob_get_clean();
}
?>

<script type="text/javascript" src="/jquery/skip_deps&amp;tinymce.js"></script>
<script type="text/javascript">
//<![CDATA[
/*$(function() {
	$("[name='<?php echo $section_name ?>_settings[paragraph][value]']").tinymce({
		"script_url" : '/webadmin/tiny_mce/tiny_mce.js',

		"plugins" : "paste,searchreplace,fullscreen,contextmenu,advlist,autoresize",
		"content_css" : "/css/newsletter_<?php echo $style_id ?>.css",

		"theme" : "advanced",
		"theme_advanced_buttons1" : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,|,fullscreen,|,forecolor,backcolor",
		"theme_advanced_buttons2" : "cut,copy,paste,pastetext,pasteword,selectall,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code",
		"theme_advanced_buttons3" : "",
		"theme_advanced_buttons4" : "",
		"theme_advanced_toolbar_location" : "bottom",
		"theme_advanced_toolbar_align" : "left",
		"theme_advanced_resizing" : true,

		//"forced_root_block" : false,
		//"force_br_newlines" : true,
		//"force_p_newlines" : false,
		"convert_newlines_to_brs" : true
	});
	$("[value='Find Links']").hide();
});*/

var search_references = [];
function settingsItemSearch(section, reference, target)
{
	var url = '';
	var options = {};
	options.order = 'desc';
	switch (section)
	{
	case 'top_banner':
	case 'large_banner':
	case 'bottom_banner':
		url = '/webadmin/banners/';
		options.active = 'y';
		options.order_by = 'id';
		break;
	case 'news':
		url = '/webadmin/news/';
		options.active = 'y';
		options.order_by = 'id';
		break;
	case 'bulk_offers':
		url = '/webadmin/bulk_offers/';
		options.status = 'y';
		break;
	case 'packages':
		url = '/webadmin/package/';
		options.active = 'y';
		break;
	case 'updates':
		url = '/webadmin/updates/';
		options.active = 'y';
		break;
	case 'classified':
		url = '/webadmin/classified/';
		options.active = 'y';
		break;
	case 'wanted':
		url = '/webadmin/wanted/';
		options.active = 'y';
		break;
	case 'forum':
		url = '/webadmin/forum/';
		options.active = 'y';
		break;
	case 'tradeshow':
		url = '/webadmin/tradeshow/';
		options.active = 'y';
		break;
	case 'arcade_machines':
		url = '/webadmin/products/';
		options.active = 'y';
		options.section = 1;
		options.order_by = 'timestamp';
		break;
	case 'arcade_boards':
		url = '/webadmin/products/';
		options.active = 'y';
		options.section = 2;
		options.order_by = 'timestamp';
		break;
	case 'arcade_parts':
		url = '/webadmin/products/';
		options.active = 'y';
		options.section = 3;
		options.order_by = 'timestamp';
		break;
	case 'arcade_merchandise':
		url = '/webadmin/products/';
		options.active = 'y';
		options.section = 4;
		options.order_by = 'timestamp';
		break;
	case 'promotion':
		url = '/webadmin/promotion/';
		options.active = 'y';
		options.on_special = 'y';
		options.order_by = 'timestamp';
		break;
	case 'specials':
		url = '/webadmin/products/';
		options.active = 'y';
		options.special = 'y';
		options.on_special = 'y';
		options.order_by = 'timestamp';
		break;
	case 'latest_offers':
		//options.latest = 'y';
	case 'featured_products':
		url = '/webadmin/products/';
		options.active = 'y';
		options.order_by = 'id';
		break;
	}
	if (url != '')
	{
		options.search_for = $(reference).val();
		options.return_message = count(search_references);
		if (target != null) search_references.push(target);
		else search_references.push(reference);
		window.open(url + "?" + $.param(options));
	}
}
function settingsItemChange(section, row_id, field)
{
	var url = '';
	var options = {};
	options.return_section = section;
	options.return_row_id = row_id;
	options.id = $("[name='<?php echo $section_name ?>_settings[" + section + "][items][" + row_id + "][str]']").val();
	switch (section)
	{
	case 'top_banner':
	case 'large_banner':
	case 'bottom_banner':
		url = '/webadmin/banners/ajax_request.php';
		options.action = 'bannerGetValue';
		options.field = 'alt';
		break;
	case 'news':
		url = '/webadmin/news/ajax_request.php';
		options.action = 'newsGetValue';
		options.field = 'news_title';
		if (field == 'text') options.field = 'long_description';
		break;
	case 'bulk_offers':
		url = '/webadmin/bulk_offers/ajax_request.php';
		options.action = 'bulkGetValue';
		options.field = 'title';
		break;
	case 'packages':
		url = '/webadmin/package/ajax_request.php';
		options.action = 'packageGetValue';
		//options.field='product_name';
		break;
	case 'updates':
		url = '/webadmin/updates/ajax_request.php';
		options.action = 'updatesGetValue';
		//options.field='product_name';
		break;
	case 'classified':
		url = '/webadmin/classified/ajax_request.php';
		options.action = 'classifiedGetValue';
		//options.field='product_name';
		break;
	case 'wanted':
		url = '/webadmin/wanted/ajax_request.php';
		options.action = 'wantedGetValue';
		//options.field='product_name';
		break;
	case 'forum':
		url = '/webadmin/forums/ajax_request.php';
		options.action = 'forumGetValue';
		//options.field='product_name';
		break;
	case 'tradeshow':
		url = '/webadmin/tradeshow/ajax_request.php';
		options.action = 'tradeshowGetValue';
		options.field='tradeshow_name';
		if (field == 'text') options.field = 'short_desc';
		break;
	case 'arcade_machines':
	case 'arcade_boards':
	case 'arcade_parts':
	case 'arcade_merchandise':
	case 'specials':
		url = '/webadmin/products/ajax_request.php';
		options.action = 'productGetValue';
		options.field = 'product_name';
		if (field == 'text') options.field = 'long_desc';
		break;
	case 'promotion':
		url = '/webadmin/promotion/ajax_request.php';
		options.action = 'promotionGetValue';
		options.field = 'name';
		if (field == 'text') options.field = 'description';
		break;
	case 'latest_offers':
	case 'featured_products':
		url = '/webadmin/products/ajax_request.php';
		options.action = 'productGetValue';
		options.field = 'product_name';
		if (field == 'text') options.field = 'long_desc';
		break;
	}
	if (url != '') $.getJSON(url, options, function (data, textStatus)
	{
		if (textStatus == 'success')
		{
			var section = data.return_section;
			var row_id = data.return_row_id;
			switch (field)
			{
			default:
			case 'str':
				$("[name='<?php echo $section_name ?>_settings[" + section + "][items][" + row_id + "][name]']").val(data.result);
				$("[name='<?php echo $section_name ?>_settings[" + section + "][items][" + row_id + "][text]']").val("").keyup();
				$("[id='<?php echo $section_name ?>_settings[" + section + "][items][" + row_id + "][advanced]']").slideUp();
				break;
			case 'text':
				$("[name='<?php echo $section_name ?>_settings[" + section + "][items][" + row_id + "][text]']").val(data.result).keyup();
				break;
			}
		}
		else alert('There was a problem fetching that request: '.textStatus);
	});
}
function settingsAutoFillAll()
{
	var sections = <?php jecho(array_keys($sections)) ?>;
	for (var i = 0; i < sections.length; i++) settingsAutoFill(sections[i]);
}
function settingsAutoFill(section)
{
	var url = '';
	var options = {};
	options.return_section = section;
	options.return_limit = 20;
	options.return_desc = 1;
	options.return_qty = $("[name='<?php echo $section_name ?>_settings[" + section + "][qty]']").val();
	if (options.return_qty > 0 && options.return_qty < 20) options.return_limit = options.return_qty;
	switch (section)
	{
	case 'top_banner':
	case 'large_banner':
		url = '/webadmin/banners/ajax_request.php';
		//options.banner_type=[2,3,4,5];
		options.action = 'bannerSearch';
		options.active = 'y';
		options.return_order = 'id';
		if(getValue('newsletter_settings[large_banner][option]')=='wide'){
			options.banner_type=[27];
		}else{
			options.banner_type=[28];
		}
		break;
	case 'bottom_banner':
		url = '/webadmin/banners/ajax_request.php';
		if(getValue('newsletter_settings[large_banner][option]')=='wide'){
			options.banner_type=[27];
		}else{
			options.banner_type=[28];
		}
		//
		options.action = 'bannerSearch';
		options.return_order = 'id';
		console.log(options);
		break;
	case 'news':
		url = '/webadmin/news/ajax_request.php';
		options.action = 'newsSearch';
		options.active = 'y';
		options.return_order = 'id';
		break;
	case 'bulk_offers':
		url = '/webadmin/bulk_offers/ajax_request.php';
		options.action = 'bulkSearch';
		options.status = 'y';
		options.return_order = 'id';
		break;
	case 'packages':
		url = '/webadmin/package/ajax_request.php';
		options.action = 'packageSearch';
		options.active = 'y';
		//options.return_order='id';
		break;
	case 'updates':
		url = '/webadmin/updates/ajax_request.php';
		options.action = 'updatesSearch';
		options.active = 'y';
		//options.return_order='id';
		break;
	case 'classified':
		url = '/webadmin/classified/ajax_request.php';
		options.action = 'classifiedSearch';
		options.active = 'y';
		//options.return_order='id';
		break;
	case 'wanted':
		url = '/webadmin/wanted/ajax_request.php';
		options.action = 'wantedSearch';
		options.active = 'y';
		//options.return_order='id';
		break;
	case 'forum':
		url = '/webadmin/forum/ajax_request.php';
		options.action = 'forumSearch';
		options.active = 'y';
		//options.return_order='id';
		break;
	case 'tradeshow':
		url = '/webadmin/tradeshow/ajax_request.php';
		options.action = 'tradeshowSearch';
		options.active = 'y';
		//options.return_order='id';
		break;
	case 'arcade_machines':
		url = '/webadmin/products/ajax_request.php';
		options.action = 'productSearch';
		options.active = 'y';
		options.return_order = 'timestamp';
		options.section = 1;
		break;
	case 'arcade_boards':
		url = '/webadmin/products/ajax_request.php';
		options.action = 'productSearch';
		options.active = 'y';
		options.return_order = 'timestamp';
		options.section = 2;
		break;
	case 'arcade_parts':
		url = '/webadmin/products/ajax_request.php';
		options.action = 'productSearch';
		options.active = 'y';
		options.return_order = 'timestamp';
		options.section = 3;
		break;
	case 'arcade_merchandise':
		url = '/webadmin/products/ajax_request.php';
		options.action = 'productSearch';
		options.active = 'y';
		options.return_order = 'timestamp';
		options.section = 4;
		break;
	case 'promotion':
		url = '/webadmin/promotion/ajax_request.php';
		options.action = 'promotionSearch';
		options.active = 'y';
		options.on_special = 'y';
		options.return_order = 'timestamp';
		break;
	case 'specials':
		url = '/webadmin/products/ajax_request.php';
		options.action = 'productSearch';
		options.active = 'y';
		options.special = 'y';
		options.on_special = 'y';
		options.return_order = 'timestamp';
		break;
	case 'latest_offers':
		options.latest = 'y';
	case 'featured_products':
		url = '/webadmin/products/ajax_request.php';
		options.action = 'productSearch';
		options.active = 'y';
		options.return_order = 'id';
		break;
	}
	if (url != '') $.getJSON(url, options, function (data, textStatus)
	{
		if (textStatus == 'success')
		{
			var section = data.return_section;
			data = data.result;
			var config_key = "<?php echo $section_name ?>_settings[" + section + "]";
			var $qty_elems = $("[name='" + config_key + "[qty]']");
			configToggleOption(section, data.length > 0);
			if (data.return_qty > 0)
			{
				$qty_elems.val(data.length);
				var new_option = 'full';
				if (section == 'news' && (data.length == 2)) new_option = 'brief';
				else if (section != 'news' && (data.length % 5) == 0) new_option = 'brief';
				else if ((data.length % 2) == 0) new_option = 'summary';
				$("[name='" + config_key + "[option]']").val(new_option);
				configQtyChange(section, $qty_elems[0]);
			}
			var $item_table = $("table[id='<?php echo $section_name ?>_settings_table[" + section + "]']");
			if ($item_table.length > 0)
			{
				var $the_rows = $("tr[id^='<?php echo $section_name ?>_settings[" + section + "][items][']", $item_table);
				var filled_vals = [];
				var to_set_vals = [];
				for (var i = 0; i < $the_rows.length; i++)
				{
					var $elem = $("[name='<?php echo $section_name ?>_settings[" + section + "][items][" + $the_rows[i].id.split("[")[3].split("]")[0] + "][str]']");
					if(section == 'large_banner') {
						to_set_vals.push($elem);
					} else {
						if ($elem.val().length > 0) filled_vals.push($elem.val());
						else to_set_vals.push($elem);
					}
					
				}
				for (var i = 0, j = 0; i < data.length, j < to_set_vals.length; i++)
				{
					var found = false;
					for (var k = 0;k < filled_vals.length;k++) if (filled_vals[k] == data[i]) found = true;
					if (!found)
					{
						to_set_vals[j].val(data[i]).change();
						j++;
					}
				}
			}
		}
		else alert('There was a problem fetching that request: '.textStatus);
	});
}
function setProductData(data)
{
	var reference = search_references[data.return_message];
	if (data.id) $(reference).val(data.product_str).change();
}
function setTradeshowData(data)
{
	var reference = search_references[data.return_message];
	if (data.id) $(reference).val(data.id).change();
}
function setNewsData(data)
{
	var reference = search_references[data.return_message];
	if (data.id) $(reference).val(data.id).change();
}
function setBannerData(data)
{
	var reference = search_references[data.return_message];
	if (data.id) $(reference).val(data.id).change();
}
function setBulkData(data)
{
	var reference = search_references[data.return_message];
	if (data.id) $(reference).val(data.id).change();
}
function setPackageData(data)
{
	var reference = search_references[data.return_message];
	if (data.id) $(reference).val(data.id);
}
function settings_row_move(row_id, direction, limit)
{
	var $the_row = $("tr[id='" + row_id + "']");
	var $siblings = $the_row.parent().children();
	var position = 0;
	for (var i = 0; i < $siblings.length; i++) if ($siblings[i].id == $the_row[0].id) position = i;
	if (direction < 0 && position + direction >= limit)
	{
		$the_row.insertBefore($siblings.eq(position + direction));
	}
	else if (direction > 0 && position + direction <= $siblings.length - limit)
	{
		$the_row.insertAfter($siblings.eq(position + direction));
	}
	var alt = false;
	$the_row.parent().children().each(function ()
	{
		$(this).toggleClass('alt', alt = !alt);
	});
}
function configShowExamples()
{
	tb_show('', <?php jecho('/images/newsletter_' . $style_id . '_examples.jpg') ?>);
}
function configShowExample(section)
{
	var $option = $("[name='<?php echo $section_name ?>_settings[" + section + "][option]']");
	if ($option.length > 0)
	{
		var content = $option.val();
		tb_show(content, <?php jecho('/images/newsletter_' . $style_id . '_') ?> + content + '_example.jpg');
	}
}
function configToggleOption(option,value)
{
	var config_key = <?php jecho($section_name . '_settings[') ?> + option + ']';
	var rowb_key = <?php jecho($section_name . '_settings_rowb[') ?> + option + ']';
	$("[name='" + config_key + "[enable]']").attr("checked", (value) ? 'checked' : '');
	if (value) $("[id='" + rowb_key + "']").fadeIn();
	else $("[id='" + rowb_key + "']").fadeOut();
}
function configTab(option,value)
{
	var config_key = <?php jecho($section_name . '_settings[') ?> + option + ']';
	var $enable_elem = $("[name='" + config_key + "[enable]']");
	if (!$enable_elem.attr("checked"))
	{
		$enable_elem.attr("checked", "checked");
		$enable_elem.change();
	}
}
function configQtyChange(option, elem)
{
	var config_key=<?php jecho($section_name . '_settings[') ?> + option + ']';
	var new_val=parseInt(elem.value);

	$("[name='" + elem.name + "']").not(elem).val(new_val);

	var $item_table=$("table[id='<?php echo $section_name ?>_settings_table[" + option + "]']");
	if ($item_table.length>0)
	{
		for(var i=0;i<new_val;i++)
		{
			var $the_row=$("tr[id='<?php echo $section_name ?>_settings[" + option + "][items][" + i + "]']",$item_table);
			if ($the_row.length==0)
			{
				var settings_rows={};
<?php
foreach($sections as $k => $v)
{
?>
				settings_rows[<?php jecho($k) ?>] = <?php echo str_replace('[COUNT]', '" + i + "', jecho(settings_row($k, '[COUNT]'), 1)) ?>;
<?php
}
?>
				$item_table.append($(settings_rows[option]));
			}
		}
		var $the_rows=$("tr[id^='<?php echo $section_name ?>_settings[" + option + "][items][']",$item_table);
		for(var i=0;i<$the_rows.length;i++) $the_rows.eq(i).toggleClass('alt',(i&1)==1);
		for(var i=0;i<$the_rows.length;i++) if(i>=new_val) $the_rows.eq(i).remove();
	}
}
function find_paragraph_urls()
{
	var $elem = $("[name='<?php echo $section_name ?>_settings[paragraph][value]']");
	var text = $elem.val();

	var regexp = /([ \t\n](http|www\.)[^ "<\t\n]+)/;
	var temp = text;
	while((temp = text.replace(regexp, function(match) {
		var trim_left = 1;
		var trim_right = 0;
		if (match.substr(match.length - 1, 1) == ".") trim_right = 1;

		var url = match.substr(trim_left, match.length - (trim_left + trim_right));
		if (url.substr(0, 4) != "http") url = "https://" + url;
		if (url.split("/").length == 3) url += "/";

		return match.substr(0, trim_left) + '<a href="' + url + '" target="_blank">' + match.substr(trim_left, match.length - (trim_left + trim_right)) + "</a>" + match.substr(match.length - trim_right);
	})) != text) text = temp;
	$elem.val(text).keyup();
}
//]]>
</script>

<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;">General Message</td>
		<td><?php echo formatHTMLTextarea($section_name . '_settings[paragraph][value]', $item_settings['paragraph']['value'], true) ?></td>
	</tr>
	<tr>
		<td class="left_title"></td>
		<td style="text-align:center;"><input onclick="find_paragraph_urls()" type="button" value="Find Links" /></td>
	</tr>
</table>
<br /><br />

<p style="text-align:center;">
	<input type="button" value="Auto-Fill" onclick="settingsAutoFillAll()" />
	<input onclick="configShowExamples()" type="button" value="Layout Examples" />
</p>

<table class="wide">
	<tr>
		<th>Order</th>
		<th>Section</th>
		<th>Qty</th>
		<th>Options</th>
	</tr>
<?php
foreach($item_settings as $k => $v) if ($k != 'paragraph')
{
	$config_key = $section_name . '_settings[' . $k . ']';
	$section = $sections[$k];
?>
	<tr id="<?php echo $section_name ?>_settings_rowa[<?php echo $k ?>]" style="background-color:#edc;">
		<td style="white-space:nowrap;">
			<input type="checkbox" name="<?php echo $config_key ?>[enable]" value="y"<?php echo ($v['enable'] == 'y') ? ' checked="checked"' : '' ?> onchange="<?php hecho('configToggleOption(' . jecho($k, 1) . ', this.checked)') ?>" />
			<a href="#" onclick="settings_row_move('<?php echo $section_name ?>_settings_rowa[<?php echo $k ?>]',-2,1);settings_row_move('<?php echo $section_name ?>_settings_rowb[<?php echo $k ?>]',-2,1);return false"><img src="/webadmin/images/up_arrow.gif" alt="&uarr;" border="0" /></a>
			<a href="#" onclick="settings_row_move('<?php echo $section_name ?>_settings_rowb[<?php echo $k ?>]',2,0);settings_row_move('<?php echo $section_name ?>_settings_rowa[<?php echo $k ?>]',2,0);return false"><img src="/webadmin/images/down_arrow.gif" alt="&darr;" border="0" /></a>
		</td>
		<td style="white-space:nowrap;font-weight:bold;"><?php hecho($section['title']) ?></td>
		<td>
<?php
	if ($section['options'] != '')
	{
?>
			<input type="text" name="<?php echo $config_key ?>[qty]" value="<?php echo $v['qty'] ?>" onchange="<?php hecho('configQtyChange(' . jecho($k, 1) . ', this)') ?>" size="2" style="text-align:right;" />
<?php
	}
?>
		</td>
		<td style="width:100%;">
<?php
	if ($section['options'] != '')
	{
?>
			<select name="<?php echo $config_key ?>[option]" onchange="<?php hecho('configTab(' . jecho($k, 1) . ', $(this).val())') ?>" style="width:auto;">
				<option value=""></option>
<?php
		foreach(explode('_', $section['options']) as $option)
		{
?>
				<option value="<?php hecho($option) ?>" <?php echo ($v['option'] == $option) ? ' selected="selected"' : '' ?>><?php hecho(ucwords($option)) ?></option>
<?php
		}
?>
			</select>
<?php
	}
?>
			<input type="button" value="Auto-Fill" onclick="<?php hecho("settingsAutoFill(" . jecho($k, 1) . ")") ?>" />
<?php
	if ($section['options'] != '')
	{
		$count = 0;
		foreach(explode('_', $section['options']) as $option) if (is_file($_SERVER["DOCUMENT_ROOT"] . "/webadmin/newsletter/images/style_" . $style_id . "_" . $option . ".jpg"))
		{
			$count++;
?>
			<a style="float:right;margin:2px 2px 2px 2px;" href="#" onmouseover="<?php hecho('tt_in(' . jecho('<img src="/webadmin/newsletter/images/style_' . $style_id . '_' . $option . '.jpg" height="512" width="378" />', 1) . ')') ?>" onmouseout="tt_out()" onclick="return false"><?php hecho(ucwords($option)) ?></a>
<?php
		}
		if ($count)
		{
?>
			<span style="float:right;margin:2px 2px 2px 2px;">Examples:</span>
<?php
		}
	}
?>
		</td>
	</tr>
	<tr id="<?php echo $section_name ?>_settings_rowb[<?php echo $k ?>]"<?php echo ($v['enable'] == 'y') ? '' : ' style="display:none;"' ?>><td colspan="5"<?php echo ($k == 'packages') ? ' class="unfinished"' : '' ?>>
<?php
	switch ($k)
	{
	case 'header':
		$header_dir = $_SERVER["DOCUMENT_ROOT"] . "/images/newsletter_" . $style_id . "/";
?>
		<table class="wide">
			<tr><td>
				<div style="float:left;margin:1px;border:1px solid #ccc;padding:1px;"><input id="header_0" type="radio" name="<?php hecho($section_name . '_settings[' . $k . '][value]') ?>" value="<?php hecho($item_settings[$k]['value']) ?>" checked="checked" /><label for="header_0"><img style="vertical-align:middle;" width="536" src="<?php hecho(substr($header_dir . $item_settings[$k]['value'], strlen($_SERVER["DOCUMENT_ROOT"]))) ?>" /></label></div><br />

				<a id="header_others_hideNote" href="#header_others" onclick="<?php hecho('toggleSection("header_others");return false') ?>">Show Others</a>
			</td></tr>
			<tr><td id="header_others" style="display:none;">
<?php
		foreach(glob($header_dir . "logo*.jpg", GLOB_BRACE) as $v) if (preg_match('@/logo[0-9]+\\.jpg$@', $v))
		{
?>
				<div style="float:left;margin:1px;border:1px solid #ccc;padding:1px;"><input id="<?php hecho("header_" . basename($v)) ?>" type="radio" name="<?php hecho($section_name . '_settings[' . $k . '][value]') ?>" value="<?php hecho(basename($v)) ?>" /><label for="<?php hecho("header_" . basename($v)) ?>"><img style="vertical-align:middle;" width="256" src="<?php hecho(substr($v, strlen($_SERVER["DOCUMENT_ROOT"]))) ?>" /></label></div>
<?php
		}
?>
			</td></tr>
		</table>
<?php
		break;

	case 'disclaimer':
		$options = array();
		$values = array();
		foreach($section_handler->templateList() as $row)
		{
			$options[$row['template_id']] = $row['template_name'];
			$values[$row['template_id']] = $row['template_text'];
		}
?>
		<script type="text/javascript">
//<![CDATA[
function setEmailTemplate(target, template_id)
{
	var templates = <?php jecho($values) ?>;
	$("[name='" + target + "']").val(templates[template_id]).keyup();
}
//]]>
		</script>
		<table class="wide">
			<tr><td>
				<select onchange="<?php hecho('setEmailTemplate(' . jecho($section_name . '_settings[' . $k . '][value]', 1) . ', this.value)') ?>" style="width:80%;">
					<option value=""></option>
					<?php echo formatHTMLOptions($options, -1) ?>
				</select>
				<a href="newsletter_options.php?section=email_template" target="_blank">Add / Edit</a>
			</td></tr>
			<tr><td><?php echo formatHTMLTextarea($section_name . '_settings[' . $k . '][value]', $item_settings[$k]['value'], true) ?></td></tr>
		</table>
<?php
		break;

	case 'top_banner':
	case 'large_banner':
	case 'bottom_banner':
	case 'news':
	case 'bulk_offers':
	case 'packages':
	case 'arcade_machines':
	case 'arcade_boards':
	case 'arcade_parts':
	case 'arcade_merchandise':
	case 'promotion':
	case 'specials':
	case 'latest_offers':
	case 'featured_products':
	case 'updates':
	case 'classified':
	case 'wanted':
	case 'forum':
	case 'tradeshow':
	default:
?>
		<table style="width:95%;margin:0 16px;" id="<?php echo $section_name ?>_settings_table[<?php echo $k ?>]">
			<tr>
				<th style="width:64px;">Order</th>
				<th>ID</th>
				<th style="width:100%;">Name</th>
				<th></th>
				<th></th>
			</tr>
<?php
		while (count($v['items']) < $v['qty']) $v['items'][] = array();
		while (count($v['items']) > $v['qty']) array_pop($v['items']);
		$count = 0;
		if (is_array($v['items'])) foreach($v['items'] as $k2 => $row) echo settings_row($k, $count++, $row);
?>
		</table>
<?php
		break;
	}
?>
	</td></tr>
<?php
}
?>
</table>
<br /><br />

<!-- /config_generic.tpl -->

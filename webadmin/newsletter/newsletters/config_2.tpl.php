<!-- config_2.tpl -->

<?php
include 'common_2.inc.php';
foreach($sections as $k => $v) if (!isset($item_settings[$k])) $item_settings[$k] = $v['defaults'];
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
		<!-- <input type="button" value="&uarr;" onclick="settings_row_move('<?php echo $main_key ?>',-1,1)" /> -->
		<a href="#" onclick="settings_row_move('<?php echo $main_key ?>',1,0);return false"><img src="/webadmin/images/down_arrow.gif" alt="&darr;" border="0" /></a>
		<!-- <input type="button" value="&darr;" onclick="settings_row_move('<?php echo $main_key ?>',1,0)" /> -->
	</td>
	<td style="white-space:nowrap;"><input size="8" type="text" name="<?php echo $main_key ?>[str]" value="<?php hecho($row['str']) ?>" onchange="settingsItemChange('<?php echo $section ?>',<?php echo $key ?>,'str')" /><input type="button" value="S" onclick="settingsItemSearch('<?php echo $section ?>',getElement('<?php echo $main_key ?>[str]'))" /></td>
	<td><span name="<?php echo $main_key ?>[name]"><?php hecho($row['name']) ?></span></td>
</tr>
<?php
	return ob_get_clean();
}
?>

<script type="text/javascript">
//<![CDATA[
var search_references=[];
function settingsItemSearch(section,reference)
{
	var url='';
	var options={};
	options.order='desc';
	switch(section)
	{
	case 'large_banner':
		url='/webadmin/banners/';
		//options.banner_type=[2,3,4,5];
		options.order_by='id';
		break;
	case 'bottom_banner':
		url='/webadmin/banners/';
		//options.banner_type=[6,7];
		options.order_by='id';
		break;
	case 'news':
		url='/webadmin/news/';
		options.active='y';
		options.order_by='id';
		break;
	case 'packages':
		url='/webadmin/package/';
		options.active='y';
		//options.order_by='id';
		break;
	case 'arcade_parts':
		url='/webadmin/products/';
		options.active='y';
		options.section=3;
		options.order_by='timestamp';
		break;
	case 'arcade_boards':
		url='/webadmin/products/';
		options.active='y';
		options.section=2;
		options.order_by='timestamp';
		break;
	case 'specials':
		url='/webadmin/products/';
		options.active='y';
		options.special='y';
		options.order_by='timestamp';
		break;
	case 'latest_offers':
		options.latest='y';
	case 'featured_products':
		url='/webadmin/products/';
		options.active='y';
		options.order_by='id';
		break;
	}
	if(url!='')
	{
		options.search_for=$(reference).val();
		options.return_message=count(search_references);
		search_references.push(reference);
		window.open(url + "?" + $.param(options));
	}
}
function settingsItemChange(section,row_id,field)
{
	var url='';
	var options={};
	options.return_section = section;
	options.return_row_id = row_id;
	options.id = $("[name='<?php echo $section_name ?>_settings[" + section + "][items][" + row_id + "][str]']").val();
	switch(section)
	{
	case 'large_banner':
	case 'bottom_banner':
		url='/webadmin/banners/ajax_request.php';
		options.action='bannerGetValue';
		options.field='name';
		break;
	case 'news':
		url='/webadmin/news/ajax_request.php';
		options.action='newsGetValue';
		options.field='news_title';
		break;
	case 'packages':
		url='/webadmin/package/ajax_request.php';
		options.action='packageGetValue';
		//options.field='product_name';
		break;
	case 'arcade_parts':
	case 'arcade_boards':
	case 'specials':
		url='/webadmin/products/ajax_request.php';
		options.action='productGetValue';
		options.field='product_name';
		break;
	case 'latest_offers':
	case 'featured_products':
		url='/webadmin/products/ajax_request.php';
		options.action='productGetValue';
		options.field='product_name';
		break;
	}
	if(url!='') $.getJSON(url,options,function(data, textStatus) {
		if(textStatus == 'success')
		{
			var section = data.return_section;
			var row_id = data.return_row_id;
			options.id = $("[name='<?php echo $section_name ?>_settings[" + section + "][items][" + row_id + "][name]']").text(data.result);
		}
		else alert('There was a problem fetching that request: '.textStatus);
	});
}
function settingsAutoFillAll()
{
	var sections=<?php jecho(array_keys($sections)) ?>;
	for(var i=0;i<sections.length;i++) settingsAutoFill(sections[i]);
}
function settingsAutoFill(section)
{

	var url='';
	var options={};
	options.return_section=section;
	options.return_limit=20;
	options.return_desc=1;
	options.return_qty=$("[name='<?php echo $section_name ?>_settings[" + section + "][qty]']").val();
	if (options.return_qty>0 && options.return_qty<20) options.return_limit=options.return_qty;
	switch(section)
	{
	case 'large_banner':
		url='/webadmin/banners/ajax_request.php';
		//options.banner_type=[2,3,4,5];
		options.action='bannerSearch';
		options.return_order='id';
		break;
	case 'bottom_banner':
		url='/webadmin/banners/ajax_request.php';
		//options.banner_type=[6,7];
		options.action='bannerSearch';
		options.return_order='id';
		break;
	case 'news':
		url='/webadmin/news/ajax_request.php';
		options.action='newsSearch';
		options.active='y';
		options.return_order='id';
		break;
	case 'packages':
		url='/webadmin/package/ajax_request.php';
		options.action='packageSearch';
		options.active='y';
		//options.return_order='id';
		break;
	case 'arcade_parts':
		url='/webadmin/products/ajax_request.php';
		options.action='productSearch';
		options.active='y';
		options.return_order='timestamp';
		options.section=3;
		break;
	case 'arcade_boards':
		url='/webadmin/products/ajax_request.php';
		options.action='productSearch';
		options.active='y';
		options.return_order='timestamp';
		options.section=2;
		break;
	case 'specials':
		url='/webadmin/products/ajax_request.php';
		options.action='productSearch';
		options.active='y';
		options.special='y';
		options.return_order='timestamp';
		break;
	case 'latest_offers':
		options.latest='y';
	case 'featured_products':
		url='/webadmin/products/ajax_request.php';
		options.action='productSearch';
		options.active='y';
		options.return_order='id';
		break;
	}
	if(url!='') $.getJSON(url,options,function(data, textStatus) {
		if(textStatus == 'success')
		{
			var section = data.return_section;
			data = data.result;

			var config_key = "<?php echo $section_name ?>_settings[" + section + "]";
			var $qty_elems=$("[name='" + config_key + "[qty]']");
			var $option_elems=$("[name='" + config_key + "[option]']");

			configToggleOption(section,data.length>0);

			if(data.return_qty>0)
			{
				$qty_elems.val(data.length);

				var new_option='full';
				if(section == 'news' && (data.length==2)) new_option='brief';
				else if(section != 'news' && (data.length%5)==0) new_option='brief';
				else if((data.length%2)==0) new_option='summary';
				$option_elems.each(function() {
					this.checked=($(this).val() == new_option);
				});
				configQtyChange(section,$qty_elems[0]);
			}

			var $item_table=$("table[id='<?php echo $section_name ?>_settings_table[" + section + "]']");
			if ($item_table.length>0)
			{
				var $the_rows=$("tr[id^='<?php echo $section_name ?>_settings[" + section + "][items][']",$item_table);
				for(var i=0;i<$the_rows.length;i++) $("[name='<?php echo $section_name ?>_settings[" + section + "][items][" + $the_rows[i].id.split("[")[3].split("]")[0] + "][str]']").val(data[i]).change();
			}
		}
		else alert('There was a problem fetching that request: '.textStatus);
	});
}
function setProductData(data)
{
	var reference=search_references[data.return_message];
	if (data.id) $(reference).val(data.id).change();
}
function setNewsData(data)
{
	var reference=search_references[data.return_message];
	if (data.id) $(reference).val(data.id).change();
}
function setBannerData(data)
{
	var reference=search_references[data.return_message];
	if (data.id) $(reference).val(data.id).change();
}
function setPackageData(data)
{
	var reference=search_references[data.return_message];
	if (data.id) $(reference).val(data.id);
}
function settings_row_move(row_id,direction,limit)
{
	var $the_row=$("tr[id='" + row_id + "']");
	var $siblings=$the_row.parent().children();
	var position=0;
	for (var i=0; i<$siblings.length; i++) if ($siblings[i].id == $the_row[0].id) position=i;
	if (direction < 0 && position + direction >= limit)
	{
		$the_row.insertBefore($siblings.eq(position + direction));
	}
	else if (direction > 0 && position + direction <= $siblings.length - limit)
	{
		$the_row.insertAfter($siblings.eq(position + direction));
	}
	var alt=false;
	$the_row.parent().children().each(function() {
		$(this).toggleClass('alt',alt=!alt);
	});
}
function configShowExamples()
{
	tb_show('','/images/newsletter_2_examples.jpg');
	//window.open('/images/newsletter_2_examples.jpg');
}
function configShowExample(section)
{
	var $option=$(":checked[name='<?php echo $section_name ?>_settings[" + section + "][option]']");
	if ($option.length>0)
	{
		var content=$option.val();
		tb_show(content,'/images/newsletter_2_' + content + '_example.jpg');
	}
}
function configToggleOption(option,value)
{
	var config_key=<?php jecho($section_name . '_settings[') ?> + option + ']';
	var rowb_key=<?php jecho($section_name . '_settings_rowb[') ?> + option + ']';
	$("[name='" + config_key + "[enable]']").attr("checked",(value)?'checked':'');
	if(value) $("[id='" + rowb_key + "']").fadeIn();
	else $("[id='" + rowb_key + "']").fadeOut();
}
function configTab(option,value)
{
	var config_key=<?php jecho($section_name . '_settings[') ?> + option + ']';
	var $qty_elem=$("[name='" + config_key + "[qty]']");
	switch(value)
	{
	case 'brief':
		if(option == 'news') $qty_elem.val("2");
		else $qty_elem.val("5");
		$qty_elem.change();
		break;
	case 'summary':
		$qty_elem.val("2");
		$qty_elem.change();
		break;
	case 'full':
		$qty_elem.val("1");
		$qty_elem.change();
		break;
	}
	var $enable_elem=$("[name='" + config_key + "[enable]']");
	if (!$enable_elem.attr("checked"))
	{
		$enable_elem.attr("checked","checked");
		$enable_elem.change();
	}
}
function configQtyChange(option,elem)
{
	var config_key=<?php jecho($section_name . '_settings[') ?> + option + ']';
	var $option_elem=$(":checked[name='" + config_key + "[option]']");
	var new_val=parseInt(elem.value);

	switch($option_elem.val())
	{
	case 'brief':
		if (option == 'news') $("[name='" + elem.name + "']").val(2);
		else $("[name='" + elem.name + "']").val(new_val - (new_val % 5));
		break;
	case 'summary':
		$("[name='" + elem.name + "']").val(new_val - (new_val % 2));
		break;
	default:
		$("[name='" + elem.name + "']").not(elem).val(new_val);
	}

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
				settings_rows[<?php jecho($k) ?>] = <?php echo str_replace('[COUNT]', "'+i+'", formatJSString(settings_row($k, '[COUNT]'))) ?>;
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
//]]>
</script>

<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;">General Message</td>
		<td><?php echo formatHTMLTextarea($section_name . '_settings[paragraph][value]', $item_settings['paragraph']['value'], true) ?></td>
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
		<th>Actions</th>
	</tr>
<?php
foreach($item_settings as $k => $v) if ($k != 'paragraph')
{
	$config_key = $section_name . '_settings[' . $k . ']';
	$section = $sections[$k];
?>
	<tr id="<?php echo $section_name ?>_settings_rowa[<?php echo $k ?>]" style="background-color:#edc;">
		<td style="white-space:nowrap;">
			<input type="checkbox" name="<?php echo $config_key ?>[enable]" value="y"<?php echo ($v['enable'] == 'y') ? ' checked="checked"' : '' ?> onchange="configToggleOption(<?php jecho($k) ?>,this.checked)" />
			<a href="#" onclick="settings_row_move('<?php echo $section_name ?>_settings_rowa[<?php echo $k ?>]',-2,1);settings_row_move('<?php echo $section_name ?>_settings_rowb[<?php echo $k ?>]',-2,1);return false"><img src="/webadmin/images/up_arrow.gif" alt="&uarr;" border="0" /></a>
			<!-- <input type="button" value="&uarr;" onclick="settings_row_move('<?php echo $section_name ?>_settings_rowa[<?php echo $k ?>]',-2,1);settings_row_move('<?php echo $section_name ?>_settings_rowb[<?php echo $k ?>]',-2,1)" /> -->
			<a href="#" onclick="settings_row_move('<?php echo $section_name ?>_settings_rowb[<?php echo $k ?>]',2,0);settings_row_move('<?php echo $section_name ?>_settings_rowa[<?php echo $k ?>]',2,0);return false"><img src="/webadmin/images/down_arrow.gif" alt="&darr;" border="0" /></a>
			<!-- <input type="button" value="&darr;" onclick="settings_row_move('<?php echo $section_name ?>_settings_rowb[<?php echo $k ?>]',2,0);settings_row_move('<?php echo $section_name ?>_settings_rowa[<?php echo $k ?>]',2,0)" /> -->
		</td>
		<td style="white-space:nowrap;font-weight:bold;"><?php hecho($section['title']) ?></td>
		<td><input type="text" name="<?php echo $config_key ?>[qty]" value="<?php echo $v['qty'] ?>" onchange="configQtyChange(<?php jecho($k) ?>,this)" size="2" style="text-align:right;" /></td>
		<td style="width:100%;text-align:center;"><ul class="float_list" style="padding:0;margin:0;text-align:left;">
<?php
	switch ($section['options'])
	{
	case 'summary_brief_full':
?>
			<li style="width:auto;"><input type="radio" id="<?php echo $section_name ?>_settings_<?php echo $k ?>_option_full" name="<?php echo $config_key ?>[option]" value="full"<?php echo ($v['option'] == 'full') ? ' checked="checked"' : '' ?> onchange="configTab(<?php jecho($k) ?>,'full')" /> <label for="<?php echo $section_name ?>_settings_<?php echo $k ?>_option_full">Full</label></li>
			<li style="width:auto;"><input type="radio" id="<?php echo $section_name ?>_settings_<?php echo $k ?>_option_summary" name="<?php echo $config_key ?>[option]" value="summary"<?php echo ($v['option'] == 'summary') ? ' checked="checked"' : '' ?> onchange="configTab(<?php jecho($k) ?>,'summary')" /> <label for="<?php echo $section_name ?>_settings_<?php echo $k ?>_option_summary">Summary</label></li>
			<li style="width:auto;"><input type="radio" id="<?php echo $section_name ?>_settings_<?php echo $k ?>_option_brief" name="<?php echo $config_key ?>[option]" value="brief"<?php echo ($v['option'] == 'brief') ? ' checked="checked"' : '' ?> onchange="configTab(<?php jecho($k) ?>,'brief')" /> <label for="<?php echo $section_name ?>_settings_<?php echo $k ?>_option_brief">Brief</label></li>
<?php
		break;

	case 'brief_full':
?>
			<li style="width:auto;"><input type="radio" id="<?php echo $section_name ?>_settings_<?php echo $k ?>_option_full" name="<?php echo $config_key ?>[option]" value="full"<?php echo ($v['option'] == 'full') ? ' checked="checked"' : '' ?> onchange="configTab(<?php jecho($k) ?>,'full')" /> <label for="<?php echo $section_name ?>_settings_<?php echo $k ?>_option_full">Full</label></li>
			<li style="width:auto;"><input type="radio" id="<?php echo $section_name ?>_settings_<?php echo $k ?>_option_brief" name="<?php echo $config_key ?>[option]" value="brief"<?php echo ($v['option'] == 'brief') ? ' checked="checked"' : '' ?> onchange="configTab(<?php jecho($k) ?>,'brief')" /> <label for="<?php echo $section_name ?>_settings_<?php echo $k ?>_option_brief">Brief</label></li>
<?php
		break;

	case 'wide_thin':
?>
			<li style="width:auto;"><input type="radio" id="<?php echo $section_name ?>_settings_<?php echo $k ?>_option_full" name="<?php echo $config_key ?>[option]" value="full"<?php echo ($v['option'] == 'full') ? ' checked="checked"' : '' ?> onchange="configTab(<?php jecho($k) ?>,'full')" /> <label for="<?php echo $section_name ?>_settings_<?php echo $k ?>_option_full">Wide</label></li>
			<li style="width:auto;"><input type="radio" id="<?php echo $section_name ?>_settings_<?php echo $k ?>_option_brief" name="<?php echo $config_key ?>[option]" value="brief"<?php echo ($v['option'] == 'brief') ? ' checked="checked"' : '' ?> onchange="configTab(<?php jecho($k) ?>,'brief')" /> <label for="<?php echo $section_name ?>_settings_<?php echo $k ?>_option_brief">Thin</label></li>
<?php
		break;

	default:
		echo '<li></li>';
		break;
	}
?>
		</ul></td>
		<td style="white-space:nowrap;">
			<!-- <input type="button" value="Auto-Fill" onclick="settingsAutoFill(<?php jecho($k) ?>)" /> -->
<?php
	/*
	if ($sections[$k]['options'] != '' && $k != 'news')
	{
	?>
	<input onclick="configShowExample(<?php jecho($k) ?>)" type="button" value="Example" />
	<?php }
	*/
?>
		</td>
	</tr>
	<tr id="<?php echo $section_name ?>_settings_rowb[<?php echo $k ?>]"<?php echo ($v['enable'] == 'y') ? '' : ' style="display:none;"' ?>><td colspan="5"<?php echo ($k == 'packages') ? ' class="unfinished"' : '' ?>>
<?php
	switch ($k)
	{
	case 'large_banner':
	case 'bottom_banner':
?>
		<table style="width:95%;margin:0 16px;" id="<?php echo $section_name ?>_settings_table[<?php echo $k ?>]">
			<tr>
				<th style="width:64px;">Order</th>
				<th>Banner ID</th>
				<th style="width:100%;">Name</th>
			</tr>
<?php
		while (count($v['items']) < $v['qty']) $v['items'][] = array();
		while (count($v['items']) > $v['qty']) array_pop($v['items']);
		$count = 0;
		if (is_array($v['items'])) foreach($v['items'] as $k2 => $row)
		{
			if (moduleLoad('banner')) $row['name'] = bannerGetValue($row['str'], 'name');
			echo settings_row($k, $count++, $row);
		}
?>
		</table>
<?php
		break;

	case 'news':
?>
		<table style="width:95%;margin:0 16px;" id="<?php echo $section_name ?>_settings_table[<?php echo $k ?>]">
			<tr>
				<th style="width:64px;">Order</th>
				<th>News ID</th>
				<th style="width:100%;">Name</th>
			</tr>
<?php
		while (count($v['items']) < $v['qty']) $v['items'][] = array();
		while (count($v['items']) > $v['qty']) array_pop($v['items']);
		$count = 0;
		if (is_array($v['items'])) foreach($v['items'] as $k2 => $row)
		{
			$row['name'] = newsGetValue($row['str'], 'news_title');
			echo settings_row($k, $count++, $row);
		}
?>
		</table>
<?php
		break;

	case 'packages':
?>
		<table style="width:95%;margin:0 16px;" id="<?php echo $section_name ?>_settings_table[<?php echo $k ?>]">
			<tr>
				<th style="width:64px;">Order</th>
				<th>Package ID</th>
				<th style="width:100%;">Name</th>
			</tr>
<?php
		while (count($v['items']) < $v['qty']) $v['items'][] = array();
		while (count($v['items']) > $v['qty']) array_pop($v['items']);
		$count = 0;
		if (is_array($v['items'])) foreach($v['items'] as $k2 => $row)
		{
			//$row['name']=packageGetValue($row['str'],'package_name');
			echo settings_row($k, $count++, $row);
		}
?>
		</table>
<?php
		break;

	case 'arcade_parts':
	case 'arcade_boards':
	case 'specials':
	case 'latest_offers':
	case 'featured_products':
?>
		<table style="width:95%;margin:0 16px;" id="<?php echo $section_name ?>_settings_table[<?php echo $k ?>]">
			<tr>
				<th style="width:64px;">Order</th>
				<th>Product ID</th>
				<th style="width:100%;">Name</th>
			</tr>
<?php
		while (count($v['items']) < $v['qty']) $v['items'][] = array();
		while (count($v['items']) > $v['qty']) array_pop($v['items']);
		$count = 0;
		if (is_array($v['items'])) foreach($v['items'] as $k2 => $row)
		{
			$row['name'] = productGetValue($row['str'], 'product_name');
			echo settings_row($k, $count++, $row);
		}
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

<!-- /config_2.tpl -->

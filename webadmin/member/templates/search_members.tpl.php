<?php
global $_GLOBALS;

$post_url = $get_vars;
unset($post_url['page_id']);
unset($post_url['search_for']);
unset($post_url['site_id']);
unset($post_url['country']);
$post_url['action'] = 'do_search';
$post_url           = '.?' . http_build_query($post_url);

$selected   = (array_key_exists('site_id', $posted)) ? (array)$posted['site_id'] : array();
$search_for = (array_key_exists('search_for', $posted)) ? $posted['search_for'] : '';

if (is_array($selected)) {
    foreach ($selected as $key => $value) {
        $selected[$key] = trim($value);
    }
}

?>
<style type="text/css">
/*<![CDATA[*/
table.search_table{width:95%;margin:auto}
table.search_table td{background-color:#FFFFCC;text-align:center}
table.search_table td.left{border-left:1px solid #FF9900;text-align:left;padding-left:55px;width:35%;font-size:12px}
/*]]>*/
</style>
<script type="text/javascript">
//<![CDATA[
function toggleSiteSelected(site_id)
{
	var all_tick=(site_id=='');
	var elems=getElements('site_id[]');
	for(i=0;i<elems.length;i++)
	{
		if(elems[i].value=='') elems[i].checked=all_tick;
		else if(all_tick) elems[i].checked=false;
	}
}
//]]>
</script>
<form action="<?=$post_url?>" method="post"><table class="search_table" style="border:1px solid #FF9900;">
	<tr><td colspan="2" style="padding-bottom:10px;"><h2 style="color:#FF9900;margin:2px;">Members Search</h2><a href=".?action=sales_search" style="color:blue;font-size:9px;">Switch To Sales Search</a></td></tr>
	<tr>
		<td class="left">Name/Email/ID/Company :</td>
		<td><input type="text" style="width:97%;" name="search_for" value="<?=htmlspecialchars($search_for)?>" /></td>
	</tr>
<?php
if ('y' == $_GLOBALS['staff']['developer']) {
?>
	<tr>
		<td class="left">Website :</td>
		<td><select name="site_id">
			<option value=""></option>
			<?=formatHTMLOptions(memberGetOptionList('site_id'), $selected)?>
		</select></td>
	</tr>
<?php
} else {
?>
	<tr>
		<td class="left">Website :</td>
		<td><select name="site_id">
			<option value=""></option>
			<?=formatHTMLOptions(array(
				'1'  => 'Highway Games',
				'2'  => 'Highway Entertainment',
				'9'  => 'Converters.tv',
				'16' => 'Car Converters',
				'20' => 'Arcade Spare Parts',
			), $selected)?>
		</select></td>
	</tr>
<?php
}
?>
	<tr>
		<td class="left">Country :</td>
		<td><select name="country">
			<option value=""></option>
			<?=formatHTMLOptions(getCountryList(false), (array_key_exists('country', $posted)) ? $posted['country'] : '')?>
		</td>
	</tr>
<?php
if (false && in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) {
?>
	<tr><td style="text-align:center;background-color:#FFFFCC;">
		<input type="checkbox" name="site_id[]" value="" onclick="toggleSiteSelected('');" <?=(!count($selected)) ? 'checked' : ''?>>All&nbsp;&nbsp;
		<input type="checkbox" name="site_id[]" value="2" onclick="toggleSiteSelected('2');" <?=(in_array('2', $selected)) ? 'checked' : ''?>>Highway Entertainment&nbsp;&nbsp;
		<input type="checkbox" name="site_id[]" value="1" onclick="toggleSiteSelected('1');" <?=(in_array('1', $selected)) ? 'checked' : ''?>>Highway Games&nbsp;&nbsp;
		<input type="checkbox" name="site_id[]" value="9" onclick="toggleSiteSelected('9');" <?=(in_array('9', $selected)) ? 'checked' : ''?>>Converters.tv&nbsp;&nbsp;
		<input type="checkbox" name="site_id[]" value="16" onclick="toggleSiteSelected('16');" <?=(in_array('16', $selected)) ? 'checked' : ''?>>Car Converters
	</td></tr>
<?php
}
?>
	<tr><td colspan="2"><input type="submit" value="Search" /></td></tr>
</table></form>

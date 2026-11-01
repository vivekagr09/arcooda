<!-- search_members.tpl -->

<?php
$post_url = $get_vars;
unset($post_url['page_id']);
unset($post_url['search_for']);
unset($post_url['site_id']);
unset($post_url['country']);
$post_url = '?' . http_build_query($post_url);

$selected = $posted['site_id'];
if (is_array($selected)) foreach($selected as $key => $value) $selected[$key] = trim($value);
if (!is_array($selected)) $selected = array();
?>
<style type="text/css">
table.search_table { width:95%; margin:auto; }
table.search_table td { background-color:#FFFFCC; text-align:center; }
table.search_table td.left { border-left:1px solid #FF9900; text-align:left; padding-left:55px; width:35%; font-size:12px; }
</style>

<script type="text/JavaScript">
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

<form action="<?php echo $post_url ?>" method="get"><table class="search_table" style="border:1px solid #FF9900;">
	<tr><td colspan="2" style="padding-bottom:10px;"><h2 style="color:#FF9900;margin:2px;">Members Search</h2></td></tr>
	<tr><td class="left">Name/Email/ID/Company :</td><td><input type="text" style="width:97%;" name="search_for" value="<?php echo formatHTMLSafe($search_for) ?>" /></td></tr>
	<tr>
		<td class="left">Website :</td>
		<td><select name="site_id">
			<option value=""></option>
			<?php echo formatHTMLOptions(memberGetOptionList('site_id'), $posted['site_id']) ?>
		</select></td>
	</tr>
	<tr><td class="left">Country :</td><td><select name="country"><option value=""></option><?php echo formatHTMLOptions(getCountryList(false), $posted['country']) ?></td></tr>
<?php
if (false && $_GLOBALS['site_settings']['site_id'] == 11)
{
?>
	<tr><td style="text-align:center;background-color:#FFFFCC;">
		<input type="checkbox" name="site_id[]" value="" onclick="toggleSiteSelected('');" <?php echo (!count($selected)) ? 'checked' : '' ?>>All&nbsp;&nbsp;
		<input type="checkbox" name="site_id[]" value="2" onclick="toggleSiteSelected('2');" <?php echo (in_array('2', $selected)) ? 'checked' : '' ?>>Highway Entertainment&nbsp;&nbsp;
		<input type="checkbox" name="site_id[]" value="1" onclick="toggleSiteSelected('1');" <?php echo (in_array('1', $selected)) ? 'checked' : '' ?>>Highway Games&nbsp;&nbsp;
		<input type="checkbox" name="site_id[]" value="9" onclick="toggleSiteSelected('9');" <?php echo (in_array('9', $selected)) ? 'checked' : '' ?>>Converters.tv&nbsp;&nbsp;
		<input type="checkbox" name="site_id[]" value="16" onclick="toggleSiteSelected('16');" <?php echo (in_array('16', $selected)) ? 'checked' : '' ?>>Car Converters
	</td></tr>
<?php
}
?>
	<tr><td colspan="2"><input type="submit" value="Search" /></td></tr>
</table></form>

<!-- /search_members.tpl -->

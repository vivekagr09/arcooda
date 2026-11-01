<?php
$post_url = $get_vars;
unset($post_url['page_id']);
unset($post_url['search_for']);
unset($post_url['site_id']);
unset($post_url['country']);
$post_url['action'] = 'sales_list';
$post_url           = '.?' . http_build_query($post_url);

$selected = $posted['site_id'];
if (is_array($selected)) {
    foreach ($selected as $key => $value) {
        $selected[$key] = trim($value);
    }
}

//print_array($selected);
//if(in_array('hg',$selected)) echo 'Highway Games!!!';
if (!is_array($selected)) {
    $selected = array();
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
	<tr style=""><td colspan="2" style="padding-bottom:10px;"><h2 style="color:#FF9900;margin:2px;">Sales Search</h2><a href=".?action=search" style="color:blue;font-size:9px;">Switch To Regular Search</a></td></tr>
	<tr><td class="left">Product Name/ID :</td><td><input type="text" style="width:97%;" name="search_for" value="<?=htmlspecialchars($search_for)?>" /></td></tr>
	<tr><td class="left">Website :</td><td><select name="site_id"><option value=""></option><?=formatHTMLOptions(array('1' => 'Highway Games', '2' => 'Highway Entertainment', '9' => 'Converters.tv', '16' => 'Car Converters', '20' => 'Arcade Spare Parts'), $posted['site_id'])?></select></td></tr>
	<tr><td class="left">Country :</td><td><select name="country"><option value=""></option><?=formatHTMLOptions(getCountryList(false), $posted['country'])?></td></tr>
	<tr><td colspan="2"><input type="submit" value="Search" /></td></tr>
</table></form>

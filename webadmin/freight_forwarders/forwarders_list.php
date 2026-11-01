<?php
include_once('forwarders_common.inc.php');
include_once('templates/forwarders_header.tpl.php');

$order=$posted['order'];
if($order!='desc') $order='asc';
$page_id=intval($posted['page_id']);
$order_by=strtolower($posted['order_by']);

//Build a search criteria array
$criteria=array();
if($posted['search_for']!='') $criteria['any_text']=$posted['search_for'];
if($posted['active']!='') $criteria['active']=$posted['active'];
if($posted['type']!='') $criteria['type']=explode('|',$posted['type']);
if(!is_null($posted['country'])) $criteria['country']=$posted['country'];
$alpha_value=$posted['alpha_value'];

$sub_criteria=$criteria;
unset($sub_criteria['country']);
$forwarder_list=forwardersSearch($sub_criteria);
if(count($forwarder_list)>0)
{
	$list_by1='country';
	$list_by2='';
?>
<div style="text-align:left;">
	<a href="#" onclick="javascript:toggleSection('sectionViewByCountry');return false;"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>Display Forwarders by Country</b></a>
	<br /><br />
	<div id="sectionViewByCountry" class="child" style="display:none">
<?php
	include('templates/forwarders_navigate.tpl.php');
?>
	</div>
</div>
<?php
}

?>
<table style="width:100%;border: 0px" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td style="width:25%;background-color:#F0F4F9">
			<img src="/webadmin/images/triDown.gif" width="16" height="16" border="0" alt="Expand/Collapse All">
			<b><a href="#" onclick="toggleSection('viewByActive');return false" target="_blank">View By Active</a></b>
		</td>
	</tr>
</table>
<div id="viewByActive"<?php echo (array_key_exists("active", $criteria)) ? "" : ' style="display:none;"' ?>>
<?php
$list_by1 = 'active';
$list_by2 = '';
$sub_criteria = $criteria;
unset($sub_criteria["active"]);
$freight_list = forwardersSearch($sub_criteria);
include_once dirname(__FILE__) . "/templates/navigate.tpl.php";
?>
</div>

<center><?php echo formatHTMLTitle('Freight Forwarders', '<a href="?" style="color:#00f;">View All</a>') ?></center>

<?php

$search=$posted['search_for'];
//include('templates/forwarders_search.tpl.php');

//Find and pagenate the forwarders
$forwarder_list=forwardersSearch($criteria,$alpha_value);
$forwarder_list=forwardersSort($forwarder_list,$order_by);
if($order=='desc') $forwarder_list=array_reverse($forwarder_list);

$items_per_page=50;
$count_total=count($forwarder_list);

$forwarder_list=array_chunk($forwarder_list,$items_per_page);
$count_pages=count($forwarder_list);
$page_id=max(0,min($count_pages-1,$page_id));
$forwarder_list=$forwarder_list[$page_id];
$count_display=count($forwarder_list);

if($count_total>0)
{
	$page_url=$get_vars;
	unset($page_url['page_id']);
	$page_url='?'.http_build_query($page_url);
	$page_links=formatHTMLPageLinks($page_url.'&page_id=',$page_id,$count_display,$count_total,$items_per_page);
?>
<br />
<?php include 'templates/forwarders_list.tpl.php'; ?>
<br />
<?php echo $page_links ?><br /><?php echo forwardersAlphaList($alpha_value, $page_url); ?>
<br />
<?php
	if($popup_window)
	{
?>
<input type="button" onclick="window.close();" value="Close Window" />
<?php
	}
}
else
{
?>
No results were found
<?php
}

include_once('templates/forwarders_footer.tpl.php');
?>
<?php
include_once('forwarders_common.inc.php');
include_once('templates/forwarders_header_search.tpl.php');

$order=$posted['order'];
if($order!='desc') $order='asc';
$page_id=intval($posted['page_id']);
$order_by=strtolower($posted['order_by']);

//Build a search criteria array
$criteria=array();
if($posted['search_for']!='') $criteria['any_text']=$posted['search_for'];
if($posted['sea_air']!='') $criteria['sea_air']=explode('|',$posted['sea_air']);
if($posted['country_from']!='') $criteria['country_from']=$posted['country_from'];
if($posted['country_to']!='') $criteria['country_to']=$posted['country_to'];
if($posted['advanced']=='true')
{
	if($posted['port_from']!='') $criteria['port_from']=$posted['port_from'];
	if($posted['port_to']!='') $criteria['port_to']=$posted['port_to'];
}

$sub_criteria=$criteria;
unset($sub_criteria['country_from']);
$quote_list=forwardersQuotesSearch($sub_criteria);
if(count($quote_list)>0 && false)
{
	$list_by1='country_from';
	$list_by2='';
?>
<div style="text-align:left;">
	<a href="#" onclick="javascript:toggleSection('sectionViewBySourceCountry');return false;"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>Display Quotes by Source Country</b></a>
	<br /><br />
	<div id="sectionViewBySourceCountry" class="child" style="display:none">
<?php
	include('templates/forwarders_quotes_navigate.tpl.php');
?>
	</div>
</div>
<?php
}

$sub_criteria=$criteria;
unset($sub_criteria['country_to']);
$quote_list=forwardersQuotesSearch($sub_criteria);
if(count($quote_list)>0 && false)
{
	$list_by1='country_to';
	$list_by2='';
?>
<div style="text-align:left;">
	<a href="#" onclick="javascript:toggleSection('sectionViewByDestinationCountry');return false;"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>Display Quotes by Destination Country</b></a>
	<br /><br />
	<div id="sectionViewByDestinationCountry" class="child" style="display:none">
<?php
	include('templates/forwarders_quotes_navigate.tpl.php');
?>
	</div>
</div>
<?php
}

$advanced=($posted['advanced']=='true');
$sea_air=$posted['sea_air'];
$country_from=$posted['country_from'];
$country_to=$posted['country_to'];
$port_from=$posted['port_from'];
$port_to=$posted['port_to'];
$currency=($posted['currency']!='')?$posted['currency']:'AUD';
include('templates/forwarders_quotes_search.tpl.php');

//Find and pagenate the forwarders
$quote_list=forwardersQuotesSearch($criteria);
$quote_list=forwardersQuotesSort($quote_list,$order_by);
if($order=='desc') $quote_list=array_reverse($quote_list);

$items_per_page=50;
$count_total=count($quote_list);

$quote_list=array_chunk($quote_list,$items_per_page);
$count_pages=count($quote_list);
$page_id=max(0,min($count_pages-1,$page_id));
$quote_list=$quote_list[$page_id];
$count_display=count($quote_list);

if($count_total>0)
{
	$page_url=$get_vars;
	unset($page_url['page_id']);
	$page_url='?'.http_build_query($page_url);

	$page_links=formatHTMLPageLinks($page_url.'&page_id=',$page_id,$count_display,$count_total,$items_per_page);
?>
<br />
<?php include 'templates/forwarders_quotes_list.tpl.php'; ?>
<br />
<?php echo $page_links ?><br />
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
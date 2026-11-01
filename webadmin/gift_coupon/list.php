<?php
include_once dirname(__FILE__) . "/common.inc.php";

include_once dirname(__FILE__) . "/header.inc.php";

$search_for=(isset($posted['search_for']))?$posted['search_for']:'';
$page=intval($posted['page']);
$order_by=(isset($posted['order_by']))?$posted['order_by']:'id';
$order=($posted['order']!='asc') ? 'desc' : 'asc';
if(isset($posted['search_for'])) include 'search.tpl.php';



$site_id=intval($posted['site_id']);
$search_for=(isset($posted['search_for']))?$posted['search_for']:'';
$tmp=explode(' ',$posted['search_for']);
foreach($tmp as $key=>$value)
	if(is_numeric($value)) $tmp[$key]=intval($value);
$posted['search_for']=implode(' ',$tmp);

$criteria=array();
if($posted['site_id']!='')		$criteria['site_id']		=$posted['site_id'];
if($posted['search_for']!='')	$criteria['any_text']		=explode(' ',$posted['search_for']);
if($posted['active']!='')		$criteria['active']			=explode('|',$posted['active']);
if($posted['return_message']!='')
{
	$criteria['active']='y';
	$criteria['expired']='n';
}
/*if (!in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43,39))) $criteria['site_id'] = array($_GLOBALS['site_settings']['site_id'], "");*/
$criteria['site_id']=39;
echo formatHTMLTitle('Coupon List');	

$item_list=giftcouponSearch($criteria);

$item_list=giftcouponSort($item_list,$order_by);



if($order=='desc') $item_list=array_reverse($item_list);

$count_total=count($item_list);

$yesno = array('y'=>'Yes','n'=>'No');
$default = array('0'=>' -- Please select an option -- ');
if($count_total>0)
{


	$items_per_page=50;
	$item_list=array_chunk($item_list,$items_per_page);
	$count_pages=count($item_list);
	$page=max(0,min($count_pages-1,$page));
	$item_list=$item_list[$page];
	$count_display=count($item_list);

	$page_url=$get_vars;
	$page_url['site_id']=$site_id;
	unset($page_url['page']);
	$page_url='?'.http_build_query($page_url);

	$page_links=formatHTMLPageLinks($page_url.'&page=',$page,$count_display,$count_total,$items_per_page);


	
	include 'list.tpl.php';
	echo $page_links;
}
else echo 'No results were found';
echo '<br /><br />';

include_once dirname(__FILE__) . "/footer.inc.php";
?>
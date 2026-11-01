<?php
include_once('insurance_common.inc.php');
include_once('templates/insurance_header.tpl.php');

$order=$posted['order'];
if($order!='asc') $order='desc';
$page_id=intval($posted['page_id']);
$order_by=strtolower($posted['order_by']);

//Build a search criteria array
$criteria=array();
if($posted['search_for']!='') $criteria['any_text']=explode('|',$posted['search_for']);
if($posted['claim_year']!='') $criteria['claim_year']=mktime(0, 0, 0, 1, 1, date('Y',$posted['claim_year']));
if($posted['claim_month']!='') $criteria['claim_month']=mktime(0, 0, 0, date('m',$posted['claim_month']), 1, date('Y',$posted['claim_month']));
if($posted['claim_week']!='') $criteria['claim_week']=mktime(0, 0, 0, date('m',$posted['claim_week']), date('d',$posted['claim_week'])+1-date('w',$posted['claim_week']), date('Y',$posted['claim_week']));

//if($search!='') include('templates/search_claims.tpl.php');

//Find and pagenate the insurances
$claim_list=insuranceClaimSearch($criteria);
$claim_list=insuranceClaimSort($claim_list,$order_by);
if($order=='desc') $claim_list=array_reverse($claim_list);

$items_per_page=100;
$count_total=count($claim_list);

?>
<?php echo formatHTMLTitle('Losses / Claims','<i><a href="index.php?action=add_claim" target="_blank">Add New Claim</a></i>') ?>
<?php

$claim_list=array_chunk($claim_list,$items_per_page);
$count_pages=count($claim_list);
$page_id=max(0,min($count_pages-1,$page_id));
$claim_list=$claim_list[$page_id];
$count_display=count($claim_list);

if($count_total>0)
{
	$page_url=$get_vars;
	unset($page_url['page_id']);
	$page_url='?'.http_build_query($page_url);

	$page_links=formatHTMLPageLinks($page_url.'&page_id=',$page_id,$count_display,$count_total,$items_per_page);
?>
<br />
<?php include 'templates/insurance_claims_list.tpl.php'; ?>
<br />
<?php echo $page_links ?><br />
<br />
<?php
	if(!$popup_window)
	{
?>
<!-- <input class="unfinished" type="button" onclick="window.open('insurance_list_printable.php?<?php echo http_build_query($get_vars) ?>');" value="Print Claims List" /> -->
<?php
	}
	else
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

include_once('templates/insurance_footer.tpl.php');
?>
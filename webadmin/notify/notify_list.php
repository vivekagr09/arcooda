<?php
include_once('notify_common.inc.php');
include_once('templates/notify_header.tpl.php');

$order=$posted['order'];
if($order!='asc') $order='desc';
$page_id=intval($posted['page_id']);
$order_by=strtolower($posted['order_by']);

//Build a search criteria array
$criteria=array();
if($posted['search_for']!='') $criteria['any_text']=$posted['search_for'];
if($posted['status']!='') $criteria['notify_status']=explode('|',$posted['status']);
if($posted['handler']!='') $criteria['handler_id']=$posted['handler'];
foreach(array('member_id','staff_id','site_id') as $key)
	if($posted[$key]>0) $criteria[$key]=intval($posted[$key]);

//Find and pagenate the notifications
$notify_list=notifySearch($criteria);
$notify_list=notifySort($notify_list,$order_by);
if($order=='desc') $notify_list=array_reverse($notify_list);

$items_per_page=50;
$count_total=count($notify_list);

if($count_total>0)
{
?>
<div style="text-align:left;">
	<a href="#" onclick="javascript:toggleSection('sectionViewBySiteHandler');return false;"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Handler/Website</b></a>
	<br /><br />
	<div id="sectionViewBySiteHandler" class="child">
<?php
	include('templates/notify_navigate.tpl.php');
?>
	</div>
	<div id="sectionViewBySiteHandler_hideNote" class="child"></div>
</div>
<script type="text/javascript">
toggleSection('sectionViewBySiteHandler');
</script>
<?php
}
?>
<?= formatHTMLTitle('Notifications') ?>
<?php

$notify_list=array_chunk($notify_list,$items_per_page);
$count_pages=count($notify_list);
$page_id=max(0,min($count_pages-1,$page_id));
$notify_list=$notify_list[$page_id];
$count_display=count($notify_list);

if($count_total>0)
{
	$page_url=$get_vars;
	unset($page_url['page_id']);
	$page_url='?'.http_build_query($page_url);

	$page_links=formatHTMLPageLinks($page_url.'&page_id=',$page_id,$count_display,$count_total,$items_per_page);
?>
<br />
<?php include 'templates/notify_list.tpl.php'; ?>
<br />
<?= $page_links ?><br />

<br />
<input type="button" onclick="window.open('notify_list_printable.php?<?= http_build_query($get_vars) ?>');" value="Print Notifications List" />
<input type="button" onclick="window.open('notify_wanted_printable.php?<?= http_build_query($get_vars) ?>');" value="Print Wanted List" />
<?php
}
else
{
?>
No results were found
<?php
}

include_once('templates/notify_footer.tpl.php');
?>
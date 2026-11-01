<?php
include_once('workmanual_common.inc.php');
include_once('templates/workmanual_header.tpl.php');

$order=$posted['order'];
if($order!='asc') $order='desc';
$order_by=strtolower($posted['order_by']);

//Build a search criteria array
$criteria=array();

//Find and pagenate the invoices
$manual_list=workmanualSearch($criteria);
$manual_list=workmanualSort($manual_list,$order_by);
if($order=='desc') $manual_list=array_reverse($manual_list);

$count_total=count($manual_list);
$count_display=$count_total;
$items_per_page=$count_total;

$addman_url=$get_vars;
$addman_url['action']='add';
unset($addman_url['manual_id']);
$addman_url='index.php?'.http_build_query($addman_url);

?>
<?= formatHTMLTitle('Work Manuals',(has_permission('WORK_MAN_W',$allowed_sections))?'<a href="'.$addman_url.'" target="_blank">Add Manual</a>':'') ?>
<?php

if($count_total>0)
{
	$page_url=$get_vars;
	unset($page_url['page_id']);
	$page_url='index.php?'.http_build_query($page_url);

	$page_links=formatHTMLPageLinks($page_url.'&page_id=',0,$count_display,$count_total,$items_per_page);
?>
<?php include 'templates/workmanual_listview.tpl.php'; ?>
<br />
<?= $page_links ?><br />
<?php
}
else
{
?>
No results were found
<?php
}

include_once('templates/workmanual_footer.tpl.php');
?>
<?php
include_once('project_common.inc.php');
include_once('templates/project_header.tpl.php');

$order=$posted['order'];
if($order!='asc') $order='desc';
$order_by=strtolower($posted['order_by']);

//Build a search criteria array
$criteria=array();
if($posted['site_id']!='11' && $posted['site_id'] != '43') $criteria['site_id']=$posted['site_id'];

//Find and pagenate the invoices
$project_list=projectSearch($criteria);
$project_list=projectSort($project_list,$order_by);
if($order=='desc') $project_list=array_reverse($project_list);

$count_total=count($project_list);
$count_display=$count_total;
$items_per_page=$count_total;

$addman_url=$get_vars;
$addman_url['action']='add';
unset($addman_url['project_id']);
$addman_url='index.php?'.http_build_query($addman_url);

?>
<?php echo formatHTMLTitle('Projects','<a href="'.$addman_url.'" target="_blank">Add Project</a>') ?>
<?php

if($count_total>0)
{
	$page_url=$get_vars;
	unset($page_url['page_id']);
	$page_url='index.php?'.http_build_query($page_url);

	$page_links=formatHTMLPageLinks($page_url.'&page_id=',0,$count_display,$count_total,$items_per_page);
?>
<?php include 'templates/project_listview.tpl.php'; ?>
<br />
<?php echo $page_links ?><br />
<?php
}
else
{
?>
No results were found
<?php
}

include_once('templates/project_footer.tpl.php');
?>
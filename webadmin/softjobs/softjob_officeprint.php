<?php
include_once 'softjob_common.inc.php';
include_once 'templates/printout_header.tpl.php';

$action='print';
$order=$posted['order'];
if($order!='asc') $order='desc';
$page_id=intval($posted['page_id']);
$order_by=strtolower($posted['order_by']);

//Build a search criteria array
$criteria=array();
$criteria['staff_id']=$staff_id;
if($posted['priority']!='') $criteria['priority']=explode('|',$posted['priority']);
if($posted['assigned']!='') $criteria['assigned']=explode('|',$posted['assigned']);
if($posted['reporter']!='') $criteria['reporter']=explode('|',$posted['reporter']);
if($posted['status']!='') $criteria['status']=explode('|',$posted['status']);
if($posted['website']!='') $criteria['website']=explode('|',$posted['website']);
if($posted['search_for']!='') $criteria['any_text']=$posted['search_for'];

//Find and pagenate the softjobs
$softjob_list=softjobSearch($criteria);
$softjob_list=softjobSort($softjob_list,$order_by);
if($order=='desc') $softjob_list=array_reverse($softjob_list);

$items_per_page=max(count($softjob_list),50);
$count_total=count($softjob_list);

?>
<table style="width:100%;border-style:none;"><tr>
	<td style="text-align:left;vertical-align:top;width:auto;border-style:none;">
		<span style="font-size:20pt;">SOFTWARE JOBS OFFICE REPORT</span><br />
	</td>
	<td style="width:200px;vertical-align:top;border-style:none;">
		Printed By <?= htmlspecialchars($all_staff_list[$staff_id]) ?><br />
		Print Date: <?= date('d/m/y') ?>
	</td>
</tr></table>
<?php

$softjob_list=array_chunk($softjob_list,$items_per_page);
$count_pages=count($softjob_list);
$page_id=max(0,min($count_pages-1,$page_id));
$softjob_list=$softjob_list[$page_id];
$count_display=count($softjob_list);

if($count_total>0)
{
	$page_url=$get_vars;
	unset($page_url['page_id']);
	$page_url='index.php?'.http_build_query($page_url);

?>
<br />
<?php include 'templates/softjob_listview.tpl.php'; ?>
<br />
<br />
<?php
}
else
{
?>
No results were found
<?php
}

include_once 'templates/printout_footer.tpl.php';
?>
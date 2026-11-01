<?php
include_once 'softjob_common.inc.php';
$_GET['return_message']=' ';
include_once 'templates/softjob_header.tpl.php';

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

//Find and pagenate the soft jobs
$softjob_list=softjobSearch($criteria);
$softjob_list=softjobSort($softjob_list,$order_by);
if($order=='desc') $softjob_list=array_reverse($softjob_list);

$items_per_page=max(count($softjob_list),50);
$count_total=count($softjob_list);

if($count_total>0)
{
	$list_by1='assigned';
	$list_by2='status';
?>
<div style="text-align:left;">
	<a href="#" onclick="javascript:toggleSection('sectionViewByHandlerStatus');return false;"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Handler/Status</b></a>
	<br /><br />
	<div id="sectionViewByHandlerStatus" class="child">
<?php
	include('templates/navigate_softjob.tpl.php');
?>
	</div>
</div>
<script type="text/javascript">
toggleSection('sectionViewByHandlerStatus');
</script>
<?php
}

if($count_total>0)
{
	$list_by1='website';
	$list_by2='assigned';
?>
<div style="text-align:left;">
	<a href="#" onclick="javascript:toggleSection('sectionViewByWebsiteHandler');return false;"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Website/Handler</b></a>
	<br /><br />
	<div id="sectionViewByWebsiteHandler" class="child">
<?php
	include('templates/navigate_softjob.tpl.php');
?>
	</div>
</div>
<script type="text/javascript">
toggleSection('sectionViewByWebsiteHandler');
</script>
<?php
}

if($count_total>0)
{
	$list_by1='priority';
	$list_by2='';
?>
<div style="text-align:left;">
	<a href="#" onclick="javascript:toggleSection('sectionViewByPriority');return false;"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>Display By Priority</b></a>
	<br /><br />
	<div id="sectionViewByPriority" class="child">
<?php
	include('templates/navigate_softjob.tpl.php');
?>
	</div>
</div>
<script type="text/javascript">
toggleSection('sectionViewByPriority');
</script>
<?php
}

if($count_total>0)
{
	$list_by1='assigned';
	$list_by2='';
?>
<div style="text-align:left;">
	<a href="#" onclick="javascript:toggleSection('sectionViewByHandler');return false;"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Handler</b></a>
	<br /><br />
	<div id="sectionViewByHandler" class="child">
<?php
	include('templates/navigate_softjob.tpl.php');
?>
	</div>
</div>
<script type="text/javascript">
toggleSection('sectionViewByHandler');
</script>
<?php
}

if($count_total>0)
{
	$list_by1='status';
	$list_by2='';
?>
<div style="text-align:left;">
	<a href="#" onclick="javascript:toggleSection('sectionViewByStatus');return false;"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Status</b></a>
	<br /><br />
	<div id="sectionViewByStatus" class="child">
<?php
	include('templates/navigate_softjob.tpl.php');
?>
	</div>
</div>
<script type="text/javascript">
toggleSection('sectionViewByStatus');
</script>
<?php
}

$action_url=$get_vars;
$action_url['action']='do_report';
$action_url='index.php?'.http_build_query($action_url);
?>
<?= formatHTMLTitle('Rank Software Jobs') ?>
<form accept-charset="utf-8" action="<?= $action_url ?>" method="post">
<?php

$softjob_list=array_chunk($softjob_list,$items_per_page);
$count_pages=count($softjob_list);
$page_id=max(0,min($count_pages-1,$page_id));
$softjob_list=$softjob_list[$page_id];
$count_display=count($softjob_list);

if($count_total>0)
{
?>
<br />
<?php include 'templates/softjob_report.tpl.php'; ?>
<br />
<br />
<input type="button" value="Print Office Summary" onclick="javascript:this.form.submit();" />
<?php
}
else
{
?>
No results were found
</form>
<?php
}

include_once 'templates/softjob_footer.tpl.php';
?>
<?php
include_once('common.inc.php');
include_once('templates/header.tpl.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$order=$posted['order'];
if($order!='asc') $order='desc';
$page_id=intval($posted['page_id']);
$order_by=strtolower($posted['order_by']);

//Build a search criteria array
$criteria = $_GLOBALS['criteria_options']['newsletter'];
unset($criteria['display_online']);
unset($criteria['active']);
foreach($section_handler->getOptionList('all_fields') as $key=>$value)
	if($posted[$key]!='') $criteria[$key]=explode('|',$posted[$key]);
	elseif(array_key_exists($key,$posted)) $criteria[$key]=$posted[$key];
unset($criteria['id']);
if(!in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) $criteria['site_id']=$_GLOBALS['site_settings']['site_id'];
if($posted['search_for']!='') $criteria['any_text']=explode('|',$posted['search_for']);
if($posted['subset_list']!='') $criteria['id']=explode('|',$posted['subset_list']);

/*
$sub_criteria=$criteria;
unset($sub_criteria['site_id']);
$item_list=$section_handler->search($sub_criteria);
if(count($item_list)>0)
{
	$list_by1='site_id';
	$list_by2='';
?>
<div style="text-align:left;">
	<a href="javascript:void(toggleSection('sectionViewBySite'))"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Site</b></a>
	<br /><br />
	<div id="sectionViewBySite" class="child" style="display:none;">
<?php
	include('templates/navigate.tpl.php');
?>
	</div>
</div>
<?php
}
*/

$search_for=$posted['search_for'];
?>
<div style="text-align:left;">
	<a href="javascript:void(toggleSection('sectionSearch'))"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>Search</b></a>
	<br /><br />
	<div id="sectionSearch" class="child"<?php echo ($search_for=='')?' style="display:none;"':'' ?>>
<?php
include 'templates/search.tpl.php';
?>
	</div>
</div>

<?php
//Find and pagenate the items
$item_list=$section_handler->search($criteria);
$item_list=$section_handler->sort($item_list,$order_by);
if($order=='desc') $item_list=array_reverse($item_list);

$items_per_page=50;
if($_GLOBALS['staff']['per_page']>0) $items_per_page=$_GLOBALS['staff']['per_page'];
$count_total=count($item_list);

$item_list=array_chunk($item_list,$items_per_page);
$count_pages=count($item_list);
$page_id=max(0,min($count_pages-1,$page_id));
$item_list=$item_list[$page_id];
$count_display=count($item_list);

if($count_total>0)
{
	$page_url=$get_vars;
	$page_url['action']='list';
	unset($page_url['page_id']);
	$page_url='index.php?'.http_build_query($page_url).'&page_id=';

	$page_links=formatHTMLPageLinks($page_url,$page_id,$count_display,$count_total,$items_per_page);

	$select_url=$get_vars;
	unset($select_url['action']);
	$select_url='select.php?'.http_build_query($select_url);
?>
<br />
<form action="<?php echo formatHTMLSafe($select_url) ?>" method="post">
<?php
	include 'templates/list.tpl.php';
?>
</form>
<br />
<table align="center" cellpadding="7" width="95%"><tr><td bgcolor="#fafafa"><div align="center"><?php echo $page_links ?></div></td></tr></table>
<br />
<?php
}
else
{
?>
No results were found
<?php
}

if($popup_window)
{
?>
<input type="button" onclick="window.close();" value="Close Window" />
<?php
}

include_once 'templates/footer.tpl.php';
?>
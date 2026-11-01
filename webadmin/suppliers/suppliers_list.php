<?php
include_once 'suppliers_common.inc.php';
include_once 'templates/suppliers_header.tpl.php';

$order=$posted['order'];
if($order!='asc') $order='desc';
$page_id=intval($posted['page_id']);
$order_by=strtolower($posted['order_by']);

//Build a search criteria array
$criteria=$_GLOBALS['criteria_options']['suppliers'];
unset($criteria['active']);
foreach(supplierGetOptionList('all_fields') as $key=>$value)
	if($posted[$key]!='') $criteria[$key]=explode('|',$posted[$key]);
	elseif(array_key_exists($key,$posted)) $criteria[$key]=$posted[$key];
unset($criteria['id']);
if($posted['search_for']!='') $criteria['any_text']=$posted['search_for'];
if($posted['subset_list']!='') $criteria['id']=explode('|',$posted['subset_list']);

/*
$sub_criteria=$criteria;
unset($sub_criteria['country']);
$supplier_list=supplierSearch($sub_criteria);
if(count($supplier_list)>0)
{
	$list_by1='country';
	$list_by2='';
?>
<div style="text-align:left;">
	<a href="javascript:void(toggleSection('sectionViewByCountry'))"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Country</b></a>
	<br /><br />
	<div id="sectionViewByCountry" class="child" style="display:none;">
<?php
	include('templates/suppliers_navigate.tpl.php');
?>
	</div>
</div>
<?php
}
*/

/*
$sub_criteria=$criteria;
unset($sub_criteria['site_id']);
$supplier_list=supplierSearch($sub_criteria);
if(count($supplier_list)>0)
{
	$list_by1='site_id';
	$list_by2='';
?>
<div style="text-align:left;">
	<a href="javascript:void(toggleSection('sectionViewBySite'))"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Site</b></a>
	<br /><br />
	<div id="sectionViewBySite" class="child" style="display:none;">
<?php
	include('templates/suppliers_navigate.tpl.php');
?>
	</div>
</div>
<?php
}
*/

/*
$search_for=$posted['search_for'];
?>
<div style="text-align:left;">
	<a href="javascript:void(toggleSection('sectionSearch'))"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>Search</b></a>
	<br /><br />
	<div id="sectionSearch" class="child"<?= ($search_for=='')?' style="display:none;"':'' ?>>
<?php
include 'templates/suppliers_search.tpl.php';
?>
	</div>
</div>
<?php
*/

if($posted['country']!='') echo '<p><b>This Page shows all the suppliers in the Country: '.htmlspecialchars($storage_list[$posted['storage']]).'</b></p>';

$supplier_list=supplierSort(supplierSearch($criteria),$order_by);
if($order=='desc') $supplier_list=array_reverse($supplier_list);
$items_per_page=50;
if($_GLOBALS['staff']['suppliers_per_page']>0) $items_per_page=$_GLOBALS['staff']['suppliers_per_page'];
$count_total=count($supplier_list);
$supplier_list=array_chunk($supplier_list,$items_per_page);
$count_pages=count($supplier_list);
$page_id=max(0,min($count_pages-1,$page_id));
$supplier_list=$supplier_list[$page_id];
$count_display=count($supplier_list);

if($count_total>0)
{
	$page_url=$get_vars;
	unset($page_url['page_id']);
	$page_url='index.php?'.http_build_query($page_url).'&page_id=';
	$page_links=formatHTMLPageLinks($page_url,$page_id,$count_display,$count_total,$items_per_page);
	$select_url=$get_vars;
	unset($select_url['action']);
	$select_url='suppliers_select.php?'.http_build_query($select_url);
?>
<form action="<?= htmlspecialchars($select_url) ?>" method="post">
<?php
	include 'templates/suppliers_list.tpl.php';
?>
</form>
<?= $page_links ?>
<br /><br />
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
include_once 'templates/suppliers_footer.tpl.php';
?>
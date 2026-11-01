<?php
include_once 'suppliers_common.inc.php';
include_once 'templates/suppliers_header.tpl.php';

$order = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$page_id  = intval($posted['page_id']);
$order_by = strtolower($posted['order_by']);

//Build a search criteria array
$criteria = $_GLOBALS['criteria_options']['suppliers'];
unset($criteria['active']);
foreach (supplierGetOptionList('all_fields') as $key => $value) {
    if ('' != $posted[$key]) {
        $criteria[$key] = explode('|', $posted[$key]);
    } elseif (array_key_exists($key, $posted)) {
        $criteria[$key] = $posted[$key];
    }
}

unset($criteria['supplier_id']);
if ('' != $posted['search_for']) {
    $criteria['any_text'] = explode('|', $posted['search_for']);
}

if ('' != $posted['subset_list']) {
    $criteria['supplier_id'] = explode('|', $posted['subset_list']);
}

if ('' != $posted['status']) {
    $criteria['supplier_status'] = explode('|', $posted['status']);
}

if ('' != $posted['handler']) {
    $criteria['handler_id'] = $posted['handler'];
}

if ('' != $posted['open_year']) {
    $criteria['open_year'] = mktime(0, 0, 0, 1, 1, date('Y', $posted['open_year']));
}

if ('' != $posted['open_month']) {
    $criteria['open_month'] = mktime(0, 0, 0, date('m', $posted['open_month']), 1, date('Y', $posted['open_month']));
}

if ('' != $posted['open_week']) {
    $criteria['open_week'] = mktime(0, 0, 0, date('m', $posted['open_week']), date('d', $posted['open_week']) + 1 - date('w', $posted['open_week']), date('Y', $posted['open_week']));
}

if ('' != $posted['supplier_list']) {
    $criteria['supplier_id'] = explode('|', $posted['supplier_list']);
}

$sub_criteria = $criteria;
unset($sub_criteria['country']);
$supplier_list = supplierSearch($sub_criteria);
if (count($supplier_list) > 0) {
    $list_by1 = 'country';
    $list_by2 = '';
?>
<div style="text-align:left;">
	<a href="javascript:void(toggleSection('sectionViewByCountry'))"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Country</b></a>
	<br /><br />
	<div id="sectionViewByCountry" class="child" style="display:none;">
<?php
include 'templates/suppliers_navigate.tpl.php';
?>
	</div>
</div>
<?php
}

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
<a href="javascript:void(toggleSection('sectionViewByWebsite'))"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Website</b></a>
<br /><br />
<div id="sectionViewByWebsite" class="child" style="display:none;">
<?php
include('templates/suppliers_navigate.tpl.php');
?>
</div>
</div>
<?php
}
 */

$status_id = $posted['type'];
$search    = $posted['search_for'];

?>
<div style="text-align:left;">
	<a href="#" onclick="javascript:toggleSection('sectionSearchSuppliers');return false;"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>Search Suppliers</b></a>
	<br /><br />
	<div id="sectionSearchSuppliers" class="child"<?=('' != $search) ? '' : ' style="display:none;"'?>>
<?php
include 'templates/suppliers_search.tpl.php';
?>
	</div>
	<div id="sectionSearchSuppliers_hideNote" class="child"></div>
</div>

<?php
//Find and pagenate the suppliers
$supplier_list = supplierSearch($criteria);
$supplier_list = supplierSort($supplier_list, $order_by);
if ('desc' == $order) {
    $supplier_list = array_reverse($supplier_list);
}

$items_per_page = 50;
if ($_GLOBALS['staff']['suppliers_per_page'] > 0) {
    $items_per_page = $_GLOBALS['staff']['suppliers_per_page'];
}

$count_total = count($supplier_list);

$supplier_list = array_chunk($supplier_list, $items_per_page);
$count_pages   = count($supplier_list);
$page_id       = max(0, min($count_pages - 1, $page_id));
$supplier_list = $supplier_list[$page_id];
$count_display = count($supplier_list);

if ($count_total > 0) {
    $page_url           = $get_vars;
    $page_url['action'] = 'list';
    unset($page_url['page_id']);
    $page_url = 'index.php?' . http_build_query($page_url) . '&page_id=';

    $page_links = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);

    $select_url = $get_vars;
    unset($select_url['action']);
    $select_url = 'supplier_select.php?' . http_build_query($select_url);
?>
<form accept-charset="utf-8" action="<?=htmlspecialchars($select_url)?>" method="post">
<?php
include 'templates/suppliers_list.tpl.php';
?>

<table align="center" cellpadding="7" width="95%"><tr><td bgcolor="#fafafa"><div align="center"><?=$page_links?></div></td></tr></table>
<br /><br />
<?php
} else {
?>
No results were found
<?php
}

if (!$popup_window) {
?>
	<span style="font-style:italic;">Notes:</span><br />
	Rows marked in <span style="background-color:#EE90A0;">red</span> are locked by another user<br />
	Rows marked in <span style="background-color:#90A0EE;">blue</span> are locked by you<br />
	<br /><br />

	<input type="button" onclick="window.open('suppliers_list_printable.php?<?=http_build_query($get_vars)?>');" value="Print Suppliers List" />
	<input type="button" onclick="window.open('suppliers_wanted_printable.php?<?=http_build_query($get_vars)?>');" value="Print Wanted List" />
<?php
if (is_array($sections) && in_array('ADMIN', $sections)) {
?>
	<input type="button" value="Admin Print List" onclick="window.open('suppliers_print_admin.php?type=$type&order_by=$order_by&order=$order&by=$by&alpha_value=$alpha_value&show=$show&sub=$sub')" />
	<input type="button" value="Export Admin Print List" onclick="window.open('supplier_exportXLS.php?type=$type&order_by=$order_by&order=$order&by=$by&alpha_value=$alpha_value&show=$show&sub=$sub')" />
<?php
}
?>
	<br /><br />
</form>
<?php
} else {
?>
<input type="button" onclick="window.close();" value="Close Window" />
<?php
}

include_once 'templates/suppliers_footer.tpl.php';
?>
<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$criteria = $_GLOBALS['criteria_options']['forwarder'];

foreach (forwarderGetOptionList('all_fields') as $k => $v) {
    if (array_key_exists($k, $_REQUEST)) {
        if (is_string($_REQUEST[$k])) $criteria[$k] = preg_split('/[, \|]+/', $_REQUEST[$k]);
        else $criteria[$k] = $_REQUEST[$k];
    }
}

$sub_criteria = $criteria;
unset($sub_criteria['country']);
$item_list = forwarderSearch($sub_criteria);
if (count($item_list)) {
    $list_by1 = 'country';
    $list_by2 = '';
?>
<div style="text-align:left;">
	<a href="#" onclick="toggleSection('sectionViewByCountry');return false"><img name="imEx" src="/webadmin/images/triDown.gif" height="16" width="16" /> <b>Display Forwarders by Country</b></a>
	<br /><br />
	<div id="sectionViewByCountry" class="child" style="display:none">
<?php
include __DIR__ . '/navigate.tpl.php';
?>
	</div>
</div>
<?php
}

$sub_criteria = $criteria;
unset($sub_criteria['active']);
$item_list = forwarderSearch($sub_criteria);
if (count($item_list)) {
    $list_by1 = 'active';
    $list_by2 = '';
?>
<div style="text-align:left;">
	<a href="#" onclick="toggleSection('sectionViewByActive');return false"><img name="imEx" src="/webadmin/images/triDown.gif" height="16" width="16" /> <b>Display Forwarders by Active</b></a>
	<br /><br />
	<div id="sectionViewByActive" class="child" style="display:none">
<?php
include __DIR__ . '/navigate.tpl.php';
?>
	</div>
</div>
<?php
}

$sub_criteria = $criteria;
unset($sub_criteria['type']);
$item_list = forwarderSearch($sub_criteria);
if (count($item_list)) {
    $list_by1 = 'type';
    $list_by2 = '';
?>
<div style="text-align:left;">
	<a href="#" onclick="toggleSection('sectionViewByType');return false"><img name="imEx" src="/webadmin/images/triDown.gif" height="16" width="16" /> <b>Display Forwarders by Type</b></a>
	<br /><br />
	<div id="sectionViewByType" class="child" style="display:none">
<?php
include __DIR__ . '/navigate.tpl.php';
?>
	</div>
</div>
<?php
}

if (array_key_exists('active', $criteria) && $criteria['active'] === 'y') {
    $view_all_url = $_REQUEST;
    $view_all_url['action'] = 'list';
    $view_all_url['active'] = array('y', 'n');
    $view_all_url = '.?' . http_build_query($view_all_url);
    echo formatHTMLTitle('Freight Forwarders', '<a href="' . hecho($view_all_url, 1) . '" style="color:#00f">View All</a>');
} else {
    echo formatHTMLTitle('Freight Forwarders');
}

$item_list = forwarderSearch($criteria);

if (array_key_exists('sort', $_REQUEST) && in_array($_REQUEST['sort'], array(

))) {
    $item_list = forwarderSort($item_list, $_REQUEST['sort']);
}

if (array_key_exists('order', $_REQUEST) && $_REQUEST['order'] === 'desc') {
    $item_list = array_reverse($item_list);
}

$per_page = 50;
$count_total    = count($item_list);
if (array_key_exists('per_page', $_REQUEST)) {
    $per_page = max(10, min($count_total, intval($_REQUEST['per_page'])));
}
$item_list     = array_chunk($item_list, $per_page);

$count_pages   = count($item_list);
$page    = 0;
if (array_key_exists('page', $_REQUEST)) {
    $page = max(0, min($count_pages - 1, intval($_REQUEST['page'])));
}
$item_list     = $item_list[$page];

$count_display = count($item_list);

$page_url = $_REQUEST;
$page_url['action'] = 'list';
unset($page_url['page']);
$page_url = '.?' . http_build_query($page_url) . '&page=';

if ($count_total > 0) {
    $page_links = formatHTMLPageLinks($page_url, $page, $count_display, $count_total, $per_page);
?>
<?=$page_links?><br />
<br />
<?php

include __DIR__ . '/list.tpl.php';

?>
<br />
<?=$page_links?><br />
<br />
<?php
if ($popup_window) {
?>
<input type="button" onclick="window.close();" value="Close Window" />
<?php
}
} else {
?>
No results were found
<?php
}

$sub_criteria = $criteria;
unset($sub_criteria['initial']);
$item_list = forwarderSearch($sub_criteria);
if (count($item_list)) {
    $list_by1 = 'initial';
    $list_by2 = '';
?>
<div style="text-align:left;">
	<div id="sectionViewByInitial">
<?php
include __DIR__ . '/navigate.tpl.php';
?>
	</div>
</div>
<?php
}

include_once __DIR__ . '/footer.inc.php';

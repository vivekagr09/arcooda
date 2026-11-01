<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('member'); //memberSurveySearch, memberSearch

include_once __DIR__ . '/header.inc.php';

$order = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$page_id  = intval($posted['page_id']);
$order_by = strtolower($posted['order_by']);

//Build a search criteria array
$criteria = array();
foreach (supplierGetOptionList('all_fields') as $key => $value) {
    if ('' != $posted[$key]) {
        $criteria[$key] = explode('|', $posted[$key]);
    } elseif (array_key_exists($key, $posted)) {
        $criteria[$key] = $posted[$key];
    }
}

unset($criteria['supplier_id']);
//if($posted['search_for']!='') $criteria['any_text']=explode('|',$posted['search_for']);
if ('' != $posted['subset_list']) {
    $criteria['supplier_id'] = explode('|', $posted['subset_list']);
}

if ('' != $posted['option']) {
    $criteria['option'] = explode('|', $posted['option']);
}

if ('' != $posted['search_for']) {

    $chunk = explode('|', $posted['search_for']);

    foreach ($chunk as $v) {

        $spaceChunk = explode(' ', $v);

        foreach ($spaceChunk as $_v) {
            $criteria['any_text'][] = $_v;
        }

    }
}

/*$sub_criteria = $criteria;
unset($sub_criteria['country']);
$member_list = supplierSearch($sub_criteria);
if (count($member_list) > 0) {
    $list_by1 = 'country';
    $list_by2 = '';*/
?>
<!-- <div style="text-align:left;">
	<a href="javascript:void(toggleSection('sectionViewByCountry'))"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Country</b></a>
	<br /><br />
	<div id="sectionViewByCountry" class="child" style="display:none;">
<?php
//include __DIR__ . '/navigate.tpl.php';
?>
	</div>
</div> -->
<?php
/*}*/
/*$sub_criteria = $criteria;
unset($sub_criteria['site_id']);
$supplier_list = memberSearch($sub_criteria);
if (count($supplier_list) > 0) {
    $list_by1 = 'site_id';
    $list_by2 = '';*/
?>


<?php
//Find and pagenate the members
$supplier_list = supplierSearch($criteria);
/*if (array_key_exists('option', $criteria)) {
    $supplier_list = memberSurveySearch(array('member_id' => $member_list, 'option' => $criteria['option']));
}*/

$supplier_list = supplierSort($supplier_list, $order_by);
if ('desc' == $order) {
    $supplier_list = array_reverse($supplier_list);
}

$items_per_page = 50;
if ($_GLOBALS['staff']['members_per_page'] > 0) {
    $items_per_page = $_GLOBALS['staff']['members_per_page'];
}
$count_total = count($supplier_list);

$supplier_list   = array_chunk($supplier_list, $items_per_page);
$count_pages   = count($supplier_list);
$page_id       = max(0, min($count_pages - 1, $page_id));
$supplier_list   = $supplier_list[$page_id] ?? [];
$count_display = count($supplier_list);

if ($count_total > 0) {
    $page_url           = $get_vars;
    $page_url['action'] = 'list';
    unset($page_url['page_id']);
    $page_url = '.?' . http_build_query($page_url) . '&page_id=';

    $page_links = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);

    $select_url = $get_vars;
    unset($select_url['action']);
    $select_url = 'select.php?' . http_build_query($select_url);
?>
<br />
<form accept-charset="utf-8" autocomplete="off" method="post" action="<?php hecho($select_url) ?>">
<?php
include __DIR__ . '/list.tpl.php';
?>
</form>
<br />

<div class="wide" style="background:#fff;border:1px solid #eee;text-align:center;padding:4px;">
    <b>By Last Initial :</b>
<?php
$initial_url = $get_vars;
    unset($initial_url['page_id']);
    unset($initial_url['last_initial']);
    $initial_url = '.?' . http_build_query($initial_url) . '&action=list&last_initial=';
    for ($i = ord('A'); $i <= ord('Z'); $i++) {
        echo '<a href="' . hecho($initial_url, 1) . chr($i) . '">' . chr($i) . '</a>' . "\n";
    }

?>
</div>
<br />
<div class="wide" style="background:#fff;border:1px solid #eee;text-align:center;padding:4px;"><?=$page_links?></div>
<br />
<?php
} else {
?>
No results were found
<?php
}

if ($popup_window) {
?>
<input type="button" onclick="window.close();" value="Close Window" />
<?php
}

include_once __DIR__ . '/footer.inc.php';

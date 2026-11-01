<?php
include_once __DIR__ . '/common.inc.php';
include_once __DIR__ . '/header.inc.php';

$order = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$page_id  = intval($posted['page_id']);
$order_by = strtolower($posted['order_by']);

//Build a search criteria array
$criteria = array();
foreach (contractorGetOptionList('all_fields') as $key => $value) {
    if ('' != $posted[$key]) {
        $criteria[$key] = explode('|', $posted[$key]);
    } elseif (array_key_exists($key, $posted)) {
        $criteria[$key] = $posted[$key];
    }
}
unset($criteria['contractor_id']);
//if($posted['search_for']!='') $criteria['any_text']=explode('|',$posted['search_for']);
if ('' != $posted['subset_list']) {
    $criteria['contractor_id'] = explode('|', $posted['subset_list']);
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

$sub_criteria = $criteria;
unset($sub_criteria['country']);
$contractor_query = contractorQuery($sub_criteria);
$contractor_list = contractorSearch($contractor_query);
if (count($contractor_list) > 0) {
    $list_by1 = 'country';
    $list_by2 = '';
?>
<div style="text-align:left;">
	<a href="javascript:void(toggleSection('sectionViewByCountry'))"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Country</b></a>
	<br /><br />
	<div id="sectionViewByCountry" class="child" style="display:none;">
<?php
include __DIR__ . '/navigate.tpl.php';
?>
	</div>
</div>
<?php
}

$sub_criteria = $criteria;
unset($sub_criteria['contractor_category']);
unset($sub_criteria['site_id']);
$contractor_query = contractorQuery($sub_criteria);
$contractor_list = contractorSearch($contractor_query);
if (count($contractor_list) > 0) {
    $list_by1 = 'contractor_category';
    $list_by2 = '';
?>
<div style="text-align:left;">
	<a href="javascript:void(toggleSection('sectionViewBycategory'))"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Category</b></a>
	<br /><br />
	<div id="sectionViewBycategory" class="child" style="display:none;">
<?php
include __DIR__ . '/navigate.tpl.php';
?>
	</div>
</div>
<?php
}

$contractor_query = contractorQuery($criteria);
$contractor_list = contractorSearch($contractor_query);
if (array_key_exists('option', $criteria)) {
    $contractor_list = contractorSurveySearch(array('contractor_id' => $contractor_list, 'option' => $criteria['option']));
}
if(isset($posted['order_by'])){
    $contractor_list = contractorSort($contractor_list, $order_by);
}


if ('desc' == $order) {
    $contractor_list = array_reverse($contractor_list);
}

$items_per_page = 50;
if ($_GLOBALS['staff']['contractors_per_page'] > 0) {
    $items_per_page = $_GLOBALS['staff']['contractors_per_page'];
}

$count_total = count($contractor_list);

$contractor_list   = array_chunk($contractor_list, $items_per_page);
$count_pages   = count($contractor_list);
$page_id       = max(0, min($count_pages - 1, $page_id));
$contractor_list   = $contractor_list[$page_id] ?? [];
$count_display = count($contractor_list);

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

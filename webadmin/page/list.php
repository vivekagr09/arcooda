<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$order_by = 'rank';
$order = 'asc';
$page_id = 0;

if (array_key_exists('order_by', $_REQUEST)) {
	$order_by = (in_array($_REQUEST['order_by'], array(
		'rank',
		'id',
		'name',
		'created',
		'modified',
		'modified_staff_id',
		'active',
	))) ? $_REQUEST['order_by'] : 'rank';
}
if (array_key_exists('order', $_REQUEST)) {
	$order = ('desc' != $_REQUEST['order']) ? 'asc' : 'desc';
}
if (array_key_exists('page_id', $_REQUEST)) {
	$page_id = intval($_REQUEST['page_id']);
}

//Build a search criteria array
$criteria              = array();
$criteria['parent_id'] = 0;
if (array_key_exists("id", $_REQUEST)) {
    $criteria["parent_id"] = intval($_REQUEST["id"]);
}

foreach (pageGetOptionList('all_fields') as $k => $v) {
	if (array_key_exists($k, $_REQUEST)) {
		if (is_array($_REQUEST[$k])) {
			$criteria[$k] = $_REQUEST[$k];
		} else {
			$criteria[$k] = preg_split('/[^0-9]+/', $_REQUEST[$k]);
		}
	}
}
foreach (array(
	'search_for' => 'any_text',
	'subset_list' => 'id',
) as $k => $v) {
	if (array_key_exists($k, $_REQUEST)) {
		if (is_array($_REQUEST[$k])) {
			$criteria[$v] = $_REQUEST[$k];
		} else {
			$criteria[$v] = preg_split('/\\s+/', $_REQUEST[$k]);
		}
	}
}


/*
$search_for = '';
if (array_key_exists('search_for', $_REQUEST)) {
	$search_for = $_REQUEST['search_for'];
}
?>
<div style="text-align:left;">
	<a href="#" onclick="$('#sectionSearch').toggle('slow');return false" class="toggleBtn" style="cursor:pointer;text-decoration:underline;"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>Search</b></a>
	<br /><br />
	<div id="sectionSearch" class="child"<?=('' == $search_for) ? ' style="display:none;"' : ''?>>
<?php
include __DIR__ . '/search.tpl.php';
?>
	</div>
</div>
<?php
*/


$parent_id = array();
if (array_key_exists('parent_id', $_REQUEST)) {
	$parent_id = preg_split('/[^0-9]+/', $_REQUEST['parent_id']);
}
?>
<div style="text-align:left;">
	<a href="" onclick="$('#sectionNavigate').toggle('slow'); return false;" class="toggleBtn" style="cursor:pointer;text-decoration:underline;"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>Navigate</b></a>
	<br /><br />
	<div id="sectionNavigate" class="child"<?=(count($parent_id)) ? '' : ' style="display:none;"'?>>
		<form action="" method="get"><table class="table_search">
			<tr><th>Choose a sub-section to view:</th></tr>
			<tr><td><select rows="20" onchange="this.form.submit()" name="parent_id">
				<option value="0">-Root-</option>
				<?=formatHTMLOptions(pageGetOptionList('parent_id'), $parent_id)?>
			</select></td></tr>
		</table></form>
	</div>
</div>


<?=formatHTMLTitle('Static Pages')?>
<?php
//Find and pagenate the pages
$item_list = pageSearch($criteria);
$item_list = pageSort($item_list, $order_by);
if ('desc' == $order) {
    $item_list = array_reverse($item_list);
}

// now check for the selected list..
$selected_list = array();
if (array_key_exists('selected_list', $_REQUEST)) {
	$selected_list = preg_split('/[^0-9]+/', $_REQUEST['selected_list']);
}
if (count($selected_list)) {
    $flipped = array_flip($item_list);
    foreach ($selected_list as $id) {
        unset($flipped[$id]);
    }

    $item_list = array_flip($flipped);
}

$items_per_page = 50;
$count_total    = count($item_list);

if ($count_total > 0) {
    $item_list     = array_chunk($item_list, $items_per_page);
    $count_pages   = count($item_list);
    $page_id       = max(0, min($count_pages - 1, $page_id));
    $item_list     = $item_list[$page_id];
    $count_display = count($item_list);

    $page_url = $get_vars;
    unset($page_url['page_id']);
    $page_url = 'index.php?' . http_build_query($page_url) . '&page_id=';

    $page_links = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);

    $select_url = $get_vars;
    unset($select_url['action']);
    $select_url = 'select.php?' . http_build_query($select_url);
?>
<br />
<form action="<?php hecho($select_url)?>" method="post">
<?php
	include __DIR__ . '/list.tpl.php';
?>
</form>
<br />
<?=$page_links?>
<br />
<?php
} else {
?>
No results were found
<?php
}

if ($popup_window) {
?>
<br /><br /><input type="button" onclick="window.close();" value="Close Window" /><br /><br />&nbsp;
<?php
}

include_once __DIR__ . '/footer.inc.php';

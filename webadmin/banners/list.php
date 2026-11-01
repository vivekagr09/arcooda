<?php
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;
global $popup_window;

include_once __DIR__ . '/header.inc.php';

$order = $_REQUEST['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$page     = intval($_REQUEST['page']);
$order_by = (isset($_REQUEST['order_by'])) ? $_REQUEST['order_by'] : 'id';

//Build a search criteria array
$criteria = $_GLOBALS['criteria_options']['banner'];
unset($criteria['active']);
unset($criteria["active_on"]);
foreach (bannerGetOptionList('all_fields') as $k => $v) {
    if (is_string($_REQUEST[$k])) {
        $criteria[$k] = explode('|', $_REQUEST[$k]);
    }
}

if ('' != $_REQUEST['search_for']) {
    $criteria['any_text'] = explode('|', $_REQUEST['search_for']);
}

if ('' != $_REQUEST['show']) {
    $criteria[$_REQUEST['show']] = explode('|', $_REQUEST['show_id']);
}

//Find and pagenate the banners
$item_list = bannerSearch($criteria);
$item_list = bannerSort($item_list, $order_by);
if ('desc' == $order) {
    $item_list = array_reverse($item_list);
}

$items_per_page = 50;
if ($_GLOBALS['staff']['banners_per_page'] > 0) {
    $items_per_page = $_GLOBALS['staff']['banners_per_page'];
}

$count_total = count($item_list);

?>
<?=formatHTMLTitle('Banners')?>
<?php

$item_list   = array_chunk($item_list, $items_per_page);
$count_pages   = count($item_list);
$page          = max(0, min($count_pages - 1, $page));
$item_list   = $item_list[$page];
$count_display = count($item_list);
if ($count_total > 0) {

    $page_url = $_REQUEST;
    unset($page_url['page']);
    $page_url   = '?' . http_build_query($page_url + array('page' => ''));
    $page_links = formatHTMLPageLinks($page_url, $page, $count_display, $count_total, $items_per_page);

    //Begin list.tpl.php

    $alt     = false;
    $ord_img = '<img src="/webadmin/' . (('desc' == $order) ? 'desc' : 'asc') . '.gif" alt="' . (('desc' == $order) ? 'desc' : 'asc') . '" />';

    $base_url  = $_REQUEST;
    unset($base_url['action']);
    unset($base_url['id']);
    $edit_url   = '.?' . http_build_query($base_url + array('action' => 'edit', 'id' => ''));
    $select_url = '.?' . http_build_query($base_url + array('action' => 'select', 'id' => ''));
    $status_url = '.?' . http_build_query($base_url + array('id' => ''));
    unset($base_url['order']);
    unset($base_url['order_by']);
    $sort_url   = '.?' . http_build_query($base_url + array('action' => 'list', 'order' => ('desc' == $order) ? 'asc' : 'desc', 'order_by' => ''));

    $active_list    = bannerGetOptionList('active');
    $item_type_list = bannerGetOptionList('banner_type');
?>
<br />
<table class="wide spacetable table_grid" align="center">
  <tr>
    <th><a href="<?= htmlspecialchars($sort_url . 'id')?>">ID</a><?=('id' == $order_by) ? $ord_img : ''?></th>
    <th><a href="<?= htmlspecialchars($sort_url . 'text')?>">Name</a><?=('text' == $order_by) ? $ord_img : ''?></th>
    <th style="width:336px;">Image</th>
    <th><a href="<?= htmlspecialchars($sort_url . 'banner_type')?>">Banner Type</a><?=('banner_type' == $order_by) ? $ord_img : ''?></th>
    <th><a href="<?= htmlspecialchars($sort_url . 'active')?>">Active</a><?=('active' == $order_by) ? $ord_img : ''?></th>
    <th><a href="<?= htmlspecialchars($sort_url . 'clicks')?>">Total Clicks</a><?=('clicks' == $order_by) ? $ord_img : ''?></th>
    <th><a href="<?= htmlspecialchars($sort_url . 'views')?>">Total Views</a><?=('views' == $order_by) ? $ord_img : ''?></th>
    <th></th>
  </tr>
<?php
    foreach ($item_list as $id) {
        $item = bannerFromDatabase($id);
        $class  = '';
        if ($alt = !$alt) {
            $class = ' class="alt"';
        }
?>
  <tr<?=$class?>>
    <td style="text-align:center;"><?=$id?></td>
    <td style="text-align:center;"><?= htmlspecialchars($item['text'])?></td>
    <td style="text-align:center;"><img style="max-width:336px;" src="<?= htmlspecialchars($item['image_url'])?>" alt="<?= htmlspecialchars($item['alt'])?>" /></td>
    <td style="text-align:center;"><?=$item_type_list[$item['banner_type']]?></td>
    <td style="text-align:center;"><?=$active_list[$item['active']]?></td>
    <td style="text-align:center;"><?=$item['clicks']?></td>
    <td style="text-align:center;"><?=$item['views']?></td>
<?php
        $links = array();
        if ('' != $_REQUEST['return_message']) {
            $links[] = '<a href="' . htmlspecialchars($select_url . $id) . '">Select</a>';
        } else {
            $links[] = '<a href="' . htmlspecialchars($edit_url . $id) . '" target="_blank">Edit</a>';
            $word    = (('y' == $item['active']) ? "De-" : "") . "Activate";
            $anc     = strtolower(str_replace('-', '', $word));
            $links[] = '<a href="' . htmlspecialchars($status_url . $id . '&action=' . $anc) . '">' . $word . '</a>';
            $links[] = '<a onclick="return confirm(\'Are you sure you want to delete this banner?\')" href="' . htmlspecialchars($status_url . $id . '&action=delete') . '">Delete</a>';
            $links[] = '<a href=".?action=clickReset&id=' . $id . '">Reset Total Clicks</a>';
            $links[] = '<a href=".?action=viewReset&id=' . $id . '">Reset Total Views</a>';
        }
?>
    <td style="text-align:center;white-space:nowrap;width:36pt;"><?=implode('<br />', $links)?></td>
  </tr>
<?php
    }
?>
</table>

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

include_once __DIR__ . '/footer.inc.php';

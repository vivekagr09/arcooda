<?php
include_once __DIR__ . "/common.inc.php";

include_once __DIR__ . "/header.inc.php";

$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value)) {
            unset($get_vars[$key]);
        }
    }
}

$like_tag   = false;
$search_for = (isset($posted['search_for'])) ? $posted['search_for'] : '';
$page       = intval($posted['page']);
$order_by   = (isset($posted['order_by'])) ? $posted['order_by'] : 'id';
$order      = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$by = $posted['by'];

$search_url           = $get_vars;
$search_url['action'] = 'list';
unset($search_url['search_for']);
$search_url = '.?' . http_build_query($search_url);
?>
<link rel="stylesheet" type="text/css" href="style.css" />
<center><?=formatHTMLTitle('Season Graphics')?></center>
<?php
$meta_list = graphicsSearch($search_for, '', $like_tag);
$meta_list = graphicsSort($meta_list, $order_by);
if ('desc' == $order) {
    $meta_list = array_reverse($meta_list);
}

$items_per_page = 50;
$count_total    = count($meta_list);
$meta_list      = array_chunk($meta_list, $items_per_page);
$count_pages    = count($meta_list);
$page           = max(0, min($count_pages - 1, $page));
$meta_list      = $meta_list[$page];
$count_display  = count($meta_list);
if ($count_total > 0) {
    $sort_url = $get_vars;
    unset($sort_url['order_by']);
    unset($sort_url['order']);
    $sort_url = '?' . http_build_query($sort_url);

    $select_url           = $get_vars;
    $select_url['action'] = ('test' == $posted['return_message']) ? 'select' : 'select_access';
    unset($select_url['meta_id']);
    $select_url = '.?' . http_build_query($select_url);

    $edit_url           = $get_vars;
    $edit_url['action'] = 'edit';
    unset($edit_url['meta_id']);
    $edit_url = '.?' . http_build_query($edit_url);

    $view_url           = $get_vars;
    $view_url['action'] = 'view';
    unset($view_url['meta_id']);
    $view_url = '.?' . http_build_query($view_url);

    $status_url = $get_vars;
    unset($status_url['meta_id']);
    $status_url = '.?' . http_build_query($status_url);

    $page_url = $get_vars;
    unset($page_url['page']);
    $page_url   = '?' . http_build_query($page_url);
    $page_links = formatHTMLPageLinks($page_url . '&page=', $page, $count_display, $count_total, $items_per_page, 'style="color:blue"');
?>
	<center><?=$page_links?></center><br />
	<br />

	<table style="width:95%" class="wide" align="center" cellspacing=0 cellpadding=2 border=0>
		<tr>
			<th style="text-align:center"><a href="<?=$sort_url?>&order_by=title&order=<?=('title' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Theme Name</a></th>
			<th style="text-align:center"><a href="<?=$sort_url?>&order_by=start_date&order=<?=('start_date' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Start Date</a></th>
			<th style="text-align:center"><a href="<?=$sort_url?>&order_by=end_date&order=<?=('end_date' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Finishing Date</a></th>
			<th style="text-align:center"><a href="<?=$sort_url?>&order_by=status&order=<?=('status' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Status</a></th>
			<th style=""></th>
		</tr>
<?php
    $alt = true;
    foreach ($meta_list as $meta_id) {
        $meta  = graphicsFromDatabase($meta_id);
        $class = '';
        if ($alt = !$alt) {
            $class = ' class="alt"';
        }
?>
		<tr <?=$class?>>
			<td style="text-align:center"><?=$meta['title']?></td>
			<td style="text-align:center"><?=date("jS M", strtotime($meta['start_date']))?></td>
			<td style="text-align:center"><?=date("jS M", strtotime($meta['end_date']))?></a></td>
			<td style="text-align:center"><a href=".?action=do_toggle&amp;id=<?=$meta_id?>"><?=('y' == $meta['status']) ? 'Active' : 'De-Active'?></a></td>
<?php
        $links   = array();
        $links[] = '<a href="' . $edit_url . '&id=' . $meta_id . '" target="_blank">Edit</a>';
        $links[] = '<a onclick="return confirm(\'Are you sure you want to delete this Page META Data?\')" href="' . $status_url . '&action=delete&id=' . $meta_id . '">Delete</a>';
?>
			<td style="text-align:center"><?=implode('<br />', $links)?></td>
		</tr>
<?php
    }
?>
	</table>
<?php
} else {
?>
	No results were found
<?php
}

include_once __DIR__ . "/footer.inc.php";

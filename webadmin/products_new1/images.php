<?php
include_once __DIR__ . '/common.inc.php';

$popup_window = ('' != $_REQUEST['return_message']);

include_once __DIR__ . '/header.inc.php';

$order = $_REQUEST['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$page_id  = intval($_REQUEST['page_id']);
$order_by = strtolower($_REQUEST['order_by']);

$criteria = array();
if (array_key_exists('search_for', $_REQUEST)) {
    foreach (array('search_for') as $k => $v) {
        if (array_key_exists($k, $_REQUEST) && !empty($_REQUEST[$k])) {
            $criteria['any_text'] = (is_array($_REQUEST[$k])) ? $_REQUEST[$k] : preg_split('/[^a-zA-Z0-9]+/', $_REQUEST[$k]);
        }
    }
}
foreach (productGetOptionList('all_fields') as $k => $v) {
    if (array_key_exists($k, $_REQUEST) && !empty($_REQUEST[$k])) {
        $criteria[$k] = (is_array($_REQUEST[$k])) ? $_REQUEST[$k] : preg_split('/[^a-zA-Z0-9]+/', $_REQUEST[$k]);
    }
}
foreach (productGetOptionList('joined_fields') as $join => $columns) {
    foreach ($columns as $k => $v) {
        if (array_key_exists($k, $_REQUEST) && !empty($_REQUEST[$k])) {
            $criteria[$k] = (is_array($_REQUEST[$k])) ? $_REQUEST[$k] : preg_split('/[^a-zA-Z0-9]+/', $_REQUEST[$k]);
        }
    }
}

$item_query = productQuery($criteria);
// now have to check if the thumbnail actually exists for these products..
$cache = array();
//foreach($temp as $list) $cache+= productsFromDatabase($list);
$item_list = productSort($item_query, $order_by);
$temp      = array_chunk($item_list, 200);
if ('desc' == $order) {
    $item_list = array_reverse($item_list);
}

$action = 'images';

include __DIR__ . '/_search.tpl.php';

$items_per_page = $current_staff['products_per_page']; //50;
if ('' == $items_per_page) {
    $items_per_page = 50;
}

$count_total   = count($item_list);
$item_list     = array_chunk($item_list, $items_per_page, true);
$count_pages   = count($item_list);
$page_id       = max(0, min($count_pages - 1, $page_id));
$item_list     = $item_list[$page_id];
$count_display = count($item_list);

echo formatHTMLTitle('Select the image you want to link to');
if ($count_total > 0) {
    $page_url            = $_GET;
    unset($page_url['page_id']);
    $page_url = '?' . http_build_query($page_url);

    $select_url           = $_GET;
    $select_url['action'] = 'select';
    unset($select_url['id']);
    $select_url = '.?' . http_build_query($select_url) . '&id=';

    $page_links = formatHTMLPageLinks($page_url . '&page_id=', $page_id, $count_display, $count_total, $items_per_page);
    // now chunk the product list into 5 to show per row...
    $chunks    = 5;
    $item_list = array_chunk($item_list, $chunks);
?>
<table class="table_grid">
<?php
$count = 0;
    foreach ($item_list as $row => $prod_list) {
?>
    <tr style="text-align:center"<?=$count++ % 2 == 1 ? ' class="alt"' : ''?>>
<?php
foreach ($prod_list as $id) {
            if (isset($cache[$id])) {
                $item = $cache[$id];
            } else {
                $item = productFromDatabase($id);
            }

            if ('' != $item['thumb_url']) {
                $caption = hecho($item['product_name'], 1);
?>
        <td style="width:20%">
            #<?=$item['product_number']?><br />
            <a href="<?=$item['view_url']?>" target="_blank"><img src="<?=$item['thumb_url']?>" style="border:1px solid #ccc;max-width:96px;max-height:96px;" ></a>
            <br /><a href="<?=$select_url . $product_id?>"><?=$caption?></a>
        </td>
<?php
}
        }
?>
    </tr>
<?php
}
?>
</table>
<br />
<div style="text-align:center;">
    <?=$page_links?>
</div>
<br />
<?php
} else {
?>
<br />
<div style="text-align:center;">
    No Products found..
</div>
<?php
}

include_once __DIR__ . '/footer.inc.php';

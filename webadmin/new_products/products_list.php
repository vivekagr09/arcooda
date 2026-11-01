<?php
include_once __DIR__ . '/products_common.inc.php';
include_once __DIR__ . '/templates/products_header.tpl.php';

$posted = array_merge($_GET, $_POST);

$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value)) {
            unset($get_vars[$key]);
        }
    }
}

$popup_window = (isset($posted['return_message'])) ? true : false;
$search_for   = (isset($posted['search_for'])) ? $posted['search_for'] : '';
$page         = intval($posted['page']);
$order_by     = (isset($posted['order_by'])) ? $posted['order_by'] : 'product_id';
$order        = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$search_url           = $get_vars;
$search_url['action'] = 'list';
unset($search_url['search_for']);
$search_url = 'index.php?' . http_build_query($search_url);
?>
<div align="right">
	<form accept-charset="utf-8" action="<?=$search_url?>" method="post">
		<input type="text" name="search_for" value="<?=str_replace('"', '&quot;', $search_for)?>" />
		<input type="submit" value="Search" />
	</form>
</div>
<?php

$site_list = getWebsitesList();
if (isset($posted['site_id']) && isset($site_list[$posted['site_id']])) {
    $new_list                     = array();
    $new_list[$posted['site_id']] = $site_list[$posted['site_id']];
    $site_list                    = $new_list;
}

foreach ($site_list as $site_id => $site_name) {
?>
	<?=formatHTMLTitle($site_name)?>
<?php
    $product_list = productSearch($search_for);
    $product_list = productSort($product_list, $order_by, $site_id);
    if ('desc' == $order) {
        $product_list = array_reverse($product_list);
    }

    $items_per_page = 50;
    $count_total    = count($product_list);
    $product_list   = array_chunk($product_list, $items_per_page);
    $count_pages    = count($product_list);
    $page           = max(0, min($count_pages - 1, $page));
    $product_list   = $product_list[$page];
    $count_display  = count($product_list);

    if ($count_total > 0) {
        $sort_url            = $get_vars;
        $sort_url['site_id'] = $site_id;
        unset($sort_url['order_by']);
        unset($sort_url['order']);
        $sort_url = '?' . http_build_query($sort_url);

        $select_url            = $get_vars;
        $select_url['action']  = 'select';
        $select_url['site_id'] = $site_id;
        unset($select_url['product_id']);
        $select_url = 'index.php?' . http_build_query($select_url);

        $edit_url            = $get_vars;
        $edit_url['action']  = 'edit';
        $edit_url['site_id'] = $site_id;
        unset($edit_url['product_id']);
        $edit_url = 'index.php?' . http_build_query($edit_url);

        $page_url            = $get_vars;
        $page_url['site_id'] = $site_id;
        unset($page_url['page']);
        $page_url = '?' . http_build_query($page_url);

        $page_links = formatHTMLPageLinks($page_url . '&page=', $page, $count_display, $count_total, $items_per_page);
?>
	<?=$page_links?><br />
	<br />

	<table class="wide" align="center">
		<tr>
			<th style="width:36pt;"><a href="<?=$sort_url?>&order_by=product_id&order=<?=('product_id' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">ID</a></th>
			<th style="width:auto;"><a href="<?=$sort_url?>&order_by=product_name&order=<?=('product_name' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Product Name</a></th>
			<th style="width:36pt;"><a href="<?=$sort_url?>&order_by=product_active&order=<?=('product_active' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Active</a></th>
			<th style="width:36pt;"><a href="<?=$sort_url?>&order_by=cost_price&order=<?=('cost_price' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Cost Price<br />(USD)</a></th>
			<th style="width:36pt;"><a href="<?=$sort_url?>&order_by=sales_price&order=<?=('sales_price' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Sales Price<br />(USD)</a></th>
			<th style="width:36pt;"></th>
		</tr>
<?php
$alt = false;
        foreach ($product_list as $product_id) {
            $product = productFromDatabase($product_id, $site_id);
?>
		<tr<?=($alt = !$alt) ? '' : ' class="alt"'?>>
			<td style="text-align:right;width:36pt;"><?=$product_id?></td>
			<td style="text-align:left;width:auto;"><a target="_blank" href="<?=$product['product_url']?>"><?=str_replace('<', '&lt;', $product['product_name'])?></a></td>
			<td style="text-align:center;width:36pt;"><?=$product['product_active']?></td>
			<td style="text-align:right;width:36pt;"><?=convertCurrency($product['cost_price'], $product['cost_currency'], 'USD')?></td>
			<td style="text-align:right;width:36pt;"><?=convertCurrency($product['sales_price'], $product['sales_currency'], 'USD')?></td>
<?php
$links   = array();
            $links[] = '<a href="' . $edit_url . '&product_id=' . $product_id . '">Edit</a>';
            if ($popup_window) {
                $links[] = '<a href="' . $select_url . '&product_id=' . $product_id . '">Select</a>';
            }

?>
			<td style="text-align:center;white-space:nowrap;width:36pt;"><?=implode('<br />', $links)?></td>
		</tr>
<?php
}
?>
	</table>

	<br />
	<br />
<?php
} else {
?>
	No results were found
<?php
}
}

include_once 'templates/products_footer.tpl.php';
?>
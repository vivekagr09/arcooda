<?php
include_once __DIR__ . '/common.inc.php';

$search = true;

include_once __DIR__ . '/header.inc.php';

$linked_id = intval($_REQUEST['linked_id']);
$order     = $_REQUEST['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$page_id  = intval($_REQUEST['page_id']);
$order_by = strtolower($_REQUEST['order_by']);

$criteria               = array();
$criteria['alert_flag'] = 'y';
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

if ('' == $_REQUEST['return_message']) {
    //Put some navigation stuff here
}

$site_list = array($_GLOBALS['site_settings']['site_id'] => $_GLOBALS['site_settings']['site_name']);

?>
<div style="margin:auto;width:95%;">
<?php
foreach (array(
    'storage' => 'in the Storage Location',
    'stock_type' => 'of Stock Type',
    'category_id' => 'in the Category',
    'category' => 'in the Category',
    'type' => 'with Type',
) as $k => $v) {
    if ('' != $_REQUEST[$k]) {
        $temp_list = productGetOptionList($k);
        if ('' != $temp_list[$_REQUEST[$k]]) {
            echo '<p><b>This Page shows all the products ' . $v . ': ' . hecho($temp_list[$_REQUEST[$k]], 1) . '</b></p>';
        }

    }
}
?>
</div>
<?php

$select_vars           = $_GET;
$select_vars['action'] = 'select';

foreach ($site_list as $site_id => $site_name) {
    $item_query          = productQuery($criteria);
    $item_list           = productSort($item_query, $order_by);
    if ('desc' == $order) {
        $item_list = array_reverse($item_list, true);
    }

    foreach (explode('|', $_REQUEST['hide_list']) as $k) {
        $key = array_search($k, $item_list);
        if (false !== $key) {
            unset($item_list[$key]);
        }

    }
    $items_per_page = 50;
    if ($_GLOBALS['staff']['products_per_page'] > 0) {
        $items_per_page = $_GLOBALS['staff']['products_per_page'];
    }

    $count_total   = count($item_list);
    $item_list     = array_chunk($item_list, $items_per_page);
    $count_pages   = count($item_list);
    $page_id       = max(0, min($count_pages - 1, $page_id));
    $item_list     = $item_list[$page_id];
    $count_display = count($item_list);
?>
<?=formatHTMLTitle($site_name . " Products")?>
<form action="select.php?<?=http_build_query($select_vars)?>" method="post">
<?php
if ($count_total > 0) {
        $ignore_ids = explode('|', $_REQUEST['ignore_list']);
        if ($linked_id) {
            $ignore_ids[] = $linked_id;
            $related      = productRelatedFromDatabase($linked_id);
            foreach ($related as $tmp) {
                $ignore_ids[] = $tmp;
            }

            $ignore_ids = array_unique($ignore_ids);
        }

        $page_url            = $_GET;
        unset($page_url['page_id']);
        $page_url   = 'alert.php?' . http_build_query($page_url) . '&page_id=';
        $page_links = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);

        include __DIR__ . '/alert.tpl.php';
?>
</form>
<?=$page_links?>
<?php
} else {
        echo 'No results were found';
    }

?>
<br />
<br />
<?php
}
if ($popup_window) {
?>
<input type="button" onclick="window.close();" value="Close Window" />
<?php
} else {
?>
<input type="button" onclick="<?php hecho("window.open(" . jecho('alert_print.php?' . http_build_query($_REQUEST), 1) . ");")?>" value="Print" />
<?php
}

include_once __DIR__ . '/footer.inc.php';

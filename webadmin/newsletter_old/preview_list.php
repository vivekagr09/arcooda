<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action = 'preview_list';
if (is_array($posted[$section_name])) {
    if (!isset($posted[$section_name]['id'])) {
        $posted['newsletter_list_rebuild'] = 'on';
    }

    foreach (array('active_id', 'mailinglist_id', 'blocklist_id', 'interest_id', 'business_id') as $key) {
        if (is_array($posted[$section_name][$key])) {
            $posted[$section_name][$key] = implode(',', $posted[$section_name][$key]);
        }

        if ('' == $posted['newsletter_list_rebuild']) {
            unset($posted[$section_name][$key]);
        }

    }
    $item = $section_handler->fromPost($posted[$section_name]);
    $id   = intval($item['id']);
} else {
    $id   = intval($posted['id']);
    $item = $section_handler->fromDatabase($id);
}

$staff_id = $_GLOBALS['staff']['id'];
$site_id  = $_GLOBALS['site_settings']['site_id'];

//Find and pagenate the items
if ('' != $posted['newsletter_list_rebuild']) {
    $item_list = $section_handler->generateMemberList($item);
    foreach ($item_list as $key => $value) {
        $item_list[$key] = array(
            'newsletter_id' => $id,
            //'member_id' => ,
            'email_address' => $value,
            'email_status'  => 'listed',
        );
    }

} else {
    $item_list = $section_handler->mailinglistFromDatabase($id);
}

if ('' != $posted['email_status']) {
    foreach ($item_list as $key => $value) {
        if ($value['email_status'] != $posted['email_status']) {
            unset($item_list[$key]);
        }
    }
}

//$item_list=$section_handler->sort($item_list,$order_by);
if ('desc' == $order) {
    $item_list = array_reverse($item_list);
}

$items_per_page = 50;
//if($_GLOBALS['staff']['per_page']>0) $items_per_page=$_GLOBALS['staff']['per_page'];
$count_total = count($item_list);

$item_list     = array_chunk($item_list, $items_per_page);
$count_pages   = count($item_list);
$page_id       = max(0, min($count_pages - 1, $page_id));
$item_list     = $item_list[$page_id];
$count_display = count($item_list);

//Nasty cheat, but I guess acceptable since we know the url won't bloat too much
foreach ($posted as $key => $value) {
    $get_vars[$key] = $value;
}

if ($count_total > 0) {
    $page_url           = $get_vars;
    $page_url['action'] = 'list';
    unset($page_url['page_id']);
    $page_url = 'preview_list.php?' . http_build_query($page_url) . '&page_id=';

    $page_links = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);
?>
<br />
<form accept-charset="utf-8" action="<?php hecho($select_url)?>" method="post">
<?php

	include __DIR__ . '/mailinglist_list.tpl.php';

?>
</form>
<br />
<table align="center" cellpadding="7" width="95%"><tr><td bgcolor="#fafafa"><div align="center"><?=$page_links?></div></td></tr></table>
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

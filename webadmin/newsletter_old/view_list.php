<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('member'); //memberSort

include_once __DIR__ . '/header.inc.php';

$action = 'view_list';
$id     = intval($posted['id']);
$item   = $section_handler->fromDatabase($id);

$staff_id = $_GLOBALS['staff']['id'];
$site_id  = $_GLOBALS['site_settings']['site_id'];

//Find and pagenate the items
$item_list = $section_handler->mailinglistFromDatabase($id);

if ('' != $posted['email_status']) {
    $email_status = explode('|', $posted['email_status']);
    foreach ($item_list as $k => $v) {
        if (!in_array($v['email_status'], $email_status)) {
            unset($item_list[$k]);
        }
    }

}
if ('' != $posted['member_id']) {
    $member_id = explode('|', $posted['member_id']);
    foreach ($item_list as $k => $v) {
        if (!in_array($v['member_id'], $member_id)) {
            unset($item_list[$k]);
        }
    }

}

//Sort hack, until someone puts it in the section handler
/*
$temp = array();
foreach($item_list as $v) $temp[$v['member_id']]=$v;
$member_list = array();
foreach($temp as $v) $member_list[]=$v['member_id'];
$item_list = array();
foreach(memberSort($member_list,$posted['order_by']) as $v) $item_list[$v]=$temp[$v];
 */

//$item_list = $section_handler->sort($item_list, $order_by);
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
    $page_url = 'view_list.php?' . http_build_query($page_url) . '&page_id=';

    $page_links = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);
?>
<br />
<form accept-charset="utf-8" action="<?php hecho($select_url)?>" method="post">
<?php
    //include __DIR__ . '/list_members.tpl.php';
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

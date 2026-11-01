<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('member'); //memberSort

include_once __DIR__ . '/header.inc.php';

$action           = 'subscribers';
$posted['active'] = array('y', 'n');

$member_list = $section_handler->generateMemberList($posted);

$order = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$page_id  = intval($posted['page_id']);
$order_by = strtolower($posted['order_by']);

$criteria = array();
foreach (array('member_id', 'country', 'status', 'member_type', 'flag', 'account_type', 'mailing_list', 'reseller', 'partner', 'best_described', 'currency', 'products_per_page', 'news_per_page', 'mailing_products', 'last_initial') as $key) {
    if ('' != $posted[$key]) {
        $criteria[$key] = explode('|', $posted[$key]);
    }
}

if ('' != $posted['search_for']) {
    $criteria['any_text'] = explode(' ', $posted['search_for']);
}

if ('' != $posted['member_type']) {
    $criteria['member_type'] = explode('|', $posted['member_type']);
}

// now have to sort out the site_id array - mainly for the search part..
if (is_array($posted['site_id'])) {
    foreach ($posted['site_id'] as $key => $tmp) {
        if ('' == $tmp) {
            unset($posted['site_id'][$key]);
        }
    }
}

if ('' != $posted['site_id']) {
    if (!is_array($posted['site_id']) && '' != $posted['site_id']) {
        $criteria['site_id'] = explode('|', $posted['site_id']);
    } elseif (count($posted['site_id'])) {
        $criteria['site_id'] = $posted['site_id'];
    }

}

$items_per_page = 50;
if ($_GLOBALS['staff']['members_per_page'] > 0) {
    $items_per_page = $_GLOBALS['staff']['members_per_page'];
}

//Find and pagenate the members
if ('' != $posted['last_initial']) {
    $order_by = 'last_name';
    $order    = 'asc';}
$member_list = memberSort($member_list, $order_by);
if ('desc' == $order) {
    $member_list = array_reverse($member_list);
}

$count_total   = count($member_list);
$member_list   = array_chunk($member_list, $items_per_page);
$count_pages   = count($member_list);
$page_id       = max(0, min($count_pages - 1, $page_id));
$member_list   = $member_list[$page_id];
$count_display = count($member_list);

$add_url           = $get_vars;
$add_url['action'] = 'add';
$add_url           = 'index.php?' . http_build_query($add_url);

?>
<?=formatHTMLTitle('Members', '<i><a style="color:black;" href="' . hecho($add_url, 1) . '">Add New Member</a></i>')?>
<?php

if ($count_total > 0) {
    $page_url = $get_vars;
    unset($page_url['page_id']);
    $page_url   = '?' . http_build_query($page_url);
    $page_links = formatHTMLPageLinks($page_url . '&page_id=', $page_id, $count_display, $count_total, $items_per_page);

    $select_url = $get_vars;
    unset($select_url['action']);
    $select_url = 'members_select.php?' . http_build_query($select_url);
?>
<br />
<form accept-charset="utf-8" action="<?php hecho($select_url)?>" method="post">
<?php

    include $_SERVER['DOCUMENT_ROOT'] . '/webadmin/member/templates/list_members.tpl.php';

    $initial_url           = $get_vars;
    $initial_url['action'] = 'list';
    unset($initial_url['page_id']);
    unset($initial_url['last_initial']);
    $initial_url = 'index.php?' . http_build_query($initial_url) . '&last_initial=';
?>
</form>
<table class="wide">
	<tr><td bgcolor="#FAFAFA"><div align="center">
		<b>By Last Name :</b>
<?php
for ($i = 'a';'z' != $i; $i++) {
        if ($posted['last_initial'] == $i) {
?>
		<span style="background-color:#7FFFD4"><?=strtoupper($i)?></span>
<?php
} else {
?>
		<a href="<?=$initial_url . $i?>"><?=strtoupper($i)?></a>
<?php
}
    }
?>
	</div></td></tr>
	<tr><td bgcolor="#FAFAFA"><div align="center">
		<?=$page_links?>
	</div></td></tr>
</table>
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

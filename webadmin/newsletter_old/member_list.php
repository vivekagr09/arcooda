<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('member'); //memberGetOptionList, memberSearch, memberGroupCount, memberSearch, memberChunk, memberSort

include_once __DIR__ . '/header.inc.php';

$order_by = 'created';
$order    = 'desc';
if ('' != $posted['last_initial']) {
    $order_by = 'last_name';
    $order    = 'asc';
}
if ('' != $posted['order_by']) {
    $order_by = strtolower($posted['order_by']);
}

if ('' != $posted['order']) {
    $order = strtolower($posted['order']);
}

if ('asc' != $order) {
    $order = 'desc';
}

$page_id = intval($posted['page_id']);

$criteria = $_GLOBALS['criteria_options']['member'];

$site_id = $posted['site_id'];
if (3 != $_GLOBALS['site_settings']['site_id']) {
    $site_id = $_GLOBALS['site_settings']['site_id'];
}

$query = $DB->query("SELECT ml.member_id FROM newsletter_styles ms INNER JOIN `highwaygroup`.`new_members_mailing_list` AS `ml` ON ml.style_id=ms.style_id INNER JOIN `highwaygroup`.`new_members` AS `m` ON m.member_id=ml.member_id WHERE ml.active='y'" . ((0 != $site_id) ? (' AND ms.site_id=' . intval($site_id)) : ''));
while ($row = $DB->fetch_assoc($query)) {
    $criteria['member_id'][] = $row['member_id'];
}

$DB->free_result($query);
unset($criteria['site_id']);
unset($criteria['active_id']);

foreach (memberGetOptionList('all_fields') as $k => $v) {
    if ($posted[$k]) {
        $criteria[$k] = ((is_array($posted[$k])) ? $posted[$k] : explode('|', $posted[$k]));
    }
}

if ('' != $posted['search_for']) {
    $criteria['any_text'] = explode(' ', $posted['search_for']);
}

if ('' != $posted['member_type']) {
    $criteria['member_type'] = explode('|', $posted['member_type']);
}

$member_list = memberSearch($criteria);
if (count($member_list)) {
    $query = $DB->query("SELECT member_id FROM `highwaygroup`.`new_members_mailing_list` WHERE active='y' AND member_id IN(" . implode(", ", $member_list) . ")");
    while ($row = $DB->fetch_assoc($query)) {
        $criteria['member_id'][] = $row['member_id'];
    }

    $DB->free_result($query);
}

?>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionSearchMembers\')">Search Members</a>')?>
<div id="sectionSearchMembers">
<?php
include __DIR__ . '/search_members.tpl.php';
?>
</div>
<div id="sectionSearchMembers_hideNote"></div>
<?php

/*
//Show By Country
$sub_criteria = $criteria;
unset($sub_criteria['country']);
$member_list=memberGroupCount(memberSearch($sub_criteria),'country');
print_r($member_list);
 */

/*
Show By Site
$sub_criteria = $criteria;
unset($sub_criteria['member_type']);
$member_list=memberChunk(memberSearch($sub_criteria),'member_type');
print_r($member_list);
 */

//Find and pagenate the members
$items_per_page = 50;
//if ($_GLOBALS['staff']['members_per_page'] > 0) $items_per_page = $_GLOBALS['staff']['members_per_page'];
$member_list = memberSort(memberSearch($criteria), $order_by);
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
$add_url['action'] = 'add_member';
$add_url           = 'index.php?' . http_build_query($add_url);
?>
<?=formatHTMLTitle('Members', '<i><a style="color:#000;" href="' . hecho($add_url, 1) . '">Add New Member</a></i>')?>
<?php
if ($count_total > 0) {
    $page_url = $get_vars;
    unset($page_url['page_id']);
    $page_url   = '?' . http_build_query($page_url);
    $page_links = formatHTMLPageLinks($page_url . '&page_id=', $page_id, $count_display, $count_total, $items_per_page);

    $select_url           = $get_vars;
    $select_url['action'] = 'select_member';
    $select_url           = 'index.php?' . http_build_query($select_url);
?>
<br />
<form accept-charset="utf-8" action="<?php hecho($select_url) ?>" method="post">
<?php
include __DIR__ . '/list_members.tpl.php';

    $initial_url = $get_vars;
    unset($initial_url['page_id']);
    unset($initial_url['last_initial']);
    $initial_url = '?' . http_build_query($initial_url) . '&last_initial=';
?>
</form>
<div class="wide" style="background:#fff;border:1px solid #ddd;">
	<b>By Last Name :</b>
<?php
for ($i = 'a';'z' != $i; $i++) {
        if ($posted['last_initial'] == $i) {
?>
		<span style="background-color:#7fc;"><?=strtoupper($i)?></span>
<?php
} else {
?>
		<a href="<?=$initial_url . $i?>"><?=strtoupper($i)?></a>
<?php
}
    }

?>
	<br />
	<?=$page_links?>
</div>
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

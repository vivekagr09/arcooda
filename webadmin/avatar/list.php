<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$page    = intval($_GET['page'] ?? 0);
$order   = ('asc' == ($_GET['order'] ?? '')) ? 'asc' : 'desc';
$sort_by = [
    'id' => 'id',
][$_GET['sort_by'] ?? ''] ?? 'id';

$item_list = array();

$query            = $DB->query("SELECT * FROM `highwaygroup`.`new_members` WHERE `avatar_image` IS NOT NULL ORDER BY `avatar_date` DESC");
while ($row = $DB->fetch_assoc($query)) {
    $item_list[] = $row['member_id'];
}

if ('desc' != $order) {
    $item_list = array_reverse($member_list);
}

//include __DIR__ . '/_navigate.tpl.php';

$per_page    = 50;
$count_total = count($item_list);

echo formatHTMLTitle('Avatar');




$item_list     = array_chunk($item_list, $per_page);
$count_pages   = count($item_list);
$page          = max(0, min($count_pages - 1, $page));
$item_list     = $item_list[$page] ?? [];
$count_display = count($item_list);

if ($count_total > 0) {
    $page_url = $get_vars;
    unset($page_url['page']);
    $page_url = '?' . http_build_query($page_url);

	$page_links = formatHTMLPageLinks($page_url . '&page=', $page, $count_display, $count_total, $per_page);
	
	$select_url           = $get_vars;
    $select_url['action'] = 'select';
    unset($select_url['action']);
    $select_url = '.?' . http_build_query($select_url);
?>
<br />
<form accept-charset="utf-8" action="<?=htmlspecialchars($select_url)?>" method="post">
<?php
    include __DIR__ . '/list.tpl.php';
?>
</form>
<br />
<?= $page_links ?><br />
<br />
<?php
} else {
?>
No results were found
<?php
}

include_once __DIR__ . '/footer.inc.php';

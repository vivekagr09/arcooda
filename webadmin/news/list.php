<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$page    = intval($posted['page'] ?? 0);
$order   = ('asc' == ($_GET['order'] ?? '')) ? 'asc' : 'desc';
$sort_by = [
	'news_id'    => 'news_id',
    'news_title' => 'news_title',
    'date_added' => 'date_added',
    'archived'   => 'archived',
    'active'     => 'active',
][$_GET['sort_by'] ?? ''] ?? 'news_id';

//Build a search criteria array
$eq         = array_merge($_GET['eq'] ?? [], $_GLOBALS['criteria_options']['news'] ?? []);
$lt         = $_GET['lt'] ?? null;
$gt         = $_GET['gt'] ?? null;

$list_by1 = 'active';
$list_by2 = 'author';
?>
<div style="text-align:left;margin:0 16px">
    <a href="#" onclick="toggleSection('sectionViewByAuthorActive');return false"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Author/Active</b></a>
    <br />
    <br />
    <div id="sectionViewByAuthorActive" class="child">
<?php
include __DIR__ . '_navigate.tpl.php';
?>
    </div>
</div>
<script type="text/javascript">
//<![CDATA[
toggleSection('sectionViewByAuthorActive');
//]]>
</script>
<?php

$list_by1 = 'archived';
$list_by2 = 'author';
?>
<div style="text-align:left;margin:0 16px">
    <a href="#" onclick="toggleSection('sectionViewByAuthorArchived');return false"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Author/Archived</b></a>
    <br />
    <br />
    <div id="sectionViewByAuthorArchived" class="child">
<?php
include '_navigate.tpl.php';
?>
    </div>
</div>
<script type="text/javascript">
//<![CDATA[
toggleSection('sectionViewByAuthorArchived');
//]]>
</script>
<?php

$item_query = newsQuery($eq, $lt, $gt);
$item_list  = newsSort($item_query, $sort_by);
if ('asc' != $order) {
    $item_list = array_reverse($item_list);
}

$per_page    = $_GLOBALS['staff']['news_per_page'] ?? 100;
$count_total = count($item_list);

echo formatHTMLTitle('News Stories');




$item_list     = array_chunk($item_list, $per_page);
$count_pages   = count($item_list);
$page          = max(0, min($count_pages - 1, $page));
$item_list     = $item_list[$page] ?? [];
$count_display = count($item_list);

if ($count_total > 0) {
    $page_url = $_GET;
    unset($page_url['page']);
    $page_url   = '?' . http_build_query($page_url);
    $page_links = formatHTMLPageLinks($page_url . '&page=', $page, $count_display, $count_total, $per_page);
?>
<br />
<form accept-charset="utf-8" action="<?=htmlspecialchars($select_url)?>" method="post">
<?php
    include __DIR__ . '/_list.tpl.php';
?>
</form>
<br />
<?=$page_links?><br />
<br />
<?php
} else {
?>
No results were found
<?php
}

include_once __DIR__ . '/footer.inc.php';

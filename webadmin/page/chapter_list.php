<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$order = $_REQUEST['order'];
if ('desc' != $order) {
    $order = 'asc';
}
$id  = intval($_REQUEST['id']);
$page_id = intval($_REQUEST['page']);
if (!$id) {
    die('No Page Id passed to the page...');
}
$item     = pageFromDatabase($id);
$order_by = strtolower($_REQUEST['order_by']);
if ('' == $order_by) {
    $order_by = 'rank';
}

//Build a search criteria array
/*$criteria=array();
foreach(pageChapterGetOptionList('all_fields') as $key=>$value)
if($_REQUEST[$key]!='') $criteria[$key]=explode('|',$_REQUEST[$key]);
elseif(array_key_exists($key,$_REQUEST)) $criteria[$key]=$_REQUEST[$key];
unset($criteria['id']);
if($_REQUEST['search_for']!='') $criteria['any_text']=$_REQUEST['search_for'];
if($_REQUEST['subset_list']!='') $criteria['id']=explode('|',$_REQUEST['subset_list']);
 */
$chapter_list   = pageChapterSearch(array('page_id' => $id));
$items_per_page = 50;
$count_total    = count($chapter_list);

?>
<center><h1><?=$item['name']?></h1></center><br />
<?php

echo formatHTMLTitle('Sub-Menus', '<a href="index.php?action=chapter_add&page_id=' . $id . '">Add New</a>');
if ($count_total) {
    $chapter_list  = array_chunk($chapter_list, $items_per_page);
    $count_pages   = count($chapter_list);
    $page_id      = max(0, min($count_pages - 1, $page_id));
    $chapter_list  = $chapter_list[$page_id];
    $count_display = count($chapter_list);

    $page_url = $get_vars;
    unset($page_url['page']);
    $page_url = 'index.php?' . http_build_query($page_url) . '&page=';

    $page_links = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);

    $select_url = $get_vars;
    unset($select_url['action']);
    $select_url = 'chapter_select.php?' . http_build_query($select_url);

?>
<br />
<form action="<?php hecho($select_url)?>" method="post">
<?php
	include __DIR__ . '/chapter_list.tpl.php';
?>
</form>
<br />
<?=$page_links?>
<br />
<?php
} else {
    echo '<center><b>No Results Found</b></center>';
}

if ($popup_window) {
?>
<br /><br /><input type="button" onclick="window.close();" value="Close Window" /><br /><br />&nbsp;
<?php
}

include_once __DIR__ . '/footer.inc.php';

<?php
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;

include_once __DIR__ . '/softjob_common.inc.php';
include_once __DIR__ . '/templates/softjob_header.tpl.php';

$order = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$page_id  = intval($posted['page_id']);
$order_by = ($posted['order_by'])?strtolower($posted['order_by']):null;

//Build a search criteria array
$criteria             = array();
//$criteria['staff_id'] = $staff_id;
if ('' != $posted['priority']) {
    $criteria['priority'] = explode('|', $posted['priority']);
}

if ('' != $posted['assigned']) {
    $criteria['assigned'] = explode('|', $posted['assigned']);
}

if ('' != $posted['reporter']) {
    $criteria['reporter'] = explode('|', $posted['reporter']);
}

if ('' != $posted['status']) {
    $criteria['status'] = explode('|', $posted['status']);
}

if ('' != $posted['website']) {
    $criteria['website'] = explode('|', $posted['website']);
}

if ('' != $posted['search_for']) {
    $criteria['any_text'] = $posted['search_for'];
}

?>
<!--<script type="text/javascript">
toggleSection('sectionViewByStatus');
</script>-->
<?= formatHTMLTitle('Jobs') ?>
<?php
//Find and pagenate the softjobs
$softjob_list = softjobSearch($criteria);

$softjob_list = softjobSort($softjob_list, $order_by);
if ('desc' == $order) {
    $softjob_list = array_reverse($softjob_list);
}

$items_per_page = 50;
$count_total    = count($softjob_list);

$softjob_list  = array_chunk($softjob_list, $items_per_page);
$count_pages   = count($softjob_list);
$page_id       = max(0, min($count_pages - 1, $page_id));
$softjob_list  = $softjob_list[$page_id] ?? [];
$count_display = count($softjob_list);

if ($count_total > 0) {
    $page_url = $get_vars;
    unset($page_url['page_id']);
    $page_url = 'index.php?' . http_build_query($page_url);

    $page_links = formatHTMLPageLinks($page_url . '&page_id=', $page_id, $count_display, $count_total, $items_per_page);
?>
<br />
<?php

    include __DIR__ . '/templates/softjob_listview.tpl.php';
?>
<br />
<?= $page_links ?><br />
<br />
<?php
$officeprint_url           = $get_vars;
    $officeprint_url['action'] = 'officeprint';
    $officeprint_url           = 'index.php?' . http_build_query($officeprint_url);

    $report_url           = $get_vars;
    $report_url['action'] = 'report';
    $report_url           = 'index.php?' . http_build_query($report_url);
?>
<input type="button" value="Print Office Summary" onclick="javascript:document.location='<?= $officeprint_url ?>';" />
<?php
if ('y' == $_GLOBALS['staff']['developer']) {
?>
<input type="button" value="Print Software Report" onclick="javascript:document.location='<?= $report_url ?>';" />
<?php
}
} else {
?>
No results were found
<?php
}

include_once __DIR__ . '/templates/softjob_footer.tpl.php';

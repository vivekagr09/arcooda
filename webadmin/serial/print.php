<?php
/**
 * @var Serial_Handler $section_handler
 * @var string         $section_name
 * @var mixed[]        $get_vars
 */
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;

$posted['popup'] = $get_vars['popup'] = $_REQUEST['popup'] = $_GET['popup'] = 3;

?><!DOCTYPE html>
<html>
<head>
	<title>Print Serial Numbers</title>
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/latest/css/bootstrap.min.css" />
	<link rel="stylesheet" href="/webadmin/css/favicons.css" type="text/css" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script type="text/javascript" src="/phpjs/arrays&amp;strings.js"></script>
	<script type="text/javascript">
//<![CDATA[
var windowObj = this;
function printpage() {
	if (window.print) {
		window.print();
	} else {
		alert("To print this page, select Print Frame from the file menu.");
	}
}
function doPrint() {
	if (confirm("Click OK to print this document")) {
		printpage();
	}
}
//]]>
	</script>
</head>
<body onload="doPrint()">
	<div class="container">
<?php
$action = 'print';

?>
<h2><?=htmlspecialchars(_('Serial Numbers'))?> - <?=date('d/m/Y', time())?></h2>
<?php

$order    = $_REQUEST['order'] ?? 'asc';
$page_id  = intval($_REQUEST['page_id'] ?? 0);
$order_by = strtolower($_REQUEST['order_by'] ?? 'name');

if ('asc' != $order) {
    $order = 'desc';
}

//Build a search criteria array
$criteria = $_GLOBALS['criteria_options'][$section_name];
foreach ($section_handler->getOptionList('all_fields') as $k => $v) {
    if (array_key_exists($k, $_REQUEST)) {
        if (is_array($_REQUEST[$k])) {
            $criteria[$k] = $_REQUEST[$k];
        } elseif ('' !== $_REQUEST[$k]) {
            $criteria[$k] = preg_split('/[^0-9a-z]+/i', $_REQUEST[$k]);
        }

        foreach ($criteria[$k] as $k2 => $v2) {
            if (empty($v2)) {
                unset($criteria[$k][$k2]);
            }
        }

    }
}

unset($criteria['id']);

if (array_key_exists('search_for', $_REQUEST) && '' != $_REQUEST['search_for']) {
    $criteria['any_text'] = explode('|', $_REQUEST['search_for']);
}

if (array_key_exists('subset_list', $_REQUEST) && '' != $_REQUEST['subset_list']) {
    $criteria['id'] = explode('|', $_REQUEST['subset_list']);
}

//Find and pagenate the items
$item_list = $section_handler->search($criteria);
$item_list = $section_handler->sort($item_list, $order_by);
if ('desc' == $order) {
    $item_list = array_reverse($item_list);
}

$count_total = count($item_list);

if ($count_total > 0) {
    $count_pages   = 1;
    $page_id       = 0;
    $count_display = count($item_list);

    $page_url           = $get_vars;
    $page_url['action'] = 'list';
    unset($page_url['page_id']);
    $page_url = '.?' . http_build_query($page_url) . '&page_id=';

    $page_links = '';

    $select_url = $get_vars;
    unset($select_url['action']);
    $select_url = 'select.php?' . http_build_query($select_url);

    include __DIR__ . '/list.tpl.php';
} else {
?>
<?=htmlspecialchars(_('No results were found.'))?>
<?php
}
?>

</body>
</html>
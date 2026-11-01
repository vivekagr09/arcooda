<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$order = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$page_id  = intval($posted['page_id']);
$order_by = strtolower($posted['order_by']);

//Build a search criteria array
$criteria = array();
//foreach(invoiceChargesGetOptionList('all_fields') as $key=>$value)
//    if($posted[$key]!='') $criteria[$key]=explode('|',$posted[$key]);
//    elseif(array_key_exists($key,$posted)) $criteria[$key]=$posted[$key];
unset($criteria['charge_id']);
if ('' != $posted['search_for']) {
    $criteria['any_text'] = $posted['search_for'];
}

if ('' != $posted['subset_list']) {
    $criteria['charge_id'] = explode('|', $posted['subset_list']);
}

if ('' != $posted['site_id']) {
    $criteria['site_id'] = explode('|', $posted['site_id']);
}

if (is_array($criteria['site_id'])) {
    $criteria['site_id'][] = '0';
}

//Find and pagenate the charges
$charges_list = invoiceChargesSearch($criteria);
//$charges_list=chargeSort($charges_list,$order_by);
if ('desc' == $order) {
    $charges_list = array_reverse($charges_list);
}

$count_total    = count($charges_list);
$items_per_page = max(1, $count_total);
$charges_list   = array_chunk($charges_list, $items_per_page);
$count_pages    = count($charges_list);
$page_id        = max(0, min($count_pages - 1, $page_id));
$charges_list   = $charges_list[$page_id];
$count_display  = count($charges_list);

?>
<form action="<?=htmlspecialchars($select_url)?>" method="post">
<?php

$page_url = $get_vars;
unset($page_url['page_id']);
$page_url = '.?' . http_build_query($page_url) . '&page_id=';

$page_links = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);

$select_url = $get_vars;
unset($select_url['action']);
$select_url = 'invoice_charges_select.php?' . http_build_query($select_url);
?>
<br />
<?php
include __DIR__ . '/templates/invoice_charges_list.tpl.php';
?>
<!-- <br /><?=$page_links?><br /> -->
<?php

if ($popup_window) {
?>
<p style="text-align:center;">
	<input type="submit" value="Select Charge" />
	<input type="button" onclick="window.close();" value="Close Window" />
</p>

<?php
}
?>
</form>
<?php

include_once __DIR__ . '/footer.inc.php';

<?php
include_once 'voucher_common.inc.php';
$popup_window = (isset($posted['return_message']));
include_once 'templates/voucher_header.tpl.php';

$site_id    = intval($posted['site_id']);
$search_for = (isset($posted['search_for'])) ? $posted['search_for'] : '';
$page       = intval($posted['page']);
$order_by   = (isset($posted['order_by'])) ? $posted['order_by'] : 'id';
$order      = ('asc' != $posted['order']) ? 'desc' : 'asc';

$preset_list = array();
$res         = $DB->query("SELECT * FROM `highwaygroup`.`gift_preset`" . (($site_id) ? " WHERE site_id=" . $site_id : ''));
while ($row = $DB->fetch_assoc($res)) {
    $preset_list[] = $row;
}

echo formatHTMLTitle('Voucher Presets');
if (count($preset_list)) {
    $items_per_page = 50;
    $preset_list    = array_chunk($preset_list, $items_per_page);
    $count_pages    = count($preset_list);
    $page           = max(0, min($count_pages - 1, $page));
    $preset_list    = $preset_list[$page];
    $count_display  = count($preset_list);

    $page_url            = $get_vars;
    $page_url['site_id'] = $site_id;
    unset($page_url['page']);
    $page_url = '?' . http_build_query($page_url);

    $page_links = formatHTMLPageLinks($page_url . '&page=', $page, $count_display, $count_total, $items_per_page);
?>
<table class="wide">
	<tr><th>ID</th><?=(!$site_id) ? '<th>Site</th>' : ''?><th>Name</th><th>Amount</th><th>Terms</th><th></th></tr>
<?php
$count = 0;
    foreach ($preset_list as $preset) {
?>
	<tr style="text-align:center;"<?=($count++ % 2 == 1) ? ' class="alt"' : ''?>>
		<td><?=$preset['id']?></td><?=(!$site_id) ? '<td>' . $site_list[$preset['site_id']] . '</td>' : ''?><td><?=$preset['name']?></td><td><?=$preset['amount'] . ' ' . $preset['currency']?></td><td><?=$terms[$preset['terms']]?></td><td><a href="voucher_preset.php?preset_id=<?=$preset['id']?>">EDIT</a></td>
	</tr>
<?php
}
} else {
    echo 'No Preset Lists found for this site..';
}

?>
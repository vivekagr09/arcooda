<?php
$sort_url           = $get_vars;
$sort_url['action'] = 'list';
unset($sort_url['order_by']);
unset($sort_url['order']);
unset($sort_url['page_id']);
$sort_url = 'index.php?' . http_build_query($sort_url) . '&order_by=';

$edit_url           = $get_vars;
$edit_url['action'] = 'edit';
unset($edit_url['id']);
$edit_url = 'index.php?' . http_build_query($edit_url) . '&id=';

$view_url           = $get_vars;
$view_url['action'] = 'view';
unset($view_url['id']);
$view_url = 'index.php?' . http_build_query($view_url) . '&id=';
?>
<table class="spacetable" style="width:512px;" align="center">
	<tr>
		<th><a href="<?=$sort_url?>name&order=<?=('name' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Currency</a></th>
		<th><a href="<?=$sort_url?>code&order=<?=('code' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Code</a></th>
		<th><a href="<?=$sort_url?>usfrom&order=<?=('usfrom' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">To USD</a></th>
		<th><a href="<?=$sort_url?>usto&order=<?=('usto' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">From USD</a></th>
	</tr>
<?php
foreach ($currency_list as $currency_id) {
    $currency = currencyFromDatabase($currency_id);
?>
	<tr<?=($alt = !$alt) ? '' : ' class="alt"'?>>
		<td style="text-align:left;"><?=htmlspecialchars($currency['name'])?></td>
		<td style="text-align:center;"><?=htmlspecialchars($currency['code'])?></td>
		<td style="text-align:right;"><?=sprintf('%.4f', $currency['usfrom'])?></td>
		<td style="text-align:right;"><?=sprintf('%.4f', $currency['usto'])?></td>
	</tr>
<?php
}
?>
</table>

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
		<th><a href="<?=$sort_url?>&order=<?=('asc' == $order) ? 'desc' : 'asc'?>">Date</a></th>
		<th>1 <?=htmlspecialchars($currency_to)?> (<?=htmlspecialchars($currency_from)?>)</th>
		<th>1 <?=htmlspecialchars($currency_from)?> (<?=htmlspecialchars($currency_to)?>)</th>
		<th><?=convertCurrency($amount_from, $currency_from)?> <?=htmlspecialchars($currency_from)?> (<?=htmlspecialchars($currency_to)?>)</th>
	</tr>
<?php
$currency_symbol1 = getCurrencySymbol($currency_from);
$currency_symbol2 = getCurrencySymbol($currency_to);
foreach ($date_list as $timestamp => $row) {
    $currency1 = $row['from'];
    $currency2 = $row['to'];
?>
	<tr<?=($alt = !$alt) ? '' : ' class="alt"'?>>
		<td style="text-align:center;"><?=date('d/m/Y', intval($timestamp))?></td>
		<td style="text-align:center;"><?=$currency_symbol1?><?=(0.0 != $currency2['usto']) ? convertCurrency($currency1['usto'] / $currency2['usto'], $currency_from) : ''?></td>
		<td style="text-align:center;"><?=$currency_symbol2?><?=(0.0 != $currency1['usto']) ? convertCurrency($currency2['usto'] / $currency1['usto'], $currency_to) : ''?></td>
		<td style="text-align:center;"><?=$currency_symbol2?><?=(0.0 != $currency1['usto']) ? convertCurrency($amount_from * $currency2['usto'] / $currency1['usto'], $currency_to) : ''?></td>
	</tr>
<?php
}
?>
</table>

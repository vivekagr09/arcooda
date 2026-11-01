<?php
moduleLoad('staff');

$alt     = false;
$ord_img = '<img src="/webadmin/images/' . (('desc' == $order) ? 'desc' : 'asc') . '.png" />';

$all_staff_list = staffGetOptionList('all_staff');

$sort_url           = $get_vars;
$sort_url['action'] = 'list';
$sort_url['order']  = ('desc' == $order) ? 'asc' : 'desc';
unset($sort_url['order_by']);
$sort_url = 'index.php?' . http_build_query($sort_url);
$sort_url .= '&order_by=';

$edit_url           = $get_vars;
$edit_url['action'] = 'edit';
unset($edit_url['asset_id']);
$edit_url = 'index.php?' . http_build_query($edit_url);
$edit_url .= '&asset_id=';

$view_url           = $get_vars;
$view_url['action'] = 'view';
unset($view_url['asset_id']);
$view_url = 'index.php?' . http_build_query($view_url);
$view_url .= '&asset_id=';

$thumb_url           = $get_vars;
$thumb_url['action'] = 'thumb';
unset($thumb_url['asset_id']);
$thumb_url = 'thumb.php?' . http_build_query($thumb_url);
$thumb_url .= '&asset_id=';

$member_url           = $get_vars;
$member_url['action'] = 'view';
unset($member_url['member_id']);
$member_url = '/webadmin/member/?' . http_build_query($member_url);
$member_url .= '&member_id=';

$unlock_url           = $get_vars;
$unlock_url['action'] = 'unlock';
unset($unlock_url['asset_id']);
$unlock_url = 'index.php?' . http_build_query($unlock_url);
$unlock_url .= '&asset_id=';

$select_url           = $get_vars;
$select_url['action'] = 'select';
unset($select_url['asset_id']);
$select_url = 'index.php?' . http_build_query($select_url);
$select_url .= '&asset_id=';

$table_info = assetTableInfoFromType($asset_type);
?>
<!-- list_assets.tpl.php -->

<table class="wide spacetable" align="center">
	<tr>
		<th><a href="<?=$sort_url?>owner_id"><?=$table_info['owner_id']?></a><?=('owner_id' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>asset_id">Asset #</a><?=('asset_id' == $order_by) ? $ord_img : ''?></th>
		<th>Icon</th>
		<th><a href="<?=$sort_url?>asset_caption">Caption</a><?=('asset_caption' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>asset_date">Date Added</a><?=('asset_date' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>filetype">File Type</a><?=('filetype' == $order_by) ? $ord_img : ''?></th>
		<th>&nbsp;</th>
	</tr>

<?php
$alt = false;
foreach ($asset_list as $asset_id) {
    $asset = assetFromDatabase($asset_type, $asset_id);
?>
	<tr<?=($alt = !$alt) ? '' : ' class="alt"'?>>
		<td style="text-align:center;"><?=$asset['owner_id']?></td>
		<td style="text-align:center;"><?=$asset['asset_id']?></td>
		<td style="text-align:center;"><a href="<?=$view_url . $asset['asset_id']?>" target="_blank"><img src="<?=$thumb_url . $asset['asset_id']?>" border="0" title="<?=htmlspecialchars($asset['asset_caption'])?>" /></a></td>
		<td style="text-align:center;"><?=htmlspecialchars($asset['asset_caption'])?></td>
		<td style="text-align:center;"><?=date('d/m/Y', $asset['asset_time'])?></td>
		<td style="text-align:center;"><?=htmlspecialchars($asset['filetype'])?></td>
		<td style="text-align:center;">
<?php
if ($popup_window) {
?>
			<a href="<?=$select_url . $asset['asset_id']?>">Select</a>
<?php
}
?>
			<a href="<?=$edit_url . $asset['asset_id']?>"<?=(!$popup_window) ? ' target="_blank"' : ''?>>Edit</a>
			<a href="<?=$view_url . $asset['asset_id']?>" target="_blank">View</a>
		</td>
	</tr>
<?php
}
?>
</table>

<br />

<!-- /list_assets.tpl.php -->

<?php
$sort_url = $get_vars;
$sort_url['action'] = 'list';
unset($sort_url['sort_by']);
unset($sort_url['order']);
unset($sort_url['page']);
if ($get_vars["order"] == "") $sort_url["order"] = "desc";
$sort_url = 'index.php?' . http_build_query($sort_url) . '&sort_by=';

$edit_url = $get_vars;
$edit_url['action'] = 'edit';
unset($edit_url['news_id']);
$edit_url = 'index.php?' . http_build_query($edit_url) . '&news_id=';

$select_url = $get_vars;
$select_url['action'] = 'select';
unset($select_url['news_id']);
$select_url = 'index.php?' . http_build_query($select_url) . '&news_id=';

$view_url = $get_vars;
$view_url['action'] = 'view';
unset($view_url['news_id']);
$view_url = 'index.php?' . http_build_query($view_url) . '&news_id=';

$active_url = $get_vars;
$active_url['action'] = 'toggle_active';
unset($active_url['news_id']);
$active_url = 'index.php?' . http_build_query($active_url) . '&news_id=';

$archived_url = $get_vars;
$archived_url['action'] = 'toggle_archived';
unset($archived_url['news_id']);
$archived_url = 'index.php?' . http_build_query($archived_url) . '&news_id=';
?>
<table class="table_grid"  style="width:95%;margin:auto;">
	<tr>
		<th><a href="<?php hecho($sort_url . "news_id") ?>">ID</a></th>
		<th><a href="<?php hecho($sort_url . "news_title") ?>">Title</a></th>
		<th><a href="<?php hecho($sort_url . "date_added") ?>">Date Posted</a></th>
		<th><a href="<?php hecho($sort_url . "archived") ?>">Front</a></th>
		<th><a href="<?php hecho($sort_url . "active") ?>">Active</a></th>
		<th></th>
	</tr>
<?php
foreach($news_list as $news_id)
{
	$news = $NH->fromDatabase($news_id);
	$url_news=sprintf(
            '/n/%s-%d.html',
            urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $news['news_title']), '-')),
            $news_id
        );
	if (trim($news['news_title']) == '') $news['news_title'] = '[Untitled]';
	$links = array();
	$links[] = '<a href="' . $edit_url . $news_id . '" target="_blank">Edit</a>';
	if ($popup_window) $links[] = '<a href="' . $select_url . $news_id . '">Select</a>';
?>
	<tr<?php echo ($alt = !$alt) ? '' : ' class="alt"' ?>>
		<td>ID#<?php echo $news_id ?></td>
		<td style="text-align:left;white-space:wrap;"><a href="<?php hecho($url_news) ?>" target="_blank"><?php hecho($news['news_title']) ?></a></td>
		<td><?php echo date('d/m/Y', $news['time_added']) ?></td>
		<td><a href="<?php hecho($archived_url . $news_id) ?>"><?php echo ($news['archived'] == 'y') ? 'No' : 'Yes' ?></a></td>
		<td><a href="<?php hecho($active_url . $news_id) ?>"><?php echo ($news['active'] == 'y') ? 'Yes' : 'No' ?></a></td>
		<td style="white-space:nowrap;"><?php echo implode(' | ', $links) ?></td>
	</tr>
<?php
}
?>
</table>

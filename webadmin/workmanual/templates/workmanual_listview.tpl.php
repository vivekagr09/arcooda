<?php
$sort_url=$get_vars;
$sort_url['action']='list';
unset($sort_url['order_by']);
unset($sort_url['order']);
$sort_url='index.php?'.http_build_query($sort_url).'&order_by=';

$view_url=$get_vars;
$view_url['action']='view';
unset($view_url['manual_id']);
$view_url='index.php?'.http_build_query($view_url).'&manual_id=';

$add_url=$get_vars;
$add_url['action']='chapter_add';
unset($add_url['manual_id']);
unset($add_url['chapter_id']);
$add_url='index.php?'.http_build_query($add_url).'&manual_id=';
?>
<table class="wide spacetable" align="center">
	<tr>
		<th><a href="<?= $sort_url ?>manual_id&order=<?= ($order_by=='manual_id' && $order=='asc')?'desc':'asc' ?>">Name of Manual</a></th>
		<th><a href="<?= $sort_url ?>change_date&order=<?= ($order_by=='change_date' && $order=='asc')?'desc':'asc' ?>">Last Updated</a></th>
		<th><a href="<?= $sort_url ?>change_staff&order=<?= ($order_by=='change_staff' && $order=='asc')?'desc':'asc' ?>">By</a></th>
		<th></th>
		<th></th>
	</tr>
<?php
foreach($manual_list as $manual_id)
{
	$manual=workmanualFromDatabase($manual_id);
	if(trim($manual['manual_title'])=='') $manual['manual_title']='[Untitled]';

	$unread_list=workmanualChapterSearch(array('manual_id'=>$manual_id));
	foreach($unread_list as $key=>$chapter_id)
	{
		$row=$DB->query_first("SELECT UNIX_TIMESTAMP(wv.view_date) AS view_time, UNIX_TIMESTAMP(wc.create_date) AS create_time, UNIX_TIMESTAMP(wc.change_date) AS change_time FROM `highwaygroup`.`workmanual_chapters` wc LEFT JOIN `highwaygroup`.`workmanual_views` AS `wv` ON wc.chapter_id=wv.chapter_id WHERE wv.staff_id=".intval($staff_id)." AND wv.chapter_id=".intval($chapter_id)." LIMIT 1",MYSQLI_ASSOC);
		if($row['view_time']>0 && $row['view_time']>$row['create_time'] && ($row['change_time']==0 || $row['view_time']>$row['change_time']))
			unset($unread_list[$key]);
	}
?>
	<tr<?= (count($unread_list)>0)?' class="highlight"':(($alt=!$alt)?'':' class="alt"') ?>>
		<td style="text-align:center;"><?= htmlspecialchars($manual['manual_title']) ?></td>
		<td style="text-align:center;"><?= date('d/m/y',($manual['change_time']>0)?$manual['change_time']:$manual['create_time']) ?></td>
		<td style="text-align:center;"><?= htmlspecialchars($all_staff_list[($manual['change_time']>0)?$manual['change_staff']:$manual['create_staff']]) ?></td>
		<td style="text-align:center;"><a href="<?= $view_url.$manual_id ?>" target="_blank">View</a></td>
		<td style="text-align:center;"><?= (has_permission('WORK_MAN_W',$allowed_sections))?('<a href="'.$add_url.$manual_id.'" target="_blank">Add</a>'):'' ?></td>
	</tr>
<?php
	}
?>
</table>

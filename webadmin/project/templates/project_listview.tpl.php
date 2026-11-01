<?php
$sort_url=$get_vars;
$sort_url['action']='list';
unset($sort_url['order_by']);
unset($sort_url['order']);
$sort_url='index.php?'.http_build_query($sort_url).'&order_by=';

$edit_url=$get_vars;
$edit_url['action']='edit';
unset($edit_url['project_id']);
$edit_url='index.php?'.http_build_query($edit_url).'&project_id=';

$view_url=$get_vars;
$view_url['action']='view';
unset($view_url['project_id']);
$view_url='index.php?'.http_build_query($view_url).'&project_id=';

$add_url=$get_vars;
$add_url['action']='chapter_add';
unset($add_url['project_id']);
unset($add_url['chapter_id']);
$add_url='index.php?'.http_build_query($add_url).'&project_id=';
?>
<table class="wide spacetable" align="center">
	<tr>
		<th><a href="<?php echo $sort_url ?>project_id&order=<?php echo ($order_by=='project_id' && $order=='asc')?'desc':'asc' ?>">Name of Project</a></th>
		<th><a href="<?php echo $sort_url ?>change_date&order=<?php echo ($order_by=='change_date' && $order=='asc')?'desc':'asc' ?>">Last Updated</a></th>
		<th><a href="<?php echo $sort_url ?>change_staff&order=<?php echo ($order_by=='change_staff' && $order=='asc')?'desc':'asc' ?>">By</a></th>
<?php
if (in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43)))
{
?>
		<th></th>
<?php
}
?>
		<th></th>
		<th></th>
		<th></th>
	</tr>
<?php
$site_list = projectGetOptionList('site_id');
$staff_list = projectGetOptionList('change_staff');

foreach($project_list as $project_id)
{
	$project=projectFromDatabase($project_id);
	if(trim($project['project_title'])=='') $project['project_title']='[Untitled]';

	$unread_list=projectChapterSearch(array('project_id'=>$project_id));
	foreach($unread_list as $key=>$chapter_id)
	{
		$row=$DB->query_first("SELECT UNIX_TIMESTAMP(wv.view_date) AS view_time, UNIX_TIMESTAMP(wc.create_date) AS create_time, UNIX_TIMESTAMP(wc.change_date) AS change_time FROM `highwaygroup`.project_chapters wc LEFT JOIN `highwaygroup`.project_views wv ON wc.chapter_id=wv.chapter_id WHERE wv.staff_id=".intval($staff_id)." AND wv.chapter_id=".intval($chapter_id)." LIMIT 1",MYSQLI_ASSOC);
		if($row['view_time']>0 && $row['view_time']>$row['create_time'] && ($row['change_time']==0 || $row['view_time']>$row['change_time']))
			unset($unread_list[$key]);
	}
?>
	<tr<?php echo (count($unread_list)>0)?' class="highlight"':(($alt=!$alt)?'':' class="alt"') ?>>
		<td style="text-align:center;"><?php echo htmlspecialchars($project['project_title']) ?></td>
		<td style="text-align:center;"><?php echo date('d/m/y',($project['change_time']>0)?$project['change_time']:$project['create_time']) ?></td>
		<td style="text-align:center;"><?php echo htmlspecialchars($staff_list[($project['change_time']>0)?$project['change_staff']:$project['create_staff']]) ?></td>
<?php
if (in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43)))
{
?>
		<td style="text-align:center;"><?php hecho($site_list[$project['site_id']]) ?></td>
<?php
}
?>
		<td style="text-align:center;"><a href="<?php echo $view_url.$project_id ?>" target="_blank">View</a></td>
		<td style="text-align:center;"><a href="<?php echo $edit_url.$project_id ?>" target="_blank">Edit</a></td>
		<td style="text-align:center;"><?php echo '<a href="'.$add_url.$project_id.'" target="_blank">Add</a>' ?></td>
	</tr>
<?php
	}
?>
</table>

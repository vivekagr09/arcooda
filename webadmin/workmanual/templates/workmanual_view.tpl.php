<?php
$sort_url=$get_vars;
$sort_url['action']='view';
unset($sort_url['order_by']);
unset($sort_url['order']);
$sort_url='index.php?'.http_build_query($sort_url).'&order_by=';

$add_url=$get_vars;
$add_url['action']='chapter_add';
$add_url='index.php?'.http_build_query($add_url);

$view_url=$get_vars;
$view_url['action']='chapter_view';
unset($view_url['chapter_id']);
$view_url='index.php?'.http_build_query($view_url).'&chapter_id=';

$moveup_url=$get_vars;
$moveup_url['action']='do_chapter_moveup';
unset($moveup_url['chapter_id']);
$moveup_url='index.php?'.http_build_query($moveup_url).'&chapter_id=';

$movedown_url=$get_vars;
$movedown_url['action']='do_chapter_movedown';
unset($movedown_url['chapter_id']);
$movedown_url='index.php?'.http_build_query($movedown_url).'&chapter_id=';

$edit_url=$get_vars;
$edit_url['action']='chapter_edit';
unset($edit_url['chapter_id']);
$edit_url='index.php?'.http_build_query($edit_url).'&chapter_id=';

$delete_url=$get_vars;
$delete_url['action']='chapter_delete';
unset($delete_url['chapter_id']);
$delete_url='index.php?'.http_build_query($delete_url).'&chapter_id=';
?>
<h1><?= htmlspecialchars($manual['manual_title']) ?></h1>

<?= formatHTMLTitle('Chapters',(has_permission('WORK_MAN_W',$allowed_sections))?('<a href="'.$add_url.'" target="_blank">Add Chapter</a>'):'') ?>

<table class="wide spacetable" align="center">
	<tr>
		<th></th>
		<th><a href="<?= $sort_url ?>chapter_rank&order=<?= ($order_by=='chapter_rank' && $order=='asc')?'desc':'asc' ?>">Number</a></th>
		<th></th>
		<th><a href="<?= $sort_url ?>chapter_title&order=<?= ($order_by=='chapter_title' && $order=='asc')?'desc':'asc' ?>">Title</a></th>
		<th><a href="<?= $sort_url ?>change_date&order=<?= ($order_by=='change_date' && $order=='asc')?'desc':'asc' ?>">Date Updated</a></th>
		<th><a href="<?= $sort_url ?>change_staff&order=<?= ($order_by=='change_staff' && $order=='asc')?'desc':'asc' ?>">Posted By</a></th>
		<th></th>
		<th></th>
		<th></th>
	</tr>
<?php
$unread_list=$manual_chapters;
foreach($unread_list as $key=>$chapter_id)
{
	$row=$DB->query_first("SELECT UNIX_TIMESTAMP(wv.view_date) AS view_time, UNIX_TIMESTAMP(wc.create_date) AS create_time, UNIX_TIMESTAMP(wc.change_date) AS change_time FROM `highwaygroup`.`workmanual_chapters` wc LEFT JOIN `highwaygroup`.`workmanual_views` AS `wv` ON wc.chapter_id=wv.chapter_id WHERE wv.staff_id=".intval($staff_id)." AND wv.chapter_id=".intval($chapter_id)." LIMIT 1",MYSQLI_ASSOC);
	if($row['view_time']>0 && $row['view_time']>$row['create_time'] && ($row['change_time']==0 || $row['view_time']>$row['change_time']))
		unset($unread_list[$key]);
}

$counter=0;
foreach($manual_chapters as $chapter_id)
{
	$counter++;
	$chapter=workmanualChapterFromDatabase($chapter_id);
	if(trim($chapter['chapter_title'])=='') $chapter['chapter_title']='[Untitled]';
?>
	<tr<?= (in_array($chapter_id,$unread_list))?' class="highlight"':(($alt=!$alt)?'':' class="alt"') ?>>
		<td style="text-align:center;"><input type="checkbox" name="chapter_list[]" value="<?= $chapter_id ?>" checked="checked" /></td>
		<td style="text-align:center;"><?= $chapter['chapter_rank'] ?></td>
		<td style="text-align:center;">
<?php
	if(has_permission('WORK_MAN_W',$allowed_sections))
	{
?>
			<a href="<?= $movedown_url.$chapter_id ?>"<?= ($counter<count($manual_chapters))?'':' style="visibility:hidden;"' ?>><img border="0" src="/webadmin/images/arrowDown.gif" /></a>
			<a href="<?= $moveup_url.$chapter_id ?>"<?= ($counter>1)?'':' style="visibility:hidden;"' ?>><img border="0" src="/webadmin/images/arrowUp.gif" /></a>
<?php
	}
?>
		</td>
		<td style="text-align:center;"><?= htmlspecialchars($chapter['chapter_title']) ?></td>
		<td style="text-align:center;"><?= date('d/m/y',($chapter['change_time']>0)?$chapter['change_time']:$chapter['create_time']) ?></td>
		<td style="text-align:center;"><?= htmlspecialchars($all_staff_list[($chapter['change_time']>0)?$chapter['change_staff']:$chapter['create_staff']]) ?></td>
		<td style="text-align:center;"><a href="<?= $view_url.$chapter_id ?>" target="_blank">View</a></td>
		<td style="text-align:center;"><?= (has_permission('WORK_MAN_W',$allowed_sections))?('<a href="'.$edit_url.$chapter_id.'" target="_blank">Edit</a>'):'' ?></td>
		<td style="text-align:center;"><?= (has_permission('WORK_MAN_W',$allowed_sections))?('<a href="'.$delete_url.$chapter_id.'">Delete</a>'):'' ?></td>
	</tr>
<?php
	}
?>
</table>

<br />
<br />

<script type="text/javascript">
function checkAll(checked)
{
	var theElems=document.getElementsByTagName('INPUT');
	for(i=0;i<theElems.length;i++)
		if(theElems[i].checked!=null)
			theElems[i].checked=checked;
}
function postPrintSummary(theForm)
{
	var oldAction=theForm.action;
	theForm.action='index.php?action=summary&manual_id=<?= $manual_id ?>';
	theForm.submit();
	theForm.action=oldAction;
}
</script>

<input type="checkbox" onchange="javascript:checkAll(this.checked);" checked="checked" /> Check All<br />

<br />
<br />

<input type="button" value="Print Summary" onclick="javascript:postPrintSummary(this.form);" />
<input type="button" value="Print Selected Manual" onclick="javascript:this.form.submit();" />

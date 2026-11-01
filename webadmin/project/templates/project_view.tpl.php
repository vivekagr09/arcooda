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

$unlock_url = $get_vars;
$unlock_url['action'] = 'unlock';
unset($unlock_url['chapter_id']);
$tmp = $unlock_url;
$unlock_url = 'index.php?' . http_build_query($unlock_url);
$unlock_url.= '&chapter_id=';
?>
<h1><?php echo htmlspecialchars($project['project_title']) ?></h1>

<?php echo formatHTMLTitle('Chapters','<a href="'.$add_url.'" target="_blank">Add Chapter</a>') ?>

<table class="wide spacetable" align="center">
	<tr>
		<th></th>
		<th><a href="<?php echo $sort_url ?>chapter_rank&order=<?php echo ($order_by=='chapter_rank' && $order=='asc')?'desc':'asc' ?>">Number</a></th>
		<th></th>
		<th><a href="<?php echo $sort_url ?>chapter_title&order=<?php echo ($order_by=='chapter_title' && $order=='asc')?'desc':'asc' ?>">Title</a></th>
		<th><a href="<?php echo $sort_url ?>change_date&order=<?php echo ($order_by=='change_date' && $order=='asc')?'desc':'asc' ?>">Date Updated</a></th>
		<th><a href="<?php echo $sort_url ?>change_staff&order=<?php echo ($order_by=='change_staff' && $order=='asc')?'desc':'asc' ?>">Last Updated By</a></th>
		<th></th>
		<th></th>
		<th></th>
	</tr>
<?php
$unread_list=$project_chapters;
foreach($unread_list as $key=>$chapter_id)
{
	$row=$DB->query_first("SELECT UNIX_TIMESTAMP(wv.view_date) AS view_time, UNIX_TIMESTAMP(wc.create_date) AS create_time, UNIX_TIMESTAMP(wc.change_date) AS change_time FROM `highwaygroup`.project_chapters wc LEFT JOIN `highwaygroup`.project_views wv ON wc.chapter_id=wv.chapter_id WHERE wv.staff_id=".intval($staff_id)." AND wv.chapter_id=".intval($chapter_id)." LIMIT 1",MYSQLI_ASSOC);
	if($row['view_time']>0 && $row['view_time']>$row['create_time'] && ($row['change_time']==0 || $row['view_time']>$row['change_time']))
		unset($unread_list[$key]);
}

$counter=0;
$project_locks = projectGetLocks();

$staff_list = projectGetOptionList('staff_id');

foreach($project_chapters as $chapter_id)
{
	$counter++;

	$project_lock = false;
	if (array_key_exists($chapter_id, $project_locks)) $project_lock = $project_locks[$chapter_id];
	$lock_tooltip = '';
	$class= "";
	if ($project_lock !== false) {
		$class=' class="locked"';
		if ($project_lock['staff_id'] != $staff_id) {
			$lock_tooltip = hecho('This chapter is currently locked to ' . $staff_list[$project_lock['staff_id']], 1);
		}
	}
	if ($project_lock['staff_id'] == $staff_id) $class = 'class="mylock"';

	$chapter=projectChapterFromDatabase($chapter_id);
	if(trim($chapter['chapter_title'])=='') $chapter['chapter_title']='[Untitled]';

?>
	<?php if ($class == "") { ?>
	<tr<?php echo (in_array($chapter_id,$unread_list))?' class="highlight"':(($alt=!$alt)?'':' class="alt"') ?>>
	<?php } else { ?>
	<tr <?=$class?>>
	<?php } ?>
		<td style="text-align:center;"><input type="checkbox" name="chapter_list[]" value="<?php echo $chapter_id ?>" checked="checked" /></td>
		<td style="text-align:center;"><?php echo $chapter['chapter_rank'] ?></td>
		<td style="text-align:center;">
			<a href="<?php echo $movedown_url.$chapter_id ?>"<?php echo ($counter<count($project_chapters))?'':' style="visibility:hidden;"' ?>><img border="0" src="/webadmin/images/arrowDown.gif" /></a>
			<a href="<?php echo $moveup_url.$chapter_id ?>"<?php echo ($counter>1)?'':' style="visibility:hidden;"' ?>><img border="0" src="/webadmin/images/arrowUp.gif" /></a>
		</td>
		<td style="text-align:center;"><?php echo htmlspecialchars($chapter['chapter_title']) ?></td>
		<td style="text-align:center;"><?php echo date('d/m/y',($chapter['change_time']>0)?$chapter['change_time']:$chapter['create_time']) ?></td>
		<td style="text-align:center;"><?php echo htmlspecialchars($staff_list[($chapter['change_time']>0)?$chapter['change_staff']:$chapter['create_staff']]) ?></td>
		<td style="text-align:center;"><a href="<?php echo $view_url.$chapter_id ?>" target="_blank">View</a></td>

		<?php if ($project_lock === false || $project_lock['staff_id'] == $_GLOBALS['staff']['id']) { ?>
		<td style="text-align:center;">
			<?php echo '<a href="'.$edit_url.$chapter_id.'" target="_blank">Edit</a>' ?>
			<?php if ($project_lock !== false && $project_lock['staff_id'] == $_GLOBALS['staff']['id']) { ?>
			<br><a href="<?php hecho($unlock_url . $chapter_id) ?>">Unlock</a>
			<?php } ?>
		</td>
		<?php } else { ?>
			<td style="text-align: center;"><a onmouseover="<?php hecho("tt_in(" . jecho($lock_tooltip, 1) . ");") ?>" onmouseout="tt_out();">Locked</a></td>
		<?php } ?>
		<td style="text-align:center;"><?php echo '<a href="'.$delete_url.$chapter_id.'">Delete</a>' ?></td>
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
	theForm.action='index.php?action=summary&project_id=<?php echo $project_id ?>';
	theForm.submit();
	theForm.action=oldAction;
}
</script>

<input type="checkbox" onchange="javascript:checkAll(this.checked);" checked="checked" /> Check All<br />

<br />
<br />

<input type="button" value="Print Summary" onclick="javascript:postPrintSummary(this.form);" />
<input type="button" value="Print Selected Project" onclick="javascript:this.form.submit();" />

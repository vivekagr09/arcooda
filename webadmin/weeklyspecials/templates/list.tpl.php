<!-- list.tpl.php -->

<?php
$alt=false;
$ord_img='<img src="/webadmin/images/'.(($order=='desc')?'desc':'asc').'.png" />';

$sort_url=$get_vars;
$sort_url['action']='list';
$sort_url['order']=($order=='desc')?'asc':'desc';
unset($sort_url['order_by']);
$sort_url='index.php?'.http_build_query($sort_url).'&order_by=';

$toggle_url=$get_vars;
$toggle_url['action']='toggle';
unset($toggle_url['id']);
$toggle_url='index.php?'.http_build_query($toggle_url).'&id=';

$edit_url=$get_vars;
$edit_url['action']='edit';
unset($edit_url['id']);
$edit_url='index.php?'.http_build_query($edit_url).'&id=';

$view_url=$get_vars;
$view_url['action']='view';
unset($view_url['id']);
$view_url='index.php?'.http_build_query($view_url).'&id=';

$unlock_url=$get_vars;
$unlock_url['action']='unlock';
unset($unlock_url['id']);
$unlock_url='index.php?'.http_build_query($unlock_url).'&id=';

$select_url=$get_vars;
$select_url['action']='select';
unset($select_url['id']);
$select_url='index.php?'.http_build_query($select_url).'&id=';
?>

<script type="text/javascript">
//<![CDATA[
//]]>
</script>

<?= formatHTMLTitle('WeeklySpecials List') ?>

<div>
	<table class="wide spacetable">
		<tr>
			<th><a href="<?= $sort_url ?>id">ID</a><?= ($order_by=='id')?$ord_img:'' ?></th>
			<th style="width:100%;"><a href="<?= $sort_url ?>title">Title</a><?= ($order_by=='title')?$ord_img:'' ?></th>
			<th>Active</th>
			<th><a href="<?= $sort_url ?>modified">Date Modified</a><?= ($order_by=='modified')?$ord_img:'' ?></th>
			<th>&nbsp;</th>
		</tr>
<?php
foreach($weeklyspecial_list as $id)
{
	$weeklyspecial=$section_handler->fromDatabase($id);
	$weeklyspecial_lock=$section_handler->getLock($id);

	$timeout=60*60*24*7;

	$class='';
	if($alt=!$alt)		$class=' class="alt"';
	if($weeklyspecial['active']=='y')	$class=' class="highlight"';
	if($weeklyspecial_lock!==false)	$class=' class="locked"';
	if($weeklyspecial_lock['staff_id']==$_GLOBALS['staff']['id'])		$class=' class="mylock"';
?>
		<tr<?= $class ?>>
			<td style="text-align:center;"><?= $section_handler->formatNumber($id) ?></td>
			<td style="text-align:center;"><?= htmlspecialchars($weeklyspecial['title']) ?></td>
			<td style="text-align:center;">
<?php
if($weeklyspecial['active']=='y') echo 'Active'; else echo 'Expired';
?>
			</td>
			<td style="text-align:center;"><?= date("d/m/y",$weeklyspecial['modified_time']) ?></td>
			<td style="text-align:center;">
<?php
	if($popup_window)
	{
?>
				<input type="button" value=" Select " onclick="window.location='<?= $select_url.$id ?>'" />
				<input type="button" value=" Edit " onclick="window.open('<?= $edit_url.$id ?>');" />
<?php
	}
	else
	{
		if($weeklyspecial_lock===false || $weeklyspecial_lock['staff_id']==$staff_id)
		{
?>
				<a href="<?= $edit_url.$id ?>" target="_blank">Edit</a>
<?php
			if($weeklyspecial['active']=='y')
			{
?>
				<a href="<?= $toggle_url.$id ?>">Deactivate</a>
<?php
			}
			else
			{
?>
				<a href="<?= $toggle_url.$id ?>">Activate</a>
<?php
			}
		}
		else
		{
?>
				<a href="<?= $view_url.$id ?>" target="_blank">View</a>
<?php
		}
		if($weeklyspecial_lock!==false && $weeklyspecial_lock['staff_id']==$_GLOBALS['staff']['id'])
		{
?>
				<a href="<?= $unlock_url.$id ?>">Unlock</a>
<?php
		}
	}
?>
			</td>
		</tr>
<?php
}
?>
	</table>
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionWeeklySpecialList_hideNote">List of WeeklySpecials can be viewed by Clicking "WeeklySpecial List" above<br /></div>

<!-- /list.tpl.php -->

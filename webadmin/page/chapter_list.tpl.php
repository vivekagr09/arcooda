<!-- chapter_list.tpl -->

<?php
$alt     = false;
$ord_img = '<img src="/webadmin/' . (('desc' == $order) ? 'desc' : 'asc') . '.gif" />';

$staff_list  = pageGetOptionList('staff_id');
$active_list = pageGetOptionList('active');

$temp = $get_vars;
unset($temp['id']);
foreach (array('activate', 'copy', 'deactivate', 'delete', 'edit', 'select', 'unlock', 'view', 'move_up', 'move_down') as $action) {
    $temp['action']     = 'chapter_' . $action;
    ${$action . '_url'} = 'index.php?' . http_build_query($temp) . '&id=';
}

$sort_url           = $get_vars;
$sort_url['action'] = 'list';
$sort_url['order']  = ('desc' == $order) ? 'asc' : 'desc';
unset($sort_url['order_by']);
unset($sort_url['id']);
$sort_url = 'index.php?' . http_build_query($sort_url) . '&order_by=';

?>
<table class="wide spacetable">
	<tr>
		<th>
<?php
if ('y' == $_REQUEST['multiple']) {
} else {
?>
			<a href="<?php hecho($sort_url . 'rank')?>">Rank</a><?=('rank' == $order_by) ? $ord_img : ''?>
<?php
}
?>
		</th>
		<th style="width:80%;"><a href="<?=$sort_url?>heading">Sub-Menu</a><?=('heading' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>created">Date Added</a><?=('created' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>created_staff_id">Added By</a><?=('created' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>modified">Date Updated</a><?=('modified' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>modified_staff_id">Change By</a><?=('created' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>active">Active</a><?=('active' == $order_by) ? $ord_img : ''?></th>
		<th>&nbsp;</th>
	</tr>
<?php
$created_staff_id_list = pageGetOptionList('created_staff_id');
$modified_staff_id_list = pageGetOptionList('modified_staff_id');
foreach ($chapter_list as $id) {
    $chapter      = pageChapterFromDatabase($id);
    $chapter_lock = pageChapterGetLock($id);

    $class = '';
    if ($alt = !$alt) {
        $class = ' class="alt"';
    }

    if (false !== $chapter_lock) {
        $class = ' class="locked"';
    }

    if ($chapter_lock['staff_id'] == $_GLOBALS['staff']['staff_id']) {
        $class = ' class="mylock"';
    }

?>
	<tr<?=$class?> style="text-align:center;">
		<td nowrap>
<?php
	if ('y' == $_REQUEST['multiple']) {
?>
			<input type="checkbox" name="chapter_list[]" value="<?=$id?>" />
<?php
	} else {
?>
			<a href="<?php hecho($move_up_url . $id)?>"><img src="/webadmin/images/arrowUp.gif" border="0" /></a><a href="<?php hecho($move_down_url . $id)?>"><img src="/webadmin/images/arrowDown.gif" border="0" /></a>
<?php
	}
?>
		</td>
		<td style="text-align:left;"><a href="<?php hecho($view_url . $id)?>" target="_blank"><?php hecho($chapter['heading'])?></a></td>
		<td><?=date('d/m/Y', $chapter['create_time'])?></td>
		<td><?=$created_staff_id_list[$chapter['created_staff_id']]?></td>
		<td><?=($chapter['change_time'] > 0) ? date('d/m/Y', $item['change_time']) : ' - '?></td>
		<td><?=$modified_staff_id_list[$chapter['modified_staff_id']]?></td>
		<td><a href="<?php hecho((('y' == $chapter['active']) ? $deactivate_url : $activate_url) . $id)?>"><?php hecho($active_list[$chapter['active']])?></a></td>
		<td>
<?php
	if ($popup_window) {
?>
			<input type="button" value=" Select " onclick="window.location='<?=$select_url . $id?>'" />
<?php
	} else {
        if (false === $chapter_lock || $chapter_lock['staff_id'] == $_GLOBALS['staff']['staff_id']) {
?>
			<a href="<?=$edit_url . $id?>" target="_blank">Edit</a>
			<a href="<?=$delete_url . $id?>" onclick="return confirm('Are you sure you want to delete this sub-menu ??')">Delete</a>
<?php
		} else {
?>
			<a href="<?=$view_url . $id?>" target="_blank">View</a>
<?php
		}
        if (false !== $chapter_lock && $chapter_lock['staff_id'] == $_GLOBALS['staff']['staff_id']) {
?>
			<a href="<?=$unlock_url . $id?>">Unlock</a>
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

<?php
if (!$popup_window && false) {
?>
<br />

<span style="font-style:italic;">Notes:</span><br />
Rows marked in <span style="background-color:#EE90A0;">red</span> are locked by another user<br />
Rows marked in <span style="background-color:#90A0EE;">blue</span> are locked by you<br />
<?php
}

if ('y' == $_REQUEST['multiple']) {
?>
<p style="text-align:center;">
	<input type="submit" value="Select Topics" />
</p>
<?php
}
?>

<!-- /chapter_list.tpl -->

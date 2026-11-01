<!-- list.tpl -->

<?php
$alt     = false;
$ord_img = '<img src="/webadmin/' . (('desc' == $order) ? 'desc' : 'asc') . '.gif" />';

$staff_list             = pageGetOptionList('staff_id') + array('N/A');
$active_list            = pageGetOptionList('active');
$modified_staff_id_list = pageGetOptionList('modified_staff_id') + array('N/A');
$created_staff_id_list  = pageGetOptionList('created_staff_id') + array('N/A');

$temp = $get_vars;
unset($temp['id']);
foreach (array('activate', 'copy', 'deactivate', 'delete', 'edit', 'select', 'unlock', 'view', 'move_up', 'move_down', 'chapter_list') as $action) {
    $temp['action']     = $action;
    ${$action . '_url'} = 'index.php?' . http_build_query($temp) . '&' . (('chapter_list' == $action) ? 'page_' : '') . 'id=';
}

$sort_url           = $get_vars;
$sort_url['action'] = 'list';
$sort_url['order']  = ('desc' == $order) ? 'asc' : 'desc';
unset($sort_url['order_by']);
unset($sort_url['page_id']);
$sort_url = 'index.php?' . http_build_query($sort_url) . '&order_by=';

$sub_page_url           = $get_vars;
$sub_page_url['action'] = 'list';
unset($sub_page_url['parent_id']);
unset($sub_page_url['page_id']);
$sub_page_url = 'index.php?' . http_build_query($sub_page_url) . '&parent_id=';
?>
<table class="table_grid">
	<tr>
		<th>
<?php
if (array_key_exists('multiple', $_REQUEST) && 'y' == $_REQUEST['multiple']) {
} else {
?>
			<a href="<?php hecho($sort_url . 'rank')?>">Rank</a><?=('rank' == $order_by) ? $ord_img : ''?>
<?php
}
?>
		</th>
		<th><a href="<?=$sort_url?>id">Topic ID</a><?=('id' == $order_by) ? $ord_img : ''?></th>
		<th style="width:80%;"><a href="<?=$sort_url?>name">Page Name</a><?=('name' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>created">Date Added</a><?=('created' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>created_staff_id">Added By</a><?=('created' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>modified">Date Updated</a><?=('modified' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>modified_staff_id">Change By</a><?=('created' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>active">Active</a><?=('active' == $order_by) ? $ord_img : ''?></th>
		<th>&nbsp;</th>
	</tr>
<?php
$done = array();
while (count($item_list)) {
	$key = array_shift($item_list);
	if (preg_match('/^(?P<indent>\\s*)(?P<id>\\d+)$/', $key, $m)) {
		$id     = intval($m['id']);
		$indent = $m['indent'];
	} else {
		$id     = intval($key);
		$indent = '';
	}

	if (in_array($id, $done)) {
		continue;
	}
	$done[] = $id;

	foreach (array_reverse(pageSearch(array('parent_id' => $id))) as $v) {
		array_unshift($item_list, $indent . ' ' . $v);
	}

	$item      = pageFromDatabase($id);
	$item_lock = pageGetLock($id);

	$class = $item['active'] == 'y'?' class="highlight"' : '';
	//if ($alt = !$alt) $class = ' class="alt"';
	if (!empty($item_lock)) {
		$class = ' class="locked"';
	}

	if ($item_lock['staff_id'] == $_GLOBALS['staff']['staff_id']) {
		$class = ' class="mylock"';
	}
?>
	<tr<?=$class?> style="text-align:center;" onmouseover="this.style.backgroundColor='#9DD6D2';" onmouseout="this.style.backgroundColor='';" >
		<td nowrap="nowrap">
<?php
	if (array_key_exists('multiple', $_REQUEST) && 'y' == $_REQUEST['multiple']) {
?>
			<input type="checkbox" name="page_list[]" value="<?php hecho($item["id"])?>" />
<?php
	} else {
?>
			<a href="<?php hecho($move_up_url . $item["id"])?>"><img src="/webadmin/images/arrowUp.gif" width="11" height="11" border="0" /></a><a href="<?php hecho($move_down_url . $item["id"])?>"><img src="/webadmin/images/arrowDown.gif" width="11" height="11" border="0" /></a>
<?php
	}
?>
		</td>
		<td><?=pageFormatNumber($item['id'])?></td>
		<td style="text-align:left;"><?=str_replace(' ', '<span style="visibility:hidden">&rarr;</span>', $indent)?><a href="<?php hecho($item['page_url'])?>" target="_blank"><?php hecho($item['name'])?></a></td>
		<td><?=date('d/m/Y', $item['create_time'])?></td>
		<td style="white-space:nowrap;"><?=$created_staff_id_list[$item['created_staff_id']]?></td>
		<td><?=($item['change_time'] > 0) ? date('d/m/Y', $item['change_time']) : ' - '?></td>
		<td style="white-space:nowrap;"><?=@$modified_staff_id_list[$item['modified_staff_id']]?></td>
		<td><a href="<?php hecho((('y' == $item['active']) ? $deactivate_url : $activate_url) . $item["id"])?>"><?php hecho($active_list[$item['active']])?></a></td>
		<td>
<?php
	if ($popup_window) {
?>
			<input type="button" value=" Select " onclick="window.location='<?=$select_url . $item["id"]?>'" />
<?php
	} else {
		if (empty($item_lock) || $item_lock['staff_id'] == $_GLOBALS['staff']['staff_id']) {
?>
			<a href="<?=$edit_url . $item["id"]?>" target="_blank">Edit</a>
			<!-- <a href="<?=$delete_url . $item["id"]?>" target="_blank">Delete</a> -->
			<!-- <a href="<?=$chapter_list_url . $item["id"]?>" target="_blank">Add/Edit Sub-Menus</a> -->
<?php
		} else {
?>
			<a href="<?=$view_url . $item["id"]?>" target="_blank">View</a>
<?php
		}
		if (!empty($item_lock) && $item_lock['staff_id'] == $_GLOBALS['staff']['staff_id']) {
?>
			<a href="<?=$unlock_url . $item["id"]?>">Unlock</a>
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

if (array_key_exists('multiple', $_REQUEST) && 'y' == $_REQUEST['multiple']) {
?>
<p style="text-align:center;">
	<input type="submit" value="Select Topics" />
</p>
<?php
}
?>

<!-- /list.tpl -->

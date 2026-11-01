<?php
include_once __DIR__ . '/common.inc.php';

global $DB,$_GLOBALS;
include_once __DIR__ . '/header.inc.php';

$view_list = array();
$query = $DB->query('SELECT * FROM `product_page_carousel` order by id desc');
while($row = $DB->fetch_assoc($query)) {
	$view_list[$row['id']] = $row;
}
$DB->free_result($query);
if (isset($view_list) && is_array($view_list) && count($view_list)) {
?>
<?= formatHTMLTitle('Front Page carousel') ?>
<div>
    <table class="table_grid" style="white-space:nowrap;" cellpadding="4">
        <tr>
            <th> Id</th>
            <th style="width:20%;">Name</th>
            <th>Comment</th>
            <th>Default</th>
            <th>Active</th>
             <th>Type</th>
            <th>Created At</th>
            <th></th>
        </tr>
<?php
    foreach ($view_list  as $list) { ?>
        <tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
    <td style="text-align:center;"><?=$list['id']?></td>
    <td style="text-align:left;"><?=$list['name']?></td>
    <td style="text-align:left;"><?=$list['comment']?></td>
    <td style="text-align:center;"><?=('y' == $list['default']) ? 'Yes' : 'No'?></td>
    <td style="text-align:center;"><?=('y' == $list['status']) ? 'Yes' : 'No'?></td>
    <td style="text-align:center;"><?=ucwords($list['type']);?></td>
    <td style="text-align:center;"><?=date('d/m/Y',strtotime($list['created_at']))?></td>
    <td style="text-align:center;">
        <a href="preview.php?id=<?=$list['id']?>" target="_blank">Preview</a> |
        <a href="addedit.php?action=do_edit&id=<?=$list['id']?>">Edit</a> |
		<a href="delete.php?id=<?=$list['id']?>">Delete</a>
    </td>
</tr>
 <?php   }
?>
    </table>
<?php
} else {
?>
    No carousel Available to display
<?php
}
?>
    <br />
    <br />
</div>
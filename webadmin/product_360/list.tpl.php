<?php
/**
 * @var int[] $item_list
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
global $_GLOBALS;
global $DB;

$view_list     = view360productlist();
if (isset($view_list) && is_array($view_list) && count($view_list)) {
?>
<?= formatHTMLTitle('360 View') ?>

<div>
    <table class="table_grid" style="white-space:nowrap;" cellpadding="4">
        <tr>
            <th> Id</th>
            <th style="width:20%;">Name</th>
            <th>Comment</th>
            <th>Active</th>
            <th>Created At</th>
            <th></th>
        </tr>
<?php
    foreach ($view_list  as $list) { ?>
        <tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
    <td style="text-align:right;"><?=$list['id']?></td>
    <td style="text-align:left;"><?=$list['name']?></td>
    <td style="text-align:left;"><?=$list['comment']?></td>
    <td style="text-align:center;"><?=(1 == $list['status']) ? 'Yes' : 'No'?></td>
    <td style="text-align:center;"><?=date('d/m/Y',strtotime($list['created_at']))?></td>
    <td style="text-align:center;">
        <a href=".?action=edit&amp;id=<?=$list['id']?>" target="_blank">Edit</a> 
    </td>
</tr>
 <?php   }
?>
    </table>
<?php
} else {
?>
    No 360 product to display
<?php
}
?>
    <br />
    <br />
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

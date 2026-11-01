<!-- _last_5_changes.tpl -->

<?php
/**
 * Recent Changes Template
 *
 * Inputs:
 * - $item_changes
 */

if (!isset($item_changes) || !is_array($item_changes)) {
    return;
}

$created_by_list = productGetOptionList('staff_id');
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionLastChanges&quot;);return false">Last 5 Changes</a>');?>

<div id="sectionLastChanges">
    <table class="table_grid">
        <tr><th colspan="4">Previous Comments</th></tr>
        <tr style="background:#E3EBF7;">
            <td width="5%"><b>Date</b></td>
            <td width="10%"><b>User</b></td>
            <td width="85%"><b>Comments</b></td>
        </tr>
<?php
$temp = $item_changes;
if (count($item_changes) > 5 && 'changes' !== $action) {
    $temp = array_chunk($temp, 5);
    $temp = reset($temp);
}
foreach ($temp as $comment_id) {
    $row = productCommentFromDatabase($comment_id);
?>
        <tr<?=(isset($alt) && $alt = !$alt) ? ' class="alt"' : ''?>>
            <td style="white-space:nowrap;"><?=date(_('Y-m-d'), (is_null($row['created_time'])) ? time() : $row['created_time'])?></td>
            <td style="text-align:center"><?php hecho($created_by_list[$row['created_by']])?></td>
            <td><div style="text-align:left;height:96px;overflow:auto;"><?=nl2br(hecho($row['comments'], 1))?></div></td>
        </tr>
<?php
}
?>
        <tr class="alt">
            <td colspan="4" style="text-align:center;"><input type="button" value="View All (<?=count($item_changes)?>)" onclick="window.open(&quot;comments.php?type=sql&amp;id=<?=$item['id']?>&quot;)" /></td>
        </tr>
    </table>
    <br /><br />
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionLatestChanges_hideNote">
    Lastest changes can be viewed by Clicking "Latest 5 Changes" above<br />
</div>

<!-- /_last_5_changes.tpl -->
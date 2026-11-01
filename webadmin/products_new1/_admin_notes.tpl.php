<!-- _admin_notes.tpl -->

<?php
/**
 * Product Admin Notes Template
 *
 * Inputs:
 * - $_SERVER
 * - - REQUEST_TIME
 * - $action
 * - $item_admin_notes[]
 * - - comments
 * - - created_at
 * - - created_by
 *
 * Overwrites:
 * - $alt
 * - $row
 * - $temp
 * - $temp2
 */

if (!isset($item_admin_notes) || !is_array($item_admin_notes)) {
    return;
}

?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionAdminNotes&quot;);return false">Admin Notes</a>');?>

<div id="sectionAdminNotes">
    <table class="table_fields">
        <tr>
            <th>New Comment :</th>
            <td><?=formatHTMLTextarea('item_admin_note', '', true)?></td>
        </tr>
    </table>
    <br />
    <br />

    <table class="table_grid">
        <tr><th colspan="4">Previous Comments</th></tr>
        <tr style="background:#E3EBF7;">
            <td width="5%"><b>Date</b></td>
            <td width="10%"><b>User</b></td>
            <td width="80%"><b>Comments</b></td>
            <td width="5%"></td>
        </tr>
<?php
if (count($item_admin_notes)) {
    $temp2 = productGetOptionList('created_by');
    $temp = $item_admin_notes;
    if (count($item_admin_notes) > 5 && 'admin_notes' !== $action) {
        $temp = array_chunk($item_admin_notes, 5);
        $temp = reset($temp);
    }
    $alt = false;
    foreach ($temp as $row) {
        $row = productCommentFromDatabase($row);
?>
        <tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
            <td style="white-space:nowrap;"><?=date(_('Y-m-d'), ($action === 'add') ? time() : strtotime($row['created_at'])) ?></td>
            <td><?php hecho($temp2[$row['created_by']])?></td>
            <td style="text-align:left;"><?=nl2br(hecho($row['comments'], 1))?></td>
            <td>
                <a href="#" class="unfinished"><i class="fas fa-pen"></i></a>
                <a href="#" class="unfinished"><i class="fas fa-trash-alt"></i></a>
            </td>
        </tr>
<?php
    }
    unset($temp);
    unset($alt);
    unset($temp2);
}
?>
    </table>
    <div style="text-align:center">
        <input type="button" value="View All (<?=count($item_admin_notes)?>)" onclick="window.open(&quot;.?action=admin_notes&amp;id=<?=$item['id']?>&quot;)" />
    </div>
    <br />
    <br />
    <input type="hidden" name="item_admin_notes_deleted" value="" />
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionAdminNotes_hideNote">
    Admin Notes can be viewed by Clicking "Admin Notes" above<br />
</div>

<!-- /_admin_notes.tpl -->

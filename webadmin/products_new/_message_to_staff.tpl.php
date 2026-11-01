<!-- _message_to_staff.tpl -->

<?php
/**
 * Product Staff Messages Template
 *
 * Inputs:
 * - $item_staff_messages
 */

if (!isset($item_staff_messages) || !is_array($item_staff_messages)) {
    return;
}

$active_staff_list = productGetOptionList('active_staff_id');
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionMessage&quot;);return false">Message To Staff</a>')?>

<div id="sectionMessage">
    <table class="table_fields">
        <tr>
            <th>Message To</th>
            <td><select name="item_message_to_staff[staff_list][0]">
                <option value=""></option>
                <?=formatHTMLOptions($active_staff_list)?>
            </select></td>
        </tr>
        <tr>
            <th>Message To</th>
            <td><select name="item_message_to_staff[staff_list][1]">
                <option value=""></option>
                <?=formatHTMLOptions($active_staff_list)?>
            </select></td>
        </tr>
        <tr>
            <th>Message<br /><span style="color:#f00;font-weight:normal;">This message will be sent by email to the staff member(s) selected</span></th>
            <td><?=formatHTMLTextarea('item_message_to_staff[message]', '', true)?></td>
        </tr>
    </table>
    <br /><br />
<?php
if (is_array($item_staff_messages) && count($item_staff_messages) > 0) {
?>
    <table class="table_grid">
        <tr><th colspan="4">Previous Messages</th></tr>
        <tr style="background:#E3EBF7;">
            <td width="5%"><b>Date</b></td>
            <td width="10%"><b>User</b></td>
            <td width="85%"><b>Message</b></td>
        </tr>
<?php
    $comments   = array_chunk($item_staff_messages, 5);
    $created_by_list = productGetOptionList('created_by');
    foreach ($comments[0] as $comment_id) {
        $row = productCommentFromDatabase($comment_id);
?>
        <tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
            <td style="white-space:nowrap;"><?=date(_('Y-m-d'), (is_null($row['created_time'])) ? time() : $row['created_time'])?></td>
            <td><?php hecho($created_by_list[$row['created_by']])?></td>
            <td><div style="text-align:left;height:96px;overflow:auto;"><?=nl2br(hecho($row['comments'], 1))?></div></td>
        </tr>
<?php
}
    if (count($comments) > 1) {
?>
        <tr class="alt">
            <td colspan="4" style="text-align:center;"><input type="button" value="View All (<?=count($item_staff_messages)?>)" onclick="window.open(&quot;.?action=admin_notes&id=<?=$item['id']?>&quot;)" /></td>
        </tr>
<?php
}
?>
    </table>
    <br /><br />
<?php
}
?>
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionMessage_hideNote">
    Message to Staff can be viewed by Clicking "Message To Staff" above<br />
</div>

<!-- /_message_to_staff.tpl -->

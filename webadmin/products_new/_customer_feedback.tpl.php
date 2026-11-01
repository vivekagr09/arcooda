<!-- _customer_feedback.tpl -->

<?php
/**
 * Product Customer Feedback Template
 *
 * Inputs:
 * - $action
 * - $item
 * - - id
 * - $item_feedback[]
 * - - comments
 * - - created_at
 *
 * Overwrites:
 * - $alt
 * - $temp
 * - $temp2
 */

if (!isset($item_feedback) || !is_array($item_feedback)) {
    return;
}

if ('add' === $action) {
    return;
}

?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionCustomerFeedback&quot;);return false">Customer Feedback</a>')?>

<div id="sectionCustomerFeedback">
    <table class="table_grid">
        <tr><th colspan="4">Previous Comments</th></tr>
        <tr style="background:#E3EBF7;">
            <th width="5%"><b>Date</b></th>
            <th width="10%"><b>Member</b></th>
            <th width="85%"><b>Comments</b></th>
        </tr>
<?php

$temp = $item_feedback;
if ('feedback' !== $action) {
    $temp = array_chunk($item_feedback, 5);
    $temp = reset($temp);
}
$alt = false;
foreach ($temp as $temp2) {
    $temp2 = productCommentFromDatabase($temp2);
?>
        <tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
            <td><?=date(_('Y-m-d'), strtotime($temp2['created_at']))?></td>
            <td></td>
            <td><?=nl2br(hecho($temp2['comments'], 1))?></td>
        </tr>
<?php
}
unset($temp2);
unset($alt);
unset($temp);

?>
    </table>
    <div style="text-align:center">
        <input type="button" value="View All (<?=count($item_feedback)?>)" onclick="window.open(&quot;.?action=feedback&amp;id=<?=$item['id']?>&quot;)" />
    </div>
    <br />
    <br />
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionCustomerFeedback_hideNote">
    Customer Feedback can be viewed by Clicking "Customer Feedback" above<br />
</div>

<!-- /_customer_feedback.tpl -->

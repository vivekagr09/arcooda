<?php
/**
 * @var Serial_Handler $section_handler
 * @var int[]          $item_changes
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
if (count($item_changes) > 0) {
    $staff_list = $section_handler->getOptionList('staff_id');
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionChanges&quot;);return false">' . htmlspecialchars(_('Changes')) . '</a>')?>

<div id="sectionChanges">
	<table class="table table_grid">
		<tr><th colspan="3" style="border:1px solid #ccc;background:#eee">Changes</th></tr>
		<tr><th>Date</th><th>User</th><th>Changes</th></tr>
<?php
    $alt = false;
    foreach ($item_changes as $message_id) {
        $change = $section_handler->commentFromDatabase($message_id);
?>
		<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
			<td style="white-space:nowrap;text-align:center"><?=date('d/m/Y', $change['comment_time'])?></td>
			<td style="white-space:nowrap;text-align:center"><?=htmlspecialchars($staff_list[$change['staff_id']] ?? 'N/A')?></td>
			<td style="width:100%"><?=nl2br(htmlspecialchars($change['comment_text']))?></td>
		</tr>
<?php
    }
?>
	</table>
	<br /><br />
</div>

<?php
}
?>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

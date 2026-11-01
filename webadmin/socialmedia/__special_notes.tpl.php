<!-- __special_notes.tpl -->

<?php
moduleLoad('member');

if (!function_exists('memberCommentsFromDatabase')) {
	return;
}

$item_special_notes = memberCommentsFromDatabase($item['member_id']);
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionSpecialNotes\');return false">Special Notes</a>')?>

<div id="sectionSpecialNotes">
	<table class="table_fields">
		<tr>
			<th>Special Notes :</th>
			<td><textarea name="item[special_notes]" class="wide" rows="5" placeholder="Type any special notes here"></textarea></td>
		</tr>
	</table>
	<br />
	<br />
<?php
if (count($item_special_notes)) {
?>
	<table class="table_grid" id="requests">
		<tr><th colspan="4">Special Notes</th></tr>
		<tr style="background-color:#eef;">
			<th width="10%" nowrap="nowrap"><b>Date</b></th>
			<th width="20%" nowrap="nowrap"><b>User</b></th>
			<th width="60%" nowrap="nowrap"><b>Comments</b></th>
			<th width="60%" nowrap="nowrap"><b>Remove</b></th>
		</tr>
<?php
	$staff_list = memberGetOptionList('staff_id');
	foreach ($item_special_notes as $special_note_id) {
		$special_note = memberCommentFromDatabase($special_note_id);
?>
		<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
			<td><?=date("d M, Y", ($special_note['comment_time']))?></td>
			<td><?php hecho($staff_list[$special_note['staff_id']])?></td>
			<td><?=nl2br(hecho($special_note['comment_text'], 1))?></td>
			<td><input type="button" value=" Remove " onclick="removeSpecialRequest('<?=$special_note['id']?>');" disabled="disabled" /></td>
		</tr>
<?php
    }
?>
	</table>
	<br />
	<br />
<?php
}
?>
</div>

<div style="background:#fff;text-align:center;padding:8px;display:none" id="sectionSpecialNotes_hideNote">Special Notes can be viewed by Clicking "Special Notes" above<br /></div>

<!-- /__special_notes.tpl -->

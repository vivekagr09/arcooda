<!-- comments.tpl -->

<?php
if (isset($contractor_comments) && is_array($contractor_comments)) {
    $staff_list = contractorGetOptionList('staff_id');
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectioncontractorComments\');return false">Comments</a>', '<a style="color:#000;" href=".?action=view_all&amp;contractor_id=' . $contractor_id . '" target="_blank">View All</a>')?>

<div id="sectioncontractorComments">
	<table class="table_fields">
		<tr>
			<th>New Comment :</th>
			<td><?=formatHTMLTextarea('comments', '')?></td>
		</tr>
	</table>
	<br /><br />
	<table class="table_grid">
		<tr><th colspan="4">Previous Comments</th></tr>
		<tr style="background-color:#eef;">
			<th style="width:10%;">Date</th>
			<th style="width:20%;">User</th>
			<th style="width:70%;">Comments</th>
		</tr>
<?php
	if (count($contractor_comments) > 0) {
        foreach (array_reverse($contractor_comments) as $comment_id) {
            $comment = contractorCommentFromDatabase($comment_id);
?>
		<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
			<td><?=date("d M, Y", ($comment['comment_time']))?></td>
			<td><?php hecho($staff_list[$comment['staff_id']])?></td>
			<td><?=nl2br(hecho($comment['comment_text'], 1))?></td>
		</tr>
<?php
		}
    }
?>
	</table>
	<br /><br />
</div>

<div style="background-color:#fff;text-align:center;padding:8px;display:none" id="sectioncontractorComments_hideNote">contractor Comments can be viewed by Clicking "Comments" above</div>

<?php
}
?>

<!-- /comments.tpl -->

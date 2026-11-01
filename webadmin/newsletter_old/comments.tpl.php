<!-- comments.tpl.php -->

<?php
if (is_array($item_comments)) {
    $staff_list = $section_handler->GetOptionList('staff_id');
?>

<script type="text/javascript">
//<![CDATA[
//]]>
</script>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionComments\');">Comments</a>', '<a style="color:#000;" href="index.php?action=comments&amp;id=' . $id . '" target="_blank">View All</a>')?>

<div id="sectionComments">
	<table class="wide">
		<tr>
			<td class="left_title" style="width:25%;">New Comment :</td>
			<td><?=formatHTMLTextarea('comments', '')?></td>
		</tr>
	</table>
	<br /><br />
	<table class="wide">
		<tr><th colspan="4" class="box">Previous Comments</th></tr>
		<tr style="background-color:#eef;">
			<th style="width:10%;" class="box">Date</th>
			<th style="width:20%;" class="box">User</th>
			<th style="width:70%;" class="box">Comments</th>
		</tr>
<?php
if (count($item_comments) > 0) {
        foreach ($item_comments as $comment_id) {
            $comment = $section_handler->CommentFromDatabase($comment_id);
?>
		<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
			<td class="box"><?=date("d M, Y", ($comment['comment_time']))?></td>
			<td class="box"><?php hecho($staff_list[$comment['staff_id']])?></td>
			<td class="box"><?=nl2br(hecho($comment['comment_text'], 1))?></td>
		</tr>
<?php
}
    }

?>
	</table>
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionComments_hideNote">Comments can be viewed by Clicking "Comments" above<br /></div>

<?php
}
?>

<!-- comments.tpl.php -->

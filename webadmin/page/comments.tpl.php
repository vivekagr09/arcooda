<!-- comments.tpl -->

<?=formatHTMLTitle('<a href="#" onclick="return toggleSection(\'sectionAdminComments\')">Admin Comments</a>')?>

<div id="sectionAdminComments">
	<table class="table_fields">
		<tr>
			<th>Comment<br /><span style="font-size:9px;color:red;font-weight:normal;">Admin comments are never displayed on the main site</span></th>
			<td><textarea name="admin_notes" rows="8" cols="80" onkeyup="textareaAutoSize(this,1,8)"></textarea></td>
		</tr>
	</table><br /><br />
<?php
if (count($item_comments)) {
    $comments = array_chunk($item_comments, 5);
    $comments = $comments[0];
?>
	<table class="table_grid">
		<tr>
			<th colspan="4" align="center" class="box"><strong>Previous Comments</strong></td>
		</tr>
		<tr style="background-color:#E3EBF7">
			<td width="12%" align="center" class="bottomLeft"><b>Date</b></td>
			<td width="13%" align="center" class="bottomLeft"><b>Staff</b></td>
			<td align="center" class="bottomLeftRight"><b>Comments</b></td>
		</tr>
<?php
	$staff_list = pageGetOptionList('staff_id');
    $alt        = false;
    foreach ($comments as $comment_id) {
        $comment = pageCommentFromDatabase($comment_id);
?>
		<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
			<td class="bottomLeft" style="text-align:center;white-space:nowrap;"><?=date("d/m/y", $comment['comment_time'])?></td>
			<td class="bottomLeft" style="text-align:center;white-space:nowrap;"><?=$staff_list[$comment['staff_id']]?></td>
			<td class="bottomLeftRight" style="text-align:left;"><?=nl2br(hecho($comment['comment'], 1))?></td>
		</tr>
<?php
	}
    if (count($item_comments) > 5) {
        $comments_url           = $get_vars;
        $comments_url['action'] = 'comments';
        $comments_url           = 'index.php?' . http_build_query($comments_url);
?>
		<tr class="alt">
			<td colspan="4" style="text-align:center;"><input type="button" value="View All (<?=count($item_comments)?>)" onclick="<?=htmlspecialchars('window.open(' . json_encode($comments_url) . ')')?>" /></td>
		</tr>
<?php
	}
?>
	</table>
	<br />
<?php
}
?>
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionAdminComments_hideNote">
	Admin Comments can be viewed by Clicking "Admin Comments" above<br />
</div>

<!-- /comments.tpl -->

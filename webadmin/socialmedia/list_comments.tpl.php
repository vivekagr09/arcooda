<?php
/**
 * @var mixed[][] $member_comments
 */
?>
<table class="wide" border="1" cellpadding="4" style="width: 95%;margin: auto;">
	<tr><th colspan="3" style="background-color:#EEEEEE">Admin Notes</td></tr>
	<tr><th>Date</th><th>User</th><th>Comment</th></tr>
<?php
$count = 0;
foreach ($member_comments as $comment) {
?>
	<tr <?=($count++ % 2 == 1) ? 'class="alt"' : ''?> style="text-align:center;">
		<td style="padding:4px;" nowrap><?=date(_('d/m/Y'), $comment['comment_time'])?></td>
		<td style="padding:4px;" nowrap><?=$comment['staff']?></td>
		<td style="padding:4px;width:100%;text-align:left;"><?=nl2br(htmlspecialchars($comment['comments']))?></td>
	</tr>
<?php
}
?>
</table><br />

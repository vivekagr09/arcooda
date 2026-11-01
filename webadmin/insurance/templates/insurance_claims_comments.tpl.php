<!-- insurance_claims_comments.tpl.php -->

<?php
foreach($insurance_claim_comments as $comment)
{
?>
<table>
	<tr>
		<th>Staff</th>
		<td><?php hecho($all_staff_list[$comment['staff_id']]) ?></td>
	</tr>
	<tr>
		<th>Date</th>
		<td><?php echo date('d/m/Y',$comment['comment_time']) ?></td>
	</tr>
	<tr>
		<th>Comment</th>
		<td style="overflow:scroll;"><?php echo nl2br(formatHTMLSafe($comment['comment_text'])) ?></td>
	</tr>
</table>
<hr />
<?php
}
?>

<!-- /insurance_claims_comments.tpl.php -->

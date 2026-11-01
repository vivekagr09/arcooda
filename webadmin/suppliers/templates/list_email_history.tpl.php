<?php
foreach($supplier_emails as $email)
{
?>
	<table class="wide" style="width:98%;" align="center">
		<tr>
			<td class="left_title" style="width:25%;">Date :</td><td style="width:25%;"><?= date('d/m/Y', $email['change_time']) ?></td>
			<td class="left_title" style="width:25%;">Sent To :</td><td style="width:25%;"><?= htmlspecialchars($email['sentto']) ?></td></tr>
		<tr>
			<td class="left_title" style="width:25%;">Title :</td>
			<td colspan="3" style="width:75%;"><?= htmlspecialchars($email['title']) ?></td></tr>
		<tr>
			<td class="left_title" style="width:25%;" valign="top">Message :</td>
			<td colspan="3" style="width:75%;"><?= nl2br(htmlspecialchars($email['query'])) ?></td>
		</tr>
	</table>
<?php
}
?>
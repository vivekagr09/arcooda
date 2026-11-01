<!-- menu.inc -->

<?php
$stub = substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT']));
$criteria = array();
$criteria['status'] = 'n';
$reviewCount = 0;
if (moduleLoad("review")) {
	$reviews = invoiceProductReview();
	foreach ($reviews as $review) {
		if($review['status'] == 'n' || $review['status'] == '') $reviewCount++;
	}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
	<tr><td class="td_options" bgcolor="#000" align="center"><b><?= htmlspecialchars(_("Product Review")) ?></b></td></tr>
	<tr><td class="td_options" nowrap="nowrap">
		<ul style="padding:0 0 0 16px;margin:0;">
			<li><a class="td_options unfinished-" href="<?=htmlspecialchars($stub)?>/?action=list"><?= htmlspecialchars(_("Product Review")) ?> (<?=count(invoiceProductReview())?>)</a></li>
			<li><a class="td_options unfinished-" href="<?=htmlspecialchars($stub)?>/?action=summary"><?= htmlspecialchars(_("Review Summary")) ?></a></li></br>
			<li><a class="td_options unfinished-" href="<?=htmlspecialchars($stub)?>/?action=approvals" style="<?= $reviewCount > 0 ? 'color:red': 'color:black'?>"><?= htmlspecialchars(_("For Approval")) ?> (<?=$reviewCount?>)</a></li>
		</ul>
	</td></tr>
</table>
<br />
<?php } ?>


<!-- /menu.inc -->

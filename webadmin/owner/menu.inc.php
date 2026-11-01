<!-- menu.inc -->

<?php
$stub = substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT']));


?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
	<tr><td class="td_options" bgcolor="#000" align="center"><b><?= htmlspecialchars(_("Owner Section")) ?></b></td></tr>
	<tr><td class="td_options" nowrap="nowrap">
		<ul style="padding:0 0 0 16px;margin:0;">
			<li><a class="td_options" href="<?=htmlspecialchars($stub)?>/products/list.php"><?= htmlspecialchars(_("Products")) ?></a></li>
			<li><a class="td_options" href="<?=htmlspecialchars($stub)?>/blogs/?action=list"><?= htmlspecialchars(_("Owner News")) ?></a></li>
			<li><a class="td_options" href="<?=htmlspecialchars($stub)?>/manuals/?action=list"><?= htmlspecialchars(_("Manuals")) ?></a></li>
			<li><a class="td_options" href="<?=htmlspecialchars($stub)?>/firmware/?action=list"><?= htmlspecialchars(_("Firmware")) ?></a></li>
			<li><a class="td_options"  href="<?=htmlspecialchars($stub)?>/registered/?action=list"><?= htmlspecialchars(_("Registered Owners")) ?></a></li>
			
			<br/>
			<li><a class="td_options " href="<?=htmlspecialchars($stub)?>/blogs/comments.php"><?= htmlspecialchars(_("All Comments")) ?></a></li>
			<?php
					$temp = $DB->query_first(sprintf(
						"SELECT COUNT(*) AS `total` FROM `comment` WHERE `is_approve` = %d",
						0
					), MYSQLI_ASSOC);
			?>
			<li><a class="td_options" href="<?=htmlspecialchars($stub)?>/blogs/comments.php?approve=1" style="color:<?=$temp['total'] > 0 ? 'red' : ''?>">For Approval (<?=$temp['total']?>)</a></li>
			<!-- <li><a class="td_options unfinished" style="color:#000" href="<?=htmlspecialchars($stub)?>/?action=list"><?= htmlspecialchars(_("Reviewed Comments")) ?></a></li> -->
		</ul>
	</td></tr>
</table>
<br />


<!-- /menu.inc -->

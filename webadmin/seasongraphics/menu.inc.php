<!-- menu.inc -->

<?php
$stub = substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT']));


?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
	<tr><td class="td_options" bgcolor="#000" align="center"><b><?= htmlspecialchars(_("META Tags")) ?></b></td></tr>
	<tr><td class="td_options" nowrap="nowrap">
		<ul style="padding:0 0 0 16px;margin:0;">
			<li><a class="td_options" href="<?=htmlspecialchars($stub)?>/?action=add"><?= htmlspecialchars(_("Add New [Module]")) ?></a></li>
			<li><a class="td_options" href="<?=htmlspecialchars($stub)?>/?action=list"><?= htmlspecialchars(_("List All META Tags")) ?></a></li>
		</ul>
	</td></tr>
</table>
<br />


<!-- /menu.inc -->

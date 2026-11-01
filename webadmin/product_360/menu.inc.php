<?php
$stub = substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT']));

?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
	<tr><td class="td_options" bgcolor="#000" align="center"><b><?= htmlspecialchars(_("360 Product View")) ?></b></td></tr>
	<tr><td class="td_options" nowrap="nowrap">
		<ul style="padding:0 0 0 16px;margin:0;">
			<li><a class="td_options" href="<?=htmlspecialchars($stub)?>/?action=add">
				<?=htmlspecialchars(_("Add 360 Product"))?>
			</a></li>
			<li><a class="td_options" href="<?=htmlspecialchars($stub)?>/?action=setting">
				<?=htmlspecialchars(_("Default Setting"))?>
			</a></li>
<?php
$temp = $DB->query_first(sprintf(
    "SELECT COUNT(*) AS `total` FROM %s",
	"`360_view`"
), MYSQLI_ASSOC);
?>
			<li><a class="td_options" href="<?=htmlspecialchars($stub)?>/?action=list">
				<?=htmlspecialchars(_("View 360 Products"))?>(<?=$temp['total']?>)
			</a></li>
<?php
$temp = $DB->query_first(sprintf(
	"SELECT COUNT(*) AS `total` FROM %s",
	"`360_side_images`"
), MYSQLI_ASSOC);
?>
			<li><a class="td_options" href="/webadmin/360view-sideimage">
				<?=htmlspecialchars(_("360 Products Side Images"))?>(<?=$temp['total']?>)
			</a></li>
		</ul>
	</td></tr>
</table>
<br />
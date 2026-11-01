<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
global $_GLOBALS;
global $DB;



$row = $DB->query_first(sprintf(
    "SELECT COUNT(DISTINCT `a`.`id`) AS `count` FROM `highwaygroup`.`serial_numbers` AS `a` LIMIT 1"
), MYSQLI_ASSOC);

if (in_array($_GLOBALS['site_settings']['site_id'], [11, 43])) {
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
	<tr><td class="td_options" bgcolor="#000" align="center"><b><?=htmlspecialchars(_('Serial Numbers'))?></b></td></tr>
	<tr><td class="td_options" style="white-space:nowrap"><ul style="padding:0 0 0 16px;margin:0">
		<li><a class="td_options" href="/webadmin/serial/?action=add"><?=htmlspecialchars(_('Add Serial Number'))?></a></li>
		<li><a class="td_options" href="/webadmin/serial/?action=search"><?=htmlspecialchars(_('Search Serial Number'))?></a></li>
		<li><a class="td_options" href="/webadmin/serial/?action=list"><?=htmlspecialchars(_('View Serial Numbers') . ' (' . $row['count'] . ')')?></a></li>
	</ul></td></tr>
</table><br />
<?php
} else {
    if (in_array($_GLOBALS['site_settings']['site_id'], [1])) {
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
<tr><td class="td_options" bgcolor="#000" align="center"><b><?=htmlspecialchars(_('Serial Numbers'))?></b></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/serial/?action=add"><?=htmlspecialchars(_('Add Serial Number'))?></a></li>
		<li><a href="/webadmin/serial/?action=search"><?=htmlspecialchars(_('Search Serial Number'))?></a></li>
		<li><a href="/webadmin/serial/?action=list"><?=htmlspecialchars(_('View Serial Numbers'))?></a></li>
	</ul></td></tr>
</table>
<?php
    } elseif (in_array($_GLOBALS['site_settings']['site_id'], [9, 16])) {
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
<tr><td class="td_options" bgcolor="#000" align="center"><b><?=htmlspecialchars(_('Serial Numbers'))?></b></td></tr>
	<tr><td class="td_options" style="white-space:nowrap"><ul style="padding:0 0 0 16px;margin:0">
		<li><a class="td_options" href="/webadmin/serial/?action=add"><?=htmlspecialchars(_('Add Serial Number'))?></a></li>
		<li><a class="td_options" href="/webadmin/serial/?action=search"><?=htmlspecialchars(_('Search Serial Number'))?></a></li>
		<li><a class="td_options" href="/webadmin/serial/?action=list"><?=htmlspecialchars(_('View Serial Numbers'))?></a></li>
	</ul></td></tr>
</table><br />
<?php
    } else {
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
	<tr><td class="td_options" bgcolor="#000" align="center"><b><?=htmlspecialchars(_('Serial Numbers'))?></b></td></tr>
	<tr><td class="td_options" nowrap="nowrap">
<ul style="padding:0 0 0 16px;margin:0;">
	<li><a class="td_options" href="/webadmin/serial/?action=add"><?=htmlspecialchars(_('Add Serial Number'))?></a></li>
	<li><a class="td_options" href="/webadmin/serial/?action=search"><?=htmlspecialchars(_('Search Serial Number'))?></a></li>
	<li><a class="td_options" href="/webadmin/serial/?action=list"><?=htmlspecialchars(_('View Serial Numbers'))?></a></li>
</ul>
</td></tr>
</table>
<?php
    }
}

//file_put_contents($temp, ob_get_flush());
?>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

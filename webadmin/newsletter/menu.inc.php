<!-- menu.inc -->

<?php
$temp = $_SERVER["DOCUMENT_ROOT"] . "/cache/webadmin/newsletter_menu.inc.php_" . md5("");
if (is_file($temp) && (filemtime($temp) + 3600) > $_SERVER["REQUEST_TIME"])
{
	readfile($temp);
	echo "<!-- #menu.inc -->\n";
	return;
}
ob_start();

if (moduleExists("newsletter") && moduleLoad("member"))
{
	if (in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43)))
	{
		$criteria = $_GLOBALS['criteria_options']['member'];
		$criteria['status'] = array("active", "not_contacted");
		$member_site = memberGroupCount(memberSearch($criteria), 'site_id');
		$sites_roots = array();
		$newsletter_site = array();
		$query = $DB->query("SELECT `ms`.`site_id`, COUNT(`ml`.`member_id`) AS `count`, `w`.`url` FROM `highwaygroup`.`newsletter_styles` AS `ms` INNER JOIN `highwaygroup`.`new_members_mailing_list` AS `ml` ON `ml`.`style_id` = `ms`.`style_id` INNER JOIN `highwaygroup`.`new_members` AS `m` ON `m`.`member_id` = `ml`.`member_id` LEFT JOIN `highwaygroup`.`websites` AS `w` ON `ms`.`site_id` = `w`.`id` WHERE `m`.`status` != 'not_active' AND `ml`.`active` = 'y' GROUP BY `ms`.`site_id`");
		while ($row = $DB->fetch_assoc($query))
		{
			$newsletter_site[$row['site_id']] = $row['count'];
			$sites_roots[$row['site_id']] = rtrim($row['url'], '/') . '/webadmin/';
		}
		$DB->free_result($query);
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
	<tr bgcolor="#00CCFF"><td nowrap="nowrap" align="center"><b><?php hecho(_("Newsletter Members")) ?></b></td></tr>
	<tr><td class="td_options" nowrap="nowrap"><ul style="padding:0 0 0 16px;margin:0;">
		<li><a href="https://www.arcade-game-sales.com/webadmin/newsletter/?action=member_list&amp;site_id=2" class="td_options" target="_blank">Arcade Game Sales (<?php echo intval($newsletter_site[2]) ?>/<?php echo intval($member_site[2]) ?>)</a></li>
<?php
		foreach(getWebsitesList() as $k => $v) if (isset($member_site[$k]))
		{
?>
		<li><a href="<?php echo (isset($sites_roots[$k]))?$sites_roots[$k] : "/webadmin/" ?>newsletter/?action=member_list&amp;site_id=<?php echo $k ?>" class="td_options" target="_blank"><?php hecho($v) ?> (<?php echo intval($newsletter_site[$k]) ?>/<?php echo intval($member_site[$k]) ?>)</a></li>
<?php
		}
?>
		<li><a href="/webadmin/newsletter/?action=list" class="td_options unfinished" target="_blank"><?php hecho(_("View Newsletters")) ?></a></li>
	</ul></td></tr>
</table><br />
<?php
	}
	else
	{
		$row = $DB->query_first("SELECT COUNT(DISTINCT `ml`.`member_id`) AS `count` FROM `highwaygroup`.`newsletter_styles` AS `ms` INNER JOIN `highwaygroup`.`new_members_mailing_list` AS `ml` ON `ml`.`style_id` = `ms`.`style_id` WHERE `ml`.`active` = 'y' AND `ms`.`site_id` = " . intval($_GLOBALS["site_settings"]["site_id"]) . " GROUP BY `ms`.`site_id`");

		if (in_array($_GLOBALS["site_settings"]["site_id"], array(1)))
		{
?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b><?php hecho(_("Newsletter")) ?></b></td></tr>
	<tr><td><ul class="blocklist">
		<!-- <li><a href="" class="unfinished"><?php hecho(_("Add Newsletter Member")) ?></a></li> -->
		<li><a href="/webadmin/newsletter/?action=build"><?php hecho(_("Create Newsletter")) ?></a></li>
		<li><a href="/webadmin/newsletter/?action=member_list"><?php hecho(_("Newsletter Members")) ?> (<?php echo intval($row["count"]) ?>/<?php echo count(memberSearch($_GLOBALS["criteria_options"]["member"])) ?>)</a></li>
		<li><a href="/webadmin/newsletter/?action=list"><?php hecho(_("View Newsletters")) ?></a></li>
	</ul></td></tr>
</table>
<?php
		}
		elseif (in_array($_GLOBALS["site_settings"]["site_id"], array(9, 16)))
		{
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
	<tr><td class="td_options" bgcolor="#000000"><div align="center"><b><?php hecho(_("Newsletter")) ?></b></div></td></tr>
	<tr><td class="td_options" nowrap="nowrap"><ul style="padding:0 0 0 16px;margin:0;">
		<!-- <li><a href="" class="unfinished td_options"><?php hecho(_("Add Newsletter Member")) ?></a></li> -->
		<li><a href="/webadmin/newsletter/?action=build" class="td_options"><?php hecho(_("Create Newsletter")) ?></a></li>
		<li><a href="/webadmin/newsletter/?action=member_list" class="td_options"><?php hecho(_("Newsletter Members")) ?> (<?php echo intval($row["count"]) ?>/<?php echo count(memberSearch($_GLOBALS["criteria_options"]["member"])) ?>)</a></li>
		<li><a href="/webadmin/newsletter/?action=list" class="td_options"><?php hecho(_("View Newsletters")) ?></a></li>
	</ul></td></tr>
</table><br />
<?php
		}
		else
		{
?>
<h3><?php hecho(_("Newsletter")) ?></h3>
<ul>
	<!-- <li><a href="" class="unfinished"><?php hecho(_("Add Newsletter Member")) ?></a></li> -->
	<li><a href="/webadmin/newsletter/?action=build"><?php hecho(_("Create Newsletter")) ?></a></li>
	<li><a href="/webadmin/newsletter/?action=member_list"><?php hecho(_("Newsletter Members")) ?> (<?php echo intval($row["count"]) ?>/<?php echo count(memberSearch($_GLOBALS["criteria_options"]["member"])) ?>)</a></li>
	<li><a href="/webadmin/newsletter/?action=list"><?php hecho(_("View Newsletters")) ?></a></li>
</ul>
<?php
		}
	}
}

file_put_contents($temp, ob_get_flush());
?>

<!-- /menu.inc -->

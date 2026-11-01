<!-- menu.inc -->

<?php
$temp = $_SERVER["DOCUMENT_ROOT"] . "/cache/webadmin/gift_coupon_menu.inc.php_" . md5("");
if (is_file($temp) && (filemtime($temp) + 3600) > $_SERVER["REQUEST_TIME"])
{
	readfile($temp);
	echo "<!-- #menu.inc -->\n";
	return;
}
ob_start();

if (moduleExists("gift_coupon"))
{
	if (in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43)))
	{
		$row = $DB->query_first("SELECT COUNT(`a`.`id`) FROM `highwaygroup`.`gift_coupon` AS `a` WHERE `a`.`active` = 'y' AND (`a`.`expiry_date` > NOW() OR `a`.`expiry_date` = '0000-00-00 00:00:00')", MYSQL_NUM);
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
	<tr bgcolor="#00CCFF"><td nowrap="nowrap" align="center"><b><?php hecho(_("Discount Coupons")) ?></b></td></tr>
	<tr><td class="td_options" nowrap="nowrap"><ul style="padding:0 0 0 16px;margin:0;">
		<li><a href="/webadmin/gift_coupon/" class="td_options">Discount Coupons (<?php echo $row[0] ?>)</a></li>
	</ul></td></tr>
</table><br />
<?php
	}
	else
	{
		$row = $DB->query_first("SELECT COUNT(`a`.`id`) FROM `highwaygroup`.`gift_coupon` AS `a` WHERE `a`.`active` = 'y' AND (`a`.`expiry_date` > NOW() OR `a`.`expiry_date` = '0000-00-00 00:00:00') AND (`a`.`site_id` = '' OR FIND_IN_SET(" . intval($_GLOBALS["site_settings"]["site_id"]) . ", `a`.`site_id`))", MYSQL_NUM);

		if (in_array($_GLOBALS["site_settings"]["site_id"], array(1)))
		{
?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b><?php hecho(_("Discount Coupons")) ?></b></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/gift_coupon/"><?php hecho(_("Discount Coupons")) ?> (<?php echo $row[0] ?>)</a></li>
	</ul></td></tr>
</table>
<?php
		}
		elseif (in_array($_GLOBALS["site_settings"]["site_id"], array(9, 16)))
		{
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
	<tr>
		<td class="td_options" bgcolor="#000000">
			<div align="center"><b><?php hecho(_("Discount Coupons")) ?></b></div>
		</td>
	</tr>
	<tr><td class="td_options" nowrap="nowrap"><ul style="padding:0 0 0 16px;margin:0;">
		<li><a href="/webadmin/gift_coupon/" class="td_options"><?php hecho(_("Discount Coupons")) ?> (<?php echo $row[0] ?>)</a></li>
	</ul></td></tr>
</table><br />
<?php
		}
		else
		{
?>
<h3><?php hecho(_("Discount Coupons")) ?></h3>
<ul>
	<li><a href="/webadmin/gift_coupon/"><?php hecho(_("Discount Coupons")) ?> (<?php echo $row[0] ?>)</a></li>
</ul>
<?php
		}
	}
}

file_put_contents($temp, ob_get_flush());
?>

<!-- /menu.inc -->

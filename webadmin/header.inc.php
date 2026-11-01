<?php
/**
 * Private header template
 *
 * Template for header and includes the left menu if one is needed
 */

/**
 * Include the common stuff
 *
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;
global $DB;

moduleLoad('permission');
moduleLoad('staff');

// @fixme Need to make this function
//staffUpdateLastDate($_GLOBALS['staff']['id']);

$allowed_sections = get_allowed_sections($_GLOBALS['staff']['user']);

$admin_root  = '/webadmin/';
$ADMIN_TITLE = 'Administration';

if (!headers_sent()) {
    header('Content-Type: text/html; charset=utf-8');
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Admin</title>
		<link rel="stylesheet" href="/webadmin/admin.css" type="text/css" />
		<link rel="stylesheet" href="/webadmin/css/favicons.css" type="text/css" />
		<link rel="stylesheet" href="/webadmin/css/flagicons.css" type="text/css" />
<?php
if (stristr($_SERVER['REQUEST_URI'], "new_s")) {
?>
		<link rel="stylesheet" href="/webadmin/new_suppliers/style.css" type="text/css" />
<?php
}
if (stristr($_SERVER['REQUEST_URI'], "products_new")) {
?>
		<link rel="stylesheet" href="/webadmin/css/select2.min.css" type="text/css" />
<?php
}
if (file_exists(dirname($_SERVER['SCRIPT_FILENAME']) . '/style.css')) {
?>
		<link rel="stylesheet" href="style.css" type="text/css" />
<?php
}
?>
		<style type="text/css">
/*<![CDATA[*/
body,p,td {font-family:Verdana, Arial, Helvetica, sans-serif;font-size:12px;}
.td_options {color:#fff;font-size:12px;}
a:hover {color:#f00;}
.top {border-top:1px solid #ccc;}
.left {border-left:1px solid #ccc;}
.right {border-right:1px solid #ccc;}
.centre {border-top:1px solid #ccc;border-bottom:1px solid #ccc;}
.bottom {border-bottom:1px solid #ccc;}
.bottomLeft {border-bottom:1px solid #ccc;border-left:1px solid #ccc;}
.bottomRight {border-bottom:1px solid #ccc;border-right:1px solid #ccc;}
.bottomLeftRight {border-bottom:1px solid #ccc;border-left:1px solid #ccc;border-right:1px solid #ccc;}
body {margin:0px;}
.box {border:1px solid #ccc;}
@media print
{
	.print_hide {display:none;}
}
.btn {border-width:1px;width:26px;height:24px;}
.btnDN {border-width:1px;width:26px;height:24px;border-style:inset;background-color:buttonhighlight;}
.btnNA {border-width:1px;width:26px;height:24px;}

.scellheader td {background-color:#000;color:#fff;/*height:33px;*/text-align:center;}
ul.blocklist {margin:0px 0px 8px 0px;padding-left:0;list-style-type:disc;list-style-position:inside;color:#fff;text-align:left;}
ul.blocklist li {white-space:nowrap;}
ul.blocklist a {color:#fff;}
ul.blocklist a:hover, ul.blocklist a:focus {color:#f00;}
/*]]>*/
		</style>
		<link rel="stylesheet" type="text/css" href="/webadmin/jquery-te/jquery-te-1.4.0.css" />
		<script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.1/js/all.min.js" integrity="sha256-IKPvaXm75eTeGvrsxwPh00y8Xjzqs203hTlQYydpLXI=" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
		<script type="text/javascript" src="/phpjs/*.js?<?=filemtime(__FILE__)?>"></script>
		<script type="text/javascript" src="/webadmin/jscript/common.php.js?<?=filemtime(__FILE__)?>" defer="defer"></script>
		<script type="text/javascript" src="/webadmin/jscript/tooltip.1.0.2.js?<?=filemtime(__FILE__)?>"></script>
		<script type="text/javascript" src="/webadmin/htmleditor/editor.js?<?=filemtime(__FILE__)?>"></script>
		<script type="text/javascript" src="/webadmin/jquery-te/jquery-te-1.4.0.min.js"></script>
<?php
if (stristr($_SERVER['REQUEST_URI'], "products_new")) {
?>
		<script type="text/javascript" src="/webadmin/jscript/select2.min.js"></script>
<?php
}
?>
		<script type="text/javascript">
//<![CDATA[
_editor_url = "/webadmin/htmleditor/";
function MM_openBrWindow(theURL,winName,features) {
	window.open(theURL,winName,features);
}
//]]>
		</script>
	</head>
	<body text="#000000" style="background:#fff;color:#000">

<!-- header.inc.php -->
<?php
if (!($_GLOBALS['popup_window'] ?? false)) {
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td style="width:15%;background-color:#000;font-weight:bold;color:#fff;font-size:14pt;text-align:center;" class="print_hide">Sections</td>
		<td style="width:85%;background-color:#ccc;"><table style="width:100%;"><tr>
			<td style="width:95%;font-weight:bold;font-size:18pt;text-align:center;">Administration</td>
			<td style="width:5%;text-align:right;white-space:nowrap;">
<?php
    //if(has_permission('SOFTUP', $allowed_sections))
    {
?>
				<input type="button" style="font-size:10px;width:32px;" value="Opt" onclick="window.open('/webadmin/staff/?action=options&amp;return_message=options_change');" />
				<input type="button" style="font-size:10px;width:32px;" value="SJ" onclick="window.open('/webadmin/softjobs/softjob_add.php?refer=true');" />
<?php
    }
?>
	</td>
		</tr></table></td>
	</tr>
	<tr>
		<td bgcolor="#000066" valign="top" align="center" class="print_hide">
			<a href="/webadmin/" class="td_options"><b><font size="3">Admin Home</font></b></a>
			<br />
			<br />
<?php

if (has_permission('JOBS', $allowed_sections)) {
?>
<table cellspacing="0" cellpadding="4" width="100%">
<?php
        $temp = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM `jobs_details` WHERE `receiver` = %s AND `date_completed` IS NULL",
            $DB->escape($_GLOBALS['staff']['user'])
        ), MYSQLI_ASSOC);
?>
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this)"><b><?=ucwords($_GLOBALS['staff']['name'])?>'s Jobs <font size="-2" color="#FF0000">(<?=$temp['total']?>)</font></b></td></tr>
	<tr><td>
		<ul class="blocklist">
		<li><a href="/webadmin/jobs_admin.php">Add/Edit Jobs</a></li>
<?php
        $temp = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM `jobs` WHERE `status` = 'open'"
        ), MYSQLI_ASSOC);
?>
		<li><a href="/webadmin/jobs_admin_all.php">All Jobs (<?=$temp['total']?>)</a></li>
	</ul></td></tr>
</table>
<?php
    }

    if (has_permission('NEWS', $allowed_sections))
    {
?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this)"><b>Web Pages</b></td></tr>
	<tr><td>
	<ul class="blocklist">
		<li><a href="/webadmin/news/?action=list">Add/Edit News</a></li>
		<li><a href="/webadmin/news_category_meta">News Category Meta</a></li>
		<li><a href="/webadmin/headertext/index.php">Top Header Text</a></li>
		<li><a href="/webadmin/front_top_menu/index.php">Front Page Top Menu</a></li>
	</ul></td></tr>
</table>

<?php
    }

    if ( has_permission('MEMBERS', $allowed_sections)) {
		include __DIR__ . '/member/menu.inc.php';
		
	}
	if ( has_permission('SUP', $allowed_sections) ){
		include __DIR__ . '/distributor/menu.inc.php';
	}
	include __DIR__ . '/socialmedia/menu.inc.php';
if (has_permission('COUPON', $allowed_sections)){
?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b>Discount Coupon</b></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/gift_coupon/preset.php?">Add New Coupon</a></li>
<?php
        $temp = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS total  FROM `highwaygroup`.`gift_coupon` where site_id=%d",
            $_GLOBALS["site_settings"]["site_id"]
        ), MYSQLI_ASSOC);
?>
		<li><a href="/webadmin/gift_coupon/list.php">All Coupons (<?=$temp['total'];?>)</a></li>
	</ul></td></tr>
</table>
<?php } 
if (has_permission('SUP', $allowed_sections)){
?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b>Contractor</b></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/contractor/?action=add">Add Contractor</a></li>
<?php
        $temp = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM `contractors`"
        ), MYSQLI_ASSOC);
?>
		<li><a href="/webadmin/contractor/?action=list">List All Contractor (<?=$temp['total']?>)</a></li>
	</ul></td></tr>
</table>
<?php
}
    if (has_permission('NEWSLETTER', $allowed_sections)) {
?>
<!-- <table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b>Emails/Mailing List</b></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/mailout/">Send Mailout</a><br /><br /></li>
		<li><a href="/webadmin/mailout/sent_items.php">Out Box/Sent Items</a></li>
		<li><a href="/webadmin/mailout/options_admin.php">Mailout Options<br /><br /></a></li>
		<li><a href="/webadmin/HE_mail.php">Old Email Sender</a></li>
		<li><a href="/webadmin/product_notify.php">Product Notify</a></li>
	</ul></td></tr>
</table> -->
<?php
        $temp = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM `mailinglist`"
        ), MYSQLI_ASSOC);

		if (moduleExists("newsletter") && moduleLoad("member"))
		{
			if (in_array($_GLOBALS["site_settings"]["site_id"], array(39)))
			{
				include __DIR__ . "/member/functions/query_member.inc.php";
				$criteria = $_GLOBALS['criteria_options']['member'];
				$criteria['status'] = array("active", "not_contacted");
				$member_site = memberGroupCount(memberSearch($criteria), 'site_id');
				$sites_roots = array();
				$newsletter_site = array();
				$query = $DB->query("SELECT `ms`.`site_id`, COUNT(`ml`.`member_id`) AS `count` FROM `highwaygroup`.`newsletter_styles` AS `ms` INNER JOIN `highwaygroup`.`new_members_mailing_list` AS `ml` ON `ml`.`style_id` = `ms`.`style_id` INNER JOIN `highwaygroup`.`new_members` AS `m` ON `m`.`member_id` = `ml`.`member_id` WHERE `ms`.`site_id`=39 AND `m`.`status` != 'not_active' AND `ml`.`active` = 'y'");
				while ($row = $DB->fetch_assoc($query))
				{
					$newsletter_site[$row['site_id']] = $row['count'];
					$sites_roots[$row['site_id']] = rtrim($row['url'], '/') . '/webadmin/';
				}
				$DB->free_result($query);
			}
		}
?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b>Newsletter Members</b></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/newsletter/?action=build" target="_blank">Create Newsletter</a></li>
		<?php
		foreach(getWebsitesList() as $k => $v) if (isset($member_site[$k]) && $k == '39')
		{
		?>
				<li><a href="<?php echo (isset($sites_roots[$k]))?$sites_roots[$k] : "/webadmin/" ?>newsletter/?action=member_list&amp;site_id=<?php echo $k ?>" class="td_options" target="_blank">Newsletter Members (<?php echo intval($newsletter_site[$k]) ?>/<?php echo intval($member_site[$k]) ?>)</a></li>
		<?php
				}
		?>
		<!-- <li><a href="/webadmin/mailinglist_admin.php?query=">Coinop Express (<?=$temp['total']?>)</a></li> -->
		<li><a href="/webadmin/newsletter/?action=list" target="_blank">View Newsletters</a></li>
	</ul></td></tr>
</table>
<?php
    }

    if (has_permission('OPTIONS', $allowed_sections)) {
?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b>Website Options</b></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/admin_currency.php">Update Currency</a></li>
		<li><a href="/webadmin/currency/">Currency Calculator</a></li>
		<li><a href="/webadmin/options">Site Options</a></li>
		<?php   if (has_permission('STATS', $allowed_sections)) { ?>
		<li><a href="/webadmin/awstats/awstats.pl?config=arcooda.com" target="_blank">Check Webstats</a></li>

		<li><a href="/webadmin/webalizer/" target="_blank">Check Webalizer Stats</a></li>
		<?php } ?>
		<li><a href="/webadmin/social/index.php">Social Media Links</a></li>
		<li><a href="/webadmin/seasongraphics/?action=list">Season Graphics</a></li>
		<li><a href="/webadmin/file-assets/?action=list">File Assets</a></li>
		<li><a href="/webadmin/options/new_site_options.php">New Site Options</a></li>
		<li><a href="/webadmin/options/terms_condition.php">Terms & Condition - Default</a></li>
		<li><a href="/webadmin/custom-terms-condition/?action=list">Terms & Condition - Custom</a></li>
		<li><a href="/webadmin/front_page_layout">Front Page Options</a></li>
		<li><a href="/webadmin/sitemeta/" target="_blank">Front Page Metatags</a></li>
		<li><a href="/webadmin/newsmeta/" target="_blank">News Page Metatags</a></li>
		<li><a href="/webadmin/invoice_address/" target="_blank">Invoice Addresses</a></li><br>
		<li><a href="/webadmin/notify/">Notification Options</a></li>
		<li><a href="/webadmin/orders/orderAdmin.php">Add/Edit Orders</a></li>
	</ul></td></tr>
</table>
<?php
    }

if (has_permission('OPTIONS', $allowed_sections) && moduleExists("page")) {
    include __DIR__ . "/page/menu.inc.php";
}

if (has_permission('BANNERS', $allowed_sections)) {
?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b>Banners</b></td></tr>
	<tr><td><ul class="blocklist">
	<li><a href="/webadmin/options/social_media_sharing.php">Social Media Sharing</a></li>
		<li><a href="/webadmin/banners">Add/Edit Banners</a></li>
		<li><a href="/webadmin/front_slider">Front Page Slider</a></li>
		<li><a href="/webadmin/product_slider">Product Page Slider</a></li>
		<li><a href="/webadmin/product_carousel">Product Carousel</a></li>

	</ul></td></tr>
</table>
<?php
    }
    if (has_permission('SOFTUP', $allowed_sections)) {

        $temp = $DB->query_first(sprintf(
            "SELECT `visited` FROM `software_visit` WHERE `user` = %s LIMIT 1",
			$DB->escape($_GLOBALS['staff']['user'])
        ), MYSQLI_ASSOC);
        $temp = ($DB->query_first(sprintf(
            "SELECT * FROM `software_updates` WHERE `up_date` > '{$temp['visited']}'"
        ), MYSQLI_ASSOC)) ? ' bgcolor="red"' : "";
?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"<?=$temp?>><b>Software Updates</b></td></tr>
	<tr><td<?=$temp?>><ul class="blocklist">
		<li><a href="/webadmin/soft_up/add.php">Add Software Update</a></li>
		<li><a href="/webadmin/soft_up/">View Software Updates</a></li>
	</ul></td></tr>
</table>
<?php
    }

    if(has_permission('ERROR', $allowed_sections))
    {
?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b>Software Jobs</b></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/softjobs/?action=add">Add New Job/Error</a></li>
<?php
        $temp = [
			1 => 0,
			2 => 0,
			3 => 0,
		];
        if ($res = $DB->query(sprintf(
            "SELECT COUNT(*) AS `total`, `status` FROM `highwaygroup`.`bugs` WHERE `website` = %d GROUP BY `status`",
			$_GLOBALS['site_settings']['site_id']
        ))) {
			while ($row = $DB->fetch_assoc($res)) {
				$temp[$row['status']] = $row['total'];
			}
			$DB->free_result($res);
        }
?>
		<li><a href="/webadmin/softjobs/?status=1">Open Jobs/Errors (<?=intval($temp[1])?>)</a></li>
		<li><a href="/webadmin/softjobs/?status=2">Fixing Jobs/Errors (<?=intval($temp[2])?>)</a></li>
		<li><a href="/webadmin/softjobs/?status=3">Closed Jobs/Errors (<?=intval($temp[3])?>)</a><br /><br /></li>
<?php
        $temp = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM `highwaygroup`.`bugs` WHERE `status` <> 3 AND `website` = %d AND `assigned` = %d",
			$_GLOBALS['site_settings']['site_id'],
            $_GLOBALS['staff']['id']
        ), MYSQLI_ASSOC);
?>
		<li><a href="/webadmin/softjobs/?status=1|2&amp;assigned=<?=$_GLOBALS['staff']['id']?>">Assigned Jobs (<?=$temp['total']?>)</a></li>
<?php
        $temp = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM `highwaygroup`.`bugs` WHERE `status` <> 3 AND `website` = %d AND `reporter` = %d",
			$_GLOBALS['site_settings']['site_id'],
            $_GLOBALS['staff']['id']
        ), MYSQLI_ASSOC);
        if ($temp['total'] > 0) {
?>
		<li><a href="/webadmin/softjobs/?status=1|2&amp;reporter=<?=$_GLOBALS['staff']['id']?>">Reported Jobs (<?=intval($temp['total'])?>)</a></li>
<?php
        }
?>
	</ul></td></tr>
</table>
<?php
    }

    if (has_permission('PRODUCT', $allowed_sections)) {
        include __DIR__ . '/products_new/menu.inc.php';
    }
?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="unfinished" ondblclick="collapse(this);"><b>Custom Art Work</b></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="" class="unfinished">Orders(0)</a></li>
	</ul></td></tr>
</table>
<?php  if (has_permission('USERS', $allowed_sections)) { ?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="unfinished" ondblclick="collapse(this);"><b>Permissions</b></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/permissions/permissions_options.php">Add/Edit Permissions</a></li>
	</ul></td></tr>
</table>
<?php } ?>
<?php  if (has_permission('WSP', $allowed_sections)) { ?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b>Weekly Specials (NEW)</b></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/weeklyspecials/?action=add">Add Weekly Special</a></li>
		<li><a href="/webadmin/weeklyspecials/?action=list">List Weekly Specials</a></li>
	</ul></td></tr>
</table>
<?php } ?>
<?php

    if (has_permission('PCONFIG', $allowed_sections)) {
?>

<?php

    if (has_permission('PROBUILDER', $allowed_sections)) {
        $option_total = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM `pb_options`"
        ), MYSQLI_ASSOC);
        $builder_total = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM `pb_builders`"
        ), MYSQLI_ASSOC);
         $category_total = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM `pb_machines_category`"
        ), MYSQLI_ASSOC);
?>
	<table cellspacing="0" cellpadding="4" width="100%">
		<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b>Product Builder</b></td></tr>
		<tr><td><ul class="blocklist">
			<li><a href="/webadmin/product_builder/?action=add_builder">Add New Builder</a></li>
			<li><a href="/webadmin/product_builder">View Builders(<?= $builder_total['total']?>)</a><br /><br /></li>
			<li><a href="/webadmin/product_builder/?action=add_option">Add New Option</a></li>
			<li><a href="/webadmin/product_builder/?action=view_option">View Options(<?= $option_total['total']?>)</a></li><br/>

			<li><a href="/webadmin/product_builder/?action=view_category">View Category(<?= $category_total['total']?>)</a><br /><br /></li>
		</ul></td></tr>
	</table>
<?php
    }
    if (has_permission('PROBUILDER', $allowed_sections)) {
    	include __DIR__ . "/product_builder_new/menu.inc.php";
?>
<?php } ?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b>Product Configuration</b></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/color/">Modify Color Options</a></li>
		<li><a href="/webadmin/generic_admin.php?table=product_style">Modify Styles</a></li>
		<li><a href="/webadmin/generic_admin.php?table=year_released">Modify Years</a></li>
		<li><a href="/webadmin/products/attachment_admin.php">Modify Manuals</a></li>
		<li><a href="/webadmin/generic_admin.php?table=product_country_location">Modify Country/Locations</a></li>
		<li><a href="/webadmin/generic_admin.php?table=product_storage_location">Modify Storage Locations</a></li>
		<li><a href="/webadmin/generic_admin.php?table=product_availability">Modify Availability</a></li>
		<!-- <li><a href="/webadmin/suppliers_admin.php">Modify Suppliers</a></li> -->
		<li><a href="/webadmin/generic_admin.php?table=manufacturers">Modify Manufacturers</a></li>
		<li><a href="/webadmin/products/screenshot_admin.php?table=product_screenshots">Modify Screenshots</a></li>
		<li><a href="/webadmin/products/sections_admin.php?table=product_types">Modify Product Types</a></li>
		<li><a href="/webadmin/labels/">Print Product Labels</a></li>
		<li><a href="/webadmin/icon/">Add/Edit Icons</a></li>
		<li><a href="/webadmin/brand/">Arcooda Brands</a></li>
	</ul></td></tr>
</table>
<?php
    }

    if (has_permission('REPORT', $allowed_sections) || has_permission('REPORTADM', $allowed_sections)) {
?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b>Weekly Report</b></td></tr>
	<tr><td><ul class="blocklist">
<?php
        if (has_permission('REPORT', $allowed_sections)) {
            if ($res = $DB->query(sprintf(
                "SELECT * FROM `highwaygroup`.`weekly_report_websites` WHERE `visible_from` = %d",
				$_GLOBALS['site_settings']['site_id']
            ))) {
				while ($row = $DB->fetch_assoc($res)) {
?>
		<li><a href="/webadmin/weeklyreport/weekly_report.php?action=add&amp;from=<?=$row['from']?>&amp;to=<?=$row['to']?>" target="_blank">Add New COE Report</a></li>
		<li><a href="/webadmin/weeklyreport/weekly_report.php?from=<?=$row['from']?>&amp;to=<?=$row['to']?>" target="_blank">View My COE Reports</a></li>
<?php
				}
				$DB->free_result($res);
			}
        }
        if (has_permission('REPORTADM', $allowed_sections)) {
            if ($res = $DB->query(sprintf(
                "SELECT * FROM `highwaygroup`.`weekly_report_websites` WHERE `visible_from` = %d",
				$_GLOBALS['site_settings']['site_id']
            ))) {
				while ($row = $DB->fetch_assoc($res)) {
?>
		<li><a href="/webadmin/weeklyreport/weekly_report_admin.php?from=<?=$row['from']?>&amp;to=<?=$row['to']?>" target="_blank">View All COE Reports</a></li>
<?php
				}
				$DB->free_result($res);
			}
        }
?>
	</ul></td></tr>
</table>
<?php
    }

    if (has_permission('FREIGHT', $allowed_sections)) {
?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b>Freight</b></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/freight/cargo/aircargo_admin.php?action=mainpage">Aircargo</a></li>
		<li><a href="https://www.josifovski.com/freight2/?site_id=3">Courier</a></li>
		<li><a href="/webadmin/freight/cargo/seacargo_admin.php?action=mainpage">Seacargo</a></li>
		<li><a href="/webadmin/freight_forwarders_/forwarders_admin.php?display_search_box=true">Freight Forwarders</a></li>
		<li><a href="/webadmin/freight3/" class="unfinished" style="color:#000;">New Freight Services</a></li>
		<li><a href="/webadmin/freight3/?action=international" class="unfinished" style="color:#000;">New International Calculator</a></li>
		<li><a href="/webadmin/freight3/?action=domestic" class="unfinished" style="color:#000;">New Domestic Calculator</a></li>
		<li><a href="/webadmin/freight_tracking">Freight Tracking</a></li>
		<li><a href="/webadmin/freight_tracking/shipments.php">Open Shipments</a></li>
	</ul></td></tr>
</table>
<?php
    }

    if (has_permission('BACKORDER', $allowed_sections) && ('y' == $_GLOBALS['staff']['developer'])) {
?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b>Back Orders</b></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/backorders/backOrders.php?view=open">Add/Edit Back Orders</a></li>
	</ul></td></tr>
</table>
<?php
    }

    if (has_permission('MYDETAIL', $allowed_sections)) {
?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b>My Details</b></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/staff_admin.php?action=edit&amp;id=<?=$_GLOBALS['staff']['id']?>" target="_blank">My Details</a></li>
		<li><a href="/webadmin/staff_admin.php" target="_blank">Other Staff</a></li>
	</ul></td></tr>
</table>
<?php
    }

    if (has_permission('SUP', $allowed_sections) && ('y' == $_GLOBALS['staff']['developer'])) {
        include __DIR__ . "/new_suppliers/functions/menu.inc.php";
?>
<?php
        $counts = menuCounters("new_");
?>
<?php
    }

    if (has_permission('WORLD', $allowed_sections)) {
?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b>World Summary</b></td></tr>
	<tr><td><ul class="blocklist">
		<li><a href="/webadmin/world/">List Countries</a></li>
		<li><a href="/webadmin/world/world_search.php?">Search Countries</a></li>
		<li><a href="/webadmin/world/seaport/seaports.php?action=view">List Seaport</a></li>
	</ul></td></tr>
</table>
<?php
    }

    if (has_permission('WORK_MAN', $allowed_sections)) {
?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b>Work Manuals</b></td></tr>
	<tr><td><ul class="blocklist">
<?php
        if (has_permission('WORK_MAN_W', $allowed_sections)) {
?>
		<li><a href="/webadmin/workmanual/?action=add">Add Manual</a></li>
<?php
        }
?>
		<li><a href="/webadmin/workmanual/">View All Manuals</a></li>
	</ul></td></tr>
</table>
<?php
    }

    if(has_permission('PROJECT', $allowed_sections))
    {
?>
<table cellspacing="0" cellpadding="4" width="100%">
<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b>Projects</b></td></tr>
	<tr><td><ul class="blocklist">
<?php
        if (has_permission('WORK_MAN_W', $allowed_sections)) {
?>
		<li><a href="/webadmin/project/?action=add">Add Project</a></li>
<?php
        }
?>
		<li><a href="/webadmin/project/">View All Projects</a></li>
	</ul></td></tr>
</table>
<?php
    }
	if (has_permission('INVOICES', $allowed_sections)) {
		include __DIR__ . "/invoice/menu.inc.php";
	}
	if (has_permission('TAX', $allowed_sections)) { ?>
		<table width="100%" border="0" cellspacing="0" cellpadding="2">
			<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b> Tax Report</b></td></tr>
			<tr>
				<td class="td_options" nowrap="nowrap">
					<ul style="padding:0px 0px 0px 16px;margin:0px;">
						<li><a class="td_options" target="_blank" href="/webadmin/invoice/?action=tax_month">Monthly Tax Report</a></li>
						<li><a class="td_options" target="_blank" href="/webadmin/invoice/?action=tax_year">Yearly Tax Report</a></li>
						<li><a class="td_options" target="_blank" href="/webadmin/invoice/?action=tax_payment_year">Payment Method Report</a></li>
						<li><a class="td_options" target="_blank" href="/webadmin/invoice/?action=tax_report">Report Printing</a></li>
						<li><a class="td_options" target="_blank" href="/webadmin/invoice/?action=tax_supplier">Supplier History</a></li>
					</ul>
				</td>
			</tr>
		</table>
		<br /><br />
	<?php }	
    if (has_permission('SUP', $allowed_sections)) {
        $temp_sup = $DB->query_first(sprintf(
            "SELECT COUNT(`supplier_id`) AS `total` FROM `new_suppliers`"
        ), MYSQLI_ASSOC);
?>

<table width="100%" border="0" cellspacing="0" cellpadding="2">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b> Suppliers</b></td></tr>
	<tr><td class="td_options" nowrap="nowrap"><ul style="padding:0px 0px 0px 16px;margin:0px;">
		<li><a  class="td_options" href="/webadmin/supplier_new/?action=add"  target="_blank">Add Supplier</a></li>
		<li><a class="td_options" href="/webadmin/supplier_new/?action=list">List All Suppliers (<?=$temp_sup['total']?>)</a></li>
		<li><a  class="td_options" href="/webadmin/supplier_new/?action=search" >Search Suppliers</a></li>
	</ul></td></tr>
</table>
<br /><br />
<?php
    }

if (has_permission('PO', $allowed_sections)) {
        $temp = [
            'working'   => 0,
            'supplier'   => 0,
            'order'    => 0,
            'cancelled'     => 0,
            'order-working'  => 0,
            'supplier-waiting' => 0,
            'payment' => 0,
            'goods'    => 0,
            'checking-goods' => 0,
            'processed' => 0,
			'awaiting-order'   => 0,
			'shipping'   => 0
        ];
		
        $res = $DB->query(sprintf(
            "SELECT `order_action`, COUNT(*) AS `total` FROM `purchase_order` WHERE `site_id` = '39' GROUP BY `order_action`"
        ));
        while ($row = $DB->fetch_assoc($res)) {
            $temp[$row['order_action']] += $row['total'];
        }
        $DB->free_result($res);
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
	<tr bgcolor="#FFFF00"><td nowrap="nowrap" align="center" class="collapseable"><b>Purchase Orders New</b></td></tr>
	<tr><td class="unfinished" nowrap="nowrap"><ul style="padding:0px 0px 0px 16px;margin:0px;">
		<li><a class="unfinished" href="/webadmin/purchase_order/?action=add&amp;status=sales">Add New Purchase Order</a></li>
		<li><a class="unfinished" href="/webadmin/purchase_order/?action=list&amp;status=proforma|sales|return|enquiry">List Open Purchase Orders (<?=intval( $temp['order'] + $temp['working']+ $temp['awaiting-order']+ $temp['shipping']+ $temp['goods']+$temp['checking-goods']+$temp['sales'])?>)</a></li>
		<li><a class="unfinished" href="/webadmin/purchase_order/?action=list">List All Purchase Order(<?=array_sum($temp)?>)</a></li>
		<br />
		<!-- <li><a class="unfinished" href="/webadmin/purchase_order/?action=list&amp;status=proforma|sales|processed|cancelled|return|completed">List All Purchase Orders (0)</a></li> -->
		<li><a class="unfinished" href="/webadmin/purchase_order/invoice_options.php">Purchase Order Options</a></li>
		<li><a class="unfinished" href="/webadmin/purchase_order/?action=search">Purchase Order Search</a></li>
		
	</ul></td></tr>
</table>
<br /><br />
<?php
    }
    if (has_permission('PROFIT', $allowed_sections)) {
        include __DIR__ . "/profits/menu.inc.php";
    }
	if (has_permission('PROFIT', $allowed_sections)) { ?>
		<table width="100%" border="0" cellspacing="0" cellpadding="2">
			<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b> Page Views</b></td></tr>
			<tr>
				<td class="td_options" nowrap="nowrap">
					<ul style="padding:0px 0px 0px 16px;margin:0px;">
						<li>
							<a href="#" class="td_options" onclick="toggleSection('menu_profit_bestviewsproducts');return false">Most Viewed Products</a>
						</li>
						<li style="display:none;list-style:none;" name="menu_profit_bestviewsproducts">
							<ul>
								<li><a class="td_options" href="/webadmin/profits/?action=products_views&amp;sale_type=weekly">Weekly Views</a></li>
								<li><a class="td_options" href="/webadmin/profits/?action=products_views&amp;sale_type=monthly">Monthly Views</a></li>
								<li><a class="td_options" href="/webadmin/profits/?action=products_views&amp;sale_type=quarterly">Quarterly Views</a></li>
								<li><a class="td_options" href="/webadmin/profits/?action=products_views">Yearly Views</a></li>
							</ul>
						</li>
					</ul>
				</td>
			</tr>
			<tr>
				<td class="td_options" nowrap="nowrap">
					<ul style="padding:0px 0px 0px 16px;margin:0px;">
						<li>
							<a href="#" class="td_options" onclick="toggleSection('menu_profit_worstviewsproducts');return false">Least Viewed Products</a>
						</li>
						<li style="display:none;list-style:none;" name="menu_profit_worstviewsproducts">
							<ul>
								<li><a class="td_options" href="/webadmin/profits/?action=products_views&amp;type=least&amp;sale_type=weekly">Weekly Views</a></li>
								<li><a class="td_options" href="/webadmin/profits/?action=products_views&amp;type=least&amp;sale_type=monthly">Monthly Views</a></li>
								<li><a class="td_options" href="/webadmin/profits/?action=products_views&amp;type=least&amp;sale_type=quarterly">Quarterly Views</a></li>
								<li><a class="td_options" href="/webadmin/profits/?action=products_views&amp;type=least">Yearly Views</a></li>
							</ul>
						</li>
					</ul>
				</td>
			</tr>
		</table>
		<br /><br />
	<?php }	
    if (has_permission('METATAGS', $allowed_sections)) {
        include __DIR__ . "/metatags/menu.inc.php";
    }
    if (has_permission('360VIEW', $allowed_sections) ) {
		include __DIR__ . "/product_360/menu.inc.php";
	}
	if (has_permission('SNUMBER', $allowed_sections) ) {
    include __DIR__ . "/serial/menu.inc.php";
	}
	if (has_permission('OSECTION', $allowed_sections) ) {
    	include __DIR__ . "/owner/menu.inc.php";
    }
    if (has_permission('OSECTION', $allowed_sections) ) { ?>
    	<table width="100%" border="0" cellspacing="0" cellpadding="2">
			<tr class="scellheader"><td nowrap="nowrap" align="center" class="collapseable"><b>FAQ Section</b></td></tr>
			<tr><td class="td_options" nowrap="nowrap"><ul style="padding:0px 0px 0px 16px;margin:0px;">
				<li><a class="td_options unfinished" style="color:black;"  href="<?=htmlspecialchars($stub)?>/faq/faq.php"><?= htmlspecialchars(_("FAQ")) ?></a></li>
				<li><a class="td_options unfinished" style="color:black;"  href="<?=htmlspecialchars($stub)?>/faq/faqcategory.php"><?= htmlspecialchars(_("FAQ Category")) ?></a></li>
			</ul></td></tr>
		</table>
   <?php  }
	include __DIR__ . "/review/menu.inc.php";
    if (has_permission('SEOS', $allowed_sections) ) {
?>

	<table width="100%" border="0" cellspacing="0" cellpadding="2">
		<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b> SEO Studio</b></td></tr>
		<tr><td class="td_options" nowrap="nowrap"><ul style="padding:0px 0px 0px 16px;margin:0px;">
			<li><a  class="td_options" href="/webadmin/seostudio/admin"  target="_blank">SEO Admin</a></li>
			<li><a class="td_options" href="/webadmin/seostudio/tools.php" target="_blank">View Tools Page</a></li>
			<li>
				<a href="#" class="td_options" onclick="toggleSection('displaytools');return false">View Tools Links</a>
				<ul id="displaytools" style="display: none;">
					<li><a  class="td_options" onclick="toggleSection('displaySearchEngines');return false" style="font-style:italic;">Search Engines</a>
						<ul id="displaySearchEngines">
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=google-serp" target="_blank">Google SERP</a></li>
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=bing-serp" target="_blank">Bing SERP</a></li>
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=top-search-queries" target="_blank">Top Search Queries</a></li>
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=indexed-pages" target="_blank">Indexed Pages</a></li>
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=robots-txt" target="_blank">Robots.txt</a></li>
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=sitemap" target="_blank">Sitemap</a></li>
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=submit-sitemap" target="_blank">Submit Sitemaps</a></li>
						</ul>

					</li>
					<li><a  class="td_options" onclick="toggleSection('displayBacklinks');return false" style="font-style:italic;">Backlinks</a>
						<ul id="displayBacklinks">
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=high-quality-backlinks" target="_blank">High Quality Backlinks</a></li>
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=new-backlinks" target="_blank">New Backlinks</a></li>
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=poor-backlinks" target="_blank">Poor Backlinks</a></li>
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=top-referrers" target="_blank">Top Referrers</a></li>
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=page-authority" target="_blank">Check Page Authority</a></li>
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=domain-authority" target="_blank">Check Domain Authority</a></li>
						</ul>
					</li>
					<li><a  class="td_options" onclick="toggleSection('displayTest');return false" style="font-style:italic;">Test</a>
						<ul id="displayTest">
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=crawlability" target="_blank">Crawlability Test</a></li>
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=mobile-support" target="_blank">Mobile Support Test</a></li>
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=headers" target="_blank">Headers</a></li>
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=speed-test" target="_blank">Speed Test</a></li>
						</ul>
					</li>
					<li><a  class="td_options" onclick="toggleSection('displayContent');return false" style="font-style:italic;">Content</a>
						<ul id="displayContent">
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=link-analysis" target="_blank">Link Analysis</a></li>
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=keyword-density" target="_blank">Keyword Density</a></li>
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=extract-meta-tags" target="_blank">Extract Meta Tags</a></li>
						</ul>
					</li>
					<li><a  class="td_options" onclick="toggleSection('displayResearch');return false" style="font-style:italic;">Research</a>
						<ul id="displayResearch">
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=keyword-research" target="_blank">Keyword Research</a></li>
							<li><a class="td_options" href="/webadmin/seostudio/tool.php?id=competition" target="_blank">Competition</a></li>
						</ul>
					</li>
				</ul>
			</li>
			<li><a  class="td_options" href="/webadmin/seostudio/Documentation"  target="_blank">Documentation</a></li>
		</ul></td></tr>
	</table>
<?php }?> 
	<br /><br />
	<?php
	$total_avatar = $DB->query_first(sprintf(
			"SELECT COUNT(*) AS total_count FROM `highwaygroup`.`new_members` WHERE `avatar_image` IS NOT NULL"
		), MYSQLI_ASSOC);
	$total_survey = $DB->query_first(sprintf(
			"SELECT COUNT(*) AS total_count FROM `highwaygroup`.`survey` WHERE `site_id`=".$_GLOBALS['site_settings']['site_id']
		), MYSQLI_ASSOC);
		?>
			<table width="100%" border="0" cellspacing="0" cellpadding="2">
				<tr class="scellheader"><td nowrap="nowrap" align="center" class="collapseable"><b>Avatars</b></td></tr>
				<tr><td class="td_options" nowrap="nowrap"><ul style="padding:0px 0px 0px 16px;margin:0px;">
					<li><a class="td_options"  href="/webadmin/avatar/for_upload/svgavatars.php">Make Avatar</a></li>
					<li><a class="td_options" href="/webadmin/avatar//list.php?action=list">Member Avatars (<?=$total_survey['total_count']?>)</a></li>
				</ul></td></tr>
			</table>
	<br /><br />
			<table width="100%" border="0" cellspacing="0" cellpadding="2">
				<tr class="scellheader"><td nowrap="nowrap" align="center" class="collapseable"><b>Survey</b></td></tr>
				<tr><td class="td_options" nowrap="nowrap"><ul style="padding:0px 0px 0px 16px;margin:0px;">
					<li><a class="td_options"  href="/webadmin/survey/index.php?action=add_survey">Add New Survey</a></li>
					<li><a class="td_options" href="/webadmin/survey/index.php?action=view_survey">View Survey (<?=$total_survey['total_count']?>)</a></li>
				</ul></td></tr>
			</table>
	<br /><br />
		<table width="100%" border="0" cellspacing="0" cellpadding="2">
			<tr class="scellheader"><td nowrap="nowrap" align="center" class="collapseable"><b>Container Shipping</b></td></tr>
			<tr>
				<td class="td_options" nowrap="nowrap">
					<ul style="padding:0px 0px 0px 16px;margin:0px;">
						<li><a style="color:black;" class="td_options unfinished" href="">Add New Container</a></li>
						<li><a style="color:black;" class="td_options unfinished" href="">Open Containers</a></li>
						<li><a style="color:black;" class="td_options unfinished" href="">View All Containers (  )</a></li>
					</ul>
				</td>
			</tr>
		</table>
		<br /><br />
		<table width="100%" border="0" cellspacing="0" cellpadding="2">
			<?php
				$total_production = $DB->query_first(sprintf(
					"SELECT COUNT(*) AS total_count FROM `production_schedules`"
				), MYSQLI_ASSOC);
			?>
			<tr class="scellheader"><td nowrap="nowrap" align="center" class="collapseable"><b>Production Schedule</b></td></tr>
			<tr>
				<td class="td_options" nowrap="nowrap">
					<ul style="padding:0px 0px 0px 16px;margin:0px;">
						<li><a class="td_options" href="/webadmin/production_schedules/index.php">Items in Production(<?= $total_production['total_count'] ?>)</a></li>
					</ul>
				</td>
			</tr>
		</table>
	<br /><br />
	<br /><br />
			</td>
			<td width="85%" valign="top">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr><td><table width="100%%" border="0" cellspacing="0" cellpadding="5"><tr>
						<td width="1%" bgcolor="#FFFFCC"><strong>Welcome </strong></td>
						<td width="99%" bgcolor="#FFFFCC"><?=htmlspecialchars($_GLOBALS['staff']['name'])?></td>
					</tr></table></td></tr>
					<tr>
						<td height="1" bgcolor="#003399"></td>
					</tr>
				</table>
<?php
}
?>

<!-- /header.inc.php -->

<?php
include_once ('common.inc.php');
include_once ('templates/header.tpl.php');

$order_by = 'date_joined';
$order = 'desc';
if ($posted['last_initial'] != '')
{
	$order_by = 'last_name';
	$order = 'asc';
}
if ($posted['order_by'] != '') $order_by = strtolower($posted['order_by']);
if ($posted['order'] != '') $order = strtolower($posted['order']);
if ($order != 'asc') $order = 'desc';
$page_id = intval($posted['page_id']);

$criteria = $_GLOBALS['criteria_options']['member'];

$site_id=$posted['site_id'];
if(!in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) $site_id=$_GLOBALS['site_settings']['site_id'];
$query = $DB->query("SELECT ml.member_id FROM highwaygroup.newsletter_styles ms INNER JOIN highwaygroup.new_members_mailing_list ml ON ml.style_id=ms.style_id INNER JOIN highwaygroup.new_members m ON m.member_id=ml.member_id WHERE ml.active='y'" . (($site_id!=0)?(' AND ms.site_id='.intval($site_id)):''));
while($row = $DB->fetch_assoc($query)) $criteria['member_id'][]=$row['member_id'];
$DB->free_result($query);
unset($criteria['site_id']);
unset($criteria['active_id']);

foreach(memberGetOptionList('all_fields') as $k=>$v) if($posted[$k]) $criteria[$k]=((is_array($posted[$k]))?$posted[$k]:explode('|', $posted[$k]));
if ($posted['search_for'] != '') $criteria['any_text'] = explode(' ', $posted['search_for']);
if ($posted['site_id'] != '') $criteria['site_id'] = explode('|', $posted['site_id']);

$member_list = memberSearch($criteria);
if(count($member_list))
{
	$query = $DB->query("SELECT member_id FROM highwaygroup.new_members_mailing_list WHERE active='y' AND member_id IN(" . implode(", ",$member_list) . ")");
	while($row = $DB->fetch_assoc($query)) $criteria['member_id'][] = $row['member_id'];
	$DB->free_result($query);
}

?>
<?php echo formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionSearchMembers\')">Search Members</a>') ?>
<div id="sectionSearchMembers">
<?php
include ('templates/search_members.tpl.php');
?>
</div>
<div id="sectionSearchMembers_hideNote"></div>
<?php

/*
//Show By Country
$sub_criteria = $criteria;
unset($sub_criteria['country']);
$member_list=memberGroupCount(memberSearch($sub_criteria),'country');
print_r($member_list);
*/

//Find and pagenate the members
$items_per_page = 50;
//if ($_GLOBALS['staff']['members_per_page'] > 0) $items_per_page = $_GLOBALS['staff']['members_per_page'];
$member_list = memberSort(memberSearch($criteria), $order_by);
if ($order == 'desc') $member_list = array_reverse($member_list);

$count_total = count($member_list);
$member_list = array_chunk($member_list, $items_per_page);
$count_pages = count($member_list);
$page_id = max(0, min($count_pages - 1, $page_id));
$member_list = $member_list[$page_id];
$count_display = count($member_list);

$add_url = $get_vars;
$add_url['action'] = 'add_member';
$add_url = 'index.php?' . http_build_query($add_url);
?>
<?php echo formatHTMLTitle('Members', '<i><a style="color:black;" href="' . formatHTMLSafe($add_url) . '">Add New Member</a></i>') ?>
<?php
if ($count_total > 0)
{
	$page_url = $get_vars;
	unset($page_url['page_id']);
	$page_url = '?' . http_build_query($page_url);
	$page_links = formatHTMLPageLinks($page_url . '&page_id=', $page_id, $count_display, $count_total, $items_per_page);

	$select_url = $get_vars;
	$select_url['action'] = 'select_member';
	$select_url = 'index.php?' . http_build_query($select_url);
?>
<br />
<form action="<?php echo formatHTMLSafe($select_url) ?>" method="post">
<?php
	include 'templates/list_members.tpl.php';

	$initial_url = $get_vars;
	unset($initial_url['page_id']);
	unset($initial_url['last_initial']);
	$initial_url = '?' . http_build_query($initial_url) . '&last_initial=';
?>
</form>
<div class="wide" style="background:#fff;border:1px solid #ddd;">
	<b>By Last Name :</b>
<?php
	for ($i = 'a'; $i != 'z'; $i++) if ($posted['last_initial'] == $i)
	{
?>
		<span style="background-color:#7fc;"><?php echo strtoupper($i) ?></span>
<?php
	}
	else
	{
?>
		<a href="<?php echo $initial_url . $i ?>"><?php echo strtoupper($i) ?></a>
<?php
	}
?>
	<br />
	<?php echo $page_links ?>
</div>
<br />
<?php
	if ($popup_window)
	{
?>
<input type="button" onclick="window.close();" value="Close Window" />
<?php
	}
}
else
{
?>
No results were found
<?php
}
include_once ('templates/footer.tpl.php');
?>
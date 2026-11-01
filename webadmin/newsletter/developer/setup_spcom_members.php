<?php
include_once 'common.inc.php';
//header('Content-Type: text/plain');
ob_start();
$site_id = 20;
$style_id = 7;
$new_members = array();
$enable_members = array();
$reenable_members = array();
$disable_members = array();

/**
 * Find all the newsletter members in the old lists
 */
$query = $DB->query("SELECT m1.* FROM highwaygroup.new_members m1 WHERE m1.email_address!='' AND FIND_IN_SET(" . $style_id . ",mailing_id)");
while ($row = $DB->fetch_assoc($query))
{
	if (is_null($row['member_id'])) $new_members[] = $row;
	else $enable_members[$row['member_id']] = $row['member_id'];
}
$DB->free_result($query);

/**
 * Check against the blocked list
 */

/**
 * Check if each member is already active
 */
$query = $DB->query("SELECT member_id,active FROM highwaygroup.new_members_mailing_list WHERE style_id=" . $style_id);
while ($row = $DB->fetch_assoc($query))
{
	if ($row['active'] == 'n') $reenable_members[$row['member_id']] = $row['member_id'];
	unset($enable_members[$row['member_id']]);
}
$DB->free_result($query);

/**
 * Build some resulting commands
 */
if (count($reenable_members)) echo "UPDATE new_members_mailing_list SET active='y', date_joined=NOW() WHERE style_id=" . $style_id . " AND member_id IN(" . implode(", ", $reenable_members) . ");\n";
if (count($enable_members))
{
	$rows = array();
	foreach($enable_members as $member_id) $rows[] = "(" . $member_id . "," . $style_id . ",'y',NOW())";
	echo "INSERT INTO new_members_mailing_list (member_id,style_id,active,date_joined) VALUES\n";
	echo implode(", ", $rows) . ";\n";
}
if (count($disable_members))
{
	$query = $DB->query("SELECT member_id FROM new_members_mailing_list WHERE active='y' AND style_id=" . $style_id . " AND member_id IN(" . implode(', ', $disable_members) . ")");
	$disable_members = array();
	while ($row = $DB->fetch_assoc($query)) $disable_members[$row['member_id']] = $row['member_id'];
}
if (count($disable_members))
{
	echo "UPDATE new_members_mailing_list SET active='n', date_cancelled=NOW() WHERE style_id=" . $style_id . " AND member_id IN(" . implode(',', $disable_members) . ");\n";
}
if (count($new_members))
{
	$rows = array();
	foreach($new_members as $member)
	{
		$names = preg_split('/[| ]/', $member['name']);
		$fname = array_shift($names);
		$lname = implode(' ', $names);
		if ($member['email'] != '') $rows[] = "(" . formatSQLString($member['company']) . ", " . formatSQLString($fname) . ", " . formatSQLString($lname) . ", " . formatSQLString($member['country']) . ", " . formatSQLString($member['ip']) . ", " . formatSQLString($member['email']) . ", " . $site_id . ")";
		else $rows[] = "(" . formatSQLString($member['company']) . ", " . formatSQLString($fname) . ", " . formatSQLString($lname) . ", " . formatSQLString($member['country']) . ", " . formatSQLString($member['ip']) . ", " . formatSQLString($member['email_address']) . ", " . $site_id . ")";
	}
	echo "INSERT INTO new_members (company_name, first_name, last_name, country, signup_IP, email_address, site_id) VALUES\n";
	echo implode(",\n", $rows) . ";\n";
	echo "#Please refresh this page after executing these sql queries\n";
}
echo '<tt style="white-space:pre-line;">' . htmlspecialchars(ob_get_clean()) . '</tt>';
?>
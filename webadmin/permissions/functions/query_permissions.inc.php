<?php
function permissionSections($site_id = 0, $admin = false)
{
	global $DB;
	$site_id = intval($site_id);

	$sections = array();
	$admins = array();
	$query = $DB->query("SELECT `section_id`, `section_description`, `sites`, `permission_type` FROM `highwaygroup`.`admin_sections` ORDER BY `section_description` ASC");
	while ($row = $DB->fetch_assoc($query))
	{
		if ($site_id != 0)
		{
			$sites = explode(',', $row['sites']);
			if (in_array($site_id, $sites))
			{
				if ($row['permission_type'] == 'admin') $admins[$row['section_id']] = $row['section_description'];
				else $sections[$row['section_id']] = $row['section_description'];
			}
		}
		elseif ($row['permission_type'] == 'admin') $admins[$row['section_id']] = $row['section_description'];
		else $sections[$row['section_id']] = $row['section_description'];
	}
	$DB->free_result($query);

	if ($admin) return $admins;
	return $sections;
}
function permissionsFromDatabase($site_id)
{
	global $DB;
	$site_id = intval($site_id);
	$sql = "SELECT user, section_id FROM highwaygroup.admin_users WHERE site_id=" . $site_id;
	$res = $DB->query($sql);
	$sections = array();
	while ($row = $DB->fetch_assoc($res)) $sections[$row['user']][] = $row['section_id'];
	return $sections;
}
function permissionsToDatabase($permissions, $site_id)
{
	global $DB;
	$site_id = intval($site_id);
	if (!$site_id) return;
	$DB->query("DELETE FROM highwaygroup.admin_users WHERE site_id=" . $site_id);
	if (is_array($permissions) && count($permissions))
	{
		foreach($permissions as $user => $sections) foreach($sections as $section_id => $on) $DB->query("INSERT INTO highwaygroup.admin_users (user, section_id, site_id) VALUES (" . formatSQLString($user) . ", " . intval($section_id) . ", " . $site_id . ")");
	}
	//print_array($permissions);
	//die();

}
function userPermittedSections($user, $site_id = 0)
{
	static $cache = array();
	if (array_key_exists($user, $cache) && array_key_exists($site_id, $cache[$user])) return $cache[$user][$site_id];

	global $DB;
	if ($user == '') return array();
	if ($site_id == 0) $site_id = 11;

	$result = array();
	$query = $DB->query("SELECT `ase`.`sect_key`, `au`.`section_id` FROM `highwaygroup`.`admin_users` AS `au` LEFT JOIN `highwaygroup`.`admin_sections` AS `ase` ON `ase`.`section_id` = `au`.`section_id` WHERE `user` = " . formatSQLString($user) . " AND `site_id` = " . intval($site_id));
	while ($row = $DB->fetch_assoc($query)) $result[] = $row['sect_key'];
	$DB->free_result($query);

	$cache[$user][$site_id] = $result;

	return $result;
}
function staffHasAccess($staff_id, $site_id)
{
	global $DB;
	$staff_id = intval($staff_id);
	$site_id = intval($site_id);
	if (!$site_id || !$staff_id) return false;
	$row = $DB->query_first("SELECT * FROM highwaygroup.website_access WHERE staff_id=" . $staff_id . " AND site_id=" . $site_id, MYSQL_ASSOC);
	if ($row) return true;
	return false;
}
function isSectionDefault($section_id)
{
	global $DB;
	$section_id = intval($section_id);
	if (!$section_id) return false;
	$res = $DB->query_first("SELECT `default` FROM highwaygroup.admin_sections WHERE section_id=" . $section_id, MYSQL_ASSOC);
	if ($res['default'] == 'y') return true;
	return false;
}
function updateDefaultSections($posted)
{
	global $DB;
	if (is_array($posted['default']))
	{
		$DB->query("UPDATE highwaygroup.admin_sections SET `default`='n' WHERE 1");
		foreach($posted['default'] as $section_id) $DB->query("UPDATE highwaygroup.admin_sections SET `default`='y' WHERE section_id=" . $section_id);
	}
}
?>
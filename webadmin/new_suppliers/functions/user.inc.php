<?php
/***********************
 * Menu Functions
 * Filename: user.inc.php
 ***********************
 * Purpose:
 *	Dump for user-related functions.
 ***********************/

function getUserByServer($dbp = "")
{
	global $_GLOBALS;

	return $_GLOBALS['staff'];
}

function getUserByString($uname, $dbp = "")
{
	global $DB;

	return $DB->query_first(sprintf(
		"SELECT * FROM `highwaygroup`.`users` WHERE `user` = %s LIMIT 1",
		$DB->escape($uname)
	), MYSQLI_ASSOC);
}

function getUserByID($id, $dbp="")
{
	global $DB;

	return $DB->query_first(sprintf(
		"SELECT * FROM `highwaygroup`.`users` WHERE `id` = %d LIMIT 1",
		$id
	), MYSQLI_ASSOC);
}

<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

include_once 'email_functions.php';

if (!function_exists('formatSQLString')) {
    function formatSQLString($instring)
    {
        $outstring = "'" . addslashes($instring) . "'";
        $outstring = str_replace(chr(13) . chr(10), '\n', $outstring);
        $outstring = str_replace(chr(13), '\n', $outstring);
        $outstring = str_replace(chr(10), '\n', $outstring);
        return $outstring;
    }
}

function getMembersEmails($member_list = false)
{
    global $DB;
    if (false === $member_list) {
        $member_list = array();
        $query       = $DB->query(sprintf(
            "SELECT `m`.`member_id` FROM `highwaygroup`.`new_members` AS `m` INNER JOIN `highwaygroup`.`new_members_mailing_list` AS `mm` ON `mm`.`member_id` = `m`.`member_id` WHERE `m`.`status` = 0 AND `mm`.`active` = 'y'"
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $member_list[] = $row['member_id'];
        }

        $DB->free_result($query);
    }
    $email_list = array();
    foreach ($member_list as $member_id) {
        $query = $DB->query("SELECT `name`, `email` FROM `highwaygroup`.`new_members_contacts` WHERE `member_id` = " . intval($member_id));
        while ($row = $DB->fetch_assoc($query)) {
            foreach (explode(',', $row['email']) as $email) {
                $email_list[trim(strtolower($email))] = trim($row['name']) . '<' . trim($email) . '>';
            }
        }

        $DB->free_result($query);
    }
    return $email_list;
}

function getMailingEmails()
{
    global $DB;
    $email_list = array();
    $query      = $DB->query("SELECT name,email FROM mailinglist WHERE active=1");
    while ($row = $DB->fetch_assoc($query)) {
        foreach (explode(',', $row['email']) as $email) {
            $email_list[trim(strtolower($email))] = trim($row['name']) . '<' . trim($email) . '>';
        }
    }

    $DB->free_result($query);
    return $email_list;
}

function getCountryEmails($country_list = array())
{
    global $DB;
    if (count($country_list)) {
        $member_list = array();
        $query       = $DB->query(sprintf(
            "SELECT `m`.`member_id` FROM `highwaygroup`.`new_members` AS `m` INNER JOIN `highwaygroup`.`new_members_mailing_list` AS `mm` ON `mm`.`member_id` = `m`.`member_id` WHERE `m`.`status` = 0 AND `mm`.`active` = 'y' AND `m`.`country` IN(%s)",
            "'" . implode("', '", $country_list) . "'"
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $member_list[] = $row['member_id'];
        }

        $DB->free_result($query);
        return getMembersEmails($member_list);
    } else {
        return getMembersEmails();
    }

}

function getCountryMailing($country_list = array())
{
    global $DB;
    if (count($country_list)) {
        $email_list = array();
        $query      = $DB->query("SELECT name,email FROM mailinglist WHERE active=1 AND country IN ('" . implode("', '", $country_list) . "')");
        while ($row = $DB->fetch_assoc($query)) {
            foreach (explode(',', $row['email']) as $email) {
                $email_list[trim(strtolower($email))] = trim($row['name']) . '<' . trim($email) . '>';
            }
        }

        $DB->free_result($query);
        return $email_list;
    } else {
        return getMailingEmails();
    }

}

function getOptionsEmails($options_list = array())
{
    global $DB;
    if (is_array($options_list) && count($options_list) > 0) {
        $member_list = array();
        $query       = $DB->query(sprintf(
            "SELECT `m`.`member_id` FROM `highwaygroup`.`new_members` AS `m` INNER JOIN `highwaygroup`.`new_members_mailing_list` AS `mm` ON `mm`.`member_id` = `m`.`member_id` LEFT JOIN `highwaygroup`.`new_members_survey` AS `ms` ON `ms`.`member_id` = `m`.`member_id` WHERE `m`.`status` = 0 AND `mm`.`active` = 'y' AND `ms`.`option` IN(%s)",
            "'" . implode("', '", $options_list) . "'"
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $member_list[] = $row['member_id'];
        }

        $DB->free_result($query);
        return getMembersEmails($member_list);
    } else {
        return getMembersEmails();
    }

}

if (!function_exists('array_intersect_key')) {
    //array array_intersect_key ( array array1, array array2 [, array ...] )
    function array_intersect_key()
    {
        $args   = func_get_args();
        $result = array_shift($args);
        foreach ($args as $intersect) {
            foreach ($result as $key => $value) {
                if (!array_key_exists($key, $intersect)) {
                    unset($result[$key]);
                }
            }
        }

        return $result;
    }
}

function generate_list()
{
    global $DB;
    global $type, $options, $country, $include_mailing, $include_members;

    if (!is_array($country) || in_array('', $country)) {
        unset($country);
    }

    if (!is_array($options)) {
        unset($options);
    }

    $email_list = array();
    if ('true' == $include_members) {
        $email_list = array_merge($email_list, getMembersEmails());
    }

    if ($options) {
        $email_list = array_intersect_key($email_list, getOptionsEmails($options));
    }

    if ($country) {
        $email_list = array_intersect_key($email_list, getCountryEmails($country));
    }

    if ('true' == $include_mailing) {
        $addme_list = getMailingEmails();
        if ($country) {
            $addme_list = array_intersect_key($addme_list, getCountryMailing($country));
        }

        $email_list = array_merge($email_list, $addme_list);
    }

    foreach ($email_list as $key => $value) {
        if (strpos($value, '@') === false) {
            unset($email_list[$key]);
        }
    }

    $fault = true;
    while ($fault) {
        $fault = false;
        foreach ($email_list as $key => $value) {
            if (str_replace(',', ' ', $value) != $value) {
                $fault            = true;
                $email_list[$key] = $value = str_replace(',', ' ', $value);
            }
            if (str_replace('  ', ' ', $value) != $value) {
                $fault            = true;
                $email_list[$key] = $value = str_replace('  ', ' ', $value);
            }
            if (strtolower(substr($value, 0, 12)) == 'not supplied') {
                $fault            = true;
                $email_list[$key] = $value = trim(substr($value, 12));
            }
            if (strtolower(substr($value, 0, 7)) == 'unknown') {
                $fault            = true;
                $email_list[$key] = $value = trim(substr($value, 7));
            }
            if (substr($value, 0, 1) == '<') {
                $fault            = true;
                $email_list[$key] = $value = trim(substr($value, 1, -1));
            }
        }
    }
    asort($email_list);

    $email_vars['param_type'] = $type;
    if (is_array($country)) {
        $email_vars['param_country'] = implode(",", $country);
    } else {
        $email_vars['param_country'] = "";
    }

    if (is_array($options)) {
        $email_vars['param_options'] = implode(",", $options);
    } else {
        $email_vars['param_options'] = "";
    }

    $email_vars['email_addresses'] = implode(",", $email_list);
    $email_vars['total']           = count($email_list);
    set_mailout_vars($email_vars);
    reset_list();

    foreach ($email_list as $value) {
        $value = explode('<', str_replace('>', '', $value));
        $name  = reset($value);
        $email = end($value);
        $DB->query("INSERT INTO mailout_tmp_list(name,email) VALUES(" . formatSQLString($name) . "," . formatSQLString($email) . ")");
    }
}

function reset_list()
{
    global $DB;
    clean_up();
    $DB->query("DELETE FROM mailout_tmp_list");
    echo "Temporary List Cleared.<br />\n";
}

?>
<form accept-charset="utf-8" name="mailout_admin" method="post" action="email_list.php">
<?php

switch ($action) {
    case 'clear':
        reset_list();
        break;
    case 'create':
        create_from_mailinglist();
        break;
    case 'gen':
        generate_list();
        break;
}

include 'header.inc.php';
include 'email_list_overview.inc.php';

?>
<div align="center">
	<p>Total Emails = <?=$num_emails['total']?></p>
</div>

<table border="0" cellspacing="0" cellpadding="10" align="center">
	<tr>
		<td><table border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<input type="button" name="back" value="&lt;&lt; Back" onclick="window.location='index.php'">
				</td>
			</tr>
		</table></td>
		<td><table border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<input type="button" name="Clear" value="Clear" onclick="window.location='email_list.php?action=clear'">
				</td>
			</tr>
		</table></td>
		<td><table border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<input type="button" name="Next" value="Next &gt;&gt;" onclick="window.location='composer.php?action2=new_email'">
				</td>
			</tr>
		</table></td>
	</tr>
</table>
</form>
<?php
include_once '../footer.inc.php';

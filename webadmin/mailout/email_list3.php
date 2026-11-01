<?php
/***********************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Bede Pavan
 ** Filename......:
 ** Last changed..:
 ** Notes.........:
 ***********************************************************************/

include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

include_once 'email_functions.php';

function generate_list()
{
    global $DB;
    global $type, $options, $country, $include_mailing, $include_members;

    $members_ids = getMembersIds($DB);
    $mailing_ids = getMailingIds($DB);
    $option_ids  = getOptionIds($DB, $options, $include_mailing, $include_members);
    $country_ids = getCountryIds($DB, $country, $include_mailing, $include_members);

    if ("true" == $include_members && "true" == $include_mailing) {
/*        echo "including mailing list and members<br>";
echo "MEMBERS: ".count($members_ids);
echo "MAILING: ".count($mailing_ids);
echo "OPTIONS: ".count($option_ids);
echo "COUNTRY: ".count($country_ids);
 */
        $temp = array_merge($members_ids, $mailing_ids);
        $ids  = arrayKeyIntersection(array($temp, $option_ids, $country_ids));
    } else if ("true" == $include_members && "true" != $include_mailing) {
/*        echo "including members only<br>";
echo "MEMBERS: ".count($members_ids);
echo "OPTIONS: ".count($option_ids);
echo "COUNTRY: ".count($country_ids);
 */
        $ids = arrayKeyIntersection(array($members_ids, $option_ids, $country_ids));
    } else if ("true" != $include_members && "true" == $include_mailing) {
/*        echo "including mailing list only<br>";
echo "MAILING: ".count($mailing_ids);
echo "OPTIONS: ".count($option_ids);
echo "COUNTRY: ".count($country_ids);
 */
        $ids = arrayKeyIntersection(array($mailing_ids, $option_ids, $country_ids));
    } else {
        return;
    }

    $email_vars['param_type'] = $type;
    if (is_array($options)) {
        $email_vars['param_country'] = implode(",", $country);
    } else {
        $email_vars['param_country'] = '';
    }

    if (is_array($options)) {
        $email_vars['param_options'] = implode(",", $options);
    } else {
        $email_vars['param_options'] = "";
    }

    $email_vars['email_addresses'] = implode(",", $ids);
    $email_vars['total']           = count($ids);
    set_mailout_vars($email_vars);
    reset_list();
    foreach ($ids as $email => $name) {
        $statement = "INSERT INTO mailout_tmp_list(name, email) VALUES ('" . addslashes($name) . "','" . addslashes($email) . "')";
        $DB->query($statement);
    }
}

// GET THE EMAIL ADDRESSES FROM THE MAILING LIST
function getMailingIds($DB)
{
    $mailing = array();
    $sql     = "SELECT name AS member_name,email FROM mailinglist WHERE active=1";
    $result  = $DB->query($sql);
    while ($row = $DB->fetch_array($result)) {
        $mailing[trim($row['email'])] = trim($row['member_name']);
    }

    return $mailing;
}

// GET THE EMAIL ADDRESSES OF ALL MEMBERS
function getMembersIds($DB)
{
    /* Updated to honour active flag
    $members = array();
    $sql = "SELECT CONCAT_WS(\" \",name,surname) AS member_name,email_address AS email FROM `highwaygroup`.`new_members`";
    $result = $DB->query($sql);
    while ($row = $DB->fetch_array($result))
    $members[trim($row['email'])] = trim($row['member_name']);
    return $members;
     *//* Updated again to use new `highwaygroup`.`new_members` DB layout
    $members = array();
    $asql = "SELECT member_id FROM `highwaygroup`.`new_members_logins` WHERE active='y'";
    $aresult = $DB->query($asql);
    while($arow = $DB->fetch_array($aresult))
    {
    $sql = "SELECT CONCAT_WS(\" \",name,surname) AS member_name,email_address AS email
    FROM `highwaygroup`.`new_members` WHERE member_id='{$arow['member_id']}'";
    $row = $DB->query_first($sql);
    $members[trim($row['email'])] = trim($row['member_name']);
    }
    return $members;
     */
    $members = array();
    $asql    = "SELECT member_id FROM `highwaygroup`.`new_members` WHERE status=0";
    $aresult = $DB->query($asql);
    while ($arow = $DB->fetch_array($aresult)) {
        $sql = "SELECT `name` AS `member_name`, `email`
				FROM `highwaygroup`.`new_members_contacts` WHERE `member_id` = '{$arow['member_id']}'";
        $result = $DB->query($sql);
        while ($row = $DB->fetch_array($result)) {
            $members[trim($row['email'])] = trim($row['member_name']);
        }
    }
    return $members;
}

// GET THE EMAIL ADDRESSES FOR THE OPTIONS SELECTED
function getOptionIds($DB, $options, $mailList, $members)
{
    $member_options = array();
    $mail_options   = array();
    if (is_array($options)) {
        $option_list = implode(',', $options);
    }

    if (isset($option_list)) {
        if ("true" == $members) {
            /* Updated for new DB layout.
            $statement = "SELECT DISTINCT(ms.member_id), CONCAT_WS(\" \",m.name,m.surname) AS member_name,m.email_address AS email
            FROM `highwaygroup`.`new_members_survey` ms
            LEFT JOIN `highwaygroup`.`new_members` m ON ms.member_id=m.member_id
            WHERE ms.option IN ($option_list)"; */
            $statement = "SELECT `mc`.`name` AS `member_name`, `mc`.`email` FROM `highwaygroup`.`new_members_contacts` AS `mc` LEFT JOIN `highwaygroup`.`new_members_survey` AS `ms` ON `ms`.`member_id` = `mc`.`member_id` WHERE `ms`.`option` IN ($option_list)";
            $result = $DB->query($statement);
            while ($row = $DB->fetch_array($result)) {
                $member_options[trim($row['email'])] = trim($row['member_name']);
            }

        }
        if ("true" == $mailList) {
            $mail_members     = array();
            $mail_non_members = array();

            // SELECT ALL THE MEMBERS FROM THE MAILING LIST THAT HAVE THE SELECTED OPTIONS
            $statement = "SELECT ml.name AS member_name,ml.email
						FROM mailinglist ml, `highwaygroup`.`new_members_survey` ms
						WHERE ml.member_id=ms.member_id AND ms.option IN ($option_list)";
            $result = $DB->query($statement);
            while ($row = $DB->fetch_array($result)) {
                $mail_members[trim($row['email'])] = trim($row['member_name']);
            }

            // SEND TO ALL NON MEMBERS OF THE MAILING LIST REGARDLESS OF OPTIONS
            $statement = "SELECT name,email FROM mailinglist WHERE member_id=0 OR member_id='' OR member_id IS NULL";
            $result2   = $DB->query($statement);
            while ($row = $DB->fetch_array($result2)) {
                $mail_non_members[trim($row['email'])] = trim($row['name']);
            }

            $mail_options = array_merge($mail_members, $mail_non_members);
        }
        $temp = array_merge($member_options, $mail_options);
        return $temp;
    }
    return array();
}

// GET THE EMAIL ADDRESSES FOR THE COUNTRIES SELECTED
function getCountryIds($DB, $country, $mailList, $members)
{
    $mail_countries   = array();
    $member_countries = array();
    if (is_array($country) && in_array("", $country)) {
        $where = "";
    } else {
        $countries = implode(',', $country);
        $countries = str_replace(",", "','", $countries);
        $where     = "WHERE country IN ('$countries')";
    }
    if ("true" == $members) {
        $statement = "SELECT CONCAT_WS(\" \", `name`, `surname`) AS `member_name`, `email_address` AS `email` FROM `highwaygroup`.`new_members` $where";
        $result    = $DB->query($statement);
        while ($row = $DB->fetch_array($result)) {
            $member_countries[trim($row['email'])] = trim($row['member_name']);
        }

    }
    if ("true" == $mailList) {
        $statement = "SELECT name AS member_name, email FROM mailinglist $where";
        $result    = $DB->query($statement);
        while ($row = $DB->fetch_array($result)) {
            $mail_countries[trim($row['email'])] = trim($row['member_name']);
        }

    }
    $temp = array_merge($member_countries, $mail_countries);
    return $temp;
}

// PERFORMS AN INTERSECTION ON AN ARRAY OF ARRAYS BASED ON THEIR KEYS
function arrayKeyIntersection($array2D)
{
    if (!is_array($array2D)) {
        return;
    }

    if (count($array2D) < 2) {
        return;
    }

    $intersection = array();

    $first = $array2D[0];
    foreach ($first as $firstKey => $firstValue) {
        $inAll = true;
        for ($i = 1; $i < count($array2D); $i++) {
            if (!array_key_exists($firstKey, $array2D[$i])) {
                $inAll = false;
                break;
            }
        }
        if ($inAll) {
            $intersection[$firstKey] = $firstValue;
        }
    }
    return $intersection;
}

function reset_list()
{
    global $DB;

    //need to delete all the attchment files.
    clean_up();
    $statement = "DELETE FROM mailout_tmp_list";

    $DB->query($statement);

    echo "Temporary List Cleared.<br>";

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
        </table>
       </td>
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

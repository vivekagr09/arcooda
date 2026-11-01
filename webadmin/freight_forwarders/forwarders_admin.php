<?php
/***********************************************************************
 ** Title.........:    Member List
 ** Version.......:
 ** Author........:    Bede Pavan [is a fucking idiot. Signed, Nathan.]
 ** Filename......:
 ** Last changed..:  20050714 by Nathan Tomkins
 ** Notes.........:    Un-kludgified the member list. Most of the functionality
 **               :  has been moved out of this file, so I've stripped it back
 **               :  to just do the member listing. I probably should template
 **               :  all of this, but it doesn't really need to be done.
 ***********************************************************************/
//Modified into Forwarders list
//October 2005 Daniel Kehl

include_once __DIR__ . '/common.inc.php';

global $DB;

include_once 'forwarders_common.inc.php';
include_once 'templates/forwarders_header.tpl.php';

/**
 * Actually doesn't make a URL, it's a function for sticking POST/GET variables on a new request
 *
 * @param string $Skip
 * @param bool   $AsInput
 *
 * @return string
 */
function MakeURL($Skip, $AsInput = false)
{
    //Loops Through Get Vars
    $RetVal = '';
    foreach ($_REQUEST as $key => $value) {
        if ($key != $Skip) {
            if ($AsInput) {
                $RetVal .= "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
            } else {
                if (null != $RetVal) {
                    $RetVal .= "&";
                }

                $RetVal .= "$key=$value";
            }
        }
    }

    return $RetVal;
}

$items = $DB->query_first("SELECT COUNT(*) as total FROM freight_services");
?>
<!-- suppliers admin -->

<form accept-charset="utf-8" autocomplete="off" method="post" action="" name="admin_form">
<?php

$search_name_email = '';
$search_id = '';
if ("Search" == $_REQUEST['action']) {
    $search_name_email = $_POST['search_name_email'];
    $search_id         = $_POST['search_member_id'];
}

//Gets "GET" Data
$Ord  = $_GET["ord"] ? ("asc" == $_GET["ord"] ? "asc" : "desc") : "desc";
$By   = $_GET["by"] ? $_GET["by"] : "id";
$page = $_GET["page"] ? $_GET["page"] : "0";
$rby  = $_GET["rby"] ? $_GET["rby"] : "";
$rto  = $_GET["rto"] ? $_GET["rto"] : "";

//Settings
$UPP = 50; //Users Per Page
$url = "forwarders_admin.php?page=$page";

//Sort Lookup Table
$DBTable["Name of Forwarder"] = "freight_services.name";
$DBTable["Country"]           = "freight_services.country";
$DBTable["Last Contact"]      = "freight_services.id";
$DBTable["Quotes"]            = "freight_services.quotes";
$DBTable["Orders"]            = "freight_services.orders";

//Makes Where Clause For Search
$nr = false;
$member_ids = array();
if ($search_name_email || $search_id) {
    if ($search_name_email) {
        $member_ids = searchMembers($search_name_email, true);
        $url .= "&amp;search_name_email=$search_name_email";
    }
    if ($search_id) {
        $member_ids = array_merge($member_ids, $search_id);
        $url .= "&amp;search_id=$search_id";
    }
    if (!count($member_ids)) {
        $nr = true;
    }
    // No Results condition
} elseif ($_REQUEST["SearchQuery"]) {
    $member_ids = searchMembers($_REQUEST['SearchQuery']);
    $url .= "&amp;SearchQuery={$_REQUEST['SearchQuery']}";
    if (!count($member_ids)) {
        $nr = true;
    }
    // No Results condition
}

// Restrict search
//if($rby && $rto)
$forwarder_ids = [];
if (is_array($rby) && count($rby)) {
    foreach ($rby as $k => $v) {
        $these_members = array();
        switch ($k) {
            case "ct":
                $sql = "SELECT DISTINCT id FROM freight_services WHERE country='" . (("XXXX" == $v) ? "" : "$v") . "'";
                //echo "$sql<BR>";
                break;
            default:
                $sql = "SELECT DISTINCT `member_id` FROM `highwaygroup`.`new_members_survey` WHERE `link` = '$k'";
                break;
        }

        $these_forwarders = [];
        if ($dbq = $DB->query(sprintf(
            "%s",
            $sql
        ))) {
            while ($r = $DB->fetch_assoc($dbq)) {
                $these_forwarders[] = $r['id'];
                $q                  = $r['id'];
            }
            $DB->free_result($dbq);
        }

        if (count($forwarder_ids)) {
            $forwarder_ids = array_intersect($these_forwarders, $forwarder_ids);
        } else {
            $forwarder_ids = $these_forwarders;

        }
        if (!count($forwarder_ids)) {
            $nr = true;
        }
        // No Results condition

        // Add this entry to $url
        $url .= "&amp;rby[$k]=$v";
    }
}

$where_arr = [];
if (count($forwarder_ids)) {
    $where_arr['id'] = "id IN ('" . implode("','", $forwarder_ids) . "')";
}
if (count($where_arr)) {
    $Where = "WHERE " . implode(" AND ", $where_arr);
}

//Get Category totals data

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/new_suppliers/functions/country.inc.php';
$country_totals = makeCountryArray();

$country_totals['NONE']['name'] = "Not Specified";
$sql                            = "SELECT country FROM freight_services" . (array_key_exists('id', $where_arr) ? " WHERE {$where_arr['id']}" : "");
//echo "$sql<BR>";
//echo "<BR>$where_arr";
if ($dbq = $DB->query(sprintf(
    "%s",
    $sql
))) {
    while ($r = $DB->fetch_assoc($dbq)) {
        if ($r['country']) {
            $country_totals[$r['country']]['count']++;
        } else {
            $country_totals['NONE']['count']++;
        }
    }
    $DB->free_result($dbq);
}

if (!$nr) {
    //Change $where_arr to apply to the next sql query
    if (count($forwarder_ids)) {
        $where_arr['id'] = "f.id IN ('" . implode("','", $forwarder_ids) . "')";
    }
    if (count($where_arr)) {
        $Where = "WHERE " . implode(" AND ", $where_arr);
    }
}

//Images To Use
$DescImg = '<i class="fas fa-sort-down"></i>';
$AscImg  = '<i class="fas fa-sort-up"></i>';

$describe_restriction = '';
if (is_array($rby) && count($rby)) {
    foreach ($rby as $k => $v) {
        switch ($k) {
            case "ct":
                $describe_restriction .= "Country: " . $country_totals[$v]['name'] . "<br />\r\n";
                break;
            default:
                $describe_restriction .= (1 == $survey_totals[$k]['parent'] ? "Category" : "Industry");
                $describe_restriction .= ": " . $survey_totals[$k]['long_desc'] . "<br />\r\n";
                break;
        }
    }
}

?>

<!-- ...................................................................... -->
<!-- ::                  Local CSS and Javascripts                       :: -->
<!-- ::..................................................................:: -->

<style type="text/css">
<!--

table#memlist
{
	width: 95%;
}

table#memlist th, table#memlist td {
	padding: 4px;
	white-space: nowrap;
}

table#memlist td.wrap {
	white-space: normal;
}

table#memlist th {
	color: white;
	background-color: #000066;
	font-weight: bold;
	font-size: 10pt;
	text-align: center;
}

table#memlist th a {
	color: white;
}

table.dispgroupbox {
	margin-left: auto;
	margin-right: auto;
	width: 90%;
	display: none;
}

table.dispgroupbox td {
	background: #FFFFFF;
	border: 1px black solid;
}

table.dispgroupbox td:hover {
	background: #DFE1F7;
}
-->
</style>

<script type="text/javascript">
//<![CDATA[
function toggleVisibility(elem_id) {
	var elem = document.getElementById(elem_id);
	if (elem == null) {
		return;
	}
	if (elem.style.display == 'none' || elem.style.display == '') {
		elem.style.display = 'block';
	} else {
		elem.style.display = 'none';
	}
}
//]]>
</script>

<!--   Category Count Boxes   -->
<a href="#" onclick="toggleVisibility('dispcountry'); return false;">Display Forwarders by Country</a><br />
<table class="dispgroupbox" id="dispcountry" style="display: none;">
	<tr>
<?php
$i = 1;
foreach ($country_totals as $ct):
    if ($ct['count']):
?>
		<td style="width:25%"><a href="<?php echo $url . "&amp;rby[ct]=" . $ct['code'] ?>"><?php echo $ct['name'] ?> (<?php echo $ct['count'] ?>)</a></td>
<?php
        if (!($i % 4)):
?>
	</tr>
	<tr>
<?php
        endif;
        $i++;
    endif;
endforeach;
?>
	</tr>
</table>

<!-- END Category Count Boxes -->

<?php
if ($rby && $rto) {
    $url .= "&amp;rby=$rby&amp;rto=$rto";
}
?>

<!-- Search Box -->
<p align="right">Search: <input type="text" name="SearchQuery" /><input type="submit" value="Go" />
<?php echo MakeURL("SearchQuery", true) ?></p>
<!-- End Search Box -->

<?php
if ($describe_restriction):
?>
<p style="text-align:right;color:red;">List restricted using these conditions:<br/><?=$describe_restriction?></p>
<?php
endif;

if ($nr):
?>
<h3 style="color:red;font-weight:bold;text-align:center">Search Found No Results.</h3>
<?php
else:
?>
<table cellspacing="1" align="center" id="memlist">
	<tr>
		<th>
			<a href="<?=$url?>&amp;by=name&ord=<?php echo $Ord == "asc" ? "desc" : "asc" ?>">Name of Forwarder</a> <?php echo ("name" == $By) ? (("asc" == $Ord) ? "$AscImg" : "$DescImg") : "" ?>
		</th>
		<th>
			<a href="<?=$url?>&amp;by=company&ord=<?php echo $Ord == "asc" ? "desc" : "asc" ?>">Country</a> <?php echo ("country" == $By) ? (("asc" == $Ord) ? "$AscImg" : "$DescImg") : "" ?>
		</th>
		<th>
			<a href="<?=$url?>&amp;by=country&ord=<?php echo $Ord == "asc" ? "desc" : "asc" ?>">Last Contact</a> <?php echo ("contact" == $By) ? (("asc" == $Ord) ? "$AscImg" : "$DescImg") : "" ?>
		</th>
		<th>
			<a href="<?=$url?>&amp;by=joined&ord=<?php echo $Ord == "asc" ? "desc" : "asc" ?>">Quotes</a> <?php echo ("quotes" == $By) ? (("asc" == $Ord) ? "$AscImg" : "$DescImg") : "" ?>
		</th>
		<th>
			<a href="<?=$url?>&amp;by=logins&ord=<?php echo $Ord == "asc" ? "desc" : "asc" ?>">Bookings</a> <?php echo ("bookings" == $By) ? (("asc" == $Ord) ? "$AscImg" : "$DescImg") : "" ?>
		<th>&nbsp;</th>
	</tr>

<?php
    //Loops Through Forwarders
    $country_list = getCountryList(false);

    if ($data = $DB->query(sprintf(
        "SELECT `f`.`id`, `f`.`name`, `f`.`country`, COUNT(`q`.`id`) AS `quote_count` FROM `freight_services` AS `f` LEFT JOIN `freight_services_quotes` AS `q` ON `q`.`product_id` = `f`.`id` %s GROUP BY `f`.`name`",
        $Where
    ))) {
        while ($row = $DB->fetch_assoc($data)) {
?>
	<tr class="<?php echo $BgCol = ("tableRow" == $BgCol) ? "tableRowAlt" : "tableRow" ?>" style="<?php echo ($ExtData['lock_name'] && !$ExtData['my_lock']) ? "background:#EE90A0" : "" ?>">
	<!--This is a particularly ugly way of getting all the data needed. It's not efficient at all, but I can't come up with an SQL statement that works to do it elegantly -->
<?php
            $counting_id = $row['id'];

            $bookings = 0;
            if ($item = $DB->query_first(sprintf(
                "SELECT COUNT(`id`) AS `booking_count` FROM `freight_services_quotes` WHERE `product_id` = %d AND `booking_confirmed` = 'y' LIMIT 1",
                $counting_id
            ), MYSQLI_ASSOC)) {
                $bookings = $item['booking_count'];
            }

            $last_booking = '';
            if ($item = $DB->query_first(sprintf(
                "SELECT MAX(`date`) AS `max_booking` FROM `freight_services_quotes` WHERE `product_id` = %d AND `booking_confirmed` = 'y' LIMIT 1",
                $counting_id
            ), MYSQLI_ASSOC)) {
                $last_booking = $item['max_booking'];
            }

            $last_quote = '';
            if ($item = $DB->query_first(sprintf(
                "SELECT MAX(`date`) AS `max_quote` FROM `freight_services_quotes` WHERE `product_id` = %d AND `booking_confirmed` = 'y' LIMIT 1",
                $counting_id
            ), MYSQLI_ASSOC)) {
                $last_quote = $item['max_quote'];
            }

            if ($last_booking < $last_quote) {
                if ("" == $last_quote) {
                    $lastContact = "None";
                } else {
                    $lastContact = date("d-m-Y", strtotime($last_quote));
                }
            } else {
                if ("" == $last_booking) {
                    $lastContact = "None";
                } else {
                    $lastContact = date("d-m-Y", strtotime($last_booking));
                }
            }
?>
		<td align="center"><?php echo $row['name']; ?></td>
		<td align="center"><?=htmlspecialchars($country_list[$row['country']] ?? $row['country'])?></td>
		<td align="center"><?=$lastContact?></td>
		<td align="center"><?php echo $row['quote_count']; ?></td>
		<td align="center"><?=$bookings?></td>
		<td>
			<a href="edit_forwarder.php?action=edit&amp;id=<?php echo $row['id'] ?>">Edit</a>&nbsp;|&nbsp;
			<a href="edit_forwarder.php?action=delete&amp;id=<?php echo $row['id'] ?>">Del</a>
		</td>
	</tr>
<?php
        }
        $DB->free_result($data);
    }
?>
</table>
<br />
<?php

    //Gets Totals Data
    if ($Total = $DB->query_first(sprintf(
        "SELECT COUNT(`id`) AS `forwarder_count` FROM `freight_services` %s LIMIT 1",
        $Where
    ), MYSQLI_ASSOC)) {
?>
<p><b>Showing : <?=($page * $UPP + 1) . " - " . min(($page + 1) * ($UPP), $Total['forwarder_count'])?> of <?=$Total['forwarder_count']?></b>:
<?=makePageLimitLinks("$url&amp;ord=$Ord&amp;by=$By", $Total['forwarder_count'], $page, $UPP, 5);?></p>
<?php
    }
endif;
?>
</form>
<!--//suppliers admin -->
<?php

include_once 'templates/forwarders_footer.tpl.php';

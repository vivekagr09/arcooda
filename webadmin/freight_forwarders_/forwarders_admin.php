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

include_once __DIR__ . '/header.inc.php';

include_once '../functions/quote_functions.inc.php';
include_once "../new_suppliers/functions/menu.inc.php"; // For Link Drawing
include_once "../new_suppliers/functions/country.inc.php"; // For messing with countries

$script = "forwarders_admin.php?view="; // TODO: Kill this.

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

$items = $DB->query_first("SELECT COUNT(*) AS `total` FROM `coinop`.`freight_forwarders`");

?>
<!-- suppliers admin -->

<form accept-charset="utf-8" autocomplete="off" method="post" action="<?=$script?>" name="admin_form">
	<br>
      <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td class="pageMenu" width="20%">
            <div align="center">Freight Forwarders</div>
          </td>
          <td class="pageMenu" width="80%">
            <div align="right">
			<a href="edit_forwarder.php?action=add">Add New Forwarder</a> |
			<a href="<?=$script?>&query=&display_search_box=true">List All Forwarders (<?=$items['total']?>)</a> |
			</div>
          </td>
        </tr>
      </table>
	  <br /><br />
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
$DBTable["Name of Forwarder"] = "freight_forwarders.name";
$DBTable["Country"]           = "freight_forwarders.country";
$DBTable["Last Contact"]      = "freight_forwarders.id";
$DBTable["Quotes"]            = "freight_forwarders.quotes";
$DBTable["Orders"]            = "freight_forwarders.orders";

//Makes Where Clause For Search
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
                $sql = "SELECT DISTINCT `id` FROM `coinop`.`freight_forwarders` WHERE `country` = '" . (("XXXX" == $v) ? "" : "$v") . "'";
                //echo "$sql<BR>";
                break;
            default:
                $sql = "SELECT DISTINCT `member_id` FROM `highwaygroup`.`new_members_survey` WHERE `link` = '$k'";
                break;
        }

        $these_forwarders = [];
        if ($dbq = $DB->query($sql)) {
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
$country_totals                 = makeCountryArray();
$country_totals['NONE']['name'] = "Not Specified";
$sql                            = "SELECT `country` FROM `coinop`.`freight_forwarders`" . (array_key_exists('id', $where_arr) ? " WHERE {$where_arr['id']}" : "");
//echo "$sql<BR>";
//echo "<BR>$where_arr";
if ($dbq = $DB->query($sql)) {
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
    //Gets Totals Data
    $Total = $DB->query_first(sprintf(
        "SELECT COUNT(`id`) AS `forwarder_count` FROM `coinop`.`freight_forwarders` %s",
        $Where
    ), MYSQLI_ASSOC);
    if (count($where_arr)) {
        $Where = "WHERE " . implode(" AND ", $where_arr);
    }

    $data = $DB->query(sprintf(
        "SELECT `f`.`id`, `f`.`name`, `f`.`country`, COUNT(`q`.`id`) AS `quote_count` FROM `coinop`.`freight_forwarders` AS `f` LEFT JOIN `coinop`.`freight_forwarders_quotes` AS `q` ON `q`.`forwarder_id` = `f`.`id` %s GROUP BY `f`.`name`",
        $Where
    ));
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
		<td style="width:25%"><a href="<?=$url . "&amp;rby[ct]=" . $ct['code']?>"><?=$ct['name']?> (<?=$ct['count']?>)</a></td>
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
<?=MakeURL("SearchQuery", true)?></p>
<!-- End Search Box -->

<?php
if ($describe_restriction) {
?>
<p style="text-align:right;color:red;">List restricted using these conditions:<br/><?=$describe_restriction;?></p>
<?php
}

if ($nr):
?>
<h3 style="color:red;font-weight:bold;text-align:center">Search Found No Results.</h3>
<?php
else:
?>
<table cellspacing="1" align="center" id="memlist">
	<tr>
		<th>
			<a href="<?=$url;?>&amp;by=name&ord=<?=$Ord == "asc" ? "desc" : "asc"?>">Name of Forwarder</a> <?=("name" == $By) ? (("asc" == $Ord) ? "$AscImg" : "$DescImg") : ""?>
		</th>
		<th>
			<a href="<?=$url;?>&amp;by=company&ord=<?=$Ord == "asc" ? "desc" : "asc"?>">Country</a> <?=("country" == $By) ? (("asc" == $Ord) ? "$AscImg" : "$DescImg") : ""?>
		</th>
		<th>
			<a href="<?=$url;?>&amp;by=country&ord=<?=$Ord == "asc" ? "desc" : "asc"?>">Last Contact</a> <?=("contact" == $By) ? (("asc" == $Ord) ? "$AscImg" : "$DescImg") : ""?>
		</th>
		<th>
			<a href="<?=$url;?>&amp;by=joined&ord=<?=$Ord == "asc" ? "desc" : "asc"?>">Quotes</a> <?=("quotes" == $By) ? (("asc" == $Ord) ? "$AscImg" : "$DescImg") : ""?>
		</th>
		<th>
			<a href="<?=$url;?>&amp;by=logins&ord=<?=$Ord == "asc" ? "desc" : "asc"?>">Bookings</a> <?=("bookings" == $By) ? (("asc" == $Ord) ? "$AscImg" : "$DescImg") : ""?>
		<th>&nbsp;</th>
	</tr>

<?php
    //Loops Through Forwarders

    $country_list = getCountryList(false);

    //echo "$sql <BR>";
    while ($row = $DB->fetch_assoc($data)):

?>
	<tr class="<?=$BgCol = ("tableRow" == $BgCol) ? "tableRowAlt" : "tableRow"?>" style="<?=($ExtData['lock_name'] && !$ExtData['my_lock']) ? "background:#EE90A0" : ""?>">
	 <!--This is a particularly ugly way of getting all the data needed. It's not efficient at all, but I can't come up with an SQL statement that works to do it elegantly -->
<?php
        $counting_id = $row['id'];
        $bookingSQL  = "SELECT COUNT(`id`) AS `booking_count` FROM `coinop`.`freight_forwarders_bookings` WHERE `forwarder_id` = $counting_id";
        $result      = $DB->query($bookingSQL);
        if ($item = $DB->fetch_assoc($result)) {
            $bookings = $item['booking_count'];
        }
        $lastBookingSQL = "SELECT MAX(`date`) AS `max_booking` FROM `coinop`.`freight_forwarders_bookings` WHERE `forwarder_id` = $counting_id";
        $result         = $DB->query($lastBookingSQL);
        if ($item = $DB->fetch_assoc($result)) {
            $last_booking = $item['max_booking'];
        }
        $lastQuoteSQL = "SELECT MAX(`date`) AS `max_quote` FROM `coinop`.`freight_forwarders_quotes` WHERE `forwarder_id` = $counting_id";
        $result       = $DB->query($lastQuoteSQL);
        if ($item = $DB->fetch_assoc($result)) {
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
		<td align="center"><?=$row['name'];?></td>
		<td align="center"><?=$country_list[$row['country']] ?? $row['country']?></td>
		<td align="center"><?=$lastContact;?></td>
		<td align="center"><?=$row['quote_count'];?></td>
		<td align="center"><?=$bookings;?></td>
		<td>
			<a href="edit_forwarder.php?action=edit&amp;id=<?=$row['id']?>">Edit</a>&nbsp;|&nbsp;
			<a href="edit_forwarder.php?action=delete&amp;id=<?=$row['id']?>">Del</a>
		</td>
	</tr>
<?php
    endwhile;
?>
</table>
<br />
<p><b>Showing : <?=($page * $UPP + 1) . " - " . min(($page + 1) * ($UPP), $Total['forwarder_count'])?> of <?=$Total['forwarder_count']?></b>:
<?=makePageLimitLinks("$url&amp;ord=$Ord&amp;by=$By", $Total['forwarder_count'], $page, $UPP, 5);?></p>
<?php
endif;
?>
</form>
<!--//suppliers admin -->
<?php

include '../footer.inc.php';

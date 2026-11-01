<?php
include_once __DIR__ . '/common.inc.php';

?><!DOCTYPE html>
<html>
	<head>
		<title>World Summary - Add/Edit Time Zones</title>
	</head>
<script language="JavaScript" type="text/javascript">
function conf(){
	if(confirm('Are you sure you to delete the Time Zone selected'))
		return true;
	else
		return false;
}
function pviiClassNew(obj, new_style) { //v2.6 by PVII
	obj.className=new_style;
}
</script>
<?php
include_once 'top.times.php'; // the header file specified to this section..

$fields['id']               = "center";
$fields['name']             = "center";
$fields['offset']           = "center";
$fields['daylight_savings'] = "center";
$fields['country']          = "center";

function add()
{
    global $DB, $data;

    if ($DB->query(sprintf(
        "INSERT INTO `world`.`time_zones` (%s) VALUES (%s)",
        get_fields($data),
        get_values($data)
    ))) {
        echo "New Time Zone Added<br>";
        return $DB->insert_id();
    }

    return 0;
}

/***********************************************************************************************/
// This function edits a section, which is already present in the database..
/***********************************************************************************************/
function edit()
{
    global $DB, $data, $user, $id;
    $statement = "UPDATE time_zones SET " . get_set_values($data) . " WHERE id = '$id'";
    $DB->query($statement);
    echo "Time Zone Id $id updated<br>";

}

/***********************************************************************************************/
// This function deletes a section, which is already present in the database..
/***********************************************************************************************/
function delete_value()
{
    global $DB, $data, $user, $id;
    $statement = "DELETE FROM `world`.`time_zones` WHERE id= '$id'";
    $DB->query($statement);
    echo "Time Zone Id $id deleted<br>";
}

/**********************************************************************************************/
// This function gets the data from the database and displays it.
/**********************************************************************************************/
function view()
{
    global $DB, $by, $order, $print, $fields, $start;
    if ("" == $by) {
        $by = "country";
    }

    if ("daylight_savings" == $by) {
        $by = "day_light";
    }

    if ("" == $order) {
        $order = "asc";
    }

    if (strcmp($start, '') == 0) {
        $start = 0;
    }

    $limit = 25; // max display per page..
    settype($limit, "float");
    $qry = "SELECT * FROM `world`.`time_zones` ORDER BY " . $by . " " . $order . " LIMIT $start,$limit";

    if ("day_light" == $by) {
        $by = "daylight_savings";
    }

    display_entries($start, $limit);
    draw_header();

    $shaded = true;
    $result = $DB->query($qry);
    $id     = '';
    while ($row = $DB->fetch_array($result)) {
        $id = $row['id'];
        // print each of the rows of the array..
        if (!$shaded) {
            echo "<tr class=\"TableRow\" onMouseOver=\"pviiClassNew(this,'TableRowOver')\" onMouseOut=\"pviiClassNew(this,'TableRow')\">";
            $shaded = !$shaded;
        } else {
            echo "<tr class=\"TableRowAlt\" onMouseOver=\"pviiClassNew(this,'TableRowOver')\" onMouseOut=\"pviiClassNew(this,'TableRowAlt')\">\n";
            $shaded = !$shaded;
        }
        echo '<td align="center">' . $row['id'] . '</td>';
        echo '<td align="center">' . $row['name'] . '</td>';
        echo '<td align="center">' . $row['offset'] . '</td>';
        if ('y' == $row['day_light']) {
            echo '<td align="center">YES</td>';
        } else {
            echo '<td align="center">NO</td>';
        }

        echo '<td align="center">' . $row['country'] . '</td>';
        echo '<td align="center"><a href="times.php?action=edit&id=' . $id . '">Edit</td>';
        echo '<td align="center"><a href="times.php?action=delete&id=' . $id . '" onClick="return conf()">Delete</a></td>';
        echo '<td align="center"><a href="times.php?action=update&id=' . $id . '">Update</td>';
    }

}

/**********************************************************************************************/
// This function updates the timezones present in the database.
/**********************************************************************************************/
function update_times($id = "")
{
    global $DB;
    $sql = "";
    if ("" == $id) {
        $sql = "SELECT * FROM `world`.`time_zones` ORDER BY name";
    } else {
        $sql = "SELECT * FROM `world`.`time_zones` WHERE id = $id";
    }

    $res = $DB->query($sql);
    $msg = array();
    while ($row = $DB->fetch_array($res)) {
        $name     = $row['name'];
        $country  = $row['country'];
        $s        = "SELECT * FROM `world`.`country` WHERE code = '$country'";
        $x        = $DB->query_first($s);
        $daylight = $row['day_light'];
        $offset   = $row['offset'];
        if ('Georgetown' == $name) {
            $name = $name . ' (' . $x['name'] . ')';
        }
        // need to do this as Georgetown is the name of the capital city for 2 countries, Cayman Islands and Guyana...
        $link = '';
        if ($r = $DB->query_first(sprintf(
            "SELECT `link` FROM `world`.`city_list` WHERE `city` = %s LIMIT 1",
            $DB->escape($name)
        ), MYSQLI_ASSOC)) {
            $link = $r['link'];
        }

        if ($link) {
            $info  = file_get_contents($link);
            $tok   = strtok($info, "<>");
            $in_ar = array();
            while ($tok) {
                $in_ar[] = $tok;
                $tok     = strtok("<>");
            }
            for ($i = 0; $i < sizeof($in_ar); $i++) {
                if (strcmp($in_ar[$i], "Standard time zone:") == 0) {
                    $str             = $in_ar[$i + 3];
                    list($utc, $gmt) = explode('/', $str);
                    if (strcmp($utc, "No UTC") == 0) {
                        $offset = "0";
                        if (strcmp($in_ar[$i + 9], "Daylight saving time:") == 0) {
                            $daylight = 'y';
                        } else {
                            $daylight = 'n';
                        }

                    } else {
                        $sign            = $gmt{4};
                        $off             = "";
                        list($gmt, $off) = preg_split('/[+-]/', $gmt);
                        $offset          = str_replace("hour", "", $off); //deletes hour from the string
                        $offset          = str_replace("s", "", $offset); //deletes s, when hours is used in the string
                        if (strlen($offset) > 3) {
                            $offset = number_format($offset, 1, '.', '') + 0.5;
                        } else {
                            $offset = number_format($offset, 0, '.', '');
                        }

                        if (strcmp($in_ar[$i + 9], "Daylight saving time:") == 0) {
                            $daylight = 'y';
                        } else {
                            $daylight = 'n';
                        }

                        if ('-' == $sign) {
                            $offset = $sign . $offset;
                        }

                    }
                }
            }

            $DB->query(sprintf(
                "UPDATE %s SET `day_light` = %s, `offset` = %s WHERE `name` = %s",
                "`world`.`time_zones`",
                $DB->escape($daylight),
                $DB->escape($offset),
                $DB->escape($name)
            ));
        } else {
            $msg[] = $name . ', ' . $x['name'];
        }

    }
    if (!empty($msg)) {
        if ("" == $id) {
            echo '<br><h5>Following Cities couldn\'t be updated. Please Update them individually.<ul>';
        } else {
            echo '<br><h5>Sorry the city couldn\'t be updated automatically.<ul>';
        }

        for ($i = 0; $i < sizeof($msg); $i++) {
            echo '<li>' . $msg[$i] . '</li>';
        }

        echo '</ul></h5>';
    }
    echo '<h4>Update is now Complete</h4>';
}

/**********************************************************************************************/
// This function draws the header for the display of the sections.
/**********************************************************************************************/
function draw_header()
{
    global $fields, $order, $by, $query, $max, $at, $page, $script;
    global $status, $website, $print, $action;
    $link = "website=$website&status=$status";
    echo '<br><table align="center" cellpadding="4" cellspacing="1">';
    echo '<tr bgcolor="#3399FF">';
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        echo '<td align="center" ' . ($by == $field ? 'bgcolor="#FF9900"' : '') . '><b>';
        echo "<a href=\"$script?query=" . urlencode($query) . "&max=$max&action=$action&at=$at&page=$page";
        echo "&$link&by=$field&order=" . ("desc" == $order ? "asc" : "desc") . '" class="td_options">';
        echo ucwords(str_replace('_', ' ', $field));
        echo '</a>';
        if ($by == $field) {
            echo '  <img src="../../' . $order . '.gif" width="7" height="7" border="0">  ';
        }

        echo '</b></td>';
        next($fields);
    }
}

/***********************************************************************************************/
// This function draws the header for the total number of entries present in the database
/***********************************************************************************************/
function display_entries($start, $limit)
{
    global $DB, $fields, $order, $by, $query, $max, $at, $page, $script, $status, $website, $print, $action;
    $qry   = "SELECT COUNT(*) FROM `world`.`time_zones` ORDER BY id DESC LIMIT 0," . $limit;
    $res   = $DB->query_first($qry);
    $pages = '';
    $count = $res[0];
    $limit = number_format($limit, 0, '.', '');
    $count = number_format($count, 0, '.', '');
    $pages = floor($count / $limit);
    if (0 != $count) {
        if ($count < ($start + $limit)) {
            echo '<center><b>Showing: ' . ($start + 1) . ' - ' . ($count) . ' of ' . $count . ' | </b>';
        } else {
            echo '<center><b>Showing: ' . ($start + 1) . ' - ' . ($start + $limit) . ' of ' . $count . ' | </b>';
        }

    }
    $orig = $start;
    $new  = 0;
    for ($i = 0; $i <= $pages; $i++) {
        if (0 == $new && 0 == $orig || $orig == $new) {
            if ($count > ($start + $limit)) {
                echo '<b>' . ($start + 1) . ' - ' . ($start + $limit) . ' | </b>';
            } else {
                echo '<b>' . ($start + 1) . ' - ' . ($count) . ' | </b>';
            }

            $new = $start + $limit;
        } else {
            $link = "action=$action&start=$new&by=$by&order=$order";
            if ($i == $pages) {
                echo '<a href="' . $page . '?' . $link . '">' . ($new + 1) . ' - ' . ($count) . '</a>';
            } else {
                echo '<a href="' . $page . '?' . $link . '">' . ($new + 1) . ' - ' . ($new + $limit) . '</a> | ';
            }

            $new += $limit;
        }
    }
    echo '</center>';
}

echo '<link rel="stylesheet" type="text/css" href="../style.css" />';
switch ($action) {
    case "view":
        view();
        break;
    case "add":
        include 'add_time.php';
        break;
    case "edit":
        include 'edit_time.php';
        break;
    case "do_add":
        add();
        break;
    case "do_edit":
        edit();
        break;
    case "delete":
        delete_value();
        break;
    case "update":
        $t_id = $_REQUEST['id'];
        update_times($t_id);
        break;
    case "":
        view();
        break;
}

?>
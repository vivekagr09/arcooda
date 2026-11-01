<?php
include_once __DIR__ . '/common.inc.php';

?><!DOCTYPE html>
<html>
	<head>
		<title>World Summary - Add/Edit Airports</title>
		<script type="text/javascript">
//<![CDATA[
function conf()
{
	if (confirm("Are you sure you to delete the Airport selected")) return true;
	else return false;
}
function pviiClassNew(obj, new_style)
{
	obj.className=new_style;
}
//]]>
		</script>
	</head>
<?php
include_once __DIR__ . "/top.airport.php"; // the header file specified to this section..

$fields['id']      = "center";
$fields['name']    = "center";
$fields['country'] = "center";

function add($posted)
{
    global $DB, $data;
        if ($DB->query(sprintf(
            "INSERT INTO `world`.`airports` (`name`, `country`) VALUES (%s, %s)",
            $DB->escape($posted['num']),
            $DB->escape($posted['data']['country'])
        ))) {
        }

    echo "New Airport(s) Added<br>";
}

/***********************************************************************************************/
// This function edits a section, which is already present in the database..
/***********************************************************************************************/
function edit($posted)
{
    global $DB, $data, $id;

    $DB->query(sprintf(
        "UPDATE `world`.`airports` SET %s WHERE `id` = %d LIMIT 1",
        get_set_values($posted['data']),
        $id
    ));

    echo "Airport Id $id updated<br>";

}

/***********************************************************************************************/
// This function deletes a section, which is already present in the database..
/***********************************************************************************************/
function delete_value()
{
    global $DB, $data, $user, $id;
    $statement = "DELETE FROM `world`.`airports` WHERE id= '$id'";
    $DB->query($statement);
    echo "Airport Id $id deleted<br>";
}

/**********************************************************************************************/
// This function gets the data from the database and displays it.
/**********************************************************************************************/
function view()
{
    global $DB, $by, $order, $start;

    $country_list = getCountryList(false);

    if ("" == $by) {
        $by = "country";
    }

    if ("" == $order) {
        $order = "asc";
    }

    $orderby = 'ORDER BY ';
    $orderby .= "`s`.`" . $by . "` " . $order;

    if (strcmp($start, '') == 0) {
        $start = 0;
    }

    $limit = 25; // max display per page..
    settype($limit, "float");

    display_letter();
    draw_header();

    $shaded = true;
    $id     = '';
    if ($query = $DB->query(sprintf(
        "SELECT `s`.* FROM `world`.`airports` AS `s` %s LIMIT %d, %d",
        $orderby,
        $start,
        $limit
    ))) {
        while ($row = $DB->fetch_array($query)) {
            $row['country_full'] = $country_list[$row['country']] ?? $row['country'];

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
            echo '<td align="center">' . $row['country_full'] . '</td>';
            echo '<td align="center"><a href="airports.php?action=edit&id=' . $id . '">Edit</td>';
            echo '<td align="center"><a href="airports.php?action=delete&id=' . $id . '" onClick="return conf()">Delete</a></td>';
        }
        $DB->free_result($query);
    }
    echo '</td></table><br><br>';
    display_entries($start, $limit);

}

/**********************************************************************************************/
// This function draws the header for the display of the sections.
/**********************************************************************************************/
function draw_header()
{
    global $fields, $order, $by, $max, $at, $page, $script;
    global $status, $website, $action, $value;
    $link = "website=$website&status=$status";
    echo '<br><table align="center" cellpadding="4" cellspacing="1">';
    echo '<tr bgcolor="#3399FF">';
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        echo '<td align="center" ' . ($by == $field ? 'bgcolor="#FF9900"' : '') . '><b>';
        echo "<a href=\"$script?query=" . urlencode($_REQUEST['query'] ?? '') . "&max=$max&action=$action&value=$value&at=$at&page=$page";
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

function show_alpha($letter)
{
    global $DB, $order, $by;
    if ('' == $by) {
        $by = 'country';
    }

    if ('' == $order) {
        $order = 'asc';
    }

    if ('country' == $by) {
        $by = 'cname';
    }

    $orderby = 'ORDER BY ';
    ('cname' == $by) ? $orderby .= "c.name $order" : $orderby .= "s.$by $order";
    if ('cname' == $by) {
        $by = 'country';
    }

    display_letter();
    //display_entries($start,$limit);
    draw_header();
    $shaded = true;
    $id     = '';
    if ($query = $DB->query(sprintf(
        "SELECT `c`.`name` AS `country_full`, `a`.* FROM `world`.`country` AS `c` INNER JOIN `world`.`airports` AS `a` ON `a`.`country` = `c`.`code` WHERE `c`.`name` LIKE %s %s",
        $DB->escape($letter . '%'),
        $orderby
    ))) {
        while ($row = $DB->fetch_array($query)) {
            // print each of the rows of the array..
            $id = $row['id'];
            if (!$shaded) {
                echo "<tr class=\"TableRow\" onMouseOver=\"pviiClassNew(this,'TableRowOver')\" onMouseOut=\"pviiClassNew(this,'TableRow')\">";
            } else {
                echo "<tr class=\"TableRowAlt\" onMouseOver=\"pviiClassNew(this,'TableRowOver')\" onMouseOut=\"pviiClassNew(this,'TableRowAlt')\">\n";
            }

            $shaded = !$shaded;
            echo '<td align="center">' . $row['id'] . '</td>';
            echo '<td align="center">' . $row['name'] . '</td>';
            echo '<td align="center">' . $row['country_full'] . '</td>';
            echo '<td align="center"><a href="airports.php?action=edit&id=' . $id . '">Edit</td>';
            echo '<td align="center"><a href="airports.php?action=delete&id=' . $id . '" onClick="return conf()">Delete</a></td>';
        }
        $DB->free_result($query);
    }
}

function display_letter()
{
    global $DB;
    echo '<table width=95% align=center cellpadding=7><td bgcolor=#FAFAFA><div align="center"><b>By Country Letter :</b> ';
    for ($i = 'A';; $i++) {
        if ($row = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM `world`.`airports` AS `a` INNER JOIN `world`.`country` AS `c` ON `c`.`code` = `a`.`country` WHERE `c`.`name` LIKE %s LIMIT 1",
            $DB->escape($i . '%')
        ), MYSQLI_ASSOC) && $row['total'] > 0) {
            echo '<a href="?action=alpha&value=' . $i . '">' . $i . '</a>&nbsp;';
        } else {
            echo '<font color="#999999">' . $i . '</font>&nbsp;';
        }

        if ("Z" == $i) {
            break;
        }

    }
    echo '</div></td></table>';
}

/***********************************************************************************************/
// This function draws the header for the total number of entries present in the database
/***********************************************************************************************/
function display_entries($start, $limit)
{
    global $DB;
    global $order, $by, $page, $action;

    $count = 0;
    if ($row = $DB->query_first(sprintf(
        "SELECT COUNT(*) AS `total` FROM `world`.`airports` ORDER BY `id` DESC LIMIT 1"
    ), MYSQLI_ASSOC)) {
        $count = $row['total'];
    }

    $pages = '';
    $limit = number_format($limit, 0, '.', '');
    $count = number_format($count, 0, '.', '');
    $pages = floor($count / $limit);
    if (0 != $count) {
        if ($count < $limit) {
            echo '<center><b>Showing: ' . ($start + 1) . ' - ' . ($count) . ' of ' . $count . ' | </b>';
        } else {
            echo '<center><b>Showing: ' . ($start + 1) . ' - ' . ($start + $limit) . ' of ' . $count . ' | </b>';
        }

    }
    $orig = $start;
    $new  = 0;
    for ($i = 0; $i <= $pages; $i++) {
        if (0 == $new && 0 == $orig || $orig == $new) {
            if ($count > $limit) {
                echo '<b>' . ($start + 1) . ' - ' . ($start + $limit) . ' | </b>';
            }

            $new = $start + $limit;
        } else {
            $link = "action=$action&start=$new&by=$by&order=$order";
            echo '<a href="' . $page . '?' . $link . '">' . ($new + 1) . ' - ' . ($new + $limit) . '</a> | ';
            $new += $limit;
        }
    }
    echo '</center>';
}
?>


<?php
echo '<link rel="stylesheet" type="text/css" href="../style.css" />';
$action = $_REQUEST['action'];
$value  = $_REQUEST['value'];
switch ($action) {
    case "view":
        view();
        break;
    case "add":
        include 'add_airport.php';
        break;
    case "edit":
        include 'edit_airport.php';
        break;
    case "do_add":
        add($posted);
        break;
    case "do_edit":
        edit($posted);
        break;
    case "delete":
        delete_value();
        break;
    case "alpha":
        show_alpha($value);
        break;
    case "":
        view();
        break;
}

?>
<?php
include_once __DIR__ . '/common.inc.php';

?><!DOCTYPE html>
<html>
	<head>
		<title>World Summary - Add/Edit Currency values</title>
	</head>
<script language="JavaScript" type="text/javascript">
function conf(){
	if(confirm('Are you sure you to delete the Currency selected'))
		return true;
	else
		return false;
}
function pviiClassNew(obj, new_style) { //v2.6 by PVII
	obj.className=new_style;
}
</script>
<?php
include_once 'top.currency.php'; // the header file specified to this section..

$fields['id']   = "center";
$fields['name'] = "center";
$fields['code'] = "center";

function add()
{
    global $DB, $data;

    if ($DB->query(sprintf(
        "INSERT INTO `world`.`currency` (%s) VALUES (%s)",
        get_fields($data),
        get_values($data)
    ))) {
        echo "New currency value Added<br>";
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
    $statement = "UPDATE currency SET " . get_set_values($data) . " WHERE id = '$id'";
    $DB->query($statement);
    echo "Currency Id $id updated<br>";

}

/***********************************************************************************************/
// This function deletes a section, which is already present in the database..
/***********************************************************************************************/
function delete_value()
{
    global $DB, $data, $user, $id;
    $statement = "DELETE FROM `world`.`currency` WHERE id= '$id'";
    $DB->query($statement);
    echo "Currency Id $id deleted<br>";
}

/**********************************************************************************************/
// This function gets the data from the database and displays it.
/**********************************************************************************************/
function view()
{
    global $DB, $by, $order, $print, $fields, $start;
    if ("" == $by) {
        $by = "id";
    } else if ("voltage_value" == $by) {
        $by = "value";
    }

    if ("" == $order) {
        $order = "asc";
    }

    if (strcmp($start, '') == 0) {
        $start = 0;
    }

    $limit = 25; // max display per page..
    $qry   = "SELECT * FROM `world`.`currency` ORDER BY " . $by . " " . $order . " LIMIT " . $start . "," . $limit;
    settype($limit, "float");
    display_entries($start, $limit);
    if ("value" == $by) {
        $by = "voltage_value";
    }

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
        echo '<td align="center">' . $row['code'] . '</td>';
        echo '<td align="center"><a href="currency.php?action=edit&id=' . $id . '">Edit</td>';
        echo '<td align="center"><a href="currency.php?action=delete&id=' . $id . '" onClick="return conf()">Delete</a></td>';
    }

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
    $qry   = "SELECT COUNT(*) FROM `world`.`currency` ORDER BY id DESC LIMIT 0," . $limit;
    $res   = $DB->query_first($qry);
    $pages = '';
    $count = $res[0];
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
switch ($action) {
    case "view":
        view();
        break;
    case "add":
        include 'add_currency.php';
        break;
    case "edit":
        include 'edit_currency.php';
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
    case "":
        view();
        break;
}

?>
<?php
include_once __DIR__ . '/common.inc.php';

?><!DOCTYPE html>
<html>
	<head>
		<title>World Summary - Add/Edit Plug Types</title>
	</head>
<script language="JavaScript" type="text/javascript">
function conf(){
	if(confirm('Are you sure you to delete the Plug Type selected'))
		return true;
	else
		return false;
}
function pviiClassNew(obj, new_style) { //v2.6 by PVII
	obj.className=new_style;
}
</script>
<?php
include_once 'top.plugs.php'; // the header file specified to this section..

$fields['id']        = "center";
$fields['plug_type'] = "center";

function add()
{
    global $DB, $data;

    if ($DB->query(sprintf(
        "INSERT INTO `world`.`plug_types` (%s) VALUES (%s)",
        get_fields($data),
        get_values($data)
    ))) {
        $id = $DB->insert_id();
        if ("" != $_FILES['file_path']['name']) {
            $plug_path = $_SERVER['DOCUMENT_ROOT'] . "/webadmin/home_common/images/plugs/";
            $target_path = $plug_path . basename($_FILES['file_path']['name']);
            if (move_uploaded_file($_FILES['file_path']['tmp_name'], $target_path)) {
                $DB->query(sprintf(
                    "UPDATE `world`.`plug_types` SET `file_path` = %s WHERE `id` = %d LIMIT 1",
                    $DB->escape($target_path),
                    $id
                ));
            }
        }
        echo "New Plug Type Added<br>";
        return $id;
    }

    return 0;
}

/***********************************************************************************************/
// This function edits a section, which is already present in the database..
/***********************************************************************************************/
function edit()
{
    global $DB, $data, $user, $id;

    $plug_path = $_SERVER['DOCUMENT_ROOT'] . "/webadmin/home_common/images/plugs/";
    $statement = "UPDATE plug_types SET " . get_set_values($data) . " WHERE id = '$id'";
    $DB->query($statement);

    if ("" != $_FILES['new_plug']['name']) {
        $target_path = $plug_path . basename($_FILES['new_plug']['name']);
        $qry         = "UPDATE plug_types SET file_path = '$target_path' WHERE id = '$id'";
        $DB->query($qry);

        /*Commented out; code doesn't work on local server --- UNCOMMENT WHEN UPLODING TO LIVE SERVER*/
        if (move_uploaded_file($_FILES['new_plug']['tmp_name'], $target_path)) {
            echo "The plug image " . basename($_FILES['new_plug']['name']) . " has been uploaded successfully";
        } else {
            echo "There was an error uploading the plug image, please try again!";
        }

    }
    echo "Plug Type Id $id updated<br>";

}

/***********************************************************************************************/
// This function deletes a section, which is already present in the database..
/***********************************************************************************************/
function delete_value()
{
    global $DB, $data, $user, $id;
    $statement = "DELETE FROM `world`.`plug_types` WHERE id= '$id'";
    $DB->query($statement);
    echo "Plug Type Id $id deleted<br>";
}

/**********************************************************************************************/
// This function gets the data from the database and displays it.
/**********************************************************************************************/
function view()
{
    global $DB, $by, $order, $print, $fields;
    if ("" == $by) {
        $by = "id";
    } else if ("plug_type" == $by) {
        $by = "value";
    }

    if ("" == $order) {
        $order = "asc";
    }

    $qry = "SELECT * FROM `world`.`plug_types` ORDER BY " . $by . " " . $order;
    if ("value" == $by) {
        $by = "plug_type";
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
        echo '<td align="center">' . $row['value'] . '</td>';
        echo '<td align="center"><a href="plugs.php?action=edit&id=' . $id . '">Edit</td>';
        echo '<td align="center"><a href="plugs.php?action=delete&id=' . $id . '" onClick="return conf()">Delete</a></td>';
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

echo '<link rel="stylesheet" type="text/css" href="../style.css" />';
switch ($action) {
    case "view":
        view();
        break;
    case "add":
        include 'add_plugs.php';
        break;
    case "edit":
        include 'edit_plugs.php';
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
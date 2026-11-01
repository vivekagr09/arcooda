<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

include_once __DIR__ . '/config.inc.php';

$script              = 'type_admin.php?table=' . $table;
$fields["id"]        = 'center';
$fields["type_name"] = 'left';
$actions[]           = '<a href="$script&action=edit&id=$id">Edit</a>';
$actions[]           = '<a href="$script&action=delete&id=$id">Delete</a>';
$items               = $DB->query_first("SELECT COUNT(*) AS `total` FROM `" . $table . "_types ");
$gets                = get_vars();
$img_location        = "images/";
$img_location_abs    = $_SERVER["DOCUMENT_ROOT"] . '/' . $img_location;
?>
<!-- type_admin -->
<form action="<?php echo 'type_admin.php' . $gets ?>" method="post" enctype="multipart/form-data" name="admin_form">
	<br />
	<table width="100%" border="0" cellspacing="2" cellpadding="2"><tr>
		<td bgcolor="#EEEEEE" width="20%"><div align="center"><b><?php echo $table_title ?> Types</b></div></td>
		<td bgcolor="#EEEEEE" width="80%"><div align="right"><a href="<?php echo $script ?>&action=add">Add New <?php echo $table_title ?> Type</a> | <a href="<?php echo $script ?>&query=">List <?php echo $table_title ?> Types (<?php echo $items[total] ?>)</a></div></td>
	</tr></table>
	<p align="right"><input type="text" name="query" size="20" value="<?php echo htmlspecialchars($_REQUEST["query"]) ?>" /> <input type="submit" name="search" value="Search" /></p>
<?php

function search($query)
{
    global $DB, $at, $fields, $order, $by, $script, $location, $type, $table;

    $query = trim($query);

    //keywords
    $arrKeywords = explode(" ", $query);

    //search query by key words

    for ($index = 0; $index < count($arrKeywords); $index++) {
        $word = trim(strtolower($arrKeywords[$index]));

        if (!empty($word)) {
            $extra .= " AND ";
        }

        if (!empty($word)) {
            if (preg_match("/^\\d+$/", $word)) //if its a search by id
            {
                $extra .= "id = '$word'";
            } elseif (preg_match("/^[\\*\\+\\\\]/", $word)) {
                $extra .= "(LOWER(type_name) LIKE '%$word%')";
            } else {
                $extra .= "(LOWER(type_name) REGEXP \"$word\")";
            }
            //else just a normail regular exp. search
        }
    } //end search query keywords

    //search statement
    $statement = "SELECT * FROM " . $table . "_types WHERE 1 ";

    //if max is not yet set
    if (!isset($max)) {
        $result = $DB->query($statement . $extra);
        $max    = $DB->num_rows($result);
    }

    $inc = 50;
    $at  = intval($at);

    if (!isset($order)) {
        $order = 'desc';
    }

    if (!isset($by)) {
        $by = key($fields);
    }

    $order_by = " ORDER BY $by $order";
    $limit    = " LIMIT $at, $inc ";
    $result   = $DB->query($statement . $extra . $order_by . $limit);

    show_links(get_links($max, "$script&query=" . urlencode($query) . "&max=$max&by=$by&order=$order", $inc, 4));

    draw_header_row();
    $shaded = true;
    while ($row = $DB->fetch_array($result)) {
        show_result($row, $shaded = !$shaded);
    }
    draw_header_end();
    //show more links
    show_links(get_links($max, "$script&query=" . urlencode($query) . "&max=$max&by=$by&order=$order", $inc, 4));
}
function draw_header_row()
{
    global $fields, $order, $by, $max, $at, $page, $script;

    reset($fields);

    echo '<br>';

    echo '<table widt="95" align="center">';
    echo '<tr bgcolor="#000066">';
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        echo '<td align="center" ' . ($by == $field ? 'bgcolor="#336699"' : '') . '><b>';
        echo "<a href=\"$script&query=" . urlencode($_REQUEST["query"]) . "&max=$max&at=$at&page=$page";
        echo "&by=$field&order=" . ("desc" == $order ? "asc" : "desc") . '" class="td_options">';
        echo $field;
        echo '</a>';
        if ($by == $field) {
            echo '  <img src="/webadmin/' . $order . '.gif" width="7" height="7" border="0">  ';
        }

        '</b></td>';
        next($fields);
    }
    echo "<td>&nbsp;</td>";
    echo '</tr>';
}
function draw_header_end()
{
    echo '</table><br>';
}
function add_new_parts_type()
{
    global $type_name, $type_image, $header_image, $img_location, $table, $type_blurb;
    global $DB;

    $header_image_name = 'header_' . strtolower(str_replace(' ', '_', htmlspecialchars($type_name)));

    if (is_file($header_image)) {
        $header_img = add_image($header_image, $header_image_name);
    }

    $statement = "INSERT INTO
					" . $table . "_types(
						type_name,
						blurb,
						header_image
					)";

    $statement .= "VALUES (
					'" . htmlspecialchars($type_name) . "' ,
					'" . htmlspecialchars($type_blurb) . "' ,
					'$header_img'
				)";

    $DB->query($statement);

    if (is_file($type_image)) {
        $id = $DB->insert_id();

        $img_dest = 'img_type_' . $table . '_' . $id;

        $image = add_image($type_image, $img_dest);

        $statement = "UPDATE " . $table . "_types SET type_image = '$image' WHERE id = '$id'";

        $DB->query($statement);
    }

    echo "New Part Type ID:$id added.<br>";
}
function edit_parts_type()
{

    global $type_name, $type_image, $header_image, $id, $img_location, $table, $type_blurb;
    global $DB;

    $header_image_name = 'header_' . strtolower(str_replace(' ', '_', htmlspecialchars($type_name)));

    if (is_file($header_image)) {
        $header_img        = add_image($header_image, $header_image_name);
        $header_img_clause = ", header_image = '$header_img' ";
    }

    $img_dest = 'img_type_' . $table . '_' . $id;

    $image_clause = '';

    if (is_file($type_image)) {
        $image = add_image($type_image, $img_dest);

        if ('' != $image) {
            $image_clause = ", type_image = '" . addslashes(htmlspecialchars($image)) . "'";
        }

    }

    $statement = "UPDATE " . $table . "_types SET
					type_name = '" . htmlspecialchars($type_name) . "',
					blurb = '" . htmlspecialchars($type_blurb) . "'
					$header_img_clause
					$image_clause
				WHERE id = '$id'";

    $DB->query($statement);
    echo "$id:" . ($type_name) . " updated<br>";
}
function delete_parts_type()
{
    global $id, $DB, $table;

    $statement = "DELETE FROM " . $table . "_types WHERE id = '$id'";
    $DB->query($statement);
    echo "ID:$id Deleted.<br>";
}
function show_result($cols, $shaded)
{
    global $fields, $actions, $script;

    reset($fields);
    $id = $cols[key($fields)];
    echo $shaded ? '<tr>' : '<tr bgcolor="#EEEEEE">';
    for ($i = 0; $i < sizeof($fields); $i++) {
        $key = key($fields);
        echo "<td align=\"" . $fields[$key] . "\">";
        echo $cols[$key] . '</td>';
        next($fields);
    }
    echo '<td nowrap>';

    $type = str_replace(' ', '_', strtolower($cols['type_name']));
    for ($i = 0; $i < sizeof($actions); $i++) {
        if (0 != $i) {
            echo " | ";
        }

        $str = addslashes($actions[$i]);
        eval("\$str = \"$str\";");
        echo "$str";

    }
    echo '</td>';
    echo "</tr>";
}

if (trim($_REQUEST["query"]) == '') {
    switch ($action) {
        case "add":
            include __DIR__ . '/type_add_form.inc.php';
            break;
        case "edit":
            include __DIR__ . '/type_edit_form.inc.php';
            break;
        case "delete":
            include __DIR__ . '/type_delete_form.inc.php';
            break;
        case "do_add":
            add_new_parts_type();
            break;
        case "do_edit":
            edit_parts_type();
            break;
        case "do_delete":
            delete_parts_type();
            break;
    }
}

if (isset($_REQUEST["query"])) {
    $_REQUEST["query"] = htmlspecialchars($_REQUEST["query"]);
    search(addslashes(addslashes(($_REQUEST["query"]))));
}
?>
</form>

<!-- /type_admin -->
<?php
include $_SERVER["DOCUMENT_ROOT"] . "/webadmin/footer.inc.php";
?>
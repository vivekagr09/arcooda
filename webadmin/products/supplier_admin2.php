<?php
$section_code = "PRD_CFG";

include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

if (!isset($table)) {
    include_once __DIR__ . '/footer.inc.php';
    exit();
}

$script = 'supplier_admin.php?table=' . $table;

foreach ($DB->table_fields($table) as $v) {
    $fields[$v] = 'left';
}

$id_field = key($fields);

reset($fields);

$actions[] = '<a href="$script&action=edit&id=$id">Edit</a>';
$actions[] = '<a href="$script&action=delete&id=$id">Delete</a>';

$items = $DB->query_first("SELECT COUNT(*) as total FROM $table");

$gets = get_vars();

$img_location     = "images/";
$img_location_abs = $_SERVER['DOCUMENT_ROOT'] . '/' . $img_location;

$table_title = ucwords(str_replace('_', ' ', $table));

?>

<!-- parts admin -->

<form action="<?php echo $script ?>" method="post" enctype="multipart/form-data" name="admin_form">
	<br>
      <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td bgcolor="#EEEEEE" width="20%">
            <div align="center"><b><?php echo $table_title ?></b></div>
          </td>
          <td bgcolor="#EEEEEE" width="80%">
            <div align="right">
			<a href="<?php echo $script ?>&action=add">Add New <?php echo $table_title ?></a> |
			<a href="<?php echo $script ?>&query=">List <?php echo $table_title . ' (' . $items[total] . ')' ?></a>
			</div>
          </td>
        </tr>
      </table>
<!-- search box -->
      <p align="right">
        <input type="text" name="query" size="20" value="<?php echo htmlspecialchars($_REQUEST["query"]) ?>">
        <input type="submit" name="search" value="Search">
      </p>
<!-- //search box -->

<?php

function search($query)
{
    global $DB, $at, $order, $by, $script, $location, $type, $table, $fields, $id_field;

    $query = trim($query);

    //keywords
    $arrKeywords = explode(" ", $query);

    //search query by key words

    for ($index = 0; $index < count($arrKeywords); $index++) {
        reset($fields);
        $word = trim(strtolower($arrKeywords[$index]));

        if (!empty($word)) {
            $extra .= " AND ";
        }

        if (!empty($word)) {
            $extra .= '(';
            for ($i = 0; $i < count($fields); $i++) {
                $key = key($fields);

                $extra .= " LOWER($key) LIKE '%$word%' ";

                if ($i >= 0 && $i < count($fields) - 1) {
                    $extra .= ' OR ';
                }

                next($fields);
            }

            $extra .= ')';
        }
    } //end search query keywords

    //search statement
    $statement = "SELECT * FROM $table WHERE 1 ";

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

    reset($fields);

    if (!isset($by)) {
        $by = $id_field;
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

function add_new()
{
    global $field_data, $table_title, $table;
    global $DB;

    $insert_fields = '(';
    $value_fields  = '(';

    for ($i = 0; $i < count($field_data); $i++) {
        $key = key($field_data);

        $insert_fields .= $key;
        $value_fields .= "'" . addslashes(htmlspecialchars($field_data[$key])) . "'";

        if ($i < count($field_data) - 1) {
            $insert_fields .= ', ';
            $value_fields .= ', ';
        }

        next($field_data);
    }
    $insert_fields .= ')';
    $value_fields .= ')';

    reset($field_data);

    $statement = "INSERT INTO $table $insert_fields VALUES $value_fields";

    $DB->query($statement);

    $id = $DB->insert_id();

    echo "New $table_title ID:$id added.<br>";
}

function edit()
{

    global $field_data, $table_title, $table, $id, $id_field;
    global $DB;

    for ($i = 0; $i < count($field_data); $i++) {
        $key = key($field_data);

        $update_fields .= "$key = '" . addslashes(htmlspecialchars($field_data[$key])) . "'";

        if ($i < count($field_data) - 1) {
            $update_fields .= ', ';
        }

        next($field_data);
    }

    reset($field_data);

    $statement = "UPDATE $table SET $update_fields WHERE $id_field = '$id'";

    $DB->query($statement);

    echo "$id: updated<br>";
}

function delete()
{
    global $id, $DB, $table, $id_field;

    $statement = "DELETE FROM $table WHERE $id_field = '$id'";
    $DB->query($statement);

    echo "ID:$id Deleted.<br>";
}

function show_result($cols, $shaded)
{
    global $fields, $actions, $script, $table;

    reset($fields);
    $id = $cols[key($fields)];

    if (locked($table, $id)) {
        echo '<tr bgcolor="pink">';
    } else {
        echo $shaded ? '<tr>' : '<tr bgcolor="#EEEEEE">';
    }

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
            include __DIR__ . '/supplier_add_form.inc.php';
            break;
        case "edit":
            include __DIR__ . '/generic_edit_form.inc.php';
            break;
        case "delete":
            include __DIR__ . '/generic_delete_form.inc.php';
            break;
        case "do_add":
            add_new();
            break;
        case "do_edit":
            if (!locked($table, $id, true)) {
                edit();
            }

            break;
        case "do_delete":
            if (!locked($table, $id, true)) {
                delete();
            }

            break;

    }
}

if (isset($_REQUEST["query"])) {
    $_REQUEST["query"] = htmlspecialchars($_REQUEST["query"]);
    search(addslashes(addslashes(($_REQUEST["query"]))));
}

//load_hidden_vars();

?>
</form>

<!--//parts admin -->
<?php
include __DIR__ . '/footer.inc.php';

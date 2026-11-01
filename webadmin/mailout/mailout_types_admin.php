<?php
/***********************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:
 ** Last changed..:
 ** Notes.........:
 ***********************************************************************/
$section_code = "EMAILS";

include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$table = 'mailout_types';

$script = 'mailout_types_admin.php?table=' . $table;

$fields['id']        = 'left';
$fields['type_name'] = 'left';
$fields['text_desc'] = 'left';

$search_fields[] = 'id';
$search_fields[] = 'type_name';

$id_field = 'id';

$actions[] = '<a href="$script&action=edit&id=$id">Edit</a>';
$actions[] = '<a href="$script&action=delete&id=$id">Delete</a>';

$statement = "SELECT COUNT(*) as total FROM $table";

$items = $DB->query_first($statement);

$img_location     = 'images/mailout/';
$img_location_abs = $_SERVER['DOCUMENT_ROOT'] . '/' . $img_location;

check_image_dir($img_location_abs, true);

$table_title = ucwords(str_replace('_', ' ', $table));

?>

<!-- <?=$table_title?> admin -->

<form accept-charset="utf-8" action="<?=$script?>" method="post" enctype="multipart/form-data" name="admin_form">
	<br>
      <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td bgcolor="#EEEEEE" width="20%">
            <div align="center"><b><?=$table_title?></b></div>
          </td>
          <td bgcolor="#EEEEEE" width="80%">
            <div align="right">
			<a href="<?=$script?>&action=add">Add New <?=$table_title?></a> |
			<a href="<?=$script?>&query=">List <?=$table_title . ' (' . $items['total'] . ')'?></a>
			</div>
          </td>
        </tr>
      </table>
<!-- search box -->
      <p align="right">
        <input type="text" name="query" size="20" value="<?=stripslashes($_REQUEST['query'])?>">
        <input type="submit" name="search" value="Search">
      </p>
<!-- //search box -->

<?php

$max = $_GET['max'];

function search($query)
{
    global $DB, $script, $max, $table, $fields, $id_field, $search_fields;

    $at    = $_GET['at'];
    $order = $_GET['order'];
    $by    = $_GET['by'];

    $query = trim($query);

    //keywords
    $arrKeywords = explode(" ", $query);

    //search query by key words
    for ($index = 0; $index < count($arrKeywords); $index++) {
        $word = trim(strtolower($arrKeywords[$index]));

        if (!empty($word) && '|' != $word) {
            $extra .= " AND ";

            //do a regular expression search on each of the search fields, case insensitive
            for ($i = 0; $i < count($search_fields); $i++) {
                $extra .= 0 == $i ? '(' : ' OR ';
                $extra .= " LOWER($search_fields[$i]) REGEXP \"$word\" ";
            }
            if (count($search_fields) > 0) {
                $extra .= ')';
            }

        }
    } //end search query keywords

    //search statement
    $statement = "SELECT * FROM $table WHERE  1 ";

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

    $num_rows = $DB->num_rows($result);

    if ($num_rows > 0) {
        show_links(get_links($max, "$script&query=" . urlencode($query) . "&max=$max&by=$by&order=$order", $inc, 4));

        draw_header_row();
        $shaded = false;
        while ($row = $DB->fetch_array($result)) {
            show_result($row, $shaded = !$shaded);
        }
        draw_header_end();
        //show more links
        show_links(get_links($max, "$script&query=" . urlencode($query) . "&max=$max&by=$by&order=$order", $inc, 4));
    } else {
        echo '<div align="center">No records</div>';
    }
}

function draw_header_row()
{
    global $fields, $script, $max;

    $order = $_GET['order'];
    $by    = $_GET['by'];
    $at    = $_GET['at'];
    $page  = $_GET['page'];

    if (null == $by || '' == $by) {
        $by = 'id';
    }

    if (null == $order || '' == $order) {
        $order = 'desc';
    }

    reset($fields);

    echo '<br>';

    echo '<table align="center" cellspacing="1" cellpadding="3">' . "\n";
    echo '<tr bgcolor="#000066">' . "\n";
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        echo '<td align="center" ' . ($by == $field ? 'bgcolor="#336699"' : '') . '><b>';
        echo "<a href=\"$script&query=" . urlencode($_REQUEST['query']) . "&max=$max&at=$at&page=$page";
        echo "&by=$field&order=" . ("desc" == $order ? "asc" : "desc") . '" class="td_options">';
        echo $field;
        echo '</a>';
        if ($by == $field) {
            echo '  <img src="' . $order . '.gif" width="7" height="7" border="0">  ';
        }

        echo '</b></td>' . "\n";
        next($fields);
    }
    echo "<td>&nbsp;</td>\n";
    echo '</tr>' . "\n";

}

function draw_header_end()
{
    echo '</table><br>';
}

function do_add()
{
    global $table_title, $data, $images, $table, $DB, $img_location;

    $statement = "INSERT INTO $table (" . get_fields($data) . ") VALUES (" . get_values($data) . ")";
    $result    = $DB->query($statement);

    $id = $DB->insert_id($result);

    for ($i = 0; $i < count($images); $i++) {
        $key = key($images);

        if (is_file($images[$key])) {
            $img = add_image($images[$key], $key . '_' . $id);

            if ($img) {
                $DB->query("UPDATE $table SET $key = '$img' WHERE id = '$id'");
            }
        }
        next($images);
    }

    echo "New $table_title added.<br>";
}

function do_edit()
{

    global $table_title, $table, $data, $images, $DB, $img_location;

    $id = $_REQUEST['id'];

    $statement = "UPDATE $table SET " . get_set_values($data) . " WHERE id = '$id'";
    $result    = $DB->query($statement);

    for ($i = 0; $i < count($images); $i++) {
        $key = key($images);

        if (is_file($images[$key])) {
            $img = add_image($images[$key], $key . '_' . $id);

            if ($img) {
                $DB->query("UPDATE $table SET $key = '$img' WHERE id = '$id'");
            }
        }
        next($images);
    }

    echo "$id: updated<br>";
}

function do_delete()
{
    global $DB, $table, $id_field;

    $id = $_REQUEST['id'];

    $statement = "DELETE FROM $table WHERE $id_field = '$id'";
    $DB->query($statement);

    echo "Product Type ID:$id Deleted.<br>";

}

function show_result($cols, $shaded)
{
    global $fields, $actions, $script;

    reset($fields);
    $id = $cols[key($fields)];
    if ($shaded) {
        echo "<tr bgcolor=\"#EEEEEE\">\n";
    } else {
        echo "<tr bgcolor=\"#FFFFFF\">\n";
    }
    for ($i = 0; $i < sizeof($fields); $i++) {
        $key = key($fields);
        echo "<td align=\"" . $fields[$key] . "\">";
        echo stripslashes($cols[$key]) . '</td>';
        next($fields);
    }
    echo '<td nowrap>';

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

if (trim($_REQUEST['query']) == '') {
    switch ($_REQUEST['action']) {
        case "add":
            include 'mailout_types_add.inc.php';
            break;
        case "edit":
            include 'mailout_types_edit.inc.php';
            break;
        case "delete":
            include '../generic_delete_form.inc.php';
            break;
        case "do_add":
            do_add();
            break;
        case "do_edit":
            do_edit();
            break;
        case "do_delete":
            do_delete();
            break;

    }
}

if (isset($_REQUEST['query'])) {
    search(addslashes($_REQUEST['query']));
}

//load_hidden_vars();

?>
</form>
<!--//<?=$table_title?> admin -->
<?php
include '../footer.inc.php';

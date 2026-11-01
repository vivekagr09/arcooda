<?php
/**
 * @var string[] $allowed_sections
 */
include_once __DIR__ . '/common.inc.php';

global $DB;

$table  = '`highwaygroup`.`users`';
$script = 'staff_admin.php';

$fields = [];
foreach ($DB->table_fields($table) as $k => $v) {
    $fields[$k] = 'left';
}
reset($fields);

$actions[] = '<a href="$script&action=edit&id=$id">Edit</a>';
if (has_permission('USERS', $allowed_sections)) {
    $actions[] = '<a href="admin.php?action=delete&id=$id">Delete</a>';
}

$items = $DB->query_first(sprintf(
    "SELECT COUNT(*) AS `total` FROM %s",
    $table
), MYSQLI_ASSOC);

$img_location     = "images/";
$img_location_abs = $_SERVER['DOCUMENT_ROOT'] . '/' . $img_location;

$table_title = ucwords(str_replace('_', ' ', $table));

?>

<!-- parts admin -->

<form action="<?=$script?>" method="post" enctype="multipart/form-data" name="admin_form">
	<br />
      <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td bgcolor="#EEEEEE" width="20%">
            <div style="text-align:center"><b><?=$table_title?></b></div>
          </td>
          <td bgcolor="#EEEEEE" width="80%">
            <div style="text-align:right">
<?php
if (has_permission('USERS', $allowed_sections)) {
?>
				<a href="admin.php?action=add">Add New <?=$table_title?></a> |
<?php
}
?>
				<a href="<?=$script?>&query=">List <?=$table_title . ' (' . $items['total'] . ')'?></a>
			</div>
          </td>
        </tr>
      </table>
<!-- search box
      <p style="text-align:right">
        <input type="text" name="query" size="20" value="<?=htmlspecialchars($_REQUEST['query'])?>">
        <input type="submit" name="search" value="Search">
      </p>
//search box -->

<?php

/**
 * @param string $query
 *
 * @return void
 */
function search($query)
{
    global $DB, $at, $order, $by, $script, $location, $type, $table, $fields;

    $query = trim($query);

    //keywords
    $arrKeywords = explode(" ", $query);
    $extra       = '';

    //search query by key words

    for ($index = 0; $index < count($arrKeywords); $index++) {
        $word = trim(strtolower($arrKeywords[$index]));

        if (!empty($word)) {
            $extra .= " AND ";
        }

        if (!empty($word)) {
            $extra .= '(';
            for ($i = 0; $i < count($fields); $i++) {
                $key = key($fields);

                $extra .= "LCASE($key) LIKE '%$word%'";

                if ($i > 0 && $i < count($fields) - 1) {
                    $extra .= 'OR';
                }

                next($fields);
            }

            $extra .= ')';
        }
    } //end search query keywords

    //search statement
    $statement = "SELECT * FROM $table WHERE 1 ";

    //if max is not yet set
    if (!isset($_REQUEST['max'])) {
        $result          = $DB->query($statement . $extra);
        $_REQUEST['max'] = $DB->num_rows($result);
    }

    $at  = intval($at);

    if (!isset($order)) {
        $order = 'desc';
    }

    reset($fields);

    if (!isset($by)) {
        $by = 'id';
    }

    $order_by = " ORDER BY $by $order";
    $result   = $DB->query($statement . $extra . $order_by);

    //show_links(get_links($_REQUEST['max'], "$script&query=".urlencode($query)."&max=" . $_REQUEST['max'] . "&by=$by&order=$order",$inc,4));

    draw_header_row();
    $shaded = true;
    while ($row = $DB->fetch_array($result)) {
        show_result($row, $shaded = !$shaded);
    }
    draw_header_end();
    //show more links
    //show_links(get_links($_REQUEST['max'], "$script&query=".urlencode($query)."&max=" . $_REQUEST['max'] . "&by=$by&order=$order",$inc,4));
}

/**
 * @return void
 */
function draw_header_row()
{
    global $fields, $order, $by, $at, $page, $script;

    reset($fields);

    echo '<br />';

    echo '<table width="95" align="center">';
    echo '<tr bgcolor="#000066">';
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        if ("id" != $field && "user" != $field && "date_added" != $field) {
            echo '<td nowrap="nowrap" align="center" ' . ($by == $field ? 'bgcolor="#336699"' : '') . '><b>';
            echo "<a href=\"$script&query=" . urlencode($_REQUEST['query']) . "&max=" . $_REQUEST['max'] . "&at=$at&page=$page";
            echo "&by=$field&order=" . ("desc" == $order ? "asc" : "desc") . '" class="td_options">';
            echo ucwords($field);
            echo '</a>';
            if ($by == $field) {
                echo '  <img src="' . $order . '.gif" width="7" height="7" border="0" />  ';
            }

            echo '</b></td>';
        }
        next($fields);
    }
    echo "<td>&nbsp;</td>";
    echo '</tr>';
}

/**
 * @param string[] $cols
 * @param bool     $shaded
 *
 * @return void
 */
function show_result($cols, $shaded)
{
    global $_GLOBALS;
    global $fields, $actions, $script, $me, $allowed_sections;

    $user    = $_GLOBALS['staff']['user'];
    $rowuser = $cols['user'];

    reset($fields);
    $id = $cols[key($fields)];

    if (true == $me && $rowuser == $user) {
        include __DIR__ . '/staff_show_row.inc.php';
    } elseif (false == $me) {
        include __DIR__ . '/staff_show_row.inc.php';
    }
}

/**
 * @return void
 */
function draw_header_end()
{
    echo '</table><br />';
}

/**
 * @return void
 */
function add_new()
{
    global $field_data, $table_title, $table;
    global $DB;

    $insert_fields = '(';
    $value_fields  = '(';

    for ($i = 0; $i < count($field_data); $i++) {
        $key = key($field_data);

        $insert_fields .= $key;
        $value_fields .= "'" . htmlspecialchars($field_data[$key]) . "'";

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

    echo "New $table_title ID:$id added.<br />";
}

/**
 * @return void
 */
function edit()
{
    global $field_data, $table, $id;
    global $DB;

    $update_fields = '';

    for ($i = 0; $i < count($field_data); $i++) {
        $key = key($field_data);

        $update_fields .= "$key = '" . htmlspecialchars($field_data[$key]) . "'";

        if ($i < count($field_data) - 1) {
            $update_fields .= ', ';
        }

        next($field_data);
    }

    reset($field_data);

    $statement = "UPDATE $table SET $update_fields WHERE `id` = '$id'";

    $DB->query($statement);

    echo "$id: updated<br />";
}

if (trim($_REQUEST['query']) == '') {
    switch ($_REQUEST['action'] ?? '') {
        case "add":
            include __DIR__ . '/staff_add_form.inc.php';
            break;
        case "edit":
            include __DIR__ . '/staff_edit_form.inc.php';
            break;
        case "delete":
            include __DIR__ . '/staff_delete_form.inc.php';
            break;
        case "do_add":
            add_new();
            break;
        case "do_edit":
            edit();
            break;
        //case "do_delete":
        //    delete();
        //    break;
    }
}

if (isset($_REQUEST['query'])) {
    search($_REQUEST['query']);
}

//load_hidden_vars();

?>
</form>

<!--//parts admin -->
<?php

include_once __DIR__ . '/footer.inc.php';

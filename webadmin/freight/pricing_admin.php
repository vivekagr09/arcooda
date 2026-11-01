
<?php
/***********************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:
 ** Last changed..:
 ** Notes.........:
 ***********************************************************************/

include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

if (!isset($table)) {
    include '../footer.inc.php';
    exit();
}

$script = 'pricing_admin.php?table=' . $table . '&name_id=' . $name_id;
?>
<script language="javascript">
function reloadPage()
{
	//history.back();
	//history.go(0);
	window.location = '<?=$script?>&query=';
}
</script>
<?php

$columns = 0;
foreach ($DB->table_fields($table) as $v) {
    $fields[$v] = 'left';
    $columns++;
}

$id_field = key($fields);

reset($fields);

$actions[] = '<a href="$script&action=delete&id=$id">Delete</a>';

$items = $DB->query_first("SELECT COUNT(*) as total FROM $table");

$gets = get_vars();

$img_location     = "images/";
$img_location_abs = $_SERVER['DOCUMENT_ROOT'] . '/' . $img_location;

$name_query = $DB->query_first("SELECT * FROM shipping_services WHERE prop_id = '$name_id'");
$name       = $name_query['name'];
//$new_table_title = $courier;
?>

<script src="tooltip.js" type="text/javascript"></script>
<form accept-charset="utf-8" action="<?=$script?>" method="post" name="admin_form">
	<br>
      <table width="95%" border="0" align="center" cellspacing="2" cellpadding="2">
        <tr>
          <td class="pageMenu">
            <div align="center"><a href="AdminMenu.php"><?=$name?> Pricing</a></div>
          </td>
          <td class="pageMenu">
            <div align="right">
			<a href="<?=$script?>&action=add">Add New Weight</a> | <a href="<?=$script?>&query=">List All <?='(' . $items[total] . ')'?></a>
			</div>
          </td>
        </tr>
      </table>
<!-- search box -->
		<table align="right">
		<tr>
			<td><input type="text" name="query" size="20" value="<?=htmlspecialchars(stripslashes($query))?>"></td>
			<td> <input type="submit" name="search" value="Search"></td>
		</tr>
		</table><p><br>
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
        $order = 'ASC';
    }

    reset($fields);

    if (!isset($by)) {
        $by = "shipping_weight";
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
}

function draw_header_row()
{
    global $fields, $order, $by, $query, $max, $at, $page, $script, $name_id;

    reset($fields);

    echo '<br>';

    echo '<table width="95%" align="center">';
    echo "<tr><td align='center' colspan='100%'><input type='hidden' name='action' value='update_all'>";
    echo "<input type='submit' name='update' value='Update'></td></tr>";
    echo '<tr bgcolor="#000066">';
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        if ('ship_id' != $field) {
            echo '<td align="center" ' . ($by == $field ? 'bgcolor="#336699"' : 'class="tableHeader"') . '><b>';
            echo "<a href=\"$script&query=" . urlencode($query) . "&max=$max&at=$at&page=$page";
            echo "&by=$field&order=" . ("desc" == $order ? "asc" : "desc") . '" class="td_options">';
            if ('shipping_weight' == $field) {
                echo "Weight";
            } else {
                echo $field;
            }
            echo '</a>';
            if ($by == $field) {
                echo '  <img src="' . $order . '.gif" width="7" height="7" border="0">  ';
            }

            '</b></td>';
        }
        next($fields);
    }
    echo "<td class='tableHeader'>&nbsp;</td>";
    echo '</tr>';

}

function draw_header_end()
{
    echo "<tr><td align='center' colspan='100%'><input type='hidden' name='action' value='update_all'>";
    echo "<input type='submit' name='update' value='Update'></td></tr>";
    echo '</table><br>';

}

function add_new()
{
    global $field_data, $table_title, $table, $name_id;
    global $DB;

    $insert_fields = '(';
    $value_fields  = '(';

    for ($i = 0; $i < count($field_data); $i++) {
        $key = key($field_data);

        $insert_fields .= $key;
        $value_fields .= "'0'";

        if ($i < count($field_data) - 1) {
            $insert_fields .= ', ';
            $value_fields .= ', ';
        }

        next($field_data);
    }
    $insert_fields .= ')';
    $value_fields .= ')';
    //reset($field_data);
    $statement = "INSERT INTO $table $insert_fields VALUES $value_fields";
    $DB->query($statement);
    $id = $DB->insert_id();
    //echo "New $table_title ID:$id added.<br>";
?>
<script language="javascript">
reloadPage();
//history.go(0)
</script>
<?php
}

function edit($id)
{

    global $field_data, $table_title, $table, $id_field, $DB;
    for ($i = 0; $i < count($field_data[$id]); $i++) {
        $key = key($field_data[$id]);

        $update_fields .= "$key = '" . addslashes(htmlspecialchars($field_data[$id][$key])) . "'";

        if ($i < count($field_data[$id]) - 1) {
            $update_fields .= ', ';
        }

        next($field_data[$id]);
    }

    reset($field_data[$id]);

    $statement = "UPDATE $table SET $update_fields WHERE $id_field = '$id'";

    $DB->query($statement);

    //echo "$id: updated<br>";
}
function updateAll()
{
    global $field_data;

    foreach ($field_data as $k => $v) {
        edit($k);
    }
    echo 'Update Ok';
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
    global $fields, $actions, $script, $table, $name_id;

    reset($fields);
    $id = $cols[key($fields)];

    if (locked($table, $id)) {
        echo '<tr bgcolor="pink">';
    } else {
        echo $shaded ? '<tr class="tableRow">' : '<tr class="tableRowAlt">';
    }

    for ($i = 0; $i < sizeof($fields); $i++) {
        $key = key($fields);
        if ('ship_id' != $key) {
            echo "<td  align='center'>";
?>
			<input name="field_data[<?=$id?>][<?=$key?>]" id="<?=$id . $key?>" type="text" style="width:62px" value="<?=stripslashes($cols[$key])?>"></input>
<?php
echo '</td>';
        }
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

if (trim($query) == '') {
    switch ($action) {
        case "add":
            //include 'pricing_add_form.inc.php';
            add_new();
            break;
        //case "edit":
        //    include 'pricing_edit_form.inc.php';
        //    break;
        case "delete":
            include 'pricing_delete_form.inc.php';
            break;
        //case "do_add":
        //add_new();
        //break;
        //    case "do_edit":
        //        if(!locked($table, $id, true))
        //            edit();
        //        break;
        case "update_all":
            if (!locked($table, $id, true)) {
                updateAll();
            }

            break;
        case "do_delete":
            if (!locked($table, $id, true)) {
                delete();
            }

            break;

    }
}

if (isset($query)) {
    $query = htmlspecialchars($query);
    search(addslashes(addslashes(($query))));
}

//load_hidden_vars();

?>
</form>

<!--//parts admin -->
<?php
include '../footer.inc.php';

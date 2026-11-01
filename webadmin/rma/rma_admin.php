<?php
include_once __DIR__ . '/common.inc.php';

$user             = get_user();
$allowed_sections = get_allowed_sections($user);
if (has_permission('RMA', $allowed_sections)) {

?>
<!--modified 180203-->
<?php
include_once '../connection.inc.php';

    if ('rma_repairers' == $table) {
        $table_title = 'RMA_Sent_To';
    } else {
        $table_title = ucwords(str_replace('_', ' ', $table));
    }

?>
<style type="text/css">
<!--
body,p,td {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px}
.td_options {  color: #FFFFFF; font-size: 12px; text-decoration: none;}
.topLinks {  color: blue; font-size: 12px;}
a:hover {  color: #FF0000}

.top {
	border-top-width: 1px;
	border-top-style: solid;
	border-top-color: #CCCCCC;
}
.left {
	border-left-width: 1px;
	border-left-style: solid;
	border-left-color: #CCCCCC;
}
.right {
	border-right-width: 1px;
	border-right-style: solid;
	border-right-color: #CCCCCC;
}
.centre {
	border-top-width: 1px;
	border-bottom-width: 1px;
	border-top-style: solid;
	border-bottom-style: solid;
	border-top-color: #CCCCCC;
	border-bottom-color: #CCCCCC;
}
.bottom {
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #CCCCCC;
}

.bottomLeft {
	border-bottom-width: 1px;
	border-left-width: 1px;
	border-bottom-style: solid;
	border-left-style: solid;
	border-bottom-color: #CCCCCC;
	border-left-color: #CCCCCC;
}
.bottomRight {
	border-right-width: 1px;
	border-bottom-width: 1px;
	border-right-style: solid;
	border-bottom-style: solid;
	border-right-color: #CCCCCC;
	border-bottom-color: #CCCCCC;
}

.bottomLeftRight {
	border-right-width: 1px;
	border-bottom-width: 1px;
	border-left-width: 1px;
	border-right-style: solid;
	border-bottom-style: solid;
	border-left-style: solid;
	border-bottom-color: #CCCCCC;
	border-right-color: #CCCCCC;
	border-left-color: #CCCCCC;
}

.box {
	border: 1px solid #CCCCCC;
}

.centerDash {
	border-top-width: 1px;
	border-top-style: dotted;
	border-top-color: #003399;
	border-bottom-width: 1px;
	border-bottom-style: dotted;
	border-bottom-color: #003399;
}
.TableRow
{
	background-color:#FFFFFF;
}
.TableRowAlt
{
	background-color:#EEEEEE;
}

.TableRowOver
{
	background-color:#D6DFF7;
}

-->
</style>
<script language="JavaScript" type="text/javascript">
<!--
function pviiClassNew(obj, new_style) { //v2.6 by PVII
  obj.className=new_style;
}
-->
</script>
<?php
if (!isset($table)) {
        include '../footer.inc.php';
        exit();
    }

    $script = 'rma_admin.php?table=' . $table;

    foreach ($DB->table_fields($table) as $v) {
        $fields[$v] = 'left';
    }
    
    $id_field = key($fields);

    reset($fields);

    $actions[] = '<a href="$script&action=edit&id=$id">Edit</a>';
    $actions[] = '<a href="$script&action=delete&id=$id">Delete</a>';

    $items = $DB->query_first("SELECT COUNT(*) as total FROM $table");

    $gets = get_vars();

//$table_title = ucwords(str_replace('_', ' ', $table));

?>

<!-- admin -->

<form action="<?=$script?>" method="post" enctype="multipart/form-data" name="admin_form">
	<br>
      <table width="100%" border="0" cellspacing="1" cellpadding="5">
        <tr>
          <td bgcolor="#EEEEEE" width="20%">
            <div align="center"><b><?=$table_title?></b></div>
          </td>
          <td bgcolor="#EEEEEE" width="80%">
            <div align="right">
			<a href="<?=$script?>&action=add">Add New <?=$table_title?></a> |
			<a href="<?=$script?>&query=">List <?=$table_title . ' (' . $items[total] . ')'?></a>|
			<a href="rma_view_all.php?table=rma_service&query=">RMA View All </a>
			</div>
          </td>
        </tr>
      </table>
<!-- search box -->
      <p align="right">
        <input type="text" name="query" size="20" value="<?=htmlspecialchars(stripslashes($query))?>">
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

        if (!isset($by))
        //$by = 'date_added';

        //$order_by = " ORDER BY $by $order";
        {
            $limit = " LIMIT $at, $inc ";
        }

        $result = $DB->query($statement . $extra . $order_by . $limit);

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
        global $fields, $order, $by, $query, $max, $at, $page, $script;

        reset($fields);

        echo '<br>';

        echo '<table align="center" cellpadding="4" cellspacing="1">';
        echo '<tr bgcolor="#3399FF">';
        for ($i = 0; $i < sizeof($fields); $i++) {
            $field = key($fields);
            echo '<td align="center" ' . ($by == $field ? 'bgcolor="#FF9900"' : '') . '><b>';
            echo "<a href=\"$script&query=" . urlencode($query) . "&max=$max&at=$at&page=$page";
            echo "&by=$field&order=" . ("desc" == $order ? "asc" : "desc") . '" class="td_options">';
            echo ucwords(str_replace('_', ' ', $field));
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

        $user = get_user();

        $insert_fields = '(';
        $value_fields  = "(";

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

        $user = get_user();

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

        if ($shaded) {
            echo "<tr class=\"TableRow\" onMouseOver=\"pviiClassNew(this,'TableRowOver')\" onMouseOut=\"pviiClassNew(this,'TableRow')\">\n";
        } else {
            echo "<tr class=\"TableRowAlt\" onMouseOver=\"pviiClassNew(this,'TableRowOver')\" onMouseOut=\"pviiClassNew(this,'TableRowAlt')\">\n";
        }

        for ($i = 0; $i < sizeof($fields); $i++) {
            $key = key($fields);
            echo "<td align=\"" . $fields[$key] . "\">";
            echo stripslashes($cols[$key]) . '</td>';
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

    switch ($action) {
        case "add":
            include '../generic_add_form.inc.php';
            break;
        case "edit":
            include '../generic_edit_form.inc.php';
            break;
        case "edit_rma":
            include 'rma_edit_form.inc.php';
            break;
        case "delete":
            include '../generic_delete_form.inc.php';
            break;
        case "do_add":
            add_new();
            break;
        case "do_edit":
            edit();
            break;
        case "do_delete":
            delete();
            break;

    }

    if (!isset($action) || $action = "") {
        $query = htmlspecialchars($query);
        search(addslashes(addslashes(($query))));
    }

?>
</form>

<!--//admin -->
<?php
include '../footer.inc.php';
?>
<?php
}

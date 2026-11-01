<?php
$table  = 'rma_service';
include_once __DIR__ . '/common.inc.php';

$user             = get_user();
$allowed_sections = get_allowed_sections($user);
if (has_permission('RMA', $allowed_sections)) {

?>
<script>
<!--
function printpage() {
        if (window.print)
        {
                window.print();
        }
        else
        {
                alert('To print this page, select Print Frame from the file menu.');
        }
}

function doPrint()
{
        if (confirm("Click OK to print this document"))
        {
                printpage();
        }

}
// -->
</script>
<?php
$script = 'rma_view_all.php';

?>
<!-- suppliers admin -->
<?php

    $items = $DB->query_first("SELECT COUNT(*) as total FROM $table");
    $gets  = get_vars();

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
<body <?php if (true == $print) {echo 'onload="doPrint();"';}?>>
<form action="<?=$script?>?action=<?=$action?>" method="post">
<?php if (true != $print) {?>
	<br>

      <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td bgcolor="#EEEEEE" width="20%">
            <div align="center"><b>RMA Service</b></div>
          </td>
          <td bgcolor="#EEEEEE" width="80%">
            <div align="right">
            <a href="rma_view_open.php?table=rma_service&query=">View Open</a> |
            <a href="<?=$script?>?action=add">Add RMA</a> |
            <a href="rma_admin.php?table=rma_priority" class="topLinks">Add/Edit Priority</a>|
            <a href="rma_admin.php?table=rma_repairers" class="topLinks">Add/Edit Sent_to</a>|
            <a href="rma_admin.php?table=rma_status" class="topLinks">Add/Edit Status</a>
			</div>
          </td>
        </tr>
      </table>
<?php }
    $script = 'rma_view_all.php';

    $fields['RMA']        = 'center';
    $fields['priority']   = 'left';
    $fields['date_added'] = 'left';
    $fields['item']       = 'left';
    $fields['customer']   = 'center';
    $fields['added_by']   = 'center';
    $fields['sent_to']    = 'center';
    $fields['status']     = 'center';

    $id_field = 'id';

    function search($query)
    {
        global $DB, $at, $fields, $order, $by, $table;
        global $script, $search_fields, $status;

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

        $statement = "SELECT
						b.id as RMA,
					    b.priority as priority,
                        p.colour, p.level, p.description as priority_desc,
						DATE_FORMAT(b.date_added, '%d-%m-%Y') as date_added,
						b.item,
						b.customer,
						b.added_by,
					    b.sent_to,
						b.status,
						(TO_DAYS(NOW()) - TO_DAYS(b.date_added)) as days

					FROM $table b, rma_priority p
					WHERE
						p.description = b.priority

						";

        //if max is not yet set
        if (!isset($max)) {
            $result = $DB->query($statement . $extra);
            $max    = $DB->num_rows($result);
        }

        $inc = 25;
        $at  = intval($at);

        if (!isset($order)) {
            $order = 'desc';
        }

        if (!isset($by))
        //$by = 'date_added';
        {
            $by = 'RMA';
        }

        $order_by = " ORDER BY '$by' $order";

        if ('date_added' == $by) {
            $order_by = " ORDER BY b.date_added $order";
        }

        //echo $statement.$extra;
        $result = $DB->query($statement . $extra . $group_by . $order_by);

        draw_header_row();
        $shaded = true;
        while ($row = $DB->fetch_array($result)) {
            show_result($row, $shaded = !$shaded);
        }
        draw_header_end();
        //show more links
    }

    function draw_header_row()
    {
        global $fields, $order, $by, $query, $max, $at, $page, $script;
        global $status;
        //$link = "website=$website&status=$status";

        reset($fields);
        echo "<br>";
        echo '<table align="center" cellpadding="4" cellspacing="1">';
        echo '<tr bgcolor="#3399FF">';
        for ($i = 0; $i < sizeof($fields); $i++) {
            $field = key($fields);
            echo '<td align="center" ' . ($by == $field ? 'bgcolor="#FF9900"' : '') . '><b>';
            echo "<a href=\"$script?query=" . urlencode($query) . "&max=$max&at=$at&page=$page";
            echo "&$link&by=$field&order=" . ("desc" == $order ? "asc" : "desc") . '" class="td_options">';
            echo ucwords(str_replace('_', ' ', $field));
            echo '</a>';
            if ($by == $field) {
                echo '  <img src="/webadmin/' . $order . '.gif" width="7" height="7" border="0">  ';
            }

            echo '</b></td>';
            next($fields);
        }
        echo '</tr>';

    }

    function draw_header_end()
    {
        global $print;
        echo '<tr><td>&nbsp;</td></tr><tr><td colspan="20" align="center">';
        if (true != $print) {
            echo '<a href="rma_view_all.php?print=true" target="_blank">Click here to print summary list</a></td></tr>';
        }
        echo '</table>';
        echo "<br>";
    }

    function priority($colour, $priority)
    {
?>
<table border="0" cellspacing="1" cellpadding="2">
  <tr>
    <td><table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td bgcolor="#000000"> <table width="100%" border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td bgcolor="<?=$colour?>"><img src="/_.gif" width="13" height="13"></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
    <td><?=$priority?></td>
  </tr>
</table>
<?php
}

    function show_result($cols, $shaded)
    {
        global $fields, $actions, $script, $DB;

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
            if ('priority' == $key) {
                priority($cols['colour'], $cols['priority_desc']);
            } else if ('added_by' == $key) {
                $result = $DB->query_first("SELECT * FROM `highwaygroup`.`users` WHERE `user` = '" . $cols[$key] . "'");
                echo $result['first_name'] . ' ' . $result['last_name'];

            } else if ('item' == $key) {
                if ('' == $cols[$key]) {
                    $item = 'No Title';
                } else {
                    $item = $cols[$key];
                }
                echo "<a href=\"rma_edit.php?action=edit_rma&id=$id\">" . $item . "</a>\n";
            } else {
                echo stripslashes($cols[$key]);
            }

            echo '</td>';
            next($fields);
        }
        echo "</tr>";
    }

//search("");
    //include 'footer.inc.php';

?>

<!-- search box --><!--
      <p align="right">
        <input type="text" name="query" size="20" value="<?=htmlspecialchars(stripslashes($query))?>">
        <input type="submit" name="search" value="Search">
      </p>
<!-- //search box -->
<?php

    function do_add()
    {
        global $DB, $data, $id;
        $user = get_user();
        $name = addslashes(trim($data['item']));
        if ('' != $name) {
            $statement = "INSERT INTO rma_service( " . get_fields($data) . ", date_added, added_by) " .
            "VALUES( " . get_values($data) . ",NOW(),'" . $user . "')";
            //echo $statement."<BR>";
            $DB->query($statement);
            echo "New RMA record added.<br />";
        } else {
            echo "Sorry you need to provide a name for the item";
        }

        $comment = addslashes(trim($data['comments']));

        //echo $comment;
        if ('' != $comment) {
            $id        = $DB->insert_id();
            $statement = "INSERT INTO rma_comments (rma, date, person, comment)
						VALUES ('$id', NOW(), '$user', '$comment')";
            $DB->query($statement);
        }

    }
    function do_edit()
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
    switch ($action) {
        case "do_add":
            do_add();
            break;
        case "do_edit":
            do_edit();
            break;
        case "do_delete":
            do_delete();
            break;
        case "add":
            include 'rma_add_form.inc.php';
            break;
        case "add_priority":
            include 'rma_add_priority.inc.php';
            break;

        case "edit_rma":
            include 'rma_edit_form.inc.php';
            break;
        case "delete":
            include '../generic_delete_form.inc.php';
            break;

    }

    if (!isset($action) || $action = "") {
        $query = htmlspecialchars($query);
        search(addslashes($query));

    }

?>
</form>
</body>

<!--//suppliers admin -->
<?php
include '../footer.inc.php';
}

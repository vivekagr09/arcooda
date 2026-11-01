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

$fields[id] = 'center';
//$fields[prop_id] = 'center';
$fields[code]        = 'center';
$fields[zone]        = 'left';
$fields[tit]         = 'left';
$fields[surcharge]   = 'left';
$fields[weightlimit] = 'left';
$prop_id             = $_REQUEST["courier"];
$script              = 'zoning_admin';

$actions[] = '<a href="$script?action=edit&id=$id&courier=$prop_id">Edit</a>';
$actions[] = '<a href="$script?action=delete&id=$id&courier=$prop_id">Delete</a>';

$statement = "SELECT COUNT(*) as total FROM shipping_countries WHERE prop_id = '" . $prop_id . "';";
$items     = $DB->query_first($statement);

$gets = get_vars();

$statement     = "SELECT * FROM shipping_services WHERE prop_id = '" . $prop_id . "';";
$courier_query = $DB->query_first($statement);

$courier = $courier_query['name'];
?>
<!-- parts screenshot admin -->
 <link rel="stylesheet" href="/webadmin/admin.css" type="text/css">
<form accept-charset="utf-8" action="<?=$script . $gets?>" method="post">

	<br>
      <table width="95%" align="center" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td class='pageMenu'>
				<a href="AdminMenu.php"><?=$courier;?> Destination Admin</a>
          </td>
          <td class="pageMenu">
            <div align="right">
			<a href="<?=$script?>?action=add&courier=<?=$prop_id?>">Add New Destination</a> |
			<a href="<?=$script?>?query=&courier=<?=$prop_id?>">List All Destinations (<?=$items[total]?>)</a>
			</div>
          </td>
        </tr>
      </table><br>
<?php

function search($query)
{
    global $DB, $at, $fields, $order, $by, $script, $prop_id, $courier;

    //keywords
    $arrKeywords = explode(" ", trim($query));

    //search query by key words
    if (!empty($query)) {
        $extra = " ";
    }

    for ($index = 0; $index < count($arrKeywords); $index++) {
        $key  = key($fields);
        $word = trim(strtolower($arrKeywords[$index]));

        if (!empty($word)) {
            $extra .= " AND ";
        }

        if (!empty($word)) {
            if (preg_match("/^\\d+$/", $word)) //if its a search by id
            {
                $extra .= "(s.ss_id = '$word' OR s.id = '$word')";
            } elseif (preg_match("/^[\\*\\+\\\\]/", $word)) {
                $extra .= "(LOWER($key) LIKE '%$word%')";
            } else {
                $extra .= "(LOWER($key) REGEXP \"$word\")";
            }
            //else just a normail regular exp. search
        }
    } //end search query keywords

    //search statement
    $statement = "SELECT * FROM shipping_countries WHERE prop_id = '" . $prop_id . "'";

    //$service .=  $prop_id;
    //if max is not yet set
    if (!isset($max)) {
        $result = $DB->query($statement . $extra);
        $max    = $DB->num_rows($result);
    }

    $inc = 25;
    $at  = intval($at);

    if (!isset($order)) {
        $order = 'asc';
    }

    if (!isset($by)) {
        $by = 'code';
    }

    $order_by = " ORDER BY $by $order";
    $limit    = " LIMIT $at, $inc ";

    //echo $statement.$extra;
    $result = $DB->query($statement . $extra . $order_by . $limit);

    draw_header_row();
    $shaded = true;
    while ($row = $DB->fetch_array($result)) {
        show_result($row, $shaded = !$shaded);
    }
    draw_header_end();
    //show more links
    show_links(get_links($max, "$script?query=" . urlencode($query) . "&max=$max&by=$by&order=$order&courier=$prop_id", 25, 4));
}

function draw_header_row()
{
    global $fields, $order, $by, $query, $max, $at, $page, $script;
    global $prop_id;

    reset($fields);
    echo "<br>";
    echo '<table widt="95" align="center">';
    echo '<tr bgcolor="#000066">';
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        if ('id' != $field && 'prop_id' != $field) {
            echo '<td align="center" ' . ($by == $field ? 'bgcolor="#336699"' : 'class="tableHeader"') . '><b>';
            echo "<a href=\"$script?query=" . urlencode($query) . "&max=$max&at=$at&page=$page&courier=$prop_id";
            echo "&by=$field&order=" . ("desc" == $order ? "asc" : "desc") . '" class="td_options">';
            if ('code' == $field) {
                echo "Name";
            } else if ('zone' == $field) {
                echo "Zone";
            } else if ('tit' == $field) {
                echo "Time in Transit";
            } else if ('surcharge' == $field) {
                echo "Surcharge";
            } else if ('weightlimit' == $field) {
                echo "Weight Limit";
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
    echo '</table>';
    echo "<br>";
}

function show_result($cols, $shaded)
{
    global $fields, $actions, $script, $DB, $prop_id;

    reset($fields);
    $id = $cols[key($fields)];
    echo $shaded ? '<tr class="tableRow">' : '<tr class="tableRowAlt">';

    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        if ('prop_id' != $field && 'id' != $field) {
            if ('code' != $field) {
                echo "<td align=\"" . $fields[key($fields)] . "\">";
                echo (stripslashes($cols[key($fields)])) . '</td>';
            } else {
                echo "<td align=\"" . $fields[key($fields)] . "\">";
                $statement  = "SELECT * FROM shipping_location WHERE code = '" . stripslashes($cols[key($fields)]) . "'";
                $code_query = $DB->query_first($statement);
                echo $code_query[name];
                echo '</td>';
            }
        }
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

function add_wanted()
{
    global $DB;
    $prop_id     = $_REQUEST["courier"];
    $code        = ($_POST["code"]);
    $zone        = ($_POST["zone"]);
    $tit         = ($_POST["tit"]);
    $surcharge   = ($_POST["surcharge"]);
    $weightlimit = ($_POST["weightlimit"]);

    $statement = "INSERT INTO shipping_countries (prop_id, code, zone, tit, surcharge, weightlimit)";

    $statement .= "VALUES ('" . addslashes(htmlspecialchars($prop_id)) . "', '" . addslashes(htmlspecialchars($code)) . "', '" . addslashes(htmlspecialchars($zone)) . "', '" . addslashes(htmlspecialchars($tit)) . "', '" . addslashes(htmlspecialchars($surcharge)) . "', '" . addslashes(htmlspecialchars($weightlimit)) . "')";
    $DB->query($statement);

    echo "New Country Added to Carrier<br>";
}

function edit_wanted()
{
    global $DB, $id;
    $prop_id     = $_REQUEST["courier"];
    $code        = ($_POST["code"]);
    $zone        = ($_POST["zone"]);
    $tit         = ($_POST["tit"]);
    $surcharge   = ($_POST["surcharge"]);
    $weightlimit = ($_POST["weightlimit"]);

    $statement = "UPDATE shipping_countries SET
					code = '" . addslashes(htmlspecialchars($code)) . "',
					zone = '" . addslashes(htmlspecialchars($zone)) . "',
					tit = '" . addslashes(htmlspecialchars($tit)) . "',
					surcharge = '" . addslashes(htmlspecialchars($surcharge)) . "',
					weightlimit = '" . addslashes(htmlspecialchars($weightlimit)) . "'
				WHERE id = '$id'";
    $DB->query($statement);

    echo "<center>Zone ID:$id updated.<br></center>";
}

function delete_wanted()
{
    global $id, $DB;

    $statement = "DELETE FROM shipping_countries WHERE id = '$id'";
    $DB->query($statement);
    echo "<center>ID:$id Deleted.<br></cente>";
}

switch ($action) {
    case "do_add":
        add_wanted();
        break;
    case "do_edit":
        edit_wanted();
        break;
    case "do_delete":
        delete_wanted();
        break;
    case "add":
        include 'zoning_add_form.inc.php';
        break;
    case "edit":
        include 'zoning_edit_form.inc.php';
        break;
    case "delete":
        include 'zoning_delete_form.inc.php';
        break;

}

if (!isset($action)) {
    $query = htmlspecialchars($query);
    search(addslashes($query));
}

?>
</form>

<!--//wanted admin -->
<?php
include '../footer.inc.php';
?>
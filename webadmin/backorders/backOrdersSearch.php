<?php
$section_code = "BACKORDER";
include_once __DIR__ . '/common.inc.php';

?>
<html>
<head>
<title>Product Search</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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
</head>

<body>
<br>
<?php

$script = "backOrdersSearch.php";

//the fields for display
$fields['id']             = 'center';
$fields['product_name']   = 'left';
$fields['cost_price']     = 'right';
$fields['sales_price']    = 'right';
$fields['sales_currency'] = 'right';
$fields['qty']            = 'right';

$search_fields[] = 'id';
$search_fields[] = 'product_name';

search($name);

function search($query)
{
    global $DB, $at, $fields, $order, $by, $script, $location, $type, $table, $search_fields;

    //search statement

    $statement = "SELECT * FROM products WHERE ";
    if ('' != $query) {
        $extra = 'product_name LIKE "%' . $query . '%" OR id = "' . $query . '"';
    } else {
        $extra = '1';
    }
    //if max is not yet set
    if (!isset($max) || '' == $max) {
        $result = $DB->query($statement . $extra);
        $max    = $DB->num_rows($result);
    }

    //number of results to show
    $inc = 50;
    $at  = intval($at);

    //default ordering is ascending
    if (!isset($order) || '' == $order) {
        $order = 'asc';
    }

    //default ordering by product name
    if (!isset($by) || '' == $by) {
        $by = 'product_name';
    }

    $order_by = " ORDER BY $by $order"; //order clause
    $limit    = " LIMIT $at, $inc "; //limit clause

    //get the results of the search
    $result = $DB->query($statement . $extra . $order_by . $limit);

    if ($DB->num_rows($result) > 0) {
        echo "<div align=\"center\"><font color=\"#FF0000\"><b>Results.....</b></font></div>\r\n";
        draw_header_row();
        $shaded = true;

        //go through each row and show the results
        while ($row = $DB->fetch_array($result)) {
            show_result($row, $shaded = !$shaded);
        }

        draw_header_end();
    } else {
?>
	<br>
	<div align="center">
		<font color="#FF0000"><b>Search yielded no results.</b></font>
	</div>
<?php
}
}

function draw_header_row()
{
    global $fields, $order, $by, $query, $max, $at, $page, $script, $print, $name;

    reset($fields);
    echo "<br>";
    if (true == $print) {
        echo '<table width="95%" border="1" cellspacing="1" cellpadding="3" align="center">';
        echo '<tr class="titles">';
    } else {
        echo '<table width="95%" align="center" cellspacing="1" cellpadding="4">';
        echo '<tr bgcolor="#3399FF">';
    }

    for ($i = 0; $i < sizeof($fields); $i++) {
        $field   = key($fields);
        $byField = $field;
        if ("product" == $field) {
            $byField = "product_name";
        }

        if ("price" == $field) {
            $byField = "offer_price";
        }

        if (true == $print) {
            echo '<td>';
        } else {
            echo '<td align="center" ' . ($by == $field ? 'bgcolor="#FF9900"' : '') . '>';
        }
        echo "<a href=\"$script?query=" . urlencode($query) . "&name=$name&max=$max&at=$at&page=$page&by=$byField&order=" . ("desc" == $order ? "asc" : "desc") . '" class="td_options">';
        echo '<b>' . titleCase($field) . '</b>';
        if ($by == $byField) {
            echo '<img src="' . $order . '.gif" width="7" height="7" border="0">  ';
        }

        echo '</a>';
        echo '</td>';
        next($fields);
    }
    if (false == $print) {
        echo "<td>&nbsp;</td>";
    }
    echo '</tr>';

}
function show_result($cols, $shaded)
{
    global $fields, $actions, $script, $DB, $print;

    reset($fields);
    $id = $cols['id'];
    if (true == $print) {
        echo '<tr class="data"></tr>';
    } else if ($shaded) {
        echo "<tr class=\"TableRow\">\n";
        //echo "<tr>\n";
    } else {
        echo "<tr class=\"TableRowAlt\">\n";
        //echo "<tr>\n";
    }
    for ($i = 0; $i < sizeof($fields); $i++) {
        echo "<td align=\"" . $fields[key($fields)] . "\">";
/*
if(key($fields) == "product")
{
$type = encode_section_url($cols['type_name']);
$item_link = encode_product_url($cols['part_name'],$cols['part_id']);
echo "<a href=\"/parts/$type/$item_link.html\" target=\"_blank\">".stripslashes($cols['part_name'])."</a>";
}
 */
        if (key($fields) == "date_added") {
            echo formatDate(strtotime($cols[key($fields)]));
        } else if (key($fields) == "price") {
            echo $cols['offer_price'];
        } else {
            echo stripslashes($cols[key($fields)]) . '</td>';
        }

        next($fields);
    }
    if (false == $print) {
        $statement         = "SELECT product_name, cost_price, sales_price FROM products WHERE id = '" . $id . "'";
        $result            = $DB->query_first($statement);
        $text              = 'ID:' . $id . ', Name:' . addslashes($result['product_name']);
        $buy_price         = $result['cost_price'];
        $online_sell_price = $result['sales_price'];

        echo '<td nowrap>';
        //echo '<input type="button" name="search'.$id.'" value="This One onclick="pasteInValue(\''.addslashes($result['part_name']).'\')">';
        echo '<input type="button" name="search' . $id . '" value="This One" onclick="window.opener.document.main.product_name.value = \'' . $text . '\';window.opener.document.main.buy_price.value = \'' . $buy_price . '\';window.opener.document.main.online_sell_price.value = \'' . $online_sell_price . '\';window.close();">';
        echo '</td>';
    }
    echo "</tr>";
}
function draw_header_end()
{
    global $print;

    echo '</table>';
    echo "<br>";
}
?>
<br>
</body>
</html>

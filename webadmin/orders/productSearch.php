<?php
include_once __DIR__ . '/common.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . "/webadmin/currency.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/product_functions.php";
?>
<html>
<head>
<title>Product Search</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="/webadmin/admin.css" type="text/css">
</head>

<body>
<br>
<?php
//the fields for display
$fields['id']           = 'center';
$fields['product_name'] = 'left';
$fields['category']     = 'left';
$fields['sales_price']  = 'right';

$search_fields[] = 'products.id';
$search_fields[] = 'products.product_name';

search($_GET['param']);

function search($query)
{
    global $DB, $at, $fields, $order, $by, $location, $type, $table, $search_fields;

    $query = trim($query);

    $dash_pos = strrpos($query, '-');
    if (intval($dash_pos) > 0) {
        $query = trim(substr($query, $dash_pos + 1, strlen($query)));
    }

    $id_search = false;

    if (!preg_match('/\D/', $query)) {
        $query     = intval($query);
        $id_search = true;

        echo '<div align="center"><b>Searching for product ID: ' . $query . '</b></div><br>';
    } else {
        echo '<div align="center"><b>Searching for product name &quot;' . $query . '&quot;</b></div><br>';
    }

    $query = str_replace(['(', ')', '+', '?', '[', ']', '$', '^'], '', $query);

    //keywords
    $arrKeywords = explode(" ", $query);

    $extra = '';

    //search query by key words
    if ($id_search) {
        $extra .= " AND products.id = '$query' ";
    } else {
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
    }

    //search statement
    $statement = "SELECT products.*,product_types.name AS category FROM products, product_types WHERE products.type=product_types.id ";

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
    global $fields;
    reset($fields);

    echo '<br>';
    echo '<table width="95%" align="center" cellpadding="4" cellspacing="1">' . "\n";
    echo '<tr class="tableHeader">' . "\n";

    //for each of the displaying fields
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);

        //draw the fields, with links for sorting and ordering
        echo '<td align="center">';
        echo $field;
        echo '</td>' . "\n";
        next($fields);
    }
    echo '<td>&nbsp;</td>' . "\n";
    echo '</tr>' . "\n";
}

function draw_header_end()
{
    echo '</table>';
}

function show_result($cols, $shaded)
{
    global $fields, $actions, $table;

    //the displaying fields
    reset($fields);

    $primKeyArr = array('id' => $cols['id']);
    if (locked($table, $primKeyArr)) {
        $class = "locked";
    } else if (my_lock($table, $primKeyArr)) {
        $class = "myLocked";
    } else {
        $class = $shaded ? "tableRowAlt" : "tableRow";
    }

    echo "<tr class=\"$class\">\r\n";

    $salesPrice = showPriceFrom($cols['sales_price'], $cols['sales_currency']);

    //for each of the fields
    for ($i = 0; $i < sizeof($fields); $i++) {
        $key = key($fields);
        echo "\n<td align=\"" . $fields[$key] . "\">";
        if ("sales_price" == $key) {
            echo $salesPrice . " USD";
        } else {
            echo stripslashes($cols[$key]);
        }

        echo '</td>';
        next($fields);
    }
    echo '<td nowrap>';
    $salesPrice = sprintf("%01.2f", floatval($salesPrice));
    $jsParams   = "'" . $_GET['i'] . "','" . $cols['id'] . "','" . $cols['product_name'] . "','$salesPrice'";
    echo '<input type="button" value="OK" onclick="javascript: opener.selectProduct(' . $jsParams . '); window.close(); ">';
    echo "</td>\r\n";
    echo "</tr>\r\n";
}
?>
<br>
</body>
</html>
<?php
/******************************************************************************
 ** Title.........: Products Administration
 ** Version.......: 1.1
 ** Author........: Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......: admin.php
 ** Last changed..: 7/18/02
 ** Notes.........:
 *****************************************************************************/

include_once __DIR__ . '/common.inc.php';
?><html>
<head>
<title>Product Ref</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
/*<![CDATA[*/
body,p,td {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px}
.td_options {  color: #FFFFFF; font-size: 12px}
a:hover {  color: #FF0000}
/*]]>*/
</style>
<script language="JavaScript">
//<![CDATA[
function MM_openBrWindow(theURL,winName,features) { //v2.0
	window.open(theURL,winName,features);
}
function product_ref(id)
{
	var product = document.admin_form["product["+id+"]"];
	opener.document.admin_form.pref_text.value = unescape(product.value);
	opener.document.admin_form.pref_id.value = id;
	opener.document.admin_form.pref_text.disabled = true;
	window.close();
}
//]]>
</script>
</head>
<!-- main -->
<body bgcolor="#FFFFFF" text="#000000">
<!--<div align="center">
<input type="button" name="Button" value="Close Product Referencing" onClick="window.close()">
</div>-->
<?php

//this script file
$script = "product_ref.php?table=$table";

//the fields for display
$fields['id']           = 'center';
$fields['product_name'] = 'left';
$fields['section']      = 'center';
$fields['category']     = 'center';
$fields['active']       = 'center';
$fields['sales_price']  = 'right';
$fields['qty']          = 'right';

$id_field = 'id';

$search_fields[] = 'p.id';
$search_fields[] = 'p.product_name';
$search_fields[] = 't.name';
$search_fields[] = 'tp.description';

$actions[] = '<a href=javascript:product_ref("$id"); >Link This</a>'; //Add screenshots link

//get the image storage location
$img_location     = $section_details['img_location'] . '/';
$img_location_abs = $_SERVER['DOCUMENT_ROOT'] . '/' . $img_location;

//get the GET variables
$gets = get_vars();
?>
<!-- products ref -->
<form action="<?=$script?>" method="post" name="admin_form">
<!-- search box -->
      <p align="right">
        <input type="text" name="query" size="20" value="<?=htmlspecialchars(stripslashes($query));?>">
        <input type="submit" name="search" value="Search">
      </p>
<!-- //search box -->

<?php
//===========================================================================
// search through the sections and display the results in a table format
//
// PARAMS: the query string
// RETURN: n/a
//===========================================================================
function search($query)
{
    global $DB, $at, $fields, $order, $by, $script, $search_fields, $table;

    $query = trim($query);
    //$query = ereg_replace('\(|\)|\+|\?|\[|\]|\$|\^', '',  $query);
    $query = str_replace(array("(", ")", "+", "?", "[", "]", "$", "^"), '', $query);
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
    $statement = "	SELECT p.*, t.name as category, tp.description as section, tp.name as type
					FROM $table p, product_types t, product_types tp
					WHERE p.type = t.id
					AND t.parent = tp.id ";

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

    //link for the more links section
    $link = "$script&query=" . urlencode(stripslashes($query)) . "&max=$max&by=$by&order=$order";

    //show the more links
    show_links(get_links($max, $link, $inc, 4));

    //draw the table header row
    drawHeaderRow();

    //alternating the row colours
    $shaded = true;

    //go through each row and show the results
    while ($row = $DB->fetch_array($result)) {
        showResult($row, $shaded = !$shaded);
    }

    //draw the table end header
    drawHeaderEnd();

    //show more links
    show_links(get_links($max, $link, $inc, 4));
}

//===========================================================================
// Draw the table header row, each fields can be sorted and ordered
//
// PARAMS: n/a
// RETURN: n/a
//===========================================================================
function drawHeaderRow()
{
    global $fields, $order, $by, $query, $max, $at, $page, $script;

    if (!isset($order) || '' == $order) {
        $order = 'asc';
    }

    reset($fields);

    echo '<table width="95%" align="center">' . "\n";
    echo '<tr bgcolor="#000066">' . "\n";

    //for each of the displaying fields
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);

        //draw the fields, with links for sorting and ordering
        echo '<td align="center" ' . ($by == $field ? 'bgcolor="#336699"' : '') . '><b>';
        echo "<a href=\"$script&query=" . urlencode(stripslashes($query)) . "&max=$max&at=$at&page=$page";
        echo "&by=$field&order=" . ('desc' == $order ? 'asc' : 'desc') . '" class="td_options">';
        echo $field;
        echo '</a>';

        //draw the ordering picture, either desc or asc gif
        if ($by == $field) {
            echo '  <img src="' . $order . '.gif" width="7" height="7" border="0">  ';
        }

        echo '</b></td>' . "\n";
        next($fields);
    }
    echo '<td>&nbsp;</td>' . "\n";
    echo '</tr>' . "\n";
}

//===========================================================================
// draw the table ending row
//
// PARAMS: n/a
// RETURN: n/a
//===========================================================================
function drawHeaderEnd()
{
    echo '</table>';
}

//===========================================================================
// draw the results row
//
// PARAMS: the current row data, alternating row colours
// RETURN: n/a
//===========================================================================
function showResult($cols, $shaded)
{
    global $fields, $actions, $script, $table;

    //the displaying fields
    reset($fields);

    //get the id first
    $id = $cols[key($fields)];

    $product_name = $cols['product_name'];

    $category = encode_section_url($cols['category']);
    $type     = $cols['type'];

    echo "<!-- ID: $id -->\n";

    echo $shaded ? '<tr>' : '<tr bgcolor="#EEEEEE">';

    //for each of the fields
    for ($i = 0; $i < sizeof($fields); $i++) {
        //the curreny field
        $key = key($fields);

        //field alignment
        echo "\n<td align=\"" . $fields[$key] . "\">";

        //if the column is the product name
        if ('product_name' == $key) {
            $item_link = encode_product_url($cols[$key], $cols['id']);
            echo "<a href=\"/products/$type/$category/$item_link.html\" target=\"_blank\">" . stripslashes($cols[$key]) . "</a>";
            echo "<input type='hidden' name='product[$id]' value='" . rawurlencode(unhtmlentities($cols[$key])) . "'>\n";
        } else if ('sales_price' == $key) {
            echo $cols[$key] . ' ' . $cols['sales_currency'];
        } else {
            echo stripslashes($cols[$key]);
        }

        echo '</td>';
        next($fields);
    }
    echo '<td nowrap>';

    for ($i = 0; $i < sizeof($actions); $i++) {
        if (0 != $i) {
            echo " | ";
        }

        $str = addslashes($actions[$i]);
        eval("\$str = \"$str\";");
        echo "$str\n";

    }
    echo '</td>' . "\n";
    echo "</tr>\n";
    echo "<!--// ID: $id -->\n";
}

//do a query search
if (isset($query)) {
    $query = htmlspecialchars($query);
    search(addslashes(addslashes(urldecode($query))));
}

?>
<br />
<!--
<div align="center">
<input type="button" name="Button" value="Close Product Referencing" onClick="window.close()">
</div>-->
</form>
<!--//products ref -->

</body>
</html>
<!--//footer -->
<?php
/***********************************************************************
 ** Title.........:    Products Administration
 ** Version.......:    1.1
 ** Author........:    Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:    admin.php
 ** Last changed..:    7/18/02
 ** Notes.........:
 ***********************************************************************/

$section_code = "PRODUCT";

include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

include_once $_SERVER["DOCUMENT_ROOT"] . "/webadmin/currency.php";
include_once __DIR__ . '/config.inc.php';

$table = 'products';

//this script file
$script = "search_products.php?table=$table";

//the fields for display
$fields['id']           = 'center';
$fields['product_name'] = 'left';
$fields['active']       = 'center';
$fields['cost_price']   = 'right';
$fields['sales_price']  = 'right';
$fields['qty']          = 'right';

$id_field = 'id';

//$search_fields[] = 'p.id';
$search_fields[] = 'p.product_name';

if (!isset($max)) {
    $max = '';
}

if (!isset($at)) {
    $at = 0;
}

if (!isset($page)) {
    $page = 0;
}

if (!isset($by)) {
    $by = '';
}

if (!isset($order)) {
    $order = '';
}

//the editing link containing all the variables
$edit_link = "&query=" . $_REQUEST["query"] . "&max=$max&at=$at&page=$page&by=$by&order=$order";
//$edit_link .= "&manufacturer=$manufacturer&type=$type&location=$location";

if (isset($_GET['popup'])) {
    $actions[] = '<a href="#" onclick="selectValue($id); return false;">Select</a>'; //Edit link
}

//$actions[]= '<input type="checkbox" name="price_changed[$id]" value="1">'; //price changed checkbox
$actions[] = '<a href="admin.php?table=products&parent=$pid&action=edit&id=$id' . $edit_link . '" target="_blank">Edit</a>'; //Edit link
//$actions[] = '<a href="admin.php?table=products&parent=$pidaction=delete&id=$id">Delete</a>'; //Delete link
//$actions[] = '<a href="screenshot_admin.php?table=product_screenshots&action=add&id=$id" targe="_blank">Add SS</a>'; //Add screenshots link

//get the image storage location
//$img_location = $section_details['img_location'].'/';
//$img_location_abs = $_SERVER['DOCUMENT_ROOT'] . '/'.$img_location;
?>
<style>
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

<form action="<?php echo 'search_products.php' . $gets ?>" method="get" name="admin_form">

<!-- search box -->
<table width="95%" border="0" align="center" cellpadding="1" cellspacing="0">
  <tr>
    <td bgcolor="#FF9900"><table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
        <tr>
          <td bgcolor="#FFFFCC"><div align="center"><strong><font color="#FF6600" size="4">Product
              Search</font></strong></div></td>
        </tr>
        <tr>
          <td bgcolor="#FFFFCC"><div align="center">Enter a product <strong>ID</strong>
              or the product <strong>name</strong></div></td>
        </tr>
        <tr>
          <td bgcolor="#FFFFCC"><div align="center">
          	  <?php if (isset($_GET['popup'])) {?>
          	  	<input type="hidden" name="popup" vlaue="true">
          	  <?php }?>
              <input name="query" type="text"  value="<?php echo htmlspecialchars(stripslashes($_REQUEST["query"])) ?>" size="30">
              <input type="submit" value="Search">
            </div></td>
        </tr>
      </table></td>
  </tr>
</table>
<br>
<script>
document.admin_form.query.focus();
document.admin_form.query.select();
</script>

<script type="text/javascript">
function selectValue(id) {

	if(window.opener!=null && window.opener.setReplacementProductId!=null)
		window.opener.setReplacementProductId(id);
	else if(window.parent!=null && window.parent.setReplacementProductId!=null)
		window.parent.setReplacementProductId(id);
	else
		alert('Unable to send product back to form');
	window.close();
}
</script>

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
    global $DB, $at, $fields, $order, $by, $script, $location, $type, $table, $manufacturer, $parent;
    global $search_fields;

    $query = trim($query);

    $dash_pos = strrpos($query, '-');
    if (intval($dash_pos) > 0) {
        $query = trim(substr($query, $dash_pos + 1, strlen($query)));
    }

    $id_search = false;

    if (!preg_match('/\D/', $query)) {
        $query     = intval($query);
        $id_search = true;

        echo '<div align="center">Searching for product ID : <b>' . $query . '</b></div><br><br>';
    } else {
        echo '<div align="center">Searching for product name &quot;<b>' . $query . '</b>&quot;</div><br><br>';
    }

    //$query = ereg_replace('\(|\)|\+|\?|\[|\]|\$|\^', '',  $query);
    $query = str_replace(array("(", ")", "+", "?", "[", "]", "$", "^"), '', $query);

    //keywords
    $arrKeywords = explode(" ", $query);

    $extra = '';

    //search query by key words
    if ($id_search) {
        $extra .= " AND p.id = '$query' ";
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
    $statement = "	SELECT p.*, t.name as type_name, tp.name as section_name, t.parent as pid
					FROM products p, product_types t, product_types tp
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
//echo $statement.$extra.$order_by.$limit;
    //link for the more links section
    $link = "$script&query=" . urlencode(stripslashes($query)) . "&max=$max&by=$by&order=$order";

    //show the more links
    show_links(get_links($max, $link, $inc, 4));

    //draw the table header row
    draw_header_row();

    //alternating the row colours
    $shaded = true;

    //go through each row and show the results
    while ($row = $DB->fetch_array($result)) {
        show_result($row, $shaded = !$shaded);
    }

    //draw the table end header
    draw_header_end();

    //show more links
    show_links(get_links($max, $link, $inc, 4));
}

//===========================================================================
// Draw the table header row, each fields can be sorted and ordered
//
// PARAMS: n/a
// RETURN: n/a
//===========================================================================
function draw_header_row()
{
    global $fields, $order, $by, $max, $at, $page, $script, $type, $location, $manufacturer;

    if (!isset($order) || '' == $order) {
        $order = 'asc';
    }

    reset($fields);

    echo '<br>';

    echo '<table width="95%" align="center" cellpadding="4" cellspacing="1">' . "\n";
    echo '<tr bgcolor="#000066">' . "\n";

    //for each of the displaying fields
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);

        //draw the fields, with links for sorting and ordering
        echo '<td align="center" ' . ($by == $field ? 'bgcolor="#336699"' : '') . '><b>';
        echo "<a href=\"$script&query=" . urlencode(stripslashes($_REQUEST["query"])) . "&max=$max&at=$at&page=$page";
        echo "&by=$field&order=" . ('desc' == $order ? 'asc' : 'desc') . '" class="td_options">';
        echo $field;
        echo '</a>';

        //draw the ordering picture, either desc or asc gif
        if ($by == $field) {
            echo '  <img src="/webadmin/' . $order . '.gif" width="7" height="7" border="0">  ';
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
function draw_header_end()
{
    echo '</table>';
    //echo '<div align="right"><input type="submit" name="update_price" value="Update Price"><input type="submit" name="update_qty" value="Update Qty"></div><br>'."\n";
    echo '<blockquote><font color="red">*</font> price fluctuates with currency exchange</blockquote>';
}

//===========================================================================
// draw the results row
//
// PARAMS: the current row data, alternating row colours
// RETURN: n/a
//===========================================================================
function show_result($cols, $shaded)
{
    global $fields, $actions, $script, $table; //, $section_details;

    //the displaying fields
    reset($fields);

    //get the id first
    $id = $cols[key($fields)];

    $pid = $cols['pid'];

    $product_name = $cols['product_name'];

    $type = encode_section_url($cols['type_name']);

    echo "<!-- ID: $id -->\n";

    if (locked($table, $id)) {
        echo '<tr bgcolor="pink">';
    }

//    else if (my_lock($table, $id))
    //        echo '<tr bgcolor="'.($shaded?'#FFFFEE':'#FFFFCC').'">';
    else {
        if ($shaded) {
            echo "<tr class=\"TableRow\" onMouseOver=\"pviiClassNew(this,'TableRowOver')\" onMouseOut=\"pviiClassNew(this,'TableRow')\">\n";
        } else {
            echo "<tr class=\"TableRowAlt\" onMouseOver=\"pviiClassNew(this,'TableRowOver')\" onMouseOut=\"pviiClassNew(this,'TableRowAlt')\">\n";
        }

    }

    //for each of the fields
    for ($i = 0; $i < sizeof($fields); $i++) {
        //the curreny field
        $key = key($fields);

        //field alignment
        echo "\n<td align=\"" . $fields[$key] . "\">";

        //if it is the quantity field
        if ('qty' == $key) {
            echo '<INPUT TYPE="text" NAME="qty_change[' . $id . ']" size="2" value="' . $cols[$key] . '" disabled>';
        } else if ('cost_price' == $key) {
            echo showPriceFrom($cols['cost_price'], $cols['cost_currency'], $cols['sales_currency']) . ' ' . $cols['sales_currency'];
        } else if ('sales_price' == $key) {
            if ('y' == $cols['price_flux']) {
                echo '<font color="red">*</font>';
            }

            echo '<input type="hidden" name="old_price[' . $id . ']" value="' . $cols[$key] . '" disabled>';
            echo '<input type="text" size="8" disabled name="price_change[' . $id . ']" ';
            if ('y' == $cols['price_flux']) {
                echo ' value="' . showpricefrom(floor(($cols['markup'] + 100.0) * $cols['cost_price']) / 100.0, $cols['cost_currency'], 'USD') . '" ';
            } else {
                echo ' value="' . $cols[$key] . '" ';
            }

            echo "onChange='checkPriceChanges(document.admin_form[\"price_changed[$id]\"],document.admin_form[\"old_price[$id]\"],document.admin_form[\"price_change[$id]\"])'>";
        } else if ('product_name' == $key) {

            $details_tooltip = '<table><tr>';
            if ('' != $cols['image']) {
                $details_tooltip .= '<td style="vertical-align:top;"><img src="/' . hecho($cols['image'], 1) . '" alt="" width="100" /></td>';
            }

            $details_tooltip .= '<td style="vertical-align:top;"><h2>' . hecho($cols['product_name'], 1) . '</h2>';
            $details_tooltip .= '<ul><li>ID: ' . $cols['id'] . '</li>';
            $details_tooltip .= '<li>Supplier: ' . $supplierName . '</li>';
            $details_tooltip .= '<li>Date added: ' . date('d/m/Y', strtotime($cols['date_added'])) . '</li>';
            $details_tooltip .= '<li>Last sold: </li>';
            $details_tooltip .= '</ul></td></tr></table>';

            $item_link = encode_product_url($cols[$key], $cols['id']);

            echo "<a onmouseover='";
            hecho("tt_in(" . jecho($details_tooltip, 1) . ");");
            echo "' onmouseout='tt_out();' href=\"/products/" . $section_details['name'] . "/$type/$item_link.html?preview=true\" target=\"_blank\">";
            echo stripslashes($cols[$key]);
            echo "</a>";

            /*$item_link = encode_product_url($cols[$key],$cols['id']);
        echo "<a href=\"/products/".$cols['section_name']."/$type/$item_link.html?preview=true\" target=\"_blank\">".stripslashes($cols[$key])."</a>";*/
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
if (isset($_REQUEST["query"])) {
    $_REQUEST["query"] = htmlspecialchars($_REQUEST["query"]);
    search(addslashes(addslashes(urldecode($_REQUEST["query"]))));
}

?>
</form>
<!--//products admin -->
<?php
include_once dirname(__FILE__) . '/footer.inc.php';

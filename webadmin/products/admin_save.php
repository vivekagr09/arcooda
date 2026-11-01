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

include '../currency.php';
include 'config.inc.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/functions/formatting.inc.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/functions/lists.inc.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/options/options_functions.inc.php';

if ("purge_img" == $_REQUEST['action']) {
    // Clean out the screenshots
    if ($_REQUEST['p']) {
        purge_image_cache($_REQUEST['p']);
    }

    die("Goodbye.");
}

//get the section details, specified by the parent ID
$statement       = "SELECT * FROM product_types WHERE id='" . $parent . "'";
$section_details = $DB->query_first($statement);
if ('' == $table) {
    $table = 'products';
}

//this script file
$script = "admin.php?table=" . $table . "&parent=" . $parent;

//the fields for display
$fields['id']           = 'center';
$fields['product_name'] = 'left';
$fields['active']       = 'center';
$fields['cost_price']   = 'right';
$fields['sales_price']  = 'right';
$fields['qty']          = 'right';
$id_field               = 'id';
$search_fields[]        = 'p.id';
$search_fields[]        = 'p.product_name';
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

if (!isset($manufacturer)) {
    $manufacturer = '';
}

if (!isset($location)) {
    $location = '';
}

if (!isset($type)) {
    $type = '';
}

//the editing link containing all the variables
$edit_link = "&query=" . $_REQUEST["query"] . "&max=" . $max . "&at=" . $at . "&page=" . $page . "&by=" . $by . "&order=" . $order;
$edit_link .= "&manufacturer=" . $manufacturer . "&type=" . $type . "&location=" . $location;
$actions[] = '<input type="checkbox" name="price_changed[$id]" value="1">'; //price changed checkbox
$actions[] = '<a href="$script&action=edit&id=$id' . $edit_link . '">Edit</a>'; //Edit link
$actions[] = '<a href="$script&action=delete&id=$id">Delete</a>'; //Delete link
$actions[] = '<a href="screenshot_admin.php?table=product_screenshots&action=add&id=$id" targe="_blank">Add SS</a>'; //Add screenshots link

//get the image storage location
$img_location     = $section_details['img_location'] . '/';
$img_location_abs = $_SERVER['DOCUMENT_ROOT'] . '/' . $img_location;

//do the types count
$typesSQL    = " SELECT" . "	t.name as type_name," . "	t.id," . "	count(p.id) as items" . " FROM " . $table . " p" . " INNER JOIN product_types t ON p.type=t.id" . " WHERE t.parent='" . $parent . "'" . " GROUP BY t.id" . " ORDER BY t.name";
$types_count = $DB->query($typesSQL);

//do the stock values, stock availability should be 1
$statement   = " SELECT" . "	t.name as type_name," . "	p.sales_price," . "	p.qty," . "	p.sales_currency" . " FROM " . $table . " p" . " INNER JOIN product_types t ON p.type=t.id" . " WHERE p.available=101 AND t.parent='" . $parent . "'" . " ORDER BY t.name";
$types_value = $DB->query($statement);

//do the location count
$statement      = " SELECT" . "	l.location," . "	l.id," . "	count(p.id) as items" . " FROM " . $table . " p" . " INNER JOIN product_storage_location l ON p.storage=l.id" . " INNER JOIN product_types t ON p.type=t.id" . " WHERE t.parent='" . $parent . "'" . " GROUP BY p.storage" . " ORDER BY l.location";
$location_count = $DB->query($statement);
$sql            = " SELECT" . "	s.id," . "	s.supplier," . "	COUNT(sp.product_id) AS items" . " FROM products p" . " INNER JOIN product_types pt ON p.type=pt.id" . " INNER JOIN supplier_product sp ON p.id=sp.product_id" . " INNER JOIN suppliers s ON sp.supplier_id=s.id" . " WHERE pt.parent='" . $parent . "'" . " GROUP BY s.id" . " ORDER BY s.supplier";
$supplier_count = $DB->query($sql);

//count the total items for this section
$items = $DB->query_first("SELECT COUNT(*) as total FROM " . $table . " p INNER JOIN product_types t ON p.type=t.id WHERE t.parent='" . $parent . "'");

//get the GET variables
$gets = get_vars();
?>
<!-- products admin -->
<?php
$cValues = [];
$result  = $DB->query("SELECT `code`, `usfrom` FROM `highwaygroup`.`currency`");
while ($row = $DB->fetch_assoc($result)) {
    $cValues[$row['code']] = $row['usfrom'];
}
$DB->free_result($result);
?>
<script type="text/javascript">
//<![CDATA[
var cValues = <?=jecho($cValues, 1)?>;
function setRate(tRate, tCurrency) {
	var tIndex = tCurrency.options[tCurrency.selectedIndex].value;
	tRate.value = cValues[tIndex];
}
function getRate(tCurrency) {
	return cValues[tCurrency];
}
function convert(fValue, fCurrency, tValue, tCurrency) {
	var fIndex = fCurrency.options[fCurrency.selectedIndex].value;
	var tIndex = tCurrency.options[tCurrency.selectedIndex].value;
	result = (cValues[fIndex]) / ((cValues[tIndex]) / (fValue.value));
	tValue.value = (Math.round(result * 100) / 100);
}

//check the price changes, only tick the price changed checkbox when
//the new price is lower than the old price
function checkPriceChanges(checkbox,oldPriceField,priceField)
{
	var oldPrice=parseFloat(oldPriceField.value);
	var newPrice=parseFloat(priceField.value);
	if(!isNaN(newPrice) && newPrice<oldPrice && newPrice>0)
		checkbox.checked=true;
	else
		checkbox.checked=false;
}
//]]>
</script>
<form action="admin.php<?php echo htmlspecialchars($gets) ?>" method="post" enctype="multipart/form-data" name="search_form">
	<br />
	<table width="100%" border="0" cellspacing="2" cellpadding="2">
		<tr>
			<td bgcolor="#EEEEEE" width="20%"><div align="center">
				<b><?php echo $section_details['description'] ?></b>
			</div></td>
			<td bgcolor="#EEEEEE" width="80%"><div align="right">
				<a href="javascript:MM_openBrWindow('admin_help.html','help','scrollbars=yes,width=500,height=400');">HELP</a> |
				<a href="<?php echo $script ?>&action=add">Add <?php echo $section_details['description'] ?></a> |
				<a href="<?php echo $script ?>&query=">List <?php echo $section_details['description'] ?> (<?php echo $items['total'] ?>)</a>
			</div></td>
		</tr>
	</table>

<!-- item types -->
	<script type="text/javascript" src="../expandToggle.js"></script>
	<a href="#" onclick="javascript:expandIt('types');return false;"><img name="imEx" src="/webadmin/triDown.gif" width="16" height="16" border="0" alt="Expand/Collapse All"> <b>List by Item Types</b></a>
	<br />
	<div id="typesChild" class="child">
		<table width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
<?php
$count = 0;
while ($row = $DB->fetch_array($types_count)) {
    if ($count % 4 == 0) {
        echo '<tr>';
    }

    echo ($row['id'] == $type) ? '<td bgcolor="#FFCC00">' : '<td>';
    echo '<a href="' . $script . '&query=&type=' . $row['id'] . '">' . $row['type_name'] . ' (' . $row['items'] . ')</a></td>';
    if ($count % 4 == 3) {
        echo "</tr>\n";
    }

    $count++;
}
?>
		</table>
	</div>
<!--//item types -->

	<br />

<!-- item suppliers -->
	<a href="#" onclick="javascript:expandIt('supplier');return false;"><img name="imEx" src="/webadmin/triDown.gif" width="16" height="16" border="0" alt="Expand/Collapse All"> <b>List by Suppliers</b></a>
	<br />
	<div id="supplierChild" class="child">
		<table width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
<?php
$count = 0;
while ($row = $DB->fetch_array($supplier_count)) {
    if ($count % 4 == 0) {
        echo '<tr>';
    }

    echo ($row['id'] == $manufacturer) ? '<td bgcolor="#FFCC00">' : '<td>';
    echo '<a href="' . $script . '&query=&supplier=' . $row['id'] . '">' . stripslashes($row['supplier']) . ' (' . $row['items'] . ')</a></td>';
    if ($count % 4 == 3) {
        echo "</tr>\n";
    }

    $count++;
}
?>
		</table>
	</div>
<!-- // item suppliers -->

	<br />

<!-- item locations -->
	<a href="#" onclick="javascript:expandIt('location');return false;"><img name="imEx" src="/webadmin/triDown.gif" width="16" height="16" border="0" alt="Expand/Collapse All"> <b>List by Location</b></a>
	<br />
	<div id="locationChild" class="child">
		<table width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
<?php
$count = 0;
while ($row = $DB->fetch_array($location_count)) {
    if ($count % 4 == 0) {
        echo '<tr>';
    }

    echo ($row['id'] == $location) ? '<td bgcolor="#FFCC00">' : '<td>';
    echo "<a href=\"$script&query=&location=" . $row['id'] . "\">" . $row['location'] . ' (' . $row['items'] . ')</a></td>';
    if ($count % 4 == 3) {
        echo "</tr>\n";
    }

    $count++;
}
?>
		</table>
	</div>
<!--//item locations -->

	<br />

<!-- item values -->
	<a href="#" onclick="javascript:expandIt('value');return false;"><img name="imEx" src="/webadmin/triDown.gif" width="16" height="16" border="0" alt="Expand/Collapse All"> <b>Stock Values</b></a>
	<br />
	<div id="valueChild" class="child">
		<table width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
<?php
$values = array();
while ($row = $DB->fetch_array($types_value)) {
    $type_name = $row['type_name'];
    if (!isset($values[$type_name])) {
        $values[$type_name] = 0;
    }

    $values[$type_name] += showPriceFrom($row['sales_price'], $row['sales_currency'], $_GLOBALS['site_options']['default_currency']) * $row['qty'];
}
$total = 0;
for ($i = 0; $i < count($values); $i++) {
    $key = key($values);
    echo "<tr" . ($count++ % 2 == 0 ? ' bgcolor="#EEEEEE"' : '') . ">\n";
    echo '<td nowrap="nowrap" width="25%"><b>';
    echo $key . '</b></td><td nowrap="nowrap" width="75%">$';
    printf('%01.2f', $values[$key]);
    echo "</td>";
    echo "</tr>\n";
    next($values);
    $total += $values[$key];
}
?>
			<tr>
				<td align="right"><b>Total :</b></td>
				<td>$<?php echo sprintf("%01.2f", $total) ?> <?php echo $_GLOBALS['site_options']['default_currency'] ?></td>
			</tr>
		</table>
	</div>
<!--//item values -->

	<br />

<!-- view list -->
	<a href="#" onclick="javascript:expandIt('viewlist');return false;"><img name="imEx" src="/webadmin/triDown.gif" width="16" height="16" border="0" alt="Expand/Collapse All"> <b>View/Print List</b></a>
	<br />
	<div id="viewlistChild" class="child">
		<table width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
<?php
$types_count = $DB->query($typesSQL);
$count       = 0;
echo '<tr><td><a href="list_details2.php?parent=' . $parent . '" target="_blank">View All</a></td></tr>' . "\r\n";
while ($row = $DB->fetch_array($types_count)) {
    if ($count % 4 == 0) {
        echo '<tr>';
    }

    echo ($row['id'] == $type) ? '<td bgcolor="#FFCC00">' : '<td>';
    echo '<a href="list_details2.php?parent=' . $parent . '&type=' . $row['id'] . '" target="_blank">' . $row['type_name'] . ' (' . $row['items'] . ')</a></td>';
    if ($count % 4 == 3) {
        echo "</tr>\n";
    }

    $count++;
}
?>
		</table>
	</div>
<!-- //view list -->

	<br />

<!-- view supply list -->
	<a href="#" onclick="javascript:expandIt('viewSupplylist');return false;"><img name="imEx" src="/webadmin/triDown.gif" width="16" height="16" border="0" alt="Expand/Collapse All"> <b>View Supply List</b></a>
	<br />
	<div id="viewSupplylistChild" class="child">
		<table width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
<?php
$types_count = $DB->query($typesSQL);
$count       = 0;
echo '<tr><td><a href="list_purchaser.php?parent=' . $parent . '" target="_blank">View All</a></td></tr>' . "\r\n";
while ($row = $DB->fetch_array($types_count)) {
    if ($count % 4 == 0) {
        echo '<tr>';
    }

    echo ($row['id'] == $type) ? '<td bgcolor="#FFCC00">' : '<td>';
    echo '<a href="list_purchaser.php?parent=' . $parent . '&type=' . $row['id'] . '" target="_blank">' . $row['type_name'] . ' (' . $row['items'] . ')</a></td>';
    if ($count % 4 == 3) {
        echo "</tr>\n";
    }

    $count++;
}
?>
		</table>
	</div>
<!-- //view list -->

<!-- search box -->
	<p align="right">
		<input type="text" name="query" size="20" value="<?php echo htmlspecialchars(stripslashes($_REQUEST["query"])) ?>" />
		<input type="submit" name="search" value="Search" />
	</p>
<!-- //search box -->

</form>

<?php if ('add' != $action && 'edit' != $action) { // Don't show in add/edit form. Hack for very badly written code?>
<div style="width: 95%; margin: auto;">
	<form action="admin.php<?php echo $gets ?>" method="get" style="margin: 0;">
		<input type="checkbox" name="show_stock_only" value="stock_only" <?=(isset($_GET['show_stock_only'])) ? 'checked' : ''?>> Stock Only
		<input type="checkbox" name="show_active_only" value="active_only" <?=(isset($_GET['show_active_only'])) ? 'checked' : ''?>> Active Only
		<input type="checkbox" name="show_include_discontinued" value="include_discontinued" <?=(isset($_GET['show_include_discontinued'])) ? 'checked' : ''?>> Include Discontinued
		<input type="hidden" name="parent" value="<?=(isset($_GET['parent'])) ? $_GET['parent'] : ''?>">

		<input type="submit" value="Update">
	</form>
</div>
<?php }?>

<form action="admin.php<?php echo $gets ?>" method="post" enctype="multipart/form-data" name="admin_form">
<?php
//when a action occurs
if (isset($action)) {
    switch ($action) {
        case "add":
            include 'add_form.inc.php';
            break;
        case "edit":
            include_once 'query_attachment.inc.php';
            include 'edit_form.inc.php';
            break;
        case "delete":
            include '../generic_delete_form.inc.php';
            break;
        case "do_add":
            do_add();
            //user wants to add more products
            if ('true' == $go_next) {
                include 'add_form.inc.php';
            }

            break;
        case "do_edit":
            //row must not be locked to update
            if (!locked($table, $id)) {
                do_edit();

                include_once 'query_attachment.inc.php';
                include 'edit_form.inc.php';
            }
            //user wants to edit the next product
            /*if ($go_next == 'true')
            {
            //get the next product ID
            $id = get_nextID();
            //lock the table straight away
            lock();
            include 'edit_form.inc.php';
            }*/
            break;

        case "do_delete":
            //row must not be locked to delete
            if (!locked($table, $id)) {
                do_delete();
            }

            break;
    }
}
//do qty updates
if (isset($update_qty) && 'Update Qty' == $update_qty) {
    echo 'Updated Qty<br />';
    update_qty();
}
//do price updates
if (isset($update_price) && 'Update Price' == $update_price) {
    echo 'Updated Prices<br />';
    update_price();
}
//do a query search
if (((isset($_REQUEST["query"]) && (isset($action) && '' == $action)) || !isset($action)) || (isset($go_next) && 'false' == $go_next) || intval($id) < 0) {
    $_REQUEST["query"] = htmlspecialchars($_REQUEST["query"]);
    search(addslashes(addslashes(urldecode($_REQUEST["query"]))));
}
?>
</form>
<!--//products admin -->
<?php
include '../footer.inc.php';

//===========================================================================
// search through the sections and display the results in a table format
//
// PARAMS: the query string
// RETURN: n/a
//===========================================================================
function search($query)
{
    global $DB, $at, $fields, $order, $by, $script, $location, $type, $table, $supplier, $parent;
    global $search_fields;
    $query = trim($query);
    //$query = ereg_replace('\(|\)|\+|\?|\[|\]|\$|\^', '', $query);
    $query = str_replace(array("(", ")", "+", "?", "[", "]", "$", "^"), '', $query);
    //keywords
    $arrKeywords = explode(" ", $query);
    $extra       = '';
    //search query by key words
    for ($index = 0; $index < count($arrKeywords); $index++) {
        $word = trim(strtolower($arrKeywords[$index]));
        if (!empty($word) && '|' != $word) {
            $extra .= " AND ";
            //do a regular expression search on each of the search fields, case insensitive
            for ($i = 0; $i < count($search_fields); $i++) {
                $extra .= (0 == $i) ? '(' : ' OR ';
                $extra .= " LOWER(" . $search_fields[$i] . ") REGEXP '" . $word . "' ";
            }
            if (count($search_fields) > 0) {
                $extra .= ')';
            }

        }
    } //end search query keywords
    //if the location is specified
    if ('' != $location) {
        $extra .= " AND p.storage='" . $location . "' ";
    }

    //if the type is specified
    if ('' != $type) {
        $extra .= " AND p.type='" . $type . "' ";
    }

    if ("" != $supplier) {
        $extraWhere = ", supplier_product sp";
        $extra .= " AND sp.supplier_id='" . $supplier . "' AND p.id=sp.product_id ";
    }

    if (isset($_GET['show_stock_only']) && !empty($_GET['show_stock_only'])) {
        $extra .= " AND p.stock_type = 'stock_for_sale'";
    }

    if (isset($_GET['show_active_only']) && !empty($_GET['show_active_only'])) {
        $extra .= " AND p.active = 'y'";
    }

    if (!isset($_GET['show_include_discontinued']) || empty($_GET['show_include_discontinued'])) {
        $extra .= " AND p.replacement = 'n'";
    }

    //search statement
    $statement = " SELECT p.*, t.name as type_name" . " FROM products p" . " INNER JOIN product_types t ON p.type=t.id" . " " . $extraWhere . " WHERE t.parent='" . $parent . "' ";
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
    $link = $script . '&query=' . urlencode(stripslashes($query)) . '&max=' . $max . '&by=' . $by . '&order=' . $order . '&type=' . $type . '&location=' . $location . '&manufacturer=' . $manufacturer;
    if ("" != $supplier) {
        $link .= "&supplier=" . $supplier;
    }

    //show the more links
    //show_links(get_links($max, $link, $inc, 4));
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
// get the next editable ID
//
// PARAMS: n/a
// RETURN: the next ID
//===========================================================================
function get_nextID()
{
    global $DB, $at, $fields, $order, $by, $script, $location, $type, $table, $manufacturer, $parent;
    global $search_fields, $id;
    $current_item      = $DB->query_first("SELECT * FROM $table WHERE id = '$id'");
    $_REQUEST["query"] = addslashes(addslashes(trim($_REQUEST["query"])));
    //keywords
    $arrKeywords = explode(" ", $_REQUEST["query"]);
    $extra       = '';
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
    //if the location is specified
    if ('' != $location) {
        $extra .= " AND p.storage = '$location' ";
    }

    //if the type is specified
    if ('' != $type) {
        $extra .= " AND p.type = '$type' ";
    }

    //when the manufacturer is specified
    if ('' != $manufacturer) {
        $extra .= " AND p.manufacturer = '$manufacturer' ";
    }

    //search statement
    $statement = "SELECT p.*, t.name as type_name FROM $table p, product_types t WHERE p.type = t.id AND p.id != '$id' AND t.parent = '$parent'";
    //default ordering is ascending
    if (!isset($order) || '' == $order) {
        $order = 'asc';
    }

    //default ordering by product name
    if (!isset($by) || '' == $by) {
        $by = 'product_name';
    }

    //if the ordering is ascending then find the next row greater then the current row
    if ('asc' == $order) {
        $clause = " AND p.$by > '" . htmlspecialchars($current_item[$by]) . "' ";
    } else {
        $clause = " AND p.$by < '" . htmlspecialchars($current_item[$by]) . "' ";
    }

    $order_by = " ORDER BY $by $order"; //order clause
    $limit    = " LIMIT 0, 1"; //limit clause
    //do the query, return only the first row
    $result = $DB->query_first($statement . $clause . $extra . $order_by . $limit);
    //we get the next ID
    return $result['id'];
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
    echo "<br/>";
    //echo '<br /><div align="right"><input type="submit" name="update_price" value="Update Price"><input type="submit" name="update_qty" value="Update Qty"></div>' . "\n";
    echo '<table width="95%" align="center">' . "\n";
    echo '<tr bgcolor="#000066">' . "\n";
    //for each of the displaying fields
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        //draw the fields, with links for sorting and ordering
        echo '<td align="center" ' . ($by == $field ? 'bgcolor="#336699"' : '') . '><b>';
        echo "<a href=\"$script&query=" . urlencode(stripslashes($_REQUEST["query"])) . "&max=$max&at=$at&page=$page";
        echo "&type=$type&manufacturer=$manufacturer&location=$location&by=$field&order=" . ('desc' == $order ? 'asc' : 'desc') . '" class="td_options">';
        echo $field;
        echo '</a>';
        //draw the ordering picture, either desc or asc gif
        if ($by == $field) {
            echo '  <img src="/webadmin/' . $order . '.gif" width="7" height="7" border="0">  ';
        }

        echo '</b></td>' . "\n";
        next($fields);
    }
    echo '<td> </td>' . "\n";
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
    echo '<div align="right"><input type="submit" name="update_price" value="Update Price"><input type="submit" name="update_qty" value="Update Qty"></div><br />' . "\n";
    echo '<blockquote><font color="red">*</font> price fluctuates with currency exchange</blockquote>';
    echo '
	<center>
	<span style="font-style:italic;">Notes:</span><br>
	Cells highlighted in <span style="background-color:#d3d3d3;">light gray</span> are discontinued products.<br>
	Cells highlighted in <span style="background-color:#BBFFBB;">light green</span> are stock for sale.<br>
	Cells highlighted in <span style="background-color:#EEBBEE;">light purple</span> are consignment products.<br>
	Cells highlighted in <span style="background-color:#FEDC6C;">light orange</span> are stock sample.<br>
	Cells highlighted in <span style="background-color:#EE90A0;">red</span> are repair item.<br>
	</center><br>
	';
}
//===========================================================================
// draw the results row
//
// PARAMS: the current row data, alternating row colours
// RETURN: n/a
//===========================================================================
function show_result($cols, $shaded)
{
    global $fields, $actions, $script, $table, $section_details, $DB;
    //the displaying fields
    reset($fields);
    //get the id first
    $id           = $cols[key($fields)];
    $product_name = $cols['product_name'];
    $type         = encode_section_url($cols['type_name']);
    echo "<!-- ID: $id -->\n";
    if (locked($table, $id)) {
        echo '<tr bgcolor="pink">';
    }

    //    else if (my_lock($table, $id))
    //        echo '<tr bgcolor="'.($shaded?'#FFFFEE':'#FFFFCC').'">';
    else {
        echo $shaded ? '<tr>' : '<tr bgcolor="#EEEEEE">';
    }

    // Colors
    $color = "";
    switch ($cols['stock_type']) {
        case "consignment":
            $color = "#EEBBEE";
            break;
        case "stock_for_sale":
            $color = "#BBFFBB";
            break;
        case "stock_sample":
            $color = "#FEDC6C";
            break;
        case "repair_item":
            $color = "#EE90A0";
            break;
    }

    if ('y' == $cols['replacement']) {
        $color = "#d3d3d3";
    }

    //for each of the fields
    for ($i = 0; $i < sizeof($fields); $i++) {
        //the curreny field
        $key = key($fields);
        //field alignment
        $style = ('id' == $key) ? "style='background: {$color};'" : "";
        echo "\n<td align=\"" . $fields[$key] . "\" {$style}>";
        //if it is the quantity field
        if ('qty' == $key) {
            echo '<INPUT TYPE="text" NAME="qty_change[' . $id . ']" size="2" value="' . $cols[$key] . '">';
        } elseif ('cost_price' == $key) {
            echo showPriceFrom($cols['cost_price'], $cols['cost_currency'], $cols['sales_currency']) . ' ' . $cols['sales_currency'];
        } elseif ('sales_price' == $key) {
            if ('y' == $cols['price_flux']) {
                echo '<font color="red">*</font>';
            }

            echo '<input type="hidden" name="old_price[' . $id . ']" value="' . $cols[$key] . '" >';
            echo '<input type="text" size="8" name="price_change[' . $id . ']" ';
            if ('y' == $cols['price_flux']) {
                echo 'value="' . showpricefrom(floor($cols['cost_price'] * (100 + $cols['markup'])) / 100.0, $cols['cost_currency'], 'USD') . '" ';
            } else {
                echo 'value="' . $cols[$key] . '" ';
            }

            echo "onChange='checkPriceChanges(document.admin_form[\"price_changed[$id]\"],document.admin_form[\"old_price[$id]\"],document.admin_form[\"price_change[$id]\"])'>";
        } elseif ('product_name' == $key) {

            $supplier = $DB->query_first("SELECT * FROM supplier_product WHERE product_id = " . $cols['id']);

            $supplierName = '';

            if (count($supplier) > 0) {
                if ($supplier['supplier_id']) {
                    $supplierDetail = $DB->query_first(sprintf(
                        "SELECT `supplier` FROM `suppliers` WHERE `id` = %d",
                        $supplier['supplier_id']
                    ), MYSQLI_ASSOC);

                    if ($supplierDetail) {
                        $supplierName = $supplierDetail['supplier'];
                    }

                }
            }

            $details_tooltip = '<table><tr>';
            if ('' != $cols['image']) {
                $details_tooltip .= '<td style="vertical-align:top;"><img src="/' . hecho($cols['image'], 1) . '" alt="" width="100" /></td>';
            }

            $details_tooltip .= '<td style="vertical-align:top;"><h2>' . hecho($cols['product_name'], 1) . '</h2>';
            $details_tooltip .= '<ul><li>ID: ' . $cols['id'] . '</li>';
            $details_tooltip .= '<li>Supplier: ' . $supplierName . '</li>';
            $details_tooltip .= '<li>Date added: ' . date('d/m/Y', strtotime($cols['supplier'])) . '</li>';
            $details_tooltip .= '<li>Last sold: </li>';
            $details_tooltip .= '</ul></td></tr></table>';

            $item_link = encode_product_url($cols[$key], $cols['id']);

            echo "<a onmouseover='";
            hecho("tt_in(" . jecho($details_tooltip, 1) . ");");
            echo "' onmouseout='tt_out();' href=\"/products/" . $section_details['name'] . "/$type/$item_link.html?preview=true\" target=\"_blank\">";
            echo stripslashes($cols[$key]);
            echo "</a>";
            //echo "<a href=\"/products/" . $section_details['name'] . "/$type/$item_link.html?preview=true\" target=\"_blank\">" . stripslashes($cols[$key]) . "</a>";
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
function purge_image_cache($pid)
{
    global $_GLOBALS;
    // Remove old cached images
    // Generate cache tag
    $fname = sys_get_temp_dir() . "/image.pid-$pid.ssid-*.wmp-?.thumb-?.*";
    // Unlink anything that matches
    foreach (glob($fname) as $fn) {
        echo "Removing cache entry: $fn<br />\r\n";
        unlink($fn);
    }
    if (!empty($_GLOBALS["site_settings"]["static_path"] ?? '')) {
        foreach (glob($_GLOBALS["site_settings"]["static_path"] . "/images/products/*/" . $pid . ".*") as $fn) {
            echo "Removing cache entry: $fn<br />\r\n";
            unlink($fn);
        }
    }
}

//===========================================================================
// add a new product to the database
//
// PARAMS: n/a
// RETURN: n/a
//===========================================================================
function do_add()
{
    global $table, $DB, $data1, $data2, $images;
    global $parent, $section_details, $img_location;
    global $related, $admin_notes;
    //get all the data for the PRODUCTS tables, do the query
    $statement = "INSERT INTO $table (" . get_fields($data1) . ", date_added, last_modified) VALUES (" . get_values($data1) . ", NOW(), NOW())";
    //add the new datas to the PRODUCTS table
    $result = $DB->query($statement);
    //get the newly inserted ID
    $id = $DB->insert_id($result);
    purge_image_cache($id);
    //added the admin notes
    $admin_notes = trim(addslashes($admin_notes));
    if ('' != $admin_notes) {
        //get the user name
        $user = get_user();
        //insert the admin note into the database
        $statement = "INSERT INTO product_admin_notes (product_id, date_added, user, note)
						VALUES ('$id', NOW(), '$user', '$admin_notes')";
        $DB->query($statement);
    }
    //if the images are present, then process the image(s) and udpate the record
    if (is_file($images['image'])) {
        $img = process_image($images['image'], 'img_' . $parent . '_' . $id);
        if ($img) {
            $DB->query("UPDATE $table SET image = '$img_location$img' WHERE id = '$id'");
        }
    }
    //now for the supplier, update the supplier_product table if the
    //supplier id is greater than 0
    $supplier_id = intval($data2['supplier']);
    if ($supplier_id > 0) {
        $DB->query("INSERT IGNORE INTO supplier_product (supplier_id, product_id) VALUES ('$supplier_id', '$id')");
    }
    // now add the loading info
    // Added: 8/7/05 - By: Jwalit Shah
    global $loading20_packed, $loading40_packed, $loading20_unpacked, $loading40_unpacked;
    global $qty20_packed, $qty40_packed, $qty20_unpacked, $qty40_unpacked;
    $q = " INSERT INTO container_load" . " VALUES (" . "	'$id'," . "	'$qty20_packed'," . "	'$qty40_packed'," . "	'$loading20_packed'," . "	'$loading40_packed'," . "	'$qty20_unpacked'," . "	'$qty40_unpacked'," . "	'$loading20_unpacked'," . "	'$loading40_unpacked'" . ")";
    $DB->query($q);
    //now for the related product sections/type
    add_update_related($related, $id);
    //Inserts Dims
    $height = $_POST['height1'];
    $depth  = $_POST['depth1'];
    $width  = $_POST['width1'];
    $weight = $_POST['volWt'];
    $acwth  = $_POST['actProdWt'];
    $DB->query("INSERT INTO products_dimen(width,depth,height,id,volume_weight,weight) VALUES('$width','$depth','$height','$id','$weight','$acwth');");
    $pack_width  = $_POST["pack_width"];
    $pack_height = $_POST["pack_height"];
    $pack_depth  = $_POST["pack_depth"];
    $pack_weight = $_POST["pack_weight"];
    $DB->query("DELETE FROM product_packing WHERE product_id='$id'");
    foreach ($pack_width as $key => $width) {
        $height = $pack_height[$key];
        $depth  = $pack_depth[$key];
        $weight = $pack_weight[$key];
        if ($width > 0 || $depth > 0 || $height > 0 || $width > 0) {
            $DB->query("INSERT INTO product_packing(product_id, width, depth, height, weight) VALUES('$id','$width','$depth','$height','$weight');");
        }

    }
    //Manuals
    //    $manual_dir=$_SERVER['DOCUMENT_ROOT'] . '/manuals/';
    //    $row=$DB->query_first("SELECT COUNT(*) FROM product_manuals WHERE product_id='".$id."'",MYSQLI_NUM);
    //    if($row[0]!=1) $DB->query("INSERT INTO product_manuals(product_id) VALUES('".$id."')");
    //    if(move_uploaded_file($_FILES['manual']['tmp_name']['fac_manual'],$manual_dir.'fac_'.$id))
    //        $DB->query("UPDATE product_manuals SET fac_manual='".addslashes($_FILES['manual']['name']['fac_manual'])."', fac_mimetype='".addslashes($_FILES['manual']['type']['fac_manual'])."' WHERE product_id='".$id."'");
    //    if(move_uploaded_file($_FILES['manual']['tmp_name']['coe_manual'],$manual_dir.'coe_'.$id))
    //        $DB->query("UPDATE product_manuals SET coe_manual='".addslashes($_FILES['manual']['name']['coe_manual'])."', coe_mimetype='".addslashes($_FILES['manual']['type']['coe_manual'])."' WHERE product_id='".$id."'");
    echo "New " . $section_details['description'] . " $id Added <br />";
} // end of do_add
//===========================================================================
// update product details
//
// PARAMS: n/a
// RETURN: n/a
//===========================================================================
function do_edit()
{
    global $id, $table, $DB, $data1, $data2, $images;
    global $parent, $section_details, $img_location;
    global $related, $update_prices, $prices, $admin_notes;
    $previous_product = $DB->query_first("SELECT * FROM $table WHERE id='$id'", MYSQLI_ASSOC);
    $posted           = $_POST;
    $fields           = getTableColumns($table);
    $set_vals         = array();
    foreach ($fields as $key => $value) {
        if (isset($posted['data1'][$key])) {
            $set_vals[] = "`" . $key . '`=' . formatSQLString(trim(rtrim($posted['data1'][$key])));
        }
    }

    $statement = "UPDATE " . $table . " SET " . implode(', ', $set_vals) . " WHERE id=" . intval($id) . " LIMIT 1";
    if ('' != $posted['ss_order_ref']) {
        foreach (explode(",", $posted['ss_order_ref']) as $key => $ssid) {
            $DB->query("UPDATE `product_screenshots` SET rank=" . intval($key + 1) . " WHERE ss_id=$ssid");
        }
    }

    /**
     *    Upload images for fb
     */
    if (isset($_FILES['fb'])) {
        $name      = $_FILES['fb']['name'];
        $nameChunk = explode('.', $name);
        $ext       = end($nameChunk);
        $ext       = strtolower($ext);

        if (isExtValid($ext)) {

            $name       = "p" . $id;
            $uploadName = $name . "." . $ext;

            fbImgDelete($name);

            if (!move_uploaded_file($_FILES['fb']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/img_fb/' . $uploadName)) {
                // Error report here. Not sure where is error report. (Confused)
            }
        } else {
            // Error report here. Not sure where is error report. (Confused)
        }
    }

    // Upload twitter image
    if (isset($_FILES['tt'])) {
        $name      = $_FILES['tt']['name'];
        $nameChunk = explode('.', $name);
        $ext       = end($nameChunk);
        $ext       = strtolower($ext);

        if (isExtValid($ext)) {

            $name = "p" . $id;

            $uploadName = $name . "." . $ext;

            ttImgDelete($name);

            if (!move_uploaded_file($_FILES['tt']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/img_tt/' . $uploadName)) {
                // Error report here. Not sure where is error report. (Confused)
            }
        } else {
            // Error report here. Not sure where is error report. (Confused)
        }
    }

    /*
    //update all the new products record
    $statement = "UPDATE $table SET ".get_set_values($temp_data1)." WHERE id = '$id'";
     */
    $DB->query($statement);
    //added the admin notes
    $admin_notes = trim(addslashes($admin_notes));
    if ('' != $admin_notes) {
        //get the user name
        $user = get_user();
        //insert the admin note into the database
        $statement = "INSERT INTO product_admin_notes (product_id, date_added, user, note)
						VALUES ('$id', NOW(), '$user', '$admin_notes')";
        $DB->query($statement);
    }
    //check for new image(s), update record if neccessary
    if (is_file($images['image'])) {
        purge_image_cache($id);
        $img = process_image($images['image'], 'img_' . $parent . '_' . $id);
        if ($img) {
            $DB->query("UPDATE $table SET image = '$img_location$img' WHERE id = '$id'");
        }
    }
    //if we need to update the prices
    if (intval($update_prices) == 1) {
        $DB->query("UPDATE $table SET old_price = sales_price, price_changed = NOW() WHERE id = '$id'");
        $statement = "UPDATE $table SET " . get_set_values($prices) . ", last_modified = NOW() WHERE id = '$id'";
        $DB->query($statement);
    }
    //need to check the suppliers, we remembered the old supplier ID from the
    //edit form, when the supplier ID changes, we update the supplier_product record
    $supplier_id     = intval($data2['supplier']);
    $old_supplier_id = intval($data2['old_supplier']);
    if ($old_supplier_id != $supplier_id) {
        $DB->query("DELETE FROM supplier_product WHERE product_id = '$id' AND supplier_id = '$old_supplier_id'");
        if ($supplier_id > 0) {
            $DB->query("INSERT IGNORE INTO supplier_product (supplier_id, product_id) VALUES ('$supplier_id', '$id')");
        }

    }
    // now update the loading information
    // Added: 8/7/05 - Jwalit Shah
    global $loading20_packed, $loading40_packed, $loading20_unpacked, $loading40_unpacked;
    global $qty20_packed, $qty40_packed, $qty20_unpacked, $qty40_unpacked;
    // firstly have to check if the row for this product exists...
    $str = "SELECT * FROM container_load WHERE product_id = '$id'";
    $res = $DB->query_first($str);
    if (empty($res)) {
        // ie no values in the DB, so create a new row
        $q = " INSERT INTO container_load" . " VALUES (" . "	'$id'," . "	'$qty20_packed'," . "	'$qty40_packed'," . "	'$loading20_packed'," . "	'$loading40_packed'," . "	'$qty20_unpacked'," . "	'$qty40_unpacked'," . "	'$loading20_unpacked'," . "	'$loading40_unpacked'" . ")";
        $DB->query($q);
    } else {
        $q = " UPDATE container_load" . " SET" . "	contload_20ft_qty='$qty20_packed'," . "	contload_40ft_qty='$qty40_packed'," . "	contload_20ft_type='$loading20_packed'," . "	contload_40ft_type='$loading40_packed'," . "	freeload_20ft_qty='$qty20_unpacked'," . "	freeload_40ft_qty='$qty40_unpacked'," . "	freeload_20ft_type='$loading20_unpacked'," . "	freeload_40ft_type='$loading40_unpacked'" . " WHERE product_id='$id'";
        $DB->query($q);
    }
    //now for the related product sections/type
    add_update_related($related, $id);
    echo "<span style='color: red;'>" . $section_details['description'] . " $id Updated</span> <br />";
    //--------------------------------------------------------------------------------
    $height = $_POST['height1'];
    $depth  = $_POST['depth1'];
    $width  = $_POST['width1'];
    $weight = $_POST['volWt'];
    $acwth  = $_POST['actProdWt'];
    $DB->query("DELETE FROM products_dimen WHERE id=$id;");
    $DB->query("INSERT INTO products_dimen(width,depth,height,id,volume_weight,weight) VALUES('$width','$depth','$height','$id','$weight','$acwth');");
    $pack_width  = $_POST["pack_width"];
    $pack_height = $_POST["pack_height"];
    $pack_depth  = $_POST["pack_depth"];
    $pack_weight = $_POST["pack_weight"];
    $DB->query("DELETE FROM product_packing WHERE product_id='$id'");
    if (is_array($pack_width) && count($pack_width) > 0) {
        foreach ($pack_width as $key => $width) {
            $height = $pack_height[$key];
            $depth  = $pack_depth[$key];
            $weight = $pack_weight[$key];
            if ($width > 0 || $depth > 0 || $height > 0 || $width > 0) {
                $DB->query("INSERT INTO product_packing(product_id, width, depth, height, weight) VALUES('$id','$width','$depth','$height','$weight');");
            }

        }
    }

    //Bulk Pricing
    $product_bulkpricing = productBulkPricingFromPost($_POST);
    productBulkPricingToDatabase($id, $product_bulkpricing);

    //Manuals
    //    $manual_dir=$_SERVER['DOCUMENT_ROOT'] . '/manuals/';
    //    $row=$DB->query_first("SELECT COUNT(*) FROM product_manuals WHERE product_id='".$id."'",MYSQLI_NUM);
    //    if($row[0]!=1) $DB->query("INSERT INTO product_manuals(product_id) VALUES('".$id."')");
    //    if(move_uploaded_file($_FILES['manual']['tmp_name']['fac_manual'],$manual_dir.'fac_'.$id))
    //        $DB->query("UPDATE product_manuals SET fac_manual='".addslashes($_FILES['manual']['name']['fac_manual'])."', fac_mimetype='".addslashes($_FILES['manual']['type']['fac_manual'])."' WHERE product_id='".$id."'");
    //    if(move_uploaded_file($_FILES['manual']['tmp_name']['coe_manual'],$manual_dir.'coe_'.$id))
    //        $DB->query("UPDATE product_manuals SET coe_manual='".addslashes($_FILES['manual']['name']['coe_manual'])."', coe_mimetype='".addslashes($_FILES['manual']['type']['coe_manual'])."' WHERE product_id='".$id."'");
    //---------------------------------------------------------------------------------
    $updated_product = $DB->query_first("SELECT * FROM $table WHERE id='$id'", MYSQLI_ASSOC);
    $link_ignore     = array('price_changed', 'old_price', 'approx_cost_exch_rate', 'last_modified');
    foreach ($link_ignore as $key) {
        unset($previous_product[$key]);
        unset($updated_product[$key]);
    }
    $link_names = array('active' => 'Active', 'cost_currency' => 'Cost Currency', 'cost_price' => 'Cost Price', 'date_available' => 'Date Available', 'dimension' => 'Dimensions', 'factory_id' => 'Factory ID', 'image' => 'Product Image', 'long_desc' => 'Long Description', 'metres3_packed' => 'Cubic Metres (with crating)', 'metres3_unpacked' => 'Cubic Metres (without crating)', 'min_order' => 'Minimum Order', 'notes' => 'Notes', 'power_consumption' => 'Power Consumption', 'product_details' => 'Product Details', 'product_name' => 'Product Name', 'qty' => 'Qty', 'sales_price' => 'Sales Price', 'sample_currency' => 'Sample Currency', 'sample_price' => 'Sample Price', 'sample_qty' => 'Sample Qty', 'short_desc' => 'Short Description', 'weight' => 'Shipping Weight (Kgs)', 'weight_inc' => 'Increment (Kgs)');
    $changes    = array();
    foreach ($updated_product as $key => $value) {
        if ($previous_product[$key] != $updated_product[$key]) {
            if ('' != $link_names[$key]) {
                $changes[] = $link_names[$key] . ' changed from ' . $previous_product[$key] . ' to ' . $value;
            } else {
                $changes[] = $key . ' changed from ' . $previous_product[$key] . ' to ' . $value;
            }
        }
    }

    if (count($changes) > 0) {
        mail('sales@arcooda.com', 'Coinop Express Product Change', 'The following changes were made to product ' . $updated_product['product_name'] . ' - ' . $id . "\n\n" . implode("\n", $changes), 'From: backup@highwaygroup.net');
    }

}
//===========================================================================
// add and/or update the related product records
//
// PARAMS: the related 2-d array, and the product ID
// RETURN: return true if successful, false otherwise
//===========================================================================
function add_update_related($related, $id)
{
    global $DB;
    //first delete all the records of the current product from the
    //products_related table
    $DB->query("DELETE FROM products_related WHERE product_id = '$id'");
    //need to check that the related array is indeed an array
    if (!is_array($related)) {
        return false;
    }
    //it was not an array
    //now for each of the related products
    for ($i = 0; $i < count($related); $i++) {
        $key = key($related);
        //get a related product detail
        $details = $related[$key];
        //make sure the type id is not empty
        if (intval($details['type_id']) > 0) {
            $statement = "INSERT INTO products_related (product_id, " . get_fields($details) . ") VALUES ('$id', " . get_values($details) . ")";
            $DB->query($statement);
        }
        //next related product details
        next($related);
    }
}
//===========================================================================
// delete a product from the database
//
// PARAMS: n/a
// RETURN: n/a
//===========================================================================
function do_delete()
{
    global $id, $DB, $table;
    $statement = "DELETE FROM $table WHERE id = '$id'";
    $DB->query($statement);
    purge_image_cache($id);
    echo "Product : $id Deleted.<br/>\n";
}
//===========================================================================
// update all the changes in quantity
//
// PARAMS: n/a
// RETURN: n/a
//===========================================================================
function update_qty()
{
    global $DB, $qty_change;
    //go through each of the qty
    for ($i = 0; $i < sizeof($qty_change); $i++) {
        $key = key($qty_change);
        //if the qty has changed, update the database
        if ('' != $qty_change[$key]) {
            update_part_qty($key, $qty_change[$key]);
        }
        next($qty_change);
    }
    reset($qty_change);
}
//===========================================================================
// update the qty of a product
//
// PARAMS: the ID, the new qty
// RETURN: n/a
//===========================================================================
function update_part_qty($id, $qty_string)
{
    global $DB, $table;
    $qty_clause = '';
    //get the qty
    $int_qty = intval($qty_string);
    //get the sign of the input
    $sign = substr($qty_string, 0, 1);
    //zero the qty
    if ('0' == $qty_string) {
        $qty_clause = "qty = 0";
    }

    //it is a + or - sign, then add or substract the qty from the database
    else if ('+' == $sign || '-' == $sign) {
        if ($int_qty < 0) {
            $qty_clause = "qty = qty $int_qty";
        }
        //minus
        else {
            $qty_clause = "qty = qty + $int_qty";
        }
        //add

    }
    //or just plain numbers, then change the qty value in the database
    else if ('+' != $sign && '-' != $sign && $int_qty > 0) {
        $qty_clause = "qty = $int_qty";
    }
    //check the clause, must not be empty, and the current row is not locked
    if ('' != $qty_clause && !locked($table, $id)) {
        //update the qty
        $DB->query("UPDATE $table SET $qty_clause WHERE id = '$id'");
    }
}
//===========================================================================
// update all the price changes
//
// PARAMS: n/a
// RETURN: n/a
//===========================================================================
function update_price()
{
    global $DB, $price_change, $price_changed;
    //for each of the rows
    for ($i = 0; $i < sizeof($price_change); $i++) {
        $key = key($price_change);
        //if the price has changed
        if ('' != $price_change[$key]) {
            //update the price
            update_part_price($key, $price_change[$key], intval($price_changed[$key]));
        }
        next($price_change);
    }
    reset($price_change);
}
//===========================================================================
// update the price of a row
//
// PARAMS: the ID, the new price, note price changes
// RETURN: n/a
//===========================================================================
function update_part_price($id, $sales_price_string, $price_changed)
{
    global $DB, $table;
    $sales_price_clause = '';
    //get the price
    $double_sales_price = doubleval($sales_price_string);
    //look for a + or - sign
    $sign = substr($sales_price_string, 0, 1);
    if ('0' == $sales_price_string) {
        $sales_price_clause = "sales_price = 0";
    }

    //do add or substract update calculations
    else if ('+' == $sign || '-' == $sign) {
        if ($double_sales_price < 0) {
            $sales_price_clause = "sales_price = sales_price $double_sales_price";
        } else {
            $sales_price_clause = "sales_price = sales_price + $double_sales_price";
        }

    }
    //or just change the old price to the new price
    else if ('+' != $sign && '-' != $sign && $double_sales_price > 0) {
        $sales_price_clause = "sales_price = $double_sales_price";
    }
    //if the row is not locked and the price changing clause is set
    if ('' != $sales_price_clause && !locked($table, $id)) {
        //if we need to note the price changes, ie. remember the old price
        //and when we change the price
        if (1 == $price_changed) {
            //copy the old price, and note the time this happened
            $sales_price_clause .= ", price_changed = NOW() ";
            $DB->query("UPDATE $table SET old_price = sales_price WHERE id = '$id'");
        }
        //now update the price changes
        $DB->query("UPDATE $table SET $sales_price_clause WHERE id = '$id'");
    }
}
//===========================================================================
// return a string of category - type options
//
// PARAMS: default selected ID
// RETURN: category - type HTML options
//===========================================================================
function get_category_type($selected = '')
{
    global $DB;
    $contents  = '';
    $statement = "SELECT t.*, c.description as parent_description
					FROM product_types t, product_types c
					WHERE t.parent = c.id
					ORDER BY parent_description ASC, t.name ASC
					";
    $result = $DB->query($statement);
    while ($row = $DB->fetch_array($result)) {
        $contents .= '<option value="' . $row['id'] . '"';
        if ('' != $selected && $selected == $row['id']) {
            $contents .= ' selected';
        }

        $contents .= '>' . stripslashes($row['parent_description'] . ' - ' . $row['name']) . '</option>' . "\n";
    }
    $DB->free_result($result);
    return $contents;
}

<?php
/***********************************************************************
 ** Title.........:    Products Administration
 ** Version.......:    1.1
 ** Author........:    Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Author........:  Kelvin <khtat@highwaygroup.net>
 ** Filename......:    admin2.php
 ** Last changed..:    02/08/2017
 ** Notes.........:
 ***********************************************************************/

$section_code = "STOCK";

include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

include '../currency.php';
include 'config.inc.php';

//get the section details, specified by the parent ID
$statement       = "SELECT * FROM product_types WHERE id = '$parent'";
$section_details = $DB->query_first($statement);

//this script file
$script = "admin2.php?table=$table&parent=$parent";

//the fields for display
$fields['id']           = 'center';
$fields['stock_cod']    = 'center';
$fields['coe_id']       = 'center';
$fields['product_name'] = 'left';
//$fields['category'] = 'center';
$fields['country'] = 'left';
//$fields['active'] = 'center';
$fields['unit_price'] = 'right';
//$fields['total_unit_price'] = 'right';
$fields['sample'] = 'right';
$fields['stock']  = 'right';
$fields['order']  = 'right';
//$fields['qty'] = 'right';

$id_field = 'id';

$search_fields[] = 's.id'; //p.id
$search_fields[] = 's.product_name';
$search_fields[] = 's.coe_id'; //p.id

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
$edit_link = "&query=" . $_REQUEST["query"] . "&max=$max&at=$at&page=$page&by=$by&order=$order";
$edit_link .= "&manufacturer=$manufacturer&type=$type&location=$location";

//$actions[]= '<input type="checkbox" name="qty_changed[$id]" value="0">'; //price changed checkbox
$actions[] = '<a href="$script&action=edit&id=$id' . $edit_link . '">Edit</a>'; //Edit link
$actions[] = '<a href="$script&action=delete&id=$id">Delete</a>'; //Delete link

//get the image storage location
$img_location     = $section_details['img_location'] . '/';
$img_location_abs = $_SERVER['DOCUMENT_ROOT'] . '/' . $img_location;

if ('' == $table) {
    $table = 'stock';
}

//do the company count
$companySQL = "SELECT c.name as comp_name, c.id, count(s.id) as items
				FROM $table s, stock_company_stock c
				WHERE s.company_stock = c.id
				GROUP BY c.id
				ORDER BY c.name";

$company_count = $DB->query($companySQL);

//do the catagory count
$catagorySQL = " SELECT" .
    "	c.name as cat_name," .
    "	c.id," .
    "	count(s.id) as items" .
    " FROM " . $table . " s" .
    " INNER JOIN stock_product_catagories c ON s.product_catagory=c.id" .
    " GROUP BY c.id" .
    " ORDER BY c.name";

$catagory_count = $DB->query($catagorySQL);

//do the types count
$typesSQL = "SELECT t.name as type_name, t.id, count(p.id) as items
				FROM $table p, product_types t
				WHERE p.product_type = t.id
				GROUP BY t.id
				ORDER BY t.name ";

$types_count = $DB->query($typesSQL);

//do the stock values, stock availability should be 1
$statement = "SELECT t.name as type_name, p.unit_price, p.qty, p.cost_currency
				FROM $table p, product_types t
				WHERE p.product_type = t.id
				ORDER BY t.name ";

$types_value = $DB->query($statement);

//do the location count
$locationSQL = "SELECT l.location, l.id, count(p.id) as items
				FROM $table p, product_storage_location l, product_types t
				WHERE p.storage = l.id AND p.product_type = t.id
				GROUP BY p.storage
				ORDER BY l.location";

$location_count = $DB->query($locationSQL);

//do a stock type count
$stocktypeSQL = "SELECT t.name as stock_name, t.id, count(p.id) as items
				FROM $table p, stock_samplestock t
				WHERE p.samplestock = t.id
				GROUP BY t.id
				ORDER BY t.name ";

$stocktype_count = $DB->query($stocktypeSQL);

/*do the manufacturers count
$statement = "SELECT m.manufacturer, m.id, count(p.id) as items
FROM $table p, manufacturers m, product_types t
WHERE p.manufacturer = m.id AND p.type = t.id AND t.parent = '$parent'
GROUP BY p.manufacturer
ORDER BY m.manufacturer";

$manufacturer_count = $DB->query($statement);
 */
$supplierSQLold = "SELECT s.id, s.supplier, COUNT(sp.product_id) AS items
		FROM stock p, product_types pt, suppliers s, supplier_product sp
		WHERE p.product_type = pt.id AND p.id = sp.product_id AND sp.supplier_id = s.id
		GROUP BY s.id
		ORDER BY s.supplier
		";

$supplierSQLold2 = "SELECT s.id, s.supplier, COUNT(s.id) AS items
		FROM $table p, suppliers s
		WHERE p.supplier = s.id
		GROUP BY s.id
		ORDER BY s.supplier
		";

$supplierSQL = "SELECT s.id, s.supplier, COUNT(s.id) AS items
		FROM stock p, suppliers s, products pr
		WHERE p.coe_id = pr.id AND pr.preferred_supplier = s.id
		GROUP BY s.id
		ORDER BY s.supplier
		";

$supplier_count = $DB->query($supplierSQL);

//count the total items for this section
$items = $DB->query_first("SELECT COUNT(*) as total FROM $table p, product_types t WHERE t.id = p.product_type");

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
	var oldPrice = parseFloat(oldPriceField.value);
	var newPrice = parseFloat(priceField.value);

	if (!isNaN(newPrice) && newPrice < oldPrice && newPrice > 0)
	{
		checkbox.checked = true;
	}
	else
	{
		checkbox.checked = false;
	}
}
//]]>
</script>
<form action="admin2.php<?php echo $gets ?>" method="post" enctype="multipart/form-data" name="admin_form">
	<br>
      <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td bgcolor="#EEEEEE" width="20%">
            <div align="center"><b><?php echo $section_details['description'] ?></b></div>
          </td>
          <td bgcolor="#EEEEEE" width="80%">
            <div align="right">
			<a href="javascript:MM_openBrWindow('admin_help.html','help','scrollbars=yes,width=500,height=400')">HELP</a> |
			<a href="<?php echo $script ?>&action=add">Add <?php echo $section_details['description'] ?></a> |
			<a href="<?php echo $script ?>&query=">List <?php echo $section_details['description'] ?>
            <!-- Modification: changing to basic database count until I can find where $items['total'] resides
            <!--(<?php echo $items['total'] ?>)</a> -->
<?php
$result = $DB->query("SELECT COUNT(id) as idcount FROM stock");

if ($row = $DB->fetch_array($result)) {
    $idcount = $row['idcount'];
    echo "($idcount)";
} else {
    echo "(0)";
}
?>
            <!--End modification -->
			</div>
          </td>
        </tr>
      </table>

<SCRIPT LANGUAGE="JavaScript" SRC="../expandToggle.js"></SCRIPT>
<!-- item company -->
<A HREF="#" onClick="expandIt('company'); return false"><IMG NAME="imEx" SRC="/webadmin/triDown.gif" WIDTH=16 HEIGHT=16 BORDER=0 ALT="Expand/Collapse All"> <b>List by Company</b></A>
<br>
<DIV ID="companyChild" CLASS="child">
<table width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
<?php
$count = 0;
while ($row = $DB->fetch_array($company_count)) {
    if ($count % 4 == 0) {
        echo '<tr>';
    }

    echo $row['id'] == $location ? '<td bgcolor="#FFCC00">' : '<td>';

    echo "<a href=\"$script&query=&company=" . $row['id'] . "\">" . $row['comp_name'] . ' (' . $row['items'] . ')</a></td>';

    if (($count + 1) % 4 == 0) {
        echo "</tr>\n";
    }

    $count++;
}

?>
</table>
</DIV>
<!--//item company -->
<br>
<!-- item catagories -->
<A HREF="#" onClick="expandIt('catagory'); return false"><IMG NAME="imEx" SRC="/webadmin/triDown.gif" WIDTH=16 HEIGHT=16 BORDER=0 ALT="Expand/Collapse All"> <b>List by Category</b></A>
<br>
<DIV ID="catagoryChild" CLASS="child">
<table width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
<?php
$count = 0;
while ($row = $DB->fetch_array($catagory_count)) {
    if ($count % 4 == 0) {
        echo '<tr>';
    }

    echo $row['id'] == $location ? '<td bgcolor="#FFCC00">' : '<td>';

    echo "<a href=\"$script&query=&catagory=" . $row['id'] . "\">" . $row['cat_name'] . ' (' . $row['items'] . ')</a></td>';

    if (($count + 1) % 4 == 0) {
        echo "</tr>\n";
    }

    $count++;
}

?>
</table>
</DIV>
<!--//item catagories -->
<br>

<?php if (false): //Disable List by Stock ?>
	<!-- stock type -->
	<A HREF="#" onClick="expandIt('stocktype'); return false"><IMG NAME="imEx" SRC="/webadmin/triDown.gif" WIDTH=16 HEIGHT=16 BORDER=0 ALT="Expand/Collapse All"> <b>List by Stock Type</b></A>
	<br>
	<DIV ID="stocktypeChild" CLASS="child">
	<table width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
<?php
    /*
    $count = 0;
    while ($row = $DB->fetch_array($stocktype_count))
    {
    if ($count % 4 == 0)
    echo '<tr>';

    echo $row['id'] == $location?'<td bgcolor="#FFCC00">':'<td>';

    echo "<a href=\"$script&query=&stype=".$row['id']."\">".$row['stock_name'].' ('.$row['items'].')</a></td>';

    if (($count + 1) % 4 == 0)
    echo "</tr>\n";

    $count++;
    }
     */
?>
	</table>
	</DIV>
	<!--//stock type -->
	<br>
	<?php endif; //End disable list by stock ?>

<!-- item types -->
<A HREF="#" onClick="expandIt('types'); return false"><IMG NAME="imEx" SRC="/webadmin/triDown.gif" WIDTH=16 HEIGHT=16 BORDER=0 ALT="Expand/Collapse All"> <b>List by Item Types</b></A>
<br>
<DIV ID="typesChild" CLASS="child">
<table width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
<?php
$count = 0;
while ($row = $DB->fetch_array($types_count)) {
    if ($count % 4 == 0) {
        echo '<tr>';
    }

    echo $row['id'] == $type ? '<td bgcolor="#FFCC00">' : '<td>';

    echo "<a href=\"$script&query=&type=" . $row['id'] . "\">" . $row['type_name'] . ' (' . $row['items'] . ')</a></td>';

    if (($count + 1) % 4 == 0) {
        echo "</tr>\n";
    }

    $count++;
}
?>
</table>
</DIV>
<!--//item types -->
<br>
<!-- item manufacturers
<A HREF="#" onClick="expandIt('manufacturer'); return false"><IMG NAME="imEx" SRC="/webadmin/triDown.gif" WIDTH=16 HEIGHT=16 BORDER=0 ALT="Expand/Collapse All"> <b>List by Manufacturers</b></A>
<br>
<DIV ID="manufacturerChild" CLASS="child">
<table width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
<?php /*
$count = 0;
while ($row = $DB->fetch_array($manufacturer_count))
{
if ($count % 4 == 0)
echo '<tr>';

echo $row['id'] == $manufacturer?'<td bgcolor="#FFCC00">':'<td>';

echo "<a href=\"$script&query=&manufacturer=".$row['id']."\">".$row['manufacturer'].' ('.$row['items'].')</a></td>';

if (($count + 1) % 4 == 0)
echo "</tr>\n";

$count++;
}*/
?>
</table>
</DIV>
item manufacturers -->
<!-- item suppliers -->
<A HREF="#" onClick="expandIt('supplier'); return false"><IMG NAME="imEx" SRC="/webadmin/triDown.gif" WIDTH=16 HEIGHT=16 BORDER=0 ALT="Expand/Collapse All"> <b>List by Suppliers</b></A>
<br>
<DIV ID="supplierChild" CLASS="child">
<table width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
<?php
$count = 0;
while ($row = $DB->fetch_array($supplier_count)) {
    if ($count % 4 == 0) {
        echo '<tr>';
    }

    echo $row['id'] == $manufacturer ? '<td bgcolor="#FFCC00">' : '<td>';

    echo "<a href=\"$script&query=&supplier=" . $row['id'] . "\">" . stripslashes($row['supplier']) . ' (' . $row['items'] . ')</a></td>';

    if (($count + 1) % 4 == 0) {
        echo "</tr>\n";
    }

    $count++;
}
?>
</table>
</DIV>
<!-- // item suppliers -->
<br>
<!-- item locations -->
<A HREF="#" onClick="expandIt('location'); return false"><IMG NAME="imEx" SRC="/webadmin/triDown.gif" WIDTH=16 HEIGHT=16 BORDER=0 ALT="Expand/Collapse All"> <b>List by Location</b></A>
<br>
<DIV ID="locationChild" CLASS="child">
<table width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
<?php
$count = 0;
while ($row = $DB->fetch_array($location_count)) {
    if ($count % 4 == 0) {
        echo '<tr>';
    }

    echo $row['id'] == $location ? '<td bgcolor="#FFCC00">' : '<td>';

    echo "<a href=\"$script&query=&location=" . $row['id'] . "\">" . $row['location'] . ' (' . $row['items'] . ')</a></td>';

    if (($count + 1) % 4 == 0) {
        echo "</tr>\n";
    }

    $count++;
}

?>
</table>
</DIV>
<!--//item locations -->
<br>
<!-- item values -->
<A HREF="#" onClick="expandIt('value'); return false"><IMG NAME="imEx" SRC="/webadmin/triDown.gif" WIDTH=16 HEIGHT=16 BORDER=0 ALT="Expand/Collapse All"> <b>Stock Values</b></A>
<br>
<DIV ID="valueChild" CLASS="child">
<table width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
<?php
$values = array();
while ($row = $DB->fetch_array($types_value)) {
    $type_name = $row['type_name'];
    if (!isset($values[$type_name])) {
        $values[$type_name] = 0;
    }

    $values[$type_name] += showPriceFrom($row['unit_price'], $row['cost_currency'], $_GLOBALS['site_options']['default_currency']) * $row['qty'];
}

$total = 0;
for ($i = 0; $i < count($values); $i++) {
    $key = key($values);

    echo "<tr" . ($count++ % 2 == 0 ? " bgcolor=\"#EEEEEE\"" : "") . ">\n";
    echo '<td nowrap width="25%"><b>';
    echo "$key</b></td><td nowrap width=\"75%\">$";
    printf("%01.2f", $values[$key]);
    echo "</td>";
    echo "</tr>\n";

    next($values);
    $total += $values[$key];
}

?>
<tr><td align="right"><b>Total :</b></td><td>$<?php printf("%01.2f", $total);
echo ' ' . $_GLOBALS['site_options']['default_currency']?></td></tr>
</table>
</DIV>
<!--//item values -->
<br>
<!-- view list -->
<A HREF="#" onClick="expandIt('viewlist'); return false"><IMG NAME="imEx" SRC="/webadmin/triDown.gif" WIDTH=16 HEIGHT=16 BORDER=0 ALT="Expand/Collapse All"> <b>View/Print List</b></A>
<br>
<DIV ID="viewlistChild" CLASS="child">
<table width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
<?php
//$types_count = $DB->query($typesSQL);
$company_count = $DB->query($companySQL);
$count         = 0;
echo "<tr><td><a href=\"list_stock_details.php?parent=$parent\" target=\"_blank\">View All</a></td></tr>\r\n";
echo "<tr><td><i>View by Company</i></td></tr>\n";
while ($row = $DB->fetch_array($company_count)) {
    if ($count % 4 == 0) {
        echo '<tr>';
    }

    echo $row['id'] == $type ? '<td bgcolor="#FFCC00">' : '<td>';

    echo "<a href=\"list_stock_details.php?parent=$parent&company=" . $row['id'] . "\" target=\"_blank\">" . $row['comp_name'] . ' (' . $row['items'] . ')</a></td>';

    if (($count + 1) % 4 == 0) {
        echo "</tr>\n";
    }

    $count++;
}
$catagory_count = $DB->query($catagorySQL);
$count          = 0;
echo "<tr><td><i>View by Category</i></td></tr>\n";
while ($row = $DB->fetch_array($catagory_count)) {
    if ($count % 4 == 0) {
        echo '<tr>';
    }

    echo $row['id'] == $type ? '<td bgcolor="#FFCC00">' : '<td>';

    echo "<a href=\"list_stock_details.php?parent=$parent&catagory=" . $row['id'] . "\" target=\"_blank\">" . $row['cat_name'] . ' (' . $row['items'] . ')</a></td>';

    if (($count + 1) % 4 == 0) {
        echo "</tr>\n";
    }

    $count++;
}

$stocktype_count = $DB->query($stocktypeSQL);
$count           = 0;
echo "<tr><td><i>View by Stock Type</i></td></tr>\n";
while ($row = $DB->fetch_array($stocktype_count)) {
    if ($count % 4 == 0) {
        echo '<tr>';
    }

    echo $row['id'] == $type ? '<td bgcolor="#FFCC00">' : '<td>';

    echo "<a href=\"list_stock_details.php?parent=$parent&samplestock=" . $row['id'] . "\" target=\"_blank\">" . $row['stock_name'] . ' (' . $row['items'] . ')</a></td>';

    if (($count + 1) % 4 == 0) {
        echo "</tr>\n";
    }

    $count++;
}

$types_count = $DB->query($typesSQL);
$count       = 0;
echo "<tr><td><i>View by Type</i></td></tr>\n";
while ($row = $DB->fetch_array($types_count)) {
    if ($count % 4 == 0) {
        echo '<tr>';
    }

    echo $row['id'] == $type ? '<td bgcolor="#FFCC00">' : '<td>';

    echo "<a href=\"list_stock_details.php?parent=$parent&type=" . $row['id'] . "\" target=\"_blank\">" . $row['type_name'] . ' (' . $row['items'] . ')</a></td>';

    if (($count + 1) % 4 == 0) {
        echo "</tr>\n";
    }

    $count++;
}
$supplier_count = $DB->query($supplierSQL);
$count          = 0;
echo "<tr><td><i>View by Supplier</i></td></tr>\n";
while ($row = $DB->fetch_array($supplier_count)) {
    if ($count % 4 == 0) {
        echo '<tr>';
    }

    echo $row['id'] == $type ? '<td bgcolor="#FFCC00">' : '<td>';

    echo "<a href=\"list_stock_details.php?parent=$parent&supplier=" . $row['id'] . "\" target=\"_blank\">" . stripslashes($row['supplier']) . ' (' . $row['items'] . ')</a></td>';

    if (($count + 1) % 4 == 0) {
        echo "</tr>\n";
    }

    $count++;
}
$location_count = $DB->query($locationSQL);
$count          = 0;
echo "<tr><td><i>View by Location</i></td></tr>\n";
while ($row = $DB->fetch_array($location_count)) {
    if ($count % 4 == 0) {
        echo '<tr>';
    }

    echo $row['id'] == $type ? '<td bgcolor="#FFCC00">' : '<td>';

    echo "<a href=\"list_stock_details.php?parent=$parent&location=" . $row['id'] . "\" target=\"_blank\">" . $row['location'] . ' (' . $row['items'] . ')</a></td>';

    if (($count + 1) % 4 == 0) {
        echo "</tr>\n";
    }

    $count++;
}
?>
</table>
</DIV>
<!-- //view list -->

<!-- search box -->
      <p align="right">
        <input type="text" name="query" size="20" value="<?php echo htmlspecialchars(stripslashes($_REQUEST["query"])) ?>">
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
    //Ordering by prices is presently incorrect, it orders by currency amount instead of USD value
    global $DB, $at, $fields, $order, $by, $script, $location, $type, $table, $supplier, $parent;
    global $company, $catagory, $stype;
    global $search_fields;

    $query = trim($query);
    //$query = ereg_replace('\(|\)|\+|\?|\[|\]|\$|\^', '',  $query);
    $query = str_replace(array("(", ")", "+", "?", "[", "]", "$", "^"), '', $query);

    //keywords
    $arrKeywords = explode(" ", $query);

    $extra = '';

    //search query by key words
    for ($index = 0; $index < count($arrKeywords); $index++) {
        $word = trim(strtolower($arrKeywords[$index]));

        if (!empty($word) && '|' != $word) {
            $extra .= " AND ";

            //do a regular expression search on each of the search fields, case insensitive
            for ($i = 0; $i < count($search_fields); $i++) {
                $extra .= 0 == $i ? '(' : ' OR ';
                //$extra .= " LOWER($search_fields[$i]) REGEXP \"$word\" ";
                $extra .= $search_fields[$i] . ' LIKE ' . formatSQLString('%' . $word . '%');
            }
            if (count($search_fields) > 0) {
                $extra .= ')';
            }

        }
    } //end search query keywords

    //if the location is specified
    if ('' != $location) {
        $extra .= " AND s.storage = '$location' ";
    }

    //if the type is specified
    if ('' != $type) {
        $extra .= " AND s.product_type = '$type' ";
    }

    //if the catagory is specified
    if ('' != $catagory) {
        $extra .= " AND s.product_catagory = '$catagory' ";
    }

    //if the company is specified
    if ('' != $company) {
        $extra .= " AND s.company_stock = '$company' ";
    }

/*
if($manufacturer != '')
$extra .= " AND p.manufacturer = '$manufacturer' ";
 */
    if ('' != $supplier) {
        $extra .= " AND s.supplier = '$supplier'";
        //$extra .= " AND sp.supplier_id = '$supplier' AND s.id = sp.product_id ";
    }

    if ('' != $stype) {
        $extra .= " AND s.samplestock = '$stype'";
    }

    //search statement
    $statement = sprintf(
        "SELECT `s`.*, `p`.`name` AS `type_name`, `s`.`unit_price` * `s`.`qty` AS `total_unit_price`, `spc`.`name` AS `category` FROM `stock` AS `s` LEFT JOIN `product_types` AS `p` ON `s`.`product_type` = `p`.`id` LEFT JOIN `stock_product_catagories` AS `spc` ON `spc`.`id` = `s`.`product_catagory` %s WHERE 1",
        '' //$extraWhere
    );

//    $statement = "    SELECT s.*, p.name as type_name, s.unit_price * s.qty as total_unit_price
    //                    FROM stock s, product_types p $extraWhere
    //                    WHERE s.product_type = p.id";//, product_types t $extraWhere";
    //WHERE p.product_type = t.id
    //AND t.parent = '$parent'

    /*SELECT p.*, t.name as type_name
    FROM products p, product_types t $extraWhere
    WHERE p.type = t.id
    AND t.parent = '$parent' ";
     */

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

    if ('country' == $by) {
        $by = 'country_location';
    }

    if ('category' == $by) {
        $order_by = " ORDER BY spc.name $order";
    } else {
        $order_by = " ORDER BY $by $order"; //order clause
    }
    $limit = " LIMIT $at, $inc "; //limit clause

    //get the results of the search
    //ho "$statement.$extra.$order_by.$limit<BR>\n";
    $result = $DB->query($statement . $extra . $order_by . $limit);
    //$result = $DB->query($statement);

    //link for the more links section
    $link = $script . '&query=' . urlencode(stripslashes($query)) . '&company=' . (isset($_GET['company']) ? $_GET['company'] : "") . '&max=' . $max . '&by=' . $by . '&order=' . $order . '&type=' . $type . '&location=' . $location . '&manufacturer=' . $manufacturer . '&catagory=' . $catagory;
    if ("" != $supplier) {
        $link .= '&supplier=' . $supplier;
    }

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
// get the next editable ID
//
// PARAMS: n/a
// RETURN: the next ID
//===========================================================================
function get_nextID()
{
    //Basic checking version for stock, gets the next ID greater than the input id, otherwise returns the lowest ID(loops)
    global $DB, $at, $fields, $order, $by, $script, $location, $type, $table, $manufacturer, $parent;
    global $search_fields, $id;

    $items = $DB->query("SELECT id FROM stock WHERE id > $id");

    if ($row = $DB->fetch_array($items)) {
        return $row['id'];
    } else {
        $items = $DB->query("SELECT id FROM stock ORDER BY id");
        if ($row = $DB->fetch_array($items)) {
            return $row['id'];
        } else {
            return 0;
        }
    }

    //Commented out, rewriting custom code for stock table

    /*$_REQUEST["query"] = addslashes(addslashes(trim($_REQUEST["query"])));

//keywords
$arrKeywords = explode(" ",$_REQUEST["query"]);

$extra = '';

//search query by key words
for ($index = 0;$index < count($arrKeywords); $index++)
{
$word = trim(strtolower($arrKeywords[$index]));

if (!empty($word) && $word != '|')
{
$extra .= " AND ";

//do a regular expression search on each of the search fields, case insensitive
for ($i = 0; $i < count($search_fields); $i++)
{
$extra .= $i == 0? '(':' OR ';
$extra .= " LOWER($search_fields[$i]) REGEXP \"$word\" ";
}
if (count($search_fields) > 0)
$extra .= ')';
}
}//end search query keywords

//if the location is specified
if ($location != '')
$extra .= " AND p.location = '$location' ";

//if the type is specified
if ($type != '')
$extra .= " AND p.type = '$type' ";

//when the manufacturer is specified
if($manufacturer != '')
$extra .= " AND p.manufacturer = '$manufacturer' ";

//search statement
$statement = "SELECT p.*, t.name as type_name FROM $table p, product_types t WHERE p.type = t.id AND p.id != '$id' AND t.parent = '$parent'";

//default ordering is ascending
if (!isset($order) || $order == '')
$order = 'asc';

//default ordering by product name
if (!isset($by) || $by == '')
$by = 'product_name';

//if the ordering is ascending then find the next row greater then the current row
if ($order == 'asc')
$clause = " AND p.$by > '".htmlspecialchars($current_item[$by])."' ";
else
$clause = " AND p.$by < '".htmlspecialchars($current_item[$by])."' ";

$order_by = " ORDER BY $by $order"; //order clause
$limit = " LIMIT 0, 1"; //limit clause

//do the query, return only the first row
$result = $DB->query_first($statement.$clause.$extra.$order_by.$limit);

//we get the next ID
return $result['id'];*/
}

//===========================================================================
// Draw the table header row, each fields can be sorted and ordered
//
// PARAMS: n/a
// RETURN: n/a
//===========================================================================
function draw_header_row()
{
    global $fields, $order, $by, $max, $at, $page, $script, $type, $location, $manufacturer, $allowed_sections;

    if (!isset($order) || '' == $order) {
        $order = 'asc';
    }

    reset($fields);

    //removing Update Price button
    //echo '<br><div align="right"><input type="submit" name="update_price" value="Update Price"><input type="submit" name="update_qty" value="Update Qty"></div>'."\n";
    if (has_permission('STOCK', $allowed_sections)) {
        echo '<br><div align="right"><input type="submit" name="update_qty" value="Update Qty"></div>' . "\n";
    }

    echo '<table width="95%" align="center">' . "\n";
    echo '<tr bgcolor="#000066">' . "\n";

    //for each of the displaying fields
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field      = key($fields);
        $sort_field = $field;
        if ("sample" == $field) {
            $sort_field = "sample_qty";
        }

        if ("stock" == $field) {
            $sort_field = "stock_qty";
        }

        if ("order" == $field) {
            $sort_field = "order_qty";
        }

        echo '<td align="center"' . ($by == $field ? ' bgcolor="#336699"' : '') . ('qty' == $field ? ' width="50px"' : '') . '><b>';
        //draw the fields, with links for sorting and ordering
        echo "<a href=\"$script&query=" . urlencode(stripslashes($_REQUEST["query"])) . "&max=$max&at=$at&page=$page";
        echo "&type=$type&manufacturer=$manufacturer&location=$location&by=$sort_field&order=" . ('desc' == $order ? 'asc' : 'desc') . '" class="td_options">';
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
//    echo '<div align="right"><input type="submit" name="update_qty" value="Update Qty"></div><br>'."\n";
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
    global $fields, $actions, $script, $table, $section_details, $DB, $allowed_sections;

    //the displaying fields
    reset($fields);

    //get the id first
    $id = $cols[key($fields)];

    $product_name = $cols['product_name'];

    $type      = encode_section_url($cols['type_name']);
    $type_name = $cols['type_name'];

    $company = $cols['country_location'];
    //$result = $DB->query("SELECT name FROM stock_company_stock WHERE id = $company");
    $result = $DB->query("SELECT name,id FROM product_country_location WHERE id = $company");

    if ($row = $DB->fetch_array($result)) {
        $cols['country_location'] = $row['name'];
        $cols['country']          = $row['name'];
    }
    echo "<!-- ID: $id -->\n";

    if (locked($table, $id)) {
        echo '<tr bgcolor="pink">';
    }

//    else if (my_lock($table, $id))
    //        echo '<tr bgcolor="'.($shaded?'#FFFFEE':'#FFFFCC').'">';
    else {
            echo $shaded ? '<tr>' : '<tr bgcolor="#EEEEEE">';
        }

        //for each of the fields
        for ($i = 0; $i < sizeof($fields); $i++) {
            //the curreny field
            $key = key($fields);

            //field alignment
            echo "\n<td align=\"" . $fields[$key] . "\">";

            //if it is the quantity field
            if ('qty' == $key) {
//            if(has_permission('STOCK', $allowed_sections))
                //                echo '<INPUT TYPE="text" NAME="qty_change['.$id.']" size="2" value="'.$cols[$key].'">';
                //            else
                echo $cols[$key];
            } else if ('sample' == $key) {
            if (has_permission('STOCK', $allowed_sections)) {
                echo '<INPUT TYPE="text" NAME="sample_qty_change[' . $id . ']" size="2" value="' . $cols['sample_qty'] . '">';
            } else {
                echo $cols['sample_qty'];
            }

            //echo $cols['sample_qty'];
        } else if ('stock' == $key) {
            if (has_permission('STOCK', $allowed_sections)) {
                echo '<INPUT TYPE="text" NAME="stock_qty_change[' . $id . ']" size="2" value="' . $cols['stock_qty'] . '">';
            } else {
                echo $cols['stock_qty'];
            }

            //echo $cols['stock_qty'];
        } else if ('order' == $key) {
            if (has_permission('STOCK', $allowed_sections)) {
                echo '<INPUT TYPE="text" NAME="order_qty_change[' . $id . ']" size="2" value="' . $cols['order_qty'] . '">';
            } else {
                echo $cols['order_qty'];
            }

            //echo $cols['order_qty'];
        } else if ('unit_price' == $key) {
            //Need to verify this is returning correct values
            //Complete this later
            echo "$" . showPriceFrom($cols['unit_price'], $cols['cost_currency']) . " USD";
            //showPriceFrom($row['unit_price'], $row['cost_currency'], $_GLOBALS['site_options']['default_currency']) * $row['qty'];
        } else if ('total_unit_price' == $key) {
            //Modifying to display unit price * quantiy
            echo "$" . showPriceFrom($cols['unit_price'] * $cols['qty'], $cols['cost_currency']) . " USD";
            //echo '<input type="hidden" name="old_price['.$id.']" value="'.$cols[$key].'" >';
            //echo '<input type="text" size="8" name="price_change['.$id.']" value="'.$cols[$key].'" ';
            //echo "onChange='checkPriceChanges(document.admin_form[\"price_changed[$id]\"],document.admin_form[\"old_price[$id]\"],document.admin_form[\"price_change[$id]\"])'>";
        } else if ('product_name' == $key) {
            //Modification: Changing to encode coeid instead of id in the URL
            $item_link = encode_product_url($cols[$key], $cols['id']);
            //$item_link = encode_product_url($cols[$key],$cols['coe_id']);
            echo "<a href=\"/products/" . $section_details['name'] . "/$type/$item_link.html?preview=true\" target=\"_blank\">" . stripslashes($cols[$key]) . "</a>";
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

//===========================================================================
// add a new product to the database
//
// PARAMS: n/a
// RETURN: n/a
//===========================================================================
//modification 17/05/04 Daniel Kehl
//Modifying this function to add stock items instead of products
//Need to examine the stripslashes for entry, is adding an extra slash
function do_add()
{
    global $table, $DB, $data1, $data2, $data3, $images;
    global $parent, $section_details, $img_location;
    global $related, $admin_notes;
    global $noteCount, $admin_note, $admin_name, $admin_date;
    global $category_colour, $category_qty;
    global $category_dimensions, $category_dimensions_qty;

    //Combine the qty's into one total
    $data3['qty'] = $data3['stock_qty'] + $data3['sample_qty'] + $data3['order_qty'];

    //Convert the date from d-m-Y format to Y-m-d format
    $data3[date_received]   = substr($data3[date_received], 6, 4) . "-" . substr($data3[date_received], 3, 2) . "-" . substr($data3[date_received], 0, 2);
    $data3[product_details] = trim(addslashes($data3[product_details]));
    //$data3[product_name] = trim(addslashes($data3[product_name]));

    //get all the data for the STOCK tables, do the query

    //$statement = "INSERT INTO $table (".get_fields($data1).", date_added, last_modified) VALUES (".get_values($data1).", NOW(), NOW())";
    $statement = "INSERT INTO stock (" . get_fields($data3) . ", date_added, last_modified) VALUES (" . get_values($data3) . ", NOW(), NOW())";

    //add the new datas to the STOCK table
    $result = $DB->query($statement);

    //get the newly inserted ID
    $id = $DB->insert_id($result);

    //added the admin notes
    $admin_notes = trim(addslashes($admin_notes));
    if ('' != $admin_notes) {
        //get the user name
        $user = get_user();

        //insert the admin note into the database
        $statement = "INSERT INTO stock_admin_notes (stock_id, date_added, user, note)
						VALUES ('$id', NOW(), '$user', '$admin_notes')";

        $DB->query($statement);
    }

    //add the colour quantities
    if (is_array($category_colour) && count($category_colour) > 0) {
        foreach ($category_colour as $key => $colour) {
            $statement = "INSERT INTO stock_colour_qty (stock_id, colour, quantity) VALUES ('$id','$colour','" . $category_qty[$key] . "')";
            $DB->query($statement);
        }
    }

    //add the dimension quantities
    if (is_array($category_dimensions) && count($category_dimensions_qty) > 0) {
        foreach ($category_dimensions as $key => $dimensions) {
            $statement = "INSERT INTO stock_dimensions_qty (stock_id, dimensions, qty) VALUES ('$id','$dimensions','" . $category_dimensions_qty[$key] . "')";
            $DB->query($statement);
        }
    }

    //Update the modification logs
    $user      = get_user();
    $statement = "UPDATE stock SET last_user = '$user', last_modification = 'Created' WHERE id = '$id'";
    $DB->query($statement);

    //add the old admin notes
    $count = $noteCount;
    //decrement the counter to get the correct index
    $count--;
    while ($count > 0) {
        $user = $admin_name[$count];
        $date = $admin_date[$count];
        $note = $admin_note[$count];
        $note = trim(addslashes($note));

        $statement = "INSERT INTO stock_admin_notes (stock_id, date_added, user, note)
						VALUES ('$id', '$date', '$user', '$note')";

        $DB->query($statement);
        $count--;
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

    //need to see if the unit price differs from that of the product in the product section
    //then update it (I've been told to do this)
    //Don't blame me should the building collapse, I was over here in this clearing...
    //Chopping trees or something
    $coe_id   = intval($_POST['data3']['coe_id']);
    $sql      = "SELECT p.weight, sp.supplier_id, p.cost_price, p.cost_currency FROM products p LEFT JOIN supplier_product sp ON sp.product_id=p.id WHERE p.id=" . $coe_id;
    $old_prod = $DB->query_first($sql);
    if ($old_prod) {
        $new_weight   = floatval($_POST['data3']['weight']);
        $new_supplier = intval($_POST['data3']['supplier']);
        $new_price    = floatval($_POST['data3']['unit_price']);
        $new_currency = $_POST['data3']['cost_currency'];
        if (0.0 != $new_weight || 0 != $new_supplier || 0.0 != $new_price) {
            if ($old_prod['weight'] != $new_weight || $old_prod['supplier_id'] != $new_supplier || $old_prod['cost_price'] != $new_price || $old_prod['cost_currency'] != $new_currency) {
                $sql = " UPDATE products" .
                    " SET" .
                    "	weight=" . $new_weight . "," .
                    "	cost_price=" . $new_price . "," .
                    "	cost_currency='" . $new_currency . "'" .
                    " WHERE id = '" . $coe_id . "'";
                $DB->query($sql);
                if ($new_supplier > 0) {
                    $DB->query("INSERT IGNORE INTO supplier_product(product_id,supplier_id) VALUES(" . $coe_id . "," . $new_supplier . ")");
                }

                //For some reason the query isn't logging
                $DB->query("INSERT INTO query_log(log_date,user_id,query) VALUES(NOW(),'" . $user . "',\"" . $sql . "\")");
            }
        }
    }

    //now for the related product sections/type
    add_update_related($related, $id);

    echo "New Stock item $id Added <br />";
}

//===========================================================================
// update product details
//
// PARAMS: n/a
// RETURN: n/a
//===========================================================================
function do_edit()
{
    global $id, $table, $DB, $data1, $data2, $data3, $images;
    global $parent, $section_details, $img_location;
    global $related, $update_prices, $prices, $admin_notes;
    global $category_colour, $category_qty;
    global $category_dimensions, $category_dimensions_qty;

    //Combine the qty's into one total
    $data3['qty'] = $data3['stock_qty'] + $data3['sample_qty'] + $data3['order_qty'];

    //Convert the date from d-m-Y format to Y-m-d format
    $data3[date_received] = substr($data3[date_received], 6, 4) . "-" . substr($data3[date_received], 3, 2) . "-" . substr($data3[date_received], 0, 2);

    $data3[product_details] = trim(addslashes($data3[product_details]));
    //update all the new products record
    $statement = "UPDATE stock SET " . get_set_values($data3) . " WHERE id = '$id'";

    $DB->query($statement);

    //added the admin notes
    $admin_notes = trim(addslashes($admin_notes));
    if ('' != $admin_notes) {
        //get the user name
        $user = get_user();

        //insert the admin note into the database
        $statement = "INSERT INTO stock_admin_notes (stock_id, date_added, user, note)
						VALUES ('$id', NOW(), '$user', '$admin_notes')";

        $DB->query($statement);
    }

    //add the colour quantities
    $DB->query("DELETE FROM stock_colour_qty WHERE stock_id='$id'");
    if (is_array($category_colour) && count($category_colour) > 0) {
        foreach ($category_colour as $key => $colour) {
            $statement = "INSERT INTO stock_colour_qty (stock_id, colour, quantity) VALUES ('$id','$colour','" . $category_qty[$key] . "')";
            $DB->query($statement);
        }
    }

    //add the dimension quantities
    $DB->query("DELETE FROM stock_dimensions_qty WHERE stock_id='$id'");
    if (is_array($category_dimensions) && count($category_dimensions_qty) > 0) {
        foreach ($category_dimensions as $key => $dimensions) {
            $statement = "INSERT INTO stock_dimensions_qty (stock_id, dimensions, qty) VALUES ('$id','$dimensions','" . $category_dimensions_qty[$key] . "')";
            $DB->query($statement);
        }
    }

    //Update the modification logs
    $user      = get_user();
    $statement = "UPDATE stock SET last_user = '$user', last_modification = 'Edited' WHERE id = '$id'";
    $DB->query($statement);

    //check for new image(s), update record if neccessary
    if (is_file($images['image'])) {
        // Remove old cached images
        // Generate cache tag
        $fname = sys_get_temp_dir() . "/image.pid-$id.ssid-0.wmp-?.thumb-?.jpg";
        // Unlink anything that matches
        foreach (glob($fname) as $fn) {
            unlink($fn);
        }

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

//    if ($old_supplier_id != $supplier_id)
    //    {
    //        $DB->query("DELETE FROM supplier_product WHERE product_id = '$id' AND supplier_id = '$old_supplier_id'");
    //
    //        if ($supplier_id > 0)
    //            $DB->query("INSERT IGNORE INTO supplier_product (supplier_id, product_id) VALUES ('$supplier_id', '$id')");
    //    }

    //need to see if the unit price differs from that of the product in the product section
    //then update it (I've been told to do this)
    //Don't blame me should the building collapse, I was over here in this clearing...
    //Chopping trees or something
    $coe_id   = intval($_POST['data3']['coe_id']);
    $sql      = "SELECT p.weight, sp.supplier_id, p.cost_price, p.cost_currency FROM products p LEFT JOIN supplier_product sp ON sp.product_id=p.id WHERE p.id=" . $coe_id;
    $old_prod = $DB->query_first($sql);
    if ($old_prod) {
        $new_weight   = floatval($_POST['data3']['weight']);
        $new_supplier = intval($_POST['data3']['supplier']);
        $new_price    = floatval($_POST['data3']['unit_price']);
        $new_currency = $_POST['data3']['cost_currency'];
        if ($old_prod['weight'] != $new_weight || $old_prod['supplier_id'] != $new_supplier || $old_prod['cost_price'] != $new_price || $old_prod['cost_currency'] != $new_currency) {
//            $sql=    " UPDATE products".
            //                    " SET".
            //                    "    weight=".$new_weight.",".
            //                    "    cost_price=".$new_price.",".
            //                    "    cost_currency='".$new_currency."'".
            //                    " WHERE id = '".$coe_id."'";
            //            $DB->query($sql);
            //            if($new_supplier>0)
            //                $DB->query("INSERT IGNORE INTO supplier_product(product_id,supplier_id) VALUES(".$coe_id.",".$new_supplier.")");
            //For some reason the query isn't logging
            //            $DB->query("INSERT INTO query_log(log_date,user_id,query) VALUES(NOW(),'".$user."',\"".$sql."\")");
        }
    }

    //now for the related product sections/type
    add_update_related($related, $id);

    //Testing whether unlock works out here
    unlock();

    echo $section_details['description'] . " $id Updated <br />";
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

    $statement = "DELETE FROM stock WHERE id = '$id'";
    $DB->query($statement);

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
    global $DB, $sample_qty_change, $stock_qty_change, $order_qty_change;

    if (is_array($sample_qty_change)) {
        foreach ($sample_qty_change as $k => $v) {
            update_part_qty($k, $v, 'sample_qty');
        }
    }
    if (is_array($stock_qty_change)) {
        foreach ($stock_qty_change as $k => $v) {
            update_part_qty($k, $v, 'stock_qty');
        }
    }
    if (is_array($order_qty_change)) {
        foreach ($order_qty_change as $k => $v) {
            update_part_qty($k, $v, 'order_qty');
        }
    }
}

//===========================================================================
// update the qty of a product
//
// PARAMS: the ID, the new qty
// RETURN: n/a
//===========================================================================
function update_part_qty($id, $qty_string, $type)
{
    global $DB, $table;

    $qty_clause = '';

    $editValue = true;

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
        //Check if the quantity is changed
        $qty_clause = "$type = $int_qty";
        $statement  = "SELECT $type FROM stock WHERE id = '$id'";
        $result     = $DB->query($statement);

        if ($row = $DB->fetch_array($result)) {
            if ($row[$type] == $int_qty) {
                $editValue = false;
            }
        }
    }

    //check the clause, must not be empty, and the current row is not locked
    if (true == $editValue) {
        if ('' != $qty_clause && !locked($table, $id)) {
            //Update the modification logs
            $user      = get_user();
            $statement = "UPDATE stock SET last_user = '$user', last_modification = 'Changed $type to $qty_clause', last_modified = NOW() WHERE id = '$id'";
            $DB->query($statement);
            //update the qty
            $DB->query("UPDATE stock SET $qty_clause WHERE id = '$id'");
        }
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
            //$sales_price_clause .= ", price_changed = NOW() ";
            //$DB->query("UPDATE stock SET old_price = sales_price WHERE id = '$id'");
        }

        //now update the price changes
        $DB->query("UPDATE stock SET $sales_price_clause WHERE id = '$id'");
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

    $contents = '';

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

//when a action occurs
if (isset($action)) {
    switch ($action) {
        case "add":
            //Modification 4/04/05 Daniel Kehl
            //Changing the included file from
            //include 'add_form.inc.php';
            //to
            include 'add_stock_form.inc.php';
            //End modification
            break;
        case "edit":
            include 'edit_stock_form.inc.php';
            break;
        case "delete":
            include 'delete_stock.php';
            break;
        case "do_add":

            do_add();

            //user wants to add more products
            if ('true' == $go_next) {
                include 'add_stock_form.inc.php';
            }

            break;
        case "do_edit":

            //row must not be locked to update
            if (!locked($table, $id)) {
                do_edit();
            }

            //user wants to edit the next product
            if ('true' == $go_next) {
                //get the next product ID
                $id = get_nextID();
                //lock the table straight away
                lock();
                include 'edit_stock_form.inc.php';
            }
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
    echo 'Updated Qty<br>';
    update_qty();
}

//do price updates
if (isset($update_price) && 'Update Price' == $update_price) {
    echo 'Updated Prices<br>';
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

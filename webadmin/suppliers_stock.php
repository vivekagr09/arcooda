<?php

include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

//the fields for display
$fields['id']           = 'center';
$fields['product_name'] = 'left';
$fields['category']     = 'center';
$fields['product_type'] = 'center';
$fields['active']       = 'center';
$fields['sales_price']  = 'right';
$fields['qty']          = 'right';

$table = 'products';

$id_field = 'id';

$search_fields[] = 'p.id';
$search_fields[] = 'p.product_name';
$search_fields[] = 'tp.description';
$search_fields[] = 't.name';

$supplier_id = intval($id);

$script = 'suppliers_stock.php?action=view&id=' . $supplier_id;

$statement = "SELECT product_id FROM supplier_product WHERE supplier_id = '$supplier_id'";

$results = $DB->query($statement);

$product_ids = '0';
while ($row = $DB->fetch_array($results)) {
    $product_ids .= ',' . $row['product_id'];
}

$statement = "SELECT COUNT(*) AS total FROM supplier_product sp WHERE sp.supplier_id = '$supplier_id'";
$items     = $DB->query_first($statement);

//do qty updates
if ('Update Qty' == $update_qty) {
    echo 'Updated Qty<br>';
    updateQty();
}

//do price updates
if ('Update Price' == $update_price) {
    echo 'Updated Prices<br>';
    updatePrice();
}

?>
<SCRIPT LANGUAGE="JavaScript">
<!--
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

-->
</script>
<form action="<?=$script?>" method="post" name="admin_form">
<br />
	  <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td bgcolor="#EEEEEE" width="20%">
            <div align="center"><b>Supplier Products</b></div>
          </td>
          <td bgcolor="#EEEEEE" width="80%">
            <div align="right">
			<a href="suppliers_admin.php?action=edit&id=<?=$supplier_id;?>">Edit Supplier Details</a> |
			<a href="suppliers_admin.php?query=">List Suppliers</a> |
			<a href="<?=$script;?>&query=">List All Products (<?=$items['total'];?>)</a>
			</div>
          </td>
        </tr>
      </table>

<!-- search box -->
      <p align="right">
        <input type="text" name="query" size="20" value="<?=htmlspecialchars(stripslashes($_REQUEST['query']));?>">
        <input type="submit" name="search" value="Search">
      </p>
<!-- //search box -->

<?php

supplierDetails($supplier_id);

if (array_key_exists('query', $_REQUEST)) {
    supplierStock($supplier_id);
}

//===========================================================================
//
//
// PARAMS: n/a
// RETURN: n/a
//===========================================================================
function supplierStock($supplier_id)
{

    global $DB, $at, $fields, $search_fields, $order, $by, $script, $table, $max;

    //keywords
    $arrKeywords = explode(" ", trim($_REQUEST['query']));

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
    $statement = "	SELECT p.*, t.name as product_type, tp.description as category, tp.name as section
					FROM products p, product_types t, product_types tp, supplier_product sp
					WHERE p.type = t.id
					AND t.parent = tp.id
					AND sp.product_id = p.id
					AND sp.supplier_id = '$supplier_id'

				";

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
    $link = "$script&query=" . urlencode(stripslashes($_REQUEST['query'])) . "&max=$max&by=$by&order=$order";

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
    global $fields, $order, $by, $max, $at, $page, $script, $id;

    if (!isset($order) || '' == $order) {
        $order = 'asc';
    }

    reset($fields);

    echo '<div align="right"><input type="submit" name="update_price" value="Update Price"><input type="submit" name="update_qty" value="Update Qty"></div>' . "\n";

    echo '<br /><table width="95%" align="center">' . "\n";
    echo '<tr bgcolor="#000066">' . "\n";

    //for each of the displaying fields
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);

        //draw the fields, with links for sorting and ordering
        echo '<td align="center" ' . ($by == $field ? 'bgcolor="#336699"' : '') . '><b>';
        echo "<a href=\"$script&query=" . urlencode(stripslashes($_REQUEST['query'])) . "&max=$max&at=$at&page=$page";
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
    echo '</table><br />';
    echo '<div align="right"><input type="submit" name="update_price" value="Update Price"><input type="submit" name="update_qty" value="Update Qty"></div><br>' . "\n";
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

    $type = str_replace(' ', '_', strtolower($cols['product_type']));

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
            echo '<INPUT TYPE="text" NAME="qty_change[' . $id . ']" size="2" value="' . $cols[$key] . '">' . "\n";
        } else if ('product_name' == $key) {
            $item_link = encode_product_url($cols[$key], $cols['id']);
            $category  = $cols['section'];
            echo "<a href=\"/products/$category/$type/$item_link.html\" target=\"_blank\">" . stripslashes($cols[$key]) . "</a>";
        } else if ('sales_price' == $key) {
            echo '<input type="hidden" name="old_price[' . $id . ']" value="' . $cols[$key] . '" >';
            echo '<input type="text" size="8" name="price_change[' . $id . ']" value="' . $cols[$key] . '" ';
            echo "onChange='checkPriceChanges(document.admin_form[\"price_changed[$id]\"],document.admin_form[\"old_price[$id]\"],document.admin_form[\"price_change[$id]\"])'>";
            echo '<input type="checkbox" name="price_changed[' . $id . ']" value="1">' . "\n";
        } else {
            echo stripslashes($cols[$key]);
        }

        echo '</td>';
        next($fields);
    }

    echo "</tr>\n";
    echo "<!--// ID: $id -->\n";
}

//===========================================================================
// update all the changes in quantity
//
// PARAMS: n/a
// RETURN: n/a
//===========================================================================
function updateQty()
{
    global $DB, $qty_change;

    //go through each of the qty
    for ($i = 0; $i < sizeof($qty_change); $i++) {
        $key = key($qty_change);

        //if the qty has changed, update the database
        if ('' != $qty_change[$key]) {
            updatePartQty($key, $qty_change[$key]);
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
function updatePartQty($id, $qty_string)
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
function updatePrice()
{
    global $DB, $price_change, $price_changed;

    //for each of the rows
    for ($i = 0; $i < sizeof($price_change); $i++) {
        $key = key($price_change);

        //if the price has changed
        if ('' != $price_change[$key]) {
            //update the price
            updatePartPrice($key, $price_change[$key], intval($price_changed[$key]));
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
function updatePartPrice($id, $sales_price_string, $price_changed)
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

function supplierDetails($id)
{
    global $DB;

    $supplier_details = $DB->query_first(sprintf(
        "SELECT * FROM `suppliers` WHERE `id` = %d LIMIT 1",
        $id
    ), MYSQLI_ASSOC);

    $country_list = getCountryList(false);

    $supplier_details['country'] = $country_list[$supplier_details['country']] ?? '';

?>
	<br />
	<div align="center">
	  <table width="80%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		  <td bgcolor="#CCCCCC"><table width="100%" border="0" cellspacing="1" cellpadding="2">
			  <tr  bgcolor="#FFFFFF"><td colspan="4" align="center"><h3><?=$supplier_details['supplier'];?></h3></td></tr>
			  <tr bgcolor="#FFFFFF">
				<td width="10%" valign="top"><div align="right"><b>Address :</b></div></td>
				<td colspan="3">
<?php
if (trim($supplier_details['address']) != '') {
        echo nl2br($supplier_details['address']) . '<br />';
    }

    if (trim($supplier_details['city']) != '') {
        echo $supplier_details['city'] . ', ';
    }

    if (trim($supplier_details['state']) != '') {
        echo $supplier_details['state'] . ', ';
    }

    echo $supplier_details['postal_code'] . '<br />';

    echo $supplier_details['country'] . '<br /><br />';

?>
				</td>
			  </tr>
			  <tr bgcolor="#FFFFFF">
				<td width="10%" valign="top"><div align="right"><b>Phone :</b></div></td>
				<td width="40%" valign="top"><?=nl2br($supplier_details['phone']);?></td>
				<td width="10%" valign="top"><div align="right"><b>Fax :</b></div></td>
				<td width="40%" valign="top"><?=nl2br($supplier_details['fax']);?></td>
			  </tr>
			  <tr bgcolor="#FFFFFF">
				<td valign="top"><div align="right"><b>Website :</b></div></td>
				<td valign="top">
<?php
$websites = explode("\n", $supplier_details['website']);

    for ($i = 0; $i < count($websites); $i++) {
        if (preg_match("@^http://@i", $websites[$i])) {
            echo '<a href="' . $websites[$i] . '" target="_blank">' . $websites[$i] . '</a><br />';
        } elseif (preg_match("@^https://@i", $websites[$i])) {
            echo '<a href="' . $websites[$i] . '" target="_blank">' . $websites[$i] . '</a><br />';
        } else {
            echo '<a href="https://' . $websites[$i] . '" target="_blank">' . $websites[$i] . '</a><br />';
        }

    }
?>
				</td>
				<td valign="top"><div align="right"><b>Email :</b></div></td>
				<td valign="top">
<?php
$emails = explode("\n", $supplier_details['email']);

    for ($i = 0; $i < count($emails); $i++) {
        echo '<a href="mailto:' . $emails[$i] . '">' . $emails[$i] . '</a><br />';
    }
?>
				</td>
			  </tr>
			  <?php if (trim($supplier_details['notes']) != '') {?>
			<tr  bgcolor="#FFFFFF"><td colspan="4"><i><?=nl2br($supplier_details['notes']);?></i></td></tr>
			<?php }?>
			</table></td>
		</tr>
	  </table>

	</div>
	<br />
<?php
}; //supplier details

?>
</form>
<?php
include_once __DIR__ . '/footer.inc.php';

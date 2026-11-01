<?php
/***********************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Bede Pavan <bpavan@highwaygroup.net>
 ** Filename......:
 ** Last changed..:    4/11/2003
 ** Notes.........:
 ***********************************************************************/

$section_code = "ORDERS";

include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';


error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
$user   = ucwords(strtolower($user));
$gets   = get_vars();
$action = $_REQUEST['action'];
$script = "orderAdmin.php";
$type   = trim($_GET['type']);

$fields['date_added'] = "center";
$fields['customer']   = "center";
$fields['product']    = "center";
$fields['qty']        = "center";
$fields['status']     = "center";
$fields['days']       = "center";

$search_fields[] = "oc.name";
$search_fields[] = "p.product_name";

$actions[] = '<a href=\"$script?action=edit&id=$id&type=' . $_GET[type] . '&max=$max&by=$by&order=$order&at=$at&page=$page\">View/Edit</a>';

// for locking purposes
$table      = "orders";
$primKeyArr = array("id" => $_REQUEST['id']);
switch ($action) {
    case "do_add":
        $GLOBALS['errorMsg'] = validateAddForm();
        if ($errorMsg) {
            $includeAddForm = true;
        } else {
            doAdd();
        }

        break;
    case "do_edit":
        if (!locked($table, $primKeyArr, true)) {
            $GLOBALS['errorMsg'] = validateEditForm();
            if ($errorMsg) {
                $includeEditForm = true;
            } else {
                doEdit();
                unlock($table, $primKeyArr);
            }
        }
        break;
    case "edit_cust":
        updateCustomer();
        break;
}

$result    = $DB->query_first("SELECT COUNT(*) FROM orders WHERE status='open'");
$numOpen   = $result[0];
$result    = $DB->query_first("SELECT COUNT(*) FROM orders WHERE status='closed'");
$numClosed = $result[0];

?>
<!-- jobs admin -->
<script language="JavaScript">
<!--
function clearSearch()
{
	document.admin_form.pref_text.value = "";
	document.admin_form.pref_id.value = "";
	document.admin_form.pref_text.disabled = false;
}
-->
</script>
<form action="<?=$script . $gets?>" method="post" name="search_form">
      <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td class="pageMenu" width="20%">
			<div align="center">
			  <?=$user . "'s " . ucwords($type) . " Jobs"?>
			</div>
		  </td>
          <td class="pageMenu" width="80%">
            <div align="right">
			<a href="<?=$script?>?action=add">Add New Order</a> |
			<a href="<?=$script?>?type=open">Open Orders(<?=$numOpen?>)</a> |
			<a href="<?=$script?>?type=closed">Closed Orders(<?=$numClosed?>)</a> |
			<a href="<?=$script?>?type=all">All Orders(<?=$numOpen + $numClosed?>)</a>
			</div>
          </td>
        </tr>
		<tr><td colspan="2">&nbsp;</td></tr>
      </table><br>
<!-- search box -->
  <p align="right">
    <input type="text" name="query" size="20" value="<?=htmlspecialchars(stripslashes($_REQUEST['query']))?>">
    <input type="submit" name="search" value="Search">
  </p>
<!-- //search box -->
</form>
<form action="<?=$script . $gets?>" method="post" name="admin_form">
<?php
if (trim($_REQUEST['query']) == '') {
    switch ($action) {
        case "add":
            include "orderAddForm.inc.php";
            break;
        case "edit":
            if (!locked($table, $primKeyArr, true)) {
                lock($table, $primKeyArr);
            }

            include "orderEditForm.inc.php";
            break;
        case "cust":
            include "editCustomerOrders.inc.php";
            break;
    }
    if ($includeAddForm) {
        include "orderAddForm.inc.php";
    } else if ($includeEditForm) {
        include "orderEditForm.inc.php";
    }

}

if (!isset($_REQUEST['action']) || (isset($_REQUEST['action']) && "" == $_REQUEST['action'])) {
    showOrders(urldecode($_REQUEST['query']));
}

function showOrders($query)
{
    global $DB, $at, $fields, $order, $by, $type, $search_fields;

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
                $extra .= " LOWER($search_fields[$i]) REGEXP \"$word\" ";
            }
            if (count($search_fields) > 0) {
                $extra .= ')';
            }

        }
    } //end search query keywords

    $whereClause = "";
    if ("open" == $type) {
        $whereClause = " AND o.status = 'open' ";
    } else if ("closed" == $type) {
        $whereClause = " AND o.status = 'closed' ";
    }

    $statement = "SELECT oc.*,o.*,oc.name AS customer, p.product_name AS product, TO_DAYS(NOW()) - TO_DAYS(o.date_added) AS daysOpen,
						TO_DAYS(o.date_closed) - TO_DAYS(o.date_added) AS daysClosed
					FROM orders o, order_customers oc, products p
					WHERE o.customer_id=oc.id AND o.product_id=p.id $whereClause";

    //if max is not yet set
    if (!isset($max)) {
        $result = $DB->query($statement . $extra);
        $max    = $DB->num_rows($result);
    }

    $inc = 25;
    $at  = intval($at);
    if (!isset($order) || "" == $order) {
        $order = 'DESC';
    }

    if (!isset($by) || "" == $by) {
        $by = 'date_added';
    }

    $order_by = " ORDER BY $by $order";
    $limit    = " LIMIT $at, $inc ";
    $result   = $DB->query($statement . $extra . $order_by . $limit);

    show_links(get_links($max, "$script?type=" . $_GET['type'] . "&query=" . urlencode($query) . "&max=$max&by=$by&order=$order", 25, 4));
    if (!$DB->num_rows($result) > 0) {
        echo "<div align=\"center\"><b>None</b></div>\r\n";
    } else {
        draw_header_row();
        $shaded = true;
        while ($row = $DB->fetch_array($result)) {
            show_result($row, $shaded = !$shaded);
        }
        draw_header_end();
    }
    show_links(get_links($max, "$script?type=" . $_GET['type'] . "&query=" . urlencode($query) . "&max=$max&by=$by&order=$order", 25, 4));
}

function draw_header_row()
{
    global $fields, $order, $by, $max, $at, $page, $script;

    reset($fields);
    echo "<br>";
    echo '<table align="center" cellspacing="1" cellpadding="5">';
    echo '<tr bgcolor="#000066">';
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field        = key($fields);
        $fieldDisplay = $field;
//        if($field == "creator")
        //            $fieldDisplay = "fromuser";
        echo '<td align="center" class="tableHeader">';
        if (!("days_open" == $field || "started_by" == $field)) {
            echo "<a href=\"$script?type=" . $_GET['type'] . "&query=" . urlencode($_REQUEST['query']) . "&max=$max&at=$at&page=$page";
            echo "&by=$field&order=" . ("desc" == $order ? "asc" : "desc") . '" class="tableHeader">' . $fieldDisplay . '</a>';
        } else {
            echo $fieldDisplay;
        }

        if ($by == $field) {
            echo '  <img src="/webadmin/images/' . strtolower($order) . '.gif" width="7" height="7" border="0">  ';
        }

        echo "</td>\r\n";
        next($fields);
    }
    echo "<td class=\"tableHeader\">&nbsp;</td>";
    echo "</tr>";
}

function draw_header_end()
{
    echo '</table>';
    echo "<br>";
}

function show_result($cols, $shaded)
{
    global $fields, $actions, $script, $table, $DB, $user;

    reset($fields);
    $id         = $cols['id'];
    $primKeyArr = array('id' => $id);
    if (locked($table, $primKeyArr)) {
        $class = "locked";
    } else if (my_lock($table, $primKeyArr)) {
        $class = "myLocked";
    } else {
        $class = $shaded ? "tableRowAlt" : "tableRow";
    }

    echo "<tr class=\"$class\">\r\n";

    for ($i = 0; $i < sizeof($fields); $i++) {
        echo "<td align=\"" . $fields[key($fields)] . "\" $color>";
        if (key($fields) == 'date_added') {
            echo formatDate(strtotime($cols['date_added']));
        } else if (key($fields) == 'customer') {
            echo "<a href=\"orderAdmin.php?action=cust&id=" . $cols['customer_id'] . "\">" . $cols['customer'] . "</a>\r\n";
        } else if (key($fields) == 'days') {
            if ("open" == $cols['status']) {
                echo $cols['daysOpen'];
            } else if ("closed" == $cols['status']) {
                echo $cols['daysClosed'];
            }

        } else {
            echo $cols[key($fields)];
        }

        echo "</td>\r\n";
        $color = "";
        next($fields);
    }
    echo '<td nowrap>';
    for ($i = 0; $i < sizeof($actions); $i++) {
        if (0 != $i) {
            echo " | ";
        }

        $str = $actions[$i];
        eval("\$str = \"$str\";");
        echo "$str";
    }
    echo '</td>';
    echo "</tr>";
}

function doAdd()
{
    global $DB;

    $customer = add_slashes_array($_POST['customer']);
    $orders   = $_POST['order'];
    $others   = add_slashes_array($_POST['other']);

    if (intval($customer['id']) <= 0) {
        $sql = "INSERT INTO order_customers (name,email,phone,country) VALUES('" . $customer['name'] .
            "','" . $customer['email'] . "','" . $customer['phone'] . "','" . $customer['country'] . "')";
        $result         = $DB->query($sql);
        $customer['id'] = $DB->insert_id($result);
        echo "<div><font color=\"#FF000\"><b>New customer created.</b></font></div>\r\n";
    }

    foreach ($orders as $item) {
        if (intval($item['id']) <= 0) {
            continue;
        }

        if ("closed" == $item['status']) {
            $dateClosed = "NOW()";
        } else {
            $dateClosed = "NULL";
        }

        $sql = "INSERT INTO orders (customer_id,product_id,qty,sales_price,buy_price,buy_currency,stock_country,notes,status,date_added,date_closed)
				VALUES ('" . $customer['id'] . "','" . $item['id'] . "','" . $item['qty'] . "','" . $item['sales_price'] . "','" . $item['buy_price'] . "','" . $others['currency'] .
            "','" . $others['country'] . "','" . $others['notes'] . "','" . $item['status'] . "',NOW(),$dateClosed)";
        $DB->query($sql);
    }

    echo "<div><font color=\"#FF000\"><b>New order created.</b></font></div>\r\n";
}

function doEdit()
{
    global $DB;

    $order = add_slashes_array($_POST['order']);
    if ($order['pid'] != $order['old_id']) {
        $prices                  = product_prices($order['pid']);
        $first                   = reset($prices);
        $order['sales_price']    = $first['sales_price'];
        $order['sales_currency'] = $first['sales_currency'];
    }

    $dataCompleted = "";
    if ("closed" == $order['status']) {
        $dateCompleted = ", date_closed=NOW()";
    }

    $sql = "UPDATE orders SET customer_id='" . $order['customer_id'] . "', product_id='" . $order['pid'] . "', qty='" . $order['qty'] .
        "', sales_price='" . $order['sales_price'] . "', sales_currency='" . $order['sales_currency'] . "', buy_price='" .
        $order['buy_price'] . "', buy_currency='" . $order['buy_currency'] . "', stock_country='" . $order['stock_country'] .
        "', status='" . $order['status'] . "', notes='" . $order['notes'] . "' $dateCompleted WHERE id='" . $order['id'] . "'";
    $DB->query($sql);

    echo "<div><font color=\"#FF0000\"><b>Order Updated.</b></font></div>\r\n";
}

function updateCustomer()
{
    global $DB;

    $customer = add_slashes_array($_POST['customer']);
    $sql      = "UPDATE order_customers SET " . get_set_values($customer) . " WHERE id='" . $_POST['custID'] . "'";
    $DB->query($sql);

    echo "<div><font color=\"#FF0000\"><b>Customer Updated.</b></font></div>\r\n";
}

function validateAddForm()
{
    $errorMsg = "";
    $customer = $_POST['customer'];
    $orders   = $_POST['order'];
    $others   = $_POST['other'];
    if (intval($customer['id']) == 0 && trim($customer['name']) == "") {
        $errorMsg .= "<li>Please select a customer or enter a new one.</li>\r\n";
    }

    foreach ($orders as $item) {
        if (intval($item['id']) != 0 && (intval($item['qty']) <= 0 || intval($item['buy_price']) <= 0)) {
            $errorMsg .= "<li>Please ensure all items have a valid qty and buy price</li>\r\n";
        }
    }

    if ("" == $others['currency']) {
        $errorMsg .= "<li>Please select the buy price currency.</li>\r\n";
    }

    return $errorMsg;
}

function validateEditForm()
{
    $errorMsg = "";
    $orders   = $_POST['order'];

    if (intval($orders['id']) <= 0) {
        $errorMsg .= "<li>Please enter a valid product id.</li>\r\n";
    }

    if (intval($orders['qty']) <= 0) {
        $errorMsg .= "<li>Please enter a valid qty.</li>\r\n";
    }

    if ("" == $orders['buy_price'] || "" == $orders['buy_currency']) {
        $errorMsg .= "<li>Please enter a valid buy price and currency.</li>\r\n";
    }

    if ("" == $orders['sales_price'] || "" == $orders['sales_currency']) {
        $errorMsg .= "<li>Please enter a valid selling price and currency.</li>\r\n";
    }

    if ("" == $orders['status']) {
        $errorMsg .= "<li>Please select the status.</li>\r\n";
    }

    return $errorMsg;
}

function showOptions($data, $selected = "")
{
    $retString = "";
    if (!is_array($data) || count($data) == 0) {
        echo "showOptions(): Invalid array as parameter<br>\r\n";
        return "";
    }
    foreach ($data as $value => $name) {
        $highlight = "";
        if (is_array($selected)) {
            foreach ($selected as $selVal) {
                if (strcasecmp($selVal, $value) == 0) {
                    $highlight = "selected";
                    break;
                }
            }
        } else if ("" != $selected) {
            if (strcasecmp($selected, $value) == 0) {
                $highlight = "selected";
            }

        }
        $retString .= "<option value=\"$value\" $highlight>$name</option>\r\n";
    }
    return $retString;
}

?>
</form>
<!--//jobs admin -->
<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/webadmin/footer.inc.php";
?>

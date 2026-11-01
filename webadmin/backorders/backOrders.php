<?php
$section_code = "BACKORDER";
include_once __DIR__ . '/common.inc.php';

if (true != $print) {
    include_once __DIR__ . '/header.inc.php';
}

$user             = get_user();
$allowed_sections = get_allowed_sections($user);
if (has_permission('BACKORDER', $allowed_sections)) {
?>
<style type="text/css">
/*<![CDATA[*/
p.breakhere {page-break-after: always}
.titles	{font-weight:bold;font-size:10pt;color:#fff;background-color:#555;text-align:center}
.data	{font-size:10pt;text-align:center;}
/*]]>*/
</style>
<script type="text/javascript">
//<![CDATA[
function printpage() {
	if (window.print) {
		window.print();
	} else {
		alert('To print this page, select Print Frame from the file menu.');
	}
}
function doPrint() {
	if (confirm("Click OK to print this document")) {
		printpage();
	}
}
window.name = "mainWindow";
//]]>
</script>
<style type="text/css">
/*<![CDATA[*/
body,p,td {font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px}
.td_options {color:#fff;font-size:12px;text-decoration:none}
.topLinks {color:blue;font-size:12px}
a:hover {color:#f00}
.top {border-top:1px solid #ccc}
.left {border-left:1px solid #ccc}
.right {border-right:1px solid #ccc}
.centre {border-top:1px solid #ccc;border-bottom:1px solid #ccc}
.bottom {border-bottom:1px solid #ccc}
.bottomLeft {border-bottom:1px solid #ccc;border-left:1px solid #ccc}
.bottomRight {border-right:1px solid #ccc;border-bottom:1px solid #ccc}
.bottomLeftRight {border-right:1px solid #ccc;border-bottom:1px solid #ccc;border-left:1px solid #ccc}
.box {border:1px solid #ccc}
.centerDash {border-top:1px dotted #039;border-bottom:1px dotted #039}
.TableRow {background-color:#fff}
.TableRowAlt {background-color:#eee}
.TableRowOver {background-color:#D6DFF7}
/*]]>*/
</style>
<script type="text/javascript">
//<![CDATA[
function pviiClassNew(obj, new_style) {	obj.className = new_style; }
//]]>
</script>
<?php
$script = "backOrders.php";
    $gets   = get_vars();

    $fields['date_added']        = 'center';
    $fields['customer']          = 'left';
    $fields['product_name']      = 'left';
    $fields['buy_price']         = 'left';
    $fields['online_sell_price'] = 'left';
    $fields['qty']               = 'center';
    $fields['price']             = 'right';
    $fields['currency']          = 'center';
    $fields['handler']           = 'center';
    $fields['status']            = 'center';
    $fields['days']              = 'center';

    $search_fields[] = 'id';
    $search_fields[] = 'product_id';
    $search_fields[] = 'customer';
    if (true != $print) {
        $actions[] = '<a href=\"$script?action=edit&id=$id\">Edit</a>';
        $actions[] = '<a href=\"$script?action=delete&id=$id\" onclick=\"javascript: return confirm(\'Are you sure you wish to delete this item?\'); \">Delete</a>';
    }
    $type = $_GET['view'];
    if ("open" == $type) {
        $title       = "Open Back Orders";
        $whereClause = "WHERE bo.status='open' ";
    } else if ("closed" == $type) {
        $title       = "Closed Back Orders";
        $whereClause = "WHERE bo.status='closed' ";
    } else {
        $title = "All Back Orders";
        //$whereClause = "";
    }

    $items     = $DB->query_first(sprintf(
        "SELECT COUNT(*) AS `total` FROM `highwaygroup`.`back_orders` WHERE `status` = 'open' LIMIT 1"
    ), MYSQLI_ASSOC);
    $numOpen   = $items['total'];

    $items     = $DB->query_first(sprintf(
        "SELECT COUNT(*) AS `total` FROM `highwaygroup`.`back_orders` WHERE `status` = 'closed' LIMIT 1"
    ), MYSQLI_ASSOC);
    $numClosed = $items['total'];
?>
<body>

<?php
if ($print) {
?>
<table width="5%" align="center" cellpadding="3" cellspacing="2">
	<tr align=center ><td nowrap colspan=2 align=center><h1>Highway Group Back Orders</h1></td></tr>
	<tr nowrap>
		<td nowrap align=right>Website :</td>
		<td nowrap align=left><a href="http://www.josifovski.com/webadmin/backorders/backOrders.php?view=<?=$view?>&action=<?=$action?>&id=<?=$id?>">http://www.josifovski.com/webadmin/backorders/backOrders.php?view=<?=$view?>&action=<?=$action?>&id=<?=$id?></a></td>
	</tr>
	<tr align=center>
		<td nowrap align=right>Printed By :</td>
<?php
$username = get_user();
        $name = ucwords($username);
?>
		<td nowrap align=left><?=$name;?> </td>
	</tr>
	<tr align=center>
		<td nowrap align=right>Print Date :</td>
		<td nowrap align=left><?=date("l d of F Y");?> </td>
	</tr>
</table>
<?php
}
?>
<!-- back orders -->

			<br>
			<?php if (true != $print) {?>
			  <table align='center' width="95%" border="0" cellspacing="1" cellpadding="3">
				<tr>
				  <td class="pageMenu" width="20%">
					<div align="center"><b><?=$title?></b></div>
				  </td>
				  <td class="pageMenu" width="80%">
					<div align="right">
					<a href="<?=$script?>?action=add">Add Back Order</a> |
					<a href="<?=$script?>?view=open">List Open (<?=$numOpen?>)</a> |
					<a href="<?=$script?>?view=closed">List Closed (<?=$numClosed?>)</a> |
					<a href="<?=$script?>">List All (<?=$numOpen + $numClosed?>)</a>
					</div>
				  </td>
				</tr>
			  </table>
			  <form accept-charset="utf-8" autocomplete="off" method="post" action="backOrders.php" name='search'>
				<table align="center">
				<tr>
					<td><b>Customer/Product :</b><input name="query" type="text"></input><input type="submit" value="Search"></input></td>
				</tr>
				</table>
			</form>
			<form accept-charset="utf-8" autocomplete="off" method="post" action="<?=$script . $gets?>" name='main'>
<?php
}
    function search($query)
    {
        global $DB, $at, $fields, $order, $by, $view;
        global $script, $search_fields, $whereClause, $print;

        if (isset($query) && '' != $query) {
            $query = htmlspecialchars(addslashes(trim($query)));
            if (isset($whereClause)) {
                $whereClause .= " AND customer LIKE '%" . $query . "%' OR product_name LIKE '%" . $query . "%' OR email LIKE '%" . $query . "%'";
            } else {
                $whereClause = "WHERE customer LIKE '%" . $query . "%' OR product_name LIKE '%" . $query . "%' OR email LIKE '%" . $query . "%'";
            }
        }

        $statement = sprintf(
            "SELECT `bo`.*, TO_DAYS(NOW()) - TO_DAYS(`bo`.`date_added`) AS `days` FROM `highwaygroup`.`back_orders` AS `bo` %s",
            $whereClause
        );

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

        if (!isset($by)) {
            $by = 'date_added';
        }

        $order_by = " ORDER BY $by $order";
        if (true != $print) {
            $limit = " LIMIT $at, $inc ";
        }
        $result = $DB->query($statement . $extra . $order_by . $limit);

        if ($DB->num_rows($result) > 0) {
            if (true != $print) {
                show_links(get_links($max, "$script?query=" . urlencode($query) . "&max=$max&by=$by&order=$order&view=$view", 25, 4));
            }
            //echo 'here';
            if ($print) {
                echo '<p class="breakhere">';
            }
            draw_header_row();
            $shaded = true;
            $count  = 1;
            $mod    = 8;
            while ($row = $DB->fetch_array($result)) {
                show_result($row, $shaded = !$shaded);
                if ($print && ($count % $mod) == 0) {
                    echo '</table></p><p class="breakhere">';
                    draw_header_row();
                    if ($count > 1 && 10 != $mod) {
                        $mod   = 10;
                        $count = 1;}
                }
                $count++;
            }
            draw_header_end();
            if (true != $print) {
                show_links(get_links($max, "$script?query=" . urlencode($query) . "&max=$max&by=$by&order=$order&view=$view", 25, 4));
            }
        } else {
            echo '<div align="center"><font color="#FF0000"><b>No matching back orders, try another search term .</b></div>';
        }
    }

    function draw_header_row()
    {
        global $fields, $order, $by, $max, $at, $page, $script, $print;

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
                $byField = "part_name";
            }

            if ("price" == $field) {
                $byField = "offer_price";
            }

            if (true == $print) {
                echo '<td>';
            } else {
                echo '<td align="center" ' . ($by == $field ? 'bgcolor="#FF9900"' : '') . '>';
            }
            echo "<a href=\"$script?query=" . urlencode($_REQUEST['query']) . "&max=$max&at=$at&page=$page&by=$byField&order=" . ("desc" == $order ? "asc" : "desc") . '" class="td_options">';
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
            echo "<tr class=\"TableRow\" onMouseOver=\"pviiClassNew(this,'TableRowOver')\" onMouseOut=\"pviiClassNew(this,'TableRow')\">\n";
            //echo "<tr>\n";
        } else {
            echo "<tr class=\"TableRowAlt\" onMouseOver=\"pviiClassNew(this,'TableRowOver')\" onMouseOut=\"pviiClassNew(this,'TableRowAlt')\">\n";
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
                echo formatDate(strtotime($cols[key($fields)])) . '</td>';
            } else if (key($fields) == "price") {
                echo $cols['offer_price'] . '</td>';
            } else if (key($fields) == "handler") {
                $result = $DB->query_first("SELECT `user` FROM `highwaygroup`.`users` WHERE `id` = '" . $cols[key($fields)] . "'");
                echo niceusername($result['user']) . '</td>';
            } else {
                echo stripslashes($cols[key($fields)]) . '</td>';
            }
            next($fields);
        }
        if (false == $print) {
            echo '<td nowrap>';
            for ($i = 0; $i < sizeof($actions); $i++) {
                if (0 != $i) {
                    echo " | ";
                }

                //$str = addslashes($actions[$i]);
                $str = $actions[$i];
                eval("\$str = \"$str\";");
                echo "$str";

            }
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

    function addBackOrder()
    {
        global $DB, $data, $comment, $product_name, $buy_price, $online_sell_price;

        //product name does not belong to array for compatibility issues
        $data['product_name']      = $product_name;
        $data['buy_price']         = $buy_price;
        $data['online_sell_price'] = $online_sell_price;

        $errorMsg = "";
        if (trim($data['customer']) == "") {
            $errorMsg .= "<li>Please enter the customer name.</li>\r\n";
        }

        if (trim($data['product_id']) == "" && trim($data['product_name']) == "") {
            $errorMsg .= "<li>Please enter the product ID OR the product name.</li>\r\n";
        }

        //if(trim($data['product_name']) == "")
        //    $errorMsg .= "<li>Please enter the product name.</li>\r\n";
        //if(trim($data['qty']) == "")
        //    $errorMsg .= "<li>Please enter the qty.</li>\r\n";
        //if(trim($data['offer_price']) == "")
        //    $errorMsg .= "<li>Please enter the offered price.</li>\r\n";
        //if(trim($data['currency']) == "")
        //    $errorMsg .= "<li>Please select the currency.</li>\r\n";

        if ("" != $errorMsg) {
            include_once "backOrdersAdd.inc.php";
        } else {
            $DB->query(sprintf(
                "INSERT INTO `highwaygroup`.`back_orders` (%s, `status`, `date_added`) VALUES (%s, 'open', NOW())",
                get_fields($data),
                get_values($data)
            ));
            $id = $DB->insert_id();
            echo "<br><font color=\"#FF0000\"><b>New back order added.</b></font><br>";

            if ('' != $comment) {
                $DB->query(sprintf(
                    "INSERT INTO `highwaygroup`.`backorder_comment` (`backorder`, `date`, `person`, `comment`) VALUES (%d, NOW(), %s, %s)",
                    $data['id'],
                    $DB->escape(get_user()),
                    $DB->escape($comment)
                ));
            }
        }
    }

    function editBackOrder()
    {
        global $DB, $data, $comment, $product_name, $buy_price, $online_sell_price;

        //product name does not belong to array for compatibility issues
        $data['product_name']      = $product_name;
        $data['buy_price']         = $buy_price;
        $data['online_sell_price'] = $online_sell_price;

        $errorMsg = "";
        if (trim($data['customer']) == "") {
            $errorMsg .= "<li>Please enter the customer name.</li>\r\n";
        }

        //if(trim($data['product_id']) == "" && trim($data['product_name']) == "")
        //    $errorMsg .= "<li>Please enter the product ID OR the product name.</li>\r\n";
        //if(trim($data['product_name']) == "")
        //    $errorMsg .= "<li>Please enter the product name.</li>\r\n";
        if (trim($data['qty']) == "") {
            $errorMsg .= "<li>Please enter the qty.</li>\r\n";
        }

        if (trim($data['offer_price']) == "") {
            $errorMsg .= "<li>Please enter the offered price.</li>\r\n";
        }

        if (trim($data['currency']) == "") {
            $errorMsg .= "<li>Please select the currency.</li>\r\n";
        }

        if ("" != $errorMsg) {
            include_once "backOrdersEdit.inc.php";
        } else {
            $DB->query(sprintf(
                "UPDATE `highwaygroup`.`back_orders` SET %s WHERE `id` = %d LIMIT 1",
                get_set_values($data),
                $data['id']
            ));
            echo "<br><font color=\"#FF0000\"><b>Back order updated.</b></font><br>";

            if ('' != $comment) {
                $DB->query(sprintf(
                    "INSERT INTO `highwaygroup`.`backorder_comment` (`backorder`, `date`, `person`, `comment`) VALUES (%d, NOW(), %s, %s)",
                    $data['id'],
                    $DB->escape(get_user()),
                    $DB->escape($comment)
                ));
            }
        }
    }

    function deleteBackOrder()
    {
        global $DB, $id;

        $DB->query(sprintf(
            "DELETE FROM `highwaygroup`.`back_orders` WHERE `id` = %d LIMIT 1",
            $id
        ));
        echo "<br><font color=\"#FF0000\"><b>Back order $id deleted.</b></font><br>";
    }

    switch ($action) {
        case 'add':
            include "backOrdersAdd.inc.php";
            break;
        case 'edit':
            include "backOrdersEdit.inc.php";
            break;
        case 'delete':
            deleteBackOrder();
            break;
        case 'do_add':
            addBackOrder();
            break;
        case 'do_edit':
            editBackOrder();
            break;
    }

    if (!isset($action) || "" == $action) {
        search(addslashes(htmlspecialchars($_REQUEST['query'])));
    }

    if (true != $print) {
        //echo $action;
        if ('edit' == $action) {
            echo '<center><a href="backOrders.php?action=edit&id=' . $id . '&print=true"  target="_blank">Click here to print summary list</a></center>';
        } else {
            echo '<center><a href="backOrders.php?print=true&view=' . $view . '" target="_blank">Click here to print summary list</a></center>';
        }
    } else {
?>
				<script language="javascript">
					doPrint();
				</script>
<?php
}
    include '../footer.inc.php';
?>
	</body>
	<?php }?>

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

$script                      = 'edit_Service_admin.php';
$fields['prop_id']           = 'left';
$fields['name']              = 'left';
$fields['accountno']         = 'left';
$fields['base_currency']     = 'left';
$fields['customer_discount'] = 'left';
$fields['store_discount']    = 'left';
$fields['handling']          = 'left';
$fields['hndasperc']         = 'left';
$fields['source']            = 'left';

$actions[] = '<a href="$script?action=edit&prop_id=$prop_id">Edit</a>';

$items = $DB->query_first("SELECT COUNT(*) AS `total` FROM `shipping_services`");

$gets = get_vars();

?>
<!-- parts screenshot admin -->

<form accept-charset="utf-8" autocomplete="off" method="post" action="<?=$script . $gets?>">

	<br>
      <table width="95%" align="center" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td class="pageMenu">
            <div align="center"><a href="AdminMenu.php"><?=$courier?> Edit Services</a>
			</div>
          </td>
          <td class="pageMenu">
            <div align="right">

			<a href="<?=$script?>?query=">List All Services (<?=$items['total']?>)</a>
			</div>
          </td>
        </tr>
		</table>

<?php

function search($query)
{
    global $DB, $at, $fields, $order, $by;
    global $script;

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
            if (preg_match("/^\\d+$/", $word)) //if its a search by prop_id
            {
                $extra .= "(s.ssid = '$word' OR s.id = '$word')";
            } elseif (preg_match("/^[\\*\\+\\\\]/", $word)) {
                $extra .= "(LOWER($key) LIKE '%$word%')";
            } else {
                $extra .= "(LOWER($key) REGEXP \"$word\")";
            }
            //else just a normail regular exp. search
        }
    } //end search query keywords

    //search statement
    $statement = "SELECT * FROM shipping_services";
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
        $by = 'name';
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
    show_links(get_links($max, "$script?query=" . urlencode($query) . "&max=$max&by=$by&order=$order", 25, 4));
}

function draw_header_row()
{
    global $fields, $order, $by, $query, $max, $at, $page, $script;

    reset($fields);
    echo "<br>";
    echo '<table width="95%" align="center">';
    echo '<tr bgcolor="#000066">';
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        if ('prop_id' != $field) {
            echo '<td align="left" ' . ($by == $field ? 'bgcolor="#336699"' : 'class="tableHeader"') . '><b>';
            echo "<a href=\"$script?query=" . urlencode($query) . "&max=$max&at=$at&page=$page";
            echo "&by=$field&order=" . ("desc" == $order ? "asc" : "desc") . '" class="td_options">';

            if ('name' == $field) {
                echo "Name";
            } else if ('accountno' == $field) {
                echo "Account No.";
            } else if ('base_currency' == $field) {
                echo "Base Currency";
            } else if ('customer_discount' == $field) {
                echo "Customer Discount";
            } else if ('store_discount' == $field) {
                echo "Store Discount";
            } else if ('handling' == $field) {
                echo "Handling";
            } else if ('hndasperc' == $field) {
                echo "Hnd as Perc";
            } else if ('source' == $field) {
                echo "Source";
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
    global $fields, $actions, $script;

    reset($fields);
    $prop_id = $cols[key($fields)];
    echo $shaded ? '<tr class="tableRow">' : '<tr class="tableRowAlt">';
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        if ('prop_id' != $field) {
            echo "<td align=\"" . $fields[key($fields)] . "\">";
            echo (stripslashes($cols[key($fields)])) . '</td>';
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

function edit_Service()
{
    global $DB, $prop_id;
    echo $name         = ($_POST["name"]);
    $accountno         = ($_POST["accountno"]);
    $base_currency     = ($_POST["base_currency"]);
    $customer_discount = ($_POST["customer_discount"]);
    $store_discount    = ($_POST["store_discount"]);
    $handling          = ($_POST["handling"]);
    $hndasperc         = ($_POST["hndasperc"]);
    $source            = ($_POST["source"]);

    $statement = "UPDATE shipping_services SET
					name = '" . addslashes(htmlspecialchars($name)) . "',
					accountno = '" . addslashes(htmlspecialchars($accountno)) . "',
					base_currency = '" . addslashes(htmlspecialchars($base_currency)) . "',
					customer_discount = '" . addslashes(htmlspecialchars($customer_discount)) . "',
					store_discount = '" . addslashes(htmlspecialchars($store_discount)) . "',
					handling = '" . addslashes(htmlspecialchars($handling)) . "',
					hndasperc = '" . addslashes(htmlspecialchars($hndasperc)) . "',
					source = '" . addslashes(htmlspecialchars($source)) . "'
				WHERE prop_id = '$prop_id'";
    $DB->query($statement);
    echo $statement;
    echo "Service id:$prop_id updated.<br>";
}

switch ($action) {
    case "do_edit":
        edit_Service();
        break;
    case "edit":
        include 'edit_Service_edit_form.inc.php';
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
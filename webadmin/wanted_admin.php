<?php
/******************************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........: Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:
 ** Last changed..:
 ** Notes.........:
 *****************************************************************************/

include_once __DIR__ . '/common.inc.php';

$table = 'wanted';

if (isset($print)) {
    $title = 'Wanted Listing';
    include_once __DIR__ . '/print_header.inc.php';
} else {
    include_once __DIR__ . '/header.inc.php';
}

$script = 'wanted_admin.php';

//$fields[id] = 'center';
$fields['date']         = 'center';
$fields['item']         = 'left';
$fields['target_price'] = 'right';
$fields['customer']     = 'center';
$fields['country']      = 'center';
$fields['expire']       = 'center';
$fields['offers']       = 'center';
$fields['active']       = 'center';

$id_field = 'id';
reset($fields);

$actions[] = '<a href="$script?action=toggle_active&state=$cols[active]&id=$id&query=">$active</a>';
$actions[] = '<a href="$script?action=edit&id=$id">Edit</a>';
$actions[] = '<a href="$script?action=delete&id=$id">Delete</a>';

$items = $DB->query_first("SELECT COUNT(*) as total FROM wanted");

$gets = get_vars();

$img_location     = "images/img_wanted/";
$img_location_abs = $_SERVER['DOCUMENT_ROOT'] . '/' . $img_location;

check_image_dir($img_location_abs);
$is_expired  = $DB->query_first("SELECT COUNT(*) AS total FROM wanted WHERE expiry_date > 0 AND expiry_date < NOW()");
$not_expired = $DB->query_first("SELECT COUNT(*) AS total FROM wanted WHERE expiry_date = 0 OR expiry_date >= NOW()");

if (!isset($print)) {

?>
<!-- wanted admin -->
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
<form action="<?=$script . $gets;?>" method="post" name="admin_form" enctype="multipart/form-data">
	<br>
      <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td bgcolor="#EEEEEE" width="20%">
            <div align="center"><b>Wanted</b></div>
          </td>
          <td bgcolor="#EEEEEE" width="80%">
            <div align="right">
			<a href="<?=$script;?>?action=add">Add New Wanted</a> |
			<a href="<?=$script;?>?query=&expired=y">List Expired (<?=$is_expired['total'];?>)</a> |
			<a href="<?=$script;?>?query=&expired=n">List Current (<?=$not_expired['total'];?>)</a> |
			<a href="<?=$script;?>?query=">List All Wanted (<?=$items['total'];?>)</a>
			</div>
          </td>
        </tr>
      </table>
<!-- search box -->
      <p align="right">
        <input type="text" name="query" size="20" value="<?=htmlspecialchars($_REQUEST['query']);?>">
        <input type="submit" name="search" value="Search">
      </p>
<!-- //search box -->
<div align="center">
<a href="<?="$script?query=" . urlencode($_REQUEST['query']) . "&max=$max&by=$by&order=$order&expired=$expired&alpha=$alpha&print=y";?>" target="_blank">Print</a>
</div>
<?php
}

function searchWanted($query)
{
    global $DB, $at, $fields, $order, $by, $alpha, $expired, $print;
    global $script;

    //$query = ereg_replace('\(|\)|\+|\?|\[|\]|\$|\^', '',  $query);
    $query = str_replace(array('(', ')', '+', '?', '[', ']', '$', '^'), '', $query);

    //keywords
    $arrKeywords = explode(" ", trim($query));

    //search query by key words
    if (!empty($query)) {
        $extra = " ";
    }

    for ($index = 0; $index < count($arrKeywords); $index++) {
        $word = trim(strtolower($arrKeywords[$index]));

        if (!empty($word)) {
            $extra .= " AND ";
        }

        if (!empty($word)) {
            $extra .= "(LOWER(w.item) REGEXP \"$word\")"; //just a normail regular exp. search
        }
    } //end search query keywords

    if ('y' == $expired) {
        $expired_clause = ' AND w.expiry_date > 0 AND w.expiry_date < NOW() ';
    } else if ('n' == $expired) {
        $expired_clause = ' AND (w.expiry_date = 0 OR w.expiry_date >= NOW()) ';
    }

    //search statement
    $statement = "SELECT w.*,
						w.pref_location AS country,
						DATE_FORMAT(w.date_added, '%d-%m-%Y') AS `date`,
						CONCAT(w.target_price, ' ', w.target_currency) AS target_price,
						w.customer_name as customer,
						DATE_FORMAT(w.expiry_date, '%d-%m-%Y') as expire,
						COUNT(wo.id) AS offers
					FROM wanted w
						LEFT JOIN wanted_offers wo ON w.id = wo.wanted_id
					WHERE 1 $expired_clause";

    if ('' != $alpha) {
        $extra .= " AND w.item like '$alpha%' ";
    }

    $group_by = ' GROUP BY w.id';
    //if max is not yet set
    if (!isset($max)) {
        $result = $DB->query($statement . $extra . $group_by);
        $max    = $DB->num_rows($result);
    }

    $inc = 50;
    $at  = intval($at);

    if (!isset($order) || '' == $order) {
        $order = 'asc';
    }

    if (!isset($by) || '' == $by) {
        $by = 'item';
    }

    $order_by = " ORDER BY $by $order";

    if (!isset($print)) {
        $limit = " LIMIT $at, $inc ";
    }

    //echo $statement.$extra;

    $result = $DB->query($statement . $extra . $group_by . $order_by . $limit);

    $statement = "	SELECT count(w.id) AS total,
					LEFT(w.item,1) AS alphabets
					FROM wanted w
					WHERE
						w.active = 'y' $expired_clause
					GROUP BY alphabets ";

    $link = "$script?query=" . urlencode($query) . "&max=$max&by=$by&order=$order&expired=$expired&alpha=";

    if (!isset($print)) {
        show_alphabets($statement, $link, $alpha);
    }

    //show_links(get_links($max, "$script?query=".urlencode($query)."&max=$max&by=$by&order=$order",25,4));

    drawHeaderRow();
    $shaded = true;
    while ($row = $DB->fetch_array($result)) {
        showResult($row, $shaded = !$shaded);
    }
    drawHeaderEnd();
    //show more links
    if (!isset($print)) {
        show_links(get_links($max, $link . $alpha, $inc, 4));
    }

}

function drawHeaderRow()
{
    global $fields, $order, $by, $max, $at, $page, $script, $alpha, $expired, $print;

    reset($fields);
    echo "<br>";

    if (isset($print)) {
        echo '<table align="center" width="100%" cellpadding="2" cellspacing="0">';
    } else {
        echo '<table align="center">';
    }

    if (isset($print)) {
        echo '<tr>';
    } else {
        echo '<tr bgcolor="#000066">';
    }

    for ($i = 0; $i < sizeof($fields); $i++) {

        $class = 'class="topRight"';

        if (!isset($print)) {
            $class = '';
        }

        if (0 == $i && isset($print)) {
            $class = 'class="topLeft"';
        }

        $field = key($fields);
        echo '<td align="center" ' . ($by == $field && !isset($print) ? 'bgcolor="#336699"' : '') . " $class><b>";
        echo "<a href=\"$script?query=" . urlencode($_REQUEST['query']) . "&max=$max&at=$at&page=$page";
        if (isset($print)) {
            echo '&print=y';
        }

        echo "&by=$field&order=" . ("desc" == $order ? "asc" : "desc") . '&alpha=' . $alpha . '&expired=' . $expired . '" class="td_options">';
        echo $field;
        echo '</a>';
        if ($by == $field) {
            echo '  <img src="' . $order . '.gif" width="7" height="7" border="0">  ';
        }

        echo '</b></td>';
        next($fields);
    }
    if (!isset($print)) {
        echo "<td>&nbsp;</td>";
    }

    echo '</tr>';

}

function drawHeaderEnd()
{
    echo '</table>';
    echo "<br>";
}

function showResult($cols, $shaded)
{
    global $fields, $actions, $script, $table, $id_field, $print;

    reset($fields);
    $id = $cols[$id_field];

    $locked = locked($table, $id);

    if ($locked) {
        echo '<tr bgcolor="pink">';
    } else {
        echo $shaded ? '<tr>' : '<tr bgcolor="#EEEEEE">';
    }

    for ($i = 0; $i < sizeof($fields); $i++) {
        $class = 'class="rowRight"';

        if (!isset($print)) {
            $class = '';
        }

        if (0 == $i && isset($print)) {
            $class = 'class="rowLeft"';
        }

        echo "<td align=\"" . $fields[key($fields)] . "\" $class>";
        if (key($fields) == 'customer') {
            echo "<a href='mailto:" . $cols['customer_email'] . "'>" . $cols['customer'] . "</a>\n";
        } else {
            echo $cols[key($fields)];
        }

        echo '&nbsp;</td>';
        next($fields);
    }

    if (!isset($print)) {
        echo '<td nowrap align="right">';

        $active = 'Activate';
        if ('y' == $cols['active']) {
            $active = 'De-Activate';
        }

        for ($i = 0; $i < sizeof($actions); $i++) {
            if (0 != $i) {
                echo " | ";
            }

            echo $actions[$i];

        }
        echo '</td>';
    }
    echo "</tr>";
}

function toggleActive($current_state)
{
    global $id, $DB;

    $statement = "UPDATE wanted SET active = '" . ("y" == $current_state ? "n" : "y") . "' WHERE id = '$id'";

    $DB->query($statement);

    echo "ID:$id is now " . ("y" == $current_state ? "De-activated" : "Activated") . ".<br>";

}

function doAdd()
{
    global $DB, $data, $images, $img_location;

    $statement = 'INSERT INTO wanted (' . get_fields($data) . ', date_added) VALUES (' . get_values($data) . ', NOW())';
    $result    = $DB->query($statement);

    $id = $DB->insert_id($result);

    if (is_file($images['image'])) {
        $img = process_image($images['image'], 'img_wanted_' . $id);

        if ($img) {
            $statement = "UPDATE wanted SET image = '$img_location$img' WHERE id = '$id'";
            $DB->query($statement);
        }
    }

    echo "New Wanted ID:$id Added<br />";
}

function doEdit()
{
    global $DB, $data, $images, $id, $img_location;

    $id = intval($id);

    $img_set_clause = '';

    if (is_file($images['image'])) {
        $img = process_image($images['image'], 'img_wanted_' . $id);
        if ($img) {
            $img_set_clause = ", image = '$img_location$img'";
        }

    }

    $statement = 'UPDATE wanted SET ' . get_set_values($data) . "$img_set_clause WHERE id = '$id'";

    $DB->query($statement);

    echo "Wanted ID:$id Updated<br />";
}

function doDelete()
{
    global $id, $DB;

    $statement = "DELETE FROM wanted WHERE id = '$id'";
    $DB->query($statement);
    echo "ID:$id Deleted.<br>";
}

if (trim($_REQUEST['query']) == '') {

    switch ($action) {
        case "toggle_active":
            toggleActive($state);
            break;
        case "do_add":
            doAdd();
            break;
        case "do_edit":
            if (!locked($table, $id)) {
                doEdit();
            }

            break;
        case "do_delete":
            if (!locked($table, $id)) {
                doDelete();
            }

            break;
        case "add":
            include __DIR__ . '/wanted_add_form.inc.php';
            break;
        case "edit":
            include __DIR__ . '/wanted_edit_form.inc.php';
            break;
        case "delete":
            include __DIR__ . '/generic_delete_form.inc.php';
            break;

    }
}

if (isset($_REQUEST['query'])) {
    searchWanted(htmlspecialchars($_REQUEST['query']));
}

?>
</form>

<!--//wanted admin -->
<?php
if (isset($print)) {
    echo '</body></html>';
} else {
    include_once __DIR__ . '/footer.inc.php';
}

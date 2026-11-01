<?php
/***********************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:
 ** Last changed..:
 ** Notes.........:
 ***********************************************************************/

$section_code = "EMAILS";

include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$script = 'sent_items.php?author=xwz';

$fields['id']          = 'center';
$fields['date_sent']   = 'left';
$fields['subject']     = 'left';
$fields['composed_by'] = 'left';

$id_field = 'id';

$table = 'mailout_archive';

//$actions[] = '<a href="$script&action=delete&id=$id">Delete</a>';
$actions[] = '<a href="$script&action=archived&id=$id">View</a>';

$items = $DB->query_first("SELECT COUNT(*) as total FROM $table");

?>

<!-- specials admin -->

<form accept-charset="utf-8" action="<?=$script;?>" method="post" name="admin_form">
	<br>
      <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td bgcolor="#EEEEEE" width="20%">
            <div align="center"><b>Sent Items</b></div>
          </td>
          <td bgcolor="#EEEEEE" width="80%">
            <div align="right">
			<a href="<?=$script?>&query=">List Sent Items(<?=$items['total']?>)</a>
			</div>
          </td>
        </tr>
      </table>

<!-- search box -->
      <p align="right">
        <input type="text" name="query" size="20" value="<?=htmlspecialchars($_REQUEST["query"] ?? '')?>">
        <input type="submit" name="search" value="Search">
      </p>
<!-- //search box -->

<?php

function search($query)
{
    global $DB, $at, $fields, $order, $by, $script, $table;

    $query = trim($query);

    //keywords
    $arrKeywords = explode(" ", $query);

    //search query by key words
    $extra = '';
    for ($index = 0; $index < count($arrKeywords); $index++) {
        $word = trim(strtolower($arrKeywords[$index]));

        if (!empty($word)) {
            $extra .= " AND ";
        }

        if (!empty($word)) {
            if (preg_match("/^[\\*\\+\\\\]/", $word)) {
                $extra .= "(LOWER(subject) LIKE '%$word%' OR LOWER(id) LIKE '%$word%')";
            } else {
                $extra .= "(LOWER(subject) REGEXP \"$word\" OR LOWER(id) REGEXP \"$word\")";
            }
            //else just a normail regular exp. search
        }
    } //end search query keywords

    //search statement
    $statement = "SELECT *, DATE_FORMAT(date_sent, '%Y-%m-%d') as date_sent, user_id as composed_by FROM $table WHERE 1 ";

    //if max is not yet set
    if (!isset($max)) {
        $result = $DB->query($statement . $extra);
        $max    = $DB->num_rows($result);
    }

    $inc = 50;
    $at  = intval($at);

    if (!isset($order)) {
        $order = 'ASC';
    }

    if (!isset($by)) {
        $by = 'date_sent';
    }

    $order_by = " ORDER BY $by $order";
    $limit    = " LIMIT $at, $inc ";
    $result   = $DB->query($statement . $extra . $order_by . $limit);

    show_links(get_links($max, "$script&query=" . urlencode($query) . "&max=$max&by=$by&order=$order", $inc, 4));

    draw_header_row();
    $shaded = true;
    while ($row = $DB->fetch_array($result)) {
        show_result($row, $shaded = !$shaded);
    }
    draw_header_end();
    //show more links
    show_links(get_links($max, "$script&query=" . urlencode($query) . "&max=$max&by=$by&order=$order", $inc, 4));
}

function draw_header_row()
{
    global $fields, $order, $by, $max, $at, $page, $script;

    reset($fields);

    echo '<table width="80%" align="center" cellspacing="1" cellpadding="4">';
    echo '<tr bgcolor="#000066">';
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        echo '<td align="center" ' . ($by == $field ? 'bgcolor="#336699"' : '') . '><b>';
        echo "<a href=\"$script&query=" . urlencode($_REQUEST['query'] ?? '') . "&max=$max&at=$at&page=$page";
        echo "&by=$field&order=" . ("desc" == $order ? "asc" : "desc") . '" class="td_options">';
        echo $field;
        echo '</a>';
        if ($by == $field) {
            echo '  <img src="../' . $order . '.gif" width="7" height="7" border="0">  ';
        }

        echo '</b></td>';
        next($fields);
    }
    echo "<td>&nbsp;</td>";
    echo '</tr>';

}

function draw_header_end()
{
    echo '</table>';
}

function do_delete()
{
    global $id, $DB, $table;

    $statement = "DELETE FROM " . $table . " WHERE id = '$id'";

    $DB->query($statement);

    echo "ID:$id Deleted.<br>";
}

function show_result($cols, $shaded)
{
    global $fields, $actions, $script, $table;

    reset($fields);
    $id = $cols[key($fields)];
    echo $shaded ? '<tr>' : '<tr bgcolor="#EEEEEE">';
    for ($i = 0; $i < sizeof($fields); $i++) {
        $key = key($fields);
        echo "<td align=\"" . $fields[$key] . "\">";
        echo $cols[$key] . '</td>';
        next($fields);
    }
    echo '<td nowrap>';
    for ($i = 0; $i < sizeof($actions); $i++) {
        if (0 != $i) {
            echo " | ";
        }

        echo $actions[$i];

    }
    echo '</td>';
    echo "</tr>";
}

function showArchivedMailout()
{
    global $id;
?>
	<script language="javascript">
		window.open('preview.php?view=archive&id=<?=$id?>');
	</script>
<?php
}

if (trim($_REQUEST["query"] ?? '') == '') {
    switch ($_REQUEST['action'] ?? '') {
        case "delete":
            include '../generic_delete_form.inc.php';
            break;
        case "do_delete":
            do_delete();
            break;
        case "archived":
            showArchivedMailout();
            break;
    }
}

//if(array_key_exists("query", $_REQUEST))
search(htmlspecialchars($_REQUEST["query"] ?? ''));

?>
</form>

<!--//specials admin -->
<?php
include '../footer.inc.php';

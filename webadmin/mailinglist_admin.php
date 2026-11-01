<?php

$section_code = "CUSTOMERS";
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

/***********************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:
 ** Last changed..:
 ** Notes.........:
 ***********************************************************************/

$script = 'mailinglist_admin.php';

$fields['id']         = 'center';
$fields['date_added'] = 'left';
$fields['name']       = 'left';
$fields['email']      = 'left';
$fields['active']     = 'center';

$actions[] = '<a href="$script?action=toggle_active&state=$cols[active]&id=$id&query=">Toggle Active</a>';
$actions[] = '<a href="$script?action=edit&id=$id">Edit</a>';
$actions[] = '<a href="$script?action=delete&id=$id">Delete</a>';

$items = $DB->query_first("SELECT COUNT(*) as total FROM mailinglist");

$gets = get_vars();
?>

<!-- parts admin -->

<form accept-charset="utf-8" action="<?=$script . $gets;?>" method="post" name="admin_form">
	<br>
      <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td bgcolor="#EEEEEE" width="20%">
            <div align="center"><b>Mailing List</b></div>
          </td>
          <td bgcolor="#EEEEEE" width="80%">
            <div align="right">
			<a href="<?=$script;?>?action=add">Add New Email</a> |
			<a href="<?=$script;?>?query=">List All Emails (<?=$items['total'];?>)</a>
			</div>
          </td>
        </tr>
      </table>
<!-- search box -->
      <p align="right">
        <input type="text" name="query" size="20" value="<?=htmlspecialchars(stripslashes($_REQUEST["query"]));?>">
        <input type="submit" name="search" value="Search">
      </p>
<!-- //search box -->

<?php

function search($query)
{
    global $DB, $at, $fields, $order, $by, $script, $location, $type;

    $query = trim($query);

    //keywords
    $arrKeywords = explode(" ", $query);

    //search query by key words

    for ($index = 0; $index < count($arrKeywords); $index++) {
        $word = trim(strtolower($arrKeywords[$index]));

        if ("" != $word) {
            $extra .= " AND ";
        }

        if (!empty($word)) {
            if (preg_match("/^\\d+$/", $word)) //if its a search by id
            {
                $extra .= "id = '$word'";
            } elseif (preg_match("/^[\\*\\+\\\\]/",$word)) {
                $extra .= "(LOWER(email) LIKE '%$word%' OR LOWER(name) LIKE '%$word%')";
            } else {
                $extra .= "(LOWER(email) REGEXP \"$word\" OR LOWER(name) REGEXP \"$word\")";
            }
            //else just a normail regular exp. search
        }
    } //end search query keywords

    //search statement
    $statement = "SELECT * FROM mailinglist WHERE 1";

    //if max is not yet set
    if (!isset($max)) {
        $result = $DB->query($statement . $extra);
        $max    = $DB->num_rows($result);
    }

    $inc = 50;
    $at  = intval($at);

    if (!isset($order)) {
        $order = 'desc';
    }

    if (!isset($by)) {
        $by = key($fields);
    }

    $order_by = " ORDER BY $by $order";
    $limit    = " LIMIT $at, $inc ";
    $result   = $DB->query($statement . $extra . $order_by . $limit);

    show_links(get_links($max, "$script?query=" . urlencode($query) . "&max=$max&by=$by&order=$order", $inc, 4));

    drawHeaderRow();
    $shaded = true;
    while ($row = $DB->fetch_array($result)) {
        showResult($row, $shaded = !$shaded);
    }
    drawHeaderEnd();
    //show more links
    show_links(get_links($max, "$script?query=" . urlencode($query) . "&max=$max&by=$by&order=$order", $inc, 4));
}

function drawHeaderRow()
{
    global $fields, $order, $by, $max, $at, $page, $script;

    reset($fields);

    echo '<br>';

    echo '<table widt="95" align="center">';
    echo '<tr bgcolor="#000066">';
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        echo '<td align="center" ' . ($by == $field ? 'bgcolor="#336699"' : '') . '><b>';
        echo "<a href=\"$script?query=" . urlencode($_REQUEST['query']) . "&max=$max&at=$at&page=$page";
        echo "&by=$field&order=" . ("desc" == $order ? "asc" : "desc") . '" class="td_options">';
        echo $field;
        echo '</a>';
        if ($by == $field) {
            echo '  <img src="' . $order . '.gif" width="7" height="7" border="0">  ';
        }

        '</b></td>';
        next($fields);
    }
    echo "<td>&nbsp;</td>";
    echo '</tr>';

}

function drawHeaderEnd()
{
    echo '</table><br>';
}

function addNewEmail()
{
    global $name, $email, $country, $company, $comments, $active;
    global $DB;

    $statement = "INSERT INTO
					mailinglist(
						name,
						email,
						date_added,
						active,
						company,
						country,
						comments
					)";

    $statement .= "VALUES (
					'" . addslashes(htmlspecialchars($name)) . "',
					'" . addslashes(htmlspecialchars($email)) . "',
					NOW(),
					'$active',
					'" . addslashes(htmlspecialchars($company)) . "',
					'" . addslashes(htmlspecialchars($country)) . "',
					'" . addslashes(htmlspecialchars($comments)) . "'

				)";

    $DB->query($statement);

    echo "New email $email added.<br>";
}

function editEmail()
{

    global $name, $email, $country, $company, $comments, $active, $id;
    global $DB;

    $statement = "UPDATE mailinglist SET
					name = '" . addslashes(htmlspecialchars($name)) . "',
					email = '" . addslashes(htmlspecialchars($email)) . "',
					country = '" . addslashes(htmlspecialchars($country)) . "',
					company = '" . addslashes(htmlspecialchars($company)) . "',
					comments = '" . addslashes(htmlspecialchars($comments)) . "',
					active = '$active'
				WHERE id = '$id'";

    $DB->query($statement);
    echo "$email updated.<br>";
}

function deleteEmail()
{
    global $id, $DB;

    $statement = "DELETE FROM mailinglist WHERE id = '$id'";
    $DB->query($statement);
    echo "Mailing list recipient deleted.<br>";
}

function showResult($cols, $shaded)
{
    global $fields, $actions, $script;

    reset($fields);
    $id = $cols[key($fields)];
    echo $shaded ? '<tr>' : '<tr bgcolor="#EEEEEE">';
    for ($i = 0; $i < sizeof($fields); $i++) {
        $key = key($fields);
        echo "<td align=\"" . $fields[$key] . "\">";
        if ('active' == $key) {
            echo $cols[$key] == '1' ? 'Yes' : 'No' . '</td>';
        } else {
            echo stripslashes($cols[$key]) . '</td>';
        }

        next($fields);
    }
    echo '<td nowrap>';

    $type = str_replace(' ', '_', strtolower($cols['type_name']));
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

function toggleActive($current_state)
{
    global $id, $DB;

    $statement = "UPDATE mailinglist SET active = '" . ("1" == $current_state ? "0" : "1") . "' WHERE id = '$id'";

    $DB->query($statement);

    echo "ID:$id is now " . ("1" == $current_state ? "De-activated" : "Activated") . ".<br>";
}

if (trim($_REQUEST["query"]) == '') {
    switch ($action) {
        case "add":
            include __DIR__ . '/mailinglist_add_form.inc.php';
            break;
        case "edit":
            include __DIR__ . '/mailinglist_edit_form.inc.php';
            break;
        case "delete":
            include __DIR__ . '/mailinglist_delete_form.inc.php';
            break;
        case "do_add":
            addNewEmail();
            break;
        case "do_edit":
            editEmail();
            break;
        case "toggle_active":
            toggleActive($state);
            break;

        case "do_delete":
            deleteEmail();
            break;

    }
}

if (array_key_exists("query", $_REQUEST)) {
    search(addslashes(htmlspecialchars($_REQUEST["query"])));
}

//load_hidden_vars();

?>
</form>

<!--//parts admin -->
<?php
include_once __DIR__ . '/footer.inc.php';

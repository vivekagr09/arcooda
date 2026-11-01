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
?>
<style type="text/css">
<!--
.titles	{
			font-weight: bold;
			font-size: 10pt;
			color: #FFFFFF;
			background-color: #555555;
			text-align: center;
		}
.data	{
			font-size: 10pt;
			text-align: center;
		}
-->
</style>
<script LANGUAGE="JavaScript">
<!--
function printpage() {
        if (window.print)
        {
			window.print();
        }
        else
        {
                alert('To print this page, select Print Frame from the file menu.');
        }
}

function doPrint()
{
        if (confirm("Click OK to print this document"))
        {
                printpage();
        }

}
-->
</script>
<?php

if (!isset($table)) {
    include_once __DIR__ . '/header.inc.php';
    exit();
}

$script = 'staff_admin.php?table=' . $table;

$columns = 0;
foreach ($DB->table_fields($table) as $v) {
    $fields[$v] = 'left';
    $columns++;
}

$id_field         = key($fields);
$user             = get_user();
$allowed_sections = get_allowed_sections($user);

reset($fields);
if (false == $print) {

    if (has_permission('USERS', $allowed_sections)) {
        $actions[] = '<a href="$script&action=edit&id=$id">Edit</a>';
        $actions[] = '<a href="/webadmin/users/admin.php?action=delete&id=$id">Delete</a>';
    }
}

$items = $DB->query_first("SELECT COUNT(*) AS `total` FROM $table");

$gets = get_vars();

$img_location     = "images/";
$img_location_abs = $_SERVER['DOCUMENT_ROOT'] . '/' . $img_location;

$table_title = ucwords(str_replace('_', ' ', $table));

?>

<!-- parts admin -->

<form action="<?=$script;?>" method="post" enctype="multipart/form-data" name="admin_form">
	<br>
	<?php if (false == $print && has_permission('USERS', $allowed_sections)) {?>
     <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td bgcolor="#EEEEEE" width="20%">
            <div align="center"><b><?=$table_title;?></b></div>
          </td>
          <td bgcolor="#EEEEEE" width="80%">
            <div align="right">

				<?php if (has_permission('USERS', $allowed_sections)) {?>
				<a href="/webadmin/users/admin.php?action=add">Add New <?=$table_title;?></a> |
				<?php }?>

				<a href="<?=$script;?>&query=">List <?=$table_title . ' (' . $items['total'] . ')';?></a>
			</div>
          </td>
        </tr>
      </table>
	 <?php }?>
<!-- search box
      <p align="right">
        <input type="text" name="query" size="20" value="<?=htmlspecialchars(stripslashes($query));?>">
        <input type="submit" name="search" value="Search">
      </p>
//search box -->

<?php

function search($query)
{
    global $DB, $at, $order, $by, $script, $location, $type, $table, $fields, $id_field, $print;

    $query = trim($query);

    //keywords
    $arrKeywords = explode(" ", $query);

    //search query by key words

    for ($index = 0; $index < count($arrKeywords); $index++) {
        $word = trim(strtolower($arrKeywords[$index]));

        if (!empty($word)) {
            $extra .= " AND ";
        }

        if (!empty($word)) {
            $extra .= '(';
            for ($i = 0; $i < count($fields); $i++) {
                $key = key($fields);

                $extra .= "LOWER($key) LIKE '%$word%'";

                if ($i > 0 && $i < count($fields) - 1) {
                    $extra .= 'OR';
                }

                next($fields);
            }

            $extra .= ')';
        }
    } //end search query keywords

    //search statement
    if (true == $print) {
        //First Name, Last Name, Home Phone, Mobile, Work Email,  Home Email,  Icq No.
        $statement = "SELECT first_name, last_name, email, home_phone, mobile, home_email, icq_no FROM $table WHERE 1 ";
    } else {
        $statement = "SELECT * FROM $table WHERE 1 ";
    }
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

    reset($fields);

    if (!isset($by)) {
        $by = 'last_name';
    }

    $order_by = " ORDER BY $by $order";
    $result   = $DB->query($statement . $extra . $order_by . $limit);

    //show_links(get_links($max, "$script&query=".urlencode($query)."&max=$max&by=$by&order=$order",$inc,4));

    drawHeaderRow();
    $shaded = true;
    while ($row = $DB->fetch_array($result)) {
        showResult($row, $shaded = !$shaded);
    }
    drawHeaderEnd();
    //show more links
    //show_links(get_links($max, "$script&query=".urlencode($query)."&max=$max&by=$by&order=$order",$inc,4));
}

function drawHeaderRow()
{
    global $fields, $order, $by, $query, $max, $at, $page, $script, $print;

    reset($fields);

    echo '<br>';
    if (true == $print) {
        echo '<table width="95%" border="1" cellspacing="1" cellpadding="3" align="center">';
    } else {
        echo '<table width="95%" cellspacing="1" cellpadding="3" align="center">';
    }

    echo '<tr bgcolor="#000066">';
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        if ('company_id' != $field && 'job_type' != $field && "id" != $field && "user" != $field && "date_added" != $field && 'bonus_ratio' != $field && (false == $print || ("post_code" != $field && "address" != $field))) {
            if (true == $print) {
                echo '<td class="titles">';
            } else {
                echo '<td align="center" ' . ($by == $field ? 'bgcolor="#336699"' : '') . '>';
            }

            echo "<b><a href=\"$script&query=" . urlencode($query) . "&max=$max&at=$at&page=$page";
            echo "&by=$field&order=" . ("desc" == $order ? "asc" : "desc") . '" class="td_options">';
            echo titleCase($field);
            echo '</a>';
            if ($by == $field) {
                echo '  <img src="' . $order . '.gif" width="7" height="7" border="0">  ';
            }

            '</b></td>';
        }
        next($fields);
    }
    if (false == $print) {
        echo "<td>&nbsp;</td>";
    }
    echo '</tr>';

}
function showResult($cols, $shaded)
{
    global $fields, $actions, $script, $me, $allowed_sections, $print;
    $user    = get_user();
    $rowuser = $cols['user'];

    reset($fields);
    $id = $cols[key($fields)];

    if (true == $me && $rowuser == $user) {
        include __DIR__ . '/staff_show_row.inc.php';
    } else if (false == $me) {
        include __DIR__ . '/staff_show_row.inc.php';
    }
}

function drawHeaderEnd()
{
    echo '</table><br>';
}

function addNew()
{
    global $field_data, $table_title, $table;
    global $DB;

    $insert_fields = '(';
    $value_fields  = '(';

    for ($i = 0; $i < count($field_data); $i++) {
        $key = key($field_data);

        $insert_fields .= $key;
        $value_fields .= "'" . htmlspecialchars($field_data[$key]) . "'";

        if ($i < count($field_data) - 1) {
            $insert_fields .= ', ';
            $value_fields .= ', ';
        }

        next($field_data);
    }
    $insert_fields .= ')';
    $value_fields .= ')';

    reset($field_data);

    $statement = "INSERT INTO $table $insert_fields VALUES $value_fields";

    $DB->query($statement);

    $id = $DB->insert_id();

    echo "New $table_title ID:$id added.<br>";
}

function edit()
{

    global $field_data, $table_title, $table, $id, $id_field;
    global $DB;

    for ($i = 0; $i < count($field_data); $i++) {
        $key = key($field_data);
        $update_fields .= "$key = '" . htmlspecialchars($field_data[$key]) . "'";
        if ($i < count($field_data) - 1) {
            $update_fields .= ', ';
        }

        next($field_data);
    }

    reset($field_data);

    $statement = "UPDATE $table SET $update_fields WHERE $id_field = '$id'";

    $DB->query($statement);

    echo "$id: updated<br>";

    $row       = $DB->query_first("SELECT * FROM `highwaygroup`.`users` WHERE `id` = '" . $id . "'");
    $user_name = $row['user'];

    $infile   = $_FILES['new_signature'];
    $file_ext = substr($infile['name'], strrpos($infile['name'], '.'));
    if (substr($file_ext, 0, 1) == '.') {
        $file_ext = substr($file_ext, 1);
    }

    $outfile  = $user_name . '.' . $file_ext;
    $root_dir = $_SERVER['DOCUMENT_ROOT'] . '/sigs/';

    if ('' == $_POST['old_signature']) {
        $row = $DB->query_first("SELECT * FROM sigs WHERE user_name='$user_name'");
        if ($row) {
            unlink($root_dir . $row['sig_image']);
            $DB->query("DELETE FROM sigs WHERE user_name='$user_name'");
        }
    }

    if (filesize($infile['tmp_name']) && !$infile['error']) {
        $row = $DB->query_first("SELECT * FROM sigs WHERE user_name='$user_name'");
        if ($row) {
            unlink($root_dir . $row['sig_image']);
            $DB->query("DELETE FROM sigs WHERE user_name='$user_name'");
        }
        if (move_uploaded_file($infile['tmp_name'], $root_dir . $outfile)) {
            $DB->query(sprintf(
                "INSERT INTO `sigs` (`staff_id`, `user_name`, `sig_image`) VALUES (%d, %s, %s)",
                $id,
                $DB->escape($user_name),
                $DB->escape($outfile)
            ));
        } else {
            echo "Failed to add new signature<br />";
        }

    }
}

if (trim($query) == '') {
    switch ($action) {
        case "add":
            include __DIR__ . '/staff_add_form.inc.php';
            break;
        case "edit":
            include __DIR__ . '/staff_edit_form.inc.php';
            break;
        case "delete":
            include __DIR__ . '/staff_delete_form.inc.php';
            break;
        case "do_add":
            addNew();
            break;
        case "do_edit":
            edit();
            break;
        case "do_delete":
            delete();
            break;

    }
}

if (isset($query)) {
    $query = htmlspecialchars($query);
    search(addslashes(addslashes(($query))));
}
; //load_hidden_vars();

?>
</form>


<center><a h</center>
<?php if (false == $print) {
?>
	<a href='staff_admin.php?print=true'>Print</a>
<?php
} else {
?>
<script language="javascript">
	doPrint();
</script>
<?php
}

include_once __DIR__ . '/footer.inc.php';

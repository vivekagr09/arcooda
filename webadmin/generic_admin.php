<?php
/******************************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........: Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:
 ** Last changed..:
 ** Notes.........:
 *****************************************************************************/

$section_code = "PRD_CFG";
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';
$table=($_REQUEST['table'])?$_REQUEST['table']:'';
$action=($_REQUEST['action'])?$_REQUEST['action']:'';
if (!isset($table)) {
    include_once __DIR__ . '/footer.inc.php';
    exit();
}

$script = 'generic_admin.php?table=' . $table;

$columns = 0;
foreach ($DB->table_fields($table) as $v) {
    $fields[$v] = 'left';
    $columns++;
}

$id_field = key($fields);

reset($fields);

$actions[] = '<a href="$script&action=edit&id=$id">Edit</a>';
$actions[] = '<a href="$script&action=delete&id=$id">Delete</a>';

$items = $DB->query_first("SELECT COUNT(*) as total FROM $table");

$gets = get_vars();

$img_location     = "images/";
$img_location_abs = $_SERVER['DOCUMENT_ROOT'] . '/' . $img_location;

$table_title = ucwords(str_replace('_', ' ', $table));

?>

<!-- parts admin -->

<form accept-charset="utf-8" action="<?= $script ?>" method="post" enctype="multipart/form-data" name="admin_form">
	<br>
      <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td bgcolor="#EEEEEE" width="20%">
            <div align="center"><b><?= $table_title ?></b></div>
          </td>
          <td bgcolor="#EEEEEE" width="80%">
            <div align="right">
			<a href="<?= $script ?>&action=add">Add New <?= $table_title ?></a> |
			<a href="<?= $script ?>&query=">List <?= $table_title ?> (<?= $items['total'] ?>)</a>
			</div>
          </td>
        </tr>
      </table>
<!-- search box -->
      <p align="right">
        <input type="text" name="query" size="20" value="<?= htmlspecialchars($_REQUEST["query"]) ?>">
        <input type="submit" name="search" value="Search">
      </p>
<!-- //search box -->

<?php

function search($query)
{
    global $DB, $at, $order, $by, $script, $location, $type, $table, $fields, $id_field;

    $query = trim($query);

    //keywords
    $arrKeywords = explode(" ", $query);

    //search query by key words

    for ($index = 0; $index < count($arrKeywords); $index++) {
        reset($fields);
        $word = trim(strtolower($arrKeywords[$index]));

        if (!empty($word)) {
            $extra .= " AND ";
        }

        if (!empty($word)) {
            $extra .= '(';
            for ($i = 0; $i < count($fields); $i++) {
                $key = key($fields);

                $extra .= " LOWER($key) LIKE '%$word%' ";

                if ($i >= 0 && $i < count($fields) - 1) {
                    $extra .= ' OR ';
                }

                next($fields);
            }

            $extra .= ')';
        }
    } //end search query keywords

    //search statement
    $statement = "SELECT * FROM $table WHERE 1 ";

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
        $by = $id_field;
    }

    $order_by = " ORDER BY $by $order";
    $limit    = " LIMIT $at, $inc ";
    $result   = $DB->query($statement . $extra . $order_by . $limit);

    show_links(get_links($max, "$script&query=" . urlencode($query) . "&max=$max&by=$by&order=$order", $inc, 4));

    drawHeaderRow();
    $shaded = true;
    while ($row = $DB->fetch_array($result)) {
        showResult($row, $shaded = !$shaded);
    }
    drawHeaderEnd();
    //show more links
    show_links(get_links($max, "$script&query=" . urlencode($query) . "&max=$max&by=$by&order=$order", $inc, 4));
}

function drawHeaderRow()
{
    global $fields, $order, $by, $max, $at, $page, $script;

    reset($fields);

    echo '<br>';

    echo '<table style="width:100%" align="center">';
    echo '<tr bgcolor="#000066">';
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        echo '<td align="center" ' . ($by == $field ? 'bgcolor="#336699"' : '') . '><b>';
        echo "<a href=\"$script&query=" . urlencode($_REQUEST['query']) . "&max=$max&at=$at&page=$page";
        echo "&by=$field&order=" . ("desc" == $order ? "asc" : "desc") . '" class="td_options">';
        if($field=='show_front'){
            echo "Display Online";
        }else{
            echo $field;
        }
        
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

function isExtValid($ext)
{
    return in_array($ext, array('jpg', 'jpeg', 'png'));
}

function addNew()
{
    global $field_data, $table_title, $table;
    global $DB;

    $insert_fields = '(';
    $value_fields  = '(';

    if (isset($_FILES['manufacturers_colour_image'])) {
        $name      = $_FILES['manufacturers_colour_image']['name'];
        $nameChunk = explode('.', $name);
        $ext       = end($nameChunk);
        $ext       = strtolower($ext);

        if (isExtValid($ext)) {

            $name = time();

            $uploadName = $name . "." . $ext;

            $field_data['img'] = '/img_manufacturers/' . $uploadName;

            $dest = $_SERVER['DOCUMENT_ROOT'] . '/img_manufacturers/' . $uploadName;

            if (!move_uploaded_file($_FILES['manufacturers_colour_image']['tmp_name'], $dest)) {
                // Error report here. Not sure where is error report. (Confused)
            }

            $destination = imagecreatetruecolor(96, 96);

            if ('jpg' == $ext || 'jpeg' == $ext) {
                $source = imagecreatefromjpeg($dest);
            } else {
                $source = imagecreatefrompng($dest);
            }

            list($width, $height) = getimagesize($dest);

            if ('png' == $ext) {
                imagealphablending($destination, false);
                imagesavealpha($destination, true);
                $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
                imagefilledrectangle($destination, 0, 0, 96, 96, $transparent);
            }

            imagecopyresampled($destination, $source, 0, 0, 0, 0, 96, 96, $width, $height);

            if ('jpg' == $ext || 'jpeg' == $ext) {
                imagejpeg($destination, $dest, 100);
            } else {
                imagepng($destination, $dest);
            }
        } else {
            // Error report here. Not sure where is error report. (Confused)
        }
    }

    /*if (isset($_FILES['manufacturers_bw_image'])) {
    $name = $_FILES['manufacturers_bw_image']['name'];
    $nameChunk = explode('.', $name);
    $ext = end($nameChunk);
    $ext = strtolower($ext);

    if (isExtValid($ext)) {

    $name = time();

    $uploadName = $name . "." . $ext;

    $field_data['bw_img'] = '/img_manufacturers/bw/' . $uploadName;

    if (!move_uploaded_file($_FILES['manufacturers_bw_image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/img_manufacturers/bw/' . $uploadName)) {
    // Error report here. Not sure where is error report. (Confused)
    }
    } else {
    // Error report here. Not sure where is error report. (Confused)
    }
    }*/
    for ($i = 0; $i < count($field_data); $i++) {
        $key = key($field_data);

        $insert_fields .= $key;
        $value_fields .= "'" . addslashes(htmlspecialchars($field_data[$key])) . "'";

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

    if (isset($_FILES['manufacturers_colour_image'])) {
        $name      = $_FILES['manufacturers_colour_image']['name'];
        $nameChunk = explode('.', $name);
        $ext       = end($nameChunk);
        $ext       = strtolower($ext);

        if (isExtValid($ext)) {

            $name = time();

            $uploadName = $name . "." . $ext;

            $_POST['field_data']['img'] = '/img_manufacturers/' . $uploadName;

            $dest = $_SERVER['DOCUMENT_ROOT'] . '/img_manufacturers/' . $uploadName;

            if (!move_uploaded_file($_FILES['manufacturers_colour_image']['tmp_name'], $dest)) {
                // Error report here. Not sure where is error report. (Confused)
            }

            $destination = imagecreatetruecolor(96, 96);

            if ('jpg' == $ext || 'jpeg' == $ext) {
                $source = imagecreatefromjpeg($dest);
            } else {
                $source = imagecreatefrompng($dest);
            }

            list($width, $height) = getimagesize($dest);

            if ('png' == $ext) {
                imagealphablending($destination, false);
                imagesavealpha($destination, true);
                $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
                imagefilledrectangle($destination, 0, 0, 96, 96, $transparent);
            }

            imagecopyresampled($destination, $source, 0, 0, 0, 0, 96, 96, $width, $height);

            if ('jpg' == $ext || 'jpeg' == $ext) {
                imagejpeg($destination, $dest, 100);
            } else {
                imagepng($destination, $dest);
            }
        } else {
            // Error report here. Not sure where is error report. (Confused)
        }
    }

    /*if (isset($_FILES['manufacturers_bw_image'])) {
    $name = $_FILES['manufacturers_bw_image']['name'];
    $nameChunk = explode('.', $name);
    $ext = end($nameChunk);
    $ext = strtolower($ext);

    if (isExtValid($ext)) {

    $name = time();

    $uploadName = $name . "." . $ext;

    $field_data['bw_img'] = '/img_manufacturers/bw/' . $uploadName;

    if (!move_uploaded_file($_FILES['manufacturers_bw_image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/img_manufacturers/bw/' . $uploadName)) {
    // Error report here. Not sure where is error report. (Confused)
    }
    } else {
    // Error report here. Not sure where is error report. (Confused)
    }
    } */
    $field_data=$_POST['field_data'];
    for ($i = 0; $i < count($field_data); $i++) {
        $key = key($field_data);

        $update_fields .= "$key = '" . addslashes(htmlspecialchars($field_data[$key])) . "'";

        if ($i < count($field_data) - 1) {
            $update_fields .= ', ';
        }

        next($field_data);
    }

    reset($field_data);

    $statement = "UPDATE $table SET $update_fields WHERE $id_field =".$_POST['id'];
    $DB->query($statement);

    echo "$id: updated<br>";
}

function delete()
{
    global $id, $DB, $table, $id_field;

    $statement = "DELETE FROM $table WHERE $id_field = '$id'";
    $DB->query($statement);

    echo "ID:$id Deleted.<br>";
}

function showResult($cols, $shaded)
{
    global $fields, $actions, $script, $table;

    reset($fields);
    $id = $cols[key($fields)];

    if (locked($table, $id)) {
        echo '<tr bgcolor="pink">';
    } else {
        echo $shaded ? '<tr>' : '<tr bgcolor="#EEEEEE">';
    }

    for ($i = 0; $i < sizeof($fields); $i++) {
        $key = key($fields);
        if($key=='show_front'){
            echo "<td align='center'>";
        }else{
            echo "<td align=\"" . $fields[$key] . "\">";
        }
        

        if ('img' == $key) {
            if ('' !== $cols[$key]) {
                echo "<img src='" . $cols[$key] . "' width='32'/>";
            } else {
                echo "<img src='https://dummyimage.com/92x92/c0c0c0/fff.png&text=img' width='32' />";
            }
        } else {
            echo $cols[$key];
        }

        echo '</td>';
        next($fields);
    }
    echo '<td nowrap>';
    //$type = str_replace(' ', '_', strtolower($cols[type_name]));
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

/*if (!isset($_REQUEST["query"])) {*/
    switch ($action) {
        case "add":
            include __DIR__ . '/generic_add_form.inc.php';
            break;
        case "edit":
            include __DIR__ . '/generic_edit_form.inc.php';
            break;
        case "delete":
            include __DIR__ . '/generic_delete_form.inc.php';
            break;
        case "do_add":
            addNew();
            break;
        case "do_edit":
            if (!locked($table, $id, true)) {
                edit();
            }

            break;
        case "do_delete":
            if (!locked($table, $id, true)) {
                delete();
            }

            break;

    }
/*}*/

if (array_key_exists("query", $_REQUEST)) {
    search(addslashes(addslashes(htmlspecialchars($_REQUEST["query"]))));
}
; //load_hidden_vars();

?>
</form>

<!--//parts admin -->
<?php
include_once __DIR__ . '/footer.inc.php';

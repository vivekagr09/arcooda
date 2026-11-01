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

$script = "options_admin.php";

function list_variables()
{
    global $DB, $script;

    $results = $DB->query("SELECT * FROM mailout_options");
?>



<form accept-charset="utf-8" action="<?=$script?>" method="post" name="admin_form">

<!-- new variable table -->
<blockquote><blockquote><br><br><br>
<table border="0" cellspacing="1" cellpadding="2">
  <tr>
    <td colspan="5"><b>Add mailout option</b></td>
  </tr>
  <tr>
    <td>
      <div align="right">option:</div>
    </td>
    <td>
      <input type="text" name="data[options]" style="width:200px">
    </td>
    <td>
      <div align="right">Value:</div>
    </td>
    <td>
      <input type="text" name="data[value]" style="width:100px">
    </td>
    <td>
      <input type="submit" name="add_new" value="Add">
    </td>
  </tr>
</table>

<!-- //new variable table -->
<br>
<B>Existing options:</b><br>
<table border="0" cellspacing="1" cellpadding="2">

<?php
while ($item = $DB->fetch_array($results)) {
        echo "<tr>";
        echo "<td>$item[options]:</td>";
        echo "<td><input type=\"text\" name=\"var[$item[options]]\" value=\"$item[value]\" style=\"width:300px\"></td>";
        echo "<td><a href=\"$script?action=delete&options=$item[options]\">Delete</a></td>";
        echo "</tr>";

    }
?>
</table>
<br><br>
<div align="center">
<input type="submit" name="update" value="Update All">
</div>
</blockquote>
</blockquote>
</form>
<?php
}

function update_variables()
{
    global $DB, $var;

    for ($i = 0; $i < count($var); $i++) {
        $key = key($var);
        $DB->query("UPDATE mailout_options SET value='$var[$key]' WHERE options = '$key'");
        next($var);
    }

    echo '<font color="blue">Variables updated</font>';

}

function add_new()
{
    global $DB, $data;

    if ('' == $data['options']) {
        return;
    }

    $var_count = $DB->query_first("SELECT COUNT(*) AS vars FROM mailout_options where options = '" . $data['options'] . "'");

    if ($var_count[vars] <= 0) {
        $DB->query("INSERT INTO mailout_options (" . get_fields($data) . ") VALUES (" . get_values($data) . ")");
        echo '<font color="blue">New variable added</font>';
    } else {
        $DB->query("UPDATE mailout_options SET value='" . addslashes($data['value']) . "' WHERE options = '" . $data['options'] . "'");
        echo '<font color="blue">Variables updated</font>';
    }

}

function delete_option($option)
{
    global $DB;

    $statement = "DELETE FROM mailout_options WHERE options ='$option'";
    $DB->query($statement);

    echo $option . ' deleted<br>';
}

if (isset($add_new)) {
    add_new();
}

if (isset($update)) {
    update_variables();
}

if (isset($action) && 'delete' == $action) {
    delete_option($options);
}

list_variables();

include '../footer.inc.php';

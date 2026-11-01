<?php
/******************************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:
 ** Last changed..:
 ** Notes.........:
 *****************************************************************************/

$section_code = "OPTIONS";
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$script = "options_admin.php";

function listVariables()
{
    global $DB, $script;

    $results = $DB->query("SELECT * FROM options");
?>



<form action="<?=$script . $gets?>" method="post" name="options_admin_form">

<!-- new variable table -->
<blockquote><blockquote><br><br><br>
<table border="0" cellspacing="1" cellpadding="2">
  <tr>
    <td colspan="5"><b>Add new global variable</b></td>
  </tr>
  <tr>
    <td>
      <div align="right">Variable:</div>
    </td>
    <td>
      <input type="text" name="variable" style="width:200px">
    </td>
    <td>
      <div align="right">Value:</div>
    </td>
    <td>
      <input type="text" name="value" style="width:100px">
    </td>
    <td>
      <input type="submit" name="add_new" value="Add">
    </td>
  </tr>
</table>

<!-- //new variable table -->
<br>
<B>Existing Variables:</b><br>
<table border="0" cellspacing="1" cellpadding="2">

<?php while ($item = $DB->fetch_array($results)) {
        echo "<tr>";
        echo "<td>$item[variable]:</td>";
        echo "<td><input type=\"text\" name=\"var[$item[id]]\" value=\"$item[value]\" style=\"width:300px\"></td>";
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

function updateVariables()
{
    global $DB, $var;

    for ($i = 0; $i < count($var); $i++) {
        $key = key($var);
        $DB->query("UPDATE options SET value='$var[$key]' WHERE id = '$key'");
        next($var);
    }

    echo '<font color="blue">Variables updated</font>';

}

function addNew()
{
    global $DB, $variable, $value;

    if (trim($variable) == '') {
        return;
    }

    $var_count = $DB->query_first("SELECT COUNT(*) AS vars FROM options where variable = '$variable'");

    if ($var_count[vars] <= 0) {
        $DB->query("INSERT INTO options (variable, value) VALUES ('$variable', '$value')");
        echo '<font color="blue">New variable added</font>';
    } else {
        $DB->query("UPDATE options SET value='$value' WHERE variable = '$variable'");
        echo '<font color="blue">Variables updated</font>';
    }

}

if (isset($add_new)) {
    addNew();
}

if (isset($update)) {
    updateVariables();
}

listVariables();

include_once __DIR__ . '/footer.inc.php';

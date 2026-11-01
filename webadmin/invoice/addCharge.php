<?php
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;
global $DB;

$site_id = $_GLOBALS['site_settings']['site_id'];
if (in_array($site_id, array(
    11, // www.highwaygroup.net
    43, // www.coinoptrading.com
))) {
    $site_id = 0;
}

$sites_list = invoiceGetOptionList("site_id");

$classes_list = array(
    1  => 'American Express Processing Fee',
    2  => 'BPAY Processing Fee',
    3  => 'PayPal Processing Fee',
    4  => 'Visa Processing Fee',
    -1 => '-',
    5  => 'GST',
    6  => 'Packing And Handling Fee',
    7  => 'CIF Insurance',
    -2 => '-',
    10 => 'Choose From List',
);

$charges_list = array();
if ($query = $DB->query(sprintf(
    "SELECT `charge_id`, `charge_name`, `site_id` FROM %s%s ORDER BY `charge_name` ASC",
    "`highwaygroup`.`charges`",
    ($site_id) ? sprintf(
        " WHERE `site_id` = 0 OR `site_id` = '' OR `site_id` = %d",
        $site_id
    ) : ""
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $charges_list[$row["charge_id"]] = sprintf(
            "C%02d - %s%s",
            $row["charge_id"],
            $row["charge_name"],
            (0 != $row["site_id"]) ? (" - " . $sites_list[$row["site_id"]]) : ""
        );
    }
    $DB->free_result($query);
}

$action = 'select';
if (!array_key_exists($_POST['class_id'], $classes_list)) {
    $action = 'list';
}

if (!array_key_exists($_POST['charge_id'], $charges_list) && 10 == $_POST['class_id']) {
    $action = 'list';
}

switch ($action) {
    case "select":
        header("Content-Type:text/html; charset=utf-8");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Add Charge</title>
		<script type="text/javascript">
//<![CDATA[
var data = <?=json_encode(array(
	"charge_id" => intval($_POST['charge_id']),
	"class_id"  => intval($_POST['class_id']),
))?>;
var return_message = <?=json_encode($_GET['return_message'])?>;
if (window.opener.setChargeData) {
	window.opener.setChargeData(data, return_message);
	window.close();
} else {
	alert("There was a problem sending the data");
}
//]]>
		</script>
	</head>
	<body>
	</body>
</html>
<?php
        break;

    default:
        //no break
    case "list":
        header("Content-Type:text/html; charset=utf-8");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Add Charge</title>
		<script type="text/javascript">
//<![CDATA[
function chooseOther()
{
	var elems = document.getElementsByName("class_id");
	for (var i = 0;i < elems.length;i++) if (elems[i].value == "10") elems[i].checked = true;
}
//]]>
		</script>
	</head>
	<body>

<form action="" method="post">
	<input type="hidden" name="action" value="do_add" />
	<table style="margin:auto;border:1px solid #00f;background-color:#9cf;">
		<tr><th>Displayed To Customer</th></tr>
<?php
        $count = 0;
        foreach ($classes_list as $k => $v) {
            if (10 == $k) {
?>
		<tr><td style="text-align:left;white-space:nowrap;">
			<input type="radio" name="class_id" value="<?=$k?>" />
			<select name="charge_id" onchange="chooseOther()">
				<option value=""></option>
				<?=formatHTMLOptions($charges_list, $_POST['class_id'])?>
			</select>
		</td></tr>
<?php
            } elseif ($k >= 0) {
?>
		<tr><td style="text-align:left;white-space:nowrap;">
			<input type="radio" name="class_id" value="<?=$k?>" id="label<?=$count?>" />
			<label for="label<?=$count++?>"><?=htmlspecialchars($v)?></label>
		</td></tr>
<?php
            } else {
?>
		<tr><td><hr /></td></tr>
<?php
            }
        }
?>
	</table>
	<br />
	<div style="text-align:center"><input type="submit" value=" Add Charge " /></div>
</form>

	</body>
</html>
<?php
        break;
}

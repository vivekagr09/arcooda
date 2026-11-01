<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

include 'freight_wizard_functions.php';
?>
<script src="tooltip.js" language="javascript"></script>
<form accept-charset="utf-8" autocomplete="off" method="post" name="addService" action="<?=$_SERVER['PHP_SELF']?>">
<table width="95%" align="center" cellpadding="5" cellpadding="1">
	<tr class="pageMenu">
		<td ><a href="AdminMenu.php">Freight Main Menu</a></td>
	</tr>
</table>
<br>
<?php
switch ($_POST['step']) {
    case 1:
        $errorMsg = "";
        $result   = $DB->query("SELECT * FROM shipping_services WHERE name='" . $_POST['name'] . "' AND source='" . $_POST['source'] . "'");
        if ($DB->num_rows($result) > 0) {
            $errorMsg .= "<li>A service with this name already exists. Please select another.";
        }

        if (is_numeric($_POST['name']) || false !== strpos($_POST['name'], " ") || "" == $_POST['name']) {
            $errorMsg .= "<li>Please enter a valid zone name.</li>\r\n";
        }

        if (!is_numeric($_POST['customer_discount']) || !is_numeric($_POST['store_discount']) ||
            !is_numeric($_POST['handling']) || !is_numeric($_POST['hndasperc'])) {
            $errorMsg .= "<li>Please enter valid discounts and handling charges.</li>\r\n";
        }

        if (intval($_POST['numZones']) <= 0) {
            $errorMsg .= "<li>Please enter a valid number of zones.</li>\r\n";
        }

        if ("" == $errorMsg) {
            include "addStep2.inc.php";
        } else {
            showErrorMsg($errorMsg);
            include "addStep1.inc.php";
        }
        break;

    case 2:
        $errorMsg = "";
        $blank    = false;
        foreach ($_POST['zones'] as $zone) {
            if (trim($zone) == "" || false !== strpos($zone, " ") || preg_match('/([^a-zA-Z0-9_-])+/', $zone)) {
                $blank = true;
            }

        }
        if ($blank) {
            $errorMsg = "<li>Please ensure all zones have valid names.</li>\r\n";
        }

        if ("" == $errorMsg) {
            // TABLE NAMES CANNOT BEGIN WITH A DIGIT SO PAD ALL NUMERICAL ZONE NAMES
            $temp = array();
            for ($i = 0; $i < count($_POST['zones']); $i++) {
                $zoneName = $_POST['zones'][$i];
                $temp[$i] = $zoneName;
                $first    = substr((string) $zoneName, 0, 1);
                if (is_numeric($first)) {
                    $first    = intval($zoneName);
                    $temp[$i] = "zone" . $first;
                }
            }
            $GLOBALS['zoneNameArray'] = $temp;
            addService();
        } else {
            showErrorMsg($errorMsg);
            include "addStep2.inc.php";
        }
        break;

    default:
        include "addStep1.inc.php";

}

function showErrorMsg($msg)
{
    echo "<div align=\"left\">\r\n";
    echo "<font color=\"#FF0000\">The following errors occured<br>";
    echo $msg;
    echo "</font></div>";
}
?>
</form>